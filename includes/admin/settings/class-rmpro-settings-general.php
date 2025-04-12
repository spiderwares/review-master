<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_General' ) ) :

    class RMPRO_Settings_General extends RMPRO_Settings {

        public function __construct() {
            $this->id       = 'general';
            $this->label    = esc_html__( 'General', 'review-master' );

            parent::__construct();
        }
        
        public function get_settings_html( $current_tab ) {
            $placeholders = RMPRO_Notification::get_placeholders();
            include RMPRO_PATH . 'includes/admin/views/settings/general.php';
        }
        
    }

    new RMPRO_Settings_General();

endif;