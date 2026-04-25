@php($announcement=getWebConfig(name: 'announcement'))

@if (isset($announcement) && $announcement['status']==1)
    <div class="text-center position-relative px-4 py-1 d--none" id="announcement"
         style="background-color: {{ $announcement['color'] }};color:{{$announcement['text_color']}}">
        <span>{{ $announcement['announcement'] }} </span>
        <span class="__close-announcement web-announcement-slideUp">X</span>
    </div>
@endif

@php($categories = \App\Utils\CategoryManager::getCategoriesWithCountingAndPriorityWiseSorting(dataLimit: 11))

<header class="rtl __inline-10" style="position: sticky; top: 0; z-index: 1030;">
    <div class="navbar-sticky mobile-head">
        <div class="navbar navbar-expand-md navbar-light flex-wrap">
            <div class="container">
                <button class="navbar-toggler border-0 d-md-none" type="button" id="mobileDrawerToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <a class="navbar-brand d-none d-sm-block me-2 flex-shrink-0 __min-w-7rem" href="{{route('home')}}">
                    <img class="__inline-11"
                         src="{{ getStorageImages(path: $web_config['web_logo'], type: 'logo') }}"
                         alt="{{$web_config['company_name']}}">
                </a>
                <a class="navbar-brand d-sm-none" href="{{route('home')}}">
                    <img class="mobile-logo-img"
                         src="{{ getStorageImages(path: $web_config['mob_logo'], type: 'logo') }}"
                         alt="{{$web_config['company_name']}}"/>
                </a>

                {{-- Category Mega Menu --}}
                <div class="cat-mega-wrap d-none d-md-block position-relative me-3">
                    <button class="cat-mega-toggle-btn" id="catMegaToggle" type="button">
                        <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.875 12.9195C9.875 12.422 9.6775 11.9452 9.32563 11.5939C8.97438 11.242 8.4975 11.0445 8 11.0445C6.75875 11.0445 4.86625 11.0445 3.625 11.0445C3.1275 11.0445 2.65062 11.242 2.29937 11.5939C1.9475 11.9452 1.75 12.422 1.75 12.9195V17.2945C1.75 17.792 1.9475 18.2689 2.29937 18.6202C2.65062 18.972 3.1275 19.1695 3.625 19.1695H8C8.4975 19.1695 8.97438 18.972 9.32563 18.6202C9.6775 18.2689 9.875 17.792 9.875 17.2945V12.9195ZM19.25 12.9195C19.25 12.422 19.0525 11.9452 18.7006 11.5939C18.3494 11.242 17.8725 11.0445 17.375 11.0445C16.1337 11.0445 14.2413 11.0445 13 11.0445C12.5025 11.0445 12.0256 11.242 11.6744 11.5939C11.3225 11.9452 11.125 12.422 11.125 12.9195V17.2945C11.125 17.792 11.3225 18.2689 11.6744 18.6202C12.0256 18.972 12.5025 19.1695 13 19.1695H17.375C17.8725 19.1695 18.3494 18.972 18.7006 18.6202C19.0525 18.2689 19.25 17.792 19.25 17.2945V12.9195ZM16.5131 9.66516L19.1206 7.05766C19.8525 6.32578 19.8525 5.13828 19.1206 4.4064L16.5131 1.79891C15.7813 1.06703 14.5937 1.06703 13.8619 1.79891L11.2544 4.4064C10.5225 5.13828 10.5225 6.32578 11.2544 7.05766L13.8619 9.66516C14.5937 10.397 15.7813 10.397 16.5131 9.66516ZM9.875 3.54453C9.875 3.04703 9.6775 2.57015 9.32563 2.2189C8.97438 1.86703 8.4975 1.66953 8 1.66953C6.75875 1.66953 4.86625 1.66953 3.625 1.66953C3.1275 1.66953 2.65062 1.86703 2.29937 2.2189C1.9475 2.57015 1.75 3.04703 1.75 3.54453V7.91953C1.75 8.41703 1.9475 8.89391 2.29937 9.24516C2.65062 9.59703 3.1275 9.79453 3.625 9.79453H8C8.4975 9.79453 8.97438 9.59703 9.32563 9.24516C9.6775 8.89391 9.875 8.41703 9.875 7.91953V3.54453Z" fill="currentColor"/>
                        </svg>
                        <i class="fa fa-list me-1"></i>
                        <span class="fw-bold">{{ translate('categories') }}</span>
                    </button>

                    <div class="cat-mega-panel" id="catMegaPanel">
                        <ul class="cat-left-list">
                            @foreach($categories as $category)
                                <li class="cat-left-item" data-target="sub-{{ $category->id }}">
                                    <a href="{{ route('products', ['category_id' => $category->id, 'data_from' => 'category', 'page' => 1]) }}" class="cat-left-link">
                                        <img width="20" height="20" class="rounded-circle me-2"
                                             src="{{ getStorageImages(path: $category?->icon_full_url, type: 'category') }}"
                                             alt="{{ $category->name }}">
                                        <span>{{ $category->name }}</span>
                                    </a>
                                    @if($category->childes->count() > 0)
                                        <i class="czi-arrow-right cat-left-arrow"></i>
                                        <div class="cat-right-panel" id="sub-{{ $category->id }}">
                                            <div class="cat-right-inner">
                                                @foreach($category->childes as $sub)
                                                    <div class="cat-right-col">
                                                        <a class="cat-right-title"
                                                           href="{{ route('products', ['sub_category_id' => $sub->id, 'data_from' => 'category', 'page' => 1]) }}">
                                                            {{ $sub->name }}
                                                        </a>
                                                        @if($sub->childes->count() > 0)
                                                            <ul class="cat-right-links">
                                                                @foreach($sub->childes as $subsub)
                                                                    <li>
                                                                        <a href="{{ route('products', ['sub_sub_category_id' => $subsub->id, 'data_from' => 'category', 'page' => 1]) }}">
                                                                            {{ $subsub->name }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                            <li class="cat-viewall">
                                <a href="{{ route('categories') }}">{{ translate('View_All') }} &rarr;</a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Search Bar - Desktop only inside navbar --}}
                <div class="input-group-overlay mx-lg-4 search-form-mobile text-align-direction d-none d-md-flex">
                    <form action="{{route('products')}}" type="submit" class="search_form w-100">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-control appended-form-control search-bar-input" type="search"
                                   autocomplete="off" data-given-value=""
                                   placeholder="{{ translate("search_for_items")}}..."
                                   name="name" value="{{ request('name') }}">
                            <input type="hidden" name="global_search_input" value="1">
                            <button class="input-group-append-overlay search_button" type="submit">
                                <span class="input-group-text __text-20px">
                                    <i class="czi-search text-white"></i>
                                </span>
                            </button>

                        </div>
                        <input name="data_from" value="search" hidden>
                        <input name="page" value="1" hidden>
                        <diV class="card search-card mobile-search-card">
                            <div class="card-body">
                                <div class="search-result-box __h-400px overflow-x-hidden overflow-y-auto"></div>
                            </div>
                        </diV>
                    </form>
                </div>

                {{-- Navbar Tools --}}
                <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center">

                    {{-- Download App --}}
                    <a href="https://drive.google.com/file/d/189gM_SlF37AyEPOCQfDe4e-GOsrigZYj/view?usp=sharing"
                       target="_blank"
                       class="navbar-tool download-app-btn text-decoration-none">
                        <div class="navbar-tool-icon-box bg-secondary">
                            <i class="fa fa-download"></i>
                        </div>
                        <div class="navbar-tool-text d-none d-md-block">
                            {{ translate('Download App') }}
                        </div>
                    </a>

                    <div class="navbar-tool dropdown d-none d-md-block {{Session::get('direction') === "rtl" ? 'mr-md-3' : 'ml-md-3'}}">
                        <a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="{{route('wishlists')}}">
                            <span class="navbar-tool-label">
                                <span class="countWishlist">
                                    {{session()->has('wish_list')?count(session('wish_list')):0}}
                                </span>
                           </span>
                            <i class="navbar-tool-icon czi-heart"></i>
                        </a>
                    </div>
                    @if(auth('customer')->check())
                        <div class="dropdown">
                            <a class="navbar-tool ml-3" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="navbar-tool-icon-box bg-secondary">
                                    <div class="navbar-tool-icon-box bg-secondary">
                                        <img class="img-profile rounded-circle __inline-14" alt=""
                                             src="{{ getStorageImages(path: auth('customer')->user()->image_full_url, type: 'avatar') }}">
                                    </div>
                                </div>
                                <div class="navbar-tool-text">
                                    <small>{{ translate('hello')}}, {{ Str::limit(auth('customer')->user()->f_name, 10) }}</small>
                                    {{ translate('dashboard')}}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{route('account-oder')}}"> {{ translate('my_Order')}} </a>
                                <a class="dropdown-item" href="{{route('user-account')}}"> {{ translate('my_Profile')}}</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('customer.auth.logout')}}">{{ translate('logout')}}</a>
                            </div>
                        </div>
                    @else
                        <div class="dropdown">
                            <a class="navbar-tool {{Session::get('direction') === "rtl" ? 'mr-md-3' : 'ml-md-3'}}"
                               type="button" data-toggle="dropdown" aria-haspopup="true" href="#" rel="nofollow" aria-expanded="false">
                                <div class="navbar-tool-icon-box bg-secondary">
                                    <div class="navbar-tool-icon-box bg-secondary">
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.25 4.41675C4.25 6.48425 5.9325 8.16675 8 8.16675C10.0675 8.16675 11.75 6.48425 11.75 4.41675C11.75 2.34925 10.0675 0.666748 8 0.666748C5.9325 0.666748 4.25 2.34925 4.25 4.41675ZM14.6667 16.5001H15.5V15.6667C15.5 12.4509 12.8825 9.83341 9.66667 9.83341H6.33333C3.11667 9.83341 0.5 12.4509 0.5 15.6667V16.5001H14.6667Z"
                                                  fill="{{ $web_config['primary_color'] ?? '#1B7FED'}}"/>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="text-align-direction dropdown-menu __auth-dropdown dropdown-menu-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{route('customer.auth.login')}}">
                                    <i class="fa fa-sign-in mr-2"></i> {{ translate('sign_in')}}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('customer.auth.sign-up')}}">
                                    <i class="fa fa-user-circle mr-2"></i>{{ translate('sign_up')}}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div id="cart_items">
                        @include('layouts.front-end.partials._cart')
                    </div>
                </div>

                {{-- Mobile Search Row --}}
                <div class="d-md-none w-100 pt-2 pb-1">
                    <form action="{{route('products')}}" method="GET" class="search_form position-relative">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-control appended-form-control search-bar-input-mobile" type="search"
                                   autocomplete="off" data-given-value=""
                                   placeholder="{{ translate('search_for_items')}}..."
                                   name="name" value="{{ request('name') }}">
                            <input type="hidden" name="global_search_input" value="1">
                            <input name="data_from" value="search" hidden>
                            <input name="page" value="1" hidden>
                            <button class="input-group-append-overlay search_button" type="submit">
                                <span class="input-group-text __text-20px">
                                    <i class="czi-search text-white"></i>
                                </span>
                            </button>
                        </div>
                        <div class="card search-card mobile-search-card">
                            <div class="card-body">
                                <div class="search-result-box __h-400px overflow-x-hidden overflow-y-auto"></div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- Mobile Drawer Overlay --}}
        <div class="mob-drawer-overlay" id="mobDrawerOverlay"></div>

        {{-- Mobile Drawer --}}
        <div class="mob-drawer" id="mobDrawer">

            {{-- Header --}}
            <div class="mob-drawer-head">
                <a href="{{route('home')}}">
                    <img height="34" src="{{ getStorageImages(path: $web_config['mob_logo'], type: 'logo') }}" alt="{{$web_config['company_name']}}">
                </a>
                <button class="mob-drawer-close" id="mobDrawerClose"><i class="tio-clear"></i></button>
            </div>

            {{-- User Section --}}
            @if(auth('customer')->check())
                <div class="mob-drawer-user">
                    <img class="mob-drawer-avatar" src="{{ getStorageImages(path: auth('customer')->user()->image_full_url, type: 'avatar') }}" alt="">
                    <div>
                        <div class="mob-drawer-uname">{{ auth('customer')->user()->f_name }} {{ auth('customer')->user()->l_name }}</div>
                        <div class="mob-drawer-uemail">{{ auth('customer')->user()->email }}</div>
                    </div>
                </div>
            @else
                <div class="mob-drawer-auth">
                    <a href="{{route('customer.auth.login')}}" class="mob-drawer-btn-signin">
                        <i class="fa fa-sign-in"></i> {{ translate('sign_in')}}
                    </a>
                    <a href="{{route('customer.auth.sign-up')}}" class="mob-drawer-btn-signup">
                        <i class="fa fa-user-plus"></i> {{ translate('sign_up')}}
                    </a>
                </div>
            @endif

            {{-- Nav --}}
            <nav class="mob-drawer-nav">
                <a href="{{route('home')}}" class="mob-drawer-item">
                    <i class="tio-home mob-drawer-icon"></i> {{ translate('home')}}
                </a>

                <div class="mob-drawer-item mob-drawer-accordion-toggle" id="mobCatToggle">
                    <span><i class="tio-category mob-drawer-icon"></i> {{ translate('categories')}}</span>
                    <i class="tio-chevron-down mob-drawer-chevron" id="mobCatChevron"></i>
                </div>
                <div class="mob-drawer-submenu" id="mobCatBody">
                    @foreach($categories as $category)
                        <a href="{{route('products',['category_id'=> $category['id'],'data_from'=>'category','page'=>1])}}" class="mob-drawer-subitem">
                            <img class="rounded-circle me-2" width="18" height="18" src="{{ getStorageImages(path: $category?->icon_full_url, type: 'category') }}" alt="{{ $category['name'] }}">
                            {{ $category['name'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('categories') }}" class="mob-drawer-subitem mob-drawer-viewall">
                        {{ translate('view_more') }} &rarr;
                    </a>
                </div>

                @if(auth('customer')->check())
                    <a href="{{route('account-oder')}}" class="mob-drawer-item">
                        <i class="tio-shopping-cart mob-drawer-icon"></i> {{ translate('my_Order')}}
                    </a>
                    <a href="{{route('wishlists')}}" class="mob-drawer-item">
                        <i class="tio-favorite mob-drawer-icon"></i> {{ translate('Wishlist')}}
                    </a>
                    <a href="{{route('user-account')}}" class="mob-drawer-item">
                        <i class="tio-user mob-drawer-icon"></i> {{ translate('user_profile')}}
                    </a>
                @endif

                <a href="https://drive.google.com/file/d/189gM_SlF37AyEPOCQfDe4e-GOsrigZYj/view?usp=sharing" target="_blank" class="mob-drawer-item">
                    <i class="fa fa-mobile mob-drawer-icon"></i> {{ translate('Download App')}}
                    <span class="mob-drawer-badge">APK</span>
                </a>
            </nav>

            @if(auth('customer')->check())
                <div class="mob-drawer-footer">
                    <a href="{{route('customer.auth.logout')}}" class="mob-drawer-logout">
                        <i class="tio-exit me-2"></i>{{ translate('logout')}}
                    </a>
                </div>
            @endif
        </div>

    </div>

</header>

@push('script')
<style>
.mob-drawer-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 2000;
    backdrop-filter: blur(2px);
}
.mob-drawer-overlay.active { display: block; }
.mob-drawer {
    position: fixed;
    top: 0; left: 0;
    width: 290px;
    height: 100%;
    background: #fff;
    z-index: 2001;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(.4,0,.2,1);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    box-shadow: 4px 0 24px rgba(0,0,0,0.13);
}
.mob-drawer.active { transform: translateX(0); }
.mob-drawer-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 1;
}
.mob-drawer-close {
    background: #f5f5f5;
    border: none;
    border-radius: 50%;
    width: 32px; height: 32px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #555;
}
.mob-drawer-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, #f5f0ff 0%, #ede9fe 100%);
    border-bottom: 1px solid #ddd6fe;
}
.mob-drawer-avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.mob-drawer-uname { font-weight: 600; font-size: 14px; color: #222; }
.mob-drawer-uemail { font-size: 12px; color: #888; margin-top: 2px; }
.mob-drawer-auth {
    display: flex;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
}
.mob-drawer-btn-signin, .mob-drawer-btn-signup {
    flex: 1;
    text-align: center;
    padding: 8px 0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: opacity .2s;
}
.mob-drawer-btn-signin {
    background: #fff;
    border: 1.5px solid #7c3aed;
    color: #7c3aed;
}
.mob-drawer-btn-signup {
    background: #7c3aed;
    color: #fff;
    border: 1.5px solid #7c3aed;
}
.mob-drawer-nav { flex: 1; padding: 8px 0; }
.mob-drawer-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 20px;
    font-size: 14px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #f7f7f7;
    cursor: pointer;
    transition: background .15s;
}
.mob-drawer-item:hover { background: #f5f0ff; color: #7c3aed; }
.mob-drawer-icon { font-size: 17px; width: 22px; text-align: center; color: #7c3aed; }
.mob-drawer-accordion-toggle { justify-content: space-between; }
.mob-drawer-accordion-toggle > span { display: flex; align-items: center; gap: 12px; }
.mob-drawer-chevron { font-size: 14px; color: #aaa; transition: transform .25s; }
.mob-drawer-chevron.open { transform: rotate(180deg); }
.mob-drawer-submenu { display: none; background: #faf8ff; }
.mob-drawer-submenu.open { display: block; }
.mob-drawer-subitem {
    display: flex;
    align-items: center;
    padding: 10px 20px 10px 44px;
    font-size: 13px;
    color: #555;
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: background .15s;
}
.mob-drawer-subitem:hover { background: #f0ebff; color: #7c3aed; }
.mob-drawer-viewall { color: #7c3aed; font-weight: 600; }
.mob-drawer-badge {
    margin-left: auto;
    background: #7c3aed;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
}
.mob-drawer-footer {
    padding: 14px 16px;
    border-top: 1px solid #f0f0f0;
    position: sticky;
    bottom: 0;
    background: #fff;
}
.mob-drawer-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
    background: #fff0f0;
    color: #e53935;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s;
}
.mob-drawer-logout:hover { background: #ffd6d6; color: #c62828; }
</style>
<script>
"use strict";

var catBtn = document.getElementById('catMegaToggle');
var catPanel = document.getElementById('catMegaPanel');

if (catBtn && catPanel) {
    catBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        catPanel.classList.toggle('cat-mega-open');
    });

    var leftItems = catPanel.querySelectorAll('.cat-left-item');
    leftItems.forEach(function(item) {
        item.addEventListener('mouseenter', function() {
            var targetId = item.getAttribute('data-target');
            catPanel.querySelectorAll('.cat-right-panel').forEach(function(p) {
                p.classList.remove('cat-right-active');
            });
            leftItems.forEach(function(li) { li.classList.remove('cat-left-active'); });
            item.classList.add('cat-left-active');
            if (targetId) {
                var target = document.getElementById(targetId);
                if (target) target.classList.add('cat-right-active');
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!catBtn.contains(e.target) && !catPanel.contains(e.target)) {
            catPanel.classList.remove('cat-mega-open');
        }
    });
}

// Mobile Drawer
var drawerToggle = document.getElementById('mobileDrawerToggle');
var drawer = document.getElementById('mobDrawer');
var overlay = document.getElementById('mobDrawerOverlay');
var drawerClose = document.getElementById('mobDrawerClose');

function openDrawer() {
    drawer.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    drawer.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

if (drawerToggle) drawerToggle.addEventListener('click', openDrawer);
if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
if (overlay) overlay.addEventListener('click', closeDrawer);

// Categories accordion
var mobCatToggle = document.getElementById('mobCatToggle');
var mobCatBody = document.getElementById('mobCatBody');
var mobCatChevron = document.getElementById('mobCatChevron');
if (mobCatToggle) {
    mobCatToggle.addEventListener('click', function() {
        mobCatBody.classList.toggle('open');
        mobCatChevron.classList.toggle('open');
    });
}
</script>
@endpush
