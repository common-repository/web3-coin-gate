<?php

/**
 * Frontend Class.
 *
 * @package WEB3TokenGate
 */
if ( !defined( 'ABSPATH' ) ) {
    die( 'No direct access' );
}
/**
 * Main class for handling frontend related assets and functionalities.
 */
class WEB3_Coin_Gate_Frontend
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        \add_action( 'init', array( $this, 'load_assets' ) );
    }
    
    /**
     * All frontend assets enqueuing should be done here.
     *
     * @return void
     */
    public function load_assets()
    {
        
        if ( !is_admin() ) {
            $frontend_script_path = WPCOINGATE_PLUGIN_URL . 'dist/frontend-free.js';
            \wp_enqueue_script(
                'web3-coin-gate-frontend-script',
                $frontend_script_path,
                array(
                'wp-api',
                'wp-i18n',
                'wp-components',
                'wp-element',
                'wp-editor'
            ),
                uniqid(),
                true
            );
            $rest_url = rest_url( '/wpcoingate/v1/verify' );
            // add an inline script.
            \wp_add_inline_script( 'web3-coin-gate-frontend-script', "let web3CoinGateRestUrl = `{$rest_url}`;", 'before' );
            \wp_enqueue_style(
                'web3-coin-gate-frontend-style',
                WPCOINGATE_PLUGIN_URL . 'dist/frontend.css',
                array( 'wp-components' ),
                uniqid()
            );
        }
    
    }

}
new WEB3_Coin_Gate_Frontend();