<?php
/**
 * Plugin Name: Modern Banner
 * Plugin URI: https://github.com/neoparad/modern-banner
 * Description: モダンな追尾バナーを実装するプラグイン（複数バナー対応版）
 * Version: 2.0.0
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: LINKTH
 * Author URI: https://github.com/neoparad
 * License: GPL-2.0+
 * Text Domain: modern-banner
 */

if (!defined('ABSPATH')) {
    exit;
}

// プラグインのバージョン
define('MODERN_BANNER_VERSION', '2.0.0');

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
        // データベーステーブルの作成
        add_action('activate_' . plugin_basename(__FILE__), array($this, 'activate'));
        
        // 管理画面の初期化
        require_once MODERN_BANNER_PATH . 'inc/class-banner-admin.php';
        new Banner_Admin();

        // ショートコードの登録
        require_once MODERN_BANNER_PATH . 'inc/class-banner-shortcode.php';
        Banner_Shortcode::register();

        // ウィジェットの登録
        require_once MODERN_BANNER_PATH . 'inc/class-banner-widget.php';
        add_action('widgets_init', array($this, 'register_widget'));

        // フロントエンドの処理を追加
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * プラグイン有効化時の処理
     */
    public function activate() {
        require_once MODERN_BANNER_PATH . 'inc/class-banner-db.php';
        Banner_DB::create_table();
    }

    /**
     * ウィジェットの登録
     */
    public function register_widget() {
        register_widget('Modern_Banner_Widget');
    }

    /**
     * スクリプトとスタイルの読み込み
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'modern-banner',
            MODERN_BANNER_URL . 'css/style.css',
            array(),
            MODERN_BANNER_VERSION
        );

        wp_enqueue_script(
            'modern-banner',
            MODERN_BANNER_URL . 'js/banner-frontend.js',
            array(),
            MODERN_BANNER_VERSION,
            true
        );
    }
}

// プラグインの初期化
new Modern_Banner();

// Githubからの自動更新用
require_once MODERN_BANNER_PATH . 'inc/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/neoparad/modern-banner/',
    __FILE__,
    'modern-banner'
);

$myUpdateChecker->setBranch('main');