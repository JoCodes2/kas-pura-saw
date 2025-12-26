<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    <img src="{{ asset('assets/img/LOGO.JPEG') }}" alt="Logo" class="img-fluid" width="70"
                        height="70">
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{-- @auth
                                {{ auth()->user()->name }}
                            @endauth
                            @auth
                                <span class="user-level">{{ auth()->user()->username }}</span>
                            @endauth --}}

                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <ul class="nav nav-primary">
                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('user*') ? 'active' : '' }}">
                    <a href="{{ url('/user') }}">
                        <i class="fas fa-calendar-check"></i>
                        <p>Pengguna</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
