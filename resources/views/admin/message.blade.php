@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4> {{Session::get('error')}}
</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-check"></i> Success!</h4>{{Session::get('success')}}
    Success alert preview. This alert is dismissable.
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(Session::has('create-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{Session::get('create-success')}}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif
@if(Session::has('update-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{Session::get('update-success')}}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif

@if(Session::has('delete-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{Session::get('delete-success')}}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif

@if(Session::has('not-found'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            position: "top-end",
            icon: "error",
            title: "{{Session::get('not-found')}}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
@endif
