@extends('Layouts.Base')
@section('title')
    Pengguna
@endsection
@section('content')
    <div class="page-inner">
        <div class="page-header ">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Daftar Kegiatan</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-end">
                            {{-- <button class="btn btn-primary " id="myBtn">
                                <i class="fas fa-plus pr-2"></i>Tambah
                            </button> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loadData" class="display table table-striped table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pengaju</th>
                                        <th>No Hp</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Tanggal Kegiatan</th>
                                        <th>Estimasi Biaya</th>
                                        <th>Proposal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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

    <div class="modal fade" id="upsertDataModal" tabindex="-1" aria-labelledby="upsertDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-sm">

                {{-- Header --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="upsertDataModalLabel">
                        <i class="fas fa-tasks mr-2"></i> Form Kegiatan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <form id="upsertDataForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="row">
                            {{-- Nama Pengaju --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Pengaju</label>
                                    <input type="text" class="form-control" name="nama_pengaju" id="nama_pengaju">
                                    <small id="nama_pengaju-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- No HP --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">No. HP</label>
                                    <input type="text" class="form-control" name="no_hp" id="no_hp">
                                    <small id="no_hp-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Nama Kegiatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Kegiatan</label>
                                    <input type="text" class="form-control" name="nama_kegiatan" id="nama_kegiatan">
                                    <small id="nama_kegiatan-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Tanggal Kegiatan --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tanggal Kegiatan</label>
                                    <input type="date" class="form-control" name="tanggal_kegiatan"
                                        id="tanggal_kegiatan">
                                    <small id="tanggal_kegiatan-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Estimasi Biaya --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Estimasi Biaya</label>
                                    <input type="text" class="form-control" name="estimasi_biaya" id="estimasi_biaya"
                                        placeholder="Rp 0">
                                    <small id="estimasi_biaya-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- File Proposal --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">File Proposal (PDF)</label>
                                    <input type="file" class="form-control" name="file_proposal" id="file_proposal"
                                        accept=".pdf">
                                    <small id="file_proposal-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Status Kegiatan --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status Kegiatan</label>
                                    <select class="form-control" name="status_kegiatan" id="status_kegiatan">
                                        <option value="menunggu">Menunggu</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="ditolak">Ditolak</option>
                                        <option value="ditunda">Ditunda</option>
                                        <option value="diadakan">Diadakan</option>
                                    </select>
                                    <small id="status_kegiatan-error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="simpanData">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            function formatRupiah(angka) {
                if (!angka) return '';
                let numberString = angka.replace(/[^,\d]/g, '');
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return 'Rp ' + rupiah;
            }

            function getData() {
                $.ajax({
                    url: `/saw/kegiatan`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        let tableBody = "";

                        $.each(response.data, function(index, item) {
                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + item.nama_pengaju + "</td>";
                            tableBody += "<td>" + item.no_hp + "</td>";
                            tableBody += "<td>" + item.nama_kegiatan + "</td>";
                            tableBody += "<td>" + item.tanggal_kegiatan + "</td>";
                            tableBody += "<td>Rp " + Number(item.estimasi_biaya).toLocaleString(
                                'id-ID') + "</td>";
                            if (item.file_proposal) {
                                tableBody += `
        <td class="text-center">
            <a href="/uploads/file-proposal/${item.file_proposal}"
               target="_blank"
               class="btn btn-sm btn-outline-info"
               title="Lihat Proposal">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    `;
                            } else {
                                tableBody += `
        <td class="text-center text-muted">
            -
        </td>
    `;
                            }
                            tableBody += "<td>" + item.status_kegiatan + "</td>";




                            tableBody += "<td class='text-center'>";
                            tableBody +=
                                "<button type='button' class='btn btn-outline-primary btn-sm edit-btn' data-id='" +
                                item.id +
                                "'><i class='fas fa-edit'></i></button> ";
                            tableBody +=
                                "<button type='button' class='btn btn-outline-danger btn-sm delete-confirm' data-id='" +
                                item.id +
                                "'><i class='fas fa-trash'></i></button>";
                            tableBody += "</td>";

                            tableBody += "</tr>";
                        });

                        $("#loadData tbody").html(tableBody);

                        $('#loadData').DataTable({
                            destroy: true,
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            order: []
                        });
                    },
                    error: function() {
                        console.log("Gagal mengambil data jam kerja");
                    }
                });
            }

            getData();

            $(document).on('click', '#simpanData', function(e) {
                $('.text-danger').text('');
                e.preventDefault();

                let id = $('#id').val();
                let formData = new FormData($('#upsertDataForm')[0]);

                let estimasi_biaya = $('#estimasi_biaya').val().replace(/[^0-9]/g, '');
                formData.set('estimasi_biaya', estimasi_biaya);
                let url = id ? `/saw/kegiatan/update/${id}` : '/saw/kegiatan/create';
                let method = id ? 'POST' : 'POST';

                loadingAllert();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        Swal.close();
                        if (response.code === 422) {
                            let errors = response.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else if (response.code === 200) {
                            successAlert();
                            reloadBrowsers();
                        } else {
                            errorAlert();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.close();
                        errorAlert();
                    }
                });
            });

            // Edit data button click handler
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: `/saw/kegiatan/get/${id}`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        $('#upsertDataModal').modal('show');

                        // Populate form fields with existing data
                        $('#id').val(response.data.id);
                        $('#nama_pengaju').val(response.data.nama_pengaju);
                        $('#no_hp').val(response.data.no_hp);
                        $('#nama_kegiatan').val(response.data.nama_kegiatan);
                        $('#tanggal_kegiatan').val(response.data.tanggal_kegiatan);
                        $('#estimasi_biaya').val(formatRupiah(res.data.estimasi_biaya
                            .toString()));

                        $('#file_proposal').val(response.data.file_proposal);

                        $('#status_kegiatan').val(response.data.status_kegiatan);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data for edit:', error);
                    }
                });
            });



            // Delete data button click handler
            $(document).on('click', '.delete-confirm', function() {
                let id = $(this).data('id');

                // Function to delete data
                function deleteData() {
                    $.ajax({
                        type: 'DELETE',
                        url: `/saw/kegiatan/delete/${id}`,
                        success: function(response) {
                            if (response.code === 200) {
                                successAlert();
                                reloadBrowsers();
                            } else {
                                errorAlert();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }

                // Show confirmation alert
                confirmAlert('Apakah Anda yakin ingin menghapus data?', deleteData);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // messeage alert
            // alert success message
            function successAlert(message) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: message,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1000,
                })
            }

            // alert error message
            function errorAlert() {
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan!',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 1000,
                });
            }

            function reloadBrowsers() {
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }


            function confirmAlert(message, callback) {
                Swal.fire({
                    title: '<span style="font-size: 22px"> Konfirmasi!</span>',
                    html: message,
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya',
                    reverseButtons: true,
                    confirmButtonColor: '#48ABF7',
                    cancelButtonColor: '#EFEFEF',
                    customClass: {
                        cancelButton: 'text-dark'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            }

            // loading alert
            function loadingAllert() {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            $('#estimasi_biaya').on('keyup', function() {
                let value = $(this).val();
                $(this).val(formatRupiah(value));
            });

            // reset modal
            $('#upsertDataModal').on('hidden.bs.modal', function() {
                $('.text-danger').text('');
                $('#upsertDataForm')[0].reset();
                $('#id').val('');
            });
            // event click btn create
            $(document).on('click', '#myBtn', function() {
                $('.text-danger').text('');
                $('#upsertDataForm')[0].reset();
                $('#id').val('');
                $('#upsertDataModal').modal('show');
                $('#imagePreview').html('');
            })

        });
    </script>
@endsection
