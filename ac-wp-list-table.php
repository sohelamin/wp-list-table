<?php
/*
Plugin Name: WP List Table Example
Plugin URI: http://appzcoder.com
Description: WP_List_Table Class Example
Version: 1.0
Author: Sohel Amin
Author URI:  http://sohelamin.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_WP_List_Table {
	/**
	 * Instance of this class.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Constructor.
	 *
	 * @param mixed
	 */
	public function __construct() {
		if ( is_admin() ) {
			include dirname( __FILE__ ) . '/includes/customer/class-customer-admin-menu.php';
			new Customer_Admin_Menu();
		}

		register_activation_hook( __FILE__, array( $this, 'create_table' ) );
	}

	/**
	 * Singleton instance.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create relevant table.
	 *
	 * @return mixed
	 */
	public function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'customers';

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(50) DEFAULT '' NOT NULL,
			address text NOT NULL,
			city varchar(50) DEFAULT '' NOT NULL,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}

AC_WP_List_Table::get_instance();
