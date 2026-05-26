<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Ajax' ) ) {
		class ABPRF_Ajax {
			public function __construct() {
				add_action( 'wp_ajax_abprf_load_property', [ $this, 'load_property' ] );
				add_action( 'wp_ajax_nopriv_abprf_load_property', [ $this, 'load_property' ] );
			}

			public function load_property() {
				if ( check_ajax_referer( 'abprf_ajax_nonce', 'nonce' ) ) {
					//echo '<pre>'; print_r( $_POST ); echo '</pre>';
					$abprf_infos = [];
					$post_id     = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
					$location        = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
					$rent_rule       = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : 'hourly';
					$rent_start_date = isset( $_POST['rent_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_start_date'] ) ) : '';
					//$all_dates=ABPRF_Function::get_post_dates($post_id);
					$start                       = $end = '';
					$filter_arg['rent_continue'] = 'on';
					$filter_arg['status']        = 'publish';
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
					}
					$abprf_infos['start_time'] = $start;
					$abprf_infos['end_time']   = $end;
					$abprf_infos['date_info']  = ABPRF_Function::get_date_time_difference( $start, $end );
                    $check_date=ABPRF_Function::check_date_exit( $abprf_infos );
					ob_start();
					//echo '<pre>';print_r( ABPRF_Function::get_time($post_id));					echo '</pre>';
					//echo '<pre>';print_r( ABPRF_Function::get_time($post_id,'js'));					echo '</pre>';
					if ( ! empty( $post_id ) && ! empty( $properties ) && sizeof( $properties ) > 0 &&  $check_date) {
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
					if ( ! empty( $post_id ) && $post_id > 0 && $check_date) {
						do_action( 'abprf_additional', $post_id, $abprf_infos );
						do_action( 'abprf_client_form', $post_id, $abprf_infos );
						do_action( 'abprf_total_price', $abprf_infos );
					}
					$property_others = ob_get_clean();
					ob_start();
					do_action( 'abprf_rental_duration', $abprf_infos['date_info'] );
					$date_details = ob_get_clean();
					wp_send_json_success( [ 'property_info' => $property_info, 'property_others' => $property_others, 'date_details' => $date_details ] );
				}
				wp_die();
			}
		}
		new ABPRF_Ajax();
	}