@include('admin.header')
@include('admin.navbar')
<!-- Content wrapper start -->
<div class="content-wrapper">
	<!-- Row start -->
	<div class="row gx-3">
		<div class="col-sm-4 col-12">
			<div class="card card-cover rounded-2">
				<div class="contact-card">
					@if($userProfile->user_status==="0")
					<a href="{{route('verify_user',$userProfile->id)}}" class="edit-contact-card btn btn-danger">
						<i class="bi bi-pencil">verify</i>
					</a>
					@elseif($userProfile->user_status==="1")
					<a href="#" class="edit-contact-card btn btn-success">
						<i class="bi bi-pencil">verified</i>
					</a>
					@endif

					<img src="{{ asset('uploads/display/' . ($userProfile->display_picture ? $userProfile->display_picture : 'avatar.png')) }}"
						alt="User Avatar" class="contact-avatar" />
					<h5>{{$userProfile->first_name}} {{$userProfile->last_name}}</h5>
					<ul class="list-group">
						<li class="list-group-item">
							<span>Email: </span>{{$userProfile->email}}
						</li>
						<li class="list-group-item">
							<span>Account Number: </span>{{$userProfile->a_number}}
						</li>
						<li class="list-group-item">
							<span>Phone: </span>{{$userProfile->phone_number}}
						</li>
						<li class="list-group-item">
							<span>Country: </span>{{$userProfile->country}}
						</li>
						<li class="list-group-item">
							<span>OTP: </span>{{$userProfile->otp}}
						</li>
						<li class="list-group-item">
							<span>CCIC Code: </span>{{$userProfile->ccic_code}}
						</li>
						<li class="list-group-item">
							<span>INT Code: </span>{{$userProfile->int_code}}
						</li>
						<li class="list-group-item">
							<span>Account Balance: </span>{{$userProfile->currency}}{{number_format($balance, 2)}}
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-sm-8 col-12">
			<!-- Security Codes Management Card -->
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Security Codes Management</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<!-- OTP Update Card -->
						<div class="col-md-4 mb-3">
							<div class="card bg-light">
								<div class="card-body text-center">
									<h6 class="card-title">OTP Code</h6>
									<p class="card-text display-6">{{$userProfile->otp}}</p>
									<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
										data-bs-target="#updateOtpModal">
										Update OTP
									</button>
								</div>
							</div>
						</div>

						<!-- CCIC Code Update Card -->
						<div class="col-md-4 mb-3">
							<div class="card bg-light">
								<div class="card-body text-center">
									<h6 class="card-title">CCIC Code</h6>
									<p class="card-text display-6">{{$userProfile->ccic_code}}</p>
									<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
										data-bs-target="#updateCcicModal">
										Update CCIC
									</button>
								</div>
							</div>
						</div>

						<!-- INT Code Update Card -->
						<div class="col-md-4 mb-3">
							<div class="card bg-light">
								<div class="card-body text-center">
									<h6 class="card-title">INT Code</h6>
									<p class="card-text display-6">{{$userProfile->int_code}}</p>
									<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
										data-bs-target="#updateIntModal">
										Update INT
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Account Actions Section -->
			<div class="card mt-3">
				<div class="card-header">
					<h5 class="card-title">Account Actions</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3 mb-2">
							<button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
								data-bs-target="#creditModal">
								Credit Account
							</button>
						</div>
						<div class="col-md-3 mb-2">
							<button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
								data-bs-target="#debitModal">
								Debit Account
							</button>
						</div>
						<div class="col-md-3 mb-2">
							<button type="button" class="btn btn-info w-100" data-bs-toggle="modal"
								data-bs-target="#updatePasswordModal">
								Update Password
							</button>
						</div>
						<div class="col-md-3 mb-2">
							<a href="{{route('verify_user',$userProfile->id)}}"
								class="btn {{$userProfile->user_status === '0' ? 'btn-warning' : 'btn-secondary'}} w-100">
								{{$userProfile->user_status === '0' ? 'Verify User' : 'User Verified'}}
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Transaction History Section -->
	<div class="row mt-3">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title">Transaction History</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="highlightRowColumn" class="table custom-table">
							<thead>
								<tr>
									<th>Transaction ID</th>
									<th>Transaction Type</th>
									<th>Description</th>
									<th>Amount</th>
									<th>Status</th>
									<th>Date</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($user_transactions as $transaction)
								<tr>
									<td>{{$transaction->transaction_ref}}</td>
									<td>{{$transaction->transaction_type}}</td>
									<td>{{$transaction->transaction_description}}</td>
									<td>{{$userProfile->currency}}{{number_format($transaction->transaction_amount, 2)}}
									</td>
									<td>
										@if ($transaction->transaction_status == '1')
										<span class="badge bg-success">Completed</span>
										@elseif($transaction->transaction_status == '0')
										<span class="badge bg-warning">Pending</span>
										@endif
									</td>
									<td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('D, M j, Y g:i A') }}
									</td>
									<td>
										<div class="btn-group">
											<form action="{{url('approve-transaction/'.$transaction->id)}}"
												method="POST" class="d-inline">
												@csrf
												<input type="hidden" name="status" value="1">
												<input type="hidden" name="user_id" value="{{$userProfile->id}}">
												<input type="hidden" name="email" value="{{$userProfile->email}}" />
												<input type="hidden" name="name"
													value="{{$userProfile->first_name}} {{$userProfile->last_name}}" />
												<input type="hidden" name="id" value="{{$userProfile->id}}" />
												<input type="hidden" name="ref"
													value="{{$transaction->transaction_ref}}" />
												<input type="hidden" name="a_number"
													value="{{$userProfile->a_number}}" />
												<input type="hidden" name="currency"
													value="{{$userProfile->currency}}" />
												<input type="hidden" name="description"
													value="{{$transaction->transaction_description}}" />
												<input type="hidden" name="amount" value="{{$transaction->amount}}">
												<button type="submit" class="btn btn-sm btn-success">Approve</button>
											</form>
											<form action="{{url('decline-transaction/'.$transaction->id)}}"
												method="POST" class="d-inline">
												@csrf
												<input type="hidden" name="status" value="2">
												<input type="hidden" name="user_id" value="{{$userProfile->id}}">
												<input type="hidden" name="email" value="{{$userProfile->email}}" />
												<input type="hidden" name="name"
													value="{{$userProfile->first_name}} {{$userProfile->last_name}}" />
												<input type="hidden" name="id" value="{{$userProfile->id}}" />
												<input type="hidden" name="ref"
													value="{{$transaction->transaction_ref}}" />
												<input type="hidden" name="a_number"
													value="{{$userProfile->a_number}}" />
												<input type="hidden" name="currency"
													value="{{$userProfile->currency}}" />
												<input type="hidden" name="description"
													value="{{$transaction->transaction_description}}" />
												<input type="hidden" name="amount" value="{{$transaction->amount}}">
												<button type="submit" class="btn btn-sm btn-danger">Decline</button>
											</form>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Individual Code Update Modals -->

