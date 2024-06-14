var $collectionHolder;

$(document).ready(function () {
    $collectionHolder = $('#images');
    $collectionHolder.data('index', $collectionHolder.find('.images-div').length);

    $('#addNewImages').on('click', function(e) {
        e.preventDefault();
        addImagesForm();
    });

    removeImagesForm();

});

function addImagesForm() {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    $collectionHolder.append(newForm);

    $(".images-div").each(function(current) {
        $(this).attr('id', 'images_div_' + current);
    });
}

function removeImagesForm() {
    $(document).on('click', '.remove-image-item', function (e) {
        e.preventDefault();
        $(this).closest('.images-div').slideUp(1000, function () {
            $(this).remove();
            if ($("#images").find('.images-div').length === 0) {
                $("#addNewImages").addClass("mt-30");
            }
        });
    });
}

