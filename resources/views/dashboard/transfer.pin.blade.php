<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>
        Pincode - Quantum Capital Holdings </title>
    <meta name="description" content="Quantum Capital Holdings Mobile Banking">
    <meta name="keywords"
        content="bootstrap, wallet, banking, fintech mobile template, cordova, phonegap, mobile, html, responsive" />
    <link rel="icon" type="image/png" href="https://portal.quantumcapitalholdings.com/assets/img/favicon.png"
        sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180"
        href="https://portal.quantumcapitalholdings.com/assets/img/icon/192x192.png">
    <link rel="stylesheet" href="https://portal.quantumcapitalholdings.com/assets/panel/css/style.css">
    <link rel="manifest" href="__manifest.json">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>


</head>

<body>

    <!-- loader -->
    <!--<div id="loader">-->
    <!--    <img src="assets/img/loading-icon.png" alt="icon" class="loading-icon">-->
    <!--</div>-->
    <!-- * loader -->

    <div id="loader">
        <span class="spinner-grow spinner-grow-sm me-05" role="status" aria-hidden="true" class="loading-icon"></span>
        Loading...
    </div>



    <!-- App Capsule -->
    <div id="appCapsule">

        <!-- App Header -->
        <div class="appHeader transparent">
            <div class="left">
                <a href="https://portal.quantumcapitalholdings.com/user/dashboard.php" class="headerButton">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle"></div>
            <div class="right">
                <a onclick="location.reload();" class="headerButton">
                    <ion-icon name="refresh"></ion-icon>
                </a>
            </div>
        </div>
        <!-- * App Header -->

        <!-- App Capsule -->
        <div id="appCapsule">

            <div class="section mt-2 text-center">
                <h1>Enter Token Code</h1>
                <h4>Enter 4 digit One-Time Code</h4>
            </div>
            <div class="section mb-5 p-2">
                <form method="POST">
                    <div class="form-group basic">
                        <input type="text" name="pin" class="form-control verification-input" autocomplete="off"
                            id="smscode" placeholder="••••" minlength="3" maxlength="4">





                        <input type="number" value="1000" name="amount" hidden id="amount">
                        <input type="text" value="Union Bank" name="bank_name" hidden id="bank_name">
                        <input type="text" value="Union Name" name="account_name" hidden id="account_name">
                        <input type="number" value="2223345678" name="account_number" hidden id="account_number">
                        <input type="text" value="Savings" name="account_type" hidden id="account_type">
                        <input type="text" value="Wire transfer" name="trans_type" hidden id="trans_type">
                        <input type="text" value="Bahrain" name="bank_country" hidden id="bank_country">
                        <input type="number" value="13" name="user_id" id="user_id" hidden>
                        <input type="text" value="555666" name="routine_number" id="routine_number" hidden>
                        <input type="text" value="768765656" name="swift_code" id="swift_code" hidden>



                    </div>

                    <div class="form-button-group transparent">
                        <button type="submit" class="btn btn-primary btn-block btn-lg" name="wire_submit">Comfirm
                            Transaction</button>
                    </div>

                </form>
            </div>

        </div>
        <!-- * App Capsule -->


        <!-- Deposit Action Sheet -->
        <div class="modal fade action-sheet" id="depositActionSheet" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"><br>
                        <h5 class="modal-title">ACTIVE ACCOUNT</h5>
                        <!-- <center>
                    <p>Send money to the account details below, your wallet will be credited instantly. <br>Charge is 0%
                        of any amount less than ₦ 10,000.00, Stamp duty charge on amount ₦10,000.00 and above.</p>
                </center> -->
                    </div>



                    <div class="section mt-3">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs capsuled" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#wema" role="tab">
                                            USD BANK
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#access" role="tab">
                                            EURO BANK
                                        </a>
                                    </li>

                                </ul>
                                <div class="tab-content mt-1">
                                    <div class="tab-pane fade show active" id="wema" role="tabpanel">

                                        <!-- card block -->
                                        <div class="card-block bg-dark mb-10 ml-10">
                                            <div class="card-main">
                                                <div class="balance">
                                                    <span class="label"
                                                        style="margin-bottom: 10px; margin-top: 10px; text-align: left">Account
                                                        No:</span>
                                                    <h1 class="title text-color2">
                                                        1202202481<a href="#" data-account="1202202481" class="account"
                                                            data-toggle="tooltip" title="Tap to copy"
                                                            style="color: #fff">
                                                            <ion-icon ios="ios-copy" md="md-copy"></ion-icon>
                                                        </a></h1>
                                                </div>
                                                <div class="in">
                                                    <div class="bottom">
                                                        <div class="card-expiry">
                                                            <span class="label" style="text-align: left">Account
                                                                Name</span>
                                                            Tari Nimi
                                                        </div>
                                                    </div>
                                                    <div class="bottom">
                                                        <div class="card-expiry">
                                                            <span class="label">Bank Name</span>
                                                            Quantum Capital Holdings
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- * card block -->

                                    </div>
                                    <div class="tab-pane fade" id="access" role="tabpanel">
                                        <!-- card block -->
                                        <div class="card-block bg-dark mb-10 ml-10">
                                            <div class="card-main">
                                                <div class="balance">
                                                    <span class="label"
                                                        style="margin-bottom: 10px; margin-top: 10px; text-align: left">Account
                                                        No:</span>
                                                    <h1 class="title text-color2">
                                                        XXXX-XXX-XXXX<a href="#" data-account="1202202481"
                                                            class="account" data-toggle="tooltip" title="Tap to copy"
                                                            style="color: #fff">
                                                            <ion-icon ios="ios-copy" md="md-copy"></ion-icon>
                                                        </a></h1>
                                                </div>
                                                <div class="in">
                                                    <div class="bottom">
                                                        <div class="card-expiry">
                                                            <span class="label" style="text-align: left">Account
                                                                Name</span>
                                                            Tari Nimi
                                                        </div>
                                                    </div>
                                                    <div class="bottom">
                                                        <div class="card-expiry">
                                                            <span class="label">Bank Name</span>
                                                            Quantum Capital Holdings
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- * card block -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal-body">

                        <div class="action-sheet-content">
                            <div class="form-group basic">
                                <button type="button" class="btn btn-primary btn-block btn-lg"
                                    data-bs-dismiss="modal">Close
                                    Tab</button>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Deposit Action Sheet -->


        <!-- * Transfer Limit Action Sheet -->
        <div class="modal fade action-sheet" id="LimitActionSheet" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"><br>
                        <h5 class="modal-title">Account Limit</h5>
                    </div>



                    <div class="section mt-3">
                        <div class="card-body">
                            <ul class="nav nav-tabs capsuled" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#user" role="tab">
                                        Starter
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#agent" role="tab">
                                        Starter Plus
                                    </a>
                                </li>

                            </ul>
                            <div class="tab-content mt-1">
                                <div class="tab-pane fade show active" id="user" role="tabpanel">

                                    <div class="card">
                                        <ul class="listview flush transparent image-listview text">
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Maximum Balance</div>
                                                        <div>$300,000</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Sending (per transaction)</div>
                                                        <div>$20,000</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Receiving (per transaction)</div>
                                                        <div>Unlimited</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Single Withdrawal</div>
                                                        <div>$20,000</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Withdrawal Per Day</div>
                                                        <div>10</div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="agent" role="tabpanel">

                                    <div class="card">
                                        <ul class="listview flush transparent image-listview text">
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Maximum Balance</div>
                                                        <div>Unlimited</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Sending (per transaction)</div>
                                                        <div>$50,000</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Receiving (per transaction)</div>
                                                        <div>Unlimited</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Single Withdrawal</div>
                                                        <div>$50,000</div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="item">
                                                    <div class="in">
                                                        <div>Withdrawal Per Day</div>
                                                        <div>50</div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal-body">

                        <div class="action-sheet-content">
                            <div class="form-group basic">
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-lg btn-danger cancel btn-block"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                    <div class="col-6">
                                        <a href="https://portal.quantumcapitalholdings.com/user/support.php"
                                            class="btn btn-lg btn-primary btn-block">Upgrade</a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Transfer Limit Action Sheet -->


        <script>
            var data = null;
console.log(data);

function crypto_type(id) {
    for (var i = 0; i < data.length; i++) {
        if (id == data[i].id) {
            $("#wallet_address").val(data[i].wallet_address);
        }
    }
}
        </script>


        <script>
            //  Preloader
jQuery(window).on("load", function() {
    $("#preloader").fadeOut(2000);
    $("#main-wrapper").addClass("show");
});
        </script>

        <script src="https://portal.quantumcapitalholdings.com/assets/panel/js/main.js"></script>
        <!-- ========= JS Files =========  -->
        <!-- Bootstrap -->
        <script src="https://portal.quantumcapitalholdings.com/assets/panel/js/lib/bootstrap.bundle.min.js"></script>
        <!-- Ionicons -->
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        <!-- Splide -->
        <script src="https://portal.quantumcapitalholdings.com/assets/panel/js/plugins/splide/splide.min.js"></script>
        <!-- Base Js File -->
        <script src="https://portal.quantumcapitalholdings.com/assets/panel/js/base.js"></script>



        <script>
            var style_url, url, token;
style_url = "https://portal.quantumcapitalholdings.com/assets/panel/css/";
url = "{{url('/')}}";
token = "{{Session::token()}}";
        </script>

</body>

</html>