<?php

/**
 * Plugin Name: Web3 Coin Gate
 * Description: Web3 Coin Gate allows website owners to restrict content to only users who verify they own a specific token in their cryptocurrency wallet.
 * Author: W3P
 * Author URI:  https://web3plugins.com/wordpress-plugins/web3-coin-gate/
 * Version: 1.0.0
 * Requires at least: 5.8.3
 * Requires PHP: 5.7
 * Text Domain: web3-coin-gate
 * Domain Path: /languages
 * Tested up to: 6.0.1
 *
 * @package WEB3CoinGate
 */
if ( !defined( 'ABSPATH' ) ) {
    die( 'No direct access' );
}
if ( !defined( 'WPCOINGATE_DIR_PATH' ) ) {
    define( 'WPCOINGATE_DIR_PATH', \plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WPCOINGATE_PLUGIN_URL' ) ) {
    define( 'WPCOINGATE_PLUGIN_URL', \plugins_url( '/', __FILE__ ) );
}
if ( !defined( 'WPCOINGATE_PLUGIN_BASE_NAME' ) ) {
    define( 'WPCOINGATE_PLUGIN_BASE_NAME', \plugin_basename( __FILE__ ) );
}
if ( is_readable( WPCOINGATE_DIR_PATH . 'lib/autoload.php' ) ) {
    include_once WPCOINGATE_DIR_PATH . 'lib/autoload.php';
}

if ( !class_exists( 'WEB3_Coin_Gate' ) ) {
    /**
     * Main plugin class
     */
    final class WEB3_Coin_Gate
    {
        /**
         * Var to make sure we only load once
         *
         * @var boolean $loaded
         */
        public static  $loaded = false ;
        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {
            
            if ( function_exists( 'web3cg_fs' ) ) {
                web3cg_fs()->set_basename( false, __FILE__ );
            } else {
                // Create a helper function for easy SDK access.
                function web3cg_fs()
                {
                    global  $web3cg_fs ;
                    
                    if ( !isset( $web3cg_fs ) ) {
                        // Include Freemius SDK.
                        require_once dirname( __FILE__ ) . '/freemius/start.php';
                        $web3cg_fs = fs_dynamic_init( array(
                            'id'             => '11698',
                            'slug'           => 'web3-coin-gate',
                            'type'           => 'plugin',
                            'public_key'     => 'pk_c21bc8f82905ec90bee84ce44b908',
                            'is_premium'     => false,
                            'premium_suffix' => 'Premium',
                            'has_addons'     => false,
                            'has_paid_plans' => true,
                            'menu'           => array(
                            'slug'    => 'web3-coin-gate',
                            'support' => false,
                        ),
                            'is_live'        => true,
                        ) );
                    }
                    
                    return $web3cg_fs;
                }
                
                // Init Freemius.
                web3cg_fs();
                // Signal that SDK was initiated.
                do_action( 'web3cg_fs_loaded' );
            }
            
            require_once WPCOINGATE_DIR_PATH . 'includes/helpers.php';
            require_once WPCOINGATE_DIR_PATH . 'includes/views/clipboard-field.php';
            require_once WPCOINGATE_DIR_PATH . 'includes/class-web3-coin-gate-admin.php';
            require_once WPCOINGATE_DIR_PATH . 'includes/class-web3-coin-gate-frontend.php';
            require_once WPCOINGATE_DIR_PATH . 'includes/rest-api/rest.php';
            require_once WPCOINGATE_DIR_PATH . 'includes/class-web3-coin-gate-settings.php';
            if ( web3cg_fs()->is_free_plan() ) {
                require_once WPCOINGATE_DIR_PATH . 'includes/class-web3-coin-gate-shortcode.php';
            }
        }
    
    }
    new WEB3_Coin_Gate();
}
