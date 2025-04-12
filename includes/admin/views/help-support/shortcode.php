<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>
<div id="shortcode-settings" class="rmpro-tab-content">
    <h1>Shortcode</h1>
    <div class="rmpro-accordion">
        <div class="rmpro-accordion-item">
            <div class="rmpro-accordion-item-header">
                <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23FFBD13' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                <b>How to display the review form?</b>
            </div>
            <div class="rmpro-accordion-item-body">
                <div class="rmpro-accordion-item-body-content">
                    
                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Module & Module Type</b></div>
                        <div class="shortcode-description">
                            Module: Assign the review form to a specific content type (e.g., product, service, event, post) using a Post Type name.
                        </div>
                        <div class="shortcode-description">
                            Module Type: Define the type of module for the review form (e.g., post_type, taxonomy, user).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> module="product"]</code></pre>
                            <pre><code>[<span>rmpro_review_form</span> module="product" module_type="post_type"]</code></pre>
                        </div>
                    </div><hr> 

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Associate ID</b></div>
                        <div class="shortcode-description">
                            Link the review form to a specific post, taxonomy, user, or other module using an ID, or use "queried" to automatically get the current page ID using function get_queried_object_id().
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> associate_id="123"]</code></pre>
                            <pre><code>[<span>rmpro_review_form</span> associate_id="queried"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Form Title</b></div>
                        <div class="shortcode-description">
                            Set a custom title for the review form, displayed above the form.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> title="Your Custom Title"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Submit Button Label</b></div>
                        <div class="shortcode-description">
                            Customize the text on the review form submit button.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> button_label="Submit Review"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Categories</b></div>
                        <div class="shortcode-description">
                            Assign the review form to specific categories for better organization.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> categories="Food,Quality"]</code></pre>
                        </div>
                    </div><hr>
                </div>
            </div>
        </div>

        <div class="rmpro-accordion-item">
            <div class="rmpro-accordion-item-header">
                <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23FFBD13' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                <b>How to display review submission form with custom module.</b>
            </div>
            <div class="rmpro-accordion-item-body">
                <div class="rmpro-accordion-item-body-content">
                    
                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Module & Module Type</b></div>
                        <div class="shortcode-description">
                            Module: If you need to set a custom module craeted a custom wordpress table which you have to integrate with review amster pro for example bookly plugin, such as (Movie or Membership, etc).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> module="movie"]</code></pre>
                            <pre><code>[<span>rmpro_review_form</span> module="movie" module_type="movie"]</code></pre>
                        </div>
                    </div><hr> 

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Associate ID</b></div>
                        <div class="shortcode-description">
                            Link the review form to a specific post, taxonomy, user, or other module using an ID, or use "queried" to automatically get the current page ID using function get_queried_object_id().
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> associate_id="123"]</code></pre>
                            <pre><code>[<span>rmpro_review_form</span> associate_id="queried"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Form Title</b></div>
                        <div class="shortcode-description">
                            Set a custom title for the review form, displayed above the form.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> title="Your Custom Title"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Submit Button Label</b></div>
                        <div class="shortcode-description">
                            Customize the text on the review form submit button.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> button_label="Submit Review"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Categories</b></div>
                        <div class="shortcode-description">
                            Assign the review form to specific categories for ratings.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_review_form</span> categories="Food,Quality"]</code></pre>
                        </div>
                    </div><hr>
                </div>
            </div>
        </div>

        <div class="rmpro-accordion-item">
            <div class="rmpro-accordion-item-header">
                <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23FFBD13' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                <b>How to display the reviews?</b>
            </div>
            <div class="rmpro-accordion-item-body">
                <div class="rmpro-accordion-item-body-content">
                    
                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Module & Module Type</b></div>
                        <div class="shortcode-description">
                            Module: Display reviews for a specific content type using a WordPress Post Type name (e.g., product, service, event).
                        </div>
                        <div class="shortcode-description">
                            Module type: Define the type of module for displaying reviews (e.g., post_type, taxonomy, user).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> module="product"]</code></pre>
                            <pre><code>[<span>rmpro_reviews</span> module="post" module_type="post_type"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Per Page Review</b></div>
                        <div class="shortcode-description">
                            Set the number of reviews displayed per page.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> per_page="10"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Associate ID</b></div>
                        <div class="shortcode-description">
                            Link reviews to a specific post or page using a Post ID or "post_id" for the current page.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> associate_id="123"]</code></pre>
                            <pre><code>[<span>rmpro_reviews</span> associate_id="post_id"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Summary</b></div>
                        <div class="shortcode-description">
                            Show or hide the review summary, including the average rating and statistics. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_summary="on"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Reviewer Image & Avtar Size </b></div>
                        <div class="shortcode-description">
                            Enable Avatar: Show or hide reviewer avatars in the reviews. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-description">
                            Avatar Size: Set the size of reviewer avatars in pixels. (e.g., 50, 100).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_avatar="on" avatar_size="40"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Excerpts & Length</b></div>
                        <div class="shortcode-description">
                            Enable Excerpts: Show or hide a short snippet of the review content. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-description">
                            Excerpt Length: Set the number of words displayed in the review excerpt. (e.g., 20, 50).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_excerpts="on" excerpt_length="20"]</code></pre>
                        </div>
                    </div><hr>

                </div>
            </div>
        </div>

        <div class="rmpro-accordion-item">
            <div class="rmpro-accordion-item-header">
                <img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23FFBD13' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
                <b>How to display reviews from custom module?</b>
            </div>
            <div class="rmpro-accordion-item-body">
                <div class="rmpro-accordion-item-body-content">
                    
                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Module & Module Type</b></div>
                        <div class="shortcode-description">
                            Module: Specify your custom module, such as a movie, membership, etc.
                        </div>
                        <div class="shortcode-description">
                            Module Type: Define the custom module type for displaying reviews (e.g., movie, membership, user).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> module="movie"]</code></pre>
                            <pre><code>[<span>rmpro_reviews</span> module="movie" module_type="movie"]</code></pre>
                        </div>
                    </div><hr>


                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Per Page Review</b></div>
                        <div class="shortcode-description">
                            Set the number of reviews displayed per page.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> per_page="10"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Associate ID</b></div>
                        <div class="shortcode-description">
                            Use "associate_id" to link reviews to a specific post by passing its Post ID. You can also use "post_id" to dynamically assign the current movie or membership etc.
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> associate_id="123"]</code></pre>
                            <pre><code>[<span>rmpro_reviews</span> associate_id="movie_id"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Summary</b></div>
                        <div class="shortcode-description">
                            Show or hide the review summary, including the average rating and statistics. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_summary="on"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Reviewer Image & Avtar Size</b></div>
                        <div class="shortcode-description">
                            Enable Avatar: Show or hide reviewer avatars in the reviews. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-description">
                            Avatar Size: Set the size of reviewer avatars in pixels. (e.g., 50, 100).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_avatar="on" avatar_size="40"]</code></pre>
                        </div>
                    </div><hr>

                    <div class="shortcode-item">
                        <div class="shortcode-heading"><b>Review Excerpts & Length</b></div>
                        <div class="shortcode-description">
                            Enable Excerpts: Show or hide a short snippet of the review content. Accepted values are "on" or "off".
                        </div>
                        <div class="shortcode-description">
                            Excerpt Length: Set the number of words displayed in the review excerpt. (e.g., 20, 50).
                        </div>
                        <div class="shortcode-container">
                            <pre><code>[<span>rmpro_reviews</span> enable_excerpts="on" excerpt_length="20"]</code></pre>
                        </div>
                    </div><hr>

                </div>
            </div>
        </div>
    </div>
</div>