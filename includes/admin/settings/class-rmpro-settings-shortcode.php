<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_Shortcode' ) ) :

    class RMPRO_Settings_Shortcode extends RMPRO_Help_Support {

        public function __construct() {
            $this->id       = 'shortcode';
            $this->label    = esc_html__( 'Shortcode', 'review-master' );
            
            parent::__construct();
        }
        
        public function get_settings_html( $current_tab ) {
            include RMPRO_PATH . 'includes/admin/views/help-support/shortcode.php';
        }
        
    }

    new RMPRO_Settings_Shortcode();

endif;