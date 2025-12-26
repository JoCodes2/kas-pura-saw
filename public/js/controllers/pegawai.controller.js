import pegawaiService from "../services/pegawai.service.js";

$(document).ready(function () {
    const pegawai = new pegawaiService();;
    pegawai.getAllData();

    $(document).on('click', '.detail-pegawai', function () {
        const id = $(this).data('id');
        pegawai.getDataById(id);
    });

    $(document).on('click', '.delete-pegawai', function () {
        const id = $(this).data('id');
        pegawai.deleteData(id);
    });

    $(document).on('click', '.setuju-pegawai', async (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).data('id');
        pegawai.updateAccountStatus(id, 'active');
    });

    $(document).on('click', '.tolak-pegawai', async (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).data('id');
        pegawai.updateAccountStatus(id, 'rejected');
    });
});
