<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Hooks' ) ) {
		class ABPRF_Hooks {
			public function __construct() {
				add_action( 'abprf_title', array( $this, 'title' ), 10, 2 );
				add_action( 'abprf_sub_title', array( $this, 'sub_title' ), 10, 2 );
				add_action( 'abprf_category', [ $this, 'category' ], 10, 3 );
				add_action( 'abprf_search_form', array( $this, 'search_form' ), 10, 2 );
				add_action( 'abprf_property_item', array( $this, 'property_item' ), 10, 2 );
				add_action( 'abprf_rental_duration', array( $this, 'rental_duration' ), 10, 2 );
				add_action( 'abprf_registration', [ $this, 'registration' ] );
				add_action( 'abprf_additional', [ $this, 'additional' ], 10, 2 );
				add_action( 'abprf_client_form', [ $this, 'client_form' ], 10, 2 );
				add_action( 'abprf_total_price', [ $this, 'total_price' ] );
				add_action( 'abprf_content', [ $this, 'the_content' ] );
				add_action( 'abprf_pagination', array( $this, 'pagination' ) );
				add_action( 'abprf_display_cart_item', [ $this, 'display_cart_item' ] );
			}

			public function title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/title.php' );
				do_action( 'abprf_title_template', $post_id, $abprf_infos );
			}

			public function sub_title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/sub_title.php' );
				do_action( 'abprf_sub_title_template', $post_id, $abprf_infos );
			}

			public function category( $post_id, $all_categories = [], $ribbon = '' ): void {
				include_once ABPRF_Function::template_path( 'layout/category.php' );
				do_action( 'abprf_category_template', $post_id, $all_categories, $ribbon );
			}

			public function search_form( $abprf_infos = [] ) {
				include_once ABPRF_Function::template_path( 'layout/search_form.php' );
				do_action( 'abprf_search_form_template', $abprf_infos );
			}

			public function property_item( $abprf_infos, $property = [] ) {
				include_once ABPRF_Function::template_path( 'layout/property_item.php' );
				do_action( 'abprf_property_item_template', $abprf_infos, $property );
			}

			public function registration( $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/registration.php' );
				do_action( 'abprf_registration_template', $abprf_infos );
			}

			public function rental_duration( $date_infos = [] ) {
				include_once ABPRF_Function::template_path( 'layout/rental_duration.php' );
				do_action( 'abprf_rental_duration_template', $date_infos );
			}

			public function additional( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/additional_services.php' );
				do_action( 'abprf_additional_template', $post_id, $abprf_infos );
			}

			public function client_form( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/client_form.php' );
				do_action( 'abprf_client_form_template', $post_id, $abprf_infos );
			}

			public function total_price( $abprf_infos ): void {
				include_once ABPRF_Function::template_path( 'layout/total_price.php' );
				do_action( 'abprf_total_price_template', $abprf_infos );
			}

			public function the_content( $post_id ): void {
				include_once ABPRF_Function::template_path( 'layout/the_content.php' );
				do_action( 'abprf_content_template', $post_id );
			}

			public function pagination( $args ): void {
				include_once ABPRF_Function::template_path( 'layout/pagination.php' );
				do_action( 'abprf_pagination_template', $args );
			}

			public function display_cart_item( $cart_item = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/display_cart_item.php' );
				do_action( 'abprf_display_cart_item_template', $cart_item );
			}
		}
		new ABPRF_Hooks();
	}