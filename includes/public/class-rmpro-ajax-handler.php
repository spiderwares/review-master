<?php
if (!defined('ABSPATH')) :
    exit; // Exit if accessed directly.
endif;

// Ensure the class doesn't already exist before defining it
if ( ! class_exists( 'RMPRO_Ajax_Handler' ) ) :
    
    /**
     * Class RMPRO_Ajax_Handler
     *
     * Handles AJAX-related functionalities for the Review Master plugin.
     */
    class RMPRO_Ajax_Handler {

        // Class properties
        protected $restriction;  // Handles form restrictions
        public $is_logged_in = false; // Stores user login status

        /**
         * Constructor - Initializes dependencies and registers AJAX hooks.
         */
        public function __construct() {
            $this->initialize_dependencies();
            $this->register_hooks();
        }

        /**
         * Initializes necessary dependencies for form restrictions and validation.
         */
        private function initialize_dependencies() {
            $this->restriction  = new RMPRO_Form_Restrication();
        }

        /**
         * Registers AJAX hooks for handling review submissions and fetching reviews.
         */
        private function register_hooks() {
            // Enqueue frontend scripts
            add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'], 10 );

            // AJAX handlers for submitting reviews (for both logged-in and guest users)
            add_action( 'wp_ajax_rmpro_submit_review', [$this, 'handle_review_submission'] );
            add_action( 'wp_ajax_nopriv_rmpro_submit_review', [$this, 'handle_review_submission'] );

            // AJAX handlers for retrieving reviews (for both logged-in and guest users)
            add_action( 'wp_ajax_rmpro_get_reviews', array( $this, 'ajax_get_reviews' ) );
            add_action( 'wp_ajax_nopriv_rmpro_get_reviews', array( $this, 'ajax_get_reviews' ) );    
        }

        /**
         * Enqueues frontend scripts and localizes AJAX variables.
         */
        public function enqueue_scripts() {
            wp_enqueue_script('rmpro-frontend', RMPRO_URL . 'assets/js/rmpro-frontend.js', ['jquery'], RMPRO_VERSION, false);
            wp_localize_script(
                'rmpro-frontend',
                'rmpro_ajax',
                [
                    'ajax_url'  => admin_url('admin-ajax.php'),
                    'nonce'     => wp_create_nonce('rmpro_nonce'), // Security nonce for AJAX requests
                ]
            );
        }

        /**
         * Sanitize and validate the submitted review input.
         *
         * @param array $input The user-submitted review data.
         * @return array|false The sanitized and validated review data, or false if invalid.
         */
        private function sanitize_and_validate_input($input) {
            // Ensure required fields are present.
            if (!isset($input['associate_id'], $input['your_review'])) :
                return false;
            endif;

            // Retrieve current user data.
            $user               = wp_get_current_user();
            $form_settings      = get_option('rmpro_form', []);
            $general_setting    = get_option('rmpro_general', []);

            // Determine default review status.
            $status             = isset($general_setting['default_status']) ? $general_setting['default_status'] : 'unapprove';
            
            // Check if automatic name/email usage is enabled.
            $auto_email         = isset($form_settings['autonamemail']) && $form_settings['autonamemail'] === 'on';
            
            // Retrieve and sanitize ratings.
            $ratings            = isset($input['ratings']) ? array_map('absint', $input['ratings']) : [];
            $avg_rating         = rmpro_calculate_average_rating($ratings);

            // Adjust status based on approval requirements.
            if (isset($general_setting['require_approval']) && $general_setting['require_approval'] == 1) :
                $status = ($avg_rating >= absint($general_setting['require_approval_for'])) ? 'approve' : 'unapprove';
            endif;
            
            // Check for blacklist status before finalizing the review status.
            $status = $status !== 'unapprove' ? $this->get_blacklist_status($form_settings, $data, $status) : $status;

            // Return sanitized and validated review data.
            return [
                'associate_id'  => absint($input['associate_id']),
                'module_type'   => sanitize_textarea_field($input['module_type']),
                'module'        => sanitize_textarea_field($input['module']),
                'title'         => sanitize_textarea_field($input['title']),
                'name'          => $auto_email && $user ? $user->display_name : (isset($input['name']) ? sanitize_text_field($input['name']) : ($user ? $user->display_name : '')),
                'email'         => $auto_email && $user ? $user->user_email : (isset($input['email']) ? sanitize_email($input['email']) : ($user ? $user->user_email : '')),
                'description'   => sanitize_textarea_field($input['your_review']),
                'ratings'       => $ratings,
                'avg_rating'    => isset($input['avg_rating']) ? absint($input['avg_rating']) : $avg_rating,
                'ip_address'    => RMPRO_Helper::get_ip_address(),
                'status'        => $status,
                'recaptcha'     => isset($input['g-recaptcha-response']) ? sanitize_text_field($input['g-recaptcha-response']) : '',
                'hcaptcha'      => isset($input['h-captcha-response']) ? sanitize_text_field($input['h-captcha-response']) : '',
            ];
        }

        /**
         * Handle Review Submission
         */
        public function handle_review_submission() {

            // Verify the security nonce to prevent CSRF attacks
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'rmpro_security_nonce' ) ) :
                wp_send_json_error( esc_html__( 'Security check failed. Please refresh the page and try again.', 'review-master' ) );
                return;
            endif;
        
            // Sanitize and validate user input
            $data = $this->sanitize_and_validate_input($_POST);
            
            // Retrieve plugin settings
            $rmpro_general  = get_option( 'rmpro_general', [] );
            $form_settings  = get_option( 'rmpro_form', [] );
            $localization   = get_option( 'rmpro_localization', [] );
        
            // Validate the review fields
            $validation_errors = $this->validate_review_fields( $_POST, $form_settings );
            if ( !empty( $validation_errors ) ) :
                wp_send_json_error( $validation_errors );
            endif;
        
            // Check if submission restrictions apply
            if ( $this->restriction->check_submission_restrictions( $data, $localization ) ) :
                return;
            endif;
        
            // Save the review and store the review ID
            $review_id = rmpro_save_review( [
                'associate_id'  => $data['associate_id'],
                'ratings'       => $data['ratings'],
                'avg_rating'    => $data['avg_rating'],
                'title'         => $data['title'],
                'name'          => $data['name'],
                'email'         => $data['email'],
                'ip_address'    => $data['ip_address'],
                'your_review'   => $data['description'],
                'status'        => $data['status'],
                'module_type'   => $data['module_type'],
                'module'        => $data['module'],
            ] );

            // Handle errors if the review was not saved successfully
            if ( ! $review_id ) :
                wp_send_json_error(
                    !empty( $localization['message_error'] ) ? 
                    esc_html( $localization['message_error'] ) : 
                    esc_html__( 'Oops! Something went wrong. Please try again later.', 'review-master' ) 
                );
            endif;
        
            // Trigger action hook after review submission
            do_action( 'rmpro_review_submited', $rmpro_general, $review_id, $data );
        
            // Return success message based on review status (approved or pending approval)
            if( $data['status'] == 'unapprove' ):
                wp_send_json_success( 
                    !empty( $localization[ 'message_pending' ] ) ? 
                    esc_html( $localization[ 'message_pending' ] ) : 
                    esc_html__( 'Your review has been submitted and is awaiting approval.', 'review-master' ) 
                );
            else:
                wp_send_json_success( 
                    !empty( $localization[ 'message_thank_you' ] ) ? 
                    esc_html( $localization[ 'message_thank_you' ] ) : 
                    esc_html__( 'Thank you for your review! We appreciate your feedback.', 'review-master' ) 
                );
            endif;
        }

        /**
         * Validate review fields based on form settings.
         *
         * @param array $data         The submitted review data.
         * @param array $form_setting The form settings containing field configurations.
         *
         * @return array An array of validation errors if any, otherwise an empty array.
         */
        public function validate_review_fields( $data, $form_setting ) {
            $fields     = isset( $form_setting['form'] ) ? $form_setting['form'] : [];
            $errors     = [];
            $is_guest   = ! is_user_logged_in(); // Check if the user is a guest
        
            foreach ( $fields as $key => $field ) :
                // Skip disabled fields
                if ( ! isset( $field['enable'] ) || 'on' !== $field['enable'] ) :
                    continue;
                endif;
        
                // If the field is restricted to guests, but the user is logged in, skip it
                if ( isset( $field['guest'] ) && 'on' === $field['guest'] && ! $is_guest ) :
                    continue;
                endif;
        
                // Required field validation
                if ( isset( $field['required'] ) && 'on' === $field['required'] ) :
                    if ( 'cat_rating' === $key ) :
                        if ( empty( $data['ratings'] ) || ! is_array( $data['ratings'] ) ) :
                            $errors[$key] = esc_html__( 'Category rating is required.', 'review-master' );
                        endif;
                    elseif ( $key ==='your_review' && ( ! isset( $data[ 'your_review' ] ) || ! is_array( $data['your_review'] ) ) ) :
                        $errors[$key] = esc_html__( 'Description field is required.', 'review-master' );
                    elseif ( ! isset( $data[ $key ] ) || empty( trim( $data[ $key ] ) ) ) :
                        $errors[$key] = sprintf( esc_html__( '%s field is required.', 'review-master' ), esc_html( $key ) );
                    endif;
                endif;
        
                // Email validation
                if ( 'email' === $key && ! empty( $data['email'] ) && ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) :
                    $errors['email'] = esc_html__( 'Please enter a valid email address.', 'review-master' );
                endif;
            endforeach;
        
            if ( ! empty( $errors ) ) :
                return [
                    'validation_errors' => $errors
                ];
            endif;
        }
              

        /**
         * Handle AJAX request to fetch reviews with pagination.
         */
        public function ajax_get_reviews() {
            
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'rmpro_nonce' ) ) :
                wp_send_json_error( esc_html__( 'Security check failed. Please refresh the page and try again.', 'review-master' ) );
                return;
            endif;

            $localization       = get_option( 'rmpro_localization', [] );

            $current_page       = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
            $orderby            = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'DESC';
            $search             = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
            $rating             = isset( $_POST['rating'] ) ? sanitize_text_field( wp_unslash( $_POST['rating'] ) ) : '';
            $module             = isset( $_POST['module'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['module'] ) ) ) : [];
            $module_type        = isset( $_POST['module_type'] ) ? sanitize_text_field( wp_unslash( $_POST['module_type'] ) ) : '';
            $per_page           = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 5;
            $associate_id       = isset( $_POST['associate_id'] ) ? absint( $_POST['associate_id'] ) : '';

            $enable_avatar      = isset( $_POST['enable_avatar'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_avatar'] ) ) : false;
            $avatar_size        = isset( $_POST['avatar_size'] ) ? absint( $_POST['avatar_size'] ) : 40;
            $enable_excerpts    = isset( $_POST['enable_excerpts'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_excerpts'] ) ) : false;
            $excerpt_length     = isset( $_POST['excerpt_length'] ) ? absint( $_POST['excerpt_length'] ) : 40;

            // Prepare arguments for fetching reviews.
            $args = array(
                'status'       => array( 'approve' ),
                'per_page'     => $per_page,
                'current_page' => $current_page,
                's'            => $search,
            );
            
            if ( ! empty( $module ) ) :
                $args['module'] = $module;
            endif;

            if ( ! empty( $module_type ) ) :
                $args['module_type'] = array( $module_type );
            endif;

            if ( ! empty( $associate_id ) ) :
                $args['associate_id'] =  $associate_id ;
            endif;

            if ( ! empty( $orderby ) && ( $orderby == 'ASC' || $orderby == 'DESC' ) ) :
                $args['order'] = $orderby;
            endif;

            if ( ! empty( $orderby ) && ( $orderby == 'highest_rated' || $orderby == 'lowest_rated' ) ) :
                $args['order_by']   = 'rating';
                $args['order'] = ( $orderby === 'highest_rated' ) ? 'DESC' : 'ASC';
            endif;

            if ( ! empty( $rating ) ) :
                $args['rating']   = $rating;
            endif;
            
            // Fetch reviews based on arguments.
            $reviews        = rmpro_get_reviews( $args );
            $total_review   = rmpro_get_reviews_count( $args );
            // Calculate total pages, ensuring at least 1 page.
            $total_pages    = max( 1, ceil( $total_review / $per_page ) );

            // Render reviews using a template.
            ob_start();
            if ( ! empty( $reviews ) ) :
                foreach ( $reviews as $review ) : 
                    $show_more      = false;
                    $categories     = rmpro_get_review_by_category( $review['id'] );
                    $response       = get_metadata( 'rmpro_reviews', $review['id'], '_response', true );
                    $response_by    = get_metadata( 'rmpro_reviews', $review['id'], '_response_by', true );
                    $response_time  = get_metadata( 'rmpro_reviews', $review['id'], '_response_time', true );
                    $response_by    = $response_by ? get_userdata( $response_by ) : false;
                    $review_content = wp_kses_post( $review['your_review'] );

                    if( $enable_excerpts ) :
                        $words          = explode( ' ', $review_content );
                        $show_more      = count( $words ) > $excerpt_length;
                        $short_review   = $show_more ? implode( ' ', array_slice( $words, 0, $excerpt_length ) ) . '...' : $review_content; 
                    else :
                        $short_review   = $review_content;
                    endif;
                    
                    rmpro_get_template(
                        'review.php',
                        array(
                            'review'            => $review,
                            'categories'        => $categories,
                            'review_content'    => $review_content,
                            'short_review'      => $short_review,
                            'response'          => $response,
                            'response_by'       => $response_by,
                            'response_time'     => $response_time,
                            'enable_avatar'     => $enable_avatar,
                            'avatar_size'       => $avatar_size,
                            'show_more'         => $show_more
                        )
                    );
                endforeach;

                rmpro_get_template(
                    'review-pagination.php',
                    array(
                        'current_page'  => $current_page,
                        'total_pages'   => $total_pages,
                    )
                );
            else :
                echo sprintf(
                    '<p class="rmpro-no-reviews">%s</p>',
                    esc_html( isset( $localization['no_review'] ) ? $localization['no_review'] : esc_html( 'No reviews found.', 'review-master' ) )
                );
            endif;
            $reviews_html = ob_get_clean();

            // Return JSON response with rendered reviews.
            wp_send_json_success( array(
                'html'         => $reviews_html,
                'total_review' => $total_review,
            ) );
        }
        
        /**
         * Determines the blacklist status of a submitted review based on configured settings.
         *
         * This function checks whether the submitted review contains blacklisted words or phrases.
         * If blacklisted content is found, the status is changed to 'unapprove'.
         *
         * @param array  $form_settings  The form settings, including blacklist options.
         * @param array  $data           The submitted review data.
         * @param string $status         The current status of the review.
         * 
         * @return string The updated review status ('approve' or 'unapprove').
         */
        public function get_blacklist_status( $form_settings, $data, $status ) {
            // Check if the blacklist feature is enabled and is not set to 'no-blacklist'
            if ( isset( $form_settings['blacklist'] ) && $form_settings['blacklist'] != 'no-blacklist' ) :

                // Initialize the blacklist entries variable
                $blacklist_entries = [];

                // Determine which blacklist to use
                if ( $form_settings['blacklist'] == 'use-the-wordpress-disallowed' ) :
                    // Use WordPress' built-in disallowed keywords list
                    $blacklist_entries = get_option('disallowed_keys');

                elseif ( $form_settings['blacklist'] == 'review-master-blacklist' ) :
                    // Use the custom blacklist entries defined in the Review Master plugin settings
                    $blacklist_entries = $form_settings['blacklist_entries'];
                endif;

                // Check if blacklist action is set and blacklist entries exist
                if ( isset( $form_settings['blacklist_action'], $blacklist_entries ) && 
                    $form_settings['blacklist_action'] == 'require_approval' ) :
                    
                    // Validate if the submitted data contains blacklisted words/phrases
                    if ( $this->blacklist->rmpro_blacklist( $data, $blacklist_entries ) ) :
                        return 'unapprove'; // If blacklisted, set status to 'unapprove'
                    endif;
                endif;
            endif;

            // If no blacklist conditions are met, return the original status
            return $status;
        }


    }

    new RMPRO_Ajax_Handler();
endif;
