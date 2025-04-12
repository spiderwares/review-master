<?php
/**
 * Review Form Template
 *
 * This template can be overridden by copying it to yourtheme/review-master-pro/review-form.php
 *
 * @package ReviewMaster
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly.
endif;

do_action( 'rmpro_before_review_form' );

// Require login check
if ( isset( $rmpro_general['require_login'] ) && ! is_user_logged_in() ) :
    do_action( 'rmpro_before_login_notice' ); ?>
    <div><?php esc_html_e( 'Log in required to write a review.', 'review-master' ); ?></div>
    <div class="rmpro-form-group">
        <?php if ( ! empty( $rmpro_general['custom_login_url'] ) ) :
            printf('<a class="button wp-element-button" href="%s">%s</a>', esc_url( $rmpro_general['custom_login_url'] ), esc_html__( 'Login', 'review-master' ) );
        endif;

        if ( ! empty( $rmpro_general['custom_registration_url'] ) ) :
            printf('<a class="button wp-element-button" href="%s">%s</a>', esc_url( $rmpro_general['custom_registration_url'] ), esc_html__( 'Register', 'review-master' ) );
        endif; ?>
    </div>
    <?php do_action( 'rmpro_after_login_notice' );
else :
    ?>
    <form id="rmpro-review-form" method="post">
        <?php do_action( 'rmpro_before_review_fields' ); ?>
        <span class="rmpro-form-title heading wp-block-heading"><?php echo esc_html( $form_heading ); ?></span>

        <?php
        // Rating Fields
        if ( 
            isset( $fields['cat_rating']['enable'] ) && 
            ( is_user_logged_in() && ! isset( $fields['cat_rating']['guest'] ) ) || ( ! is_user_logged_in() && isset( $fields['cat_rating']['guest'] ) ) 
        ) :

            if( ! empty(  $categories ) ) :

                foreach ( $categories as $key => $category ) :
                    do_action( 'rmpro_before_rating_field', $category ); ?>
                    
                    <div class="rmpro-rating-field">
                        <label class="rmpro-category-label">
                            <?php echo esc_html( $category ); ?>
                        </label>
                        
                        <div class="rmpro-star-rating">
                            
                            <?php for ( $i = 5; $i >= 1; $i-- ) : ?>
                                <input 
                                    type="radio" id="<?php echo esc_attr( "{$category}-star-$i" ); ?>"
                                    name="<?php echo esc_attr( "ratings[{$category}]" ); ?>"
                                    value="<?php echo esc_attr( $i ); ?>">

                                <label for="<?php echo esc_attr("{$category}-star-$i"); ?>" title="<?php echo esc_attr( $i ); ?> stars">
                                    <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='transparent' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <?php do_action( 'rmpro_after_rating_field', $category );
                    
                endforeach;
            else : 
                do_action( 'rmpro_before_default_rating_field' ); ?>
                    
                <div class="rmpro-rating-field">
                    <label class="rmpro-category-label">
                        <?php echo esc_html( ! empty( $fields['cat_rating']['field_label'] ) ? $fields['cat_rating']['field_label'] : esc_html__( 'Rating', 'review-master' )  ); ?>
                    </label>
                    
                    <div class="rmpro-star-rating">
                        <?php for ( $i = 5; $i >= 1; $i-- ) : ?>
                            <input 
                                type="radio" 
                                id="<?php echo esc_attr("star-$i"); ?>"
                                name="avg_rating"
                                value="<?php echo esc_attr( $i ); ?>">

                            <label for="<?php echo esc_attr("star-$i"); ?>" title="<?php echo esc_attr( $i ); ?> stars">
                                <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='transparent' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <?php do_action( 'rmpro_after_default_rating_field' );
            endif; ?>
            <div class="rmpro-error" id="rmpro-error-cat_rating"></div>

        <?php endif;

        // Dynamic Fields
        $field_types = apply_filters( 'rmpro_review_form_fields', [ 'title', 'name', 'email', 'your_review' ] );

        foreach ( $field_types as $field ) :
            if ( isset( $fields[ $field ] ) && 
                ( RMPRO_Helper::display_field( $fields[ $field ] ) && ! isset( $fields[ $field ]['guest'] ) ) ||
                ( isset( $fields[ $field ]['guest'] ) && $fields[ $field ]['guest'] === 'on' && ! $is_logged_in ) ) :
                
                do_action( "rmpro_before_{$field}_field" ); ?>

                <div class="rmpro-form-group">
                    <label><?php echo esc_html( $fields[ $field ]['field_label'] ); ?></label>
                    
                    <?php if ( $field === 'your_review' ) : ?>
                        <textarea name="<?php echo esc_attr( $field ); ?>" 
                            placeholder="<?php echo esc_attr( isset( $fields[ $field ]['placeholder'] ) ? $fields[ $field ]['placeholder'] : '' ); ?>"></textarea>

                    <?php else : ?>
                        <input type="<?php echo $field === 'email' ? 'email' : 'text'; ?>" 
                            name="<?php echo esc_attr( $field ); ?>"
                            placeholder="<?php echo esc_attr( isset( $fields[ $field ]['placeholder'] ) ? $fields[ $field ]['placeholder'] : '' ); ?>"/>

                    <?php endif; ?>
                    <div class="rmpro-error" id="rmpro-error-<?php echo esc_attr($field); ?>"></div>
                </div>

                <?php do_action( "rmpro_after_{$field}_field" );
            endif;
        endforeach;


        // reCAPTCHA Integration
        if ( isset( $form_settings['captcha_type'], $form_settings['captcha_usage'] ) 
            && $form_settings['captcha_type'] === 'recaptcha-v2' 
            && ( $form_settings['captcha_usage'] === 'everyone' || ( $form_settings['captcha_usage'] === 'guests' && ! $is_logged_in ) ) ) :
            do_action( 'rmpro_before_captcha' ); ?>
            <div class="rmpro-form-group">
                <div class="g-recaptcha"
                    data-theme="<?php echo esc_attr( $form_settings['captcha_theme'] ); ?>" 
                    data-sitekey="<?php echo esc_attr( $form_settings['site_key'] ); ?>" >
                </div>
            </div>
            <?php
            do_action( 'rmpro_after_captcha' );

        endif; 
        
        if ( isset( $form_settings['captcha_type'], $form_settings['captcha_usage'] ) 
            && $form_settings['captcha_type'] === 'h_captcha' 
            && ( $form_settings['captcha_usage'] === 'everyone' || ( $form_settings['captcha_usage'] === 'guests' && ! $is_logged_in ) ) ) : 
            do_action( 'rmpro_before_captcha' ); ?>
            <div class="rmpro-form-group">
                <div class="h-captcha" 
                    data-theme="<?php echo esc_attr( $form_settings['captcha_theme'] ); ?>"
                    data-sitekey="<?php echo esc_attr( $form_settings['hcaptcha_site_key'] ); ?>">
                </div>
            </div>
            <?php do_action( 'rmpro_after_captcha' ); 
        endif; ?>

        <?php do_action( 'rmpro_before_submit_button' ); ?>
        <div class="rmpro-form-group">
            <?php wp_nonce_field( 'rmpro_security_nonce', 'nonce' ); ?>
            <input type="hidden" name="module_type" value="<?php echo esc_attr( $module_type ); ?>">
            <input type="hidden" name="module" value="<?php echo esc_attr( $module ); ?>">
            <input type="hidden" name="associate_id" value="<?php echo esc_attr( $associate_id ); ?>">
            <button class="button wp-element-button" type="submit">
                <?php echo esc_html( $button_label ?: __( 'Submit Review', 'review-master' ) ); ?>
            </button>
            <img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" class="rmpro-spinner spinner" alt="Loading..." style="display: none;">
        </div>
        <div id="rmpro-review-message"></div>
        <?php do_action( 'rmpro_after_submit_button' ); ?>
    </form>
<?php
endif;

do_action( 'rmpro_after_review_form' );
