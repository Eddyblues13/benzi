@include('dashboard.header')
<style>
    .virtual-card {
        max-width: 380px;
        border-radius: 16px;
        padding: 24px;
        color: #fff;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 55%, #1eaae7 100%);
        box-shadow: 0 10px 24px rgba(30, 60, 114, .25);
    }
    .virtual-card .card-number { font-size: 1.35rem; letter-spacing: 2px; font-family: monospace; }
    .virtual-card small { opacity: .75; text-transform: uppercase; font-size: .7rem; letter-spacing: 1px; }
</style>
<div class="content-body">
    <div class="container-fluid">
        @include('dashboard.partials.alerts')

        <div class="form-head mb-4">
            <h2 class="text-black font-w600 mb-0">Virtual Card</h2>
            <p class="mb-0">Specifically designed for online transactions.</p>
        </div>

        <div class="row">
            @foreach($details as $card)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="virtual-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <strong>Benzipaid</strong>
                        <i class="fa fa-cc-visa fa-2x"></i>
                    </div>
                    <div class="card-number mb-4">{{ implode(' ', str_split($card->card_number, 4)) }}</div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Card holder</small>
                            <div>{{ Auth::user()->name }}</div>
                        </div>
                        <div>
                            <small>Expires</small>
                            <div>{{ $card->card_expiry?->format('m/y') }}</div>
                        </div>
                        <div>
                            <small>CVV</small>
                            <div>{{ $card->card_cvc }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-xl-8 col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-black">{{ $details->isEmpty() ? 'Get your virtual card' : 'Need another card?' }}</h4>
                        <p>The Premium Virtual Card is a digital payment card for frequent online shoppers: a secure,
                            flexible alternative to a physical card. It is issued instantly on request.</p>
                        <p class="mb-3"><strong>Issuing fee:</strong> {{ Auth::user()->currency }}{{ number_format($fee, 2) }}, deducted from your balance.</p>
                        <form action="{{ route('request.card') }}" method="POST"
                            onsubmit="return confirm('A fee of {{ Auth::user()->currency }}{{ number_format($fee, 2) }} will be deducted from your balance. Continue?');">
                            @csrf
                            <button type="submit" class="btn btn-success">Request a New Virtual Card</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.footer')
