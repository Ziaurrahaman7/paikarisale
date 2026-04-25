@if ($categories->count() > 0 )
    <section class="container rtl py-4 px-3 px-md-3">
        <div class="__inline-62 pt-3 pb-4 px-4">
            <div class="text-center mb-3">
                <h2 class="our-products-title m-0 m-0 letter-spacing-0">
                    <span class="font-semibold">{{ translate('categories')}}</span>
                </h2>
                <div class="our-products-title-border"></div>
            </div>

            {{-- Desktop --}}
            <div class="d-none d-lg-block">
                <div class="d-flex flex-wrap gap-4">
                    @foreach($categories as $key => $category)
                        @if ($key < 7)
                            <a href="{{route('products',['category_id'=> $category['id'],'data_from'=>'category','page'=>1])}}"
                               class="cat-home-card text-decoration-none text-center">
                                <div class="cat-home-img">
                                    <img loading="lazy" alt="{{ $category->name }}"
                                         src="{{ getStorageImages(path:$category->icon_full_url, type: 'category') }}">
                                </div>
                                <h3 class="cat-home-name">{{Str::limit($category->name, 15)}}</h3>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Mobile --}}
            <div class="d-lg-none">
                <div class="row row-cols-2 row-cols-md-4 g-3 mt-1">
                    @foreach($categories as $key => $category)
                        @if ($key < 8)
                            <div class="col my-2">
                                <a href="{{route('products',['category_id'=> $category['id'],'data_from'=>'category','page'=>1])}}"
                                   class="cat-home-card text-decoration-none text-center d-block">
                                    <div class="cat-home-img">
                                        <img alt="{{ $category->name }}"
                                             src="{{ getStorageImages(path: $category->icon_full_url, type: 'category') }}">
                                    </div>
                                    <h3 class="cat-home-name">{{ $category->name }}</h3>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
