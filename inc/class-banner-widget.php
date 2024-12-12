<?php
/**
 * バナー表示用のウィジェットクラス
 */
class Modern_Banner_Widget extends WP_Widget {
    /**
     * コンストラクタ
     */
    public function __construct() {
        parent::__construct(
            'modern-banner-widget',
            'モダンバナー',
            array(
                'description' => 'バナーを表示するウィジェットです。',
                'classname' => 'widget_modern_banner',
            )
        );
    }

    /**
     * ウィジェットのフロントエンド表示
     */
    public function widget($args, $instance) {
        $banner_id = !empty($instance['banner_id']) ? intval($instance['banner_id']) : 0;
        if (!$banner_id) {
            return;
        }

        // 現在のページIDを取得
        $current_page_id = get_the_ID();
        
        // バナー情報を取得
        $banner = Banner_DB::get_banner($banner_id);
        if (!$banner) {
            return;
        }

        // 除外ページのチェック
        if (!empty($banner->exclude_pages)) {
            $exclude_pages = array_map('trim', explode(',', $banner->exclude_pages));
            if (in_array($current_page_id, $exclude_pages)) {
                return;
            }
        }

        // ウィジェットの前後に出力される HTMLを表示
        echo $args['before_widget'];
        
        // ショートコードを使用してバナーを表示
        echo do_shortcode("[modern_banner id=\"$banner_id\"]");
        
        echo $args['after_widget'];
    }

    /**
     * バックエンド管理画面でのウィジェット設定フォーム
     */
    public function form($instance) {
        $banner_id = isset($instance['banner_id']) ? intval($instance['banner_id']) : 0;
        
        // 利用可能なバナーを取得
        $banners = Banner_DB::get_banners();
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('banner_id'); ?>">表示するバナー:</label>
            <select id="<?php echo $this->get_field_id('banner_id'); ?>" 
                    name="<?php echo $this->get_field_name('banner_id'); ?>" 
                    class="widefat">
                <option value="">選択してください</option>
                <?php foreach ($banners as $banner) : ?>
                    <option value="<?php echo $banner->id; ?>" <?php selected($banner_id, $banner->id); ?>>
                        <?php echo esc_html($banner->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="description">
            選択したバナーがウィジェットエリアに表示されます。<br>
            除外ページとして設定されているページでは表示されません。
        </p>
        <?php
    }

    /**
     * ウィジェット設定の保存処理
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['banner_id'] = !empty($new_instance['banner_id']) ? 
            intval($new_instance['banner_id']) : 0;
        return $instance;
    }
}