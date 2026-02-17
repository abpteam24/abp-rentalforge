<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$params = $params ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$from = array_key_exists('from', $params) ? $params['from'] : '';
	$to = array_key_exists('to', $params) ? $params['to'] : '';
	$cat = array_key_exists('cat', $params) ? $params['cat'] : '';
	$show_post = array_key_exists('post', $params) && $params['post'] ? $params['post'] : 9;
	$column = array_key_exists('column', $params) ? $params['column'] : 3;
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
        <div class=" abptm_grid abprf_pagination_area">
            <div class="_f_gap_f_wrap_mar_tb">
				<?php foreach ($all_equipment_ids as $equipment_id => $title) {
					$post_count++;
					$abprf_infos = ABPRF_Function::get_all_meta($equipment_id);
					$image_url = ABPRF_Function::get_image_url($equipment_id); ?>
                    <div class="pagination_item list_item _reflex <?php echo esc_attr('grid_' . $column); ?> <?php echo esc_attr($show_post >= $post_count ? '' : '_d_none'); ?>">
						<?php do_action('abprf_category', $abprf_infos, true); ?>
						<?php do_action('abprf_capacity', $abprf_infos, true); ?>
                        <div data-image-href="<?php echo esc_url($image_url); ?>"><img class="_img_control" src="#" alt="<?php echo esc_attr($title); ?>"></div>
                        <a class="_abprf list_title" href="<?php echo esc_url(get_the_permalink($equipment_id) . '?_bp= ' . $from . '&_dp=' . $to); ?>" target="_blank"><?php echo esc_html($title); ?></a>
                    </div>
				<?php } ?>
            </div>
			<?php do_action('abprf_pagination', $args); ?>
        </div>
		<?php
	} else {
		ABPRF_Layout::layout_warning_info('not_found');
	}