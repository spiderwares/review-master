<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>
<div id="form-settings" class="rmpro-tab-content">

    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e( 'Form Heading', 'review-master' ); ?></th>
            <td>
                <input type="text" class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form_heading]"
                    value="<?php echo esc_attr( isset( $this->settings['form_heading'] ) ? $this->settings['form_heading'] : '' ); ?>">
            </td>
        </tr>
       
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Form Fields', 'review-master' ); ?></th>
            <td>
                <table class="form-settings-table rmpro-form-fields-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Field Name', 'review-master' ); ?></th>
                            <th><?php esc_html_e( 'Enable/Disable', 'review-master' ); ?></th>
                            <th><?php esc_html_e( 'Required', 'review-master' ); ?></th> <!-- New Column -->
                            <th><?php esc_html_e( 'Display for Guest Users Only', 'review-master' ); ?></th> <!-- New Column -->
                            <th><?php esc_html_e( 'Placeholder', 'review-master' ); ?></th>
                            <th><?php esc_html_e( 'Field Label', 'review-master' ); ?></th> <!-- New Column -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $default_fields as $field_name => $field ) : ?>
                            <tr>
                                <td><?php echo esc_html( $field['field'] ); ?></td>
                                <td>
                                    <div class="rmpro-switch">
                                        <input type="checkbox" 
                                            class="rmpro-toggle-field" 
                                            id="<?php echo esc_attr('switch-field-' . $field_name); ?>" 
                                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form][<?php echo esc_attr($field_name); ?>][enable]" 
                                            <?php checked( isset( $this->settings['form'][esc_attr($field_name)]['enable'] ) && $this->settings['form'][esc_attr($field_name)]['enable'] ); ?> 
                                            data-show=".field-options-<?php echo esc_attr($field_name); ?>"
                                        />
                                        <label for="<?php echo esc_attr('switch-field-' . $field_name); ?>" class="rmpro-switch-label"></label>
                                    </div>
                                </td>

                                <td>
                                    <div class="rmpro-switch"> <!-- Required Column -->
                                        <input type="checkbox" 
                                            class="rmpro-toggle-required" 
                                            id="<?php echo esc_attr('switch-required-' . $field_name); ?>" 
                                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form][<?php echo esc_attr($field_name); ?>][required]" 
                                            <?php checked( isset( $this->settings['form'][esc_attr($field_name)]['required'] ) && $this->settings['form'][esc_attr($field_name)]['required'] ); ?>
                                        />
                                        <label for="<?php echo esc_attr('switch-required-' . $field_name); ?>" class="rmpro-switch-label"></label>
                                    </div>
                                </td>

                                <td>
                                    <div class="rmpro-switch"> <!-- Required Column -->
                                        <input type="checkbox" 
                                            class="rmpro-toggle-guest" 
                                            id="<?php echo esc_attr('switch-guest-' . $field_name); ?>" 
                                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form][<?php echo esc_attr($field_name); ?>][guest]" 
                                            <?php checked( isset( $this->settings['form'][esc_attr($field_name)]['guest'] ) && $this->settings['form'][esc_attr($field_name)]['guest'] ); ?>
                                        />
                                        <label for="<?php echo esc_attr('switch-guest-' . $field_name); ?>" class="rmpro-switch-label"></label>
                                    </div>
                                </td>

                                <?php if ( $field_name !== 'cat_rating' ) : ?>
                                    <td>
                                        <input type="text" 
                                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form][<?php echo esc_attr($field_name); ?>][placeholder]" 
                                            value="<?php echo esc_attr( isset( $this->settings['form'][esc_attr($field_name)]['placeholder'] ) ? $this->settings['form'][esc_attr($field_name)]['placeholder'] : '' ); ?>">
                                    </td>
                                <?php else : ?>
                                    <td></td> <!-- Empty cell for Required -->
                                <?php endif; ?>

                                <td>
                                    <input type="text" 
                                        name="rmpro_<?php echo esc_attr( $current_tab ); ?>[form][<?php echo esc_attr($field_name); ?>][field_label]" 
                                        value="<?php echo esc_attr( isset( $this->settings['form'][esc_attr($field_name)]['field_label'] ) ? $this->settings['form'][esc_attr($field_name)]['field_label'] : '' ); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Auto Insert Name & Email', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch"> <!-- Required Column -->
                    <input type="checkbox" 
                        class="rmpro-toggle-autonamemail" 
                        id="<?php echo esc_attr('switch-autonamemail'); ?>" 
                        name="rmpro_<?php echo esc_attr( $current_tab ); ?>[autonamemail]" 
                        <?php checked( isset( $this->settings['autonamemail'] ) && $this->settings['autonamemail'] ); ?>
                    />
                    <label for="<?php echo esc_attr('switch-autonamemail'); ?>" class="rmpro-switch-label"></label>
                </div>
                <strong><?php esc_html_e( 'Check this checkbox to automatically retrieve the username and email address of the logged-in user, and hide the name and email fields from the review submission form.', 'review-master' ); ?></strong>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Submit Button Label', 'review-master' ); ?></th>
            <td>
                <input type="text" class="regular-text"
                    name="rmpro_<?php echo esc_attr( $current_tab ); ?>[button_label]"
                    value="<?php echo esc_attr( isset( $this->settings['button_label'] ) ? $this->settings['button_label'] : '' ); ?>">
            </td>
        </tr>
        <!-- CAPTCHA Select Control -->
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'CAPTCHA', 'review-master' ); ?></th>
            <td>
                <select class="regular-text" data-hide=".captcha-options" id="captcha-type" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[captcha_type]">
                    <option value="none" data-show="" <?php selected( isset( $this->settings['captcha_type'] ) ? $this->settings['captcha_type'] : '', 'none' ); ?> ><?php esc_html_e( 'Do not use', 'review-master' ); ?></option>
                    <option data-show=".recaptcha-options" value="recaptcha-v2"  <?php selected( isset( $this->settings['captcha_type'] ) ? $this->settings['captcha_type'] : '', 'recaptcha-v2' ); ?>><?php esc_html_e( 'reCAPTCHA v2', 'review-master' ); ?></option>
                    <option data-show=".hcaptcha-options" value="h_captcha" <?php selected( $this->settings['captcha_type'] ?? '', 'h_captcha' ); ?>><?php esc_html_e( 'h-Captcha', 'review-master' ); ?></option>
                </select>

                <table class="captcha-options recaptcha-options" style="<?php echo esc_attr( selected( $this->settings['captcha_type'], 'recaptcha-v2', false ) ? '' : 'display: none;' ); ?>">
                    <tr valign="top" class="site_key recaptcha-field">
                        <th scope="row"><?php esc_html_e( 'Site Key', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[site_key]" value="<?php echo esc_attr( $this->settings['site_key'] ? $this->settings['site_key'] : '' ); ?>" />
                        </td>
                    </tr>
                    <tr valign="top" class="secret_key recaptcha-field">
                        <th scope="row"><?php esc_html_e( 'Secret Key', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[secret_key]" value="<?php echo esc_attr( isset( $this->settings['secret_key'] ) ? $this->settings['secret_key'] : '' ); ?>" />
                        </td>
                    </tr>
                    <tr valign="top" class="captcha_theme recaptcha-field">
                        <th scope="row"><?php esc_html_e( 'CAPTCHA Theme', 'review-master' ); ?></th>
                        <td>
                            <select class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[captcha_theme]">
                                <option value="light" <?php selected( isset( $this->settings['captcha_theme'] ) ? $this->settings['captcha_theme'] : '', 'light' ); ?>><?php esc_html_e( 'Light', 'review-master' ); ?></option>
                                <option value="dark" <?php selected( isset( $this->settings['captcha_theme'] ) ? $this->settings['captcha_theme'] : '', 'dark' ); ?>><?php esc_html_e( 'Dark', 'review-master' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top" class="captcha_usage recaptcha-field">
                        <th scope="row"><?php esc_html_e( 'CAPTCHA Usage', 'review-master' ); ?></th>
                        <td>
                            <select class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[captcha_usage]">
                                <option value="everyone" <?php selected( isset( $this->settings['captcha_usage'] ) ? $this->settings['captcha_usage'] : '', 'everyone' ); ?>><?php esc_html_e( 'Use for everyone', 'review-master' ); ?></option>
                                <option value="guests" <?php selected( isset( $this->settings['captcha_usage'] ) ? $this->settings['captcha_usage'] : '', 'guests' ); ?>><?php esc_html_e( 'Use only for guest users', 'review-master' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <!-- hCaptcha Options -->
                <table class="captcha-options hcaptcha-options" style="<?php echo esc_attr( selected( $this->settings['captcha_type'], 'h_captcha', false ) ? '' : 'display: none;' ); ?>">
                    <tr valign="top" class="site_key hcaptcha-field">
                        <th scope="row"><?php esc_html_e( 'Site Key', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[hcaptcha_site_key]" value="<?php echo esc_attr( $this->settings['hcaptcha_site_key'] ?? '' ); ?>" />
                        </td>
                    </tr>
                    <tr valign="top" class="secret_key hcaptcha-field">
                        <th scope="row"><?php esc_html_e( 'Secret Key', 'review-master' ); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[hcaptcha_secret_key]" value="<?php echo esc_attr( $this->settings['hcaptcha_secret_key'] ?? '' ); ?>" />
                        </td>
                    </tr>
                    <tr valign="top" class="captcha_theme hcaptcha-field">
                        <th scope="row"><?php esc_html_e( 'CAPTCHA Theme', 'review-master' ); ?></th>
                        <td>
                            <select class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[captcha_theme]">
                                <option value="light" <?php selected( isset( $this->settings['captcha_theme'] ) ? $this->settings['captcha_theme'] : '', 'light' ); ?>><?php esc_html_e( 'Light', 'review-master' ); ?></option>
                                <option value="dark" <?php selected( isset( $this->settings['captcha_theme'] ) ? $this->settings['captcha_theme'] : '', 'dark' ); ?>><?php esc_html_e( 'Dark', 'review-master' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top" class="captcha_usage hcaptcha-field">
                        <th scope="row"><?php esc_html_e( 'CAPTCHA Usage', 'review-master' ); ?></th>
                        <td>
                            <select class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[captcha_usage]">
                                <option value="everyone" <?php selected( isset( $this->settings['captcha_usage'] ) ? $this->settings['captcha_usage'] : '', 'everyone' ); ?>><?php esc_html_e( 'Use for everyone', 'review-master' ); ?></option>
                                <option value="guests" <?php selected( isset( $this->settings['captcha_usage'] ) ? $this->settings['captcha_usage'] : '', 'guests' ); ?>><?php esc_html_e( 'Use only for guest users', 'review-master' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Limit Reviews', 'review-master' ); ?></th>
            <td>
                <select id="limit_review" class="regular-text" data-hide=".limit-options" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[limit_review]">
                    <?php foreach ( $limit_review_option as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( isset( $this->settings['limit_review'] ) ? $this->settings['limit_review'] : '', $value ); ?>  data-show=".<?php echo esc_attr( $value ); ?>" >
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <table class="limit-options by-email-address by-ip-address by-username" style="<?php echo esc_attr( selected( $this->settings['limit_review'], 'no-limit', false ) ? 'display: none;' : '' ); ?>">
                    <tr valign="top" id="limit_time">
                        <th scope="row"><?php esc_html_e( 'Limit Reviews For', 'review-master' ); ?></th>
                        <td>
                        <input type="number" class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[limit_time]"
                            value="<?php echo esc_attr( isset( $this->settings['limit_time'] ) ? $this->settings['limit_time'] : 7 ); ?>">
                            <p class="description"><?php esc_html_e( 'Set Limit Reviews in days', 'review-master' ); ?></p>
                        </td>
                    </tr>
                </table>

                <table class="limit-options by-email-address" style="<?php echo esc_attr( selected( $this->settings['limit_review'], 'by-email-address', false ) ? '' : 'display: none;' ); ?>">
                    <tr valign="top" id="email_whitelist">
                        <th scope="row"><?php esc_html_e( 'Email Whitelist', 'review-master' ); ?></th>
                        <td>
                            <textarea class="regular-text"  rows="5"  name="rmpro_<?php echo esc_attr( $current_tab ); ?>[email_whitelist]"><?php echo esc_textarea( isset( $this->settings['email_whitelist'] ) ? $this->settings['email_whitelist'] : '' ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Enter IPs to blacklist, separated by commas. ', 'review-master' ); ?></p>
                        </td>
                    </tr>
                </table>

                <table class="limit-options by-ip-address" style="<?php echo esc_attr( selected( $this->settings['limit_review'], 'by-ip-address', false ) ? '' : 'display: none;' ); ?>">
                    <tr valign="top" id="ip_whitelist">
                        <th scope="row"><?php esc_html_e( 'IP Address Whitelist', 'review-master' ); ?></th>
                        <td>
                            <textarea class="regular-text"  rows="5"  name="rmpro_<?php echo esc_attr( $current_tab ); ?>[ip_whitelist]"><?php echo esc_textarea( isset( $this->settings['ip_whitelist'] ) ? $this->settings['ip_whitelist'] : '' ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Enter IPs to blacklist, separated by commas. ', 'review-master' ); ?></p>
                        </td>
                    </tr>
                </table>

                <table class="limit-options by-username" style="<?php echo esc_attr( selected( $this->settings['limit_review'], 'by-username', false ) ? '' : 'display: none;' ); ?>">
                    <tr valign="top" id="username_whitelist">
                        <th scope="row"><?php esc_html_e( 'Username Whitelist', 'review-master' ); ?></th>
                        <td>
                            <textarea class="regular-text"  rows="5"  name="rmpro_<?php echo esc_attr( $current_tab ); ?>[username_whitelist]"><?php echo esc_textarea( isset( $this->settings['username_whitelist'] ) ? $this->settings['username_whitelist'] : '' ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Enter IPs to blacklist, separated by commas. ', 'review-master' ); ?></p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Enable Akismet', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="enable-akismet" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[enable-akismet]" 
                        data-show=".akismet-option"
                        value="1" 
                        <?php checked( isset( $this->settings['enable-akismet'] ) && $this->settings['enable-akismet'] ); ?> />
                    <label for="enable-akismet" class="rmpro-switch-label"></label>
                </div>

                <div class="akismet-option" style="<?php echo esc_attr( isset( $this->settings['enable-akismet'] ) ? '' : 'display: none;' ); ?>">
                    <table>
                        <tr>
                            <th><?php esc_html_e( 'Akismet API Key', 'review-master' ); ?></th>
                            <td>
                                <input 
                                    class="regular-text"
                                    type="password"
                                    name="rmpro_<?php echo esc_attr( $current_tab );?>[akismet_api_key]" 
                                    value="<?php echo esc_attr( isset( $this->settings['akismet_api_key'] ) ? $this->settings['akismet_api_key'] : '' ); ?>" 
                                />
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Prevent Duplicates', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="prevent-duplicates" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[prevent-duplicates]" 
                        value="1" 
                        <?php checked( isset( $this->settings['prevent-duplicates'] ) && $this->settings['prevent-duplicates'] ); ?> />
                    <label for="prevent-duplicates" class="rmpro-switch-label"></label>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Blacklist', 'review-master' ); ?></th>
            <td>
                <select id="blacklist" class="regular-text" data-hide=".blacklist-options" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[blacklist]">
                    <?php foreach ( $blacklist_option as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $this->settings['blacklist'], $value ); ?> data-show=".<?php echo esc_attr( $value ); ?>">
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>    
                </select>
            </td>
        </tr>
        <tr valign="top" id="blacklist_entries" class="blacklist-options review-master-blacklist" style="<?php echo esc_attr( selected( $this->settings['blacklist'], 'review-master-blacklist', false ) ? '' : 'display: none;' ); ?>">
            <th scope="row"><?php esc_html_e( 'Blacklist Entries', 'review-master' ); ?></th>
            <td>
                <textarea class="regular-text"  rows="5"  name="rmpro_<?php echo esc_attr( $current_tab ); ?>[blacklist_entries]"><?php echo esc_textarea( isset( $this->settings['blacklist_entries'] ) ? $this->settings['blacklist_entries'] : '' ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Reviews with any of these entries in the title, content, name, email, or IP address will be rejected. This is case-insensitive and matches partial words (e.g., "press" matches "WordPress").', 'review-master' ); ?></p>
            </td>
        </tr>
        <tr valign="top" class="blacklist-options review-master-blacklist use-the-wordpress-disallowed" style="<?php echo esc_attr( selected( $this->settings['blacklist'], 'no-blacklist', false ) ? 'display: none;' : '' ); ?>">
            <th scope="row"><?php esc_html_e( 'Blacklist Action', 'review-master' ); ?></th>
            <td>
                <select id="blacklist_action" class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[blacklist_action]">
                    <?php foreach ( $blacklist_action_option as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php echo isset( $this->settings['blacklist_action'] ) && $this->settings['blacklist_action'] === $value ? 'selected' : ''; ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>    
                </select>
            </td>
        </tr>
    </table>
</div>
