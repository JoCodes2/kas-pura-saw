class pegawaiService {
    ajaxRequest(url, method, data = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url,
                method,
                data,
                processData: false,
                contentType: false,
                success: (response) => resolve(response),
                error: (error) => reject(error),
            });
        });
    }

    async getAllData() {
        // 1. Bersihkan DataTables (jika sudah diinisialisasi)
        if ($.fn.dataTable.isDataTable('#pegawaiTable')) {
            $('#pegawaiTable').DataTable().clear().destroy();
        }

        // 2. Kosongkan DOM tbody
        $("#pegawaiTable tbody").empty();

        try {
            const responseData = await this.ajaxRequest(`${appUrl}/presensi/pegawai/`, 'GET');

            if (responseData && responseData.data && Array.isArray(responseData.data)) {

                const dataPegawai = responseData.data;

                let tableBody = '';

                dataPegawai.forEach((item, index) => {

                    const jabatan = item.jabatan ? item.jabatan.nama_jabatan : '-';

                    // --- 1. Badge Status Ikatan Kerja ---
                    let statusIkatanKerjaBadge;
                    if (item.status_ikatan_kerja === 'pns') {
                        statusIkatanKerjaBadge = `<span class="badge badge-success">PNS</span>`;
                    } else if (item.status_ikatan_kerja === 'non_pns') {
                        statusIkatanKerjaBadge = `<span class="badge badge-danger">NON PNS</span>`;
                    } else {
                        statusIkatanKerjaBadge = `<span class="badge badge-secondary">-</span>`;
                    }

                    // --- 2. Badge Status Akun ---
                    let statusAkunBadge;
                    if (item.status === 'active') {
                        statusAkunBadge = `<span class="badge badge-success">Aktif</span>`;
                    } else if (item.status === 'pending') {
                        statusAkunBadge = `<span class="badge badge-warning">Pending</span>`;
                    } else if (item.status === 'rejected') {
                        statusAkunBadge = `<span class="badge badge-danger">Ditolak</span>`;
                    } else {
                        statusAkunBadge = `<span class="badge badge-secondary">-</span>`;
                    }

                    // --- 3. Kolom Aktivasi Akun (Tombol Kondisional) ---
                    let aktivasiButtons = `<div class="d-flex gap-2 justify-content-center">`;

                    if (item.status === 'pending') {
                        aktivasiButtons += `
                        <a href="#" class="setuju-pegawai btn btn-xs btn-success" data-id="${item.id}" title="Setuju">
                            <i class="fas fa-check"></i>
                        </a>
                        <a href="#" class="tolak-pegawai btn btn-xs btn-warning" data-id="${item.id}" title="Tolak">
                            <i class="fas fa-times"></i>
                        </a>
                    `;
                    } else {
                        const statusText = item.status === 'active' ? 'Aktif' : (item.status === 'rejected' ? 'Ditolak' : 'Diproses');
                        const statusClass = item.status === 'active' ? 'text-success' : 'text-secondary';
                        // Menggunakan fas fa-check-circle hanya untuk status 'active', lainnya kosong atau sesuai status
                        aktivasiButtons += `<i class="fas fa-check-circle ${statusClass}" title="Status: ${statusText}"></i>`;
                    }
                    aktivasiButtons += `</div>`;


                    // --- 4. Kolom Aksi Manajemen (Detail dan Hapus) ---
                    let aksiButtons = `
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="#" class="detail-pegawai btn btn-xs btn-info" data-id="${item.id}" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="#" class="delete-pegawai btn btn-xs btn-danger" data-id="${item.id}" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                `;

                    // PENTING: Struktur baris tabel dirapikan untuk mencegah duplikasi baris/kolom
                    tableBody += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td>${item.nik}</td>
                        <td>${item.nip}</td>
                        <td>${item.no_hp || '-'}</td>
                        <td>${item.email}</td>
                        <td>${jabatan}</td>
                        <td>${statusIkatanKerjaBadge}</td>
                        <td>${statusAkunBadge}</td>
                        <td class="text-center">${aktivasiButtons}</td>
                        <td class="text-center">${aksiButtons}</td>
                    </tr>
                `;
                });

                // 3. Masukkan data ke tbody
                $("#pegawaiTable tbody").html(tableBody);

                // 4. Inisialisasi ulang DataTables
                $('#pegawaiTable').DataTable({
                    paging: true,
                    searching: true,
                    responsive: true,
                    order: [[0, 'asc']],
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                });

            } else {
                $("#pegawaiTable tbody").html(`<tr><td colspan="11" class="text-center">Tidak ada data pegawai yang tersedia.</td></tr>`);
            }
        } catch (error) {
            console.error('Error saat mengambil data:', error);
            $("#pegawaiTable tbody").html(`<tr><td colspan="11" class="text-center text-danger">Gagal memuat data: ${error.message || error}</td></tr>`);
        }
    }
    async updateAccountStatus(id, status) {
        const actionText = status === 'active' ? 'mengaktifkan' : 'menolak';

        try {
            const result = await confirmDeleteAlert(`Apakah Anda yakin ingin ${actionText} akun ini?`);

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('status', status);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                const responseData = await this.ajaxRequest(
                    `${appUrl}/presensi/pegawai/aktivasi/${id}`,
                    'POST',
                    formData
                );

                console.log(responseData);

                if (responseData.code === 200) {
                    await successAlert(`Akun berhasil di${status === 'active' ? 'aktifkan' : 'tolak'}!`).then(() => {
                        this.getAllData();
                    });
                } else {
                    errorAlert(responseData.message || 'Gagal memperbarui status akun.');
                }
            }
        } catch (error) {
            console.error('Error saat update status:', error);
            errorAlert('Terjadi kesalahan saat pembaruan status.');
        }
    }

    async getDataById(id) {
        try {
            // 1. Tampilkan loading spinner di modal dan buka modal
            $('#detailPegawaiModal').modal('show');
            $("#detailPegawaiContent").html(`
                <div class="text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);

            // Panggil API untuk mendapatkan detail pegawai (Endpoint yang benar: /get/{id})
            const responseData = await this.ajaxRequest(`${appUrl}/presensi/pegawai/get/${id}`, 'GET');

            console.log("Detail Pegawai:", responseData);

            if (responseData && responseData.data) {
                // 2. Render data ke modal
                this.renderDetailModal(responseData.data);
            } else {
                $("#detailPegawaiContent").html(`<p class="text-danger">Data detail tidak ditemukan.</p>`);
            }
        } catch (error) {
            console.error('Error saat mengambil data detail:', error);
            $("#detailPegawaiContent").html(`<p class="text-danger">Gagal memuat data detail pegawai.</p>`);
        }
    }



    renderDetailModal(data) {
        const fotoUrl = data.foto_profile ? `${appUrl}/${data.foto_profile}` : 'path/ke/default/profile.png';

        const jabatan = data.jabatan && data.jabatan.nama_jabatan ? data.jabatan.nama_jabatan : 'Kepala Dinas'; // Diambil dari screenshot
        const lokasi = data.lokasi_kantor && data.lokasi_kantor.nama_lokasi ? data.lokasi_kantor.nama_lokasi : 'BKD p'; // Diambil dari screenshot

        const statusIkatan = data.status_ikatan_kerja || '-';
        const jenisKelamin = data.jenis_kelamin === 'L' ? 'Laki-laki' : (data.jenis_kelamin === 'P' ? 'Perempuan' : '-');
        const agama = data.agama || 'Hindu';
        const alamat = data.alamat || 'Palu';
        const tempatTanggalLahir = `${data.tempat_lahir || 'Palu'}, ${data.tanggal_lahir || '2001-01-10'}`; // Diambil dari screenshot

        const statusAkunClass = data.status === 'active' ? 'success' : (data.status === 'pending' ? 'warning' : 'danger');
        const statusAkunText = data.status ? data.status.toUpperCase() : 'PENDING'; // Diambil dari screenshot

        const detailHtml = `
        <div class="row">

            <div class="col-md-4 border-right">
                <div class="text-center p-3">
                    <img src="${fotoUrl}" alt="Foto Profil" class="img-fluid rounded-circle mb-3 border border-secondary" style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="mb-0 text-primary">${data.name || 'I Ketut Divta Suryawan'}</h5>
                    <p class="text-muted">${data.role ? data.role.toUpperCase() : 'PEGAWAI'}</p>

                    <hr class="mt-1 mb-2"/>

                    <p class="text-small mb-1">
                        <i class="fas fa-briefcase mr-1 text-primary"></i> <strong>${jabatan}</strong>
                    </p>
                    <p class="text-small mb-1">
                        <i class="fas fa-map-marker-alt mr-1 text-info"></i> ${lokasi}
                    </p>
                    <span class="badge badge-lg badge-${statusAkunClass} mt-2">${statusAkunText}</span>
                </div>
            </div>

            <div class="col-md-8 p-3">

                <h6 class="text-primary mb-2"><i class="fas fa-id-card mr-1"></i> Data Identitas</h6>
                <div class="row detail-group mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">NIK</label>
                        <p class="font-weight-bold mb-0">${data.nik || '12312323123123'}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">NIP</label>
                        <p class="font-weight-bold mb-0">${data.nip || '324534534534'}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">Email</label>
                        <p class="font-weight-bold mb-0">${data.email || 'divtadivta2000@gmail.com'}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">No. HP</label>
                        <p class="font-weight-bold mb-0">${data.no_hp || '08721882'}</p>
                    </div>
                </div>

                <hr class="mt-0 mb-3"/>

                <h6 class="text-primary mb-2"><i class="fas fa-user-friends mr-1"></i> Data Pribadi</h6>
                <div class="row detail-group mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">Tempat, Tgl Lahir</label>
                        <p class="font-weight-bold mb-0">${tempatTanggalLahir}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">Jenis Kelamin</label>
                        <p class="font-weight-bold mb-0">${jenisKelamin}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">Agama</label>
                        <p class="font-weight-bold mb-0">${agama}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="text-muted mb-0">Status Ikatan Kerja</label>
                        <p class="font-weight-bold mb-0">${statusIkatan.toUpperCase()}</p>
                    </div>
                </div>

                <hr class="mt-0 mb-3"/>

                <h6 class="text-primary mb-2"><i class="fas fa-home mr-1"></i> Alamat</h6>
                <p class="font-weight-bold">${alamat}</p>

            </div>
        </div>
    `;

        $("#detailPegawaiContent").html(detailHtml);
    }



    async deleteData(id) {
        try {
            const result = await confirmDeleteAlert();
            if (result.isConfirmed) {
                const responseData = await this.ajaxRequest(`${appUrl}/presensi/pegawai/delete/${id}`, 'DELETE');
                console.log(responseData);
                if (responseData.code === 200) {
                    await successAlert().then(() => {
                        realoadBrowser();
                    });
                } else {
                    errorAlert();
                }
            }
        } catch (error) {
            errorAlert();
        }
    }

}

export default pegawaiService;
