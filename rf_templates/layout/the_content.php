<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_content_template', function ( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );
		if ( $post_id > 0 && ! empty( $content ) ) {
			?>
            <div class="the_post_content">
				<?php echo wp_kses_post( wpautop( $content ) ); ?>
            </div>
			<?php
		}
	}, 10, 2 );