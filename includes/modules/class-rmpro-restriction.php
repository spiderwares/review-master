<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

if( ! class_exists( 'RMPRO_Form_Restrication' ) ) :
        
    class RMPRO_Form_Restrication {
        
        protected $akismet;
        protected $duplication;
        protected $blacklist;

        public function __construct() {
            $this->initialize_dependencies();
        }

        private function initialize_dependencies() {
            $this->akismet      = new RMPRO_Akismet();
            $this->duplication  = new RMPRO_Duplication();
            $this->blacklist    = new RMPRO_Blacklist();
        }

        /**
         * Validates reCAPTCHA.
         */
        private function validate_recaptcha( $form_settings, $captcha ) {
            $secret_key   = isset( $form_settings['secret_key'] ) ? $form_settings['secret_key'] : false;
            
            $url          = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $captcha;
            $request      = wp_remote_get( $url );
            $body         = wp_remote_retrieve_body( $request );
            $response     = json_decode( $body );
            return ! empty( $response->success ) && $response->success === true ? true : false;
        }

        /**
         * Validates hCaptcha.
         */
        private function validate_hcaptcha( $form_settings, $captcha ) {
            $secret_key   = isset( $form_settings['hcaptcha_secret_key'] ) ? $form_settings['hcaptcha_secret_key'] : false;
            $url          = 'https://api.hcaptcha.com/siteverify?secret=' . $secret_key . '&response=' . $captcha;
            $request      = wp_remote_get( $url );
            $body         = wp_remote_retrieve_body( $request );
            $response     = json_decode( $body );

            return ! empty( $response->success ) && $response->success === true ? true : false;
        }

        public function rmpro_validate_by_email( $associate_id, $email, $setting ) {
            global $wpdb;

            // Check if email is in the whitelist
            if ( !empty($setting['email_whitelist']) ) :
                $whitelist = explode( ',', $setting['email_whitelist'] );
                if ( in_array($email, $whitelist) ) :
                    return true; 
                endif;
            endif;
        
            $time_limit = !empty($setting['limit_time']) ? intval($setting['limit_time']) : 0;
            $time_condition = '';

            if ( $time_limit > 0 ) :
                $time_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)";
            endif;

            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->rmpro_reviews} WHERE associate_id = %d AND email = %s $time_condition",
                $associate_id,
                $email
            );

            $count = $wpdb->get_var($query);
            return $count > 0;
    
        }

        public function rmpro_validate_by_ip( $associate_id, $ip_address, $setting ) {
            global $wpdb;

            // Check if IP is in the whitelist
            if ( !empty($setting['ip_whitelist']) ) :
                $whitelist = explode( ',', $setting['ip_whitelist'] );
                if ( in_array($ip_address, $whitelist) ) :
                    return true; 
                endif;
            endif;

            // Check review limit by IP
            $time_limit = !empty($setting['limit_time']) ? intval($setting['limit_time']) : 0;
            $time_condition = '';

            if ( $time_limit > 0 ) :
                $time_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)";
            endif;

            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->rmpro_reviews} WHERE associate_id = %d AND ip_address = %s $time_condition",
                $associate_id,
                $ip_address
            );

            $count = $wpdb->get_var($query);
            return $count > 0;
        }

        public function rmpro_validate_by_username( $associate_id, $name, $setting ) {
            global $wpdb;

            // Check if username is in the whitelist
            if ( !empty($setting['username_whitelist']) ) :
                $whitelist = explode( ',', $setting['username_whitelist'] );
                if ( in_array($ip_address, $whitelist) ) :
                    return true; 
                endif;
            endif;
                
            // Check review count within the limit_time period (if set)
            $time_limit = !empty($setting['limit_time']) ? intval($setting['limit_time']) : 0;
            $time_condition = '';

            if ( $time_limit > 0 ) :
                $time_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)";
            endif;

            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->rmpro_reviews} WHERE associate_id = %d AND name = %s $time_condition",
                $associate_id,
                $name
            );

            $count = $wpdb->get_var($query);
            return $count > 0;
        }

        public function check_submission_restrictions( $data, $localization ) {
            $form_settings  = get_option('rmpro_form', []);

            // Verify reCAPTCHA
            if ( isset( $form_settings['captcha_type'], $form_settings['captcha_usage'] ) &&
                $form_settings['captcha_type'] === 'recaptcha-v2' &&
                ( $form_settings['captcha_usage'] === 'everyone' || ( $form_settings['captcha_usage'] === 'guests' && !is_logged_in() ) ) &&
                ! $this->validate_recaptcha( $form_settings, $data['recaptcha'] ) ) :
                wp_send_json_error( esc_html__('captcha validation failed. Please try again.', 'review-master' ) );
                return true;
            endif;

            // Verify hCAPTCHA
            if ( isset( $form_settings['captcha_type'], $form_settings['captcha_usage'] ) &&
                $form_settings['captcha_type'] === 'h_captcha' &&
                ( $form_settings['captcha_usage'] === 'everyone' || ( $form_settings['captcha_usage'] === 'guests' && !is_logged_in() ) ) &&
                ! $this->validate_hcaptcha( $form_settings, $data['hcaptcha'] ) ) :
                wp_send_json_error( esc_html__('captcha validation failed. Please try again.', 'review-master' ) );
                return true;
            endif;

            // Restrict by email
            if (isset($form_settings['limit_review']) && $form_settings['limit_review'] === 'by-email-address') :
                if ($this->restriction->rmpro_validate_by_email( $data['associate_id'], $data['email'], $form_settings ) ) :
                    wp_send_json_error( 
                        !empty( $localization['message_already_submitted_email'] ) ? 
                        esc_html( $localization['message_already_submitted_email'] ) : 
                        esc_html__( 'Looks like this email has already been used to submit a review for this post.', 'review-master' ) 
                    );
                    return true;
                endif;
            endif;

            // Restrict by IP address
            if (isset($form_settings['limit_review']) && $form_settings['limit_review'] === 'by-ip-address') :
                if ($this->restriction->rmpro_validate_by_ip( $data['associate_id'], $data['ip_address'], $form_settings )) :
                    wp_send_json_error( 
                        !empty( $localization['message_already_submitted_ip'] ) ? 
                        esc_html( $localization['message_already_submitted_ip'] ) : 
                        esc_html__( 'We have already received a review from this IP.', 'review-master' ) 
                    );        
                    return true;
                endif;
            endif;

            // Restrict by username
            if (is_user_logged_in() && isset($form_settings['limit_review']) && $form_settings['limit_review'] === 'by-username') :
                if ($this->restriction->rmpro_validate_by_username( $data['associate_id'], $data['name'], $form_settings )) :
                    wp_send_json_error( 
                        !empty( $localization['message_already_submitted_username']) ? 
                        esc_html( $localization['message_already_submitted_username'] ) : 
                        esc_html__( 'You have already submitted a review for this post.', 'review-master') 
                    );
                    return true;
                endif;
            endif;

            // Check Akismet for spam
            if (isset($form_settings['enable-akismet']) && $form_settings['enable-akismet'] === '1') :
                if ($this->akismet->rmpro_akismet_review($data['associate_id'], $data['name'], $data['email'], $data['description'], $data['ip_address'], $form_settings)) :
                    wp_send_json_error( 
                        !empty( $localization['message_spam'] ) ? 
                        esc_html( $localization['message_spam'] ) : 
                        esc_html__( 'Oops! Your review has been flagged as spam.', 'review-master' ) 
                    );
                    return true;
                endif;
            endif;

            // Prevent duplicate reviews
            if (isset($form_settings['prevent-duplicates']) && $form_settings['prevent-duplicates'] === '1') :
                if ($this->duplication->rmpro_duplication_review($data['associate_id'], $data['name'], $data['email'], $data['description'], $data['ip_address'])) :
                    wp_send_json_error( 
                        !empty( $localization['message_duplicate'] ) ? 
                        esc_html( $localization['message_duplicate'] ) : 
                        esc_html__( 'It looks as though you have already said that!', 'review-master' ) 
                    );
                    return true;
                endif;
            endif;

            //  Check Blacklist
            if (isset($form_settings['blacklist']) && $form_settings['blacklist'] != 'no-blacklist') :

                // Check blacklist setting and assign appropriate blacklist entries
                $blacklist_entries = '';
                if ($form_settings['blacklist'] == 'use-the-wordpress-disallowed') :
                    // Use WordPress disallowed keywords
                    $blacklist_entries = get_option('disallowed_keys');
                elseif($form_settings['blacklist'] == 'review-master-blacklist') :
                    // Use review master blacklist entries 
                    $blacklist_entries = $form_settings['blacklist_entries'];
                endif;


                if ( $this->blacklist->rmpro_blacklist($data, $blacklist_entries ) ) :
                    if ( isset( $form_settings['blacklist_action'] ) && $form_settings['blacklist_action'] == 'reject_submission' ) :
                        wp_send_json_error( 
                            !empty( $localization['blacklist_entries_message'] ) ? 
                            esc_html( $localization['blacklist_entries_message'] ) : 
                            esc_html__( 'Your submission contains disallowed content and cannot be submitted.', 'review-master' ) 
                        );
                        return true;
                    endif;
                endif;
            endif;

            return false;
        }

    }

    new RMPRO_Form_Restrication();

endif;