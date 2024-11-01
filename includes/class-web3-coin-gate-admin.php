<?php

/**
 * Admin Class.
 *
 * @package WEB3TokenGate
 */
if ( !defined( 'ABSPATH' ) ) {
    die( 'No direct access' );
}
/**
 * Main class for handling admin related assets and functionalities.
 */
class WEB3_Coin_Gate_Admin
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', array( $this, 'register' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
    }
    
    /**
     * All the registerations should be done here.
     *
     * @return void
     */
    public function register()
    {
        add_action( 'admin_menu', function () {
            add_menu_page(
                __( 'Web3 Coin Gate', 'web3-coin-gate' ),
                __( 'Web3 Coin Gate', 'web3-coin-gate' ),
                'manage_options',
                'web3-coin-gate',
                function () {
                ?>
							<div id="wp-verify-nft-root"></div>
						<?php 
            },
                'dashicons-admin-generic',
                99
            );
        } );
    }
    
    /**
     * All assets enqueuing should be done here
     *
     * @param string $hook_suffix - Page suffix.
     * @return void
     */
    public function load_assets( $hook_suffix )
    {
        
        if ( web3cg_fs()->is_free_plan() && 'toplevel_page_web3-coin-gate' === $hook_suffix ) {
            \wp_enqueue_script(
                'web3coingate-admin-script',
                WPCOINGATE_PLUGIN_URL . 'dist/admin.js',
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
            \wp_enqueue_style(
                'web3coingate-admin-style',
                WPCOINGATE_PLUGIN_URL . 'dist/admin.css',
                array( 'wp-components' ),
                uniqid()
            );
        }
    
    }
    
    /**
     * All block editor assets should be loaded here.
     *
     * @return void
     */
    public function load_block_editor_assets()
    {
    }

}
new WEB3_Coin_Gate_Admin();