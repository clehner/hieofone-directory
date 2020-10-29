@extends('layouts.app')

@section('view.stylesheet')
	<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
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
							<h4 class="panel-title" style="height:35px;display:table-cell !important;vertical-align:middle;">Sign In</h4>
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
						<div id="uport_indicator" style="text-align: center;display:none;">
							<i class="fa fa-spinner fa-spin fa-pulse fa-2x fa-fw"></i><span id="modaltext" style="margin:10px">Loading uPort...</span><br><br>
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
								<button type="button" class="btn btn-primary btn-block" onclick="loginBtnClick()">
									Sign In with Credential Wallet
								</button>
								<button type="button" class="btn btn-primary btn-block" id="connectUportBtn1"><i class="fa fa-btn fa-plus"></i> Add Doximity Clinician Verification</button>

								<!-- <button type="button" class="btn btn-primary btn-block" id="connectUportBtn1" onclick="uportConnect()">Connect uPort</button> -->
								<!-- <button type="button" class="btn btn-primary btn-block" id="connectUportBtn2" onclick="sendEther()">Send Ether</button> -->
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
			<div id="modal1_header" class="modal-header">Add clinician credential to uPort from Doximity?</div>
			<div id="modal1_body" class="modal-body" style="height:30vh;overflow-y:auto;">
				<p>We're demonstrating the addition of a verified credential to a blockchain identity by using Doximity. Anyone with a Doximity sign in is able to add this credential.</p>
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
<script src="{{ asset('assets/js/web3.js') }}"></script>
<script src="{{ asset('assets/js/uport-connect.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#username").focus();
		$('[data-toggle="tooltip"]').tooltip();
		$("#connectUportBtn1").click(function(){
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
	// Setup
	const Connect = window.uportconnect;
	const appName = 'Trustee Directory';
	const uport = new Connect(appName, {
		'network': 'rinkeby'
	});

	const loginBtnClick = () => {
		$('#uport_indicator').show();
		uport.requestDisclosure({
			requested: ['name', 'email', 'NPI'],
			notifications: true // We want this if we want to recieve credentials
	    });
	    uport.onResponse('disclosureReq').then((res) => {
			var uport_url = '<?php echo route("login_uport"); ?>';
			if ($('#admin_set_id').val() == 'yes') {
				uport_url += '/admin';
			}
			var uport_data = 'name=' + res.payload.name + '&uport=' + res.payload.did;
			if (typeof res.payload.NPI !== 'undefined') {
				uport_data += '&npi=' + res.payload.NPI;
			}
			if (typeof res.payload.email !== 'undefined') {
				uport_data += '&email=' + res.payload.email;
			}
			console.log(uport_data);
			$.ajax({
				type: "POST",
				url: uport_url,
				data: uport_data,
				dataType: 'json',
				beforeSend: function(request) {
					return request.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr('content'));
				},
				success: function(data){
					if (data.message !== 'OK') {
						toastr.error(data.message);
						// console.log(data);
					} else {
						window.location = data.url;
					}
				}
			});
		}, console.err);
	};

	let globalState = {
		uportId: "",
		txHash: "",
		sendToAddr: "0x687422eea2cb73b5d3e242ba5456b782919afc85",
		sendToVal: "5"
	};

	const uportConnect = function () {
		web3.eth.getCoinbase((error, address) => {
			if (error) { throw error; }
			console.log(address);
			globalState.uportId = address;
		});
	};

	const sendEther = () => {
		const value = parseFloat(globalState.sendToVal) * 1.0e18;
		const gasPrice = 100000000000;
		const gas = 500000;
		web3.eth.sendTransaction(
			{
				from: globalState.uportId,
				to: globalState.sendToAddr,
				value: value,
				gasPrice: gasPrice,
				gas: gas
			},
			(error, txHash) => {
				if (error) { throw error; }
				globalState.txHash = txHash;
				console.log(txHash);
			}
		);
	};

	const attest = () => {
		connect.requestCredentials({
	      requested: ['name', 'phone', 'country', 'email', 'description'],
	      notifications: true // We want this if we want to recieve credentials
	    }).then((credentials) => {
			console.log(credentials);
			connect.attestCredentials({
			  sub: credentials.address,
			  claim: { "NPI": "1023005410" },
			  exp: new Date().getTime() + 30 * 24 * 60 * 60 * 1000
			})
		});
	}
</script>
@endsection
