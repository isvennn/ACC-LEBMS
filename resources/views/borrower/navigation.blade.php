<aside class="main-sidebar elevation-4 sidebar-dark-lime">
    <a href="#" class="brand-link">
        <img src="{{ asset('dist/img/acclogo.png') }}" alt="ACC Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">ACC-LEBMS</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image position-relative">
                <img src="{{ asset('dist/img/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
                <!-- Online Status Icon -->
                <span class="position-absolute status-icon bg-success border border-white rounded-circle"
                    style="width: 10px; height: 10px; bottom: 0; right: 0;"></span>
            </div>
            <div class="info ml-2">
                <a href="#" class="d-block font-weight-bold">{{ auth()->user()->fullname }}</a>
                <span class="d-block text-muted text-lime">{{ auth()->user()->user_role }}</span>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-collapse-hide-child"
                data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('viewBorrowerDashboard') }}" class="nav-link @yield('active-dashboard')">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('viewBorrowerItem') }}" class="nav-link @yield('active-items')">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Items</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('viewBorrowerTransaction') }}" class="nav-link @yield('active-transactions')">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Transactions</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
