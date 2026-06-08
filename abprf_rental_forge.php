<?php
	/**
	 * Plugin Name: ABP RentalForge
	 * Description: RentalForge is a WooCommerce-based WordPress rental plugin that helps you manage Property and tool bookings, availability and rentals from a single dashboard.
	 * Version: 1.0.0
	 * Author: abpteam
	 * Author URI: https://abp-team.com
	 * Text Domain: abp-rentalforge
	 * Domain Path: /languages
	 * WC requires at least: 8.0.0
	 *  WC tested up to: latest
	 *  Requires PHP: 7.4
	 *  Requires MySQL: 5.7+
	 *  License: GPLv3
	 *  License URI: https://www.gnu.org/licenses/gpl-3.0.html
	 */
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Rental_Forge' ) ) {
		class ABPRF_Rental_Forge {
			public function __construct() {
				$this->load_plugin();
			}

			private function load_plugin(): void {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				if ( ! defined( 'ABPRF_DIR' ) ) {
					define( 'ABPRF_DIR', dirname( __FILE__ ) );
				}
				if ( ! defined( 'ABPRF_URL' ) ) {
					define( 'ABPRF_URL', plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) ) );
				}
				if ( ! defined( 'ABPRF_BASE' ) ) {
					define( 'ABPRF_BASE', basename( __FILE__ ) );
				}
				if ( ! defined( 'ABPRF_BLANK_IMG_URL' ) ) {
					define( 'ABPRF_BLANK_IMG_URL', ABPRF_URL . '/assets/images/blank_image.png' );
				}
				if ( ! defined( 'ABPRF_PLUGIN_FILE' ) ) {
					define( 'ABPRF_PLUGIN_FILE', __FILE__ );
				}
				require_once ABPRF_DIR . '/includes/abprf_dependencies.php';
				if ( ! defined( 'ABPRF_WC' ) ) {
					define( 'ABPRF_WC',  ABPRF_Function::check_wc());
				}
				if ( ! defined( 'ABPRF_Configuration' ) ) {
					define( 'ABPRF_Configuration',  ABPRF_Function::get_option( 'abprf_configuration' ));
				}
				if ( ! defined( 'ABPRF_Dates' ) ) {
					define( 'ABPRF_Dates',  ABPRF_Function::get_option( 'abprf_dates' ));
				}
				if ( ! defined( 'ABPRF_Category' ) ) {
					define( 'ABPRF_Category',  ABPRF_Function::get_option( 'abprf_category' ));
				}
				if ( ! defined( 'ABPRF_Features' ) ) {
					define( 'ABPRF_Features',  ABPRF_Function::get_option( 'abprf_feature' ));
				}
				if ( ! defined( 'ABPRF_Locations' ) ) {
					define( 'ABPRF_Locations',  ABPRF_Function::get_option( 'abprf_location' ));
				}
				if ( ! defined( 'ABPRF_Brands' ) ) {
					define( 'ABPRF_Brands',  ABPRF_Function::get_option( 'abprf_brand' ));
				}
				if ( ! defined( 'ABPRF_Min_Price' ) ) {
					define( 'ABPRF_Min_Price',  ABPRF_Function::get_option( 'abprf_min_price' ));
				}
				if ( ! defined( 'ABPRF_Date_Format' ) ) {
					define( 'ABPRF_Date_Format', is_array(ABPRF_Dates) && array_key_exists('date_format',ABPRF_Dates)?ABPRF_Dates['date_format']: 'D d M , yy' );
				}
				if ( ! defined( 'ABPRF_Time_Format' ) ) {
					define( 'ABPRF_Time_Format', is_array(ABPRF_Dates) && array_key_exists('time_format',ABPRF_Dates)?ABPRF_Dates['time_format']: get_option( 'time_format' ));
				}
			}
		}
		new ABPRF_Rental_Forge();
		register_activation_hook( __FILE__, function () {
			if ( class_exists( 'ABPRF_Dependencies' ) ) {
				ABPRF_Dependencies::activation();
			}
		} );
		register_deactivation_hook( __FILE__, function () {
			if ( class_exists( 'ABPRF_Dependencies' ) ) {
				ABPRF_Dependencies::deactivate();
			}
		} );
	}