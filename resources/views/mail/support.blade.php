<div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 22px; color: #222;">
    <h2 style="margin: 0 0 16px;">New support ticket</h2>
    <p style="margin: 0 0 4px;"><strong>From:</strong> {{ $data['name'] }} &lt;{{ $data['email'] }}&gt;</p>
    <p style="margin: 0 0 4px;"><strong>Account number:</strong> {{ $data['a_number'] ?? '-' }}</p>
    <p style="margin: 0 0 16px;"><strong>Subject:</strong> {{ $data['subject'] }}</p>
    <div style="padding: 12px 16px; background: #f5f5f5; border-radius: 6px; white-space: pre-wrap;">{{ $data['message'] }}</div>
</div>
