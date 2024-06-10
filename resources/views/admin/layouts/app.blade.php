<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Laravel Shop :: Administrative Panel</title>
        <link rel="icon" type="image/x-icon" href="{{asset('Cosmetic_resource/img/icon.png')}}">
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

        <!-- Font Awesome -->
		<link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css') }}">

		<!-- Theme style -->
		<link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css') }}">

        <!-- dropzone -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.css') }}">

        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.css') }}">

        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2/css/select2.css') }}">


        <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css') }}">
		<meta name="csrf-token" content="{{csrf_token()}}">
	</head>
	<body class="hold-transition sidebar-mini">
		<!-- Site wrapper -->
		<div class="wrapper">
			<!-- Navbar -->
			<nav class="main-header navbar navbar-expand navbar-white navbar-light">
				<!-- Right navbar links -->
				<ul class="navbar-nav">
					<li class="nav-item">
					  	<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>
				</ul>
				<div class="navbar-nav pl-2">
					<!-- <ol class="breadcrumb p-0 m-0 bg-white">
						<li class="breadcrumb-item active">Dashboard</li>
					</ol> -->
				</div>

				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" data-widget="fullscreen" href="#" role="button">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link p-0 pr-3" data-toggle="dropdown" href="#">
							<img src="{{ asset('admin-assets/img/avatar5.png')}}" class='img-circle elevation-2' width="40" height="40" alt="">
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3">
							<h4 class="h4 mb-0"><strong>{{Auth::guard('admin')->user()->name}}</strong></h4>
							<div class="mb-3">{{Auth::guard('admin')->user()->email}}</div>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-user-cog mr-2"></i> Settings
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-lock mr-2"></i> Change Password
							</a>
							<div class="dropdown-divider"></div>
							<a href="{{route("admin.logout")}}" class="dropdown-item text-danger">
								<i class="fas fa-sign-out-alt mr-2"></i> Logout
							</a>
						</div>
					</li>
				</ul>
			</nav>
			<!-- /.navbar -->

			<!-- Main Sidebar Container -->
			@include('admin.layouts.sidebar')

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				@yield('content')
			</div>
			<!-- /.content-wrapper -->
			<footer class="main-footer">

				<strong>Copyright &copy; 2014-2022 AmazingShop All rights reserved.
			</footer>

		</div>
        {{-- @if(Session::has('success'))
            <!-- Modal HTML -->
			<style>
				body {
					font-family: 'Varela Round', sans-serif;
				}
				.modal-confirm {
					top:30%;
					color: #636363;
					width: 325px;
					font-size: 14px;
				}
				.modal-confirm .modal-content {
					padding: 20px;
					border-radius: 5px;
					border: none;
				}
				.modal-confirm .modal-header {
					border-bottom: none;
					position: relative;
				}
				.modal-confirm h4 {
					text-align: center;
					font-size: 26px;
					margin: 30px 0 -15px;
				}
				.modal-confirm .form-control, .modal-confirm .btn {
					min-height: 40px;
					border-radius: 3px;
				}
				.modal-confirm .close {
					position: absolute;
					top: -5px;
					right: -5px;
				}
				.modal-confirm .modal-footer {
					border: none;
					text-align: center;
					border-radius: 5px;
					font-size: 13px;
				}
				.modal-confirm .icon-box {
					color: #fff;
					position: absolute;
					margin: 0 auto;
					left: 0;
					right: 0;
					top: -70px;
					width: 95px;
					height: 95px;
					border-radius: 50%;
					z-index: 9;
					background: #82ce34;
					padding: 15px;
					text-align: center;
					box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
				}
				.modal-confirm .icon-box i {
					font-size: 58px;
					position: relative;
					top: 3px;
				}
				.modal-confirm.modal-dialog {
					margin-top: 80px;
				}
				.modal-confirm .btn {
					color: #fff;
					border-radius: 4px;
					background: #82ce34;
					text-decoration: none;
					transition: all 0.4s;
					line-height: normal;
					border: none;
				}
				.modal-confirm .btn:hover, .modal-confirm .btn:focus {
					background: #6fb32b;
					outline: none;
				}
				.trigger-btn {
					display: inline-block;
					margin: 100px auto;
				}
				</style>
            <div id="myModal" class="modal fade">
                <div class="modal-dialog modal-confirm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="icon-box">
                                <i class="material-icons">&#xE876;</i>
                            </div>
                            <h4 class="modal-title w-100">Welcome</h4>
                        </div>
                        <div class="modal-body">
                            <p class="text-center">Now you can access the admin panel</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success btn-block" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}
		<!-- ./wrapper -->
		<!-- jQuery -->
		<script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>
		<!-- Bootstrap 4 -->
		<script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
		<!-- AdminLTE App -->
		<script src="{{ asset('admin-assets/js/adminlte.min.js') }}"></script>

        <script src="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.js') }}"></script>

        <script src="{{ asset('admin-assets/plugins/select2/js/select2.min.js') }}"></script>

		<!-- AdminLTE for demo purposes -->
		<script src="{{ asset('admin-assets/js/demo.js') }}"></script>
        <!-- dropzone -->
        <script src="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
		<script type="text/javascript">
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
            $(document).ready(function(){
                $(".summernote").summernote({
                    height:250
                });
            });
	    </script>
		@yield('customJs')
	</body>
</html>
