<!-- prettier-ignore -->
<div align="center">

<img src="./public/apple-touch-icon.png" alt="sho-tsukamoto.jp logo" width="96" height="96" />

# sho-tsukamoto.jp

*塚本 翔（Sho Tsukamoto）のポートフォリオサイトのフロントエンドソースコード*

[![Vite](https://img.shields.io/badge/Vite-v8.1.2-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vite.dev/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3.4.19-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3.15.12-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white)](https://alpinejs.dev/)
[![Node.js](https://img.shields.io/badge/Node.js->=20-3C873A?style=flat-square&logo=node.js&logoColor=white)](https://nodejs.org)

⭐ If you like this project, star it on GitHub — it helps a lot!

[Overview](#overview) • [Features](#features) • [Tech Stack](#tech-stack) • [Directory Structure](#directory-structure) • [Getting Started](#getting-started) • [Profile & Works](#profile--works)

</div>

---

## Overview

本リポジトリは、**塚本 翔（Sho Tsukamoto）**の公式ポートフォリオサイト [sho-tsukamoto.jp](https://sho-tsukamoto.jp) のフロントエンドソースコードです。

Next.jsやReactなどの重厚なフレームワークをあえて使用せず、**Vite** と **`vite-plugin-html-inject`** を組み合わせたシンプルな静的サイト構成を採用しています。HTMLをパーツ単位でコンポーネント化しつつ、**Tailwind CSS + SCSS** によるモダンで柔軟なスタイリングと、**Alpine.js** による超軽量なリアクティブ制御を実現した、パフォーマンスとメンテナンス性の高い設計が特徴です。

> [!NOTE]
> **なぜこの構成なのか？**
> ポートフォリオサイトのようなコンテンツ主体の静的ページにおいて、過剰なJavaScriptフレームワークは初期読み込み（LCPやFIDなどのWeb Vitals）を阻害する要因になります。Viteの高速なビルドとHTMLインジェクション、最小限のランタイムを持つAlpine.jsを組み合わせることで、開発効率とブラウザでの表示パフォーマンスを両立しています。

## Features

- 🧱 **HTMLコンポーネント設計**: `vite-plugin-html-inject` を導入し、`components/` ディレクトリ配下にセクションごとのHTMLを分割管理。ルートの `index.html` から `<load ="..." />` 形式で読み込むため、保守性が非常に高いです。
- ⚡ **超高速な開発環境**: ビルドツールに Vite を採用。瞬時の Hot Module Replacement (HMR) と、最適化された本番ビルドを提供します。
- 🎨 **Tailwind CSS & SCSS のハイブリッド装飾**: ユーティリティファーストなスタイリングを基本としつつ、複雑なUIパーツやアニメーションは SCSS (`assets/css/app.scss`) で記述する柔軟なスタイル設計。
- 🏃 **Alpine.js による軽量インタラクション**: 状態管理やDOM操作には Alpine.js を採用し、Vanilla JS に近い速度と記述量でアコーディオンやモーダルなどの動きを実装。
- 🌀 **リッチなビジュアル表現**: Swiper によるカルーセル表示や、Animate.css による動的なスクロールアニメーションを搭載。

## Tech Stack

### Core & Build
| テクノロジー | バージョン | 用途 |
| :--- | :--- | :--- |
| **Vite** | `^8.1.2` | ビルドツール / 開発サーバー |
| **vite-plugin-html-inject** | `^1.1.2` | HTMLの分割・インジェクション |
| **PostCSS** | `^8.5.16` | CSSの後処理・最適化 |

### Styling & UI
| テクノロジー | バージョン | 用途 |
| :--- | :--- | :--- |
| **Tailwind CSS** | `^3.4.19` | ユーティリティファーストCSS |
| **Sass (SCSS)** | `^1.101.0` | CSSプリプロセッサ |
| **Autoprefixer** | `^10.5.2` | ベンダープレフィックス自動付与 |
| **Swiper** | `^14.0.1` | カルーセル・スライダーUI |
| **Animate.css** | `^4.1.1` | CSSアニメーションライブラリ |

### Client-side Logic & Helpers
| テクノロジー | バージョン | 用途 |
| :--- | :--- | :--- |
| **Alpine.js** | `^3.15.12` | 軽量リアクティブJavaScriptフレームワーク |
| **Axios** | `^1.18.1` | HTTPクライアント（APIリクエスト用） |
| **Lodash** | `^4.18.1` | JavaScriptユーティリティ |

## Directory Structure

```text
.
├── index.html              # メインエントリーHTML
├── tailwind.config.js       # Tailwind CSS 設定
├── postcss.config.js        # PostCSS 設定
├── vite.config.js           # Vite 設定
├── assets/                  # アセットディレクトリ
│   ├── css/
│   │   └── app.scss         # メインスタイルシート (Tailwindディレクティブ含む)
│   └── js/
│       ├── app.js           # JavaScriptエントリーポイント
│       └── bootstrap.js     # ライブラリ等の初期化処理
├── components/              # 分割されたHTMLコンポーネント
│   ├── head/                # headタグ内の各種メタデータ (SEO, OGPなど)
│   ├── about.html           # 自己紹介セクション
│   ├── blog.html            # ブログセクション
│   ├── skills.html          # スキルセクション
│   ├── works.html           # 制作実績セクション
│   └── ...                  # その他UIパーツ
├── public/                  # 静的ファイル (画像, OGP, ファビコンなど)
└── dist/                    # ビルド後の成果物出力先
```

## Getting Started

### Prerequisites

- **Node.js** >= `20.x` (LTS推奨)
- **npm** (Node.jsに同梱)

### Installation

リポジトリをクローンし、依存パッケージをインストールします。

```bash
git clone https://github.com/shoppie70/public-portfolio.git
cd public-portfolio
npm install
```

### Development

ローカル開発サーバーを起動し、リアルタイムにコードの変更をプレビューします。

```bash
npm run dev
```

ブラウザで `http://localhost:5173` が自動的に立ち上がります。

### Build

本番環境用の静的ファイルを最適化してビルドします。

```bash
npm run build
```

ビルドが完了すると、プロジェクトルートに `dist/` ディレクトリが生成され、本番配備可能な HTML/CSS/JS が出力されます。

> [!IMPORTANT]
> コードを変更した後は、デプロイやコミットの前に必ず `npm run build` を実行して、ビルドエラーが発生しないことを確認してください。

---

## Profile & Works

ポートフォリオに掲載されている塚本 翔の経歴および代表的な制作実績の要約です。詳細な職務経歴は以下のリンクから確認できます。

📄 **[詳細な職務経歴書 (CV) を HackMD で見る](https://hackmd.io/@sho-tsukamoto/CV)**

### About Sho Tsukamoto
幼少期からコンピュータに興味を持ち、高専の情報工学科で基礎を習得。PHP/Laravelを中心としたWebシステム開発に約8年の経験を持ち、顧客折衝、要件定義、データベース設計、インフラ構築、フロントおよびバックエンド開発まで「一気通貫で対応できること」を最大の強みとしています。
現在は株式会社ハジメクリエイトにて、社内最年少でチーフエンジニアに昇格し、開発統括やチームマネジメントを担っています。

### Qualifications & Certifications
- **国家資格**: 第二種電気工事士、工事担任者 第二級デジタル通信、二級小型船舶操縦士
- **その他認定**: Google デジタルマーケティングの基礎 認定、ドローン・ジャパン ドローンエンジニア養成塾 修了

### Representative Works
| プロジェクト名 | 使用技術 | 概要 |
| :--- | :--- | :--- |
| **ずきメモ** | Laravel, React, Inertia.js, Tailwind CSS | 頭痛・服薬ログ管理Webアプリ。気象APIと連携可能。 |
| **Instagram Graph API Token Generator** | Laravel | Instagram埋め込み用トークンの自動取得・更新ツール（個人開発）。 |
| **シオヨミ** | Vanilla JS, CSS | 気象庁API等のデータを活用した潮見表確認ツール。 |
| **ほいサーチ** | React, Tailwind CSS | 広島市の保育施設を視覚的に検索・比較できる個人開発ツール。 |
