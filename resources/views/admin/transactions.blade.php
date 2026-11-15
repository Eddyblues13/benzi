@include('admin.header')
@include('admin.navbar')

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

    <!-- Main header starts -->
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-window-split"></i>
            </div>
            <div class="page-title d-none d-md-block">
                <h5>
                    @if($filter_type) {{ $filter_type }} Transactions
                    @elseif($filter_status === '0') Pending Approval
                    @else Transaction History @endif
                </h5>
            </div>
        </div>
    </div>
    <!-- Main header ends -->

    <!-- Content wrapper start -->
    <div class="content-wrapper">
        @include('admin.partials.alerts')

        <div class="row gx-3">
            <div class="col-sm-12 col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="card-title">Transactions ({{ $user_transactions->count() }})</div>
                        <form method="GET" action="{{ route('user.transaction') }}" class="d-flex gap-2">
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All types</option>
                                @foreach(['Deposit', 'Loan', 'Credit', 'Debit', 'Card', 'Bank Transfer', 'Local Transfer', 'International Transfer', 'Crypto Withdrawal', 'Paypal Withdrawal', 'Skrill Withdrawal', 'Revolut Withdrawal', 'Wise Withdrawal'] as $type)
                                <option value="{{ $type }}" @selected($filter_type === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Any status</option>
                                <option value="0" @selected($filter_status === '0')>Pending</option>
                                <option value="1" @selected($filter_status === '1')>Completed</option>
                                <option value="2" @selected($filter_status === '2')>Declined</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="{{ $user_transactions->isEmpty() ? 'transactionsEmpty' : 'highlightRowColumn' }}" class="table custom-table align-middle" data-order="[]">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Customer</th>
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
                                            @if($transaction->user)
                                            <a href="{{ route('admin.user.profile', $transaction->user_id) }}">{{ $transaction->user->name }}</a>
                                            @else
                                            <span class="text-muted">Deleted user</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->transaction ?: $transaction->transaction_type }}
                                            <div class="small {{ $transaction->transaction_type === 'Debit' ? 'text-danger' : 'text-success' }}">{{ $transaction->transaction_type }}</div>
                                        </td>
                                        <td>
                                            {{ $transaction->transaction_description }}
                                            @include('admin.partials.transaction-details')
                                        </td>
                                        <td class="text-nowrap">{{ $transaction->user?->currency }}{{ number_format($transaction->transaction_amount, 2) }}</td>
                                        <td>@include('admin.partials.transaction-status')</td>
                                        <td class="text-nowrap">{{ $transaction->created_at?->format('D, M j, Y g:i A') }}</td>
                                        <td class="text-nowrap">@include('admin.partials.transaction-actions')</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No transactions found.</td>
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

@include('admin.footer')
