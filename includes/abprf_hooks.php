<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Hooks' ) ) {
		class ABPRF_Hooks {
			public function __construct() {
				add_action( 'abprf_load_details_template', [ $this, 'details_template' ]);
				add_action( 'abprf_title', [ $this, 'title' ], 10, 2 );
				add_action( 'abprf_sub_title', [ $this, 'sub_title' ], 10, 2 );
				add_action( 'abprf_category', [ $this, 'category' ], 10, 3 );
				add_action( 'abprf_location', [ $this, 'location' ], 10, 3 );
				add_action( 'abprf_search_form', [ $this, 'search_form' ], 10, 2 );
				add_action( 'abprf_post_filter', [ $this, 'post_filter' ], 10, 2 );
				add_action( 'abprf_property_item', [ $this, 'property_item' ], 10, 2 );
				add_action( 'abprf_property_item_group', [ $this, 'property_item_group' ], 10, 2 );
				add_action( 'abprf_rental_duration', [ $this, 'rental_duration' ], 10, 2 );
				add_action( 'abprf_registration', [ $this, 'registration' ] );
				add_action( 'abprf_additional', [ $this, 'additional' ], 10, 2 );
				add_action( 'abprf_client_form', [ $this, 'client_form' ], 10, 2 );
				add_action( 'abprf_total_price', [ $this, 'total_price' ] );
				add_action( 'abprf_content', [ $this, 'the_content' ] );
				add_action( 'abprf_pagination', [ $this, 'pagination' ] );
				add_action( 'abprf_display_cart_item', [ $this, 'display_cart_item' ] );
				add_action( 'abprf_faq', [ $this, 'faq' ], 10, 2 );
				add_action( 'abprf_term_condition', [ $this, 'term_condition' ], 10, 2 );
				add_action( 'abprf_slider', [ $this, 'slider' ], 10, 3 );
				add_action( 'abprf_slider_popup', [ $this, 'slider_popup' ], 10, 3 );
			}
			public function details_template($post_id): void {
				require_once ABPRF_Function::details_template_path( $post_id);
				$template_name = ABPRF_Function::get_post_info( $post_id, 'abprf_template', 'grid' );
				do_action( 'abprf_details_' . $template_name . '_template', $post_id );
			}

			public function title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/title.php' );
				do_action( 'abprf_title_template', $post_id, $abprf_infos );
			}

			public function sub_title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/sub_title.php' );
				do_action( 'abprf_sub_title_template', $post_id, $abprf_infos );
			}

			public function category( $post_id,$ribbon = '' ): void {
				include_once ABPRF_Function::template_path( 'layout/category.php' );
				do_action( 'abprf_category_template', $post_id, $ribbon );
			}
			public function location( $post_id,$ribbon = '' ): void {
				include_once ABPRF_Function::template_path( 'layout/location.php' );
				do_action( 'abprf_location_template', $post_id, $ribbon );
			}

			public function search_form( $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/search_form.php' );
				do_action( 'abprf_search_form_template', $abprf_infos );
			}
			public function post_filter( $params = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/post_filter.php' );
				do_action( 'abprf_post_filter_template', $params );
			}

			public function property_item( $abprf_infos, $property = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/property_item.php' );
				do_action( 'abprf_property_item_template', $abprf_infos, $property );
			}
			public function property_item_group( $abprf_infos, $properties = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/property_item_group.php' );
				do_action( 'abprf_property_item_group_template', $abprf_infos, $properties );
			}

			public function registration( $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/registration.php' );
				do_action( 'abprf_registration_template', $abprf_infos );
			}

			public function rental_duration( $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/rental_duration.php' );
				do_action( 'abprf_rental_duration_template', $abprf_infos );
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
			}public function term_condition( $abprf_infos = [], $type = '' ): void {
				include_once ABPRF_Function::template_path( 'layout/term_condition.php' );
				do_action( 'abprf_term_condition_template', $abprf_infos, $type );
			}

			public function slider( $img_infos = [], $params = []): void {
				if ( ! empty( $img_infos ) ) {
					$style    = array_key_exists( 'style', $params ) && $params['style'] ? $params['style'] : 'gallery';
					$image_column    = array_key_exists( 'column', $params ) && $params['column'] ? $params['column'] : '';
					$abprf_slider = ABPRF_Function::get_option( 'abprf_slider' );
					if(!empty($image_column)){
						$abprf_slider['image_column'] = $image_column;
					}
					if ( ! empty( $style ) ) {
						$slider_style = $style == 'gallery' ? 'gallery' : 'slider';
					} else {
						$slider_style = isset( $abprf_slider['slider_style'] ) && $abprf_slider['slider_style'] ? $abprf_slider['slider_style'] : 'slider';
					}
					include_once ABPRF_Function::template_path( 'layout/' . $slider_style . '.php' );
					do_action( 'abprf_' . $slider_style . '_template', $img_infos, $abprf_slider );
				}
			}

			public function slider_popup( $abprf_slider, $img_infos, $popup_id = '#abprf_slider_' ): void {
				include_once ABPRF_Function::template_path( 'layout/slider_popup.php' );
				do_action( 'abprf_slider_popup_template', $abprf_slider, $img_infos, $popup_id );
			}
		}
		new ABPRF_Hooks();
	}