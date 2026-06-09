<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_ADMIN' ) ) {
		class ABPRF_ADMIN {
			public function __construct() {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'abprf_load_global', array( $this, 'load_global' ) );
			}

			public function admin_menu(): void {
				$label = ABPRF_Function::label();
				$slug  = ABPRF_Function::slug();
				$icon  = ABPRF_Function::icon_wp();
				add_menu_page( $label, $label, 'manage_options', $slug, array( $this, 'load_main_page' ), $icon, 50 );
			}

			public function load_main_page(): void {
				remove_all_actions( 'user_admin_notices' );
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
				remove_all_actions( 'network_admin_notices' );
				add_filter( 'wp_dependency_installer_errors', '__return_false' );
				$abprf_info     = ABPRF_Query::get_info();
				$brand_label    = ABPRF_Function::label();
				$brand_icon     = ABPRF_Function::icon();
				$total_post     = isset( $abprf_info['total_post'] ) && $abprf_info['total_post'] ? $abprf_info['total_post'] : 0;
				$total_property = isset( $abprf_info['total_property'] ) && $abprf_info['total_property'] ? $abprf_info['total_property'] : 0;
				$total_order    = isset( $abprf_info['total_order'] ) && $abprf_info['total_order'] ? $abprf_info['total_order'] : 0;
				$new_post_url   = isset( $abprf_info['new_post_url'] ) && $abprf_info['new_post_url'] ? $abprf_info['new_post_url'] : '';
				$allowed_tabs   = [ 'dashboard', 'posts', 'properties', 'orders', 'global', 'configuration', 'status', 'documentation' ];
				$active_tab     = isset( $_GET['rf_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['rf_tab'] ) ) : 'posts';
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'posts';
				}
				?>
                <div class="abprf_area  abprf_admin">
                    <div class="admin_head _fj_between">
                        <div class="head_brand _d_flex">
                            <div class="brand_icon _all_center_mar_r_xs"><?php ABPRF_Layout::image_icon( $brand_icon ); ?></div>
                            <div class="_fd_column">
                                <h4 class="_abprf"><?php echo esc_html( $brand_label ); ?></h4>
                                <span class="brand_version"><?php echo esc_html( get_plugin_data( ABPRF_PLUGIN_FILE ) ['Version'] ); ?></span>
                            </div>
                        </div>
						<?php if ( ABPRF_WC == 2 ) { ?>
                            <div class="_group_content">
                                <button type="button" class="_btn_white" data-href="<?php echo esc_url( $new_post_url ); ?>" data-blank="_blank"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Post', 'abp-rentalforge' ); ?></button>
                                <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abp-rentalforge' ); ?></button>
                                <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="category"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abp-rentalforge' ) . ' ' . esc_html( ABPRF_Function::category_label() ); ?></button>
                                <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Location', 'abp-rentalforge' ); ?></button>
                            </div>
						<?php } ?>
                    </div>
                    <div class="admin_menu">
                        <div class="menu_list">
                            <!--                            <a href="--><?php //echo esc_url( add_query_arg( 'rf_tab', 'dashboard' ) ); ?><!--" class="_btn_light_info --><?php //echo esc_attr( $active_tab == 'dashboard' ? 'rf_active' : '' ); ?><!--"><span class="_mar_r_xs">📊</span>--><?php //esc_html_e( 'Dashboard', 'abp-rentalforge' ); ?><!--</a>-->
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'posts' ) ); ?>" class="_btn_info post_tab <?php echo esc_attr( $active_tab == 'posts' ? 'rf_active' : '' ); ?>"><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?><?php esc_html_e( 'Post Lists', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_post ); ?></sup></a>
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'properties' ) ); ?>" class="_btn_info properties_tab <?php echo esc_attr( $active_tab == 'properties' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏠</span><?php esc_html_e( 'Properties', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_property ); ?></sup></a>
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'orders' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'orders' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📋</span><?php esc_html_e( 'Orders', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_order ); ?></sup></a>
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'global' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'global' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🌐</span><?php esc_html_e( 'Global Data', 'abp-rentalforge' ); ?></a>
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'configuration' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'configuration' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">⚙️</span><?php esc_html_e( 'Configuration', 'abp-rentalforge' ); ?></a>
                            <a href="<?php echo esc_url( add_query_arg( 'rf_tab', 'status' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'status' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🛡️</span><?php esc_html_e( 'Status', 'abp-rentalforge' ); ?></a>
							<?php do_action( 'abprf_add_admin_menu_tab', $active_tab ); ?>
                        </div>
                    </div>
                    <div class="dashboard_content">
						<?php do_action( 'abprf_load_' . $active_tab, $abprf_info ); ?>
                    </div>
					<?php ABPRF_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}

			public function load_global( $abprf_info ): void {
				$allowed_tabs = [ 'dates', 'additional_service', 'client_form', 'tc', 'faq', 'category', 'location', 'feature', 'brand' ];
				$active_tab   = isset( $_GET['rf_global'] ) ? sanitize_text_field( wp_unslash( $_GET['rf_global'] ) ) : 'dates';
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'dates';
				}
				?>
                <div class="_abp_panel_max_1200_mar_auto">
                    <div class="abprf_tabs tab_top">
                        <div class="_panel_head">
                            <div class="menu_list _group_content">
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'dates' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'dates' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🗓️</span> <?php esc_html_e( 'Dates', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'additional_service' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'additional_service' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">💰</span> <?php esc_html_e( 'Additional services', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'client_form' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'client_form' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Client Form', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'tc' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'tc' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🤝</span> <?php esc_html_e( 'T & C', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'faq' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'faq' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'FAQ', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'category' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'category' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏘️</span><?php echo esc_html( ABPRF_Function::category_label() ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'location' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'location' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📍</span><?php esc_html_e( 'Location', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'brand' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'brand' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏷️</span><?php esc_html_e( 'Brand', 'abp-rentalforge' ); ?></a>
                                <a href="<?php echo esc_url( add_query_arg( 'rf_global', 'feature' ) ); ?>" class="_btn_light_white  <?php echo esc_attr( $active_tab == 'feature' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🔗</span><?php esc_html_e( 'Feature', 'abp-rentalforge' ); ?></a>
								<?php do_action( 'abprf_add_admin_global_tab', $active_tab ); ?>
                            </div>
                        </div>
                        <div class="_panel_body  _bg_white">
							<?php do_action( 'abprf_global_' . $active_tab, $abprf_info ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		new ABPRF_ADMIN();
	}