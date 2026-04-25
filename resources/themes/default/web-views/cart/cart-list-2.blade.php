@section('content')
    @include(VIEW_FILE_NAMES['products_cart_details_partials'])

    <span id="get-cart-select-cart-items" data-route="{{ route('cart.select-cart-items') }}"></span>
@endsection