<!-- Update OTP Modal -->
<div class="modal fade" id="updateOtpModal" tabindex="-1" aria-labelledby="updateOtpModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateOtpModalLabel">Update OTP Code for {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{route('update.otp',$userProfile->id)}}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="otpInput" class="form-label">New OTP Code</label>
						<input type="number" class="form-control" id="otpInput" name="otp" value="{{$userProfile->otp}}"
							required>
						<div class="form-text">Current OTP: <strong>{{$userProfile->otp}}</strong></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update OTP</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update CCIC Modal -->
<div class="modal fade" id="updateCcicModal" tabindex="-1" aria-labelledby="updateCcicModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateCcicModalLabel">Update CCIC Code for {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{route('update.cic',$userProfile->id)}}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="ccicInput" class="form-label">New CCIC Code</label>
						<input type="number" class="form-control" id="ccicInput" name="cic_code"
							value="{{$userProfile->ccic_code}}" required>
						<div class="form-text">Current CCIC: <strong>{{$userProfile->ccic_code}}</strong></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update CCIC</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update INT Modal -->
<div class="modal fade" id="updateIntModal" tabindex="-1" aria-labelledby="updateIntModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateIntModalLabel">Update INT Code for {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{route('update.int',$userProfile->id)}}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="intInput" class="form-label">New INT Code</label>
						<input type="number" class="form-control" id="intInput" name="int_code"
							value="{{$userProfile->int_code}}" required>
						<div class="form-text">Current INT: <strong>{{$userProfile->int_code}}</strong></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update INT</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Account Action Modals -->

