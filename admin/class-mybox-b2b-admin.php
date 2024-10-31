<?php
$p = 0;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link one2tek.com
 * @since 2.10.7
 *
 * @package Mybox_B2b
 * @subpackage Mybox_B2b/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package Mybox_B2b
 * @subpackage Mybox_B2b/admin
 * @author One2tek <admin@one2tek.com>
 */


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_Native_Table extends WP_List_Table {
	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'XA', 'sp' ), //singular name of the listed records
			'plural'   => __( 'XAs', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );
	}

	public function extra_tablenav( $which ) {
		/**
		 * $which will return either 'top' or 'bottom'
		 */
	}

	public function get_columns() {
		$columns = [
			'name'            => __( 'Full Name', 'sp' ),
			'xa'              => __( 'XA Number', 'sp' ),
			'ordering_number' => __( 'Ordering Number', 'sp' ),
			'email'           => __( 'Email', 'sp' ),
			'status'          => __( 'Status', 'sp' ),
			'brc_or_button'   => __( '<span class="dashicons dashicons-media-text"></span>', 'sp' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'xa'              => array( 'xa', true ),
			'ordering_number' => array( 'ordering_number', true ),
			'first_name'      => __( 'first_name', true ),
			'status'          => array( 'status', true )
		);

		return $sortable_columns;
	}

	function remove_get_param( $param ) {
		$url             = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]";
		$url_second_part = "$_SERVER[REQUEST_URI]";
		$parsed          = parse_url( $url . $url_second_part );
		$path            = $parsed['path'];
		unset( $_GET[ $param ] );
		$url_parsed = $url . $path . '?' . http_build_query( $_GET );

		return $url_parsed;
	}

	function prepare_items() {
		if ( isset( $_POST["s"] ) ) {
			$url_parsed = $this->remove_get_param( "s" );
			$url_parsed = $this->remove_get_param( "paged" );

			$s = strlen( $_POST['s'] ) > 0 ? "&s={".sanitize_text_field($_POST['s'])."}" : "";
			header( "Location: {$url_parsed}{$s}" );
			exit;

		}
		global $wpdb;

		/**
		 * First, lets decide how many records per page to show
		 * if not specify then will take default as n row
		 */
		$per_page = $this->get_items_per_page( 'url_per_page', 20 );
		$columns  = $this->get_columns();
		$hidden   = array();

		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();

		$data = $this->get_name_url();


		function usort_reorder( $a, $b ) {

			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? strtolower( sanitize_text_field($_REQUEST['orderby']) ) : 'id';
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field($_REQUEST['order']) : 'asc';
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );

			return ( $order === 'asc' ) ? $result : - $result;

		}

		usort( $data, 'usort_reorder' );

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );
		$this->items = $data;

	}

	public static function get_name_url( $per_page = 125, $page_number = 1 ) {
		global $wpdb;

		$req = "";

		if ( isset( $_GET["s"] ) ) {
			$like = "LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%'";

			$arr = array(
				"xa",
				"ordering_number",
				"first_name",
				"last_name",
				"weight",
				"address",
				"zone",
				"reference_of_address",
				"phone_number_1",
				"phone_number_2"
			);

			foreach ( $arr as $key => $value ) {
				if ( $key === 0 ) {
					$req .= " WHERE ";
				}
				$req .= " ${value} {$like}";
				if ( $key !== count( $arr ) - 1 ) {
					$req .= " OR ";
				}
			}

			$req .= " ";
		}

		$sql = "SELECT * FROM {$wpdb->prefix}mybox_settings";

		$is_production = get_option( 'mybox_option_page' )['is_production'] ? 1 : 0;
		if (!isset( $_GET["s"])) {
			$req .= " WHERE";
		} else {
			$req .= " OR";
		}
		$req .= " is_production=".$is_production;

		$sql .= $req;
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	public function column_default( $item, $column_name ) {
		if ( $column_name === "name" ) {
			$xa_data = json_encode( $item );

			return "<a class=\"row-title m_title\" onclick='event.preventDefault(); open_xa_modal({$xa_data}); return;'>{$item['first_name']} {$item['last_name']}</a>";
		}
		if ( $column_name === "xa" ) {
			$is_production = get_option( 'mybox_option_page' )['is_production'] ? true : false;
			return "
				<span>
					<span>XA{$item['xa']}</span>
					<br>
					<span id='info_{$item['xa']}' style='font-size: 10px;color: #1f72aa;display: none'>Copied tracking link <span style='color: #1f72aa;'>&check;</span></span>
					<span onclick='copyTracking({$item['xa']}, {$is_production})' id='button_{$item['xa']}' style='font-size: 10px;color: #1f72aa; cursor: pointer'>Copy tracking link</span>
				</span>
			";
		}
		if ( $column_name === "status" ) {
			return strtoupper( $item['status'] );
		}
		if ( $column_name === "brc_or_button" ) {
			$protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" );
			$host     = "://$_SERVER[HTTP_HOST]";

			if ( $item["status"] === "sent" ) {
				$print_url = plugin_dir_url( __FILE__ ) . "/print-file.php?";
				foreach ( $item as $key => $value ) {
					$print_url .= $key . "=" . $value . "&";
				}

				$print_link = "<a href=\"{$print_url}\" target=\"popup\" onclick=\"window.open('{$print_url}','popup','height=760px,width=960px,top=100,left=500'); return false;\" rel=\"noopener noreferrer\" class=\"prnt-btn\"><button class='button'>Print</button></a>";
				return $print_link;
			} else {
				$actual_link = $protocol . $host . "$_SERVER[REQUEST_URI]";

				return "<a class='button rsnd-btn' href='{$actual_link}&resend=" . $item["id"] . "'>Resend</a>";
			}
		}

		return "<p>{$item[$column_name]}</p>";
	}
}


