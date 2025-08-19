@include('dashboard.header')
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4">
            <h2 class="text-black font-w600 mb-0">Dashboard</h2>
        </div>
        <div class="row">

            <div class="col-sm-12col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>Welcome <span class="text-danger">{{Auth::user()->first_name}}
                                        {{Auth::user()->last_name}},</span></h3>
                                <h6>Total Balance</h6>
                                <h3 class="fw-bold text-black">{{Auth::user()->currency}}{{number_format($balance, 2)}}
                                </h3>
                            </div>
                            <div style="padding: 10px 30px;" class="align-items-center justify-content-center
									 d-flex bg-warning rounded">
                                <a href="{{route('deposit')}}"><span class="fw-bold text-white">&plus;</span></a>
                            </div>
                        </div>
                        <hr class="hr">
                        <div class="grid-container-two">
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-danger">
                                    <a href="{{route('inter.bank.transfer')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/bank.png')}}"></a>
                                </div>
                                <a href="{{route('inter.bank.transfer')}}"><span>International Bank Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-warning">
                                    <a href="{{route('crypto')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/crypto.png')}}"></a>
                                </div>
                                <a href="{{route('crypto')}}"><span>Crypto Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-info">
                                    <a href="{{route('paypal')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/paypal.png')}}"></a>
                                </div>
                                <span><a href="{{route('paypal')}}">Paypal Withdrawal</a></span>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-success">
                                    <a href="{{route('loan')}}"><img class="svg-icon"
                                            src="{{asset('user/images/loan.png')}}"></a>
                                </div>
                                <span><a href="{{route('loan')}}">Loan</a></span>
                            </div>
                        </div>
                    </div>

                </div>
                
            </div>
                    <div class="col-sm-12col-lg-12">
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
                                            src="{{asset('user/images/revolut.png')}}"></a>
                                </div>
                                <a href="{{route('revolut.bank.transfer')}}"><span>Revolut Transfer</span></a>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-info">
                                    <a href="{{route('wise.bank.transfer')}}"><img style="color:white; width:30px;"
                                            src="{{asset('user/images/wise.png')}}"></a>
                                </div>
                                <span><a href="{{route('wise.bank.transfer')}}">Wise Withdrawal</a></span>
                            </div>
                            <div class="d-block text-center">
                                <div class="withdraw-icon bg-success">
                                    <a href="{{route('logOut')}}"><img class="svg-icon"
                                            src="{{asset('user/images/logout.jpg')}}"></a>
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
                        <h6>Refundable Balance</h6>
                        <h3 class="fw-bold text-black">{{Auth::user()->currency}}{{number_format($balance, 2)}}</h3>
                    </div>
                </div>
            </div>

            <!-- <div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
								<div class="card-bx stacked">
									<img src="images/card/card.png" alt="" class="mw-100">
									<div class="card-info text-white">
										<p class="mb-1">Main Balance</p>
										<h2 class="fs-36 text-white mb-sm-4 mb-3">{{Auth::user()->currency}}{{number_format($balance, 2, '.', ',')}}</h2>
										<div class="d-flex align-items-center justify-content-between mb-sm-5 mb-3">
											<img src="images/dual-dot.png" alt="" class="dot-img">
											<h4 class="fs-20 text-white mb-0">8717916732</h4>
										</div>
										<div class="d-flex">
											<div class="me-5">
												<p class="fs-14 mb-1 op6">ACCOUNT TYPE</p>
												<span>Savings Account</span>
											</div>
											<div>
												<p class="fs-14 mb-1 op6">CARD HOLDER</p>
												<span>Blues Wayne</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> 

                    <!-- <div class="grid-container-two">
						<div class="col-xl-12 col-sm-12">
							<div class="card bg-danger">
								<div class="card-body">
									<div class="media align-items-center invoice-card">
										<div class="media-body text-center">
											<a href="./withdrawal" class="withdraw-icon text-center">
												&darr;
											</a>
										</div>
										<a href="./withdrawal" class="p-1 ms-3">
											<i class="flaticon-381-exit-1 fs-36 text-danger"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-12 col-sm-12">
							<div class="card">
								<div class="card-body">
									<div class="media align-items-center invoice-card">
										<div class="media-body">
											<a href="{{route('deposit')}}" class="withdraw-icon">Dep</a>
										</div>
										<a href="{{route('deposit')}}" class="p-1 ms-3">
											<i class="flaticon-381-send fs-36 text-success"></i>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12 col-sm-12">
							<div class="card">
								<div class="card-body">
									<div class="media align-items-center invoice-card">
										<div class="media-body">
											<a href="{{route('deposit')}}" class="withdraw-icon">Dep</a>
										</div>
										<a href="{{route('deposit')}}" class="p-1 ms-3">
											<i class="flaticon-381-send fs-36 text-success"></i>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12 col-sm-12">
							<div class="card">
								<div class="card-body">
									<div class="media align-items-center invoice-card">
										<div class="media-body">
											<a href="{{route('deposit')}}" class="withdraw-icon">De</a>
										</div>
										<a href="{{route('deposit')}}" class="p-1 ms-3">
											<i class="flaticon-381-send fs-36 text-success"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div> -->


            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body pb-1">
                        <div class="row align-items-center">
                            <div class="col-xl-5 col-xxl-12 col-md-5">
                                <h4 class="fs-20 text-black mb-4">Account Overview</h4>
                                <div class="row">

                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-sm-12 col-xxl-12 col-md-12">
                                <div class="grid-container">
                                    <div class="col-12">
                                        <div class="bg-secondary rounded text-center p-3">
                                            <div class="d-inline-block position-relative donut-chart-sale mb-3">
                                                <span class="donut1"
                                                    data-peity='{ "fill": ["rgb(255, 255, 255)", "rgba(255, 255, 255, 0.2)"],   "innerRadius": 33, "radius": 10}'>0/100</span>

                                            </div>
                                            <span class="fs-14 text-white d-block">Withdrawals</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="bg-success rounded text-center p-3">
                                            <div class="d-inline-block position-relative donut-chart-sale mb-3">
                                                <span class="donut1"
                                                    data-peity='{ "fill": ["rgb(255, 255, 255)", "rgba(255, 255, 255, 0.2)"],   "innerRadius": 33, "radius": 10}'>0/10</span>

                                            </div>
                                            <span class="fs-14 text-white d-block">Transfers</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border border-2 border-primary rounded text-center p-3">
                                            <div class="d-inline-block position-relative donut-chart-sale mb-3">
                                                <span class="donut1"
                                                    data-peity='{ "fill": ["rgb(250, 48, 56)", "rgba(234, 234, 234, 1)"],   "innerRadius": 33, "radius": 10}'>10/100</span>

                                            </div>
                                            <span class="fs-14 text-black d-block">Investments</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="bg-info rounded text-center p-3">
                                            <div class="d-inline-block position-relative donut-chart-sale mb-3">
                                                <span class="donut1"
                                                    data-peity='{ "fill": ["rgb(255, 255, 255)", "rgba(255, 255, 255, 0.2)"],   "innerRadius": 33, "radius": 10}'>0/100</span>

                                            </div>
                                            <span class="fs-14 text-white d-block">Loans</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.footer')