<?php
/**
 * データベース処理用クラス
 */
class Banner_DB {
    /**
     * テーブル名を取得
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'modern_banners';
    }

    /**
     * テーブルを作成
     */
    public static function create_table() {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) DEFAULT NULL,
            link_url text DEFAULT NULL,
            pc_image text DEFAULT NULL,
            pc_width varchar(50) DEFAULT '250px',
            pc_image_alt text DEFAULT NULL,
            pc_image_loading varchar(20) DEFAULT 'lazy',
            pc_image_fetchpriority varchar(20) DEFAULT 'auto',
            pc_custom_attributes text DEFAULT NULL,
            sp_image text DEFAULT NULL,
            sp_width varchar(50) DEFAULT '100%',
            sp_image_alt text DEFAULT NULL,
            sp_image_loading varchar(20) DEFAULT 'lazy',
            sp_image_fetchpriority varchar(20) DEFAULT 'auto',
            sp_custom_attributes text DEFAULT NULL,
            exclude_pages text DEFAULT NULL,
            custom_css text DEFAULT NULL,
            status varchar(20) DEFAULT 'publish',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * バナーを取得
     */
    public static function get_banner($id) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            )
        );
    }

    /**
     * 全バナーを取得
     */
    public static function get_banners($args = array()) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $defaults = array(
            'status' => 'publish',
            'orderby' => 'id',
            'order' => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        if ($args['status']) {
            $where[] = $wpdb->prepare('status = %s', $args['status']);
        }
        
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $order_clause = "ORDER BY {$args['orderby']} {$args['order']}";
        
        return $wpdb->get_results(
            "SELECT * FROM $table_name $where_clause $order_clause"
        );
    }

    /**
     * バナーを保存
     */
    public static function save_banner($data) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $defaults = array(
            'title' => '',
            'link_url' => '',
            'pc_image' => '',
            'pc_width' => '250px',
            'sp_image' => '',
            'sp_width' => '100%',
            'status' => 'publish',
        );
        
        $data = wp_parse_args($data, $defaults);
        
        if (isset($data['id'])) {
            // 更新
            $wpdb->update(
                $table_name,
                $data,
                array('id' => $data['id']),
                array('%s', '%s'),
                array('%d')
            );
            return $data['id'];
        } else {
            // 新規追加
            $wpdb->insert(
                $table_name,
                $data,
                array('%s', '%s')
            );
            return $wpdb->insert_id;
        }
    }

    /**
     * バナーを削除
     */
    public static function delete_banner($id) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }
}