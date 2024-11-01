<?php
/**
 * All the shortcode registeration should be done here.
 *
 * @package WEB3CoinGate
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access' );
}

/**
 * Main class for handling shortcode.
 */
class WEB3_Coin_Gate_Shortcode {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'coin-gate-verify-app', array( $this, 'render_application' ) );
		add_shortcode( 'coin-gate-output', array( $this, 'render_verification_placeholder' ) );
	}

	/**
	 * Renders the verification application.
	 *
	 * @return string
	 */
	public function render_application() {

		return "
		<div class='wpcoingate-verification-application'>
			<button class='wpcoingate-verification-connect'>Connect Wallet</button>
			<p class='wpcoingate-wallet-connected-placeholder'></p>
		</div>";

	}


	/**
	 * Renders the verification placeholder.
	 *
	 * @return string
	 */
	public function render_verification_placeholder() {
		return "
			<p class='wpcoingate-verification-placeholder unloaded'></p>
		";
	}

}


new WEB3_Coin_Gate_Shortcode();
