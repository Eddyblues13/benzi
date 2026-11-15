@include('admin.header')
@include('admin.navbar')

@php
    $isVerified = (string) $userProfile->user_status === '1';
    $fullName = trim($userProfile->first_name . ' ' . $userProfile->last_name);
@endphp

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

	<!-- Main header starts -->
	<div class="main-header d-flex align-items-center justify-content-between position-relative">
		<div class="d-flex align-items-center justify-content-center">
			<div class="page-icon">
				<i class="bi bi-person"></i>
			</div>
			<div class="page-title d-none d-md-block">
				<h5>{{ $fullName }}</h5>
			</div>
		</div>
		<a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Back to users</a>
	</div>
	<!-- Main header ends -->

	<!-- Content wrapper start -->
	<div class="content-wrapper">
		@include('admin.partials.alerts')

		<div class="row gx-3">
			<div class="col-xl-4 col-12">
				<div class="card card-cover rounded-2">
					<div class="contact-card">
						<form action="{{ route('verify_user', $userProfile->id) }}" method="POST" class="edit-contact-card">
							@csrf
							<button type="submit" class="btn btn-sm {{ $isVerified ? 'btn-success' : 'btn-danger' }}"
								title="{{ $isVerified ? 'Click to remove verification' : 'Click to verify this user' }}">
								<i class="bi {{ $isVerified ? 'bi-patch-check' : 'bi-patch-exclamation' }}"></i>
								{{ $isVerified ? 'Verified' : 'Verify' }}
							</button>
						</form>

						<img src="{{ asset('uploads/display/' . ($userProfile->display_picture ?: 'avatar.png')) }}"
							alt="User Avatar" class="contact-avatar" />
						<h5>{{ $fullName }}</h5>
						<ul class="list-group">
							<li class="list-group-item"><span>Email: </span>{{ $userProfile->email }}</li>
							<li class="list-group-item"><span>Account Number: </span>{{ $userProfile->a_number }}</li>
							<li class="list-group-item"><span>Account Type: </span>{{ $userProfile->account_type }}</li>
							<li class="list-group-item"><span>Phone: </span>{{ $userProfile->phone_number }}</li>
							<li class="list-group-item"><span>Country: </span>{{ $userProfile->country }}</li>
							<li class="list-group-item"><span>Address: </span>{{ $userProfile->address }}</li>
							<li class="list-group-item"><span>Email activated: </span>{{ (string) $userProfile->is_activated === '1' ? 'Yes' : 'No' }}</li>
							<li class="list-group-item"><span>Joined: </span>{{ $userProfile->created_at?->format('M j, Y') }}</li>
							<li class="list-group-item"><span>Account Balance: </span><strong>{{ $userProfile->currency }}{{ number_format($balance, 2) }}</strong></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="col-xl-8 col-12">
				<!-- Security Codes Management Card -->
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Security Codes &amp; Loan Settings</h5>
					</div>
					<div class="card-body">
						<p class="text-muted small mb-3">Customers must enter the OTP, CCIC and INT codes to submit any transfer. Loans require the SSN and are capped at the eligible amount.</p>
						<div class="row">
							@foreach([
								['OTP Code', $userProfile->otp, '#updateOtpModal'],
								['CCIC Code', $userProfile->ccic_code, '#updateCcicModal'],
								['INT Code', $userProfile->int_code, '#updateIntModal'],
								['SSN', $userProfile->ssn, '#updateSsnModal'],
								['Eligible Loan', $userProfile->currency . number_format((float) $userProfile->eligible_loan, 2), '#updateLoanModal'],
							] as [$label, $value, $modal])
							<div class="col-md-4 col-sm-6 mb-3">
								<div class="card bg-light h-100 mb-0">
									<div class="card-body text-center">
										<h6 class="card-title">{{ $label }}</h6>
										<p class="card-text fs-4 text-break">{{ $value !== null && $value !== '' ? $value : 'Not set' }}</p>
										<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="{{ $modal }}">
											Update
										</button>
									</div>
								</div>
							</div>
							@endforeach
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
							<div class="col-md-4 mb-2">
								<button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#creditModal">
									Credit Account
								</button>
							</div>
							<div class="col-md-4 mb-2">
								<button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#debitModal">
									Debit Account
								</button>
							</div>
							<div class="col-md-4 mb-2">
								<button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">
									Reset Password
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Transaction History Section -->
		<div class="row gx-3 mt-3">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="card-title">Transaction History</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="{{ $user_transactions->isEmpty() ? 'userTransactionsEmpty' : 'highlightRowColumn' }}" class="table custom-table align-middle" data-order="[]">
								<thead>
									<tr>
										<th>Reference</th>
										<th>Type</th>
										<th>Description / Details</th>
										<th>Amount</th>
										<th>Status</th>
										<th>Date</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									@forelse($user_transactions as $transaction)
									<tr>
										<td>{{ $transaction->transaction_ref }}</td>
										<td>
											{{ $transaction->transaction ?: $transaction->transaction_type }}
											<div class="small {{ $transaction->transaction_type === 'Debit' ? 'text-danger' : 'text-success' }}">{{ $transaction->transaction_type }}</div>
										</td>
										<td>
											{{ $transaction->transaction_description }}
											@include('admin.partials.transaction-details')
											@if($transaction->transaction === 'Deposit' && ($deposit = $deposits->get((int) $transaction->transaction_id)))
												@foreach(['front_cheque' => 'Front of cheque', 'back_cheque' => 'Back of cheque'] as $field => $label)
													@if($deposit->{$field})
													<div class="small"><a href="{{ asset('storage/' . $deposit->{$field}) }}" target="_blank" rel="noopener">{{ $label }}</a></div>
													@endif
												@endforeach
											@endif
										</td>
										<td class="text-nowrap">{{ $userProfile->currency }}{{ number_format($transaction->transaction_amount, 2) }}</td>
										<td>@include('admin.partials.transaction-status')</td>
										<td class="text-nowrap">{{ $transaction->created_at?->format('D, M j, Y g:i A') }}</td>
										<td class="text-nowrap">@include('admin.partials.transaction-actions')</td>
									</tr>
									@empty
									<tr>
										<td colspan="7" class="text-center text-muted py-4">No transactions yet.</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Content wrapper end -->
