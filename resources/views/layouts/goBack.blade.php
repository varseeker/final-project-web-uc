<nav class="mb-3" aria-label="Navigasi kembali">
  <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('dashboard') }}" class="nav-back-link">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
  <span class="text-muted mx-1">|</span>
  <a href="{{ url('dashboard') }}" class="nav-back-link">
    <i class="bi bi-grid"></i> Dashboard
  </a>
</nav>
