<?php
/**
 * バナー管理画面用クラス
 */
class Banner_Admin {
    /**
     * コンストラクタ
     */
    public function __construct() {
        // 管理メニューの追加
        add_action('admin_menu', array($this, 'add_admin_menu'));
        // 管理画面用スクリプトの読み込み
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        // 管理画面の処理
        add_action('admin_post_save_modern_banner', array($this, 'save_banner'));
        add_action('admin_post_delete_modern_banner', array($this, 'delete_banner'));
    }

    /**
     * 管理メニューの追加
     */
    public function add_admin_menu() {
        add_menu_page(
            'バナー管理',
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
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'new':
            case 'edit':
                $this->render_banner_form();
                break;
            default:
                $this->render_banner_list();
                break;
        }
    }

    /**
     * バナー一覧の表示
     */
    private function render_banner_list() {
        $banners = Banner_DB::get_banners();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">バナー管理</h1>
            <a href="<?php echo esc_url(admin_url('admin.php?page=modern-banner&action=new')); ?>" class="page-title-action">新規追加</a>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>タイトル</th>
                        <th>ショートコード</th>
                        <th>PC用バナー</th>
                        <th>スマホ用バナー</th>
                        <th>ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $banner): ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=modern-banner&action=edit&id=' . $banner->id)); ?>">
                                        <?php echo esc_html($banner->title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <code>[modern_banner id="<?php echo $banner->id; ?>"]</code>
                            </td>
                            <td>
                                <?php if ($banner->pc_image): ?>
                                    <img src="<?php echo esc_url($banner->pc_image); ?>" alt="" style="max-width: 100px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($banner->sp_image): ?>
                                    <img src="<?php echo esc_url($banner->sp_image); ?>" alt="" style="max-width: 100px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $banner->id; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * バナー編集フォームの表示
     */
    private function render_banner_form() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $banner = $id ? Banner_DB::get_banner($id) : null;
        ?>
        <div class="wrap">
            <h1><?php echo $id ? 'バナーを編集' : '新規バナーを追加'; ?></h1>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('save_modern_banner', 'modern_banner_nonce'); ?>
                <input type="hidden" name="action" value="save_modern_banner">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                <?php endif; ?>

                <table class="form-table">
                    <!-- タイトル設定 -->
                    <tr>
                        <th scope="row">タイトル</th>
                        <td>
                            <input type="text" name="title" class="regular-text" 
                                   value="<?php echo esc_attr($banner->title ?? ''); ?>" required>
                        </td>
                    </tr>

                    <!-- リンクURL設定 -->
                    <tr>
                        <th scope="row">リンクURL</th>
                        <td>
                            <input type="url" name="link_url" class="regular-text" 
                                   value="<?php echo esc_attr($banner->link_url ?? ''); ?>" required>
                        </td>
                    </tr>

                    <!-- PC用バナー設定 -->
                    <tr>
                        <th scope="row">PC用バナー設定</th>
                        <td>
                            <div class="image-upload-wrap">
                                <input type="hidden" name="pc_image" class="image-url" 
                                       value="<?php echo esc_attr($banner->pc_image ?? ''); ?>">
                                <button type="button" class="button image-upload-button">画像を選択</button>
                                <?php if (!empty($banner->pc_image)): ?>
                                    <img src="<?php echo esc_url($banner->pc_image); ?>" 
                                         alt="" class="image-preview" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                            <p>
                                <label>横幅設定：</label>
                                <input type="text" name="pc_width" 
                                       value="<?php echo esc_attr($banner->pc_width ?? '250px'); ?>" 
                                       placeholder="例: 250px">
                            </p>
                            <p>
                                <label>代替テキスト：</label>
                                <input type="text" name="pc_image_alt" class="regular-text"
                                       value="<?php echo esc_attr($banner->pc_image_alt ?? ''); ?>">
                            </p>
                        </td>
                    </tr>

                    <!-- スマホ用バナー設定 -->
                    <tr>
                        <th scope="row">スマホ用バナー設定</th>
                        <td>
                            <div class="image-upload-wrap">
                                <input type="hidden" name="sp_image" class="image-url" 
                                       value="<?php echo esc_attr($banner->sp_image ?? ''); ?>">
                                <button type="button" class="button image-upload-button">画像を選択</button>
                                <?php if (!empty($banner->sp_image)): ?>
                                    <img src="<?php echo esc_url($banner->sp_image); ?>" 
                                         alt="" class="image-preview" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                            <p>
                                <label>横幅設定：</label>
                                <input type="text" name="sp_width" 
                                       value="<?php echo esc_attr($banner->sp_width ?? '100%'); ?>" 
                                       placeholder="例: 100%">
                            </p>
                            <p>
                                <label>代替テキスト：</label>
                                <input type="text" name="sp_image_alt" class="regular-text"
                                       value="<?php echo esc_attr($banner->sp_image_alt ?? ''); ?>">
                            </p>
                        </td>
                    </tr>

                    <!-- 除外ページ設定 -->
                    <tr>
                        <th scope="row">除外するページ</th>
                        <td>
                            <input type="text" name="exclude_pages" class="regular-text" 
                                   value="<?php echo esc_attr($banner->exclude_pages ?? ''); ?>"
                                   placeholder="例: 1,2,3">
                            <p class="description">除外するページのIDをカンマ区切りで入力してください。</p>
                        </td>
                    </tr>

                    <!-- カスタムCSS設定 -->
                    <tr>
                        <th scope="row">カスタムCSS</th>
                        <td>
                            <textarea name="custom_css" rows="10" class="large-text code"><?php 
                                echo esc_textarea($banner->custom_css ?? ''); 
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
     * バナーの保存処理
     */
    public function save_banner() {
        if (!current_user_can('manage_options')) {
            wp_die('権限がありません');
        }

        check_admin_referer('save_modern_banner', 'modern_banner_nonce');

        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'link_url' => esc_url_raw($_POST['link_url']),
            'pc_image' => esc_url_raw($_POST['pc_image']),
            'pc_width' => sanitize_text_field($_POST['pc_width']),
            'pc_image_alt' => sanitize_text_field($_POST['pc_image_alt']),
            'sp_image' => esc_url_raw($_POST['sp_image']),
            'sp_width' => sanitize_text_field($_POST['sp_width']),
            'sp_image_alt' => sanitize_text_field($_POST['sp_image_alt']),
            'exclude_pages' => sanitize_text_field($_POST['exclude_pages']),
            'custom_css' => sanitize_textarea_field($_POST['custom_css']),
        );

        if (isset($_POST['id'])) {
            $data['id'] = intval($_POST['id']);
        }

        $banner_id = Banner_DB::save_banner($data);

        wp_redirect(add_query_arg(
            array(
                'page' => 'modern-banner',
                'message' => 'saved'
            ),
            admin_url('admin.php')
        ));
        exit;
    }

    /**
     * バナーの削除処理
     */
    public function delete_banner() {
        if (!current_user_can('manage_options')) {
            wp_die('権限がありません');
        }

        check_admin_referer('delete_modern_banner');

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id) {
            Banner_DB::delete_banner($id);
        }

        wp_redirect(add_query_arg(
            array(
                'page' => 'modern-banner',
                'message' => 'deleted'
            ),
            admin_url('admin.php')
        ));
        exit;
    }
}