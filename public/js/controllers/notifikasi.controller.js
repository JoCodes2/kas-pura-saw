import notifService from "../services/notif.service.js";

$(document).ready(function () {
    const notifikasi = new notifService;
    notifikasi.getLatestDropdownData();
})
