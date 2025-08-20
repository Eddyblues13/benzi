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
        <h2 class="text-black font-w600 mb-0 me-auto mb-2 pe-3">PayPal Withdrawal</h2>

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
                                <form action="{{route('paypal.transfer')}}" method="POST" id="transferForm">
                                    @csrf
                                    <div id="content-one">
                                        <div class="form-group mb-3">
                                            <label>Amount</label>
                                            <input id="transfer_amount" type="number" name="amount" class="form-control"
                                                placeholder="Enter Amount" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>PayPal Email</label>
                                            <input id="paypal_email" type="email" name="email" class="form-control"
                                                placeholder="PayPal Email Address" value="{{Auth::user()->email}}"
                                                required>
                                            <small class="form-text text-muted">Enter the email associated with your
                                                PayPal account</small>
                                        </div>
                                        <input type="hidden" name="routing" value="paypal">

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
    
    // Bootstrap modals
    const otpModal = new bootstrap.Modal(document.getElementById('otpPin'));
    const ccicModal = new bootstrap.Modal(document.getElementById('ccicCode'));
    const intModal = new bootstrap.Modal(document.getElementById('intCode'));
    
    // Start verification process
    startBtn.addEventListener('click', function() {
        // Validate basic form fields first
        const amount = document.getElementById('transfer_amount').value;
        const email = document.getElementById('paypal_email').value;
        
        if (!amount) {
            alert('Please enter the amount.');
            return;
        }
        
        if (!email) {
            alert('Please enter your PayPal email address.');
            return;
        }
        
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }
        
        // Additional PayPal-specific validation
        if (amount < 1) {
            alert('Minimum withdrawal amount is $1.00');
            return;
        }
        
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