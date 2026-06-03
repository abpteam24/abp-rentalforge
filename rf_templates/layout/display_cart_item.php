<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_display_cart_item_template', function ( $cart_item = [] ) {
		$start_time      = $cart_item['start_time'] ?? '';
		$end_time        = $cart_item['end_time'] ?? '';
		$duration        = $cart_item['duration'] ?? '';
		$location        = $cart_item['location'] ?? '';
		$ticket_infos    = $cart_item['ticket_info'] ?? [];
		$additional_info = $cart_item['additional_info'] ?? [];
		$attendee_infos  = $cart_item['pass_info'] ?? [];
		//echo '<pre>';print_r($cart_item);echo '</pre>';
		?>
        <div class="abprf_area">
			<?php if ( ! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) { ?>
                <div class="_section_xs">
                    <h6 class="_abprf _color_theme"><?php esc_html_e( 'Booking Information : ', 'abprf-rental-forge' ); ?></h6>
                    <div class="_divider_xxs"></div>
                    <ul class="_abprf cart_list">
                        <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Rent Start : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( ABPRF_Function::date_format( $start_time ) ); ?></li>
                        <li><span class="fas fa-calendar-check _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Rent End : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( ABPRF_Function::date_format( $end_time ) ); ?></li>
                        <li><span class="fas fa-business-time _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Duration : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( $duration ); ?> </li>
						<?php if ( ! empty( $location ) ) { ?>
                            <li><span class="fas fa-location _mar_r_xs"></span><span class="_fs_label"><?php esc_html_e( 'Location : ', 'abprf-rental-forge' ); ?></span>&nbsp;<?php echo esc_html( ABPRF_Function::location_value( $location ) ); ?> </li>
						<?php } ?>
                    </ul>
                </div>
                <div class="_section_xs">
                    <h6 class="_abprf _color_theme"><?php esc_html_e( 'Property Information : ', 'abprf-rental-forge' ); ?></h6>
					<?php foreach ( $ticket_infos as $ticket_info ) {
						$brand   = array_key_exists( 'brand', $ticket_info ) ? $ticket_info['brand'] : '';
						$price   = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : 0;
						$price   = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
						$deposit = array_key_exists( 'deposit', $ticket_info ) ? $ticket_info['deposit'] : '';
						?>
                        <div class="_divider_xxs"></div>
                        <ul class="_abprf cart_list">
                            <li><span class="_fs_label"><?php esc_html_e( 'Name : ', 'abprf-rental-forge' ); ?></span><?php echo esc_html( $ticket_info['name'] ); ?></li>
                            <li><span class="_fs_label"><?php esc_html_e( 'Quantity : ', 'abprf-rental-forge' ); ?></span><?php echo esc_html( $ticket_info['qty'] ); ?></li>
                            <li><span class="_fs_label"><?php esc_html_e( 'Rent : ', 'abprf-rental-forge' ); ?></span><?php echo wp_kses_post( $price ); ?></li>
							<?php if ( ! empty( $deposit ) ) { ?>
                                <li><span class="_fs_label"><?php esc_html_e( 'Deposit : ', 'abprf-rental-forge' ); ?></span><?php echo wp_kses_post( wc_price( $deposit ) ); ?></li>
							<?php } ?>
							<?php								if ( ! empty( $brand ) ) {	 	?>
                                    <li><span class="_fs_label"><?php esc_html_e( 'Brand : ', 'abprf-rental-forge' ); ?></span><?php echo esc_html( ABPRF_Function::brand_value($brand) ); ?></li>
								<?php } ?>
                        </ul>
					<?php } ?>
                </div>
				<?php if ( ! empty( $additional_info ) && sizeof( $additional_info ) > 0 ) { ?>
                    <div class="_section_xs">
                        <h6 class="_abprf _color_theme"><?php esc_html_e( 'Additional Information : ', 'abprf-rental-forge' ); ?></h6>
                        <div class="_divider_xxs"></div>
                        <ul class="_abprf cart_list">
							<?php foreach ( $additional_info as $additional ) {
								if ( is_array( $additional ) && sizeof( $additional ) > 0 ) {
									$icon_image = array_key_exists( 'icon', $additional ) && $additional['icon'] ? $additional['icon'] : '';
									$name       = array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : '';
									$qty        = array_key_exists( 'qty', $additional ) ? $additional['qty'] : 1;
									$price      = array_key_exists( 'price', $additional ) ? $additional['price'] : 0;
									$price_text = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
									$ex_price   = $price > 0 ? wc_price( $price * $qty ) : __( 'FREE', 'abprf-rental-forge' );
									?>
                                    <li class="_f_wrap">
										<?php ABPRF_Layout::image_icon( $icon_image, '_mar_r_xxs' ); ?><?php echo esc_html( $name . __( ' : ', 'abprf-rental-forge' ) ); ?>
										<?php echo wp_kses_post( $price_text ) . esc_html( ' X ' ) . esc_html( $qty ) . esc_html( '  = ' ) . wp_kses_post( $ex_price ); ?>
                                    </li>
								<?php }
							} ?>
                        </ul>
                    </div>
					<?php
				}
				if ( ! empty( $attendee_infos ) && sizeof( $attendee_infos ) > 0 ) { ?>
                    <div class="_section_xs">
                        <h6 class="_abprf _color_theme"><?php esc_html_e( 'Client Information : ', 'abprf-rental-forge' ); ?></h6>
                        <div class="_divider_xxs"></div>
                        <ul class=" _abprf cart_list">
							<?php foreach ( $attendee_infos as $attendee_info ) {
								$label = array_key_exists( 'label', $attendee_info ) ? $attendee_info['label'] : '';
								$value = array_key_exists( 'value', $attendee_info ) ? $attendee_info['value'] : '';
								if ( ! empty( $label ) && ! empty( $value ) ) { ?>
                                    <li><span class="_fs_label_mar_r_xs"><?php echo esc_html( $label . __( ' : ', 'abprf-rental-forge' ) ); ?></span> <?php echo esc_html( $value ); ?></li>
									<?php
								}
							}
							?>
                        </ul>
                    </div>
				<?php } ?>
			<?php } ?>
        </div>
		<?php
	}, 10, 2 );
