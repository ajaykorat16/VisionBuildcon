$(document).ready(function() {

    $('.get-quote-button').on('click', function(event) {
        event.preventDefault();
        const requestForm = $('#requestForm');

        if (requestForm.length) {
            $('body').animate({
                scrollTop: requestForm.offset().top
            }, 1000, function() {
                requestForm.show();
            });
        } else {
            console.error('Request form not found.');
        }
    });

    $('#request-form').on('submit', function(event) {
        event.preventDefault();
        var $form = $(this);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            success: function(response) {
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
                alert('An error occurred. Please try again.');
            }
        });
    });

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
