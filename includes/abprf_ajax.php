<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Ajax' ) ) {
		class ABPRF_Ajax {
			public function __construct() {
				add_action( 'wp_ajax_abprf_load_property', [ $this, 'load_property' ] );
				add_action( 'wp_ajax_nopriv_abprf_load_property', [ $this, 'load_property' ] );
				add_action( 'wp_ajax_abprf_load_end_date', [ $this, 'load_end_date' ] );
				add_action( 'wp_ajax_nopriv_abprf_load_end_date', [ $this, 'load_end_date' ] );
			}

			public function load_property() {
				if ( check_ajax_referer( 'abprf_ajax_nonce', 'nonce' ) ) {
					//echo '<pre>'; print_r( $_POST ); echo '</pre>';
					$abprf_infos     = [];
					$post_id         = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
					$location        = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
					$rent_rule       = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : 'hourly';
					$rent_start_date = isset( $_POST['rent_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_start_date'] ) ) : '';
					$rent_end_date   = isset( $_POST['rent_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_end_date'] ) ) : '';
					//$all_dates=ABPRF_Function::get_post_dates($post_id);
					$start                       = $end = '';
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
					if ( $rent_rule == 'hourly' && ! empty( $rent_start_date ) ) {
						$start_time = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
						$end_time   = isset( $_POST['end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_time'] ) ) : '';
						$start      = $rent_start_date . ' ' . $start_time;
						$end        = $rent_start_date . ' ' . $end_time;
						$date_info  = ABPRF_Function::get_date_time_difference( $start, $end,$rent_rule );
					} elseif ( ($rent_rule == 'daily' || $rent_rule == 'multi_month') && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
						$start     = gmdate( 'Y-m-d', strtotime( $rent_start_date ) );
						$end       = gmdate( 'Y-m-d', strtotime( $rent_end_date ) );
						$date_info = ABPRF_Function::get_date_time_difference( $start, $end,$rent_rule );
					} elseif ( $rent_rule == 'multi_day' && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
						$start_time = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
						$end_time   = isset( $_POST['end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_time'] ) ) : '';
						$start      = $rent_start_date . ' ' . $start_time;
						$end        = $rent_end_date . ' ' . $end_time;
						$date_info  = ABPRF_Function::get_date_time_difference( $start, $end,$rent_rule );
					}elseif ( $rent_rule == 'monthly' && ! empty( $rent_start_date ) && ! empty( $rent_end_date ) ) {
						$start      = $rent_start_date ;
						$end        = $rent_end_date;
						$date_info  = ABPRF_Function::get_date_time_difference( $start, $end,$rent_rule );
					}
					$abprf_infos['start_time'] = $start;
					$abprf_infos['end_time']   = $end;
					$abprf_infos['date_info']  = $date_info;
					$check_date                = ABPRF_Function::check_date_exit( $abprf_infos );
					ob_start();
					//echo '<pre>';print_r( $abprf_infos);					echo '</pre>';
					if ( ! empty( $post_id ) && ! empty( $properties ) && sizeof( $properties ) > 0 && $check_date ) {
						$template = ABPRF_Function::get_post_info( $post_id, 'abprf_template', 'grid' );
						?>
                        <input type="hidden" name="start_time" value="<?php echo esc_attr( $start ); ?> "/>
                        <input type="hidden" name="end_time" value="<?php echo esc_attr( $end ); ?> "/>
                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?> "/>
                        <input type="hidden" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
                        <input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>"/>
						<?php
						if ( empty( $template ) || $template == 'grid' ) {
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
					if ( ! empty( $post_id ) && $post_id > 0 && $check_date ) {
						do_action( 'abprf_additional', $post_id, $abprf_infos );
						do_action( 'abprf_client_form', $post_id, $abprf_infos );
						do_action( 'abprf_total_price', $abprf_infos );
					}
					$property_others = ob_get_clean();
					ob_start();
					do_action( 'abprf_rental_duration', $abprf_infos );
					$date_details = ob_get_clean();
					wp_send_json_success( [ 'property_info' => $property_info, 'property_others' => $property_others, 'date_details' => $date_details ] );
				}
				wp_die();
			}

			public function load_end_date() {
				if ( check_ajax_referer( 'abprf_ajax_nonce', 'nonce' ) ) {
					$post_id         = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
					$rent_rule         = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : '';
					$rent_start_date = isset( $_POST['rent_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_start_date'] ) ) : '';
					$rent_start_date = ! empty( $rent_start_date ) ? gmdate( 'Y-m-d', strtotime( $rent_start_date ) ) : '';
					ob_start();
                    if($rent_rule=='monthly'){
                        ABPRF_Layout::rent_end_month($post_id, $rent_start_date);
                    }else {
	                    $all_end_dates   = ABPRF_Function::get_end_dates( $post_id, $rent_start_date );
	                    ABPRF_Layout::rent_end_date( $all_end_dates );
                    }
					$html = ob_get_clean();
					wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Rent End Date Loaded...|', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}
		}
		new ABPRF_Ajax();
	}