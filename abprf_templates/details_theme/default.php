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
		$sale_continue = array_key_exists('sale_continue', $abprf_infos) ? $abprf_infos['sale_continue'] : 'on';
		$form_data = ABPRF_Function::get_form_data($abprf_infos);
		?>
        <div id="abprf_area" class="abprf_area default_details_page">
            <div class="abprf_container">
                <div class="_abprf_row details_page_top">
                    <div class="_col_4_12_800_bg_border_all_center"> <?php do_action('abprf_slider', $post_id); ?></div>
                    <div class="details_page_info _col_4_12_800 _all_center">
                        <div>
							<?php do_action('abprf_title', $abprf_infos); ?>
                            <div class="_fd_column_mar_t_xxs title_details">
								<?php do_action('abprf_category', $abprf_infos); ?>
								<?php do_action('abprf_organizer', $abprf_infos); ?>
								<?php do_action('abprf_capacity', $abprf_infos); ?>
                            </div>
                            <div class="_divider_xs"></div>
                            <div class="_f_wrap">
								<?php do_action('abptm_route_direction', $abprf_infos); ?>
                            </div>
                        </div>
                    </div>
                    <div class="_col_4_12_800_bg_border_all_center">
						<?php if ($sale_continue == 'on') {
							do_action('abptm_search_form', $abprf_infos, ['form' => 'column'], $form_data);
						} else {
							ABPRF_LIB_Layout::layout_warning_info('sale_close_msg');
						}
						?>
                    </div>
                </div>
				<?php do_action('abptm_the_content', $abprf_infos); ?>
                <div class="_abprf_row">
					<?php if ($sale_continue == 'on') { ?>
                        <div class=" abprf_rental_result">
							<?php ABPRF_Layout::transport_list($form_data); ?>
                        </div>
					<?php } else {
						ABPRF_LIB_Layout::layout_warning_info('sale_close_msg');
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
