<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{asset('admin/dist/img/logo.jpg')}}" alt="{{ config('app.name', 'Laravel') }} Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('admin/dist/img/avatar.png')}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block text-capitalize">{{auth()->user() ? auth()->user()->username : ''}}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link{{Route::currentRouteName() == 'dashboard' ? ' active':''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>{{__('page.dashboard')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('inbound')}}" class="nav-link{{Route::currentRouteName() == 'inbound' ? ' active':''}}">
                        <i class="nav-icon fas fa-headset"></i>
                        <p>{{__('page.inbound')}}</p>
                    </a>
                </li>
                @if (preg_match('/Admin/',auth()->user()->permission))
                <li class="nav-item">
                    <a href="{{route('user.index')}}" class="nav-link{{Route::currentRouteName() == 'user.index' ? ' active':''}}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{__('page.user.index')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link{{Route::currentRouteName() == 'settings.index' ? ' active':''}}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Settings</p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{route('logout.get')}}" class="nav-link">
                        <i class="nav-icon fas fa-unlock"></i>
                        <p>{{__('page.logout')}}</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
