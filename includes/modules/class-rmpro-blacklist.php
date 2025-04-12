<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

if ( ! class_exists( 'RMPRO_Blacklist' ) ) :

    class RMPRO_Blacklist {

        public function rmpro_blacklist( $data, $blacklist_entries ) {

            $mod_keys = trim( $blacklist_entries );

            if ( '' === $mod_keys ) :
                return false; // If moderation keys are empty.
            endif;

            $name       = $data['name'];
            $email      = $data['email'];
            $comment    = $data['description'];
            $ip_address = $data['ip_address'];

            // Ensure HTML tags are not being used to bypass the list of disallowed characters and words.
            $comment_without_html = wp_strip_all_tags( $comment );

            $words = explode( ",", $mod_keys );

            foreach ( (array) $words as $word ) :
                $word = trim( $word );

                // Skip empty lines.
                if ( empty( $word ) ) :
                    continue; 
                endif;

                // Do some escaping magic so that '#' chars in the spam words don't break things:
                $word = preg_quote( $word, '#' );

                $pattern = "#$word#iu";

                if ( preg_match( $pattern, $name )
                    || preg_match( $pattern, $email )
                    || preg_match( $pattern, $comment )
                    || preg_match( $pattern, $comment_without_html )
                    || preg_match( $pattern, $ip_address )
                ) :
                    return true;
                endif;
            endforeach;
            return false;
        }
    }

    new RMPRO_Blacklist();

endif;