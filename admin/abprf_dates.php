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
				$date_infos = ABPRF_Function::get_option( 'abprf_dates' );
				?>
                <form class="_reflex_6_abp_panel_max_1200_mar_auto save_dates" method="post" action="">
                    <div class="_panel_head">
                        <h4 class="_abprf"><span class="_mar_r_xxs">🗓️</span> <?php esc_html_e( 'Global Date Configuration', 'abprf-rental-forge' ); ?></h4>
                    </div>
                    <div class="_panel_body">
						<?php $this->date_format_buffer( $date_infos );
							$this->date_content( $date_infos ); ?>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Date Configuration', 'abprf-rental-forge' ); ?></button>
                    </div>
                </form>
				<?php
			}

			public function post_content_dates( $post_infos ): void {
				$date_infos          = array_key_exists( 'abprf_dates', $post_infos ) ? $post_infos['abprf_dates'] : [];
				$active_global_dates = array_key_exists( 'active_global_dates', $post_infos ) ? $post_infos['active_global_dates'] : 'on';
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
						<?php $this->date_content( $date_infos ); ?>
                    </div>
                </div>
				<?php
			}

			public function date_content( $date_infos ): void {
				$date_type = array_key_exists( 'date_type', $date_infos ) ? $date_infos['date_type'] : 'periodic_date';
				//echo '<pre>';print_r($date_rule_array);echo '</pre>';
				$this->date_type_advance_date( $date_infos );
				$this->specific_date_settings( $date_infos ); ?>
                <div class="<?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-collapse="#periodic_date">
					<?php $this->rent_start_end_date( $date_infos );
						$this->operation_time_periodic( $date_infos );
						$this->date_rule( $date_infos );
						$this->off_dates( $date_infos );
						$this->off_date_range( $date_infos );
						$this->select_weekend( $date_infos );
						$this->on_dates( $date_infos );
						$this->day_wise_time( $date_infos ); ?>
                </div>
				<?php
			}

			public function save_global_date() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$date_infos                             = $this->get_date_array();
					$date_infos['date_picker_format']       = isset( $_POST['date_picker_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_picker_format'] ) ) : '';
					$date_infos['ticket_sale_close_before'] = isset( $_POST['ticket_sale_close_before'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket_sale_close_before'] ) ) : '';
					update_option( 'abprf_dates', $date_infos );
					wp_send_json_success( esc_html__( 'Date Configuration Saved Successfully ! ', 'abprf-rental-forge' ) );
				} else {
					wp_send_json_success( esc_html__( 'Date Configuration not Saved !', 'abprf-rental-forge' ) );
				}
				wp_die();
			}

			public function get_date_array( $date_infos = [] ) {
				if ( is_admin() && ( ( isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) || check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) ) ) {
					$date_infos['date_type']            = isset( $_POST['date_type'] ) ? sanitize_text_field( wp_unslash( $_POST['date_type'] ) ) : 'periodic_date';
					$date_infos['advance_date_number']  = isset( $_POST['advance_date_number'] ) ? sanitize_text_field( wp_unslash( $_POST['advance_date_number'] ) ) : '28';
					$periodic_start_date                = isset( $_POST['periodic_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_start_date'] ) ) : '';
					$periodic_end_date                  = isset( $_POST['periodic_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_end_date'] ) ) : '';
					$date_infos['periodic_start_date']  = $periodic_start_date ? gmdate( 'Y-m-d', strtotime( $periodic_start_date ) ) : '';
					$date_infos['periodic_end_date']    = $periodic_end_date ? gmdate( 'Y-m-d', strtotime( $periodic_end_date ) ) : '';
					$date_infos['operation_time_start'] = isset( $_POST['operation_time_start'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_start'] ) ) : '';
					$date_infos['operation_time_end']   = isset( $_POST['operation_time_end'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_end'] ) ) : '';
					$date_infos['periodic_after']       = isset( $_POST['periodic_after'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_after'] ) ) : '1';
					$date_infos['date_rule']            = isset( $_POST['date_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['date_rule'] ) ) : '';
					$date_infos['weekend']              = isset( $_POST['weekend'] ) ? sanitize_text_field( wp_unslash( $_POST['weekend'] ) ) : '';
					//======================//
					$specific_off_dates = isset( $_POST['specific_off_dates'] ) && is_array( $_POST['specific_off_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_off_dates'] ) ) : [];
					$off_dates          = array();
					if ( sizeof( $specific_off_dates ) > 0 ) {
						foreach ( $specific_off_dates as $off_date ) {
							if ( $off_date ) {
								$off_dates[] = gmdate( 'Y-m-d', strtotime( $off_date ) );
							}
						}
					}
					$date_infos['specific_off_dates'] = array_unique( $off_dates );
					$off_schedules                    = [];
					$from_dates                       = isset( $_POST['abprf_off_from'] ) && is_array( $_POST['abprf_off_from'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_from'] ) ) : [];
					$to_dates                         = isset( $_POST['abprf_off_to'] ) && is_array( $_POST['abprf_off_to'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_to'] ) ) : [];
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
					$date_infos['off_date_range'] = $off_schedules;
					//======================//
					$special_on_dates      = isset( $_POST['special_on_dates'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_dates'] ) ) : [];
					$special_on_time_start = isset( $_POST['special_on_time_start'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_time_start'] ) ) : [];
					$special_on_time_end   = isset( $_POST['special_on_time_end'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_time_end'] ) ) : [];
					$specific_on           = array();
					if ( sizeof( $special_on_dates ) > 0 ) {
						foreach ( $special_on_dates as $key => $specific_date ) {
							if ( $specific_date ) {
								$specific_on[ $key ]['date']  = gmdate( 'Y-m-d', strtotime( $specific_date ) );
								$specific_on[ $key ]['start'] = array_key_exists( $key, $special_on_time_start ) ? $special_on_time_start[ $key ] : '';
								$specific_on[ $key ]['end']   = array_key_exists( $key, $special_on_time_end ) ? $special_on_time_end[ $key ] : '';
							}
						}
					}
					$date_infos['special_on_dates'] = $specific_on;
					//======================//
					$days      = ABPRF_Layout::week_day();
					$time_info = [];
					foreach ( $days as $key => $day ) {
						$start_time_key = $key . '_time_start';
						$end_time_key   = $key . '_time_end';
						$start_time     = isset( $_POST[ $start_time_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $start_time_key ] ) ) : '';
						$end_time       = isset( $_POST[ $end_time_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $end_time_key ] ) ) : '';
						if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
							$time_info[ $key ]['start'] = $start_time;
							$time_info[ $key ]['end']   = $end_time;
						}
					}
					$date_infos['day_wise_time'] = $time_info;
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
					$date_infos['specific_dates'] = $specific;
				}

				//echo '<pre>';print_r($date_infos);echo '</pre>';die();
				return $date_infos;
			}

			//=============================//
			public function date_format_buffer( $date_infos ): void {
				$format_array = ABPRF_Layout::date_picker_format_array();
				$date_format  = array_key_exists( 'date_picker_format', $date_infos ) ? $date_infos['date_picker_format'] : 'D d M , yy';
				$buffer_time  = array_key_exists( 'ticket_sale_close_before', $date_infos ) ? $date_infos['ticket_sale_close_before'] : '0';
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

			public function date_type_advance_date( $date_infos ): void {
				$date_type           = array_key_exists( 'date_type', $date_infos ) ? $date_infos['date_type'] : 'periodic_date';
				$advance_date_number = array_key_exists( 'advance_date_number', $date_infos ) ? $date_infos['advance_date_number'] : '28';
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
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="advance_date_number" placeholder="Ex: 28" value="<?php echo esc_attr( $advance_date_number ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'advance_date_number' ); ?>
                    </div>
                </div>
				<?php
			}

			public function rent_start_end_date( $date_infos ): void {
				$start_date = array_key_exists( 'periodic_start_date', $date_infos ) ? $date_infos['periodic_start_date'] : '';
				$end_date   = array_key_exists( 'periodic_end_date', $date_infos ) ? $date_infos['periodic_end_date'] : '';
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

			public function operation_time_periodic( $date_infos ): void {
				$operation_time_start = array_key_exists( 'operation_time_start', $date_infos ) ? $date_infos['operation_time_start'] : '';
				$operation_time_end   = array_key_exists( 'operation_time_end', $date_infos ) ? $date_infos['operation_time_end'] : '';
				$periodic_after       = array_key_exists( 'periodic_after', $date_infos ) ? $date_infos['periodic_after'] : '1';
				?>
                <div class="group_setting">
                    <div class=" _setting_item">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Operation Time(optional)', 'abprf-rental-forge' ); ?></span>
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

			public function date_rule( $date_infos ) {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$date_rules      = ABPRF_Layout::date_option_rules();
				?>
                <div class="_fj_between">
                    <h5 class="_abprf_color_theme"><?php esc_html_e( 'Special On/Off Date Time(optional)', 'abprf-rental-forge' ); ?></h5>
                    <div class="abprf_checkbox">
                        <input type="hidden" name="date_rule" value="<?php echo esc_attr( $date_rule ); ?>"/>
						<?php foreach ( $date_rules as $key => $rule ) { ?>
                            <div class="checkbox_item _min_100">
                                <button type="button" class="_btn_white_xs <?php echo esc_attr( in_array( $key, $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse-target="#<?php echo esc_attr( $key ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                    <span data-icon class="_mar_r_xs far <?php echo esc_attr( in_array( $key, $date_rule_array ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $rule ); ?>
                                </button>
                            </div>
						<?php } ?>
                    </div>
                </div>
				<?php ABPRF_Layout::info_text( 'date_rule' ); ?>
                <div class="_divider_xs"></div>
				<?php
			}

			public function off_dates( $date_infos ): void {
				$date_rule          = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array    = $date_rule ? explode( ',', $date_rule ) : [];
				$specific_off_dates = array_key_exists( 'specific_off_dates', $date_infos ) ? $date_infos['specific_off_dates'] : [];
				?>
                <div class="_setting_item <?php echo esc_attr( in_array( 'specific_of_date', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#specific_of_date">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Off Dates(optional)', 'abprf-rental-forge' ); ?></span>
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
				<?php
			}

			public function off_date_range( $date_infos ) {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$off_date_range  = array_key_exists( 'off_date_range', $date_infos ) ? $date_infos['off_date_range'] : [];
				?>
                <div class="_setting_item <?php echo esc_attr( in_array( 'off_date_range', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#off_date_range">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Off Date Range(optional)', 'abprf-rental-forge' ); ?></span>
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
				<?php
			}

			public function select_weekend( $date_infos ): void {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$weekend         = array_key_exists( 'weekend', $date_infos ) ? $date_infos['weekend'] : '';
				$off_day_array   = $weekend ? explode( ',', $weekend ) : [];
				$days            = ABPRF_Layout::week_day(); ?>
                <div data-collapse="#weekend" class="<?php echo esc_attr( in_array( 'weekend', $date_rule_array ) ? 'rf_active' : '' ); ?>">
                    <div class="_setting_item ">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Weekend(optional)', 'abprf-rental-forge' ); ?></span>
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
                </div>
				<?php
			}

			public function on_dates( $date_infos ) {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$special_dates   = array_key_exists( 'special_on_dates', $date_infos ) ? $date_infos['special_on_dates'] : [];
				//echo '<pre>';print_r($special_dates);echo '</pre>';
				?>
                <div class="_setting_item  <?php echo esc_attr( in_array( 'special_on_dates', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#special_on_dates">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Special On Dates (optional)', 'abprf-rental-forge' ); ?></span>
                        <div class="configuration_content">
                            <div class="insertable_area sortable_area">
								<?php
									if ( sizeof( $special_dates ) ) {
										foreach ( $special_dates as $specific_date ) {
											if ( $specific_date && is_array( $specific_date ) ) {
												$this->special_on_date_item( $specific_date );
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add Special On Dates', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="hidden_content">
									<?php $this->special_on_date_item(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'special_on_dates' ); ?>
                </div>
				<?php
			}

			public function day_wise_time( $date_infos ) {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$operation_times = array_key_exists( 'day_wise_time', $date_infos ) ? $date_infos['day_wise_time'] : [];
				$days            = ABPRF_Layout::week_day();
				?>
                <div class="_setting_item  <?php echo esc_attr( in_array( 'day_wise_time', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#day_wise_time">
                    <div class="_f_wrap_f_equal_f_gap_xxs">
                        <span class="_fs_label_min_500_max_600"><?php esc_html_e( 'Operation Time day Wise(Optional) ', 'abprf-rental-forge' ); ?></span>
						<?php foreach ( $days as $key => $day ) {
							$times = array_key_exists( $key, $operation_times ) && sizeof( $operation_times[ $key ] ) > 0 ? $operation_times[ $key ] : [];
							$start = array_key_exists( 'start', $times ) ? $times['start'] : '';
							$end   = array_key_exists( 'end', $times ) ? $times['end'] : '';
							?>
                            <div class="_fj_between_fa_center_min_500_max_600">
                                <span class="_mar_lr_xs_fs_label"><?php echo esc_html( $day ); ?></span>
                                <div class="_group_content">
									<?php ABPRF_Layout::input_time( $key . '_time_start', $start );
										ABPRF_Layout::input_time( $key . '_time_end', $end ); ?>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'day_wise_time' ); ?>
                </div>
				<?php
			}

			public function off_day_range( $from_date = '', $to_date = '' ): void {
				?>
                <div class="delete_area">
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

			public function specific_date_settings( $date_infos ): void {
				$date_type      = array_key_exists( 'date_type', $date_infos ) ? $date_infos['date_type'] : 'periodic_date';
				$specific_dates = array_key_exists( 'specific_dates', $date_infos ) ? $date_infos['specific_dates'] : [];
				?>
                <div class="_setting_item  <?php echo esc_attr( $date_type == 'specific_date' ? 'rf_active' : '' ); ?>" data-collapse="#specific_date">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Dates & Operation Times', 'abprf-rental-forge' ); ?></span>
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

			public function special_on_date_item( $specific_date = [] ): void {
				$date       = is_array( $specific_date ) && array_key_exists( 'date', $specific_date ) ? $specific_date['date'] : '';
				$time_start = is_array( $specific_date ) && array_key_exists( 'start', $specific_date ) ? $specific_date['start'] : '';
				$time_end   = is_array( $specific_date ) && array_key_exists( 'end', $specific_date ) ? $specific_date['end'] : '';
				?>
                <div class="delete_area ">
                    <div class="_all_center">
                        <div class="_group_content">
							<?php
								ABPRF_Layout::button_sort();
								ABPRF_Layout::input_date( 'special_on_dates[]', $date );
								ABPRF_Layout::input_time( 'special_on_time_start[]', $time_start );
								ABPRF_Layout::input_time( 'special_on_time_end[]', $time_end );
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