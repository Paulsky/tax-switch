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
 * The Switch block functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the Switch block functionality.
 * This class is responsible for registering and rendering the tax switch block and shortcode.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Block_Switch extends Wdevs_Tax_Switch_Block {

	/**
	 * @since 1.2.4
	 */
	public function register_frontend_scripts() {
		$script_asset = $this->register_script('wdevs-tax-switch-view-script', 'switch', 'view');

		wp_register_style(
			'wdevs-tax-switch-style',
			plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/style-index.css',
			[],
			$script_asset['version']
		);
	}

	/**
	 * @since 1.0,0
	 */
	public function init_block() {
		$editor_asset = $this->register_script('wdevs-tax-switch-editor-script', 'switch', 'index');
		register_block_type( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/block.json', array(
			'editor_script'   => 'wdevs-tax-switch-editor-script',
			'render_callback' => [ $this, 'block_render_callback' ],
		) );

		register_block_style( 'wdevs/tax-switch', [
			'name'  => 'inline',
			'label' => __( 'Inline style', 'tax-switch-for-woocommerce' ),
		] );

		wp_set_script_translations( 'wdevs-tax-switch-editor-script', 'tax-switch-for-woocommerce', plugin_dir_path( dirname( __FILE__ ) ) . 'languages' );
	}

	/**
	 * @since 1.0,0
	 */
	public function register_shortcode() {
		add_shortcode( 'wdevs_tax_switch', array( $this, 'shortcode_render_callback' ) );
	}

	/**
	 * https://developer.woocommerce.com/2021/11/15/how-does-woocommerce-blocks-render-interactive-blocks-in-the-frontend/
	 *
	 * @since 1.0,0
	 */
	public function block_render_callback( $attributes = [], $content = '' ) {
		$should_render = $this->before_component_render();
		if ( ! $should_render ) {
			return '';
		}

		return $this->add_attributes_to_block( $attributes, $content );
	}

	/**
	 * @since 1.0,0
	 */
	public function shortcode_render_callback( $attributes = [], $content = '' ) {
		$should_render = $this->before_component_render();
		if ( ! $should_render ) {
			return '';
		}

		$attributes = shortcode_atts( [
			'class-name'                      => 'is-style-default',
			'switch-type'                     => 'switch',
			'switch-color'                    => '',
			'switch-color-checked'            => '',
			'switch-background-color'         => '',
			'switch-background-color-checked' => '',
			'switch-text-color'               => '',
			'switch-label-incl'               => '',
			'switch-label-excl'               => '',
		], $attributes );

		$container_class_name = 'wp-block-wdevs-tax-switch'; //important for rendering JS

		return $this->render_shortcode_html( $attributes, $container_class_name, $content );
	}

	/**
	 * @since 1.0,0
	 */
	public function enqueue_frontend_scripts() {
		if ( wp_style_is( 'wdevs-tax-switch-style', 'registered' ) ) {
			wp_enqueue_style( 'wdevs-tax-switch-style' );
		}

		if ( wp_script_is( 'wdevs-tax-switch-view-script', 'registered' ) ) {
			wp_enqueue_script( 'wdevs-tax-switch-view-script' );

			$original_tax_display = $this->get_original_tax_display();
			$check_price_elements = $this->should_hide_on_non_price_pages();

			wp_localize_script(
				'wdevs-tax-switch-view-script',
				'wtsViewObject',
				[
					'originalTaxDisplay' => $original_tax_display,
					'checkPriceElements' => $check_price_elements
				]
			);

			wp_set_script_translations(
				'wdevs-tax-switch-view-script',
				'tax-switch-for-woocommerce',
				plugin_dir_path( dirname( __FILE__ ) ) . 'languages'
			);
		}
	}
}
