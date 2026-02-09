<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$routing_infos = array_key_exists('routing_infos', $abprf_infos) ? $abprf_infos['routing_infos'] : [];
	if (sizeof($routing_infos) > 0) {
		foreach ($routing_infos as $routing_info) {
			$route_time = array_key_exists('time', $routing_info) ? $routing_info['time'] : '';
			$route_type = array_key_exists('type', $routing_info) ? $routing_info['type'] : '';
			$route_stop = array_key_exists('stop', $routing_info) ? $routing_info['stop'] : '';
			if ($route_type && $route_stop) {
				$route_class = $route_type == 'bp' || $route_type == 'both' ? 'fas fa-route  _color_theme' : 'fas fa-map-marker-alt  _color_warning';
				?>
                <p class="_abprf_fa_center">
                    <span class="<?php echo esc_attr($route_class); ?> _mar_r_xs"></span>
                    <span><?php echo esc_html($route_stop); ?></span>
					<?php if ($route_time) { ?>
                        <span class="_mar_r_pL_xs">(<?php echo esc_html(ABPRF_LIB_Function::date_format($route_time, 'time')); ?>)</span>
					<?php } ?>
                </p>
				<?php
			}
		}
	}
