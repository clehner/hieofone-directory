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
				<div class="panel-heading"><span style="font-size:large">Get Credentials from Doximity</span></div>
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
					@if (isset($start))
						<div class="form-group">
							<div class="col-md-6 col-md-offset-3">
								<a class="btn btn-primary btn-block" href="{{ route('doximity') }}">
									<i class="fa fa-btn fa-openid"></i> Verify with Doximity
								</a>
							</div>
						</div>
					@else
						<div class="form-group">
							<div style="text-align: center;">
								<p>Please scan this QR code with your credential wallet to receive the credential:</p>
								{!! QrCode::size(300)->generate($vc_offer_url) !!}
							</div>
						</div>
							<div class="col-md-6 col-md-offset-3">
								<button type="Button" class="btn btn-primary btn-block" onclick="$('#modal2').modal('show')">
									Continue
								</button>
							</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal1" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div id="modal1_header" class="modal-header">Add NPI credential to credential wallet?</div>
			<div id="modal1_body" class="modal-body" style="height:30vh;overflow-y:auto;">
				<p>This will simulate adding a verifiable credential to your existing credential wallet.</p>
				<p>After the simulated NPI credential is added, click on Sign in with credential wallet</p>
				<p>This will enable you to write a prescription.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" onClick="attest()"><i class="fa fa-btn fa-check"></i> Proceed</button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-btn fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="modal2" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div id="modal1_header" class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Doximity Credentials to Credential Wallet</h4>
			</div>
			<div id="modal1_body" class="modal-body" style="height:60vh;overflow-y:auto;">
				<p>You should have successfully added Doximity credentials to your credential wallet.</p>
				<p>The credential expires in 1 month.  Return to this site to renew it.</p>
				<p>You can verify this by clicking on Verifications in your credential wallet with a verification coming from HIE of One with NPI and Speciality claims added.</p>
				<p>
				<!-- <p><a href="https://shihjay.xyz/nosh">Click here to access Alice's Health Record again</a></p> -->
				<p>Problems adding your credentials?</p>
				<p><a href="{{ route('doximity_start') }}">Try Again</a></p>
				<p><a href="{{ $finish }}">Finish and Close</a></p>
			</div>
		</div>
	</div>
</div>
@endsection

@section('view.scripts')
@endsection
