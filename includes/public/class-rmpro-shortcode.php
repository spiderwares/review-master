<?php
/**
 * RMPRO Reviews Class
 *
 * Handles the review shortcodes and displays reviews.
 *
 * @package RMPRO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RMPRO_Shortcode' ) ) {
    /**
     * Class RMPRO_Shortcode
     */
    class RMPRO_Shortcode {
        /**
         * Form settings options.
         *
         * @var array
         */
        private $form_settings;
        
        /**
         * Review categories options.
         *
         * @var array
         */
        private $rmpro_options;
        
        /**
         * General settings options.
         *
         * @var array
         */
        private $rmpro_general;

        /**
         * style_format settings options.
         *
         * @var array
         */
        private $style_format;

        /**
         * style_format settings options.
         *
         * @var array
         */
        public static $date_format;


        /**
         * Constructor.
         * Initializes the class and hooks into WordPress.
         */
        public function __construct() {
            $this->initialize_settings();
            $this->event_handler();
        }

        /**
         * Initialize global settings options.
         */
        private function initialize_settings() {
            $this->form_settings = get_option( 'rmpro_form', [] );
            $this->rmpro_options = get_option( 'rmpro_review_categories', [] );
            $this->rmpro_general = get_option( 'rmpro_general', [] );
            $this->style_format  = get_option( 'rmpro_style-format', [] );
            
            $this->set_date_format();
        }

        /**
         * Get Date Format.
         */
        public static function get_date_formated( $date ) {
            if ( empty( $date ) ) :
                return '';
            endif;
        
            return gmdate( self::$date_format, strtotime( $date ) );
        }
        
        /**
         * Set Date Format.
         */
        public function set_date_format(){
            if ( is_null( self::$date_format ) ) :
                $dateformat     = $this->style_format['date_format'];
                $customformat   = $this->style_format['custom_date_format'];

                self::$date_format = $dateformat === 'custom' ? ( empty( $customformat ) ? get_option( 'date_format' ) : $customformat ) : get_option( 'date_format' );
            endif;
        }


        /**
         * Register WordPress shortcodes.
         */
        private function event_handler() {
            add_filter( 'comments_template', array( $this, 'review_form_list_template' ), 999, 1 );

            add_shortcode( 'rmpro_reviews', array( $this, 'render_reviews' ) );
            add_shortcode( 'rmpro_review_form', array( $this, 'render_review_form' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }

        /**
         * Enqueue assets
         **/
        public function enqueue_scripts() {
            wp_enqueue_style( 'rmpro-frontend', RMPRO_URL . 'assets/css/rmpro-frontend.css', array(), RMPRO_VERSION, false );
            if ( isset( $this->form_settings['captcha_type'], $this->form_settings['captcha_usage'] ) && $this->form_settings['captcha_type'] === 'recaptcha-v2' && ( $this->form_settings['captcha_usage'] === 'everyone' || ( $this->form_settings['captcha_usage'] === 'guests' && ! is_user_logged_in() ) ) ) :
                wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), RMPRO_VERSION, true );
            endif;

            // Enqueue hCaptcha if selected
            if ( $this->form_settings['captcha_type'] === 'h_captcha' && ( $this->form_settings['captcha_usage'] === 'everyone' || ( $this->form_settings['captcha_usage'] === 'guests' && ! is_user_logged_in() ) ) ) :
                wp_enqueue_script( 'hcaptcha', 'https://js.hcaptcha.com/1/api.js', array( 'jquery' ), RMPRO_VERSION, true );
            endif;
        }

        /**
         * Displays an error message.
         *
         * @param string $message Error message to display.
         * @return string HTML formatted error message.
         */
        private function display_error( $message ) {
            return '<div class="rmpro-error" style="color: red; font-weight: bold;">' . esc_html( $message ) . '</div>';
        }

        /**
         * Determine module and module type based on object.
         *
         * @param object $object WordPress object.
         * @return array|false Module details or false if invalid.
         */
        private function get_module( $object ) {
            if ( $object instanceof WP_Term ) :
                return [
                    'module'      => $object->taxonomy,
                    'module_type' => 'taxonomy',
                ];
            endif;

            if ( $object instanceof WP_Post ) :
                return [
                    'module'      => get_post_type( $object ),
                    'module_type' => 'post_type',
                ];
            endif;

            if ( $object instanceof WP_User ) :
                return [
                    'module'      => 'user',
                    'module_type' => 'user',
                ];
            endif;
            return false;
        }

        /**
         * Validate required attributes.
         *
         * @param array $atts Attributes array.
         * @return bool|string Returns error message if validation fails.
         */
        private function validate_atts( $atts ) {
            if ( empty( $atts['module'] ) || empty( $atts['module_type'] ) || empty( $atts['associate_id'] ) ) :
                return $this->display_error( __( 'Error: Missing required parameters (module, module_type, associate_id).', 'review-master' ) );
            endif;
            return true;
        }

        /**
         * Renders the reviews via shortcode.
         *
         * @param array $atts Shortcode attributes.
         * @return string HTML output for reviews or error message.
         */
        public function render_reviews( $atts ) {
            $atts = shortcode_atts(
                [
                    'module'            => [],
                    'module_type'       => '',
                    'per_page'          => 5,
                    'associate_id'      => '',
                    'enable_summary'    => 'on',
                    'enable_avatar'     => isset( $this->style_format['enable_avatar'] ) && ! empty( $this->style_format['enable_avatar'] ) ? $this->style_format['enable_avatar'] : false,
                    'avatar_size'       => isset( $this->style_format['avatar_size'] ) ? $this->style_format['avatar_size'] : 50,
                    'enable_excerpts'   => isset( $this->style_format['enable_excerpts'] ) && ! empty( $this->style_format['enable_excerpts'] ) ? $this->style_format['enable_excerpts'] : false,
                    'excerpt_length'    => isset( $this->style_format['excerpt_length'] ) ? $this->style_format['excerpt_length'] : 80,
                ],
                $atts
            );

            $validation = $this->validate_atts( $atts );
            if ( ! $validation ) :
                return $validation;
            endif;

            $args = [
                'status'       => [ 'approve' ],
                'per_page'     => absint( $atts['per_page'] ),
                'current_page' => max( 1, get_query_var( 'paged', 1 ) ),
                'module'       => $atts['module'],
                'associate_id' => absint( $atts['associate_id'] ),
            ];

            $reviews        = rmpro_get_reviews( $args );
            $review_summary = rmpro_get_rating_summary( $args );
            
            $total_pages    = max( 1, ceil( $review_summary['summary']['total_reviews'] / $args['per_page'] ) );
            $localization   = get_option( 'rmpro_localization', [] );

            ob_start();
            rmpro_get_template(
                'reviews.php',
                apply_filters(
                    'rmpro_reviews_template_args',
                    array( 
                        'reviews'           => $reviews, 
                        'review_summary'    => $review_summary['summary'],
                        'category_summary'  => $review_summary['category_summary'],
                        'total_pages'       => $total_pages, 
                        'localization'      => $localization,
                        'per_page'          => $atts['per_page'],
                        'current_page'      => $args['current_page'], 
                        'module'            => $atts['module'], 
                        'module_type'       => $atts['module_type'],
                        'associate_id'      => $atts['associate_id'],
                        'enable_avatar'     => $atts['enable_avatar'],
                        'avatar_size'       => $atts['avatar_size'],
                        'enable_excerpts'   => $atts['enable_excerpts'],
                        'excerpt_length'    => $atts['excerpt_length'],
                        'associate_id'      => $atts['associate_id'],
                        'enable_summary'    => $atts['enable_summary'],
                    )
                )
            );
            return ob_get_clean();
        }

        /**
         * Renders the review form via shortcode.
         *
         * @param array $atts Shortcode attributes.
         * @return string HTML output for the review form or error message.
         */
        public function render_review_form( $atts ) {
            $atts = shortcode_atts(
                [
                    'module'       => '',
                    'module_type'  => '',
                    'associate_id' => '',
                    'title'        => '',
                    'button_label' => '',
                    'categories'   => '',
                ],
                $atts
            );

            $validation = $this->validate_atts( $atts );
            if ( ! $validation ) :
                return $validation;
            endif;

            $is_logged_in  = is_user_logged_in();
            $title         = sanitize_text_field( $atts['title'] );
            $button_label  = sanitize_text_field( $atts['button_label'] );
            $module_type   = sanitize_text_field( $atts['module_type'] );
            $module        = sanitize_text_field( $atts['module'] );
            $associate_id  = $atts['associate_id'] === 'queried' ? get_queried_object_id() : sanitize_text_field( $atts['associate_id'] );
            $categories    = explode( ',', $atts['categories'] );

            $fields        = isset( $this->form_settings['form'] ) ? $this->form_settings['form'] : [];
            $categories    = ! empty( $categories )
                                ? $categories
                                : ( $this->rmpro_options[$module]['category'] ? $this->rmpro_options[$module]['category'] : [] );
     
                                
            $form_heading  = $title ? $title : ( isset( $this->form_settings['form_heading'] ) ? $this->form_settings['form_heading'] : '' );
            $button_label  = $button_label ? $button_label : ( isset( $this->form_settings['button_label'] ) ? $this->form_settings['button_label'] : esc_html__( 'Submit', 'review-master' ) );

            if ( $is_logged_in && isset( $this->form_settings['autonamemail'] ) ) :
                unset( $fields['name'], $fields['email'] );
            endif;

            ob_start();
            rmpro_get_template(
                'review-form.php',
                apply_filters(
                    'rmpro_review_form_template_args',
                    array( 
                        'fields'            => $fields,
                        'form_heading'      => $form_heading,
                        'button_label'      => $button_label,
                        'is_logged_in'      => $is_logged_in,
                        'categories'        => $categories,
                        'module_type'       => $module_type,
                        'module'            => $module,
                        'form_settings'     => $this->form_settings,
                        'rmpro_general'     => $this->rmpro_general,
                    )
                )
            );
            return ob_get_clean();
        }

        /**
         * replace wordpress comment form with review form
         **/
        public function review_form_list_template( $template ) {

            $object         = get_queried_object();
            $moduleatts     = $this->get_module( $object );
            $module         = isset( $moduleatts['module'] ) ? $moduleatts['module'] : false;
            $module_type    = isset( $moduleatts['module_type'] ) ? $moduleatts['module_type'] : false;

            if ( isset( $this->rmpro_options[ $module ]['enable'] ) && $this->rmpro_options[ $module ]['enable'] ) :

                $is_logged_in   = is_user_logged_in();
                $associate_id   = get_queried_object_id();

                $per_page       = isset($this->rmpro_options[$module]['per_page']) && !empty($this->rmpro_options[$module]['per_page']) ? $this->rmpro_options[$module]['per_page'] : 5;
                $enable_summary = isset($this->rmpro_options[$module]['enable_summary']) ? $this->rmpro_options[$module]['enable_summary'] : 'off';

                $fields         = isset( $this->form_settings['form'] ) ? $this->form_settings['form'] : [];
                $categories     = isset( $this->rmpro_options[$module]['category'] ) ? $this->rmpro_options[$module]['category'] : [];
                        
                $form_heading   = isset( $this->form_settings['form_heading'] ) ? $this->form_settings['form_heading'] : '';
                $button_label   = isset( $this->form_settings['button_label'] ) ? $this->form_settings['button_label'] : esc_html__( 'Submit', 'review-master' );

                if ( $is_logged_in && isset( $this->form_settings['autonamemail'] ) ) :
                    unset( $fields['name'], $fields['email'] );
                endif;

                $current_page = max( 1, get_query_var( 'paged', 1 ) );
                $args =  apply_filters(
                    'rmpro_get_reviews_args',
                    array(
                        'status'       => [ 'approve' ],
                        'per_page'     => $per_page,
                        'current_page' => $current_page,
                        'module'       => [ $module ],
                        'associate_id' => $associate_id,
                    )
                );

                $reviews        = rmpro_get_reviews( $args );
                $review_summary = rmpro_get_rating_summary( $args );
                $total_pages    = max( 1, ceil( $review_summary['summary']['total_reviews'] / $per_page ) );
                $start_page     = max( 1, $current_page - absint( 3 ) );
                $end_page       = min( $total_pages, $current_page + absint( 3 ) );            
                $localization   = get_option( 'rmpro_localization', [] );

                rmpro_get_template(
                    'reviews.php',
                    apply_filters(
                        'rmpro_reviews_template_args',
                        array( 
                            'reviews'           => $reviews,
                            'review_summary'    => $review_summary['summary'],
                            'category_summary'  => $review_summary['category_summary'],
                            'total_pages'       => $total_pages,
                            'current_page'      => $current_page,
                            'module_type'       => $module_type,
                            'module'            => $module,
                            'per_page'          => $per_page,
                            'associate_id'      => $associate_id,
                            'enable_summary'    => $enable_summary,
                            'enable_avatar'     => isset( $this->style_format['enable_avatar'] ) ? $this->style_format['enable_avatar'] : false,
                            'avatar_size'       => isset( $this->style_format['avatar_size'] ) ? $this->style_format['avatar_size'] : 40,
                            'enable_excerpts'   => isset( $this->style_format['enable_excerpts'] ) ? $this->style_format['enable_excerpts'] : true,
                            'excerpt_length'    => isset( $this->style_format['excerpt_length'] ) ? $this->style_format['excerpt_length'] : 80,
                            'localization'      => $localization
                        )
                    )
                );

                rmpro_get_template(
                    'review-form.php',
                    apply_filters(
                        'rmpro_review_form_template_args',
                        array( 
                            'fields'            => $fields,
                            'form_heading'      => $form_heading,
                            'button_label'      => $button_label,
                            'is_logged_in'      => $is_logged_in,
                            'categories'        => $categories,
                            'module_type'       => $module_type,
                            'module'            => $module,
                            'associate_id'      => $associate_id,
                            'form_settings'     => $this->form_settings,
                            'rmpro_general'     => $this->rmpro_general,
                        )
                    )
                );
                return RMPRO_PATH . 'templates/empty-template.php';

            endif;

            return $template;
        }
    }

    // Initialize the class.
    new RMPRO_Shortcode();
}