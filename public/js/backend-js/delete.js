var Delete = Delete || {};

(function($, module) {

    module.onShowModal = function () {
        $(document).ready(function () {
            const modalSelector = "#deleteModal";
            const $modal = $(modalSelector);

            $modal.on('show.bs.modal', function (event) {
                const deleteSelector = $(event.relatedTarget);

                $modal.find("#deleteName").text(deleteSelector.data('name'));
                $modal.find("#Id").val(deleteSelector.data('id'));
            });
        })
    }

    module.deleteService = function () {
        let $url = 'services_delete';
        let row = '#service-row';
        let $list = $("#servicesList tr:not(.no-results-row)");
        module.onDelete($url,row,$list);
    }

    module.deleteProject = function () {
        let $url = 'projects_delete';
        let row = '#project-row';
        let $list = $("#projects-list tr:not(.no-results-row)");
        module.onDelete($url,row,$list);
    }

    module.deleteTeam = function () {
        let $url = 'teams_delete';
        let row = '#team-row';
        let $list = $("#teamsList tr:not(.no-results-row)");
        module.onDelete($url,row,$list);
    }

    module.deleteClient = function () {
        let $url = 'clients_delete';
        let row = '#client-row';
        let $list = $("#clientsList tr:not(.no-results-row)");
        module.onDelete($url,row,$list);
    }

    module.onDelete = function ($url,row,$list) {
        $(document).on('click', '#delete', function(e) {
            const modalSelector = "#deleteModal";
            const $modal = $(modalSelector);

            const Id = $(this).closest(modalSelector).find('#Id').val();
            const url = Routing.generate($url, {"id" : Id});

            $.get(url, function(data, status, jqXHR) {
                if (jqXHR.getResponseHeader('X-Authenticated') == 'NO') {
                    return window.location.reload();
                }

                if (data.status === 'ok') {
                    $(row-`${Id}`).remove();
                    console.log(data.message);
                    window.location.reload();
                    $('.flash-messages').append(data.message);

                    if ($list.length === 0) {
                        $(".no-results-row").show();
                    }
                }
                $modal.modal("hide");
                setTimeout(function() {
                    $('.flash-messages .alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 500);
            }).fail(function (xhr, status, error) {
                $('.flash-messages').append('<div class="alert alert-danger">Error deleting project.</div>');
            });
        })

    }
})(jQuery,Delete)