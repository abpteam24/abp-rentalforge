<?php
    if (!defined('ABSPATH')) {
        exit;
    }
    add_action('abprf_term_condition_template', function ($abprf_infos = [], $type = '') {
        if (ABPRF_Function::on_off('tc')) {
            $infos = '';
            if (!empty($abprf_infos) && is_array($abprf_infos)) {
                $display = $abprf_infos['display_tc'] ?? 'on';
                $active_global_tc = $abprf_infos['active_global_tc'] ?? 'on';
                if ($display === 'on') {
                    $infos = ($active_global_tc === 'on') ? ABPRF_Function::get_option('abprf_tc', '') : ($abprf_infos['abprf_tc'] ?? '');
                }
            } elseif ($type === 'global') {
                $infos = ABPRF_Function::get_option('abprf_tc', '');
            }
            if (!empty($infos) && is_string($infos)) {
                ?>
                <div class="_section_alert">
                    <h4 class="_abp"><span class="_mar_r_xxs">🤝</span> <?php esc_html_e('Term & Conditions', 'abp-rentalforge'); ?></h4>
                    <div class="_divider_xs"></div>
                    <?php
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
                        echo wp_kses_post(apply_filters('the_content', $infos));
                    ?>
                </div>
                <?php
            }
        }
    }, 10, 2);