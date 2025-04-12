<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;

$rest_url = esc_url( get_rest_url() );
?>

<div class="rmpro-api-accordion">

    <!-- Get Review -->
    <div class="rmpro-api-accordion-item">
        <button class="rmpro-api-accordion-button active">Get Single Reviews</button>
        <div class="rmpro-api-accordion-content" style="display: block;">
            <p>Fetch a single review by review ID.</p>
            <code>GET <?php echo esc_url($rest_url) ?>rmpro/v1/review/ </code><br><br>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th></tr>
                <tr><td>review_id</td><td>integer</td><td>The ID of the review you want to retrieve.</td></tr>
            </table>
            <pre><?php echo esc_url($rest_url) ?>rmpro/v1/review/?review_id=5</pre>
        </div>
    </div>

    <!-- Get Reviews -->
    <div class="rmpro-api-accordion-item">
        <button class="rmpro-api-accordion-button">Get Reviews</button>
        <div class="rmpro-api-accordion-content">
            <p>Fetch multiple reviews with filter and pagination options.</p>
            <code>GET <?php echo esc_url($rest_url) ?>/rmpro/v1/reviews/</code><br><br>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>per_page</td>
                    <td>integer</td>
                    <td>Number of reviews per page</td>
                </tr>
                <tr>
                    <td>current_page</td>
                    <td>integer</td>
                    <td>Current page number</td>
                </tr>
                <tr>
                    <td>status</td>
                    <td>string</td>
                    <td>Review status (e.g., approved, pending, spam)</td>
                </tr>
                <tr>
                    <td>module</td>
                    <td>string</td>
                    <td>Review module (e.g., product, post, custom)</td>
                </tr>
                <tr>
                    <td>s</td>
                    <td>string</td>
                    <td>Search keyword for review content</td>
                </tr>
                <tr>
                    <td>associate_id</td>
                    <td>integer</td>
                    <td>Related post/user ID</td>
                </tr>
                <tr>
                    <td>order_by</td>
                    <td>string</td>
                    <td>Sorting field (e.g., date, rating, author)</td>
                </tr>
                <tr>
                    <td>order</td>
                    <td>string</td>
                    <td>Sorting order (asc or desc)</td>
                </tr>
                <tr>
                    <td>rating</td>
                    <td>integer</td>
                    <td>Filter by rating (1-5)</td>
                </tr>
            </table>
            <pre><?php echo esc_url($rest_url) ?>rmpro/v1/reviews/?per_page=10&status=approved&order_by=date&order=desc</pre>
        </div>
    </div>


<!-- Add Review -->
<div class="rmpro-api-accordion-item">
    <button class="rmpro-api-accordion-button">Add a Review</button>
    <div class="rmpro-api-accordion-content">
        <p>Submit a new review.</p>
        <code>POST <?php echo esc_url($rest_url) ?>rmpro/v1/add-review/</code><br><br>
        <table>
            <tr><th>Parameter</th><th>Type</th><th>Description</th></tr>
            <tr><td>review_id</td><td>integer</td><td>Review ID (for updating an existing review)</td></tr>
            <tr><td>associate_id</td><td>integer</td><td>Related post/user ID</td></tr>
            <tr><td>title</td><td>string</td><td>Review title</td></tr>
            <tr><td>name</td><td>string</td><td>Reviewer’s name</td></tr>
            <tr><td>email</td><td>string</td><td>Reviewer’s email</td></tr>
            <tr><td>ratings</td><td>integer</td><td>Star rating (e.g., 1–5)</td></tr>
            <tr><td>status</td><td>string</td><td>Review status (e.g., pending, approved)</td></tr>
            <tr><td>ip_address</td><td>string</td><td>IP address of the reviewer</td></tr>
            <tr><td>your_review</td><td>string</td><td>The main review content</td></tr>
            <tr><td>score</td><td>integer</td><td>Optional score or secondary rating</td></tr>
            <tr><td>module_type</td><td>string</td><td>Module type (e.g., post, product, etc.)</td></tr>
            <tr><td>module</td><td>string</td><td>Module identifier</td></tr>
            <tr><td>avg_rating</td><td>float</td><td>Average rating (if calculated externally)</td></tr>
        </table>
        <pre>
        <?php echo esc_url($rest_url) ?>rmpro/v1/add-review/

{
    "associate_id": 123,
    "title": "Fantastic!",
    "name": "John Doe",
    "email": "john@example.com",
    "ratings": 5,
    "status": "pending",
    "ip_address": "192.168.0.1",
    "your_review": "Excellent quality and support.",
    "score": 10,
    "module_type": "product",
    "module": "woocommerce",
    "avg_rating": 4.7
}
        </pre>
    </div>
</div>


</div>