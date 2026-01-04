@extends('Layouts.Base')
@section('title', 'Penilaian Kegiatan')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"><i class="fas fa-edit pr-2"></i>Matriks Penilaian (Input Nilai SAW)</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{-- Widget Saldo Kas Utama --}}
                <div class="card card-stats card-round shadow-sm border-left border-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-university"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Saldo Kas Utama Saat Ini</p>
                                    <h4 class="card-title" id="display_saldo_utama">Rp 0</h4>
                                    <input type="hidden" id="val_saldo_utama" value="0">
                                </div>
                            </div>
                            <div class="col text-right">
                                <span class="badge badge-info">Sinkronisasi Otomatis</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 1: MATRIKS PENILAIAN --}}
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Matriks Penilaian</h4>
                                <p class="card-category">Berikan penilaian skor (0-100%) untuk kriteria kualitatif.</p>
                            </div>
                            <button type="button" class="btn btn-success btn-round" id="btnSimpanSemua">
                                <i class="fas fa-save pr-2"></i>Simpan Semua Penilaian
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form id="formPenilaian">
                                @csrf
                                <table class="table table-bordered table-head-bg-primary table-hover">
                                    <thead>
                                        <tr id="headerKriteria">
                                            <th style="min-width: 250px; vertical-align: middle;">Nama Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyPenilaian">
                                        {{-- Row Kegiatan via JS --}}
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: HASIL PERANKINGAN --}}
                <div id="containerHasilRanking" style="display: none;">
                    <div class="card shadow-lg border-primary mt-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title text-white"><i class="fas fa-trophy pr-2"></i>Hasil Perankingan SAW</h4>
                                <button id="btnSimpanKeHistory" class="btn btn-light btn-sm text-primary font-weight-bold">
                                    <i class="fas fa-archive pr-1"></i> Simpan Hasil & Bersihkan Matriks
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Ranking</th>
                                            <th class="text-left">Nama Kegiatan</th>
                                            <th>Total Skor Preferensi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyRanking">
                                        {{-- Render via JS --}}
                                    </tbody>
                                </table>
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

        function successAlert(msg = 'Berhasil!') {
            Swal.fire({ title: 'Berhasil!', text: msg, icon: 'success', showConfirmButton: false, timer: 1500 });
        }
        function errorAlert(msg = 'Terjadi kesalahan!') {
            Swal.fire({ title: 'Error', text: msg, icon: 'error', showConfirmButton: false, timer: 1500 });
        }
        function confirmAlert(message, callback) {
            Swal.fire({
                title: 'Konfirmasi!', html: message, icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Ya', cancelButtonText: 'Tidak', reverseButtons: true
            }).then((result) => { if (result.isConfirmed) callback(); });
        }
        function loadingAlert() {
            Swal.fire({ title: 'Mohon Tunggu...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        }

        // 1. Ambil Data
        function loadMatriks() {
            loadingAlert();
            const getKriteria = $.ajax({ url: '/saw/kriteria', method: 'GET' });
            const getKegiatan = $.ajax({ url: '/saw/kegiatan', method: 'GET' });
            const getKas      = $.ajax({ url: '/saw/master', method: 'GET' });
            const getNilai    = $.ajax({ url: '/saw/nilai/', method: 'GET' });

            $.when(getKriteria, getKegiatan, getKas, getNilai).done(function(resKriteria, resKegiatan, resKas, resNilai) {
                Swal.close();
                let kriteriaList = resKriteria[0].data;
                let kegiatanList = resKegiatan[0].data.filter(k => k.status_kegiatan === 'diproses');
                let kasUtama = resKas[0].data.find(k => k.is_utama == 1);
                let saldo = kasUtama ? parseFloat(kasUtama.saldo) : 0;
                let nilaiTersimpan = resNilai[0].data;

                $('#display_saldo_utama').text('Rp ' + new Intl.NumberFormat('id-ID').format(saldo));
                renderTable(kriteriaList, kegiatanList, saldo, nilaiTersimpan);
                checkIsCompleted(kegiatanList, kriteriaList, nilaiTersimpan);

            }).fail(function(){ errorAlert(); });
        }

        // 2. Render Tabel Penilaian
        function renderTable(kriterias, kegiatans, saldoUtama, nilaiTersimpan) {
            let headerHtml = '<th style="background: #f4f4f4; vertical-align: middle;">Nama Kegiatan</th>';
            $.each(kriterias, function(i, k) {
                headerHtml += `<th class="text-center">${k.nama_kriteria}<br><span class="badge ${k.tipe == 'benefit' ? 'badge-success' : 'badge-warning'}" style="font-size: 10px;">${k.tipe.toUpperCase()}</span></th>`;
            });
            $("#headerKriteria").html(headerHtml);

            let bodyHtml = "";
            if(kegiatans.length === 0) {
                bodyHtml = `<tr><td colspan="${kriterias.length + 1}" class="text-center py-5 text-muted">Tidak ada kegiatan 'Diproses'.</td></tr>`;
            } else {
                $.each(kegiatans, function(i, keg) {
                    bodyHtml += `<tr><td class="font-weight-bold text-primary">${keg.nama_kegiatan}</td>`;
                    $.each(kriterias, function(j, kri) {
                        let valExist = nilaiTersimpan.find(n => n.id_kegiatan === keg.id && n.id_kriteria === kri.id);
                        let currentVal = valExist ? valExist.nilai : "";
                        let inputHtml = "";
                        let nama = kri.nama_kriteria.toLowerCase();

                        if (nama.includes('biaya')) {
                            inputHtml = `<input type="number" name="nilai[${keg.id}][${kri.id}]" class="form-control bg-light" value="${Math.round(keg.estimasi_biaya)}" readonly>`;
                        } else if (nama.includes('dana') || nama.includes('ketersediaan')) {
                            let skorDana = Math.min((saldoUtama / keg.estimasi_biaya) * 100, 100);
                            inputHtml = `<input type="number" name="nilai[${keg.id}][${kri.id}]" class="form-control bg-light text-success font-weight-bold" value="${skorDana.toFixed(0)}" readonly>`;
                        } else {
                            inputHtml = `<input type="number" name="nilai[${keg.id}][${kri.id}]" class="form-control input-skala border-primary input-nilai" value="${currentVal}" min="0" max="100" required>`;
                        }
                        bodyHtml += `<td>${inputHtml}</td>`;
                    });
                    bodyHtml += `</tr>`;
                });
            }
            $("#bodyPenilaian").html(bodyHtml);
        }

        // 3. Cek Kelengkapan
        function checkIsCompleted(kegiatans, kriterias, nilaiTersimpan) {
            let totalNeeded = kegiatans.length * kriterias.length;
            let totalSaved = nilaiTersimpan.filter(n => kegiatans.some(k => k.id === n.id_kegiatan)).length;

            if (totalSaved >= totalNeeded && totalNeeded > 0) {
                $('#btnSimpanSemua')
                    .html('<i class="fas fa-calculator pr-2"></i>Hitung & Lihat Ranking')
                    .removeClass('btn-success').addClass('btn-primary').attr('id', 'btnProsesRanking');
                $('#formPenilaian input').attr('disabled', true);
            }
        }

        // 4. Hitung SAW (Tampil Preview)
        $(document).on('click', '#btnProsesRanking', function() {
            loadingAlert();
            $.ajax({
                url: '/saw/nilai/data',
                method: 'GET',
                success: function(response) {
                    Swal.close();
                    if(response.code === 200) {
                        let html = "";
                        $.each(response.data, function(i, item) {
                            let rank = i + 1;
                            let badge = (rank === 1) ? 'badge-success' : (rank === 2 ? 'badge-info' : 'badge-dark');
                            html += `
                            <tr class="text-center">
                                <td><span class="badge ${badge}">#${rank}</span></td>
                                <td class="text-left font-weight-bold">${item.nama_kegiatan}</td>
                                <td><h4 class="text-primary font-weight-bold mb-0">${item.skor_total}</h4></td>
                            </tr>`;
                        });
                        $('#bodyRanking').html(html);
                        $('#containerHasilRanking').fadeIn();
                        $('html, body').animate({ scrollTop: $("#containerHasilRanking").offset().top - 50 }, 800);
                    }
                },
                error: function() { Swal.close(); errorAlert(); }
            });
        });

        // 5. Simpan Hasil ke DB (ARSIP & DELETE PENILAIAN)
        $(document).on('click', '#btnSimpanKeHistory', function() {
            confirmAlert('Simpan hasil ini ke history? <br><small class="text-danger">Matriks penilaian akan dibersihkan setelah ini.</small>', function() {
                loadingAlert();
                $.ajax({
                    url: '/saw/nilai/simpan-hasil',
                    method: 'POST',
                    success: function(res) {
                        Swal.close();
                        if(res.code === 200) {
                            Swal.fire('Berhasil!', 'Data diarsipkan dan penilaian dibersihkan.', 'success')
                            .then(() => { window.location.href = "/saw/keputusan"; });
                        }
                    },
                    error: function(err) { Swal.close(); errorAlert(err.responseJSON.message); }
                });
            });
        });

        // 6. Simpan Inputan Sementara
        $(document).on('click', '#btnSimpanSemua', function() {
            let empty = false;
            $('#formPenilaian input[required]').each(function() {
                if ($(this).val() === "") { empty = true; $(this).parent().addClass('has-error'); }
            });
            if(empty) return Swal.fire('Peringatan', 'Lengkapi semua skor!', 'warning');

            confirmAlert('Simpan penilaian sementara?', function() {
                loadingAlert();
                $.ajax({
                    url: '/saw/nilai/create',
                    method: 'POST',
                    data: $('#formPenilaian').serialize(),
                    success: function(res) {
                        Swal.close();
                        if(res.code === 200) { successAlert('Tersimpan!'); loadMatriks(); }
                    },
                    error: function() { Swal.close(); errorAlert(); }
                });
            });
        });

        loadMatriks();
    });
</script>

<style>
    .has-error .form-control { border-color: #f3545d !important; }
    .table-head-bg-primary th { border: none !important; vertical-align: middle; text-align: center; }
    .badge-dark { background: #5c5d5e; color: white; }
</style>
@endsection
