$(document).ready(function() {
    $('.menu-toggle').click(function() {
        $(this).attr('aria-expanded', function(i, attr) {
            return attr == 'true' ? 'false' : 'true';
        });

        $('.ast-mobile-header-content').toggleClass('is-open');
        
        $(this).find('.ast-menu-svg').toggleClass('ast-visible');
        $(this).find('.ast-close-svg').toggleClass('ast-visible');
    });
});
