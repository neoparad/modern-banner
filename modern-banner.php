<?php
/**
 * Plugin Name: Modern Banner
 * Plugin URI: https://github.com/あなたのユーザー名/modern-banner
 * Description: モダンな追尾バナーを実装するプラグイン
 * Version: 1.0.0
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: LINKTH
 * License: GPL-2.0+
 * Text Domain: modern-banner
 */

if (!defined('ABSPATH')) {
    exit;
}

// プラグインのバージョン
define('MODERN_BANNER_VERSION', '1.0.0');

// プラグインのパス
define('MODERN_BANNER_PATH', plugin_dir_path(__FILE__));

// プラグインのURL
define('MODERN_BANNER_URL', plugin_dir_url(__FILE__));

/**
 * プラグインの初期化
 */
class Modern_Banner {
    /**
     * コンストラクタ
     */
    public function __construct() {
        // 管理画面の初期化
        require_once MODERN_BANNER_PATH . 'inc/class-banner-admin.php';
        new Banner_Admin();

        // フロントエンドの処理を追加
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_banner'));
    }

    /**
     * スクリプトとスタイルの読み込み
     */
    public function enqueue_scripts() {
        // CSSの読み込み
        wp_enqueue_style(
            'modern-banner',
            MODERN_BANNER_URL . 'css/style.css',
            array(),
            MODERN_BANNER_VERSION
        );

        // JavaScriptの読み込み
        wp_enqueue_script(
            'modern-banner',
            MODERN_BANNER_URL . 'js/banner-frontend.js',
            array(),
            MODERN_BANNER_VERSION,
            true
        );
    }

    /**
     * カスタム属性の解析
     */
    private function parse_custom_attributes($attributes_string) {
        $attributes = array();
        if (empty($attributes_string)) {
            return $attributes;
        }

        // 簡易的な属性パーサー
        preg_match_all('/([a-zA-Z0-9_-]+)=[\'"]([^\'"]*)[\'"]/', $attributes_string, $matches);
        if (!empty($matches[1]) && !empty($matches[2])) {
            foreach ($matches[1] as $key => $attribute_name) {
                $attributes[$attribute_name] = $matches[2][$key];
            }
        }

        return $attributes;
    }

    /**
     * バナーのHTML出力
     */
    public function render_banner() {
        $options = get_option('modern_banner_options', array());
        
        // オプションが設定されていない場合は表示しない
        if (empty($options['pc_image']) && empty($options['sp_image'])) {
            return;
        }

        // カスタムCSS
        if (!empty($options['custom_css'])) {
            echo '<style id="modern-banner-custom-css">';
            echo wp_strip_all_tags($options['custom_css']);
            echo '</style>';
        }

        $banner_html = '<div class="modern-banner">';
        $banner_html .= '<a href="' . esc_url($options['link_url'] ?? '#') . '" class="modern-banner-link">';
        
        // PC用バナー
        if (!empty($options['pc_image'])) {
            $pc_attributes = array(
                'src' => esc_url($options['pc_image']),
                'alt' => esc_attr($options['pc_image_alt'] ?? ''),
                'class' => 'modern-banner-image-pc',
                'loading' => esc_attr($options['pc_image_loading'] ?? 'lazy'),
                'fetchpriority' => esc_attr($options['pc_image_fetchpriority'] ?? 'auto'),
            );

            // カスタム属性の追加
            if (!empty($options['pc_image_custom_attributes'])) {
                $custom_attrs = $this->parse_custom_attributes($options['pc_image_custom_attributes']);
                $pc_attributes = array_merge($pc_attributes, $custom_attrs);
            }

            // 属性を文字列に変換
            $pc_attributes_str = '';
            foreach ($pc_attributes as $attr_name => $attr_value) {
                $pc_attributes_str .= ' ' . esc_attr($attr_name) . '="' . esc_attr($attr_value) . '"';
            }

            $banner_html .= '<img' . $pc_attributes_str . '>';
        }
        
        // スマホ用バナー
        if (!empty($options['sp_image'])) {
            $sp_attributes = array(
                'src' => esc_url($options['sp_image']),
                'alt' => esc_attr($options['sp_image_alt'] ?? ''),
                'class' => 'modern-banner-image-sp',
                'loading' => esc_attr($options['sp_image_loading'] ?? 'lazy'),
                'fetchpriority' => esc_attr($options['sp_image_fetchpriority'] ?? 'auto'),
            );

            // カスタム属性の追加
            if (!empty($options['sp_image_custom_attributes'])) {
                $custom_attrs = $this->parse_custom_attributes($options['sp_image_custom_attributes']);
                $sp_attributes = array_merge($sp_attributes, $custom_attrs);
            }

            // 属性を文字列に変換
            $sp_attributes_str = '';
            foreach ($sp_attributes as $attr_name => $attr_value) {
                $sp_attributes_str .= ' ' . esc_attr($attr_name) . '="' . esc_attr($attr_value) . '"';
            }

            $banner_html .= '<img' . $sp_attributes_str . '>';
        }
        
        $banner_html .= '</a>';
        
        // 閉じるボタン
        $banner_html .= '<button class="modern-banner-close" aria-label="バナーを閉じる">×</button>';
        $banner_html .= '</div>';

        echo $banner_html;
    }
}

// プラグインの初期化
new Modern_Banner();

// Githubからの自動更新用
require_once MODERN_BANNER_PATH . 'inc/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/あなたのユーザー名/modern-banner/',
    __FILE__,
    'modern-banner'
);

// mainブランチを使用
$myUpdateChecker->setBranch('main');