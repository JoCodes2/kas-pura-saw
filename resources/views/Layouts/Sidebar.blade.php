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

                <li class="nav-item {{ request()->is('pengguna*') ? 'active' : '' }}">
                    <a href="{{ url('/pengguna') }}">
                        <i class="fas fa-calendar-check"></i>
                        <p>Pengguna</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('master*') ? 'active' : '' }}">
                    <a href="{{ url('/master') }}">
                        <i class="fas fa-list"></i>
                        <p>Master</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('kas_masuk*') ? 'active' : '' }}">
                    <a href="{{ url('/kas_masuk') }}">
                        <i class="fas fa-money-bill"></i>
                        <p>Kas Masuk</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('kegiatan*') ? 'active' : '' }}">
                    <a href="{{ url('/kegiatan') }}">
                        <i class="fas fa-calendar-check"></i>
                        <p>Kegiatan</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
