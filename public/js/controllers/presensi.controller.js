// presensi.controller.js

import PresensiService from "../services/presensi.service.js"; // Sesuaikan path

$(document).ready(function () {
    const presensi = new PresensiService();

    // Inisialisasi DataTables
    presensi.initDataTable();
});
