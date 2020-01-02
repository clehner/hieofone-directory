<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Directory @yield('page')</title>

	<!-- Styles -->
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
	{{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
	@yield('view.stylesheet')
	@yield('css')
	<style>
		@import url(https://fonts.googleapis.com/css?family=Nunito);
		body {
			font-family: 'Nunito';
		}
		.fa-btn {
			margin-right: 6px;
		}
	</style>
</head>
<body id="app-layout">
	<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
			<div class="navbar-header">

				<!-- Collapsed Hamburger -->
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<!-- Branding Image -->
				<a class="navbar-brand" href="{{ url('/') }}">
					Directory
					@if (isset($name))
						for {{ $name }}
					@endif
				</a>
			</div>

			<div class="collapse navbar-collapse" id="app-navbar-collapse">
				<!-- Left Side Of Navbar -->
				<ul class="nav navbar-nav">
					@if (!Auth::guest())
						@if (Session::get('is_owner') == 'yes')
							<li><a href="{{ url('/setup_mail') }}">E-mail Service</a></li>
							<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> Settings <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="{{ url('/settings') }}">Directory Settings</a></li>
									<li><a href="{{ url('/default_policies') }}">Default Policies</a></li>
									<li><a href="{{ url('/privacy_policy') }}">Privacy Policy</a></li>
									<li><a href="{{ url('/home') }}">Patients</a></li>
									<li><a href="{{ url('/invitation_list') }}">Pending Invitiations</a></li>
									<li><a href="{{ url('/activity_logs') }}">Activity Logs</a></li>
								</ul>
							</li>
							<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> Users <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="{{ url('/add_owner') }}">Add Administrative User</a></li>
									<li><a href="{{ url('/users') }}">Authorized</a></li>
									<li><a href="{{ url('/authorize_user') }}">Pending Authorization</a></li>
								</ul>
							</li>
							<li><a href="{{ url('/make_invitation') }}">Invite</a></li>

						@else
							<li><a href="{{ url('/home') }}">My Patients</a></li>
						@endif
						<!-- <li><a href="{{ url('/forum') }}">Forum</a></li> -->
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								Support <span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="http://bit.ly/TrusteeForum" target="_blank"><i class="fa fa-fw fa-question"></i>F.A.Q</a></li>
							</ul>
						</li>
						<li><a href="{{ url('/reports') }}">Reports</a></li>
						@if (Session::get('is_owner') == 'no')
							<li><a href="{{ url('/privacy_policy') }}">Privacy Policy</a></li>
						@endif
					@endif
				</ul>

				<!-- Right Side Of Navbar -->
				<ul class="nav navbar-nav navbar-right">
					<!-- Authentication Links -->
					@if (Auth::guest())
						@if (!isset($noheader))
							<li><a href="{{ url('/patients') }}">Patients</a></li>
							<li><a href="{{ url('/signup') }}">Clinicians</a></li>
							<!-- <li><a href="{{ url('/others') }}">Others</a></li> -->
							<li><a href="{{ url('/privacy_policy') }}">Privacy Policy</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
									Support <span class="caret"></span>
								</a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="http://bit.ly/TrusteeForum " target="_blank"><i class="fa fa-fw fa-question"></i>F.A.Q</a></li>
									<li><a href="{{ url('/support') }}"><i class="fa fa-fw fa-list"></i>Support Form</a></li>
								</ul>
							</li>
							<!-- <li><a href="{{ url('/signup') }}">Sign Up</a></li> -->
							@if (isset($demo))
								<!-- <li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
										Demo User <span class="caret"></span>
									</a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="{{ url('/demo_patient_list') }}"><i class="fa fa-btn fa-list"></i>Public Patient List</a></li>
										<li><a href=""><i class="fa fa-btn fa-cogs"></i>My Information</a></li>
										<li><a href=""><i class="fa fa-btn fa-sign-out"></i>Sign Out</a></li>
									</ul>
								</li> -->
							@else
								<!-- <li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
										Demo Pages <span class="caret"></span>
									</a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="{{ url('/demo_patient_list') }}"><i class="fa fa-btn fa-list"></i>Public Patient List</a></li>
										<li><a href="{{ url('/demo_patient_list/yes') }}"><i class="fa fa-btn fa-list"></i>Patient List after Login</a></li>
									</ul>
								</li> -->
								<li><a href="{{ url('/login') }}">Sign In</a></li>
							@endif
						@endif
					@else
						@if (Session::get('is_owner') == 'yes')
							<li class="dropdown" id="owner_li">
						@else
							<li class="dropdown">
						@endif
							@if (Session::get('is_owner') == 'yes')
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							@else
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							@endif
								{{ Session::get('full_name') }} <span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ url('/my_info') }}"><i class="fa fa-fw fa-btn fa-cogs"></i>My Information</a></li>
								<li><a href="{{ url('/logout') }}"><i class="fa fa-fw fa-btn fa-sign-out"></i>Sign Out</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</nav>

	@if (isset($searchbar))
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<form class="input-group form" border="0" id="search_patient_form" role="search" action="{{ url('/search') }}" method="POST" style="margin-bottom:0px;" data-nosh-target="search_patient_results">
					<input type="text" class="form-control search" id="search_field" name="search_field" placeholder="Search Directory" style="margin-bottom:0px;" required autocomplete="off">
					<input type="hidden" name="type" value="div">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-md" id="search_patient_submit" name="submit" value="Go"><i class="glyphicon glyphicon-search"></i></button>
					</span>
				</form>
				<div class="list-group" id="search_patient_results"></div>
			</div>
		</div>
	</div>
	@endif
	@yield('content')

	<!-- JavaScripts -->
	<script src="{{ asset('assets/js/jquery-3.1.1.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.maskedinput.min.js') }}"></script>
	{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
	@yield('view.scripts')
	<script type="text/javascript">
		// var check_demo = false;
		// setInterval(function() {
		// 	$.ajax({
		// 		type: "GET",
		// 		url: "check_demo_self",
		// 		beforeSend: function(request) {
		// 			return request.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr('content'));
		// 		},
		// 		success: function(data){
		// 			if (data !== 'OK') {
		// 				if (check_demo === false) {
		// 					alert(data);
		// 					check_demo = true;
		// 				}
		// 			}
		// 		}
		// 	});
		// }, 3000);
	</script>
	@yield('js')
	@yield('footer')
</body>
</html>
