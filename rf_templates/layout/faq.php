<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_faq_template', function ( $abprf_infos = [], $type = '' ) {
		$faq_infos = [];
		if ( ! empty( $abprf_infos ) ) {
			$display           = array_key_exists( 'display_faq', $abprf_infos ) ? $abprf_infos['display_faq'] : 'on';
			$_faq              = array_key_exists( 'abprf_faqs', $abprf_infos ) ? $abprf_infos['abprf_faqs'] : [];
			$active_global_faq = array_key_exists( 'active_global_faq', $abprf_infos ) ? $abprf_infos['active_global_faq'] : 'on';
			$faqs              = $active_global_faq == 'on' ? ABPRF_Function::get_option( 'abprf_faqs' ) : $_faq;
			$faq_infos         = $display == 'on' ? $faqs : $faq_infos;
		} else {
			$faq_infos = $type == 'global' ? ABPRF_Function::get_option( 'abprf_faqs' ) : $faq_infos;
		}
		if ( ! empty( $faq_infos ) && sizeof( $faq_infos ) > 0 ) {
			?>
            <div class="_abp_panel faq_area">
                <div class="_panel_head">
                    <h4 class="_abprf"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'Frequently Asked Questions', 'abp-rentalforge' ); ?></h4>
                </div>
                <div class="_panel_body_xs faq_list">
					<?php
						foreach ( $faq_infos as $faq ) {
							$title       = array_key_exists( 'title', $faq ) ? $faq['title'] : '';
							$description = array_key_exists( 'des', $faq ) ? $faq['des'] : '';
							$description = $description ? html_entity_decode( $description ) : '';
							if ( ! empty( $title ) ) {
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
												echo wp_kses_post( apply_filters( 'the_content', $description ) ); ?>
                                        </div>
                                    </div>
                                </div>
								<?php
							}
						}
					?>
                </div>
            </div>
			<?php
		}
	}, 10, 3 );
