<?php 
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif; // Prevent direct access.

if ( ! class_exists( 'RMPRO_Helper' ) ) :

    class RMPRO_Helper {

        /**
         * Get client IP address.
         *
         * @return string|false IP address or false on failure.
         */
        public static function get_ip_address() {
            $response = wp_remote_get( 'https://api.ipify.org?format=json', array( 'timeout' => 5 ) );
            
            if ( is_wp_error( $response ) ) :
                return false;
            endif;

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );

            return isset( $data['ip'] ) ? sanitize_text_field( $data['ip'] ) : false;
        }

        /**
         * Check if a field should be displayed.
         *
         * @param array $field Field configuration array.
         * @return bool Whether to display the field.
         */
        public static function display_field( $field ) {

            return isset( $field['enable'] ) && $field['enable'] === 'on' &&
                   ( ( isset( $field['guest'] ) && $field['guest'] === 'on' && ! is_user_logged_in() ) || 
                     ( ! isset( $field['guest'] ) || $field['guest'] !== 'on' ) );
        }
    }

endif;
