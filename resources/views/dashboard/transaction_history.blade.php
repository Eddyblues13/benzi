@include('dashboard.header')
<div class="content-body">
    <div class="container-fluid">
        @include('dashboard.partials.alerts')
        <h2 class="text-black font-w600 mb-4">Recent Transaction Activities</h2>
        <div class="card">
            <div class="card-body">
                @forelse($details as $detail)
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom py-3">
                    <div class="me-3">
                        <h5 class="mb-1">{{ $detail->transaction_ref }}</h5>
                        <p class="m-0 text-muted">{{ $detail->created_at?->format('D, M j, Y g:i A') }}</p>
                        <p class="m-0 text-primary">{{ $detail->transaction_description }}</p>
                    </div>
                    <div class="me-3">
                        <b>Amount</b>
                        @if($detail->transaction_type == 'Debit')
                        <h5 class="text-danger mb-0">-{{ Auth::user()->currency }}{{ number_format($detail->transaction_amount, 2) }}</h5>
                        @else
                        <h5 class="text-success mb-0">+{{ Auth::user()->currency }}{{ number_format($detail->transaction_amount, 2) }}</h5>
                        @endif
                        <small class="{{ $detail->transaction_status == 1 ? 'text-success' : ($detail->transaction_status == 2 ? 'text-danger' : 'text-warning') }}">{{ $detail->statusLabel() }}</small>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $detail->transaction ?: $detail->transaction_type }}</h6>
                        <a href="{{ route('view.invoice', $detail->id) }}" class="btn btn-primary btn-xs">View Details</a>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted my-4">No transactions yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@include('dashboard.footer')
