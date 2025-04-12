<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_Style_Format' ) ) :

    class RMPRO_Settings_Style_Format extends RMPRO_Settings {

        public function __construct() {
            $this->id       = 'style-format';
            $this->label    = esc_html__( 'Style & Format', 'review-master' );

            parent::__construct();
        }
        
        public function get_settings_html( $current_tab ) {
            include RMPRO_PATH . 'includes/admin/views/settings/style-format.php';
        }
        
    }

    new RMPRO_Settings_Style_Format();

endif;