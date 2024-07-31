let loadingMore = false;
var swiper;

var Main = Main || {};
(function($, module) {

    //----------- projectList-load-more ---------------//
    $(window).on('scroll', function () {
        let $table = $("#projectListing");
        let totalItems = $("#projectContent").data('total-items');
        let url = 'project_content_load_more';
        module.ContentLoader($table, totalItems, url);
    });
    //----------- /projectList-load-more ---------------//

    module.ContentLoader = function ($table, totalItems, url) {
        let scrolled = window.scrollY;
        let availableScroll = Math.max($(document).height() - $(window).height(), 0);
        let scrollPercentage = Math.round(scrolled / availableScroll * 100);

        if (scrollPercentage > 40 && !loadingMore) {
            let loadedItems = $table.find('.project-item').length;

            let hasMore = totalItems > loadedItems;

            if (hasMore) {
                loadingMore = true;
                const loadMoreUrl = Routing.generate(url, {offset: loadedItems});
                $(".loading-image").removeClass("visibility-hidden");

                $.get(loadMoreUrl, function(data, status, jqXHR) {
                    if (jqXHR.getResponseHeader('X-Authenticated') === 'NO') {
                        return window.location.reload();
                    }

                    if (data.content && data.content.length > 0) {
                        data.content.forEach(function(html) {
                            $table.find('#projectContent').append(html);
                        });
                    }

                    loadedItems = $table.find('.project-item').length;
                    loadingMore = false;

                    setTimeout(function(){$(".loading-image").addClass("visibility-hidden")}, 500);

                }).fail(function (xhr, status, error) {
                    console.error("Failed to load more content: ", error);
                    loadingMore = false;
                });
            }
        }
    }

})(jQuery, Main);
