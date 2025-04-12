<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

if ( ! class_exists( 'RMPRO_Notification' ) ) :

class RMPRO_Notification {

    /**
     * Holds placeholders for email templates
     * @var array
     */
    private $placeholders = array();

    /**
     * Constructor - Registers event handlers
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Registers WordPress hooks for notifications
     */
    private function register_hooks() {
        add_action( 'rmpro_review_submited', array( $this, 'send_admin_notification' ), 10, 3 );
        add_action( 'rmpro_review_submited', array( $this, 'send_author_notification' ), 10, 3 );
        add_action( 'rmpro_review_submited', array( $this, 'send_other_notification' ), 10, 3 );
    }

    /**
     * Get available placeholders with descriptions
     * This is used for both settings display and email processing
     *
     * @return array List of placeholders with their default values
     */
    public static function get_placeholders() {
        return apply_filters( 
            'rmpro_email_placeholders', 
            array(
                '{name}',
                '{email}',
                '{description}',
                '{ip_address}',
                '{avg_rating}',
                '{categories_rating}',
                '{status}',
                '{module}',
                '{module_type}',
                '{site_title}',
                '{site_url}'
            )
        );
    }

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

    /**
     * Set placeholders dynamically using review data
     *
     * @param array $review The submitted review data
     */
    private function set_placeholders( $review ) {
        $default_placeholders = self::get_placeholders();
        $categories_rating    = [];
        if ( isset( $review['ratings'] ) ) :
            foreach ( $review['ratings'] as $category => $rating ) :
                $solid_stars = str_repeat( '★', $rating );
                $empty_stars = str_repeat( '☆', 5 - $rating );
                
                $categories_rating[] = sprintf( '<b>%s</b>: %s%s', esc_html( $category ), $solid_stars, $empty_stars );
            endforeach;
        endif;
        

        // Fill placeholders with actual review data
        $this->placeholders['{name}']               = isset( $review['name'] ) ? $review['name'] : '';
        $this->placeholders['{email}']              = isset( $review['email'] ) ? $review['email'] : '';
        $this->placeholders['{description}']        = isset( $review['description'] ) ? $review['description'] : '';
        $this->placeholders['{ip_address}']         = isset( $review['ip_address'] ) ? $review['ip_address'] : '';
        $this->placeholders['{status}']             = isset( $review['status'] ) ? $review['status'] : '';
        $this->placeholders['{avg_rating}']         = isset( $review['avg_rating'] ) ? $review['avg_rating'] : '';
        $this->placeholders['{categories_rating}']  = implode( '\n', (array)$categories_rating );
        $this->placeholders['{module}']             = isset( $review['module'] ) ? $review['module'] : '';
        $this->placeholders['{module_type}']        = isset( $review['module_type'] ) ? $review['module_type'] : '';
        $this->placeholders['{site_title}']         = $this->get_blogname();
        $this->placeholders['{site_url}']           =  home_url();
    }

    /**
     * Replace placeholders in a given text
     *
     * @param string $text The text containing placeholders
     * @return string Processed text with replaced placeholders
     */
    private function replace_placeholders( $text ) {
        return str_replace(array_keys($this->placeholders), array_values($this->placeholders), $text);
    }

    /**
     * Sends an email notification
     *
     * @param string $to Recipient email(s)
     * @param string $subject Email subject
     * @param string $template Email template content
     * @param string $from Sender email
     */
    private function send_email( $to, $subject, $email_content, $from ) {
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $from
        );

        wp_mail( $to, $subject, $email_content, $headers );
    }

    /**
     * Sends notification to the administrator
     *
     * @param array $rmpro_general General settings for notifications
     * @param array $review The submitted review data
     */
    public function send_admin_notification( $rmpro_general, $review_id, $data ) {
        if ( ! empty( $rmpro_general['send_to_administrator'] ) ) :
            $this->set_placeholders( $data );

            $template       = isset( $rmpro_general['notification_template'] ) ? $rmpro_general['notification_template'] : '';
            $email_content  = $this->replace_placeholders( $template );

            $this->send_email(
                get_option( 'admin_email' ),
                esc_html__( 'New Review', 'review-master' ),
                $email_content,
                $rmpro_general['send_emails_from'] ?? get_option('admin_email')
            );
        endif;
    }

    /**
     * Sends notification to the review author
     *
     * @param array $rmpro_general General settings for notifications
     * @param array $review The submitted review data
     */
    public function send_author_notification( $rmpro_general, $review_id, $data ) {
        if ( ! empty( $rmpro_general['send_to_author'] ) && isset( $review['email'] ) ) :
            $this->set_placeholders( $data );

            $template       = isset( $rmpro_general['notification_template_author'] ) ? $rmpro_general['notification_template_author'] : '';
            $email_content  = $this->replace_placeholders( $template );

            $this->send_email(
                $review['email'],
                esc_html__( 'Your Review Submission', 'review-master' ),
                $email_content,
                $rmpro_general['send_emails_from_author'] ?? get_option('admin_email')
            );
        endif;
    }

    /**
     * Sends notification to other specified email addresses
     *
     * @param array $rmpro_general General settings for notifications
     * @param array $review The submitted review data
     */
    public function send_other_notification( $rmpro_general, $review_id, $data ) {
        if ( ! empty( $rmpro_general['email_addresses'] ) ) :
            $this->set_placeholders( $data );

            $to = is_array($rmpro_general['email_addresses']) ? implode(',', $rmpro_general['email_addresses']) : $rmpro_general['email_addresses'];

            $template       = isset( $rmpro_general['notification_template_addresses'] ) ? $rmpro_general['notification_template_addresses'] : '';
            $email_content  = $this->replace_placeholders( $template );

            $this->send_email(
                $to,
                esc_html__( 'New Review Notification', 'review-master' ),
                $email_content,
                $rmpro_general['send_emails_from_addresses'] ?? get_option('admin_email')
            );
        endif;
    }
}

// Instantiate the class to activate its functionality
new RMPRO_Notification();

endif;
