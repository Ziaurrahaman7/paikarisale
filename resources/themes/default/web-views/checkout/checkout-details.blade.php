@extends('layouts.front-end.app')

@section('title', translate('Checkout'))

@section('content')

@php
    $shippingMethod = getWebConfig(name: 'shipping_method');
    $adminShipping = \App\Models\ShippingType::where('seller_id', 0)->first();
    $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
    $shippings = \App\Utils\Helpers::getShippingMethods(1, 'admin');
    $cartGroupIds = \App\Utils\CartManager::get_cart_group_ids();
    $firstGroupId = count($cartGroupIds) > 0 ? $cartGroupIds[0] : null;
    $chosenShipping = $firstGroupId ? \App\Models\CartShipping::where(['cart_group_id' => $firstGroupId])->first() : null;
@endphp

<style>
.checkout-details-page {
    background: #f8f9fa;
    min-height: 80vh;
}
.checkout-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    margin-bottom: 20px;
}
.checkout-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 12px 12px 0 0 !important;
    padding: 16px 20px;
}
.checkout-card .card-header h5 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}
.checkout-card .card-body {
    padding: 20px;
}
.auth-tabs .nav-tabs {
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 20px;
}
.auth-tabs .nav-link {
    border: none;
    color: #888;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 0;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}
.auth-tabs .nav-link.active {
    color: var(--primary-clr, #f55d2c);
    border-bottom: 2px solid var(--primary-clr, #f55d2c);
    background: transparent;
}
.shipping-option {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.shipping-option:hover {
    border-color: var(--primary-clr, #f55d2c);
    background: #fff8f5;
}
.shipping-option.selected {
    border-color: var(--primary-clr, #f55d2c);
    background: #fff8f5;
}
.shipping-option .shipping-title {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}
.shipping-option .shipping-duration {
    font-size: 12px;
    color: #888;
}
.shipping-option .shipping-cost {
    font-weight: 700;
    color: var(--primary-clr, #f55d2c);
    font-size: 15px;
}
.step-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary-clr, #f55d2c);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}
.already-logged-in {
    background: linear-gradient(135deg, #f0fff4, #e6ffed);
    border: 1px solid #b7ebc8;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.already-logged-in i {
    font-size: 24px;
    color: #28a745;
}
</style>

<div class="container pb-5 mb-2 mb-md-4 rtl __inline-54 text-align-direction checkout-details-page">
    <div class="row">
        <div class="col-md-12 mb-4 pt-4">
            @include('web-views.partials._checkout-steps', ['step' => 1])
        </div>

        <section class="col-lg-8">

            {{-- Authentication Card --}}
            <div class="checkout-card card">
                <div class="card-header">
                    <h5>
                        <span class="step-badge">1</span>
                        {{ translate('authentication') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if(auth('customer')->check())
                        <div class="already-logged-in">
                            <i class="tio-checkmark-circle"></i>
                            <div>
                                <div class="font-weight-bold">{{ auth('customer')->user()->f_name }} {{ auth('customer')->user()->l_name }}</div>
                                <small class="text-muted">{{ translate('you_are_already_Sign_in_proceed') }}.</small>
                            </div>
                        </div>
                    @else
                        <div class="auth-tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#signin" data-toggle="tab" role="tab">
                                        <i class="tio-user mr-1"></i> {{ translate('sign_in') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#signup" data-toggle="tab" role="tab">
                                        <i class="tio-user-add mr-1"></i> {{ translate('sign_up') }}
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- Sign In --}}
                                <div class="tab-pane fade show active" id="signin" role="tabpanel">
                                    <form class="needs-validation" autocomplete="off" id="login-form"
                                          action="{{ route('customer.auth.login') }}" method="post" novalidate>
                                        @csrf
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('email_address') }}</label>
                                                    <input class="form-control" type="email" name="email"
                                                           value="{{ old('email') }}"
                                                           placeholder="{{ translate('enter_your_email') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('password') }}</label>
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control" name="password" type="password" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="czi-eye password-toggle-indicator"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <input type="checkbox" name="remember" id="remember_me">
                                                <label for="remember_me" class="cursor-pointer mb-0">{{ translate('remember_me') }}</label>
                                            </div>
                                            <a class="text-primary font-size-sm" href="{{ route('customer.auth.recover-password') }}">
                                                {{ translate('forgot_password') }}?
                                            </a>
                                        </div>
                                        <button class="btn btn--primary btn-block" type="submit">
                                            <i class="tio-enter mr-1"></i> {{ translate('sing_in') }}
                                        </button>
                                    </form>
                                </div>

                                {{-- Sign Up --}}
                                <div class="tab-pane fade" id="signup" role="tabpanel">
                                    <form class="needs-validation_" autocomplete="off" novalidate id="sign-up-form"
                                          action="{{ route('customer.auth.register') }}" method="post">
                                        @csrf
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('first_name') }}</label>
                                                    <input class="form-control" type="text" name="f_name"
                                                           placeholder="{{ translate('John') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('last_name') }}</label>
                                                    <input class="form-control" type="text" name="l_name"
                                                           placeholder="{{ translate('Doe') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('email_address') }}</label>
                                                    <input class="form-control" name="email" type="email"
                                                           placeholder="{{ translate('enter_your_email') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('phone') }}</label>
                                                    <input class="form-control" name="phone" type="number"
                                                           placeholder="{{ translate('01700000000') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('password') }}</label>
                                                    <div class="password-toggle">
                                                        <input class="form-control" name="password" type="password" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="czi-eye password-toggle-indicator"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('confirm_password') }}</label>
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control" name="con_password" type="password" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="czi-eye password-toggle-indicator"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn--primary btn-block" type="submit">
                                            <i class="tio-user-add mr-1"></i> {{ translate('sign_up') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

{{-- Fixed Delivery Charge --}}
@php($fixedDeliveryCharge = 250)
            <div class="checkout-card card">
                <div class="card-header">
                    <h5>
                        <span class="step-badge">2</span>
                        {{ translate('delivery_info') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <i class="tio-delivery-truck text-primary mr-2 fs-20"></i>
                            <div>
                                <div class="font-weight-bold">{{ translate('home_delivery') }}</div>
                                <small class="text-muted">{{ translate('standard_delivery_time') }}</small>
                            </div>
                        </div>
                        <div class="font-weight-bold text-primary fs-18">
                            {{ webCurrencyConverter(amount: $fixedDeliveryCharge) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Method Section --}}
            <div class="checkout-card card">
                <div class="card-header">
                    <h5>
                        <span class="step-badge">3</span>
                        {{ translate('payment_method') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="payment-method-options">
                        <div class="form-check shipping-option selected" onclick="selectPaymentMethod('cash_on_delivery')">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <input type="radio" name="payment_method" id="cod" value="cash_on_delivery" checked>
                                <label for="cod" class="d-flex align-items-center gap-2 w-100 cursor-pointer">
                                    <i class="tio-money text-success fs-20"></i>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold">{{ translate('cash_on_delivery') }}</div>
                                        <small class="text-muted">{{ translate('pay_with_cash_at_delivery') }}</small>
                                    </div>
                                    <i class="tio-checkmark-circle text-success fs-20 selected-check"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="row mt-2">
                <div class="col-6">
                    <a class="btn btn-outline-secondary btn-block" href="{{ route('shop-cart') }}">
                        <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }} mr-1"></i>
                        <span class="d-none d-sm-inline">{{ translate('back_to_cart') }}</span>
                        <span class="d-inline d-sm-none">{{ translate('back') }}</span>
                    </a>
                </div>
<div class="col-6">
                    @if(auth('customer')->check())
                        <button type="button" class="btn btn--primary btn-block" id="proceed_to_checkout_btn">
                            <span class="d-none d-sm-inline">{{ translate('proceed_to_Checkout') }}</span>
                            <span class="d-inline d-sm-none">{{ translate('next') }}</span>
                            <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} ml-1"></i>
                        </button>
                    @endif
                </div>
            </div>

        </section>

        @include('web-views.partials._order-summary')
    </div>
</div>

<span id="route-action-checkout-function" data-route="checkout-details"></span>

@endsection

@push('script')
<script>
$(document).ready(function () {
    const $headCheck = $(".shop-head-check");
    if (!$headCheck.prop("checked")) {
        $headCheck.click();
    }
});

function selectShippingMethod(shippingId, el) {
    $('.shipping-option').removeClass('selected');
    $(el).addClass('selected');
    $('#selected_shipping_method_id').val(shippingId);

    const url = $('#route-customer-set-shipping-method').data('url');
    $.post({
        url: url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { shipping_method_id: shippingId },
        success: function(data) {
            if (data.status == 1) {
                // Update the order summary without reloading the page
                $.get(window.location.href, function(data) {
                    // Extract the order summary section from the response
                    var newOrderSummary = $(data).find('#cart-summary').html();
                    
                    // Update the order summary in the current page
                    $('#cart-summary').html(newOrderSummary);
                    
                    // Also update the navbar cart if it exists
                    if (typeof updateNavCart === "function") {
                        updateNavCart();
                    }
                });
            }
        }
    });
}

$('#login-form').submit(function (e) {
    e.preventDefault();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.post({
        url: '{{ route('customer.auth.login') }}',
        dataType: 'json',
        data: $('#login-form').serialize(),
        beforeSend: function () { $('#loading').show(); },
        success: function (data) {
            toastr.success(data.message, { CloseButton: true, ProgressBar: true });
            location.reload();
        },
        complete: function () { $('#loading').hide(); },
        error: function () {
            toastr.error('{{ translate("credential_not_matched") }}!', { CloseButton: true, ProgressBar: true });
        }
    });
});

$('#sign-up-form').submit(function (e) {
    e.preventDefault();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.post({
        url: '{{ route('customer.auth.register') }}',
        dataType: 'json',
        data: $('#sign-up-form').serialize(),
        beforeSend: function () { $('#loading').show(); },
        success: function (data) {
            if (data.errors) {
                for (var i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, { CloseButton: true, ProgressBar: true });
                }
            } else {
                toastr.success(data.message, { CloseButton: true, ProgressBar: true });
                setInterval(function () { location.href = data.url; }, 2000);
            }
        },
        complete: function () { $('#loading').hide(); },
        error: function () {
            toastr.error('{{ translate("something_went_wrong") }}!', { CloseButton: true, ProgressBar: true });
        }
    });
});

// Proceed to Checkout - Go to Step 2 (Shipping Form)
$('#proceed_to_checkout_btn').click(function (e) {
    e.preventDefault();
    
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ translate("processing") }}...');
    
    // Get selected payment method
    var paymentMethod = $('input[name="payment_method"]:checked').val();
    
    // Go to Step 2 (Shipping Form) with Cash on Delivery pre-selected
    window.location.href = '{{ route("checkout-shipping") }}?payment_method=' + paymentMethod;
});

// Payment Method Selection
function selectPaymentMethod(method) {
    $('.payment-method-options .shipping-option').removeClass('selected');
    $('.payment-method-options input[value="' + method + '"]').closest('.shipping-option').addClass('selected');
    $('.payment-method-options input[value="' + method + '"]').prop('checked', true);
}
</script>
@endpush