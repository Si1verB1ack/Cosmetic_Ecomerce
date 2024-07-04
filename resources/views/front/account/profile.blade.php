@extends('front.layouts.app')

@section('content')

    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                    @include('admin.message')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form action="" name="profileForm" id="profileForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input value="{{$user->name}}" type="text" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input value="{{$user->email}}" type="text" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input value="{{$user->phone}}" type="text" name="phone" id="phone" placeholder="Enter Your Phone" class="form-control">
                                        <p></p>
                                    </div>

                                    {{-- <div class="mb-3">
                                        <label for="phone">Address</label>
                                        <textarea name="address" id="address" class="form-control" cols="30" rows="5" placeholder="Enter Your Address"></textarea>
                                    </div> --}}

                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card mt-5">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                        </div>
                        <form action="" name="addressForm" id="addressForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="lastname">First Name</label>
                                        <input value="{{(!empty($address)) ? $address->first_name : ''}}" type="text" name="first_name" id="first_name" placeholder="Enter Your First Name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastname">Last Name</label>
                                        <input value="{{(!empty($address)) ? $address->last_name : ''}}" type="text" name="last_name" id="last_name" placeholder="Enter Your Last Name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email">Email</label>
                                        <input value="{{(!empty($address)) ? $address->email : ''}}" type="text" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email">Mobile</label>
                                        <input value="{{(!empty($address)) ? $address->mobile : ''}}" type="text" name="mobile" id="mobile" placeholder="Enter Your mobile" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="form-control">
                                            <option value="">Select a Country</option>
                                            @if($countries->isNotEmpty())
                                                @foreach ($countries as $country)
                                                    <option {{(!empty($address) && $address->country_id == $country->id) ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone">Address</label>
                                        <textarea name="address" id="address" class="form-control" cols="30" rows="5" placeholder="Enter Your Address">{{(!empty($address)) ? $address->address : ''}}</textarea>
                                        <p></p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone">Apartment</label>
                                        <input value="{{(!empty($address)) ? $address->apartment : ''}}" type="text" name="apartment" id="apartment" placeholder="Apartment" class="form-control">
                                        <p></p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone">City</label>
                                        <input value="{{(!empty($address)) ? $address->city : ''}}" type="text" name="city" id="city" placeholder="City" class="form-control">
                                        <p></p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone">State</label>
                                        <input value="{{(!empty($address)) ? $address->state : ''}}" type="text" name="state" id="state" placeholder="State" class="form-control">
                                        <p></p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone">Zip</label>
                                        <input value="{{(!empty($address)) ? $address->zip : ''}}" type="text" name="zip" id="zip" placeholder="Zip" class="form-control">
                                        <p></p>
                                    </div>

                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('customJs')

<script>
    $("#profileForm").submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url: '{{route("account.updateProfile")}}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success:function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response["status"]== true){
                        window.location.href="{{route('account.profile')}}"
                        $("#profileForm #name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#profileForm #email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#profileForm #phone").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }else{
                        var errors = response['errors']
                        if(errors['name']){
                            $("#profileForm #name").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['name']);
                        }else{
                            $("#profileForm #name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['email']){
                            $("#profileForm #email").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['email']);
                        }else{
                            $("#profileForm #email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['phone']){
                            $("#profileForm #phone").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['phone']);
                        }else{
                            $("#profileForm #phone").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }
                    }

                },error: function(jqXHR, exception){
                    console.log("something went wrong");
                }
            })
        });

        $("#addressForm").submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url: '{{route("account.updateAddress")}}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success:function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response["status"]== true){
                        window.location.href="{{route('account.profile')}}"
                        $("#addressForm #first_name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #last_name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #country").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #address").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #apartment").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #city").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #state").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #zip").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        $("#addressForm #mobile").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }else{
                        var errors = response['errors']
                        if(errors['first_name']){
                            $("#addressForm #first_name").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['first_name']);
                        }else{
                            $("#addressForm #first_name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['last_name']){
                            $("#addressForm #last_name").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['last_name']);
                        }else{
                            $("#addressForm #last_name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['email']){
                            $("#addressForm #email").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['email']);
                        }else{
                            $("#addressForm #email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['country']){
                            $("#addressForm #country").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['country']);
                        }else{
                            $("#addressForm #country").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['address']){
                            $("#addressForm #address").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['address']);
                        }else{
                            $("#addressForm #address").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['apartment']){
                            $("#addressForm #apartment").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['apartment']);
                        }else{
                            $("#addressForm #apartment").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['city']){
                            $("#addressForm #city").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['city']);
                        }else{
                            $("#addressForm #city").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['state']){
                            $("#addressForm #state").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['state']);
                        }else{
                            $("#addressForm #state").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['zip']){
                            $("#addressForm #zip").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['zip']);
                        }else{
                            $("#addressForm #zip").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['mobile']){
                            $("#addressForm #mobile").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['mobile']);
                        }else{
                            $("#addressForm #mobile").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }
                    }

                },error: function(jqXHR, exception){
                    console.log("something went wrong");
                }
            })
        });

</script>

@endsection