</div>
<!-- Content wrapper scroll end -->

<!-- Code / setting update modals -->
@foreach([
	['updateOtpModal', 'OTP Code', route('update.otp', $userProfile->id), 'otp', $userProfile->otp, 'text'],
	['updateCcicModal', 'CCIC Code', route('update.cic', $userProfile->id), 'cic_code', $userProfile->ccic_code, 'text'],
	['updateIntModal', 'INT Code', route('update.int', $userProfile->id), 'int_code', $userProfile->int_code, 'text'],
	['updateSsnModal', 'SSN', route('update.reflection', $userProfile->id), 'reflection', $userProfile->ssn, 'text'],
	['updateLoanModal', 'Eligible Loan Amount', route('update.eligible.loan', $userProfile->id), 'eligible_loan', $userProfile->eligible_loan, 'number'],
] as [$modalId, $label, $action, $field, $current, $type])
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="{{ $modalId }}Label">Update {{ $label }} for {{ $fullName }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ $action }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="{{ $modalId }}Input" class="form-label">New {{ $label }}</label>
						<input type="{{ $type }}" class="form-control" id="{{ $modalId }}Input" name="{{ $field }}"
							value="{{ $current }}" @if($type === 'number') step="0.01" min="0" @else maxlength="50" autocomplete="off" @endif required>
						<div class="form-text">Current: <strong>{{ $current !== null && $current !== '' ? $current : 'Not set' }}</strong></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endforeach

<!-- Credit / Debit modals -->
@foreach([['creditModal', 'Credit', route('credit.user'), 'btn-success'], ['debitModal', 'Debit', route('debit.user'), 'btn-danger']] as [$modalId, $label, $action, $btn])
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="{{ $modalId }}Label">{{ $label }} Account - {{ $fullName }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ $action }}" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{ $userProfile->id }}" />
				<div class="modal-body">
					<p class="text-muted small">Current balance: {{ $userProfile->currency }}{{ number_format($balance, 2) }}. The customer is emailed a {{ strtolower($label) }} alert.</p>
					<div class="mb-3">
						<label class="form-label">Amount ({{ $userProfile->currency }})</label>
						<input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="Enter amount" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea name="description" class="form-control" rows="3" maxlength="255" placeholder="Transaction description" required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn {{ $btn }}">{{ $label }} Account</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endforeach

<!-- Reset Password Modal -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updatePasswordModalLabel">Reset Password - {{ $fullName }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('reset.password', $userProfile->id) }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">New Password</label>
						<input type="password" name="password" class="form-control" minlength="8" placeholder="At least 8 characters" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Confirm Password</label>
						<input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Confirm new password" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Reset Password</button>
				</div>
			</form>
		</div>
	</div>
</div>

@include('admin.footer')
