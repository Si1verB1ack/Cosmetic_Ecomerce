@extends('front.layouts.app')

@section('content')

    <section class="container">
        @include('admin.message')
        <div class="col-md-12 text-center py-5">
            <h1>Thank you for your purchase</h1>
            <p>Your Order Id is: {{$id}}</p>
        </div>
    </section>

@endsection
