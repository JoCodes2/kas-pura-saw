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
                        </div>
                    </div>
                </div>

                {{-- CARD 1: MATRIKS PENILAIAN --}}
                <div class="card card-round shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Matriks Penilaian</h4>
                                <p class="card-category" id="status-teks">Berikan penilaian skor (1-100) untuk kriteria kualitatif.</p>
                            </div>
                            <div id="wrapper-tombol">
                                <button type="button" class="btn btn-success btn-round" id="btnSimpanSemua">
                                    <i class="fas fa-save pr-2"></i>Simpan Penilaian
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form id="formPenilaian">
                                @csrf
                                <table class="table table-bordered table-head-bg-primary table-hover" id="tablePenilaian">
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
                    <div class="card shadow-lg border-primary mt-4 card-round">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title text-white"><i class="fas fa-trophy pr-2"></i>Hasil Perankingan SAW</h4>
                                <button id="btnSimpanHasil" class="btn btn-light btn-sm text-primary font-weight-bold btn-round">
                                    <i class="fas fa-archive pr-1"></i> Simpan Hasil
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="100">Ranking</th>
                                            <th class="text-left">Nama Kegiatan</th>
                                            <th width="200">Total Skor Preferensi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyRanking">
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

        let kriteriaList = [];
        let kegiatanList = [];
        let nilaiTersimpan = [];
        let hasValidationError = false;

        function loadMatriks() {
            loadingAlert();

            Promise.all([
                $.ajax({ url: '/saw/kriteria', method: 'GET' }),
                $.ajax({ url: '/saw/kegiatan', method: 'GET' }),
                $.ajax({ url: '/saw/master', method: 'GET' }),
                $.ajax({ url: '/saw/nilai/', method: 'GET' })
            ])
            .then(function([resKriteria, resKegiatan, resKas, resNilai]) {
                Swal.close();

                kriteriaList = resKriteria.data || resKriteria[0]?.data || [];
                kegiatanList = (resKegiatan.data || resKegiatan[0]?.data || []).filter(k => k.status_kegiatan === 'diproses');
                let kasUtama = (resKas.data || resKas[0]?.data || []).find(k => k.is_utama == 1);
                let saldo = kasUtama ? parseFloat(kasUtama.saldo) : 0;

                // Coba berbagai struktur data
                let nilaiData = null;
                if (resNilai && resNilai.data) {
                    nilaiData = resNilai.data;
                } else if (resNilai && Array.isArray(resNilai)) {
                    nilaiData = resNilai;
                } else if (resNilai && resNilai[0] && resNilai[0].data) {
                    nilaiData = resNilai[0].data;
                } else {
                    nilaiData = [];
                }

                $('#display_saldo_utama').text('Rp ' + new Intl.NumberFormat('id-ID').format(saldo));
                $('#val_saldo_utama').val(saldo);
                renderTable(kriteriaList, kegiatanList, saldo, nilaiData);

            })
            .catch(function(error) {
                simpleAlert('Gagal memuat data', 'error');
            });
        }

        function renderTable(kriterias, kegiatans, saldoUtama, nilaiTersimpanData) {
            // Pastikan nilaiTersimpan adalah array
            if (Array.isArray(nilaiTersimpanData)) {
                nilaiTersimpan = nilaiTersimpanData;
            } else if (nilaiTersimpanData && Array.isArray(nilaiTersimpanData.data)) {
                nilaiTersimpan = nilaiTersimpanData.data;
            } else if (nilaiTersimpanData && typeof nilaiTersimpanData === 'object') {
                nilaiTersimpan = Object.values(nilaiTersimpanData);
            } else {
                nilaiTersimpan = [];
            }

            // Tentukan mode saat ini
            const isSimpanMode = $('#tablePenilaian').hasClass('table-simpan');

            // Render header
            let headerHtml = '<th style="background: #f8f9fa; vertical-align: middle;">Nama Kegiatan</th>';
            $.each(kriterias, function(i, k) {
                let badgeClass = k.tipe == 'benefit' ? 'badge-success' : 'badge-warning';

                headerHtml += `
                    <th class="text-center" style="background: #f8f9fa; min-width: 120px;">
                        <div class="small font-weight-bold">${k.nama_kriteria}</div>
                        <div><span class="badge ${badgeClass}" style="font-size: 9px;">${k.tipe.toUpperCase()}</span></div>
                    </th>`;
            });
            $("#headerKriteria").html(headerHtml);

            // Render body
            let bodyHtml = "";
            if(kegiatans.length === 0) {
                bodyHtml = `<tr><td colspan="${kriterias.length + 1}" class="text-center py-5 text-muted">Tidak ada kegiatan 'Diproses'.</td></tr>`;
            } else {
                $.each(kegiatans, function(i, keg) {
                    bodyHtml += `<tr data-kegiatan="${keg.id}"><td class="font-weight-bold" style="vertical-align: middle;">${keg.nama_kegiatan}</td>`;

                    $.each(kriterias, function(j, kri) {
                        // Cari nilai yang tersimpan
                        let valExist = null;
                        let currentVal = "";

                        try {
                            if (Array.isArray(nilaiTersimpan)) {
                                valExist = nilaiTersimpan.find(n => {
                                    if (!n) return false;
                                    return n.id_kegiatan == keg.id && n.id_kriteria == kri.id;
                                });
                            }
                        } catch (error) {
                            console.error('Error finding nilai:', error);
                        }

                        currentVal = valExist && valExist.nilai != null ? valExist.nilai : "";
                        let nama = kri.nama_kriteria.toLowerCase();
                        let isAuto = nama.includes('biaya') || nama.includes('dana') || nama.includes('ketersediaan');
                        let inputHtml = "";

                        if (nama.includes('biaya')) {
                            // Logika BIAYA
                            let biaya = Math.round(keg.estimasi_biaya || 0);
                            inputHtml = `
                                <div class="text-center">
                                    <div class="text-dark small font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(biaya)}</div>
                                    <input type="hidden" name="nilai[${keg.id}][${kri.id}]" value="${biaya}">
                                </div>`;
                        }
                        else if (nama.includes('peserta')) {
                            // Mode Khusus Peserta (Input Jumlah Orang Manual)
                            if (isSimpanMode) {
                                // Tampilan setelah disimpan (Badge "Orang")
                                inputHtml = `
                                    <div class="text-center" style="vertical-align: middle;">
                                        <span class="badge badge-secondary px-2 py-1 display-nilai small">${currentVal || 0} Orang</span>
                                        <input type="hidden" name="nilai[${keg.id}][${kri.id}]" value="${currentVal || ''}">
                                    </div>`;
                            } else {
                                // Mode Input Manual (Bukan persentase, tapi jumlah orang)
                                inputHtml = `
                                    <div class="text-center px-1">
                                        <input type="number"
                                            name="nilai[${keg.id}][${kri.id}]"
                                            class="form-control input-nilai input-peserta text-center"
                                            value="${currentVal}"
                                            min="1"
                                            placeholder="Jml"
                                            style="height: 35px; font-size: 13px; width: 80px; margin: 0 auto; border-color: #6861ce !important;"
                                            data-kegiatan="${keg.id}"
                                            data-kriteria="${kri.id}">
                                        <div class="text-muted mt-1" style="font-size: 10px;">Jumlah Orang</div>
                                        <div class="invalid-feedback d-block text-center small mt-1" style="font-size: 10px; display: none;">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Wajib diisi
                                        </div>
                                    </div>`;
                            }
                        }
                        else if (nama.includes('dana') || nama.includes('ketersediaan')) {
                            // Logika KETERSEDIAAN DANA
                            let biayaEstimasi = keg.estimasi_biaya || 1;
                            let skorDana = saldoUtama > 0 ? Math.min((saldoUtama / biayaEstimasi) * 100, 100) : 0;
                            inputHtml = `
                                <div class="text-center">
                                    <span class="badge badge-success px-2 py-1 small" style="font-size: 11px;">${skorDana.toFixed(0)}</span>
                                    <input type="hidden" name="nilai[${keg.id}][${kri.id}]" value="${skorDana.toFixed(0)}">
                                </div>`;
                        } else {
                            // Kriteria kualitatif
                            if (isSimpanMode) {
                                // Mode display (sudah disimpan)
                                inputHtml = `
                                    <div class="text-center" style="vertical-align: middle;">
                                        <span class="badge badge-info px-2 py-1 display-nilai small">${currentVal || 0}</span>
                                        <input type="hidden" name="nilai[${keg.id}][${kri.id}]" value="${currentVal || ''}">
                                    </div>`;
                            } else {
                                // Mode input - tanpa feedback visual awal
                                inputHtml = `
                                    <div class="text-center px-1" style="position: relative;">
                                        <input type="number"
                                            name="nilai[${keg.id}][${kri.id}]"
                                            class="form-control input-nilai text-center"
                                            value="${currentVal}"
                                            min="1"
                                            max="100"
                                            placeholder="0"
                                            style="height: 35px; font-size: 13px; padding: 5px; width: 70px; margin: 0 auto;"
                                            data-kegiatan="${keg.id}"
                                            data-kriteria="${kri.id}">
                                        <div class="invalid-feedback d-block text-center small mt-1" style="font-size: 10px; display: none;">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Wajib diisi
                                        </div>
                                    </div>`;
                            }
                        }
                        bodyHtml += `<td style="vertical-align: middle;">${inputHtml}</td>`;
                    });
                    bodyHtml += `</tr>`;
                });
            }

            $("#bodyPenilaian").html(bodyHtml);

            // Check status setelah render
            checkStatusPenilaian();
        }

        function checkStatusPenilaian() {
            // Hitung jumlah kriteria kualitatif
            let kualitatifKriteria = kriteriaList.filter(k => {
                let nama = k.nama_kriteria.toLowerCase();
                return !(nama.includes('biaya') || nama.includes('dana') || nama.includes('ketersediaan'));
            });

            let totalNeeded = kegiatanList.length * kualitatifKriteria.length;
            let totalSaved = 0;

            // Hitung nilai yang sudah tersimpan untuk kriteria kualitatif saja
            $.each(kegiatanList, function(i, keg) {
                $.each(kualitatifKriteria, function(j, kri) {
                    try {
                        if (Array.isArray(nilaiTersimpan)) {
                            let valExist = nilaiTersimpan.find(n => {
                                if (!n) return false;
                                return n.id_kegiatan == keg.id && n.id_kriteria == kri.id;
                            });

                            if (valExist && valExist.nilai !== "" && valExist.nilai !== null && valExist.nilai >= 1) {
                                totalSaved++;
                            }
                        }
                    } catch (error) {
                        console.error('Error checking status:', error);
                    }
                });
            });

            if (totalNeeded > 0 && totalSaved >= totalNeeded && !$('#tablePenilaian').hasClass('table-simpan')) {
                // Pindah ke mode simpan jika semua sudah diisi dan belum dalam mode simpan
                showModeSimpan();
            } else if (totalNeeded > 0 && totalSaved < totalNeeded && $('#tablePenilaian').hasClass('table-simpan')) {
                // Kembali ke mode input jika belum lengkap tapi dalam mode simpan
                showModeInput();
            }
        }

        function showModeSimpan() {
            // Update tombol
            $('#wrapper-tombol').html(`
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-warning btn-round mr-2" id="btnResetPenilaian">
                        <i class="fas fa-redo pr-2"></i>Reset Penilaian
                    </button>
                    <button type="button" class="btn btn-primary btn-round" id="btnHitungSekarang">
                        <i class="fas fa-calculator pr-2"></i>Hitung Sekarang
                    </button>
                </div>
            `);

            $('#status-teks').html('<span class="badge badge-success px-3 py-2 small"><i class="fas fa-check mr-1"></i> Semua Nilai Tersimpan</span>');
            $('#tablePenilaian').addClass('table-simpan');

            // Render ulang table dalam mode simpan
            let kasUtama = parseFloat($('#val_saldo_utama').val());
            renderTable(kriteriaList, kegiatanList, kasUtama, nilaiTersimpan);

            // Hide hasil ranking jika ada
            $('#containerHasilRanking').hide();

            bindButtonEvents();
        }

        function showModeInput() {
            // Update tombol
            $('#wrapper-tombol').html(`
                <button type="button" class="btn btn-success btn-round" id="btnSimpanSemua">
                    <i class="fas fa-save pr-2"></i>Simpan Penilaian
                </button>
            `);

            $('#status-teks').html('<span class="badge badge-info px-3 py-2 small"><i class="fas fa-edit mr-1"></i> Masukkan Nilai (1-100)</span>');
            $('#tablePenilaian').removeClass('table-simpan');

            // Render ulang table dalam mode input
            let kasUtama = parseFloat($('#val_saldo_utama').val());
            renderTable(kriteriaList, kegiatanList, kasUtama, nilaiTersimpan);

            // Hide hasil ranking
            $('#containerHasilRanking').hide();

            bindButtonEvents();
        }

        function bindButtonEvents() {
            // Remove existing event listeners
            $(document).off('click', '#btnSimpanSemua');
            $(document).off('click', '#btnResetPenilaian');
            $(document).off('click', '#btnHitungSekarang');
            $(document).off('click', '#btnSimpanHasil');

            // Bind new event listeners
            $(document).on('click', '#btnSimpanSemua', simpanPenilaian);
            $(document).on('click', '#btnResetPenilaian', resetPenilaian);
            $(document).on('click', '#btnHitungSekarang', hitungRanking);
            $(document).on('click', '#btnSimpanHasil', simpanHasil);
        }

        function clearValidationErrors() {
            // Hapus semua status validasi
            $('.input-nilai').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            hasValidationError = false;
        }

        function showValidationErrors() {
            let hasEmpty = false;
            let hasInvalidRange = false;

            $('.input-nilai').each(function() {
                let $input = $(this);
                let val = $input.val();
                let $feedback = $input.closest('td').find('.invalid-feedback');

                // Cek apakah ini input peserta atau kriteria kualitatif biasa
                let isPeserta = $input.hasClass('input-peserta');

                if (val === "" || val === null) {
                    hasEmpty = true;
                    $input.addClass('is-invalid');
                    $feedback.html('Wajib diisi').show();
                } else if (val < 1) {
                    hasInvalidRange = true;
                    $input.addClass('is-invalid');
                    $feedback.html('Min. 1').show();
                } else if (!isPeserta && val > 100) {
                    // Hanya kriteria NON-PESERTA yang dibatasi maksimal 100
                    hasInvalidRange = true;
                    $input.addClass('is-invalid');
                    $feedback.html('Maks. 100').show();
                }
            });

            if (hasEmpty || hasInvalidRange) {
                hasValidationError = true;

                // Scroll ke input pertama yang error
                let $firstInvalid = $('.input-nilai.is-invalid').first();
                if ($firstInvalid.length) {
                    $('html, body').animate({
                        scrollTop: $firstInvalid.closest('tr').offset().top - 100
                    }, 500);
                }

                return false;
            }

            return true;
        }

        function simpanPenilaian() {
            // Clear previous validation errors
            clearValidationErrors();

            // Show validation errors
            if (!showValidationErrors()) {
                // Validation failed, don't proceed
                return;
            }

            loadingAlert();
            $.ajax({
                url: '/saw/nilai/create',
                method: 'POST',
                data: $('#formPenilaian').serialize(),
                success: function(res) {
                    Swal.close();
                    if(res.code === 200) {
                        simpleAlert('Berhasil', 'success');

                        // Update nilaiTersimpan dengan data terbaru
                        if (res.data && Array.isArray(res.data)) {
                            nilaiTersimpan = res.data;
                        }
                        reloadBrowsers();

                        // Clear validation errors after successful save
                        clearValidationErrors();

                        // Check status setelah simpan
                        checkStatusPenilaian();
                    } else {
                        simpleAlert('Gagal menyimpan penilaian', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    simpleAlert(xhr.responseJSON?.message || 'Gagal menyimpan penilaian', 'error');
                }
            });
        }

        function resetPenilaian() {
            confirmAlert('Konfirmasi!', 'Apakah anda yakin?')
            .then((result) => {
                if (result.isConfirmed) {
                    loadingAlert();

                    // Panggil API untuk menghapus semua penilaian
                    $.ajax({
                        url: '/saw/nilai/clear-penilaian',
                        method: 'DELETE',
                        success: function(res) {
                            Swal.close();
                            if(res.code === 200) {
                                // Kosongkan nilai tersimpan
                                nilaiTersimpan = [];

                                // Kembali ke mode input
                                showModeInput();

                                // Clear validation errors
                                clearValidationErrors();

                                simpleAlert('Berhasil', 'success');
                                reloadBrowsers();
                            } else {
                                simpleAlert('Gagal mereset penilaian', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            simpleAlert(xhr.responseJSON?.message || 'Gagal mereset penilaian', 'error');
                        }
                    });
                }
            });
        }

        function hitungRanking() {
            loadingAlert();
            $.ajax({
                url: '/saw/nilai/data',
                method: 'GET',
                success: function(response) {
                    Swal.close();

                    if(response.code === 200 && response.data && response.data.length > 0) {
                        // Tampilkan hasil ranking
                        let html = "";
                        $.each(response.data, function(i, item) {
                            let rank = i + 1;
                            let badge = '';
                            let icon = '';

                            if (rank === 1) {
                                badge = 'badge-success';
                                icon = '<i class="fas fa-crown mr-1"></i>';
                            } else if (rank === 2) {
                                badge = 'badge-info';
                                icon = '<i class="fas fa-medal mr-1"></i>';
                            } else if (rank === 3) {
                                badge = 'badge-warning';
                                icon = '<i class="fas fa-award mr-1"></i>';
                            } else {
                                badge = 'badge-secondary';
                                icon = '<i class="fas fa-hashtag mr-1"></i>';
                            }

                            html += `<tr class="text-center">
                                <td style="vertical-align: middle;">
                                    <span class="badge ${badge} px-2 py-1" style="font-size: 13px;">
                                        ${icon} ${rank}
                                    </span>
                                </td>
                                <td class="text-left" style="vertical-align: middle;">${item.nama_kegiatan}</td>
                                <td style="vertical-align: middle;">
                                    <span class="text-primary font-weight-bold" style="font-size: 16px;">  ${(Math.round(parseFloat(item.nilai_preferensi) * 100) / 100).toLocaleString('id-ID', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}</span>
                                </td>
                            </tr>`;
                        });

                        $('#bodyRanking').html(html);
                        $('#containerHasilRanking').fadeIn();

                        // Update tombol (disable reset dan hitung, tambahkan simpan hasil)
                        $('#wrapper-tombol').html(`
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-warning btn-round mr-2 btn-disabled" disabled>
                                    <i class="fas fa-redo pr-2"></i>Reset Penilaian
                                </button>
                                <button type="button" class="btn btn-primary btn-round mr-2 btn-disabled" disabled>
                                    <i class="fas fa-calculator pr-2"></i>Hitung Sekarang
                                </button>
                            </div>
                        `);

                        simpleAlert('Berhasil', 'success');
                        bindButtonEvents();

                    } else {
                        simpleAlert('Tidak ada data untuk dihitung', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    simpleAlert('Gagal menghitung ranking', 'error');
                }
            });
        }

        function simpanHasil() {
            confirmAlert('Konfirmasi!', 'Apakah anda yakin?')
            .then((result) => {
                if (result.isConfirmed) {
                    loadingAlert();
                    $.ajax({
                        url: '/saw/nilai/simpan-hasil',
                        method: 'POST',
                        success: function(res) {
                            Swal.close();
                            if(res.code === 200) {
                                simpleAlert('Berhasil', 'success')
                                .then(() => {
                                    window.location.href = "/keputusan";
                                });
                            } else {
                                simpleAlert('Gagal menyimpan hasil', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            simpleAlert(xhr.responseJSON?.message || 'Gagal menyimpan hasil', 'error');
                        }
                    });
                }
            });
        }

        // Validasi real-time hanya untuk user feedback (tidak muncul saat input)
        $(document).on('input', '.input-nilai', function() {
            // Hanya hapus error jika user mengisi field yang sebelumnya error
            let $input = $(this);
            if ($input.hasClass('is-invalid')) {
                $input.removeClass('is-invalid');
                $input.closest('td').find('.invalid-feedback').hide();
            }
        });

        // Initial load
        loadMatriks();
        bindButtonEvents();
    });
</script>

<style>
    .table-head-bg-primary th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        vertical-align: middle !important;
        text-align: center !important;
        font-size: 13px;
        padding: 10px 5px !important;
    }

    .table-simpan {
        border: 2px solid #28a745 !important;
    }

    .table-simpan td {
        background-color: #f8fff9 !important;
    }

    .input-nilai {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        transition: border-color 0.15s ease-in-out !important;
        display: inline-block !important;
    }

    .input-nilai:focus {
        border-color: #4e73df !important;
        box-shadow: 0 0 0 0.1rem rgba(78, 115, 223, 0.25) !important;
        outline: none !important;
    }

    .input-nilai.is-invalid {
        border-color: #e74a3b !important;
    }

    .input-nilai.is-invalid:focus {
        box-shadow: 0 0 0 0.1rem rgba(231, 74, 59, 0.25) !important;
    }

    .display-nilai {
        font-size: 13px !important;
        font-weight: 500 !important;
        border-radius: 4px !important;
        min-width: 40px !important;
        display: inline-block !important;
    }

    #tablePenilaian {
        border-radius: 8px;
        overflow: hidden;
        font-size: 13px;
    }

    #tablePenilaian td, #tablePenilaian th {
        padding: 8px 5px !important;
        border: 1px solid #e3e6f0 !important;
    }

    #tablePenilaian td:first-child {
        font-weight: 500;
        color: #2d3748;
    }

    .badge-success {
        background-color: #28a745 !important;
    }

    .badge-info {
        background-color: #17a2b8 !important;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-secondary {
        background-color: #6c757d !important;
    }

    .btn-round {
        border-radius: 20px !important;
        padding: 8px 20px !important;
        font-size: 14px !important;
    }

    .btn-disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }

    #status-teks {
        font-size: 13px;
        margin-top: 3px;
    }

    #containerHasilRanking {
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Compact table styling */
    .small {
        font-size: 12px !important;
    }

    .table td .text-dark.small {
        font-size: 11px !important;
        line-height: 1.2;
    }

    /* Validation styles */
    .invalid-feedback {
        font-size: 10px !important;
        padding: 2px 0 !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        color: #e74a3b !important;
    }
</style>
@endsection
