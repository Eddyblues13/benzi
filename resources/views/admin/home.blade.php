@include('admin.header')
@include('admin.navbar')

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

    <!-- Main header starts -->
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-house"></i>
            </div>
            <div class="page-title d-none d-md-block">
                <h5>Welcome back, {{ Auth::user()->name }}</h5>
            </div>
        </div>
    </div>
    <!-- Main header ends -->

    <!-- Content wrapper start -->
    <div class="content-wrapper">
        @include('admin.partials.alerts')

        <div class="row gx-3">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Customers</h6>
                        <h3 class="mb-0">{{ number_format($stats['users']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <a href="{{ route('user.transaction', ['status' => 0]) }}" class="text-decoration-none">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Pending approval</h6>
                            <h3 class="mb-0 {{ $stats['pending'] ? 'text-danger' : '' }}">{{ number_format($stats['pending']) }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Approved credits</h6>
                        <h3 class="mb-0 text-success">{{ number_format($stats['credits'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Approved debits</h6>
                        <h3 class="mb-0 text-danger">{{ number_format($stats['debits'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-3">
            <div class="col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Users</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered m-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Account No.</th>
                                        <th>Activated</th>
                                        <th>Joined</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($result as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->a_number }}</td>
                                        <td>
                                            @if((string) $user->is_activated === '1')
                                            <span class="badge shade-green">Yes</span>
                                            @else
                                            <span class="badge shade-red">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at?->format('M j, Y') }}</td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('admin.user.profile', $user->id) }}" class="btn btn-sm btn-primary">View User</a>
                                            <form action="{{ route('delete.user', $user->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->first_name) }} and all of their transactions? This cannot be undone.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No customers have registered yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content wrapper end -->
</div>
<!-- Content wrapper scroll end -->

@include('admin.footer')
