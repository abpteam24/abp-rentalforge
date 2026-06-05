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

			public function global_brand() {
				?>
                <div class="tab_item brand_area" data-tabs="#abprf_global_brand">
                    <div class="brand_list _ov_auto">
						<?php $this->brand_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="configuration_content">
                        <div class="form_area">
                            <div class="hide_on_load">
                                <table class="_abprf ">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Icon', 'abprf-rental-forge' ); ?></th>
                                        <th><?php esc_html_e( 'Brand Title', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                        <th><?php esc_html_e( 'Brand Slug (Optional)', 'abprf-rental-forge' ); ?></th>
                                        <th><?php esc_html_e( 'Description', 'abprf-rental-forge' ); ?></th>
                                        <th class="_w_10"><?php esc_html_e( 'Action', 'abprf-rental-forge' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="insertable_area sortable_area">
                                    </tbody>
                                </table>
                                <div class="_divider_xs"></div>
                            </div>
                            <div class="_fj_between">
								<?php ABPRF_Layout::button_add( __( 'Add New Brand', 'abprf-rental-forge' ) ); ?>
                                <button type="button" class="_btn_theme hide_on_load save_brand"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Brand', 'abprf-rental-forge' ); ?></button>
                            </div>
                        </div>
                        <div class="abprf_d_none">
                            <table class="_abprf">
                                <tbody class="hidden_content">
								<?php self::form_brand(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function save_brand() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$ids          = isset( $_POST['brand_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['brand_id'] ) ) : [];
					$names        = isset( $_POST['brand_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['brand_name'] ) ) : [];
					$icons        = isset( $_POST['brand_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['brand_icon'] ) ) : [];
					$slugs        = isset( $_POST['brand_slug'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['brand_slug'] ) ) : [];
					$descriptions = isset( $_POST['brand_description'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['brand_description'] ) ) : [];
					$property_add = isset( $_POST['property_add'] ) ? sanitize_text_field( wp_unslash( $_POST['property_add'] ) ) : 0;
					$options      = [];
					if ( ! empty( $names ) && sizeof( $names ) > 0 ) {
						foreach ( $names as $key => $name ) {
							if ( ! empty( $name ) ) {
								$id          = array_key_exists( $key, $ids ) && ! empty( $ids[ $key ] ) ? $ids[ $key ] : '';
								$slug        = array_key_exists( $key, $slugs ) && ! empty( $slugs[ $key ] ) ? $slugs[ $key ] : '';
								$description = array_key_exists( $key, $descriptions ) && ! empty( $descriptions[ $key ] ) ? $descriptions[ $key ] : '';
								if ( ! empty( $id ) ) {
									$result = wp_update_term( $id, 'abprf_brand', array(
										'name' => $name,
										'slug' => $slug,
										'description' => $description,
									) );
								} else {
									$result = wp_insert_term(
										$name,
										'abprf_brand',
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
										$options[ $id ] = array_key_exists( $key, $icons ) && ! empty( $icons[ $key ] ) ? $icons[ $key ] : '';
									}
								}
							}
						}
						$this->update_brand( $options );
						if ( empty( $msg ) ) {
							$msg = esc_html__( 'Brand Saved Successfully !', 'abprf-rental-forge' );
						}
					} else {
						$msg = esc_html__( 'Brand not Saved ! Brand Name can not Blank !...!', 'abprf-rental-forge' );
					}
					ob_start();
					if ( ! empty( $property_add ) && $property_add > 0 ) {
						$brands      = '';
						$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
						if ( ! empty( $property_id ) ) {
							$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
							if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
								$property = current( $properties );
								$brands   = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
							}
						}
						self::brand_selection( $brands );
					} else {
						$this->brand_list();
					}
					$html = ob_get_clean();
				} else {
					$html = '';
					$msg  = esc_html__( 'Brand not Saved ! Authentication Error .', 'abprf-rental-forge' );
				}
				wp_send_json_success( [ 'html' => $html, 'msg' => $msg ] );
				wp_die();
			}

			public function delete_brand() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$brand_id = isset( $_POST['brand_id'] ) ? sanitize_text_field( wp_unslash( $_POST['brand_id'] ) ) : '';
					$result   = wp_delete_term( $brand_id, 'abprf_brand' );
					if ( ! is_wp_error( $result ) && ! empty( $brand_id ) ) {
						$brands = ABPRF_Function::get_option( 'abprf_brand' );
						unset( $brands[ $brand_id ] );
						update_option( 'abprf_brand', $brands );
					}
					ob_start();
					$this->brand_list();
					$html = ob_get_clean();
					if ( is_wp_error( $result ) ) {
						wp_send_json_success( [ 'html' => $html, 'msg' => $result->get_error_message() ] );
					} else {
						wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Brand Delete Successfully !', 'abprf-rental-forge' ) ] );
					}
				}
				wp_die();
			}

			public function edit_brand() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
					$brands = ABPRF_Function::get_option( 'abprf_brand' );
					$brand  = array_key_exists( $id, $brands ) ? $brands[ $id ] : [];
					ob_start();
					self::form_brand( $brand, $id );
					$html = ob_get_clean();
					wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Feature Ready to Editing...... !', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function update_brand( $options = [] ): void {
				$taxonomies = ABPRF_Function::get_taxonomy( 'abprf_brand' );
				$brands     = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
					$brand = ABPRF_Function::get_option( 'abprf_brand' );
					$brand = is_array( $brand ) ? $brand : [];
					foreach ( $taxonomies as $taxonomy ) {
						$term_id = $taxonomy->term_id;
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
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abprf-rental-forge' ) ?></th>
                            <th class="_min_200"><?php esc_html_e( 'Brand Title', 'abprf-rental-forge' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abprf-rental-forge' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Description', 'abprf-rental-forge' ) ?></th>
                            <th class="_w_250"><?php esc_html_e( 'Shortcode Property', 'abprf-rental-forge' ) ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Action', 'abprf-rental-forge' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $abprf_brands as $term_id => $brand ) {
							$name        = is_array( $brand ) && array_key_exists( 'name', $brand ) ? $brand['name'] : '';
							$description = is_array( $brand ) && array_key_exists( 'description', $brand ) ? $brand['description'] : '';
							$icon        = is_array( $brand ) && array_key_exists( 'icon', $brand ) ? $brand['icon'] : '';
							?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" target="_blank" class="_abprf_fs_h5 _color_theme"><?php ABPRF_Layout::image_icon( $icon ); ?><?php echo esc_html( $name ); ?></a></th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <td><?php echo esc_html( $description ); ?></td>
                                <th><code> [abprf-property brand_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th>
                                    <div class="_f_wrap">
                                        <button type="button" class="_btn_light_yellow_mar_r_xxs edit_brand" data-id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Edit : ', 'abprf-rental-forge' ) . ' ' . esc_attr( $name ); ?>">✍️</button>
                                        <button type="button" class="_btn_light_danger_xxs delete_brand" data-id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abprf-rental-forge' ) . ' ' . esc_attr( $name ); ?>">❌</button>
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
                            <option value="" selected><?php esc_html_e( 'Please Select Brand', 'abprf-rental-forge' ); ?></option>
							<?php foreach ( $brands as $key => $brand ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( in_array( $key, $brand_array ) ? 'selected' : '' ); ?>><?php echo esc_html( is_array( $brand ) && array_key_exists( 'name', $brand ) ? $brand['name'] : '' ); ?></option>
							<?php } ?>
                        </select>
                    </label>
				<?php } else { ?>
                    <p class="_abprf"><?php echo esc_html( ABPRF_Layout::array_info( 'no_brand' ) ); ?></p>
				<?php }
			}

			public static function form_brand( $brand = [], $id = '' ): void {
				$name        = is_array( $brand ) && array_key_exists( 'name', $brand ) ? $brand['name'] : '';
				$slug        = is_array( $brand ) && array_key_exists( 'slug', $brand ) ? $brand['slug'] : '';
				$icon        = is_array( $brand ) && array_key_exists( 'icon', $brand ) ? $brand['icon'] : '';
				$description = is_array( $brand ) && array_key_exists( 'description', $brand ) ? $brand['description'] : '';
				?>
                <tr class="delete_area">
                    <th><?php do_action( 'abprf_add_icon', 'brand_icon[]', $icon ); ?></th>
                    <th>
                        <label>
                            <input type="hidden" name="brand_id[]" value="<?php echo esc_attr( $id ); ?>"/>
                            <input class="_form_control_min_auto_w_full" name="brand_name[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abprf-rental-forge' ); ?>" required/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input class="_form_control_min_auto_w_full" name="brand_slug[]" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abprf-rental-forge' ); ?>"/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <textarea class="_form_control_min_auto_w_full" name="brand_description[]" placeholder="<?php esc_attr_e( 'Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                    </th>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}

			public static function brand_selection_form( $brand = [], $id = '' ) {
				$name        = is_array( $brand ) && array_key_exists( 'name', $brand ) ? $brand['name'] : '';
				$slug        = is_array( $brand ) && array_key_exists( 'slug', $brand ) ? $brand['slug'] : '';
				$icon        = is_array( $brand ) && array_key_exists( 'icon', $brand ) ? $brand['icon'] : '';
				$description = is_array( $brand ) && array_key_exists( 'description', $brand ) ? $brand['description'] : '';
				?>
                <div class="configuration_content">
                    <div class="form_area">
                        <div class="hide_on_load">
                            <table class="_abprf ">
                                <tbody class="insertable_area sortable_area">
                                </tbody>
                            </table>
                            <div class="_divider_xs"></div>
                        </div>
                        <div class="_fj_between">
							<?php ABPRF_Layout::button_add_xs( __( 'Add New Brand', 'abprf-rental-forge' ) ); ?>
                            <button type="button" class="_btn_theme_xs hide_on_load save_brand"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Brand', 'abprf-rental-forge' ); ?></button>
                        </div>
                    </div>
                    <div class="abprf_d_none">
                        <table class="_abprf">
                            <tbody class="hidden_content">
                            <tr class="delete_area">
                                <th><?php do_action( 'abprf_add_icon', 'brand_icon[]', $icon ); ?></th>
                                <th>
                                    <label>
                                        <input type="hidden" name="brand_id[]" value="<?php echo esc_attr( $id ); ?>"/>
                                        <input class="_form_control_min_auto_w_full" name="brand_name[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abprf-rental-forge' ); ?>" required/>
                                    </label>
                                </th>
                                <th>
                                    <label>
                                        <input class="_form_control_min_auto_w_full" name="brand_slug[]" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abprf-rental-forge' ); ?>"/>
                                    </label>
                                </th>
                                <th>
                                    <label>
                                        <textarea class="_form_control_min_auto_w_full" name="brand_description[]" placeholder="<?php esc_attr_e( 'Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                                    </label>
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}
		}
		new ABPRF_Brand();
	}