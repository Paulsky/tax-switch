<?php

/**
 * The block functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The block functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the block functionality.
 * This class is responsible for registering and rendering the tax switch block and shortcode.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Block {

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

	public function init_block() {
		register_block_type( plugin_dir_path( dirname( __FILE__ ) ) . 'build/block.json', array(
			'render_callback' => [ $this, 'block_render_callback' ],
		) );

		register_block_style( 'wdevs/tax-switch', [
			'name'  => 'inline',
			'label' => __( 'Inline style', 'tax-switch-for-woocommerce' ),
		] );

		wp_set_script_translations( 'wdevs-tax-switch-editor-script', 'tax-switch-for-woocommerce', plugin_dir_path( dirname( __FILE__ ) ) . 'languages' );
	}

	//https://developer.woocommerce.com/2021/11/15/how-does-woocommerce-blocks-render-interactive-blocks-in-the-frontend/
	public function block_render_callback( $attributes = [], $content = '' ) {
		if ( ! is_admin() ) {
			$this->enqueue_frontend_script();
		}

		return $this->add_attributes_to_block( $attributes, $content );
	}

	public function register_shortcode() {
		add_shortcode( 'wdevs_tax_switch', array( $this, 'shortcode_render_callback' ) );
	}

	public function shortcode_render_callback( $attributes = [], $content = '' ) {
		if ( ! is_admin() ) {
			$this->enqueue_frontend_script();
		}

		$attributes = shortcode_atts( [
			'class-name'                      => 'is-style-default',
			'switch-color'                    => '',
			'switch-color-checked'            => '',
			'switch-background-color'         => '',
			'switch-background-color-checked' => '',
			'switch-label-incl'               => '',
			'switch-label-excl'               => ''
		], $attributes );

		$holder_class_name = 'wp-block-wdevs-tax-switch'; //important for rendering JS
		if ( isset( $attributes['class-name'] ) && ! empty( $attributes['class-name'] ) ) {
			$holder_class_name .= ' ' . $attributes['class-name'];
		}

		$content = '<div class="' . $holder_class_name . '"></div>';

		return $this->add_attributes_to_block( $attributes, $content );
	}

	public function enqueue_frontend_script() {
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/view.asset.php' );
		wp_enqueue_script( 'wdevs-tax-switch-view-script', plugin_dir_url( dirname( __FILE__ ) ) . 'build/view.js', $script_asset['dependencies'], $script_asset['version'] );
		wp_enqueue_style( 'wdevs-tax-switch-style', plugin_dir_url( dirname( __FILE__ ) ) . 'build/style-index.css', [], $this->version );

		$original_tax_display = $this->get_original_tax_display();

		wp_localize_script(
			'wdevs-tax-switch-view-script',
			'wtsViewObject',
			[
				'originalTaxDisplay' => $original_tax_display
			]
		);

		wp_set_script_translations(
			'wdevs-tax-switch-view-script',
			'tax-switch-for-woocommerce',
			plugin_dir_path( dirname( __FILE__ ) ) . 'languages'
		);
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
