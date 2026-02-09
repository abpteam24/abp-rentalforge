<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$cart_item = $cart_item ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $cart_item) ? $cart_item['post_id'] : 0;
	$display_single_additional = ABPRF_LIB_Function::get_post_info($post_id, 'display_single_additional', 'on');
	$origin = array_key_exists('origin', $cart_item) ? $cart_item['origin'] : '';
	$origin_time = array_key_exists('origin_time', $cart_item) ? $cart_item['origin_time'] : '';
	$bp = array_key_exists('bp', $cart_item) ? $cart_item['bp'] : '';
	$bp_time = array_key_exists('bp_time', $cart_item) ? $cart_item['bp_time'] : '';
	$dp = array_key_exists('dp', $cart_item) ? $cart_item['dp'] : '';
	$dp_time = array_key_exists('dp_time', $cart_item) ? $cart_item['dp_time'] : '';
	$seat_type = array_key_exists('seat_type', $cart_item) ? $cart_item['seat_type'] : '';
	$ticket_infos = array_key_exists('ticket_info', $cart_item) ? $cart_item['ticket_info'] : [];
	$pickup = array_key_exists('pickup', $cart_item) ? $cart_item['pickup'] : '';
	$drop = array_key_exists('drop', $cart_item) ? $cart_item['drop'] : '';
	$additional_info = array_key_exists('additional_info', $cart_item) ? $cart_item['additional_info'] : [];
	$ticket_count = 0;
