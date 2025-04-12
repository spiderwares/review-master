<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

if ( ! class_exists( 'RMPRO_Help_Support' ) ) :

    abstract class RMPRO_Help_Support {

        protected $id       = '';
        protected $label    = '';
        protected $priority = 10;
        protected $settings = [];

        /**
         * Constructor method.
         * Initializes settings and event handlers.
         */
        public function __construct() {
            $this->event_handler();
        }

        /**
         * Register event handlers.
         */
        public function event_handler() : void {
            add_filter( 'rmpro_help_support_tabs', array( $this, 'add_settings_tab' ), 99, 1 );
            add_action( 'rmpro_help_support_tab_content_' . $this->id, array( $this, 'get_settings_html' ), 10, 1 );
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

    }

endif;
