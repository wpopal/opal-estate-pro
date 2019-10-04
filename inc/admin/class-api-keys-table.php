<?php
/**
 * API Key Table Class
 *
 * @package     Opalestate
 * @subpackage  Admin/Tools/APIKeys
 * @copyright   Copyright (c) 2019, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Opalestate_API_Keys_Table Class
 *
 * Renders the API Keys table
 *
 * @since 1.1
 */
class Opalestate_API_Keys_Table extends WP_List_Table {

	/**
	 * @var int Number of items per page
	 * @since 1.1
	 */
	public $per_page = 30;

	/**
	 * @var object Query results
	 * @since 1.1
	 */
	private $keys;

	/**
	 * Get things started
	 *
	 * @since 1.1
	 * @see   WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( [
			'singular' => esc_html__( 'API Key', 'opalestate-pro' ),     // Singular name of the listed records
			'plural'   => esc_html__( 'API Keys', 'opalestate-pro' ),    // Plural name of the listed records
			'ajax'     => false                       // Does this table support ajax?
		] );

		$this->query();
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @param array  $item        Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since  1.1
	 *
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Displays the public key rows
	 *
	 * @access public
	 * @param array  $item        Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since  1.1
	 *
	 */
	public function column_key( $item ) {
		return '<input onClick="this.setSelectionRange(0, this.value.length)" readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item['key'] ) . '"/>';
	}

	/**
	 * Displays the token rows
	 *
	 * @access public
	 * @param array  $item        Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since  1.1
	 *
	 */
	public function column_token( $item ) {
		return '<input onClick="this.setSelectionRange(0, this.value.length)" readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item['token'] ) . '"/>';
	}

	/**
	 * Displays the secret key rows
	 *
	 * @access public
	 * @param array  $item        Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since  1.1
	 *
	 */
	public function column_secret( $item ) {
		return '<input onClick="this.setSelectionRange(0, this.value.length)" readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item['secret'] ) . '"/>';
	}

	/**
	 * Renders the column for the user field
	 *
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function column_user( $item ) {

		$actions = [];

		if ( apply_filters( 'opalestate_api_log_requests', true ) ) {
			$actions['view'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( [
					'view'      => 'api_requests',
					'post_type' => 'opalestate_forms',
					'page'      => 'opalestate-reports',
					'tab'       => 'logs',
					's'         => $item['email'],
				], 'edit.php' ) ),
				esc_html__( 'View API Log', 'opalestate-pro' )
			);
		}

		$actions['reissue'] = sprintf(
			'<a href="%s" class="opalestate-regenerate-api-key">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( [
				'user_id'                => $item['id'],
				'opalestate_action'      => 'process_api_key',
				'opalestate_api_process' => 'regenerate',
			] ), 'opalestate-api-nonce' ) ),
			esc_html__( 'Reissue', 'opalestate-pro' )
		);
		$actions['revoke']  = sprintf(
			'<a href="%s" class="opalestate-revoke-api-key opalestate-delete">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( [
				'user_id'                => $item['id'],
				'opalestate_action'      => 'process_api_key',
				'opalestate_api_process' => 'revoke',
			] ), 'opalestate-api-nonce' ) ),
			esc_html__( 'Revoke', 'opalestate-pro' )
		);

		$actions = apply_filters( 'opalestate_api_row_actions', array_filter( $actions ) );

		return sprintf( '%1$s %2$s', $item['user'], $this->row_actions( $actions ) );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @return array $columns Array of all the list table columns
	 * @since  1.1
	 */
	public function get_columns() {
		$columns = [
			'user'   => esc_html__( 'Username', 'opalestate-pro' ),
			'key'    => esc_html__( 'Public Key', 'opalestate-pro' ),
			'token'  => esc_html__( 'Token', 'opalestate-pro' ),
			'secret' => esc_html__( 'Secret Key', 'opalestate-pro' ),
		];

		return $columns;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 * @since  3.1.0
	 * @access protected
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
            </div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

            <br class="clear"/>
        </div>
		<?php
	}

	/**
	 * Display the key generation form
	 *
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place
		static $opalestate_api_is_bottom;

		if ( $opalestate_api_is_bottom ) {
			return;
		}
		?>
        <input type="hidden" name="opalestate_action" value="process_api_key"/>
        <input type="hidden" name="opalestate_api_process" value="generate"/>
		<?php wp_nonce_field( 'opalestate-api-nonce' ); ?>
		<?php echo OpalEstate()->html->ajax_user_search( [
			'name' => esc_html__( 'User', 'opalestate-pro' ),
		] ); ?>
		<?php submit_button( esc_html__( 'Generate New API Keys', 'opalestate-pro' ), 'secondary', 'submit', false ); ?>
		<?php
		$opalestate_api_is_bottom = true;
	}

	/**
	 * Retrieve the current page number
	 *
	 * @access public
	 * @return int Current page number
	 * @since  1.1
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Performs the key query
	 *
	 * @access public
	 * @return array
	 * @since  1.1
	 */
	public function query() {
		$users = get_users( [
			'meta_value' => 'opalestate_user_secret_key',
			'number'     => $this->per_page,
			'offset'     => $this->per_page * ( $this->get_paged() - 1 ),
		] );

		$keys = [];

		foreach ( $users as $user ) {
			$keys[ $user->ID ]['id']     = $user->ID;
			$keys[ $user->ID ]['email']  = $user->user_email;
			$keys[ $user->ID ]['user']   = '<a href="' . add_query_arg( 'user_id', $user->ID, 'user-edit.php' ) . '"><strong>' . $user->user_login . '</strong></a>';
			$keys[ $user->ID ]['key']    = OpalEstate()->api_admin->get_user_public_key( $user->ID );
			$keys[ $user->ID ]['secret'] = OpalEstate()->api_admin->get_user_secret_key( $user->ID );
			$keys[ $user->ID ]['token']  = OpalEstate()->api_admin->get_token( $user->ID );
		}

		return $keys;
	}


	/**
	 * Retrieve count of total users with keys
	 *
	 * @access public
	 * @return int
	 * @since  1.1
	 */
	public function total_items() {
		global $wpdb;

		if ( ! get_transient( 'opalestate_total_api_keys' ) ) {
			$total_items = $wpdb->get_var( "SELECT count(user_id) FROM $wpdb->usermeta WHERE meta_value='opalestate_user_secret_key'" );

			set_transient( 'opalestate_total_api_keys', $total_items, 60 * 60 );
		}

		return get_transient( 'opalestate_total_api_keys' );
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function prepare_items() {
		$columns = $this->get_columns();

		$hidden   = []; // No hidden columns
		$sortable = []; // Not sortable... for now

		$this->_column_headers = [ $columns, $hidden, $sortable, 'id' ];

		$data = $this->query();

		$total_items = $this->total_items();

		$this->items = $data;

		$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			]
		);
	}
}
