<?php
/**
 * バナー管理画面のクラス
 */
class Banner_Admin {
    /**
     * コンストラクタ
     */
    public function __construct() {
        // データベースクラスの存在チェック
        if (!class_exists('Banner_DB')) {
            require_once MODERN_BANNER_PATH . 'inc/class-banner-db.php';
        }

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_post_save_modern_banner', array($this, 'save_settings'));
        
        // エラーメッセージ表示用
        add_action('admin_notices', array($this, 'show_admin_notices'));
    }

    /**
     * 管理メニューの追加
     */
    public function add_admin_menu() {
        try {
            add_menu_page(
                'バナー設定',
                'バナー管理',
                'manage_options',
                'modern-banner',
                array($this, 'render_admin_page'),
                'dashicons-images-alt2',
                20
            );
        } catch (Exception $e) {
            error_log('Modern Banner admin menu error: ' . $e->getMessage());
        }
    }

    // 既存のenqueue_admin_scripts関数はそのまま

    /**
     * 管理画面の表示
     */
    public function render_admin_page() {
        try {
            $options = get_option('modern_banner_options', array());
            if ($options === false) {
                throw new Exception('オプションの取得に失敗しました。');
            }

            // 既存の管理画面HTML出力処理
            include(MODERN_BANNER_PATH . 'templates/admin-page.php');
        } catch (Exception $e) {
            error_log('Modern Banner render error: ' . $e->getMessage());
            echo '<div class="notice notice-error"><p>' . esc_html($e->getMessage()) . '</p></div>';
        }
    }

    /**
     * エラーメッセージの表示
     */
    public function show_admin_notices() {
        if (isset($_GET['modern_banner_error'])) {
            $error = sanitize_text_field($_GET['modern_banner_error']);
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . esc_html($error) . '</p>';
            echo '</div>';
        }
    }

    /**
     * 設定の保存
     */
    public function save_settings() {
        try {
            if (!current_user_can('manage_options')) {
                throw new Exception('権限がありません');
            }

            check_admin_referer('modern_banner_nonce', 'modern_banner_nonce');

            $options = array(
                'link_url' => esc_url_raw($_POST['link_url'] ?? ''),
                'pc_image' => esc_url_raw($_POST['pc_image'] ?? ''),
                'pc_image_alt' => sanitize_text_field($_POST['pc_image_alt'] ?? ''),
                'pc_image_loading' => sanitize_text_field($_POST['pc_image_loading'] ?? 'lazy'),
                'pc_image_fetchpriority' => sanitize_text_field($_POST['pc_image_fetchpriority'] ?? 'auto'),
                'pc_image_custom_attributes' => sanitize_textarea_field($_POST['pc_image_custom_attributes'] ?? ''),
                'sp_image' => esc_url_raw($_POST['sp_image'] ?? ''),
                'sp_image_alt' => sanitize_text_field($_POST['sp_image_alt'] ?? ''),
                'sp_image_loading' => sanitize_text_field($_POST['sp_image_loading'] ?? 'lazy'),
                'sp_image_fetchpriority' => sanitize_text_field($_POST['sp_image_fetchpriority'] ?? 'auto'),
                'sp_image_custom_attributes' => sanitize_textarea_field($_POST['sp_image_custom_attributes'] ?? ''),
                'custom_css' => sanitize_textarea_field($_POST['custom_css'] ?? ''),
            );

            $updated = update_option('modern_banner_options', $options);
            if ($updated === false) {
                throw new Exception('設定の保存に失敗しました。');
            }

            wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=modern-banner')));
            exit;

        } catch (Exception $e) {
            error_log('Modern Banner save error: ' . $e->getMessage());
            wp_redirect(add_query_arg(
                'modern_banner_error',
                urlencode($e->getMessage()),
                admin_url('admin.php?page=modern-banner')
            ));
            exit;
        }
    }
}
