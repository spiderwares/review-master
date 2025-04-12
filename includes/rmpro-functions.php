<?php
/**
 * Utility functions for Review Master plugin.
 *
 * @package ReviewMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rmpro_get_template' ) ) :
    /**
     * Get template file.
     *
     * @param string $template_name The template file name.
     * @param array  $args Optional. Arguments to pass to the template.
     * @param string $default_path Optional. Default template path.
     * @return void|WP_Error
     */
    function rmpro_get_template( $template_name, $args = array(), $default_path = '' ) {
        if ( empty( $default_path ) ) :
            $default_path = RMPRO_PATH . 'templates/';
        endif;

        $template = locate_template(
            array(
                trailingslashit( 'rmpro' ) . $template_name,
                $template_name,
            )
        );

        if ( empty( $template ) ) :
            $template = $default_path . $template_name;
        endif;

        if ( ! file_exists( $template ) ) :
            return new WP_Error(
                'error',
                /* translators: %s Template file path. */
                sprintf(
                    esc_html__( '%s does not exist.', 'review-master' ),
                    '<code>' . esc_html( $template ) . '</code>'
                )
            );
        endif;

        do_action( 'rmpro_before_template_part', $template, $args, $default_path );

        if ( ! empty( $args ) && is_array( $args ) ) :
            extract( $args, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
        endif;

        include $template;

        do_action( 'rmpro_after_template_part', $template, $args, $default_path );
    }
endif;


if ( ! function_exists( 'rmpro_get_review' ) ) :
    /**
     * Retrieve a single review by ID.
     *
     * @param int $review_id The review ID.
     * @return array|null The review data or null if not found.
     */
    function rmpro_get_review( $review_id ) {
        global $wpdb;
        $table = esc_sql( $wpdb->rmpro_reviews ); 

        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM `$table` WHERE id = %d", $review_id ),
            ARRAY_A
        );
    }
endif;

if ( ! function_exists( 'rmpro_get_reviews' ) ) :
    /**
     * Retrieve paginated reviews with filters and search.
     *
     * @param array $args {
     *     Optional. Arguments to filter reviews.
     *
     *     @type int    $per_page     Number of reviews per page. Default 10.
     *     @type int    $current_page Current page number. Default 1.
     *     @type array  $status       List of review statuses (e.g., ['approve', 'unapprove']). Default empty (all).
     *     @type array  $module       List of modules (e.g., ['product', 'product_cart', 'users']). Default empty (all).
     *     @type string $s            Search term to filter reviews by your_review, email, name, or title.
     *     @type string $order_by     Column to order by. Default 'id'.
     *     @type string $order        Order direction (ASC or DESC). Default 'DESC'.
     * }
     * @return array List of reviews.
     */
    function rmpro_get_reviews( $args = array() ) {
        global $wpdb;
        $table = $wpdb->rmpro_reviews;

        $defaults = array(
            'per_page'     => 10,
            'current_page' => 1,
            'status'       => array('approve'),
            'module'       => [],
            's'            => '',
            'associate_id' => '',
            'order_by'     => 'id',
            'order'        => 'DESC',
            'rating'       => ''
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'rmpro_get_reviews_args', $args );
        $offset = ( $args['current_page'] - 1 ) * $args['per_page'];

        $query = "SELECT * FROM $table WHERE 1=1";
        $query_params = array();

        if ( ! empty( $args['status'] ) ) :
            $placeholders = implode( ', ', array_fill( 0, count( $args['status'] ), '%s' ) );
            $query .= " AND status IN ($placeholders)";
            $query_params = array_merge( $query_params, $args['status'] );
        endif;

        if ( ! empty( $args['module'] ) ) :
            $placeholders = implode( ', ', array_fill( 0, count( (array)$args['module'] ), '%s' ) );
            $query .= " AND module IN ($placeholders)";
            $query_params = array_merge( $query_params, (array)$args['module'] );
        endif;

        if ( ! empty( $args['s'] ) ) :
            $search_term = '%' . $wpdb->esc_like( $args['s'] ) . '%';
            $query .= " AND (your_review LIKE %s OR email LIKE %s OR name LIKE %s OR title LIKE %s)";
            array_push( $query_params, $search_term, $search_term, $search_term, $search_term );
        endif;

        if ( ! empty( $args['associate_id'] ) ) :
            $query .= " AND associate_id = %d";
            array_push( $query_params, absint( $args['associate_id'] ) );
        endif;

        if ( ! empty( $args['rating'] ) && in_array( $args['rating'], array( 1, 2, 3, 4, 5 ) ) ) :
            $query .= " AND ROUND(rating) = %d";
            array_push( $query_params, intval( $args['rating'] ) );
        endif;    

        $order_by = isset( $args['order_by'] ) && ! empty( $args['order_by'] ) ? $args['order_by'] : 'id';
        $order    = isset( $args['order'] ) && strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        if( isset( $args['per_page'], $offset ) ) :
            $query .= " ORDER BY $order_by $order LIMIT %d OFFSET %d";
            array_push( $query_params, $args['per_page'], $offset );
        endif;

        if( is_array( $query_params ) && !empty( $query_params ) ) :
            $prepared_query = $wpdb->prepare( $query, ...$query_params );
        else :
            $prepared_query = $query;
        endif;
        
        return $wpdb->get_results( $prepared_query, ARRAY_A );
    }
