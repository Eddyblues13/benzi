{{-- Approve / decline buttons for a pending transaction. Expects $transaction. --}}
@if($transaction->transaction_status == 0)
<div class="btn-group">
    <form action="{{ route('approve.transaction', $transaction->id) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Approve {{ $transaction->transaction_ref }}?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-success">Approve</button>
    </form>
    <form action="{{ route('decline.transaction', $transaction->id) }}" method="POST" class="d-inline ms-1"
        onsubmit="return confirm('Decline {{ $transaction->transaction_ref }}?{{ $transaction->transaction_type === 'Debit' ? ' The amount will be returned to the customer\'s balance.' : '' }}');">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger">Decline</button>
    </form>
</div>
@else
<span class="text-muted">&mdash;</span>
@endif
