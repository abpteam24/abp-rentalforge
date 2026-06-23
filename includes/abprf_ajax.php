<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Ajax' ) ) {
		class ABPRF_Ajax {
			public function __construct() {
				add_action( 'wp_ajax_abprf_get_global_booking', [ $this, 'global_booking' ] );
				add_action( 'wp_ajax_nopriv_abprf_get_global_booking', [ $this, 'global_booking' ] );
				add_action( 'wp_ajax_abprf_load_property', [ $this, 'load_property' ] );
				add_action( 'wp_ajax_nopriv_abprf_load_property', [ $this, 'load_property' ] );
				add_action( 'wp_ajax_abprf_load_end_date', [ $this, 'load_end_date' ] );
				add_action( 'wp_ajax_nopriv_abprf_load_end_date', [ $this, 'load_end_date' ] );
			}
			public function global_booking() {
				$form_html = '';
				$all_time  = [];
				if ( ! check_ajax_referer( 'abprf_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'time_info' => $all_time, 'details' => '', 'form' => '', 'msg' => esc_html__( 'Session expired. Please refresh the page.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_id       = $post_int( 'post_id' );
				$all_dates     = [];
				$all_end_dates = [];
				ob_start();
				$params['global_order'] = 'yes';
				$params['post_id']      = $post_id;
				do_action( 'abprf_search_form', $params );
				$form_html = ob_get_clean();
				ob_start();
				if ( ! empty( $post_id ) ) {
					$abprf_infos = ABPRF_Function::get_all_meta( $post_id );
					$rent_rule   = $post_val( 'rent_rule' );
					$rent_rule   = ! empty( $rent_rule ) ? $rent_rule : ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
					if ( ! empty( $rent_rule ) && $rent_rule != 'monthly' ) {
						$all_dates     = ABPRF_Function::get_start_dates( $post_id );
						$upcoming_date = current( $all_dates );
						$upcoming_date = ! empty( $upcoming_date ) ? gmdate( 'Y-m-d', strtotime( $upcoming_date ) ) : '';
						$all_end_dates = ABPRF_Function::get_end_dates( $post_id, $upcoming_date, $all_dates );
						$all_dates     = ABPRF_Layout::create_datepicker_array( $all_dates );
						$all_end_dates = ABPRF_Layout::create_datepicker_array( $all_end_dates );
					}
					if ( $rent_rule == 'hourly' || $rent_rule == 'multi_day' ) {
						$all_time = ABPRF_Function::get_time( $post_id, 'js' );
					}
					do_action( 'abprf_registration', $abprf_infos );
					$msg = get_the_title( $post_id ) . ' ' . __( 'Loaded Successfully.....! ', 'abp-rentalforge' );
				} else {
					$params['all_post'] = ABPRF_Query::get_post_id( $params );
					do_action( 'abprf_post_filter', $params );
					include_once ABPRF_Function::template_path( 'list/default.php' );
					do_action( 'abprf_default_template', $params );
					$msg = __( 'Post List Loaded Successfully.....! ', 'abp-rentalforge' );
				}
				$details = ob_get_clean();
				wp_send_json_success( [ 'time_info' => $all_time, 'start_date' => $all_dates, 'end_date' => $all_end_dates, 'details' => $details, 'form' => $form_html, 'msg' => $msg ] );
			}
			public function load_property(): void {
				if ( ! check_ajax_referer( 'abprf_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'msg' => esc_html__( 'Session expired. Please refresh the page.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int                    = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val                    = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$abprf_infos                 = [];
				$post_id                     = $post_int( 'post_id' );
				$rent_start_date             = $post_val( 'rent_start_date' );
				$rent_end_date               = $post_val( 'rent_end_date' );
				$rent_rule                   = $post_val( 'rent_rule' );
				$rent_rule                   = ! empty( $rent_rule ) ? $rent_rule : ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
				$location                    = $post_val( 'location' );
				$start                       = '';
				$end                         = '';
				$filter_arg['rent_continue'] = 'on';
				$filter_arg['status']        = 'publish';
				$date_info                   = [];
				if ( ! empty( $post_id ) ) {
					$abprf_infos['post_id'] = $post_id;
					$filter_arg['post_id']  = $post_id;
				}
				if ( ! empty( $rent_rule ) ) {
					$abprf_infos['rent_rule'] = $rent_rule;
					$filter_arg['rent_rule']  = $rent_rule;
				}
				if ( ! empty( $location ) ) {
					$abprf_infos['location'] = $location;
					$filter_arg['location']  = $location;
				}
				$properties = ABPRF_Query::get_property( $filter_arg );
				if ( 'hourly' === $rent_rule && ! empty( $rent_start_date ) ) {
					$start_time = $post_val( 'start_time' );
					$end_time   = $post_val( 'end_time' );
					$start      = $rent_start_date . ' ' . $start_time;
					$end        = $rent_start_date . ' ' . $end_time;
					$date_info  = ABPRF_Function::get_date_time_difference( $start, $end, $rent_rule );
				} elseif ( ( 'daily' === $rent_rule || 'multi_month' === $rent_rule ) && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
					$start     = gmdate( 'Y-m-d', strtotime( $rent_start_date ) );
					$end       = gmdate( 'Y-m-d', strtotime( $rent_end_date ) );
					$date_info = ABPRF_Function::get_date_time_difference( $start, $end, $rent_rule );
				} elseif ( 'multi_day' === $rent_rule && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
					$start_time = $post_val( 'start_time' );
					$end_time   = $post_val( 'end_time' );
					$start      = $rent_start_date . ' ' . $start_time;
					$end        = $rent_end_date . ' ' . $end_time;
					$date_info  = ABPRF_Function::get_date_time_difference( $start, $end, $rent_rule );
				} elseif ( 'monthly' === $rent_rule && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
					$start     = $rent_start_date;
					$end       = $rent_end_date;
					$date_info = ABPRF_Function::get_date_time_difference( $start, $end, $rent_rule );
				}
				$abprf_infos['start_time'] = $start;
				$abprf_infos['end_time']   = $end;
				$abprf_infos['date_info']  = $date_info;
				$check_date                = ABPRF_Function::check_date_exit( $abprf_infos );
				ob_start();
				if ( ! empty( $post_id ) && ! empty( $properties ) && $check_date ) {
					$template = ABPRF_Function::get_post_info( $post_id, 'abprf_template', 'grid' );
					?>
                    <input type="hidden" name="start_time" value="<?php echo esc_attr( $start ); ?>"/>
                    <input type="hidden" name="end_time" value="<?php echo esc_attr( $end ); ?>"/>
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                    <input type="hidden" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
                    <input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>"/>
					<?php
					if ( empty( $template ) || 'grid' === $template ) {
						foreach ( $properties as $property ) {
							do_action( 'abprf_property_item', $abprf_infos, $property );
						}
					} else {
						do_action( 'abprf_property_item_group', $abprf_infos, $properties );
					}
				} else {
					ABPRF_Layout::layout_warning_info( 'no_property_found' );
				}
				$property_info = ob_get_clean();
				ob_start();
				if ( ! empty( $post_id ) && $check_date ) {
					do_action( 'abprf_additional', $post_id, $abprf_infos );
					do_action( 'abprf_client_form', $post_id, $abprf_infos );
					do_action( 'abprf_total_price', $abprf_infos );
				}
				$property_others = ob_get_clean();
				ob_start();
				do_action( 'abprf_rental_duration', $abprf_infos );
				$date_details = ob_get_clean();
				wp_send_json_success( [
					'property_info' => $property_info,
					'property_others' => $property_others,
					'date_details' => $date_details,
					'msg' => __( 'Property List already Loaded !', 'abp-rentalforge' )
				] );
			}
			public function load_end_date(): void {
				if ( ! check_ajax_referer( 'abprf_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'msg' => esc_html__( 'Session expired. Please refresh the page.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_id         = $post_int( 'post_id' );
				$rent_start_date = $post_val( 'rent_start_date' );
				$rent_rule = $post_val( 'rent_rule' );
				$rent_start_date   = ! empty( $rent_start_date ) ? gmdate( 'Y-m-d', strtotime( $rent_start_date ) ) : '';
				$new_picker_config = [];
				ob_start();
				if ( 'monthly' === $rent_rule ) {
					ABPRF_Layout::rent_end_month( $post_id, $rent_start_date );
				} else {
					$all_end_dates = ABPRF_Function::get_end_dates( $post_id, $rent_start_date );
					ABPRF_Layout::rent_end_date( $all_end_dates, $post_id );
					$new_picker_config = ! empty( $all_end_dates ) ? ABPRF_Layout::create_datepicker_array( $all_end_dates ) : '';
				}
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'msg' => esc_html__( 'Rent End Date Loaded successfully.', 'abp-rentalforge' ),
					'picker_config' => $new_picker_config,
					'selector' => '#end_date'
				] );
			}
		}
		new ABPRF_Ajax();
	}