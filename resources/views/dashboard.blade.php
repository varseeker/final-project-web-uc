@extends('layouts.app')

@section('content')
<main class="container py-4">
    <h1 class="page-header">Halo, <strong>{{ Auth::user()->name }}</strong></h1>
    <p class="text-muted mb-4">Pilih modul manajemen di bawah.</p>
    <div class="row row-cols-1 row-cols-md-2 g-4">

    <div class="col">
    <div class="card bg-dark text-white rounded-3 dashboard-card">
        <a href="/dashboard/menu" class="text-white">

            <img src="img/menu_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Menu Management</h2>
                <p class="card-text small mb-0">Kelola daftar menu, harga, dan kategori.</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3 dashboard-card">
        <a href="/dashboard/crew" class="text-white">

            <img src="img/crew_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Crew Management</h2>
                <p class="card-text small mb-0">Kelola data karyawan dan peran.</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3 dashboard-card">
        <a href="/dashboard/order" class="text-white">

            <img src="img/order_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Order Management</h2>
                <p class="card-text small mb-0">Lihat dan ekspor riwayat pesanan.</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3 dashboard-card">
        <a href="/dashboard/payment" class="text-white">

            <img src="img/payment_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Payment Record</h2>
                <p class="card-text small mb-0">Lihat dan ekspor data pembayaran.</p>
            </div>

        </a>
    </div>
    </div>
    

</main>
@endsection
    