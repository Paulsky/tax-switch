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
 * The abstract block functionality of the plugin.
 *
 * Defines the structure and common functions of a Tax Switch block.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
abstract class Wdevs_Tax_Switch_Block {

	use Wdevs_Tax_Switch_Helper;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	protected $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.5.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	abstract public function register_frontend_scripts();
	abstract public function init_block();
	abstract public function register_shortcode();

	/**
	 * @param array $attributes
	 * @param string $container_class_name
	 * @param string $content
	 *
	 * @return string
	 *
	 * @since 1.5.0
	 */
	protected function render_shortcode_html( array $attributes, string $container_class_name, string $content, string $element = 'div' ): string {
		$wpml_active             = defined( 'ICL_SITEPRESS_VERSION' );
		$translatable_attributes = [ 'switch-label-incl', 'switch-label-excl' ];
		foreach ( $translatable_attributes as $label_key ) {
			if ( ! empty( $attributes[ $label_key ] ) ) {
				$attributes[ $label_key ] = __( $attributes[ $label_key ], 'tax-switch-for-woocommerce' );
				if ( $wpml_active ) {
					do_action( 'wpml_register_single_string', 'tax-switch-for-woocommerce', 'Tax switch shortcode - ' . $label_key, $attributes[ $label_key ] );
				}
			}
		}


		if ( isset( $attributes['class-name'] ) && ! empty( $attributes['class-name'] ) ) {
			$container_class_name .= ' ' . esc_attr( $attributes['class-name'] );
		}

		$content = '<div class="' . $container_class_name . '"></div>';

		return $this->add_attributes_to_block( $attributes, $content );
	}

	/**
	 * @param array $attributes
	 * @param string $content
	 *
	 * @return string
	 *
	 * @since 1.5.0
	 */
	protected function add_attributes_to_block( $attributes = [], $content = '' ) {
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

	/**
	 * @since 1.5,1
	 */
	protected function before_component_render() {
		if ( ! is_admin() ) {

			if ( $this->is_in_cart_or_checkout() ) {
				return false;
			}

			if ( $this->should_hide_on_current_page() ) {
				return false;
			}

			$this->enqueue_frontend_scripts();
		}

		return true;
	}
}
