<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( ! class_exists( 'RMPRO_Settings_Form' ) ) :

    class RMPRO_Settings_Form extends RMPRO_Settings {

        public function __construct() {
            $this->id       = 'form';
            $this->label    = esc_html__( 'Form', 'review-master' );

            parent::__construct();
        }

        public function get_settings_html( $current_tab  ) {
            // Define $default_fields here
            $default_fields = array(
                'title'   =>  array(
                    'field'        => esc_html__( 'Title', 'review-master' ),
                    'placeholder'  => esc_html__( 'Title', 'review-master' ),
                    'field_label'  => esc_html__( 'Title', 'review-master' ),
                ),
                'cat_rating'   =>  array(
                    'field'        => esc_html__( 'Rating', 'review-master' ),
                    'placeholder'  => '',
                    'field_label'  => esc_html__( 'Rating', 'review-master' ),
                ),
                'name'  =>  array(
                    'field'        => esc_html__( 'Name', 'review-master' ),
                    'placeholder'  => esc_html__( 'Enter your name', 'review-master' ),
                    'field_label'  => esc_html__( 'Your name', 'review-master' ),
                ),
                'email' =>  array(
                    'field'        => esc_html__( 'Email', 'review-master' ),
                    'placeholder'  => esc_html__( 'Enter your email', 'review-master' ),
                    'field_label'  => esc_html__( 'Your email', 'review-master' ),
                ),
                'your_review'   =>  array(
                    'field'        => esc_html__( 'Your Review', 'review-master' ),
                    'placeholder'  => esc_html__( 'Enter your Review', 'review-master' ),
                    'field_label'  => esc_html__( 'Your Review', 'review-master' ),
                )
            );

            $limit_review_option = [
                'no-limit'          => esc_html__( 'No Limit', 'review-master' ),
                'by-email-address'  => esc_html__( 'By Email Address', 'review-master' ),
                'by-ip-address'     => esc_html__( 'By IP Address', 'review-master' ),
                'by-username'       => esc_html__( 'By Username (will work only registered users)', 'review-master' ),
            ];

            $blacklist_option = [
                'no-blacklist'                  => esc_html__( 'No Blacklist', 'review-master' ),
                'use-the-wordpress-disallowed'  => esc_html__( 'Use The WordPress Disallowed Comment keys', 'review-master' ),
                'review-master-blacklist'       => esc_html__( 'Review Master Blacklist', 'review-master' ),
            ];

            $blacklist_action_option = [
                'require_approval'  => esc_html__( 'Require approval', 'review-master' ),
                'reject_submission' => esc_html__( 'Reject Submmission', 'review-master' ),
            ];

            include RMPRO_PATH . 'includes/admin/views/settings/form.php';
        }
    }

    new RMPRO_Settings_Form();

endif;
