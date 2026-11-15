@include('dashboard.header')
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-black font-w600 mb-0">Transaction Receipt</h2>
            <a href="{{ route('transactions') }}" class="btn btn-outline-primary btn-sm">Back to transactions</a>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ $invoice->transaction ?: $invoice->transaction_type }} &middot; {{ $invoice->transaction_ref }}</h4>
                        @if($invoice->transaction_status == 1)
                        <span class="badge light badge-success">Completed</span>
                        @elseif($invoice->transaction_status == 2)
                        <span class="badge light badge-danger">Declined</span>
                        @else
                        <span class="badge light badge-warning">Pending</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-1">Account holder</h6>
                                <p class="mb-0 text-black">{{ Auth::user()->name }}</p>
                                <p class="mb-0">{{ Auth::user()->a_number }}</p>
                                <p class="mb-0">{{ Auth::user()->address }}</p>
                            </div>
                            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                <h6 class="text-muted mb-1">Date</h6>
                                <p class="mb-0 text-black">{{ $invoice->created_at?->format('D, M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%">Description</th>
                                        <td>{{ $invoice->transaction_description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Direction</th>
                                        <td>{{ $invoice->transaction_type }}</td>
                                    </tr>
                                    @foreach([
                                        'account_name' => 'Beneficiary name',
                                        'account_number' => 'Account number',
                                        'bank_name' => 'Bank name',
                                        'routing_number' => 'Routing number',
                                        'iban_number' => 'IBAN',
                                        'swift_code' => 'SWIFT / BIC',
                                        'bank_address' => 'Bank address',
                                        'country' => 'Country',
                                        'recipient_email' => 'Recipient email',
                                        'card_number' => 'Card number',
                                        'wallet_type' => 'Wallet type',
                                        'wallet_address' => 'Wallet address',
                                    ] as $field => $label)
                                    @if($invoice->{$field})
                                    <tr>
                                        <th>{{ $label }}</th>
                                        <td class="text-break">{{ $invoice->{$field} }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $invoice->statusLabel() }}</td>
                                    </tr>
                                    <tr>
                                        <th><h5 class="mb-0">Amount</h5></th>
                                        <td><h5 class="mb-0 {{ $invoice->transaction_type === 'Debit' ? 'text-danger' : 'text-success' }}">
                                            {{ $invoice->transaction_type === 'Debit' ? '-' : '+' }}{{ Auth::user()->currency }}{{ number_format($invoice->transaction_amount, 2) }}
                                        </h5></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button onclick="window.print()" class="btn btn-outline-dark">Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.footer')
