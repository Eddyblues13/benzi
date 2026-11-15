@include('dashboard.header')
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        @include('dashboard.partials.alerts')

        <div class="form-head mb-4">
            <h2 class="text-black font-w600 mb-0">Dashboard</h2>
        </div>
        <div class="row">

            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>Welcome <span class="text-danger">{{Auth::user()->first_name}}
                                        {{Auth::user()->last_name}},</span></h3>
                                <h6>Total Balance</h6>
                                <h3 class="fw-bold text-black">{{Auth::user()->currency}}{{number_format($balance, 2)}}
                                </h3>
                                <small class="text-muted">Account number: {{Auth::user()->a_number}}</small>
                            </div>
                            <div style="padding: 10px 30px;" class="align-items-center justify-content-center d-flex bg-warning rounded">
                                <a href="{{route('deposit')}}" title="Deposit"><span class="fw-bold text-white">&plus;</span></a>
                            </div>
                        </div>
                        <hr class="hr">
                        <div class="grid-container-two">
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-danger">
                                    <a href="{{route('inter.bank.transfer')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/bank.png')}}" alt=""></a>
                                </div>
                                <a href="{{route('inter.bank.transfer')}}"><span>International Bank Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-warning">
                                    <a href="{{route('crypto')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/crypto.png')}}" alt=""></a>
                                </div>
                                <a href="{{route('crypto')}}"><span>Crypto Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-info">
                                    <a href="{{route('paypal')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/paypal.png')}}" alt=""></a>
                                </div>
                                <span><a href="{{route('paypal')}}">Paypal Withdrawal</a></span>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-success">
                                    <a href="{{route('loan')}}"><img class="svg-icon"
                                            src="{{asset('user/images/loan.png')}}" alt=""></a>
                                </div>
                                <span><a href="{{route('loan')}}">Loan</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="grid-container-two">
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-danger">
                                    <a href="{{route('local.bank.transfer')}}">&darr;</a>
                                </div>
                                <a href="{{route('local.bank.transfer')}}"><span>Local Bank Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-warning">
                                    <a href="{{route('revolut.bank.transfer')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/revolut.png')}}" alt=""></a>
                                </div>
                                <a href="{{route('revolut.bank.transfer')}}"><span>Revolut Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-info">
                                    <a href="{{route('wise.bank.transfer')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/wise.png')}}" alt=""></a>
                                </div>
                                <span><a href="{{route('wise.bank.transfer')}}">Wise Withdrawal</a></span>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-success">
                                    <a href="{{route('logOut')}}"><img class="svg-icon"
                                            src="{{asset('user/images/logout.jpg')}}" alt=""></a>
                                </div>
                                <span><a href="{{route('logOut')}}">Logout</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-container">
                <div class="card">
                    <div class="card-body">
                        <h6>Available Balance</h6>
                        <h3 class="fw-bold text-black">{{Auth::user()->currency}}{{number_format($balance, 2)}}</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6>Account Type</h6>
                        <h3 class="fw-bold text-black">{{Auth::user()->account_type ?: 'Savings'}}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body pb-1">
                        <h4 class="fs-20 text-black mb-4">Account Overview</h4>
                        <div class="grid-container">
                            <div class="col-12">
                                <div class="bg-secondary rounded text-center p-3 mb-3">
                                    <h2 class="text-white mb-1">{{ $overview['withdrawals'] }}</h2>
                                    <span class="fs-14 text-white d-block">Withdrawals</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-success rounded text-center p-3 mb-3">
                                    <h2 class="text-white mb-1">{{ $overview['transfers'] }}</h2>
                                    <span class="fs-14 text-white d-block">Completed Transfers</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="border border-2 border-primary rounded text-center p-3 mb-3">
                                    <h2 class="text-black mb-1">{{ $overview['cards'] }}</h2>
                                    <span class="fs-14 text-black d-block">Virtual Cards</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-info rounded text-center p-3 mb-3">
                                    <h2 class="text-white mb-1">{{ $overview['loans'] }}</h2>
                                    <span class="fs-14 text-white d-block">Loans</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 text-black mb-0">Recent Transactions</h4>
                        <a href="{{ route('transactions') }}" class="btn btn-outline-primary btn-sm">View all</a>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partials.transaction-table', ['rows' => $recent_transactions, 'tableId' => 'recentTransactions'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.footer')
