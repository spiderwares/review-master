<?php

/**
 * Slack Notification Handler for Review Master
 *
 * @package ReviewMaster
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) :
    exit;
endif;

if ( ! class_exists( 'RMPRO_Slack_Notification' ) ) :

    class RMPRO_Slack_Notification {

        /**
         * Constructor: Hooks into WordPress event system.
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
         * Sends the message to Slack.
         */
        public function send( $options, $review_id, $review ) {
            $slack_webhook = $this->get_webhook_url( $options, $review );
            
            if ( empty( $slack_webhook ) || ! $options['send_to_slack'] ) :
                return false;
            endif;

            $content  = $this->get_content( $options, $review, $review_id );
            wp_remote_post($slack_webhook, [
                'body'    => wp_json_encode($content),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'method'  => 'POST',
            ]);

        }

        /**
         * Retrieves the Slack webhook URL.
         */
        public function get_webhook_url( $options, $review ) {
            return apply_filters( 'rmpro_get_slack_webhook_url', $options['slack_webhook_url'], $options, $review );
        }

        /**
         * Formats the review as a Slack Block Kit message.
         */
        private function get_content( $options, $review, $review_id ) {
            return apply_filters( 
                'rmpro_get_slack_content', 
                [
                    'blocks' => array_merge(
                        [
                            $this->header( $review ),
                            $this->title( $review ),
                        ],
                        $this->ratings( $review ),
                        [
                            $this->content( $review ),
                            $this->fields( $review ),
                            $this->action_button( $review, $review_id ),
                        ]
                    ),
                ],
                $options, 
                $review,
                $review_id
            );
        }

        /**
         * Generates the Slack message header.
         */
        protected function header( $review ) {
            $site_title = get_bloginfo( 'name' );
            $avg_rating = rmpro_calculate_average_rating( $review['ratings'] );
            
            return [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => sprintf( '%s New Review %s', $site_title, $avg_rating ),
                ],
            ];
        }

        /**
         * Generates the title section.
         */
        protected function title( $review ): array {
            $title = ! empty( trim( $review['title'] ) ) ? $review['title'] : esc_html__( '(no title)', 'review-master' );

            return [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $title,
                ],
            ];
        }

        /**
         * Generates rating sections.
         */
        protected function ratings( $review ): array {
            $rating_blocks = [];
            
            if ( ! empty( $review['ratings'] ) ) :
                foreach ( $review['ratings'] as $category => $rating ) :
                    $solid_stars = str_repeat( '★', $rating );
                    $empty_stars = str_repeat( '☆', 5 - $rating );
                    
                    $rating_blocks[] = [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => sprintf( '*%s*: %s%s', esc_html( $category ), $solid_stars, $empty_stars ),
                        ],
                    ];
                endforeach;
            endif;
            
            return $rating_blocks;
        }

        /**
         * Generates content section.
         */
        protected function content( $review ): array {
            return empty( trim( $review['description'] ) ) ? [] : [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $review['description'],
                ],
            ];
        }

        /**
         * Generates fields section.
         */
        protected function fields( $review ): array {
            return [
                'type'   => 'section',
                'fields' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => sprintf( '*Name:* %s', esc_html( $review['name'] ) ),
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => sprintf( '*Email:* %s', esc_html( $review['email'] ) ),
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => sprintf( '*IP Address:* %s', esc_html( $review['ip_address'] ) ),
                    ],
                ],
            ];
        }

        /**
         * Generates action buttons.
         */
        protected function action_button( $review, $review_id ): array {
            $elements   = [];
            $actionObj  = new RMPRO_Permalink_Action_Handler();

            if ( 'approve' !== $review['status'] ) :
                $elements[] = [
                    'type' => 'button',
                    'text' => [ 'type' => 'plain_text', 'text' => esc_html__( 'Approve', 'review-master' ) ],
                    'url'  => $actionObj->get_review_status_update_url( $review_id, 'approve' ),
                ];
            else :
                $elements[] = [
                    'type' => 'button',
                    'text' => [ 'type' => 'plain_text', 'text' => esc_html__( 'Unapprove', 'review-master' ) ],
                    'url'  => $actionObj->get_review_status_update_url( $review_id, 'unapprove' ),
                ];
                $elements[] = [
                    'type' => 'button',
                    'text' => [ 'type' => 'plain_text', 'text' => esc_html__( 'Spam', 'review-master' ) ],
                    'url'  => $actionObj->get_review_status_update_url( $review_id, 'spam' ),
                ];
            endif;

            $elements[] = [
                'type' => 'button',
                'text' => [ 'type' => 'plain_text', 'text' => esc_html__( 'Edit', 'review-master' ) ],
                'url'  => rmpro_get_review_edit_url( $review_id ),
            ];

            return [ 'type' => 'actions', 'elements' => $elements ];
        }
    }

    new RMPRO_Slack_Notification();

endif;
