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
                                    <input type="hidden" id="discount_type" name="discount_type" />
                                    <input type="hidden" id="discount_type_amount" name="discount_type_amount" />
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
                            {{-- <div class="form-check">
                                <input type="radio" checked name="payment_method" value="cod"
                                    id="payment_method_one">
                                <label for="payment_method_one" class="form-check-label">COD</label>
                            </div> --}}

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="cod" id="payment_method_two">
                                <label for="payment_method_two" class="form-check-label">Stripe</label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="cod" id="payment_method_three">
                                <label for="payment_method_three" class="form-check-label">Paypal</label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="cod" id="payment_method_four">
                                <label for="payment_method_four" class="form-check-label">ABA</label>
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
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control" id="paypal-amount" readonly
                                                value="{{ number_format($grandTotal, 2) }}" aria-label="Amount">

                                            <span class="input-group-text">.00</span>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div id="payment_options"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal Payment Form Section -->
                            <div class="card-body p-0 d-none mt-3" id="aba-payment-form">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mt-2 mb-2 summery-end">
                                        <div class="h5"><strong>Total</strong></div>
                                        <div class="h5"><strong
                                                id="grandTotalPayPal">${{ number_format($grandTotal, 2) }}</strong></div>
                                    </div>
                                    <input type="button" id="checkout_button" class="btn-dark btn btn-block w-100"
                                        value="Check out">
                                </div>
                            </div>


                            <div class="pt-1">
                                {{-- <a href="#" class="btn-dark btn btn-block w-100">Pay Now</a> --}}
                                <button type="submit" hidden class="btn-dark btn btn-block w-100">Pay Now</button>
                            </div>
                        </div>


                        <!-- CREDIT CARD FORM ENDS HERE -->

                    </div>
                </div>
            </form>
            <form method="POST" target="aba_webservice" action="{{ config('aba.api_url') }}" id="aba_merchant_request"
                name="aba_merchant_request">
                @csrf
                <input type="hidden" name="hash" id="hash" />
                <input type="hidden" name="tran_id" id="tran_id" />
                <input type="hidden" name="amount" id="amount" />
                <input type="hidden" name="firstname" id="firstname" />
                <input type="hidden" name="lastname" id="lastname" />
                <input type="hidden" name="phone" id="phone" />
                <input type="hidden" name="abaemail" id="abaemail" />
                <input type="hidden" name="items" id="items" />
                <input type="hidden" name="return_params" id="return_params" />
                <input type="hidden" name="currency" id="currency" />
                <input type="hidden" name="shipping" id="shipping" />
                <input type="hidden" name="type" id="type" />
                <input type="hidden" name="payment_option" id="payment_option" />
                <input type="hidden" name="merchant_id" id="merchant_id" />
                <input type="hidden" name="req_time" id="req_time" />
            </form>
        </div>
    </section>

@endsection

