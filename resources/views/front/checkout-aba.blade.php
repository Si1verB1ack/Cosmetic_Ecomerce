<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <h2></h2>
    <form method="POST" target="aba_webservice" action="{{ config('aba.api_url') }}" id="aba_merchant_request">
        @csrf
        <input type="hidden" name="hash" value="{{ $hash }}" id="hash" />
        <input type="hidden" name="tran_id" value="{{ $transaction_id }}" id="tran_id" />
        <input type="hidden" name="amount" value="{{ $amount }}" id="amount" />
        <input type="hidden" name="firstname" value="{{ $firstName }}" id="firstname" />
        <input type="hidden" name="lastname" value="{{ $lastName }}" id="lastname" />
        <input type="hidden" name="phone" value="{{ $phone }}" id="phone" />
        <input type="hidden" name="email" value="{{ $email }}" id="email" />
        <input type="hidden" name="items" value="{{ $items }}" id="items" />
        <input type="hidden" name="return_params" value="{{ $return_params }}" id="return_params" />
        <input type="hidden" name="currency" value="{{ $currency }}" id="currency" />
        <input type="hidden" name="shipping" value="{{ $shipping }}" id="shipping" />
        <input type="hidden" name="type" value="{{ $type }}" id="type" />
        <input type="hidden" name="payment_option" value="{{ $payment_option }}" id="payment_option" />
        <input type="hidden" name="merchant_id" value="{{ $merchant_id }}" id="merchant_id" />
        <input type="hidden" name="req_time" value="{{ $req_time }}" id="req_time" />
        <input type="hidden" name="return_param" value="{{ $return_params }}" />
    </form>
    <input type="submit" id="aba-checkout" class="btn btn-primary">Submit Payment</input>


    <script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>
    <script>
        $(document).ready(function() {
            // Attach the click event to the button after the DOM is ready
            $('#aba-checkout').click(function() {
                AbaPayway.checkout();
            });
        });
    </script>
</body>

</html>
