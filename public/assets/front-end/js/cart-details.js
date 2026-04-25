"use strict";

(function ($) {

    window.dataLayer = window.dataLayer || [];

    const trackControl = {
        viewCart: false,
        beginCheckout: false
    };

    const hideCartCheckboxes = () => {
        $('.shop-item-check, .shop-head-check').css({
            'position': 'absolute',
            'opacity': 0,
            'width': '0px',
            'height': '0px',
            'pointer-events': 'none'
        });
    };

    const quantityListener = () => {
        $('.qty_input').each(function () {
            const $qty = $(this);
            const minOrder = $qty.data('minimum-order') ?? 1;
            const currentVal = parseInt($qty.val());

            if (currentVal <= minOrder) {
                $qty.siblings('.qty_minus').html('<i class="tio-delete text-danger fs-12"></i>');
            } else {
                $qty.siblings('.qty_minus').html('<i class="tio-remove"></i>');
            }
        });
    };

    const getEcommerceItems = () => {
        let items = [];
        $('.qty_input').each(function () {
            const $row = $(this).closest('tr');
            if ($row.length === 0) return; // Row না থাকলে স্কিপ

            const itemName = $.trim($row.find('a').last().text()) || 'Product';
            const itemPrice = parseFloat(($row.find('.__w-15p div').text() || "0").replace(/[^0-9.]/g, ''));

            items.push({
                'item_id': $(this).data('cart-id'),
                'item_name': itemName,
                'price': itemPrice,
                'quantity': parseInt($(this).val()),
                'currency': 'BDT'
            });
        });
        return items;
    };

 
    window.removeProductFromCartList = function (key) {
        $.post($('#route-cart-remove').data('url'), {
            _token: $('meta[name="_token"]').attr('content'),
            key: key
        }, function (response) {

            window.dataLayer.push({ ecommerce: null }); 
            window.dataLayer.push({
                'event': 'remove_from_cart',
                'ecommerce': {
                    'currency': 'BDT',
                    'items': [{ 'item_id': key }]
                }
            });

            if (typeof updateNavCart === "function") updateNavCart();
            toastr.info($('#message-item-has-been-removed-from-cart').data('text'));

            if (window.location.pathname.includes('checkout')) {
                location.reload();
            }

            $('#cart-summary').empty().html(response.data);
            quantityListener();
            hideCartCheckboxes();
        });
    };

    window.updateCartCommon = function (minimum_order_qty, key, incr, e, quantity_id) {
        let exQuantity = $("#" + quantity_id + key);
        let quantity = parseInt(exQuantity.val()) + parseInt(incr);

        if (minimum_order_qty > quantity && e !== 'delete') {
            toastr.error($('#message-minimum-order-quantity-cannot-less-than').data('text') + minimum_order_qty);
            return false;
        }

        if (exQuantity.val() == exQuantity.data('min') && e === 'delete') {
            window.removeProductFromCartList(key);
        } else {
            $.post($('#route-cart-updateQuantity').data('url'), {
                _token: $('meta[name="_token"]').attr('content'),
                key: key,
                quantity: quantity
            }, function (response) {
                if (typeof updateNavCart === "function") updateNavCart();
                $('#cart-summary').empty().html(response);
                hideCartCheckboxes();
                quantityListener();
            });
        }
    };

    window.updateCartQuantityList = (mq, k, i, e) => window.updateCartCommon(mq, k, i, e, 'cart_quantity_web');
    window.updateCartQuantityListMobile = (mq, k, i, e) => window.updateCartCommon(mq, k, i, e, 'cart_quantity_mobile');

    window.setShippingIdCartDetails = function (id, cart_group_id) {
        $.get({
            url: $('#route-set-shipping-id').data('url'),
            dataType: 'json',
            data: { id: id, cart_group_id: cart_group_id },
            beforeSend: function () { $('#loading').addClass('d-grid'); },
            success: function () { location.reload(); }
        });
    };


    $(document).ready(function () {
        quantityListener();
        hideCartCheckboxes();

     
        if (!trackControl.viewCart) {
            const cartTotal = parseFloat(($('.cart_value').last().text() || "0").replace(/[^0-9.]/g, ''));
            const items = getEcommerceItems();
            
            if (items.length > 0) {
                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    'event': 'view_cart',
                    'ecommerce': {
                        'currency': 'BDT',
                        'value': cartTotal,
                        'items': items
                    }
                });
                trackControl.viewCart = true;
            }
        }

     
        $(document).off('click', '.action-checkout-function').on('click', '.action-checkout-function', function () {
            if (trackControl.beginCheckout) return;

            const currentTotal = parseFloat(($('.cart_value').last().text() || "0").replace(/[^0-9.]/g, ''));
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                'event': 'begin_checkout',
                'ecommerce': {
                    'currency': 'BDT',
                    'value': currentTotal,
                    'items': getEcommerceItems()
                }
            });
            trackControl.beginCheckout = true;
        });


        $(document).off('click', '.setShippingIdFunctionCartDetails').on('click', '.setShippingIdFunctionCartDetails', function () {
            window.setShippingIdCartDetails($(this).data('id'), $(this).data('cart-group'));
        });

        $(document).off('change', '.set_shipping_onchange').on('change', '.set_shipping_onchange', function () {
            window.setShippingIdCartDetails($(this).val(), 'all_cart_group');
        });
    });

    $(document).ajaxComplete(function () {
        hideCartCheckboxes();
    });

})(jQuery);