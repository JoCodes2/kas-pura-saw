@extends('Layouts.Base')
@section('title')
    Kas Masuk
@endsection
@section('content')
    <div class="page-inner">
        <div class="page-header ">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Daftar Kas Masuk</h4>
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
                                        <th>Tanggal</th>
                                        <th>Sumber</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
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
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content shadow-sm">

                {{-- Header --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="upsertDataModalLabel">
                        <i class="fas fa-briefcase mr-2"></i> Form Kas Masuk
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
                            <label class="font-weight-bold">Nama Kas</label>
                            <select class="form-control" name="kas_id" id="kas_id">
                                <option value="">-- Pilih Kas --</option>
                            </select>
                            <small id="kas_id-error" class="text-danger"></small>
                        </div>



                        <div class="form-group">
                            <label for="tanggal" class="font-weight-bold">
                                Tanggal
                            </label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal">
                            <small id="tanggal-error" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="sumber" class="font-weight-bold">
                                Sumber
                            </label>
                            <input type="text" class="form-control" name="sumber" id="sumber">
                            <small id="sumber-error" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="jumlah" class="font-weight-bold">
                                Jumlah
                            </label>
                            <input type="text" class="form-control" name="jumlah" id="jumlah">
                            <small id="jumlah-error" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="keterangan" class="font-weight-bold">
                                Keterangan
                            </label>
                            <input type="text" class="form-control" name="keterangan" id="keterangan">
                            <small id="keterangan-error" class="text-danger"></small>
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



            function loadMasterKas(selectedId = null) {
                $.ajax({
                    url: '/saw/kas-masuk/master-kas',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let options = '<option value="">-- Pilih Kas --</option>';

                        $.each(response.data, function(index, item) {
                            let selected = selectedId == item.id ? 'selected' : '';
                            options += `
                    <option value="${item.id}" ${selected}>
                        ${item.nama_kas}
                    </option>`;
                        });

                        $('#kas_id').html(options);
                    },
                    error: function(xhr) {
                        console.error('Gagal load master kas', xhr.responseText);
                    }
                });
            }




            function getData() {
                $.ajax({
                    url: `/saw/kas-masuk`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        let tableBody = "";

                        $.each(response.data, function(index, item) {
                            tableBody += "<tr>";
                            tableBody += "<td>" + (index + 1) + "</td>";
                            tableBody += "<td>" + (item.kas ? item.kas.nama_kas : '-') +
                                "</td>";
                            tableBody += "<td>" + item.tanggal + "</td>";
                            tableBody += "<td>" + item.sumber + "</td>";
                            tableBody += "<td>Rp " + Number(item.jumlah).toLocaleString(
                                'id-ID') + "</td>";
                            tableBody += "<td>" + item.keterangan + "</td>";

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

                let jumlah = $('#jumlah').val().replace(/[^0-9]/g, '');
                formData.set('jumlah', jumlah);
                let url = id ? `/saw/kas-masuk/update/${id}` : '/saw/kas-masuk/create';
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

                $.get(`/saw/kas-masuk/get/${id}`, function(res) {
                    $('#id').val(res.data.id);
                    $('#tanggal').val(res.data.tanggal);
                    $('#sumber').val(res.data.sumber);
                    $('#jumlah').val(formatRupiah(res.data.jumlah.toString()));
                    $('#keterangan').val(res.data.keterangan);

                    loadMasterKas(res.data.kas_id); // ⬅️ auto selected

                    $('#upsertDataModal').modal('show');
                });
            });

            // Delete data button click handler
            $(document).on('click', '.delete-confirm', function() {
                let id = $(this).data('id');

                // Function to delete data
                function deleteData() {
                    $.ajax({
                        type: 'DELETE',
                        url: `/saw/kas-masuk/delete/${id}`,
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


            //     // Show confirmation alert
            //     confirmAlert('Apakah Anda yakin ingin menghapus data?', deleteData);
            // });

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

            $('#jumlah').on('keyup', function() {
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
            // $(document).on('click', '#myBtn', function() {
            //     $('.text-danger').text('');
            //     $('#upsertDataForm')[0].reset();
            //     $('#id').val('');
            //     $('#upsertDataModal').modal('show');
            //     $('#imagePreview').html('');
            // })
            $(document).on('click', '#myBtn', function() {
                $('#upsertDataForm')[0].reset();
                $('#id').val('');

                loadMasterKas(); // ⬅️ WAJIB

                $('#upsertDataModal').modal('show');
            });



        });
    </script>
@endsection
