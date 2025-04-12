<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

// Check if the class does not already exist.
if ( ! class_exists( 'RMPRO_Admin' ) ) :

    /**
     * Class RMPRO_Admin
     *
     * Handles the admin area functionality for the Reviews Master plugin.
     */
    class RMPRO_Admin {

        /**
         * Constructor method.
         *
         * Initializes the admin hooks.
         */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            add_action( 'admin_head', array( $this, 'remove_edit_submenu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'admin_post_rmpro_save_review', array( $this, 'save_review' ) );
        }

        /**
         * Enqueue admin scripts and styles.
         */
        public function enqueue_admin_scripts() {
            wp_enqueue_style( 'jquery-ui-css', RMPRO_URL . 'assets/css/jquery-ui.css', array(), RMPRO_VERSION, false );
            wp_enqueue_style( 'rmpro-admin', RMPRO_URL . 'assets/css/rmpro-admin.css', array(), RMPRO_VERSION, false );

            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'rmpro-admin', RMPRO_URL . 'assets/js/rmpro-admin.js', array( 'jquery' ), RMPRO_VERSION, true );

            wp_localize_script( 'rmpro-admin', 'rmpro_params', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'rmpro_nonce_action' ),
            ));
        }

        /**
         * Add admin menu and submenu pages.
         */
        public function add_admin_menu() {
            $pending_reviews_count = rmpro_get_reviews_count( array( 'status' => [ 'unapprove' ] ) );

            // Set the menu title
            $menu_title  = esc_html__( 'Review Master', 'review-master' );
            $menu_title .= ' <span class="rmpro-pending"><span class="pending-count rmpro-pending-count-' . $pending_reviews_count . '" aria-hidden="true">' . $pending_reviews_count . '</span></span>';
        
            // Add the main menu page with the dynamic title
            add_menu_page(
                esc_html__( 'Review Master', 'review-master' ),
                $menu_title,
                'manage_options',
                'rmpro',
                array( $this, 'reviews_page' ),
                'dashicons-star-filled',
                25
            );

            add_submenu_page(
                'rmpro',
                esc_html__( 'Reviews', 'review-master' ),
                esc_html__( 'All Reviews', 'review-master' ),
                'manage_options',
                'rmpro',
                array( $this, 'reviews_page' )
            );

            add_submenu_page(
                'rmpro',
                esc_html__( 'Edit Review', 'review-master' ),
                esc_html__( 'Edit Review', 'review-master' ),
                'manage_options',
                'rmpro-review',
                array( $this, 'review_form' ),
            );

            add_submenu_page(
                'rmpro',
                esc_html__( 'Settings', 'review-master' ),
                esc_html__( 'Settings', 'review-master' ),
                'manage_options',
                'rmpro-settings',
                array( $this, 'settings_page' )
            );

            add_submenu_page(
                'rmpro',
                esc_html__( 'Help & Support', 'review-master' ),
                esc_html__( 'Help & Support', 'review-master' ),
                'manage_options',
                'rmpro-support',
                array( $this, 'help_support_page' )
            );
        }

        /**
         * Function to remove submenu pages.
         */
        public function remove_edit_submenu() {
            // Remove the 'Edit Review' submenu
            remove_submenu_page( 'rmpro', 'rmpro-review' );
        }

        /**
         * Render the reviews page.
         */
        public function reviews_page() {
            // Include the Reviews List Table class and prepare the table items.
            $reviews_table = new RMPRO_Reviews_List_Table();
            $reviews_table->prepare_items();

            // Include the reviews list view template.
            include RMPRO_PATH . 'includes/admin/views/reviews-list.php';
        }

        /**
         * Render the Add new review page.
         */
        public function review_form() {
            $review_id      = isset( $_GET['review_id'] ) ? intval( $_GET['review_id'] ) : false;
            $review         = rmpro_get_review( $review_id );
            $reviews_by_cat = rmpro_get_review_by_category( $review_id );
            
            if( ! $review && isset( $review_id ) ) :
                wp_redirect( 'admin.php?page=rmpro' );
            endif;

            // Display a success message if settings are saved.
            if ( isset( $_GET['rmpro_message'] ) && 'true' === $_GET['rmpro_message'] ) :
                echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Review updated successfully.', 'review-master' ) . '</p></div>';
            endif;

            if( isset( $review ) ) :
                $module_link = rmpro_get_module_html_link( $review );
            endif;
            
            // You might want to display a form or some other content here
            include RMPRO_PATH . 'includes/admin/views/review-form.php'; // Include the view template for adding a new review
        }
        

        /**
         * Render the settings page.
         */
        public function settings_page() {
            // Get settings tabs and sort them by priority.
            $tabs = apply_filters( 'rmpro_settings_tabs', array() );
            uasort( $tabs, function ( $a, $b ) {
                return ( isset( $a['priority'] ) ? $a['priority'] : 0 ) - ( isset( $b['priority'] ) ? $b['priority'] : 0 );
            });

            // Display a success message if settings are saved.
            if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) :
                echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings saved', 'review-master' ) . '</p></div>';
            endif;

            // Determine the current tab.
            $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

            // Include the settings page template.
            include RMPRO_PATH . 'includes/admin/views/settings.php';
        }

        /**
         * Render the Help & Support page.
         */
        public function help_support_page() {
            // Get settings tabs and sort them by priority.
            $tabs = apply_filters( 'rmpro_help_support_tabs', array() );
            uasort( $tabs, function ( $a, $b ) {
                return ( isset( $a['priority'] ) ? $a['priority'] : 0 ) - ( isset( $b['priority'] ) ? $b['priority'] : 0 );
            });

            // Display a success message if settings are saved.
            if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) :
                echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings saved', 'review-master' ) . '</p></div>';
            endif;

            // Determine the current tab.
            $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'shortcode';

            // Include the settings page template.
            include RMPRO_PATH . 'includes/admin/views/support.php';
        }

        // Hook into the form submission action

        public function save_review() {

            // Verify nonce security
            if ( ! isset( $_POST['rmpro_save_review'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rmpro_save_review'] ) ), 'rmpro_save_review_action' ) ) :
                wp_die( esc_html__( 'Security check failed. Please refresh and try again.', 'review-master' ) );
            endif;
        
            $review_id  =  isset( $_POST['review_id'] ) ? absint( $_POST['review_id'] ) : 0;
            $user_id    =  get_current_user_id();
            $response   =  wp_kses_post( wp_unslash( isset( $_POST['rmpro_review_respond'] ) ? $_POST['rmpro_review_respond'] : '' ) );
            $ratings    =  isset( $_POST['ratings'] ) ? array_map( 'absint', $_POST['ratings'] ) : [];

            // Prepare review data
            $args = array(
                'review_id'     => $review_id,
                'associate_id'  => sanitize_text_field( isset( $_POST['associate_id'] ) ? wp_unslash( $_POST['associate_id'] ) : '' ),
                'title'         => sanitize_text_field( isset( $_POST['rmpro_title'] ) ? wp_unslash( $_POST['rmpro_title'] ) : '' ),
                'name'          => sanitize_text_field( isset( $_POST['rmpro_name'] ) ? wp_unslash( $_POST['rmpro_name'] ) : '' ),
                'email'         => sanitize_email( isset( $_POST['rmpro_email'] ) ? wp_unslash( $_POST['rmpro_email'] ) : '' ),
                'ratings'       => $ratings,
                'status'        => sanitize_text_field( isset( $_POST['review_status'] ) ? wp_unslash( $_POST['review_status'] ) : 'pending' ),
                'ip_address'    => sanitize_text_field( isset( $_POST['rmpro_ip_address'] ) ? wp_unslash( $_POST['rmpro_ip_address'] ) : '' ),
                'your_review'   => wp_kses_post( wp_unslash( isset( $_POST['rmpro_review_content'] ) ? $_POST['rmpro_review_content'] : '' ) ),
                'score'         => isset( $_POST['score'] ) ? intval( $_POST['score'] ) : 0,
                'module_type'   => sanitize_text_field( isset( $_POST['module_type'] ) ? wp_unslash( $_POST['module_type'] ) : '' ),
                'module'        => sanitize_text_field( isset( $_POST['module'] ) ? wp_unslash( $_POST['module'] ) : '' ),
                'avg_rating'    => isset( $_POST['avg_rating'] ) ? floatval( wp_unslash( $_POST['avg_rating'] ) ) : 0,
            );
            // Save or update review
            $review_id  = rmpro_save_review( $args );
            $user_id    = get_current_user_id();
            $response   = sanitize_textarea_field( isset( $_POST['rmpro_review_respond'] ) ? wp_unslash( $_POST['rmpro_review_respond'] ) : '' );

            update_metadata( 'rmpro_reviews', $review_id, '_response', $response);
            update_metadata( 'rmpro_reviews', $review_id, '_response_by', $user_id );
            update_metadata( 'rmpro_reviews', $review_id, '_response_time', time() ); 

            // Handle success or failure
            if ( $review_id ) :
                wp_redirect( add_query_arg( array( 'updated' => 'true', 'message' => 'review_updated' ), wp_get_referer() ) );
                exit;
            else :
                wp_die( esc_html__( 'There was an error saving the review.', 'review-master' ) );
            endif;
        }
        

    }

    // Instantiate the RMPRO_Admin class.
    new RMPRO_Admin();

endif;