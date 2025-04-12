<?php
/**
 * RMPRO Pagination Template
 *
 * This template handles the pagination display for reviews.
 *
 * This template can be overridden by copying it to yourtheme/review-master-pro/review-pagination.php.
 *
 * However, on occasion, RMPRO may need to update template files, and you (the theme developer) will
 * need to copy the new files to your theme to maintain compatibility. We try to minimize such updates.
 *
 * @package ReviewMaster
 */

if ( ! defined( 'ABSPATH' ) ) :
    exit; // Prevent direct access.
endif; ?>

<?php if ( $total_pages > 1 ) : 
    $start_page     = max( 1, $current_page - 2 );
    $end_page       = min( $total_pages, $current_page + 2 ); 
?>
    <div class="rmpro-pagination">
        
        <!-- Previous Button -->
        <?php if ( $current_page > 1 ) : ?>
            <a href="#" class="rmpro-page-link prev-page" data-page="<?php echo esc_attr( $current_page - 1 ); ?>">
                <span>&larr;</span> <!-- Left Arrow -->
            </a>
        <?php endif; ?>

        <!-- Always Show First Page -->
        <?php if ( $start_page > 1 ) : ?>
            <a href="#" class="rmpro-page-link" data-page="1">
                <?php echo esc_html__( '1', 'review-master' ); ?>
            </a>
            <?php if ( $start_page > 2 ) : ?>
                <span class="ellipsis">&hellip;</span>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Display Main Page Numbers -->
        <?php for ( $i = $start_page; $i <= $end_page; $i++ ) : ?>
            <a href="#" class="rmpro-page-link <?php echo ( $i === $current_page ) ? 'active' : ''; ?>" data-page="<?php echo esc_attr( $i ); ?>">
                <?php echo esc_html( $i ); ?>
            </a>
        <?php endfor; ?>

        <!-- Always Show Last Page -->
        <?php if ( $end_page < $total_pages ) : ?>
            <?php if ( $end_page < ( $total_pages - 1 ) ) : ?>
                <span class="ellipsis">&hellip;</span>
            <?php endif; ?>
            <a href="#" class="rmpro-page-link" data-page="<?php echo esc_attr( $total_pages ); ?>">
                <?php echo esc_html( $total_pages ); ?>
            </a>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ( $current_page < $total_pages ) : ?>
            <a href="#" class="rmpro-page-link next-page" data-page="<?php echo esc_attr( $current_page + 1 ); ?>">
                <span>&rarr;</span> <!-- Right Arrow -->
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="rmpro-loader"><img src="<?php echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); ?>" alt="Loading..."></div>