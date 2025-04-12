<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>

<div id="localization-messages" class="rmpro-tab-content">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Already Submitted - Email', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_already_submitted_email]" 
                       value="<?php echo esc_attr( isset( $this->settings['message_already_submitted_email'] ) ? $this->settings['message_already_submitted_email'] : 'Looks like this email has already been used to submit a review for this post.' ); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Already Submitted - IP', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_already_submitted_ip]" 
                       value="<?php echo esc_attr( isset( $this->settings['message_already_submitted_ip'] ) ? $this->settings['message_already_submitted_ip'] : 'We have already received a review from this IP.' ); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Already Submitted - Username', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_already_submitted_username]" 
                       value="<?php echo esc_attr( isset( $this->settings['message_already_submitted_username'] ) ? $this->settings['message_already_submitted_username'] : 'You have already submitted a review for this post.' ); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Marked as Spam', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_spam]" 
                       value="<?php echo esc_attr( isset( $this->settings['message_spam'] ) ? $this->settings['message_spam'] : 'Oops! Your review has been flagged as spam.' ); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Duplicate Review', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_duplicate]" 
                       value="<?php echo esc_attr( isset( $this->settings['message_duplicate'] ) ? $this->settings['message_duplicate'] : 'It looks as though you have already said that!' ); ?>" />
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Pending Review Status Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_pending]" 
                    value="<?php echo esc_attr( isset( $this->settings['message_pending'] ) ? $this->settings['message_pending'] : 'Your review has been submitted and is awaiting approval.' ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Error Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_error]" 
                    value="<?php echo esc_attr( isset( $this->settings['message_error'] ) ? $this->settings['message_error'] : 'Oops! Something went wrong. Please try again later.' ); ?>" />
            </td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Blacklist Entries Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[blacklist_entries_message]" 
                       value="<?php echo esc_attr( isset( $this->settings['blacklist_entries_message'] ) ? $this->settings['blacklist_entries_message'] : 'Your submission contains disallowed content and cannot be submitted.' ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Thank You Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[message_thank_you]" 
                    value="<?php echo esc_attr( isset( $this->settings['message_thank_you'] ) ? $this->settings['message_thank_you'] : 'Thank you for your review! We appreciate your feedback.' ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'No Review Found Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[no_review]" 
                    value="<?php echo esc_attr( isset( $this->settings['no_review'] ) ? $this->settings['no_review'] : 'No review found.' ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'reCAPTCHA validation Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[reCAPTCHA]" 
                    value="<?php echo esc_attr( isset( $this->settings['reCAPTCHA'] ) ? $this->settings['reCAPTCHA'] : 'reCAPTCHA validation failed. Please try again.' ); ?>" />
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'hCAPTCHA validation Message', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[hCAPTCHA]" 
                    value="<?php echo esc_attr( isset( $this->settings['hCAPTCHA'] ) ? $this->settings['hCAPTCHA'] : 'hCAPTCHA validation failed. Please try again.' ); ?>" />
            </td>
        </tr>
        
    </table>
</div>