@section('customJs')
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>
    <script>
        $.LoadingOverlaySetup({
            background: "rgba(255, 255, 255, 0.8)", // Set the background color with transparency
            image: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle r="80" cx="500" cy="90"/><circle r="80" cx="500" cy="910"/><circle r="80" cx="90" cy="500"/><circle r="80" cx="910" cy="500"/><circle r="80" cx="212" cy="212"/><circle r="80" cx="788" cy="212"/><circle r="80" cx="212" cy="788"/><circle r="80" cx="788" cy="788"/></svg>', // Custom SVG image
            imageAnimation: "2000ms", // Image animation duration
            imageColor: "#202020", // Image color
            imageAutoResize: true, // Resize image automatically
            imageResizeFactor: 0.3, // Resize factor for the image
            imageOrder: 1, // Image order
            fontawesome: false, // Disable FontAwesome icons
            text: "", // No text
            textColor: "#202020", // Text color (if you add text)
            textAutoResize: true, // Resize text automatically
            textResizeFactor: 0.5, // Text resize factor
            progress: false, // No progress bar
            progressAutoResize: true, // Resize progress bar automatically
            progressResizeFactor: 0.25, // Resize factor for the progress bar
            progressColor: "#a0a0a0", // Progress bar color
            progressOrder: 5, // Progress bar order
            progressFixedPosition: false, // Disable fixed position for the progress bar
            progressSpeed: 200, // Progress bar speed
            size: 50, // Initial size of the overlay
            maxSize: 120, // Max size of the overlay
            minSize: 20, // Min size of the overlay
            direction: "column", // Layout direction for items in the overlay
            fade: 400, // Fade duration
            resizeInterval: 50, // Resize interval
            hideAfter: 5000 // Hide overlay after 5 seconds
        });
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location);
            const status = "{{ session('status') }}";

            if (status === 'success') {
                Swal.fire({
                    title: "Transaction Complete",
                    text: "Thank you for purchasing!",
                    icon: "success"
                }).then(() => {
                    $.LoadingOverlay("show");
                    // Submit the form after the alert is confirmed
                    $("#selected_payment_method").val("2");
                    localStorage.removeItem('discountType');
                    localStorage.removeItem('discountTypeAmount');
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

        const totalShippingCharge = {!! json_encode($totalShippingCharge) !!};



        // Initialize Stripe.js
        const stripe = Stripe('{{ env('PUBLISHABLE_KEY') }}');

        initialize();

        // Fetch Checkout Session and retrieve the client secret
        async function initialize() {
            const discountType = localStorage.getItem('discountType');
            const discountTypeAmount = parseFloat(localStorage.getItem('discountTypeAmount'));

            const fetchClientSecret = async () => {
                const response = await fetch("/create-checkout-session", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        products: cartItems,
                        totalShippingCharge: totalShippingCharge,
                        discountTypeAmount: discountTypeAmount,
                        discountType: discountType,
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


    {{-- aba --}}
    <script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>
    <script>
        $(document).ready(function() {

            const abaItems = {!! json_encode(
                Cart::content()->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'unit_amount' => $item->price, // Stripe expects the price in cents
                            'quantity' => $item->qty,
                        ];
                    })->toArray(),
            ) !!};

            const discountElement = document.getElementById('discount_value');
            const discountValue = discountElement ? parseFloat(discountElement.innerText.replace('$', '').trim()) :
                0;

            console.log("disounted value" + discountValue); // This will print the discount value


            // Prepare the data to send to the backend (you can dynamically get these values as needed)
            const productsList = abaItems; // Assume you have the cart items data here
            const totalShipping = {!! json_encode($totalShippingCharge) !!}; // Example shipping charge
            // const grandTotal = "{{ $grandTotal }}";

            const requestData = {
                products: productsList, // Product data
                amount: 0,
                discount: discountValue,
                firstName: $('#first_name').val(),
                lastName: $('#last_name').val(),
                phone: $('#mobile').val(),
                email: $('#email').val(),
                return_params: 'Test Params',
                type: 'purchase',
                currency: 'USD',
                shipping: totalShipping,
                payment_option: null
            };

            // Make the AJAX request
            $.ajax({
                url: '/aba/create/', // The route to your controller method
                type: 'GET',
                data: requestData, // Pass data to the backend
                success: function(response) {
                    // Dynamically set form values from the response
                    $('#hash').val(response.hash);
                    $('#tran_id').val(response.transaction_id);
                    $('#amount').val(response.amount); // Set amount in the hidden field
                    $('#total-amount-aba').text(response.amount); // Display amount where it's needed
                    $('#firstname').val(response.firstName);
                    $('#lastname').val(response.lastName);
                    $('#phone').val(response.phone);
                    $('#abaemail').val(response.abaemail);
                    $('#items').val(response.items);
                    $('#return_params').val(response.return_params);
                    $('#currency').val(response.currency);
                    $('#shipping').val(response.shipping);
                    $('#type').val(response.type);
                    $('#payment_option').val(response.payment_option);
                    $('#merchant_id').val(response.merchant_id);
                    $('#req_time').val(response.req_time);

                    // Optionally, you can handle the form submission here if needed
                    // $('#aba_merchant_request').submit();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
        $('#checkout_button').click(function() {
            AbaPayway.checkout();
            // Store the original callback
            var originalSuccessCallback = _abaCallbackOnSuccess;
            var originalErrorCallback = _abaCallbackOnError;

            // Redefine with extended functionality
            _abaCallbackOnSuccess = function(response) {
                Swal.fire({
                    title: "Transaction Complete",
                    text: "Thank you for purchasing!",
                    icon: "success"
                }).then(() => {
                    $.LoadingOverlay("show");
                    localStorage.removeItem('discountType');
                    localStorage.removeItem('discountTypeAmount');
                    // Submit the form after the alert is confirmed
                    $("#orderForm").submit();
                });
            };

            _abaCallbackOnError = function(error) {
                // Call original callback first
                if (originalErrorCallback) {
                    originalErrorCallback(error);
                }

                // Add your custom error handling
                console.error('Additional error handling', error);
            };
        });
    </script>

    <script
        src="https://www.paypal.com/sdk/js?client-id=test&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card"
        data-sdk-integration-source="developer-studio"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                var grandTotalText = $("#grandTotal").text(); // Get the text inside <strong id="grandTotal">

                // Remove dollar sign and commas to sanitize the value
                var sanitizedGrandTotal = grandTotalText.replace(/[^0-9.]/g, '');

                // Parse it as a float and ensure it's 2 decimal places
                var grandTotalValue = parseFloat(sanitizedGrandTotal).toFixed(2);

                // Now you can use this value for PayPal or any other purpose
                console.log("Updated Grand Total (formatted):", grandTotalValue);

                var value = grandTotalValue;

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
                                $.LoadingOverlay("show");
                                localStorage.removeItem('discountType');
                                localStorage.removeItem('discountTypeAmount');

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
            // Reset any previous validation error messages
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass('is-invalid');

            // Initialize a flag for validation
            var isValid = true;

            // Validate shipping address fields
            var firstName = $("#first_name").val().trim();
            var lastName = $("#last_name").val().trim();
            var email = $("#email").val().trim();
            var country = $("#country").val().trim();
            var address = $("#address").val().trim();
            var city = $("#city").val().trim();
            var state = $("#state").val().trim();
            var zip = $("#zip").val().trim();
            var mobile = $("#mobile").val().trim();

            // Validate address fields
            if (!firstName) {
                isValid = false;
                $("#first_name").after("<p class='invalid-feedback'>First Name is required</p>").addClass(
                    'is-invalid');
            }
            if (!lastName) {
                isValid = false;
                $("#last_name").after("<p class='invalid-feedback'>Last Name is required</p>").addClass(
                    'is-invalid');
            }
            if (!email) {
                isValid = false;
                $("#email").after("<p class='invalid-feedback'>Email is required</p>").addClass('is-invalid');
            }
            if (!country) {
                isValid = false;
                $("#country").after("<p class='invalid-feedback'>Country is required</p>").addClass('is-invalid');
            }
            if (!address) {
                isValid = false;
                $("#address").after("<p class='invalid-feedback'>Address is required</p>").addClass('is-invalid');
            }
            if (!city) {
                isValid = false;
                $("#city").after("<p class='invalid-feedback'>City is required</p>").addClass('is-invalid');
            }
            if (!state) {
                isValid = false;
                $("#state").after("<p class='invalid-feedback'>State is required</p>").addClass('is-invalid');
            }
            if (!zip) {
                isValid = false;
                $("#zip").after("<p class='invalid-feedback'>Zip is required</p>").addClass('is-invalid');
            }
            if (!mobile) {
                isValid = false;
                $("#mobile").after("<p class='invalid-feedback'>Mobile number is required</p>").addClass(
                    'is-invalid');
            }

            // Check selected payment method only if the form is valid
            if (isValid) {
                if ($("#payment_method_one").is(":checked")) {
                    // COD selected, hide both forms and set hidden input to "COD"
                    $("#stripe-payment-form, #paypal-payment-form, #aba-payment-form").addClass("d-none");
                    $("#selected_payment_method").val("1");
                } else if ($("#payment_method_two").is(":checked")) {
                    // Stripe selected, show only Stripe form and set hidden input to "Stripe"
                    $("#stripe-payment-form").removeClass("d-none");
                    $("#paypal-payment-form, #aba-payment-form").addClass("d-none");
                    $("#selected_payment_method").val("2");
                } else if ($("#payment_method_three").is(":checked")) {
                    // PayPal selected, show only PayPal form and set hidden input to "PayPal"
                    $("#paypal-payment-form").removeClass("d-none");
                    $("#stripe-payment-form, #aba-payment-form").addClass("d-none");
                    $("#selected_payment_method").val("3");
                } else if ($("#payment_method_four").is(":checked")) {
                    // ABA selected, show only ABA form and set hidden input to "ABA"
                    $("#aba-payment-form").removeClass("d-none");
                    $("#paypal-payment-form, #stripe-payment-form").addClass("d-none");
                    $("#selected_payment_method").val("4");
                }
            }

            // If the form is not valid, prevent the selection of payment method
            if (!isValid) {
                return false; // Prevent selection of the payment method
            }
        });






        $("#orderForm").submit(function(event) {
            localStorage.removeItem('discountType');
            localStorage.removeItem('discountTypeAmount');
            $.LoadingOverlay("show");
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
                    $.LoadingOverlay("hide");
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
                    $.LoadingOverlay("hide");
                    $("button[type=submit]").prop('disabled', false);
                    alert("An unexpected error occurred. Please try again.");
                    console.log("Raw response:", xhr.responseText);
                    console.log("Error status:", status);
                    console.log("Error:", error);
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
                        console.log("Discount Type: ", response.discountType); // Debugging line
                        localStorage.setItem('discountType', response.discountType);
                        localStorage.setItem('discountTypeAmount', response.discountTypeAmount);
                        $("#discount_type").val(response.discountType);
                        $("#discount_type_amount").val(response.discountTypeAmount);

                        $("#discount-response-wrapper").html(response.discountString);

                        let sanitizedGrandTotal = response.grandTotal.replace(/[^0-9.]/g, '');
                        window.grandTotalValue = parseFloat(sanitizedGrandTotal).toFixed(
                            2); // Ensure it's a float with 2 decimals

                        console.log("Updated Grand Total (formatted):", window.grandTotalValue);

                        location.reload();

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
                        console.log("Discount Type: ", response.discountType);

                        $("#discount_type").val(response.discountType); // Update hidden input
                        localStorage.setItem('discountType', response.discountType);

                        localStorage.setItem('discountTypeAmount', response.discountTypeAmount);
                        $("#discount_type_amount").val(response.discountTypeAmount);



                        $("#discount-response").html('');
                        $("#discount_code").val('');
                        location.reload();
                    }
                }
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve the discountType from localStorage
            const discountType = localStorage.getItem('discountType');
            const discountTypeAmount = localStorage.getItem('discountTypeAmount');
            if (discountType) {
                // Set the discountType value in the hidden input
                $("#discount_type").val(discountType);
                $("#discount_type_amount").val(discountTypeAmount);
            }
        });
    </script>
@endsection
