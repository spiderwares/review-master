<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

// Check if the class does not already exist.
if ( ! class_exists( 'RMPRO_Restapi' ) ) :

    /**
     * Class RMPRO_Restapi
     *
     * Handles the REST API functionality for the Reviews Master plugin.
     */
    class RMPRO_Restapi {

        /**
         * Namespace for the REST API.
         *
         * @var string
         */
        private $namespace = 'rmpro/v1';

        /**
         * Constructor: Hooks into WordPress to register the REST API.
         */
        public function __construct() {
            add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        }

        /**
         * Registers custom REST API routes.
         */
        public function register_routes() {
            register_rest_route( $this->namespace, '/review/', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_review' ],
                'permission_callback' => '__return_true', // Public API access
            ] );

            register_rest_route( $this->namespace, '/reviews/', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_reviews' ],
                'permission_callback' => '__return_true', // Public API access
            ] );

            register_rest_route( $this->namespace, '/save-review/', [
                'methods'  => 'POST',
                'callback' => [ $this, 'create_review' ],
                'permission_callback' => [ $this, 'validate_permissions' ],
                'args' => [
                    'author'  => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                    'rating'  => [ 'required' => true, 'validate_callback' => [ $this, 'validate_rating' ] ],
                    'comment' => [ 'required' => true, 'sanitize_callback' => 'sanitize_textarea_field' ],
                ],
            ] );
        }

        /**
         * Fetches review
         *
         * @param WP_REST_Request $request
         * @return WP_REST_Response
         */
        public function get_review( $request ) {
            $review_id  = $request->get_param( 'review_id' );
            $review     = rmpro_get_review( $review_id );

            if ( empty( $review ) ) :
                return new WP_REST_Response( [ 'message' => esc_html__( 'No reviews found.', 'review-master' ) ], 404 );
            endif;

            return new WP_REST_Response( $review, 200 );
        }

        /**
         * Fetches reviews 
         *
         * @param WP_REST_Request $request
         * @return WP_REST_Response
         */
        public function get_reviews( $request ) {
            $args = array(
                'per_page'          => $request->get_param( 'per_page' ),
                'current_page'      => $request->get_param( 'current_page' ),
                'status'            => $request->get_param( 'status' ),
                'module'            => $request->get_param( 'module' ),
                's'                 => $request->get_param( 's' ),
                'associate_id'      => $request->get_param( 'associate_id' ),
                'order_by'          => $request->get_param( 'order_by' ),
                'order'             => $request->get_param( 'order' ),
                'rating'            => $request->get_param( 'rating' ),
            );
            $reviews = rmpro_get_reviews( $args );

            if ( empty( $reviews ) ) :
                return new WP_REST_Response( [ 'message' => esc_html__( 'No reviews found.', 'review-master' ) ], 404 );
            endif;

            return new WP_REST_Response( $reviews, 200 );
        }

        /**
         * Creates a new review.
         *
         * @param WP_REST_Request $request
         * @return WP_REST_Response
         */
        public function create_review( $request ) {
            $args = array(
                'review_id'     => $request->get_param( 'review_id' ),
                'associate_id'  => $request->get_param( 'associate_id' ),
                'title'         => $request->get_param( 'title' ),
                'name'          => $request->get_param( 'name' ),
                'email'         => $request->get_param( 'email' ),
                'ratings'       => $request->get_param( 'ratings' ),
                'status'        => $request->get_param( 'status' ),
                'ip_address'    => $request->get_param( 'ip_address' ),
                'your_review'   => $request->get_param( 'your_review' ),
                'score'         => $request->get_param( 'score' ),
                'module_type'   => $request->get_param( 'module_type' ),
                'module'        => $request->get_param( 'module' ),
                'avg_rating'    => $request->get_param( 'avg_rating' ),
            );
            // Save or update review
            $review_id  = rmpro_save_review( $args );

            if ( ! $review_id ) :
                return new WP_REST_Response( [ 'message' => esc_html__('Failed to add review.', 'review-master' ) ], 500 );
            endif;

            return new WP_REST_Response( [ 'message' => 'Review added successfully.', 'id' => $review_id ], 201 );
        }

        /**
         * Validates user permissions for adding a review.
         *
         * @return bool
         */
        public function validate_permissions() {
            return current_user_can( 'edit_posts' ); // Adjust permission as needed
        }

    }

    // Initialize the REST API class.
    new RMPRO_Restapi();

endif;
