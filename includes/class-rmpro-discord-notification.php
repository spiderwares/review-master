<?php

/**
 * RMPRO Discord Notification
 *
 * Handles sending review notifications to Discord via webhook.
 *
 * @package ReviewMaster
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

if ( ! class_exists( 'RMPRO_Discord_Notification' ) ) :

    class RMPRO_Discord_Notification {

        /**
         * Constructor to initialize event handler.
         */
        public function __construct() {
            $this->event_handler();
        }

        /**
         * Registers the event handler for review submission.
         */
        public function event_handler() {
            add_action( 'rmpro_review_submited', array( $this, 'send' ), 10, 3 );
        }

        /**
         * Sends the formatted review message to Discord.
         *
         * @param array $options  Plugin options.
         * @param int   $review_id Review ID.
         * @param array $review   Review data.
         */
        public function send( $options, $review_id, $review ) {
            if ( empty( $options['send_to_discord'] ) || empty( $options['discord_webhook_url'] ) ) {
                return false;
            }
            
            $discord_webhook = esc_url_raw( $options['discord_webhook_url'] );
            $payload = $this->get_content( $options, $review, $review_id );

            wp_remote_post( $discord_webhook, [
                'body'    => wp_json_encode( $payload ),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'method'  => 'POST'
            ]);
        }

        /**
         * Formats the review as a Discord message.
         */
        private function get_content( $options, $review, $review_id ) {
            return [
                'content' => $this->header( $review ),
                'embeds'  => [
                    [
                        'color'       => 16776960,
                        'title'       => $this->title( $review ),
                        'description' => $this->description( $review ),
                        'fields'      => $this->fields( $review, $review_id ),
                    ],
                ],
            ];
        }

        /**
         * Generates the message header.
         */
        private function header( $review ) {
            $site_title = get_bloginfo( 'name' );
            $avg_rating = isset( $review['ratings'] ) ? rmpro_calculate_average_rating( $review['ratings'] ) : 'N/A';
            return '[' . esc_html( $site_title ) . '] ' . esc_html__( 'New Review ', 'review-master' ) . $avg_rating;
        }

        /**
         * Gets the review title.
         */
        private function title( $review ) {
            return ! empty( $review['title'] ) ? esc_html( trim( $review['title'] ) ) : esc_html__( '(no title)', 'review-master' );
        }

        /**
         * Constructs the review description.
         */
        private function description( $review ) {
            $description = isset( $review['description'] ) ? $review['description'] : '';
            return trim( mb_substr( $description, 0, 1999 ) );
        }

        /**
         * Generates review ratings formatted with stars.
         */
        private function ratings( $review ) {
            $ratings = isset( $review['ratings'] ) ? $review['ratings'] : [];
            $formatted_ratings = [];

            foreach ( $ratings as $category => $rating ) {
                $solid_stars = str_repeat( '★', $rating );
                $empty_stars = str_repeat( '☆', 5 - $rating );
                $formatted_ratings[] = esc_html( "{$category}: {$solid_stars}{$empty_stars}" );
            }

            return $formatted_ratings;
        }

        /**
         * Generates the review fields for Discord embed.
         */
        private function fields( $review, $review_id ) {
            return [
                [
                    'name'   => esc_html( 'Name', 'review-master' ),
                    'value'  => isset( $review['name'] ) ? esc_html( $review['name'] ) : 'N/A',
                    'inline' => true,
                ],
                [
                    'name'   => esc_html( 'Email', 'review-master' ),
                    'value'  => isset( $review['email'] ) ? esc_html( $review['email'] ) : 'N/A',
                    'inline' => true,
                ],
                [
                    'name'   => esc_html( 'IP Address', 'review-master' ),
                    'value'  => isset( $review['ip_address'] ) ? esc_html( $review['ip_address'] ) : 'N/A',
                    'inline' => true,
                ],
                [
                    'name'   => esc_html( 'Actions', 'review-master' ),
                    'value'  => $this->action_button( $review, $review_id ),
                    'inline' => false,
                ],
            ];
        }

        /**
         * Generates the review actions for Discord embed.
         */
        protected function action_button( $review, $review_id ) {
            $action_links = [];
            $actionObj    = new RMPRO_Permalink_Action_Handler();

            if ( isset( $review['status'] ) && $review['status'] != 'approve' ) :
                $action_links[] = sprintf( '[%s](%s)', esc_html__( 'Approve', 'review-master' ), esc_url( $actionObj->get_review_status_update_url( $review_id, 'approve' ) ) );
            else :
                $action_links[] = sprintf( '[%s](%s)', esc_html__( 'Unapprove', 'review-master' ), esc_url( $actionObj->get_review_status_update_url( $review_id, 'unapprove' ) ) );
                $action_links[] = sprintf( '[%s](%s)', esc_html__( 'Spam', 'review-master' ), esc_url( $actionObj->get_review_status_update_url( $review_id, 'spam' ) ) );
            endif;

            $action_links[] = sprintf( '[%s](%s)', esc_html__( 'Edit', 'review-master' ), esc_url( rmpro_get_review_edit_url( $review_id ) ) );
            return implode( ' | ', $action_links );
        }
    }

    new RMPRO_Discord_Notification();

endif;