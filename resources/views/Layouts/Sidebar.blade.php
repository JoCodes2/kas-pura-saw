<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">

            <ul class="nav nav-primary">

                {{-- DASHBOARD --}}
                <li class="nav-item {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- MASTER DATA --}}
                <li class="nav-item {{ request()->is('master*') ? 'active' : '' }}">
                    <a href="{{ url('/master') }}">
                        <i class="fas fa-database"></i>
                        <p>Master Data</p>
                    </a>
                </li>

                {{-- KAS MASUK --}}
                <li class="nav-item {{ request()->is('kas_masuk*') ? 'active' : '' }}">
                    <a href="{{ url('/kas_masuk') }}">
                        <i class="fas fa-hand-holding-usd"></i>
                        <p>Kas Masuk</p>
                    </a>
                </li>

                {{-- KEGIATAN --}}
                <li class="nav-item {{ request()->is('kegiatan*') ? 'active' : '' }}">
                    <a href="{{ url('/kegiatan') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Kegiatan</p>
                    </a>
                </li>

                {{-- KRITERIA & NORMALISASI SAW --}}
                <li class="nav-item {{ request()->is('kriteria*', 'normalisasi*') ? 'active' : '' }}">
                    <a href="{{ url('/kriteria') }}">
                        <i class="fas fa-balance-scale"></i>
                        <p>Kriteria Penilaian SAW</p>
                    </a>
                </li>

                {{-- KEPUTUSAN SAW --}}
                <li class="nav-item {{ request()->is('keputusan*') ? 'active' : '' }}">
                    <a href="{{ url('/keputusan') }}">
                        <i class="fas fa-gavel"></i>
                        <p>Keputusan SAW</p>
                    </a>
                </li>

                {{-- KAS KELUAR --}}
                <li class="nav-item {{ request()->is('kas-keluar*') ? 'active' : '' }}">
                    <a href="{{ url('/kas-keluar') }}">
                        <i class="fas fa-hand-holding-usd"></i>
                        <p>Kas Keluar</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
