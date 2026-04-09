<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dates' ) ) {
		class ABPRF_Dates {
			public function __construct() {
				add_action( 'abprf_load_dates', array( $this, 'load_global_dates' ) );
				add_action( 'abprf_post_content', array( $this, 'post_content_dates' ) );
				add_action( 'wp_ajax_abprf_save_dates', array( $this, 'save_global_date' ) );
				add_filter( 'abprf_get_date_array', array( $this, 'get_date_array' ) );
			}

			public function load_global_dates(): void {
				$abprf_dates = ABPRF_Function::get_option( 'abprf_dates' );
				?>
                <form class="_section_xs abprf_save_dates" method="post" action="">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🗓️</span> <?php esc_html_e( 'Global Date Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
					<?php $this->global_start_end_date( $abprf_dates );
						$this->date_format_buffer( $abprf_dates );
						$this->date_content( $abprf_dates ); ?>
                    <div class="_divider_xs"></div>
                    <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Date Configuration', 'abprf-rental-forge' ); ?></button>
                </form>
				<?php
			}

			public function post_content_dates( $abprf_infos ): void {
				$abprf_dates         = array_key_exists( 'abprf_dates', $abprf_infos ) ? $abprf_infos['abprf_dates'] : [];
				$active_global_dates = array_key_exists( 'active_global_dates', $abprf_infos ) ? $abprf_infos['active_global_dates'] : 'on';
				?>
                <div class="tab_item" data-tabs="#abprf_dates">
                    <h4 class="_abprf_color_theme"><span class=" _mar_r_xs">🗓️</span> <?php esc_html_e( 'Date Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox( 'active_global_dates', $active_global_dates ); ?>
                            <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Use Global Date Configuration?', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'active_global_dates' ); ?>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_dates == 'off' ? 'rf_active' : '' ); ?>" data-collapse="#active_global_dates">
						<?php $this->date_content( $abprf_dates ); ?>
                    </div>
                </div>
				<?php
			}

			public function date_content( $abprf_dates ): void {
				$date_type = array_key_exists( 'date_type', $abprf_dates ) ? $abprf_dates['date_type'] : 'periodic_date';
				$weekend   = array_key_exists( 'weekend', $abprf_dates ) ? $abprf_dates['weekend'] : '';
				$this->date_type_advance_date( $abprf_dates );
				$this->specific_date_settings( $abprf_dates ); ?>
                <div class="<?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-collapse="#periodic_date">
					<?php $this->rent_start_end_date( $abprf_dates );
						$this->operation_time_periodic( $abprf_dates );
						$this->off_dates( $abprf_dates );
						$this->select_weekend( $weekend ); ?>
                </div>
				<?php
			}

			public function save_global_date() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$abprf_dates                             = $this->get_date_array();
					$abprf_dates['global_start_date']        = isset( $_POST['global_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['global_start_date'] ) ) : '';
					$abprf_dates['global_end_date']          = isset( $_POST['global_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['global_end_date'] ) ) : '';
					$abprf_dates['date_picker_format']       = isset( $_POST['date_picker_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_picker_format'] ) ) : '';
					$abprf_dates['ticket_sale_close_before'] = isset( $_POST['ticket_sale_close_before'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket_sale_close_before'] ) ) : '';
					update_option( 'abprf_dates', $abprf_dates );
					wp_send_json_success( esc_html__( 'Date Configuration Saved Successfully ! ', 'abprf-rental-forge' ) );
				} else {
					wp_send_json_success( esc_html__( 'Date Configuration not Saved !', 'abprf-rental-forge' ) );
				}
				wp_die();
			}

			public function get_date_array( $abprf_dates = [] ) {
				if ( is_admin() && ( ( isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) || check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) ) ) {
					$abprf_dates['date_type']            = isset( $_POST['date_type'] ) ? sanitize_text_field( wp_unslash( $_POST['date_type'] ) ) : 'periodic_date';
					$abprf_dates['advance_date_number']  = isset( $_POST['advance_date_number'] ) ? sanitize_text_field( wp_unslash( $_POST['advance_date_number'] ) ) : '15';
					$periodic_start_date                 = isset( $_POST['periodic_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_start_date'] ) ) : '';
					$periodic_end_date                   = isset( $_POST['periodic_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_end_date'] ) ) : '';
					$abprf_dates['periodic_start_date']  = $periodic_start_date ? gmdate( 'Y-m-d', strtotime( $periodic_start_date ) ) : '';
					$abprf_dates['periodic_end_date']    = $periodic_end_date ? gmdate( 'Y-m-d', strtotime( $periodic_end_date ) ) : '';
					$abprf_dates['operation_time_start'] = isset( $_POST['operation_time_start'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_start'] ) ) : '';
					$abprf_dates['operation_time_end']   = isset( $_POST['operation_time_end'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_end'] ) ) : '';
					$abprf_dates['periodic_after']       = isset( $_POST['periodic_after'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_after'] ) ) : '1';
					$abprf_dates['weekend']              = isset( $_POST['weekend'] ) ? sanitize_text_field( wp_unslash( $_POST['weekend'] ) ) : '';
					$specific_off_dates                  = isset( $_POST['specific_off_dates'] ) && is_array( $_POST['specific_off_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_off_dates'] ) ) : [];
					$off_dates                           = array();
					if ( sizeof( $specific_off_dates ) > 0 ) {
						foreach ( $specific_off_dates as $off_date ) {
							if ( $off_date ) {
								$off_dates[] = gmdate( 'Y-m-d', strtotime( $off_date ) );
							}
						}
					}
					$abprf_dates['specific_off_dates'] = array_unique( $off_dates );
					$off_schedules                     = [];
					$from_dates                        = isset( $_POST['abprf_off_from'] ) && is_array( $_POST['abprf_off_from'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_from'] ) ) : [];
					$to_dates                          = isset( $_POST['abprf_off_to'] ) && is_array( $_POST['abprf_off_to'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_to'] ) ) : [];
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
					$abprf_dates['off_date_range'] = $off_schedules;
					//======================//
					$specific_dates      = isset( $_POST['specific_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_dates'] ) ) : [];
					$specific_time_start = isset( $_POST['specific_time_start'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_start'] ) ) : [];
					$specific_time_end   = isset( $_POST['specific_time_end'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_end'] ) ) : [];
					$specific            = array();
					if ( sizeof( $specific_dates ) > 0 ) {
						foreach ( $specific_dates as $key => $specific_date ) {
							if ( $specific_date ) {
								$specific[ $key ]['date']  = gmdate( 'Y-m-d', strtotime( $specific_date ) );
								$specific[ $key ]['start'] = array_key_exists( $key, $specific_time_start ) ? $specific_time_start[ $key ] : '';
								$specific[ $key ]['end']   = array_key_exists( $key, $specific_time_end ) ? $specific_time_end[ $key ] : '';
							}
						}
					}
					$abprf_dates['specific_dates'] = $specific;
				}

				return $abprf_dates;
			}

			//=============================//
			public function global_start_end_date( $abprf_dates ): void {
				$start_date = array_key_exists( 'global_start_date', $abprf_dates ) ? $abprf_dates['global_start_date'] : '';
				$end_date   = array_key_exists( 'global_end_date', $abprf_dates ) ? $abprf_dates['global_end_date'] : '';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Available From (Optional)', 'abprf-rental-forge' ); ?></span>
							<?php ABPRF_Layout::input_date( 'global_start_date', $start_date ); ?>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'global_start_date' ); ?>
                    </div>
                    <div class="_setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Available To (Optional)', 'abprf-rental-forge' ); ?></span>
							<?php ABPRF_Layout::input_date( 'global_end_date', $end_date ); ?>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'global_end_date' ); ?>
                    </div>
                </div>
				<?php
			}

			public function date_format_buffer( $abprf_dates ): void {
				$format_array = ABPRF_Layout::date_picker_format_array();
				$date_format  = array_key_exists( 'date_picker_format', $abprf_dates ) ? $abprf_dates['date_picker_format'] : 'D d M , yy';
				$buffer_time  = array_key_exists( 'ticket_sale_close_before', $abprf_dates ) ? $abprf_dates['ticket_sale_close_before'] : '0';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <label class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Date Picker Format', 'abprf-rental-forge' ); ?></span>
							<?php if ( sizeof( $format_array ) > 0 ) { ?>
                                <select class="_form_control " name="date_picker_format">
									<?php foreach ( $format_array as $key => $format ) { ?>
                                        <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $date_format == $key ? 'selected' : '' ); ?>><?php echo esc_html( $format ); ?></option>
									<?php } ?>
                                </select>
							<?php } ?>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'date_picker_format' ); ?>
                    </div>
                    <div class="_setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Buffer time in MIN (Optional)', 'abprf-rental-forge' ); ?></span>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="ticket_sale_close_before" placeholder="Ex: 15" value="<?php echo esc_attr( $buffer_time ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'ticket_sale_close_before' ); ?>
                    </div>
                </div>
				<?php
			}

			public function date_type_advance_date( $abprf_dates ): void {
				$date_type           = array_key_exists( 'date_type', $abprf_dates ) ? $abprf_dates['date_type'] : 'periodic_date';
				$advance_date_number = array_key_exists( 'advance_date_number', $abprf_dates ) ? $abprf_dates['advance_date_number'] : '15';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <label class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Operational Date Type', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <select class="_form_control " name="date_type" data-collapse-target required>
                                <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
                                <option value="specific_date" data-option-target="#specific_date" <?php echo esc_attr( $date_type == 'specific_date' ? 'selected' : '' ); ?>><?php esc_html_e( 'Specific Dates', 'abprf-rental-forge' ); ?></option>
                                <option value="periodic_date" data-option-target="#periodic_date" <?php echo esc_attr( $date_type == 'periodic_date' ? 'selected' : '' ); ?>><?php esc_html_e( 'Periodic Dates', 'abprf-rental-forge' ); ?></option>
                            </select>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'date_type' ); ?>
                    </div>
                    <div class="_setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Number of advance booking date', 'abprf-rental-forge' ); ?></span>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="advance_date_number" placeholder="Ex: 15" value="<?php echo esc_attr( $advance_date_number ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'advance_date_number' ); ?>
                    </div>
                </div>
				<?php
			}

			public function rent_start_end_date( $abprf_dates ): void {
				$start_date = array_key_exists( 'periodic_start_date', $abprf_dates ) ? $abprf_dates['periodic_start_date'] : '';
				$end_date   = array_key_exists( 'periodic_end_date', $abprf_dates ) ? $abprf_dates['periodic_end_date'] : '';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Rent From (Optional)', 'abprf-rental-forge' ); ?></span>
							<?php ABPRF_Layout::input_date( 'periodic_start_date', $start_date ); ?>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'periodic_start_date' ); ?>
                    </div>
                    <div class="_setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Rent To (Optional)', 'abprf-rental-forge' ); ?></span>
							<?php ABPRF_Layout::input_date( 'periodic_end_date', $end_date ); ?>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'periodic_end_date' ); ?>
                    </div>
                </div>
				<?php
			}

			public function operation_time_periodic( $abprf_dates ): void {
				$operation_time_start = array_key_exists( 'operation_time_start', $abprf_dates ) ? $abprf_dates['operation_time_start'] : '';
				$operation_time_end   = array_key_exists( 'operation_time_end', $abprf_dates ) ? $abprf_dates['operation_time_end'] : '';
				$periodic_after       = array_key_exists( 'periodic_after', $abprf_dates ) ? $abprf_dates['periodic_after'] : '1';
				?>
                <div class="group_setting">
                    <div class=" _setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Operation Time', 'abprf-rental-forge' ); ?></span>
                            <div class="_group_content">
								<?php ABPRF_Layout::input_time( 'operation_time_start', $operation_time_start );
									ABPRF_Layout::input_time( 'operation_time_end', $operation_time_end ); ?>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'operation_time' ); ?>
                    </div>
                    <div class="_setting_item">
                        <label class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Periodic after', 'abprf-rental-forge' ); ?></span>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="periodic_after" placeholder="Ex: 5" value="<?php echo esc_attr( $periodic_after ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'periodic_after' ); ?>
                    </div>
                </div>
				<?php
			}

			public function off_dates( $abprf_dates ): void {
				$specific_off_dates = array_key_exists( 'specific_off_dates', $abprf_dates ) ? $abprf_dates['specific_off_dates'] : [];
				$off_date_range     = array_key_exists( 'off_date_range', $abprf_dates ) ? $abprf_dates['off_date_range'] : [];
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <div class="_f_wrap_fj_between">
                            <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Off Dates', 'abprf-rental-forge' ); ?></span>
                            <div class="configuration_content">
                                <div class="insertable_area sortable_area">
									<?php
										if ( sizeof( $specific_off_dates ) ) {
											foreach ( $specific_off_dates as $specific_date ) {
												if ( $specific_date ) {
													$this->date_item( 'specific_off_dates[]', $specific_date );
												}
											}
										}
									?>
                                </div>
								<?php ABPRF_Layout::button_add( __( 'Add Specific Off Date', 'abprf-rental-forge' ) ); ?>
                                <div class="abprf_d_none">
                                    <div class="hidden_content">
										<?php $this->date_item( 'specific_off_dates[]' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'specific_off_dates' ); ?>
                    </div>
                    <div class="_setting_item  date_range">
                        <div class="_f_wrap_fj_between">
                            <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Off Date Range', 'abprf-rental-forge' ); ?></span>
                            <div class="configuration_content">
                                <div class="insertable_area sortable_area">
									<?php
										if ( sizeof( $off_date_range ) ) {
											foreach ( $off_date_range as $specific_date ) {
												if ( sizeof( $specific_date ) > 0 && $specific_date['from'] && $specific_date['to'] ) {
													$this->off_day_range( $specific_date['from'], $specific_date['to'] );
												}
											}
										}
									?>
                                </div>
								<?php ABPRF_Layout::button_add( __( 'Add Off Date Range', 'abprf-rental-forge' ) ); ?>
                                <div class="abprf_d_none">
                                    <div class="hidden_content">
										<?php $this->off_day_range(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'off_date_range' ); ?>
                    </div>
                </div>
				<?php
			}

			public function select_weekend( $weekend ): void {
				$off_day_array = $weekend ? explode( ',', $weekend ) : [];
				$days          = ABPRF_Layout::week_day(); ?>
                <div class="_setting_item ">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Weekend', 'abprf-rental-forge' ); ?></span>
                        <div class="abprf_checkbox">
                            <input type="hidden" name="weekend" value="<?php echo esc_attr( $weekend ); ?>"/>
							<?php foreach ( $days as $key => $day ) { ?>
                                <div class="checkbox_item _min_100">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr( in_array( $key, $off_day_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( in_array( $key, $off_day_array ) ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $day ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'weekend' ); ?>
                </div>
				<?php
			}

			public function off_day_range( $from_date = '', $to_date = '' ): void {
				?>
                <div class="delete_area ">
                    <div class="_all_center">
                        <div class="_group_content">
							<?php
								ABPRF_Layout::button_sort();
								ABPRF_Layout::input_date( 'abprf_off_from[]', $from_date );
								ABPRF_Layout::input_date( 'abprf_off_to[]', $to_date );
								ABPRF_Layout::button_delete();
							?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}

			public function date_item( $name, $date = '' ): void {
				?>
                <div class="delete_area ">
                    <div class="_all_center">
                        <div class="_group_content">
							<?php
								ABPRF_Layout::button_sort();
								ABPRF_Layout::input_date( $name, $date );
								ABPRF_Layout::button_delete();
							?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}

			public function specific_date_settings( $abprf_dates ): void {
				$date_type      = array_key_exists( 'date_type', $abprf_dates ) ? $abprf_dates['date_type'] : 'periodic_date';
				$specific_dates = array_key_exists( 'specific_dates', $abprf_dates ) ? $abprf_dates['specific_dates'] : [];
				?>
                <div class="_setting_item  <?php echo esc_attr( $date_type == 'specific_date' ? 'rf_active' : '' ); ?>" data-collapse="#specific_date">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Dates', 'abprf-rental-forge' ); ?></span>
                        <div class="configuration_content">
                            <div class="insertable_area sortable_area">
								<?php
									if ( sizeof( $specific_dates ) ) {
										foreach ( $specific_dates as $specific_date ) {
											if ( $specific_date && is_array( $specific_date ) ) {
												$this->specific_date_item( $specific_date );
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add Specific Date', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="hidden_content">
									<?php $this->specific_date_item(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'specific_dates' ); ?>
                </div>
				<?php
			}

			public function specific_date_item( $specific_date = [] ): void {
				$date       = is_array( $specific_date ) && array_key_exists( 'date', $specific_date ) ? $specific_date['date'] : '';
				$time_start = is_array( $specific_date ) && array_key_exists( 'start', $specific_date ) ? $specific_date['start'] : '';
				$time_end   = is_array( $specific_date ) && array_key_exists( 'end', $specific_date ) ? $specific_date['end'] : '';
				?>
                <div class="delete_area ">
                    <div class="_all_center">
                        <div class="_group_content">
							<?php
								ABPRF_Layout::button_sort();
								ABPRF_Layout::input_date( 'specific_dates[]', $date );
								ABPRF_Layout::input_time( 'specific_time_start[]', $time_start );
								ABPRF_Layout::input_time( 'specific_time_end[]', $time_end );
								ABPRF_Layout::button_delete(); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}
		}
		new ABPRF_Dates();
	}