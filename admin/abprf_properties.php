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
                        <h4 class="_abprf_color_white"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties', 'abprf-rental-forge' ); ?></h4>
                        <div class="abp_dropdown _max_400">
                            <label class="_abprf_all_center">
                                <input type="hidden" name="select_property_hidden" value=""/>
                                <input type="text" class="_form_control_text_center validation_name" name="select_property" placeholder="<?php esc_attr_e( 'Search  Post', 'abprf-rental-forge' ); ?>" value=""/>
                            </label>
                            <div class="dropdown_list">
                                <ul class="_abprf">
                                    <li data-value="all" data-text="<?php esc_attr_e( 'All Post', 'abprf-rental-forge' ); ?>"><?php esc_html_e( 'All Post', 'abprf-rental-forge' ); ?></li>
                                    <li data-value="on" data-text="<?php esc_attr_e( 'Rent Active', 'abprf-rental-forge' ); ?>"><?php esc_html_e( 'Rent Active', 'abprf-rental-forge' ); ?></li>
                                    <li data-value="off" data-text="<?php esc_attr_e( 'Rent De-active', 'abprf-rental-forge' ); ?>"><?php esc_html_e( 'Rent De-active', 'abprf-rental-forge' ); ?></li>
									<?php if ( ! empty( $post_ids ) && is_array( $post_ids ) && sizeof( $post_ids ) > 0 ) { ?>
										<?php foreach ( $post_ids as $post_id ) { ?>
                                            <li data-value="<?php echo esc_attr( $post_id ); ?>" data-text="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></li>
										<?php } ?>
									<?php } ?>
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="_btn_light_white_xs" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abprf-rental-forge' ); ?></button>
                    </div>
                    <div class="_panel_body properties_list">
						<?php $this->properties_table( $filter_args ); ?>
                    </div>
                </div>
				<?php
			}

			public function tab_content( $abprf_infos ): void {
				$copy_post_id                = array_key_exists( 'copy_post_id', $abprf_infos ) ? $abprf_infos['copy_post_id'] : '';
				$rent_rule                   = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : 'hourly';
				$day_time_start              = array_key_exists( 'day_time_start', $abprf_infos ) ? $abprf_infos['day_time_start'] : '';
				$day_time_end                = array_key_exists( 'day_time_end', $abprf_infos ) ? $abprf_infos['day_time_end'] : '';
				$hour_threshold              = array_key_exists( 'hour_threshold', $abprf_infos ) ? $abprf_infos['hour_threshold'] : 24;
				$cut_off_date                = array_key_exists( 'cut_off_date', $abprf_infos ) ? $abprf_infos['cut_off_date'] : 10;
				$day_threshold               = array_key_exists( 'day_threshold', $abprf_infos ) ? $abprf_infos['day_threshold'] : 30;
				$filter_args['copy_post_id'] = $copy_post_id;
				$post_id                     = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
				$filter_args['post_id']      = $post_id;
				$rent_rules                  = ABPRF_Layout::rent_rules();
				?>
                <div class="tab_item abprf_equipment_price" data-tabs="#abprf_equipment_price">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties and Price Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item ">
                        <div class="_fj_between">
                            <h5 class="_abprf"><?php esc_html_e( 'Rent Date & Time Rule', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></h5>
                            <div class="custom_radio">
                                <input type="hidden" class="_form_control" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
								<?php foreach ( $rent_rules as $key => $rule ) { ?>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $rent_rule == $key ? 'rf_active' : '' ); ?>" data-close-target="#<?php echo esc_attr( $key ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <i class="_abprf_fs_h5"><span data-icon class="_mar_r_xs <?php echo esc_attr( $rent_rule == $key ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span></i><span class="_text_left_fs_label"><?php echo esc_html( $rule ); ?></span>
                                        </button>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'rent_rule' ); ?>
                    </div>
                    <div class="<?php echo esc_attr( $rent_rule == 'daily' ? 'rf_active' : '' ); ?>" data-close="#daily">
                        <div class="group_setting">
                            <div class="_setting_item">
                                <div class="_f_wrap_fj_between_fa_center">
                                    <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Day time Start-End', 'abprf-rental-forge' ); ?></span>
                                    <div class="_group_content">
										<?php ABPRF_Layout::input_time( 'day_time_start', $day_time_start );
											ABPRF_Layout::input_time( 'day_time_end', $day_time_end ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'day_time_start_end' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $rent_rule == 'multi_day' ? 'rf_active' : '' ); ?>" data-close="#multi_day">
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Hour Threshold', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="hour_threshold" placeholder="Ex:30" value="<?php echo esc_attr( $hour_threshold ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'hour_threshold' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $rent_rule == 'monthly' ? 'rf_active' : '' ); ?>" data-close="#monthly">
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Month Cut-Off Date', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="cut_off_date" placeholder="Ex:10" value="<?php echo esc_attr( $cut_off_date ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'cut_off_date' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $rent_rule == 'multi_month' ? 'rf_active' : '' ); ?>" data-close="#multi_month">
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Day Threshold', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="day_threshold" placeholder="Ex:10" value="<?php echo esc_attr( $day_threshold ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'day_threshold' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="properties_list">
						<?php $this->properties_table( $filter_args ); ?>
                    </div>
					<?php if ( empty( $copy_post_id ) ) { ?>
                        <div class="_divider_xs"></div>
                        <button type="button" class="_btn_default" data-post_id="<?php echo esc_attr( $post_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abprf-rental-forge' ); ?></button>
					<?php } ?>
                </div>
				<?php
			}

			public function save_property() {
				$filter_args = [];
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$filter_args = isset( $_POST['filter_args'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['filter_args'] ) ), true ) : [];
					$post_id     = isset( $_POST['property_post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_post_id'] ) ) : '';
					$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
					$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
					$rent_rule   = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : '';
					$_rent_rule  = ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'rent_rule' ) : '';
					$rent_rule   = ! empty( $_rent_rule ) ? $_rent_rule : $rent_rule;
					$rent_rules  = ABPRF_Layout::rent_rules();
					if ( $name && $rent_rule ) {
						$price_info            = [];
						$rent_continue         = isset( $_POST['rent_continue'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_continue'] ) ) : 'on';
						$qty                   = intval( isset( $_POST['qty'] ) ? sanitize_text_field( wp_unslash( $_POST['qty'] ) ) : 1 );
						$reserve               = intval( isset( $_POST['qty_reserve'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_reserve'] ) ) : 0 );
						$qty_min               = isset( $_POST['qty_min'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_min'] ) ) : '';
						$qty_max               = isset( $_POST['qty_max'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_max'] ) ) : '';
						$deposit_info['type']  = isset( $_POST['deposit_type'] ) ? sanitize_text_field( wp_unslash( $_POST['deposit_type'] ) ) : '';
						$deposit_info['value'] = isset( $_POST['deposit_value'] ) ? sanitize_text_field( wp_unslash( $_POST['deposit_value'] ) ) : '';
						foreach ( $rent_rules as $key => $label ) {
							$price = isset( $_POST[ 'price_' . $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'price_' . $key ] ) ) : '';
							if ( ! empty( $price ) ) {
								$exit        = 1;
								$multi_price = '';
								if ( $key == 'multi_day' || $key == 'multi_month' ) {
									$exit        = 0;
									$multi_price = isset( $_POST[ 'price_' . $key . '_price' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'price_' . $key . '_price' ] ) ) : '';
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
									$price_info[ $key ]['min']     = isset( $_POST[ 'min_' . $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'min_' . $key ] ) ) : 1;
									$price_info[ $key ]['max']     = isset( $_POST[ 'max_' . $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'max_' . $key ] ) ) : '';
									if ( $key == 'multi_day' || $key == 'multi_month' ) {
										$price_info[ $key ]['price_multi'] = $multi_price;
									}
									$price_info[ $key ]['deposit'] = $deposit_info;
								}
							}
						}
						$others ['icon']        = isset( $_POST['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['icon'] ) ) : '';
						$others ['description'] = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
						$data                   = [
							'post_id' => intval( $post_id ),
							'rent_continue' => $rent_continue,
							'name' => sanitize_text_field( $name ),
							'brand' => isset( $_POST['brand'] ) ? sanitize_text_field( wp_unslash( $_POST['brand'] ) ) : '',
							'category' => ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'abprf_category' ) : '',
							'location' => ! empty( $post_id ) ? ABPRF_Function::get_post_info( $post_id, 'abprf_location' ) : '',
							'features' => isset( $_POST['feature'] ) ? sanitize_text_field( wp_unslash( $_POST['feature'] ) ) : '',
							'rent_rule' => sanitize_text_field( $rent_rule ),
							'price_qty_info' => json_encode( $price_info ),
							'gallery' => isset( $_POST['abprf_sliders'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_sliders'] ) ) : '',
							'status' => ! empty( $post_id ) ? get_post_status( $post_id ) : '',
							'others' => json_encode( $others ),
							'updated_at' => current_time( 'Y-m-d H:i' )
						];
						global $wpdb;
						$table_name = $wpdb->prefix . 'abprf_property';
						if ( $property_id ) {
							$where = [ 'id' => $property_id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
							$msg = esc_html__( 'Property Updated Successfully...... !! ', 'abprf-rental-forge' );
						} else {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->insert( $table_name, $data );
							$msg = esc_html__( 'Property Saved Successfully.... !! ', 'abprf-rental-forge' );
						}
						ABPRF_Function::update_global_data( $post_id );
					} else {
						$msg = esc_html__( 'Property name , Rent rule  Can not be Blank . Property not Saved ..... !! ', 'abprf-rental-forge' );
					}
				} else {
					$msg = esc_html__( 'Property not Saved ! Authentication Error .... !! ', 'abprf-rental-forge' );
				}
				ob_start();
				$this->properties_table( $filter_args );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
				wp_die();
			}

			public function add_property() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$property_id     = isset( $_POST['tax_id'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_id'] ) ) : '';
					$current_post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
					$property_copy   = isset( $_POST['property_copy'] ) ? sanitize_text_field( wp_unslash( $_POST['property_copy'] ) ) : 0;
					$post_ids        = ABPRF_Query::get_post_id( [ 'status' => [ 'publish', 'draft', 'private', 'trash' ] ] );
					$save_text       = __( 'Save Property Configuration', 'abprf-rental-forge' );
					$property        = [];
					if ( $property_id ) {
						$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
						if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
							$property  = current( $properties );
							$save_text = __( 'Update Property Configuration', 'abprf-rental-forge' );
						}
						if ( $property_copy > 0 ) {
							$property_id = '';
							$save_text   = __( 'Copy Property Configuration', 'abprf-rental-forge' );
						}
					}
					?>
                    <div class="data_property rf_close_area">
                        <input type="hidden" name="property_id" value="<?php echo esc_attr( $property_id ); ?>">
						<?php
							$this->post_rent_continue( $property, $post_ids, $current_post_id );
							$this->name_image_icon( $property );
							$this->brand_description( $property );
							$this->property_price_qty( $property, $current_post_id );
							$this->features( $property );
							$this->gallery( $property );
						?>
                        <div class="_divider_xs"></div>
                        <button type="button" class="_btn_theme save_property"><span class="_mar_r_xxs">💾</span><?php echo esc_html( $save_text ); ?></button>
                    </div>
					<?php
				}
				wp_die();
			}

			public function reload_property_list() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$filter_args            = isset( $_POST['filter_args'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['filter_args'] ) ) : [];
					$post_id                = array_key_exists( 'post_id', $filter_args ) && $filter_args['post_id'] != '' ? $filter_args['post_id'] : 'all';
					$filter_args['post_id'] = $post_id;
					$this->properties_table( $filter_args );
				}
				wp_die();
			}

			public function property_delete() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
					if ( ! empty( $property_id ) && $property_id > 0 ) {
						$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
						if ( ! empty( $properties ) && sizeof( $properties ) > 0 ) {
							global $wpdb;
							$table_name = $wpdb->prefix . 'abprf_property';
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->delete( $table_name, array( 'id' => $property_id ), array( '%d' ) );
						}
					}
					ob_start();
					$filter_args            = isset( $_POST['filter_args'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['filter_args'] ) ) : [];
					$post_id                = array_key_exists( 'post_id', $filter_args ) && $filter_args['post_id'] != '' ? $filter_args['post_id'] : 'all';
					$filter_args['post_id'] = $post_id;
					$this->properties_table( $filter_args );
					$html = ob_get_clean();
					wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Deleted Successfully............. ! ', 'abprf-rental-forge' ) ] );
				} else {
					wp_send_json_success( [ 'html' => esc_html__( 'Something Error Occur !', 'abprf-rental-forge' ), 'msg' => esc_html__( 'Something Error Occur !', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function properties_table( $filter_args ): void {
				$total_property = ABPRF_Query::get_property( $filter_args, true );
				// echo '<pre>';print_r($filter_args);echo '</pre>';
				$page_number           = array_key_exists( 'page_number', $filter_args ) && is_numeric( $filter_args['page_number'] ) ? (int) $filter_args['page_number'] : 1;
				$limit                 = array_key_exists( 'page_item', $filter_args ) && is_numeric( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$count                 = ( $page_number - 1 ) * $limit + 1;
				$filter_args['limit']  = $limit;
				$filter_args['offset'] = $count - 1;
				$properties            = ABPRF_Query::get_property( $filter_args );
				if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
					$filter_post_id = array_key_exists( 'post_id', $filter_args ) ? $filter_args['post_id'] : '';
					$copy_post_id   = array_key_exists( 'copy_post_id', $filter_args ) ? $filter_args['copy_post_id'] : '';
					$rent_rules     = ABPRF_Layout::rent_rules();
					?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abprf-rental-forge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image/icon', 'abprf-rental-forge' ); ?></th>
                            <th><?php esc_html_e( 'Property', 'abprf-rental-forge' ); ?></th>
							<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                <th><?php esc_html_e( 'Post Information', 'abprf-rental-forge' ); ?></th>
							<?php } ?>
                            <th><?php esc_html_e( 'Price', 'abprf-rental-forge' ); ?></th>
                            <th><?php esc_html_e( 'Deposit', 'abprf-rental-forge' ); ?></th>
                            <th><?php esc_html_e( 'Stock', 'abprf-rental-forge' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'abprf-rental-forge' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $properties as $property ) {
							$others             = array_key_exists( 'others', $property ) ? $property['others'] : '';
							$others             = ! empty( $others ) ? json_decode( $others, true ) : [];
							$icon               = array_key_exists( 'icon', $others ) ? $others['icon'] : '';
							$name               = array_key_exists( 'name', $property ) ? $property['name'] : '';
							$property_id        = array_key_exists( 'id', $property ) ? $property['id'] : '';
							$post_id            = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
							$status             = array_key_exists( 'status', $property ) ? $property['status'] : '';
							$rent_continue      = array_key_exists( 'rent_continue', $property ) ? $property['rent_continue'] : '';
							$post_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
							$post_status        = get_post_status( $post_id );
							$rent_rule          = array_key_exists( 'rent_rule', $property ) ? $property['rent_rule'] : '';
							$price_qty_info     = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
							$price_qty_info     = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
							$_price_info        = array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
							?>
                            <tr class="delete_area">
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_fs_h2"><?php ABPRF_Layout::image_icon( $icon ); ?></th>
                                <td>
                                    <div class="_fd_column">
                                        <h5 class="_abprf_color_theme"><?php echo esc_html( $name ); ?></h5>
                                        <div class="_d_flex">
											<?php if ( ! empty( $copy_post_id ) ) { ?>
                                                <input type="hidden" name="copy_property_id[]" value="<?php echo esc_attr( $property_id ); ?>"/>
											<?php } else { ?>
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Property Id : ', 'abprf-rental-forge' ) . ' ' . $property_id ); ?></span>
											<?php } ?>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $rent_continue == 'on' ? __( 'Rent On', 'abprf-rental-forge' ) : __( 'Rent Off', 'abprf-rental-forge' ) ); ?></span>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span>
                                        </div>
                                    </div>
                                </td>
								<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                    <td>
										<?php if ( ! empty( $post_id ) ) { ?>
                                            <a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>" class="_abprf_fs_h5 _color_theme"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
                                            <div class="_d_flex">
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Post Id : ', 'abprf-rental-forge' ) . ' ' . $post_id ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $post_rent_continue == 'on' ? __( 'Rent On', 'abprf-rental-forge' ) : __( 'Rent Off', 'abprf-rental-forge' ) ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                            </div>
										<?php } else {
											echo esc_html( '❌' );
										} ?>
                                    </td>
								<?php } ?>
                                <th>
									<?php foreach ( $rent_rules as $key => $label ) {
										$price_info = array_key_exists( $key, $price_qty_info ) ? $price_qty_info[ $key ] : []; ?>
                                        <div class="<?php echo esc_attr( $rent_rule == $key ? 'rf_active' : '' ); ?>" data-close="#<?php echo esc_attr( $key ); ?>">
											<?php //echo '<pre>';				print_r( $price_info );				echo '</pre>';
												$price = is_array( $price_info ) && array_key_exists( 'price', $price_info ) ? $price_info['price'] : '';
												if ( ! empty( $price ) ) {
													echo wp_kses_post( wc_price( $price ) );
													if ( $key == 'multi_day' || $key == 'multi_month' ) {
														$price_multi = is_array( $price_info ) && array_key_exists( 'price_multi', $price_info ) ? $price_info['price_multi'] : '';
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
                                <th>
									<?php
										$deposit_info  = array_key_exists( 'deposit', $_price_info ) ? $_price_info['deposit'] : [];
										$deposit_type  = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
										$deposit_value = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
										if ( ! empty( $deposit_type ) && ! empty( $deposit_value ) ) {
											ABPRF_Layout::item_deposit( $_price_info );
										} else {
											echo esc_html( '❌' );
										}
									?>
                                </th>
                                <th><?php echo esc_html( array_key_exists( 'qty', $_price_info ) ? $_price_info['qty'] : '' ); ?></th>
                                <th>
									<?php if ( empty( $copy_post_id ) ) { ?>
                                        <div class="_f_wrap">
                                            <button type="button" class="_btn_light_yellow_mar_r_xxs" data-id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property" title="<?php echo esc_html__( 'Edit : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">✍️</button>
                                            <button type="button" class="_btn_light_navy_blue _mar_r_xxs property_copy" data-id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_global_popup" data-type="property" title="<?php echo esc_html__( 'Copy/Clone : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">🔁</button>
                                            <button type="button" class="_btn_light_danger_xxs delete_property" data-property_id="<?php echo esc_attr( $property_id ); ?>" title="<?php echo esc_html__( 'Trash : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">❌</button>
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
				$current_post_id = array_key_exists( 'post_id', $property ) ? $property['post_id'] : $_current_post_id;
				$rent_continue   = array_key_exists( 'rent_continue', $property ) ? $property['rent_continue'] : 'on';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Select Post', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <select class="_form_control " name="property_post_id" required>
                                <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
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
                    <div class="_setting_item">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox( 'rent_continue', $rent_continue ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Rent continue?', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'rent_continue' ); ?>
                    </div>
                </div>
				<?php
			}

			public function name_image_icon( $property = [] ): void {
				$others = array_key_exists( 'others', $property ) ? $property['others'] : '';
				$others = ! empty( $others ) ? json_decode( $others, true ) : [];
				$icon   = array_key_exists( 'icon', $others ) ? $others['icon'] : '';
				$name   = array_key_exists( 'name', $property ) ? $property['name'] : '';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Property Name', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <input type="text" class="_form_control validation_name" name="name" placeholder="<?php esc_attr_e( 'EX: Bike', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $name ); ?>" required/>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'name' ); ?>
                    </div>
                    <div class="_setting_item">
                        <divl class="_f_equal_f_wrap">
                            <span class="_fs_label_mar_r_xs"><?php esc_html_e( 'Property Icon/Image', 'abprf-rental-forge' ); ?></span>
							<?php do_action( 'abprf_add_image_icon', 'icon', $icon ); ?>
                        </divl>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'icon' ); ?>
                    </div>
                </div>
				<?php
			}

			public function brand_description( $property = [] ): void {
				$others      = array_key_exists( 'others', $property ) ? $property['others'] : '';
				$others      = ! empty( $others ) ? json_decode( $others, true ) : [];
				$description = array_key_exists( 'description', $others ) ? $others['description'] : '';
				$brand       = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
				?>
                <div class="group_setting">
                    <div class="_setting_item">
                        <div class="_f_equal_f_wrap">
                            <span class="_mar_r_xs_fs_label"><?php esc_html_e( 'Property Brand', 'abprf-rental-forge' ); ?></span>
                            <div class="brand_selection"><?php ABPRF_Brand::brand_selection( $brand ); ?></div>
                        </div>
						<?php ABPRF_Brand::brand_selection_form(); ?>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'brand' ); ?>
                    </div>
                    <div class="_setting_item">
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Property Short Description', 'abprf-rental-forge' ); ?></span>
                            <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'EX: Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'description' ); ?>
                    </div>
                </div>
				<?php
			}

			public function property_price_qty( $property = [], $current_post_id = '' ): void {
				$price_info  = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
				$rent_rule   = array_key_exists( 'rent_rule', $property ) ? $property['rent_rule'] : 'multi_day';
				$rent_rule   = ! empty( $current_post_id ) ? ABPRF_Function::get_post_info( $current_post_id, 'rent_rule' ) : $rent_rule;
				$price_info  = ! empty( $price_info ) ? json_decode( $price_info, true ) : [];
				$_price_info = ! empty( $price_info ) && array_key_exists( $rent_rule, $price_info ) ? $price_info[ $rent_rule ] : [];
				/**************************/
				$qty         = array_key_exists( 'qty', $_price_info ) ? $_price_info['qty'] : '';
				$qty_reserve = array_key_exists( 'reserve', $_price_info ) ? $_price_info['reserve'] : '';
				$qty_min     = array_key_exists( 'qty_min', $_price_info ) ? $_price_info['qty_min'] : '';
				$qty_max     = array_key_exists( 'qty_min', $_price_info ) ? $_price_info['qty_min'] : '';
				/**************************/
				$deposit_info  = array_key_exists( 'deposit', $_price_info ) ? $_price_info['deposit'] : [];
				$deposit_type  = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
				$deposit_value = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
				/**************************/
				$hourly_info  = array_key_exists( 'hourly', $price_info ) ? $price_info['hourly'] : [];
				$price_hourly = is_array( $hourly_info ) && array_key_exists( 'price', $hourly_info ) ? $hourly_info['price'] : '';
				$min_hourly   = is_array( $hourly_info ) && array_key_exists( 'min', $hourly_info ) ? $hourly_info['min'] : '';
				$max_hourly   = is_array( $hourly_info ) && array_key_exists( 'max', $hourly_info ) ? $hourly_info['max'] : '';
				/**************************/
				$daily_info  = array_key_exists( 'daily', $price_info ) ? $price_info['daily'] : [];
				$price_daily = is_array( $daily_info ) && array_key_exists( 'price', $daily_info ) ? $daily_info['price'] : '';
				$min_daily   = is_array( $daily_info ) && array_key_exists( 'min', $daily_info ) ? $daily_info['min'] : '';
				$max_daily   = is_array( $daily_info ) && array_key_exists( 'max', $daily_info ) ? $daily_info['max'] : '';
				/**************************/
				$multi_day_info       = array_key_exists( 'multi_day', $price_info ) ? $price_info['multi_day'] : [];
				$price_multi_day      = is_array( $multi_day_info ) && array_key_exists( 'price', $multi_day_info ) ? $multi_day_info['price'] : '';
				$price_multi_day_hour = is_array( $multi_day_info ) && array_key_exists( 'price_multi', $multi_day_info ) ? $multi_day_info['price_multi'] : '';
				$min_multi_day        = is_array( $multi_day_info ) && array_key_exists( 'min', $multi_day_info ) ? $multi_day_info['min'] : '';
				$max_multi_day        = is_array( $multi_day_info ) && array_key_exists( 'max', $multi_day_info ) ? $multi_day_info['max'] : '';
				/**************************/
				$monthly_info  = array_key_exists( 'monthly', $price_info ) ? $price_info['monthly'] : [];
				$price_monthly = is_array( $monthly_info ) && array_key_exists( 'price', $monthly_info ) ? $monthly_info['price'] : '';
				$min_monthly   = is_array( $monthly_info ) && array_key_exists( 'min', $monthly_info ) ? $monthly_info['min'] : '';
				$max_monthly   = is_array( $monthly_info ) && array_key_exists( 'max', $monthly_info ) ? $monthly_info['max'] : '';
				/**************************/
				$multi_month_info      = array_key_exists( 'multi_month', $price_info ) ? $price_info['multi_month'] : [];
				$price_multi_month_day = is_array( $multi_month_info ) && array_key_exists( 'price_multi', $multi_month_info ) ? $multi_month_info['price_multi'] : '';
				$price_multi_month     = is_array( $multi_month_info ) && array_key_exists( 'price', $multi_month_info ) ? $multi_month_info['price'] : '';
				$min_multi_month       = is_array( $multi_month_info ) && array_key_exists( 'min', $multi_month_info ) ? $multi_month_info['min'] : '';
				$max_multi_month       = is_array( $multi_month_info ) && array_key_exists( 'max', $multi_month_info ) ? $multi_month_info['max'] : '';
				/**************************/
				$rent_rules = ABPRF_Layout::rent_rules();
				?>
                <div class="_setting_item">
                    <div class=" _fj_between">
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Pricing and Quantity Configuration', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></h5>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
							<?php foreach ( $rent_rules as $key => $rule_label ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $rent_rule == $key ? 'rf_active' : '' ); ?>" data-close-target="#<?php echo esc_attr( $key ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <i class="_abprf_fs_h6"><span data-icon class="_mar_r_xs <?php echo esc_attr( $rent_rule == $key ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span></i><span class="_text_left_fs_label"><?php echo esc_html( $rule_label ); ?></span>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php ABPRF_Layout::info_text( 'price_rule' ); ?>
                    <div class="_divider_xs"></div>
                    <div class=" _ov_auto">
                        <table class="_abprf_fixed">
                            <thead>
                            <tr>
                                <th>
                                    <div class="_f_equal _fj_center">
                                        <span><?php esc_html_e( 'Available Qty', 'abprf-rental-forge' ); ?></span>
                                        <span><?php esc_html_e( 'Reserve Qty', 'abprf-rental-forge' ); ?></span>
                                        <span><?php esc_html_e( 'Min Qty', 'abprf-rental-forge' ); ?></span>
                                        <span><?php esc_html_e( 'Max Qty', 'abprf-rental-forge' ); ?></span>
                                    </div>
                                </th>
                                <th>
                                    <div class="_f_equal _fj_center">
                                        <span><?php esc_html_e( 'Deposit Type', 'abprf-rental-forge' ); ?></span>
                                        <span><?php esc_html_e( 'Deposit Value', 'abprf-rental-forge' ); ?></span>
                                    </div>
                                </th>
                                <th>
                                    <div data-close="#hourly" class=" <?php echo esc_attr( $rent_rule == 'hourly' ? 'rf_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Hours ', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Hours', 'abprf-rental-forge' ); ?></span>
                                        </div>
                                    </div>
                                    <div data-close="#daily" class=" <?php echo esc_attr( $rent_rule == 'daily' ? 'rf_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Days ', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Days', 'abprf-rental-forge' ); ?></span>
                                        </div>
                                    </div>
                                    <div data-close="#multi_day" class=" <?php echo esc_attr( $rent_rule == 'multi_day' ? 'rf_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Days ', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Days', 'abprf-rental-forge' ); ?></span>
                                        </div>
                                    </div>
                                    <div data-close="#monthly" class="<?php echo esc_attr( $rent_rule == 'monthly' ? 'rf_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Monthly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Months ', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Months', 'abprf-rental-forge' ); ?></span>
                                        </div>
                                    </div>
                                    <div data-close="#multi_month" class="<?php echo esc_attr( $rent_rule == 'multi_month' ? 'rf_active' : '' ); ?>">
                                        <div class="_f_equal _fj_center">
                                            <span><?php esc_html_e( 'Monthly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Min Months ', 'abprf-rental-forge' ); ?></span>
                                            <span><?php esc_html_e( 'Max Months', 'abprf-rental-forge' ); ?></span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="_bg_white">
                            <tr>
                                <th>
                                    <div class="_group_content">
                                        <label>
                                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty ); ?>" required/>
                                        </label>
                                        <label>
                                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_reserve" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_reserve ); ?>"/>
                                        </label>
                                        <label>
                                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_min" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_min ); ?>" required/>
                                        </label>
                                        <label>
                                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_max" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_max ); ?>"/>
                                        </label>
                                    </div>
                                    <div class="_divider_xxs"></div>
									<?php ABPRF_Layout::info_text( 'qty_reserve_min_max' ); ?>
                                </th>
                                <th>
                                    <div class="_group_content">
                                        <label>
                                            <select class="_form_control " name="deposit_type">
                                                <option disabled selected><?php esc_html_e( 'Please Select Deposit Type', 'abprf-rental-forge' ); ?></option>
                                                <option value="fixed" <?php echo esc_attr( $deposit_type == 'fixed' ? 'selected' : '' ); ?>><?php esc_html_e( 'Fixed Amount', 'abprf-rental-forge' ); ?></option>
                                                <option value="percent" <?php echo esc_attr( $deposit_type == 'percent' ? 'selected' : '' ); ?>><?php esc_html_e( 'Percentage(%) of Total Price', 'abprf-rental-forge' ); ?></option>
                                                <option value="qty" <?php echo esc_attr( $deposit_type == 'qty' ? 'selected' : '' ); ?>><?php esc_html_e( 'Fixed Amount per Quantity', 'abprf-rental-forge' ); ?></option>
                                            </select>
                                        </label>
                                        <label>
                                            <input type="text" class="_form_control validation_price" name="deposit_value" placeholder="Ex: 10" value="<?php echo esc_attr( $deposit_value ); ?>"/>
                                        </label>
                                    </div>
                                    <div class="_divider_xxs"></div>
									<?php ABPRF_Layout::info_text( 'deposit_type' ); ?>
                                </th>
                                <th>
                                    <div data-close="#hourly" class=" <?php echo esc_attr( $rent_rule == 'hourly' ? 'rf_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_hourly" placeholder="Ex: 10" value="<?php echo esc_attr( $price_hourly ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_hourly" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_hourly ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_hourly" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_hourly ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'hourly_min_max' ); ?>
                                    </div>
                                    <div data-close="#daily" class=" <?php echo esc_attr( $rent_rule == 'daily' ? 'rf_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_daily" placeholder="Ex: 10" value="<?php echo esc_attr( $price_daily ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_daily" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_daily ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_daily" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_daily ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'daily_min_max' ); ?>
                                    </div>
                                    <div data-close="#multi_day" class=" <?php echo esc_attr( $rent_rule == 'multi_day' ? 'rf_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_day" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_day ); ?>"/></label>
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_day_price" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_day_hour ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_multi_day" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_multi_day ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_multi_day" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_multi_day ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'daily_min_max' ); ?>
                                    </div>
                                    <div data-close="#monthly" class=" <?php echo esc_attr( $rent_rule == 'monthly' ? 'rf_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_monthly" placeholder="Ex: 10" value="<?php echo esc_attr( $price_monthly ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_monthly" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_monthly ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_monthly" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_monthly ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'monthly_min_max' ); ?>
                                    </div>
                                    <div data-close="#multi_month" class=" <?php echo esc_attr( $rent_rule == 'multi_month' ? 'rf_active' : '' ); ?>">
                                        <div class="_group_content">
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_month" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_month ); ?>"/></label>
                                            <label><input type="text" class="_form_control validation_price" name="price_multi_month_price" placeholder="Ex: 10" value="<?php echo esc_attr( $price_multi_month_day ); ?>"/></label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_multi_month" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_multi_month ); ?>"/>
                                            </label>
                                            <label>
                                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="max_multi_month" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_multi_month ); ?>"/>
                                            </label>
                                        </div>
                                        <div class="_divider_xxs"></div>
										<?php ABPRF_Layout::info_text( 'monthly_min_max' ); ?>
                                    </div>
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}

			public function features( $property = [] ): void {
				$features = array_key_exists( 'features', $property ) ? $property['features'] : '';
				?>
                <div class="_setting_item">
                    <h5 class="_abprf_color_theme"><?php esc_html_e( 'Feature Configuration', 'abprf-rental-forge' ); ?></h5>
					<?php ABPRF_Layout::info_text( 'property_feature' ); ?>
                    <div class="_divider_xs"></div>
                    <div class="feature_selection"><?php ABPRF_Feature::feature_selection( $features ); ?></div>
                </div>
				<?php
			}

			public function gallery( $property = [] ): void {
				$sliders = array_key_exists( 'gallery', $property ) ? $property['gallery'] : '';
				?>
                <div class="_setting_item">
                    <h5 class="_abprf_color_theme"><?php esc_html_e( 'Gallery Configuration', 'abprf-rental-forge' ); ?></h5>
					<?php ABPRF_Layout::info_text( 'abprf_sliders' ); ?>
                    <div class="_divider_xs"></div>
					<?php do_action( 'abprf_add_image_multiple', 'abprf_sliders', $sliders ); ?>
                </div>
				<?php
			}
		}
		new ABPRF_Properties();
	}