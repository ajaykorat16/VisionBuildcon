var Main = Main || {};

(function($, module){

    // Function to remove 'is-invalid' class on input
    function removeInvalidClass(selector) {
        $(document).on("keyup click", selector, function () {
            $(this).removeClass("is-invalid");
        });
    }

    // Generalized save event handler
    function saveHandler(saveBtn, nameSelector, descSelector, imgSelector, validateImage) {
        $(document).on("click", saveBtn, function (e) {
            e.preventDefault();

            const $name = $(nameSelector);
            const $textarea = $(descSelector);
            const $image = imgSelector ? $(imgSelector) : null;

            const isValid = module.validate($textarea, $name, $image, validateImage);

            if (isValid) {
                $(this).closest('form').submit();
            }
        });
    }

    // Register remove 'is-invalid' events
    removeInvalidClass("#project_name, #project_description, #client_name, #client_description, #service_name, #service_description, #team_name, #images");

    saveHandler("#project_save", "#project_name", "#project_description", null, false);
    saveHandler("#client_save", "#client_name", "#client_description", "#client_logo", true);
    saveHandler("#service_save", "#service_name", "#service_description", "#service_servicePhoto", true);
    saveHandler("#team_save", "#team_name", null, "#team_teamPhoto", true);

    module.validate = function ($textarea, $name, $image, validateImage) {
        let isValid = true;

        if ($textarea && !$textarea.val()) {
            $textarea.addClass("is-invalid");
            isValid = false;
        } else {
            $textarea && $textarea.removeClass("is-invalid");
        }

        if (!$name.val()) {
            $name.addClass("is-invalid");
            isValid = false;
        } else {
            $name.removeClass("is-invalid");
        }

        if (validateImage && $image && !$image.val()) {
            $('#images').addClass("is-invalid");
            isValid = false;
        } else {
            $image && $('#images').removeClass("is-invalid");
        }

        return isValid;
    };

})(jQuery, Main);
