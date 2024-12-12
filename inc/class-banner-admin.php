<?php
/**
 * バナー管理画面のクラス
 */
class Banner_Admin {
    /**
     * コンストラクタ
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_post_save_modern_banner', array($this, 'save_settings'));
    }

    /**
     * 管理メニューの追加
     */
    public function add_admin_menu() {
        add_menu_page(
            'バナー設定',
            'バナー管理',
            'manage_options',
            'modern-banner',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            20
        );
    }

    /**
     * 管理画面用スクリプトの読み込み
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_modern-banner' !== $hook) {
            return;
        }

        // メディアアップローダー用のスクリプト
        wp_enqueue_media();

        // 管理画面用のCSS
        wp_enqueue_style(
            'modern-banner-admin',
            MODERN_BANNER_URL . 'css/style.css',
            array(),
            MODERN_BANNER_VERSION
        );

        // 管理画面用のJavaScript
        wp_enqueue_script(
            'modern-banner-admin',
            MODERN_BANNER_URL . 'js/banner-admin.js',
            array(),
            MODERN_BANNER_VERSION,
            true
        );
    }

    /**
     * 管理画面の表示
     */
    public function render_admin_page() {
        $options = get_option('modern_banner_options', array());
        ?>
        <div class="wrap">
            <h1>バナー設定</h1>

            <?php if (isset($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible">
                    <p>設定を保存しました。</p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('modern_banner_nonce', 'modern_banner_nonce'); ?>
                <input type="hidden" name="action" value="save_modern_banner">

                <table class="form-table">
                    <!-- リンクURL設定 -->
                    <tr>
                        <th scope="row">リンクURL</th>
                        <td>
                            <input type="url" name="link_url" class="regular-text" 
                                   value="<?php echo esc_attr($options['link_url'] ?? ''); ?>">
                        </td>
                    </tr>

                    <!-- PC用バナー設定 -->
                    <tr>
                        <th scope="row">PC用バナー設定</th>
                        <td>
                            <div class="image-upload-wrap">
                                <input type="hidden" name="pc_image" class="image-url" 
                                       value="<?php echo esc_attr($options['pc_image'] ?? ''); ?>">
                                <button type="button" class="button image-upload-button">画像を選択</button>
                                <?php if (!empty($options['pc_image'])) : ?>
                                    <img src="<?php echo esc_url($options['pc_image']); ?>" 
                                         alt="" class="image-preview" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                            <p class="description">推奨サイズ: 横幅250px</p>

                            <div class="image-settings">
                                <p>
                                    <label>代替テキスト（alt属性）：</label><br>
                                    <input type="text" name="pc_image_alt" class="regular-text"
                                           value="<?php echo esc_attr($options['pc_image_alt'] ?? ''); ?>"
                                           placeholder="代替テキストを入力">
                                </p>

                                <p>
                                    <label>読み込み方式（loading属性）：</label><br>
                                    <select name="pc_image_loading" class="regular-text">
                                        <option value="lazy" <?php selected($options['pc_image_loading'] ?? 'lazy', 'lazy'); ?>>
                                            lazy - 遅延読み込み
                                        </option>
                                        <option value="eager" <?php selected($options['pc_image_loading'] ?? 'lazy', 'eager'); ?>>
                                            eager - 即時読み込み
                                        </option>
                                    </select>
                                </p>

                                <p>
                                    <label>読み込み優先度（fetchpriority属性）：</label><br>
                                    <select name="pc_image_fetchpriority" class="regular-text">
                                        <option value="auto" <?php selected($options['pc_image_fetchpriority'] ?? 'auto', 'auto'); ?>>
                                            auto - 自動
                                        </option>
                                        <option value="high" <?php selected($options['pc_image_fetchpriority'] ?? 'auto', 'high'); ?>>
                                            high - 高優先度
                                        </option>
                                        <option value="low" <?php selected($options['pc_image_fetchpriority'] ?? 'auto', 'low'); ?>>
                                            low - 低優先度
                                        </option>
                                    </select>
                                </p>

                                <p>
                                    <label>カスタム属性：</label><br>
                                    <textarea name="pc_image_custom_attributes" class="large-text code" rows="3"
                                              placeholder="カスタム属性を入力（例：data-src='example.jpg' crossorigin='anonymous'）"
                                    ><?php echo esc_textarea($options['pc_image_custom_attributes'] ?? ''); ?></textarea>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- スマートフォン用バナー設定 -->
                    <tr>
                        <th scope="row">スマートフォン用バナー設定</th>
                        <td>
                            <div class="image-upload-wrap">
                                <input type="hidden" name="sp_image" class="image-url" 
                                       value="<?php echo esc_attr($options['sp_image'] ?? ''); ?>">
                                <button type="button" class="button image-upload-button">画像を選択</button>
                                <?php if (!empty($options['sp_image'])) : ?>
                                    <img src="<?php echo esc_url($options['sp_image']); ?>" 
                                         alt="" class="image-preview" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                            <p class="description">推奨サイズ: 横幅100%</p>

                            <div class="image-settings">
                                <p>
                                    <label>代替テキスト（alt属性）：</label><br>
                                    <input type="text" name="sp_image_alt" class="regular-text"
                                           value="<?php echo esc_attr($options['sp_image_alt'] ?? ''); ?>"
                                           placeholder="代替テキストを入力">
                                </p>

                                <p>
                                    <label>読み込み方式（loading属性）：</label><br>
                                    <select name="sp_image_loading" class="regular-text">
                                        <option value="lazy" <?php selected($options['sp_image_loading'] ?? 'lazy', 'lazy'); ?>>
                                            lazy - 遅延読み込み
                                        </option>
                                        <option value="eager" <?php selected($options['sp_image_loading'] ?? 'lazy', 'eager'); ?>>
                                            eager - 即時読み込み
                                        </option>
                                    </select>
                                </p>

                                <p>
                                    <label>読み込み優先度（fetchpriority属性）：</label><br>
                                    <select name="sp_image_fetchpriority" class="regular-text">
                                        <option value="auto" <?php selected($options['sp_image_fetchpriority'] ?? 'auto', 'auto'); ?>>
                                            auto - 自動
                                        </option>
                                        <option value="high" <?php selected($options['sp_image_fetchpriority'] ?? 'auto', 'high'); ?>>
                                            high - 高優先度
                                        </option>
                                        <option value="low" <?php selected($options['sp_image_fetchpriority'] ?? 'auto', 'low'); ?>>
                                            low - 低優先度
                                        </option>
                                    </select>
                                </p>

                                <p>
                                    <label>カスタム属性：</label><br>
                                    <textarea name="sp_image_custom_attributes" class="large-text code" rows="3"
                                              placeholder="カスタム属性を入力（例：data-src='example.jpg' crossorigin='anonymous'）"
                                    ><?php echo esc_textarea($options['sp_image_custom_attributes'] ?? ''); ?></textarea>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- カスタムCSS設定 -->
                    <tr>
                        <th scope="row">カスタムCSS</th>
                        <td>
                            <textarea name="custom_css" rows="10" class="large-text code"><?php 
                                echo esc_textarea($options['custom_css'] ?? ''); 
                            ?></textarea>
                            <p class="description">バナーのアニメーションやスタイルをカスタマイズできます。</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * 設定の保存
     */
    public function save_settings() {
        if (!current_user_can('manage_options')) {
            wp_die('権限がありません');
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

        update_option('modern_banner_options', $options);

        wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=modern-banner')));
        exit;
    }
}