<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_ADMIN')) {
        class ABPRF_ADMIN {
            public function __construct() {
                add_action('admin_menu', array($this, 'admin_menu'));
            }
            public function admin_menu(): void {
                $label = ABPRF_Function::label();
                $slug = ABPRF_Function::slug();
                $icon = ABPRF_Function::icon_wp();
                add_menu_page($label, $label, 'manage_options', $slug, array($this, 'load_main_page'), $icon, 50);
            }
            public function load_main_page(): void {
                remove_all_actions('user_admin_notices');
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
                remove_all_actions('network_admin_notices');
                add_filter('wp_dependency_installer_errors', '__return_false');
                $abprf_info = ABPRF_Query::get_info();
                $label = ABPRF_Function::label();
                $icon = ABPRF_Function::icon();
                $total_post = $abprf_info['total_post'] ?? 0;
                $total_property = $abprf_info['total_property'] ?? 0;
                $total_order = $abprf_info['total_order'] ?? 0;
                $allowed_tabs = ['dashboard', 'posts', 'properties', 'orders', 'configuration', 'admin_order','documentation'];
                $active_tab = 'dashboard';
                if (isset($_GET['_abprf_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_abprf_nonce'])), 'abprf_url_action')) {
                    $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'dashboard';
                }
                if ($active_tab === 'global') {
                    $active_tab = 'configuration';
                }
                if (!in_array($active_tab, $allowed_tabs, true)) {
                    $active_tab = 'dashboard';
                }
                if (ABPRF_WC < 2) {
                    $active_tab = 'dashboard';
                }
                ?>
                <div class="abprf_area  abprf_admin">
                    <div class="admin_head">
                        <div class="head_brand">
                            <div class="brand_icon _all_center"><?php ABPRF_Layout::image_icon($icon); ?></div>
                            <div class="_fd_column">
                                <h4 class="abp"><?php echo esc_html($label); ?></h4>
                                <span class="brand_version"><?php echo esc_html(ABPRF_VERSION); ?></span>
                            </div>
                        </div>
                        <nav class="abprf_nav">
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('dashboard')); ?>" class="_btn_xs <?php echo esc_attr($active_tab == 'dashboard' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('status'); ?><?php esc_html_e('Dashboard', 'abp-rentalforge'); ?></a>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('posts')); ?>" class="_btn_xs post_tab <?php echo esc_attr($active_tab == 'posts' ? 'abp_active' : ''); ?>"><?php ABPRF_Layout::image_icon($icon); ?><?php esc_html_e('Post Lists', 'abp-rentalforge'); ?><sup class="_color_theme">(<?php echo esc_html($total_post); ?>)</sup></a>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('properties')); ?>" class="_btn_xs properties_tab <?php echo esc_attr($active_tab == 'properties' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('property'); ?><?php esc_html_e('Properties', 'abp-rentalforge'); ?><sup class="_color_theme">(<?php echo esc_html($total_property); ?>)</sup></a>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('orders')); ?>" class="_btn_xs <?php echo esc_attr($active_tab == 'orders' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('order'); ?><?php esc_html_e('Orders', 'abp-rentalforge'); ?><sup class="_color_theme">(<?php echo esc_html($total_order); ?>)</sup></a>
                            <?php do_action('abprf_add_admin_menu_tab_middle', $active_tab); ?>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('configuration')); ?>" class="_btn_xs <?php echo esc_attr($active_tab == 'configuration' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('setting'); ?><?php esc_html_e('Global Configuration', 'abp-rentalforge'); ?></a>
                            <?php do_action('abprf_add_admin_menu_tab', $active_tab); ?>
                            <a href="<?php echo esc_url( ABPRF_Function::build_url( 'documentation' ) ); ?>" class="_btn_xs <?php echo esc_attr( $active_tab == 'documentation' ? 'abp_active' : '' ); ?>">
                                <span>📘</span><?php esc_html_e( 'Documentation', 'abp-rentalforge' ); ?>
                            </a>
                        </nav>
                        <?php if (ABPRF_WC == 2) { ?>
                            <div class="_group_content">
                                <button type="button" class="_btn_xs" data-href="<?php echo esc_url(admin_url('post-new.php?post_type=' . ABPRF_Function::get_cpt())); ?>" data-blank="_blank"><?php echo esc_html($label); ?><?php ABPRF_Static::svg('plus'); ?></button>
                                <button type="button" class="_btn_xs" data-target-popup="#abprf_global_popup" data-type="property"><?php esc_html_e('Property', 'abp-rentalforge'); ?><?php ABPRF_Static::svg('plus'); ?></button>
                                <?php if (ABPRF_Function::on_off('category')) { ?>
                                    <button type="button" class="_btn_xs" data-target-popup="#abprf_global_popup" data-type="category"><?php echo esc_html(ABPRF_Function::category_label()); ?><?php ABPRF_Static::svg('plus'); ?></button>
                                <?php } ?>
                                <?php if (ABPRF_Function::on_off('location')) { ?>
                                    <button type="button" class="_btn_xs" data-target-popup="#abprf_global_popup" data-type="location"><?php echo esc_html(ABPRF_Function::location_label()); ?><?php ABPRF_Static::svg('plus'); ?></button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="dashboard_content">
                        <?php
                            do_action('abprf_notice');
                        do_action('abprf_load_' . $active_tab, $abprf_info); ?>
                    </div>
                    <?php ABPRF_Layout::load_admin_globally(); ?>
                </div>
                <?php
            }
        }
        new ABPRF_ADMIN();
    }