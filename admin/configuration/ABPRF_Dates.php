<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dates' ) ) {
		class ABPRF_Dates {
			public function __construct() {
				add_action( 'abprf_post_content', [ $this, 'tab_content' ] );
			}

			public function tab_content( $abprf_infos ): void {
				$abprf_configuration = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : [];
				$post_title          = array_key_exists( 'post_title', $abprf_infos ) ? $abprf_infos['post_title'] : '';
				$equipment_icon      = isset( $abprf_configuration['equipment_icon'] ) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				$date_type           = array_key_exists( 'date_type', $abprf_infos ) ? $abprf_infos['date_type'] : 'periodic_date';
				$periodic_start_date = array_key_exists( 'periodic_start_date', $abprf_infos ) ? $abprf_infos['periodic_start_date'] : '';
				$periodic_end_date   = array_key_exists( 'periodic_end_date', $abprf_infos ) ? $abprf_infos['periodic_end_date'] : '';
				$periodic_after      = array_key_exists( 'periodic_after', $abprf_infos ) ? $abprf_infos['periodic_after'] : '1';
				$weekend             = array_key_exists( 'weekend', $abprf_infos ) ? $abprf_infos['weekend'] : '';
				$specific_off_dates  = array_key_exists( 'specific_off_dates', $abprf_infos ) ? $abprf_infos['specific_off_dates'] : [];
				$off_date_range      = array_key_exists( 'off_date_range', $abprf_infos ) ? $abprf_infos['off_date_range'] : [];
				$price_type          = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				?>
                <div class="tab_item" data-tabs="#abprf_dates">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr( $equipment_icon ); ?> _mar_r_xs"></span> <?php echo esc_html( $post_title . ' ' . __( ' : ', 'abprf-rental-forge' ) . ' ' . __( 'Date Configuration', 'abprf-rental-forge' ) ); ?></h4>
                    <div class="_divider_xs"></div>
					<?php $this->select_date_type( $date_type ); ?>
                    <div class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'daily' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>" data-collapse="#hourly_daily">
						<?php $this->specific_date_settings( $abprf_infos ); ?>
                    </div>
                    <div class="<?php echo esc_attr( $date_type == 'periodic_date' ? 'rf_active' : '' ); ?>" data-collapse="#periodic_date">
						<?php $this->periodic_start_date( $periodic_start_date );
							$this->periodic_end_date( $periodic_end_date ); ?>
                        <div class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'daily' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>" data-collapse="#hourly_daily">
							<?php $this->operation_time( $abprf_infos );
								$this->select_periodic_after( $periodic_after );
								$this->select_weekend( $weekend );
								$this->specific_off_dates( $specific_off_dates );
								$this->off_date_range( $off_date_range ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			//=============================//
			public function select_date_type( $date_type ): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
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
				<?php
			}

			public function specific_date_settings( $abprf_infos ): void {
				$price_type     = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				$date_type      = array_key_exists( 'date_type', $abprf_infos ) ? $abprf_infos['date_type'] : 'periodic_date';
				$specific_dates = array_key_exists( 'specific_dates', $abprf_infos ) ? $abprf_infos['specific_dates'] : [];
				?>
                <div class="_setting_item  <?php echo esc_attr( $date_type == 'specific_date' ? 'rf_active' : '' ); ?>" data-collapse="#specific_date">
                    <div class="_f_wrap">
                        <span class="_fs_label_mar_r_xs_max_250"><?php esc_html_e( 'Specific Dates', 'abprf-rental-forge' ); ?></span>
                        <div class="abprf_configuration_content">
                            <div class="_group_content_full_width_max_500">
                                <div class="_btn_warning_xs_opacity_zero"><span class="fas fa-arrows-alt"></span></div>
                                <div class="_btn_max_250"><?php esc_html_e( 'Dates', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></div>
                                <div data-collapse="#hourly" class="_btn_max_200 <?php echo esc_attr( $price_type == 'hourly' ? 'rf_active' : '' ); ?>"><?php esc_html_e( 'Operation Time', 'abprf-rental-forge' ); ?></div>
                                <div class="_btn_danger_xs_opacity_zero"><span class="fas fa-times"></span></div>
                            </div>
                            <div class="abprf_insert_item abprf_sortable">
								<?php
									if ( sizeof( $specific_dates ) ) {
										foreach ( $specific_dates as $specific_date ) {
											if ( $specific_date && is_array( $specific_date ) ) {
												$this->specific_date_item( $abprf_infos, $specific_date );
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add Specific Date', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="abprf_hidden_item">
									<?php $this->specific_date_item( $abprf_infos ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'specific_dates' ); ?>
                </div>
				<?php
			}

			public function specific_date_item( $abprf_infos, $specific_date = [] ): void {
				$price_type = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				$date       = is_array( $specific_date ) && array_key_exists( 'date', $specific_date ) ? $specific_date['date'] : '';
				$time_start = is_array( $specific_date ) && array_key_exists( 'start', $specific_date ) ? $specific_date['start'] : '';
				$time_end   = is_array( $specific_date ) && array_key_exists( 'end', $specific_date ) ? $specific_date['end'] : '';
				?>
                <div class="abprf_delete_area ">
                    <div class="_all_center">
                        <div class="_group_content">
							<?php
								ABPRF_Layout::button_sort();
								ABPRF_Layout::input_date( 'specific_dates[]', $date );
							?>
                            <div data-collapse="#hourly" class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>">
								<?php ABPRF_Layout::input_time( 'specific_time_start[]', $time_start ); ?>
                            </div>
                            <div data-collapse="#hourly" class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>">
								<?php ABPRF_Layout::input_time( 'specific_time_end[]', $time_end ); ?>
                            </div>
							<?php ABPRF_Layout::button_delete(); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}

			//=============================//
			public function periodic_start_date( $periodic_start_date ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Launching Date', 'abprf-rental-forge' ); ?></span>
						<?php ABPRF_Layout::input_date( 'periodic_start_date', $periodic_start_date ); ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'periodic_start_date' ); ?>
                </div>
				<?php
			}

			public function periodic_end_date( $periodic_end_date ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Terminate Date', 'abprf-rental-forge' ); ?></span>
						<?php ABPRF_Layout::input_date( 'periodic_end_date', $periodic_end_date ); ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'periodic_end_date' ); ?>
                </div>
				<?php
			}

			public function operation_time( $abprf_infos ): void {
				$price_type           = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				$operation_time_start = array_key_exists( 'operation_time_start', $abprf_infos ) ? $abprf_infos['operation_time_start'] : '';
				$operation_time_end   = array_key_exists( 'operation_time_end', $abprf_infos ) ? $abprf_infos['operation_time_end'] : '';
				?>
                <div data-collapse="#hourly" class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>">
                    <div class="_setting_item">
                        <div class="_f_equal_max_500_f_wrap">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Operation Time', 'abprf-rental-forge' ); ?></span>
                            <div class="_group_content">
								<?php ABPRF_Layout::input_time( 'operation_time_start', $operation_time_start );
									ABPRF_Layout::input_time( 'operation_time_end', $operation_time_end ); ?>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'operation_time' ); ?>
                    </div>
                </div>
				<?php
			}

			public function select_periodic_after( $periodic_after ): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Periodic after', 'abprf-rental-forge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="periodic_after" placeholder="Ex: 5" value="<?php echo esc_attr( $periodic_after ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'periodic_after' ); ?>
                </div>
				<?php
			}

			public function select_weekend( $weekend ): void {
				$off_day_array = $weekend ? explode( ',', $weekend ) : [];
				$days          = ABPRF_Layout::week_day(); ?>
                <div class="_setting_item ">
                    <div class="_d_flex">
                        <span class="_fs_label_mar_r_xs_max_250"><?php esc_html_e( 'Weekend', 'abprf-rental-forge' ); ?></span>
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

			public function specific_off_dates( $specific_off_dates ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_wrap">
                        <span class="_fs_label_mar_r_xs_max_250"><?php esc_html_e( 'Specific Off Dates', 'abprf-rental-forge' ); ?></span>
                        <div class="abprf_configuration_content">
                            <div class="abprf_insert_item abprf_sortable">
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
                                <div class="abprf_hidden_item">
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

			public function off_date_range( $off_date_range ): void {
				?>
                <div class="_setting_item  abprf_date_range">
                    <div class="_f_wrap">
                        <span class="_fs_label_mar_r_xs_max_250"><?php esc_html_e( 'Off Date Range', 'abprf-rental-forge' ); ?></span>
                        <div class="abprf_configuration_content">
                            <div class="abprf_insert_item abprf_sortable">
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
                                <div class="abprf_hidden_item">
									<?php $this->off_day_range( 'off_date_range[]' ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'off_date_range' ); ?>
                </div>
				<?php
			}

			public function off_day_range( $from_date = '', $to_date = '' ): void {
				?>
                <div class="abprf_delete_area ">
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
                <div class="abprf_delete_area ">
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
		}
		new ABPRF_Dates();
	}