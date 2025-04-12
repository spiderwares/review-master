<?php
/**
 * Plugin Name:       Review Master
 * Description:       Review Master - Effortlessly collect and manage ratings across multiple categories with our powerful plugin. Customize rating criteria, display detailed reviews, and enhance user engagement by showcasing category-specific feedback on your WordPress site. Perfect for businesses and content creators who want to gather comprehensive insights from their audience.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            SpiderWares
 * Author URI:        https://spiderwares.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       review-master-pro
 */

if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! defined( 'RMPRO_PLUGIN_FILE' ) ) :
	define( 'RMPRO_PLUGIN_FILE', __FILE__ );
endif;

if( ! defined( 'RMPRO_PLUGIN_BASENAME' ) ) :
	define( 'RMPRO_PLUGIN_BASENAME', plugin_basename( RMPRO_PLUGIN_FILE ) );
endif;

if ( ! defined( 'RMPRO_VERSION' ) ) :
	define( 'RMPRO_VERSION', '1.0.0' );
endif;

if ( ! defined( 'RMPRO_PATH' ) ) :
	define( 'RMPRO_PATH', plugin_dir_path( __FILE__ ) );
endif;

if ( ! defined( 'RMPRO_URL' ) ) :
	define( 'RMPRO_URL', plugin_dir_url( __FILE__ ) );
endif;

require_once RMPRO_PATH . 'includes/class-rmpro.php';

// Ensure the class exists before trying to use it
if ( class_exists( 'RMPRO' ) ) :
    // Instantiate the class or call instance method if needed
    $RMPRO = RMPRO::instance();

    // Register the activation hook
    register_activation_hook( __FILE__, array( 'RMPRO', 'activate' ) );

endif;
