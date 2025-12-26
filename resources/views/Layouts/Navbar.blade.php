<div class="main-header">
    <div class="logo-header" data-background-color="blue">

        <a href="/" class="logo d-flex align-items-center text-decoration-none">
            <span class="text-white fw-bold fs-4">
                KAS PURA
            </span>
        </a>


        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="icon-menu"></i>
            </span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

                <li class="nav-item dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" id="notifDropdown" href="#" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" title="Pemberitahuan">

                        <i class="fa fa-bell text-light fa-lg"></i>

                        {{-- Badge untuk hitungan notif belum dibaca. Diisi oleh JavaScript --}}
                        <span class="notification bg-danger" id="notifCountBadge">0</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right notification-menu animated fadeIn shadow-lg"
                        aria-labelledby="notifDropdown" id="notificationDropdownMenu">

                        <li>
                            <div
                                class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <h6 class="mb-0 text-dark font-weight-bold">Pemberitahuan Terbaru</h6>
                            </div>
                        </li>

                        {{-- Konten Notifikasi Terbaru (Akan diisi oleh JavaScript) --}}
                        <div id="notifDropdownContent" class="dropdown-body"
                            style="max-height: 350px; overflow-y: auto;">
                            <div class="text-center p-3 text-muted small">
                                <i class="fas fa-sync-alt fa-spin mr-1"></i> Memuat...
                            </div>
                        </div>
                    </ul>
                </li>

                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class=" my-auto">
                            <i class="fas fa-sign-out-alt fa-2x text-light" id="iconLogout"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
