// dashboard.service.js

class DashboardService {

    // Metode AJAX Request (Tidak Berubah)
    ajaxRequest(url, method, data = null) {
        // Asumsi appUrl tersedia di global scope
        return new Promise((resolve, reject) => {
            $.ajax({
                url,
                method,
                data,
                success: (response) => resolve(response),
                error: (error) => reject(error),
            });
        });
    }

    /**
     * Helper untuk menghitung durasi shift dalam format H jam M menit.
     * Menerima string waktu 'HH:MM'
     */
    calculateShiftDuration(startTime, endTime) {
        if (!startTime || !endTime) {
            return '-';
        }

        try {
            // Konversi string waktu 'HH:MM' ke jumlah menit
            const [startHour, startMinute] = startTime.split(':').map(Number);
            const [endHour, endMinute] = endTime.split(':').map(Number);

            let startTotalMinutes = (startHour * 60) + startMinute;
            let endTotalMinutes = (endHour * 60) + endMinute;

            // Handle shift yang melewati tengah malam (misal 22:00 - 06:00)
            if (endTotalMinutes < startTotalMinutes) {
                endTotalMinutes += (24 * 60); // Tambah 24 jam
            }

            const durationMinutes = endTotalMinutes - startTotalMinutes;

            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;

            if (hours === 0 && minutes === 0) {
                return '-';
            } else if (hours === 0) {
                return `${minutes} menit`;
            } else if (minutes === 0) {
                return `${hours} jam`;
            } else {
                return `${hours} jam ${minutes} menit`;
            }

        } catch (e) {
            console.error("Gagal menghitung durasi shift:", e);
            return '-';
        }
    }


    /**
     * Helper untuk memformat status badge Absen MASUK. (Tidak Berubah)
     */
    formatStatusMasuk(status) {
        switch (status) {
            case 'tepat_waktu':
            case 'hadir':
                return { text: 'Hadir Tepat Waktu', class: 'badge-success' };
            case 'terlambat':
                return { text: 'Terlambat', class: 'badge-warning' };
            case 'tidak_absen':
                return { text: 'Tidak Absen (Alpha)', class: 'badge-danger' };
            default:
                return { text: 'N/A', class: 'badge-secondary' };
        }
    }

    /**
     * Helper untuk memformat status badge Absen KELUAR (Pulang). (Tidak Berubah)
     */
    formatStatusKeluar(status) {
        switch (status) {
            case 'tepat_waktu':
            case 'pulang_tepat_waktu':
                return { text: 'Pulang Tepat Waktu', class: 'badge-success' };
            case 'pulang_cepat':
                return { text: 'Pulang Cepat', class: 'badge-info' };
            case 'tidak_absen':
                return { text: 'Tidak Absen (Alpha)', class: 'badge-danger' };
            default:
                return { text: 'N/A', class: 'badge-secondary' };
        }
    }

    /**
     * Merender Jam Kerja Default di Card Utama.
     * PERUBAHAN: Menghitung dan menampilkan durasi shift, menyembunyikan Batas Terlambat.
     */
    renderYourAttendance(jamKerja) {
        const $cardHeader = $('#yourAttendanceCard').find('.card-header');

        const jamKerjaMasuk = jamKerja ? jamKerja.jam_masuk.substring(0, 5) : '-- : --';
        const jamKerjaKeluar = jamKerja ? jamKerja.jam_keluar.substring(0, 5) : '-- : --';

        // HITUNG DURASI SHIFT
        const shiftDuration = this.calculateShiftDuration(jamKerja?.jam_masuk, jamKerja?.jam_keluar);


        // 1. Update Header Card
        $cardHeader.html(`
            <h5 class="mb-0"><i class="fas fa-calendar-day mr-2"></i> Jam Kerja Default Hari Ini (${jamKerja ? jamKerja.nama_shift : 'N/A'})</h5>
            <small class="mt-1">Hari ini: ${new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</small>
        `);

        // 2. Update Card Body: Hanya menampilkan Waktu Jam Kerja Wajib dan Durasi
        const cardBodyContent = `
            <div class="row text-center p-3">
                <div class="col-md-6 border-right">
                    <h6 class="text-muted">Waktu Wajib Absen Masuk</h6>
                    <h2 class="display-4 text-primary fw-bold">${jamKerjaMasuk}</h2>
                    <p class="text-sm">Shift Durasi: ${shiftDuration}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Waktu Wajib Absen Pulang</h6>
                    <h2 class="display-4 text-secondary fw-bold">${jamKerjaKeluar}</h2>
                    <p class="text-sm">Tipe Hari: Hari Kerja</p>
                </div>
            </div>
        `;
        // *Opsional: Anda bisa memindahkan Durasi Shift ke bagian Pulang, atau membiarkan Tipe Hari/Keterangan lain di kolom Pulang*

        $('#yourAttendanceCard').find('.card-body').html(cardBodyContent);
    }

