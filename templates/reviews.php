<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php if ( isset( $review_summary['total_reviews'] ) && $review_summary['total_reviews'] > 0 ) : ?>

    <div class="rmpro-review">
        <?php if( $enable_summary == 'on' ): ?>
            <div class="row rmpro-reviewbar">
                <!-- Rating Overview -->
                <div class="rmpro-ratings-dashboard rmpro-reviewbar-col">
                    <div class="rmpro-ratingboard-inner">
                        <div class="rmpro-ratings__average">
                            <span><?php echo esc_html($review_summary['average_rating']); ?></span>
                        </div>
                        <div>
                            <div class="rmpro-rating-container">
                                <div class="rmpro-rating-stars" style="width: <?php echo esc_attr($review_summary['average_rating'] / 5 * 100); ?>%;"></div>
                            </div>
                            <div class="rmpro__rating-count">
                                <?php printf( wp_kses_post('<span>%s</span> Reviews', 'review-master'), esc_html( $review_summary['total_reviews'] ) ); ?>
                            </div>

                        </div>
                    </div>
                </div>        

                <!-- Review Summary Bar -->
                <div class="rmpro-reviews__bar reviews-bar rmpro-reviewbar-col">
                    <ul class="list-reset reviews-bar__list">
                        <?php  foreach ( range(5, 1) as $rating ) : ?>
                            <li class="reviews-bar__item">
                                <div class="progress-bar">
                                    <span class="progress-bar__star">
                                        <?php echo esc_html( $rating ); ?> 
                                        <span class="rmpro-star"></span>
                                    </span>
                                    <div class="progress-bar__outter-line" data-rating="<?php echo esc_attr( $review_summary[  $rating . '_star' ] ); ?>">
                                        <span class="progress-bar__inner-line" style="width: <?php echo esc_attr( ( $review_summary[ $rating . '_star' ] / $review_summary['total_reviews'] * 100 ) . '%' ); ?>;"></span>
                                    </div>
                                    <span class="progress-bar__quantity"><?php echo esc_html( $review_summary[  $rating . '_star' ] ); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Review Categories -->
                <div class="rmpro-category-summary">
                    <?php if( isset( $category_summary ) && ! empty( $category_summary ) ) : ?>
                        <ul class="rmpro-summary-category">
                            <?php foreach( $category_summary as $category ) : ?>
                                <li class="category">
                                    <span class="rating-colorbar"></span>
                                    <span class="rating"><b><?php echo esc_html( $category['average_rating']  ); ?></b>/5</span>
                                    <span class="rating-label"><?php echo esc_html( $category['category'] ); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Review List -->
        <div class="row">
        
            <div class="rmpro-reviews-filter">
                <div class="rmpro-reviews-filter__count-heading">
                    <?php printf( wp_kses_post('<span class="rmpro_total_review_count">%s</span> Reviews', 'review-master'), esc_html( $review_summary['total_reviews'] ) ); ?>
                </div>
                <form class="rmpro-review-filter">
                    <div class="rmpro-reviews-filter__dropdowns">
                        
                        <label for="rmpro-sort-by"><b><?php esc_html_e( 'SORT BY', 'review-master' ); ?></b>
                            <select class="rmpro-select" name="orderby">
                                <option value="DESC" selected ><?php esc_html_e( 'Newest', 'review-master' ); ?></option>
                                <option value="ASC"><?php esc_html_e( 'Oldest', 'review-master' ); ?></option>
                                <option value="highest_rated"><?php esc_html_e( 'Highest Rated', 'review-master' ); ?></option>
                                <option value="lowest_rated"><?php esc_html_e( 'Lowest Rated', 'review-master' ); ?></option>
                            </select>
                        </label>

                        <label for="rmpro-filter-by"><b><?php esc_html_e( 'FILTER BY', 'review-master' ); ?></b>
                            <select class="rmpro-select" name="rating">
                                <option value=""><?php esc_html_e( 'All stars', 'review-master' ); ?></option>
                                <option value="5"><?php esc_html_e( '5 star only', 'review-master' ); ?></option>
                                <option value="4"><?php esc_html_e( '4 star only', 'review-master' ); ?></option>
                                <option value="3"><?php esc_html_e( '3 star only', 'review-master' ); ?></option>
                                <option value="2"><?php esc_html_e( '2 star only', 'review-master' ); ?></option>
                                <option value="1"><?php esc_html_e( '1 star only', 'review-master' ); ?></option>
                            </select>
                        </label>
                        
                        <div class="rmpro_search">
                            <input type="text" name="search" placeholder="<?php esc_attr_e( 'Search here...', 'review-master' ); ?>" />
                            <input type="hidden" name="module" value="<?php echo esc_attr( wp_json_encode( $module ) ); ?>" />
                            <input type="hidden" name="module_type" value="<?php echo esc_attr( $module_type ); ?>" />
                            <input type="hidden" name="enable_avatar" value="<?php echo esc_attr( $enable_avatar ); ?>" />
                            <input type="hidden" name="avatar_size" value="<?php echo esc_attr( $avatar_size ); ?>" />
                            <input type="hidden" name="enable_excerpts" value="<?php echo esc_attr( $enable_excerpts ); ?>" />
                            <input type="hidden" name="excerpt_length" value="<?php echo esc_attr( $excerpt_length ); ?>" />
                            <input type="hidden" name="associate_id" value="<?php echo esc_attr( $associate_id ); ?>">
                            <input type="hidden" name="per_page" value="<?php echo esc_attr( $per_page ); ?>" />
                            <button class="rmpro_button"><?php esc_html_e( 'Submit', 'review-master' ); ?></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="rmpro-review-container" data-view="list">
                <?php foreach ( $reviews as $review ) :                
                    $show_more      = false;
                    $categories     = rmpro_get_review_by_category( $review['id'] );
                    $response       = get_metadata( 'rmpro_reviews', $review['id'], '_response', true );
                    $response_by    = get_metadata( 'rmpro_reviews', $review['id'], '_response_by', true );
                    $response_time  = get_metadata( 'rmpro_reviews', $review['id'], '_response_time', true );
                    $response_by    = $response_by ? get_userdata( $response_by ) : false;
                    $review_content = wp_kses_post( $review['your_review'] );

                    if( $enable_excerpts ) :
                        $words          = explode( ' ', $review_content );
                        $show_more      = count( $words ) > $excerpt_length;
                        $short_review   = $show_more ? implode( ' ', array_slice( $words, 0, $excerpt_length ) ) . '...' : $review_content; 
                    else :
                        $short_review   = $review_content;
                    endif;

                    rmpro_get_template(
                        'review.php',
                        array(
                            'review'         => $review,
                            'categories'     => $categories,
                            'review_content' => $review_content,
                            'short_review'   => $short_review,
                            'response'       => $response,
                            'response_by'    => $response_by,
                            'response_time'  => $response_time,
                            'enable_avatar'  => $enable_avatar,
                            'avatar_size'    => $avatar_size,
                            'show_more'      => $show_more
                        )
                    ); 
                endforeach;
                
                rmpro_get_template(
                    'review-pagination.php',
                    array(
                        'current_page'  => $current_page,
                        'total_pages'   => $total_pages,
                    )
                ); ?>
            </div>
        </div>
    </div>
<?php else : ?>
    <h3 class="rmpro-rating-average"><?php echo esc_html( isset( $localization['no_review'] ) ? $localization['no_review'] : esc_html_e( 'No reviews found.', 'review-master' ) ) ?></h3>
<?php endif; ?>
