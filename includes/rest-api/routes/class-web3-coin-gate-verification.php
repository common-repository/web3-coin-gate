<?php
/**
 * Redirect route for verifying tokens.
 *
 * @package WEB3CoinGate
 */

register_rest_route(
	'wpcoingate/v1',
	'/verify',
	array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => function( WP_REST_Request $request ) {

			$has_connection_id = $request->has_param( 'connection_id' );
			$connection_id = $request->get_param( 'connection_id' );
			$post_id = $request->get_param( 'post_id' );

			$has_post_id = $request->has_param( 'post_id' );

			if ( ! $request->has_param( 'signature' ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Missing signature parameter.',
					),
					400
				);
			}

			$signature = $request->get_param( 'signature' );

			$token_address = get_option( 'web3-coin-gate-token-address' ); // nft token.
			$chain = 'ethereum';

			if ( $has_connection_id ) {
				$token_address = get_post_meta( $connection_id, 'coingate-token-address', true );
				$chain = get_post_meta( $connection_id, 'coingate-chain-id', true );
			}

			if ( empty( $token_address ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'No token address set.',
					),
					404
				);
			}

			$app_config         = w3gc_get_config();
			$verification_route = $app_config['backend_url'] . '/verify-ownership';

			$request = wp_remote_post(
				$verification_route,
				array(
					'headers'     => array( 'Content-Type' => 'application/json' ),
					'blocking'    => true,
					'timeout'     => 20,
					'body'        => wp_json_encode(
						array(
							'signature'     => $signature,
							'token_address' => $token_address,
							'chain'         => web3cg_fs()->can_use_premium_code__premium_only() ? $chain : 'ethereum',
						)
					),
					'data_format' => 'body',
				)
			);

			$response_data = json_decode( wp_remote_retrieve_body( $request ), true );
			$current_token_balance = isset( $response_data['currentBalance'] ) ? $response_data['currentBalance'] : 0;

			$response_code = wp_remote_retrieve_response_code( $request );
			$is_success = 200 === $response_code;

			$success_text = get_option( 'web3-coin-gate-success-text' );
			$failure_text = get_option( 'web3-coin-gate-failure-text' );

			if ( $has_connection_id ) {
				$success_text = w3gc_get_verification_output( $connection_id, $current_token_balance );
				$failure_text = w3gc_find_verification_output( $connection_id, 'failure' );

				if ( false === $success_text ) {
					$is_success = false;
				}
			} else {
				// Verifying token balance on free.
				$required_token_balance = get_option( 'web3-coin-gate-minimum-token-count' );

				if ( $current_token_balance < $required_token_balance ) {
					$is_success = false;
				}
			}

			if ( $has_post_id ) {
				// Checking output type.
				$output_type = get_post_meta( $post_id, 'coingate-success-output-type', true );

				if ( 'page-content' === $output_type ) {
					$output_post = get_post( $post_id );

					$success_text = apply_filters(
						'the_content',
						$output_post->post_content
					);

				}
			}

			if ( $is_success ) {
				return new WP_REST_Response(
					array(
						'success' => true,
						'message' => do_blocks( $success_text ),
					),
					200
				);
			} else {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => do_blocks( $failure_text ),
					),
					500
				);
			}

		},
	)
);

