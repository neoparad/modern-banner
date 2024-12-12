# Modern Banner Plugin

WordPressサイト用のモダンな追尾バナープラグイン。PC・スマートフォン対応で、画像の遅延読み込みやカスタムアニメーションをサポートします。

## 特徴

- レスポンシブ対応（PC/スマートフォン別の画像設定）
- 画像の遅延読み込み（Lazy Loading）対応
- カスタムCSS機能搭載
- スクロールに応じた表示制御
- 閉じるボタン（24時間非表示機能付き）
- SEO対策（fetchpriorityとloading属性のカスタマイズ可能）

## インストール

1. GitHubからZIPをダウンロード
2. WordPressの管理画面からプラグインをアップロード
3. プラグインを有効化

## 設定方法

1. 管理画面の「バナー管理」メニューにアクセス
2. 以下の項目を設定：
   - リンクURL
   - PC用バナー画像
   - スマートフォン用バナー画像
   - 画像の読み込み設定
   - カスタムCSS（必要に応じて）

## カスタマイズ

### カスタムCSS例

```css
/* キラキラエフェクト */
.modern-banner {
  animation: sparkle 2s infinite;
}

@keyframes sparkle {
  0% { opacity: 1; }
  50% { opacity: 0.7; }
  100% { opacity: 1; }
}