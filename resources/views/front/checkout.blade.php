@extends('front.layouts.app')

@section('customCss')
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency=USD&intent=capture"
        data-sdk-integration-source="sandbox"></script>
@endsection

@section('content')

    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="{{ route('front.home') }}" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">
        <div class="container">
            @include('admin.message')
            <form id="orderForm" name="orderForm" action="" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="sub-title">
                            <h2>Shipping Address</h2>
                        </div>
                        <div class="card shadow-lg border-0">
                            <div class="card-body checkout-form">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="first_name" id="first_name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}"
                                                class="form-control" placeholder="First Name">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="last_name" id="last_name" class="form-control"
                                                placeholder="Last Name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->last_name : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="email" id="email" class="form-control"
                                                placeholder="Email"
                                                value="{{ !empty($customerAddress) ? $customerAddress->email : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select a Country</option>
                                                @if ($countries->isNotEmpty())
                                                    @foreach ($countries as $country)
                                                        <option
                                                            {{ !empty($customerAddress) && $customerAddress->country_id == $country->id ? 'selected' : '' }}
                                                            value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ !empty($customerAddress) ? $customerAddress->address : '' }}</textarea>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="apartment" id="apartment" class="form-control"
                                                placeholder="Apartment, suite, unit, etc. (optional)"
                                                value="{{ !empty($customerAddress) ? $customerAddress->apartment : '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="city" id="city" class="form-control"
                                                placeholder="City"
                                                value="{{ !empty($customerAddress) ? $customerAddress->city : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="state" id="state" class="form-control"
                                                placeholder="State"
                                                value="{{ !empty($customerAddress) ? $customerAddress->state : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="zip" id="zip" class="form-control"
                                                placeholder="Zip"
                                                value="{{ !empty($customerAddress) ? $customerAddress->zip : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                placeholder="Mobile No."
                                                value="{{ !empty($customerAddress) ? $customerAddress->mobile : '' }}">
                                            <p></p>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="notes" id="notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                class="form-control">{{ !empty($customerAddress) ? $customerAddress->notes : '' }}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-title">
                            <h2>Order Summery</h3>
                        </div>
                        <div class="card cart-summery">
                            <div class="card-body">
                                @foreach (Cart::content() as $item)
                                    <div class="d-flex justify-content-between pb-2">
                                        <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                        <div class="h6">${{ $item->price * $item->qty }}</div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Subtotal</strong></div>
                                    <div class="h6"><strong>${{ Cart::subtotal() }}</strong></div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Discount</strong></div>
                                    <div class="h6" id="discount_value"><strong> ${{ $discount }} </strong></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="h6"><strong>Shipping</strong></div>
                                    <div class="h6"><strong
                                            id="shippingAmount">${{ number_format($totalShippingCharge, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5"><strong
                                            id="grandTotal">${{ number_format($grandTotal, 2) }}</strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group apply-coupan mt-4">
                            <input type="text" placeholder="Coupon Code" class="form-control" name="discount_code"
                                id="discount_code">
                            <button class="btn btn-dark" type="button" id="apply-discount">Apply Coupon</button>
                        </div>
                        <div id="discount-response-wrapper">
                            @if (Session::has('code'))
                                <div class="mt-4" id="discount-response">
                                    <strong>{{ Session::get('code')->code }}</strong>
                                    <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                                </div>
                            @endif
                        </div>
                        <div class="card payment-form ">
                            <input type="hidden" name="selected_payment_method" id="selected_payment_method"
                                value="1">


                            <h3 class="card-title h5 mb-3">Payment Method</h3>
                            <div class="form-check">
                                <input type="radio" checked name="payment_method" value="cod"
                                    id="payment_method_one">
                                <label for="payment_method_one" class="form-check-label">COD</label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="cod" id="payment_method_two">
                                <label for="payment_method_two" class="form-check-label">Stripe</label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="cod" id="payment_method_three">
                                <label for="payment_method_three" class="form-check-label">Paypal</label>
                            </div>

                            <!-- Stripe Payment Form Section -->
                            <div class="card-body p-0 d-none mt-3" id="stripe-payment-form">
                                <div class="mb-3">
                                    <div id="checkout">
                                        <!-- Checkout will insert the payment form here -->
                                    </div>
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="card_number" class="mb-2">Card Number</label>
                                    <input type="text" name="card_number" id="card_number"
                                        placeholder="Valid Card Number" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="mb-2">Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cvv_code" class="mb-2">CVV Code</label>
                                        <input type="text" name="cvv_code" id="cvv_code" placeholder="123"
                                            class="form-control">
                                    </div>
                                </div> --}}
                            </div>

                            <!-- PayPal Payment Form Section -->
                            <div class="card-body p-0 d-none mt-3" id="paypal-payment-form">
                                <div class="mb-3">
                                    <div class="row mt-3">
                                        <div class="col-12 col-lg-6 offset-lg-3">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" id="paypal-amount"
                                                    value="{{ number_format($grandTotal, 2) }}" aria-label="Amount">

                                                <span class="input-group-text">.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div id="payment_options"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="pt-4">
                                {{-- <a href="#" class="btn-dark btn btn-block w-100">Pay Now</a> --}}
                                <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                            </div>
                        </div>


                        <!-- CREDIT CARD FORM ENDS HERE -->

                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('customJs')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location);
            const status = "{{ session('status') }}";
            console.log(status);

            if (status === 'success') {
                Swal.fire({
                    title: "Transaction Complete",
                    text: "Thank you for purchasing!",
                    icon: "success"
                }).then(() => {
                    // Submit the form after the alert is confirmed
                    $("#selected_payment_method").val("2");
                    $("#orderForm").submit();
                });
            } else if (status === 'failed') {
                Swal.fire({
                    title: "Cancelled",
                    text: "Your Payment was cancelled.",
                    icon: "error"
                });
            }

            // Remove the status parameter after displaying the message
            // if (status) {
            //     url.searchParams.delete('status');
            //     window.history.replaceState({}, document.title, url.toString());
            // }
        });

        const cartItems = {!! json_encode(
            Cart::content()->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'unit_amount' => $item->price * 100, // Stripe expects the price in cents
                        'quantity' => $item->qty,
                    ];
                })->toArray(),
        ) !!};

        // Initialize Stripe.js
        const stripe = Stripe('{{ env('PUBLISHABLE_KEY') }}');

        initialize();

        // Fetch Checkout Session and retrieve the client secret
        async function initialize() {
            const fetchClientSecret = async () => {
                const response = await fetch("/create-checkout-session", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        products: cartItems
                    })
                });
                const {
                    clientSecret
                } = await response.json();
                return clientSecret;
            };

            // Initialize Checkout
            const checkout = await stripe.initEmbeddedCheckout({
                fetchClientSecret,
            });

            // Mount Checkout
            checkout.mount('#checkout');
        }
    </script>

    <script
        src="https://www.paypal.com/sdk/js?client-id=test&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card"
        data-sdk-integration-source="developer-studio"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                var value = parseFloat("{{ $grandTotal }}").toFixed(
                    2); // Ensure value is a float with 2 decimals

                // Ensure value is a valid number
                if (isNaN(value) || value <= 0) {
                    alert("Invalid amount.");
                    return;
                }

                var url = "/create/" + encodeURIComponent(value);

                // Use fetch to make the request
                return fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            console.error("Error: Response not OK",
                                response); // Log full response for debugging
                            throw new Error("Failed to create order. Please try again.");
                        }
                        return response.text(); // Return the response as text (order ID)
                    })
                    .then(id => {
                        console.log("Order ID created: " + id); // Log the order ID
                        return id; // Return the order ID to PayPal Buttons
                    })
                    .catch(error => {
                        console.error("Error creating order:", error); // Log full error
                        alert("There was an error creating the order. Please try again.");
                        return Promise.reject(error); // Reject the promise to prevent further actions
                    });
            },

            onApprove: function(data, actions) {
                const fields = [{
                        id: 'first_name',
                        name: 'First Name'
                    },
                    {
                        id: 'last_name',
                        name: 'Last Name'
                    },
                    {
                        id: 'email',
                        name: 'Email'
                    },
                    {
                        id: 'address',
                        name: 'Address'
                    },
                    {
                        id: 'city',
                        name: 'City'
                    },
                    {
                        id: 'state',
                        name: 'State'
                    },
                    {
                        id: 'zip',
                        name: 'Zip Code'
                    },
                    {
                        id: 'mobile',
                        name: 'Mobile'
                    },
                    {
                        id: 'country',
                        name: 'Country'
                    }
                ];

                let isValid = true;

                fields.forEach(field => {
                    let fieldValue = $("#" + field.id).val().trim();

                    // If the field is empty, show an error
                    if (fieldValue === "") {
                        $("#" + field.id).siblings('p').addClass('invalid-feedback').html(
                            `${field.name} is required.`);
                        $("#" + field.id).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $("#" + field.id).siblings('p').removeClass('invalid-feedback').html("");
                        $("#" + field.id).removeClass('is-invalid');
                    }
                });

                // If the validation passes, proceed with PayPal approval
                if (isValid) {
                    return fetch("/complete", {
                            method: "post",
                            headers: {
                                "X-CSRF-Token": '{{ csrf_token() }}',
                                "Content-Type": "application/json" // Ensure content type is set for JSON
                            }
                        })
                        .then((response) => {
                            Swal.fire({
                                title: "Transaction Complete",
                                text: "Thank you for purchasing!",
                                icon: "success"
                            }).then(() => {
                                // Submit the form after the alert is confirmed
                                $("#orderForm").submit();
                            });
                            response.json();
                        })
                        .then((order_details) => {
                            console.log(order_details);
                            // document.getElementById("paypal-success").style.display = 'block';
                            //paypal_buttons.close();

                        })
                        .catch((error) => {
                            console.log(error);
                        });
                } else {
                    // If validation fails, do not proceed with PayPal
                    Swal.fire({
                        title: "Validation Failed",
                        text: "Please fill all the required fields.",
                        icon: "error"
                    });
                }
            },


            onCancel: function(data) {
                console.log('cancel');
                Swal.fire({
                    title: "Cancelled",
                    text: "Your Payment was cancelled.",
                    icon: "error"
                });
            },

            onError: function(err) {
                console.log(err);
            }
        }).render('#payment_options');
    </script>


    <script>
        $("input[name='payment_method']").click(function() {
            if ($("#payment_method_one").is(":checked")) {
                // COD selected, hide both forms and set hidden input to "COD"
                $("#stripe-payment-form, #paypal-payment-form").addClass("d-none");
                $("#selected_payment_method").val("1");
            } else if ($("#payment_method_two").is(":checked")) {
                // Stripe selected, show only Stripe form and set hidden input to "Stripe"
                $("#stripe-payment-form").removeClass("d-none");
                $("#paypal-payment-form").addClass("d-none");
                $("#selected_payment_method").val("2");
            } else if ($("#payment_method_three").is(":checked")) {
                // PayPal selected, show only PayPal form and set hidden input to "PayPal"
                $("#paypal-payment-form").removeClass("d-none");
                $("#stripe-payment-form").addClass("d-none");
                $("#selected_payment_method").val("3");
            }
        });


        $("#orderForm").submit(function(event) {
            event.preventDefault();
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('front.processCheckout') }}',
                type: 'post',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    var errors = response.errors;
                    if (response.status == false) {

                        if (errors.first_name) {
                            $("#first_name").siblings('p').addClass('invalid-feedback').html(errors
                                .first_name);
                            $("#first_name").addClass('is-invalid');
                        } else {
                            $("#first_name").siblings('p').removeClass('invalid-feedback').html("");
                            $("#first_name").removeClass('is-invalid');
                        }
                        if (errors.last_name) {
                            $("#last_name").siblings('p').addClass('invalid-feedback').html(errors
                                .last_name);
                            $("#last_name").addClass('is-invalid');
                        } else {
                            $("#last_name").siblings('p').removeClass('invalid-feedback').html("");
                            $("#last_name").removeClass('is-invalid');
                        }
                        if (errors.country) {
                            $("#country").siblings('p').addClass('invalid-feedback').html(errors
                                .country);
                            $("#country").addClass('is-invalid');
                        } else {
                            $("#country").siblings('p').removeClass('invalid-feedback').html("");
                            $("#country").removeClass('is-invalid');
                        }
                        if (errors.email) {
                            $("#email").siblings('p').addClass('invalid-feedback').html(errors.email);
                            $("#email").addClass('is-invalid');
                        } else {
                            $("#email").siblings('p').removeClass('invalid-feedback').html("");
                            $("#email").removeClass('is-invalid');
                        }
                        if (errors.address) {
                            $("#address").siblings('p').addClass('invalid-feedback').html(errors
                                .address);
                            $("#address").addClass('is-invalid');
                        } else {
                            $("#address").siblings('p').removeClass('invalid-feedback').html("");
                            $("#address").removeClass('is-invalid');
                        }
                        if (errors.city) {
                            $("#city").siblings('p').addClass('invalid-feedback').html(errors.city);
                            $("#city").addClass('is-invalid');
                        } else {
                            $("#city").siblings('p').removeClass('invalid-feedback').html("");
                            $("#city").removeClass('is-invalid');
                        }
                        if (errors.state) {
                            $("#state").siblings('p').addClass('invalid-feedback').html(errors.state);
                            $("#state").addClass('is-invalid');
                        } else {
                            $("#state").siblings('p').removeClass('invalid-feedback').html("");
                            $("#state").removeClass('is-invalid');
                        }
                        if (errors.zip) {
                            $("#zip").siblings('p').addClass('invalid-feedback').html(errors.zip);
                            $("#zip").addClass('is-invalid');
                        } else {
                            $("#zip").siblings('p').removeClass('invalid-feedback').html("");
                            $("#zip").removeClass('is-invalid');
                        }
                        if (errors.mobile) {
                            $("#mobile").siblings('p').addClass('invalid-feedback').html(errors.mobile);
                            $("#mobile").addClass('is-invalid');
                        } else {
                            $("#mobile").siblings('p').removeClass('invalid-feedback').html("");
                            $("#mobile").removeClass('is-invalid');
                        }
                    } else {
                        $("#first_name").siblings('p').removeClass('invalid-feedback').html("");
                        $("#first_name").removeClass('is-invalid');
                        $("#last_name").siblings('p').removeClass('invalid-feedback').html("");
                        $("#last_name").removeClass('is-invalid');
                        $("#country").siblings('p').removeClass('invalid-feedback').html("");
                        $("#country").removeClass('is-invalid');
                        $("#email").siblings('p').removeClass('invalid-feedback').html("");
                        $("#email").removeClass('is-invalid');
                        $("#address").siblings('p').removeClass('invalid-feedback').html("");
                        $("#address").removeClass('is-invalid');
                        $("#city").siblings('p').removeClass('invalid-feedback').html("");
                        $("#city").removeClass('is-invalid');
                        $("#state").siblings('p').removeClass('invalid-feedback').html("");
                        $("#state").removeClass('is-invalid');
                        $("#zip").siblings('p').removeClass('invalid-feedback').html("");
                        $("#zip").removeClass('is-invalid');
                        $("#mobile").siblings('p').removeClass('invalid-feedback').html("");
                        $("#mobile").removeClass('is-invalid');


                        window.location.href = "{{ url('/thanks/') }}/" + response.orderId;

                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });


        //country
        $("#country").change(function() {
            // event.preventDefault();
            // $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url: '{{ route('front.getOrderSummary') }}',
                type: 'post',
                data: {
                    country_id: $(this).val()
                },
                dataType: 'json',
                success: function(response) {
                    // $("button[type=submit]").prop('disabled',false);
                    // var errors = response.errors;
                    if (response.status == true) {
                        $("#shippingAmount").html('$' + response.shippingCharge);
                        $("#grandTotal").html('$' + response.grandTotal);
                    } else {

                    }
                },
                error: function(jQXHR, exception) {
                    console.log("something is wrong");
                }
            });
        });

        $("#apply-discount").click(function() {
            // event.preventDefault();
            // $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url: '{{ route('front.applyDicount') }}',
                type: 'post',
                data: {
                    code: $('#discount_code').val(),
                    country_id: $('#country').val()
                },
                dataType: 'json',
                success: function(response) {
                    // $("button[type=submit]").prop('disabled',false);
                    if (response.status == true) {
                        $("#shippingAmount").html('$' + response.shippingCharge);
                        $("#grandTotal").html('$' + response.grandTotal);
                        $("#discount_value").html('<strong>$' + response.discount + '</strong>');
                        $("#discount-response-wrapper").html(response.discountString);
                    } else {
                        $("#discount-response-wrapper").html('<span class="text-danger">' + response
                            .message + '</span>');
                    }
                }
            });
        });

        $('body').on('click', "#remove-discount", function() {
            $.ajax({
                url: '{{ route('front.removeCoupon') }}',
                type: 'post',
                data: {
                    country_id: $('#country').val()
                },
                dataType: 'json',
                success: function(response) {
                    // $("button[type=submit]").prop('disabled',false);
                    if (response.status == true) {
                        $("#shippingAmount").html('$' + response.shippingCharge);
                        $("#grandTotal").html('$' + response.grandTotal);
                        $("#discount_value").html('<strong>$' + response.discount + '</strong>');
                        $("#discount-response").html('');
                        $("#discount_code").val('');
                    }
                }
            });

        });
        // $("#remove-dicount").click(function(){
        //     // event.preventDefault();
        //     // $("button[type=submit]").prop('disabled',true);

        // });
    </script>
@endsection
