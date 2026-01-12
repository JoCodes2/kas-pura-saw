@extends('Layouts.Base')
@section('title')
    Master Kas
@endsection
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Daftar Master Kas</h4>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-6">
                <div class="card card-stats card-round shadow-sm border-left border-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-university"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category text-primary font-weight-bold">Saldo Kas Utama</p>
                                    <h4 class="card-title" id="totalKasUtama">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="card card-stats card-round shadow-sm border-left border-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category text-success font-weight-bold">Total Saldo Semua Kas</p>
                                    <h4 class="card-title" id="totalSemuaKas">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary " id="myBtn">
                                <i class="fas fa-plus pr-2"></i>Tambah
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loadData" class="display table table-striped table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kas</th>
                                        <th>Saldo</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="upsertDataModal" tabindex="-1" role="dialog" aria-labelledby="upsertDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content shadow-sm">
                {{-- Header --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="upsertDataModalLabel">
                        <i class="fas fa-briefcase mr-2"></i> Form Master Kas
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <form id="upsertDataForm" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label for="nama_kas" class="font-weight-bold">Nama Kas</label>
                            <input type="text" class="form-control" name="nama_kas" id="nama_kas">
                            <small id="nama_kas-error" class="text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label for="saldo" class="font-weight-bold">Saldo</label>
                            {{-- Saldo dibuat readonly dengan default 0 --}}
                            <input type="number" class="form-control bg-light" name="saldo" id="saldo" value="0" readonly>
                            <small class="text-muted">Saldo akan bertambah otomatis melalui Kas Masuk.</small>
                        </div>

                        <div class="form-group ml-1" id="container-is-utama">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="is_utama" id="is_utama" value="1">
                                <label class="custom-control-label font-weight-bold" for="is_utama">Jadikan Kas Utama (Sumber Dana SAW)</label>
                            </div>
                            <small id="info-utama" class="text-warning d-none">
                                <i class="fas fa-exclamation-circle"></i> Kas utama sudah tersedia.
                            </small>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpanData">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let hasMainCash = false;

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            function getData() {
                $.ajax({
                    url: `/saw/master`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        let tableBody = "";
                        hasMainCash = false;

                        let saldoUtama = 0;
                        let saldoTotalSemua = 0;

                        $.each(response.data, function(index, item) {
                            let currentSaldo = parseFloat(item.saldo);

                            // Hitung Total Semua Kas
                            saldoTotalSemua += currentSaldo;

                            // Hitung Kas Utama
                            if(item.is_utama == 1) {
                                hasMainCash = true;
                                saldoUtama += currentSaldo;
                            }

                            let statusBadge = item.is_utama == 1
                                ? '<span class="badge badge-success"><i class="fas fa-star mr-1"></i> Utama</span>'
                                : '<span class="badge badge-secondary">Reguler</span>';

                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + item.nama_kas + "</td>";
                            tableBody += "<td>Rp " + currentSaldo.toLocaleString('id-ID') + "</td>";
                            tableBody += "<td>" + statusBadge + "</td>";
                            tableBody += "<td class='text-center'>";
                            tableBody += `<button type='button' class='btn btn-outline-primary btn-sm edit-btn' data-id='${item.id}' data-isutama='${item.is_utama}'><i class='fas fa-edit'></i></button> `;
                            tableBody += `<button type='button' class='btn btn-outline-danger btn-sm delete-confirm' data-id='${item.id}'><i class='fas fa-trash'></i></button>`;
                            tableBody += "</td></tr>";
                        });

                        // Tampilkan Hasil Perhitungan ke Widget
                        $('#totalKasUtama').text("Rp " + saldoUtama.toLocaleString('id-ID'));
                        $('#totalSemuaKas').text("Rp " + saldoTotalSemua.toLocaleString('id-ID'));

                        $("#loadData tbody").html(tableBody);

                        if ($.fn.DataTable.isDataTable('#loadData')) {
                            $('#loadData').DataTable().destroy();
                        }
                        $('#loadData').DataTable({
                            destroy: true, paging: true, searching: true, ordering: true, info: true, order: []
                        });
                    }
                });
            }

            getData();
            function reloadBrowsers() {
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }

            $(document).on('click', '#simpanData', function(e) {
                e.preventDefault();
                $('.text-danger').text('');

                let id = $('#id').val();
                let formData = new FormData($('#upsertDataForm')[0]);

                // Logic tambahan untuk checkbox
                formData.set('is_utama', $('#is_utama').is(':checked') ? 1 : 0);

                let url = id ? `/saw/master/update/${id}` : '/saw/master/create';

                loadingAllert();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.close();
                        if (response.code === 200) {
                            successAlert();
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

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let currentIsUtama = $(this).data('isutama');

                $.ajax({
                    url: `/saw/master/get/${id}`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        $('#upsertDataModal').modal('show');
                        $('#id').val(response.data.id);
                        $('#nama_kas').val(response.data.nama_kas);
                        $('#saldo').val(response.data.saldo);
                        $('.text-danger').text('');

                        if(currentIsUtama == 1) {
                            $('#is_utama').prop('checked', true).parent().show();
                            $('#info-utama').addClass('d-none');
                        } else if(hasMainCash) {
                            $('#is_utama').prop('checked', false).parent().hide();
                            $('#info-utama').removeClass('d-none');
                        } else {
                            $('#is_utama').prop('checked', false).parent().show();
                            $('#info-utama').addClass('d-none');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-confirm', function() {
                let id = $(this).data('id');
                confirmAlert('Apakah Anda yakin ingin menghapus data?', function() {
                    $.ajax({
                        type: 'DELETE',
                        url: `/saw/master/delete/${id}`,
                        success: function(response) {
                            if (response.code === 200) {
                                successAlert();
                                reloadBrowsers();
                            } else {
                                errorAlert();
                            }
                        }
                    });
                });
            });

            function successAlert() {
                Swal.fire({ title: 'Berhasil!', icon: 'success', showConfirmButton: false, timer: 1000 });
            }

            function errorAlert() {
                Swal.fire({ title: 'Error', text: 'Terjadi kesalahan!', icon: 'error', showConfirmButton: false, timer: 1000 });
            }

            function confirmAlert(message, callback) {
                Swal.fire({
                    title: 'Konfirmasi!',
                    html: message,
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    reverseButtons: true
                }).then((result) => { if (result.isConfirmed) callback(); });
            }

            function loadingAllert() {
                Swal.fire({ title: 'Loading...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            }

            $(document).on('click', '#myBtn', function() {
                $('.text-danger').text('');
                $('#upsertDataForm')[0].reset();
                $('#id').val('');
                $('#saldo').val(0);

                if(hasMainCash) {
                    $('#is_utama').prop('checked', false).parent().hide();
                    $('#info-utama').removeClass('d-none');
                } else {
                    $('#is_utama').parent().show();
                    $('#info-utama').addClass('d-none');
                }
                $('#upsertDataModal').modal('show');
            });
        });
    </script>
@endsection
