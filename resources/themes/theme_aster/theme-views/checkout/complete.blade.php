@extends('layouts.front-end.app')

@section('title', translate('order_Complete'))

@section('content')
    <div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card">
                    @if(auth('customer')->check() || session('guest_id'))
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                            </div>

                            <h6 class="font-black fw-bold text-center">
                                @if(isset($isNewCustomerInSession) && $isNewCustomerInSession)
                                    {{ translate('Order_Placed_&_Account_Created_Successfully') }}!
                                @else
                                    {{ translate('Order_Placed_Successfully') }}!
                                @endif
                            </h6>

                            @if (isset($order_ids) && count($order_ids) > 0)
                                <p class="text-center fs-12">
                                    {{ translate('your_payment_has_been_successfully_processed_and_your_order') }} -
                                    <span class="fw-bold text-primary">
                                        @foreach ($order_ids as $key => $order_id)
                                            @if($key > 0), @endif{{ $order_id }}
                                        @endforeach
                                    </span>
                                    {{ translate('has_been_placed.') }}
                                </p>

                                {{-- GTM Purchase Event Tracking --}}
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        window.dataLayer = window.dataLayer || [];
                                        
                                        // Clear previous ecommerce data
                                        dataLayer.push({ ecommerce: null }); 

                                        // Prepare items array
                                        const items = [
                                            @if(isset($orders) && count($orders) > 0)
                                                @foreach($orders as $order)
                                                    @if($order->details && count($order->details) > 0)
                                                        @foreach($order->details as $item)
                                                        {
                                                            'item_id': '{{ $item->product_id ?? "N/A" }}',
                                                            'item_name': '{{ $item->product->name ?? "Product" }}',
                                                            'affiliation': '{{ $item->seller_is == "admin" ? "PaikariSale24" : ($item->seller->shop ?? "Vendor") }}',
                                                            'coupon': '{{ session("coupon_code") ?? "" }}',
                                                            'currency': 'BDT',
                                                            'discount': {{ $item->discount ?? 0 }},
                                                            'index': {{ $loop->index }},
                                                            'item_brand': '{{ $item->product->brand->name ?? "N/A" }}',
                                                            'item_category': '{{ $item->product->category->name ?? "N/A" }}',
                                                            'item_variant': '{{ $item->variant ?? "N/A" }}',
                                                            'price': {{ $item->price }},
                                                            'quantity': {{ $item->qty }}
                                                        },
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        ];

                                        // Push Purchase Event
                                        dataLayer.push({
                                            'event': 'purchase',
                                            'ecommerce': {
                                                'transaction_id': '{{ implode(",", $order_ids) }}',
                                                'affiliation': 'PaikariSale24',
                                                'value': {{ $total_amount ?? 0 }},
                                                'tax': {{ $tax_amount ?? 0 }},
                                                'shipping': {{ $shipping_cost ?? 0 }},
                                                'currency': 'BDT',
                                                'coupon': '{{ session("coupon_code") ?? "" }}',
                                                'items': items
                                            }
                                        });

                                        // Log for debugging
                                        console.log('GTM Purchase Event:', {
                                            transaction_id: '{{ implode(",", $order_ids) }}',
                                            total_amount: {{ $total_amount ?? 0 }},
                                            items_count: items.length
                                        });
                                    });
                                </script>
                                {{-- GTM Purchase Event Tracking End --}}

                            @else
                                <p class="text-center fs-12">
                                    {{ translate('your_order_is_being_processed_and_will_be_completed.') }}
                                    {{ translate('You_will_receive_an_email_confirmation_when_your_order_is_placed.') }}
                                </p>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ route('track-order.index') }}"
                                       class="btn btn--primary mb-3 text-center">
                                        {{ translate('track_Order')}}
                                    </a>
                                </div>
                                <div class="col-12 text-center">
                                    <a href="{{route('home')}}" class="text-center">
                                        {{ translate('Continue_Shopping') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection