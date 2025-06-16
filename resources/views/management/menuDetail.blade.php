@extends('layouts.app')

@section('content')
<!-- Main Content -->
<main class="container py-4">
@include('layouts.goBack')

    <h1 class="mt-3">Modifying Menu</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
        <div class="border border-dark rounded-3 mb-3" style="max-width: 1200px;">
            <div class="row g-0">
                    <div class="col-md-8">
                    <div class="" style="padding: 20px 30px;">

    @foreach ($menuItems as $item)
    <form method="POST" action="/dashboard/menu/{{ $item->id }}"  enctype="multipart/form-data">
      @method('put')
      @csrf

    <div class="input-group flex-nowrap">

      <div class="flex-fill me-4">
        <label for="exampleInputEmail1" class="form-label">Menu ID</label>
      <input class="form-control  mb-2" id="disabledInput" type="text" placeholder="{{ $item->id }}" disabled>

      </div>

      <div class="flex-fill"> 

        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Menu name</label>
          <input type="text" class="form-control" name="name" value="{{ $item->name }}">
          <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
        </div>
          
      </div>

    </div>


    <div class="mb-3">
      <label for="exampleFormControlTextarea1" class="form-label">Menu Description</label>
      <textarea class="form-control" name="description" rows="3">{{ $item->description }}</textarea>
    </div>


    <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">Rp</span>
      <input id="dengan-rupiah" type="text" class="form-control" value="{{ number_format( $item->price, 0, '','.') }}" name="price" aria-describedby="basic-addon1">
    </div>

    <div class="input-group flex-nowrap">

      <div class="input-group me-4">
        <label class="input-group-text" for="inputGroupSelect01">Menu Category</label>
        <select class="form-select" id="inputGroupSelect01" name="category">
          <option selected>{{ $item->category }} / Click to change</option>
          <option value="Coffee">Coffee</option>
          <option value="Non-Coffee">Non-Coffee</option>
          <option value="Snack">Snack</option>
        </select>
      </div>

      <div class="input-group">
        <div class="input-group-text">
          <input class="form-check-input mt-0" type="checkbox" value="1" name="most_ordered" aria-label="Checkbox for following text input">
        </div>
        <input type="text" class="form-control" value="This menu is most ordered." aria-label="This menu is most ordered." disabled>
      </div>

    </div>

    <div class="my-2">
      <label for="formFile" class="form-label">Upload replacement image.</label>
      <input class="form-control" type="file" id="formFile" name="gambar">
        <div class="form-text ps-2">
          The image should not be over 1mb <br> Image ratio is recomended to be 3:2 <br> Image size recomended is 295px x 210px
        </div>
    </div>

    
                    <div class="form-check">
                        <div class="d-flex flex-row-reverse py-4" >
                            <button type="submit" class="btn btn-outline-success">Update Menu</button>
                        </div>
                    </div>
</form>
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

<!-- <script>
                $(document).ready(function(){
                /* Dengan Rupiah */
                var dengan_rupiah = document.getElementById('dengan-rupiah');

                dengan_rupiah.addEventListener('keyup', function(e)
                {
                    dengan_rupiah.value = formatRupiah(this.value, 'Rp. ');
                });

                /* Fungsi */
                function formatRupiah(angka, prefix)
                {
                    var number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split    = number_string.split(','),
                        sisa     = split[0].length % 3,
                        rupiah     = split[0].substr(0, sisa),
                        ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
                        
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
                }

            });
            </script> -->
<script>
                $(document).ready(function(){
                /* Dengan Rupiah */
                var dengan_rupiah = document.getElementById('dengan-rupiah');

                dengan_rupiah.addEventListener('keyup', function(e)
                {
                    dengan_rupiah.value = formatRupiah(this.value, '');
                });

                /* Fungsi */
                function formatRupiah(angka, prefix)
                {
                    var number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split    = number_string.split(','),
                        sisa     = split[0].length % 3,
                        rupiah     = split[0].substr(0, sisa),
                        ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
                        
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
                }

            });
            </script>

</main>
@endsection