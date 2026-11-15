{{-- Beneficiary / destination details captured with a transfer. Expects $transaction. --}}
@php
    $detailLabels = [
        'account_name' => 'Name', 'account_number' => 'Acct', 'bank_name' => 'Bank',
        'routing_number' => 'Routing', 'iban_number' => 'IBAN', 'swift_code' => 'SWIFT',
        'bank_address' => 'Bank addr', 'country' => 'Country', 'recipient_email' => 'Email',
        'card_number' => 'Card', 'wallet_type' => 'Wallet', 'wallet_address' => 'Address',
    ];
@endphp
@foreach($detailLabels as $field => $label)
@if($transaction->{$field})
<div class="small text-break"><span class="text-muted">{{ $label }}:</span> {{ $transaction->{$field} }}</div>
@endif
@endforeach
