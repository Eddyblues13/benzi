@include('admin.header')
@include('admin.navbar')

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

    <!-- Main header starts -->
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="page-title d-none d-md-block">
                <h5>Wallet Settings</h5>
            </div>
        </div>
    </div>
    <!-- Main header ends -->

    <!-- Content wrapper start -->
    <div class="content-wrapper">
        @include('admin.partials.alerts')

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Update Ethereum Wallet Address</h4>
                        <form method="POST" action="{{ route('admin.save.wallet') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="wallet_address">Ethereum Wallet Address</label>
                                <input type="text" id="wallet_address" name="wallet_address" class="form-control" maxlength="150"
                                    value="{{ old('wallet_address', Auth::user()->wallet_address) }}" placeholder="New Wallet Address" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="image">Upload Bar Code (QR image)</label>
                                <input type="file" name="image" class="form-control" id="image" accept="image/*" />
                                @if(Auth::user()->bar_code)
                                <img src="{{ asset('admin/uploads/admin/' . Auth::user()->bar_code) }}" alt="Current bar code" class="mt-3 border rounded" width="120" height="120" />
                                @endif
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content wrapper end -->
</div>
<!-- Content wrapper scroll end -->

@include('admin.footer')
