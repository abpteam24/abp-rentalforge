<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Properties' ) ) {
		class ABPRF_Properties {
			public function __construct() {
				add_action( 'abprf_load_properties', array( $this, 'load_properties' ) );
				add_action( 'abprf_post_content', [ $this, 'tab_content' ] );
				add_action( 'wp_ajax_abprf_save_property', array( $this, 'save_property' ) );
				add_action( 'wp_ajax_abprf_add_property', array( $this, 'add_property' ) );
				add_action( 'wp_ajax_abprf_reload_property_list', array( $this, 'reload_property_list' ) );
				add_action( 'wp_ajax_abprf_property_delete', array( $this, 'property_delete' ) );
			}
			public function load_properties( $abprf_info ): void {
				//$total_property = isset( $abprf_info['total_property'] ) && $abprf_info['total_property'] ? $abprf_info['total_property'] : 0;
				$post_ids               = isset( $abprf_info['post_ids'] ) && $abprf_info['post_ids'] ? $abprf_info['post_ids'] : [];
				$filter_args['post_id'] = 'all';
				?>
                <div class="abprf_properties _abp_panel">
                    <div class="_panel_head_fj_between_f_wrap">
                        <h4 class="_abprf_color_white"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties', 'abp-rentalforge' ); ?></h4>
                        <div class="abp_dropdown _max_400">
                            <label class="_abprf_all_center">
                                <input type="hidden" name="select_property_hidden" value=""/>
                                <input type="text" class="_form_control_text_center validation_name" name="select_property" placeholder="<?php esc_attr_e( 'Search  Post', 'abp-rentalforge' ); ?>" value=""/>
                            </label>
                            <div class="dropdown_list">
                                <ul class="_abprf">
                                    <li data-value="all" data-text="<?php esc_attr_e( 'All Post', 'abp-rentalforge' ); ?>"><?php esc_html_e( 'All Post', 'abp-rentalforge' ); ?></li>
                                    <li data-value="on" data-text="<?php esc_attr_e( 'Rent Active', 'abp-rentalforge' ); ?>"><?php esc_html_e( 'Rent Active', 'abp-rentalforge' ); ?></li>
                                    <li data-value="off" data-text="<?php esc_attr_e( 'Rent De-active', 'abp-rentalforge' ); ?>"><?php esc_html_e( 'Rent De-active', 'abp-rentalforge' ); ?></li>
									<?php if ( ! empty( $post_ids ) && is_array( $post_ids ) && sizeof( $post_ids ) > 0 ) {
										foreach ( $post_ids as $post_id ) {
											$title = get_the_title( $post_id );
											if ( ! empty( $title ) ) { ?>
                                                <li data-value="<?php echo esc_attr( $post_id ); ?>" data-text="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></li>
											<?php }
										}
									} ?>
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="_btn_light_white_xs" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abp-rentalforge' ); ?></button>
                    </div>
                    <div class="_panel_body properties_list">
						<?php $this->properties_table( $filter_args ); ?>
                    </div>
                </div>
				<?php
			}
			public function tab_content( $abprf_infos ): void {
				$rent_rules                  = ABPRF_Layout::rent_rules_options();
				$post_id                     = $abprf_infos['post_id'] ?? '';
				$copy_post_id                = $abprf_infos['copy_post_id'] ?? '';
				$rent_rule                   = ( $abprf_infos['rent_rule'] ?? null ) ?: key( $rent_rules );
				$day_time_start              = $abprf_infos['day_time_start'] ?? '';
				$day_time_end                = $abprf_infos['day_time_end'] ?? '';
				$hour_threshold              = ( $abprf_infos['hour_threshold'] ?? null ) ?: 24;
				$cut_off_date                = ( $abprf_infos['cut_off_date'] ?? null ) ?: 10;
				$day_threshold               = ( $abprf_infos['day_threshold'] ?? null ) ?: 30;
				$filter_args['copy_post_id'] = $copy_post_id;
				$filter_args['post_id']      = $post_id;
				//echo '<pre>';print_r( ABPRF_Layout::rent_rules_options());					echo '</pre>';
				?>
                <div class="tab_item abprf_equipment_price" data-tabs="#abprf_equipment_price">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties and Price Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item full_width">
                            <div class="_fj_between">
                                <span class="_abp_label"><?php esc_html_e( 'Rent Date & Time Rule', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                                <div class="custom_radio">
                                    <input type="hidden" class="_form_control" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
									<?php foreach ( $rent_rules as $key => $rule ) { ?>
                                        <div class="radio_item">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $rent_rule == $key ? 'abp_active' : '' ); ?>" data-close-target="#<?php echo esc_attr( $key ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                                <i class="_abprf_fs_h5"><span data-icon class="_mar_r_xs <?php echo esc_attr( $rent_rule == $key ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span></i><span class="_text_left_fs_label"><?php echo esc_html( $rule ); ?></span>
                                            </button>
                                        </div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'rent_rule' ); ?>
                        </div>
						<?php if ( $rent_rules['daily'] ?? '' ) { ?>
                            <div class="setting_item <?php echo esc_attr( $rent_rule == 'daily' ? 'abp_active' : '' ); ?>" data-close="#daily">
                                <div class="_f_wrap_fj_between_fa_center">
                                    <span class="_abp_label"><?php esc_html_e( 'Day time Start-End', 'abp-rentalforge' ); ?></span>
                                    <div class="_group_content">
										<?php ABPRF_Layout::input_time( 'day_time_start', $day_time_start );
											ABPRF_Layout::input_time( 'day_time_end', $day_time_end ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'day_time_start_end' ); ?>
                            </div>
						<?php } ?>
						<?php if ( $rent_rules['multi_day'] ?? '' ) { ?>
                            <div class="setting_item full_width <?php echo esc_attr( $rent_rule == 'multi_day' ? 'abp_active' : '' ); ?>" data-close="#multi_day">
                                <label class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Hour Threshold', 'abp-rentalforge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="hour_threshold" placeholder="Ex:30" value="<?php echo esc_attr( $hour_threshold ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'hour_threshold' ); ?>
                            </div>
						<?php } ?>
						<?php if ( $rent_rules['monthly'] ?? '' ) { ?>
                            <div class="setting_item full_width <?php echo esc_attr( $rent_rule == 'monthly' ? 'abp_active' : '' ); ?>" data-close="#monthly">
                                <label class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Month Cut-Off Date', 'abp-rentalforge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="cut_off_date" placeholder="Ex:10" value="<?php echo esc_attr( $cut_off_date ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'cut_off_date' ); ?>
                            </div>
						<?php } ?>
						<?php if ( $rent_rules['multi_month'] ?? '' ) { ?>
                            <div class="setting_item <?php echo esc_attr( $rent_rule == 'multi_month' ? 'abp_active' : '' ); ?>" data-close="#multi_month">
                                <label class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Day Threshold', 'abp-rentalforge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="day_threshold" placeholder="Ex:10" value="<?php echo esc_attr( $day_threshold ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'day_threshold' ); ?>
                            </div>
						<?php } ?>
                    </div>
                    <div class="properties_list">
						<?php $this->properties_table( $filter_args ); ?>
                    </div>
					<?php if ( empty( $copy_post_id ) ) { ?>
                        <div class="_divider_xs"></div>
                        <button type="button" class="_btn_default" data-post_id="<?php echo esc_attr( $post_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abp-rentalforge' ); ?></button>
					<?php } ?>
                </div>
				<?php
			}
			public function properties_table( $filter_args ): void {
				//echo '<pre>';				print_r( $filter_args );				echo '</pre>';
				$total_property        = ABPRF_Query::get_property( $filter_args, true );
				$page_number           = is_numeric( $filter_args['page_number'] ?? null ) ? (int) $filter_args['page_number'] : 1;
				$limit                 = is_numeric( $filter_args['page_item'] ?? null ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$count                 = ( $page_number - 1 ) * $limit + 1;
				$filter_args['limit']  = $limit;
				$filter_args['offset'] = $count - 1;
				$properties            = ABPRF_Query::get_property( $filter_args );
				if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
					$filter_post_id = $filter_args['post_id'] ?? '';
					$copy_post_id   = $filter_args['copy_post_id'] ?? '';
					$rent_rules     = ABPRF_Layout::rent_rules_options();
					?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abp-rentalforge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image/icon', 'abp-rentalforge' ); ?></th>
                            <th><?php esc_html_e( 'Property', 'abp-rentalforge' ); ?></th>
							<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                <th><?php esc_html_e( 'Post Information', 'abp-rentalforge' ); ?></th>
							<?php } ?>
                            <th><?php esc_html_e( 'Price', 'abp-rentalforge' ); ?></th>
							<?php if ( ABPRF_Function::on_off( 'deposit' ) ) { ?>
                                <th><?php esc_html_e( 'Deposit', 'abp-rentalforge' ); ?></th>
							<?php } ?>
                            <th><?php esc_html_e( 'Stock', 'abp-rentalforge' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'abp-rentalforge' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $properties as $property ) {
							$others             = json_decode( $property['others'] ?? '', true ) ?: [];
							$icon               = $others['icon'] ?? '';
							$name               = $property['name'] ?? '';
							$property_id        = $property['id'] ?? '';
							$post_id            = $property['post_id'] ?? '';
							$status             = $property['status'] ?? '';
							$rent_continue      = $property['rent_continue'] ?? '';
							$post_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
							$post_status        = get_post_status( $post_id );
							$rent_rule          = $property['rent_rule'] ?? '';
							$price_qty_info     = json_decode( $property['price_qty_info'] ?? '', true ) ?: [];
							$_price_info        = $price_qty_info[ $rent_rule ] ?? [];
							?>
                            <tr class="delete_area">
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_fs_h2"><?php ABPRF_Layout::image_icon( $icon, '' ); ?></th>
                                <td>
                                    <div class="_fd_column">
                                        <h5 class="_abprf_color_theme"><?php echo esc_html( $name ); ?></h5>
                                        <div class="_d_flex">
											<?php if ( ! empty( $copy_post_id ) ) { ?>
                                                <input type="hidden" name="copy_property_id[]" value="<?php echo esc_attr( $property_id ); ?>"/>
											<?php } else { ?>
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Property Id : ', 'abp-rentalforge' ) . ' ' . $property_id ); ?></span>
											<?php } ?>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $rent_continue == 'on' ? __( 'Rent On', 'abp-rentalforge' ) : __( 'Rent Off', 'abp-rentalforge' ) ); ?></span>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span>
                                        </div>
                                    </div>
                                </td>
								<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                    <td>
										<?php if ( ! empty( $post_id ) ) { ?>
                                            <a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>" class="_abprf_fs_h5 _color_theme">
												<?php if ( ABPRF_Function::on_off( 'post_icon' ) ) {
													ABPRF_Layout::image_icon( ABPRF_Function::get_post_info( $post_id, 'post_icon' ) );
												}
													echo esc_html( get_the_title( $post_id ) ); ?>
                                            </a>
                                            <div class="_d_flex">
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Post Id : ', 'abp-rentalforge' ) . ' ' . $post_id ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $post_rent_continue == 'on' ? __( 'Rent On', 'abp-rentalforge' ) : __( 'Rent Off', 'abp-rentalforge' ) ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                            </div>
										<?php } else {
											echo esc_html( '❌' );
										} ?>
                                    </td>
								<?php } ?>
                                <th>
									<?php foreach ( $rent_rules as $key => $label ) {
										$price_info = $price_qty_info[ $key ] ?? []; ?>
                                        <div class="<?php echo esc_attr( $rent_rule == $key ? 'abp_active' : '' ); ?>" data-close="#<?php echo esc_attr( $key ); ?>">
											<?php //echo '<pre>';				print_r( $price_info );				echo '</pre>';
												$price = $price_info['price'] ?? '';
												if ( ! empty( $price ) ) {
													echo wp_kses_post( wc_price( $price ) );
													if ( $key == 'multi_day' || $key == 'multi_month' ) {
														$price_multi = $price_info['price_multi'] ?? '';
														if ( ! empty( $price_multi ) ) {
															echo '-' . wp_kses_post( wc_price( $price_multi ) );
														} else {
															echo esc_html( '❌' );
														}
													}
													?><span class="publish _d_block"><?php echo esc_html( $label ); ?></span><?php
												} else {
													echo esc_html( '❌' );
												}
											?>
                                        </div>
									<?php } ?>
                                </th>
								<?php if ( ABPRF_Function::on_off( 'deposit' ) ) { ?>
                                    <th>
										<?php
											$deposit_info  = $_price_info['deposit'] ?? [];
											$deposit_type  = $deposit_info['type'] ?? '';
											$deposit_value = $deposit_info['value'] ?? '';
											if ( ! empty( $deposit_type ) && ! empty( $deposit_value ) ) {
												ABPRF_Layout::item_deposit( $_price_info );
											} else {
												echo esc_html( '❌' );
											}
										?>
                                    </th>
								<?php } ?>
                                <th><?php echo esc_html( $_price_info['qty'] ?? '' ); ?></th>
                                <th>
									<?php if ( empty( $copy_post_id ) ) { ?>
                                        <div class="_group_content">
                                            <button type="button" class="_btn_light_info_xxs" data-id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property" title="<?php echo esc_html__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_html( $name ); ?>">✍️</button>
                                            <button type="button" class="_btn_light_info_xxs property_copy" data-id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property" title="<?php echo esc_html__( 'Copy/Clone : ', 'abp-rentalforge' ) . ' ' . esc_html( $name ); ?>">🔁</button>
                                            <button type="button" class="_btn_light_info_xxs delete_property" data-property_id="<?php echo esc_attr( $property_id ); ?>" title="<?php echo esc_html__( 'Trash : ', 'abp-rentalforge' ) . ' ' . esc_html( $name ); ?>">❌</button>
                                        </div>
									<?php } else { ?>
										<?php ABPRF_Layout::button_delete(); ?>
									<?php } ?>
                                </th>
                            </tr>
							<?php
							$count ++;
						} ?>
                        </tbody>
                    </table>
					<?php
					do_action( 'abprf_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_property, 'style' => 'ajax' ] );
				} else {
					ABPRF_Layout::layout_warning_info( 'not_property_found' );
				}
				//echo '<pre>';				print_r( $properties );				echo '</pre>';
			}
			public function post_rent_continue( $property = [], $post_ids = [], $_current_post_id = 0 ): void {
				$current_post_id = $property['post_id'] ?? $_current_post_id;
				$rent_continue   = $property['rent_continue'] ?? 'on';
				?>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Select Post', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <select class="_form_control " name="property_post_id" required>
                            <option disabled selected><?php esc_html_e( 'Please Select', 'abp-rentalforge' ); ?></option>
							<?php foreach ( $post_ids as $post_id ) {
								$title = get_the_title( $post_id );
								if ( ! empty( $title ) ) {
									?>
                                    <option value="<?php echo esc_attr( $post_id ); ?>" <?php echo esc_attr( $post_id == $current_post_id ? 'selected' : '' ); ?>><?php echo esc_html( $title ); ?></option>
								<?php }
							} ?>
                        </select>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'post_id' ); ?>
                </div>
                <div class="setting_item">
                    <div class="_fa_center">
						<?php ABPRF_Layout::switch_checkbox( 'rent_continue', $rent_continue ); ?>
                        <span class="_abp_label"><?php esc_html_e( 'Rent continue?', 'abp-rentalforge' ); ?></span>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'rent_continue' ); ?>
                </div>
				<?php
			}
			public function name_image_icon( $property = [] ): void {
				$others = json_decode( $property['others'] ?? '', true ) ?: [];
				$icon   = $others['icon'] ?? '';
				$name   = $property['name'] ?? '';
				?>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Property Name', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="text" class="_form_control validation_name" name="name" placeholder="<?php esc_attr_e( 'EX: Bike', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $name ); ?>" required/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'name' ); ?>
                </div>
                <div class="setting_item">
                    <divl class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Property Icon/Image', 'abp-rentalforge' ); ?></span>
						<?php do_action( 'abprf_add_image_icon', 'icon', $icon ); ?>
                    </divl>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'icon' ); ?>
                </div>
				<?php
			}
			public function brand_description( $property = [] ): void {
				$others      = json_decode( $property['others'] ?? '', true ) ?: [];
				$description = $others['description'] ?? '';
				$brand       = $property['brand'] ?? '';
				if ( ABPRF_Function::on_off( 'brand' ) ) {
					?>
                    <div class="setting_item">
                        <div class="_f_equal_f_wrap">
                            <span class="_abp_label"><?php esc_html_e( 'Property Brand', 'abp-rentalforge' ); ?></span>
                            <div class="brand_selection"><?php ABPRF_Brand::brand_selection( $brand ); ?></div>
                        </div>
						<?php ABPRF_Brand::brand_selection_form(); ?>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'brand' ); ?>
                    </div>
				<?php }
				if ( ABPRF_Function::on_off( 'property_des' ) ) { ?>
                    <div class="setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_abp_label"><?php esc_html_e( 'Property Short Description', 'abp-rentalforge' ); ?></span>
                            <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'EX: Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'description' ); ?>
                    </div>
					<?php
				}
			}
			public function property_price_qty( $property = [], $current_post_id = '' ): void {
				$rent_rules = ABPRF_Layout::rent_rules_options();
				$rent_rule  = ( $property['rent_rule'] ?? null ) ?: key( $rent_rules );
				$rent_rule  = ! empty( $current_post_id ) ? ABPRF_Function::get_post_info( $current_post_id, 'rent_rule', 'multi_day' ) : $rent_rule;
				$price_info = json_decode( $property['price_qty_info'] ?? '', true ) ?: [];
				?>
                <div class="setting_item full_width property_price_settings">
                    <div class=" _fj_between">
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Pricing and Quantity Configuration', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></h5>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
							<?php foreach ( $rent_rules as $key => $rule_label ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $rent_rule == $key ? 'abp_active' : '' ); ?>" data-close-target="#<?php echo esc_attr( $key ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <i class="_abprf_fs_h6"><span data-icon class="_mar_r_xs <?php echo esc_attr( $rent_rule == $key ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span></i><span class="_text_left_fs_label"><?php echo esc_html( $rule_label ); ?></span>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php ABPRF_Layout::info_text( 'price_rule' ); ?>
                    <div class="_divider_xs"></div>
					<?php self::price_qty( $price_info, $rent_rule, $current_post_id ); ?>
                </div>
				<?php
				do_action( 'abprf_location_stock', $price_info, $rent_rule, $current_post_id );
			}
			public static function price_qty( $price_info, $rent_rule, $current_post_id, $loc_id = '' ): void {
				$rent_rules = ABPRF_Layout::rent_rules_options();
				if ( ! empty( $loc_id ) ) {
					$price_info = ( $price_info[ $loc_id ] ?? null ) ?: [];
				}
				$_price_info = ( $price_info[ $rent_rule ] ?? null ) ?: [];
				?>
                <div class=" _ov_auto">
                    <table class="_abprf_fixed">
                        <thead>
                        <tr>
                            <th>
                                <div class="_f_equal _fj_center">
                                    <span><?php esc_html_e( 'Available Qty', 'abp-rentalforge' ); ?></span>
                                    <span><?php esc_html_e( 'Reserve Qty', 'abp-rentalforge' ); ?></span>
                                    <span><?php esc_html_e( 'Min Qty', 'abp-rentalforge' ); ?></span>
                                    <span><?php esc_html_e( 'Max Qty', 'abp-rentalforge' ); ?></span>
                                </div>
                            </th>
							<?php if ( ABPRF_Function::on_off( 'deposit' ) ) { ?>
                                <th>
                                    <div class="_f_equal _fj_center">
                                        <span><?php esc_html_e( 'Deposit Type', 'abp-rentalforge' ); ?></span>
                                        <span><?php esc_html_e( 'Deposit Value', 'abp-rentalforge' ); ?></span>
                                    </div>
                                </th>
							<?php } ?>
                            <th>
								<?php if ( $rent_rules['hourly'] ?? '' ) { ?>
                                    <div data-close="#hourly" class=" <?php echo esc_attr( $rent_rule == 'hourly' ? 'abp_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Hourly Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Hours ', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Hours', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['daily'] ?? '' ) { ?>
                                    <div data-close="#daily" class=" <?php echo esc_attr( $rent_rule == 'daily' ? 'abp_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Daily Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Days ', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Days', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['multi_day'] ?? '' ) { ?>
                                    <div data-close="#multi_day" class=" <?php echo esc_attr( $rent_rule == 'multi_day' ? 'abp_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Daily Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Hourly Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Days ', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Days', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['monthly'] ?? '' ) { ?>
                                    <div data-close="#monthly" class="<?php echo esc_attr( $rent_rule == 'monthly' ? 'abp_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Monthly Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Months ', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Months', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['multi_month'] ?? '' ) { ?>
                                    <div data-close="#multi_month" class="<?php echo esc_attr( $rent_rule == 'multi_month' ? 'abp_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Monthly Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Daily Rate', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Months ', 'abp-rentalforge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Months', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="_bg_white">
                        <tr>
                            <th>
								<?php
									$qty         = $_price_info['qty'] ?? '';
									$qty_reserve = $_price_info['reserve'] ?? '';
									$qty_min     = $_price_info['qty_min'] ?? '';
									$qty_max     = $_price_info['qty_min'] ?? ''; ?>
                                <div class="_group_content">
                                    <label>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $qty ); ?>" required/>
                                    </label>
                                    <label>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_reserve<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $qty_reserve ); ?>"/>
                                    </label>
                                    <label>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_min<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $qty_min ); ?>" required/>
                                    </label>
                                    <label>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_max<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $qty_max ); ?>"/>
                                    </label>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPRF_Layout::info_text( 'qty_reserve_min_max' ); ?>
                            </th>
							<?php if ( ABPRF_Function::on_off( 'deposit' ) ) { ?>
                                <th>
									<?php $deposit_info = $_price_info['deposit'] ?? [];
										$deposit_type   = $deposit_info['type'] ?? '';
										$deposit_value  = $deposit_info['value'] ?? ''; ?>
                                    <div class="_group_content">
                                        <label>
                                            <select class="_form_control " name="deposit_type<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>">
                                                <option disabled selected><?php esc_html_e( 'Please Select Deposit Type', 'abp-rentalforge' ); ?></option>
                                                <option value="fixed" <?php echo esc_attr( $deposit_type == 'fixed' ? 'selected' : '' ); ?>><?php esc_html_e( 'Fixed Amount', 'abp-rentalforge' ); ?></option>
                                                <option value="percent" <?php echo esc_attr( $deposit_type == 'percent' ? 'selected' : '' ); ?>><?php esc_html_e( 'Percentage(%) of Total Price', 'abp-rentalforge' ); ?></option>
                                                <option value="qty" <?php echo esc_attr( $deposit_type == 'qty' ? 'selected' : '' ); ?>><?php esc_html_e( 'Fixed Amount per Quantity', 'abp-rentalforge' ); ?></option>
                                            </select>
                                        </label>
                                        <label>
                                            <input type="text" class="_form_control validation_price" name="deposit_value<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $deposit_value ); ?>"/>
                                        </label>
                                    </div>
                                    <div class="_divider_xxs"></div>
									<?php ABPRF_Layout::info_text( 'deposit_type' ); ?>
                                </th>
							<?php } ?>
                            <th>
								<?php if ( $rent_rules['hourly'] ?? '' ) {
									$hourly_info  = $price_info['hourly'] ?? [];
									$price_hourly = $hourly_info['price'] ?? '';
									$min_hourly   = $hourly_info['min'] ?? '';
									$max_hourly   = $hourly_info['max'] ?? ''; ?>
                                    <div data-close="#hourly" class=" <?php echo esc_attr( $rent_rule == 'hourly' ? 'abp_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_hourly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_hourly ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_hourly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $min_hourly ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_hourly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_hourly ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'hourly_min_max' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['daily'] ?? '' ) {
									$daily_info  = $price_info['daily'] ?? [];
									$price_daily = $daily_info['price'] ?? '';
									$min_daily   = $daily_info['min'] ?? '';
									$max_daily   = $daily_info['max'] ?? ''; ?>
                                    <div data-close="#daily" class=" <?php echo esc_attr( $rent_rule == 'daily' ? 'abp_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_daily<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_daily ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_daily<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $min_daily ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_daily<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_daily ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'daily_min_max' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['multi_day'] ?? '' ) {
									$multi_day_info       = $price_info['multi_day'] ?? [];
									$price_multi_day      = $multi_day_info['price'] ?? '';
									$price_multi_day_hour = $multi_day_info['price_multi'] ?? '';
									$min_multi_day        = $multi_day_info['min'] ?? '';
									$max_multi_day        = $multi_day_info['max'] ?? ''; ?>
                                    <div data-close="#multi_day" class=" <?php echo esc_attr( $rent_rule == 'multi_day' ? 'abp_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_day<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_day ); ?>"/></label>
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_day_price<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_day_hour ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_multi_day<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $min_multi_day ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_multi_day<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_multi_day ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'daily_min_max' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['monthly'] ?? '' ) {
									$monthly_info  = $price_info['monthly'] ?? [];
									$price_monthly = $monthly_info['price'] ?? '';
									$min_monthly   = $monthly_info['min'] ?? '';
									$max_monthly   = $monthly_info['max'] ?? ''; ?>
                                    <div data-close="#monthly" class=" <?php echo esc_attr( $rent_rule == 'monthly' ? 'abp_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_monthly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_monthly ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_monthly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $min_monthly ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_monthly<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_monthly ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'monthly_min_max' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $rent_rules['multi_month'] ?? '' ) {
									$multi_month_info      = $price_info['multi_month'] ?? [];
									$price_multi_month_day = $multi_month_info['price_multi'] ?? '';
									$price_multi_month     = $multi_month_info['price'] ?? '';
									$min_multi_month       = $multi_month_info['min'] ?? '';
									$max_multi_month       = $multi_month_info['max'] ?? ''; ?>
                                    <div data-close="#multi_month" class=" <?php echo esc_attr( $rent_rule == 'multi_month' ? 'abp_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_month<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_month ); ?>"/></label>
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_month_price<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_month_day ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_multi_month<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $min_multi_month ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_multi_month<?php echo esc_attr( ! empty( $loc_id ) ? '_' . $loc_id : '' ); ?>" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_multi_month ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'monthly_min_max' ); ?>
                                    </div>
								<?php } ?>
                            </th>
                        </tr>
                        </tbody>
                    </table>
                </div>
				<?php
				do_action( 'abprf_property_discount', $price_info, $rent_rule, $current_post_id, $loc_id );
			}
			public function features( $property = [] ): void {
				if ( ABPRF_Function::on_off( 'feature' ) ) {
					?>
                    <div class="setting_item full_width property_feature">
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Feature Configuration', 'abp-rentalforge' ); ?></h5>
						<?php ABPRF_Layout::info_text( 'property_feature' ); ?>
                        <div class="_divider_xs"></div>
                        <div class="_d_flex">
                            <div class="selection_area">
                                <label>
                                    <input class="_form_control item_search" type="text" placeholder="<?php esc_attr_e( 'Search feature ....', 'abp-rentalforge' ); ?>"/>
                                </label>
                                <div class="selection_list"></div>
                            </div>
                            <div class="selected_area">
                                <input type="hidden" name="feature" value="<?php echo esc_attr( $property['features'] ?? '' ); ?>"/>
                                <div class="selected_list"></div>
                            </div>
                        </div>
                        <div class="configuration_content _mar_t_xs">
                            <div class="form_area">
                                <div class="hide_on_load">
                                    <table class="_abprf ">
                                        <tbody class="insertable_area sortable_area">
                                        </tbody>
                                    </table>
                                    <div class="_divider_xs"></div>
                                </div>
                                <div class="_fj_between">
									<?php ABPRF_Layout::button_add_xs( __( 'Add New Feature', 'abp-rentalforge' ) ); ?>
                                    <button type="button" class="_btn_theme_xs hide_on_load save_feature"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Feature', 'abp-rentalforge' ); ?></button>
                                </div>
                            </div>
                            <div class="abprf_d_none">
                                <table class="_abprf">
                                    <tbody class="hidden_content">
									<?php ABPRF_Feature::form_feature(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			public function gallery( $property = [] ): void {
				?>
                <div class="setting_item full_width">
                    <h5 class="_abprf_color_theme"><?php esc_html_e( 'Gallery Configuration', 'abp-rentalforge' ); ?></h5>
					<?php ABPRF_Layout::info_text( 'abprf_sliders' ); ?>
                    <div class="_divider_xs"></div>
					<?php do_action( 'abprf_add_image_multiple', 'abprf_sliders', ( $property['gallery'] ?? '' ) ); ?>
                </div>
				<?php
			}
			//===========================//
			public function save_property(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_val      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$int_val       = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? intval( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_textarea = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_array    = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args   = $post_array( 'filter_args' );
				$post_id       = $int_val( 'property_post_id' );
				$property_id   = $int_val( 'property_id' );
				$name          = $post_val( 'name' );
				$rent_rule     = $post_val( 'rent_rule' );
				$_rent_rule    = ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'rent_rule' ) : '';
				$rent_rule     = ! empty( $_rent_rule ) ? $_rent_rule : $rent_rule;
				$rent_rules    = ABPRF_Layout::rent_rules_options();
				if ( $name && $rent_rule ) {
					$price_info    = [];
					$rent_continue = $post_val( 'rent_continue', 'on' );
					$qty           = $int_val( 'qty', 1 );
					$reserve       = $int_val( 'qty_reserve', 0 );
					$qty_min       = $int_val( 'qty_min', 0 );
					$qty_max       = $int_val( 'qty_max' );
					$deposit_info  = [
						'type' => $post_val( 'deposit_type' ),
						'value' => $post_val( 'deposit_value' ),
					];
					foreach ( $rent_rules as $key => $label ) {
						$price = $post_val( 'price_' . $key );
						if ( ! empty( $price ) ) {
							$exit        = 1;
							$multi_price = '';
							if ( $key == 'multi_day' || $key == 'multi_month' ) {
								$exit        = 0;
								$multi_price = $post_val( 'price_' . $key . '_price' );
								if ( ! empty( $multi_price ) ) {
									$exit = 1;
								}
							}
							if ( $exit > 0 ) {
								$price_info[ $key ]['qty']     = $qty;
								$price_info[ $key ]['reserve'] = $reserve;
								$price_info[ $key ]['qty_min'] = $qty_min;
								$price_info[ $key ]['qty_max'] = $qty_max;
								$price_info[ $key ]['price']   = $price;
								$price_info[ $key ]['min']     = $int_val( 'min_' . $key, 1 );
								$price_info[ $key ]['max']     = $int_val( 'max_' . $key );
								if ( $key == 'multi_day' || $key == 'multi_month' ) {
									$price_info[ $key ]['price_multi'] = $multi_price;
								}
								$price_info[ $key ]['deposit'] = $deposit_info;
							}
						}
					}
					$price_info = apply_filters( 'abprf_filter_price_info', $price_info, $post_id );
					$others     = [
						'icon' => $post_val( 'icon' ),
						'description' => $post_textarea( 'description' )
					];
					$data       = [
						'post_id' => intval( $post_id ),
						'rent_continue' => $rent_continue,
						'name' => $name,
						'brand' => $post_val( 'brand' ),
						'category' => ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'abprf_category' ) : '',
						'location' => ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'abprf_location' ) : '',
						'features' => $post_val( 'feature' ),
						'rent_rule' => $rent_rule,
						'price_qty_info' => wp_json_encode( $price_info ),
						'gallery' => $post_val( 'abprf_sliders' ),
						'status' => ! empty( $post_id ) ? get_post_status( $post_id ) : '',
						'others' => wp_json_encode( $others ),
						'updated_at' => current_time( 'Y-m-d H:i' )
					];
					global $wpdb;
					$table_name = $wpdb->prefix . 'abprf_property';
					if ( $property_id ) {
						$where = [ 'id' => intval( $property_id ) ];
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $table_name, $data, $where );
						$msg = esc_html__( 'Property Updated Successfully...... !! ', 'abp-rentalforge' );
					} else {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->insert( $table_name, $data );
						$msg = esc_html__( 'Property Saved Successfully.... !! ', 'abp-rentalforge' );
					}
					ABPRF_Function::update_global_data( $post_id );
				} else {
					$msg = esc_html__( 'Property name , Rent rule  Can not be Blank . Property not Saved ..... !! ', 'abp-rentalforge' );
				}
				ob_start();
				$this->properties_table( $filter_args );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
			}
			public function add_property(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				ob_start();
				$post_int        = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? absint( wp_unslash( $_POST[ $key ] ) ) : $default;
				$property_id     = $post_int( 'tax_id' );
				$current_post_id = $post_int( 'post_id' );
				$property_copy   = $post_int( 'property_copy', 0 );
				$post_ids        = ABPRF_Query::get_post_id( [ 'status' => [ 'publish', 'draft', 'private', 'trash' ] ] );
				$save_text       = __( 'Save Property Configuration', 'abp-rentalforge' );
				$property        = [];
				if ( $property_id ) {
					$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
					if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
						$property  = current( $properties );
						$save_text = __( 'Update Property Configuration', 'abp-rentalforge' );
					}
					if ( $property_copy > 0 ) {
						$property_id = '';
						$save_text   = __( 'Copy Property Configuration', 'abp-rentalforge' );
					}
				}
				?>
                <div class="data_property rf_close_area">
                    <input type="hidden" name="property_id" value="<?php echo esc_attr( $property_id ); ?>">
                    <div class="group_setting">
						<?php
							$this->post_rent_continue( $property, $post_ids, $current_post_id );
							$this->name_image_icon( $property );
							$this->brand_description( $property );
							$this->property_price_qty( $property, $current_post_id );
							if ( method_exists( $this, 'features' ) ) {
								$this->features( $property );
							}
							if ( method_exists( $this, 'gallery' ) ) {
								$this->gallery( $property );
							}
						?>
                    </div>
                    <div class="_divider_xs"></div>
                    <button type="button" class="_btn_theme save_property"><span class="_mar_r_xxs">💾</span><?php echo esc_html( $save_text ); ?></button>
                </div>
				<?php
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Property Form Loaded Successfully .....! ', 'abp-rentalforge' ) ] );
			}
			public function reload_property_list(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				ob_start();
				$post_array             = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args            = $post_array( 'filter_args' );
				$post_id                = ( $filter_args['post_id'] ?? 'all' ) ?: 'all';
				$filter_args['post_id'] = $post_id;
				$this->properties_table( $filter_args );
				$table_html = ob_get_clean();
				wp_send_json_success( [ 'html' => $table_html, 'msg' => __( 'Property List Loaded successfully...... !! ', 'abp-rentalforge' ) ] );
			}
			public function property_delete(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
				if ( ! empty( $property_id ) && $property_id > 0 ) {
					$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
					if ( ! empty( $properties ) && sizeof( $properties ) > 0 ) {
						global $wpdb;
						$table_name = $wpdb->prefix . 'abprf_property';
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->delete( $table_name, array( 'id' => intval( $property_id ) ), array( '%d' ) );
					}
				}
				ob_start();
				$post_array             = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args            = $post_array( 'filter_args' );
				$post_id                = ( $filter_args['post_id'] ?? 'all' ) ?: 'all';
				$filter_args['post_id'] = $post_id;
				$this->properties_table( $filter_args );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Deleted Successfully............. ! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Properties();
	}