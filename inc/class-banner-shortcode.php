<?php
/**
 * バナーのショートコード機能を管理するクラス
 */
class Banner_Shortcode {
    /**
     * ショートコードを登録
     */
    public static function register() {
        add_shortcode('modern_banner', array(__CLASS__, 'render'));
    }

    /**
     * バナーのHTML出力
     *
     * @param array $atts ショートコードの属性
     * @return string バナーのHTML
     */
    public static function render($atts) {
        // 属性を解析
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        // IDのバリデーション
        $id = intval($atts['id']);
        if (!$id) {
            return '';
        }

        // バナー情報を取得
        $banner = Banner_DB::get_banner($id);
        if (!$banner) {
            return '';
        }

        // 現在のページIDを取得
        $current_page_id = get_the_ID();

        // 除外ページのチェック
        if (!empty($banner->exclude_pages)) {
            $exclude_pages = array_map('trim', explode(',', $banner->exclude_pages));
            if (in_array($current_page_id, $exclude_pages)) {
                return '';
            }
        }

        // カスタムCSSの出力
        $custom_css = '';
        if (!empty($banner->custom_css)) {
            $custom_css = '<style>' . wp_strip_all_tags($banner->custom_css) . '</style>';
        }

        // バナーのHTML生成
        $html = $custom_css;
        $html .= '<div class="modern-banner" data-id="' . esc_attr($id) . '">';
        $html .= '<a href="' . esc_url($banner->link_url) . '" class="modern-banner-link">';
        
        // PC用バナー
        if (!empty($banner->pc_image)) {
            $pc_style = !empty($banner->pc_width) ? sprintf('max-width: %s;', esc_attr($banner->pc_width)) : '';
            $html .= sprintf(
                '<img src="%s" alt="%s" class="modern-banner-image-pc" loading="%s" fetchpriority="%s" style="%s">',
                esc_url($banner->pc_image),
                esc_attr($banner->pc_image_alt),
                esc_attr($banner->pc_image_loading ?? 'lazy'),
                esc_attr($banner->pc_image_fetchpriority ?? 'auto'),
                $pc_style
            );
        }
        
        // スマホ用バナー
        if (!empty($banner->sp_image)) {
            $sp_style = !empty($banner->sp_width) ? sprintf('max-width: %s;', esc_attr($banner->sp_width)) : '';
            $html .= sprintf(
                '<img src="%s" alt="%s" class="modern-banner-image-sp" loading="%s" fetchpriority="%s" style="%s">',
                esc_url($banner->sp_image),
                esc_attr($banner->sp_image_alt),
                esc_attr($banner->sp_image_loading ?? 'lazy'),
                esc_attr($banner->sp_image_fetchpriority ?? 'auto'),
                $sp_style
            );
        }
        
        $html .= '</a>';
        
        // 閉じるボタン
        $html .= '<button class="modern-banner-close" aria-label="バナーを閉じる">×</button>';
        $html .= '</div>';

        return $html;
    }
}