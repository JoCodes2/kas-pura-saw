@extends('Layouts.Base')
@section('title')
    Kriteria
@endsection
@section('content')
    <div class="page-inner">
        <div class="page-header ">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Daftar Kriteria</h4>
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
                                        <th>Nama Kriteria</th>
                                        <th>Tipe</th>
                                        <th>Bobot</th>
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
                        <i class="fas fa-tasks mr-2"></i> Form Kriteria
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <form id="upsertDataForm" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="row">
                            {{-- Nama Kriteria --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Kriteria</label>
                                    <input type="text" class="form-control" name="nama_kriteria" id="nama_kriteria"
                                        placeholder="Contoh: Biaya, Jarak, Prioritas">
                                    <small id="nama_kriteria-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Tipe Kriteria --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipe Kriteria</label>
                                    <select class="form-control" name="tipe" id="tipe">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="benefit">Benefit</option>
                                        <option value="cost">Cost</option>
                                    </select>
                                    <small id="tipe-error" class="text-danger"></small>
                                </div>
                            </div>

                            {{-- Bobot --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Bobot</label>
                                    <input type="number" class="form-control" name="bobot" id="bobot" step="0.01"
                                        min="0" max="100" placeholder="Contoh: 25">
                                    <small id="bobot-error" class="text-danger"></small>
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

            function getData() {
                $.ajax({
                    url: `/saw/kriteria`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        let tableBody = "";

                        $.each(response.data, function(index, item) {
                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + item.nama_kriteria + "</td>";
                            tableBody += "<td>" + item.tipe + "</td>";
                            tableBody += "<td>" + item.bobot + "</td>";

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

                let url = id ? `/saw/kriteria/update/${id}` : '/saw/kriteria/create';
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
                    url: `/saw/kriteria/get/${id}`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        $('#upsertDataModal').modal('show');

                        // Populate form fields with existing data
                        $('#id').val(response.data.id);
                        $('#nama_kriteria').val(response.data.nama_kriteria);
                        $('#tipe').val(response.data.tipe);
                        $('#bobot').val(response.data.bobot);

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
                        url: `/saw/kriteria/delete/${id}`,
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
