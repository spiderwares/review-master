<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) :
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
endif;

class RMPRO_Reviews_List_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct([
            'singular' => esc_html__( 'Review', 'review-master' ),
            'plural'   => esc_html__( 'Reviews', 'review-master' ),
            'ajax'     => false,
        ]);
    }

    /**
     * Add Empty Trash button in the extra tablenav.
     */
    protected function extra_tablenav($which) {
        if ($which === 'top' && isset($_GET['review_status']) && $_GET['review_status'] === 'trash' && $this->has_items() ) :
            $empty_trash_url = add_query_arg([
                'action'  => 'empty_trash'
            ]);

            echo '<div class="alignleft actions">';
            echo '<a href="' . esc_url( $empty_trash_url ) . '" class="button action">' . esc_html__( 'Empty Trash', 'review-master' ) . '</a>';
            echo '</div>';
        endif;
    }

    /**
     * Define columns
     */
    public function get_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => esc_html__( 'Title', 'review-master' ),
            'author'        => esc_html__( 'Author', 'review-master' ),
            'rating'        => esc_html__( 'Rating', 'review-master' ),
            'review'        => esc_html__( 'Review', 'review-master' ),
            'module_title'  => esc_html__( 'Module', 'review-master' ),
            'created_at'    => esc_html__( 'Submitted on', 'review-master' ),
        ];
    }

    /**
     * Define sortable columns
     */
    public function get_sortable_columns() {
        return [
            'title'      => ['title', true],
            'rating'     => ['rating', true],
            'created_at' => ['created_at', false],
        ];
    }

    /**
     * Define bulk actions
     */
    protected function get_bulk_actions() {
        return [
            'trash'     => esc_html__( 'Trash', 'review-master' ),
            'spam'      => esc_html__( 'Spam', 'review-master' ),
            'approve'   => esc_html__( 'Approve', 'review-master' ),
            'unapprove' => esc_html__( 'Unapprove', 'review-master' ),
        ];
    }

    /**
     * Bulk Action
     */
    public function process_bulk_action() {
        // Verify nonce for security
        
        if ( ! empty( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-reviews' ) ) :
            wp_die( esc_html__( 'Invalid nonce!', 'review-master' ) );
        endif;
        

        if ( isset( $_GET['action'] ) ) :
            $review_ids = isset( $_GET['review'] ) ? array_map( 'intval', $_GET['review'] ) : [];

            if ( 'approve' === $this->current_action() ) :
                rmpro_update_status( $review_ids, 'approve' );

            elseif ( 'unapprove' === $this->current_action() ) :
                rmpro_update_status( $review_ids, 'unapprove' );

            elseif ( 'spam' === $this->current_action() ) :
                rmpro_update_status( $review_ids, 'spam' );

            elseif ( 'trash' === $this->current_action() ) :
                rmpro_update_status( $review_ids, 'trash' );

            elseif( 'empty_trash' === $this->current_action() ) :
                rmpro_delete_trashed_reviews();
            endif;

            $review_nonce = wp_create_nonce( 'bulk-reviews' );
            $query_string = add_query_arg(
                [
                    'page'          => 'rmpro',
                    'success'       => 'true',
                    '_wpnonce'      => $review_nonce
                ], 
                $query_string
            );
            wp_safe_redirect( esc_url_raw( admin_url( 'admin.php' . $query_string ) ) );
            exit;
            

        elseif ( ! empty( $_GET['_wp_http_referer'] ) ) :
            wp_safe_redirect( admin_url( 'admin.php?page=rmpro' ) );
            exit;

        endif;
    }

    /**
     * Prepare items for display
     */
    public function prepare_items() {
        $this->process_bulk_action(); // Process bulk actions

        $per_page               = $this->get_items_per_page( 'reviews_per_page', 20 );
        $current_page           = $this->get_pagenum();
        $status                 = isset( $_GET['review_status'] ) && ! empty( $_GET['review_status'] ) ? [ sanitize_text_field( wp_unslash( $_GET['review_status'] ) ) ] : [];
        $search                 = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // Capture the search term
        $orderby                = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : ''; // Capture the search term
        $order                  = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : ''; // Capture the search term
        $total_items            = rmpro_get_reviews_count( array(
                                    'status'        => $status,
                                    's'             => $search,
                                ) );

        $this->_column_headers  = [$this->get_columns(), [], $this->get_sortable_columns()];
        $this->items            = rmpro_get_reviews( 
            array( 
                'per_page'      => $per_page,
                'current_page'  => $current_page,
                'status'        => $status,
                's'             => $search,
                'order_by'      => $orderby,
                'order'         => $order,
            ) 
        );
        // Set pagination arguments
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
        ]);
    }    

    /**
     * Get Views
     */
    protected function get_views() {
        $status_links = [];
        $link = admin_url('admin.php?page=rmpro');

        // Define possible statuses
        $statuses = apply_filters( 'rmpro_review_statuses_filter', 
            array(
                ''              => esc_html__( 'All %s', 'review-master' ),
                'unapprove'     => esc_html__( 'Pending %s', 'review-master' ),
                'approve'       => esc_html__( 'Approved %s', 'review-master' ),
                'spam'          => esc_html__( 'Spam %s', 'review-master' ),
                'trash'         => esc_html__( 'Trash %s', 'review-master' ),
            )
        );

        foreach ( $statuses as $status => $label ) :
            // Get count for the specific status
            $count = ($status == '') ? rmpro_get_reviews_count() : rmpro_get_reviews_count( array( 'status' => array( $status ) ) );

            $status_links[$status] = sprintf(
                '<a href="%s"%s>%s</a>',
                esc_url(add_query_arg('review_status', $status, $link)),
                ( isset($_GET['review_status']) && $_GET['review_status'] === $status ) ? ' class="current"' : '',
                sprintf($label, '<span class="count">(' . number_format_i18n($count) . ')</span>')
            );
        endforeach;

        return apply_filters( 'rmpro_review_status_links', $status_links );
    }

    /**
     * @param array $review Review object.
     */
    public function single_row( $review ) { 
        ?>
        <tr id='<?php echo esc_attr( 'rmpro-review-' . $review['id'] ); ?>' class='<?php echo esc_attr( 'rmpro-review-row rmpro-' . $review['status'] ); ?>'>
		    <?php $this->single_row_columns( $review ); ?>
		</tr> 
        <?php 
    }

    /**
     * @param array $review Review object.
     */
    public function column_created_at( $review ) {
        printf(
            /* translators: 1: Review date, 2: Review time. */
            esc_html__( '%1$s at %2$s', 'review-master' ),
            esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review['created_at'] ) ) ),
            esc_html( date_i18n( get_option( 'time_format' ), strtotime( $review['created_at'] ) ) )
        );
    }    

    
    /**
     * Checkbox column
     */
    public function column_cb($review) {
        return sprintf('<input type="checkbox" name="review[]" value="%s" />', esc_attr($review['id']));
    }

    /**
     * Author column
     */
    public function column_author($review) {
        return sprintf(
            '<div class="heading_col title-wraper">
                <img decoding="async" src="%1$s" alt="%2$s Avatar">
                <div class="review-content">
                    <div class="rmpro-reviewr-name">%2$s</div>
                    <div class="rmpro-reviewr-email">%3$s</div>
                </div>
            </div>',
            esc_url( get_avatar_url( $review['email'], [ 'size' => 35 ] ) ),
            esc_attr($review['name']),
            esc_html($review['email']),
        );
    }

    /**
     * Title column with actions.
     *
     * @param array $review The current item data.
     * @return string
     */
    public function column_title( $review ) {
        $title    = ! empty( $review['title'] ) ? $review['title'] : '—';
        $edit_url = rmpro_get_review_edit_url( $review['id'] );

        if ( $edit_url ) :
            $link = sprintf(
                '<a href="%s" class="row-title" aria-label="%s">%s</a>',
                esc_url( $edit_url ),
                esc_attr( sprintf( esc_attr( '“%1$s” (%2$s)', 'review-master' ), $title, _x( 'Edit', 'admin-text', 'review-master' ) ) ),
                esc_html( $title )
            );
        endif;

        // Fetch row actions
        $actions = $this->get_row_actions( $review );
        return sprintf( '%s %s', $link, $this->row_actions( $actions ) );
    }

     /**
     * Module title column
     *
     * @param array $review
     * @return string
     */
    public function column_module_title( $review ) {
        $module_link = rmpro_get_module_html_link( $review );
        return $module_link ? $module_link : '—';
    }

    /**
     * Default column rendering
     */
    public function column_default($review, $column_name) {
        return wp_kses_data($review[$column_name]);
    }

    /**
     * Define row actions for each item
     */
    public function get_row_actions( $review, $always_visible = false ) {
        $actions['id'] = sprintf(
            '<span>%s%d</span>',
            esc_html__( 'ID: ', 'review-master' ),
            esc_html( $review['id'] )
        );

        $actions['edit'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url( rmpro_get_review_edit_url( $review['id'] ) ),
            esc_html__( 'Edit', 'review-master' )
        );

        if( 'spam' === $review['status'] ) :
            $actions['approve'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'approve' ),
                esc_html__( 'Not Spam', 'review-master' ),
            );
        endif;

        if( in_array( $review['status'], array( 'spam', 'trash' ) ) ) :
            $actions['delete'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'delete' ),
                esc_html__( 'Delete Permanently', 'review-master' )
            );
        endif;

        if( ! in_array( $review['status'], array( 'spam', 'trash' ) ) ) :
            $actions['trash'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'trash' ),
                esc_html__( 'Trash', 'review-master' )
            );

            $actions['spam'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'spam' ),
                esc_html__( 'Spam', 'review-master' )
            );

            $actions['respond'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'respond' ),
                esc_html__( 'Respond', 'review-master' )
            );
        endif;
        
        if( 'trash' === $review['status'] ) :
            $actions['restore'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'approve' ),
                esc_html__('Restore', 'review-master')
            );
        endif;

        if( 'unapprove' === $review['status'] ) :
            $actions['approve'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'approve' ),
                esc_html__( 'Approve', 'review-master' ),
            );
        endif;

        if( 'approve' === $review['status'] ) :
            $actions['unapprove'] = sprintf(
                '<a href="javascript:void(0)" review-id="%d" action="%s">%s</a>',
                esc_attr( $review['id'] ),
                esc_attr( 'unapprove' ),
                esc_html__( 'Unapprove', 'review-master' ),
            );
        endif;
        return $actions;
    }

    /**
     * Column Review content
     */
    public function column_review( $review ) {
        global $wpdb;
        $review_response    = get_metadata( 'rmpro_reviews', $review['id'], '_response', true );
        $review_text        = esc_html( $review['your_review'] );
        $shortened_review   = ( strlen( $review_text ) > 50 ) ? substr( $review_text, 0, 50 ) . '...' : $review_text;

        return '<span data-review="' . esc_attr( $review['your_review'] ) . '">' . $shortened_review . '</span><input type="hidden" name="review_response" value="' . esc_attr( $review_response ) . '">';
    }


    /**
     * Render rating column
     */
    public function column_rating( $review) {
        $categories         = rmpro_get_review_by_category( $review['id']);
        $tooltip_content    = '';
    
        // Generate the tooltip content as a table or list
        if ( ! empty( $categories ) ) :
        $tooltip_content .= '<div class="rmpro-tooltip"><table>';
            foreach ($categories as $category) :
                $tooltip_content .= sprintf(
                    '<tr>
                        <td>%s</td>
                        <td>
                            <div class="rmpro-rating-container">
                                <div class="rmpro-rating-stars" style="width: %d%%;"></div>
                            </div>
                        </td>
                    </tr>',
                    esc_html($category['label']),
                    esc_attr($this->calculate_star_width(floatval( $category['rating']))),
                );
            endforeach;
            $tooltip_content .= '</table></div>';
        endif;        
    
        return sprintf(
            '<div class="rmpro-rating-container">
                <div class="rmpro-rating-stars" style="width: %d%%;"></div>
                %s
            </div>',
            esc_attr($this->calculate_star_width(floatval( $review['rating']))),
            $tooltip_content
        );
    }    
    

    /**
     * Review reply form
     */
    public function review_reply_form() { 
        include RMPRO_PATH . 'includes/admin/views/review-reply-form.php';
    }

    /**
     * Calculate star width based on rating
     */
    public static function calculate_star_width( $rating ) {
        return ($rating / 5) * 100;
    }
}
