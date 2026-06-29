<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Brand' ) ) {
		class ABPRF_Brand {
			public function __construct() {
				add_action( 'abprf_global_brand', array( $this, 'global_brand' ) );
				add_action( 'abprf_brand_update', array( $this, 'update_brand' ) );
				add_action( 'wp_ajax_abprf_save_brand', array( $this, 'save_brand' ) );
				add_action( 'wp_ajax_abprf_delete_brand', array( $this, 'delete_brand' ) );
				add_action( 'wp_ajax_abprf_edit_brand', array( $this, 'edit_brand' ) );
			}
			public function global_brand(): void {
				if ( ABPRF_Function::on_off( 'brand' ) ) {
					?>
                    <div class="tab_item brand_area" data-tabs="#abprf_global_brand">
                        <div class="brand_list _ov_auto">
							<?php $this->brand_list(); ?>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="configuration_content">
                            <div class="form_area">
                                <div class="hide_on_load">
                                    <table class="_abp ">
                                        <thead>
                                        <tr>
                                            <th><?php esc_html_e( 'Icon', 'abp-rentalforge' ); ?></th>
                                            <th><?php esc_html_e( 'Brand Title', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></th>
                                            <th><?php esc_html_e( 'Brand Slug (Optional)', 'abp-rentalforge' ); ?></th>
                                            <th><?php esc_html_e( 'Description', 'abp-rentalforge' ); ?></th>
                                            <th class="_w_10"><?php esc_html_e( 'Action', 'abp-rentalforge' ); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody class="insertable_area sortable_area">
                                        </tbody>
                                    </table>
                                    <div class="_divider_xs"></div>
                                </div>
                                <div class="_fj_between">
									<?php ABPRF_Layout::button_add( __( 'Add New Brand', 'abp-rentalforge' ) ); ?>
                                    <button type="button" class="_btn_theme hide_on_load save_brand"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Brand', 'abp-rentalforge' ); ?></button>
                                </div>
                            </div>
                            <div class="abprf_d_none">
                                <table class="_abp">
                                    <tbody class="hidden_content">
									<?php self::form_brand(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			public function update_brand( $options = [] ): void {
				$taxonomies = ABPRF_Function::get_taxonomy( 'abprf_brand' );
				$brands     = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
					$brand = ABPRF_Function::get_option( 'abprf_brand' );
					$brand = is_array( $brand ) ? $brand : [];
					foreach ( $taxonomies as $taxonomy ) {
						$term_id                           = $taxonomy->term_id;
						$brands[ $term_id ]['name']        = $taxonomy->name;
						$brands[ $term_id ]['description'] = $taxonomy->description;
						$brands[ $term_id ]['slug']        = $taxonomy->slug;
						if ( isset( $options[ $term_id ] ) ) {
							$brands[ $term_id ]['icon'] = $options[ $term_id ];
						} elseif ( isset( $brand[ $term_id ]['icon'] ) ) {
							$brands[ $term_id ]['icon'] = $brand[ $term_id ]['icon'];
						} else {
							$brands[ $term_id ]['icon'] = '';
						}
					}
				}
				ksort( $brands );
				update_option( 'abprf_brand', $brands );
			}
			public function brand_list(): void {
				$abprf_brands = ABPRF_Function::get_option( 'abprf_brand' );
				$count        = 1;
				if ( ! empty( $abprf_brands ) && is_array( $abprf_brands ) && sizeof( $abprf_brands ) > 0 ) { ?>
                    <table class="_abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abp-rentalforge' ) ?></th>
                            <th class="_min_200"><?php esc_html_e( 'Brand Title', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abp-rentalforge' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Description', 'abp-rentalforge' ) ?></th>
                            <th class="_w_250"><?php esc_html_e( 'Shortcode Property', 'abp-rentalforge' ) ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Action', 'abp-rentalforge' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $abprf_brands as $term_id => $brand ) {
							$name        = $brand['name'] ?? '';
							$description = $brand['description'] ?? '';
							$icon        = $brand['icon'] ?? '';
							?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" target="_blank" class="_abp_fs_h5 _color_theme"><?php ABPRF_Layout::image_icon( $icon ); ?><?php echo esc_html( $name ); ?></a></th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <td><?php echo esc_html( $description ); ?></td>
                                <th><code> [abprf-property brand_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th>
                                    <div class="_f_wrap">
                                        <button type="button" class="_btn_light_yellow_mar_r_xxs edit_brand" data-id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">✍️</button>
                                        <button type="button" class="_btn_light_danger_xxs delete_brand" data-id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">❌</button>
                                    </div>
                                </th>
                            </tr>
							<?php $count ++;
						} ?>
                        </tbody>
                    </table>
				<?php } else {
					ABPRF_Layout::layout_warning_info( 'no_brand' );
				}
			}
			public static function brand_selection( $_brand = '' ): void {
				$brand_array = ! empty( $_brand ) ? explode( ',', $_brand ) : [];
				$brands      = ABPRF_Function::get_option( 'abprf_brand' );
				if ( ! empty( $brands ) && is_array( $brands ) && sizeof( $brands ) > 0 ) { ?>
                    <label class="_w_full">
                        <select class="_form_control" name="brand">
                            <option value="" selected><?php esc_html_e( 'Please Select Brand', 'abp-rentalforge' ); ?></option>
							<?php foreach ( $brands as $key => $brand ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( in_array( $key, $brand_array ) ? 'selected' : '' ); ?>><?php echo esc_html( $brand['name'] ?? '' ); ?></option>
							<?php } ?>
                        </select>
                    </label>
				<?php } else { ?>
                    <p class="_abp"><?php echo esc_html( ABPRF_Layout::array_info( 'no_brand' ) ); ?></p>
				<?php }
			}
			public static function form_brand( $brand = [], $id = '' ): void {
				$name        = $brand['name'] ?? '';
				$slug        = $brand['slug'] ?? '';
				$icon        = $brand['icon'] ?? '';
				$description = $brand['description'] ?? '';
				?>
                <tr class="delete_area">
                    <th><?php do_action( 'abprf_add_icon', 'brand_icon[]', $icon ); ?></th>
                    <th>
                        <label>
                            <input type="hidden" name="brand_id[]" value="<?php echo esc_attr( $id ); ?>"/>
                            <input class="_form_control_min_auto_w_full" name="brand_name[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-rentalforge' ); ?>" required/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input class="_form_control_min_auto_w_full" name="brand_slug[]" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-rentalforge' ); ?>"/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <textarea class="_form_control_min_auto_w_full" name="brand_description[]" placeholder="<?php esc_attr_e( 'Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                    </th>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			public static function brand_selection_form( $brand = [], $id = '' ): void {
				$name        = $brand['name'] ?? '';
				$slug        = $brand['slug'] ?? '';
				$icon        = $brand['icon'] ?? '';
				$description = $brand['description'] ?? '';
				?>
                <div class="configuration_content">
                    <div class="form_area">
                        <div class="hide_on_load">
                            <table class="_abp ">
                                <tbody class="insertable_area sortable_area">
                                </tbody>
                            </table>
                            <div class="_divider_xs"></div>
                        </div>
                        <div class="_fj_between">
							<?php ABPRF_Layout::button_add_xs( __( 'Add New Brand', 'abp-rentalforge' ) ); ?>
                            <button type="button" class="_btn_theme_xs hide_on_load save_brand"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Brand', 'abp-rentalforge' ); ?></button>
                        </div>
                    </div>
                    <div class="abprf_d_none">
                        <table class="_abp">
                            <tbody class="hidden_content">
                            <tr class="delete_area">
                                <th><?php do_action( 'abprf_add_icon', 'brand_icon[]', $icon ); ?></th>
                                <th>
                                    <label>
                                        <input type="hidden" name="brand_id[]" value="<?php echo esc_attr( $id ); ?>"/>
                                        <input class="_form_control_min_auto_w_full" name="brand_name[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-rentalforge' ); ?>" required/>
                                    </label>
                                </th>
                                <th>
                                    <label>
                                        <input class="_form_control_min_auto_w_full" name="brand_slug[]" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-rentalforge' ); ?>"/>
                                    </label>
                                </th>
                                <th>
                                    <label>
                                        <textarea class="_form_control_min_auto_w_full" name="brand_description[]" placeholder="<?php esc_attr_e( 'Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                                    </label>
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}
			public function save_brand(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int            = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_int_array      = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'absint', wp_unslash( $_POST[ $key ] ) ) : [];
				$post_array          = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$post_slug_array     = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_title', wp_unslash( $_POST[ $key ] ) ) : [];
				$post_textarea_array = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$ids                 = $post_int_array( 'brand_id' );
				$names               = $post_array( 'brand_name' );
				$icons               = $post_array( 'brand_icon' );
				$slugs               = $post_slug_array( 'brand_slug' );
				$descriptions        = $post_textarea_array( 'brand_description' );
				$property_add        = $post_int( 'property_add' );
				$options             = [];
				$msg                 = '';
				if ( ! empty( $names ) ) {
					foreach ( $names as $key => $name ) {
						if ( ! empty( $name ) ) {
							$id          = $ids[ $key ] ?? 0;
							$slug        = $slugs[ $key ] ?? '';
							$description = $descriptions[ $key ] ?? '';
							if ( $id > 0 ) {
								$result = wp_update_term( $id, 'abprf_brand', [
									'name' => $name,
									'slug' => $slug,
									'description' => $description,
								] );
							} else {
								$result = wp_insert_term( $name, 'abprf_brand', [
									'slug' => $slug,
									'description' => $description,
								] );
							}
							if ( is_wp_error( $result ) ) {
								$msg = $result->get_error_message();
							} else {
								if ( ! $id ) {
									$id = absint( $result['term_id'] ?? 0 );
								}
								if ( $id > 0 ) {
									$options[ $id ] = $icons[ $key ] ?? '';
								}
							}
						}
					}
					$this->update_brand( $options );
					if ( empty( $msg ) ) {
						$msg = __( 'Brand Saved Successfully !', 'abp-rentalforge' );
					}
				} else {
					$msg = __( 'Brand not Saved ! Brand Name can not Blank !...!', 'abp-rentalforge' );
				}
				ob_start();
				if ( $property_add > 0 ) {
					$brands      = '';
					$property_id = $post_int( 'property_id' );
					if ( $property_id > 0 ) {
						$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
						if ( ! empty( $properties ) && is_array( $properties ) ) {
							$property = current( $properties );
							$brands   = $property['brand'] ?? '';
						}
					}
					self::brand_selection( $brands );
				} else {
					$this->brand_list();
				}
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
			}
			public function delete_brand(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$brand_id = isset( $_POST['brand_id'] ) ? absint( wp_unslash( $_POST['brand_id'] ) ) : 0;
				if ( $brand_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Brand ID ..... !!', 'abp-rentalforge' ) ], 400 );
				}
				$result = wp_delete_term( $brand_id, 'abprf_brand' );
				if ( is_wp_error( $result ) ) {
					ob_start();
					$this->brand_list();
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message() ], 400 );
				}
				$brands = ABPRF_Function::get_option( 'abprf_brand' );
				if ( is_array( $brands ) && isset( $brands[ $brand_id ] ) ) {
					unset( $brands[ $brand_id ] );
					update_option( 'abprf_brand', $brands );
				}
				ob_start();
				$this->brand_list();
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Brand Delete Successfully !', 'abp-rentalforge' ) ] );
			}
			public function edit_brand(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
				if ( $id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Brand ID ..... !!', 'abp-rentalforge' ) ], 400 );
				}
				$brands = ABPRF_Function::get_option( 'abprf_brand' );
				$brands = is_array( $brands ) ? $brands : [];
				$brand  = $brands[ $id ] ?? [];
				ob_start();
				self::form_brand( $brand, $id );
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'msg' => __( 'Brand Ready to Editing...... !', 'abp-rentalforge' )
				] );
			}
		}
		new ABPRF_Brand();
	}