@if ($transaction->transaction_status == 1)
<span class="badge bg-success">Completed</span>
@elseif ($transaction->transaction_status == 2)
<span class="badge bg-danger">Declined</span>
@else
<span class="badge bg-warning text-dark">Pending</span>
@endif
