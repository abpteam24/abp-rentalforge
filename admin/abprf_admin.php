<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_ADMIN')) {
        class ABPRF_ADMIN {
            public function __construct() {
                add_action('admin_menu', array($this, 'admin_menu'));
                add_action('abprf_load_global', array($this, 'load_global'));
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
                $allowed_tabs = ['dashboard', 'posts', 'properties', 'orders', 'global', 'configuration', 'admin_order','documentation'];
                $active_tab = 'dashboard';
                if (isset($_GET['_abprf_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_abprf_nonce'])), 'abprf_url_action')) {
                    $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'dashboard';
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
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('global')); ?>" class="_btn_xs <?php echo esc_attr($active_tab == 'global' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('globe'); ?><?php esc_html_e('Global Data', 'abp-rentalforge'); ?></a>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('configuration')); ?>" class="_btn_xs <?php echo esc_attr($active_tab == 'configuration' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('setting'); ?><?php esc_html_e('Configuration', 'abp-rentalforge'); ?></a>
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
                        <?php if (ABPRF_WC < 2) { ?>
                            <div class="_section_1_xs abp_notice">
                                <h6 class=" abp_color_warning_gap_xs"><span class="fas fa-exclamation-triangle"></span><?php esc_html_e('ABP RentalForge is entirely dependent on the WooCommerce plugin. Please install and activate the WooCommerce plugin otherwise the plugin will not work. Installing this tool may take some time', 'abp-rentalforge'); ?></h6>
                                <?php if (ABPRF_WC == 1) { ?>
                                    <button class="_btn_warning_xs" onclick="abprf_wc_config('wc_active')" type="button"><span class="fas fa-tasks"></span><?php esc_html_e('Active Now', 'abp-rentalforge'); ?></button>
                                <?php } else { ?>
                                    <button class="_btn_warning_xs" onclick="abprf_wc_config('wc_install_active')" type="button"><span class="fas fa-file-download"></span><?php esc_html_e('Install & Active Now', 'abp-rentalforge'); ?></button>
                                <?php } ?>
                            </div>
                            <div class="_divider"></div>
                            <?php
                        }
                            do_action('abprf_notice');do_action('abprf_load_' . $active_tab, $abprf_info); ?>
                    </div>
                    <?php ABPRF_Layout::load_admin_globally(); ?>
                </div>
                <?php
            }
            public function load_global($abprf_info): void {
                $allowed_tabs = ['dates', 'additional', 'client_form', 'resource', 'category', 'location', 'feature', 'brand', 'discount'];
                $active_tab = 'dates';
                if (isset($_GET['_abprf_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_abprf_nonce'])), 'abprf_url_action')) {
                    $active_tab = isset($_GET['global']) ? sanitize_text_field(wp_unslash($_GET['global'])) : 'dates';
                }
                if (!in_array($active_tab, $allowed_tabs, true)) {
                    $active_tab = 'dates';
                }
                ?>
                <div class="_max_1200_mar_auto">
                    <div class="_section_card">
                        <div class="_group_content_f_equal_f_wrap_w_full">
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'dates'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'dates' ? 'abp_active' : ''); ?>"><?php ABPRF_Static::svg('date_1');
                                    esc_html_e('Dates', 'abp-rentalforge'); ?></a>
                            <?php if (ABPRF_Function::on_off('additional_info')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'additional'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'additional' ? 'abp_active' : ''); ?>">💰 <?php esc_html_e('Additional services', 'abp-rentalforge'); ?></a>
                            <?php } ?>
                            <?php if (ABPRF_Function::on_off('client_info')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'client_form'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'client_form' ? 'abp_active' : ''); ?>">📋<?php esc_html_e('Client Form', 'abp-rentalforge'); ?></a>
                            <?php } ?>
                            <?php do_action('abprf_add_admin_global_tab', $active_tab); ?>
                            <?php if (ABPRF_Function::on_off('tc') || ABPRF_Function::on_off('faq')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'resource'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'resource' ? 'abp_active' : ''); ?>">📚<?php esc_html_e('Resources', 'abp-rentalforge'); ?></a>
                            <?php } ?>
                            <?php if (ABPRF_Function::on_off('category')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'category'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'category' ? 'abp_active' : ''); ?>">🏘️<?php echo esc_html(ABPRF_Function::category_label()); ?></a>
                            <?php } ?>
                            <?php if (ABPRF_Function::on_off('location')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'location'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'location' ? 'abp_active' : ''); ?>">📍<?php echo esc_html(ABPRF_Function::location_label()); ?></a>
                            <?php } ?>
                            <?php if (ABPRF_Function::on_off('brand')) { ?>
                                <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'brand'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'brand' ? 'abp_active' : ''); ?>">🏷️<?php echo esc_html(ABPRF_Function::brand_label()) ?></a>
                            <?php } ?>
                            <a href="<?php echo esc_url(ABPRF_Function::build_url('global', ['global' => 'feature'])); ?>" class="_btn_light_purple_xs  <?php echo esc_attr($active_tab == 'feature' ? 'abp_active' : ''); ?>">🔗<?php esc_html_e('Feature', 'abp-rentalforge'); ?></a>
                        </div>
                        <div class="_divider_xs"></div>
                        <?php do_action('abprf_global_' . $active_tab, $abprf_info); ?>
                    </div>
                </div>
                <?php
            }
        }
        new ABPRF_ADMIN();
    }