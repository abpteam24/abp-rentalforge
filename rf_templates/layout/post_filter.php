<?php
    if (!defined('ABSPATH')) {
        exit;
    }
    add_action('abprf_post_filter_template', function ($params) {
        if (!is_array($params)) {
            return;
        }
        $style = $params['style'] ?? 'grid';
        $post_ids = $params['all_post'] ?? [];
        $cat_id = $params['cat_id'] ?? null;
        $loc_id = $params['loc_id'] ?? null;
        $brand_id = $params['brand_id'] ?? null;
        $categories = [];
        if (empty($cat_id) && is_array($post_ids) && !empty($post_ids)) {
            foreach ($post_ids as $post_id) {
                $category = ABPRF_Function::get_post_info($post_id, 'abprf_category');
                if (!empty($category)) {
                    $categories[] = $category;
                }
            }
            $categories = array_unique($categories);
        }
        $locations = [];
        if (empty($loc_id) && is_array($post_ids) && !empty($post_ids)) {
            foreach ($post_ids as $post_id) {
                $location_array = ABPRF_Function::get_post_info($post_id, 'abprf_location');
                if (!empty($location_array)) {
                    $location_array = explode(',', $location_array);
                    $locations = array_merge($locations, $location_array);
                }
            }
            $locations = array_unique($locations);
        }
        $brands = [];
        if (empty($brand_id) && is_array($post_ids) && !empty($post_ids)) {
            foreach ($post_ids as $post_id) {
                $brand_array = ABPRF_Function::get_post_info($post_id, 'abprf_brand');
                if (!empty($brand_array)) {
                    $brand_array = explode(',', $brand_array);
                    $brands = array_merge($brands, $brand_array);
                }
            }
            $brands = array_unique($brands);
        }
        $cat_count = count($categories);
        $loc_count = count($locations);
        $brand_count = count($brands);
        if ($cat_count > 1 || $loc_count > 1 || $brand_count > 1 || $style === 'grid' || $style === 'list') { ?>
            <div class="post_top_filter">
                <?php if ($cat_count > 1 && is_array(ABPRF_Category)) { ?>
                    <label>
                        <select class="_form_control" name="cat_id">
                            <option value="" selected><?php echo esc_html__('All ', 'abp-rentalforge') . ' ' . esc_html(ABPRF_Function::category_label()); ?></option>
                            <?php
                                foreach ($categories as $current_cat_id) {
                                    $name = ABPRF_Category[$current_cat_id]['name'] ?? '';
                                    if ($name !== '') {
                                        ?>
                                        <option value="<?php echo esc_attr($current_cat_id); ?>"><?php echo esc_html($name); ?></option>
                                        <?php
                                    }
                                }
                            ?>
                        </select>
                    </label>
                <?php }
                    if ($loc_count > 1 && is_array(ABPRF_Locations)) {
                        ?>
                        <label>
                            <select class="_form_control" name="loc_id">
                                <option value="" selected><?php esc_html_e('All Location', 'abp-rentalforge'); ?></option>
                                <?php
                                    foreach ($locations as $current_loc_id) {
                                        $name = ABPRF_Locations[$current_loc_id]['name'] ?? '';
                                        if ($name !== '') {
                                            ?>
                                            <option value="<?php echo esc_attr($current_loc_id); ?>"><?php echo esc_html($name); ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </label>
                    <?php }
                    if ($brand_count > 1 && is_array(ABPRF_Brands)) {
                        ?>
                        <label>
                            <select class="_form_control" name="brand_id">
                                <option value="" selected><?php echo esc_html__('All ', 'abp-rentalforge') . ' ' . esc_html(ABPRF_Function::brand_label()); ?></option>
                                <?php
                                    foreach ($brands as $current_brand_id) {
                                        $name = ABPRF_Brands[$current_brand_id]['name'] ?? '';
                                        if ($name !== '') {
                                            ?>
                                            <option value="<?php echo esc_attr($current_brand_id); ?>"><?php echo esc_html($name); ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </label>
                    <?php }
                    if ($style === 'grid' || $style === 'list') { ?>
                        <div class="_group_content">
                            <button type="button" class="_btn_light_info_xs_fs_h6 grid_view <?php echo esc_attr($style === 'grid' ? 'abp_active' : ''); ?>"><span class="fas fa-table-cells"></span></button>
                            <button type="button" class="_btn_light_info_xs_fs_h6 list_view <?php echo esc_attr($style === 'list' ? 'abp_active' : ''); ?>"><span class="fas fa-list"></span></button>
                        </div>
                    <?php } ?>
            </div>
            <?php
        }
    });