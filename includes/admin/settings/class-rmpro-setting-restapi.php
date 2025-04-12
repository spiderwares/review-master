<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_RESTAPI' ) ) :

    class RMPRO_Settings_RESTAPI extends RMPRO_Help_Support {

        public function __construct() {
            $this->id       = 'restapi';
            $this->label    = esc_html__( 'Rest API', 'review-master' );
            
            parent::__construct();
        }
        
        public function get_settings_html( $current_tab ) {
            include RMPRO_PATH . 'includes/admin/views/help-support/restapi.php';
        }
        
    }

    new RMPRO_Settings_RESTAPI();

endif;