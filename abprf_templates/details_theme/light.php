<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$post_id = $post_id ?? get_the_id();
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	if ($post_id > 0) {
		$abprf_infos = ABPRF_LIB_Function::get_all_meta($post_id);
		$abprf_infos['_post_id'] = $post_id;
		$abprf_infos['single_post'] = true;
		$display_slider = array_key_exists('display_slider', $abprf_infos) ? $abprf_infos['display_slider'] : 'on';
		$sale_continue = array_key_exists('sale_continue', $abprf_infos) ? $abprf_infos['sale_continue'] : 'on';
		$form_data = ABPRF_Function::get_form_data($abprf_infos);
		?>
        <div class="abprf_area light_details_page">
            <div class="abprf_container">
                <div class="_abprf_row details_page_top">
					<?php if ($display_slider == 'on') { ?>
                        <div class="_col_6_12_800 ">
							<?php do_action('abprf_slider', $post_id); ?>
                        </div>
					<?php } ?>
                    <div class="details_page_info <?php echo esc_attr($display_slider == 'on' ? '_col_6_12_800' : '_col_12'); ?>">
						<?php do_action('abprf_title', $abprf_infos); ?>
                        <div class="_d_flex_mar_t_xs">
							<?php do_action('abprf_category', $abprf_infos); ?>
							<?php do_action('abprf_capacity', $abprf_infos); ?>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="<?php echo esc_attr($display_slider == 'on' ? '_fd_column' : '_f_wrap'); ?>_mar_t_xs">
							<?php do_action('abptm_route_direction', $abprf_infos); ?>
                        </div>
                    </div>
                </div>
				<?php do_action('abptm_the_content', $abprf_infos); ?>
                <div id="abprf_area" class="_abprf_row details_page_registration">
					<?php if ($sale_continue == 'on') { ?>
						<?php do_action('abprf_search_form', $abprf_infos, [], $form_data); ?>
                        <div class=" abprf_rental_result">
							<?php ABPRF_Layout::transport_list($form_data); ?>
                        </div>
					<?php } else {
						ABPRF_Layout::layout_warning_info('sale_close_msg');
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
