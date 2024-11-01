<?php

/**
 * Application Routes
 */

add_action(
	'rest_api_init',
	function () {

		remove_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );

		add_filter(
			'render_block',
			function( $block_content, $block ) {
				if ( $block['blockName'] === 'core/group' ) {
					return $block_content;
				}

				if ( function_exists( 'wp_render_layout_support_flag' ) ) {
					return wp_render_layout_support_flag( $block_content, $block );
				}

				return $block_content;
			},
			10,
			2
		);

		require_once WPCOINGATE_DIR_PATH . 'includes/rest-api/routes/class-web3-coin-gate-verification.php';
	}
);

