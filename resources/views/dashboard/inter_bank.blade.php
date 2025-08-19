@include('dashboard.header')
<!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">
    @if (session('error'))
    <div class="alert alert-danger" role="alert">
        <b>Error!</b>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @elseif (session('status'))
    <div class="alert alert-success" role="alert">
        <b>Success!</b> {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="container-fluid">
        <h2 class="text-black font-w600 mb-0 me-auto mb-2 pe-3">International Bank Transfer</h2>

        <div class="row">
            <div class="col-xl-4">
                <div class="row">


                </div>
            </div>
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Balance: {{Auth::user()->currency}}{{number_format($balance, 2, '.',
                            ',')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <p>You're about to transfer from your account's available balance. This action cannot be
                                    reversed. Be sure to enter correct details.</p>
                                <div id="response_code"></div>
                                <form action="{{route('inter.transfer')}}" method="POST">
                                    @csrf
                                    <p id="server"></p>
                                    <div id="content-one">
                                        <div class="form-group mb-3">
                                            <label>Amount</label>
                                            <input id="pin_amount" type="number" name="amount" class="form-control"
                                                placeholder="Enter Amount" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Holder Name</label>
                                            <input id="pin_account_number" type="text" name="holder_name"
                                                class="form-control" placeholder="Holder Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Account Number</label>
                                            <input id="pin_account_number" type="text" name="account_number"
                                                class="form-control" placeholder="Account Number">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Bank Name</label>
                                            <input id="pin_account_number" type="text" name="bank_name"
                                                class="form-control" placeholder="Bank Name">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>IBAN Number</label>
                                            <input id="pin_account_number" type="text" name="iban_number"
                                                class="form-control" placeholder="IBAN Number">
                                        </div>



                                        <button type="button" id="waitone" class="btn btn-primary w-100"
                                            data-bs-toggle="modal" data-bs-target="#otpPin">Proceed</button>
                                    </div>

                                    <!-- Start modal -->
                                    <div class="modal fade transfer_pin_modal" id="otpPin">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">OTP Pin</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid OTP pin for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" name="otp"
                                                            class="form-control form-control-lg">
                                                    </div>
                                                    <button type="button" id="waitone" class="btn btn-primary w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#ccicCode">Proceed</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Start modal -->
                                    <div class="modal fade transfer_pin_modal" id="ccicCode">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">CCIC Code</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid CCIC Code for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" name="ccic_code"
                                                            class="form-control form-control-lg">
                                                    </div>
                                                    <button type="button" id="waitone" class="btn btn-primary w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#intCode">Proceed</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Start modal -->
                                    <div class="modal fade transfer_pin_modal" id="ccicCode">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">CCIC Code</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid CCIC Code for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" name="ccic_code"
                                                            class="form-control form-control-lg">
                                                    </div>
                                                    <button type="button" id="waitone" class="btn btn-primary w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#intCode">Proceed</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Start modal -->
                                    <div class="modal fade transfer_pin_modal" id="intCode">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">INT Code</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid INT Code for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" name="int_code"
                                                            class="form-control form-control-lg">
                                                    </div>
                                                    <button type="submit" id="waitone"
                                                        class="btn btn-primary w-100">Proceed</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
            Content body end
        ***********************************-->
@include('dashboard.footer')