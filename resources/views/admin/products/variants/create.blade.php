@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Product Variant</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="{{ route('product.variants.store', $product->id) }}" method="post" id="variantForm" name="variantForm"
            enctype="multipart/form-data">
            @csrf
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Combined Variant, Pricing, and Inventory Information -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Variant Details</h2>
                                <div class="row">
                                    <!-- Color Selection -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="color_id">Color</label>
                                            <select name="color_id" id="color_id" class="form-control">
                                                <option value="">Select a Color</option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}"
                                                        {{ old('color_id') == $color->id ? 'selected' : '' }}>
                                                        {{ $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-danger">
                                                @error('color_id')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Size Selection -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="size_id">Size</label>
                                            <select name="size_id" id="size_id" class="form-control">
                                                <option value="">Select a Size</option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}"
                                                        {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                                        {{ $size->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-danger">
                                                @error('size_id')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Price Adjustment -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price_adjustment">Price Adjustment</label>
                                            <input type="number" name="price_adjustment" id="price_adjustment"
                                                class="form-control" placeholder="Price Adjustment"
                                                value="{{ old('price_adjustment') }}">
                                            <p class="text-danger">
                                                @error('price_adjustment')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>
                                    </div>

                                    <!-- SKU -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU</label>
                                            <input type="text" name="sku" id="sku" class="form-control"
                                                placeholder="SKU" value="{{ old('sku') }}">
                                            <p class="text-danger">
                                                @error('sku')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="qty">Quantity</label>
                                            <input type="number" min="0" name="qty" id="qty"
                                                class="form-control" placeholder="Quantity" value="{{ old('qty') }}">
                                            <p class="text-danger">
                                                @error('qty')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Dropzone for Image Upload -->
                                    <div class="col-md-12">
                                        <h2 class="h4 mb-3">Media</h2>
                                        <input type="hidden" name="image_id" id="image_id">
                                        <div id="image" class="dropzone dz-clickable">
                                            <div class="dz-message needsclick">
                                                <br>Drop files here or click to upload.<br><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Add Variant</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                // Set the image ID in the hidden input after successful upload
                $("#image_id").val(response.image_id);
                console.log(response);
            },
            maxfilesexceeded: function(file) {
                // Remove the previous file if maxFiles limit is reached
                if (this.files[1] != null) {
                    this.removeFile(this.files[0]); // Remove the first file
                }
                this.addFile(file); // Add the new file
            }
        });
    </script>
@endsection
