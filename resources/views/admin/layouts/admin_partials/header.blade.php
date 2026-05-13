{{--
  This is the original Skodash header shell. The dynamic admin UI for
  Inertia pages is rendered in resources/js/Layouts/AdminLayout.vue so
  that language switching works without a full page refresh. This file
  is kept as the canonical reference and can be used by any
  non-Inertia (server-rendered) Blade route that wants the same chrome.
--}}
<header class="top-header">
    <nav class="navbar navbar-expand">
        <div class="mobile-toggle-icon d-xl-none">
            <i class="bi bi-list"></i>
        </div>

        <div class="top-navbar d-none d-xl-block">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">{{ __('coffee.dashboard') }}</a>
                </li>
                @if (auth()->check() && auth()->user()->branches)
                    <li class="nav-item">
                        <span class="nav-link text-muted">
                            <i class="bi bi-shop me-1"></i>
                            {{ session('active_branch_name', __('coffee.all_branches')) }}
                        </span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="search-toggle-icon d-xl-none ms-auto">
            <i class="bi bi-search"></i>
        </div>
        <form class="searchbar d-none d-xl-flex ms-auto" onsubmit="event.preventDefault();">
            <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
            <input class="form-control" type="text" placeholder="{{ __('coffee.search') }}">
            <div class="position-absolute top-50 translate-middle-y d-block d-xl-none search-close-icon"><i class="bi bi-x-lg"></i></div>
        </form>

        <div class="top-navbar-right ms-3">
            <ul class="navbar-nav align-items-center">
                {{-- Language switcher (legacy, falls back to a full reload) --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-globe2"></i>
                        <span class="ms-1 text-uppercase">{{ app()->getLocale() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="en">
                                <button class="dropdown-item" type="submit">
                                    {{ __('coffee.english') }}
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="kh">
                                <button class="dropdown-item" type="submit">
                                    {{ __('coffee.khmer') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

                {{-- Authenticated user dropdown --}}
                <li class="nav-item dropdown dropdown-large">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <div class="user-setting d-flex align-items-center gap-1">
                            <img src="{{ asset('assets/backend/assets/images/avatars/avatar-1.png') }}" class="user-img" alt="">
                            <div class="user-name d-none d-sm-block">
                                {{ auth()->user()->name ?? 'Guest' }}
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/backend/assets/images/avatars/avatar-1.png') }}" alt="" class="rounded-circle" width="60" height="60">
                                    <div class="ms-3">
                                        <h6 class="mb-0 dropdown-user-name">{{ auth()->user()->name ?? 'Guest' }}</h6>
                                        <small class="mb-0 dropdown-user-designation text-secondary">
                                            {{ optional(auth()->user())->role?->name ?? __('coffee.user') }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon"><i class="bi bi-person-fill"></i></div>
                                    <div class="setting-text ms-3"><span>{{ __('coffee.profile') }}</span></div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon"><i class="bi bi-gear-fill"></i></div>
                                    <div class="setting-text ms-3"><span>{{ __('coffee.settings') }}</span></div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon"><i class="bi bi-speedometer"></i></div>
                                    <div class="setting-text ms-3"><span>{{ __('coffee.dashboard') }}</span></div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <div class="d-flex align-items-center">
                                        <div class="setting-icon"><i class="bi bi-box-arrow-right"></i></div>
                                        <div class="setting-text ms-3"><span>{{ __('coffee.logout') }}</span></div>
                                    </div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