class Mybox {
	static $instance;
	public $xa_obj;

	private $mybox_options;

	public function removeParameterFromUrl( $key ) {

	}

	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );


		add_action( 'admin_menu', array( $this, 'mybox_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'mybox_page_init' ) );
	}


	public function mybox_add_plugin_page() {
		$hook = add_menu_page(
			'Mybox XA', // page_title
			'Mybox XA', // menu_title
			'manage_options', // capability
			'mybox-settings-xa', // menu_slug
			array( $this, 'mybox_create_xa_page' ), // function
			'dashicons-archive', // icon_url. 
			3 // position
		);

		add_submenu_page(
			'mybox-settings-xa',
			'Mybox Settings', // page_title
			'Mybox Settings', // menu_title
			'manage_options', // capability
			'mybox-settings', // menu_slug
			array( $this, 'mybox_create_admin_page' ) // function
		);
		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}

	public function mybox_create_admin_page() {
		$this->mybox_options = get_option( 'mybox_option_page' ); ?>

        <div class="wrap"><?php settings_errors(); ?>

            <div style="position: relative">
                <div id="loading" style="display: none" class="loading-holder">
                    <div class="loading-icon"></div>
                </div>

                <div class="error" id="message" style="display:none;"><p><b>Wrong API Key!</b></p></div>

                <form method="post" action="options.php">

					<?php
					settings_fields( 'mybox_option_group' );
					do_settings_sections( 'mybox-admin' );
					?>

                    <input name="submit" id="the_button" class="button button-primary" style="display:none;"
                           type="submit"
                           value="<?php esc_attr_e( 'Save API' ); ?>"/>
                </form>
                <input name="submit" onclick="apiCall()" class="button button-primary" type="submit"
                       value="<?php esc_attr_e( 'Save API' ); ?>"/>
            </div>
        </div>
		<?php

	}

	public function screen_option() {
		$option = 'per_page';
		$args   = [
			'label'  => 'URL',
			'option' => 'url_per_page'
		];
		add_screen_option( $option, $args );
		$this->xa_obj = new WP_Native_Table();
	}

	public function checkForResend() {
		if ( @$_GET["resend"] ) {
			resend_xa( $_GET["resend"] );
			$WP_Native_Table = new WP_Native_Table();
			$url_parsed      = $WP_Native_Table->remove_get_param( "resend" );

			wp_redirect( esc_url_raw( $url_parsed ) );
			exit;
		}
	}

	public function mybox_create_xa_page() {
		$this->checkForResend();
		?>
        <div class="wrap" style="margin-top:65px; position: relative;">
            <!-- The Modal -->
            <div id="myModal" class="modal">

                <!-- Modal content -->
                <div class="modal-content">
                    <div>
                        <span onclick="closeModal()" class="close">&times;</span>
                        <h2 id="modal-title">Some text in the Modal..</h2>
                    </div>

                    <div id="modal-content"></div>
                </div>
            </div>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
								<?php
								$this->xa_obj->prepare_items();
								$this->xa_obj->search_box( 'Search', 'search' );
								$this->xa_obj->display();
								?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
		<?php
	}

	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function mybox_page_init() {
		register_setting(
			'mybox_option_group', // option_group
			'mybox_option_page', // option_name
			array( $this, 'mybox_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'mybox_setting_section', // id
			'Mybox Settings', // title
			array( $this, 'mybox_section_info' ), // callback
			'mybox-admin' // page
		);

		add_settings_field(
			'is_production', // id
			'Is Production', // title
			array( $this, 'is_production_callback' ), // callback
			'mybox-admin', // page
			'mybox_setting_section' // section
		);

		add_settings_field(
			'api_key_staging', // id
			'API Key - Staging', // title
			array( $this, 'api_key_staging_callback' ), // callback
			'mybox-admin', // page
			'mybox_setting_section' // section
		);

		add_settings_field(
			'api_key_0', // id
			'API Key - Production', // title
			array( $this, 'api_key_0_callback' ), // callback
			'mybox-admin', // page
			'mybox_setting_section' // section
		);
	}

	public function mybox_sanitize( $input ) {

		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}

		if ( isset( $input['api_key_staging'] ) ) {
			$sanitary_values['api_key_staging'] = sanitize_text_field( $input['api_key_staging'] );
		}

		if ( isset( $input['is_production'] ) ) {
			$sanitary_values['is_production'] = true;
		} else {
			$sanitary_values['is_production'] = false;
		}
		return $sanitary_values;
	}

	public function mybox_section_info() {

	}

	public function api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="mybox_option_page[api_key_0]" id="api_key_0" value="%s">',
			isset( $this->mybox_options['api_key_0'] ) ? esc_attr( $this->mybox_options['api_key_0'] ) : ''
		);
	}

	public function api_key_staging_callback() {
		printf(
			'<input class="regular-text" type="text" name="mybox_option_page[api_key_staging]" id="api_key_staging" value="%s">',
			isset( $this->mybox_options['api_key_staging'] ) ? esc_attr( $this->mybox_options['api_key_staging'] ) : ''
		);
	}

	public function is_production_callback() {
		printf(
			'<input type="checkbox" name="mybox_option_page[is_production]" id="is_production" %s>',
			$this->mybox_options['is_production'] ? "checked" : ""
		);
	}
}

