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
// 設定値 (Cloudflare Turnstile 秘密鍵 & 送信先アドレス)
// -------------------------------------------------------------------
// TODO: 本番運用時は Cloudflare ダッシュボードで取得した Secret Key に差し替えてください。
// テスト用 Secret Key ("1x0000000000000000000000000000000AA" は常にPassします)
define('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA');
define('ADMIN_EMAIL', 'contact@sho-tsukamoto.jp');
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
// Cloudflare Turnstile 検証
// -------------------------------------------------------------------
if (TURNSTILE_SECRET_KEY !== 'OFF') {
    $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $postFields = http_build_query([
        'secret'   => TURNSTILE_SECRET_KEY,
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
    if (!$verifyData || empty($verifyData['success'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'スパム検証（Turnstile）に失敗しました。もう一度お試しください。']);
        exit;
    }
}

// -------------------------------------------------------------------
// メール送信処理
// -------------------------------------------------------------------
mb_language('japanese');
mb_internal_encoding('UTF-8');

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
送信日時: " . date('Y-m-d H:i:s') . "
送信元IP: {$_SERVER['REMOTE_ADDR']}
EOD;

$adminHeaders = [
    'From' => 'no-reply@sho-tsukamoto.jp',
    'Reply-To' => $email,
    'X-Mailer' => 'PHP/' . phpversion(),
    'Content-Type' => 'text/plain; charset=UTF-8'
];

$mailSent = mb_send_mail(ADMIN_EMAIL, $adminSubject, $adminBody, $adminHeaders);

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

$userHeaders = [
    'From' => 'contact@sho-tsukamoto.jp',
    'X-Mailer' => 'PHP/' . phpversion(),
    'Content-Type' => 'text/plain; charset=UTF-8'
];

@mb_send_mail($email, $userSubject, $userBody, $userHeaders);

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'お問い合わせを送信しました。自動返信メールをご確認ください。']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'メール送信処理に失敗しました。時間をおいて直接 contact@sho-tsukamoto.jp へご連絡ください。']);
}
