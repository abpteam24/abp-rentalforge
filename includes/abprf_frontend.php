<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Frontend' ) ) {
		class ABPRF_Frontend {
			public function __construct() {
				add_filter( 'single_template', [ $this, 'load_single_page' ] );
				add_filter( 'template_include', array( $this, 'load_taxonomy_page' ) );
			}

			public function load_single_page( $template ): string {
				if ( is_singular( ABPRF_Function::get_cpt() ) ) {
					$custom_template = ABPRF_Function::template_path( 'page/details_page.php' );
					if ( ! empty( $custom_template ) ) {
						return $custom_template;
					}
				}

				return (string) $template;
			}

			public function load_taxonomy_page( $template ): string {
				if ( is_tax( 'abprf_category' ) ) {
					return ABPRF_Function::template_path( 'page/category.php' );
				}
				if ( is_tax( 'abprf_location' ) ) {
					return ABPRF_Function::template_path( 'page/location.php' );
				}
				if ( is_tax( 'abprf_brand' ) ) {
					return ABPRF_Function::template_path( 'page/brand.php' );
				}

				return (string) $template;
			}
		}
		new ABPRF_Frontend();
	}