    /**
     * Merender data absensi semua pegawai ke dalam tabel (Tidak Berubah).
     */
    renderEmployeeAttendance(absensiPegawai) {
        const $tableMasukBody = $('#tableAbsenMasuk tbody');
        const $tablePulangBody = $('#tableAbsenPulang tbody');

        let htmlMasuk = '';
        let htmlPulang = '';

        if (absensiPegawai.length === 0) {
            const emptyRow = '<tr><td colspan="3" class="text-center text-muted">Belum ada data absensi hari ini.</td></tr>';
            $tableMasukBody.html(emptyRow);
            $tablePulangBody.html(emptyRow);
            return;
        }

        absensiPegawai.forEach(absen => {
            const userName = absen.user ? absen.user.name : 'Pegawai Tidak Dikenal';

            // --- Absen Masuk ---
            const statusMasuk = this.formatStatusMasuk(absen.status_masuk || 'alpha');
            const waktuMasuk = absen.jam_masuk ? absen.jam_masuk.substring(0, 5) : '--:--';

            htmlMasuk += `
                <tr>
                    <td>${userName}</td>
                    <td>${waktuMasuk}</td>
                    <td><span class="badge ${statusMasuk.class}">${statusMasuk.text}</span></td>
                </tr>
            `;

            // --- Absen Pulang ---
            const statusKeluar = this.formatStatusKeluar(absen.status_keluar || 'tidak_absen');
            const waktuPulang = absen.jam_keluar ? absen.jam_keluar.substring(0, 5) : '--:--';

            htmlPulang += `
                <tr>
                    <td>${userName}</td>
                    <td>${waktuPulang}</td>
                    <td><span class="badge ${statusKeluar.class}">${statusKeluar.text}</span></td>
                </tr>
            `;
        });

        $tableMasukBody.html(htmlMasuk);
        $tablePulangBody.html(htmlPulang);
    }


    /**
     * Fungsi utama untuk mengambil semua data dan merender.
     * Ditambahkan logika filter Tanggal Hari Ini.
     */
    async loadDashboardData() {
        try {
            // 1. Dapatkan tanggal hari ini dalam format YYYY-MM-DD (sesuai format database/API)
            const today = new Date().toISOString().split('T')[0];

            // Tampilkan loading state
            $('#yourAttendanceCard').find('.card-body').html('<div class="text-center p-5"><i class="fas fa-sync-alt fa-spin mr-2"></i> Memuat Data Jam Kerja...</div>');
            $('#tableAbsenMasuk tbody').html('<tr><td colspan="3" class="text-center text-muted"><i class="fas fa-sync-alt fa-spin mr-1"></i> Memuat Data Pegawai...</td></tr>');
            $('#tableAbsenPulang tbody').html('<tr><td colspan="3" class="text-center text-muted"><i class="fas fa-sync-alt fa-spin mr-1"></i> Memuat Data Pegawai...</td></tr>');

            const [jamKerjaResponse, absensiPegawaiResponse] = await Promise.all([
                this.ajaxRequest(`${appUrl}/presensi/jam/`, 'GET'),
                this.ajaxRequest(`${appUrl}/presensi/bkd/`, 'GET'),
            ]);

            const jamKerja = jamKerjaResponse.data && jamKerjaResponse.data.length > 0 ? jamKerjaResponse.data[0] : null;

            // 2. FILTER DATA: Hanya ambil data yang tanggalnya sama dengan hari ini
            const allData = absensiPegawaiResponse.data || [];
            const absensiPegawaiHariIni = allData.filter(absen => absen.tanggal === today);

            // 3. RENDER
            this.renderYourAttendance(jamKerja);
            this.renderEmployeeAttendance(absensiPegawaiHariIni);

        } catch (error) {
            console.error('Gagal memuat data dashboard:', error);
            $('#yourAttendanceCard').find('.card-body').html('<div class="text-center p-5 text-danger"><i class="fas fa-exclamation-circle mr-2"></i> Gagal memuat data dashboard.</div>');
            const errorRow = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>';
            $('#tableAbsenMasuk tbody').html(errorRow);
            $('#tableAbsenPulang tbody').html(errorRow);
        }
    }
}

export default DashboardService;
