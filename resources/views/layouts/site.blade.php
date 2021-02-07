<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    {!! meta($_meta ?? $settings) !!}

    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/bootstrap-icons/bootstrap-icons.css') }}">
    @stack('styles')
</head>
<body>
<div id="app">
    <div class="container">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <ul>
                <li><a href="{{ $settings['linkedin'] ?? '#' }}"><i class="bi bi-linkedin linkedin"></i></a></li>
                <li><a href="{{ $settings['github'] ?? '#' }}"><i class="bi bi-github github"></i></a></li>
                <li><a href="{{ $settings['youtube'] ?? '#' }}"><i class="bi bi-youtube youtube"></i></a></li>
            </ul>
            <ul>
                @guest
                    <li onclick="__login()"><i class="bi bi-person"></i> {{ __('site.auth.login') }}</li>
                    <span class="bracket"></span>
                    <li onclick="__register()"><i class="bi bi-person-plus"></i> {{ __('site.auth.register') }}</li>
                    <span class="bracket"></span>
                @endguest
                @auth
                    <li onclick="location.href = '{{ route('web.profile') }}'"><i class="bi bi-person"></i> {{ __('site.auth.profile') }}</li>
                    <span class="bracket"></span>
                    <li onclick="document.getElementById('logout-form').submit();"><i class="bi bi-person-x"></i> {{ __('site.auth.logout') }}</li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                    <span class="bracket"></span>
                @endauth
                @foreach($languages as $language)
                    @if(app()->getLocale() === $language->code)
                        @continue
                    @endif
                        <li>
                            <a href="{{ LaravelLocalization::getLocalizedURL($language->code) }}">
                                <img src="{{ asset('img/flags') }}/{{ $language->code }}.svg" width="20" alt="{{ $language->name }}">
                                {{ strtoupper($language->code) }}
                            </a>
                        </li>
                @endforeach
            </ul>
        </div>
    </div>
    {{--
    @php($logoLight = resizeById($settings['logo_light']))
    @php($logoDark = resizeById($settings['logo_dark']))
    --}}
    <header>
        <div class="container">
            <input type="checkbox" id="menu-toggle"/>
            <nav class="d-flex justify-content-between">
                <div class="logo logo-white">
                    <a href="{{ route('web.index') }}">
                        <img src="{{ asset('site/img/logo-light.svg') }}" alt="">
                    </a>
                </div>
                <div class="logo logo-dark">
                    <a href="{{ route('web.index') }}">
                        <img src="{{ asset('site/img/logo-dark.svg') }}" alt="">
                    </a>
                </div>
                <ul class="menus">
                    @foreach($header_menus as $header_menu)
                        @if(count($header_menu->children))
                            <li class="has-children">
                                <a href="{{ createUrl($header_menu->url) }}">{{ $header_menu->title }}</a>
                                <ul class="sub-menus">
                                    @foreach($header_menu->children as $cMenu)
                                    <li><a href="{{ createUrl($header_menu->url) }}">{{ $header_menu->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li><a href="{{ createUrl($header_menu->url) }}">{{ $header_menu->title }}</a></li>
                        @endif
                    @endforeach
                    <li>
                        <button onclick="__contact()">
                            {{ __('site.contact_us') }}
                        </button>
                    </li>
                </ul>
                <label for="menu-toggle">
                    <i class="bi bi-list-nested"></i>
                </label>
            </nav>
        </div>
    </header>
    @yield('content')
    <footer style="background: linear-gradient(to right,rgba(12, 41, 116, 0.84) 0%,rgba(35, 107, 237, 0.84) 48%), url(https://images.pexels.com/photos/270348/pexels-photo-270348.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=500) no-repeat;background-size: cover;">
        <div class="container pt-5 pb-5">
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="logo">
                        <a href="{{ route('web.index') }}"><img src="{{ asset('site/img/logo-light.svg') }}" alt=""></a>
                    </div>
                    <p>{{ $footer->description ?? '' }}</p>
                    <ul class="d-flex justify-content-center justify-content-md-start">
                        <li><a href="{{ $settings['linkedin'] ?? '#' }}" class="ps-0"><i class="bi bi-linkedin"></i></a></li>
                        <li><a href="{{ $settings['github'] ?? '#' }}"><i class="bi bi-github"></i></a></li>
                        <li><a href="{{ $settings['youtube'] ?? '#' }}"><i class="bi bi-youtube"></i></a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h5><i class="bi bi-list"></i> {{ __('site.categories') }}</h5>
                    <ul>
                        @foreach($category_links as $category_link)
                            <li><a href="{{ createUrl($category_link->url) }}"><i class="bi bi-chevron-double-right"></i> {{ $category_link->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h5><i class="bi bi-link-45deg"></i> {{ __('site.quick_links') }}</h5>
                    <ul>
                        @foreach($quick_links as $quick_link)
                            <li><a href="{{ url($quick_link->url) }}"><i class="bi bi-chevron-double-right"></i> {{ $quick_link->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h5><i class="bi bi-cursor"></i> {{ __('site.contact_us') }}</h5>
                    <ul>
                        <li><a href="tel:{{ $settings['phone'] ?? '' }}"><i class="bi bi-phone-vibrate"></i> {{ $settings['phone'] ?? '' }}</a></li>
                        <li><a href="mailto:{{ $settings['email'] ?? '' }}"><i class="bi bi-envelope"></i> {{ $settings['email'] ?? '' }}</a></li>
                        <li><a href="{{ $settings['address'] ?? '' }}"><i class="bi bi-geo-alt"></i> {{ $settings['address'] ?? '' }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>
<script>window.appUrl = (path = '') => `{{ url('') }}/${path}`</script>
<script defer src="{{ asset('site/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
