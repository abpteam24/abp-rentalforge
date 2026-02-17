<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$origin_place = array_key_exists('origin_place', $form_data) ? $form_data['origin_place'] : '';
	$bp_time = array_key_exists('bp_time', $form_data) ? $form_data['bp_time'] : '';
	$dp_time = array_key_exists('dp_time', $form_data) ? $form_data['dp_time'] : '';
	$origin_time = array_key_exists('origin_time', $form_data) ? $form_data['origin_time'] : '';
	$full_infos = array_key_exists('all_info', $form_data) ? $form_data['all_info'] : [];
	$abprf_infos = $abprf_infos ?? [];
	$display_total_seat = array_key_exists('display_total_seat', $abprf_infos) ? $abprf_infos['display_total_seat'] : 'on';
	$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
	$seat_ticket_key = $seat_type == 'seat_plan' ? 'seat' : 'ticket';
	$sold_seats = sizeof(array_key_exists('booking_info', $abprf_infos) && array_key_exists('seat', $abprf_infos['booking_info']) ? $abprf_infos['booking_info']['seat'] : []);
	$total_seat = array_key_exists('total_seat', $abprf_infos) ? $abprf_infos['total_seat'] : '';
	$available_seat = $total_seat - $sold_seats;
?>
    <div class="transport_route_details_info">
        <ul class="_abprf_list">
            <li>
                <span class="fas fa-map-marker-alt _mar_r_xs"></span>
                <span class="_fs_label"><?php esc_html_e('Departure : ', 'abprf-rental-forge'); ?></span>&nbsp;
				<?php echo esc_html($bp) . ' ' . esc_html($bp_time ? ' (' . ABPRF_Function::date_format($bp_time, 'full') . ' )' : ''); ?>
            </li>
            <li>
                <span class="fas fa-map-marker-alt _mar_r_xs"></span>
                <span class="_fs_label"><?php esc_html_e('Arrival : ', 'abprf-rental-forge'); ?></span>&nbsp;
				<?php echo esc_html($dp) . ' ' . esc_html($dp_time ? ' (' . ABPRF_Function::date_format($dp_time, 'full') . ' )' : ''); ?>
            </li>
			<?php if ($origin_place !== $bp) { ?>
                <li>
                    <span class="fas fa-map-marker-alt _mar_r_xs"></span>
                    <span class="_fs_label"><?php esc_html_e('Starting point : ', 'abprf-rental-forge'); ?></span>&nbsp;
					<?php echo esc_html($origin_place) . ' ' . esc_html($origin_time ? ' (' . ABPRF_Function::date_format($origin_time, 'full') . ' )' : ''); ?>
                </li>
			<?php } ?>
			<?php if ($display_total_seat == 'on') {
				if ($seat_type == 'seat_plan') { ?>
                    <li>
                        <span class="_fs_h6_mar_r_xs fas fa-chair"></span>
                        <span class="_fs_label"><?php esc_html_e('Available Seat : ', 'abprf-rental-forge'); ?></span>&nbsp;
						<?php echo esc_html($available_seat . '/' . $total_seat); ?>
                    </li>
				<?php } else { ?>
                    <li>
                        <span class="_fs_h6_mar_r_xs fas fa-ticket-alt"></span>
                        <span class="_fs_label"><?php esc_html_e('Available Ticket : ', 'abprf-rental-forge'); ?></span>&nbsp;
						<?php echo esc_html($available_seat . '/' . $total_seat); ?>
                    </li>
				<?php }
			} ?>
			<?php if ($seat_type == 'seat_plan') { ?>
                <li>
                    <span class="fa fa-tag _mar_r_xs"></span>
                    <span class="_fs_label"><?php esc_html_e('Price : ', 'abprf-rental-forge'); ?></span>
					<?php echo wp_kses_post(wc_price(ABPRF_Function::get_price($post_id, $bp, $dp))); ?>
                </li>
			<?php } ?>
            <li>
                <span class="fas fa-business-time _mar_r_xs"></span>
                <span class="_fs_label"><?php esc_html_e('Approximate Time : ', 'abprf-rental-forge'); ?></span>
				<?php echo esc_html(ABPRF_Function::get_date_time_difference($bp_time, $dp_time)); ?>
            </li>
        </ul>
    </div>
<?php
