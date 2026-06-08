<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPRF_Status' ) ) {
		class ABPRF_Status {
			public function __construct() {
				add_action( 'abprf_load_status', array( $this, 'load_status' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_install_and_active_wc', array( $this, 'install_and_active_wc' ) );
				add_action( 'wp_ajax_abprf_active_wc', array( $this, 'active_wc' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_create_post_list_page', array( $this, 'create_post_list_page' ) );
				add_action( 'wp_ajax_abprf_create_property_list_page', array( $this, 'create_property_list_page' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_import_dummy', array( $this, 'import_dummy' ) );
			}

			public function load_status( $abprf_info ): void {
				?>
                <div class="_abp_panel_max_1200_mar_auto abprf_status">
                    <div class="_panel_head">
                        <h3 class="_abprf"><span class="_mar_r_xxs">🛡️</span> <?php esc_html_e( 'Status  & Information', 'abp-rentalforge' ); ?></h3>
                    </div>
                    <div class="_panel_body">
						<?php
							if ( ABPRF_WC < 2 ) {
								ABPRF_Layout::layout_warning_info_xs( 'must_wc' );
								if ( ABPRF_WC == 1 ) { ?>
                                    <button class="_btn_navy_blue_mar_t active_wc" type="button"><span class="fas fa-tasks _mar_r_xxs"></span><?php esc_html_e( 'Active Now', 'abp-rentalforge' ); ?></button>
								<?php } else { ?>
                                    <button class="_btn_navy_blue_mar_t _mar_t install_and_active_wc" type="button"><span class="fas fa-file-download _mar_r_xxs"></span><?php esc_html_e( 'Install & Active Now', 'abp-rentalforge' ); ?></button>
								<?php }
							}
							$this->version();
							$this->wordpress();
							$this->php();
							$this->wc();
							if ( ABPRF_WC > 1 ) {
								do_action( 'abprf_add_tools' );
								$this->post_page( $abprf_info );
							}
						?>
                    </div>
                </div>
				<?php
			}

			public function version(): void {
				?>
                <div class="_section_xs_mar_t_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'RentalForge Version', 'abp-rentalforge' ) ?> </h6>
                        <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( get_plugin_data( ABPRF_PLUGIN_FILE ) ['Version'] ); ?></button>
                    </div>
                </div>
				<?php
			}

			public function wordpress(): void {
				$version = get_bloginfo( 'version' );
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'WordPress Version', 'abp-rentalforge' ); ?> </h6>
						<?php if ( $version > 5.5 ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( $version ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html( $version ); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}

			public function php(): void {
				$version = phpversion();
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'Php Version', 'abp-rentalforge' ); ?> </h6>
						<?php if ( $version > 7.4 ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( $version ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html( $version ); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}

			public function wc(): void {
				$title = ABPRF_WC == 2 ? __( 'Woocommerce Plugin', 'abp-rentalforge' ) : __( 'Woocommerce need to install and active', 'abp-rentalforge' );
				$title = ABPRF_WC == 1 ? __( 'Woocommerce already installed but  not  activated', 'abp-rentalforge' ) : $title;
				$name  = get_option( 'woocommerce_email_from_name' );
				$email = get_option( 'woocommerce_email_from_address' );
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php echo esc_html( $title ); ?></h6>
						<?php if ( ABPRF_WC == 2 ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e( 'Activated', 'abp-rentalforge' ); ?></button>
						<?php } elseif ( ABPRF_WC == 1 ) { ?>
                            <button class="_btn_warning_xs_min_125 active_wc" type="button"><span class="fas fa-tasks _mar_r_xxs"></span><?php esc_html_e( 'Active Now', 'abp-rentalforge' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 install_and_active_wc" type="button"><span class="fas fa-file-download _mar_r_xxs"></span><?php esc_html_e( 'Install & Active Now', 'abp-rentalforge' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php if ( ABPRF_WC == 2 && defined( 'WC_VERSION' ) ) { ?>
                        <div class="_fa_center_fj_between">
                            <h6 class="_abprf"><?php esc_html_e( 'Woocommerce Version', 'abp-rentalforge' ); ?></h6>
							<?php if ( version_compare( WC_VERSION, '8.0', '>' ) ) { ?>
                                <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( WC_VERSION ); ?></button>
							<?php } else { ?>
                                <button class="_btn_light_warning_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html( WC_VERSION ); ?></button>
							<?php } ?>
                        </div>
						<?php if ( ! empty( $name ) ) { ?>
                            <div class="_divider_xs"></div>
                            <div class="_fa_center_fj_between">
                                <h6 class="_abprf"><?php esc_html_e( 'Name', 'abp-rentalforge' ); ?></h6>
                                <button class="_btn_light_success_xs_min_125" type="button"><?php echo esc_html( $name ); ?></button>
                            </div>
						<?php } ?>
						<?php if ( ! empty( $email ) ) { ?>
                            <div class="_divider_xs"></div>
                            <div class="_fa_center_fj_between">
                                <h6 class="_abprf"><?php esc_html_e( 'Email Address', 'abp-rentalforge' ); ?></h6>
                                <button class="_btn_light_success_xs_min_125_text_inherit" type="button"><?php echo esc_html( $email ); ?></button>
                            </div>
						<?php } ?>
					<?php } else { ?>
                        <div class="_color_warning"><span class=" _abprf_mar_r_xxs  fas fa-exclamation-triangle"></span><?php echo esc_html( ABPRF_Layout::array_info( 'must_wc' ) ); ?></div>
					<?php } ?>
                </div>
				<?php
			}

			public function post_page( $abprf_info ): void {
				$label          = ABPRF_Function::label();
				$total          = sizeof( isset( $abprf_info['post_ids'] ) && $abprf_info['post_ids'] ? $abprf_info['post_ids'] : ABPRF_Query::get_post_id() );
				$total_property = isset( $abprf_info['total_property'] ) && $abprf_info['total_property'] ? $abprf_info['total_property'] : 0;
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Post List Page', 'abp-rentalforge' ); ?></h6>
						<?php if ( ABPRF_Function::get_page_by_slug( 'rf_post_list' ) ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e( 'Activated', 'abp-rentalforge' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 create_post_list_page" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e( 'Add RentalForge List Page', 'abp-rentalforge' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"><?php esc_html_e( 'Property List Page', 'abp-rentalforge' ); ?></h6>
						<?php if ( ABPRF_Function::get_page_by_slug( 'rf_property_list' ) ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e( 'Activated', 'abp-rentalforge' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 create_property_list_page" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e( 'Add Property List Page', 'abp-rentalforge' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'Number of Post', 'abp-rentalforge' ); ?> </h6>
						<?php if ( $total > 0 ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( $total ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php esc_html_e( 'Can Not Find Post', 'abp-rentalforge' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'Number of Property', 'abp-rentalforge' ); ?> </h6>
						<?php if ( $total_property > 0 ) { ?>
                            <button class="_btn_light_success_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html( $total_property ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php esc_html_e( 'Can Not Find Any Property', 'abp-rentalforge' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e( 'Dummy Import', 'abp-rentalforge' ); ?> </h6>
                        <button class="<?php echo esc_attr( $total > 0 ? '_btn_light_success_xs' : '_btn_warning_xs' ); ?>_btn_theme_min_125 import_dummy" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e( 'Add New Dummy Post', 'abp-rentalforge' ); ?></button>
                    </div>
                </div>
				<?php
			}

			//=============================//
			public function install_and_active_wc(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
				include_once( ABSPATH . 'wp-admin/includes/file.php' );
				include_once( ABSPATH . 'wp-admin/includes/misc.php' );
				include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
				$plugin = 'woocommerce';
				$api    = plugins_api( 'plugin_information', array(
					'slug' => $plugin,
					'fields' => array(
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					),
				) );
				if ( is_wp_error( $api ) ) {
					wp_send_json_error( ['html'=>'', 'msg' => $api->get_error_message() ] );
				}
				$title              = 'title';
				$url                = 'url';
				$nonce              = 'nonce';
				$woocommerce_plugin = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
				$installed          = $woocommerce_plugin->install( $api->download_link );
				if ( is_wp_error( $installed ) ) {
					wp_send_json_error( [ 'msg' => $installed->get_error_message() ] );
				}
				$activated = activate_plugin( 'woocommerce/woocommerce.php' );
				if ( is_wp_error( $activated ) ) {
					wp_send_json_error( [ 'msg' => $activated->get_error_message() ] );
				}
				wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce installed and activated successfully!', 'abp-rentalforge' ) ] );
			}

			public function active_wc(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				if ( defined( 'ABPRF_WC' ) && ABPRF_WC == 1 ) {
					$activated = activate_plugin( 'woocommerce/woocommerce.php' );
					if ( is_wp_error( $activated ) ) {
						wp_send_json_error( [ 'msg' => $activated->get_error_message() ] );
					}
					wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce activated successfully!', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( [ 'msg' => esc_html__( 'WooCommerce is either not installed or already active.', 'abp-rentalforge' ) ] );
			}

			public function create_post_list_page(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				if ( ! ABPRF_Function::get_page_by_slug( 'rf_post_list' ) ) {
					$label   = ABPRF_Function::label();
					$page    = array(
						'post_type' => 'page',
						'post_name' => 'rf_post_list',
						'post_title' => $label . ' ' . __( 'List', 'abp-rentalforge' ),
						'post_content' => '[abprf-post]',
						'post_status' => 'publish',
					);
					$post_id = wp_insert_post( $page );
					if ( is_wp_error( $post_id ) || 0 === $post_id ) {
						wp_send_json_error( [ 'msg' => esc_html__( 'Failed to create page.', 'abp-rentalforge' ) ] );
					}
					flush_rewrite_rules();
					/* translators: %s: Custom rental item type label (e.g., Vehicle, Equipment, Property) */
					$translated_format = esc_html__( '%s Page Created successfully.....', 'abp-rentalforge' );
					$msg               = sprintf( $translated_format, $label );
					wp_send_json_success( [ 'msg' => $msg ] );
				}
				wp_send_json_error( [ 'msg' => esc_html__( 'Page already exists.', 'abp-rentalforge' ) ] );
			}

			public function create_property_list_page(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				if ( ! ABPRF_Function::get_page_by_slug( 'rf_property_list' ) ) {
					$label   = ABPRF_Function::label();
					$page    = array(
						'post_type' => 'page',
						'post_name' => 'rf_property_list',
						'post_title' => $label . ' ' . __( 'Property List', 'abp-rentalforge' ),
						'post_content' => '[abprf-property]',
						'post_status' => 'publish',
					);
					$post_id = wp_insert_post( $page );
					if ( is_wp_error( $post_id ) || 0 === $post_id ) {
						wp_send_json_error( [ 'msg' => esc_html__( 'Failed to create property page.', 'abp-rentalforge' ) ] );
					}
					flush_rewrite_rules();
					/* translators: %s: Custom rental item type label (e.g., Vehicle, Equipment, Property) */
					$translated_format = esc_html__( '%s Property Page Created successfully.....', 'abp-rentalforge' );
					$msg = sprintf( $translated_format, $label );
					wp_send_json_success( [ 'msg' => $msg ] );
				}
				wp_send_json_error( [ 'msg' => esc_html__( 'Property page already exists.', 'abp-rentalforge' ) ] );
			}

			public function import_dummy(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
				try {
					$this->add_data( $category );
					flush_rewrite_rules();
					wp_send_json_success( [
						'msg' => esc_html__( 'Dummy data imported successfully!', 'abp-rentalforge' )
					] );
				} catch ( Exception $e ) {
					wp_send_json_error( [
						'msg' => esc_html__( 'An error occurred during data import.', 'abp-rentalforge' )
					] );
				}
			}

			public static function add_data( $_category ): void {
				global $wpdb;
				$table_name  = $wpdb->prefix . 'abprf_property';
				$dummy_infos = self::dummy_data();
				if ( array_key_exists( 'taxonomy', $dummy_infos ) ) {
					foreach ( $dummy_infos['taxonomy'] as $tax => $taxonomy_option ) {
						if ( taxonomy_exists( $tax ) ) {
							$check_terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
							if ( is_string( $check_terms ) || sizeof( $check_terms ) == 0 ) {
								foreach ( $taxonomy_option as $taxonomy_data ) {
									unset( $term );
									$term = wp_insert_term( $taxonomy_data['name'], $tax );
								}
							}
						}
					}
					do_action( 'abprf_category_update' );
					do_action( 'abprf_location_update' );
					do_action( 'abprf_brand_update' );
				}
				if ( array_key_exists( 'options', $dummy_infos ) ) {
					foreach ( $dummy_infos['options'] as $option => $dummy_option ) {
						$option_data = get_option( $option );
						if ( ! $option_data || sizeof( $option_data ) == 0 ) {
							update_option( $option, $dummy_option );
						}
					}
				}
				if ( array_key_exists( 'custom_post', $dummy_infos ) ) {
					$abprf_location = ABPRF_Function::get_option( 'abprf_location' );
					$abprf_brand    = ABPRF_Function::get_option( 'abprf_brand' );
					$abprf_category = ABPRF_Function::get_option( 'abprf_category' );
					$_category      = ! empty( $_category ) ? explode( ',', $_category ) : [ 'transport' ];
					foreach ( $dummy_infos['custom_post'] as $custom_post => $dummy_post ) {
						foreach ( $dummy_post as $cat_key => $cat_data ) {
							if ( in_array( $cat_key, $_category ) ) {
								foreach ( $cat_data as $dummy_data ) {
									$args = array();
									if ( isset( $dummy_data['name'] ) ) {
										$args['post_title'] = $dummy_data['name'];
									}
									$args['post_status'] = 'publish';
									$args['post_type']   = $custom_post;
									$post_id             = wp_insert_post( $args );
									$post_data           = $dummy_data['post_data'] ?? [];
									if ( ! empty( $post_data ) ) {
										$common_data = $post_data['common_data'] ?? [];
										foreach ( $common_data as $meta_key => $data ) {
											update_post_meta( $post_id, $meta_key, $data );
										}
										$template = $post_data['abprf_template'] ?? 'grid';
										update_post_meta( $post_id, 'abprf_template', $template );
										$rent_rule = $post_data['rent_rule'] ?? 'hourly';
										update_post_meta( $post_id, 'rent_rule', $rent_rule );
										$post_cat = sizeof( $abprf_category ) > 0 ? array_key_first( $abprf_category ) : '';
										update_post_meta( $post_id, 'abprf_category', $post_cat );
										$post_loc = '';
										if ( sizeof( $abprf_location ) > 3 ) {
											$post_loc_key = array_rand( $abprf_location, 3 );
											$post_loc     = implode( ',', $post_loc_key );
										}
										update_post_meta( $post_id, 'abprf_location', $post_loc );
										$properties = $post_data['property'] ?? [];
										if ( ! empty( $properties ) ) {
											foreach ( $properties as $property ) {
												$post_brand = sizeof( $abprf_brand ) > 0 ? array_rand( $abprf_brand ) : '';
												$data       = [
													'post_id' => intval( $post_id ),
													'rent_continue' => 'on',
													'name' => sanitize_text_field( $property['name'] ?? '' ),
													'brand' => sanitize_text_field( $post_brand ),
													'category' => sanitize_text_field( $post_cat ),
													'location' => sanitize_text_field( $post_loc ),
													'features' => sanitize_text_field( $property['features'] ?? '' ),
													'rent_rule' => sanitize_text_field( $rent_rule ),
													'price_qty_info' => json_encode( $property['price_qty_info'] ?? [] ),
													'gallery' => sanitize_text_field( $property['gallery'] ?? '' ),
													'status' => ! empty( $post_id ) ? get_post_status( $post_id ) : '',
													'others' => json_encode( $property['others'] ?? [] ),
													'updated_at' => current_time( 'Y-m-d H:i' )
												];
												// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
												$wpdb->insert( $table_name, $data );
											}
										}
										ABPRF_Function::update_dates( $post_id );
										ABPRF_Function::update_time_slot( $post_id );
										ABPRF_Function::update_global_data( $post_id );
									}
								}
							}
						}
					}
				}
			}

			public static function dummy_data(): array {
				return [
					'taxonomy' => [
						'abprf_category' => [
							'transport' => [ 'name' => 'Transport' ],
							'camera' => [ 'name' => 'Camera' ],
							'party' => [ 'name' => 'Party Equipment' ],
							'drone' => [ 'name' => 'Drone' ],
							'resort' => [ 'name' => 'Resort' ],
							'travel' => [ 'name' => 'Travel Equipment' ],
							'sport' => [ 'name' => 'Sport Equipment' ],
						],
						'abprf_location' => [
							0 => [ 'name' => 'Washington, D.C' ],
							1 => [ 'name' => 'New York City' ],
							2 => [ 'name' => 'California' ],
							3 => [ 'name' => 'Los Angeles' ],
							4 => [ 'name' => 'Chicago' ],
							5 => [ 'name' => 'San Francisco' ],
						],
						'abprf_brand' => [
							0 => [ 'name' => 'Mercedes-Benz' ],
							1 => [ 'name' => 'Toyota Motor' ],
							2 => [ 'name' => 'Tesla' ],
							3 => [ 'name' => 'BMW' ],
							4 => [ 'name' => 'Canon' ],
							5 => [ 'name' => 'Nikon' ],
							6 => [ 'name' => 'Sony' ],
							7 => [ 'name' => 'Fujifilm' ],
							8 => [ 'name' => 'Pioneer DJ' ],
							9 => [ 'name' => 'Yamaha' ],
							10 => [ 'name' => 'DJI' ],
							11 => [ 'name' => 'Autel Robotics' ],
							12 => [ 'name' => 'Marriott International' ],
							13 => [ 'name' => 'Hilton Worldwide' ],
							14 => [ 'name' => 'American Tourister' ],
							15 => [ 'name' => 'Samsonite International' ],
							16 => [ 'name' => 'Nike' ],
							17 => [ 'name' => 'Adidas' ],
							18 => [ 'name' => 'Puma' ],
						],
					],
					'options' => [
						'abprf_feature' => ABPRF_Layout::static_feature(),
						'abprf_additional' => ABPRF_Layout::static_additional(),
						'abprf_forms' => ABPRF_Layout::static_form(),
					],
					'custom_post' => [
						'abprf_post' => [
							'transport' => [
								0 => [
									'name' => 'Motor Bike',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'hourly',
										'property' => [
											0 => [
												'name' => 'Ducati Panigale V4',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🏍️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 2, 'max' => 5 ],
													'daily' => [ 'qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											1 => [
												'name' => 'Yamaha YZF-R1',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => 'fas fa-motorcycle' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											2 => [
												'name' => 'BMW S1000RR',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🏍️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 5, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											3 => [
												'name' => 'Kawasaki Ninja H2',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => 'fas fa-motorcycle' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 20, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											4 => [
												'name' => 'Honda CBR1000RR-R Fireblade',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🏍️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 8, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 25, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 3, 'min' => 1, 'max' => 10 ],
												]
											]
										]
									]
								],
								1 => [
									'name' => 'Bicycle',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'BMC',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚴‍♂️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 2, 'max' => 5 ],
													'daily' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											1 => [
												'name' => 'Trek',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚲' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											2 => [
												'name' => 'Giant',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚴‍♂️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 5, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											3 => [
												'name' => 'Cannondale',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚲' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 20, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10 ],
												]
											],
											4 => [
												'name' => 'Specialized Bicycle',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚴‍♂️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 18, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 25, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 3, 'min' => 1, 'max' => 10 ],
												]
											]
										]
									]
								],
								2 => [
									'name' => 'Car',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'multi_day',
										'property' => [
											0 => [
												'name' => 'Bugatti Chiron',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚗️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 2, 'max' => 5 ],
													'daily' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 100, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 100, 'price_multi' => 15, 'min' => 1, 'max' => 10 ],
												]
											],
											1 => [
												'name' => 'Ferrari SF90 Stradale',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => 'fas fa-car' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 15, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 120, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 130, 'price_multi' => 20, 'min' => 1, 'max' => 10 ],
												]
											],
											2 => [
												'name' => 'Rolls-Royce Phantom',
												'features' => 'fec_id_2,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚗️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'price_multi' => 25, 'min' => 1, 'max' => 10 ],
												]
											],
											3 => [
												'name' => 'Tesla Model S Plaid',
												'features' => 'fec_id_1,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => 'fas fa-car' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 16, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'price_multi' => 20, 'min' => 1, 'max' => 10 ],
												]
											],
											4 => [
												'name' => 'Lamborghini Revuelto',
												'features' => 'fec_id_1,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => [ 'icon' => '🚗️' ],
												'price_qty_info' => [
													'hourly' => [ 'qty' => 18, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 17, 'min' => 2, 'max' => 10 ],
													'daily' => [ 'qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 250, 'min' => 1, 'max' => 10 ],
													'multi_day' => [ 'qty' => 16, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 150, 'price_multi' => 30, 'min' => 1, 'max' => 10 ],
												]
											]
										]
									]
								],
							],
						]
					]
				];
			}

			public static function post_data(): array {
				return [
					'rent_continue' => 'on',
					'display_sku' => 'on',
					'post_sku' => wp_rand( 100, 999 ),
					'display_category' => 'on',
					'display_location' => 'on',
					'day_time_start' => '11:00',
					'day_time_end' => '10:00',
					'hour_threshold' => '20',
					'cut_off_date' => '10',
					'day_threshold' => '25',
					'active_global_dates' => 'on',
					'display_additional_services' => 'on',
					'active_global_additional' => 'on',
					'display_client_form' => 'on',
					'active_global_form' => 'on',
					'display_faq' => 'on',
					'active_global_faq' => 'on',
					'display_tc' => 'on',
					'active_global_tc' => 'on',
					'dummy' => 'on',
				];
			}
		}
		new ABPRF_Status();
	}