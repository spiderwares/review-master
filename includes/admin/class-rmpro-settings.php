<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

if ( ! class_exists( 'RMPRO_Settings' ) ) :

    abstract class RMPRO_Settings {

        protected $id       = '';
        protected $label    = '';
        protected $priority = 10;
        protected $settings = [];

        /**
         * Constructor method.
         * Initializes settings and event handlers.
         */
        public function __construct() {
            $this->get_settings();
            $this->event_handler();
        }

        /**
         * Register event handlers.
         */
        public function event_handler() : void {
            add_filter( 'rmpro_settings_tabs', array( $this, 'add_settings_tab' ), 99, 1 );
            add_action( 'rmpro_settings_tab_content_' . $this->id, array( $this, 'get_settings_html' ), 10, 1 );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
        }

        /**
         * Get the ID of the settings tab.
         *
         * @return string
         */
        public function get_id() : string {
            return $this->id;
        }

        /**
         * Get the label of the settings tab.
         *
         * @return string
         */
        public function get_label() : string {
            return $this->label;
        }

        /**
         * Add a settings tab to the plugin's settings.
         *
         * @param array $tabs Existing tabs.
         * @return array Updated tabs.
         */
        public function add_settings_tab( array $tabs ) : array {
            if ( ! empty( $this->id ) && ! empty( $this->label ) ) :
                $tabs[ $this->id ] = [
                    'label'    => $this->label,
                    'priority' => $this->priority,
                ];
            endif;
            return $tabs;
        }

        /**
         * Register settings for the current tab.
         */
        public function register_settings() : void {
            register_setting(
                'rmpro_' . $this->id . '_group', // Option group.
                'rmpro_' . $this->id,           // Option name in the database.
                [
                    'sanitize_callback' => [ $this, 'sanitize_rmpro_options' ],
                ] // Sanitize callback function.
            );
        }

        /**
         * Get the saved settings.
         *
         * @return array
         */
        public function get_settings() : array {
            return $this->settings = get_option( 'rmpro_' . $this->id, [] );
        }

        /**
         * Sanitize options input.
         *
         * @param mixed $input The input data.
         * @return array|string Sanitized data.
         */
        public function sanitize_rmpro_options( $input ) {

            $sanitized_input = [];
            if ( is_array( $input ) ) :
                foreach ( $input as $key => $value ) :
                    if ( is_array( $value ) ) :
                        $sanitized_input[ $key ] = $this->sanitize_array_recursive( $value );
                    else :
                        $sanitized_input[ $key ] = wp_kses_post( $value );
                    endif;
                endforeach;
            else :
                $sanitized_input = wp_kses_post( $input );
            endif;
            return apply_filters( 'rmpro_sanitized_input_data', $sanitized_input, $input );
        }

        /**
         * Recursively sanitize arrays.
         *
         * @param array $array The array to sanitize.
         * @return array Sanitized array.
         */
        protected function sanitize_array_recursive( array $array ) : array {
            $sanitized_array = [];
            foreach ( $array as $key => $value ) :
                if ( is_array( $value ) ) :
                    $sanitized_array[ $key ] = $this->sanitize_array_recursive( $value );
                else :
                    $sanitized_array[ $key ] = wp_kses_post( $value );
                endif;
            endforeach;
            return $sanitized_array;
        }
    }

endif;
