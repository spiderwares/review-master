<?php

if (!class_exists('RMPRO_Permalink_Action_Handler')) :

    class RMPRO_Permalink_Action_Handler {

        public function __construct() {
            add_action('init', [$this, 'add_rewrite_rule']);
            add_filter('query_vars', [$this, 'add_query_vars']);
            add_action('template_redirect', [$this, 'update_review_status']);
        }

        public function add_rewrite_rule() {
            add_rewrite_rule('^rmpro-review/([^/]+)/?$', 'index.php?review_data=$matches[1]', 'top');
        }

        public function add_query_vars($query_vars) {
            $query_vars[] = 'review_data';
            return $query_vars;
        }

        public function update_review_status() {
            $encoded_param = get_query_var('review_data');
            if ( ! $encoded_param ) :
                return;
            endif;

            global $wpdb;
            
            $decoded_data = urldecode($encoded_param);
            $parts = explode('|', $decoded_data);

            if (count($parts) !== 4) :
                wp_die(esc_html__('Invalid request format.', 'review-master' ));
            endif;

            list($review_id, $status, $review_token, $hash) = $parts;
            $expected_hash = md5("$review_id|$review_token");

            if ( $expected_hash !== $hash ) :
                wp_die(esc_html__('Data integrity check failed.', 'review-master' ));
            endif;

            $table_name = esc_sql( $wpdb->rmpro_reviews );
            $stored_token = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT token FROM `$table_name` WHERE id = %d",
                    $review_id
                )
            );            

            if ( ! $stored_token || ! hash_equals( $stored_token, $review_token ) ) :
                wp_die(esc_html__('Invalid or expired token.', 'review-master' ));
            endif;
            $response = rmpro_update_status( $review_id, $status );
            
            $wpdb->update($table_name, ['token' => NULL], ['id' => $review_id ] );
            
            if ( $response ) :
                wp_die( esc_html__( 'Status Updated', 'review-master' ) );
            else :
                wp_die( esc_html__( 'Failed, While updating status', 'review-master' ) );
            endif;
            exit;
        }

        protected function generate_review_token($review_id) {
            global $wpdb;
            if (!current_user_can('edit_posts')) :
                return false;
            endif;
            
            $token = substr(md5(uniqid(wp_rand(), true)), 0, 6);
            
            $wpdb->update(
                $wpdb->rmpro_reviews,
                ['token' => $token],
                ['id' => $review_id],
                ['%s'],
                ['%d']
            );
            
            return $token;
        }

        public function get_review_status_update_url($review_id, $status) {
            if (!current_user_can('edit_posts')) :
                return '';
            endif;

            global $wpdb;
            $token = $wpdb->get_var($wpdb->prepare("SELECT token FROM $wpdb->rmpro_reviews WHERE id = %d", $review_id));

            if ( ! $token ) :
                $token = $this->generate_review_token($review_id);
            endif;

            $hash = md5("$review_id|$token");
            $encoded_param = urlencode("$review_id|$status|$token|$hash");

            return home_url("/rmpro-review/$encoded_param/");
        }
    }

    new RMPRO_Permalink_Action_Handler();

endif;
