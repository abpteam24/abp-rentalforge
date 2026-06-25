<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Category' ) ) {
		class ABPRF_Category {
			public function __construct() {
				add_action( 'abprf_global_category', array( $this, 'global_category' ) );
				add_action( 'abprf_category_update', array( $this, 'update_category' ) );
				add_action( 'wp_ajax_abprf_save_category', array( $this, 'save_category' ) );
				add_action( 'wp_ajax_abprf_delete_category', array( $this, 'delete_category' ) );
				add_action( 'wp_ajax_abprf_add_category', array( $this, 'add_category' ) );
			}
			public function global_category(): void {
				if ( ABPRF_Function::on_off( 'category' ) ) {
					$category_label = ABPRF_Function::category_label();
					?>
                    <div class="category_list _ov_auto">
						<?php $this->category_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <button type="button" class="_btn_default" data-target-popup="#abprf_global_popup" data-type="category"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abp-rentalforge' ) . ' ' . esc_html( $category_label ); ?></button>
					<?php
				}
			}
			public function update_category(): void {
				$taxonomies = ABPRF_Function::get_taxonomy( 'abprf_category' );
				$category   = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$category[ $taxonomy->term_id ]['name']        = $taxonomy->name;
						$category[ $taxonomy->term_id ]['description'] = $taxonomy->description;
					}
				}
				ksort( $category );
				update_option( 'abprf_category', $category );
			}
			public function category_list(): void {
				$all_categories = ABPRF_Function::get_option( 'abprf_category' );
				$count          = 1;
				if ( ! empty( $all_categories ) && is_array( $all_categories ) && sizeof( $all_categories ) > 0 ) { ?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abp-rentalforge' ) ?></th>
                            <th class="_min_200"><?php esc_html_e( 'Category Title', 'abp-rentalforge' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abp-rentalforge' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Description', 'abp-rentalforge' ) ?></th>
                            <th class="_w_250"><?php esc_html_e( 'Shortcode Post', 'abp-rentalforge' ) ?></th>
                            <th class="_w_250"><?php esc_html_e( 'Shortcode Property', 'abp-rentalforge' ) ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Action', 'abp-rentalforge' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $all_categories as $term_id => $category ) {
							$name        = $category['name'] ?? '';
							$description = $category['description'] ?? '';
							?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" target="_blank" class="_abprf_fs_h5 _color_theme"><?php echo esc_html( $name ); ?></a></th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <td><?php echo esc_html( $description ); ?></td>
                                <th><code> [abprf-post cat_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th><code> [abprf-property cat_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th>
                                    <div class="_f_wrap">
                                        <button type="button" class="_btn_light_yellow_mar_r_xxs" data-id="<?php echo esc_attr( $term_id ); ?>" data-target-popup="#abprf_global_popup" data-type="category" title="<?php echo esc_attr__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">✍️</button>
                                        <button type="button" class="_btn_light_danger_xxs delete_category" data-cat_id="<?php echo esc_attr( $term_id ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abp-rentalforge' ) . ' ' . esc_attr( $name ); ?>">❌</button>
                                    </div>
                                </th>
                            </tr>
							<?php $count ++;
						} ?>
                        </tbody>
                    </table>
				<?php } else {
					ABPRF_Layout::layout_warning_info( 'no_category' );
				}
			}
			public function form( $term_id = '' ): void {
				$name           = $slug = $des = '';
				$category_label = ABPRF_Function::category_label();
				if ( ! empty( $term_id ) ) {
					$term = get_term( $term_id );
					if ( ! empty( $term ) ) {
						$name = $term->name;
						$slug = $term->slug;
						$des  = $term->description;
					}
				}
				?>
                <input type="hidden" name="cat_term_id" value="<?php echo esc_attr( $term_id ); ?>"/>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Name', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-rentalforge' ); ?>" required/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'cat_name' ); ?>
                </div>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Slug (Optional)', 'abp-rentalforge' ); ?></span>
                        <input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-rentalforge' ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'cat_slug' ); ?>
                </div>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span class="_abp_label"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Description', 'abp-rentalforge' ); ?></span>
                        <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $des ); ?></textarea>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'cat_des' ); ?>
                </div>
                <div class="_divider_xs"></div>
                <button type="button" class="_btn_theme save_category"><span class="_mar_r_xxs">💾</span><?php echo ( ! empty( $term_id ) ? esc_html__( 'Update', 'abp-rentalforge' ) : esc_html__( 'Save', 'abp-rentalforge' ) ) . ' ' . esc_html( $category_label ); ?></button>
				<?php
			}
			public static function category_selection( $_category = '' ): void {
				$all_categories = ABPRF_Function::get_option( 'abprf_category' );
				if ( ! empty( $all_categories ) && is_array( $all_categories ) && sizeof( $all_categories ) > 0 ) { ?>
                    <div class="custom_radio _fj_end">
                        <input type="hidden" name="abprf_category" value="<?php echo esc_attr( $_category ); ?>"/>
						<?php foreach ( $all_categories as $key => $category ) {
							$name = $category['name'] ?? ''; ?>
                            <div class="radio_item">
                                <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $_category == $key ? 'abp_active' : '' ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                    <span data-icon class="_mar_r_xs <?php echo esc_attr( $_category == $key ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><span class="_text_left_fs_label"><?php echo esc_html( $name ); ?></span>
                                </button>
                            </div>
						<?php } ?>
                        <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="category"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abp-rentalforge' ) . ' ' . esc_html( ABPRF_Function::category_label() ); ?></button>
                    </div>
				<?php } else { ?>
                    <p><?php echo esc_html( ABPRF_Layout::array_info( 'no_category' ) ); ?></p>
                    <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="category"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abp-rentalforge' ) . ' ' . esc_html( ABPRF_Function::category_label() ); ?></button>
					<?php
				}
			}
			public function save_category(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_int      = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_textarea = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_slug     = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_title( wp_unslash( $_POST[ $key ] ) ) : $default;
				$cat_term_id   = $post_int( 'cat_term_id' );
				$name          = $post_val( 'name' );
				$slug          = $post_slug( 'slug' );
				$description   = $post_textarea( 'description' );
				$abprf_post_id = $post_int( 'abprf_post_id' );
				if ( empty( $name ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Category Name cannot be blank!', 'abp-rentalforge' ) ], 400 );
				}
				if ( $cat_term_id > 0 ) {
					$result = wp_update_term( $cat_term_id, 'abprf_category', [
						'name' => $name,
						'slug' => $slug,
						'description' => $description,
					] );
				} else {
					$result = wp_insert_term( $name, 'abprf_category', [
						'slug' => $slug,
						'description' => $description,
					] );
				}
				$this->update_category();
				ob_start();
				if ( $abprf_post_id > 0 ) {
					$_category = ABPRF_Function::get_post_info( $abprf_post_id, 'abprf_category' );
					self::category_selection( $_category );
				} else {
					$this->category_list();
				}
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message() ], 400 );
				}
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Category Saved Successfully !', 'abp-rentalforge' ) ] );
			}
			public function delete_category(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$cat_id = isset( $_POST['cat_id'] ) ? absint( wp_unslash( $_POST['cat_id'] ) ) : 0;
				if ( ! $cat_id ) {
					wp_send_json_error( __( 'Invalid Category ID.', 'abp-rentalforge' ), 400 );
				}
				$result = wp_delete_term( $cat_id, 'abprf_category' );
				$this->update_category();
				ob_start();
				$this->category_list();
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message() ], 400 );
				}
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$all_ids    = ABPRF_Query::get_post_id( [ 'cat_id' => $cat_id ] );
				if ( count( $all_ids ) > 0 ) {
					foreach ( $all_ids as $id ) {
						$id       = absint( $id );
						$category = ABPRF_Function::get_post_info( $id, 'category' );
						$category = ! empty( $category ) ? explode( ',', $category ) : [];
						if ( ! empty( $category ) && in_array( (string) $cat_id, $category, true ) ) {
							$category = array_diff( $category, [ (string) $cat_id ] );
							$category = ! empty( $category ) ? implode( ',', $category ) : '';
							update_post_meta( $id, 'category', $category );
							$data  = [ 'category' => $category ];
							$where = [ 'post_id' => $id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s' ], [ '%d' ] );
						}
					}
				}
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Category Delete Successfully !', 'abp-rentalforge' ) ] );
			}
			public function add_category(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$cat_id = isset( $_POST['tax_id'] ) ? absint( wp_unslash( $_POST['tax_id'] ) ) : 0;
				ob_start();
				$this->form( $cat_id );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => __( 'Category Form Loaded Successfully .....! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Category();
	}