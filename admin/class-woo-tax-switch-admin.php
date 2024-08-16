<?php

/**
 * The admin-specific functionality of the Woo Tax Switch plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/admin
 * @author     Wijnberg Developments
 */
class Woo_Tax_Switch_Admin {

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

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_woo_tax_switch', array( $this, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_woo_tax_switch', array( $this, 'update_settings' ) );
	}

	/**
	 * Enqueue styles for the block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/woo-tax-switch-shared.css', array(), $this->version, 'all' );
	}

	/**
	 * Add settings tab to WooCommerce settings.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs.
	 *
	 * @return   array    $settings_tabs    Array of WooCommerce setting tabs.
	 * @since    1.0.0
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['woo_tax_switch'] = __( 'Tax switch', 'woo-tax-switch' );

		return $settings_tabs;
	}

	/**
	 * Get settings for the Woo Tax Switch tab.
	 *
	 * @return   array    $settings    Array of settings.
	 * @since    1.0.0
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Tax switch settings', 'woo-tax-switch' ),
				'type' => 'title',
				'desc' => __( 'Customize the tax switch settings.', 'woo-tax-switch' ),
				'id'   => 'woo_tax_switch_section_title'
			),
			array(
				'name'        => __( 'Including VAT Text', 'woo-tax-switch' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices including VAT.', 'woo-tax-switch' ),
				'id'          => 'woo_tax_switch_incl_vat',
				'placeholder' => __( 'Incl. VAT' )
			),
			array(
				'name'        => __( 'Excluding VAT Text', 'woo-tax-switch' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices excluding VAT.', 'woo-tax-switch' ),
				'id'          => 'woo_tax_switch_excl_vat',
				'placeholder' => __( 'Excl. VAT' )
			),
			array(
				'type' => 'sectionend',
				'id'   => 'woo_tax_switch_section_end'
			)
		);

		return apply_filters( 'woo_tax_switch_settings', $settings );
	}

	/**
	 * Output the settings.
	 *
	 * @since    1.0.0
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save the settings.
	 *
	 * @since    1.0.0
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Filter the WooCommerce tax display option.
	 *
	 * @param string $value The current value of the option.
	 * @param string $option The name of the option.
	 *
	 * @return   string    The filtered value of the option.
	 * @since    1.0.0
	 */
	public function filter_woocommerce_tax_display_shop_option( $value, $option ) {
		if ( ! is_admin() && ! wp_doing_ajax() && ! defined( 'REST_REQUEST' ) ) {
			if ( isset( $_COOKIE['wts_is_switched'] ) ) {
				$is_switched = filter_var( $_COOKIE['wts_is_switched'], FILTER_VALIDATE_BOOLEAN );
				if ( $is_switched ) {
					//standard value
					if ( $value === 'incl' ) {
						return 'excl';
					} else {
						return 'incl';
					}
				}
			}
		}

		return $value;
	}
}
