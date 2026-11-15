{{-- Expects $rows (collection of Transaction) and optional $tableId. --}}
<div class="table-responsive">
    <table id="{{ $tableId ?? 'example5' }}" class="table display mb-0">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th class="text-center">Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td><span class="text-black">{{ $row->transaction_ref }}</span></td>
                <td><span class="text-black">{{ $row->transaction ?: $row->transaction_type }}</span></td>
                <td><span class="text-black">{{ $row->transaction_description }}</span></td>
                <td>
                    @if($row->transaction_type === 'Debit')
                    <span class="text-danger font-w500 text-nowrap">-{{ Auth::user()->currency }}{{ number_format($row->transaction_amount, 2) }}</span>
                    @else
                    <span class="text-success font-w500 text-nowrap">+{{ Auth::user()->currency }}{{ number_format($row->transaction_amount, 2) }}</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($row->transaction_status == 1)
                    <span class="badge light badge-success">Completed</span>
                    @elseif($row->transaction_status == 2)
                    <span class="badge light badge-danger">Declined</span>
                    @else
                    <span class="badge light badge-warning">Pending</span>
                    @endif
                </td>
                <td><span class="text-black text-nowrap">{{ $row->created_at?->format('D, M j, Y g:i A') }}</span></td>
                <td><a href="{{ route('view.invoice', $row->id) }}" class="btn btn-primary btn-xs">Details</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">No transactions yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
