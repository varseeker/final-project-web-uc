@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
    
    <h1> Hello, <strong>{{ Auth::user()->name }}</strong> </h1>
    <div class="row row-cols-1 row-cols-md-2 g-3 mt-2">

    <div class="col">
    <div class="card bg-dark text-white rounded-3">
        <a href="/dashboard/menu" class="text-white">

            <img src="img/menu_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Menu Management</h2>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <p class="card-text">Last updated 3 mins ago</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3">
        <a href="/dashboard/crew" class="text-white">

            <img src="img/crew_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Crew Management</h2>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <p class="card-text">Last updated 3 mins ago</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3">
        <a href="/dashboard/order" class="text-white">

            <img src="img/order_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Order Management</h2>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <p class="card-text">Last updated 3 mins ago</p>
            </div>

        </a>
    </div>
    </div>

    <div class="col">
    <div class="card bg-dark text-white rounded-3">
        <a href="/dashboard/payment" class="text-white">

            <img src="img/payment_placeholder.png" class="card-img">
            <div class="card-img-overlay">
                <h2 class="card-title mt-3">Payment Record</h2>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <p class="card-text">Last updated 3 mins ago</p>
            </div>

        </a>
    </div>
    </div>
    

</main>
@endsection
    