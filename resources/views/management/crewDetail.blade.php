@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

<div class="row justify-content-center flex ">
        <div class="col-md-8">
    <h1 class="mt-3">Editing Crew Data</h1>
    
        <div class="border border-dark rounded-3 mb-3" style="max-width: 1200px;">
            <div class="row g-0">
                    <div class="col-md-6">
                    <div class="align-self-center" style="padding: 20px 30px;">

    @foreach ($crews as $crew)
    <form method="POST" action="/dashboard/crew/{{ $crew->id }}">
      @method('put')
      @csrf


    <!-- <div class="d-flex"> -->

      <!-- <div class="mb-3 me-4" style="width: 50%;"> -->
        <label for="exampleFormControlInput1" class="form-label mt-4">Name</label>
        <input type="text" class="form-control" id="exampleFormControlInput1" name="name" value="{{ $crew->name }}">
      <!-- </div> -->
      
      <!-- <div class="mb-3"  style="width: 50%;"> -->
        <label for="exampleFormControlInput1" class="form-label mt-4">Email</label>
        <input type="email" class="form-control" id="exampleFormControlInput1" name="email" value="{{ $crew->email }}">
      <!-- </div> -->

    <!-- </div> -->

    <!-- <div class="d-flex"> -->

      <!-- <div class="mb-3 me-4 mt-auto" style="width: 50%;"> -->
        <div class="input-group me-4 mt-4">
          <label class="input-group-text" for="inputGroupSelect01">Crew Role</label>
          <select class="form-select" id="inputGroupSelect01" name="role">
            <option selected> {{ $crew->role }} / Click to change</option>
            <option value="crew">Crew</option>
            <option value="cashier">Cashier</option>
            <option value="admin">Admin</option>
            <!-- <option value="crew">Crew</option> -->
          </select>
        </div>
      <!-- </div> -->
      
      <!-- <div class="mb-3"  style="width: 50%;"> -->
        <label for="exampleFormControlInput1" class="form-label  mt-4">Phone Number</label>
        <input type="phone" class="form-control" id="exampleFormControlInput1" name="phone"  value="{{ $crew->phone }}">
      <!-- </div> -->

    <!-- </div> -->

    <div class="mb-3">
      <label for="exampleFormControlTextarea1" class="form-label mt-4">Address</label>
      <textarea class="form-control" name="address" rows="3">{{ $crew->address }}</textarea>
    </div>

                    <div class="form-check">
                        <div class="d-flex flex-row-reverse py-4" >
                            <button type="submit" class="btn btn-outline-success">Update Crew Data</button>
                        </div>
                    </div>
</form>
    
                    </div>
                </div>
                
                <div class="col-md-6">
                    <img src="../../../img/form-art.png" class="img-fluid rounded-end" style="heigh: 1500px;">
                </div>
            </div>
        </div>
</div>
</div>
    @endforeach
    
                    </div>
                </div>
                
                <div class="col-md-4">
                    <img src="../../../img/form-art.png" class="img-fluid rounded-end" style="heigh: 1500px;">
                </div>
            </div>
        </div>
</div>
</div>

</main>
@endsection