let loadingMore = false;

var Main = Main || {};
(function($, module) {

//----------- projectList-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#projectsList");
        let totalItems = $("#projects-list").data('total-items');
        let url = 'projects_load_more';
        module.listingLoader($table, totalItems,url);

    });
//----------- /projectList-load-more ---------------//

//----------- client-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#clients-list");
        let totalItems = $("#clientsList").data('total-items');
        let url = 'clients_load_more';
        module.listingLoader($table, totalItems,url);

    });
//----------- /client-load-more ---------------//

//----------- team-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#teams-list");
        let totalItems = $("#teamsList").data('total-items');
        let url = 'teams_load_more';
        module.listingLoader($table, totalItems,url);

    });
//----------- /team-load-more ---------------//

//----------- service-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#services-list");
        let totalItems = $("#servicesList").data('total-items');
        let url = 'services_load_more';
        module.listingLoader($table, totalItems,url);

    });
//----------- /service-load-more ---------------//

//----------- request-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#request-list");
        let totalItems = $("#requestList").data('total-items');
        let url = 'request_load_more';
        module.listingLoader($table, totalItems,url);

    });
//----------- /request-load-more ---------------//

module.listingLoader = function ($table,totalItems,url) {
    let scrolled = window.scrollY;
    let availableScroll = Math.max($(document).height() - $(window).height(), 0);
    let scrollPercentage = Math.round(scrolled / availableScroll * 100);

    if (scrollPercentage > 90 && !loadingMore ) {
        let loadedItems = $table.find('tbody tr:not(.no-results-row)').length;
        let hasMore = totalItems > loadedItems;

        if (hasMore) {
            loadingMore = true;
            const loadMoreUrl = Routing.generate(url, {offset: loadedItems});

            $(".loading-image").removeClass("visibility-hidden");

            $.get(loadMoreUrl, function(data, status, jqXHR) {
                if (jqXHR.getResponseHeader('X-Authenticated') == 'NO') {
                    return window.location.reload();
                }

                if (data.length > 0) {
                    $table.find('tbody').append(data)
                }

                loadingMore = false;

                $(".loading-image").addClass("visibility-hidden");
            }).fail(function (xhr, status, error) {
            });
        }
    }
};
})(jQuery, Main);

