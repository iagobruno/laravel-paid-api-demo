<header class="navbar navbar-linght border-bottom" style="height: 70px">
    <div class="d-flex container justify-between">
        <a class="navbar-brand fs-5 fw-bold" href="@yield('header_brand_link', '/')">{{ config('app.name') }}</a>

        <div class="d-flex align-items-center gap-3">
            @auth
                @if (!Route::is('dashboard') && Auth::user()->subscribed())
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Dashboard</a>
                @endif

                <span>{{ Auth::user()->name }}</span>

                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm">Sair</button>
                    </form>
                @endif
            @else
                @if (Route::has('auth.github'))
                    <a href="{{ route('auth.github') }}" class="btn btn-primary btn-sm">Entrar</a>
                @endif
            @endauth
        </div>
    </div>
</header>
