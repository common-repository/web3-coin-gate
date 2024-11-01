<?php
/**
 * All the settings registeration should be done here.
 *
 * @package WEB3TokenGate
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access' );
}

/**
 * Main class representation for settings.
 */
class WEB3_Coin_Gate_Settings {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'plugin_action_links_' . WPCOINGATE_PLUGIN_BASE_NAME, array( $this, 'merge_link' ) );
	}

	/**
	 * Merges the settings link for the settings homepage.
	 *
	 * @param string[] $links - Existing links.
	 *
	 * @return string[] - Updated Links
	 */
	public function merge_link( $links ) {

		if ( web3cg_fs()->is_free_plan() ) {
			// Build and escape the URL.
			$settings_url = esc_url(
				add_query_arg(
					'page',
					'web3-coin-gate',
					get_admin_url() . 'admin.php'
				)
			);

			$upgrade_url = web3cg_fs()->get_upgrade_url();

			$settings_link = "<a href='$settings_url'>" . __( 'Settings' ) . '</a>';
			$upgrade_link  = "<a href='$upgrade_url' style='color:red; font-weight: bold;'>" . __( 'Go Pro' ) . '</a>';

			// Appending the new link.
			array_push(
				$links,
				$settings_link
			);

			array_push(
				$links,
				$upgrade_link
			);
		}

		return $links;
	}

	/**
	 * Register setting fields.
	 *
	 * @return void
	 */
	public function register() {

		register_setting(
			'web3-coin-gate',
			'web3-coin-gate-token-address',
			array(
				'default'           => '',
				'show_in_rest'      => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'web3-coin-gate',
			'web3-coin-gate-success-text',
			array(
				'default'           => 'Verified! You have the token',
				'show_in_rest'      => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'web3-coin-gate',
			'web3-coin-gate-failure-text',
			array(
				'default'           => 'Oops! You don\'t have the token',
				'show_in_rest'      => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'web3-coin-gate',
			'web3-coin-gate-minimum-token-count',
			array(
				'default'           => 1,
				'show_in_rest'      => true,
				'type'              => 'number',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

	}

}

new WEB3_Coin_Gate_Settings();
