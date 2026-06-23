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
				$label          = ABPRF_Function::label();
				$icon           = ABPRF_Function::icon();
				$total_post     = $abprf_info['total_post'] ?? 0;
				$total_property = $abprf_info['total_property'] ?? 0;
				$total_order    = $abprf_info['total_order'] ?? 0;
				$allowed_tabs   = [ 'dashboard', 'posts', 'properties', 'orders', 'global', 'configuration', 'status', 'documentation', 'admin_order' ];
				$active_tab     = 'posts';
				if ( isset( $_GET['_abprf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abprf_nonce'] ) ), 'abprf_url_action' ) ) {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'posts';
				}
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'posts';
				}
				?>
                <div class="abprf_area  abprf_admin">
                    <div class="admin_head _fj_between">
                        <div class="head_brand _d_flex">
                            <div class="brand_icon _all_center"><?php ABPRF_Layout::image_icon( $icon ); ?></div>
                            <div class="_fd_column">
                                <h4 class="_abprf"><?php echo esc_html( $label ); ?></h4>
                                <span class="brand_version"><?php echo esc_html( ABPRF_VERSION ); ?></span>
                            </div>
                        </div>
						<?php if ( ABPRF_WC == 2 ) { ?>
                            <div class="_group_content">
                                <button type="button" class="_btn_white" data-href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPRF_Function::get_cpt() ) ); ?>" data-blank="_blank"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Post', 'abp-rentalforge' ); ?></button>
                                <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="property"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abp-rentalforge' ); ?></button>
								<?php if ( ABPRF_Function::on_off( 'category' ) ) { ?>
                                    <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="category"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abp-rentalforge' ) . ' ' . esc_html( ABPRF_Function::category_label() ); ?></button>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'location' ) ) { ?>
                                    <button type="button" class="_btn_white" data-target-popup="#abprf_global_popup" data-type="location"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Location', 'abp-rentalforge' ); ?></button>
								<?php } ?>
                            </div>
						<?php } ?>
                    </div>
                    <div class="admin_menu">
                        <div class="menu_list">
                            <!--                            <a href="--><?php //echo esc_url( add_query_arg( 'rf_tab', 'dashboard' ) ); ?><!--" class="_btn_light_info --><?php //echo esc_attr( $active_tab == 'dashboard' ? 'rf_active' : '' ); ?><!--"><span class="_mar_r_xs">📊</span>--><?php //esc_html_e( 'Dashboard', 'abp-rentalforge' ); ?><!--</a>-->
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'posts' ) ); ?>" class="_btn_info post_tab <?php echo esc_attr( $active_tab == 'posts' ? 'rf_active' : '' ); ?>"><?php ABPRF_Layout::image_icon( $icon ); ?><?php esc_html_e( 'Post Lists', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_post ); ?></sup></a>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'properties' ) ); ?>" class="_btn_info properties_tab <?php echo esc_attr( $active_tab == 'properties' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏠</span><?php esc_html_e( 'Properties', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_property ); ?></sup></a>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'orders' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'orders' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📋</span><?php esc_html_e( 'Orders', 'abp-rentalforge' ); ?><sup class="_mar_l_xs_circle_icon_xs"><?php echo esc_html( $total_order ); ?></sup></a>
							<?php do_action( 'abprf_add_admin_menu_tab_middle', $active_tab ); ?>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'global' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🌐</span><?php esc_html_e( 'Global Data', 'abp-rentalforge' ); ?></a>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'configuration' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'configuration' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">⚙️</span><?php esc_html_e( 'Configuration', 'abp-rentalforge' ); ?></a>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'status' ) ); ?>" class="_btn_info <?php echo esc_attr( $active_tab == 'status' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🛡️</span><?php esc_html_e( 'Status', 'abp-rentalforge' ); ?></a>
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
				$allowed_tabs = [ 'dates', 'additional', 'client_form', 'tc', 'faq', 'category', 'location', 'feature', 'brand', 'discount' ];
				$active_tab   = 'dates';
				if ( isset( $_GET['_abprf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abprf_nonce'] ) ), 'abprf_url_action' ) ) {
					$active_tab = isset( $_GET['global'] ) ? sanitize_text_field( wp_unslash( $_GET['global'] ) ) : 'dates';
				}
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'dates';
				}
				?>
                <div class="_abp_panel_max_1200_mar_auto">
                    <div class="abprf_tabs tab_top">
                        <div class="_panel_head">
                            <div class="tab_lists _group_content">
                                <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'dates' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'dates' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🗓️</span> <?php esc_html_e( 'Dates', 'abp-rentalforge' ); ?></a>
								<?php if ( ABPRF_Function::on_off( 'additional_info' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'additional' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'additional' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">💰</span> <?php esc_html_e( 'Additional services', 'abp-rentalforge' ); ?></a>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'client_info' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'client_form' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'client_form' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Client Form', 'abp-rentalforge' ); ?></a>
								<?php } ?>
								<?php do_action( 'abprf_add_admin_global_tab', $active_tab ); ?>
								<?php if ( ABPRF_Function::on_off( 'tc' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'tc' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'tc' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🤝</span> <?php esc_html_e( 'T & C', 'abp-rentalforge' ); ?></a>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'faq' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'faq' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'faq' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'FAQ', 'abp-rentalforge' ); ?></a>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'category' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'category' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'category' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏘️</span><?php echo esc_html( ABPRF_Function::category_label() ); ?></a>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'location' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'location' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'location' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">📍</span><?php echo esc_html( ABPRF_Function::location_label() ); ?></a>
								<?php } ?>
								<?php if ( ABPRF_Function::on_off( 'brand' ) ) { ?>
                                    <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'brand' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'brand' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🏷️</span><?php echo esc_html( ABPRF_Function::brand_label() ) ?></a>
								<?php } ?>
                                <a href="<?php echo esc_url( ABPRF_Function::build_url( 'global', [ 'global' => 'feature' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'feature' ? 'rf_active' : '' ); ?>"><span class="_mar_r_xxs">🔗</span><?php esc_html_e( 'Feature', 'abp-rentalforge' ); ?></a>
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