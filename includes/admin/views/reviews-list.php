<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif; ?>

<div class="wrap rmpro-page">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php $reviews_table->views(); ?>

    <form id="reviews-filter" method="get">
        <?php $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : static::MENU_SLUG; ?>

        <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
        <input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', true ) ); ?>" />
        <?php $reviews_table->search_box( esc_html__( 'Search Review', 'review-master' ), 'reviews' ); ?>

        <?php $reviews_table->display(); ?>
    </form>
</div>
<?php
$reviews_table->review_reply_form(); ?>