<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : '';
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$_j_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
	if ($bp && $dp && $_j_date && $post_id > 0) {
		$abprf_infos = ABPRF_Function::get_all_meta($post_id);
		$sale_continue = array_key_exists('sale_continue', $abprf_infos) ? $abprf_infos['sale_continue'] : 'on';
		$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
		if ($sale_continue == 'on') {
			$full_infos = ABPRF_Function::get_route_full_info($post_id, $bp, $_j_date);
			if (sizeof($full_infos) > 0) {
				$origin_place = current($full_infos)['stop'];
				$origin_time = current($full_infos)['time'];
				$bp_time = $dp_time = '';
				$drop_info = $pick_info = [];
				foreach ($full_infos as $full_info) {
					if ($full_info['stop'] == $bp) {
						$bp_time = $full_info['time'];
						$pick_info = array_key_exists('pickup_infos', $full_info) ? $full_info['pickup_infos'] : [];
					}
					if ($full_info['stop'] == $dp) {
						$dp_time = $full_info['time'];
						$drop_info = array_key_exists('drop_infos', $full_info) ? $full_info['drop_infos'] : [];
					}
				}
				$form_data['bp_time'] = $bp_time;
				$form_data['dp_time'] = $dp_time;
				$form_data['origin_place'] = $origin_place;
				$form_data['origin_time'] = $origin_time;
				$form_data['pickup_infos'] = $pick_info;
				$form_data['drop_infos'] = $drop_info;
				$form_data['all_info'] = $full_infos;
				$abprf_infos['booking_info']=ABPRF_Query::get_sold_info($post_id,$bp,$dp,$origin_time,$seat_type);
                //echo '<pre>';print_r($abprf_infos);echo '</pre>';
				?>
                <div class="abprf_registration_item _section_xs">
                    <form class="" action="" method="post">
                        <input type="hidden" name="equipment_id" value="<?php  echo esc_attr($post_id); ?>"/>
                        <input type="hidden" name='origin_place' value='<?php  echo esc_attr($origin_place); ?>'/>
                        <input type="hidden" name='origin_time' value='<?php echo esc_attr($origin_time); ?>'/>
                        <input type="hidden" name='transport_bp' value='<?php echo  esc_attr($bp); ?>'/>
                        <input type="hidden" name='bp_time' value='<?php  echo esc_attr($bp_time); ?>'/>
                        <input type="hidden" name='transport_dp' value='<?php  echo esc_attr($dp); ?>'/>
                        <input type="hidden" name='dp_time' value='<?php  echo esc_attr($dp_time); ?>'/>
                        <input type="hidden" name='seat_type' value='<?php echo  esc_attr($seat_type); ?>'/>
                        <input type="hidden" name='display_single_form' value='<?php  echo esc_attr(array_key_exists('display_single_form', $abprf_infos) ? $abprf_infos['display_single_form'] : 'on'); ?>'/>
                        <input type="hidden" name='checkout_system' value='<?php  echo esc_attr((is_admin() && str_contains(wp_get_referer(), 'add_order')) ? 'default' : ABPRF_Function::get_options('abprf_configuration', 'checkout_system', 'default')); ?>'/>
						<?php ABPRF_Layout::hidden_search_form($form_data); ?>
						<?php wp_nonce_field('abprf_registration_nonce'); ?>
                        <div class="_abprf_row">
                            <div class="_col_6_12_800">
								<?php
									if ($seat_type == 'seat_plan') {
										do_action('abptm_seat_plan', $abprf_infos, $form_data);
									} else {
										do_action('abptm_ticket', $abprf_infos, $form_data);
									}
								?>
                            </div>
                            <div class="_col_6_12_800 seat_details_area">
                                <div>
									<?php
										do_action('abprf_admin_order', $post_id);
										do_action('abptm_details_info', $abprf_infos, $form_data);
										if ($seat_type == 'seat_plan') {
											do_action('abptm_selection_item');
										}
										do_action('abptm_pickup_drop', $abprf_infos, $form_data);
										do_action('abprf_client_form', $abprf_infos, $form_data); ?>
                                </div>
								<?php do_action('abprf_total_price', $post_id); ?>
                            </div>
                        </div>
                    </form>
					<?php do_action('abprf_hidden_form', $abprf_infos, $form_data); ?>
                </div>
				<?php
			}
		} else {
			ABPRF_Layout::layout_warning_info('sale_close_msg');
		}
	} else {
		ABPRF_Layout::layout_warning_info('search_get_wrong_data_info');
	}
