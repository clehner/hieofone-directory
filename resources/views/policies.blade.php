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
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="login_direct" {{ $login_direct }}> Anyone signed-in directly to this Authorization Server sees Everything</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#login_direct_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="login_direct_detail" class="collapse">
									<p>Any party that you invite directly to be an authorized user to this Authorization Server has access to your Protected Health Information (PHI).  For example, this can be a spouse, partner, family members, guardian, or attorney.</p>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="public_publish_directory" {!! $public_publish_directory !!}> Anyone can see where this resource is located in a Directory</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#public_publish_directory_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="public_publish_directory_detail" class="collapse">
									<p>Any party that has access to a Directory that you participate in can see where this resource is found.</p>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="private_publish_directory" {!! $private_publish_directory !!}> Only previously authorized users can see where this resource is located in a Directory</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#private_publish_directory_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="private_publish_directory_detail" class="collapse">
									<p>Only previously authorized users that has access to a Directory that you participate in can see where this resource is found.</p>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="last_activity" {!! $last_activity !!}> Publish the most recent date and time when this Authorization was active to a Directory</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#last_activity_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="last_activity_detail" class="collapse">
									<p>Activty of an authorizaion server refers to when a client attempts to view/edit a resource, adding/updating/removing a resource server, or registering new clients</p>
								</div>
							</div>
						</div>

						<!-- <div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="login_uport" {{ $login_uport }}> Anyone signed in via uPort may be able to see these Resources</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#login_uport_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="login_uport_detail" class="collapse">
									<p>uPort is a complete self-sovereign identity system built on Ethereum.  By checking this box, any new user with a uPort identity accessing this Authorization Server will notify you via SMS or Email for your decision for the uPort user's access to your Protected Health Information</p>
								</div>
							</div>
						</div> -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label><input type="checkbox" name="any_npi" {{ $any_npi }}> Anyone that has a National Provider Identifier (NPI) sees these Resources</label>
									<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#any_npi_detail" style="margin-left:20px">Details</button>
								</div>
								<div id="any_npi_detail" class="collapse">
									<p>All individual HIPAA covered healthcare providers have a National Provider Identifier, including:</p>
									<ul>
										<li>Physicians</li>
										<li>Pharmacists</li>
										<li>Physician assistants</li>
										<li>Midwives</li>
										<li>Nurse practitioners</li>
										<li>Nurse anesthetists</li>
										<li>Dentsits</li>
										<li>Denturists</li>
										<li>Chiropractors</li>
										<li>Podiatrists</li>
										<li>Naturopathic physicians</li>
										<li>Clinical social workers</li>
										<li>Professional counselors</li>
										<li>Psychologists</li>
										<li>Physical therapists</li>
										<li>Occupational therapists</li>
										<li>Pharmacy technicians</li>
										<li>Atheletic trainers</li>
									</ul>
									<p>By setting this as a default, you allow any healthcare provider, known or unknown at any given time to access and edit your protected health information.</p>
								</div>
							</div>
						</div>

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
