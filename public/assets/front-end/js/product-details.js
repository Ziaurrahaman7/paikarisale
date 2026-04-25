"use strict";

$(document).ready(function () {
    const $stickyElement = $(".bottom-sticky");
    const $offsetElement = $(".product-details-shipping-details");

    $(window).on("scroll", function () {
        const elementOffset = $offsetElement?.offset()?.top;
        const scrollTop = $(window).scrollTop();

        if (scrollTop >= elementOffset) {
            $stickyElement.addClass("stick");
            $(".floating-btn-grp").removeClass("style-2");
        } else {
            $stickyElement.removeClass("stick");
            $(".floating-btn-grp").addClass("style-2");
        }
    });
});

$(document).ready(function () {
    // Constants
    const DESKTOP_BREAKPOINT = 767;
    const ANIMATION_DELAY = 150;

    // Cache selectors
    const $window = $(window);
    const $stickyTop = $('.product-details-sticky-top');
    const $stickySection = $('.product-details-sticky');

    function bindStickyHover() {
        if ($stickySection.hasClass('multi-variation-product')) {
            $stickySection.hover(
                function () {
                    $stickyTop.stop(true, true).delay(ANIMATION_DELAY).slideDown();
                },
                function () {
                    $stickyTop.stop(true, true).delay(ANIMATION_DELAY).slideUp();
                }
            );
        }
    }

    function unbindStickyHover() {
        $stickySection.off('mouseenter mouseleave');
        $stickyTop.stop(true, true).hide();
    }

    function handleBreakpoint() {
        const windowWidth = $window.width();

        if (windowWidth > DESKTOP_BREAKPOINT) {
            bindStickyHover();
        } else {
            unbindStickyHover();
        }
    }

    let resizeTimeout;
    $window.on('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleBreakpoint, 100);
    });

    handleBreakpoint();
});


// Select the element
const targetElement = document.querySelector('.product-add-and-buy-section-parent');

// Define the action to take when the element is in the viewport
function handleIntersect(entries) {
    let getHeight = $('.product-details-sticky-bottom').height();
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            $('.product-details-sticky').removeClass('active');
            $('.floating-btn-grp').removeClass('has-product-details-sticky');
            $('body').css('padding-bottom', "0px");
        } else {
            $('.product-details-sticky').addClass('active');
            $('.floating-btn-grp').addClass('has-product-details-sticky');
            $('body').css('padding-bottom', `calc(${getHeight}px + 2rem)`);
        }
    });
}

// Create an intersection observer
const observer = new IntersectionObserver(handleIntersect, {
    root: null, // Use the viewport as the root
    threshold: 0.1 // Trigger when 10% of the element is visible
});

// Start observing the target element
if (targetElement) {
    observer.observe(targetElement);
}

cartQuantityInitialize();
getVariantPrice(".add-to-cart-details-form");
getVariantPrice(".add-to-cart-sticky-form");

$(".view_more_button").on("click", function () {
    loadReviewOnDetailsPage();
});

let loadReviewCount = 1;

function loadReviewOnDetailsPage() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
        },
    });
    $.ajax({
        type: "post",
        url: $("#route-review-list-product").data("url"),
        data: {
            product_id: $("#products-details-page-data").data("id"),
            offset: loadReviewCount,
        },
        success: function (data) {
            $("#product-review-list").append(data.productReview);
            if (data.checkReviews == 0) {
                $(".view_more_button").removeClass("d-none").addClass("d-none");
            } else {
                $(".view_more_button").addClass("d-none").removeClass("d-none");
            }

            $(".show-instant-image").on("click", function () {
                let link = $(this).data("link");
                showInstantImage(link);
            });
        },
    });
    loadReviewCount++;
}

$("#chat-form").on("submit", function (e) {
    e.preventDefault();

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
        },
    });

    $.ajax({
        type: "post",
        url: $("#route-messages-store").data("url"),
        data: $("#chat-form").serialize(),
        success: function (respons) {
            toastr.success($("#message-send-successfully").data("text"), {
                CloseButton: true,
                ProgressBar: true,
            });
            $("#chat-form").trigger("reset");
        },
    });
});

function renderFocusPreviewImageByColor() {
    $(".focus-preview-image-by-color").on("click", function () {
        let id = $(this).data("colorid");
        $(`.color-variants-${id}`).click();
    });
}
renderFocusPreviewImageByColor();
// --- GTM E-commerce Tracking Configuration ---
$(document).ready(function () {
    
    function getCleanPrice(selector) {
        let element = $(selector).first();
        if (element.length) {
            let text = element.text().trim();
            let cleanNumber = text.replace(/[^0-9.]/g, '');
            let val = parseFloat(cleanNumber);
            return isNaN(val) ? 0 : val;
        }
        return 0;
    }


    const pageProductId = $("#products-details-page-data").data("id");
    if (pageProductId) {
        const productName = $("h2.__inline-24").text().trim() || "Product";
        let price = getCleanPrice(".discounted-unit-price");
        if (price === 0) price = getCleanPrice(".product-details-chosen-price-amount");

        window.dataLayer = window.dataLayer || [];
        dataLayer.push({ 'ecommerce': null }); 
        dataLayer.push({
            'event': 'view_item',
            'ecommerce': {
                'currency': 'BDT', 
                'value': price,
                'items': [{
                    'item_id': pageProductId.toString(),
                    'item_name': productName,
                    'price': price,
                    'quantity': 1
                }]
            }
        });
        console.log("GTM: view_item Success | Price: " + price);
    }

    
    
    $(document).on('click', '.action-product-quick-view, .add-to-cart, .add-to-cart-details-form button', function(e) {
        
        const clickedId = $(this).data('product-id') || $("#products-details-page-data").data("id");
        const productName = $("h2.__inline-24").text().trim() || "Product";
        
        let price = getCleanPrice(".discounted-unit-price");
        if (price === 0) price = getCleanPrice(".product-details-chosen-price-amount");
        
        const quantity = parseInt($(".product_quantity__qty").val()) || 1;

        if(clickedId) {
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({ 'ecommerce': null }); 
            dataLayer.push({
                'event': 'add_to_cart',
                'ecommerce': {
                    'currency': 'BDT',
                    'value': price * quantity,
                    'items': [{
                        'item_id': clickedId.toString(),
                        'item_name': productName,
                        'price': price,
                        'quantity': quantity
                    }]
                }
            });
            console.log("GTM: add_to_cart Success | ID: " + clickedId + " | Total: " + (price * quantity));
        }
    });
});