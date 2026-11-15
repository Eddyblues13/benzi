    <!-- Main container start -->
      <div class="main-container">

        <!-- Sidebar wrapper start -->
        <nav class="sidebar-wrapper">

          <!-- Sidebar brand starts -->
          <div class="brand">
            <a href="{{ route('dashboard') }}" class="logo">
              <img src="{{ asset('asset/img/logo.png') }}" class="d-none d-md-block me-4" alt="Benzipaid" style="max-height: 48px;" />
              <img src="{{ asset('asset/img/logo.png') }}" class="d-block d-md-none me-4" alt="Benzipaid" style="max-height: 40px;" />
            </a>
          </div>
          <!-- Sidebar brand ends -->

          <!-- Sidebar menu starts -->
          <div class="sidebar-menu">
            <div class="sidebarMenuScroll">
              @php
                $currentType = request()->routeIs('user.transaction') ? request('type') : null;
                $currentStatus = request()->routeIs('user.transaction') ? request('status') : null;
              @endphp
              <ul>
                <li class="{{ request()->routeIs('dashboard', 'home', 'admin.user.profile') ? 'active-page-link' : '' }}">
                  <a href="{{ route('dashboard') }}">
                    <i class="bi bi-people"></i>
                    <span class="menu-text">Users</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('user.transaction') && ! $currentType && $currentStatus === null ? 'active-page-link' : '' }}">
                  <a href="{{ route('user.transaction') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="menu-text">All Transactions</span>
                  </a>
                </li>
                <li class="{{ $currentStatus === '0' ? 'active-page-link' : '' }}">
                  <a href="{{ route('user.transaction', ['status' => 0]) }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span class="menu-text">Pending Approval</span>
                  </a>
                </li>
                <li class="{{ $currentType === 'Deposit' ? 'active-page-link' : '' }}">
                  <a href="{{ route('user.transaction', ['type' => 'Deposit']) }}">
                    <i class="bi bi-box-arrow-in-down"></i>
                    <span class="menu-text">Deposits</span>
                  </a>
                </li>
                <li class="{{ $currentType === 'Loan' ? 'active-page-link' : '' }}">
                  <a href="{{ route('user.transaction', ['type' => 'Loan']) }}">
                    <i class="bi bi-calendar4"></i>
                    <span class="menu-text">Loans</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('update.wallet') ? 'active-page-link' : '' }}">
                  <a href="{{ route('update.wallet') }}">
                    <i class="bi bi-wallet2"></i>
                    <span class="menu-text">Wallet Settings</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('admin.change.password') ? 'active-page-link' : '' }}">
                  <a href="{{ route('admin.change.password') }}">
                    <i class="bi bi-shield-lock"></i>
                    <span class="menu-text">Change Password</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('logOut') }}">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="menu-text">Logout</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!-- Sidebar menu ends -->

        </nav>
        <!-- Sidebar wrapper end -->
