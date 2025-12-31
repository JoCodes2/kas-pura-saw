@extends('Layouts.Base')
@section('title', 'Kriteria SAW')
@section('content')
    <div class="page-inner">
        <div class="page-header ">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Pengaturan Kriteria SAW</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{-- Panduan untuk User --}}
                <div class="card card-info card-announcement card-round shadow-sm">
                    <div class="card-body">
                        <div class="card-opening">Panduan Penilaian:</div>
                        <div class="card-desc">
                            1. <b>Benefit:</b> Semakin besar nilai, semakin diprioritaskan (Contoh: Urgensi). <br>
                            2. <b>Cost:</b> Semakin kecil nilai, semakin diprioritaskan (Contoh: Estimasi Biaya). <br>
                            3. <b>Bobot:</b> Masukkan angka 1-100. Pastikan <b>Total Bobot</b> semua kriteria berjumlah <b>100</b>.
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Daftar Kriteria</h4>
                            </div>
                            <button class="btn btn-primary btn-round" id="myBtn">
                                <i class="fas fa-plus pr-2"></i>Tambah Kriteria
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loadData" class="display table table-striped table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kriteria</th>
                                        <th>Tipe</th>
                                        <th>Bobot (%)</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tBody"></tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa; font-weight: bold;">
                                        <td colspan="3" class="text-right">Total Bobot saat ini:</td>
                                        <td id="totalBobot" style="font-size: 1.2rem;">0</td>
                                        <td><small id="statusBobot"></small></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ url('/normalisasi') }}" id="btnLanjut" class="btn btn-success btn-round disabled" style="cursor: not-allowed;">
                                    Lanjut ke Penilaian <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Upsert --}}
    <div class="modal fade" id="upsertDataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Data Kriteria</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="upsertDataForm">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kriteria</label>
                            <input type="text" class="form-control" name="nama_kriteria" id="nama_kriteria" placeholder="Contoh: Urgensi Upacara">
                            <small id="nama_kriteria-error" class="text-danger"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipe</label>
                                    <select class="form-control" name="tipe" id="tipe">
                                        <option value="benefit">Benefit (Keuntungan)</option>
                                        <option value="cost">Cost (Biaya)</option>
                                    </select>
                                    <small id="tipe-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bobot (1-100)</label>
                                    <input type="number" class="form-control" name="bobot" id="bobot" placeholder="Misal: 25">
                                    <small id="bobot-error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-border" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="simpanData">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        // Setup CSRF Token untuk Laravel
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getData() {
            $.ajax({
                url: `/saw/kriteria`,
                method: "GET",
                dataType: "json",
                success: function(response) {
                    let tableBody = "";
                    let totalBobot = 0;

                    if (response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            let bobotValue = parseFloat(item.bobot);
                            totalBobot += bobotValue;

                            tableBody += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.nama_kriteria}</td>
                                <td><span class="badge ${item.tipe == 'benefit' ? 'badge-success' : 'badge-warning'}">${item.tipe.toUpperCase()}</span></td>
                                <td class="font-weight-bold">${item.bobot}%</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-id="${item.id}"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-confirm" data-id="${item.id}"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>`;
                        });
                    } else {
                        tableBody = '<tr><td colspan="5" class="text-center">Belum ada data kriteria.</td></tr>';
                    }

                    $("#loadData tbody").html(tableBody);
                    $("#totalBobot").text(totalBobot + "%");

                    // LOGIKA VALIDASI LANJUT PERHITUNGAN
                    const btnLanjut = $('#btnLanjut');

                    if(Math.abs(totalBobot - 100) < 0.01) { // Menggunakan toleransi desimal
                        $("#totalBobot").removeClass('text-danger').addClass('text-success');
                        $("#statusBobot").html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Siap Hitung</span>');

                        // Aktifkan Tombol
                        btnLanjut.removeClass('disabled').css('cursor', 'pointer');
                    } else {
                        $("#totalBobot").removeClass('text-success').addClass('text-danger');
                        $("#statusBobot").html('<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Total Bobot Harus 100%</span>');

                        // Matikan Tombol
                        btnLanjut.addClass('disabled').css('cursor', 'not-allowed');
                    }
                }
            });
        }

        // Tambahkan event handler untuk mencegah klik jika masih disabled
        $(document).on('click', '#btnLanjut', function(e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak',
                    text: 'Anda tidak dapat melanjutkan perhitungan sebelum total bobot kriteria berjumlah 100%!',
                });
            }
        });

        // Jalankan fungsi saat halaman load
        getData();

        // 2. Fungsi Simpan & Update (Upsert)
        $(document).on('click', '#simpanData', function(e) {
            e.preventDefault();
            $('.text-danger').text(''); // Reset pesan error

            let id = $('#id').val();
            let formData = new FormData($('#upsertDataForm')[0]);
            let url = id ? `/saw/kriteria/update/${id}` : '/saw/kriteria/create';

            loadingAlert(); // Panggil sweetalert loading

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.close();
                    if (response.code === 200) {
                        successAlert(response.message);
                        $('#upsertDataModal').modal('hide');
                        reloadBrowsers();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        let response = xhr.responseJSON;
                        let errors = response.data;

                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else {
                        errorAlert();
                    }
                }
            });
        });
        function reloadBrowsers() {
            setTimeout(function() {
                location.reload();
            }, 1500);
        }

        // 3. Fungsi Get Data untuk Edit
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            $('.text-danger').text('');

            $.ajax({
                url: `/saw/kriteria/get/${id}`,
                method: "GET",
                dataType: "json",
                success: function(response) {
                    $('#upsertDataModal').modal('show');
                    $('#id').val(response.data.id);
                    $('#nama_kriteria').val(response.data.nama_kriteria);
                    $('#tipe').val(response.data.tipe);
                    $('#bobot').val(response.data.bobot);
                }
            });
        });

        // 4. Fungsi Hapus
        $(document).on('click', '.delete-confirm', function() {
            let id = $(this).data('id');

            confirmAlert('Apakah Anda yakin ingin menghapus kriteria ini?', function() {
                $.ajax({
                    type: 'DELETE',
                    url: `/saw/kriteria/delete/${id}`,
                    success: function(response) {
                        if (response.code === 200) {
                            successAlert('Kriteria berhasil dihapus');
                            reloadBrowsers();
                        } else {
                            errorAlert();
                        }
                    }
                });
            });
        });

        // --- Helper functions untuk SweetAlert ---
        function successAlert(msg) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: msg, timer: 1500, showConfirmButton: false });
        }

        function errorAlert(msg = 'Terjadi kesalahan!') {
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        }

        function loadingAlert() {
            Swal.fire({ title: 'Mohon Tunggu...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        }

        function confirmAlert(message, callback) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) { callback(); } });
        }

        // Reset Modal saat ditutup
        $('#upsertDataModal').on('hidden.bs.modal', function() {
            $('#upsertDataForm')[0].reset();
            $('#id').val('');
            $('.text-danger').text('');
        });

        // Tombol Tambah Baru
        $(document).on('click', '#myBtn', function() {
            $('#upsertDataModal').modal('show');
        });
    });
</script>
@endsection
