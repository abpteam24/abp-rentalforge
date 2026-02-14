<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$params = $params ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$from = array_key_exists('from', $params) ? $params['from'] : '';
	$to = array_key_exists('to', $params) ? $params['to'] : '';
	$cat = array_key_exists('cat', $params) ? $params['cat'] : '';
	$show_post = array_key_exists('post', $params) && $params['post'] ? $params['post'] : 50;
	$transports = ABPRF_Query::get_equipment_id($from, $to, $cat);
	if (sizeof($transports) > 0) {
		$post_count = 0;
		$args['total'] = sizeof($transports);
		$args['page_item'] = $show_post;
		$all_equipment_ids = [];
		foreach ($transports as $equipment_id) {
			$all_equipment_ids[$equipment_id] = get_the_title($equipment_id);
		}
		asort($all_equipment_ids);
		?>
        <div class=" abptm_list abprf_pagination_area">
            <div class="_f_wrap_mar_t_xs">
				<?php foreach ($all_equipment_ids as $equipment_id => $title) {
					$post_count++;
					$display_category = ABPRF_LIB_Function::get_post_info($equipment_id, 'display_category', 'on');
					$category = ABPRF_LIB_Function::get_post_info($equipment_id, 'category'); ?>
                    <a class="_abprf_margin_xxs_border_b pagination_item  <?php echo esc_attr($show_post >= $post_count ? '' : '_d_none'); ?>" href="<?php echo esc_url(get_the_permalink($equipment_id) . '?_bp= ' . $from . '&_dp=' . $to); ?>">
						<?php
							echo esc_html($title);
							if ($category && $display_category == 'on') {
								echo esc_html('-' . $category);
							}
						?>
                    </a>
				<?php } ?>
            </div>
			<?php do_action('abprf_pagination', $args); ?>
        </div>
		<?php
	} else {
		ABPRF_LIB_Layout::layout_warning_info('not_found');
	}