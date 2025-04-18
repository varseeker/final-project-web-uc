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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">
          <!-- Authentication Links -->
          @guest
              @if (Route::has('login'))
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                  </li>
              @endif
  
              <!-- @if (Route::has('register'))
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
                  </li>
              @endif -->
          @else
              <li class="nav-item dropdown">
                  <a id="navbarDropdown" class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {{ Auth::user()->name }}
                  </a>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                      
                    @if (Auth::user()->role == 1)
                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                            {{ __('Dashboard') }}
                        </a>
                    @endif
                    @if (Auth::user()->role == 2)
                        <a href="#" class="btn btn-secondary dropdown-item">Add Order</a>
                    @endif
                      <a class="dropdown-item" href="{{ route('logout') }}"
                         onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                          {{ __('Logout') }}
                      </a>
  
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                          @csrf
                      </form>
                  </div>
              </li>
          @endguest
        </ul>
      </div>
    </div>
</nav>