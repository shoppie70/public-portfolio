# Design System Master File

> **LOGIC:** When building a specific page, first check `design-system/pages/[page-name].md`.
> If that file exists, its rules **override** this Master file.
> If not, strictly follow the rules below.

---

**Project:** Sho Tsukamoto Portfolio
**Updated:** 2026-06-30
**Design Direction:** Apple-inspired — Dark, Minimal, Refined

---

## Global Design Philosophy

Apple.comに倣い、以下を基本原則とする：
- **余白を恐れない** — 大きなホワイトスペースが品質の証
- **タイポグラフィで語る** — 装飾より文字の力で訴える
- **モノクロベース + 単色アクセント** — 散漫にならない
- **トランジションは控えめに** — 動きは意味を持つときのみ

---

## Global Rules

### Color Palette

| Role | Hex | CSS Variable |
|------|-----|--------------|
| Background | `#000000` | `--color-bg` |
| Surface | `#111111` | `--color-surface` |
| Surface 2 | `#1a1a1a` | `--color-surface-2` |
| Border | `rgba(255,255,255,0.08)` | `--color-border` |
| Border Hover | `rgba(255,255,255,0.15)` | `--color-border-hover` |
| Text Primary | `#f5f5f7` | `--color-text` |
| Text Secondary | `#a1a1a6` | `--color-text-secondary` |
| Text Tertiary | `#6e6e73` | `--color-text-tertiary` |
| Accent (Blue) | `#2997ff` | `--color-accent` |
| Accent Glow | `rgba(41,151,255,0.15)` | `--color-accent-glow` |

**Color Notes:** Apple.comのダークデザイン準拠。#000黒背景 + #2997ffアップルブルー。

### Typography

- **Heading Font:** Inter (wght: 300–900)
- **Mono Font:** JetBrains Mono (wght: 400–600)
- **Mood:** Apple, premium, minimal, clean, precise
- **Google Fonts:** Inter + JetBrains Mono

**CSS Import:**
```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap');
```

**Typography Scale:**
```css
.headline-giant  { font-size: clamp(3rem, 10vw, 8rem); font-weight: 700; letter-spacing: -0.03em; }
.headline-large  { font-size: clamp(2rem, 5vw, 4rem);  font-weight: 700; letter-spacing: -0.025em; }
.headline-medium { font-size: clamp(1.5rem, 3vw, 2.5rem); font-weight: 600; letter-spacing: -0.02em; }
.section-label   { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #2997ff; }
```

### Spacing Variables

| Token | Value | Usage |
|-------|-------|-------|
| Section Padding | `py-24 md:py-32` | 全セクション統一 |
| Card Padding | `p-6 md:p-8` | カード内部 |
| Gap | `gap-5` or `gap-6` | グリッドギャップ |
| Container | `max-w-screen-lg mx-auto` | コンテンツ幅 |

---

## Component Specs

### Navigation

```css
.nav-glass {
  background: rgba(0, 0, 0, 0.72);
  backdrop-filter: saturate(180%) blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 9999px;
}
```

- Fixed top-3, 左右4px余白
- リンクテキスト: `text-[#a1a1a6]` hover `text-white`
- ブランドロゴ: `text-sm font-semibold tracking-widest`

### Buttons

```css
/* Primary — Apple Blue */
.btn-primary {
  padding: 12px 24px;
  background: #2997ff;
  color: #fff;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
}
.btn-primary:hover { background: #409cff; }

/* Secondary — Ghost */
.btn-secondary {
  padding: 12px 24px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 9999px;
  color: #f5f5f7;
}
.btn-secondary:hover { background: rgba(255,255,255,0.1); }
```

### Cards

```css
.surface-card {
  background: #111;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  transition: border-color 0.3s ease;
}

.bento-card {
  background: #111;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 28px;
  transition: border-color 0.3s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.bento-card:hover {
  border-color: rgba(255,255,255,0.15);
  transform: translateY(-2px);
}
```

