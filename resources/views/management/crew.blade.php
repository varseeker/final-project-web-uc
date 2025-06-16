@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

    <div class="my-3 me-5 d-flex justify-content-start">
      <h1>Crew Management</h1>
    </div>
    <div class="my-3 me-5 d-flex justify-content-end">
      <a href="/dashboard/crew/new" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Insert new crew record
            </button>
            
    </a>
    </div>

    <table class="table table-striped">
  <thead class="table-dark">
    <tr>
      <th scope="col">Crew ID</th>
      <th scope="col">Name</th>
      <th scope="col">Email</th>
      <th scope="col">Role</th>
      <th scope="col">Phone</th>
      <th scope="col">Address</th>
      <th scope="col">Manage</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($crews as $crew)
    <tr >
      <th scope="row" class="align-middle">{{ $crew->id }}</th>
      <td class="align-middle">{{ $crew->name }}</td>
      <td class="align-middle">{{ $crew->email }}</td>
      <td class="align-middle">{{ $crew->role }}</td>
      <td class="align-middle" >{{ $crew->phone }}</td>
      <td class="align-middle">{{ $crew->address }}</td>
      <td class="align-middle">
        <a href="/dashboard/crew/{{ $crew->id }}" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Modify
            </button>
        </a>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal-{{ $crew->id }}">
          Delete
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal-{{ $crew->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel">Deletion Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">

                <h3 class="mb-4 fw-bold">You are about to delete {{ $crew->name }} record.</h3>

                <form action="/dashboard/crew/{{ $crew->id }}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                  <input type="hidden" name="delete-target" value="{{$crew->id}}">
                                  <button id="delete" type="submit" class="btn btn-outline-danger">Yes, Delete This</button>
                </form>

              </div>
            </div>
          </div>
        </div>

      </td>
    </tr>
    @endforeach
  </tbody>
</table>

</main>
@endsection