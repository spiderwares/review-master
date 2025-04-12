<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>

<div id="general-settings" class="rmpro-tab-content">
    <table class="form-table">
    <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Default Status On submit', 'review-master' ); ?></th>
            <td>
                <select 
                    class="regular-text"
                    name="rmpro_<?php echo esc_attr( $current_tab );?>[default_status]">
                    <option <?php selected( "approve", $this->settings['default_status'] ); ?> value="approve"><?php echo esc_html( 'Approve', 'review-master' ); ?></option>
                    <option <?php selected( "spam", $this->settings['default_status'] ); ?> value="spam"><?php echo esc_html( 'Spam', 'review-master' ); ?></option>
                    <option <?php selected( "unapprove", $this->settings['default_status'] ); ?> value="unapprove"><?php echo esc_html( 'Unapprove', 'review-master' ); ?></option>
                </select>
            </td>
        </tr>

        <!-- Require Approval -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Require Approval', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" 
                        id="require-approval" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[require_approval]" 
                        data-show="#require-approval-options"
                        value="1" 
                        class="rmpro-toggle-required"
                        <?php checked( isset( $this->settings['require_approval'] ) && $this->settings['require_approval'] ); ?> 
                    />
                    <label for="require-approval" class="rmpro-switch-label"></label>
                    <label for="require-approval"><?php esc_html_e( 'Enable approval requirement', 'review-master' ); ?></label>
                </div>
                <div id="require-approval-options" style="<?php echo esc_attr( isset( $this->settings['require_approval'] ) ? '' : 'display: none;' ); ?>" >
                    <table>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Require Approval For', 'review-master' ); ?></th>
                            <td>
                                <select 
                                    class="regular-text"
                                    name="rmpro_<?php echo esc_attr( $current_tab ); ?>[require_approval_for]">
                                    <?php for ( $i = 5; $i >= 1; $i-- ) : 
                                        // Translators: %s represents the number of stars
                                        printf(
                                            '<option value="%s" %s>%s</option>',
                                            esc_attr( $i ),
                                            selected( isset( $this->settings['require_approval_for'] ) ? $this->settings['require_approval_for'] : '', $i, false ),
                                            esc_html(
                                                sprintf(
                                                    /* translators: %s represents the number of stars */
                                                    _n( '%s star or less', '%s stars or less', $i, 'review-master' ),
                                                    esc_html( $i )
                                                )
                                            )
                                        );
                                    endfor; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

        </tr>

        <!-- Require Login -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Require Login', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="require-login" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[require_login]" 
                        data-show=".require-login-options"
                        value="1" 
                        <?php checked( isset( $this->settings['require_login'] ) && $this->settings['require_login'] ); ?> />
                    <label for="require-login" class="rmpro-switch-label"></label>
                    <label for="require-login"><?php esc_html_e( 'Enable login requirement', 'review-master' ); ?></label>
                </div>
                <div class="require-login-options" style="<?php echo esc_attr( isset( $this->settings['require_login'] ) ? '' : 'display: none;' ); ?>">
                    <table>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Custom Login URL', 'review-master' ); ?></th>
                            <td>
                                <input class="regular-text" type="url" name="rmpro_<?php echo esc_attr( $current_tab );?>[custom_login_url]" value="<?php echo esc_attr( isset( $this->settings['custom_login_url'] ) ? $this->settings['custom_login_url'] : '' ); ?>" />
                            </td>
                        </tr>
                        <!-- Show Registration Link -->
                        <tr valign="top" class="require-login-options" style="<?php echo esc_attr( isset( $this->settings['require_login'] ) ? '' : 'display: none;' ); ?>">
                            <th scope="row"><?php esc_html_e( 'Show Registration Link', 'review-master' ); ?></th>
                            <td>
                                <div class="rmpro-switch">
                                    <input type="checkbox" id="show-registration-link" 
                                        name="rmpro_<?php echo esc_attr( $current_tab );?>[show_registration_link]" 
                                        data-show=".registration-link-options"
                                        value="1" 
                                        <?php checked( isset( $this->settings['show_registration_link'] ) && $this->settings['show_registration_link'] ); ?> />
                                    <label for="show-registration-link" class="rmpro-switch-label"></label>
                                    <label for="show-registration-link"><?php esc_html_e( 'Enable registration link', 'review-master' ); ?></label>
                                </div>
                            </td>
                        </tr>
                        
                        <tr class="registration-link-options"  style="<?php echo esc_attr( isset( $this->settings['require_login'] ) && isset( $this->settings['show_registration_link'] ) ? '' : 'display: none;' ); ?>">
                            <th scope="row"><?php esc_html_e( 'Custom Registration URL', 'review-master' ); ?></th>
                            <td>
                                <input class="regular-text" type="url" name="rmpro_<?php echo esc_attr( $current_tab );?>[custom_registration_url]" value="<?php echo esc_attr( isset( $this->settings['custom_registration_url'] ) ? $this->settings['custom_registration_url'] : '' ); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        
        <tr>
            <th scope="row"><?php esc_html_e( 'Send to Administrator', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="send-to-administrator" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[send_to_administrator]" 
                        data-show=".administrator-notifications-options"
                        value="1"
                        <?php checked( isset( $this->settings['send_to_administrator'] ) && $this->settings['send_to_administrator'] ); ?> />
                    <label for="send-to-administrator" class="rmpro-switch-label"></label>
                    <label for="send-to-administrator"><?php esc_html_e( 'Send notifications to administrator', 'review-master' ); ?></label>
                </div>
                <table class="administrator-notifications-options" style="<?php echo esc_attr( isset( $this->settings['send_to_administrator'] ) ? '' : 'display: none;' ); ?>">
                    <!-- Send Emails From -->
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Send Emails From', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab );?>[send_emails_from]" value="<?php echo esc_attr( isset( $this->settings['send_emails_from'] ) ? $this->settings['send_emails_from'] : '' ); ?>" />
                        </td>
                    </tr>

                    <!-- Notification Template -->
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Notification Template', 'review-master' ); ?></th>
                        <td>
                        <div id="notification-template-options-admin">
                            <p class="description"><?php esc_html_e( 'Click a button to insert the corresponding template tag into the editor:', 'review-master' ); ?></p>
                            <div class="template-buttons">
                                <?php if( ! empty( $placeholders ) && is_array( $placeholders ) ) :
                                    foreach( $placeholders as $placeholder ) : ?>
                                        <code><?php echo esc_html( $placeholder );?></code>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                            <?php 
                            $default_text_author = "Hello {Name},\n\nHow are you?\n\nBest regards,\nABC";
                            wp_editor( 
                                isset( $this->settings['notification_template'] ) ? $this->settings['notification_template'] : $default_text_author,
                                'notification_template', 
                                array(
                                    'textarea_name' => 'rmpro_' . esc_attr( $current_tab ) . '[notification_template]',
                                    'media_buttons' => false,
                                    'tinymce' => array(
                                        'toolbar1' => 'formatselect, bold, italic, underline, bullist, numlist, link, unlink',
                                        'toolbar2' => 'alignleft, aligncenter, alignright, alignjustify, outdent, indent, removeformat, undo, redo'
                                    )
                                )
                            ); 
                            ?>
                            <p class="description"><?php esc_html_e( 'You can use the following dynamic tags:', 'review-master' ); ?></p>
                        </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>


        <!-- Send to Author -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Send to Author', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="send-to-author" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[send_to_author]" 
                        data-show=".author-notifications-options"
                        value="1" 
                        <?php checked( isset( $this->settings['send_to_author'] ) && $this->settings['send_to_author'] ); ?> />
                    <label for="send-to-author" class="rmpro-switch-label"></label>
                    <label for="send-to-author"><?php esc_html_e( 'Send notifications to the author of the page', 'review-master' ); ?></label>
                </div>

                <div class="author-notifications-options" style="<?php echo esc_attr( isset( $this->settings['send_to_author'] ) ? '' : 'display: none;' ); ?>">
                    <table>
                        <!-- Send Emails From (same as administrator) -->
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Send Emails From', 'review-master' ); ?></th>
                            <td>
                                <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab );?>[send_emails_from_author]" value="<?php echo esc_attr( isset( $this->settings['send_emails_from_author'] ) ? $this->settings['send_emails_from_author'] : '' ); ?>" />
                            </td>
                        </tr>
                        <!-- Notification Template (same as administrator) -->
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Notification Template', 'review-master' ); ?></th>
                            <td>
                                <div id="notification-template-options-author">
                                    <p class="description"><?php esc_html_e( 'Click a button to insert the corresponding template tag into the editor:', 'review-master' ); ?></p>
                                    <div class="template-buttons">
                                        <?php if( ! empty( $placeholders ) && is_array( $placeholders ) ) :
                                            foreach( $placeholders as $placeholder ) : ?>
                                                <code><?php echo esc_html( $placeholder );?></code>
                                            <?php endforeach; 
                                        endif; ?>
                                    </div>
                                    <?php 
                                    wp_editor( 
                                        isset( $this->settings['notification_template_author'] ) ? $this->settings['notification_template_author'] : '', 
                                        'notification_template_author', 
                                        array(
                                            'textarea_name' => 'rmpro_' . esc_attr( $current_tab ) . '[notification_template_author]',
                                            'media_buttons' => false,
                                            'tinymce' => array(
                                                'toolbar1' => 'formatselect, bold, italic, underline, bullist, numlist, link, unlink',
                                                'toolbar2' => 'alignleft, aligncenter, alignright, alignjustify, outdent, indent, removeformat, undo, redo'
                                            )
                                        )
                                    ); 
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>

        <!-- Send to One or More Email Addresses -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Send to One or More Email Addresses', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="send-to-email-addresses" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[send_to_email_addresses]" 
                        data-show=".email-addresses-notifications-options"
                        value="1" 
                        <?php checked( isset( $this->settings['send_to_email_addresses'] ) && $this->settings['send_to_email_addresses'] ); ?> />
                        <label for="send-to-email-addresses" class="rmpro-switch-label"></label>
                       <label for="send-to-email-addresses"><?php esc_html_e( 'Send notifications to one or more email addresses', 'review-master' ); ?></label>
                </div>
                <table class="email-addresses-notifications-options" style="<?php echo esc_attr( isset( $this->settings['send_to_email_addresses'] ) ? '' : 'display: none;' ); ?>">
                    <!-- Send Emails From (same as administrator) -->
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Send Emails From', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab );?>[send_emails_from_addresses]" value="<?php echo esc_attr( isset( $this->settings['send_emails_from_addresses'] ) ? $this->settings['send_emails_from_addresses'] : '' ); ?>" />
                        </td>
                    </tr>

                    <!-- Notification Template (same as administrator) -->
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Notification Template', 'review-master' ); ?></th>
                        <td>
                            <div id="notification-template-options-addresses">
                                <p class="description"><?php esc_html_e( 'Click a button to insert the corresponding template tag into the editor:', 'review-master' ); ?></p>
                                <div class="template-buttons">
                                    <?php if( ! empty( $placeholders ) && is_array( $placeholders ) ) :
                                        foreach( $placeholders as $placeholder ) : ?>
                                            <code><?php echo esc_html( $placeholder );?></code>
                                        <?php endforeach; 
                                    endif; ?>
                                </div>
                                <?php 
                                wp_editor( 
                                    isset( $this->settings['notification_template_addresses'] ) ? $this->settings['notification_template_addresses'] : '',
                                    'notification_template_addresses', 
                                    array(
                                        'textarea_name' => 'rmpro_' . esc_attr( $current_tab ) . '[notification_template_addresses]',
                                        'media_buttons' => false,
                                        'tinymce' => array(
                                            'toolbar1' => 'formatselect, bold, italic, underline, bullist, numlist, link, unlink',
                                            'toolbar2' => 'alignleft, aligncenter, alignright, alignjustify, outdent, indent, removeformat, undo, redo'
                                        )
                                    )
                                ); 
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Email Addresses', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab );?>[email_addresses]" value="<?php echo esc_attr( isset( $this->settings['email_addresses'] ) ? $this->settings['email_addresses'] : '' ); ?>" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Send to Discord Channel -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Send to Discord Channel', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="send-to-discord" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[send_to_discord]" 
                        data-show=".discord-notifications-options"
                        value="1" 
                        <?php checked( isset( $this->settings['send_to_discord'] ) && $this->settings['send_to_discord'] ); ?> />
                    <label for="send-to-discord" class="rmpro-switch-label"></label>
                    <label for="send-to-discord"><?php esc_html_e( 'Send notifications to a Discord channel', 'review-master' ); ?></label>
                </div>

                <div class="discord-notifications-options" style="<?php echo esc_attr( isset( $this->settings['send_to_discord'] ) ? '' : 'display: none;' ); ?>">
                    <table>
                        <tr>
                            <th><?php esc_html_e( 'Discord Webhook URL', 'review-master' ); ?></th>
                            <td>
                                <input 
                                    class="regular-text"
                                    type="password"
                                    name="rmpro_<?php echo esc_attr( $current_tab );?>[discord_webhook_url]" 
                                    value="<?php echo esc_attr( isset( $this->settings['discord_webhook_url'] ) ? $this->settings['discord_webhook_url'] : '' ); ?>" 
                                />
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <!-- Send to Slack -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Send to Slack', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="send-to-slack" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[send_to_slack]" 
                        data-show=".slack-notifications-options"
                        value="1" 
                        <?php checked( isset( $this->settings['send_to_slack'] ) && $this->settings['send_to_slack'] ); ?> />
                    <label for="send-to-slack" class="rmpro-switch-label"></label>
                    <label for="send-to-slack"><?php esc_html_e( 'Send notifications to a Slack channel', 'review-master' ); ?></label>
                </div>

                <div class="slack-notifications-options" style="<?php echo esc_attr( isset( $this->settings['send_to_slack'] ) ? '' : 'display: none;' ); ?>">
                    <table>
                        <tr>
                            <th><?php esc_html_e( 'Slack Webhook URL', 'review-master' ); ?></th>
                            <td>
                                <input class="regular-text" type="password" name="rmpro_<?php echo esc_attr( $current_tab );?>[slack_webhook_url]" value="<?php echo esc_attr( isset( $this->settings['slack_webhook_url'] ) ? $this->settings['slack_webhook_url'] : '' ); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

    </table>
</div>
