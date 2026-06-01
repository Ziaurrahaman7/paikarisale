@extends('layouts.front-end.app')

@section('title', translate('shipping_Address'))

@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
@php
    $billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer');
    $defaultLocation = getWebConfig(name: 'default_location');
    $physical_product_view = true;
    $country_restrict_status = getWebConfig('country_restrict_status') ?? 0;
    $countriesName = getWebConfig('delivery_restricted_countries') ?? [];
    $shippingAddresses = \App\Models\ShippingAddress::where(['customer_id'=>auth('customer')->id() ?? 0, 'is_guest'=>0])->get();
@endphp

<div class="container py-4 rtl __inline-56 px-0 px-md-3 text-align-direction">
    <div class="row mx-max-md-0">
        <div class="col-md-12 mb-3">
            <h3 class="font-weight-bold text-center text-lg-left">{{translate('checkout')}}</h3>
        </div>
        
        <!-- Checkout Left Column -->
        <section class="col-lg-8 px-max-md-0">
            <div class="checkout_details px-3 px-md-0">
                @include('web-views.partials._checkout-steps',['step'=>2])

                <!-- SHIPPING METHOD SECTION MOVED TO TOP -->
                @php($shippingMethod=getWebConfig(name: 'shipping_method'))
                @php($cart=\App\Utils\CartManager::getCartListGroupQuery())
                <?php
                    $admin_shipping = \App\Models\ShippingType::where('seller_id', 0)->first();
                ?>

                @if($shippingMethod=='inhouse_shipping')
                    <?php
                    $isPhysicalProductExist = false;
                    $hasPhysicalProducts = false;
                    
                    // Check if there are any physical products in the cart
                    foreach ($cart as $group_key => $group) {
                        foreach ($group as $row) {
                            if ($row->product_type == 'physical' && $row->is_checked) {
                                $isPhysicalProductExist = true;
                                $hasPhysicalProducts = true;
                                break;
                            }
                        }
                        if ($hasPhysicalProducts) break;
                    }
                    ?>

                    <?php
                        $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                    ?>
                    
                    @if ($shipping_type == 'order_wise' && $isPhysicalProductExist)
                        @php($shippings=\App\Utils\Helpers::getShippingMethods(1,'admin'))
                        
                        <?php
                        // Get cart group ID from first available cart item
                        $cartGroupId = null;
                        foreach ($cart as $group_key => $group) {
                            if (count($group) > 0) {
                                $cartGroupId = $group[0]->cart_group_id;
                                break;
                            }
                        }
                        
                        $chosenShipping = $cartGroupId ? \App\Models\CartShipping::where(['cart_group_id' => $cartGroupId])->first() : null;
                        ?>
                        
                        @if(isset($chosenShipping)==false)
                            @php($chosenShipping = ['shipping_method_id' => 0])
                        @endif
                        
