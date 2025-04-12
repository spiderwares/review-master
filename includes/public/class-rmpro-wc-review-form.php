<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

// Check if the class does not already exist.
if ( ! class_exists( 'RMPRO_Review_Product_Review_Form_Handler' ) ) :

    /**
     * Class RMPRO_Review_Product_Review_Form_Handler
     *
     * Handles replacing the WooCommerce review form with a custom shortcode.
     */
    class RMPRO_Review_Product_Review_Form_Handler {

        /**
         * Constructor method.
         *
         * Initializes hooks to replace the review form.
         */
        public function __construct() {
            $this->event_handler();
        }

        /**
         * Initialize WordPress hooks.
         */
        public function event_handler() {
            $rmpro_review_categories      = get_option( 'rmpro_review_categories', array() );
            if( isset( $rmpro_review_categories['product']['enable'] ) && $rmpro_review_categories['product']['enable'] == 'on' ):
                add_filter( 'comments_template', array( $this, 'replace_woocommerce_review_form' ), 99);
                add_filter( 'woocommerce_product_reviews_tab_title', array( $this, 'set_review_tab_title' ), 2, 2 );
                add_filter( 'woocommerce_product_get_average_rating', array( $this, 'modify_avg_rating' ), 100, 2 );
                add_filter( 'woocommerce_product_get_review_count', array( $this, 'modify_review_count' ), 100, 2 );
            endif;
        }

        /**
         * Add custom review form shortcode in place of WooCommerce review form.
         */       
        public function replace_woocommerce_review_form($template) {
            if ( is_singular( 'product' ) ) :
                $template = RMPRO_PATH . 'templates/woocommerce-review.php'; 
            endif;
            return $template;
        }

        /**
         * Modify or remove review title count
         */
        public function set_review_tab_title($title, $product) {
            if ( ! is_a( $product, 'WC_Product' ) ) :
                $product = wc_get_product( get_the_ID() ); 
                $count = rmpro_get_reviews_count( array(
                    'associate_id' => $product->get_id(), 
                    'module'       => array( 'product' ),
                    'status'       => array( 'approve' ) 
                    ) );
                    
                return sprintf( esc_html__( 'Reviews (%d)', 'review-master' ), $count );
            else:
                return $title;
            endif;
        }

        /**
         * Modify the average rating
         */
        public function modify_avg_rating( $average, $product ) {
            $summary = rmpro_get_rating_summary( array(
                'associate_id' => $product->get_id(), 
                'module'       => array( 'product' ),
                'status'       => array( 'approve' ) 
            ) );

            return !empty( $summary['summary']['average_rating'] ) ? $summary['summary']['average_rating'] : 0;
        }

        /**
         * Modify the review count
         */
        public function modify_review_count( $count, $product ) {
            $count = rmpro_get_reviews_count( array(
                'associate_id' => $product->get_id(), 
                'module'       => array( 'product' ),
                'status'       => array( 'approve' ) 
                ) );

            return $count;
        }

    }

    // Initialize the class.
    new RMPRO_Review_Product_Review_Form_Handler();

endif;
