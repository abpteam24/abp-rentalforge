<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$ribbon = $ribbon ?? false;
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$display_category = array_key_exists('display_category', $abprf_infos) ? $abprf_infos['display_category'] : 'on';
	$category = array_key_exists('category', $abprf_infos) ? $abprf_infos['category'] : '';
	if ($category && $display_category == 'on') {
		if ($ribbon) { ?>
            <div class="ribbon "><?php echo esc_html($category); ?></div>
		<?php } else { ?>
            <span class="_fs_label_mar_r">
                <?php
	                $abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : ABPRF_LIB_Function::get_option('abprf_configuration');
	                $category_label = is_array($abprf_configuration) && array_key_exists('category_label', $abprf_configuration) && $abprf_configuration['category_label'] ? $abprf_configuration['category_label'] : __('Category : ', 'abprf-rental-forge');
	                echo esc_html($category_label . ' ' . $category);
                ?>
        </span>
			<?php
		}
	}
