<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_Localization' ) ) :

    class RMPRO_Settings_Localization extends RMPRO_Settings {

        public function __construct() {
            $this->id       = 'localization';
            $this->label    = esc_html__( 'Localization', 'review-master' );

            parent::__construct();
        }
        
        public function get_settings_html( $current_tab ) {
            include RMPRO_PATH . 'includes/admin/views/settings/localization.php';
        }
        
    }

    new RMPRO_Settings_Localization();

endif;