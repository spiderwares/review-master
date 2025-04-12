<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit; // Ensures the plugin is loaded within WordPress context.

if ( ! class_exists( 'RMPRO' ) ) : // Check if the class is already defined to prevent redeclaration.

    // Define the main class for your plugin
	final class RMPRO {

        // Initializes the plugin by loading necessary components and setting up database
        public static function instance() {
            self::includes();  // Load required files
            self::db_intialize();  // Initialize the custom database tables
        }


        // Includes necessary files for different plugin functionalities
        private static function includes() {
            // Include core modules
            require_once RMPRO_PATH . 'includes/class-rmpro-install.php'; 
            require_once RMPRO_PATH . 'includes/modules/class-rmpro-helper.php';
            require_once RMPRO_PATH . 'includes/modules/class-rmpro-akismet.php';
            require_once RMPRO_PATH . 'includes/modules/class-rmpro-duplication.php';
            require_once RMPRO_PATH . 'includes/modules/class-rmpro-blacklist.php';
            require_once RMPRO_PATH . 'includes/modules/class-rmpro-restriction.php';
            
            // Include admin-specific files if in the admin area
            if( is_admin() ) :
                require_once RMPRO_PATH . 'includes/admin/class-rmpro-admin.php';
                require_once RMPRO_PATH . 'includes/admin/class-rmpro-review-handler.php';
                require_once RMPRO_PATH . 'includes/admin/class-rmpro-reviews-list-table.php';
                
                require_once RMPRO_PATH . 'includes/admin/class-rmpro-settings.php';
                require_once RMPRO_PATH . 'includes/admin/class-rmpro-help-support.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-general.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-style-format.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-review-category.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-form.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-localization.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-settings-shortcode.php';
                require_once RMPRO_PATH . 'includes/admin/settings/class-rmpro-setting-restapi.php';
            endif;

            // Include public-facing and utility files
            require_once RMPRO_PATH . 'includes/rmpro-functions.php';
            require_once RMPRO_PATH . 'includes/restapi/class-rmpro-restapi.php';
            require_once RMPRO_PATH . 'includes/class-rmpro-pemalink-handler.php';
            require_once RMPRO_PATH . 'includes/public/class-rmpro-shortcode.php';
            require_once RMPRO_PATH . 'includes/public/class-rmpro-ajax-handler.php';
            require_once RMPRO_PATH . 'includes/public/class-rmpro-wc-review-form.php';
            require_once RMPRO_PATH . 'includes/class-rmpro-notification.php';
            require_once RMPRO_PATH . 'includes/class-rmpro-slack-notification.php';
            require_once RMPRO_PATH . 'includes/class-rmpro-discord-notification.php';
        }

        /**
         * Execute function on plugin activation
         */
        public static function activate() {
            self::create_tables(); // Create necessary database tables
            // Include the file containing the default options
            $defaultOptions = require_once RMPRO_PATH . 'includes/static/rmpro-default-option.php';

            // Loop through each default option
            foreach ( $defaultOptions as $optionKey => $option ) :
                // Get the existing option value
                $existingOption = get_option( $optionKey );

                // If the option is not set, update it with the default value
                if ( ! $existingOption ) :
                    update_option( $optionKey, $option );
                endif;
            endforeach;
        }

        // Initialize custom database tables with custom prefixes
        private static function db_intialize() {
            global $wpdb;
            // Set up custom table names with WordPress prefix
            $wpdb->rmpro_reviews     = $wpdb->prefix . 'rmpro_reviews';
            $wpdb->rmpro_reviewsmeta = $wpdb->prefix . 'rmpro_reviewsmeta';
        }

        // Function to create the required tables if they don't exist
        public static function create_tables() {
            global $wpdb;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php'; // Include the WordPress upgrade file to handle dbDelta
            $db_delta_result = dbDelta(self::get_schema()); // Create or update the database tables based on the schema
            return $db_delta_result; // Return the result of the database operation
        }

        private static function get_schema() {
            global $wpdb;

            $collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) :
                $collate = $wpdb->get_charset_collate();
            endif;

            return "
                CREATE TABLE {$wpdb->prefix}rmpro_reviews (
                    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    associate_id BIGINT(20) UNSIGNED NOT NULL,
                    title TEXT NOT NULL,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL,
                    rating FLOAT(2,1) NOT NULL,
                    your_review LONGTEXT NOT NULL,
                    status VARCHAR(20) DEFAULT 'pending',
                    ip_address VARCHAR(20),
                    module_type VARCHAR(20) NOT NULL,
                    module VARCHAR(20) NOT NULL,
                    token VARCHAR(20) NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $collate;

                CREATE TABLE {$wpdb->prefix}rmpro_reviewsmeta (
                    meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    rmpro_reviews_id BIGINT(20) UNSIGNED NOT NULL,
                    meta_key VARCHAR(255) NOT NULL,
                    meta_value LONGTEXT NOT NULL,
                    PRIMARY KEY (meta_id),
                    KEY rmpro_reviews_id (rmpro_reviews_id),
                    KEY meta_key (meta_key)
                ) $collate;
            ";
        }

    }

endif;
