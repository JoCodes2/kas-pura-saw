@extends('Layouts.Base')
@section('title', 'Dashboard Keuangan')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title"><i class="fas fa-home pr-2"></i>Dashboard Keuangan</h4>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5"><div class="icon-big text-center icon-primary"><i class="fas fa-university"></i></div></div>
                        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Kas Utama</p><h4 class="card-title" id="dash_kas_utama">Rp 0</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5"><div class="icon-big text-center icon-info"><i class="fas fa-wallet"></i></div></div>
                        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Total Semua Kas</p><h4 class="card-title" id="dash_total_kas">Rp 0</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round shadow-sm border-left border-success">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5"><div class="icon-big text-center icon-success"><i class="fas fa-arrow-down"></i></div></div>
                        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Total Kas Masuk</p><h4 class="card-title text-success" id="dash_total_masuk">Rp 0</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round shadow-sm border-left border-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5"><div class="icon-big text-center icon-danger"><i class="fas fa-arrow-up"></i></div></div>
                        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Total Kas Keluar</p><h4 class="card-title text-danger" id="dash_total_keluar">Rp 0</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header"><div class="card-title"><i class="fas fa-history pr-2"></i>Kas Masuk Terakhir</div></div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table table-head-bg-success table-hover">
                            <thead><tr><th>Tanggal</th><th>Kas</th><th>Jumlah</th></tr></thead>
                            <tbody id="dash_body_masuk"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header"><div class="card-title"><i class="fas fa-history pr-2"></i>Kas Keluar Terakhir</div></div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table table-head-bg-danger table-hover">
                            <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Jumlah</th></tr></thead>
                            <tbody id="dash_body_keluar"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    const formatIDR = (val) => "Rp " + parseFloat(val).toLocaleString('id-ID');

    function loadDashboard() {
        // 1. Ambil Data Master Kas (Untuk Saldo)
        $.get('/saw/master').done(res => {
            let totalSemua = 0;
            let kasUtama = 0;
            res.data.forEach(k => {
                totalSemua += parseFloat(k.saldo);
                if(k.is_utama == 1) kasUtama = k.saldo;
            });
            $('#dash_kas_utama').text(formatIDR(kasUtama));
            $('#dash_total_kas').text(formatIDR(totalSemua));
        });

        // 2. Ambil Data Kas Masuk
        $.get('/saw/kas-masuk').done(res => {
            let totalMasuk = 0;
            let htmlMasuk = '';
            // Ambil 5 data terbaru saja
            res.data.slice(0, 5).forEach(item => {
                totalMasuk += parseFloat(item.jumlah);
                htmlMasuk += `<tr>
                    <td><small>${item.tanggal}</small></td>
                    <td>${item.kas.nama_kas}</td>
                    <td class="text-success font-weight-bold">${formatIDR(item.jumlah)}</td>
                </tr>`;
            });
            $('#dash_total_masuk').text(formatIDR(totalMasuk));
            $('#dash_body_masuk').html(htmlMasuk || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
        });

        // 3. Ambil Data Kas Keluar
        $.get('/saw/master/kas-out').done(res => {
            let totalKeluar = 0;
            let htmlKeluar = '';
            res.data.slice(0, 5).forEach(item => {
                totalKeluar += parseFloat(item.jumlah);
                htmlKeluar += `<tr>
                    <td><small>${item.tanggal}</small></td>
                    <td>${item.kegiatan ? item.kegiatan.nama_kegiatan : 'Lainnya'}</td>
                    <td class="text-danger font-weight-bold">${formatIDR(item.jumlah)}</td>
                </tr>`;
            });
            $('#dash_total_keluar').text(formatIDR(totalKeluar));
            $('#dash_body_keluar').html(htmlKeluar || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
        });
    }

    loadDashboard();
});
</script>
@endsection
