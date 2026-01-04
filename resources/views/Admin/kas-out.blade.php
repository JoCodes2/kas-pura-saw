@extends('Layouts.Base')
@section('title', 'Kas Keluar')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"><i class="fas fa-sign-out-alt pr-2"></i>Riwayat Kas Keluar</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-round shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tableKasOut" class="display table table-hover">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Tanggal</th>
                                        <th>Kegiatan</th>
                                        <th>Kas Sumber</th>
                                        <th class="text-right">Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="tBody">
                                    </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="4" class="text-right font-weight-bold">TOTAL PENGELUARAN :</th>
                                        <th class="text-right font-weight-bold text-danger" id="totalKasKeluar" style="font-size: 1.1rem;">Rp 0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        };

        function loadKasOut() {
            $('#tBody').html('<tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>');

            $.ajax({
                url: '/saw/master/kas-out',
                method: 'GET',
                success: function(res) {
                    let html = '';
                    let total = 0;

                    if (res.data && res.data.length > 0) {
                        res.data.forEach((item, index) => {
                            let jumlah = parseFloat(item.jumlah);
                            total += jumlah;

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><span class="badge badge-count">${item.tanggal}</span></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">${item.kegiatan ? item.kegiatan.nama_kegiatan : '-'}</span>
                                            <small class="text-muted">Pengaju: ${item.kegiatan ? item.kegiatan.nama_pengaju : '-'}</small>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-university text-primary mr-1"></i> ${item.kas ? item.kas.nama_kas : '-'}</td>
                                    <td class="text-right font-weight-bold text-danger">
                                        ${formatRupiah(jumlah)}
                                    </td>
                                    <td><small class="text-muted">${item.keterangan || '-'}</small></td>
                                </tr>
                            `;
                        });

                        $('#totalKasKeluar').text(formatRupiah(total));
                        $('#tBody').html(html);

                        // Hancurkan DataTable lama jika ada sebelum inisialisasi ulang
                        if ($.fn.DataTable.isDataTable('#tableKasOut')) {
                            $('#tableKasOut').DataTable().destroy();
                        }

                        $('#tableKasOut').DataTable({
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                            }
                        });
                    } else {
                        $('#tBody').html('<tr><td colspan="6" class="text-center py-5">Belum ada data kas keluar.</td></tr>');
                        $('#totalKasKeluar').text(formatRupiah(0));
                    }
                },
                error: function() {
                    $('#tBody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>');
                }
            });
        }

        loadKasOut();
    });
</script>
@endsection
