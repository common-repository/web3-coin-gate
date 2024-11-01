<?php
/**
 * Custom helper utilities.
 *
 * @package WPTokenGate
 */

/**
 * Obtains the application configuration.
 *
 * @return array Configuration.
 */
function w3gc_get_config() {

	$config_file = WPCOINGATE_DIR_PATH . 'config.json';

	global $wp_filesystem;

	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();

	if ( ! $wp_filesystem->exists( $config_file ) ) {
		return array();
	}

	$config = json_decode( $wp_filesystem->get_contents( $config_file ), true );

	return ! is_array( $config ) ? array() : $config;

}

/**
 * Obtains the token gate verification output.
 *
 * @param int|string $gate_id - Gate id.
 * @param string     $output_type - Output type, Can either be 'success' or 'failure'.
 */
function w3gc_find_verification_output( $gate_id, $output_type = 'success' ) {

	$post                = get_post( $gate_id );
	$blocks              = parse_blocks( $post->post_content );
	$verification_output = '';

	foreach ( $blocks as $block ) {

		if ( ! isset( $block['blockName'] ) ) {
			continue;
		}

		$is_output_block        = 'web3coingate/verification-output' === $block['blockName'];
		$is_current_output_type = $block['attrs']['type'] === $output_type;

		if ( $is_output_block && $is_current_output_type ) {
			$verification_output = serialize_block( $block );
			break;
		}
	}

	return $verification_output;
}


/**
 * Sorts the verification output blocks based on
 * the minimum token balance attribute.
 *
 * @param array $blocks - List of mixed blocks, will be filtered automatically.
 * @return array - Filtered and sorted verification output blocks.
 */
function w3gc_sort_verification_outputs( $blocks ) {

	// Filtering verification blocks.
	$verification_blocks = array_filter(
		$blocks,
		function( $current_block ) {
			return isset( $current_block['blockName'] ) &&
			'web3coingate/verification-output' === $current_block['blockName'] &&
			'failure' !== $current_block['attrs']['type'];
		}
	);

	// Adding default minimumTokenCount value.
	$verification_blocks = array_map(
		function( $blocks ) {

			if ( ! isset( $blocks['attrs']['minimumTokenCount'] ) ) {
				$blocks['attrs']['minimumTokenCount'] = 1;
			}

			return $blocks;
		},
		$verification_blocks
	);

	// Sorting verification blocks .
	usort(
		$verification_blocks,
		function( $previous_block, $current_block ) {
			if ( (int) $previous_block['attrs']['minimumTokenCount'] === (int) $current_block['attrs']['minimumTokenCount'] ) {
				return 0;
			} elseif ( (int) $previous_block['attrs']['minimumTokenCount'] > (int) $current_block['attrs']['minimumTokenCount'] ) {
				return 1;
			} else {
				return -1;
			}
		}
	);

	return $verification_blocks;
}

/**
 * Finds the satisfied token gate success response based on
 * user wallet balance.
 *
 * @param int|string $connection_id - Connection id.
 * @param int        $token_balance - Token balance.
 *
 * @return string|false - True if output found, otherwise false.
 */
function w3gc_get_verification_output( $connection_id, $token_balance ) {
	$connection = get_post( $connection_id );

	// Checking if the connection exists.
	if ( is_null( $connection ) ) {
		return false;
	}

	$connection_blocks = parse_blocks( $connection->post_content );

	// Filtered and sorted blocks.
	$connection_blocks = w3gc_sort_verification_outputs( $connection_blocks );

	$verification_output = false;

	foreach ( $connection_blocks as $connection_block ) {

		// Required token balance for the current ouput block.
		$required_minimum_token_balance = (int) $connection_block['attrs']['minimumTokenCount'];

		// Checking if the current verification block matches the requirement.
		if ( $token_balance > $required_minimum_token_balance || $required_minimum_token_balance === $token_balance ) {
			$verification_output = serialize_block( $connection_block );
		}
	}

	return $verification_output;
}
