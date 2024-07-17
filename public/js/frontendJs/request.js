$(document).ready(function() {

    // Smooth scroll to the request form when the get-quote button is clicked
    $('.get-quote-button').off('click').on('click', function(event) {
        event.preventDefault();
        console.log('Get quote button clicked');
        const requestForm = $('#requestForm');

        if (requestForm.length) {
            $('body').animate({
                scrollTop: requestForm.offset().top
            }, 1000, function() {
                console.log('Scroll animation completed');
                requestForm.show();
            });
        } else {
            console.error('Request form not found.');
        }
    });

    // Handle form submission via AJAX
    $('#request-form').off('submit').on('submit', function(event) {
        event.preventDefault();
        console.log('Request form submitted');
        var $form = $(this);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            success: function(response) {
                console.log('AJAX request successful');
                var responseHtml = $(response);
                var newFormHtml = responseHtml.find('#request-form').html();
                var successMessage = responseHtml.find('.flash-messages').html();
                var errorMessages = responseHtml.find('#error-email').html();

                $form.html(newFormHtml);
                if (successMessage && successMessage.trim().length > 0) {
                    $('.flash-messages').html(successMessage).fadeIn();
                    scrollToSuccessMessage();
                } else if (errorMessages && errorMessages.trim().length > 0) {
                    scrollToForm();
                } else {
                    console.log('No success or validation errors found');
                }
            },
            error: function(response) {
                console.error('AJAX request failed');
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Scroll to the success message
    function scrollToSuccessMessage() {
        var successMessage = $('.flash-messages');
        if (successMessage.length > 0) {
            $('html, body').animate({
                scrollTop: successMessage.offset().top
            }, 'slow', function() {
                console.log('Scroll to success message completed');
            });
        }
    }

    // Scroll back to the form in case of errors
    function scrollToForm() {
        var requestForm = $('#requestForm');
        if (requestForm.length > 0) {
            $('html, body').animate({
                scrollTop: requestForm.offset().top
            }, 'slow', function() {
                console.log('Scroll to form completed');
            });
        }
    }
});
