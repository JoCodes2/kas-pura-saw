// notifService.js

class notifService {

    // Metode AJAX Request (tidak berubah)
    ajaxRequest(url, method, data = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url,
                method,
                data,
                processData: method !== 'GET' ? false : undefined,
                contentType: method !== 'GET' ? false : undefined,
                success: (response) => resolve(response),
                error: (error) => reject(error),
            });
        });
    }

    /**
        * Memperbarui badge notifikasi.
        */
    updateNotifBadge(count) {
        const $badge = $('#notifCountBadge');

        // Pastikan count adalah angka
        const displayCount = parseInt(count) || 0;

        $badge.text(displayCount);

        if (displayCount > 0) {
            $badge.show().addClass('visible').removeClass('d-none');
        } else {
            $badge.hide().removeClass('visible').addClass('d-none');
        }
    }

    /**
     * Menandai semua sebagai sudah dibaca.
     * PENTING: Panggil ini di Controller saat dropdown diklik.
     */
    async markAllAsRead() {
        try {
            // Gunakan endpoint mark-all-read Anda
            await this.ajaxRequest(`${window.appUrl}/presensi/notifikasi/mark-all-read`, 'POST');

            // Paksa badge menjadi 0 di UI tanpa menunggu reload data
            this.updateNotifBadge(0);
        } catch (error) {
            console.warn('Gagal mark read, mungkin endpoint belum siap:', error);
            // Tetap set 0 di frontend jika Anda ingin badge hilang meskipun API gagal
            this.updateNotifBadge(0);
        }
    }

    /**
     * Merender notifikasi ke dalam elemen #notifDropdownContent.
     */
    renderDropdownNotifications(notifications) {
        const $content = $('#notifDropdownContent');
        let html = '';

        // Gunakan moment untuk filter hari ini
        const today = moment().startOf('day');
        const todayNotifications = notifications.filter(notif => {
            return moment(notif.created_at).isSameOrAfter(today);
        });

        // Ambil 7 terbaru
        const notificationsToDisplay = todayNotifications.slice(0, 7);

        if (notificationsToDisplay.length === 0) {
            $content.html('<div class="text-center p-3 text-muted small">Tidak ada notifikasi baru hari ini.</div>');
            return;
        }

        notificationsToDisplay.forEach(notif => {
            const style = this.getNotificationStyle(notif);
            const time = moment(notif.created_at).fromNow();
            const dibacaClass = notif.is_dibaca ? '' : 'unread-item';

            // PERBAIKAN: Jangan hardcode route Laravel di JS, gunakan URL path biasa
            html += `
                <a href="${window.appUrl}/notifikasi?id=${notif.id}"
                   class="dropdown-item d-flex align-items-start ${dibacaClass} border-bottom px-3 py-2">
                    <div class="notif-icon mr-3 mt-1">
                        <i class="${style.icon} ${style.colorClass.replace('timeline-', 'text-')} fa-lg"></i>
                    </div>
                    <div class="notif-content flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="subject text-dark font-weight-bold small">${style.title}</span>
                            <span class="time text-muted small">${time}</span>
                        </div>
                        <p class="text-muted mb-0 text-xs text-wrap" style="line-height: 1.3;">
                            ${notif.pesan ? notif.pesan.substring(0, 55) : ''}...
                        </p>
                    </div>
                </a>
            `;
        });

        $content.html(html);
    }

    async getLatestDropdownData() {
        const $content = $('#notifDropdownContent');
        try {
            const responseData = await this.ajaxRequest(`${window.appUrl}/presensi/notifikasi/`, 'GET');
            const notifications = responseData.data || [];

            // 1. Update Badge berdasarkan jumlah is_dibaca === 0
            const unreadCount = notifications.filter(notif => !notif.is_dibaca).length;
            this.updateNotifBadge(unreadCount);

            // 2. Render List
            this.renderDropdownNotifications(notifications);

        } catch (error) {
            console.error('Error fetching notif:', error);
            $content.html('<div class="text-center p-3 text-danger small">Gagal memuat.</div>');
        }
    }
}

export default notifService;
