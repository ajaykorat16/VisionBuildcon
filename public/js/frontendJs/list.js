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

                    // module.onShowModal();
                    // module.onHideModal();

                }).fail(function (xhr, status, error) {
                    console.error("Failed to load more content: ", error);
                    loadingMore = false;
                });
            }
        }
    }

    module.onShowModal = function () {
        $(document).on('click', 'a[data-bs-toggle="modal"]', function() {
            var projectSlug = $(this).data('slug');
            module.fetchProjectImages(projectSlug);
        });
    }
    
    module.onHideModal = function () {
        $('#showImages').on('hidden.bs.modal', function () {
            if (swiper) {
                swiper.destroy(true, true);
                swiper = undefined;
            }
        });
    }

    module.fetchProjectImages = function(projectSlug) {
        $.ajax({
            url: '/project/' + projectSlug,  // Adjust to match your route
            method: 'GET',
            success: function(response) {
                console.log('Fetched images:', response.images);  // Add this line
                var images = response.images;
                var $gallery = $('#image-gallery');
                $gallery.empty();

                images.forEach(function(image) {
                    var slide = $('<div class="swiper-slide">').append(
                        $('<img>').attr('src', '/image/' + image)
                    );
                    $gallery.append(slide);
                });
                    // Initialize Swiper
                if (!swiper) {
                    swiper = new Swiper('.swiper-container', {
                        loop: true,
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                    });
                } else {
                    swiper.update();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching images:', error);
            }
        });
    }
})(jQuery, Main);