endif;


if ( ! function_exists( 'rmpro_get_rating_summary' ) ) :
    /**
     * Retrieves the rating summary and category-wise average rating.
     *
     * @param array $args Arguments to filter ratings.
     * @return array Rating summary and category-wise ratings.
     */
    function rmpro_get_rating_summary( $args ) {
        global $wpdb;

        // Set default parameters.
        $defaults = array(
            'associate_id' => 0,
            'module'       => array(),
        );
        
        $args = wp_parse_args( $args, $defaults );

        // Define table names.
        $reviews_table = $wpdb->rmpro_reviews;
        $meta_table    = $wpdb->rmpro_reviewsmeta;

        // Base query conditions.
        $conditions = array( "r.status = 'approve'" );
        $values     = array();

        // Filter by associate ID if provided.
        if ( ! empty( $args['associate_id'] ) ) :
            $conditions[] = 'r.associate_id = %d';
            $values[]     = $args['associate_id'];
        endif;

        // Filter by module if provided.
        if ( ! empty( $args['module'] ) && is_array( $args['module'] ) ) :
            $placeholders = implode( ',', array_fill( 0, count( (array) $args['module'] ), '%s' ) );
            $conditions[] = "r.module IN ($placeholders)";
            $values       = array_merge( $values, (array) $args['module'] );
        endif;

        $where_clause = ! empty( $conditions ) ? 'WHERE ' . implode( ' AND ', $conditions ) : '';

        // Query to get category-wise average rating.
        $category_query = "
            SELECT label.meta_value AS category, 
                ROUND(AVG(CAST(rating.meta_value AS DECIMAL)), 2) AS average_rating
            FROM $meta_table AS label
            INNER JOIN $meta_table AS rating 
                ON rating.rmpro_reviews_id = label.rmpro_reviews_id 
                AND rating.meta_key = REPLACE(label.meta_key, '_rmpro_category_label_', '_rmpro_category_rating_')  
            INNER JOIN $reviews_table AS r 
                ON r.id = label.rmpro_reviews_id 
                AND r.status = 'approve'
            WHERE label.meta_key LIKE '_rmpro_category_label_%'
            " . ( ! empty( $conditions ) ? ' AND ' . implode( ' AND ', $conditions ) : '' ) . "
            GROUP BY label.meta_value
        ";
        
        $category_summary = ( is_array( $values ) && ! empty( $values ) )
            ? $wpdb->get_results( $wpdb->prepare( $category_query, ...$values ), ARRAY_A )
            : $wpdb->get_results( $category_query, ARRAY_A );

        // Query to get overall rating summary.
        $summary_query = "
            SELECT 
                ROUND(AVG(r.rating), 2) AS average_rating,
                COUNT(*) AS total_reviews,
                SUM(CASE WHEN ROUND(r.rating) = 5 THEN 1 ELSE 0 END) AS 5_star,
                SUM(CASE WHEN ROUND(r.rating) = 4 THEN 1 ELSE 0 END) AS 4_star,
                SUM(CASE WHEN ROUND(r.rating) = 3 THEN 1 ELSE 0 END) AS 3_star,
                SUM(CASE WHEN ROUND(r.rating) = 2 THEN 1 ELSE 0 END) AS 2_star,
                SUM(CASE WHEN ROUND(r.rating) = 1 THEN 1 ELSE 0 END) AS 1_star
            FROM $reviews_table AS r
            $where_clause
        ";

        $summary = ( is_array( $values ) && ! empty( $values ) )
            ? $wpdb->get_row( $wpdb->prepare( $summary_query, ...$values ), ARRAY_A )
            : $wpdb->get_row( $summary_query, ARRAY_A );

        // Return combined result.
        return array(
            'summary'          => $summary,
            'category_summary' => $category_summary,
        );
    }
