"use strict";

$(document).ready(function () {
    window.dataLayer = window.dataLayer || [];


    function getEcommerceData() {
        let items = [];
        $("table tbody tr").each(function () {
            let $row = $(this);
            let name = $row.find('a').first().text().trim();
            let price = parseFloat($row.find('td.__w-15p.text-end div').text().replace(/[^0-9.]/g, "")) || 0;
            let qty = parseInt($row.find('.qty_input').val()) || 1;
            if (name && price > 0) {
                items.push({ 'item_name': name, 'price': price, 'quantity': qty, 'currency': 'BDT' });
            }
        });
        let total = $(".cart_total .cart_value").last().text().replace(/[^0-9.]/g, "") || "0";
        return { items: items, total: total };
    }


    function updateButtonState() {
        let isAgreed = $('.payment-input-checkbox:checked').length > 0;
        let isMethodSelected = $('input[name="payment_method"]:checked').length > 0 || 
                               $('input[type="radio"]:checked').length > 0;

        if (isAgreed && isMethodSelected) {
         
            $(".proceed_to_next_button").removeClass("disabled").prop("disabled", false).css("opacity", "1");
            console.log("✅ Button Enabled");
        } else {
            // বাটন লক রাখা
            $(".proceed_to_next_button").addClass("disabled").prop("disabled", true).css("opacity", "0.6");
        }
    }


    if (window.location.href.includes('checkout-payment')) {
    
        $(".proceed_to_next_button").addClass("disabled").prop("disabled", true);

     
        $(document).on('change', 'input[type="radio"], .payment-input-checkbox', function() {
            updateButtonState();
        });

     
        $(document).on('click', '.proceed_to_next_button', function (e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                return false;
            }

            let data = getEcommerceData();
            sessionStorage.setItem("ga4_purchase_items", JSON.stringify(data.items));
            sessionStorage.setItem("ga4_purchase_total", data.total);

            window.dataLayer.push({
                'event': 'begin_checkout',
                'ecommerce': { 'value': parseFloat(data.total), 'currency': 'BDT', 'items': data.items }
            });

            let checked_radio = $('input[type="radio"]:checked');
            $("#" + checked_radio.attr("id") + "_form").submit();
        });
    }

  
if (window.location.href.includes('checkout-complete')) {

    let storedItems = sessionStorage.getItem("ga4_purchase_items");
    let storedTotal = sessionStorage.getItem("ga4_purchase_total");

    // 🔒 Duplicate event prevent
    if (sessionStorage.getItem("purchase_sent")) {
        return;
    }

    if (storedItems && storedTotal) {

        let orderID = $(".order-id").first().text().trim();

        // fallback যদি না পাওয়া যায়
        if (!orderID) {
            orderID = 'ORD-' + new Date().getTime();
        }

        // GA4 clear previous ecommerce object
        window.dataLayer.push({ ecommerce: null });

        // ✅ Purchase Event
        window.dataLayer.push({
            event: 'purchase',
            ecommerce: {
                transaction_id: orderID,
                value: parseFloat(storedTotal),
                currency: 'BDT',
                items: JSON.parse(storedItems)
            }
        });

        console.log("🚀 Purchase Event Sent!");

        // mark as sent
        sessionStorage.setItem("purchase_sent", "yes");

        // clean storage
        sessionStorage.removeItem("ga4_purchase_items");
        sessionStorage.removeItem("ga4_purchase_total");
    }
}
});