/* Atelier Pivoine — interactions légères */
(function () {
    'use strict';

    var header = document.getElementById('site-header');
    var toggle = document.querySelector('.menu-toggle');

    /* En-tête : ombre légère au défilement */
    function onScroll() {
        if (!header) return;
        header.classList.toggle('is-scrolled', window.scrollY > 10);
    }
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    /* Menu mobile */
    if (toggle && header) {
        toggle.addEventListener('click', function () {
            var open = header.classList.toggle('menu-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        header.querySelectorAll('.main-navigation a').forEach(function (link) {
            link.addEventListener('click', function () {
                header.classList.remove('menu-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }


    /* Rail de bouquets : boutons précédent / suivant */
    var rail = document.getElementById('rail');
    document.querySelectorAll('.rail-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!rail) return;
            var step = (rail.querySelector('.product-card') || { offsetWidth: 300 }).offsetWidth + 18;
            rail.scrollBy({ left: step * parseInt(btn.dataset.rail, 10), behavior: 'smooth' });
        });
    });

    /* Apparition douce au défilement */
    var items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    items.forEach(function (el) { observer.observe(el); });
})();


/* ==========================================================================
   Boutique : panier latéral, ajout rapide, connexion, animations
   ========================================================================== */
(function () {
    'use strict';

    var cfg = window.atelierPivoine || null;
    var drawer = document.getElementById('cart-drawer');
    var overlay = document.querySelector('.drawer-overlay');
    var inner = document.getElementById('cart-drawer-inner');
    var toastEl = document.getElementById('ap-toast');
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var lastFocus = null;
    var busy = false;

    /* ---------- Panier latéral ---------- */
    function openDrawer() {
        if (!drawer) return false;
        lastFocus = document.activeElement;
        drawer.removeAttribute('inert');
        drawer.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('drawer-open');
        var closeBtn = drawer.querySelector('.drawer-close');
        setTimeout(function () { if (closeBtn) closeBtn.focus(); }, 60);
        return true;
    }

    function closeDrawer() {
        if (!drawer) return;
        document.documentElement.classList.remove('drawer-open');
        drawer.setAttribute('aria-hidden', 'true');
        drawer.setAttribute('inert', '');
        if (lastFocus && document.body.contains(lastFocus)) lastFocus.focus();
    }

    function toast(msg) {
        if (!toastEl) return;
        toastEl.textContent = msg;
        toastEl.classList.add('is-visible');
        clearTimeout(toast.t);
        toast.t = setTimeout(function () { toastEl.classList.remove('is-visible'); }, 2400);
    }

    function bumpCart() {
        document.querySelectorAll('.header-cart').forEach(function (el) {
            el.classList.remove('is-bumped');
            void el.offsetWidth;
            el.classList.add('is-bumped');
        });
    }

    /* Petite image qui « vole » de la carte jusqu'au panier */
    function flyToCart(fromEl) {
        if (reduceMotion || !fromEl) return;
        var card = fromEl.closest('.shop-card, .product-card');
        var img = card && card.querySelector('img');
        var target = document.querySelector('.header-cart');
        if (!img || !target) return;
        var a = img.getBoundingClientRect();
        var b = target.getBoundingClientRect();
        var clone = img.cloneNode();
        clone.className = 'fly-img';
        clone.removeAttribute('loading');
        clone.style.cssText = 'left:' + a.left + 'px;top:' + a.top + 'px;width:' + a.width + 'px;height:' + a.height + 'px;';
        document.body.appendChild(clone);
        requestAnimationFrame(function () {
            var dx = b.left + b.width / 2 - (a.left + a.width / 2);
            var dy = b.top + b.height / 2 - (a.top + a.height / 2);
            clone.style.transform = 'translate(' + dx + 'px,' + dy + 'px) scale(.08) rotate(12deg)';
            clone.style.opacity = '.4';
        });
        setTimeout(function () { clone.remove(); }, 800);
    }

    function stepperHTML(id, qty, name) {
        return '<div class="stepper" role="group" aria-label="Quantité de ' + name + '">' +
            '<button type="button" data-ap-dec="' + id + '" aria-label="Retirer un ' + name + '">−</button>' +
            '<output aria-live="polite">' + qty + '</output>' +
            '<button type="button" data-ap-inc="' + id + '" aria-label="Ajouter un ' + name + '">+</button></div>';
    }

    function escapeAttr(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function renderControls(qtys) {
        document.querySelectorAll('[data-ap-ctrl]').forEach(function (box) {
            var id = box.getAttribute('data-ap-ctrl');
            var name = escapeAttr(box.getAttribute('data-ap-name') || 'bouquet');
            var qty = qtys[id] || 0;
            var current = box.querySelector('output');
            if (qty && current && current.textContent === String(qty)) return;
            if (!qty && box.querySelector('[data-ap-add]')) return;
            box.innerHTML = qty
                ? stepperHTML(id, qty, name)
                : '<a class="add-btn" href="?add-to-cart=' + id + '" data-ap-add="' + id + '" rel="nofollow">Ajouter</a>';
        });
    }

    function currentQty(id) {
        var out = document.querySelector('[data-ap-ctrl="' + id + '"] output') ||
            (inner && inner.querySelector('[data-ap-inc="' + id + '"]') && inner.querySelector('[data-ap-inc="' + id + '"]').parentNode.querySelector('output'));
        return out ? parseInt(out.textContent, 10) || 0 : 0;
    }

    function setQty(id, qty, opts) {
        opts = opts || {};
        if (!cfg || busy) return;
        busy = true;
        var focusSel = opts.focus;
        var inDrawer = !!(document.activeElement && drawer && drawer.contains(document.activeElement));
        document.documentElement.classList.add('cart-busy');

        var body = new FormData();
        body.append('product_id', id);
        body.append('qty', qty);
        body.append('nonce', cfg.nonce);

        fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res || !res.success) {
                    toast((res && res.data && res.data.message) || 'Une erreur est survenue, réessayez.');
                    return;
                }
                var d = res.data;
                // Panier détaillé : on recharge pour mettre à jour lignes et totaux
                if (document.body.classList.contains('woocommerce-cart')) {
                    window.location.reload();
                    return;
                }
                document.querySelectorAll('.cart-count').forEach(function (c) {
                    c.textContent = d.count;
                    c.classList.toggle('is-empty', !d.count);
                });
                if (inner) inner.innerHTML = d.drawer;
                renderControls(d.qtys || {});
                // Le bloc mini-panier de l'en-tête se met à jour lui aussi
                document.body.dispatchEvent(new CustomEvent('wc-blocks_added_to_cart', { bubbles: true, detail: { preserveCartData: false } }));
                if (opts.added) {
                    bumpCart();
                    toast(opts.added);
                }
                if (opts.open) openDrawer();
                else if (focusSel) {
                    var scope = inDrawer ? drawer : document;
                    var el = scope.querySelector(focusSel(d.qtys && d.qtys[id] ? d.qtys[id] : 0));
                    if (el) el.focus();
                }
            })
            .catch(function () { toast('Connexion perdue, réessayez.'); })
            .then(function () {
                busy = false;
                document.documentElement.classList.remove('cart-busy');
            });
    }

    function productName(id) {
        var box = document.querySelector('[data-ap-ctrl="' + id + '"]');
        if (box) return box.getAttribute('data-ap-name');
        var line = inner && inner.querySelector('[data-ap-inc="' + id + '"]');
        var n = line && line.closest('.drawer-line') && line.closest('.drawer-line').querySelector('.drawer-name');
        return n ? n.textContent : 'Bouquet';
    }

    document.addEventListener('click', function (e) {
        var t = e.target.closest('[data-ap-add],[data-ap-inc],[data-ap-dec],[data-ap-remove],[data-ap-close],[data-ap-open-cart],[data-auth-tab]');
        if (!t) return;

        if (t.hasAttribute('data-ap-open-cart')) {
            if (openDrawer()) e.preventDefault();
            return;
        }
        if (t.hasAttribute('data-ap-close')) {
            e.preventDefault();
            closeDrawer();
            return;
        }
        if (t.hasAttribute('data-auth-tab')) {
            var which = t.getAttribute('data-auth-tab');
            document.querySelectorAll('[data-auth-tab]').forEach(function (b) {
                b.setAttribute('aria-selected', String(b === t));
            });
            document.querySelectorAll('.auth-panel').forEach(function (p) {
                p.hidden = p.id !== 'panel-' + which;
            });
            return;
        }
        if (!cfg) return; // sans AJAX, les liens gardent leur comportement normal

        var id;
        if ((id = t.getAttribute('data-ap-add'))) {
            e.preventDefault();
            flyToCart(t);
            setQty(id, 1, { open: true, added: '« ' + productName(id) + ' » ajouté au panier' });
        } else if ((id = t.getAttribute('data-ap-inc'))) {
            setQty(id, currentQty(id) + 1, {
                added: '« ' + productName(id) + ' » ajouté au panier',
                focus: function () { return '[data-ap-inc="' + id + '"]'; }
            });
        } else if ((id = t.getAttribute('data-ap-dec'))) {
            setQty(id, Math.max(0, currentQty(id) - 1), {
                focus: function (q) { return q ? '[data-ap-dec="' + id + '"]' : '[data-ap-add="' + id + '"]'; }
            });
        } else if ((id = t.getAttribute('data-ap-remove'))) {
            setQty(id, 0, { focus: function () { return '.drawer-close'; } });
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.documentElement.classList.contains('drawer-open')) closeDrawer();
    });

    /* Tri de la boutique : envoi automatique */
    document.querySelectorAll('[data-ap-autosubmit]').forEach(function (sel) {
        sel.addEventListener('change', function () { sel.form.submit(); });
    });

    /* Commande : les options de livraison s'affichent en cartes dans l'étape ② */
    var shipChoice = document.getElementById('ap-shipping-choice');
    if (shipChoice && window.jQuery) {
        var syncShipping = function () {
            var list = document.querySelector('#order_review ul#shipping_method');
            var cell = document.querySelector('#order_review tr.woocommerce-shipping-totals td');
            if (list) {
                shipChoice.innerHTML = '';
                shipChoice.appendChild(list);
            } else if (cell && !shipChoice.querySelector('ul')) {
                var hint = document.createElement('p');
                hint.className = 'ap-ship-hint';
                hint.textContent = cell.textContent.trim() || 'Indiquez votre code postal pour voir les options.';
                shipChoice.innerHTML = '';
                shipChoice.appendChild(hint);
            }
            var chosen = shipChoice.querySelector('input.shipping_method:checked') ||
                shipChoice.querySelector('input.shipping_method[type="hidden"]');
            document.body.classList.toggle('ap-pickup', !!(chosen && /^local_pickup/.test(chosen.value)));
            // Dans le récapitulatif, on rappelle seulement l'option choisie
            if (cell && list) {
                var label = chosen && shipChoice.querySelector('label[for="' + chosen.id + '"]');
                cell.textContent = label ? label.textContent.trim() : '';
            }
        };
        window.jQuery(document.body).on('updated_checkout', syncShipping);
        syncShipping();
    }

    /* Commande : compteur et aperçu du message de la carte */
    var msg = document.getElementById('ap_message');
    if (msg) {
        var wrap = msg.closest('.form-row');
        var counter = document.createElement('span');
        counter.className = 'card-counter';
        counter.setAttribute('aria-live', 'polite');
        var preview = document.createElement('div');
        preview.className = 'card-preview';
        preview.setAttribute('aria-hidden', 'true');
        wrap.appendChild(counter);
        wrap.appendChild(preview);
        var update = function () {
            counter.textContent = msg.value.length + ' / 200';
            preview.textContent = msg.value || 'Votre message apparaîtra ici, écrit à la main.';
            preview.classList.toggle('is-empty', !msg.value);
        };
        msg.addEventListener('input', update);
        update();
    }

    /* ---------- Animations ---------- */

    // Apparition en cascade des cartes
    document.querySelectorAll('.shop-grid, .atelier-values, .account-shortcuts').forEach(function (grid) {
        Array.prototype.forEach.call(grid.children, function (el, i) {
            el.style.transitionDelay = (i % 4) * 90 + 'ms';
        });
    });

    var lateItems = document.querySelectorAll('.shop-page .reveal, .atelier-page .reveal, .account-shortcuts > *');
    if ('IntersectionObserver' in window && lateItems.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
        lateItems.forEach(function (el) {
            if (!el.classList.contains('reveal')) el.classList.add('reveal');
            io.observe(el);
        });
    } else {
        lateItems.forEach(function (el) { el.classList.add('is-visible'); });
    }

    if (reduceMotion) return;

    // Léger effet de profondeur sur les grandes photos au défilement
    var parallax = document.querySelectorAll('.page-hero-bg img, .atelier-hero-img img, .atelier-craft-img img');
    if (parallax.length) {
        var ticking = false;
        var onScroll = function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                parallax.forEach(function (img) {
                    var r = img.parentNode.getBoundingClientRect();
                    if (r.bottom < 0 || r.top > window.innerHeight) return;
                    var p = (r.top + r.height / 2 - window.innerHeight / 2) / window.innerHeight;
                    img.style.transform = 'translate3d(0,' + (p * -40).toFixed(1) + 'px,0) scale(1.12)';
                });
                ticking = false;
            });
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Les photos des cartes suivent doucement la souris
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        document.querySelectorAll('.shop-card-img').forEach(function (box) {
            var img = box.querySelector('img');
            if (!img) return;
            box.addEventListener('mousemove', function (e) {
                var r = box.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width - 0.5;
                var y = (e.clientY - r.top) / r.height - 0.5;
                img.style.transform = 'scale(1.08) translate(' + (x * -10).toFixed(1) + 'px,' + (y * -10).toFixed(1) + 'px)';
            });
            box.addEventListener('mouseleave', function () { img.style.transform = ''; });
        });
    }
})();


