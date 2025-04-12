<?php 

if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

return apply_filters( 'rmpro_get_default_options',
    array(
        'rmpro_general' => array(
            'default_status'                    => 'approve',
            'require_approval_for'              => '5',
            'custom_login_url'                  => '',
            'custom_registration_url'           => '',
            'send_emails_from'                  => '',
            'notification_template'             => "Hello {name},\n\nHow are you? We received your feedback.\n\nHere’s a summary of your submission:\nEmail: {email}\nDescription: {description}\nIP Address: {ip_address}\nAverage Rating: {avg_rating}\n\nBest regards,\n{site_title}",
            'send_emails_from_author'           => '',
            'notification_template_author'      => "Hello {name},\n\nHow are you? We received your feedback.\n\nHere’s a summary of your submission:\nEmail: {email}\nDescription: {description}\nIP Address: {ip_address}\nAverage Rating: {avg_rating}\n\nBest regards,\n{site_title}",
            'send_emails_from_addresses'        => '',
            'notification_template_addresses'   => "Hello {name},\n\nHow are you? We received your feedback.\n\nHere’s a summary of your submission:\nEmail: {email}\nDescription: {description}\nIP Address: {ip_address}\nAverage Rating: {avg_rating}\n\nBest regards,\n{site_title}",
            'email_addresses'                   => '',
            'discord_webhook_url'               => '',
            'slack_webhook_url'                 => ''
        ),

        'rmpro_style-format' => array(
            'date_format'           => 'default',
            'custom_date_format'    => 'F j, Y',
            'enable_avatar'         => '1',
            'avatar_size'           => '40',
            'enable_excerpts'       => '1',
            'excerpt_length'        => '40'
        ),
        'rmpro_form' => array(
            'form_heading' => 'Review Master',
            'form'          => array(
                'title'     => array(
                    'enable'        => 'on',
                    'placeholder'   => 'Title',
                    'field_label'   => 'Title'
                ),
                'cat_rating' => array(
                    'enable'        => 'on',
                    'field_label'   => 'Rating'
                ),
                'name'      => array(
                    'enable'        => 'on',
                    'guest'         => 'on',
                    'placeholder'   => 'Name',
                    'field_label'   => 'Name'
                ),
                'email' => array(
                    'enable'        => 'on',
                    'guest'         => 'on',
                    'placeholder'   => 'Email',
                    'field_label'   => 'Email'
                ),
                'your_review' => array(
                    'enable'        => 'on',
                    'placeholder'   => 'Description',
                    'field_label'   => 'Description'
                )
            ),
            'button_label'          => 'Submit Review',
            'captcha_type'          => 'none',
            'site_key'              => '',
            'secret_key'            => '',
            'captcha_theme'         => 'light',
            'captcha_usage'         => 'everyone',
            'hcaptcha_site_key'     => '',
            'hcaptcha_secret_key'   => '',
            'limit_review'          => 'no-limit',
            'limit_time'            => '7',
            'email_whitelist'       => '',
            'ip_whitelist'          => '',
            'username_whitelist'    => '',
            'blacklist'             => 'no-blacklist',
            'blacklist_entries'     => '',
            'blacklist_action'      => 'require_approval'
        ),
        'rmpro_localization' => array(
            'message_already_submitted_email'       => 'Looks like this email has already been used to submit a review for this post.',
            'message_already_submitted_ip'          => 'We have already received a review from this IP.',
            'message_already_submitted_username'    => 'You have already submitted a review for this post.',
            'message_spam'                          => 'Oops! Your review has been flagged as spam.',
            'message_duplicate'                     => 'It looks as though you have already said that!',
            'message_pending'                       => 'Your review has been submitted and is awaiting approval.',
            'message_error'                         => 'Oops! Something went wrong. Please try again later.',
            'blacklist_entries_message'             => 'Your submission contains disallowed content and cannot be submitted.',
            'message_thank_you'                     => 'Thank you for your review! We appreciate your feedback.',
            'no_review'                             => 'No review found.',
            'reCAPTCHA'                             => 'reCAPTCHA validation failed. Please try again.',
            'hCAPTCHA'                              => 'hCAPTCHA validation failed. Please try again.'
        ),
    )
);
    