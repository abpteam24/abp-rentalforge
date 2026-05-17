<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Hooks' ) ) {
		class ABPRF_Hooks {
			public function __construct() {
				add_action( 'abprf_title', [ $this, 'title' ], 10, 2 );
				add_action( 'abprf_sub_title', [ $this, 'sub_title' ], 10, 2 );
				add_action( 'abprf_category', [ $this, 'category' ], 10, 3 );
				add_action( 'abprf_search_form', [ $this, 'search_form' ], 10, 2 );
				add_action( 'abprf_property_item', [ $this, 'property_item' ], 10, 2 );
				add_action( 'abprf_rental_duration', [ $this, 'rental_duration' ], 10, 2 );
				add_action( 'abprf_registration', [ $this, 'registration' ] );
				add_action( 'abprf_additional', [ $this, 'additional' ], 10, 2 );
				add_action( 'abprf_client_form', [ $this, 'client_form' ], 10, 2 );
				add_action( 'abprf_total_price', [ $this, 'total_price' ] );
				add_action( 'abprf_content', [ $this, 'the_content' ] );
				add_action( 'abprf_pagination', [ $this, 'pagination' ] );
				add_action( 'abprf_display_cart_item', [ $this, 'display_cart_item' ] );
				add_action( 'abprf_faq', [ $this, 'faq' ], 10, 2 );
				add_action( 'abprf_slider', [ $this, 'slider' ], 10, 3 );
				add_action( 'abprf_slider_popup', [ $this, 'slider_popup' ], 10, 3 );
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

			public function faq( $abprf_infos = [], $type = '' ): void {
				include_once ABPRF_Function::template_path( 'layout/faq.php' );
				do_action( 'abprf_faq_template', $abprf_infos, $type );
			}

			public function slider( $post_id = '', $style = '' ): void {
				$abprf_slider = ABPRF_Function::get_option( 'abprf_slider' );
				if ( ! empty( $style ) ) {
					$slider_style = $style == 'gallery' ? 'gallery' : 'slider';
				} else {
					$slider_style = isset( $abprf_slider['slider_style'] ) && $abprf_slider['slider_style'] ? $abprf_slider['slider_style'] : 'slider';
				}
				include_once ABPRF_Function::template_path( 'layout/' . $slider_style . '.php' );
				do_action( 'abprf_' . $slider_style . '_template', $abprf_slider, $post_id );
			}

			public function slider_popup( $abprf_slider, $img_infos, $popup_id = '#abprf_slider_' ): void {
				include_once ABPRF_Function::template_path( 'layout/slider_popup.php' );
				do_action( 'abprf_slider_popup_template', $abprf_slider, $img_infos, $popup_id );
			}
		}
		new ABPRF_Hooks();
	}