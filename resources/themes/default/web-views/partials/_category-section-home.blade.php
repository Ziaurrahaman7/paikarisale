@if ($categories->count() > 0 )
    <section class="container rtl py-4 px-3 px-md-3" id="category-section">
        <div class="__inline-62 pt-3 pb-4 px-4">
            <div class="text-center mb-4">
                <h2 class="our-products-title m-0 letter-spacing-0">
                    <span class="font-semibold">{{ translate('categories')}}</span>
                </h2>
                <div class="our-products-title-border"></div>
            </div>

            {{-- Desktop - 6 per row --}}
            <div class="d-none d-lg-block">
                <div class="row g-3" id="category-container">
                    @foreach($categories as $key => $category)
                        @if ($key < 12)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <a href="{{route('products',['category_id'=> $category['id'],'data_from'=>'category','page'=>1])}}"
                                   class="category-card-new text-decoration-none">
                                    <div class="category-image-wrapper">
                                        <img loading="lazy" alt="{{ $category->name }}"
                                             src="{{ getStorageImages(path:$category->icon_full_url, type: 'category') }}">
                                    </div>
                                    <h3 class="category-name-new">{{ $category->name }}</h3>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($categories->count() > 12)
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary btn-load-more" id="load-more-categories" data-loaded="12" data-total="{{ $categories->count() }}">
                            <i class="tio-reload mr-1"></i> {{ translate('load_more_categories') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Mobile - 2 per row --}}
            <div class="d-lg-none">
                <div class="row row-cols-2 g-3 mt-1" id="category-container-mobile">
                    @foreach($categories as $key => $category)
                        @if ($key < 8)
                            <div class="col">
                                <a href="{{route('products',['category_id'=> $category['id'],'data_from'=>'category','page'=>1])}}"
                                   class="category-card-new text-decoration-none">
                                    <div class="category-image-wrapper">
                                        <img alt="{{ $category->name }}"
                                             src="{{ getStorageImages(path: $category->icon_full_url, type: 'category') }}">
                                    </div>
                                    <h3 class="category-name-new">{{ $category->name }}</h3>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($categories->count() > 8)
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary btn-sm btn-load-more-mobile" id="load-more-categories-mobile" data-loaded="8" data-total="{{ $categories->count() }}">
                            <i class="tio-reload mr-1"></i> {{ translate('load_more') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <style>
    /* Category Card Styles */
    .category-card-new {
        display: block;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    .category-card-new:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: translateY(-4px);
    }
    .category-image-wrapper {
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        position: relative;
        background: #f8f9fa;
        overflow: hidden;
    }
    .category-image-wrapper img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .category-name-new {
        padding: 12px 10px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.3;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-load-more, .btn-load-more-mobile {
        min-width: 200px;
        font-weight: 600;
        border-radius: 50px;
        padding: 10px 24px;
    }
    .btn-load-more:hover, .btn-load-more-mobile:hover {
        background: var(--primary-clr, #f55d2c);
        color: #fff;
        border-color: var(--primary-clr, #f55d2c);
    }
    @media (max-width: 991px) {
        .category-name-new {
            font-size: 13px;
            padding: 10px 8px;
            min-height: 42px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categories = @json($categories);
        
        // Desktop Load More
        const loadMoreBtn = document.getElementById('load-more-categories');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                let loaded = parseInt(this.dataset.loaded);
                const total = parseInt(this.dataset.total);
                const container = document.getElementById('category-container');
                
                const toLoad = Math.min(loaded + 12, total);
                
                for (let i = loaded; i < toLoad; i++) {
                    const category = categories[i];
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-lg-2 col-md-3 col-sm-4 col-6';
                    colDiv.style.display = 'none';
                    colDiv.innerHTML = `
                        <a href="{{ url('products') }}?category_id=${category.id}&data_from=category&page=1"
                           class="category-card-new text-decoration-none">
                            <div class="category-image-wrapper">
                                <img loading="lazy" alt="${category.name}"
                                     src="${category.icon_full_url}">
                            </div>
                            <h3 class="category-name-new">${category.name}</h3>
                        </a>
                    `;
                    container.appendChild(colDiv);
                    setTimeout(() => {
                        colDiv.style.display = 'block';
                        colDiv.style.animation = 'fadeInUp 0.5s ease';
                    }, 50);
                }
                
                this.dataset.loaded = toLoad;
                
                if (toLoad >= total) {
                    this.style.display = 'none';
                }
            });
        }
        
        // Mobile Load More
        const loadMoreMobileBtn = document.getElementById('load-more-categories-mobile');
        if (loadMoreMobileBtn) {
            loadMoreMobileBtn.addEventListener('click', function() {
                let loaded = parseInt(this.dataset.loaded);
                const total = parseInt(this.dataset.total);
                const container = document.getElementById('category-container-mobile');
                
                const toLoad = Math.min(loaded + 8, total);
                
                for (let i = loaded; i < toLoad; i++) {
                    const category = categories[i];
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col';
                    colDiv.style.display = 'none';
                    colDiv.innerHTML = `
                        <a href="{{ url('products') }}?category_id=${category.id}&data_from=category&page=1"
                           class="category-card-new text-decoration-none">
                            <div class="category-image-wrapper">
                                <img alt="${category.name}"
                                     src="${category.icon_full_url}">
                            </div>
                            <h3 class="category-name-new">${category.name}</h3>
                        </a>
                    `;
                    container.appendChild(colDiv);
                    setTimeout(() => {
                        colDiv.style.display = 'block';
                        colDiv.style.animation = 'fadeInUp 0.5s ease';
                    }, 50);
                }
                
                this.dataset.loaded = toLoad;
                
                if (toLoad >= total) {
                    this.style.display = 'none';
                }
            });
        }
    });
    
    // Animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
    </script>
@endif
