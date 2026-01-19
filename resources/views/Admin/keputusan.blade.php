@extends('Layouts.Base')
@section('title', 'Hasil Keputusan')
@section('content')
    <div class="page-inner">
        <div class="page-header ">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Hasil Keputusan</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title" id="saldoUtama"></h4>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm" id="btnRefresh">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="loadingData" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat data...</p>
                            </div>
                            <div id="dataContainer" style="display: none;">
                                <div id="dateGroups"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const loadingAlert = () => Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        function reloadBrowsers() {
            setTimeout(function() {
                location.reload();
            }, 1500);
        }

        const simpleAlert = (title, icon) => Swal.fire({
            title: title,
            icon: icon,
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });

        const confirmAlert = (title, text) => Swal.fire({
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        });

        // Format tanggal Indonesia
        function formatTanggal(tanggal) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(tanggal).toLocaleDateString('id-ID', options);
        }

        // Format mata uang
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

        // Render badge peringkat
        function renderBadgePeringkat(peringkat) {
            if (peringkat === 1) {
                return '<span class="badge badge-success p-2"><i class="fas fa-crown mr-1"></i> #' + peringkat + '</span>';
            } else if (peringkat === 2) {
                return '<span class="badge badge-info p-2"><i class="fas fa-medal mr-1"></i> #' + peringkat + '</span>';
            } else if (peringkat === 3) {
                return '<span class="badge badge-warning p-2"><i class="fas fa-award mr-1"></i> #' + peringkat + '</span>';
            } else {
                return '<span class="badge badge-secondary p-2">#' + peringkat + '</span>';
            }
        }

        // Render badge status
        function renderBadgeStatus(status) {
            if (status === 'diproses') {
                return '<span class="badge badge-info">Diproses</span>';
            } else if (status === 'diadakan') {
                return '<span class="badge badge-success">Diadakan</span>';
            } else if (status === 'ditolak') {
                return '<span class="badge badge-danger">Ditolak</span>';
            } else if (status === 'ditunda') {
                return '<span class="badge badge-warning">Ditunda</span>';
            }
            return '<span class="badge badge-secondary">' + status + '</span>';
        }

        // Cek apakah status sudah final (tidak bisa diubah lagi)
        function isStatusFinal(status) {
            return status === 'diadakan' || status === 'ditolak' || status === 'ditunda';
        }

        // Cek saldo kas sebelum update status "diadakan"
        function checkKasSaldo(kegiatanId, namaKegiatan, estimasiBiaya, $row) {
            loadingAlert();

            $.ajax({
                url: '/saw/master',
                method: 'GET',
                success: function(res) {
                    console.log(res);
                    $('#saldoUtama').text('Saldo saat ini Rp. ' + formatRupiah(res.saldo));
                    Swal.close();

                    if (res.code === 200) {
                        // Cari kas utama
                        const kasUtama = res.data.find(k => k.is_utama == 1);
                        const saldoUtama = kasUtama ? parseFloat(kasUtama.saldo) : 0;
                        const biaya = parseFloat(estimasiBiaya);
                        if (saldoUtama < biaya) {
                            Swal.fire({
                                title: 'Saldo Tidak Cukup!',
                                icon: 'error',
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            confirmDiadakan(kegiatanId, namaKegiatan, estimasiBiaya, saldoUtama, $row);
                        }
                    } else {
                        simpleAlert('Gagal memeriksa saldo kas', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    simpleAlert('Gagal memeriksa saldo kas', 'error');
                }
            });
        }
        function getSaldoUtama() {
            $.ajax({
                url: '/saw/master',
                method: 'GET',
                success: function(res) {
                    const kasUtama = res.data.find(k => k.is_utama == 1);

                    if (kasUtama) {
                        $('#saldoUtama').text('Saldo saat ini Rp. ' + formatRupiah(kasUtama.saldo));
                    } else {
                        $('#saldoUtama').text('Saldo saat ini Rp. 0');
                    }
                },
                error: function(xhr) {
                    simpleAlert('Gagal memeriksa saldo kas', 'error');
                }
            });
        }
        getSaldoUtama();
        // Konfirmasi update status "diadakan"
        function confirmDiadakan(kegiatanId, namaKegiatan, estimasiBiaya, saldoUtama, $row) {
            const biaya = parseFloat(estimasiBiaya);
            const sisaSaldo = saldoUtama - biaya;

            Swal.fire({
                title: 'Konfirmasi!',
                text:'Apakah anda yakin?',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatusKegiatan(kegiatanId, 'diadakan', $row);
                }
            });
        }

        // Load data dari API
        function loadData() {
            $('#loadingData').show();
            $('#dataContainer').hide();

            $.ajax({
                url: '/saw/nilai/hasil',
                method: 'GET',
                success: function(response) {
                    $('#loadingData').hide();

                    if (response.code === 200 && response.data.length > 0) {
                        renderData(response.data);
                        $('#dataContainer').show();
                    } else {
                        $('#dataContainer').html(`
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Tidak ada data</h4>
                                    <p class="text-muted">Belum ada hasil perankingan yang tersimpan.</p>
                                </div>
                            </div>
                        `).show();
                    }
                },
                error: function(xhr) {
                    $('#loadingData').hide();
                    $('#dataContainer').html(`
                        <div class="text-center py-5">
                            <div class="error-state">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Gagal memuat data</h4>
                                <p class="text-muted">Terjadi kesalahan saat mengambil data dari server.</p>
                                <button class="btn btn-primary btn-sm mt-2" id="btnRetry">
                                    <i class="fas fa-redo mr-1"></i> Coba Lagi
                                </button>
                            </div>
                        </div>
                    `).show();

                    // Event untuk retry
                    $('#btnRetry').off('click').on('click', loadData);
                }
            });
        }

        function renderData(data) {
            $('#dateGroups').empty();

            // 1. Kelompokkan data berdasarkan 'created_at' karena memiliki Jam:Menit:Detik yang unik
            const groupedData = {};
            data.forEach(item => {
                // Kita gunakan created_at sebagai kunci agar sesi yang berbeda jamnya terpisah
                const key = item.created_at;
                if (!groupedData[key]) {
                    groupedData[key] = [];
                }
                groupedData[key].push(item);
            });

            // 2. Urutkan Sesi dari yang terbaru (Descending)
            const sortedKeys = Object.keys(groupedData).sort().reverse();

            let html = '';

            sortedKeys.forEach((waktuSesi, index) => {
                const items = groupedData[waktuSesi];

                // 3. Urutkan item di dalam sesi berdasarkan skor preferensi tertinggi ke terendah
                const sortedItems = items.sort((a, b) => b.nilai_preferensi - a.nilai_preferensi);

                html += `
                    <div class="card card-round shadow-sm mb-5 ${index === 0 ? 'border-primary' : ''}">
                        <div class="card-header ${index === 0 ? 'bg-primary text-white' : 'bg-light'}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history mr-2"></i>
                                    Sesi Perhitungan: ${formatTanggalMakassar(waktuSesi)}
                                </h5>
                                <span class="badge ${index === 0 ? 'badge-light text-primary' : 'badge-primary'}">
                                    ${items.length} Alternatif
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="80">Rank</th>
                                            <th class="text-left">Nama Kegiatan</th>
                                            <th width="150">Estimasi Biaya</th>
                                            <th width="150">Skor Preferensi</th>
                                            <th width="120">Status</th>
                                            <th width="180">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                sortedItems.forEach((item, i) => {
                    const kegiatan = item.kegiatan;
                    const isFinal = isStatusFinal(kegiatan.status_kegiatan);
                    // Gunakan index looping + 1 untuk peringkat jika ingin peringkat visual yang rapi
                    const rank = i + 1;

                    html += `
                        <tr>
                            <td class="text-center">${renderBadgePeringkat(rank)}</td>
                            <td>
                                <div class="font-weight-bold">${kegiatan.nama_kegiatan}</div>
                                <small class="text-muted">Pengaju: ${kegiatan.nama_pengaju}</small>
                            </td>
                            <td class="text-center text-primary font-weight-bold">
                                ${formatRupiah(parseFloat(kegiatan.estimasi_biaya))}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-count text-success border border-success">
                                   ${parseFloat(item.nilai_preferensi).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                </span>
                            </td>
                            <td class="text-center">${renderBadgeStatus(kegiatan.status_kegiatan)}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    ${isFinal ?
                                        `<span class="badge badge-secondary"><i class="fas fa-lock"></i> Terkunci</span>` :

                                        `<button type="button" class="btn btn-success btn-action" data-id="${kegiatan.id}" data-action="diadakan" data-nama="${kegiatan.nama_kegiatan}">
                                            <i class="fas fa-check"></i> Diadakan
                                        </button>
                                        <button type="button" class="btn btn-danger btn-action" data-id="${kegiatan.id}" data-action="ditolak" data-nama="${kegiatan.nama_kegiatan}">
                                            <i class="fas fa-times"></i> Ditolak
                                        </button>
                                        <button type="button" class="btn btn-warning btn-action" data-id="${kegiatan . id}" data-action="ditunda" data-nama="${kegiatan . nama_kegiatan}">
                                            <i class="fas fa-clock"></i> Ditunda
                                        </button>`
                                    }
                                </div>
                            </td>
                        </tr>`;
                });
                html += `</tbody></table></div></div></div>`;
            });

            $('#dateGroups').html(html);
            bindActionEvents();
        }

        // Fungsi Helper untuk format waktu Makassar
        function formatTanggalMakassar(isoString) {
            const date = new Date(isoString);
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }) + ' WITA';
        }

        // Bind event untuk tombol aksi
        function bindActionEvents() {
            $('.btn-action:not(:disabled)').off('click').on('click', function() {
                const kegiatanId = $(this).data('id');
                const action = $(this).data('action');
                const namaKegiatan = $(this).data('nama');
                const $row = $(this).closest('tr');

                // Ambil estimasi biaya dari kolom ke-4
                const estimasiBiayaText = $row.find('td:nth-child(4) span').text();
                const estimasiBiaya = estimasiBiayaText.replace(/[^0-9.-]+/g,"");

                if (action === 'diadakan') {
                    checkKasSaldo(kegiatanId, namaKegiatan, estimasiBiaya, $row);
                } else {
                    let actionText = '';

                    switch(action) {
                        case 'ditolak':
                            actionText = 'menolak';
                            break;
                        case 'ditunda':
                            actionText = 'menunda';
                            break;
                    }

                    confirmAlert(
                        `Konfirmasi!`,
                        `Apakah Anda yakin?`
                    ).then((result) => {
                        if (result.isConfirmed) {
                            updateStatusKegiatan(kegiatanId, action, $row);
                        }
                    });
                }
            });
        }

        // Update status kegiatan
        function updateStatusKegiatan(kegiatanId, status, $row) {
            loadingAlert();

            $.ajax({
                url: `/saw/kegiatan/update-status/${kegiatanId}/${status}`,
                method: 'POST',
                success: function(response) {
                    Swal.close();
                    if (response.code === 200) {
                        $row.find('td:nth-child(6)').html(renderBadgeStatus(status));
                        const $actionGroup = $row.find('.btn-group');
                        $actionGroup.html(`
                            <span class="btn btn-outline-secondary" style="cursor: default;">
                                <i class="fas fa-lock mr-1"></i> Status Final
                            </span>
                        `);

                        simpleAlert('Berhasil', 'success');
                        reloadBrowsers();
                    } else {
                        Swal.fire({
                            title: 'Gagal Mengadakan!',
                            text: response.message || 'Terjadi kesalahan saat memperbarui status',
                            icon: 'error',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui status';

                    if (errorMsg.includes('tidak cukup') || errorMsg.includes('saldo') || errorMsg.includes('Kas Utama')) {
                        Swal.fire({
                            title: 'Saldo Tidak Cukup!',
                            html: `
                                <div class="text-left">
                                    <p>${errorMsg}</p>
                                    <p class="text-danger mt-2"><i class="fas fa-exclamation-triangle mr-1"></i> Silakan tambahkan saldo kas utama terlebih dahulu.</p>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        simpleAlert(errorMsg, 'error');
                    }
                }
            });
        }

        // Event untuk refresh data
        $('#btnRefresh').on('click', function() {
            loadingAlert();
            getSaldoUtama();
            setTimeout(() => {
                loadData();
                Swal.close();
                simpleAlert('Data berhasil direfresh', 'success');
            }, 500);
        });
        loadData();
    });
</script>

<style>
    .card-round {
        border-radius: 10px;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        vertical-align: middle !important;
    }

    .badge-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .badge-info {
        background: linear-gradient(135deg, #17a2b8 0%, #20c9c9 100%) !important;
    }

    .badge-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ffd454 100%) !important;
        color: #212529 !important;
    }

    .badge-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    }

    .badge-primary {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%) !important;
    }

    .btn-action {
        border-radius: 4px !important;
        font-size: 12px !important;
        padding: 5px 10px !important;
    }

    .btn-action.btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .btn-action.btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .btn-action.btn-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #212529 !important;
    }

    .btn-action:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .empty-state, .error-state {
        padding: 40px 20px;
    }

    .empty-state i, .error-state i {
        opacity: 0.5;
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #e3e6f0;
    }

    .card-footer.bg-light {
        background-color: #f8f9fa !important;
        border-top: 1px solid #e3e6f0;
    }

    .border-primary {
        border: 2px solid #007bff !important;
    }

    @media (max-width: 768px) {
        .btn-group .btn-action {
            padding: 4px 8px !important;
            font-size: 11px !important;
        }

        .table th, .table td {
            padding: 8px 5px !important;
        }
    }
</style>
@endsection
