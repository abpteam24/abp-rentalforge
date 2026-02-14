<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Configuration_Post' ) ) {
		class ABPRF_Configuration_Post {
			public function __construct() {
				add_action( 'add_meta_boxes', [ $this, 'settings_meta' ] );
				add_action( 'save_post', array( $this, 'save_settings' ) );
			}

			//=============================//
			public function settings_meta(): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option( 'abprf_configuration' );
				$label               = isset( $abprf_configuration['label'] ) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __( 'RentalForge', 'abprf-rental-forge' );
				$equipment_icon      = isset( $abprf_configuration['equipment_icon'] ) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				$label               = $label . ' ' . __( 'Configuration', 'abprf-rental-forge' ) . get_the_title( get_the_id() );
				add_meta_box( 'abprf_configuration', '<span class="' . esc_attr( $equipment_icon ?: '' ) . '"></span>' . esc_html( $label ), array( $this, 'settings' ), esc_attr( ABPRF_Function::get_cpt() ), 'normal', 'high' );
			}

			//=============================//
			public function settings(): void {
				$post_id             = get_the_id();
				$abprf_infos         = ABPRF_LIB_Function::get_all_meta( $post_id );
				$abprf_configuration = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : [];
				$equipment_icon      = isset( $abprf_configuration['equipment_icon'] ) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				wp_nonce_field( 'abprf_post_nonce', 'abprf_post_nonce' );
				?>
                <input type="hidden" name="abprf_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                <div class="abprf_area">
                    <div class="_reflex_6_abprf_panel">
                        <div class="abprf_tabs tab_left">
                            <ul class="_abprf tab_lists">
                                <li data-tabs-target="#abprf_general"><span class="fas fa-rainbow"></span><?php esc_html_e( 'General Configuration', 'abprf-rental-forge' ); ?></li>
                                <li data-tabs-target="#abprf_dates"><span class="fas fa-calendar-check"></span><?php esc_html_e( 'Date Configuration', 'abprf-rental-forge' ); ?></li>
                                <li data-tabs-target="#abprf_equipment_price"><span class="<?php echo esc_attr( $equipment_icon ); ?>"></span><?php esc_html_e( 'Equipment and Price', 'abprf-rental-forge' ); ?></li>
                                <li data-tabs-target="#abprf_additional_service"><span class="fas fa-hand-holding-usd"></span><?php esc_html_e( 'Additional services', 'abprf-rental-forge' ); ?></li>
								<?php do_action( 'abprf_post_tab_menu', $abprf_infos ); ?>
                                <li data-tabs-target="#abprf_slider"><span class="fas fa-photo-video"></span><?php esc_html_e( 'Slider Configuration', 'abprf-rental-forge' ); ?></li>
                                <li data-tabs-target="#abprf_tax"><span class="fas fa-money-bill-wave"></span><?php esc_html_e( 'Tax Configuration', 'abprf-rental-forge' ); ?></li>
                            </ul>
                            <div class="tab_content">
								<?php do_action( 'abprf_post_content', $abprf_infos ); ?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function save_settings( $post_id ): void {
				if ( ! isset( $_POST['abprf_post_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) && defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE && ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
					$meta_info                         = [];
					$meta_info['sale_continue']        = isset( $_POST['sale_continue'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_continue'] ) ) : 'off';
					$meta_info['display_equipment_id'] = isset( $_POST['display_equipment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['display_equipment_id'] ) ) : 'off';
					$meta_info['equipment_id']         = isset( $_POST['equipment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['equipment_id'] ) ) : '';
					$meta_info['display_category']     = isset( $_POST['display_category'] ) ? sanitize_text_field( wp_unslash( $_POST['display_category'] ) ) : 'off';
					$meta_info['category']             = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
					$meta_info['abprf_template']       = isset( $_POST['abprf_template'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_template'] ) ) : 'default';
					//=============================//
					$meta_info['date_type']            = isset( $_POST['date_type'] ) ? sanitize_text_field( wp_unslash( $_POST['date_type'] ) ) : 'periodic_date';
					$periodic_start_date               = isset( $_POST['periodic_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_start_date'] ) ) : '';
					$periodic_end_date                 = isset( $_POST['periodic_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_end_date'] ) ) : '';
					$meta_info['periodic_start_date']  = $periodic_start_date ? gmdate( 'Y-m-d', strtotime( $periodic_start_date ) ) : '';
					$meta_info['periodic_end_date']    = $periodic_end_date ? gmdate( 'Y-m-d', strtotime( $periodic_end_date ) ) : '';
					$meta_info['operation_time_start'] = isset( $_POST['operation_time_start'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_start'] ) ) : '';
					$meta_info['operation_time_end']   = isset( $_POST['operation_time_end'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_end'] ) ) : '';
					$meta_info['periodic_after']       = isset( $_POST['periodic_after'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_after'] ) ) : '1';
					$meta_info['advance_date_number']  = isset( $_POST['advance_date_number'] ) ? sanitize_text_field( wp_unslash( $_POST['advance_date_number'] ) ) : '';
					$meta_info['weekend']              = isset( $_POST['weekend'] ) ? sanitize_text_field( wp_unslash( $_POST['weekend'] ) ) : '';
					$specific_off_dates                = isset( $_POST['specific_off_dates'] ) && is_array( $_POST['specific_off_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_off_dates'] ) ) : [];
					$off_dates                         = array();
					if ( sizeof( $specific_off_dates ) > 0 ) {
						foreach ( $specific_off_dates as $off_date ) {
							if ( $off_date ) {
								$off_dates[] = gmdate( 'Y-m-d', strtotime( $off_date ) );
							}
						}
					}
					$meta_info['specific_off_dates'] = array_unique( $off_dates );
					$off_schedules                   = [];
					$from_dates                      = isset( $_POST['abprf_off_from'] ) && is_array( $_POST['abprf_off_from'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_from'] ) ) : [];
					$to_dates                        = isset( $_POST['abprf_off_to'] ) && is_array( $_POST['abprf_off_to'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_to'] ) ) : [];
					if ( sizeof( $from_dates ) > 0 ) {
						foreach ( $from_dates as $key => $from_date ) {
							if ( $from_date && $to_dates[ $key ] ) {
								$off_schedules[] = [
									'from' => $from_date,
									'to' => $to_dates[ $key ],
								];
							}
						}
					}
					$meta_info['off_date_range'] = $off_schedules;
					//======================//
					$specific_dates      = isset( $_POST['specific_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_dates'] ) ) : [];
					$specific_time_start = isset( $_POST['specific_time_start'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_start'] ) ) : [];
					$specific_time_end   = isset( $_POST['specific_time_end'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_end'] ) ) : [];
					$specific            = array();
					if ( sizeof( $specific_dates ) > 0 ) {
						foreach ( $specific_dates as $key => $specific_date ) {
							if ( $specific_date ) {
								$specific['date']  = gmdate( 'Y-m-d', strtotime( $specific_date ) );
								$specific['start'] = array_key_exists( $key, $specific_time_start ) ? $specific_time_start[ $key ] : '';
								$specific['end']   = array_key_exists( $key, $specific_time_end ) ? $specific_time_end[ $key ] : '';
							}
						}
					}
					$meta_info['specific_dates'] = array_unique( $specific );
					//=============================//
					$ticket_infos  = array();
					$hidden_ids    = isset( $_POST['equipment_hidden_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_hidden_id'] ) ) : [];
					$icon          = isset( $_POST['equipment_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_icon'] ) ) : [];
					$name          = isset( $_POST['equipment_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_name'] ) ) : [];
					$qty           = isset( $_POST['equipment_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_qty'] ) ) : [];
					$max_qty       = isset( $_POST['equipment_max_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_max_qty'] ) ) : [];
					$hourly_price  = isset( $_POST['hourly_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['hourly_price'] ) ) : '';
					$daily_price   = isset( $_POST['daily_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['daily_price'] ) ) : '';
					$weekly_price  = isset( $_POST['weekly_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['weekly_price'] ) ) : '';
					$monthly_price = isset( $_POST['monthly_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['monthly_price'] ) ) : '';
					$description   = isset( $_POST['equipment_description'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['equipment_description'] ) ) : [];
					if ( sizeof( $hidden_ids ) > 0 ) {
						foreach ( $hidden_ids as $key => $hidden_id ) {
							$hidden_id = ! empty( $hidden_id ) ? $hidden_id : uniqid();
							if ( $name[ $key ] && $qty[ $key ] > 0 && ( $hourly_price[ $key ] || $daily_price[ $key ] || $weekly_price[ $key ] || $monthly_price[ $key ] ) ) {
								$ticket_infos[ $hidden_id ]['icon']          = $icon[ $key ] ?? '';
								$ticket_infos[ $hidden_id ]['name']          = $name[ $key ];
								$ticket_infos[ $hidden_id ]['qty']           = $qty[ $key ];
								$ticket_infos[ $hidden_id ]['max_qty']       = $max_qty[ $key ];
								$ticket_infos[ $hidden_id ]['hourly_price']  = $hourly_price[ $key ];
								$ticket_infos[ $hidden_id ]['daily_price']   = $daily_price[ $key ];
								$ticket_infos[ $hidden_id ]['weekly_price']  = $weekly_price[ $key ];
								$ticket_infos[ $hidden_id ]['monthly_price'] = $monthly_price[ $key ];
								$ticket_infos[ $hidden_id ]['description']   = $description[ $key ] ?? '';
							}
						}
						$meta_info['equipment_infos'] = $ticket_infos;
					}
					//=============================//
					$meta_info['display_additional_services'] = isset( $_POST['display_additional_services'] ) ? sanitize_text_field( wp_unslash( $_POST['display_additional_services'] ) ) : 'off';
					$meta_info['additional_services']         = ABPRF_Additional::service_info();
					//===========gallery==================//
					$images                      = isset( $_POST['abprf_sliders'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_sliders'] ) ) : '';
					$meta_info['display_slider'] = isset( $_POST['display_slider'] ) ? sanitize_text_field( wp_unslash( $_POST['display_slider'] ) ) : 'off';
					$meta_info['abprf_sliders']  = explode( ',', $images );
					//=============================//
					if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
						$meta_info['_tax_status'] = isset( $_POST['_tax_status'] ) ? sanitize_text_field( wp_unslash( $_POST['_tax_status'] ) ) : 'none';
						$meta_info['_tax_class']  = isset( $_POST['_tax_class'] ) ? sanitize_text_field( wp_unslash( $_POST['_tax_class'] ) ) : '';
					}
					//=============================//
					$meta_info = apply_filters( 'abprf_meta_info_update', $meta_info );
					if ( sizeof( $meta_info ) > 0 ) {
						foreach ( $meta_info as $key => $value ) {
							update_post_meta( $post_id, sanitize_key( $key ), $value );
						}
					}
				}
			}
		}
		new ABPRF_Configuration_Post();
	}