if ( is_admin() ) {
	$mybox = new Mybox();
}


// Hook in
add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
	$is_production = get_option( 'mybox_option_page' )['is_production'];
	$apiPrefix = $is_production ? "" : "staging.";
	$apiUrl        = "https://".$apiPrefix."api.myboxlogistics.io/public/zones";
	$access_key    = get_option( 'mybox_option_page' )[$is_production ? 'api_key_0' : 'api_key_staging'];

	$zones = null;

	$args         = array(
		'headers' => array(
			'Content-Type' => 'application/json',
			'Access-Key'   => $access_key
		)
	);
	$response     = wp_remote_get( $apiUrl, $args );
	$responseBody = wp_remote_retrieve_body( $response );
	$result       = json_decode( $responseBody );
	if ( ! is_wp_error( $response ) ) {
		if ( ! $result->zones ) {
			return;
		}
		$zones = $result->zones;
	}

	$fields['billing']['zone'] = array(
		'label'       => __( 'Zone', 'woocommerce' ),
		'placeholder' => _x( 'Zone', 'placeholder', 'woocommerce' ),
		'required'    => true,
		'class'       => array(
			'zone-parent',
			'form-row',
			'form-row-wide',
			'address-field',
			'validate-required'
		),
		'clear'       => false,
		'priority'    => 60
	);

	$fields['billing']['zone_id'] = array(
		'required' => true,
		'class'    => array( 'zone-hidden-input' ),
		'clear'    => true,
		'priority' => 60
	);



	$fields['billing']['address_reference'] = array(
		'label'       => __( 'Address Reference', 'woocommerce' ),
		'required' => false,
		'class'    => array( 'form-row', 'form-row-wide', 'address-field' ),
		'clear'    => true,
		'priority' => 60
	);

	$fields['billing']['secondary_phone'] = array(
		'label'       => __( 'Secondary Phone', 'woocommerce' ),
		'placeholder' => _x( 'Secondary Phone', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row', 'form-row-wide', 'address-field' ),
		'clear'       => true,
		'priority'    => 100
	);

	wp_enqueue_style( 'woocommerce_style', plugins_url( '/css/woocommerce_style.css', __FILE__ ) );
	wp_register_script( 'woocommerce_script', plugins_url( '/js/woocommerce_script.js', __FILE__ ) );
	wp_localize_script( 'woocommerce_script', 'zones', $zones );
	wp_enqueue_script( 'woocommerce_script' );

	return $fields;
}


add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_fields_add' );

