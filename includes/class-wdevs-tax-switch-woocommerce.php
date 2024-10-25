<?php

/**
 * The WooCommerce functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The WooCommerce functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for WooCommerce functionality.
 * This class is responsible for registering and rendering the WooCommerce settings.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Woocommerce {

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

	/**
	 * Declare WooCommerce compatibility
	 *
	 * @since 1.0.0
	 */
	public function declare_compatibility(){
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'tax-switch-for-woocommerce/wdevs-tax-switch.php', true );
		}
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
		$settings_tabs['wdevs_tax_switch'] = __( 'Tax switch', 'tax-switch-for-woocommerce' );

		return $settings_tabs;
	}

	/**
	 * Get settings for the Wdevs Tax Switch tab.
	 *
	 * @return   array    $settings    Array of settings.
	 * @since    1.0.0
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Tax switch settings', 'tax-switch-for-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Customize the tax switch settings.', 'tax-switch-for-woocommerce' ),
				'id'   => 'wdevs_tax_switch_section_title'
			),
			array(
				'name'        => __( 'Including VAT text', 'tax-switch-for-woocommerce' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices including VAT.', 'tax-switch-for-woocommerce' ),
				'id'          => 'wdevs_tax_switch_incl_vat',
				'placeholder' => __( 'Incl. VAT', 'tax-switch-for-woocommerce' )
			),
			array(
				'name'        => __( 'Excluding VAT text', 'tax-switch-for-woocommerce' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices excluding VAT.', 'tax-switch-for-woocommerce' ),
				'id'          => 'wdevs_tax_switch_excl_vat',
				'placeholder' => __( 'Excl. VAT', 'tax-switch-for-woocommerce' )
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wdevs_tax_switch_section_end'
			),
		);

		return apply_filters( 'wdevs_tax_switch_settings', $settings );
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

}