?>
    <div class="abprf_area">
        <div class="_section_xs">
            <ul class="_abprf cart_list">
                <li><span class="fas fa-map-marker-alt _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Departure : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html($bp); ?></li>
                <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Departure Time : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html(ABPRF_LIB_Function::date_format($bp_time, 'full')); ?></li>
                <li><span class="fas fa-map-marker-alt _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Arrival : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html($dp); ?> </li>
                <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Arrival Time : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html(ABPRF_LIB_Function::date_format($dp_time, 'full')); ?></li>
				<?php if ($bp !== $origin) { ?>
                    <li><span class="fas fa-map-marker-alt _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Starting point : ', 'abprf-rental-forge'); ?>&nbsp;</span> <?php echo esc_html($origin); ?> </li>
                    <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Starting Time : ', 'abprf-rental-forge'); ?>&nbsp;</span> <?php echo esc_html(ABPRF_LIB_Function::date_format($origin_time, 'full')); ?> </li>
				<?php }
					if ($pickup) { ?>
                        <li><span class="fas fa-map-pin _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Pickup Point : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html($pickup); ?></li>
					<?php }
					if ($drop) { ?>
                        <li><span class="fas fa-map-pin _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Drop-off Point : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html($drop); ?></li>
					<?php } ?>
                <li><span class="fas fa-business-time _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e('Approximate Time : ', 'abprf-rental-forge'); ?></span>&nbsp;<?php echo esc_html(ABPRF_LIB_Function::get_date_time_difference($bp_time, $dp_time)); ?></li>
            </ul>
        </div>
		<?php if (sizeof($ticket_infos) > 0) { ?>
            <div class="_section_xs">
				<?php foreach ($ticket_infos as $key => $ticket_info) {
					if ($ticket_count > 0) { ?>
                        <div class="_divider_xs"></div>
					<?php } ?>
                    <ul class="_abprf cart_list">
                        <li>
							<?php if ($seat_type == 'seat_plan') { ?>
                                <span class="_fs_label_mar_r_xs fas fa-chair"></span><span class="_fs_label"><?php echo esc_html(__('Seat : ', 'abprf-rental-forge') . ($ticket_info['dd'] ? __('Upper Deck', 'abprf-rental-forge') : '')); ?></span>
							<?php } else { ?>
                                <span class="_fs_label_mar_r_xs fas fa-ticket-alt"></span><span class="_fs_label"><?php esc_html_e('Ticket : ', 'abprf-rental-forge'); ?></span>
							<?php } ?>
                            &nbsp;<span class="_fs_label_mar_r_xs"><?php echo esc_html($ticket_info['seat'] . ' ' . ABPRF_Function::get_seat_type($ticket_info['type'])); ?></span>
                            <span><?php echo esc_html(' ( ') . wp_kses_post(wc_price($ticket_info['price'])) . esc_html(' X ') . esc_html($ticket_info['qty']) . esc_html(' ) = ') . wp_kses_post(wc_price($ticket_info['price'] * $ticket_info['qty'])); ?></span>
                        </li>
						<?php
							if ($display_single_additional != 'on' && sizeof($additional_info) > 0 && array_key_exists($key, $additional_info)) {
								$additional_infos = $additional_info[$key];
								if (sizeof($additional_infos) > 0) {
									foreach ($additional_infos as $additional) {
										if (is_array($additional) && sizeof($additional) > 0) {
											$icon_image = array_key_exists('icon', $additional) && $additional['icon'] ? $additional['icon'] : '';
											$icon = $image = "";
											if ($icon_image) {
												if (preg_match('/\s/', $icon_image)) {
													$icon = $icon_image;
												} else {
													$image = $icon_image;
												}
											}
											$name = array_key_exists('name', $additional) && $additional['name'] ? $additional['name'] : ''; ?>
                                            <li>
												<?php if ($image) { ?>
                                                    <div class="_w_25"><?php ABPRF_LIB_Layout::bg_image('', $image); ?></div><?php }
													if ($icon) { ?><span class="<?php echo esc_attr($icon); ?> _mar_r_xs"></span><?php } ?>
                                                <span class="_fs_label_mar_r_xs"><?php echo esc_html($name . __(' : ', 'abprf-rental-forge')); ?></span>
												<?php echo wp_kses_post(wc_price($additional['price'])) . esc_html(' X ') . esc_html($additional['qty']) . esc_html('  = ') . wp_kses_post(wc_price($additional['price'] * $additional['qty'])); ?>
                                            </li>
										<?php }
									}
								}
							}
							do_action('abptm_cart_display_traveller_info', $cart_item, $key);
							$ticket_count++; ?>
                    </ul>
				<?php }
					if ($display_single_additional == 'on' && sizeof($additional_info) > 0) {
						$additional_infos = current($additional_info);
						if (sizeof($additional_infos) > 0) { ?>
                            <div class="_divider_xs"></div>
                            <ul class="_abprf cart_list">
								<?php foreach ($additional_infos as $additional) {
									if (is_array($additional) && sizeof($additional) > 0) {
										$icon_image = array_key_exists('icon', $additional) && $additional['icon'] ? $additional['icon'] : '';
										$icon = $image = "";
										if ($icon_image) {
											if (preg_match('/\s/', $icon_image)) {
												$icon = $icon_image;
											} else {
												$image = $icon_image;
											}
										}
										$name = array_key_exists('name', $additional) && $additional['name'] ? $additional['name'] : ''; ?>
                                        <li>
											<?php if ($image) { ?>
                                                <div class="_w_25"><?php ABPRF_LIB_Layout::bg_image('', $image); ?></div><?php }
												if ($icon) { ?><span class="<?php echo esc_attr($icon); ?> _mar_r_xs"></span><?php } ?>
                                            <span class="_fs_label_mar_r_xs"><?php echo esc_html($name . __(' : ', 'abprf-rental-forge')); ?></span>
											<?php echo wp_kses_post(wc_price($additional['price'])) . esc_html(' X ') . esc_html($additional['qty']) . esc_html('  = ') . wp_kses_post(wc_price($additional['price'] * $additional['qty'])); ?>
                                        </li>
									<?php }
								} ?>
                            </ul>
						<?php }
					}
					do_action('abptm_cart_display_traveller_info', $cart_item);
				?>
            </div>
		<?php } ?>
    </div>
<?php
