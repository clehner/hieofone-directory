@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Default Policies for Trustee Authroization Servers Deployed By This Directory</div>
				<div class="panel-body">
					<div class="alert alert-info">
						<p>Below are polices that Trustee Authorization Servers will adopt as default when initially deployed</p>
						<p>These do not pertain to resources registered in the Trustee Authorizaion Server that the Directory itself will be able to access</p>
					</div>
					<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('change_policy') }}">
						<div style="text-align: center;">
							{!! $content !!}
						</div>
						{!! $policies !!}
						<div class="form-group">
							<div class="col-md-6 col-md-offset-3">
								<button type="submit" class="btn btn-success btn-block" name="submit" value="save">
									<i class="fa fa-btn fa-check"></i> Save
								</button>
								<button type="submit" class="btn btn-danger btn-block" name="submit" value="cancel">
									<i class="fa fa-btn fa-times"></i> Cancel
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('view.scripts')
<script type="text/javascript">
	$(document).ready(function() {
	});
</script>
@endsection
