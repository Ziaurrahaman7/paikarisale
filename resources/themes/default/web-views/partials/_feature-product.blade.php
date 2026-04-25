@php($overallRating = getOverallRating($product->reviews))
<style>
    .inline_product img {
    transform: none !important;
    transition: none !important;
}

.inline_product:hover img {
    transform: none !important;
}
</style>
<div class="product-single-hover shadow-none rtl">
    <div class="overflow-hidden position-relative">
        <div class="inline_product clickable">
            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                    <span class="direction-ltr d-block">
                       -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                    </span>
                </span>
            @else
                <span class="for-discount-value-null"></span>
            @endif
            <a href="{{ route('product',$product->slug) }}">
                <img loading="lazy" src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="">
            </a>

            

            @if($product->product_type == 'physical' && $product->current_stock <= 0)
                <span class="out_fo_stock">{{ translate('out_of_stock') }}</span>
            @endif
        </div>

        <div class="single-product-details mt-2 d-flex flex-column align-items-center">
            @if($overallRating[0] != 0)
                <div class="rating-show justify-content-between mb-1">
                    <span class="d-inline-block font-size-sm text-body">
                        @for($inc = 1; $inc <= 5; $inc++)
                            @if ($inc <= (int)$overallRating[0])
                                <i class="tio-star text-warning"></i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                            @endif
                        @endfor
                        <label class="badge-style">( {{ count($product->reviews) }} )</label>
                    </span>
                </div>
            @endif

            <h3 class="mb-1 letter-spacing-0 text-center product-title-clamp">
                <a href="{{ route('product',$product->slug) }}" class="text-capitalize fw-semibold">
                    {{ $product['name'] }}
                </a>
            </h3>

            <h4 class="product-price lh-1 mb-2 letter-spacing-0 text-center">
                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                    <del class="category-single-product-price">
                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                    </del>
                @endif
                <span class="text-accent text-dark">
                   {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                </span>
            </h4>

            <!-- Centered Add to Cart / Quick View Button -->
            <div class="product-add-to-cart mt-2 w-100 d-flex justify-content-center">
               <button type="button"
                        class="btn btn-secondary w-100 stopPropagation action-product-quick-view"
                        data-product-id="{{ $product->id }}">
                    <i class="czi-cart"></i> {{ translate('add_to_cart') }}
                </button>
            </div>
        </div>
    </div>
</div>
