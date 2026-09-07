<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_Location')) {
        class ABPRF_Location {
            public function __construct() {
                add_action('abprf_global_location', [$this, 'global_location']);
                add_action('abprf_location_update', [$this, 'update_location']);
                add_action('wp_ajax_abprf_save_location', [$this, 'save_location']);
                add_action('wp_ajax_abprf_delete_location', [$this, 'delete_location']);
                add_action('wp_ajax_abprf_add_location', [$this, 'add_location']);
            }
            public function global_location(): void {
                if (ABPRF_Function::on_off('location')) {
                    ?>
                    <div class="location_list _ov_auto">
                        <?php $this->location_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <button type="button" class="_btn_default" data-target-popup="#abprf_global_popup" data-type="location">
                        <?php ABPRF_Static::svg('plus');
                            echo esc_html__('Add New Location', 'abp-rentalforge'); ?>
                    </button>
                    <?php
                }
            }
            public function update_location(): void {
                $taxonomies = ABPRF_Function::get_taxonomy('abprf_location');
                $taxonomies = is_array($taxonomies) ? $taxonomies : [];
                $location = [];
                if (count($taxonomies) > 0) {
                    foreach ($taxonomies as $taxonomy) {
                        $term_id = $taxonomy->term_id;
                        $location[$term_id]['name'] = $taxonomy->name;
                        $location[$term_id]['description'] = $taxonomy->description;
                        $location[$term_id]['slug'] = $taxonomy->slug;
                        $location[$term_id]['lat'] = get_term_meta((int)$term_id, 'abprf_loc_lat', true);
                        $location[$term_id]['lng'] = get_term_meta((int)$term_id, 'abprf_loc_lng', true);
                        $location[$term_id]['radius'] = get_term_meta((int)$term_id, 'abprf_loc_radius', true);
                        $polygon = get_term_meta((int)$term_id, 'abprf_loc_polygon', true);
                        $location[$term_id]['polygon'] = is_array($polygon) ? $polygon : [];
                    }
                }
                ksort($location);
                update_option('abprf_location', $location);
            }
            public function location_list(): void {
                $all_locations = ABPRF_Function::get_option('abprf_location');
                $all_locations = is_array($all_locations) ? $all_locations : [];
                $count = 1;
                if (count($all_locations) > 0) { ?>
                    <table class="abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e('SI', 'abp-rentalforge') ?></th>
                            <th><?php esc_html_e('ID', 'abp-rentalforge') ?></th>
                            <th class="_min_150"><?php esc_html_e('Location Title', 'abp-rentalforge') ?></th>
                            <th><?php esc_html_e('Location Full Address', 'abp-rentalforge') ?></th>
                            <th><?php esc_html_e('Shortcode Post', 'abp-rentalforge') ?></th>
                            <th><?php esc_html_e('Shortcode Property', 'abp-rentalforge') ?></th>
                            <th><?php esc_html_e('Action', 'abp-rentalforge') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($all_locations as $term_id => $location) {
                            $name = $location['name'] ?? '';
                            $description = $location['description'] ?? '';
                            $lat = $location['lat'] ?? '';
                            $lng = $location['lng'] ?? '';
                            $radius = $location['radius'] ?? '';
                            $has_geo = $lat !== '' || $lng !== '' || $radius !== '' || !empty($location['polygon']);
                            ?>
                            <tr>
                                <th><?php echo esc_html($count); ?>.</th>
                                <th><?php echo esc_html($term_id); ?></th>
                                <th class="_text_left"><a href="<?php echo esc_url(get_term_link((int)$term_id)); ?>" target="_blank" class="abp_fs_h5_color_theme"><?php echo esc_html($name); ?></a></th>

                                <td>
                                    <?php if ($has_geo) { ?>
                                        <span class="abp_color_info">📍 <?php echo esc_html($lat . ($lng ? ', ' . $lng : '')); ?><?php echo $radius !== '' ? ' · ' . esc_html($radius) . ' km' : ''; ?><?php echo !empty($location['polygon']) ? ' · ⛛ polygon' : ''; ?></span><br/>
                                    <?php } ?>
                                    <?php echo esc_html($description); ?>
                                </td>
                                <th class="_text_nowrap"><code> [abprf-post loc_id="<?php echo esc_attr($term_id); ?>"]</code></th>
                                <th class="_text_nowrap"><code> [abprf-property loc_id="<?php echo esc_attr($term_id); ?>"]</code></th>
                                <td>
                                    <div class="_group_content">
                                        <button type="button" class="_btn_light_yellow_xxs" data-id="<?php echo esc_attr($term_id); ?>" data-target-popup="#abprf_global_popup" data-type="location" title="<?php echo esc_attr__('Edit : ', 'abp-rentalforge') . ' ' . esc_attr($name); ?>"><?php ABPRF_Static::svg('edit'); ?></button>
                                        <button type="button" class="_btn_light_danger_xxs delete_location" data-loc_id="<?php echo esc_attr($term_id); ?>" title="<?php echo esc_attr__('Trash : ', 'abp-rentalforge') . ' ' . esc_attr($name); ?>"><?php ABPRF_Static::svg('close_1'); ?></button>
                                    </div>
                                </td>
                            </tr>
                            <?php $count++;
                        } ?>
                        </tbody>
                    </table>
                <?php } else {
                    ABPRF_Layout::layout_warning_info('no_location');
                }
            }
            public function form($location = '', $loc_id = ''): void {
                $name = $location['name'] ?? '';
                $des = $location['description'] ?? '';
                $slug = $location['slug'] ?? '';
                $lat = $location['lat'] ?? '';
                $lng = $location['lng'] ?? '';
                $radius = $location['radius'] ?? '';
                $polygon = $location['polygon'] ?? [];
                $polygon = is_array($polygon) ? $polygon : [];
                $polygon_text = '';
                foreach ($polygon as $point) {
                    if (is_array($point) && isset($point['lat'], $point['lng'])) {
                        $polygon_text .= esc_textarea($point['lat'] . ',' . $point['lng']) . "\n";
                    }
                }
                ?>
                <input type="hidden" name="loc_term_id" value="<?php echo esc_attr($loc_id); ?>"/>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span><?php esc_html_e('Location Name', 'abp-rentalforge'); ?><sup class="_color_required">*</sup></span>
                        <input class="_form_control" name="name" value="<?php echo esc_attr($name); ?>" placeholder="<?php esc_attr_e('Name', 'abp-rentalforge'); ?>" required/>
                    </label>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_name'); ?>
                </div>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span><?php esc_html_e('Location Slug (Optional)', 'abp-rentalforge'); ?></span>
                        <input class="_form_control" name="slug" value="<?php echo esc_attr($slug); ?>" placeholder="<?php esc_attr_e('Slug', 'abp-rentalforge'); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_slug'); ?>
                </div>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span><?php esc_html_e('Location Full Address', 'abp-rentalforge'); ?></span>
                        <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e('Address', 'abp-rentalforge'); ?>"><?php echo esc_html($des); ?></textarea>
                    </label>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_des'); ?>
                </div>
                <div class="setting_item _mar_b_xs">
                    <div class="_f_equal">
                        <label class="_f_equal_f_wrap _mar_r_xxs">
                            <span><?php esc_html_e('Latitude (Optional)', 'abp-rentalforge'); ?></span>
                            <input class="_form_control" name="lat" value="<?php echo esc_attr($lat); ?>" placeholder="23.8103" step="any"/>
                        </label>
                        <label class="_f_equal_f_wrap _mar_l_xxs">
                            <span><?php esc_html_e('Longitude (Optional)', 'abp-rentalforge'); ?></span>
                            <input class="_form_control" name="lng" value="<?php echo esc_attr($lng); ?>" placeholder="90.4125" step="any"/>
                        </label>
                    </div>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_geo'); ?>
                </div>
                <div class="setting_item _mar_b_xs">
                    <label class="_f_equal_f_wrap">
                        <span><?php esc_html_e('Service Radius (km)', 'abp-rentalforge'); ?></span>
                        <input class="_form_control" name="radius" value="<?php echo esc_attr($radius); ?>" placeholder="<?php esc_attr_e('e.g. 10', 'abp-rentalforge'); ?>" step="any"/>
                    </label>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_radius'); ?>
                </div>
                <div class="setting_item">
                    <label class="_f_equal_f_wrap">
                        <span><?php esc_html_e('Polygon Coordinates (Optional)', 'abp-rentalforge'); ?></span>
                        <textarea class="_form_control" name="polygon" rows="4" placeholder="<?php esc_attr_e("One pair per line:\nlat,lng", 'abp-rentalforge'); ?>"><?php echo $polygon_text; // phpcs:ignore WordPress.Security.EscapeOutput ?></textarea>
                    </label>
                    <div class="_divider_xs"></div>
                    <?php ABPRF_Layout::info_text('loc_polygon'); ?>
                </div>
                <div class="_divider_xs"></div>
                <button type="button" class="_btn_theme save_location"><?php ABPRF_Static::svg('save');
                        echo(!empty($loc_id) ? esc_html__('Update Location', 'abp-rentalforge') : esc_html__('Save Location', 'abp-rentalforge')); ?></button>
                <?php
            }
            public static function location_selection($_location = ''): void {
                $all_location = ABPRF_Function::get_option('abprf_location');
                $all_location = is_array($all_location) ? $all_location : [];
                $location_array = !empty($_location) ? explode(',', $_location) : [];
                if (count($all_location) > 0) { ?>
                    <div class="custom_checkbox _fj_end">
                        <input type="hidden" name="abprf_location" value="<?php echo esc_attr($_location); ?>"/>
                        <?php foreach ($all_location as $key => $location) {
                            $name = $location['name'] ?? ''; ?>
                            <div class="checkbox_item _min_100">
                                <button type="button" class="_btn_light_info_xs <?php echo esc_attr(in_array((string)$key, $location_array, true) ? 'abp_active' : ''); ?>" data-checked="<?php echo esc_attr($key); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                    <span data-icon class="far <?php echo esc_attr(in_array((string)$key, $location_array, true) ? 'far fa-check-square' : 'fa-square'); ?>"></span><?php echo esc_html($name); ?>
                                </button>
                            </div>
                        <?php } ?>
                        <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="location"><?php ABPRF_Static::svg('plus'); ?><?php echo esc_html__('Add New Location', 'abp-rentalforge'); ?></button>
                    </div>
                <?php } else { ?>
                    <p><?php echo esc_html(ABPRF_Static::array_info('no_location')); ?></p>
                    <button type="button" class="_btn_default_xs" data-target-popup="#abprf_global_popup" data-type="location"><?php ABPRF_Static::svg('plus'); ?><?php echo esc_html__('Add New Location', 'abp-rentalforge'); ?></button>
                    <?php
                }
            }
            public function add_location(): void {
                if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['html' => '', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
                }
                if (!current_user_can('manage_options')) {
                    wp_send_json_error(['html' => '', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
                }
                $loc_id = isset($_POST['tax_id']) ? sanitize_text_field(wp_unslash($_POST['tax_id'])) : '';
                $locations = ABPRF_Function::get_option('abprf_location');
                $locations = is_array($locations) ? $locations : [];
                $location = $locations[$loc_id] ?? [];
                ob_start();
                $this->form($location, $loc_id);
                $html = ob_get_clean();
                wp_send_json_success(['html' => $html, 'msg' => __('Location Form Loaded Successfully .....! ', 'abp-rentalforge')]);
            }
            public function save_location(): void {
                if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['html' => '', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
                }
                if (!current_user_can('manage_options')) {
                    wp_send_json_error(['html' => '', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
                }
                $post_int = fn($key, $default = 0) => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
                $post_slug = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_title(wp_unslash($_POST[$key])) : $default;
                $cat_term_id = $post_int('loc_term_id');
                $name = $post_val('name');
                $slug = $post_slug('slug');
                $description = $post_val('description');
                $lat = $post_val('lat');
                $lng = $post_val('lng');
                $radius = $post_val('radius');
                $polygon = [];
                if (isset($_POST['polygon'])) {
                    $polygon_lines = preg_split('/\r\n|\r|\n/', sanitize_textarea_field(wp_unslash($_POST['polygon'])));
                    if (is_array($polygon_lines)) {
                        foreach ($polygon_lines as $line) {
                            $line = trim($line);
                            if ($line === '') {
                                continue;
                            }
                            $parts = array_map('trim', explode(',', $line));
                            if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                                $polygon[] = ['lat' => (float) $parts[0], 'lng' => (float) $parts[1]];
                            }
                        }
                    }
                }
                $abprf_post_id = $post_int('abprf_post_id');
                if (empty($name)) {
                    wp_send_json_error(['html' => '', 'msg' => __('Location Name cannot be blank!', 'abp-rentalforge')]);
                }
                if ($cat_term_id > 0) {
                    $result = wp_update_term($cat_term_id, 'abprf_location', [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                    ]);
                } else {
                    $result = wp_insert_term($name, 'abprf_location', [
                        'slug' => $slug,
                        'description' => $description,
                    ]);
                }
                if (is_wp_error($result)) {
                    wp_send_json_error(['html' => '', 'msg' => $result->get_error_message()]);
                }
                $term_id = absint($result['term_id'] ?? 0);
                if ($term_id <= 0) {
                    wp_send_json_error(['html' => '', 'msg' => __('Failed to resolve location context.', 'abp-rentalforge')]);
                }
                update_term_meta((int)$term_id, 'abprf_loc_lat', sanitize_text_field($lat));
                update_term_meta((int)$term_id, 'abprf_loc_lng', sanitize_text_field($lng));
                update_term_meta((int)$term_id, 'abprf_loc_radius', sanitize_text_field($radius));
                update_term_meta((int)$term_id, 'abprf_loc_polygon', $polygon);
                $this->update_location();
                $msg = __('Location Saved Successfully !', 'abp-rentalforge');
                ob_start();
                if ($abprf_post_id > 0) {
                    $_location = ABPRF_Function::get_post_info($abprf_post_id, 'abprf_location');
                    self::location_selection($_location);
                } else {
                    $this->location_list();
                }
                $html = ob_get_clean();
                wp_send_json_success(['html' => $html, 'msg' => $msg]);
            }
            public function delete_location(): void {
                if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['html' => '', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
                }
                if (!current_user_can('manage_options')) {
                    wp_send_json_error(['html' => '', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
                }
                $loc_id = isset($_POST['loc_id']) ? sanitize_text_field(wp_unslash($_POST['loc_id'])) : '';
                $result = wp_delete_term((int)$loc_id, 'abprf_location');
                $this->update_location();
                ob_start();
                $this->location_list();
                $html = ob_get_clean();
                if (is_wp_error($result)) {
                    wp_send_json_error(['html' => $html, 'msg' => $result->get_error_message()]);
                }
                global $wpdb;
                $table_name = $wpdb->prefix . 'abprf_property';
                $all_ids = ABPRF_Query::get_post_id(['loc_id' => $loc_id]);
                if (count($all_ids) > 0) {
                    foreach ($all_ids as $id) {
                        $location = ABPRF_Function::get_post_info($id, 'location');
                        $location_array = !empty($location) ? explode(',', $location) : [];
                        if (in_array($loc_id, $location_array, true)) {
                            $location_array = array_diff($location_array, [$loc_id]);
                            $updated_location = !empty($location_array) ? implode(',', $location_array) : '';
                            update_post_meta($id, 'location', $updated_location);
                            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                            $wpdb->update(
                                $table_name,
                                ['location' => $updated_location],
                                ['post_id' => (int)$id],
                                ['%s'],
                                ['%d']
                            );
                        }
                    }
                }
                wp_send_json_success(['html' => $html, 'msg' => __('Location Deleted Successfully !', 'abp-rentalforge')]);
            }
        }
        new ABPRF_Location();
    }