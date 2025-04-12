<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

if ( ! class_exists( 'RMPRO_Duplication' ) ) :

    class RMPRO_Duplication {

        public function rmpro_duplication_review( $associate_id, $name, $email, $description, $ip_address ) {
            
            global $wpdb;

            $count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->rmpro_reviews} 
                     WHERE associate_id = %d 
                     AND email = %s 
                     AND ip_address = %s 
                     AND name = %s",
                    $associate_id,
                    $email,
                    $ip_address,
                    $name
                )
            );
        
            return $count > 0;
        }
    }

    new RMPRO_Duplication();

endif;
