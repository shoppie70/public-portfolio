<?php
/**
 * お問い合わせフォーム API エンドポイント
 * スターサーバー / PHP 7.4〜8.x 対応
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// POSTリクエストのみ許可
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// -------------------------------------------------------------------
// .env ファイルの読み込み処理（親ディレクトリも探索）
// -------------------------------------------------------------------
function loadEnv($envPath) {
    if (!file_exists($envPath) || !is_readable($envPath)) {
        return false;
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
    return true;
}

foreach ([__DIR__ . '/.env', __DIR__ . '/../.env', __DIR__ . '/../../.env'] as $possibleEnv) {
    if (loadEnv($possibleEnv)) {
        break;
    }
}

// -------------------------------------------------------------------
// 設定値 (Cloudflare Turnstile 秘密鍵 & 送信先アドレス)
// -------------------------------------------------------------------
$turnstileSecret = getenv('TURNSTILE_SECRET') ?: ($_ENV['TURNSTILE_SECRET'] ?? ($_SERVER['TURNSTILE_SECRET'] ?? '1x0000000000000000000000000000000AA'));
$adminEmail = getenv('ADMIN_EMAIL') ?: ($_ENV['ADMIN_EMAIL'] ?? 'contact@sho-tsukamoto.jp');

define('ADMIN_EMAIL', $adminEmail);
define('SITE_NAME', 'Sho Tsukamoto Portfolio');

// -------------------------------------------------------------------
// 入力データの取得（JSONまたはPOST）
// -------------------------------------------------------------------
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

// Honeypot チェック (ロボットが自動入力する隠しフィールド)
if (!empty($data['hp_website'])) {
    // スパムボットには成功レスポンスを返して終了
    echo json_encode(['success' => true, 'message' => '送信が完了しました。']);
    exit;
}

$name    = isset($data['name']) ? trim($data['name']) : '';
$email   = isset($data['email']) ? trim($data['email']) : '';
$type    = isset($data['type']) ? trim($data['type']) : 'その他';
$message = isset($data['message']) ? trim($data['message']) : '';
$token   = isset($data['cf_turnstile_response']) ? trim($data['cf_turnstile_response']) : '';

// -------------------------------------------------------------------
// 入力バリデーション
// -------------------------------------------------------------------
$errors = [];

if (empty($name)) {
    $errors[] = 'お名前を入力してください。';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = '有効なメールアドレスを入力してください。';
}

if (empty($message)) {
    $errors[] = 'お問い合わせ内容を入力してください。';
} elseif (mb_strlen($message) < 10) {
    $errors[] = 'お問い合わせ内容は10文字以上で入力してください。';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// -------------------------------------------------------------------
// Cloudflare Turnstile 検証 (canonical siteverify call)
// -------------------------------------------------------------------
$verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
$postFields = http_build_query([
    'secret'   => $turnstileSecret,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$verifyResult = curl_exec($ch);
curl_close($ch);

$verifyData = json_decode($verifyResult, true);
if (!$verifyData || empty($verifyData['success']) || $verifyData['success'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'スパム検証（Turnstile）に失敗しました。もう一度お試しください。']);
    exit;
}

// -------------------------------------------------------------------
// メール送信ヘルパー関数 (確実な UTF-8 送信)
// -------------------------------------------------------------------
function sendUtf8Mail($to, $subject, $body, $from, $replyTo = null) {
    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");

    $headers = [];
    $headers[] = 'From: ' . $from;
    if ($replyTo) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    return mail($to, $encodedSubject, $body, implode("\r\n", $headers));
}

// -------------------------------------------------------------------
// メール本文作成 & 送信
// -------------------------------------------------------------------
$now = date('Y-m-d H:i:s');
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '不明';

// 1. 管理者宛メール通知
$adminSubject = "【ポートフォリオお問い合わせ】{$name}様より ({$type})";
$adminBody = <<<EOD
ポートフォリオサイトより新しいお問い合わせが届きました。

■ お名前:
{$name}

■ メールアドレス:
{$email}

■ お問い合わせ種別:
{$type}

■ お問い合わせ内容:
{$message}

--------------------------------------------------
送信日時: {$now}
送信元IP: {$clientIp}
EOD;

$mailSent = sendUtf8Mail(ADMIN_EMAIL, $adminSubject, $adminBody, 'no-reply@sho-tsukamoto.jp', $email);

// 2. 自動返信メール (ユーザー宛)
$userSubject = "【自動送信】お問い合わせを受け付けました | " . SITE_NAME;
$userBody = <<<EOD
{$name} 様

この度はお問い合わせいただき、誠にありがとうございます。
塚本 翔（Sho Tsukamoto）です。

以下の内容でお問い合わせを承りました。
内容を確認の上、通常2営業日以内にご返信いたします。

--------------------------------------------------
■ お問い合わせ種別: {$type}
■ お問い合わせ内容:
{$message}
--------------------------------------------------

※このメールは送信専用アドレスから自動配信されています。

--------------------------------------------------
塚本 翔 / Sho Tsukamoto
Web Developer / Chief Engineer
Email: contact@sho-tsukamoto.jp
Web: https://sho-tsukamoto.jp
--------------------------------------------------
EOD;

sendUtf8Mail($email, $userSubject, $userBody, 'contact@sho-tsukamoto.jp');

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'お問い合わせを送信しました。自動返信メールをご確認ください。']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'メール送信処理に失敗しました。時間をおいて直接 contact@sho-tsukamoto.jp へご連絡ください。']);
}