<!-- Credit Modal -->
<div class="modal fade" id="creditModal" tabindex="-1" aria-labelledby="creditModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="creditModalLabel">Credit Account - {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{route('credit.user')}}" method="POST">
				@csrf
				<div class="modal-body">
					<input type="hidden" name="email" value="{{$userProfile->email}}" />
					<input type="hidden" name="name" value="{{$userProfile->first_name}} {{$userProfile->last_name}}" />
					<input type="hidden" name="id" value="{{$userProfile->id}}" />
					<input type="hidden" name="balance" value="{{$balance}}" />
					<input type="hidden" name="a_number" value="{{$userProfile->a_number}}" />
					<input type="hidden" name="currency" value="{{$userProfile->currency}}" />

					<div class="mb-3">
						<label class="form-label">Amount ({{$userProfile->currency}})</label>
						<input type="number" step="0.01" name="amount" class="form-control" placeholder="Enter amount"
							required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea name="description" class="form-control" rows="3" placeholder="Transaction description"
							required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-success">Credit Account</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Debit Modal -->
<div class="modal fade" id="debitModal" tabindex="-1" aria-labelledby="debitModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="debitModalLabel">Debit Account - {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{route('debit.user')}}" method="POST">
				@csrf
				<div class="modal-body">
					<input type="hidden" name="email" value="{{$userProfile->email}}" />
					<input type="hidden" name="name" value="{{$userProfile->first_name}} {{$userProfile->last_name}}" />
					<input type="hidden" name="id" value="{{$userProfile->id}}" />
					<input type="hidden" name="balance" value="{{$balance}}" />
					<input type="hidden" name="a_number" value="{{$userProfile->a_number}}" />
					<input type="hidden" name="currency" value="{{$userProfile->currency}}" />

					<div class="mb-3">
						<label class="form-label">Amount ({{$userProfile->currency}})</label>
						<input type="number" step="0.01" name="amount" class="form-control" placeholder="Enter amount"
							required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea name="description" class="form-control" rows="3" placeholder="Transaction description"
							required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger">Debit Account</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Update Password Modal -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updatePasswordModalLabel">Update Password - {{$userProfile->first_name}}
					{{$userProfile->last_name}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('reset.password', $userProfile->id) }}" method="POST">
				@csrf
				<div class="modal-body">
					<input type="hidden" name="email" value="{{$userProfile->email}}" />
					<input type="hidden" name="name" value="{{$userProfile->first_name}} {{$userProfile->last_name}}" />
					<input type="hidden" name="id" value="{{$userProfile->id}}" />

					<div class="mb-3">
						<label class="form-label">New Password</label>
						<input type="password" name="password" class="form-control" placeholder="Enter new password"
							required>
					</div>
					<div class="mb-3">
						<label class="form-label">Confirm Password</label>
						<input type="password" name="password_confirmation" class="form-control"
							placeholder="Confirm new password" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update Password</button>
				</div>
			</form>
		</div>
	</div>
</div>

@include('admin.footer')