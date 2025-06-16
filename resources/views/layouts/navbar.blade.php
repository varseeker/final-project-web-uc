<style>
    .navbar-brand img {
      max-height: 40px; /* Sesuaikan tinggi logo */
    }

    .navbar-nav .nav-item .nav-link {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    @media (max-width: 991.98px) {
    .navbar-nav .nav-item .nav-link {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        }
    }

</style>

<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a href="{{ url('/') }}" class="navbar-brand">
        <div class="navbar-brand text-white">
            <strong>Underground<br>Cafe</strong>
        </div>
        <!-- <img src="{{ asset('img/logo-with-name-black.png') }}" alt="Web Banking Hero Image" class="img-fluid" style="height: 40px;"> -->
    </a>
    <form class="d-flex">
      <!-- Authentication Links -->
          @guest
          
              @if (Route::has('login'))
                      <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
              @endif
          @else
                <span class="">Login as </span>
                <a class="nav-link text-white  ms-2 me-4 fw-bold" href="#" role="button">
                       {{ Auth::user()->name }}
                      
                </a>

                @if ( Auth::user()->role == 'admin')
                      <a class="nav-link text-white me-4" href="{{ route('dashboard') }}">Dashboard</a>
                @endif

                
                      <a class="nav-link text-white me-4" href="{{ route('home') }}">POS</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                          @csrf
                <a class="nav-link text-white" href="{{ route('logout') }}">
                          {{ __('Logout') }}
                </a>
  
                </form>
          @endguest
    </form>
  </div>
</nav>
