<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$bp_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
	$origin_time = array_key_exists('origin_time', $form_data) ? $form_data['origin_time'] : '';
	$full_infos = array_key_exists('all_info', $form_data) ? $form_data['all_info'] : [];
	$abprf_infos = $abprf_infos ?? [];
	$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
	$ticket_infos = array_key_exists('abptm_ticket_info', $abprf_infos) ? $abprf_infos['abptm_ticket_info'] : [];
	$ticket_sold = array_key_exists('booking_info', $abprf_infos) && array_key_exists('ticket', $abprf_infos['booking_info']) ? $abprf_infos['booking_info']['ticket'] : [];
	if ($bp && $dp && $bp_date && $post_id > 0 && sizeof($full_infos) > 0 && $seat_type == 'ticket_type' && sizeof($ticket_infos) > 0) { ?>
        <div class="abptm_ticket_area">
            <div class="_p_relative ticket_area">
				<?php $count = 0;
					foreach ($ticket_infos as $key => $ticket_info) {
						$icon_image = array_key_exists('icon', $ticket_info) ? $ticket_info['icon'] : '';
						$name = array_key_exists('name', $ticket_info) ? $ticket_info['name'] : '';
						$price = ABPRF_Function:: get_price($post_id, $bp, $dp, $key);
						$qty = array_key_exists('qty', $ticket_info) ? $ticket_info['qty'] : '';
						$max_qty = array_key_exists('max_qty', $ticket_info) ? $ticket_info['max_qty'] : '';
						$sold = array_key_exists($key, $ticket_sold) ? $ticket_sold[$key] : 0;
						$available = $qty - $sold - ABPRF_Function::already_in_cart($post_id, $bp, $dp, $bp_date, $name);
						$description = array_key_exists('description', $ticket_info) ? $ticket_info['description'] : '';
						$icon = $image = "";
						if ($icon_image) {
							if (preg_match('/\s/', $icon_image)) {
								$icon = $icon_image;
							} else {
								$image = $icon_image;
							}
						}
						if ($count > 0) { ?>
                            <div class="_divider_xs"></div>
						<?php }
						$count++; ?>
                        <div class="service_item _d_flex">
							<?php if ($image) { ?>
                                <div class="_w_125"><?php ABPRF_LIB_Layout::bg_image('', $image); ?></div>
							<?php } ?>
                            <div class="_fd_column_full_width">
                                <div class="_fj_between">
                                    <h6 class="_abprf_fa_center">
										<?php if ($icon) { ?>
                                            <span class="<?php echo esc_attr($icon); ?> _mar_r_xs"></span>
										<?php }
											echo esc_html($name); ?>
                                        <input type="hidden" name="ticket_types[]" value="<?php echo esc_attr($name); ?>"/>
                                        <input type="hidden" name="ticket_id[]" value="<?php echo esc_attr($key); ?>"/>
                                    </h6>
									<?php if ($available > 0) {
										$input_info = ['name' => 'ticket_qty[]', 'price' => $price, 'available' => $available, 'max_qty' => $max_qty,];
										ABPRF_LIB_Layout::quantity_input($input_info);
									} else { ?>
                                        <span class="_color_warning"> <?php esc_html_e('Not Available !', 'abprf-rental-forge'); ?></span>
									<?php } ?>
                                </div>
                                <h5 class="_abprf_color_theme">
									<?php if ($price > 0) {
										echo wp_kses_post(wc_price($price));
									} else {
										esc_html_e('Free', 'abprf-rental-forge');
									} ?>
                                </h5>
                                <p class="_abprf"><?php echo esc_html($description); ?></p>
                            </div>
                        </div>
					<?php } ?>
            </div>
        </div>
		<?php
	}