function custom_checkout_fields_add( $order_id ) {
	if ( ! empty( $_POST['zone'] ) ) {
		update_post_meta( $order_id, 'zone', sanitize_text_field( $_POST['zone'] ) );
		update_post_meta( $order_id, 'zone_id', sanitize_text_field( $_POST['zone_id'] ) );
	}
	if ( ! empty( $_POST['address_reference'] ) ) {
		update_post_meta( $order_id, 'address_reference', sanitize_text_field( $_POST['address_reference'] ) );
	}

	if ( ! empty( $_POST['secondary_phone'] ) ) {
		update_post_meta( $order_id, 'secondary_phone', sanitize_text_field( $_POST['secondary_phone'] ) );
	}
}


add_action( 'woocommerce_order_status_completed', 'woocommerce_checkout_order_processed', 1, 1 );
function woocommerce_checkout_order_processed( $order_id ) {
	global $wpdb;
	$order_orig = new WC_Order( $order_id );
	$order      = json_decode( $order_orig );

	$order_metadata = $order->meta_data;

	$zone         = null;
	$zone_id      = 0;
	$reference_of_address = null;
	$secondary_phone      = null;

	foreach ( $order_metadata as $item ) {
		if ( $item->key === "zone" ) {
			$zone = $item->value;
		}
		if ( $item->key === "zone_id" ) {
			$zone_id = $item->value;
		}
		if ( $item->key === "address_reference" ) {
			$address_reference = $item->value;
		}
		if ( $item->key === "secondary_phone" ) {
			$secondary_phone = $item->value;
		}
	}

	$is_production = get_option( 'mybox_option_page' )['is_production'];

	$wpdb->insert(
		$wpdb->prefix . 'mybox_settings',
		array(
			'status'               => 'pending',
			'time'                 => current_time( 'mysql' ),
			'ordering_number'      => $order->id,
			'first_name'           => $order->billing->first_name,
			'last_name'            => $order->billing->last_name,
			'weight'               => 1,
			'fragile'              => "true",
			'category_box_id'      => 1,
			'address'              => $order->billing->address_1,
			'zone'         => $zone,
			'zone_id'     => $zone_id,
			'reference_of_address' => $address_reference,
			'phone_number_1'       => $order->billing->phone,
			'phone_number_2'       => $secondary_phone,
			'email'                => $order->billing->email,
			'is_production'		   => $is_production ? 1 : 0	
		),
		array(
			'%s',
			'%s'
		)
	);
	send_xa( $wpdb->insert_id );
}

function resend_xa( $xa_id ) {
	send_xa( $xa_id );
}

function send_xa( $xa_id ) {
	global $wpdb;

	$is_production = get_option( 'mybox_option_page' )['is_production'];
	$apiPrefix = $is_production ? "" : "staging.";
	$apiUrl        = "https://".$apiPrefix."api.myboxlogistics.io/public/xa";
	$access_key    = get_option( 'mybox_option_page' )[$is_production ? 'api_key_0' : 'api_key_staging'];

	if ( ! $access_key ) {
		return;
	}

	$xa = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mybox_settings WHERE id={$xa_id}" );

	// API insert
	$args   = array(
		'body'        => json_encode( array(
			"xa" => array(
				"shopify"  => false,
				"weight"  => $xa->weight,
				"xa_details" => array(
					"first_name"      		 => $xa->first_name,
					"last_name"       		 => $xa->last_name,
					"zone_id"		  		 => $xa->zone_id,
					"email"					 => $xa->email,
					"ordering_number"		 => $xa->ordering_number,
					"category_metrics_id"	 => $xa->category_box_id,
					"phones"    => array(
						"phone_number_1" => $xa->phone_number_1,
						"phone_number_2" => $xa->phone_number_2 ?: "Empty"
					),
					"address" => array(
						"address"              => $xa->address,
						"reference_of_address" => $xa->reference_of_address ?: "Empty"
					),
				),
			),
		) ),
		'timeout'     => '5',
		'redirection' => '5',
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(
			'Content-Type' => 'application/json',
			'Access-Key'   => $access_key
		),
		'cookies'     => array(),
	);
	$response     = wp_remote_post( $apiUrl, $args );
	$responseBody = wp_remote_retrieve_body( $response );
	$result       = json_decode( $responseBody );
	if ( ! is_wp_error( $response ) ) {
		$wpdb->update( $wpdb->prefix . 'mybox_settings', array(
			'xa'     => $result->id,
			'status' => 'sent',
			'b2b' => $result->b2b_name,
			'route' => $result->route_name,
			'area' => $result->area_name
		), array( 'id' => $xa_id ) );
	}
}


class Mybox_B2b_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;


	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mybox_B2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mybox_B2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mybox-b2b-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mybox_B2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mybox_B2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mybox-b2b-admin.js', array( 'jquery' ), $this->version, false );

	}

}
