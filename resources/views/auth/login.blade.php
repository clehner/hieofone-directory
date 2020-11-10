@extends('layouts.app')

@section('view.stylesheet')
	<style>
	html {
		position: relative;
		min-height: 100%;
	}
	body {
	/* Margin bottom by footer height */
		margin-bottom: 60px;
	}
	.footer {
		position: absolute;
		bottom: 0;
		width: 100%;
		/* Set the fixed height of the footer here */
		height: 60px;
		background-color: #f5f5f5;
	}
	.container .text-muted {
		margin: 20px 0;
	}
	.footer > .container {
		padding-right: 15px;
		padding-left: 15px;
	}
	</style>
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="container-fluid panel-container">
						<div class="col-xs-6 col-md-9 text-left">
							<h4 class="panel-title" style="height:35px;display:table-cell !important;vertical-align:middle;">
								Sign In
								@if (isset($vp_request_url))
								with Credential Wallet
								@endif
							</h4>
						</div>
						<div class="col-xs-3 text-right">
							<button type="button" class="btn btn-primary" id="admin_button" title="Trustee Users/Administrator Login"><i class="fa fa-btn fa-cogs"></i></button>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div style="text-align: center;">
						<div style="text-align: center;">
							<i class="fa fa-child fa-5x" aria-hidden="true" style="margin:20px;text-align: center;"></i>
							@if ($errors->has('tryagain'))
								<div class="form-group has-error">
									<span class="help-block has-error">
										<strong>{{ $errors->first('tryagain') }}</strong>
									</span>
								</div>
							@endif
						</div>
					</div>
					<input type="hidden" id="admin_set_id" value="no">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
						{{ csrf_field() }}

						<div class="admin_form form-group{{ $errors->has('username') ? ' has-error' : '' }}" style="display:none;">
							<label for="username" class="col-md-4 control-label">Username </label>

							<div class="col-md-6">
								<input id="username" class="form-control" name="username" value="{{ old('username') }}" data-toggle="tooltip" title="">

								@if ($errors->has('username'))
									<span class="help-block">
										<strong>{{ $errors->first('username') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="admin_form form-group{{ $errors->has('password') ? ' has-error' : '' }}" style="display:none;">
							<label for="password" class="col-md-4 control-label">Password</label>

							<div class="col-md-6">
								<input id="password" type="password" class="form-control" name="password" data-toggle="tooltip" title="">

								@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="admin_form form-group" style="display:none;">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="admin_form form-group" style="display:none;">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-btn fa-sign-in"></i> Sign In
								</button>

								<a class="btn btn-link" href="{{ url('/password_email') }}">Forgot Your Password?</a>
							</div>
						</div>
						@if (!isset($nooauth))
						<div class="form-group">
							<div class="col-md-8 col-md-offset-2">
								<a href="{{ route('login_vp') }}" type="button" class="btn btn-primary btn-block">
									Sign In with Credential Wallet
								</a>
								<button type="button" class="btn btn-primary btn-block" id="addVerificationBtn"><i class="fa fa-btn fa-plus"></i> Add Doximity Clinician Verification</button>

								<!-- @if (isset($google))
									<a class="btn btn-primary btn-block" href="{{ url('/google') }}">
										<i class="fa fa-btn fa-google"></i> Login with Google
									</a>
								@endif
								@if (isset($twitter))
									<a class="btn btn-primary btn-block" href="{{ url('/twitter') }}">
										<i class="fa fa-btn fa-twitter"></i> Login with Twitter
									</a>
								@endif -->
							</div>
							<div class="col-md-8 col-md-offset-8">

							</div>
						</div>
						@endif
						@if (isset($vp_request_url))
						<div class="form-group">
							<div style="text-align: center;">
								<p>Please scan this QR code with your credential wallet to proceed:</p>
								<!-- QR-Code:{{$vp_request_url}} -->
								{!! QrCode::size(300)->generate($vp_request_url) !!}
								<p id="errors"></p>
<!--
								<p>The code expires {{ $vp_request_expiration->shortRelativeToNowDiffForHumans() }}.</p>
							</div>
-->
						</div>
						@endif
						@if ($errors->has('tryagain') && isset($vp_received))
						<div class="col-md-6 col-md-offset-3">
							<a href="?retry_vp=1" class="btn btn-primary btn-block">
								Try again
							</a>
						</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<footer class="footer">
	<div class="container">
		<p class="text-muted pull-right">Version git-{{ $version }}</p>
	</div>
</footer>
<div class="modal" id="modal1" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div id="modal1_header" class="modal-header">Add clinician credential to credential wallet from Doximity?</div>
			<div id="modal1_body" class="modal-body" style="height:30vh;overflow-y:auto;">
				<p>We're demonstrating the addition of a verified credential to a cryptographic identity by using Doximity. Anyone with a Doximity sign in is able to add this credential.</p>
				<p>Please review Doximity's user verification policies before trusting this credential for any particular purpose.</p>
				<!-- <p>This will simulate adding a verified credential to your existing uPort.</p>
				<p>Clicking proceed with add a simulated NPI number</p>
				<p>Clicking on Get from Doximity will demonstrate how you can get a verified credential if you have an existing Doximity account</p>
				<p>After the credential is added, click on Login with uPort</p>
				<p>This will enable you to write a prescription.</p> -->
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-default" data-dismiss="modal" onClick="attest()"><i class="fa fa-btn fa-check"></i> Proceed</button> -->
				<a href="{{ route('doximity_start') }}" target="_blank" class="btn btn-default" id="doximity_modal"><i class="fa fa-btn fa-hand-o-right"></i> Get from Doximity</a>
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-btn fa-times"></i> Close</button>
			  </div>
		</div>
	</div>
</div>
@endsection

@section('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$("#username").focus();
		$('[data-toggle="tooltip"]').tooltip();
		$("#addVerificationBtn").click(function(){
            $('#modal1').modal('show');
        });
		$('#doximity_modal').click(function(){
			$('#modal1').modal('hide');
		});
		$('#admin_button').click(function(){
			if ($("#admin_set_id").val() == 'no') {
				$('.admin_form').show();
				$('#admin_set_id').val('yes');
			} else {
				$('.admin_form').hide();
				$('#admin_set_id').val('no');
			}
		});
	});
	@if (isset($vp_request_url))
	var pollUrl = {!! json_encode(route('login_vp_poll')) !!};
	var csrfToken = $("meta[name='csrf-token']").attr('content');
	var interval = 3e3;
	function pollLogin() {
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function () {
			if (this.readyState !== 4) return;
			switch (this.status) {
			case 200: // Login completed
				return location.reload();
			case 410: // VP request expired
				errors.innerText = 'Your QR code expired. Please reload the page to get a new one.';
				return;
			case 400: // VP request not in session
				errors.innerText = 'There was a problem coordinating with the server. Please reload the page to try again.';
				return;
			case 404: // VP request not in db
				errors.innerText = 'There was a problem with the request. Please reload the page to try again.';
				return;
			case 403: // Waiting
				errors.innerText = '';
				break;
			case 0: // Connection error
				errors.innerText = 'There was a problem communicating with the server. Are you offline?';
				break;
			}
			setTimeout(pollLogin, interval);
		};
		xhr.open('POST', pollUrl);
		xhr.setRequestHeader('X-CSRF-Token', csrfToken);
		xhr.send(null);
	}
	pollLogin()
	@endif
</script>
@endsection
