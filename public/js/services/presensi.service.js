// presensi.service.js

class PresensiService {

    // Asumsi: Kita menggunakan formatStatusMasuk/Keluar yang sama dengan DashboardService.
    // Jika tidak ada library helper global, kita definisikan ulang di sini.

    /**
     * Helper untuk memformat status badge Absen MASUK.
     */
    formatStatusMasuk(status) {
        switch (status) {
            case 'tepat_waktu':
            case 'hadir':
                return { text: 'Hadir Tepat Waktu', class: 'badge-success' };
            case 'terlambat':
                return { text: 'Terlambat', class: 'badge-warning' };
            case 'tidak_absen':
                return { text: 'Alpha', class: 'badge-danger' };
            default:
                return { text: '-', class: 'badge-secondary' };
        }
    }

    /**
     * Helper untuk memformat status badge Absen KELUAR (Pulang).
     */
    formatStatusKeluar(status) {
        switch (status) {
            case 'tepat_waktu':
            case 'pulang_tepat_waktu':
                return { text: 'Pulang Tepat Waktu', class: 'badge-success' };
            case 'pulang_cepat':
                return { text: 'Pulang Cepat', class: 'badge-info' };
            case 'tidak_absen':
                return { text: 'Alpha', class: 'badge-secondary' };
            default:
                return { text: '-', class: 'badge-secondary' };
        }
    }

    /**
     * Inisialisasi DataTables untuk menampilkan data presensi.
     */
    initDataTable() {
        const self = this;
        const appUrl = window.appUrl || '';

        $('#presensiTable').DataTable({
            processing: true,
            serverSide: false, // Karena kita memuat semua data sekaligus dari API
            ajax: {
                url: `${appUrl}/presensi/bkd/`,
                type: 'GET',
                dataSrc: 'data' // Menunjuk ke array 'data' dalam respons JSON
            },
            columns: [
                // 0. No
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                // 1. Nama
                {
                    data: 'user.name',
                    defaultContent: 'N/A'
                },
                // 2. Tanggal
                {
                    data: 'tanggal',
                    render: function (data) {
                        // Format tanggal dari YYYY-MM-DD ke DD MMMM YYYY
                        return new Date(data).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    }
                },
                // 3. Jam Masuk
                {
                    data: 'jam_masuk',
                    render: function (data) {
                        return data ? data.substring(0, 5) : '--:--';
                    }
                },
                // 4. Jam Pulang
                {
                    data: 'jam_keluar',
                    render: function (data) {
                        return data ? data.substring(0, 5) : '--:--';
                    }
                },
                // 5. Status Masuk
                {
                    data: 'status_masuk',
                    render: function (data) {
                        const status = self.formatStatusMasuk(data || 'alpha');
                        return `<span class="badge ${status.class}">${status.text}</span>`;
                    }
                },
                // 6. Status Pulang
                {
                    data: 'status_keluar',
                    render: function (data) {
                        const status = self.formatStatusKeluar(data || 'tidak_absen');
                        return `<span class="badge ${status.class}">${status.text}</span>`;
                    }
                },
                // 7. Keterangan
                {
                    data: 'keterangan',
                    defaultContent: '-'
                }
                // Jika Anda ingin menambahkan kolom Aksi, letakkan di sini.
            ],
            order: [[2, 'desc']] // Default order berdasarkan tanggal terbaru
        });
    }
}

export default PresensiService;
