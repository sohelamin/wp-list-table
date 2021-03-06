<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Menu
 */
class Customer_Admin_Menu {
	/**
	 * WP_List_Table object.
	 *
	 * @var object
	 */
	public $customer_obj;

	/**
	 * Constructor.
	 *
	 * @param mixed
	 */
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}

	/**
	 * Setting screen option.
	 *
	 * @param  string $status, $option, $value
	 *
	 * @return string
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Registering plugin menu.
	 *
	 * @return mixed
	 */
	public function plugin_menu() {
		$hook = add_menu_page(
			'AppzCoder WP_List_Table',
			'AC WP_List_Table',
			'manage_options',
			'ac-wp-list-table',
			array( $this, 'plugin_settings_page' ),
			'dashicons-groups', null
		);

		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Plugin settings page.
	 *
	 * @return mixed
	 */
	public function plugin_settings_page() {
        $action 	= isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        $id			= isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $template 	= '';

        switch ($action) {
            case 'view':
                $template = dirname( __FILE__ ) . '/views/customer-single.php';
                break;

            case 'edit':
            	$this->customer_obj->process_form_submit();
                $template = dirname( __FILE__ ) . '/views/customer-edit.php';
                break;

            case 'new':
            	$this->customer_obj->process_form_submit();
                $template = dirname( __FILE__ ) . '/views/customer-new.php';
                break;

            default:
                $template = dirname( __FILE__ ) . '/views/customer-list.php';
                break;
        }

        if ( file_exists( $template ) ) {
			include( $template );
        }
	}

	/**
	 * Screen options.
	 *
	 * @return mixed
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		);

		add_screen_option( $option, $args );

		include dirname( __FILE__ ) . '/class-customers-list.php';
		$this->customer_obj = new Customers_List();
	}
}
