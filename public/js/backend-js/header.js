$(document).ready(function() {
    const $navLinks = $('.nav-link');

    setActiveLink();

    function setActiveLink() {
        const currentPath = window.location.pathname;

        $navLinks.each(function() {
            const linkHref = $(this).attr('href');
            if (!linkHref) return;

            const linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/$/, '');

            if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    //------------------- front-end header-------------------------//
    const $navbarLinks = $('.site-navigation .menu-item-object-page');

    setHeaderActiveLink();

    function setHeaderActiveLink() {
        const currentRoute = window.location.pathname;
        let hasActiveLink = false;

        $navbarLinks.each(function() {
            const linkHref = $(this).find('a').attr('href');
            if (!linkHref) return;
            const linkRoutePath = new URL(linkHref, window.location.origin).pathname;

            if ((currentRoute === linkRoutePath || (currentRoute.startsWith(linkRoutePath) && linkRoutePath !== '/')) && !hasActiveLink) {
                $(this).addClass('current-menu-item page_item page-item-7 current_page_item');
                hasActiveLink = true;
            } else {
                $(this).removeClass('current-menu-item page_item page-item-7 current_page_item');
            }
        });
    }
});
