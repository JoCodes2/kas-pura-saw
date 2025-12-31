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
                                            {{-- Header Kriteria via JS --}}
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

        // 1. Fetch Data
        function loadMatriks() {
            loadingAlert('Menghubungkan Database...');

            const getKriteria = $.ajax({ url: '/saw/kriteria', method: 'GET' });
            const getKegiatan = $.ajax({ url: '/saw/kegiatan', method: 'GET' });
            const getKas      = $.ajax({ url: '/saw/master', method: 'GET' });

            $.when(getKriteria, getKegiatan, getKas).done(function(resKriteria, resKegiatan, resKas) {
                Swal.close();

                let kriteriaList = resKriteria[0].data;
                let kegiatanList = resKegiatan[0].data.filter(k => k.status_kegiatan === 'diproses');
                let kasUtama = resKas[0].data.find(k => k.is_utama == 1);
                let saldo = kasUtama ? parseFloat(kasUtama.saldo) : 0;

                $('#display_saldo_utama').text('Rp ' + new Intl.NumberFormat('id-ID').format(saldo));
                $('#val_saldo_utama').val(saldo);

                renderTable(kriteriaList, kegiatanList, saldo);
            }).fail(function(){
                Swal.fire('Error', 'Gagal sinkronisasi data.', 'error');
            });
        }

        // 2. Render Table
        function renderTable(kriterias, kegiatans, saldoUtama) {
            // Header
            let headerHtml = '<th style="background: #f4f4f4; vertical-align: middle;">Nama Kegiatan</th>';
            $.each(kriterias, function(i, k) {
                headerHtml += `
                    <th class="text-center" style="min-width: 150px">
                        ${k.nama_kriteria}<br>
                        <span class="badge ${k.tipe == 'benefit' ? 'badge-success' : 'badge-warning'}" style="font-size: 10px;">
                            ${k.tipe.toUpperCase()}
                        </span>
                        <div class="mt-1 small text-white-50">Bobot: ${k.bobot}%</div>
                    </th>`;
            });
            $("#headerKriteria").html(headerHtml);

            // Body
            let bodyHtml = "";
            if(kegiatans.length === 0) {
                bodyHtml = `<tr><td colspan="${kriterias.length + 1}" class="text-center py-5 text-muted">Tidak ada kegiatan 'Diproses' yang tersedia.</td></tr>`;
            } else {
                $.each(kegiatans, function(i, keg) {
                    bodyHtml += `<tr><td class="font-weight-bold text-primary">${keg.nama_kegiatan}</td>`;

                    $.each(kriterias, function(j, kri) {
                        let inputHtml = "";
                        let nama = kri.nama_kriteria.toLowerCase();

                        // KRITERIA: BIAYA (Cost - Otomatis)
                        if (nama.includes('biaya')) {
                            inputHtml = `
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" name="nilai[${keg.id}][${kri.id}]"
                                        class="form-control bg-light" value="${Math.round(keg.estimasi_biaya)}" readonly>
                                </div>
                                <small class="text-muted">Nilai Riil</small>`;
                        }
                        // KRITERIA: DANA (Benefit - Otomatis %)
                        else if (nama.includes('dana') || nama.includes('ketersediaan')) {
                            let skorDana = (saldoUtama / keg.estimasi_biaya) * 100;
                            if (skorDana > 100) skorDana = 100;
                            if (saldoUtama <= 0) skorDana = 0;

                            inputHtml = `
                                <div class="input-group input-group-sm">
                                    <input type="number" name="nilai[${keg.id}][${kri.id}]"
                                        class="form-control bg-light text-success font-weight-bold"
                                        value="${skorDana.toFixed(0)}" readonly>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <small class="text-info font-italic">Skor Kas</small>`;
                        }
                        // KRITERIA: PESERTA (Benefit - Riil)
                        else if (nama.includes('peserta')) {
                            inputHtml = `
                                <div class="input-group input-group-sm">
                                    <input type="number" name="nilai[${keg.id}][${kri.id}]"
                                        class="form-control border-primary" placeholder="Orang" required>
                                </div>
                                <small class="text-muted">Jml Peserta</small>`;
                        }
                        // KRITERIA: URGENSI & ADAT (Benefit - Manual 0-100%)
                        else {
                            inputHtml = `
                                <div class="input-group input-group-sm">
                                    <input type="number" name="nilai[${keg.id}][${kri.id}]"
                                        class="form-control input-skala border-primary"
                                        placeholder="0-100" min="0" max="100" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <small class="text-muted">Input Skor</small>`;
                        }
                        bodyHtml += `<td>${inputHtml}</td>`;
                    });
                    bodyHtml += `</tr>`;
                });
            }
            $("#bodyPenilaian").html(bodyHtml);
        }

        // 3. Validasi Input Maksimal 100%
        $(document).on('keyup input', '.input-skala', function() {
            let val = parseFloat($(this).val());
            if (val > 100) {
                $(this).val(100);
                toastAlert('Maksimal nilai skor adalah 100%');
            } else if (val < 0) {
                $(this).val(0);
            }
        });

        // 4. Batch Store
        $(document).on('click', '#btnSimpanSemua', function() {
            let empty = false;
            $('#formPenilaian input[required]').each(function() {
                if ($(this).val() === "") {
                    empty = true;
                    $(this).parent().addClass('has-error');
                } else {
                    $(this).parent().removeClass('has-error');
                }
            });

            if(empty) return Swal.fire('Peringatan', 'Harap lengkapi semua skor penilaian!', 'warning');

            let formData = $('#formPenilaian').serialize();

            loadingAlert('Menyimpan Data Matriks...');
            $.ajax({
                url: '/saw/penilaian/batch-store',
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.close();
                    if(response.code === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Nilai matriks penilaian telah tersimpan.',
                            showConfirmButton: true
                        }).then(() => { location.reload(); });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Gagal menyimpan penilaian.', 'error');
                }
            });
        });

        function loadingAlert(title) {
            Swal.fire({ title: title, allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        }

        function toastAlert(msg) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({ icon: 'warning', title: msg });
        }

        loadMatriks();
    });
</script>

<style>
    .has-error .form-control { border-color: #f3545d !important; }
    .input-group-text { font-size: 11px; font-weight: bold; }
    .table-head-bg-primary th { border: none !important; }
</style>
@endsection