endif;


if ( ! function_exists( 'rmpro_get_reviews_count' ) ) :
    /**
     * Get the total number of reviews in the database with filters and search.
     *
     * @param array $args {
     *     Arguments to filter review count.
     *
     *     @type array  $status List of review statuses (e.g., ['approve', 'unapprove']).
     *     @type array  $module List of modules (e.g., ['product', 'product_cart', 'users']).
     *     @type string $s      Search term to filter reviews by your_review, email, name, or title.
     * }
     * @return int Total count of filtered reviews.
     */
    function rmpro_get_reviews_count( $args = array() ) {
        global $wpdb;
        $table = $wpdb->rmpro_reviews;

        $defaults = array(
            'status' => [],
            'module' => [],
            's'      => '',
            'rating'       => ''
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'rmpro_get_reviews_count_args', $args );

        $query = "SELECT COUNT(*) FROM $table WHERE 1=1";
        $query_params = array();

        if ( ! empty( $args['status'] ) ) :
            $placeholders = implode( ', ', array_fill( 0, count( $args['status'] ), '%s' ) );
            $query .= " AND status IN ($placeholders)";
            $query_params = array_merge( $query_params, $args['status'] );
        endif;

        if ( ! empty( $args['module'] ) ) :
            $placeholders = implode( ', ', array_fill( 0, count( (array)$args['module'] ), '%s' ) );
            $query .= " AND module IN ($placeholders)";
            $query_params = array_merge( $query_params, (array)$args['module'] );
        endif;

        if ( ! empty( $args['associate_id'] ) ) :
            $query .= " AND associate_id = %d";
            array_push( $query_params, absint( $args['associate_id'] ) );
        endif;

        if ( ! empty( $args['s'] ) ) :
            $search_term = '%' . $wpdb->esc_like( $args['s'] ) . '%';
            $query .= " AND (your_review LIKE %s OR email LIKE %s OR name LIKE %s OR title LIKE %s)";
            array_push( $query_params, $search_term, $search_term, $search_term, $search_term );
        endif;

        if ( ! empty( $args['rating'] ) && in_array( $args['rating'], array( 1, 2, 3, 4, 5 ) ) ) :
            $query .= " AND ROUND(rating) = %d";
            array_push( $query_params, intval( $args['rating'] ) );
        endif;
        
        if( is_array( $query_params ) && !empty( $query_params ) ) :
            $prepared_query = $wpdb->prepare( $query, ...$query_params );
        else :
            $prepared_query = $query;
        endif;


        return (int) $wpdb->get_var( $prepared_query );
    }

endif;


if ( ! function_exists( 'rmpro_get_review_edit_url' ) ) :

    /**
     * Get Post Edit Link
     *
     * @return string
     */
    function rmpro_get_review_edit_url( $review_id ) {
        $link = admin_url( '/admin.php?page=rmpro-review&review_id=' . $review_id );
        return $link;
    }

endif;

