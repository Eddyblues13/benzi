@include('admin.header')
@include('admin.navbar')

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

    <!-- Main header starts -->
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="page-title d-none d-md-block">
                <h5>Change Password</h5>
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
                        <form action="{{ route('admin.update.password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="old_password">Current password</label>
                                <input id="old_password" name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="new_password">New password</label>
                                <input id="new_password" name="new_password" type="password" minlength="8" class="form-control @error('new_password') is-invalid @enderror" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="new_password_confirmation">Confirm new password</label>
                                <input id="new_password_confirmation" name="new_password_confirmation" type="password" minlength="8" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                            <p class="text-muted mt-3 mb-0"><small>You will be signed out and asked to log in with your new password.</small></p>
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
