/* Repère de la page en cours dans le menu, et apparitions au défilement */
(function () {
    'use strict';
    var ici = location.pathname.replace(/\/$/, '');
    document.querySelectorAll('.ap-nav a').forEach(function (a) {
        var p = new URL(a.href, location.href).pathname.replace(/\/$/, '');
        var boutique = /\/boutique$/.test(p) && document.body.matches('.woocommerce-shop, .tax-product_cat, .single-product');
        if (p === ici || boutique) a.setAttribute('aria-current', 'page');
    });
})();
