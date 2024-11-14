@extends('front.layouts.app')

@section('customCss')
    <style>
        .color-options {
            display: flex;
        }

        .color-option {
            width: 30px;
            height: 30px;
            margin-right: 5px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .color-option:hover {
            border-color: #333;
        }

        .size-option {
            text-decoration: none;
            border: 1px solid;
            color: #000000;
            font-weight: bold;
            padding: 5px 10px;
            display: inline-block;
            margin-right: 10px;
        }

        .size-option:hover {
            color: #677380;
        }

        .size-option.disabled {
            color: #ccc;
            pointer-events: none;
            cursor: not-allowed;
        }

        .out-of-stock {
            font-size: 12px;
            color: red;
            margin-left: 5px;
            font-weight: normal;
        }
    </style>
@endsection
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item"><a class="text-black-50"
                            href="{{ route('front.product', $product->slug) }}">{{ $product->title }}</a></li>

                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                @include('admin.message')
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light">
                            @if (!$product_variant)
                                @foreach ($product->product_images as $key => $productImage)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img class="w-100 h-100"
                                            src="{{ asset('uploads/product/large/' . $productImage->image) }}"
                                            alt="Image" onload="this.style.opacity='1'; this.style.transform='scale(1)';"
                                            style="opacity: 0; transform: scale(1.05); transition: opacity 1.5s ease-in-out, transform 2s ease-in-out;">
                                        <!-- Smooth scaling -->
                                    </div>
                                @endforeach
                            @else
                                <div class="carousel-item active">
                                    <img class="w-100 h-100"
                                        src="{{ asset('uploads/product/large/variants/' . $variantImages->image) }}"
                                        alt="{{ $variantImages->image }}"
                                        onload="this.style.opacity='1'; this.style.transform='scale(1)';"
                                        style="opacity: 0; transform: scale(1.05); transition: opacity 1.5s ease-in-out, transform 2s ease-in-out;">
                                    <!-- Smooth scaling -->
                                </div>
                            @endif
                        </div>
                        {{-- <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a> --}}
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="bg-light right">

                        <h1>{{ $product->title }}</h1>

                        <h6>Available in stock:
                            {{ $product_variant ? $product_variant->qty : $product->qty }}
                        </h6>

                        {{-- <h6>avavabile in stock: {{ $product->qty }}</h6> --}}


                        <div class="product-options mt-2">
                            <div class="color-options">
                                @php
                                    $shownColors = [];
                                @endphp

                                @foreach ($product->variants as $variant)
                                    @if (!in_array($variant->color->name, $shownColors))
                                        <a href="{{ request()->has('size') ? request()->fullUrlWithQuery(['color' => $variant->size->id]) : route('product.variant.details', ['productId' => $product->id, 'slug' => $product->slug, 'variant_id' => $variant->id, 'color' => $variant->color->id]) }}"
                                            class="color-option" style="background-color: {{ $variant->color->name }}"
                                            title="{{ $variant->color->name }}">
                                        </a>

                                        @php
                                            $shownColors[] = $variant->color->name;
                                        @endphp
                                    @endif
                                @endforeach
                            </div>


                            @php
                                // Initialize $shownSizes as an empty array to avoid the undefined variable error
                                $shownSizes = [];
                            @endphp

                            <!-- Size Options -->

                            <div class="size-options mt-2 mb-2">
                                <div class="size-options-list">
                                    @foreach ($product->variants as $variant)
                                        @if (!in_array($variant->size, $shownSizes))
                                            <a href="{{ request()->has('color') ? request()->fullUrlWithQuery(['size' => $variant->size->id]) : route('product.variant.details', ['productId' => $product->id, 'slug' => $product->slug, 'variant_id' => $variant->id, 'size' => $variant->size->id]) }}"
                                                class="size-option {{ !$variant->qty ? 'disabled' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                data-variant-id="{{ $variant->id }}"
                                                data-size="{{ $variant->size->name }}"
                                                {{ $variant->qty ? '' : 'aria-disabled="true"' }}>

                                                @if ($variant->qty)
                                                    {{ $variant->size->name }}
                                                @endif

                                                @if (!$variant->qty)
                                                    <span class="out-of-stock">Out of Stock</span>
                                                @endif
                                            </a>

                                            @php
                                                $shownSizes[] = $variant->size;
                                            @endphp
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                        </div>


                        <div class="d-flex mb-3">
                            {{-- <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div> --}}
                            <div class="star-rating mt-2" title="">
                                <div class="back-stars">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>

                                    <div class="front-stars" style="width: {{ $avgRatingPercentage }}%">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <small
                                class="pt-2 ps-2">({{ $product->product_ratings_count > 1 ? $product->product_ratings_count . ' Reviews' : $product->product_ratings_count . ' Review' }})</small>
                        </div>

                        @if ($product->compare_price > 0)
                            <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        @endif

                        <p>
                            {!! $product_variant
                                ? '<div class="price" style="display: inline;">$' .
                                    ($product_variant->price_adjustment + $product->price) .
                                    '</div>' .
                                    ' <span class="adjusted-price" style="display: inline; font-size: medium;">(Adjusted Price: $' .
                                    $product_variant->price_adjustment .
                                    ')</span>'
                                : '<div class="price" style="display: inline;">$' . $product->price . '</div>' !!}
                        </p>


                        {!! $product->short_description !!}
                        {{-- {{$product->id}} --}}
                        @if ($product->track_qty == 'Yes')
                            @if (!$product_variant)
                                @if ($product->qty > 0)
                                    <a class="btn btn-dark" href="javascript:void(0);"
                                        onclick="addToCart({{ $product->id }}{{ $product_variant ? ', ' . $product_variant->id : '' }});">
                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                    </a>
                                @else
                                    <a class="btn btn-dark" href="javascript:void(0);">
                                        Out Of Stock
                                    </a>
                                @endif
                            @else
                                @if ($product_variant->qty > 0)
                                    <a class="btn btn-dark" href="javascript:void(0);"
                                        onclick="addToCart({{ $product->id }}{{ $product_variant ? ', ' . $product_variant->id : '' }});">
                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                    </a>
                                @else
                                    <a class="btn btn-dark" href="javascript:void(0);">
                                        Out Of Stock
                                    </a>
                                @endif
                            @endif
                        @else
                            <a class="btn btn-dark" href="javascript:void(0);"
                                onclick="addToCart({{ $product->id }}{{ $product_variant ? ', ' . $product_variant->id : '' }});">
                                <i class="fa fa-shopping-cart"></i> Add To Cart
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab"
                                    aria-controls="description" aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab"
                                    data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping"
                                    aria-selected="false">Shipping
                                    &
                                    Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                                    type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                {!! $product->description !!}
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->shipping_returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="col-md-8">
                                    <div class="row">
                                        <form action="" name="productRatingForm" id="productRatingForm"
                                            method="post">
                                            <input type="hidden" name="id" id="id"
                                                value="{{ $product->id }}">
                                            <h3 class="h4 pb-3">Write a Review</h3>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="Name">
                                                <p></p>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="Email">
                                                <p></p>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="rating">Rating</label>
                                                <br>
                                                <div class="rating" style="width: 10rem">
                                                    <input id="rating-5" type="radio" name="rating"
                                                        value="5" /><label for="rating-5"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-4" type="radio" name="rating"
                                                        value="4" /><label for="rating-4"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-3" type="radio" name="rating"
                                                        value="3" /><label for="rating-3"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-2" type="radio" name="rating"
                                                        value="2" /><label for="rating-2"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-1" type="radio" name="rating"
                                                        value="1" /><label for="rating-1"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                </div>
                                                <p class="product-rating-error text-danger" style="font-size: 14px"></p>

                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="">How was your overall experience?</label>
                                                <textarea name="comment" id="comment" class="form-control" cols="30" rows="10"
                                                    placeholder="How was your overall experience?"></textarea>
                                                <p></p>
                                            </div>
                                            <div>
                                                <button class="btn btn-dark" type="submit">Submit</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <div class="overall-rating mb-3">
                                        <div class="d-flex">
                                            <h1 class="h3 pe-3">{{ $avgRating }}</h1>
                                            <div class="star-rating mt-2" title="">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: {{ $avgRatingPercentage }}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pt-2 ps-2">
                                                ({{ $product->product_ratings_count > 1 ? $product->product_ratings_count . ' Reviews' : $product->product_ratings_count . ' Review' }})
                                            </div>
                                        </div>

                                    </div>
                                    @if ($product->product_ratings->isNotEmpty())
                                        @foreach ($product->product_ratings as $rating)
                                            @php
                                                $ratingPercent = ($rating->rating * 100) / 5;
                                            @endphp
                                            <div class="rating-group mb-4">
                                                <span> <strong>{{ $rating->username }} </strong></span>
                                                <div class="star-rating mt-2" title="70%">
                                                    <div class="back-stars">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>

                                                        <div class="front-stars" style="width: {{ $ratingPercent }}%">
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                            <i class="fa fa-star" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="my-3">
                                                    <p>
                                                        {{ $rating->comment }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Related Products</h2>
            </div>
            <div class="col-md-12">
                <div id="related-products" class="carousel">
                    @if (!empty($relatedProucts))
                        @foreach ($relatedProucts as $relProduct)
                            @php
                                $productImage = $relProduct->product_images->first();
                            @endphp
                            <div class="card product-card">
                                <div class="product-image position-relative">
                                    <a href="{{ route('front.product', $relProduct->slug) }}" class="product-img">
                                        @if (!empty($productImage->image))
                                            <img class="card-img-top"
                                                src="{{ asset('uploads/product/small/' . $productImage->image) }}">
                                        @else
                                            <img src="{{ asset('admin-assets/img/default-150x150.png') }}">
                                        @endif
                                    </a>

                                    <a class="whishlist" href="222"><i class="far fa-heart"></i></a>

                                    <div class="product-action">
                                        @if ($relProduct->track_qty == 'Yes')
                                            @if ($relProduct->qty > 0)
                                                <a class="btn btn-dark" href="javascript:void(0);"
                                                    onclick="addToCart({{ $product->id }}{{ $product_variant ? ', ' . $product_variant->id : '' }});">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            @else
                                                <a class="btn btn-dark" href="javascript:void(0);">
                                                    Out Of Stock
                                                </a>
                                            @endif
                                        @else
                                            <a class="btn btn-dark" href="javascript:void(0);"
                                                onclick="addToCart({{ $product->id }}{{ $product_variant ? ', ' . $product_variant->id : '' }});">
                                                <i class="fa fa-shopping-cart"></i> Add To Cart
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="">{{ $relProduct->title }}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>{{ $relProduct->price }}</strong></span>
                                        @if ($product->compare_price > 0)
                                            <span
                                                class="h6 text-underline"><del>${{ $product->compare_price }}</del></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script type="text/javascript">
        $("#productRatingForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('front.saveRating') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        window.location.href = "{{ route('front.product', $product->slug) }}"
                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#comment").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    } else {
                        var errors = response['errors']
                        if (errors['name']) {
                            $("#name").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $("#name").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['email']) {
                            $("#email").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['email']);
                        } else {
                            $("#email").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['comment']) {
                            $("#comment").addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback').html(errors['comment']);
                        } else {
                            $("#comment").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['rating']) {
                            $(".product-rating-error").html(errors['rating']);
                        } else {
                            $(".product-rating-error").html("");
                        }
                    }

                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                }
            });
        });
    </script>
@endsection
