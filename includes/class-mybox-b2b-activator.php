<?php

/**
 * Fired during plugin activation
 *
 * @link       one2tek.com
 * @since      1.3.0
 *
 * @package    Mybox_B2b
 * @subpackage Mybox_B2b/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mybox_B2b
 * @subpackage Mybox_B2b/includes
 * @author     One2tek <admin@one2tek.com>
 */
class Mybox_B2b_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'mybox_settings';
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			xa varchar(50) COLLATE utf8mb4_unicode_ci NULL,
			status varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			ordering_number varchar(50) COLLATE utf8mb4_unicode_ci NULL,
			first_name varchar(20) COLLATE utf8mb4_unicode_ci NULL,
			last_name varchar(20) COLLATE utf8mb4_unicode_ci NULL,
			weight int(11) NULL,
			fragile varchar(5) COLLATE utf8mb4_unicode_ci NULL,
			category_box_id int(1) NULL,
			email varchar(50) COLLATE utf8mb4_unicode_ci NULL,

			zone_id int(11) NULL,
			zone varchar(244) COLLATE utf8mb4_unicode_ci NULL,
			
			route varchar(244) COLLATE utf8mb4_unicode_ci NULL,
			area varchar(244) COLLATE utf8mb4_unicode_ci NULL,
			b2b varchar(244) COLLATE utf8mb4_unicode_ci NULL,

			address varchar(244) COLLATE utf8mb4_unicode_ci NULL,
			reference_of_address varchar(244) COLLATE utf8mb4_unicode_ci NULL,
			phone_number_1 varchar(20) COLLATE utf8mb4_unicode_ci NULL,
			phone_number_2 varchar(20) COLLATE utf8mb4_unicode_ci NULL,
			is_production tinyint(1) DEFAULT 0,
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

}
