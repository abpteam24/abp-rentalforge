<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Location' ) ) {
		class ABPRF_Location {
			public function __construct() {
				add_action( 'abprf_global_location', [ $this, 'global_location' ] );
				add_action( 'abprf_location_update', [ $this, 'update_location' ] );
				add_action( 'wp_ajax_abprf_save_location', [ $this, 'save_location' ] );
				add_action( 'wp_ajax_abprf_delete_location', [ $this, 'delete_location' ] );
				add_action( 'wp_ajax_abprf_add_location', [ $this, 'add_location' ] );
			}
			public function global_location(): void {
				if ( ABPRF_Function::on_off( 'location' ) ) {
					?>
                    <div class="location_list _ov_auto">
						<?php $this->location_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <button type="button" class="_btn_default" data-target-popup="#abprf_global_popup" data-type="location">
                        <span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abp-rentalforge' ); ?>
                    </button>
					<?php
				}
			}
			public function update_location( $options = [], $id = '' ): void {
				$taxonomies   = ABPRF_Function::get_taxonomy( 'abprf_location' );
				$taxonomies   = is_array( $taxonomies ) ? $taxonomies : [];
				$location     = [];
				$old_location = ABPRF_Function::get_option( 'abprf_location' );
				$old_location = is_array( $old_location ) ? $old_location : [];
				if ( count( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$term_id                             = $taxonomy->term_id;
						$location[ $term_id ]['name']        = $taxonomy->name;
						$location[ $term_id ]['description'] = $taxonomy->description;
						$location[ $term_id ]['slug']        = $taxonomy->slug;
						if ( ! empty( $id ) && ! empty( $options ) && (int) $id === (int) $term_id ) {
							$new_location = $options;
						} else {
							$new_location = $old_location[ $term_id ] ?? [];
						}
						$location[ $term_id ]['display_pickup'] = $new_location['display_pickup'] ?? 'off';
						$location[ $term_id ]['pick_info']      = $new_location['pick_info'] ?? [];
						$location[ $term_id ]['display_drop']   = $new_location['display_drop'] ?? 'off';
						$location[ $term_id ]['drop_info']      = $new_location['drop_info'] ?? [];
					}
				}
				ksort( $location );
				update_option( 'abprf_location', $location );
			}
			public function location_list(): void {
				$all_locations = ABPRF_Function::get_option( 'abprf_location' );
				$all_locations = is_array( $all_locations ) ? $all_locations : [];
				$count         = 1;
				if ( count( $all_locations ) > 0 ) { ?>
                    <table class="_abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abp-rentalforge' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Location Title', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Pickup Point', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Drop-off Point', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Location Full Address', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Shortcode Post', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Shortcode Property', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'abp-rentalforge' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $all_locations as $term_id => $location ) {
							$name           = $location['name'] ?? '';
							$description    = $location['description'] ?? '';
							$display_pickup = $location['display_pickup'] ?? 'off';
							$pick_info      = $location['pick_info'] ?? [];
							$display_drop   = $location['display_drop'] ?? 'off';
							$drop_info      = $location['drop_info'] ?? [];
							?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( (int) $term_id ) ); ?>" target="_blank" class="_abp_fs_h5_color_theme"><?php echo esc_html( $name ); ?></a></th>
                                <th>
									<?php if ( $display_pickup === 'on' && ! empty( $pick_info ) ) { ?>
										<?php foreach ( $pick_info as $pick ) { ?>
                                            <div class="_section_xxs"> <?php echo esc_html( $pick ); ?></div>
										<?php } ?>
									<?php } ?>
                                </th>
                                <th>
									<?php if ( $display_drop === 'on' && ! empty( $drop_info ) ) { ?>
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
                                        <button type="button" class="_btn_light_yellow_mar_r_xxs" data-id="<?php echo esc_attr( $term_id ); ?>" data-target-popup="#abprf_global_popup" data-type="location" title="<?php echo esc_attr__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">✍️</button>
                                        <button type="button" class="_btn_light_danger_xxs delete_location" data-loc_id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">❌</button>
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
			public function form( $location = '', $loc_id = '' ): void {
				$name           = $location['name'] ?? '';
				$des            = $location['description'] ?? '';
				$slug           = $location['slug'] ?? '';
				$display_pickup = $location['display_pickup'] ?? 'off';
				$pick_infos     = $location['pick_info'] ?? [];
				$display_drop   = $location['display_drop'] ?? 'off';
				$drop_infos     = $location['drop_info'] ?? [];
				?>
                <input type="hidden" name="loc_term_id" value="<?php echo esc_attr( $loc_id ); ?>"/>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Location Name', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-rentalforge' ); ?>" required/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_name' ); ?>
                </div>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Location Slug (Optional)', 'abp-rentalforge' ); ?></span>
                        <input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-rentalforge' ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_slug' ); ?>
                </div>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php esc_html_e( 'Location Full Address', 'abp-rentalforge' ); ?></span>
                        <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Address', 'abp-rentalforge' ); ?>"><?php echo esc_html( $des ); ?></textarea>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'loc_des' ); ?>
                </div>
                <div class="setting_item _d_none">
                    <div class="_fj_between">
                        <div class="_fa_start">
							<?php ABPRF_Layout::switch_checkbox( 'display_pickup', $display_pickup ); ?>
                            <span class="_mar_b_xs"><?php esc_html_e( 'Multiple Pickup Point ?', 'abp-rentalforge' ); ?></span>
                        </div>
                        <div data-collapse="#display_pickup" class="configuration_content <?php echo esc_attr( $display_pickup === 'on' ? 'abp_active' : '' ); ?>">
                            <div class="insertable_area sortable_area _fd_column">
								<?php if ( ! empty( $pick_infos ) ) {
									foreach ( $pick_infos as $key => $pick_info ) {
										self::pickup_form( $pick_info, $key );
									}
								} ?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add New Pickup Point', 'abp-rentalforge' ) ); ?>
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
                <div class="setting_item _d_none">
                    <div class="_fj_between">
                        <div class="_fa_start">
							<?php ABPRF_Layout::switch_checkbox( 'display_drop', $display_drop ); ?>
                            <span class="_mar_b_xs"><?php esc_html_e( 'Multiple Drop Point ?', 'abp-rentalforge' ); ?></span>
                        </div>
                        <div data-collapse="#display_drop" class="configuration_content <?php echo esc_attr( $display_drop === 'on' ? 'abp_active' : '' ); ?>">
                            <div class="insertable_area sortable_area _fd_column">
								<?php if ( ! empty( $drop_infos ) ) {
									foreach ( $drop_infos as $key => $drop_info ) {
										self::drop_form( $drop_info, $key );
									}
								} ?>
                            </div>
							<?php ABPRF_Layout::button_add( __( 'Add New Drop Point', 'abp-rentalforge' ) ); ?>
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
                <button type="button" class="_btn_theme save_location"><span class="_mar_r_xxs">💾</span><?php echo( ! empty( $loc_id ) ? esc_html__( 'Update Location', 'abp-rentalforge' ) : esc_html__( 'Save Location', 'abp-rentalforge' ) ); ?></button>
				<?php
			}
			public static function pickup_form( $point = '', $key = '' ): void {
				?>
                <div class="delete_area _group_content _mar_b_xxs">
                    <label>
                        <input type="hidden" name="pick_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                        <input type="text" class="_form_control validation_name" name="pick_name[]" placeholder="<?php esc_attr_e( 'EX: Boston', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $point ); ?>" required/>
                    </label>
					<?php ABPRF_Layout::button_delete_sort(); ?>
                </div>
				<?php
			}
			public static function drop_form( $point = '', $key = '' ): void {
				?>
                <div class="delete_area _group_content _mar_b_xxs">
                    <label>
                        <input type="hidden" name="drop_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                        <input type="text" class="_form_control validation_name" name="drop_name[]" placeholder="<?php esc_attr_e( 'EX: Boston', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $point ); ?>" required/>
                    </label>
					<?php ABPRF_Layout::button_delete_sort(); ?>
                </div>
				<?php
			}
			public static function location_selection( $_location = '' ): void {
				$all_location   = ABPRF_Function::get_option( 'abprf_location' );
				$all_location   = is_array( $all_location ) ? $all_location : [];
				$location_array = ! empty( $_location ) ? explode( ',', $_location ) : [];
				if ( count( $all_location ) > 0 ) { ?>
                    <div class="custom_checkbox _fj_end">
                        <input type="hidden" name="abprf_location" value="<?php echo esc_attr( $_location ); ?>"/>
						<?php foreach ( $all_location as $key => $location ) {
							$name = $location['name'] ?? ''; ?>
                            <div class="checkbox_item _min_100">
                                <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( (string) $key, $location_array, true ) ? 'abp_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                    <span data-icon class="_mar_r_xs far <?php echo esc_attr( in_array( (string) $key, $location_array, true ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $name ); ?>
                                </button>
                            </div>
						<?php } ?>
                        <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abp-rentalforge' ); ?></button>
                    </div>
				<?php } else { ?>
                    <p><?php echo esc_html( ABPRF_Layout::array_info( 'no_location' ) ); ?></p>
                    <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New Location', 'abp-rentalforge' ); ?></button>
					<?php
				}
			}
			public function add_location(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$loc_id    = isset( $_POST['tax_id'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_id'] ) ) : '';
				$locations = ABPRF_Function::get_option( 'abprf_location' );
				$locations = is_array( $locations ) ? $locations : [];
				$location  = $locations[ $loc_id ] ?? [];
				ob_start();
				$this->form( $location, $loc_id );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Location Form Loaded Successfully .....! ', 'abp-rentalforge' ) ] );
			}
			public function save_location(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int       = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val       = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_slug      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_title( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_array     = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$cat_term_id    = $post_int( 'loc_term_id' );
				$name           = $post_val( 'name' );
				$slug           = $post_slug( 'slug' );
				$description    = $post_val( 'description' );
				$display_pickup = $post_val( 'display_pickup', 'off' );
				$display_drop   = $post_val( 'display_drop', 'off' );
				$abprf_post_id  = $post_int( 'abprf_post_id' );
				$pick_ids       = $post_array( 'pick_id' );
				$pick_names     = $post_array( 'pick_name' );
				$drop_ids       = $post_array( 'drop_id' );
				$drop_names     = $post_array( 'drop_name' );
				if ( empty( $name ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Location Name cannot be blank!', 'abp-rentalforge' ) ] );
				}
				if ( $cat_term_id > 0 ) {
					$result = wp_update_term( $cat_term_id, 'abprf_location', [
						'name' => $name,
						'slug' => $slug,
						'description' => $description,
					] );
				} else {
					$result = wp_insert_term( $name, 'abprf_location', [
						'slug' => $slug,
						'description' => $description,
					] );
				}
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => $result->get_error_message() ] );
				}
				$term_id = absint( $result['term_id'] ?? 0 );
				if ( $term_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Failed to resolve location context.', 'abp-rentalforge' ) ] );
				}
				$pickup_info = [];
				$drop_info   = [];
				$number      = 0;
				$number_drop = 0;
				if ( ! empty( $pick_names ) ) {
					foreach ( $pick_names as $key => $pick ) {
						if ( ! empty( $pick ) ) {
							$pick_id = $pick_ids[ $key ] ?? '';
							if ( empty( $pick_id ) ) {
								$pick_id = 'pick_id_' . $number;
								while ( isset( $pickup_info[ $pick_id ] ) ) {
									$number ++;
									$pick_id = 'pick_id_' . $number;
								}
							}
							$pickup_info[ $pick_id ] = $pick;
						}
					}
				}
				if ( ! empty( $drop_names ) ) {
					foreach ( $drop_names as $key => $drop ) {
						if ( ! empty( $drop ) ) {
							$drop_id = $drop_ids[ $key ] ?? '';
							if ( empty( $drop_id ) ) {
								$drop_id = 'drop_id_' . $number_drop;
								while ( isset( $drop_info[ $drop_id ] ) ) {
									$number_drop ++;
									$drop_id = 'drop_id_' . $number_drop;
								}
							}
							$drop_info[ $drop_id ] = $drop;
						}
					}
				}
				$options = [
					'display_pickup' => $display_pickup,
					'pick_info' => $pickup_info,
					'display_drop' => $display_drop,
					'drop_info' => $drop_info,
				];
				$this->update_location( $options, $term_id );
				$msg = __( 'Location Saved Successfully !', 'abp-rentalforge' );
				ob_start();
				if ( $abprf_post_id > 0 ) {
					$_location = ABPRF_Function::get_post_info( $abprf_post_id, 'abprf_location' );
					self::location_selection( $_location );
				} else {
					$this->location_list();
				}
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
			}
			public function delete_location(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$loc_id = isset( $_POST['loc_id'] ) ? sanitize_text_field( wp_unslash( $_POST['loc_id'] ) ) : '';
				$result = wp_delete_term( (int) $loc_id, 'abprf_location' );
				$this->update_location();
				ob_start();
				$this->location_list();
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message() ] );
				}
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$all_ids    = ABPRF_Query::get_post_id( [ 'loc_id' => $loc_id ] );
				if ( count( $all_ids ) > 0 ) {
					foreach ( $all_ids as $id ) {
						$location       = ABPRF_Function::get_post_info( $id, 'location' );
						$location_array = ! empty( $location ) ? explode( ',', $location ) : [];
						if ( in_array( $loc_id, $location_array, true ) ) {
							$location_array   = array_diff( $location_array, [ $loc_id ] );
							$updated_location = ! empty( $location_array ) ? implode( ',', $location_array ) : '';
							update_post_meta( $id, 'location', $updated_location );
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update(
								$table_name,
								[ 'location' => $updated_location ],
								[ 'post_id' => (int) $id ],
								[ '%s' ],
								[ '%d' ]
							);
						}
					}
				}
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Location Deleted Successfully !', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Location();
	}