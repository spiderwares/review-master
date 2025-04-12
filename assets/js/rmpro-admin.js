jQuery(function($) {

    class RMProReviewManager {
        sortableList = $('.rmpro-category-list');

        constructor() {
            this.init();
        }

        init() {
            this.eventHandlers();
        }

        eventHandlers() {
            $(document.body).ready(this.initializeSortable.bind(this));
            $(document.body).on( 'click', '.rmpro-add-category', this.handleAddCategory.bind(this) );
            $(document.body).on( 'click', '.rmpro-remove-category', this.handleRemoveCategory.bind(this) );
            $(document.body).on( 'change', '[type="checkbox"], select', this.toggleVisibility.bind(this) );
            
            // Event handlers for notification template options  
            $(document.body).on( 'click', '.template-option', this.handleTemplateOptionClick.bind(this) );
            $(document.body).on( 'click', '.rmpro-page .row-actions .delete a, .rmpro-page .row-actions .spam a, .rmpro-page .row-actions .unapprove a, .rmpro-page .row-actions .trash a, .rmpro-page .row-actions .approve a,  .rmpro-page .row-actions .restore a', this.processQuickAction.bind(this) );
            $(document.body).on( 'click', '.rmpro-page .row-actions span.respond a', this.review_respond_form_open.bind(this) );
            $(document.body).on( 'click', '.rmpro-page button.cancel', this.cancel_respond.bind(this) );
            $(document.body).on( 'click', '.rmpro-respond .save', this.handleRespond.bind(this) );
            $(document.body).on( 'change', '.category-rating-table tbody input[type="radio"]', this.updateOverallRating.bind(this) );
            $(document.body).on( 'click', '.rmpro-api-accordion-button', this.handleAccordion.bind(this) );
        }

        handleAddCategory(e) {
            e.preventDefault();

            const __this = $(e.currentTarget),
                list = __this.parent().siblings('.rmpro-category-list'),
                input = __this.siblings('.rmpro-category-input'),
                moduleType = __this.data('module-type'),
                newCategory = input.val().trim();

            if (newCategory !== '') {
                var listItem = '<li><span class="rmpro-category-icon">&#9776;</span><span class="rmpro-category-name">' + newCategory 
                            + '</span><a href="#" data-category="' + newCategory + '" class="rmpro-remove-category">' + 
                            wp.i18n.__( "Remove", "review-master-pro" ) + 
                            '</a><input type="hidden" name="rmpro_review_categories[' + moduleType + '][category][]" value="' + newCategory + '"/>' +
                        '</li>';

                list.append(listItem);
                input.val(''); // Clear the input field
            }
        }

        handleRemoveCategory(e) {
            e.preventDefault();
            const __this = $(e.currentTarget);
            __this.closest('li').remove();
        }

        initializeSortable() {
            this.sortableList.sortable({
                items: 'li',
                cursor: 'move',
                opacity: 0.6,
                revert: true,
            });
        }

        toggleVisibility(e) {
            var _this = $(e.currentTarget);

            if (_this.is('select')) {
                var target      = _this.find(':selected').data('show'),
                    hideElemnt  = _this.data( 'hide' );
                    $(document.body).find(hideElemnt).hide();
                    $(document.body).find(target).show();
            } else {
                var target = _this.data('show');
                $(document.body).find(target).toggle();
            }
        }

        handleTemplateOptionClick(e) {
            e.preventDefault();
            const templateOption = $(e.currentTarget).data('template');
            let targetId = '';

            // Determine the context based on the button's parent container
            if ($(e.currentTarget).closest('#notification-template-options-admin').length) {
                targetId = 'notification_template';
            } else if ($(e.currentTarget).closest('#notification-template-options-author').length) {
                targetId = 'notification_template_author';
            } else if ($(e.currentTarget).closest('#notification-template-options-addresses').length) {
                targetId = 'notification_template_addresses';
            }

            // Get the WP Editor instance based on the context
            const wpEditor = tinymce.get(targetId);
            if (wpEditor) {
                const content = wpEditor.getContent();
                wpEditor.setContent(content + ' ' + templateOption);
                wpEditor.focus();
            }
        }

        processQuickAction(e) {
            e.preventDefault();
            var __this   = $(e.currentTarget),
                action   = __this.attr( 'action' ),
                reviewId = __this.attr( 'review-id' ),
                row      = __this.closest('tr');

            $.ajax({
                url: rmpro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'rmpro_handle_review_action',
                    review_id: reviewId,
                    review_action: action,
                    nonce: rmpro_params.nonce
                },
                beforeSend: function() {
                    row.css('opacity', '0.5');
                },
                success: function(response) {
                    if ( response.success ) {
                        if (action === 'delete' || action === 'spam' || action === 'trash' ) {
                            row.fadeOut(300, function() {
                                __this.remove();
                            });
                        }
                        
                        if( response.data ) {
                            row.attr("class", function(i, c) {
                                return (c || "").replace(/\brmpro-\S+/g, "").trim();
                            }).addClass(response.data.class);                            
                            row.find("td.column-title").html( response.data.quick_action );
                            $(".rmpro-page ul.subsubsub").replaceWith( response.data.status_filter );
                            $('.rmpro-pending .pending-count').html('<span class="pending-count rmpro-pending-count-'+ response.data.pending_count + '" aria-hidden="true">' + response.data.pending_count + '</span>');
                        }
                    }
                },
                error: function() {
                    alert( 'An error occurred. Please try again.' );
                },
                complete: function() {
                    row.removeAttr( 'style' );
                }
            });
        }

        review_respond_form_open(e) {
            e.preventDefault();
            var __this   = $( e.currentTarget ),
                reviewId = __this.attr( 'review-id' );

            if ( reviewId > 0) {
                var editRow   = $("#respondreview").clone(),
                    reviewRow = $("#rmpro-review-" + reviewId);

                $(".inline-edit-row").hide(); // Hide any other open inline editors
                editRow.find( "[name='review_id']" ).val( reviewId );
                editRow.insertAfter(reviewRow).show(); // Show the correct inline editor

                // Fill in the review text dynamically (if needed)
                var reviewContent = reviewRow.find("td.review.column-review span").data( 'review' );
                editRow.find("textarea[data-name='_rmpro_review']").val(reviewContent);

                var reviewResponse = reviewRow.find("td.review.column-review input[name='review_response']").val();
                editRow.find("textarea[name='_response']").val(reviewResponse);
            }
        }

        cancel_respond(e) {
            e.preventDefault();
            var __this = $(e.currentTarget);
            __this.parents('#respondreview').remove();
        }

        handleRespond(e){
            e.preventDefault();
            var __this      = $(e.currentTarget).closest('.rmpro-respond'),
                response    = __this.find('textarea[name="_response"]').val(),
                review_id   = __this.find('input[name="review_id"]').val(),
                spinner     = __this.find('.spinner');
           
            // Send the AJAX request to save the response
            $.ajax({
                url: rmpro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'rmpro_save_review_response',
                    review_id: review_id,
                    response: response,
                    nonce: rmpro_params.nonce
                },
                beforeSend: function() {
                    spinner.addClass('is-active');
                },
                success: function(response) {
                    if (response.success) {
                        __this.closest('tr').hide();
                    } else {
                        console.log('Error saving the response: ' + response.data.message);
                    }
                },
                error: function() {
                    console.log('Error sending the response. Please try again.');
                },
                compelte: function() {
                    spinner.removeClass('is-active');
                }
            });
        }

        updateOverallRating() {
            var totalRating     = 0,
                totalCategories = 0;
    
            $('.category-rating-table tbody tr').each(function() {
                var rating = $(this).find('input[type="radio"]:checked').val();
                if (rating) {
                    totalRating += parseInt(rating);
                    totalCategories++;
                }
            });
    
            // Calculate the average rating
            if (totalCategories > 0) {
                var averageRating   = totalRating / totalCategories,
                    percentage      = (averageRating / 5) * 100; 
                $('.rmpro-rating-stars.overall-rating').css('width', percentage + '%');
                $('.rmpro_review input[name="avg_rating"]').val(averageRating);
            }
        }

        handleAccordion(e){
        var __this = $(e.currentTarget),
            content = __this.next('.rmpro-api-accordion-content');

        // Close all other items
        $('.rmpro-api-accordion-button').not(__this).removeClass('active');
        $('.rmpro-api-accordion-content').not(content).slideUp();

        // Toggle this item
        __this.toggleClass('active');
        content.slideToggle();
        }
        
    }

    new RMProReviewManager();
});
