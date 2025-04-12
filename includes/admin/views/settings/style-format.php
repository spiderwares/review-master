<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>
<div id="form-settings" class="rmpro-tab-content">
    <table class="form-table">

        <tr valign="top">
            <th scope="row" colspan="2"><div class="rmpro-setting-title"><?php esc_html_e( 'Review Settings', 'review-master' ); ?></div></th>
        </tr>
    
        <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Date Format', 'review-master' ); ?></th>
            <td>
                <select class="regular-text" data-hide=".date-formate-option" name="rmpro_<?php echo esc_attr( $current_tab );?>[date_format]" id="date_format">
                    <option data-show=".default" value="default" <?php selected( isset( $this->settings['date_format'] ) && $this->settings['date_format'] === 'default' ); ?>>
                        <?php esc_html_e( 'Use the default date format', 'review-master' ); ?>
                    </option>
                    <option data-show=".custom" value="custom" <?php selected( isset( $this->settings['date_format'] ) && $this->settings['date_format'] === 'custom' ); ?>>
                        <?php esc_html_e( 'Use a custom date format', 'review-master' ); ?>
                    </option>
                </select>
                <p class="description"><?php esc_html_e( 'Choose the date format for the reviews.', 'review-master' ); ?></p>
            </td>
        </tr>

        <tr valign="top" class="date-formate-option custom" style="<?php echo esc_attr( selected( $this->settings['date_format'], 'custom', false ) ? '' : 'display: none;' ); ?>">
            <th scope="row"><?php esc_html_e( 'Custom Date Format', 'review-master' ); ?></th>
            <td>
                <input type="text" class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab );?>[custom_date_format]" 
                       value="<?php echo isset( $this->settings['custom_date_format'] ) ? esc_attr( $this->settings['custom_date_format'] ) : 'F j, Y'; ?>" />
                <p class="description"><?php esc_html_e( 'Enter a custom date format, e.g., "F j, Y". For more options.', 'review-master' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Enable Avatar', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="enable-avatar" data-show=".avatar-options" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[enable_avatar]" 
                        value="1"
                        <?php checked( isset( $this->settings['enable_avatar'] ) && $this->settings['enable_avatar'] ); ?> />
                    <label for="enable-avatar" class="rmpro-switch-label"></label>
                    <label for="enable-avatar"><?php esc_html_e( 'Enable avatar', 'review-master' ); ?></label>
                </div>
            </td>
        </tr>

        <tr valign="top" class="avatar-options" style="<?php echo esc_attr( isset( $this->settings['enable_avatar'] ) ? '' : 'display: none;' ); ?>">
            <th scope="row"><?php esc_html_e( 'Avatar Size', 'review-master' ); ?></th>
            <td>
                <input type="number" class="regular-text" name="rmpro_<?php echo esc_attr( $current_tab );?>[avatar_size]" 
                    value="<?php echo isset( $this->settings['avatar_size'] ) ? esc_attr( $this->settings['avatar_size'] ) : 40; ?>" 
                    min="10" step="1" />
                <p class="description"><?php esc_html_e( 'Set the size of the avatar in pixels.', 'review-master' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Enable Excerpts', 'review-master' ); ?></th>
            <td>
                <div class="rmpro-switch">
                    <input type="checkbox" id="enable-excerpts" 
                        name="rmpro_<?php echo esc_attr( $current_tab );?>[enable_excerpts]" 
                        data-show=".excerpt-options"
                        value="1"
                        <?php checked( isset( $this->settings['enable_excerpts'] ) && $this->settings['enable_excerpts'] ); ?> />
                    <label for="enable-excerpts" class="rmpro-switch-label"></label>
                    <label for="enable-excerpts"><?php esc_html_e( 'Enable excerpts on review description.', 'review-master' ); ?></label>
                </div>

            </td>
        </tr>
        
        <tr valign="top" class="excerpt-options" style="<?php echo esc_attr( isset( $this->settings['enable_excerpts'] ) ? '' : 'display: none;' ); ?>">
            <th scope="row"><?php esc_html_e( 'Excerpt Length', 'review-master' ); ?></th>
            <td>
                <input class="regular-text" type="number" name="rmpro_<?php echo esc_attr( $current_tab );?>[excerpt_length]" value="<?php echo esc_attr( isset( $this->settings['excerpt_length'] ) ? $this->settings['excerpt_length'] : 55 ); ?>" min="1" step="1" />
                <p class="description"><?php esc_html_e( 'Set the number of words to display in the excerpt.', 'review-master' ); ?></p>
            </td>
        </tr>

    </table>
</div>