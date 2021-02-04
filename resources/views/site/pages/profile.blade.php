@extends('layouts.site')

@section('content')
    <section class="page-breadcrumb" style="background: linear-gradient(to right,rgba(12, 41, 116, 0.84) 0%,rgba(35, 107, 237, 0.84) 48%), url(https://images.pexels.com/photos/270348/pexels-photo-270348.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=500);">
        <nav>
            <ul>
                <li><a href="{{ route('web.index') }}">{{ __('site.home') }}</a></li>
                <li>{{ __('site.auth.profile')  }}</li>
            </ul>
        </nav>
    </section>

    <section class="container mb-5">
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="{{ asset('img/profile.webp') }}" alt="Admin" class="rounded-circle" width="150">
                            <div class="mt-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="text-secondary mb-1">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <ul class="profile-actions">
                        <li><a href="#"><i class="bi bi-bookmark"></i> {{ __('site.auth.saved_posts') }}</a></li>
                        <li onclick="document.getElementById('logout-form').submit();"><i class="bi bi-person-x"></i> {{ __('site.auth.logout') }}</li>
                        <li onclick="openModal('#delete-account-modal')"><i class="bi bi-person-dash"></i> {{ __('site.auth.delete_account') }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <form id="profile-form">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">{{ __('users.fullname') }}</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="ce-f-group">
                                        <input type="text" name="name" value="{{ $user->name }}" required>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">{{ __('users.email') }}</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="ce-f-group">
                                        <input type="text" value="{{ $user->email }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">{{ __('users.phone') }}</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="ce-f-group">
                                        <input type="text" name="phone" value="{{ $user->phone }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">{{ __('users.current_password') }}</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="ce-f-group">
                                        <input type="password" name="current_password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mb-3">
                        <button class="ce-btn me-0" type="submit">{{ __('site.save') }} <i class="bi bi-save"></i></button>
                    </div>
                    <div class="alert fade show d-none ce-alert" role="alert"></div>
                </form>
            </div>
        </div>
    </section>
    @include('site.auth.delete-account-modal')
@endsection

@push('scripts')
    <script>
        document.getElementById('profile-form').addEventListener('submit', async e => {
            e.preventDefault()
            const {data: { status, message }} = await request.post('{{ route('web.update_profile') }}', serialize(e.target, {hash: true, empty: true}))

            const alertEl = e.target.querySelector('.alert')
            if (status){
                replaceClasses(alertEl, ['d-none', 'alert-danger'], ['alert-success'])
            }else{
                replaceClasses(alertEl, ['d-none', 'alert-success'], ['alert-danger'])
            }
            alertEl.innerText = message
        })

        document.getElementById('delete-account-form').addEventListener('submit', async e => {
            e.preventDefault()

            const {data: { status, message }} = await request.post('{{ route('web.delete_account') }}', serialize(e.target, {hash: true, empty: true}))

            const alertEl = e.target.querySelector('.alert')
            if (status){
                replaceClasses(alertEl, ['d-none', 'alert-danger'], ['alert-success'])
                e.target.reset()
                setTimeout(() => {
                    location.href = appUrl()
                }, 2000)
            }else{
                replaceClasses(alertEl, ['d-none', 'alert-success'], ['alert-danger'])
            }
            alertEl.innerText = message
        })
    </script>
@endpush
