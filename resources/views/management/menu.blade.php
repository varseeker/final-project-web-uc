@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')
    <div class="my-3 me-5 d-flex justify-content-start">
      <h1>Menu Management</h1>
    </div>
    <div class="my-3 me-5 d-flex justify-content-end">
      <a href="/dashboard/menu/new" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Insert new menu
            </button>
            
    </a>
    </div>

    <table class="table table-striped">
  <thead class="table-dark">
    <tr>
      <th scope="col">Menu ID</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col">Category</th>
      <th scope="col">Most Ordered</th>
      <th scope="col">Image</th>
      <th scope="col">Manage</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($menuItems as $item)
    <tr>
      <th scope="row" class="align-middle">{{ $item->id }}</th>
      <td class="align-middle">{{ $item->name }}</td>
      <td class="align-middle">{{ $item->description }}</td>
      <td class="align-middle">{{ $item->category }}</td>
      <td class="align-middle" style="align-middle">{{ $item->most_ordered }}</td>
      <td class="align-middle"
      ><img class="image-fluid" style="max-width: 150px; max-height:250px;" src="
        ../{{ $item->img_url }}" alt="">
    </td>
      <td class="align-middle">
        <a href="/dashboard/menu/{{ $item->id }}" class="text-white align-middle">
            <button  class="btn btn-outline-secondary">
                Modify
            </button>
        </a>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal-{{ $item->id }}">
          Delete
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal-{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel">Deletion Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">

                <h3 class="mb-4 fw-bold">You are about to delete {{ $item->name }} data.</h3>

                <form action="/dashboard/menu/{{ $item->id }}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                  <input type="hidden" name="delete-target" value="{{$item->id}}">
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