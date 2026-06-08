<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dates' ) ) {
		class ABPRF_Dates {
			public function __construct() {
				add_action( 'abprf_global_dates', array( $this, 'global_dates' ) );
				add_action( 'abprf_post_content', array( $this, 'post_content_dates' ) );
				add_filter( 'abprf_get_date_array', array( $this, 'get_date_array' ) );
				add_action( 'wp_ajax_abprf_save_dates', array( $this, 'save_global_date' ) );
			}

			public function global_dates(): void {
				$date_infos = ABPRF_Function::get_option( 'abprf_dates' );
				?>
                <div class="tab_item" data-tabs="#abprf_global_dates">
                    <form class="save_dates" method="post" action="">
                        <h4 class="_abprf"><span class="_mar_r_xxs">🗓️</span> <?php esc_html_e( 'Global Date Configuration', 'abp-rentalforge' ); ?></h4>
						<?php ABPRF_Layout::info_text( 'abprf_dates' ); ?>
                        <div class="_divider_xs"></div>
						<?php $this->date_time_format( $date_infos );
							$this->date_content( $date_infos ); ?>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Date Configuration', 'abp-rentalforge' ); ?></button>
                    </form>
                </div>
				<?php
			}

			public function post_content_dates( $post_infos ): void {
				$date_infos          = array_key_exists( 'abprf_dates', $post_infos ) ? $post_infos['abprf_dates'] : [];
				$active_global_dates = array_key_exists( 'active_global_dates', $post_infos ) ? $post_infos['active_global_dates'] : 'on';
				?>
                <div class="tab_item" data-tabs="#abprf_dates">
                    <h4 class="_abprf_color_theme"><span class=" _mar_r_xs">🗓️</span> <?php esc_html_e( 'Date Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item">
                            <div class="_fa_center">
								<?php ABPRF_Layout::switch_checkbox( 'active_global_dates', $active_global_dates ); ?>
                                <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Use Global Date Configuration?', 'abp-rentalforge' ); ?></span>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'active_global_dates' ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_dates == 'off' ? 'rf_active' : '' ); ?>" data-collapse="#active_global_dates">
						<?php $this->date_content( $date_infos ); ?>
                    </div>
                </div>
				<?php
			}

			public function date_content( $date_infos ): void {
				//echo '<pre>';print_r($date_rule_array);echo '</pre>';
				?>
                <div class="_mar_t_xs group_setting"><?php
				$this->operation_time_slot( $date_infos );
				$this->buffer_time( $date_infos );
				$this->date_type( $date_infos );
				$this->advance_day( $date_infos );
				$this->specific_date_settings( $date_infos );
				$this->operation_date( $date_infos );
				$this->periodic_after( $date_infos );
				?></div><?php
				$this->special_on_off_dates( $date_infos );
			}

			//=============================//
			public function date_time_format( $date_infos = [] ): void {
				$format_array = ABPRF_Layout::array_date_format();
				$date_format  = array_key_exists( 'date_format', $date_infos ) ? $date_infos['date_format'] : 'D d M , yy';
				$time_format  = array_key_exists( 'time_format', $date_infos ) ? $date_infos['time_format'] : ABPRF_Time_Format;
				?>
                <div class="group_setting">
                    <div class="setting_item">
                        <label class="_f_wrap_fj_between_fa_center">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Date Format', 'abp-rentalforge' ); ?></span>
							<?php if ( sizeof( $format_array ) > 0 ) { ?>
                                <select class="_form_control " name="date_format">
									<?php foreach ( $format_array as $key => $format ) { ?>
                                        <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $date_format == $key ? 'selected' : '' ); ?>><?php echo esc_html( $format ); ?></option>
									<?php } ?>
                                </select>
							<?php } ?>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'date_format' ); ?>
                    </div>
                    <div class="setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Time Format', 'abp-rentalforge' ); ?></span>
                            <input type="text" class="_form_control" name="time_format" placeholder="<?php echo esc_attr( ABPRF_Time_Format ); ?>" value="<?php echo esc_attr( $time_format ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'time_format' ); ?>
                    </div>
                </div>
				<?php
			}

			public function operation_time_slot( $date_infos = [] ): void {
				$operation_time_start = array_key_exists( 'operation_time_start', $date_infos ) ? $date_infos['operation_time_start'] : '';
				$operation_time_end   = array_key_exists( 'operation_time_end', $date_infos ) ? $date_infos['operation_time_end'] : '';
				$time_slot_length     = array_key_exists( 'time_slot_length', $date_infos ) ? $date_infos['time_slot_length'] : 60;
				?>
                <div class="setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Operation Time(optional)', 'abp-rentalforge' ); ?></span>
                        <div class="_group_content">
							<?php ABPRF_Layout::input_time( 'operation_time_start', $operation_time_start );
								ABPRF_Layout::input_time( 'operation_time_end', $operation_time_end ); ?>
                        </div>
                    </div>
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Time Slot length in Min', 'abp-rentalforge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="time_slot_length" placeholder="Ex:30" value="<?php echo esc_attr( $time_slot_length ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'operation_time' ); ?>
					<?php ABPRF_Layout::info_text( 'time_slot_length' ); ?>
                </div>
				<?php
			}

			public function buffer_time( $date_infos = [] ): void {
				$buffer_time_before = array_key_exists( 'sale_close_before', $date_infos ) ? $date_infos['sale_close_before'] : 0;
				$buffer_time_after  = array_key_exists( 'sale_close_after', $date_infos ) ? $date_infos['sale_close_after'] : 0;
				?>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Before Buffer time in MIN (Optional)', 'abp-rentalforge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="sale_close_before" placeholder="Ex: 15" value="<?php echo esc_attr( $buffer_time_before ); ?>"/>
                    </label>
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'After Buffer time in MIN (Optional)', 'abp-rentalforge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="sale_close_after" placeholder="Ex: 15" value="<?php echo esc_attr( $buffer_time_after ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'sale_close_after' ); ?>
					<?php ABPRF_Layout::info_text( 'sale_close_before' ); ?>
                </div>
				<?php
			}

			public function date_type( $date_infos = [] ): void {
				$date_type = array_key_exists( 'date_type', $date_infos ) && $date_infos['date_type'] ? $date_infos['date_type'] : 'periodic_date';
				?>
                <div class="setting_item">
                    <div class=" _fj_between">
                        <h5 class="_abprf"><?php esc_html_e( 'Operational Date Type', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></h5>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="date_type" value="<?php echo esc_attr( $date_type ); ?>"/>
                            <div class="radio_item">
                                <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $date_type == 'specific_date' ? 'rf_active' : '' ); ?>" data-close-target="#specific_date" data-radio="specific_date" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                    <span data-icon class="_mar_r_xs <?php echo esc_attr( $date_type == 'specific_date' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Specific Dates', 'abp-rentalforge' ); ?>
                                </button>
                            </div>
                            <div class="radio_item">
                                <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-close-target="#periodic_date" data-radio="periodic_date" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                    <span data-icon class="_mar_r_xs <?php echo esc_attr( $date_type == 'periodic_date' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Periodic Dates', 'abp-rentalforge' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'date_type' ); ?>
                </div>
				<?php
			}

			public function advance_day( $date_infos = [] ): void {
				$advance_date_number = array_key_exists( 'advance_date_number', $date_infos ) ? $date_infos['advance_date_number'] : 28;
				?>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Number of advance booking date', 'abp-rentalforge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="advance_date_number" placeholder="Ex: 28" value="<?php echo esc_attr( $advance_date_number ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'advance_date_number' ); ?>
                </div>
				<?php
			}

			public function operation_date( $date_infos = [] ): void {
				$date_type  = array_key_exists( 'date_type', $date_infos ) && $date_infos['date_type'] ? $date_infos['date_type'] : 'periodic_date';
				$start_date = array_key_exists( 'periodic_start_date', $date_infos ) ? $date_infos['periodic_start_date'] : '';
				$end_date   = array_key_exists( 'periodic_end_date', $date_infos ) ? $date_infos['periodic_end_date'] : '';
				?>
                <div class="setting_item <?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-close="#periodic_date">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Rent From (Optional)', 'abp-rentalforge' ); ?></span>
						<?php ABPRF_Layout::input_date( 'periodic_start_date', $start_date ); ?>
                    </div>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Rent To (Optional)', 'abp-rentalforge' ); ?></span>
						<?php ABPRF_Layout::input_date( 'periodic_end_date', $end_date ); ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'periodic_start_date' ); ?>
					<?php ABPRF_Layout::info_text( 'periodic_end_date' ); ?>
                </div>
				<?php
			}

			public function periodic_after( $date_infos = [] ): void {
				$date_type      = array_key_exists( 'date_type', $date_infos ) && $date_infos['date_type'] ? $date_infos['date_type'] : 'periodic_date';
				$periodic_after = array_key_exists( 'periodic_after', $date_infos ) ? $date_infos['periodic_after'] : '1';
				?>
                <div class="setting_item <?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-close="#periodic_date">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Periodic after', 'abp-rentalforge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="periodic_after" placeholder="Ex: 5" value="<?php echo esc_attr( $periodic_after ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'periodic_after' ); ?>
                </div>
				<?php
			}

			public function special_on_off_dates( $date_infos = [] ): void {
				$date_type       = array_key_exists( 'date_type', $date_infos ) && $date_infos['date_type'] ? $date_infos['date_type'] : 'periodic_date';
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$date_rules      = ABPRF_Layout::date_option_rules();
				?>
                <div class="<?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-close="#periodic_date">
                    <div class="group_setting _mar_t_xs">
                        <div class="setting_item span_2">
                            <div class="_fj_between _mar_t_xs">
                                <h5 class="_abprf_color_theme"><?php esc_html_e( 'Special On/Off Date Time(optional)', 'abp-rentalforge' ); ?></h5>
                                <div class="custom_checkbox">
                                    <input type="hidden" name="date_rule" value="<?php echo esc_attr( $date_rule ); ?>"/>
									<?php foreach ( $date_rules as $key => $rule ) { ?>
                                        <div class="checkbox_item _min_100">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse-target="#<?php echo esc_attr( $key ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                                <span data-icon class="_mar_r_xs far <?php echo esc_attr( in_array( $key, $date_rule_array ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $rule ); ?>
                                            </button>
                                        </div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'date_rule' ); ?>
                        </div>
						<?php
							$this->weekend( $date_infos );
							$this->off_dates( $date_infos );
							$this->off_date_range( $date_infos );
							$this->special_on( $date_infos );
							$this->day_wise_time( $date_infos ); ?>
                    </div>
                </div>
				<?php
			}

			public function weekend( $date_infos = [] ): void {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$weekend         = array_key_exists( 'weekend', $date_infos ) ? $date_infos['weekend'] : '';
				$off_day_array   = $weekend ? explode( ',', $weekend ) : [];
				$days            = ABPRF_Layout::week_day(); ?>
                <div class="setting_item span_2 <?php echo esc_attr( in_array( 'weekend', $date_rule_array ) ? 'rf_active' : '' ); ?> " data-collapse="#weekend">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Weekend(optional)', 'abp-rentalforge' ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" name="weekend" value="<?php echo esc_attr( $weekend ); ?>"/>
							<?php foreach ( $days as $key => $day ) { ?>
                                <div class="checkbox_item _min_100">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $off_day_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
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

			public function off_dates( $date_infos = [] ): void {
				$date_rule          = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array    = $date_rule ? explode( ',', $date_rule ) : [];
				$specific_off_dates = array_key_exists( 'specific_off_dates', $date_infos ) ? $date_infos['specific_off_dates'] : [];
				?>
                <div class="setting_item span_2 <?php echo esc_attr( in_array( 'specific_of_date', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#specific_of_date">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Off Dates(optional)', 'abp-rentalforge' ); ?></span>
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
							<?php ABPRF_Layout::button_add( __( 'Add Specific Off Date', 'abp-rentalforge' ) ); ?>
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

			public function off_date_range( $date_infos = [] ): void {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$off_date_range  = array_key_exists( 'off_date_range', $date_infos ) ? $date_infos['off_date_range'] : [];
				?>
                <div class="setting_item span_2 <?php echo esc_attr( in_array( 'off_date_range', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#off_date_range">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Off Date Range(optional)', 'abp-rentalforge' ); ?></span>
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
							<?php ABPRF_Layout::button_add( __( 'Add Off Date Range', 'abp-rentalforge' ) ); ?>
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

			public function special_on( $date_infos = [] ): void {
				$date_rule       = $date_infos['date_rule'] ?? '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$special_dates   = $date_infos['special_on_dates'] ?? [];
				//echo '<pre>';print_r($special_dates);echo '</pre>';
				?>
                <div class="setting_item span_2  <?php echo esc_attr( in_array( 'special_on_dates', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#special_on_dates">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Special On Dates (optional)', 'abp-rentalforge' ); ?></span>
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
							<?php ABPRF_Layout::button_add( __( 'Add Special On Dates', 'abp-rentalforge' ) ); ?>
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

			public function day_wise_time( $date_infos = [] ): void {
				$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$operation_times = array_key_exists( 'day_wise_time', $date_infos ) ? $date_infos['day_wise_time'] : [];
				$days            = ABPRF_Layout::week_day();
				?>
                <div class="setting_item span_2  <?php echo esc_attr( in_array( 'day_wise_time', $date_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse="#day_wise_time">
                    <div class="_f_wrap_f_equal_f_gap_xxs">
                        <span class="_fs_label_min_500_max_600"><?php esc_html_e( 'Operation Time day Wise(Optional) ', 'abp-rentalforge' ); ?></span>
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

			public function specific_date_settings( $date_infos = [] ): void {
				$date_type      = $date_infos['date_type'] ?? 'periodic_date';
				$specific_dates = $date_infos['specific_dates'] ?? [];
				?>
                <div class="setting_item span_2  <?php echo esc_attr( $date_type == 'specific_date' ? 'rf_active' : '' ); ?>" data-close="#specific_date">
                    <div class="_f_wrap_fj_between">
                        <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Specific Dates & Operation Times', 'abp-rentalforge' ); ?></span>
                        <div class="configuration_content">
                            <div class="insertable_area sortable_area">
								<?php
									if ( sizeof( $specific_dates ) ) {
										foreach ( $specific_dates as $specific_date ) {
											if ( ! empty( $specific_date ) && is_array( $specific_date ) ) {
												$this->specific_date_item( $specific_date );
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add Specific Date', 'abp-rentalforge' ) ); ?>
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
				$date       = $specific_date['date'] ?? '';
				$time_start = $specific_date['start'] ?? '';
				$time_end   = $specific_date['end'] ?? '';
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
				$date       = $specific_date['date'] ?? '';
				$time_start = $specific_date['start'] ?? '';
				$time_end   = $specific_date['end'] ?? '';
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

			//==============================//
			public function get_date_array( array $date_infos = [] ): array {
				$has_post_nonce = isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' );
				$has_ajax_nonce = check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false );
				if ( ( $has_post_nonce || $has_ajax_nonce ) && current_user_can( 'manage_options' ) ) {
					$date_infos['date_type']            = isset( $_POST['date_type'] ) ? sanitize_text_field( wp_unslash( $_POST['date_type'] ) ) : 'periodic_date';
					$date_infos['operation_time_start'] = isset( $_POST['operation_time_start'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_start'] ) ) : '';
					$date_infos['operation_time_end']   = isset( $_POST['operation_time_end'] ) ? sanitize_text_field( wp_unslash( $_POST['operation_time_end'] ) ) : '';
					$date_infos['time_slot_length']     = isset( $_POST['time_slot_length'] ) ? sanitize_text_field( wp_unslash( $_POST['time_slot_length'] ) ) : '60';
					$date_infos['advance_date_number']  = isset( $_POST['advance_date_number'] ) ? sanitize_text_field( wp_unslash( $_POST['advance_date_number'] ) ) : '28';
					$date_infos['sale_close_before']    = isset( $_POST['sale_close_before'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_close_before'] ) ) : '';
					$date_infos['sale_close_after']     = isset( $_POST['sale_close_after'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_close_after'] ) ) : '';
					$periodic_start_date                = isset( $_POST['periodic_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_start_date'] ) ) : '';
					$periodic_end_date                  = isset( $_POST['periodic_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_end_date'] ) ) : '';
					$date_infos['periodic_start_date']  = $periodic_start_date ? gmdate( 'Y-m-d', strtotime( $periodic_start_date ) ) : '';
					$date_infos['periodic_end_date']    = $periodic_end_date ? gmdate( 'Y-m-d', strtotime( $periodic_end_date ) ) : '';
					$date_infos['periodic_after']       = isset( $_POST['periodic_after'] ) ? sanitize_text_field( wp_unslash( $_POST['periodic_after'] ) ) : '1';
					$date_infos['date_rule']            = isset( $_POST['date_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['date_rule'] ) ) : '';
					$date_infos['weekend']              = isset( $_POST['weekend'] ) ? sanitize_text_field( wp_unslash( $_POST['weekend'] ) ) : '';
					//======================//
					$specific_off_dates = ( isset( $_POST['specific_off_dates'] ) && is_array( $_POST['specific_off_dates'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_off_dates'] ) ) : [];
					$off_dates          = [];
					if ( count( $specific_off_dates ) > 0 ) {
						foreach ( $specific_off_dates as $off_date ) {
							if ( $off_date ) {
								$off_dates[] = gmdate( 'Y-m-d', strtotime( $off_date ) );
							}
						}
					}
					$date_infos['specific_off_dates'] = array_unique( $off_dates );
					$off_schedules                    = [];
					$from_dates                       = ( isset( $_POST['abprf_off_from'] ) && is_array( $_POST['abprf_off_from'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_from'] ) ) : [];
					$to_dates                         = ( isset( $_POST['abprf_off_to'] ) && is_array( $_POST['abprf_off_to'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['abprf_off_to'] ) ) : [];
					if ( count( $from_dates ) > 0 ) {
						foreach ( $from_dates as $key => $from_date ) {
							if ( $from_date && isset( $to_dates[ $key ] ) && $to_dates[ $key ] ) {
								$off_schedules[] = [
									'from' => $from_date,
									'to' => $to_dates[ $key ],
								];
							}
						}
					}
					$date_infos['off_date_range'] = $off_schedules;
					//======================//
					$special_on_dates      = ( isset( $_POST['special_on_dates'] ) && is_array( $_POST['special_on_dates'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_dates'] ) ) : [];
					$special_on_time_start = ( isset( $_POST['special_on_time_start'] ) && is_array( $_POST['special_on_time_start'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_time_start'] ) ) : [];
					$special_on_time_end   = ( isset( $_POST['special_on_time_end'] ) && is_array( $_POST['special_on_time_end'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['special_on_time_end'] ) ) : [];
					$specific_on           = [];
					if ( count( $special_on_dates ) > 0 ) {
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
					$specific_dates      = ( isset( $_POST['specific_dates'] ) && is_array( $_POST['specific_dates'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_dates'] ) ) : [];
					$specific_time_start = ( isset( $_POST['specific_time_start'] ) && is_array( $_POST['specific_time_start'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_start'] ) ) : [];
					$specific_time_end   = ( isset( $_POST['specific_time_end'] ) && is_array( $_POST['specific_time_end'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['specific_time_end'] ) ) : [];
					$specific            = [];
					if ( count( $specific_dates ) > 0 ) {
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

				return $date_infos;
			}

			public function save_global_date(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				$date_infos                = $this->get_date_array();
				$date_infos['date_format'] = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : '';
				$date_infos['time_format'] = isset( $_POST['time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['time_format'] ) ) : '';
				update_option( 'abprf_dates', $date_infos );
				ABPRF_Function::update_dates( 'global' );
				ABPRF_Function::update_time_slot();
				wp_send_json_success( [ 'msg' => __( 'Date Configuration Saved Successfully ! ! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Dates();
	}