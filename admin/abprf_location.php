<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Location' ) ) {
		class ABPRF_Location {
			public function __construct() {
				add_action( 'abprf_global_location', array( $this, 'global_location' ) );
				add_action( 'wp_ajax_abprf_save_location', array( $this, 'save_location' ) );
				add_action( 'wp_ajax_abprf_delete_location', array( $this, 'delete_location' ) );
				add_action( 'wp_ajax_abprf_add_location', array( $this, 'add_location' ) );
			}

			public function global_location(): void {
				?>
                <div class="tab_item" data-tabs="#abprf_global_location">
                    <div class="location_list _ov_auto">
						<?php $this->location_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <button type="button" class="_btn_default" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abprf-rental-forge' ); ?></button>
                </div>
				<?php
			}

			public function save_location() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$cat_term_id    = isset( $_POST['loc_term_id'] ) ? sanitize_text_field( wp_unslash( $_POST['loc_term_id'] ) ) : '';
					$name           = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
					$slug           = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
					$description    = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
					$display_pickup = isset( $_POST['display_pickup'] ) ? sanitize_text_field( wp_unslash( $_POST['display_pickup'] ) ) : 'off';
					$pick_ids       = isset( $_POST['pick_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['pick_id'] ) ) : [];
					$pick_names     = isset( $_POST['pick_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['pick_name'] ) ) : [];
					$display_drop   = isset( $_POST['display_drop'] ) ? sanitize_text_field( wp_unslash( $_POST['display_drop'] ) ) : 'off';
					$drop_ids       = isset( $_POST['drop_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['drop_id'] ) ) : [];
					$drop_names     = isset( $_POST['drop_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['drop_name'] ) ) : [];
					$target_type    = isset( $_POST['target_type'] ) ? sanitize_text_field( wp_unslash( $_POST['target_type'] ) ) : 0;
					$options        = [];
					if ( ! empty( $name ) ) {
						if ( ! empty( $cat_term_id ) ) {
							$result = wp_update_term( $cat_term_id, 'abprf_location', array(
								'name' => $name,
								'slug' => $slug,
								'description' => $description,
							) );
						} else {
							$result = wp_insert_term(
								$name,
								'abprf_location',
								array(
									'slug' => $slug,
									'description' => $description,
								)
							);
						}
						if ( is_wp_error( $result ) ) {
							$msg = $result->get_error_message();
						} else {
							if ( empty( $id ) ) {
								$id = is_array( $result ) && array_key_exists( 'term_id', $result ) ? $result['term_id'] : '';
							}
							if ( ! empty( $id ) ) {
								$pickup_info = [];
								$drop_info   = [];
								$number      = 0;
								$number_drop = 0;
								if ( ! empty( $pick_names ) ) {
									foreach ( $pick_names as $key => $pick ) {
										if ( ! empty( $pick ) ) {
											$pick_id = array_key_exists( $key, $pick_ids ) ? $pick_ids[ $key ] : '';
											if ( empty( $pick_id ) ) {
												$pick_id = 'pick_id_' . $number;
												while ( array_key_exists( $pick_id, $pickup_info ) ) {
													$number    = (int) str_replace( 'pick_id_', '', $pick_id );
													$new_count = $number + 1;
													$pick_id   = 'pick_id_' . $new_count;
												}
											}
											$pickup_info[ $pick_id ] = $pick;
										}
									}
									$options['display_pickup'] = $display_pickup;
									$options['pick_info']      = $pickup_info;
									if ( ! empty( $drop_names ) ) {
										foreach ( $drop_names as $key => $drop ) {
											if ( ! empty( $drop ) ) {
												$drop_id = array_key_exists( $key, $drop_ids ) ? $drop_ids[ $key ] : '';
												if ( empty( $drop_id ) ) {
													$drop_id = 'drop_id_' . $number_drop;
													while ( array_key_exists( $drop_id, $drop_info ) ) {
														$number_drop = (int) str_replace( 'drop_id_', '', $drop_id );
														$new_count   = $number_drop + 1;
														$drop_id     = 'drop_id_' . $new_count;
													}
												}
												$drop_info[ $drop_id ] = $drop;
											}
										}
										$options['display_drop'] = $display_drop;
										$options['drop_info']    = $drop_info;
									}
								}
							}
							$this->update_location( $options, $id );
							$msg = esc_html__( 'Location Saved Successfully !', 'abprf-rental-forge' );
						}
					} else {
						$msg = esc_html__( 'Location Name can not blank....!', 'abprf-rental-forge' );
					}
					ob_start();
					if ( $target_type == 'post' ) {
						self::location_selection();
					} elseif ( $target_type == 'list' ) {
						$this->location_list();
					}
					$html = ob_get_clean();
				} else {
					$html = '';
					$msg  = esc_html__( 'not Saved !', 'abprf-rental-forge' );
				}
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
				wp_die();
			}

			public function delete_location() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$loc_id = isset( $_POST['loc_id'] ) ? sanitize_text_field( wp_unslash( $_POST['loc_id'] ) ) : '';
					$result = wp_delete_term( $loc_id, 'abprf_location' );
					$this->update_location();
					ob_start();
					$this->location_list();
					$html = ob_get_clean();
					if ( is_wp_error( $result ) ) {
						wp_send_json_success( [ 'html' => $html, 'msg' => $result->get_error_message() ] );
					} else {
						global $wpdb;
						$table_name = $wpdb->prefix . 'abprf_property';
						$all_ids    = ABPRF_Query::get_post_id( [ 'loc_id' => $loc_id ] );
						if ( ! empty( $all_ids ) && sizeof( $all_ids ) > 0 ) {
							foreach ( $all_ids as $id ) {
								$location = ABPRF_Function::get_post_info( $id, 'location' );
								$location = ! empty( $location ) ? explode( ',', $location ) : [];
								if ( ! empty( $location ) && in_array( $loc_id, $location ) ) {
									$location = array_diff( $location, [ $loc_id ] );
									$location = ! empty( $location ) ? implode( ',', $location ) : '';
									update_post_meta( $id, 'location', $location );
									$data  = [ 'location' => $location ];
									$where = [ 'post_id' => $id ];
									// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
									$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
								}
							}
						}
						wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Location Delete Successfully !', 'abprf-rental-forge' ) ] );
					}
				}
				wp_die();
			}

			public function update_location( $options = [], $id = '' ): void {
				$taxonomies = ABPRF_Function::get_taxonomy( 'abprf_location' );
				$location   = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					$old_location = ABPRF_Function::get_option( 'abprf_location' );
					foreach ( $taxonomies as $taxonomy ) {
						$location[ $taxonomy->term_id ]['name']        = $taxonomy->name;
						$location[ $taxonomy->term_id ]['description'] = $taxonomy->description;
						$location[ $taxonomy->term_id ]['slug']        = $taxonomy->slug;
						if ( ! empty( $id ) && ! empty( $options ) && $id == $taxonomy->term_id ) {
							$new_location = $options;
						} else {
							if ( array_key_exists( $taxonomy->term_id, $old_location ) ) {
								$new_location = $old_location[ $taxonomy->term_id ];
							}
						}
						if ( ! empty( $new_location ) ) {
							$location[ $taxonomy->term_id ]['display_pickup'] = array_key_exists( 'display_pickup', $new_location ) ? $new_location['display_pickup'] : 'off';
							$location[ $taxonomy->term_id ]['pick_info']      = array_key_exists( 'pick_info', $new_location ) ? $new_location['pick_info'] : [];
							$location[ $taxonomy->term_id ]['display_drop']   = array_key_exists( 'display_drop', $new_location ) ? $new_location['display_drop'] : 'off';
							$location[ $taxonomy->term_id ]['drop_info']      = array_key_exists( 'drop_info', $new_location ) ? $new_location['drop_info'] : [];
						}
					}
				}
				ksort( $location );
				update_option( 'abprf_location', $location );
			}

			public function location_list(): void {
				$all_locations = ABPRF_Locations;
				//echo '<pre>'; print_r( $all_locations ); echo '</pre>';
				$count = 1;
				if ( ! empty( $all_locations ) && is_array( $all_locations ) && sizeof( $all_locations ) > 0 ) { ?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abprf-rental-forge' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Location Title', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Pickup Point', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Drop-off Point', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Location Full Address', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Shortcode Post', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Shortcode Property', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'abprf-rental-forge' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $all_locations as $term_id => $location ) {
							$name           = is_array( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : '';
							$description    = is_array( $location ) && array_key_exists( 'description', $location ) ? $location['description'] : '';
							$display_pickup = is_array( $location ) && array_key_exists( 'display_pickup', $location ) ? $location['display_pickup'] : 'off';
							$pick_info      = is_array( $location ) && array_key_exists( 'pick_info', $location ) ? $location['pick_info'] : [];
							$display_drop   = is_array( $location ) && array_key_exists( 'display_drop', $location ) ? $location['display_drop'] : 'off';
							$drop_info      = is_array( $location ) && array_key_exists( 'drop_info', $location ) ? $location['drop_info'] : [];
							?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" target="_blank" class="_abprf_fs_h5 _color_theme"><?php echo esc_html( $name ); ?></a></th>
                                <th>
									<?php if ( $display_pickup == 'on' && ! empty( $pick_info ) ) { ?>
										<?php foreach ( $pick_info as $pick ) { ?>
                                            <div class="_section_xxs"> <?php echo esc_html( $pick ); ?></div>
										<?php } ?>
									<?php } ?>
                                </th>
                                <th>
									<?php if ( $display_drop == 'on' && ! empty( $drop_info ) ) { ?>
										<?php foreach ( $drop_info as $drop ) { ?>
                                            <div class="_section_xxs"> <?php echo esc_html( $drop ); ?></div>
										<?php } ?>
									<?php } ?>
                                </th>
                                <td><?php echo esc_html( $description ); ?></td>
                                <th class="_text_nowrap"><code> [abprf-post loc_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th class="_text_nowrap"><code> [abprf-property loc_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <td>
                                    <div class="_d_flex">
                                        <button type="button" class="_btn_light_yellow_mar_r_xxs" data-id="<?php echo esc_attr( $term_id ); ?>" data-target-popup="#abprf_global_popup" data-type="location" title="<?php echo esc_attr__( 'Edit : ', 'abprf-rental-forge' ) . ' ' . esc_attr( $name ); ?>">✍️</button>
                                        <button type="button" class="_btn_light_danger_xxs delete_location" data-loc_id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abprf-rental-forge' ) . ' ' . esc_attr( $name ); ?>">❌</button>
                                    </div>
                                </td>
                            </tr>
							<?php $count ++;
						} ?>
                        </tbody>
                    </table>
				<?php } else {
					ABPRF_Layout::layout_warning_info( 'no_location' );
				}
			}

			public function add_location() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$loc_id    = isset( $_POST['tax_id'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_id'] ) ) : '';
					$locations = ABPRF_Function::get_option( 'abprf_location' );
					$location  = array_key_exists( $loc_id, $locations ) ? $locations[ $loc_id ] : [];
					$this->form( $location, $loc_id );
				}
				wp_die();
			}

			public function form( $location = '', $loc_id = '' ) {
				$name           = is_array( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : '';
				$des            = is_array( $location ) && array_key_exists( 'description', $location ) ? $location['description'] : '';
				$slug           = is_array( $location ) && array_key_exists( 'slug', $location ) ? $location['slug'] : '';
				$display_pickup = array_key_exists( 'display_pickup', $location ) ? $location['display_pickup'] : 'off';
				$pick_infos     = array_key_exists( 'pick_info', $location ) ? $location['pick_info'] : [];
				$display_drop   = array_key_exists( 'display_drop', $location ) ? $location['display_drop'] : 'off';
				$drop_infos     = array_key_exists( 'drop_info', $location ) ? $location['drop_info'] : [];
				?>
                <input type="hidden" name="loc_term_id" value="<?php echo esc_attr( $loc_id ); ?>"/>
                <div class="_setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Location Name', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                        <input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abprf-rental-forge' ); ?>" required/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_name' ); ?>
                </div>
                <div class="_setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Location Slug (Optional)', 'abprf-rental-forge' ); ?></span>
                        <input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abprf-rental-forge' ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_slug' ); ?>
                </div>
                <div class="_setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Location Full Address', 'abprf-rental-forge' ); ?></span>
                        <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Address', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $des ); ?></textarea>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_des' ); ?>
                </div>
                <div class="_setting_item">
                    <div class="_fj_between">
                        <div class="_fa_start">
							<?php ABPRF_Layout::switch_checkbox( 'display_pickup', $display_pickup ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Multiple Pickup Point ?', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div data-collapse="#display_pickup" class=" configuration_content <?php echo esc_attr( $display_pickup == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="insertable_area sortable_area _fd_column">
								<?php if ( sizeof( $pick_infos ) > 0 ) {
									foreach ( $pick_infos as $key => $pick_info ) {
										self::pickup_form( $pick_info, $key );
									}
								} ?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add New Pickup Point', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="hidden_content">
									<?php self::pickup_form(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'display_pickup' ); ?>
                </div>
                <div class="_setting_item">
                    <div class="_fj_between">
                        <div class="_fa_start">
							<?php ABPRF_Layout::switch_checkbox( 'display_drop', $display_drop ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Multiple Drop Point ?', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div data-collapse="#display_drop" class=" configuration_content <?php echo esc_attr( $display_drop == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="insertable_area sortable_area _fd_column">
								<?php if ( sizeof( $drop_infos ) > 0 ) {
									foreach ( $drop_infos as $key => $drop_info ) {
										self::drop_form( $drop_info, $key );
									}
								} ?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add New Drop Point', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="hidden_content">
									<?php self::drop_form(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'display_drop' ); ?>
                </div>
                <div class="_divider_xs"></div>
                <button type="button" class="_btn_theme save_location"><span class="_mar_r_xxs">💾</span><?php echo( ! empty( $term_id ) ? esc_html__( 'Update Location', 'abprf-rental-forge' ) : esc_html__( 'Save Location', 'abprf-rental-forge' ) ); ?></button>
				<?php
			}

			public function pickup_form( $point = '', $key = '' ) {
				?>
                <div class="delete_area _group_content _mar_b_xxs">
                    <label>
                        <input type="hidden" name="pick_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                        <input type="text" class="_form_control validation_name" name="pick_name[]" placeholder="<?php esc_attr_e( 'EX: Boston', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $point ); ?>" required/>
                    </label>
					<?php ABPRF_Layout::button_delete_sort(); ?>
                </div>
				<?php
			}

			public function drop_form( $point = '', $key = '' ) {
				?>
                <div class="delete_area _group_content _mar_b_xxs">
                    <label>
                        <input type="hidden" name="drop_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                        <input type="text" class="_form_control validation_name" name="drop_name[]" placeholder="<?php esc_attr_e( 'EX: Boston', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $point ); ?>" required/>
                    </label>
					<?php ABPRF_Layout::button_delete_sort(); ?>
                </div>
				<?php
			}

			public static function location_selection( $_location = '' ): void {
				$all_location   = ABPRF_Function::get_option( 'abprf_location' );
				$location_array = ! empty( $_location ) ? explode( ',', $_location ) : [];
				if ( ! empty( $all_location ) && is_array( $all_location ) && sizeof( $all_location ) > 0 ) { ?>
                    <div class="custom_checkbox">
                        <input type="hidden" name="abprf_location" value="<?php echo esc_attr( $_location ); ?>"/>
						<?php foreach ( $all_location as $key => $location ) {
							$name = is_array( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : ''; ?>
                            <div class="checkbox_item _min_100">
                                <button type="button" class="_btn_white_xs <?php echo esc_attr( in_array( $key, $location_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                    <span data-icon class="_mar_r_xs far <?php echo esc_attr( in_array( $key, $location_array ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $name ); ?>
                                </button>
                            </div>
						<?php } ?>
                        <button type="button" class="_btn_theme_xs" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abprf-rental-forge' ); ?></button>
                    </div>
				<?php } else { ?>
                    <p><?php echo esc_html( ABPRF_Layout::array_info( 'no_location' ) ); ?></p>
                    <button type="button" class="_btn_theme_xs" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abprf-rental-forge' ); ?></button>
					<?php
				}
			}
		}
		new ABPRF_Location();
	}