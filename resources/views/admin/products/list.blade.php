@extends('admin.layouts.app')

@section('customCss')
    <style>
        .variant-row {
            transition: height 0.3s ease, opacity 0.3s ease;
            /* Smooth transition for height and opacity */
            height: 0;
            /* Start with a height of 0 */
            opacity: 0;
            /* Start with full transparency */
            overflow: hidden;
            /* Prevent content overflow */
        }

        .variant-row.show {
            height: auto;
            /* Set to auto when visible */
            opacity: 1;
            /* Fully opaque when visible */
        }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
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
                            <button type="button" onclick="window.location.href='{{ route('products.index') }}'"
                                class="btn btn-default btn-sm">
                                Reset
                            </button>
                        </div>
                        <div class="card-tools">
                            <div class="input-group" style="width: 250px;">
                                <input type="text" value="{{ Request::get('keyword') }}" id="keyword" name="keyword"
                                    class="form-control float-right" placeholder="Search">

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
                                <th width="60" class="text-center">ID</th>
                                <th width="80"></th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>SKU</th>
                                <th width="100">Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($products->isNotEmpty())
                                @foreach ($products as $product)
                                    <!-- Main product row -->
                                    <tr>
                                        <td>
                                            <!-- Toggle button for variants -->
                                            <a href="javascript:void(0);" onclick="toggleVariants({{ $product->id }})"
                                                class="text-decoration-none text-gray-700 hover:text-blue-500 transition duration-200 pr-2">
                                                <i id="toggle-icon-{{ $product->id }}"
                                                    class="fas fa-chevron-right me-2"></i> <!-- Right arrow icon -->
                                            </a>
                                            {{ $product->id }}</>
                                        <td>
                                            @if ($product->product_images->isNotEmpty())
                                                {{-- Display the first image --}}
                                                <img src="{{ asset('uploads/product/small/' . $product->product_images->first()->image) }}"
                                                    class="img-thumbnail" width="50" alt="{{ $product->title }}">
                                            @else
                                                {{-- Display the default image --}}
                                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                    class="img-thumbnail" width="50" alt="Default Image">
                                            @endif
                                        </td>
                                        <td>{{ $product->title }}</td>
                                        <td>${{ $product->price }}</td>
                                        <td>{{ $product->qty }} left in stock</td>
                                        <td>SKU-{{ $product->sku }}</td>
                                        <td>
                                            @if ($product->status == 1)
                                                <svg class="text-success-500 h-6 w-6 text-success ml-2"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" aria-hidden="true" width="25"
                                                    height="25">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="text-danger h-6 w-6 ml-2" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" aria-hidden="true" width="25" height="25">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}">
                                                <svg class="filament-link-icon w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                                    width="30" height="30"
                                                    style="border-right: 1px solid #0066ff; margin-left: -5px;">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="#" onclick="deleteCategory({{ $product->id }})"
                                                class="text-danger w-6 h-6">
                                                <svg wire:loading.remove.delay="" wire:target=""
                                                    class="filament-link-icon w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                                    width="30" height="30"
                                                    style="border-right: 1px solid #0066ff; margin-left: -5px;">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('product.variants.create', $product->id) }}"
                                                class="text-info" title="Add Variant">
                                                <svg version="1.1" id="Layer_1" class="filament-link-icon w-6 h-6"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                    viewBox="0 0 122.87 122.88"
                                                    style="enable-background:new 0 0 122.87 122.88" xml:space="preserve">
                                                    <g>
                                                        <path
                                                            d="M33.24,40.86l27.67-9.21c0.33-0.11,0.68-0.1,0.98,0v0l28.03,9.6c0.69,0.23,1.11,0.9,1.04,1.6 c0.01,0.03,0.01,0.07,0.01,0.11v32.6h-0.01c0,0.56-0.31,1.11-0.85,1.38L62.28,91.08c-0.23,0.14-0.51,0.22-0.8,0.22 c-0.31,0-0.6-0.09-0.84-0.25l-27.9-14.55c-0.53-0.28-0.83-0.81-0.83-1.37h0V42.4C31.9,41.61,32.48,40.97,33.24,40.86L33.24,40.86 L33.24,40.86z M24.28,21.66l8.46,8.46c0.74,0.74,0.74,1.93,0,2.67c-0.73,0.73-1.93,0.73-2.66,0l-8.4-8.4l0.23,5.56 c0,0.05,0,0.11-0.02,0.16c-0.13,0.42-0.4,0.78-0.74,1.03c-0.34,0.25-0.75,0.4-1.2,0.4c-0.56,0.01-1.08-0.22-1.45-0.59 c-0.38-0.37-0.61-0.88-0.62-1.45c-0.16-3.2-0.49-6.78-0.49-9.93c0-0.64,0.22-1.18,0.61-1.56c0.38-0.37,0.9-0.59,1.52-0.6 c2.68-0.1,7.21,0.26,10,0.46c0.56,0.01,1.07,0.23,1.43,0.6c0.36,0.36,0.59,0.86,0.61,1.41v0.05c0,0.56-0.23,1.08-0.6,1.45 c-0.36,0.36-0.86,0.59-1.41,0.6l-0.04,0l0,0c-1.7,0-3.01-0.12-4.31-0.24L24.28,21.66L24.28,21.66z M7.04,59.58H19 c1.04,0,1.88,0.84,1.88,1.88s-0.84,1.88-1.88,1.88H7.12l4.1,3.77c0.04,0.04,0.07,0.08,0.1,0.13c0.2,0.39,0.27,0.83,0.2,1.25 c-0.06,0.41-0.25,0.81-0.57,1.13c-0.39,0.4-0.92,0.61-1.44,0.61c-0.53,0-1.06-0.19-1.46-0.59c-2.37-2.15-5.14-4.45-7.37-6.68 C0.22,62.52,0,61.99,0,61.45c0-0.53,0.22-1.05,0.65-1.49c1.82-1.97,5.29-4.91,7.4-6.74c0.4-0.39,0.92-0.59,1.44-0.59 c0.51,0,1.02,0.19,1.42,0.56l0.04,0.04c0.4,0.4,0.6,0.93,0.6,1.45c0,0.51-0.19,1.02-0.57,1.42l-0.02,0.03l0,0 c-1.2,1.21-2.21,2.04-3.22,2.87L7.04,59.58L7.04,59.58z M21.66,98.6l8.46-8.46c0.73-0.73,1.93-0.73,2.66,0 c0.74,0.74,0.74,1.93,0,2.67l-8.4,8.4l5.56-0.23c0.05,0,0.11,0.01,0.16,0.02c0.42,0.14,0.78,0.4,1.03,0.74 c0.25,0.34,0.4,0.75,0.4,1.2c0,0.56-0.22,1.08-0.59,1.45c-0.37,0.38-0.88,0.61-1.45,0.62c-3.2,0.16-6.78,0.49-9.94,0.49 c-0.64,0-1.18-0.22-1.56-0.6c-0.37-0.38-0.59-0.9-0.6-1.52c-0.11-2.68,0.26-7.21,0.46-10c0.01-0.56,0.23-1.07,0.6-1.43 c0.36-0.36,0.86-0.59,1.4-0.61h0.05c0.56,0,1.08,0.23,1.45,0.6c0.36,0.36,0.59,0.86,0.61,1.41l0,0.03l0,0 c0.01,1.71-0.12,3.01-0.24,4.31L21.66,98.6L21.66,98.6z M59.58,115.83v-11.96c0-1.04,0.84-1.88,1.88-1.88 c1.04,0,1.88,0.84,1.88,1.88v11.88l3.77-4.1c0.04-0.04,0.08-0.07,0.13-0.1c0.39-0.2,0.83-0.27,1.25-0.2 c0.41,0.06,0.81,0.25,1.13,0.57c0.4,0.39,0.61,0.92,0.61,1.45c0,0.53-0.19,1.06-0.59,1.46c-2.15,2.37-4.45,5.14-6.68,7.37 c-0.46,0.45-0.99,0.68-1.53,0.68c-0.53,0-1.05-0.22-1.49-0.65c-1.97-1.82-4.91-5.28-6.74-7.4c-0.39-0.4-0.59-0.92-0.59-1.44 c0-0.51,0.19-1.03,0.56-1.42l0.04-0.04c0.4-0.4,0.93-0.6,1.45-0.6c0.51,0,1.02,0.19,1.42,0.57l0.02,0.02l0,0 c1.21,1.2,2.04,2.21,2.87,3.22L59.58,115.83L59.58,115.83z M98.6,101.22l-8.46-8.46c-0.74-0.74-0.74-1.93,0-2.67 c0.73-0.73,1.93-0.73,2.66,0l8.4,8.4l-0.23-5.56c0-0.05,0-0.11,0.02-0.16c0.13-0.42,0.4-0.78,0.74-1.03c0.34-0.25,0.75-0.4,1.2-0.4 c0.56-0.01,1.08,0.22,1.45,0.59c0.38,0.37,0.61,0.88,0.62,1.45c0.16,3.2,0.49,6.78,0.49,9.94c0,0.64-0.22,1.18-0.61,1.56 c-0.38,0.37-0.9,0.59-1.52,0.6c-2.68,0.1-7.21-0.26-10-0.46c-0.56-0.01-1.07-0.23-1.43-0.6c-0.36-0.36-0.59-0.86-0.61-1.41v-0.05 c0-0.56,0.23-1.08,0.6-1.45c0.36-0.36,0.86-0.59,1.41-0.61l0.04,0l0,0c1.71-0.01,3.01,0.12,4.3,0.24L98.6,101.22L98.6,101.22z M115.84,63.29h-11.96c-1.04,0-1.89-0.84-1.89-1.88c0-1.04,0.85-1.88,1.89-1.88h11.88l-4.1-3.77c-0.04-0.04-0.07-0.08-0.1-0.13 c-0.2-0.39-0.27-0.83-0.2-1.25c0.06-0.41,0.25-0.81,0.57-1.13c0.4-0.4,0.92-0.61,1.45-0.61c0.53,0,1.06,0.19,1.46,0.59 c2.37,2.15,5.14,4.45,7.37,6.68c0.45,0.46,0.68,0.99,0.67,1.53c0,0.53-0.22,1.05-0.65,1.49c-1.82,1.97-5.29,4.91-7.4,6.74 c-0.4,0.39-0.92,0.59-1.44,0.59c-0.51,0-1.03-0.19-1.42-0.56l-0.04-0.04c-0.4-0.4-0.6-0.93-0.6-1.45c0-0.51,0.19-1.03,0.57-1.42 l0.02-0.03l0,0c1.2-1.21,2.21-2.04,3.22-2.87L115.84,63.29L115.84,63.29z M101.21,24.28l-8.46,8.46c-0.73,0.73-1.93,0.73-2.66,0 c-0.74-0.74-0.74-1.93,0-2.66l8.4-8.4l-5.56,0.23c-0.05,0-0.11-0.01-0.16-0.02c-0.42-0.14-0.78-0.4-1.03-0.74 c-0.25-0.34-0.4-0.75-0.4-1.2c0-0.56,0.22-1.08,0.59-1.45c0.37-0.38,0.88-0.61,1.45-0.62c3.2-0.16,6.78-0.49,9.94-0.49 c0.64,0,1.18,0.22,1.56,0.6c0.37,0.38,0.59,0.9,0.6,1.52c0.11,2.68-0.26,7.21-0.46,10c-0.01,0.56-0.23,1.07-0.6,1.44 c-0.36,0.36-0.86,0.59-1.41,0.61h-0.05c-0.56,0-1.08-0.23-1.45-0.6c-0.36-0.36-0.59-0.86-0.61-1.41l0-0.03l0,0 c0-1.71,0.12-3.01,0.24-4.31L101.21,24.28L101.21,24.28z M63.29,7.04V19c0,1.04-0.84,1.88-1.88,1.88c-1.04,0-1.89-0.84-1.89-1.88 V7.13l-3.76,4.09c-0.04,0.04-0.08,0.07-0.13,0.1c-0.39,0.2-0.83,0.27-1.25,0.2c-0.41-0.06-0.81-0.25-1.13-0.57 c-0.4-0.39-0.61-0.92-0.61-1.44c0-0.53,0.19-1.06,0.59-1.46c2.15-2.37,4.45-5.14,6.68-7.37C60.35,0.22,60.89,0,61.43,0 c0.53,0,1.05,0.22,1.49,0.65c1.97,1.82,4.91,5.28,6.74,7.4c0.39,0.4,0.59,0.92,0.59,1.44c0,0.51-0.19,1.02-0.56,1.42l-0.04,0.04 c-0.4,0.4-0.93,0.6-1.45,0.6c-0.51,0-1.02-0.19-1.42-0.57l-0.03-0.02l0,0c-1.21-1.2-2.04-2.21-2.87-3.22L63.29,7.04L63.29,7.04z M39.36,64.75c0-0.59,0.48-1.08,1.08-1.08c0.59,0,1.08,0.48,1.08,1.08v4.39c0,0.03,0,0.07-0.01,0.11c0,0.15,0.02,0.27,0.05,0.37 c0.02,0.03,0.03,0.06,0.06,0.08l2.69,1.25c0.54,0.25,0.77,0.89,0.53,1.43c-0.25,0.54-0.88,0.77-1.42,0.53l-2.75-1.28 c-0.05-0.02-0.1-0.04-0.14-0.08c-0.44-0.28-0.75-0.65-0.94-1.11c-0.15-0.37-0.22-0.78-0.21-1.22v-0.07L39.36,64.75L39.36,64.75 L39.36,64.75z M59.93,87.21V56.02L35,44.72v29.48L59.93,87.21L59.93,87.21L59.93,87.21z M87.86,45.09L63.03,56.04v31.2l24.83-12.62 V45.09L87.86,45.09L87.86,45.09z M61.38,34.74l-23.57,7.85l23.68,10.74L85.17,42.9L61.38,34.74L61.38,34.74L61.38,34.74z" />
                                                    </g>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr id="variants-{{ $product->id }}" class="variant-row" style="display: none;">
                                        <td colspan="9">
                                            <div class="variant-details mt-2 p-3 border rounded shadow-sm bg-light">
                                                <strong>Variants:</strong>
                                                <div class="row">
                                                    @if ($product->variants->isNotEmpty())
                                                        @foreach ($product->variants as $variant)
                                                            <div class="col-md-4 mb-2" id="variant-{{ $variant->id }}">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <!-- Variant Image -->
                                                                        <div>
                                                                            @foreach ($variant->images as $image)
                                                                                <img src="{{ asset('uploads/product/large/variants/' . $image->image) }}"
                                                                                    alt="Variant Image"
                                                                                    class="img-fluid rounded"
                                                                                    style="max-width: 100px;">
                                                                            @endforeach

                                                                            {{-- Add a placeholder if no image exists --}}
                                                                            @if ($variant->images->isEmpty())
                                                                                <img src="{{ asset('path/to/placeholder.jpg') }}"
                                                                                    alt="Placeholder Image"
                                                                                    class="img-fluid rounded"
                                                                                    style="max-width: 100px;">
                                                                            @endif
                                                                        </div>
                                                                        <!-- Variant Details -->
                                                                        <h5 class="card-title my-2">
                                                                            SKU: <strong>{{ $variant->sku }}</strong>
                                                                        </h5>
                                                                        <p class="card-text">
                                                                            Color:
                                                                            <strong>{{ $variant->color->name ?? 'N/A' }}</strong><br>
                                                                            Size:
                                                                            <strong>{{ $variant->size->name ?? 'N/A' }}</strong><br>
                                                                            Quantity:
                                                                            <strong>{{ $variant->qty }}</strong><br>
                                                                            Price Adjustment:
                                                                            <strong>${{ number_format($variant->price_adjustment, 2) }}</strong>
                                                                        </p>

                                                                        <!-- Edit and Delete Buttons -->
                                                                        <div class="d-flex justify-content-between">
                                                                            <!-- Edit Link -->
                                                                            <a href="{{ route('product.variants.edit', [$product->id, $variant->id]) }}"
                                                                                class="btn btn-outline-primary btn-sm px-4 py-2">
                                                                                Edit
                                                                            </a>


                                                                            <!-- Delete Button (with Modal) -->
                                                                            <!-- Delete button -->
                                                                            <button
                                                                                class="btn btn-danger btn-sm px-4 py-2"onclick="deleteVariant({{ $product->id }}, {{ $variant->id }})">
                                                                                Delete
                                                                            </button>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="px-4">No Variants</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        function deleteVariant(productId, variantId) {
            // Construct the URL dynamically with both productId and variantId
            var url =
                '{{ route('product.variants.destroy', ['productId' => ':productId', 'variantId' => ':variantId']) }}';
            newUrl = url.replace(':productId', productId).replace(':variantId', variantId);

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
                        url: newUrl,
                        type: 'delete',
                        data: {},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response["status"] == true) {
                                window.location.href = "{{ route('products.index') }}"
                            } else {
                                window.location.href = "{{ route('products.index') }}"
                            }
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire("deletion cancel  ", "", "error");
                }
            });
        }
    </script>
    <script>
        function deleteCategory(id) {
            var url = '{{ route('products.delete', 'ID') }}'
            var newUrl = url.replace("ID", id)

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
                        url: newUrl,
                        type: 'delete',
                        data: {},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response["status"] == true) {
                                window.location.href = "{{ route('products.index') }}"
                            } else {
                                window.location.href = "{{ route('products.index') }}"
                            }
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire("deletion cancel  ", "", "error");
                }
            });
        }
    </script>
    <script>
        function toggleVariants(productId) {
            const variantRow = document.getElementById(`variants-${productId}`);
            const toggleIcon = document.getElementById(`toggle-icon-${productId}`);

            // Toggle visibility with animation
            const isVisible = variantRow.classList.contains('show');
            if (isVisible) {
                variantRow.classList.remove('show');
                setTimeout(() => {
                    variantRow.style.display = 'none'; // Set display to none after transition
                }, 300); // Match the timeout with the CSS transition duration
            } else {
                variantRow.style.display = 'table-row'; // Show the row
                setTimeout(() => {
                    variantRow.classList.add('show'); // Add class to trigger transition
                }, 10); // Short timeout to allow display change
            }

            // Change icon direction
            toggleIcon.classList.toggle('fa-chevron-right', isVisible);
            toggleIcon.classList.toggle('fa-chevron-down', !isVisible);
        }
    </script>
@endsection
