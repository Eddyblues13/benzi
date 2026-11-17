<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Limpopay| ACCOUNT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">

    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5 text-muted">
                        <a href="{{url('/')}}" class="d-block auth-logo">
                            <img src="{{asset('asset/img/logo.png')}}" alt="" height="20"
                                class="auth-logo-dark mx-auto">
                            <img src="{{asset('asset/img/logo.png')}}" alt="" height="20"
                                class="auth-logo-light mx-auto">
                        </a>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body">

                            <div class="p-2">
                                <div class="text-center">
                                    @if(session('error'))
                                    <div class="alert alert-danger">{{session('error')}}</div>
                                    @endif

                                    @if(session('message'))
                                    <div class="alert alert-success">{{session('message')}}</div>
                                    @endif

                                    <div class="avatar-md mx-auto">
                                        <div class="avatar-title rounded-circle bg-light">
                                            <i class="bx bxs-envelope h1 mb-0 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="p-2 mt-4">

                                        <h4>Verify your email</h4>
                                        <p class="mb-5">Please enter the code sent to <span
                                                class="fw-semibold">{{$email}}</span></p>

                                        <form action="{{ route('code') }}" method="POST">
                                            @csrf
                                            <input type="text" id="email" name="email" style="display:none;"
                                                value="{{$email}}">




                                            <div class="mb-3">
                                                <label for="digit4-input" class="visually-hidden">Dight 4</label>
                                                <input type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="10"
                                                    class="form-control form-control-lg text-center two-step"
                                                    name="digit" placeholder="Enter code" required autofocus>
                                                @error('digit')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                    </div>


                                    <div class="mt-4">
                                        <button type="submit" id="send" onclick='send(this)'
                                            class="btn btn-success w-md">Confirm</button>
                                    </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="mt-5 text-center">
                    <p class="response"></p>
                    <p>Didn't receive a code ?
                        <a id="otp" href="{{ route('resendCode') }}" class="fw-bold text-primary"> Resend <i
                                class='bx bx-mail-send'></i> </a>
                    </p>
                    <p>Not you? <a href="{{ route('logOut') }}" class="fw-bold text-primary">Log out</a></p>
                    <p>©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> Copyright
                        <i class="bx bx-check-shield text-success"></i>Limpopay
                    </p>
                </div>

            </div>
        </div>
    </div>
    </div>

        <div class="position-fixed top-0 end-0 p-2" style="z-index: 1005">
        <div id="ErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="{{asset('asset/img/logo.png')}}" alt="" class="me-2" height="18">
                <strong class="me-auto">Error</strong>
                <small>Now..</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="background-color:red;">

            </div>
        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- validation init -->
    <script src="{{asset('assets/js/pages/validation.init.js')}}"></script>

    <!-- App js -->
    <script src="{{asset('assets/js/app.js')}}"></script>
    <!-- Bootstrap Toasts Js -->
    <script src="{{asset('assets/js/pages/bootstrap-toastr.init.js')}}"></script>

</body>

</html>
