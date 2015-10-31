<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Customers_List extends WP_List_Table {
	/**
	 * Constructor.
	 *
	 * @param mixed
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Customer', 'ac' ),
			'plural'   => __( 'Customers', 'ac' ),
			'ajax'     => false
		) );
	}

	/**
	 * Retrieve customer data from the database.
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public static function get_customer( $id=0 ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}customers WHERE id = %d", $id ) );
	}

	/**
	 * Retrieve customers data from the database.
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}customers";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE name LIKE "%' . esc_sql( $_REQUEST['s'] ) . '%"' ;
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer id
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}customers",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}customers";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE name LIKE "%' . esc_sql( $_REQUEST['s'] ) . '%"' ;
		}

		return $wpdb->get_var( $sql );
	}

	/**
	 * Text displayed when no customer data is available.
	 *
	 * @return mixed
	 */
	public function no_items() {
		_e( 'No customers avaliable.', 'ac' );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'address':
			case 'city':
				return $item[ $column_name ];
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Render the bulk edit checkbox.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
	 * Method for name column.
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce( 'ac_delete_customer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit'   => sprintf( '<a href="?page=%s&action=%s&id=%d">Edit</a>',  esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return sprintf( '<a href="?page=%s&action=%s&id=%d">%s</a>',  esc_attr( $_REQUEST['page'] ), 'view', absint( $item['id'] ), $title ) . $this->row_actions( $actions );
	}

	/**
	 *  Associative array of columns.
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'ac' ),
			'address' => __( 'Address', 'ac' ),
			'city'    => __( 'City', 'ac' )
		];

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'city' => array( 'city', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @return mixed
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$this->items = self::get_customers( $per_page, $current_page );
	}

	/**
	 * Handles bulk action and delete.
	 *
	 * @return mixed
	 */
	public function process_bulk_action() {
		$page_url = menu_page_url( 'ac-wp-list-table', false );

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'ac_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_customer( absint( $_GET['id'] ) );

				// Redirect
				$query = array( 'message' => 'deleted');
				$redirect_to = add_query_arg( $query, $page_url );
				wp_redirect( $redirect_to );
				exit;
			}
		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record ids and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );
			}

			// Redirect
			$query = array( 'message' => 'deleted');
			$redirect_to = add_query_arg( $query, $page_url );
			wp_redirect( $redirect_to );
			exit;
		}
	}

	/**
	 * Handles form data when submitted.
	 *
	 * @return mixed
	 */
	public function process_form_submit() {
        if ( ! isset( $_POST['submit_customer'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'ac_new_customer' ) ) {
            die( 'Go get a life script kiddies' );
        }

        if ( ! current_user_can( 'read' ) ) {
            wp_die( __( 'Permission Denied!', 'ac' ) );
        }

        $errors		= array();
        $page_url 	= menu_page_url( 'ac-wp-list-table', false );
        $field_id 	= isset( $_POST['field_id'] ) ? absint( $_POST['field_id'] ) : 0;

        $name 		= isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $address 	= isset( $_POST['address'] ) ? wp_kses_post( $_POST['address'] ) : '';
        $city 		= isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';

        // some basic validation
        if ( ! $name ) {
            $errors[] = __( 'Error: Name is required', 'ac' );
        }

        // bail out if error found
        if ( $errors ) {
            $first_error = reset( $errors );
			$query = array( 'error' => $first_error );
			$redirect_to = add_query_arg( $query, $page_url );
			// Redirect
			wp_redirect( $redirect_to );
			exit;
        }

        $fields = array(
            'name' 		=> $name,
            'address' 	=> $address,
            'city' 		=> $city,
        );

        // New or edit?
        if ( ! $field_id ) {
            $insert_id = $this->insert_customer( $fields );
        } else {
            $fields['id'] = $field_id;

            $insert_id = $this->insert_customer( $fields );
        }

        if ( is_wp_error( $insert_id ) ) {
            $redirect_to = add_query_arg( array( 'message' => 'error' ), $page_url );
        } else {
            $redirect_to = add_query_arg( array( 'message' => 'success' ), $page_url );
        }

		// Redirect
		wp_redirect( $redirect_to );
		exit;
	}

	/**
	 * Insert a new customer.
	 *
	 * @param boolean
	 */
	public function insert_customer( $args = array() ) {
	    global $wpdb;

	    $defaults = array(
	        'id'		=> null,
	        'name'		=> '',
	        'address'	=> '',
	        'city'		=> '',
	    );

	    $args       = wp_parse_args( $args, $defaults );
	    $table_name = $wpdb->prefix . 'customers';

	    // some basic validation
	    if ( empty( $args['name'] ) ) {
	        return new WP_Error( 'no-name', __( 'No Name provided.', 'ac' ) );
	    }

	    // remove row id to determine if new or update
	    $row_id = (int) $args['id'];
	    unset( $args['id'] );

	    if ( ! $row_id ) {
	        $args['date'] = current_time( 'mysql' );

	        // insert a new
	        if ( $wpdb->insert( $table_name, $args ) ) {
	            return $wpdb->insert_id;
	        }
	    } else {
	        // do update method here
	        if ( $wpdb->update( $table_name, $args, array( 'id' => $row_id ) ) ) {
	            return $row_id;
	        }
	    }

	    return false;
	}
}
