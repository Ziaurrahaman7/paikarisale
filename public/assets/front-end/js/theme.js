window.dataLayer = window.dataLayer || [];
"use strict";

function _typeof(e) {
    return (_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) { return typeof e; } : function (e) { return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e; })(e);
}

function ownKeys(t, e) {
    var r = Object.keys(t);
    if (Object.getOwnPropertySymbols) {
        var n = Object.getOwnPropertySymbols(t);
        e && (n = n.filter(function (e) { return Object.getOwnPropertyDescriptor(t, e).enumerable; })), r.push.apply(r, n);
    }
    return r;
}

function _objectSpread(t) {
    for (var e = 1; e < arguments.length; e++) {
        var r = null != arguments[e] ? arguments[e] : {};
        e % 2 ? ownKeys(Object(r), !0).forEach(function (e) { _defineProperty(t, e, r[e]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(r)) : ownKeys(Object(r)).forEach(function (e) { Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(r, e)); });
    }
    return t;
}

function _defineProperty(e, t, r) {
    return t in e ? Object.defineProperty(e, t, { value: r, enumerable: !0, configurable: !0, writable: !0 }) : (e[t] = r), e;
}

!(function (t) {
    var e = {
        init: function () {
            e.masonryGrid(),
            e.stickyNavbar(),
            e.stuckNavbarMenuToggle(),
            e.passwordVisibilityToggle(),
            e.customFileInput(),
            e.fileDropArea(),
            e.formValidation(),
            e.multilevelDropdown(),
            e.smoothScroll(),
            e.scrollTopButton(),
            e.offcanvasSidebar(),
            e.tooltips(),
            e.popovers(),
            e.toasts(),
            e.disableDropdownAutohide(),
            e.carousel(),
            e.gallery(),
            e.productGallery(),
            e.imageZoom(),
            e.videoPopupBtn(),
            e.ajaxifySubscribeForm(),
            e.rangeSlider(),
            e.filterList(),
            e.dataFilter(),
            e.labelUpdate(),
            e.radioTabs(),
            e.countdown(),
            e.creditCard(),
            e.charts();
        },

        masonryGrid: function () {
            var e, t = document.querySelectorAll(".cz-masonry-grid");
            if (null !== t)
                for (var r = 0; r < t.length; r++)
                    (e = new Shuffle(t[r], { itemSelector: ".grid-item", sizer: ".grid-item" })),
                    imagesLoaded(t[r]).on("progress", function () { e.layout(); });
        },

        stickyNavbar: function () {
            var t = document.querySelector(".navbar-sticky");
            if (null != t) {
                var e = t.classList,
                    r = t.offsetHeight;
                if (e.contains("navbar-floating") && e.contains("navbar-dark")) {
                    window.addEventListener("scroll", function (event) {
                        if (event.currentTarget.pageYOffset > 500) {
                            t.classList.remove("navbar-dark");
                            t.classList.add("navbar-light", "navbar-stuck");
                        } else {
                            t.classList.remove("navbar-light", "navbar-stuck");
                            t.classList.add("navbar-dark");
                        }
                    });
                } else if (e.contains("navbar-floating") && e.contains("navbar-light")) {
                    window.addEventListener("scroll", function (event) {
                        event.currentTarget.pageYOffset > 500 ? t.classList.add("navbar-stuck") : t.classList.remove("navbar-stuck");
                    });
                } else {
                    window.addEventListener("scroll", function (event) {
                        if (event.currentTarget.pageYOffset > 200) {
                            document.body.style.paddingTop = r + "px";
                            t.classList.add("navbar-stuck");
                        } else {
                            document.body.style.paddingTop = "";
                            t.classList.remove("navbar-stuck");
                        }
                    });
                }
            }
        },

        stuckNavbarMenuToggle: function () {
            var btn = document.querySelector(".navbar-stuck-toggler"),
                menu = document.querySelector(".navbar-stuck-menu");
            if (btn && menu) {
                btn.addEventListener("click", function (event) {
                    menu.classList.toggle("show");
                    event.preventDefault();
                    this.classList.toggle("show");
                });
            }
        },

        passwordVisibilityToggle: function () {
            var r = document.querySelectorAll(".password-toggle");
            for (var t = 0; t < r.length; t++) {
                (function (index) {
                    var input = r[index].querySelector(".form-control");
                    var btn = r[index].querySelector(".password-toggle-btn");
                    if (btn && input) {
                        btn.addEventListener("click", function (e) {
                            if (e.target.type === "checkbox") {
                                input.type = e.target.checked ? "text" : "password";
                            }
                        }, false);
                    }
                })(t);
            }
        },

        customFileInput: function () { bsCustomFileInput.init(); },
        
        fileDropArea: function () {
            var t = document.querySelectorAll(".cz-file-drop-area");
            for (var r = 0; r < t.length; r++) {
                (function (idx) {
                    var o = t[idx].querySelector(".cz-file-drop-input"),
                        a = t[idx].querySelector(".cz-file-drop-message"),
                        i = t[idx].querySelector(".cz-file-drop-icon");
                    t[idx].querySelector(".cz-file-drop-btn").addEventListener("click", function () { o.click(); });
                    o.addEventListener("change", function () {
                        if (o.files && o.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var res = e.target.result, name = o.files[0].name;
                                a.innerHTML = name;
                                if (res.startsWith("data:image")) {
                                    var img = new Image(); img.src = res;
                                    img.onload = function () {
                                        i.className = "cz-file-drop-preview img-thumbnail rounded";
                                        i.innerHTML = '<img src="' + img.src + '" alt="' + name + '">';
                                    };
                                } else {
                                    i.innerHTML = ""; i.className = "cz-file-drop-icon czi-document";
                                }
                            };
                            reader.readAsDataURL(o.files[0]);
                        }
                    });
                })(r);
            }
        },

        formValidation: function () {
            window.addEventListener("load", function () {
                var e = document.getElementsByClassName("needs-validation");
                Array.prototype.filter.call(e, function (t) {
                    t.addEventListener("submit", function (e) {
                        if (t.checkValidity() === false) { e.preventDefault(); e.stopPropagation(); }
                        t.classList.add("was-validated");
                    }, false);
                });
            }, false);
        },

        multilevelDropdown: function () {
            t(".dropdown-menu [data-toggle='dropdown']").on("click", function (e) {
                e.preventDefault(); e.stopPropagation();
                t(this).siblings().toggleClass("show");
                if (!t(this).next().hasClass("show")) t(this).parents(".dropdown-menu").first().find(".show").removeClass("show");
                t(this).parents("li.nav-item.dropdown.show").on("hidden.bs.dropdown", function () {
                    t(".dropdown-submenu .show").removeClass("show");
                });
            });
        },

        smoothScroll: function () {
            new SmoothScroll("[data-scroll]", { speed: 800, speedAsDuration: true, offset: 40, header: "[data-scroll-header]", updateURL: false });
        },

        scrollTopButton: function () {
            var btn = document.querySelector(".btn-scroll-top");
            if (null != btn) {
                window.addEventListener("scroll", function (e) {
                    e.currentTarget.pageYOffset > 600 ? btn.classList.add("show") : btn.classList.remove("show");
                });
            }
        },

        offcanvasSidebar: function () {
            var openers = document.querySelectorAll('[data-toggle="sidebar"]'),
                closers = document.querySelectorAll('[data-dismiss="sidebar"]'),
                body = document.querySelector("body");
            for (var n = 0; n < openers.length; n++) {
                openers[n].addEventListener("click", function (e) {
                    e.preventDefault();
                    var target = e.currentTarget.getAttribute("href");
                    document.querySelector(target).classList.add("show");
                    body.classList.add("offcanvas-open");
                });
            }
            for (var o = 0; o < closers.length; o++) {
                closers[o].addEventListener("click", function (e) {
                    e.currentTarget.closest(".cz-sidebar").classList.remove("show");
                    body.classList.remove("offcanvas-open");
                });
            }
        },

        tooltips: function () { t('[data-toggle="tooltip"]').tooltip(); },
        popovers: function () { t('[data-toggle="popover"]').popover(); },
        toasts: function () {
            t('[data-toggle="toast"]').on("click", function () {
                var target = t(this).data("target");
                t(target).toast("show");
            });
        },

        disableDropdownAutohide: function () {
            var selects = document.querySelectorAll(".disable-autohide .custom-select");
            for (var i = 0; i < selects.length; i++) {
                selects[i].addEventListener("click", function (e) { e.stopPropagation(); });
            }
        },

        carousel: function () {
            var carousels = document.querySelectorAll(".cz-carousel .cz-carousel-inner");
            for (var i = 0; i < carousels.length; i++) {
                var opts, container = carousels[i];
                var defaults = {
                    container: container,
                    controlsText: ['<i class="czi-arrow-left"></i>', '<i class="czi-arrow-right"></i>'],
                    navPosition: "bottom",
                    mouseDrag: true,
                    speed: 500,
                    autoplayHoverPause: true,
                    autoplayButtonOutput: false
                };
                if (null != container.dataset.carouselOptions) opts = JSON.parse(container.dataset.carouselOptions);
                tns(_objectSpread(_objectSpread({}, defaults), opts));
            }
        },

        gallery: function () {
            var galleries = document.querySelectorAll(".cz-gallery");
            if (galleries.length) {
                for (var i = 0; i < galleries.length; i++) {
                    lightGallery(galleries[i], { selector: ".gallery-item", download: false, videojs: true });
                }
            }
        },

        productGallery: function () {
            var galleries = document.querySelectorAll(".cz-product-gallery");
            if (galleries.length) {
                for (var i = 0; i < galleries.length; i++) {
                    (function (idx) {
                        var thumbs = galleries[idx].querySelectorAll(".cz-thumblist-item:not(.video-item)"),
                            previews = galleries[idx].querySelectorAll(".cz-preview-item");
                        for (var j = 0; j < thumbs.length; j++) {
                            thumbs[j].addEventListener("click", function (e) {
                                e.preventDefault();
                                for (var k = 0; k < thumbs.length; k++) {
                                    previews[k].classList.remove("active");
                                    thumbs[k].classList.remove("active");
                                }
                                this.classList.add("active");
                                galleries[idx].querySelector(this.getAttribute("href")).classList.add("active");
                            });
                        }
                    })(i);
                }
            }
        },

        imageZoom: function () {
            var elements = document.querySelectorAll(".cz-image-zoom");
            for (var i = 0; i < elements.length; i++) {
                new Drift(elements[i], { paneContainer: elements[i].parentElement.querySelector(".cz-image-zoom-pane") });
            }
        },

        videoPopupBtn: function () {
            var btns = document.querySelectorAll(".video-popup-btn");
            for (var i = 0; i < btns.length; i++) {
                lightGallery(btns[i], { selector: "this", download: false, videojs: true });
            }
        },

        ajaxifySubscribeForm: function () {
            var forms = document.querySelectorAll(".cz-subscribe-form");
            if (forms !== null) {
                for (var i = 0; i < forms.length; i++) {
                    (function (idx) {
                        var btn = forms[idx].querySelector('button[type="submit"]'),
                            btnText = btn.innerHTML,
                            input = forms[idx].querySelector(".form-control"),
                            antispam = forms[idx].querySelector(".cz-subscribe-form-antispam"),
                            status = forms[idx].querySelector(".subscribe-status");
                        forms[idx].addEventListener("submit", function (e) {
                            if (e) e.preventDefault();
                            if (antispam.value === "") {
                                btn.innerHTML = "Sending...";
                                // Logic for subscription... (Simplified)
                                btn.innerHTML = btnText;
                            }
                        });
                    })(i);
                }
            }
        },

        rangeSlider: function () {
            var sliders = document.querySelectorAll(".cz-range-slider");
            for (var i = 0; i < sliders.length; i++) {
                (function (idx) {
                    var ui = sliders[idx].querySelector(".cz-range-slider-ui"),
                        min = sliders[idx].querySelector(".cz-range-slider-value-min"),
                        max = sliders[idx].querySelector(".cz-range-slider-value-max"),
                        data = {
                            startMin: parseInt(sliders[idx].dataset.startMin, 10),
                            startMax: parseInt(sliders[idx].dataset.startMax, 10),
                            min: parseInt(sliders[idx].dataset.min, 10),
                            max: parseInt(sliders[idx].dataset.max, 10),
                            step: parseInt(sliders[idx].dataset.step, 10)
                        };
                    noUiSlider.create(ui, {
                        start: [data.startMin, data.startMax],
                        connect: true,
                        step: data.step,
                        range: { min: data.min, max: data.max },
                        format: { to: function (v) { return "$" + parseInt(v, 10); }, from: function (v) { return Number(v); } }
                    });
                    ui.noUiSlider.on("update", function (v, t) {
                        var val = v[t].replace(/\D/g, "");
                        t ? (max.value = Math.round(val)) : (min.value = Math.round(val));
                    });
                })(i);
            }
        },

        filterList: function () {
            var filters = document.querySelectorAll(".cz-filter");
            for (var i = 0; i < filters.length; i++) {
                var search = filters[i].querySelector(".cz-filter-search"),
                    items = filters[i].querySelector(".cz-filter-list").querySelectorAll(".cz-filter-item");
                if (search) {
                    search.addEventListener("keyup", function () {
                        var val = search.value.toLowerCase();
                        for (var j = 0; j < items.length; j++) {
                            var text = items[j].querySelector(".cz-filter-item-text").innerHTML.toLowerCase();
                            text.indexOf(val) > -1 ? items[j].classList.remove("d-none") : items[j].classList.add("d-none");
                        }
                    });
                }
            }
        },

        dataFilter: function () {
            var trigger = document.querySelector('[data-filter="trigger"]'),
                targets = document.querySelectorAll('[data-filter="target"]');
            if (trigger !== null) {
                trigger.addEventListener("change", function () {
                    var val = this.value.toLowerCase();
                    if (val === "all") {
                        for (var i = 0; i < targets.length; i++) targets[i].classList.remove("d-none");
                    } else {
                        for (var j = 0; j < targets.length; j++) targets[j].classList.add("d-none");
                        document.querySelector("#" + val).classList.remove("d-none");
                    }
                });
            }
        },

        labelUpdate: function () {
            var elements = document.querySelectorAll("[data-label]");
            for (var i = 0; i < elements.length; i++) {
                elements[i].addEventListener("change", function () {
                    var targetId = this.dataset.label;
                    try { document.getElementById(targetId).textContent = this.value; } catch (err) { console.error("Target ID not found!"); }
                });
            }
        },

        radioTabs: function () {
            var tabs = document.querySelectorAll('[data-toggle="radioTab"]');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].addEventListener("click", function () {
                    var target = this.dataset.target;
                    document.querySelector(this.dataset.parent).querySelectorAll(".radio-tab-pane").forEach(function (el) {
                        el.classList.remove("active");
                    });
                    document.querySelector(target).classList.add("active");
                });
            }
        },

        countdown: function () {
            var counters = document.querySelectorAll(".cz-countdown");
            if (counters !== null) {
                for (var i = 0; i < counters.length; i++) {
                    (function (idx) {
                        var date = new Date(counters[idx].dataset.countdown).getTime();
                        if (isNaN(date)) return;
                        setInterval(function () {
                            var now = new Date().getTime(), t = parseInt((date - now) / 1000);
                            if (t >= 0) {
                                var d = parseInt(t / 86400); t %= 86400;
                                var h = parseInt(t / 3600); t %= 3600;
                                var m = parseInt(t / 60); var s = parseInt(t % 60);
                                var dEl = counters[idx].querySelector(".cz-countdown-days .cz-countdown-value"),
                                    hEl = counters[idx].querySelector(".cz-countdown-hours .cz-countdown-value"),
                                    mEl = counters[idx].querySelector(".cz-countdown-minutes .cz-countdown-value"),
                                    sEl = counters[idx].querySelector(".cz-countdown-seconds .cz-countdown-value");
                                if (dEl) dEl.innerHTML = d;
                                if (hEl) hEl.innerHTML = h < 10 ? "0" + h : h;
                                if (mEl) mEl.innerHTML = m < 10 ? "0" + m : m;
                                if (sEl) sEl.innerHTML = s < 10 ? "0" + s : s;
                            }
                        }, 1000);
                    })(i);
                }
            }
        },

        creditCard: function () {
            var el = document.querySelector(".interactive-credit-card");
            if (el !== null) new Card({ form: el, container: ".card-wrapper" });
        },

        charts: function () {
            // Logic for charts (Chartist initialization)...
        }
    };

    e.pushToDataLayer = function (name, data) {
        window.dataLayer.push({ 'event': name, 'data': data });
    };

    e.pushToDataLayer('site_initialized', { 'status': 'success' });
    e.init();

    // OTP Counter
    function startCountdown() {
        var el = $(".verifyCounter");
        if (el.length) {
            var sec = el.data("second");
            function tick() {
                var m = Math.floor(sec / 60), s = sec % 60;
                sec--;
                el.html(m + ":" + (s < 10 ? "0" : "") + s);
                if (sec > 0) {
                    setTimeout(tick, 1000);
                    $(".resend-otp-button").attr("disabled", true);
                } else {
                    $(".resend-otp-button").removeAttr("disabled");
                    el.html("0:00");
                }
            }
            tick();
        }
    }
    startCountdown();
    console.log("Data Layer has been enabled successfully!");
})(jQuery);