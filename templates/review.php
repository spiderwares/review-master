<?php
/**
 * RMPRO Review Template
 *
 * This template is used to display individual reviews.
 *
 * This template can be overridden by copying it to yourtheme/review-master-pro/review.php.
 *
 * However, on occasion, RMPRO may need to update template files, and you (the theme developer) will
 * need to copy the new files to your theme to maintain compatibility. We try to minimize such updates.
 *
 * @package ReviewMaster
 */

if ( ! defined( 'ABSPATH' ) ) :
    exit; // Prevent direct access.
endif; ?>

<div class="review">
    <div class="review_heading_wrap">
        <div class="heading_col title-wraper">
            <?php if( $enable_avatar )  : ?>
                <img src="<?php echo esc_url( get_avatar_url( $review['email'], [ 'size' => $avatar_size ] ) ); ?>" alt="<?php echo esc_attr( $review['name'] ); ?> Avatar"> 
            <?php endif; ?>
            <div class="review-content">
                <div class="rmpro-reviewrname">
                    <?php echo esc_html( $review['name'] ); ?>
                </div>
                <div class="stars">
                    <div class="rmpro-rating-container">
                        <div class="rmpro-rating-stars" 
                             style="width: <?php echo esc_attr( $review['rating'] * 20 ); ?>%;"></div>
                        
                        <?php if ( ! empty( $categories ) ) : ?>
                            <div class="rmpro-tooltip">
                                <table>
                                    <?php foreach ( $categories as $category ) : ?>
                                        <tr>
                                            <td><?php echo esc_html( $category['label'] ); ?></td>
                                            <td>
                                                <div class="rmpro-rating-container">
                                                    <div class="rmpro-rating-stars" 
                                                         style="width: <?php echo esc_attr( $category['rating'] * 20 ); ?>%;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="heading_col date-wraper">
            <div class="rmpro-comment__date">
                <?php echo esc_html( RMPRO_Shortcode::get_date_formated( $review['created_at'] ) ); ?>
            </div>
        </div>
    </div>
    <div class="rmpro-desc">
        <?php if( isset( $review['title'] ) ) : ?>
            <div class="review-title"><?php echo esc_html( $review['title'] ) ?></div>
        <?php endif; ?>

        <span class="review-content">
            <?php echo esc_html( $short_review ); ?>
            <?php if ( $show_more ) : ?>
                <a href="#" class="toggle-review"><small><?php esc_html_e( 'Show More', 'review-master' ); ?></small></a>
            <?php endif; ?>
        </span>

        <?php if ( $show_more ) : ?>
            <span class="full-review" style="display: none;">
                <?php echo esc_html( $review_content ); ?>            
                <a href="#" class="toggle-review"><small><?php esc_html_e( 'Show Less', 'review-master' ); ?></small></a>
            </span>
        <?php endif; ?>

        <?php if( $response ) : ?>
            <div class="review-response">

                <?php if( $response_by ) : ?>
                    <div class="heading_col title-wraper">
                        <?php if( $enable_avatar  ) : ?>
                            <img src="<?php echo esc_url( get_avatar_url( $response_by->ID, [ 'size' => $avatar_size ] ) ); ?>" alt="<?php echo esc_attr( $review['name'] ); ?> Avatar">
                        <?php endif; ?>
                        <div class="review-username">
                            <strong><?php echo esc_html( $response_by->display_name ); ?></strong>
                        </div>
                        <div class="response-time">
                            <?php echo esc_html( RMPRO_Shortcode::get_date_formated( $response_time ) ); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="review-response-desc">
                    <?php echo esc_html( $response ); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>