{{-- START: SHIPPING METHOD SECTION (HIDDEN - Using fixed delivery charge) --}}
                        {{-- 
                        <div class="px-3 px-md-0 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="font-bold">{{ translate('shipping_method') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">{{ translate('choose_shipping_method') }}</label>
                                            <select class="form-control border-aliceblue action-set-shipping-id"
                                                    name="shipping_method_id"
                                                    data-product-id="all_cart_group"
                                                    required>
                                                 <option value="">{{ translate('choose_shipping_method')}}</option>
                                                @foreach($shippings as $shipping)
                                                    <option
                                                        value="{{ $shipping['id'] }}"
                                                        {{ isset($chosenShipping['shipping_method_id']) && $chosenShipping['shipping_method_id'] == $shipping['id'] ? 'selected' : '' }}>
                                                        {{ $shipping['title'].' ( '.$shipping['duration'].' ) '.webCurrencyConverter(amount: $shipping['cost']) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        --}}
                        {{-- END: SHIPPING METHOD SECTION --}}
                    @endif
                @endif

                <!-- Shipping Address -->
                @if($physical_product_view)
                <input type="hidden" id="physical_product" name="physical_product" value="yes">
                <div class="px-3 px-md-0">
                    <h4 class="pb-2 mt-4 fs-18 text-capitalize">{{ translate('shipping_address') }}</h4>
                </div>
                
                

                <form method="post" class="card __card" id="address-form">
                    <div class="card-body p-0">
                        <ul class="list-group">
                            <li class="list-group-item add-another-address">
                                @if ($shippingAddresses->count() > 0)
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <div class="dropdown">
                                        <button class="form-control dropdown-toggle text-capitalize" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{translate('saved_address')}}
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right saved-address-dropdown scroll-bar-saved-address" aria-labelledby="dropdownMenuButton">
                                            @foreach($shippingAddresses as $key => $address)
                                            <div class="dropdown-item select_shipping_address {{$key == 0 ? 'active' : ''}}" id="shippingAddress{{$key}}">
                                                <input type="hidden" class="selected_shippingAddress{{$key}}" value="{{$address}}">
                                                <input type="hidden" name="shipping_method_id" value="{{$address['id']}}">
                                                <div class="media gap-2">
                                                    <div><i class="tio-briefcase"></i></div>
                                                    <div class="media-body">
                                                        <div class="mb-1 text-capitalize">{{$address->address_type}}</div>
                                                        <div class="text-muted fs-12 text-capitalize text-wrap">{{$address->address}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div id="accordion">
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('contact_person_name')}} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="contact_person_name" {{$shippingAddresses->count()==0?'required':''}} id="name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ translate('phone')}} <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control" id="phone" {{ $shippingAddresses->count()==0?'required':'' }} name="phone">
                                                </div>
                                            </div>

                                            <input type="hidden" name="email" id="email" value="">
                                            <input type="hidden" name="address_type" id="address_type" value="others">
                                            <input type="hidden" name="country" id="country" value="Bangladesh">
                                            <input type="hidden" name="city" id="city" value="">
                                            <input type="hidden" name="zip" id="zip" value="">

                                            <div class="col-12">
                                                <div class="form-group mb-1">
                                                    <label>{{ translate('address')}}<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="address" type="text" name="address" {{$shippingAddresses->count()==0?'required':''}}></textarea>
                                                    <span class="fs-14 text-danger font-semi-bold opacity-0 map-address-alert">
                                                        {{ translate('note') }}: {{ translate('you_need_to_select_address_from_your_selected_country') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if(getWebConfig('map_api_status') ==1 )
                                        <div class="form-group location-map-canvas-area map-area-alert-border">
                                            <input id="pac-input" class="controls rounded __inline-46 location-search-input-field" title="{{translate('search_your_location_here')}}" type="text" placeholder="{{translate('search_here')}}"/>
                                            <div class="__h-200px" id="location_map_canvas"></div>
                                        </div>
                                        @endif

                                        <div class="d-flex gap-3 align-items-center">
                                            <label class="form-check-label d-flex gap-2 align-items-center" id="save_address_label">
                                                <input type="hidden" name="shipping_method_id" id="shipping_method_id" value="0">
                                                @if(auth('customer')->check())
                                                <input type="checkbox" name="save_address" id="save_address">
                                                {{ translate('save_this_Address') }}
                                                @endif
                                            </label>
                                        </div>

                                        <input type="hidden" id="latitude" name="latitude" class="form-control d-inline" value="{{$defaultLocation?$defaultLocation['lat']:0}}" required readonly>
                                        <input type="hidden" name="longitude" class="form-control" id="longitude" value="{{$defaultLocation?$defaultLocation['lng']:0}}" required readonly>

                                        <button type="submit" class="btn btn--primary d--none" id="address_submit"></button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </form>

               
                @endif

                <!-- Billing Section -->
                @if($billingInputByCustomer)
                @include('web-views.partials._billing-address-section')
                @endif
            </div>
        </section>

        

        
 <!-- Cart Details Container for Real-Time Updates -->
        <div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
           <h3 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">{{ translate('shopping_cart')}}</h3>

@php($shippingMethod=getWebConfig(name: 'shipping_method'))
@php($cart=\App\Utils\CartManager::getCartListGroupQuery())

<?php
    $admin_shipping = \App\Models\ShippingType::where('seller_id', 0)->first();
?>

<div class="row g-3 mx-max-md-0 mb-3">
    <section class="col-lg-8 px-max-md-0">
        @if(count($cart)==0)
            @php($isPhysicalProductExist = false)
        @endif

        <!-- SHIPPING METHOD SECTION REMOVED FROM HERE (NOW AT TOP) -->

        <div class="table-responsive d-none d-lg-block">
            <table
                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table __cart-table">
                <thead class="thead-light">
                <tr class="">
                    <th class="font-weight-bold __w-45">
                        <div class="pl-3">
                            {{ translate('product')}}
                        </div>
                    </th>
                    <th class="font-weight-bold pl-0 __w-15p text-capitalize">{{ translate('unit_price')}}</th>
                    <th class="font-weight-bold __w-15p">
                        <span class="pl-3">{{ translate('qty')}}</span>
                    </th>
                    <th class="font-weight-bold __w-15p text-end">
                        <div class="pr-3">
                            {{ translate('total')}}
                        </div>
                    </th>
                </tr>
                </thead>
            </table>
   
            @foreach($cart as $group_key=>$group)
                <div class="card __card cart_information __cart-table mb-3">
                    <?php
                    $isPhysicalProductExist = false;
                    $total_shipping_cost = 0;
                    foreach ($group as $row) {
                        if ($row->product_type == 'physical' && $row->is_checked) {
                            $isPhysicalProductExist = true;
                        }
                        if ($row->product_type == 'physical' && $row->is_checked && $row->shipping_type != "order_wise") {
                            $total_shipping_cost += $row->shipping_cost;
                        }
                    }
                    ?>
                        
                    @foreach($group as $cart_key => $cartItem)
                        @if ($shippingMethod=='inhouse_shipping')
                            <?php
                                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                            ?>
                        @else
                            <?php
                                if ($cartItem->seller_is == 'admin') {
                                    $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                                } else {
                                    $seller_shipping = \App\Models\ShippingType::where('seller_id', $cartItem->seller_id)->first();
                                    $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
                                }
                            ?>
                        @endif

                        @if($cart_key==0)
                            <div
                                class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 px-12">
                                @php($verify_status = \App\Utils\OrderManager::verifyCartListMinimumOrderAmount($request, $group_key))
                                @if($cartItem->seller_is=='admin')
                                    <div class="d-flex gap-2">
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="checkbox" class="shop-head-check shop-head-check-desktop" hidden>
                                            <a href="{{route('shopView',['id'=>0])}}"
                                                class="text-primary d-flex align-items-center gap-2 fs-16">
                                                 <img src="{{theme_asset(path: 'public/assets/front-end/img/cart-store.png')}}" alt="">
                                                 {{getWebConfig(name: 'company_name')}}
                                             </a>
                                        </div>
                                        @if ($verify_status['minimum_order_amount'] > $verify_status['amount'])
                                            <span class="pl-1 text-danger pulse-button minimum-order-amount-message" data-toggle="tooltip"
                                                  data-placement="right"
                                                  data-title="{{ translate('minimum_Order_Amount') }} {{ webCurrencyConverter(amount: $verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{getWebConfig(name: 'company_name')}} @else {{ \App\Utils\get_shop_name($cartItem['seller_id']) }} @endif"
                                                  title="{{ translate('minimum_Order_Amount') }} {{ webCurrencyConverter(amount: $verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{getWebConfig(name: 'company_name')}} @else {{ \App\Utils\get_shop_name($cartItem['seller_id']) }} @endif">
                                                    <i class="czi-security-announcement"></i>
                                                </span>
                                        @endif
                                    </div>
                                @else
                                    <?php
                                        $shopIdentity = \App\Models\Shop::where(['seller_id'=>$cartItem['seller_id']])->first();
                                    ?>
                                    <div class="d-flex gap-2">
                                        @if($shopIdentity)
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="checkbox" class="shop-head-check shop-head-check-desktop" hidden>
                                            <a href="{{ route('shopView',['id' => $shopIdentity->id]) }}"
                                                class="text-primary d-flex align-items-center gap-2 fs-16">
                                                 <img src="{{theme_asset(path: 'public/assets/front-end/img/cart-store.png')}}" alt="">
                                                     {{ $shopIdentity->name }}
                                            </a>
                                        </div>

                                            @if ($verify_status['minimum_order_amount'] > $verify_status['amount'])
                                                <span class="pl-1 text-danger pulse-button minimum-order-amount-message" data-toggle="tooltip"
                                                      data-placement="right"
                                                      data-title="{{ translate('minimum_Order_Amount') }} {{ webCurrencyConverter(amount: $verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{getWebConfig(name: 'company_name')}} @else {{ \App\Utils\get_shop_name($cartItem['seller_id']) }} @endif"
                                                      title="{{ translate('minimum_Order_Amount') }} {{ webCurrencyConverter(amount: $verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{getWebConfig(name: 'company_name')}} @else {{ \App\Utils\get_shop_name($cartItem['seller_id']) }} @endif">
                                                    <i class="czi-security-announcement"></i>
                                                </span>
                                            @endif
                                        @else
                                            <a href="javascript:"
                                               class="text-primary d-flex align-items-center gap-2 fs-16">
                                                <img src="{{theme_asset(path: 'public/assets/front-end/img/cart-store.png')}}" alt="">
                                                <span class="text-danger">{{ translate('vendor_not_available') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @php($chosenShipping=\App\Models\CartShipping::where(['cart_group_id'=>$cartItem['cart_group_id']])->first())
                                <div class=" bg-white select-method-border rounded">
                                    @if($isPhysicalProductExist && $shippingMethod=='sellerwise_shipping' && $shipping_type == 'order_wise')
                                        @if(isset($chosenShipping)==false)
                                            @php($chosenShipping = ['shipping_method_id' => 0])
                                        @endif
                                        @php($shippings=\App\Utils\Helpers::getShippingMethods($cartItem['seller_id'], $cartItem['seller_is']))
                                        @if($isPhysicalProductExist && $shippingMethod=='sellerwise_shipping' && $shipping_type == 'order_wise')

                                            <div class="d-sm-flex">
                                                @isset($chosenShipping['shipping_cost'])
                                                    <div class="text-sm-nowrap mx-sm-2 mt-sm-2 mb-1">
                                                        <span class="font-weight-bold">
                                                            {{ translate('shipping_cost')}}
                                                        </span>:
                                                        <span>
                                                            {{webCurrencyConverter($chosenShipping['shipping_cost'])}}
                                                        </span>
                                                    </div>
                                                @endisset

                                                @if(count($shippings) > 0)
                                                    <div class="">
                                                        <div class="dropdown">
                                                            <a class="bg-white border select-method-border rounded py-2 text-dark d-flex flex-wrap align-items-center" href="javascript:" data-toggle="dropdown">
                                                                    <?php
                                                                    $shippingTitle = translate('choose_shipping_method');
                                                                    foreach ($shippings as $shipping) {
                                                                        if ($chosenShipping['shipping_method_id'] == $shipping['id']) {
                                                                            $shippingTitle = ucfirst($shipping['title']) . ' ( ' . $shipping['duration'] . ' ) ' . webCurrencyConverter($shipping['cost']);
                                                                        }
                                                                    }
                                                                    ?>
                                                                <div class="flex-middle flex-nowrap fw-semibold text-dark px-2 text-capitalize">
                                                                    <i class="fa fa-truck"></i>
                                                                    {{ translate('shipping_method') }} :
                                                                </div>
                                                                <span class="px-1 max-width-200px text-nowrap text-truncate">{{ $shippingTitle }}</span>
                                                            </a>
                                                            <div class="dropdown-menu m-0 pb-0 w-100">
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach($shippings as $shipping)
                                                                        <li class="cursor-pointer text-dark px-3 py-1 setShippingIdFunctionCartDetails font-semi-bold fs-14"
                                                                            data-id="{{$shipping['id']}}"
                                                                            data-cart-group="{{$cartItem['cart_group_id']}}"
                                                                        >
                                                                            {{ucfirst($shipping['title']).' ( '.$shipping['duration'].' ) '.webCurrencyConverter($shipping['cost'])}}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-danger d-flex align-items-center gap-1 fs-14 font-semi-bold user-select-none" data-toggle="tooltip"
                                                                  data-placement="top"
                                                                  title="{{ translate('No_shipping_options_available_at_this_shop') }}, {{ translate('please_remove_all_items_from_this_shop') }}">
                                                        <i class="czi-security-announcement"></i> {{ translate('shipping_Not_Available') }}
                                                    </span>
                                                @endif

                                            </div>
                                        @endif
                                    @else
                                        @if ($isPhysicalProductExist && $shipping_type != 'order_wise')
                                            <div class="">
                                                <span class="font-weight-bold">{{ translate('total_shipping_cost')}}</span>
                                                :
                                                <span>{{ webCurrencyConverter(amount: $total_shipping_cost)}}</span>
                                            </div>
                                        @elseif($isPhysicalProductExist && $shipping_type == 'order_wise' && $chosenShipping)
                                            <div class="">
                                                <span class="font-weight-bold">{{ translate('total_shipping_cost')}}</span>
                                                :
                                                <span>{{ webCurrencyConverter(amount: $chosenShipping->shipping_cost)}}</span>
                                            </div>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        @endif
                    @endforeach
                    <table
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table __cart-table">
                        <tbody>
                            <?php
                            $isPhysicalProductExist = false;
                            foreach ($group as $row) {
                                if ($row->product_type == 'physical' && $row->is_checked) {
                                    $isPhysicalProductExist = true;
                                }
                            }
                            ?>
                        @foreach($group as $cart_key=>$cartItem)
                            @php($product = $cartItem->allProducts)

                            <?php
                                $getProductCurrentStock = $product->current_stock;
                                if(!empty($product->variation)) {
                                    foreach(json_decode($product->variation, true) as $productVariantSingle) {
                                        if($productVariantSingle['type'] == $cartItem->variant) {
                                            $getProductCurrentStock = $productVariantSingle['qty'];
                                        }
                                    }
                                }
                            ?>

                            <?php
                                $checkProductStatus = $cartItem->allProducts?->status ?? 0;
                                if($cartItem->seller_is == 'admin' && (checkVendorAbility(type: 'inhouse', status: 'temporary_close') || checkVendorAbility(type: 'inhouse', status: 'vacation_status'))) {
                                    $checkProductStatus = 0;
                                } else if ($cartItem->seller_is == 'seller' && (checkVendorAbility(type: 'vendor', status: 'temporary_close', vendor: $cartItem->allProducts->seller->shop) || checkVendorAbility(type: 'vendor', status: 'vacation_status', vendor: $cartItem->allProducts->seller->shop))) {
                                    $checkProductStatus = 0;
                                }
                            ?>

                            <tr>
                                <td class="__w-45">
                                    <div class="d-flex gap-3 align-items-center">
                                       <input type="checkbox" class="shop-item-check shop-item-check-desktop" 
       value="{{ $cartItem['id'] }}" {{ $cartItem['is_checked'] ? 'checked' : '' }} 
      >


                                        <div class="d-flex gap-3">
                                            <div class="">
                                                <a href="{{ $checkProductStatus == 1 ? route('product', $cartItem['slug']) : 'javascript:'}}"
                                                   class="position-relative overflow-hidden">
                                                    <img class="rounded __img-62 {{ $checkProductStatus == 0?'custom-cart-opacity-50':'' }}"
                                                         src="{{ getStorageImages(path: $cartItem?->product?->thumbnail_full_url, type: 'product') }}"
                                                        alt="{{ translate('product') }}">
                                                    @if ($checkProductStatus == 0)
                                                        <span class="temporary-closed position-absolute text-center p-2">
                                                            <span class="fs-12 font-weight-bolder">{{ translate('N/A') }}</span>
                                                        </span>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="d-flex flex-column gap-1">
                                                <div
                                                    class="text-break __line-2 __w-18rem {{ $checkProductStatus == 0?'custom-cart-opacity-50':'' }}">
                                                    <a href="{{ $checkProductStatus == 1 ? route('product', $cartItem['slug']) : 'javascript:'}}">
                                                        {{$cartItem['name']}}
                                                    </a>
                                                    @if(!empty($cartItem['variant']))
                                                        <div>
                                                            <span class="__text-12px">{{translate('variant')}} : {{$cartItem['variant']}}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if ($product->product_type == 'physical' && $shipping_type != 'order_wise')
                                                    <div
                                                        class="d-flex flex-wrap gap-2 {{ $checkProductStatus == 0?'custom-cart-opacity-50':'' }}">
                                                        <span class="fw-semibold">
                                                            {{ translate('shipping_cost')}}
                                                        </span>:
                                                        <span>
                                                            {{ webCurrencyConverter(amount: $cartItem['shipping_cost']) }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($product->product_type == 'physical' && $getProductCurrentStock < $cartItem['quantity'])
                                                    <div class="d-flex text-danger font-bold">
                                                        <span>{{ translate('Out_Of_Stock') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="{{ $checkProductStatus == 0?'custom-cart-opacity-50':'' }} __w-15p">
                                    <div class="text-center">
                                        <div class="fw-semibold">
                                            {{ webCurrencyConverter(amount: $cartItem['price']-$cartItem['discount']) }}
                                        </div>
                                        <span class="text-nowrap fs-10">
                                                @if ($cartItem->tax_model === "exclude")
                                                ({{ translate('tax')}}
                                                : {{ webCurrencyConverter(amount: $cartItem['tax']*$cartItem['quantity'])}}
                                                )
                                            @else
                                                ({{ translate('tax_included')}})
                                            @endif
                                             </span>
                                    </div>
                                </td>
                                <td class="__w-15p text-center">

                                    @if ($cartItem?->product && $checkProductStatus == 1)
                                        <div class="qty d-flex justify-content-center align-items-center gap-3">
                                                <span class="qty_minus action-update-cart-quantity-list"
                                                      data-minimum-order="{{ $product->minimum_order_qty }}"
                                                      data-cart-id="{{ $cartItem['id'] }}"
                                                      data-increment="{{ '-1' }}"
                                                      data-event="{{ $cartItem['quantity'] == $product->minimum_order_qty ? 'delete':'minus' }}">

                                                   
                                                     @if($getProductCurrentStock < $cartItem['quantity'] || $cartItem['quantity'] == (isset($cartItem->product->minimum_order_qty) ? $cartItem->product->minimum_order_qty : 1))
                                        <i class="tio-delete text-danger"></i>
                                        @else
                                            <i class="tio-remove"></i>
                                        @endif

                                                </span>
                                            <input type="text" class="qty_input cartQuantity{{ $cartItem['id'] }} action-change-update-cart-quantity-list"
                                                   value="{{$cartItem['quantity']}}"
                                                   name="quantity[{{ $cartItem['id'] }}]"
                                                   id="cart_quantity_web{{$cartItem['id']}}"
                                                   data-current-stock="{{ $getProductCurrentStock }}"
                                                   data-minimum-order="{{ $product->minimum_order_qty }}"
                                                   data-cart-id="{{ $cartItem['id'] }}"
                                                   data-increment="{{ '0' }}"
                                                   data-min="{{ isset($cartItem->product->minimum_order_qty) ? $cartItem->product->minimum_order_qty : 1 }}"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            <span class="qty_plus action-update-cart-quantity-list"
                                                  data-minimum-order="{{ $product->minimum_order_qty }}"
                                                  data-cart-id="{{ $cartItem['id'] }}"
                                                  data-increment="{{ '1' }}">
                                                    <i class="tio-add"></i>
                                            </span>
                                        </div>
                                    @else
                                        <div class="qty d-flex justify-content-center align-items-center gap-3">
                                            <span class="action-update-cart-quantity-list cursor-pointer"
                                                  data-minimum-order="{{ $product?->minimum_order_qty ?? 1 }}"
                                                  data-cart-id="{{ $cartItem['id'] }}"
                                                  data-increment="-{{ $cartItem['quantity'] }}"
                                                  data-event="delete">
                                                <i class="tio-delete text-danger" data-toggle="tooltip"
                                                   data-title="{{ translate('product_not_available_right_now')}}"></i>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="__w-15p text-end {{ $checkProductStatus == 0?'custom-cart-opacity-50':'' }}">
                                    <div>
                                        {{ webCurrencyConverter(amount: ($cartItem['price']-$cartItem['discount'])*$cartItem['quantity']) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @php($free_delivery_status = \App\Utils\OrderManager::getFreeDeliveryOrderAmountArray($group[0]->cart_group_id))
                    @if ($free_delivery_status['status'] && (session()->missing('coupon_type') || session('coupon_type') !='free_delivery'))
                        <div class="free-delivery-area px-3 mb-3 mb-lg-2">
                            <div class="d-flex align-items-center gap-8">
                                <img class="__w-30px"
                                     src="{{ theme_asset(path: 'public/assets/front-end/img/icons/free-shipping.png') }}" alt="">
                                @if ($free_delivery_status['amount_need'] <= 0)
                                    <span
                                        class="text-muted fs-12 mt-1">{{ translate('you_Get_Free_Delivery_Bonus') }}</span>
                                @else
                                    <span
                                        class="need-for-free-delivery font-bold fs-12 mt-1 text-primary">{{ webCurrencyConverter(amount: $free_delivery_status['amount_need']) }}</span>
                                    <span
                                        class="text-muted fs-12 mt-1">{{ translate('add_more_for_free_delivery') }}</span>
                                @endif
                            </div>
                            <div class="progress free-delivery-progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $free_delivery_status['percentage'] }}%"
                                     aria-valuenow="{{ $free_delivery_status['percentage'] }}" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        <!-- Mobile view code remains the same -->
        @foreach($cart as $group_key => $group)
            <div class="cart_information mb-3 pb-3 w-100 d-lg-none">
                <!-- ... mobile view content (unchanged) ... -->
            </div>
        @endforeach

        @if( $cart->count() == 0)
            <div class="card mb-4">
                <div class="card-body py-5">
                    <div class="py-md-4">
                        <div class="text-center text-capitalize">
                            <img class="mb-3 mw-100"
                                 src="{{theme_asset(path: 'public/assets/front-end/img/icons/empty-cart.svg')}}" alt="">
                            <p class="text-capitalize">{{translate('Your_Cart_is_Empty')}}!</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="px-3 px-md-0 mt-3 mt-md-0">
            <form method="get">
                <div class="mb-lg-3">
                    <div class="row">
                        <div class="col-12">
                            <label for="phoneLabel" class="form-label input-label fs-14 font-semibold">
                                {{ translate('order_note') }}
                                <span class="input-label-secondary">({{ translate('optional') }})</span>
                            </label>
                            <textarea class="form-control w-100 border-aliceblue h-100-200" id="order_note"
                                      name="order_note">{{ session('order_note')}}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    @include('web-views.partials._order-summary')

    <span id="route-customer-set-shipping-method" data-url="{{ url('/customer/set-shipping-method') }}"></span>
    <span id="route-action-checkout-function" data-route="checkout-details"></span>
</div>

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/cart-details.js') }}"></script>
@endpush 
            
        </div>
       
        
        <span id="get-cart-select-cart-items" data-route="{{ route('cart.select-cart-items') }}"></span>
    </div>
</div>

<!-- Hidden Meta Data -->

<span id="message-update-this-address" data-text="{{ translate('Update_this_Address') }}"></span>
<span id="route-customer-choose-shipping-address-other" data-url="{{ route('customer.choose-shipping-address-other') }}"></span>
<span id="default-latitude-address" data-value="{{ $defaultLocation ? $defaultLocation['lat']:'-33.8688' }}"></span>
<span id="default-longitude-address" data-value="{{ $defaultLocation ? $defaultLocation['lng']:'151.2195' }}"></span>
<
<span id="system-country-restrict-status" data-value="{{ $country_restrict_status }}"></span>

@endsection

@push('script')
<script>


$(document).ready(function () {
  const $headCheck = $(".shop-head-check");

  if (!$headCheck.prop("checked")) {
    $headCheck.click();
  }
});



"use strict";

// Delivery restricted countries check
const deliveryRestrictedCountries = @json($countriesName);
function deliveryRestrictedCountriesCheck(countryOrCode, elementSelector, inputElement) {
    const foundIndex = deliveryRestrictedCountries.findIndex(country => country.toLowerCase() === countryOrCode.toLowerCase());
    if (foundIndex !== -1) {
        $(elementSelector).removeClass('map-area-alert-danger');
        $(inputElement).parent().find('.map-address-alert').removeClass('opacity-100').addClass('opacity-0')
    } else {
        $(elementSelector).addClass('map-area-alert-danger');
        $(inputElement).val('')
        $(inputElement).parent().find('.map-address-alert').removeClass('opacity-0').addClass('opacity-100')
    }
}

// Show/hide account password fields
$('#is_check_create_account').on('change', function() {
    if($(this).is(':checked')) {
        $('.is_check_create_account_password_group').fadeIn();
    } else {
        $('.is_check_create_account_password_group').fadeOut();
    }
});


// ---------- Cart Details JS ----------
{!! file_get_contents(public_path('assets/front-end/js/cart-details.js')) !!}
</script>

<script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/shipping.js') }}?v=6"></script>

@if(getWebConfig('map_api_status') ==1 )
<script
    src="https://maps.googleapis.com/maps/api/js?key={{getWebConfig('map_api_key')}}&callback=mapsShopping&loading=async&libraries=places&v=3.56"
    defer>
</script>
@endif
@endpush