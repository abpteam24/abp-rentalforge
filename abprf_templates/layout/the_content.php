<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
	if ($post_id > 0 && get_the_content()) {
		?>
        <div class="_section">
			<?php the_content(); ?>
        </div>
		<?php
	}