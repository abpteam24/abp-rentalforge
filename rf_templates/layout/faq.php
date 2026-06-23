<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_faq_template', function ( $abprf_infos = [], $type = '' ) {
		if ( ABPRF_Function::on_off( 'faq' ) ) {
			$faq_infos = [];
			if ( ! empty( $abprf_infos ) ) {
				$display           = $abprf_infos['display_faq'] ?? 'on';
				$active_global_faq = $abprf_infos['active_global_faq'] ?? 'on';
				if ( $display === 'on' ) {
					$faq_infos = ( $active_global_faq === 'on' )
						? ABPRF_Function::get_option( 'abprf_faqs' )
						: ( $abprf_infos['abprf_faqs'] ?? [] );
				}
			} elseif ( $type === 'global' ) {
				$faq_infos = ABPRF_Function::get_option( 'abprf_faqs' );
			}
			if ( empty( $faq_infos ) || ! is_array( $faq_infos ) ) {
				return;
			}
			?>
            <div class="_abp_panel faq_area">
                <div class="_panel_head">
                    <h4 class="_abprf">
                        <span class="_mar_r_xxs">❓</span>
						<?php esc_html_e( 'Frequently Asked Questions', 'abp-rentalforge' ); ?>
                    </h4>
                </div>
                <div class="_panel_body_xs faq_list">
					<?php
						foreach ( $faq_infos as $faq ) {
							$title       = $faq['title'] ?? '';
							$description = $faq['des'] ?? '';
							if ( $description !== '' ) {
								$description = html_entity_decode( $description );
							}
							if ( empty( $title ) ) {
								continue;
							}
							?>
                            <div class="faq_item">
                                <div class="faq_question faq_target">
                                    <h5 class="_abprf"><?php echo esc_html( $title ); ?></h5>
                                    <span class="faq_icon"></span>
                                </div>
                                <div class="faq_answer">
                                    <div class="faq_answer_content">
										<?php
											// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
											echo wp_kses_post( apply_filters( 'the_content', $description ) );
										?>
                                    </div>
                                </div>
                            </div>
							<?php
						}
					?>
                </div>
            </div>
			<?php
		}
	}, 10, 2 );