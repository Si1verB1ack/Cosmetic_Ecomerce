<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>

@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" aria-label="Close" style="position: absolute; right: 15px;" onclick="this.closest('.alert').remove();">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="alert-heading"><i class="icon fa fa-ban"></i> Error!</h4>
        <p>{{ Session::get('error') }}</p>
    </div>
@endif




@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4><i class="icon fa fa-check"></i> Success!</h4>{{ Session::get('success') }}
    </div>
@endif

@if (Session::has('create-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{ Session::get('create-success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif
@if (Session::has('update-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{ Session::get('update-success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif

@if (Session::has('delete-success'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "{{ Session::get('delete-success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    {{-- {{Session::get('create-success')}} --}}
@endif

@if (Session::has('not-found'))
    <script>
        Swal.fire({
            position: "top-end",
            icon: "error",
            title: "{{ Session::get('not-found') }}",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
@endif

@if (Session::has('login-success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: "{{ Session::get('login-success') }}"
        });
    </script>
@endif

@if (Session::has('login-failed'))
    <script>
        Swal.fire({
            title: "{{ Session::get('login-failed') }}",
            icon: "error"
        });
    </script>
@endif