if ( ! function_exists( 'rmpro_get_module_html_link' ) ) :

    /**
     * Get Post Title & Link
     *
     * @return string
     */
    function rmpro_get_module_html_link( $review ) {

        switch ( $review['module_type'] ) :
            case 'taxonomy':
                $term = get_term( (int) $review['associate_id'] );
                if ( $term && ! is_wp_error( $term ) ) :
                    $taxonomy_title = get_taxonomy( $term->taxonomy )->label;
                    $title = esc_html( $term->name . ' - ' . $taxonomy_title );
                    $link  = get_term_link( $term );
                endif;
                break;

            case 'post_type':
                $post = get_post( (int) $review['associate_id'] );
                if ( $post ) :
                    $title = esc_html( $post->post_title );
                    $link  = get_permalink( $post );
                endif;
                break;

            case 'users':
                if ( $review['module'] === 'users' ) :
                    $user = get_user_by( 'id', (int) $review['associate_id'] );
                    if ( $user ) :
                        $title = esc_html( $user->display_name );
                        $link  = esc_url( admin_url( 'user-edit.php?user_id=' . $user->ID ) );
                    endif;
                endif;
                break;

            default:
                $title = sprintf(
                    '<div class="module-type"><b>%s</b> %s</div><div class="module"><b>%s</b> %s</div>',
                    esc_html__('Module Type:', 'review-master'),
                    esc_html($review['module_type']),
                    esc_html__('Module:', 'review-master'),
                    esc_html($review['module'])
                );
                $title = apply_filters( 'rmpro_get_module', $title, $review );
                break;

        endswitch;

        if ( isset( $link ) ) :
            return sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $title ) );
        endif;

        if ( isset( $title ) ) :
            return wp_kses_post( $title );
        endif;
        return false;
    }

endif;

if ( ! function_exists( 'rmpro_get_review_by_category' ) ) :

    /**
     * Get Review by category with labels and ratings grouped together
     * @return array
     */
    function rmpro_get_review_by_category( $review_id ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    label.meta_value AS label, 
                    rating.meta_value AS rating 
                 FROM {$wpdb->rmpro_reviewsmeta} AS label
                 JOIN {$wpdb->rmpro_reviewsmeta} AS rating 
                   ON label.rmpro_reviews_id = rating.rmpro_reviews_id
                   AND REPLACE(label.meta_key, '_rmpro_category_label_', '') = REPLACE(rating.meta_key, '_rmpro_category_rating_', '')
                 WHERE label.rmpro_reviews_id = %d
                 AND label.meta_key LIKE '_rmpro_category_label_%%'
                 AND rating.meta_key LIKE '_rmpro_category_rating_%%'",
                $review_id
            ),
            ARRAY_A
        );
    }

endif;

if ( ! function_exists( 'rmpro_calculate_average_rating' ) ) :

    /**
     * Calculate the average rating.
     *
     * @param array $ratings List of numeric ratings.
     * @return float|false Average rating or false if empty.
     */
    function rmpro_calculate_average_rating( $ratings ) {
        if ( empty( $ratings ) || ! is_array( $ratings ) ) :
            return false;
        endif;

        $num_categories = count( $ratings );
        return $num_categories > 0 ? round( array_sum( $ratings ) / $num_categories, 2 ) : false;
    }
endif;

/**
 * Save or update a review in the database.
 *
 * @param array $args Review data.
 * @return int|bool Review ID on success, false on failure.
 */
if ( ! function_exists( 'rmpro_save_review' ) ) :

    function rmpro_save_review( $args ) {
        global $wpdb;

        // Ensure $args is an array
        if ( empty( $args ) || ! is_array( $args ) ) :
            return false;
        endif;

        // Define default values
        $defaults = array(
            'review_id'    => 0,
            'associate_id' => 0,
            'title'        => '',
            'name'         => '',
            'email'        => '',
            'avg_rating'   => 0,
            'status'       => 'unapprove',
            'ip_address'   => '',
            'your_review'  => '',
            'module_type'  => '',
            'module'       => '',
            'ratings'      => array(),
        );
        // Merge $args with defaults
        $args = wp_parse_args( $args, $defaults );

        // Prepare data for database
        $data = apply_filters( 
            'rmpro_save_review_data',
            array(
                'associate_id' => $args['associate_id'],
                'title'        => $args['title'],
                'name'         => $args['name'],
                'email'        => $args['email'],
                'rating'       => $args['avg_rating'],
                'status'       => $args['status'],
                'ip_address'   => $args['ip_address'],
                'your_review'  => $args['your_review'],
                'module_type'  => $args['module_type'],
                'module'       => $args['module'],
            )
        );

        $format    = [ '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s' ];
        do_action( 'rmpro_before_save_review', $args['review_id'], $args );

        // Check if updating or inserting a new review
        if ( $args['review_id'] ) :
            $review_id = absint( $args['review_id'] );
            $updated   = $wpdb->update( "{$wpdb->rmpro_reviews}", $data, [ 'id' => $review_id ], $format, [ '%d' ] );

            if ( false === $updated ) :
                return false; // Update failed
            endif;
        else :
            $wpdb->insert( "{$wpdb->rmpro_reviews}", $data, $format );
            $review_id = $wpdb->insert_id;

            if ( ! $review_id ) :
                return false; // Insert failed
            endif;
        endif;

        // Update review metadata
        if ( ! empty( $args['ratings'] ) && $review_id ) :
            rmpro_update_ratings_category( $args['ratings'], $review_id );
        endif;

        // Trigger action after review is saved
        do_action( 'rmpro_after_save_review', $review_id, $args );

        return $review_id;
    }