### Tags / Badges

```css
.tag {
  background: rgba(41, 151, 255, 0.1);
  color: #2997ff;
  border: 1px solid rgba(41, 151, 255, 0.2);
  border-radius: 9999px;
  padding: 4px 12px;
  font-size: 0.7rem;
  font-weight: 600;
}
```

---

## Layout System

### Bento Grid

```css
.bento-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 16px;
}
.bento-card.span-4  { grid-column: span 4; }
.bento-card.span-6  { grid-column: span 6; }
.bento-card.span-8  { grid-column: span 8; }
.bento-card.span-12 { grid-column: span 12; }

@media (max-width: 1024px) {
  .bento-card { grid-column: span 12; }
}
```

### Section Structure

```
Section Label (青 uppercase tracking)
↓
Headline (大きく、tight letter-spacing)
↓
Content (surface-card または bento-grid)
```

---

## Animation & Motion

```css
.reveal {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}
.reveal.visible { opacity: 1; transform: none; }

/* MUST HAVE */
@media (prefers-reduced-motion: reduce) {
  .reveal { opacity: 1; transform: none; transition: none; }
}
```

- **Intersection Observer** でスクロールトリガー（WOW.jsは使わない）
- アニメーション時間: 600-700ms cubic-bezier
- Delay: `reveal-delay-1` 0.1s / `-2` 0.2s / `-3` 0.3s

---

## Style Guidelines

**Direction:** Apple-inspired Dark Minimal

**Keywords:** precision, restraint, negative space, clarity, premium

**Key Principles:**
- セクション見出しは大型 (`clamp(2rem, 5vw, 4rem)`)
- セクションラベルは `section-label` クラス（青 uppercase）
- 背景の変化は使わない（すべて `#000` 統一）
- `section-divider` で視覚的区切り

### Page Structure

```
1. Hero (フルビューポート、大型見出し)
2. About (Core Value カード + プロフィール)
3. Skills (Bento Grid)
4. Certifications (リスト型カード)
5. Experience (タイムライン)
6. Works (3カラムグリッド + モーダル)
7. Blog (3カラムグリッド)
8. Footer (シンプル)
```

---

## Anti-Patterns (Do NOT Use)

- ❌ 絵文字アイコン — SVGアイコンを使う（Heroicons系）
- ❌ 不要な英語ラベル — ユーザーに無意味な英語は日本語化
- ❌ セクション背景の色分け — `#000` 統一、`section-divider` で区切る
- ❌ WOW.js / animate.css — Intersection Observer で実装
- ❌ `transform: scale` のホバー — レイアウトシフトになるため `translateY(-2px)` を使う
- ❌ 高速アニメーション — 最低 300ms、品質感のある動き
- ❌ Flat design without depth — subtle borders and shadows

### Forbidden Patterns

- ❌ **Emojis as icons** — Use SVG icons (Heroicons)
- ❌ **Missing cursor:pointer** — All clickable elements must have cursor:pointer
- ❌ **Layout-shifting hovers** — Use translateY, not scale
- ❌ **Low contrast text** — Maintain 4.5:1 minimum
- ❌ **Instant state changes** — Always 150-300ms transitions
- ❌ **Invisible focus states** — Focus states must be visible

---

## Pre-Delivery Checklist

- [ ] No emojis used as icons (use SVG)
- [ ] All icons from Heroicons/Lucide
- [ ] `cursor-pointer` on all clickable elements
- [ ] Hover states with smooth transitions (200-300ms)
- [ ] Text contrast 4.5:1 minimum
- [ ] Focus states visible for keyboard navigation
- [ ] `prefers-reduced-motion` respected
- [ ] Responsive: 375px, 768px, 1024px, 1440px
- [ ] No horizontal scroll on mobile
- [ ] No content hidden behind fixed navbar
- [ ] Section labels in Japanese (not English marketing terms)
