<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if( ! class_exists( 'RMPRO_Settings_Review_Category' ) ) :

    class RMPRO_Settings_Review_Category extends RMPRO_Settings {

        public function __construct() {
            $this->id       = 'review_categories';
            $this->label    = esc_html__( 'Review Categories', 'review-master' );
            parent::__construct();
        }

        public function get_settings_html( $current_tab  ) {        
            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
            // $users      = get_users();

            include RMPRO_PATH . 'includes/admin/views/settings/reviews-category.php';
        }
        
    }
    new RMPRO_Settings_Review_Category();
endif;