endif;

/**
 * Updates the ratings category metadata.
 *
 * @param array $ratings An associative array of category labels and their corresponding ratings.
 */
if ( ! function_exists( 'rmpro_update_ratings_category' ) ) :

    function rmpro_update_ratings_category( $ratings, $review_id ) {
        // Ensure ratings is an array before proceeding.
        if ( ! is_array( $ratings ) ) :
            return false;
        endif;

        $index = 0;
        foreach ( $ratings as $category => $rating ) :
            // Update metadata with category label and rating.
            update_metadata( 'rmpro_reviews', $review_id, "_rmpro_category_label_{$index}", $category );
            update_metadata( 'rmpro_reviews', $review_id, "_rmpro_category_rating_{$index}", $rating );

            $index++;
        endforeach;
        return true;
    }

endif;


/**
 * Update the status of a review.
 *
 * @param int    $review_id The ID of the review.
 * @param string $status    The new status of the review.
 */
if ( ! function_exists( 'rmpro_update_status' ) ) :

    function rmpro_update_status( $review_ids, $status ) {
        global $wpdb;
        $status = $status === 'restore' ? 'unapprove' : $status;

        if ( empty( $review_ids ) ) :
            return false;
        endif;

        // Convert IDs array into a comma-separated string
        $ids_string = implode(',', (array)$review_ids);

        // Execute a single query for batch updating
        return $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->rmpro_reviews} SET status = %s WHERE id IN ($ids_string)", $status)
        );
    }

endif;


/**
 * Delete a review from the database.
 *
 * @param int $review_id The ID of the review to delete.
 */
if ( ! function_exists( 'rmpro_delete_review' ) ) :       
        
    function rmpro_delete_review( $review_id ) {
        global $wpdb;
        $wpdb->delete(  $wpdb->rmpro_reviews, array( 'id' => $review_id ), array( '%d' ) );
        $wpdb->delete(  $wpdb->rmpro_reviewsmeta, array( 'rmpro_reviews_id' => $review_id ), array( '%d' ) );
    }

endif;

/**
 * Deletes all reviews with status 'trash' along with their meta data.
 *
 * This function retrieves all review IDs marked as 'trash' from the reviews table,
 * deletes their corresponding meta data from the meta table, and then removes the
 * reviews themselves.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool True if reviews were deleted, false if no trashed reviews were found.
 */
if ( ! function_exists( 'rmpro_delete_trashed_reviews' ) ) :

    function rmpro_delete_trashed_reviews() {
        global $wpdb;

        // Get all review IDs where status is 'trash'.
        $review_ids = $wpdb->get_col( "SELECT id FROM {$wpdb->rmpro_reviews} WHERE status = 'trash'" );

        // If there are no trashed reviews, return false.
        if ( empty( $review_ids ) ) :
            return false;
        endif;

        // Convert array of IDs into a comma-separated string for the SQL query.
        $ids_string = implode( ',', array_map( 'intval', (array)$review_ids ) );

        // Delete associated meta data from the reviews meta table.
        $wpdb->query( "DELETE FROM {$wpdb->rmpro_reviews_meta} WHERE review_id IN ($ids_string)" );

        // Delete the trashed reviews from the main reviews table.
        $wpdb->query( "DELETE FROM {$wpdb->rmpro_reviews} WHERE id IN ($ids_string)" );

        return true; // Return true indicating deletion was successful.
    }
endif;