/* ==========================================================================
   Animations : titres mot par mot, pétales, barre de lecture, défilement
   ========================================================================== */
(function () {
    'use strict';

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    /* Découpe un titre en mots (en gardant <em>, <br>…) pour les faire monter un par un */
    function splitWords(el) {
        if (el.dataset.split) return;
        el.dataset.split = '1';
        var i = 0;
        (function walk(node) {
            Array.prototype.slice.call(node.childNodes).forEach(function (child) {
                if (child.nodeType === 3) {
                    var parts = child.textContent.split(/(\s+)/);
                    var frag = document.createDocumentFragment();
                    parts.forEach(function (part) {
                        if (!part) return;
                        if (/^\s+$/.test(part)) {
                            frag.appendChild(document.createTextNode(part));
                            return;
                        }
                        var outer = document.createElement('span');
                        outer.className = 'split-word';
                        var inner = document.createElement('span');
                        inner.textContent = part;
                        inner.style.setProperty('--i', i++);
                        outer.appendChild(inner);
                        frag.appendChild(outer);
                    });
                    child.parentNode.replaceChild(frag, child);
                } else if (child.nodeType === 1 && child.tagName !== 'BR') {
                    walk(child);
                }
            });
        })(el);
    }

    var titles = document.querySelectorAll(
        '.hero h1, .section-heading h2, .story-content h2, .home-message h2, ' +
        '.page-hero h1, .atelier-hero-text h1, .atelier-craft-text h2, .account-hello h2'
    );
    titles.forEach(splitWords);

    /* Éléments qui s'animent quand ils arrivent à l'écran */
    var watched = Array.prototype.slice.call(titles).concat(
        Array.prototype.slice.call(document.querySelectorAll('.rail, .home-story, .footer-main'))
    );

    function show(el) {
        if (el.matches('h1, h2')) el.classList.add('split-in');
        else el.classList.add('is-visible');
    }

    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                show(entry.target);
                io.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        watched.forEach(function (el) { io.observe(el); });
    } else {
        watched.forEach(show);
    }

    /* Filet de sécurité : rien ne doit rester caché si une animation ne s'est pas déclenchée */
    window.addEventListener('load', function () {
        setTimeout(function () {
            watched.forEach(function (el) {
                var r = el.getBoundingClientRect();
                if (r.top < window.innerHeight && r.bottom > 0) show(el);
            });
        }, 2500);
    });

    /* Pétales qui tombent doucement sur la grande photo de l'accueil */
    var hero = document.querySelector('.hero');
    if (hero) {
        var box = document.createElement('div');
        box.className = 'hero-petals';
        box.setAttribute('aria-hidden', 'true');
        var colors = ['#F3B6C1', '#D9677C', '#FBF6EF', '#F3B6C1'];
        var count = window.innerWidth < 700 ? 7 : 12;
        for (var p = 0; p < count; p++) {
            var petal = document.createElement('span');
            petal.className = 'hero-petal';
            var size = 10 + Math.random() * 14;
            var dur = 11 + Math.random() * 9;
            petal.style.setProperty('--x', (Math.random() * 100).toFixed(1) + '%');
            petal.style.setProperty('--s', size.toFixed(0) + 'px');
            petal.style.setProperty('--c', colors[p % colors.length]);
            petal.style.setProperty('--d', dur.toFixed(1) + 's');
            petal.style.setProperty('--delay', (1.5 + Math.random() * dur).toFixed(1) * -1 + 's');
            petal.style.setProperty('--sway', ((Math.random() - 0.5) * 160).toFixed(0) + 'px');
            box.appendChild(petal);
        }
        hero.appendChild(box);
    }

    /* Fine barre rose qui avance quand on descend dans la page */
    var bar = document.createElement('div');
    bar.className = 'scroll-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);
    var pending = false;
    function updateBar() {
        pending = false;
        var max = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, window.scrollY / max) : 0) + ')';
    }
    window.addEventListener('scroll', function () {
        if (!pending) { pending = true; requestAnimationFrame(updateBar); }
    }, { passive: true });
    window.addEventListener('resize', updateBar);
    updateBar();
})();
