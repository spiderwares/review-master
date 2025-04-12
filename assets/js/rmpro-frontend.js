jQuery(function($) {

    class RMProReviewFormHandler {

        constructor() {
            this.eventHandlers();
        }

        eventHandlers() {
            $(document.body).on('submit', '#rmpro-review-form', this.handleFormSubmit.bind(this));
            $(document.body).on('click', '.rmpro-view-btn', this.reviewViewPort.bind(this));
            $(document.body).on('click', '.rmpro-pagination a.rmpro-page-link:not(.active)', this.loadReviews.bind(this));
            $(document.body).on('submit', 'form.rmpro-review-filter', this.loadReviews.bind(this));
            $(document.body).on("click", ".toggle-review", this.toggleShoeMoreLess.bind(this));
        }

        toggleShoeMoreLess(e) {
            e.preventDefault();
            var __this = $(e.currentTarget),
                review = __this.closest(".rmpro-desc");

            review.find(".review-content, .full-review").toggle();
        }

        handleFormSubmit(e) {
            e.preventDefault();
            const __this        = $(e.currentTarget),
                formData        = __this.serialize() + '&action=rmpro_submit_review',
                submitButton    = __this.find('button[type="submit"]'),
                spinner         = __this.find('.rmpro-spinner');
                
            $.ajax({
                url: rmpro_ajax.ajax_url,
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    submitButton.prop('disabled', true);
                    spinner.show();
                },
                success: function(response) {
                    if (response.success) {
                        $('#rmpro-review-message').html('<p class="rmpro-success">' + response.data + '</p>');
                        location.reload();
                    } else if (response.data && response.data.validation_errors) {
                        $.each(response.data.validation_errors, function(fieldKey, errorMessage) {
                            $('#rmpro-error-' + fieldKey).text(errorMessage);
                        });
                    } else {
                        $('#rmpro-review-message').html('<p class="rmpro-error">' + response.data + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#rmpro-review-message').html('<p class="rmpro-error">' + error + '</p>');
                },
                complete: function() {
                    submitButton.prop( 'disabled', false );
                    spinner.hide();
                }
            });
        }


        reviewViewPort(e) {
            var __this   = $(e.currentTarget),
                viewtype = __this.data('viewtype');

            $('.rmpro-view-btn').removeClass('active'); // Remove active class from all buttons
            __this.addClass('active'); // Add active class to the clicked button
            $('.rmpro-review-container').attr('data-view', viewtype);
        }


        loadReviews(e) {
            e.preventDefault();
            let __this      = $(e.currentTarget),
                page        = __this.data('page') || 1,
                formData    = $('.rmpro-review-filter').serialize()+ '&page='+page+'&nonce='+rmpro_ajax.nonce+'&action=rmpro_get_reviews';
        
            $.ajax({
                url: rmpro_ajax.ajax_url,
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    $('.rmpro-review-container').addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        $('.rmpro-review-container').html( response.data.html );
                        $('.rmpro_total_review_count').text( response.data.total_review );
                    }
                },
                error: function() {
                    console.log('Error loading reviews.');
                },
                complete: function(){
                    $('.rmpro-review-container').removeClass('loading');
                }
            });
        }
        

    }

    new RMProReviewFormHandler();
    
});