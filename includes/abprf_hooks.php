<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Hooks' ) ) {
		class ABPRF_Hooks {
			public function __construct() {
				add_action( 'abprf_title', array( $this, 'title' ), 10, 2 );
				add_action( 'abprf_sub_title', array( $this, 'sub_title' ), 10, 2 );
				add_action( 'abprf_search_form', array( $this, 'search_form' ), 10, 2 );
				add_action( 'abprf_property_item', array( $this, 'property_item' ), 10, 2 );
				add_action( 'abprf_rental_duration', array( $this, 'rental_duration' ), 10, 2 );
				add_action( 'abprf_pagination', array( $this, 'pagination' ) );
				//=============================//
				add_action( 'abprf_category', [ $this, 'category' ], 10, 2 );
				add_action( 'abptm_capacity', [ $this, 'capacity' ], 10, 2 );
				add_action( 'abptm_route_direction', [ $this, 'route_direction' ] );
				add_action( 'abptm_the_content', [ $this, 'the_content' ] );
				add_action( 'abptm_registration', [ $this, 'registration' ] );
				add_action( 'abptm_details_info', [ $this, 'details_info' ], 10, 2 );
				add_action( 'abptm_selection_item', [ $this, 'selection_item' ] );
				add_action( 'abptm_ticket', [ $this, 'ticket' ], 10, 2 );
				add_action( 'abptm_seat_plan', [ $this, 'seat_plan' ], 10, 2 );
				add_action( 'abprf_additional', [ $this, 'additional_services' ], 10, 2 );
				add_action( 'abptm_pickup_drop', [ $this, 'pickup_drop' ], 10, 2 );
				add_action( 'abprf_total_price', [ $this, 'total_price' ] );
				add_action( 'abprf_hidden_form', [ $this, 'hidden_form' ], 10, 2 );
				add_action( 'abptm_display_cart_item', [ $this, 'display_cart_item' ] );
			}

			public function title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/title.php' );
				do_action( 'abprf_title_template', $post_id, $abprf_infos );
			}

			public function sub_title( $post_id, $abprf_infos = [] ): void {
				include_once ABPRF_Function::template_path( 'layout/sub_title.php' );
				do_action( 'abprf_sub_title_template', $post_id, $abprf_infos );
			}

			public function search_form( $abprf_infos = [] ) {
				include_once ABPRF_Function::template_path( 'layout/search_form.php' );
				do_action( 'abprf_search_form_template', $abprf_infos );
			}

			public function property_item( $abprf_infos, $property = [] ) {
				include_once ABPRF_Function::template_path( 'layout/property_item.php' );
				do_action( 'abprf_property_item_template', $abprf_infos, $property );
			}
			public function rental_duration($date_infos=[]) {
				include_once ABPRF_Function::template_path( 'layout/rental_duration.php' );
				do_action( 'abprf_rental_duration_template', $date_infos );
			}

			public function pagination( $args ): void {
				include ABPRF_Function::template_path( 'layout/pagination.php' );
				do_action( 'abprf_pagination_template', $args );
			}

			//=============================//
			public function category( $abprf_infos = [], $ribbon = false ): void { include ABPRF_Function::template_path( 'layout/category.php' ); }

			public function capacity( $abprf_infos = [], $ribbon = false ): void { include ABPRF_Function::template_path( 'layout/capacity.php' ); }

			public function route_direction( $abprf_infos = [] ): void { include ABPRF_Function::template_path( 'layout/route_direction.php' ); }

			public function the_content( $abprf_infos = [] ): void { include ABPRF_Function::template_path( 'layout/the_content.php' ); }

			public function registration( $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/registration.php' ); }

			public function details_info( $abprf_infos = [], $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/details_info.php' ); }

			public function selection_item(): void { include ABPRF_Function::template_path( 'layout/selection_item.php' ); }

			public function ticket( $abprf_infos = [], $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/ticket.php' ); }

			public function seat_plan( $abprf_infos = [], $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/seat_plan.php' ); }

			public function additional_services( $abprf_infos = [], $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/additional_services.php' ); }

			public function pickup_drop( $abprf_infos, $form_data ): void { include ABPRF_Function::template_path( 'layout/pickup_drop.php' ); }

			public function total_price( $post_id ): void { include ABPRF_Function::template_path( 'layout/total_price.php' ); }





			public function hidden_form( $abprf_infos = [], $form_data = [] ): void { include ABPRF_Function::template_path( 'layout/hidden_form.php' ); }

			public function display_cart_item( $cart_item = [] ): void { include ABPRF_Function::template_path( 'layout/display_cart_item.php' ); }
		}
		new ABPRF_Hooks();
	}