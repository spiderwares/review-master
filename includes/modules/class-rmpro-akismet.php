<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

if ( ! class_exists( 'RMPRO_Akismet' ) ) :

    class RMPRO_Akismet {

        public function rmpro_akismet_review( $associate_id, $name, $email, $description, $ip_address, $form_settings ) {
            // Prepare data for Akismet
            $api_key    = isset( $form_settings['akismet_api_key'] ) ? $form_settings['akismet_api_key'] : '';

            if ( empty( $api_key ) ) :
                return false; // API key not set, return false.
            endif;

            $blog_url   = site_url(); // Get the WordPress site URL
            $submission = [
                'blog'                  => $blog_url,
                'blog_lang'             => get_locale(),
                'user_ip'               => $ip_address,
                'referrer'              => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
                'user_agent'            => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                'comment_author'        => $name,
                'comment_author_email'  => $email,
                'comment_content'       => $name . "\n\n" . $description,
                'comment_type'          => 'review',
            ];

            // Make the request to Akismet to check for spam
            $url = "https://{$api_key}.rest.akismet.com/1.1/comment-check";
            $response = wp_remote_post(
                $url,
                [
                    'method'  => 'POST',
                    'body'    => http_build_query( $submission ), // Properly encode the data for the request
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded', // Set content type header
                    ]
                ]
            );

            // Check if there was an error with the response
            if ( is_wp_error( $response ) ) :
                return false;
            endif;

            // Get the body of the response
            $response_body = wp_remote_retrieve_body( $response );

            // Check if the response is 'true', meaning it's spam
            if ( $response_body === 'true' ) :
                return true; // Spam detected
            endif;

            return false; // Not spam
        }
    }

    new RMPRO_Akismet();

endif;
