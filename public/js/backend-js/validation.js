var Main = Main || {};

(function($, module){

    // Function to remove 'is-invalid' class on input
    function removeInvalidClass(selector) {
        $(document).on("keyup click", selector, function () {
            $(this).removeClass("is-invalid");
        });
    }

    // Generalized save event handler
    function saveHandler(saveBtn, nameSelector, descSelector) {
        $(document).on("click", saveBtn, function (e) {
            e.preventDefault();

            const $name = $(nameSelector);
            const $textarea = descSelector ? $(descSelector) : null;

            const isValid = module.validate($textarea, $name);
            console.log(isValid);
            if (isValid) {
                $(this).closest('form').submit();
            }
        });
    }

    // Register remove 'is-invalid' events
    removeInvalidClass("#project_name, #project_description, #client_name, #client_description, #service_name, #service_description, #team_name");

    saveHandler("#project_save", "#project_name", "#project_description");
    saveHandler("#client_save", "#client_name", "#client_description");
    saveHandler("#service_save", "#service_name", "#service_description");
    saveHandler("#team_save", "#team_name", null);

    module.validate = function ($textarea, $name) {
        let isValid = true;
        console.log(isValid);
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
        console.log(isValid);

        return isValid;
    };

})(jQuery, Main);
