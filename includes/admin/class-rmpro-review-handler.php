<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

// Check if the class does not already exist.
if ( ! class_exists( 'RMPRO_Review_Handler' ) ) :

    /**
     * Class RMPRO_Review_Handler
     *
     * Handles the admin area functionality for the Reviews Master plugin.
     */
    class RMPRO_Review_Handler {

        /**
         * Constructor method.
         *
         * Initializes the admin hooks.
         */
        public function __construct() {
           $this->event_handler();
        }

        /**
         * Event Handler method.
         *
         * Initializes the admin hooks.
         */
        public function event_handler(){
            add_action( 'wp_ajax_rmpro_handle_review_action', array( $this, 'process_quick_action' ) );
            add_action( 'wp_ajax_rmpro_save_review_response', array( $this, 'save_review_response' ) );
        }

        /**
         * Get Post Edit Link
         *
         * @return string
         */
        protected function get_review_link(  $review  ) {
            if ( ! $review ) :
                return '';
            endif;

            $title    = $review['title'];
            $edit_url = rmpro_get_review_edit_url( $review['id'] );

            // translators: %1$s is the title, %2$s is the edit action.
            $link = sprintf(
                '<a href="%s" class="row-title" aria-label="%s">%s</a>',
                esc_url($edit_url),
                esc_attr(sprintf(__('“%1$s” (%2$s)', 'review-master'), $title, _x('Edit', 'admin-text', 'review-master'))),
                esc_html($title)
            );


            return $link;
        }

        /**
         * Process quick actions (delete, mark as spam, approve, unapprove) for reviews.
         */
        public function process_quick_action() {
            // Check if the form was submitted.
            if ( isset( $_POST['review_action'], $_POST['review_id'] ) ) :

                // Verify the nonce for security.
                if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'rmpro_nonce_action' ) ) :
                    wp_die( esc_html__( 'Security check failed.', 'review-master' ) );
                endif;

                // Sanitize inputs.
                $action    = sanitize_text_field( wp_unslash( $_POST['review_action'] ) );
                $review_id = absint( $_POST['review_id'] );

                // Process the action.
                switch ( $action ) :

                    case 'delete':
                        rmpro_delete_review( $review_id );
                        wp_send_json_success();
                        break;

                    case 'spam':
                    case 'trash':
                    case 'approve':
                    case 'unapprove':
                        rmpro_update_status( $review_id, $action );
                        $resposne = $this->response( $review_id );
                        wp_send_json_success( $resposne );
                        break;

                    default:
                        wp_send_json_error( esc_html__( 'Action failed.', 'review-master' ) );
                        break;
                        
                endswitch;
            endif;
            wp_send_json_error( esc_html__( 'Something wrong with you!.', 'review-master' ) );
        }

        public function response( $review_id ) {
            $review         = rmpro_get_review( $review_id );
            $reviews_table  = new RMPRO_Reviews_List_Table();
            $admin_class    = new RMPRO_Admin(); // Instantiate the RMPRO_Admin class
            $pending_count  = rmpro_get_reviews_count( [ 'status' => ['unapprove']]); 

            $status_filter  = ob_start();
            $reviews_table->views();
            $status_filter  = ob_get_clean();

            return [
                'class'             => "rmpro-{$review['status']}",
                'status_filter'     => $status_filter,
                'quick_action'      => $reviews_table->column_title( $review ),
                'pending_count'     => $pending_count,
            ];
        }

        public function save_review_response() {        
            
            // Verify the nonce for security.
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'rmpro_nonce_action' ) ) :
                wp_die( esc_html__( 'Security check failed.', 'review-master' ) );
            endif;

            // Sanitize and retrieve the response and review ID
            $user_id    = get_current_user_id();
            $response   = sanitize_textarea_field( isset( $_POST['response'] ) ? wp_unslash( $_POST['response'] ) : '' );
            $review_id  = sanitize_text_field( isset( $_POST['review_id'] ) ? wp_unslash( $_POST['review_id'] ) : '' );

            // Store in wp_postmeta (if reviews are a post type)
            update_metadata( 'rmpro_reviews', $review_id, '_response', $response);
            update_metadata( 'rmpro_reviews', $review_id, '_response_by', $user_id );
            update_metadata( 'rmpro_reviews', $review_id, '_response_time', time() );

            wp_send_json_success(array('message' => esc_html__( 'Response saved successfully.', 'review-master' ) ));
        }
        
    }
    new RMPRO_Review_Handler();
endif;