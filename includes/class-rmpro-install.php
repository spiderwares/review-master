<?php

/**
 * Installation related functions and actions.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'PMPRO_install' ) ) :

    /**
     * PMPRO_install Class
     *
     * Handles installation processes like creating database tables,
     * setting up roles, and creating necessary pages on plugin activation.
     */
    class PMPRO_install {

        /**
         * Hook into WordPress actions and filters.
         */
        public static function init() {
            add_filter( 'plugin_action_links_' . plugin_basename( RMPRO_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );
        }

        /**
         * Install plugin.
         *
         * Creates tables, roles, and necessary pages on plugin activation.
         */
        public static function install() {
            if ( ! is_blog_installed() ) :
                return;
            endif;
            
        }

        /**
         * Add plugin action links.
         *
         * @param array $links Array of action links.
         * @return array Modified array of action links.
         */
        public static function plugin_action_links( $links ) {
            $action_links = array(
                'settings' => sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    admin_url( 'admin.php?page=rmpro-settings' ),
                    esc_attr__( 'Review Master Settings', 'review-master' ),
                    esc_html__( 'Settings', 'review-master' )
                ),
                'documentation' => sprintf(
                    '<a target="_blank" href="%s" aria-label="%s">%s</a>',
                    esc_url( 'https://spiderwares.com/documentation/rmpro/' ),
                    esc_attr__( 'Documentation', 'review-master' ),
                    esc_html__( 'Documentation', 'review-master' )
                ),
            );
            return array_merge( $action_links, $links );
        }

    }

    // Initialize the installation process
    PMPRO_install::init();

endif;
