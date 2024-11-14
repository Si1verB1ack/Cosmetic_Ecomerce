@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products Rating</h1>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <div class="card">
                <form action="" method="get">
                    <div class="card-header">
                        <div class="card-title">
                            <button type="button" onclick="window.location.href='{{route('products.productRatings')}}'" class="btn btn-default btn-sm">
                                Reset
                            </button>
                        </div>
                        <div class="card-tools">
                            <div class="input-group input-group" style="width: 250px;">
                                <input type="text" value="{{Request::get('keyword')}}" id="keyword" name="keyword" class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Title</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Rated By</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($ratings->isNotEmpty())
                                @foreach ($ratings as $rating)
                                    <tr>
                                        <td>{{$rating->id}}</td>
                                        <td>{{$rating->ProductTitle}}</td>
                                        <td>${{$rating->rating}}</td>
                                        <td>{{$rating->comment}}</td>
                                        <td>{{$rating->username}}</td>
                                        <td>
                                            @if ($rating->status==1)
                                            <a href="javascript:void(0);" onclick="changeStatus(0,'{{$rating->id}}');">
                                            <svg class="text-success-500 h-6 w-6 text-success ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"  width="25" height="25">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <a href="javascript:void(0);" onclick="changeStatus(1,'{{$rating->id}}');">
                                            <svg class="text-danger h-6 w-6 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" width="25" height="25">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{$ratings->links()}}
                    {{-- <ul class="pagination pagination m-0 float-right">
                        <li class="page-item"><a class="page-link" href="#">«</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">»</a></li>
                    </ul> --}}
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        function changeStatus(status,id) {

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showDenyButton: true,
                // showCancelButton: true,
                confirmButtonText: "Delete",
                denyButtonText: `Cancel`,
                backdrop: `
                    rgba(0,0,123,0.4)
                    url('{{ asset('img/nyan-cat.gif') }}')
                    left top
                    no-repeat
                `
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Swal.fire("Saved!", "", "success");
                    $.ajax({
                        url: '{{ route("products.changeRatingStatus") }}',
                        type: 'get',
                        data: {status:status,id:id},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response["status"]==true) {
                                window.location.href = "{{ route('products.productRatings') }}"
                            }else{
                                window.location.href = "{{ route('products.productRatings') }}"
                            }
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire("deletion cancel  ", "", "error");
                }
            });
        }
    </script>
@endsection
