<?php

/**
 * The admin-specific functionality of the Wdevs Tax Switch plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/admin
 * @author     Wijnberg Developments
 */
class Wdevs_Tax_Switch_Admin {

	use Wdevs_Tax_Switch_Helper;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * @since 1.5.3
	 */
	const AJAX_NONCE_ACTION = 'wdevs-tax-switch-nonce';

	/**
	 * @since 1.5.3
	 */
	const AJAX_ACTION_RENDER = 'render_by_request';


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Enqueue styles and scripts for the block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/wdevs-tax-switch-shared.css', array(), $this->version );

		$original_tax_display = $this->get_original_tax_display();
		$check_price_elements = false;

		wp_localize_script(
			'wdevs-tax-switch-editor-script',
			'wtsEditorObject',
			[
				'originalTaxDisplay' => $original_tax_display,
				'checkPriceElements' => $check_price_elements
			]
		);

		wp_localize_script(
			'wdevs-tax-switch-label-editor-script',
			'wtsEditorObject',
			[
				'originalTaxDisplay' => $original_tax_display,
				'checkPriceElements' => $check_price_elements
			]
		);

	}

	/**
	 * Enqueue scripts for the admin section.
	 *
	 * @since    1.4.0
	 */
	public function enqueue_admin_scripts() {
		if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'wdevs_tax_switch' && isset( $_GET['section'] ) && $_GET['section'] === 'shortcode' ) {

			$woocommerce_admin_handle = $this->plugin_name . '-admin-woocommerce';

			wp_enqueue_script( $woocommerce_admin_handle, plugin_dir_url( __FILE__ ) . 'js/wdevs-tax-switch-woocommerce.js', [
				'jquery',
				'wc-backbone-modal',
				'wp-color-picker',
				'underscore',
				'wdevs-tax-switch-view-script'
			], $this->version, true );

			wp_localize_script(
				$woocommerce_admin_handle,
				'wtsAjaxObject',
				[
					'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
					'nonce'        => wp_create_nonce( self::AJAX_NONCE_ACTION ),
					'renderAction' => self::AJAX_ACTION_RENDER
				]
			);

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wdevs-tax-switch-style' );
		}
	}

	/**
	 * @since 1.5.3
	 */
	public function add_action_links( $actions ) {
		$links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wdevs_tax_switch' ) . '">' . __( 'Settings' ) . '</a>', //Yes, just use WordPress text domain
		);

		$actions = array_merge( $actions, $links );

		return $actions;
	}

	/**
	 * AJAX request
	 * Render the shortcode by AJAX request
	 *
	 * @since 1.5.3
	 */
	public function render_by_request_action() {
		if ( ! isset( $_POST['nonce'], $_POST['attributes'] ) ) {
			wp_send_json_error( 'missing_fields' );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), self::AJAX_NONCE_ACTION ) ) {
			wp_send_json_error( 'bad_nonce' );
		}

		$raw_attributes = wp_unslash( $_POST['attributes'] );
		$attributes     = array();

		if ( is_string( $raw_attributes ) ) {
			$attributes = json_decode( $raw_attributes, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$attributes = array();
			}
		} elseif ( is_array( $raw_attributes ) ) {
			$attributes = $raw_attributes;
		}

		$allowed_attributes = [
			'class-name',
			'switch-type',
			'switch-color',
			'switch-color-checked',
			'switch-background-color',
			'switch-background-color-checked',
			'switch-text-color',
			'switch-label-incl',
			'switch-label-excl'
		];

		$shortcode = '[wdevs_tax_switch';

		foreach ( $attributes as $key => $value ) {
			$sanitized_key = sanitize_key( $key );

			if ( ! in_array( $sanitized_key, $allowed_attributes ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				continue;
			}

			$sanitized_value = sanitize_text_field( $value );

			$shortcode .= ' ' . $sanitized_key . '="' . esc_attr( $sanitized_value ) . '"';
		}

		$shortcode .= ']';

		wp_send_json_success(
			array(
				'shortcode' => do_shortcode( $shortcode ),
			)
		);
	}
}
