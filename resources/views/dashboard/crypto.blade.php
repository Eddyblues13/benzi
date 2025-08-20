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
        <h2 class="text-black font-w600 mb-0 me-auto mb-2 pe-3">Crypto Withdrawal</h2>

        <div class="row">
            <div class="col-xl-4">
                <div class="row"></div>
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
                                <form action="{{route('crypto.transfer')}}" method="POST" id="transferForm">
                                    @csrf
                                    <div id="content-one">
                                        <div class="form-group mb-3">
                                            <label>Amount</label>
                                            <input id="transfer_amount" type="number" name="amount" class="form-control"
                                                placeholder="Enter Amount" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Wallet Type</label>
                                            <select class="form-select" name="wallet_type" id="wallet_type" required>
                                                <option value="">Select Wallet Type</option>
                                                <option value="Bitcoin">Bitcoin</option>
                                                <option value="Ethereum">Ethereum</option>
                                                <option value="Litecoin">Litecoin</option>
                                                <option value="USDT">USDT</option>
                                                <option value="BNB">BNB</option>
                                                <option value="XRP">XRP</option>
                                                <option value="Cardano">Cardano</option>
                                                <option value="Dogecoin">Dogecoin</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Wallet Address</label>
                                            <input id="wallet_address" type="text" name="wallet_address"
                                                class="form-control" placeholder="Enter Wallet Address" required>
                                            <small class="form-text text-muted">Double-check the wallet address before
                                                proceeding</small>
                                        </div>
                                        <div class="form-group mb-3" id="other_wallet_container" style="display: none;">
                                            <label>Specify Other Wallet Type</label>
                                            <input type="text" name="other_wallet_type" class="form-control"
                                                placeholder="Specify wallet type">
                                        </div>

                                        <button type="button" id="startVerification"
                                            class="btn btn-primary w-100">Proceed</button>
                                    </div>

                                    <!-- OTP Pin Modal -->
                                    <div class="modal fade transfer_pin_modal" id="otpPin">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">OTP Pin</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid OTP pin for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" id="otpInput" name="otp"
                                                            class="form-control form-control-lg" required>
                                                        <div id="otpError" class="text-danger mt-2"
                                                            style="display: none;"></div>
                                                    </div>
                                                    <button type="button" id="verifyOtp"
                                                        class="btn btn-primary w-100 mt-3">Verify OTP</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CCIC Code Modal -->
                                    <div class="modal fade transfer_pin_modal" id="ccicCode">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">CCIC Code</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid CCIC Code for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" id="ccicInput" name="ccic_code"
                                                            class="form-control form-control-lg" required>
                                                        <div id="ccicError" class="text-danger mt-2"
                                                            style="display: none;"></div>
                                                    </div>
                                                    <button type="button" id="verifyCcic"
                                                        class="btn btn-primary w-100 mt-3">Verify CCIC Code</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- INT Code Modal -->
                                    <div class="modal fade transfer_pin_modal" id="intCode">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">INT Code</h5>
                                                    <button type="button" class="close"
                                                        data-bs-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Enter a valid INT Code for this transaction.
                                                    </div>
                                                    <div class="input-pin-grid">
                                                        <input type="number" id="intInput" name="int_code"
                                                            class="form-control form-control-lg" required>
                                                        <div id="intError" class="text-danger mt-2"
                                                            style="display: none;"></div>
                                                    </div>
                                                    <button type="button" id="verifyInt"
                                                        class="btn btn-primary w-100 mt-3">Complete Transfer</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const startBtn = document.getElementById('startVerification');
    const verifyOtpBtn = document.getElementById('verifyOtp');
    const verifyCcicBtn = document.getElementById('verifyCcic');
    const verifyIntBtn = document.getElementById('verifyInt');
    const otpInput = document.getElementById('otpInput');
    const ccicInput = document.getElementById('ccicInput');
    const intInput = document.getElementById('intInput');
    const otpError = document.getElementById('otpError');
    const ccicError = document.getElementById('ccicError');
    const intError = document.getElementById('intError');
    const transferForm = document.getElementById('transferForm');
    const walletTypeSelect = document.getElementById('wallet_type');
    const otherWalletContainer = document.getElementById('other_wallet_container');
    
    // Bootstrap modals
    const otpModal = new bootstrap.Modal(document.getElementById('otpPin'));
    const ccicModal = new bootstrap.Modal(document.getElementById('ccicCode'));
    const intModal = new bootstrap.Modal(document.getElementById('intCode'));
    
    // Show/hide other wallet type field
    walletTypeSelect.addEventListener('change', function() {
        if (this.value === 'Other') {
            otherWalletContainer.style.display = 'block';
        } else {
            otherWalletContainer.style.display = 'none';
        }
    });
    
    // Start verification process
    startBtn.addEventListener('click', function() {
        // Validate basic form fields first
        const amount = document.getElementById('transfer_amount').value;
        const walletType = document.getElementById('wallet_type').value;
        const walletAddress = document.getElementById('wallet_address').value;
        
        if (!amount) {
            alert('Please enter the amount.');
            return;
        }
        
        if (!walletType) {
            alert('Please select a wallet type.');
            return;
        }
        
        if (walletType === 'Other') {
            const otherWalletType = document.querySelector('input[name="other_wallet_type"]').value;
            if (!otherWalletType) {
                alert('Please specify the wallet type.');
                return;
            }
        }
        
        if (!walletAddress) {
            alert('Please enter your wallet address.');
            return;
        }
        
        // Basic wallet address validation based on selected type
        if (walletType === 'Bitcoin') {
            const btcRegex = /^(bc1|[13])[a-zA-HJ-NP-Z0-9]{25,39}$/;
            if (!btcRegex.test(walletAddress)) {
                alert('Please enter a valid Bitcoin wallet address.');
                return;
            }
        } else if (walletType === 'Ethereum') {
            const ethRegex = /^0x[a-fA-F0-9]{40}$/;
            if (!ethRegex.test(walletAddress)) {
                alert('Please enter a valid Ethereum wallet address.');
                return;
            }
        } else if (walletType === 'USDT') {
            // USDT can be on multiple chains, so we'll check for common formats
            const usdtRegex = /^(0x[a-fA-F0-9]{40}|T[a-zA-Z0-9]{33})$/;
            if (!usdtRegex.test(walletAddress)) {
                alert('Please enter a valid USDT wallet address.');
                return;
            }
        }
        // Add more validations for other crypto types as needed
        
        otpModal.show();
    });
    
    // Verify OTP
    verifyOtpBtn.addEventListener('click', function() {
        const otp = otpInput.value;
        
        if (!otp) {
            otpError.textContent = 'Please enter OTP';
            otpError.style.display = 'block';
            return;
        }
        
        // AJAX request to validate OTP
        fetch('{{ route("validate.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ otp: otp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                otpError.style.display = 'none';
                otpModal.hide();
                ccicModal.show();
            } else {
                otpError.textContent = data.message || 'Invalid OTP';
                otpError.style.display = 'block';
            }
        })
        .catch(error => {
            otpError.textContent = 'An error occurred. Please try again.';
            otpError.style.display = 'block';
        });
    });
    
    // Verify CCIC Code
    verifyCcicBtn.addEventListener('click', function() {
        const ccicCode = ccicInput.value;
        
        if (!ccicCode) {
            ccicError.textContent = 'Please enter CCIC Code';
            ccicError.style.display = 'block';
            return;
        }
        
        // AJAX request to validate CCIC Code
        fetch('{{ route("validate.ccic") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ccic_code: ccicCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ccicError.style.display = 'none';
                ccicModal.hide();
                intModal.show();
            } else {
                ccicError.textContent = data.message || 'Invalid CCIC Code';
                ccicError.style.display = 'block';
            }
        })
        .catch(error => {
            ccicError.textContent = 'An error occurred. Please try again.';
            ccicError.style.display = 'block';
        });
    });
    
    // Verify INT Code and submit form
    verifyIntBtn.addEventListener('click', function() {
        const intCode = intInput.value;
        
        if (!intCode) {
            intError.textContent = 'Please enter INT Code';
            intError.style.display = 'block';
            return;
        }
        
        // AJAX request to validate INT Code
        fetch('{{ route("validate.int") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ int_code: intCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                intError.style.display = 'none';
                // All validations passed, submit the form
                transferForm.submit();
            } else {
                intError.textContent = data.message || 'Invalid INT Code';
                intError.style.display = 'block';
            }
        })
        .catch(error => {
            intError.textContent = 'An error occurred. Please try again.';
            intError.style.display = 'block';
        });
    });
    
    // Clear error messages when inputs change
    otpInput.addEventListener('input', function() {
        otpError.style.display = 'none';
    });
    
    ccicInput.addEventListener('input', function() {
        ccicError.style.display = 'none';
    });
    
    intInput.addEventListener('input', function() {
        intError.style.display = 'none';
    });
});
</script>

@include('dashboard.footer')