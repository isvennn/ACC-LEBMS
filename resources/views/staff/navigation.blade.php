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
                    <a href="{{ route('viewStaffDashboard') }}" class="nav-link @yield('active-dashboard')">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item @yield('active-items-open')">
                    <a href="#" class="nav-link @yield('active-items')">
                        <i class="nav-icon fas fa-suitcase"></i>
                        <p>
                            Items
                            <i class="right fas fa-chevron-down"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('viewStaffCategory') }}" class="nav-link @yield('active-items-category')">
                                <i class="nav-icon fas fa-tag"></i>
                                <p>Item Category</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('viewStaffItem') }}" class="nav-link @yield('active-items-list')">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Item Lists</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('viewStaffTransaction') }}" class="nav-link @yield('active-transactions')">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Transactions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('viewStaffInventory') }}" class="nav-link @yield('active-inventories')">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Inventory</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link @yield('active-penalties')">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>Penalties</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('viewStaffReport') }}" class="nav-link @yield('active-reports')">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Report</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
