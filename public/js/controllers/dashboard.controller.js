import DashboardService from "../services/dashboard.service.js";

$(document).ready(function () {
    const dashboard = new DashboardService();

    // Panggil fungsi utama untuk memuat dan merender semua data dashboard
    dashboard.loadDashboardData();

    // Catatan: Jika ada tombol refresh, Anda dapat mengaitkannya di sini
});
