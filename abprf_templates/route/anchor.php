<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$params = $params ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$all_route = $all_route ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$args = $args ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$show_post = array_key_exists('post', $params) && $params['post'] ? $params['post'] : 50;
	$redirect_search = ABPRF_LIB_Function::get_options('abprf_configuration', 'redirect_search');
	$post_count = 0;
	if (sizeof($all_route) > 0) {
		?>
        <div class=" abptm_route abprf_pagination_area">
            <div class="_f_wrap_mar_t_xs">
				<?php foreach ($all_route as $route) {
					$url = $redirect_search ? (get_home_url() . '/' . get_page_uri($redirect_search) . '?_bp= ' . $route['start'] . '&_dp=' . $route['end']) : home_url( add_query_arg( null, null ) ) . '?_bp= ' . $route['start'] . '&_dp=' . $route['end'];
					$post_count++; ?>
                    <a class="_abprf_margin_xxs_border_b pagination_item  <?php echo esc_attr($show_post >= $post_count ? '' : '_d_none'); ?>" href="<?php echo esc_url($url); ?>">
						<?php echo esc_html($route['start']); ?>
                        <span class="fas fa-arrow-right _color_theme _mar_lr_xs"></span>
						<?php echo esc_html($route['end']); ?>
                    </a>
				<?php } ?>
            </div>
			<?php do_action('abprf_pagination', $args); ?>
        </div>
		<?php
	}