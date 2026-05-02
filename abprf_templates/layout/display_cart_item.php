<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_display_cart_item_template', function ( $cart_item = [] ) {
		$start_time      = array_key_exists( 'start_time', $cart_item ) ? $cart_item['start_time'] : '';
		$end_time        = array_key_exists( 'end_time', $cart_item ) ? $cart_item['end_time'] : '';
		$duration        = array_key_exists( 'duration', $cart_item ) ? $cart_item['duration'] : '';
		$ticket_infos    = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : [];
		$additional_info = array_key_exists( 'additional_info', $cart_item ) ? $cart_item['additional_info'] : [];
		$attendee_infos  = array_key_exists( 'pass_info', $cart_item ) ? $cart_item['pass_info'] : [];
		$ticket_count    = 0;
		?>
        <div class="abprf_area">
            <div class="_section_xs">
                <h6 class="_abprf _color_theme _border_b"><?php esc_html_e( 'Booking Information : ', 'abprf-rental-forge' ); ?></h6>
                <ul class="_abprf cart_list">
                    <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Rent Start Time : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( ABPRF_Function::date_format( $start_time, 'full' ) ); ?></li>
                    <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Rent End Time : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( ABPRF_Function::date_format( $end_time, 'full' ) ); ?></li>
                    <li><span class="fas fa-business-time _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Duration : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( $duration ); ?> </li>
                </ul>
            </div>
			<?php if (! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) { ?>
                <div class="_section_xs">
                    <h6 class="_abprf _color_theme _border_b"><?php esc_html_e( 'Property Information : ', 'abprf-rental-forge' ); ?></h6>
					<?php foreach ( $ticket_infos as $ticket_info ) {
						if ( $ticket_count > 0 ) { ?>
                            <div class="_divider_xs"></div>
						<?php } ?>
                        <ul class="_abprf cart_list">
                            <li><span class="_fs_label"><?php esc_html_e( 'Name : ', 'abprf-rental-forge' ); ?></span><?php echo esc_html( $ticket_info['name'] ); ?></li>
                            <li><span class="_fs_label"><?php esc_html_e( 'Quantity : ', 'abprf-rental-forge' ); ?></span><?php echo esc_html( $ticket_info['qty'] ); ?></li>
                            <li><span class="_fs_label"><?php esc_html_e( 'Price : ', 'abprf-rental-forge' ); ?></span><?php echo wp_kses_post( wc_price( $ticket_info['price'] ) ) . esc_html( ' X ' ) . esc_html( $ticket_info['qty'] ) . esc_html( '  = ' ) . wp_kses_post( wc_price( $ticket_info['price'] * $ticket_info['qty'] ) ); ?></li>
							<?php $ticket_count ++; ?>
                        </ul>
					<?php }
						if ( ! empty( $additional_info ) && sizeof( $additional_info ) > 0 ) { ?>
                            <div class="_divider_xs"></div>
                            <h6 class="_abprf _color_theme _border_b"><?php esc_html_e( 'Additional Information : ', 'abprf-rental-forge' ); ?></h6>
                            <ul class="_abprf cart_list">
								<?php foreach ( $additional_info as $additional ) {
									if ( is_array( $additional ) && sizeof( $additional ) > 0 ) {
										$icon_image = array_key_exists( 'icon', $additional ) && $additional['icon'] ? $additional['icon'] : '';
										$name       = array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : ''; ?>
                                        <li>
                                            <span class="_fs_label_mar_r_xs"><?php ABPRF_Layout::image_icon( $icon_image ); ?><?php echo esc_html( $name . __( ' : ', 'abprf-rental-forge' ) ); ?></span>
											<?php echo wp_kses_post( wc_price( $additional['price'] ) ) . esc_html( ' X ' ) . esc_html( $additional['qty'] ) . esc_html( '  = ' ) . wp_kses_post( wc_price( $additional['price'] * $additional['qty'] ) ); ?>
                                        </li>
									<?php }
								} ?>
                            </ul>
							<?php
						}
						if ( ! empty( $attendee_infos ) && sizeof( $attendee_infos ) > 0 ) { ?>
                            <div class="_divider_xs"></div>
                            <h6 class="_abprf _color_theme _border_b"><?php esc_html_e( 'Client Information : ', 'abprf-rental-forge' ); ?></h6>
                            <ul class=" _abprf cart_list">
								<?php foreach ( $attendee_infos as $attendee_info ) {
									$label = array_key_exists( 'label', $attendee_info ) ? $attendee_info['label'] : '';
									$value = array_key_exists( 'value', $attendee_info ) ? $attendee_info['value'] : '';
									if (  ! empty( $label) &&  ! empty( $value) ) { ?>
                                        <li><span class="_fs_label_mar_r_xs"><?php echo esc_html( $label . __( ' : ', 'abprf-rental-forge-pro' ) ); ?></span> <?php echo esc_html( $value ); ?></li>
										<?php
									}
								}
								?>
                            </ul>
						<?php } ?>
                </div>
			<?php } ?>
        </div>
		<?php
	}, 10, 2 );
