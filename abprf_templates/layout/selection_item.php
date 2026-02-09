<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
?>
    <div class="transport_selection _mar_b">
        <table class="_abprf">
            <thead>
            <tr>
                <th><?php esc_html_e('Seat', 'abprf-rental-forge'); ?></th>
                <th><?php esc_html_e('Price', 'abprf-rental-forge'); ?></th>
                <th class="_text_center"><?php esc_html_e('Action', 'abprf-rental-forge'); ?></th>
            </tr>
            </thead>
            <tbody class="insert_item">
            </tbody>
            <tfoot>
            <tr class="_fs_h5">
                <th><?php esc_html_e('Sub-Total', 'abprf-rental-forge'); ?></th>
                <th class="_color_theme abptm_sub_total"></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        <div class="abprf_d_none">
            <table class="_abprf">
                <tbody class="abprf_hidden_item">
                <tr class="abprf_delete_area " data-type="" data-name="">
                    <th class="seat_name"></th>
                    <th class="seat_price"></th>
                    <th>
                        <div class="_all_center"><?php ABPRF_LIB_Layout::button_delete('abptm_seat_remove'); ?></div>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php