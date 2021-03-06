@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Reset Demo for HIE of One</div>
				<div class="panel-body">
					<div style="text-align: center;">
					  <i class="fa fa-child fa-5x" aria-hidden="true" style="margin:20px;text-align: center;"></i>
					</div>
					@if ($timer == true)
						<span class="help-block">
							<strong>Another person was using the demo {{ $timer_val1 }} minutes ago.  Please try again in {{ $timer_val }} minutes or more.</strong>
						</span>
						<div style="text-align: center;">
							<a href="#" id="advanced">Advanced</a>
						</div>
						<form id="hidden_form" class="form-horizontal" role="form" method="POST" action="{{ url('/reset_demo') }}" style="display:none">
							{{ csrf_field() }}

							<div class="alert alert-warning">
								<strong>CAUTION!</strong> The open source code associated with this demo is intended to inform standards and regulations and is NOT SECURE and NOT TESTED FOR CLINICAL USE. We hope you will join our GitHub communities and contribute.
							</div>

							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-md-4 control-label">E-Mail Address</label>

								<div class="col-md-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

									@if ($errors->has('email'))
										<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">
										<i class="fa fa-btn fa-sign-in"></i> Force Reset Demo
									</button>
								</div>
							</div>
						</form>
					@else
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/reset_demo') }}">
							{{ csrf_field() }}

							<div class="alert alert-warning">
								<strong>CAUTION!</strong> The open source code associated with this demo is intended to inform standards and regulations and is NOT SECURE and NOT TESTED FOR CLINICAL USE. We hope you will join our GitHub communities and contribute.
							</div>

							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-md-4 control-label">E-Mail Address</label>

								<div class="col-md-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

									@if ($errors->has('email'))
										<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">
										<i class="fa fa-btn fa-sign-in"></i> Reset Demo
									</button>
								</div>
							</div>
						</form>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$("#email").focus();
		$("#advanced").click(function(){
			$("#hidden_form").show();
		});
	});
</script>
@endsection
