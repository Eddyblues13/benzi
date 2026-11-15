@include('dashboard.header')
<div class="content-body">
    <div class="container-fluid">
        @include('dashboard.partials.alerts')
        <h2 class="text-black font-w600 mb-4">Transaction History</h2>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @include('dashboard.partials.transaction-table', ['rows' => $transaction, 'tableId' => 'example5'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.footer')
