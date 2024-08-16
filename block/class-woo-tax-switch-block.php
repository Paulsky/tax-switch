<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Woo_Tax_Switch_Block {

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
	 * Ajax Manager class
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object $ajax_manager The Ajax Manager class
	 */
	protected $ajax_manager;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param object $ajax_manager The Ajax Manager class
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, $ajax_manager ) {

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->ajax_manager = $ajax_manager;
	}

	public function init_block() {
		register_block_type( plugin_dir_path( dirname( __FILE__ ) ) . 'build/block.json', array(
			'render_callback' => [ $this, 'block_render_callback' ],
		) );

		register_block_style( 'wdevs/woo-tax-switch', [
			'name'  => 'inline',
			'label' => __( 'Inline style', 'woo-tax-switch' ),
		] );

		wp_set_script_translations( 'wdevs-woo-tax-switch-editor-script', 'woo-tax-switch', plugin_dir_path( dirname( __FILE__ ) ) . 'languages' );
	}

	//https://developer.woocommerce.com/2021/11/15/how-does-woocommerce-blocks-render-interactive-blocks-in-the-frontend/
	public function block_render_callback( $attributes = [], $content = '' ) {
		if ( ! is_admin() ) {
			$this->enqueue_frontend_script();
		}

		return $this->add_attributes_to_block( $attributes, $content );
	}

	public function register_shortcode() {
		add_shortcode( 'woo_tax_switch', array( $this, 'shortcode_render_callback' ) );
	}

	public function shortcode_render_callback( $attributes = [], $content = '' ) {
		if ( ! is_admin() ) {
			$this->enqueue_frontend_script();
		}

		$attributes = shortcode_atts( [
			'class-name'                      => 'is-style-default',
			'switch-color'                    => '',
			'switch-background-color'         => '',
			'switch-background-color-checked' => ''
		], $attributes );

		$holder_class_name = 'wp-block-wdevs-woo-tax-switch'; //important for rendering JS
		if ( isset( $attributes['class-name'] ) && ! empty( $attributes['class-name'] ) ) {
			$holder_class_name .= ' ' . $attributes['class-name'];
		}

		$content = '<div class="' . $holder_class_name . '"></div>';

		return $this->add_attributes_to_block( $attributes, $content );
	}

	public function enqueue_frontend_script() {

		$is_switched = false;
		if ( isset( $_COOKIE['wts_is_switched'] ) ) {
			$is_switched = filter_var( $_COOKIE['wts_is_switched'], FILTER_VALIDATE_BOOLEAN );
		}

		wp_localize_script(
			'wdevs-woo-tax-switch-view-script',
			'wtsAjaxObject',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajaxNonce'  => wp_create_nonce( 'ajax-nonce' ),
				'ajaxAction' => $this->ajax_manager->getFormAction(),
				'isSwitched' => $is_switched
			]
		);

		wp_set_script_translations( 'wdevs-woo-tax-switch-view-script', 'woo-tax-switch', plugin_dir_path( dirname( __FILE__ ) ) . 'languages' );

	}

	public function add_attributes_to_block( $attributes = [], $content = '' ) {
		$escaped_data_attributes = [];

		foreach ( $attributes as $key => $value ) {

			if ( is_bool( $value ) ) {
				$value = $value ? 'true' : 'false';
			}
			if ( ! is_scalar( $value ) ) {
				$value = wp_json_encode( $value );
			}
			$escaped_data_attributes[] = 'data-' . esc_attr( strtolower( preg_replace( '/(?<!\ )[A-Z]/', '-$0', $key ) ) ) . '="' . esc_attr( $value ) . '"';
		}

		return preg_replace( '/^<div /', '<div ' . implode( ' ', $escaped_data_attributes ) . ' ', trim( $content ) );
	}
}
