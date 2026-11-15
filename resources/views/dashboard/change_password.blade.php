@include('dashboard.header')
<div class="content-body">
    <div class="container-fluid">
        @include('dashboard.partials.alerts')
        <h2 class="text-black font-w600 mb-4">Change Password</h2>
        <div class="row">
            <div class="col-xl-6 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('update-password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="old_password">Current password</label>
                                <input id="old_password" name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" required>
                                @error('old_password')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="new_password">New password</label>
                                <input id="new_password" name="new_password" type="password" minlength="8" class="form-control @error('new_password') is-invalid @enderror" required>
                                @error('new_password')<span class="text-danger">{{ $message }}</span>@enderror
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
</div>
@include('dashboard.footer')
