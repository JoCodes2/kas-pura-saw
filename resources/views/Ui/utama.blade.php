<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pura Agung Wana Kertha Jagatnatha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <nav class="sticky top-0 z-40 w-full glass-effect border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 md:py-4 flex justify-between items-center">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-white p-1 rounded-lg shadow-sm border border-slate-100 flex-shrink-0">
                    <img src="{{ asset('assets/img/LOGO.JPEG') }}" alt="Logo Pura"
                        class="h-8 w-8 md:h-10 md:w-10 object-contain">
                </div>
                <span class="font-bold text-sm sm:text-lg md:text-xl tracking-tight text-emerald-800 leading-tight">
                    Pura Agung <span class="block sm:inline">Wana Kertha Jagatnatha</span>
                </span>
            </div>
        </div>
    </nav>

    <header class="relative bg-emerald-900 py-12 md:py-20 px-4 md:px-6 overflow-hidden">
        <div
            class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 md:w-96 md:h-96 bg-emerald-700 rounded-full opacity-20 blur-3xl">
        </div>

        <div class="max-w-7xl mx-auto relative z-10 grid md:grid-cols-2 gap-8 md:gap-12 items-center">
            <div class="text-white text-center md:text-left">
                <span
                    class="bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-xs md:text-sm font-semibold uppercase tracking-wider">
                    Pusat Spiritual & Sosial
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold mt-4 leading-tight">
                    Pura Agung <br class="hidden md:block">
                    <span class="text-emerald-400">Wana Kertha Jagatnatha</span>
                </h1>
                <p class="mt-4 md:mt-6 text-emerald-100 text-sm md:text-lg leading-relaxed opacity-90">
                    Mewujudkan kerukunan dan kedamaian melalui pelayanan umat, manajemen kegiatan yang transparan, dan
                    pelestarian adat budaya Bali.
                </p>
                <div class="mt-8 md:mt-10 flex flex-col sm:flex-row justify-center md:justify-start gap-4">
                    <button onclick="openModal()"
                        class="w-full sm:w-auto px-8 py-4 bg-white text-emerald-800 font-bold rounded-xl shadow-xl hover:bg-emerald-50 transition transform hover:-translate-y-1 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i> Ajukan Kegiatan
                    </button>
                </div>
            </div>

            <div class="hidden md:block">
                <div class="bg-white/10 p-4 rounded-3xl backdrop-blur-sm border border-white/20">
                    <div class="rounded-2xl overflow-hidden shadow-2xl bg-white flex items-center justify-center">
                        <img src="{{ asset('assets/img/pura.png') }}" alt="Ilustrasi Pura"
                            class="w-full h-64 md:h-80 object-contain p-4">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div id="upsertDataModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-2 sm:p-4">
        <div
            class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[95vh]">

            <div
                class="flex justify-between items-center bg-emerald-700 text-white px-5 py-4 md:px-8 md:py-5 flex-shrink-0">
                <div>
                    <h3 class="font-bold text-lg md:text-xl leading-tight">Formulir Kegiatan</h3>
                    <p class="text-emerald-100 text-[10px] md:text-xs mt-1">Lengkapi data untuk pengajuan sistem</p>
                </div>
                <button onclick="closeModal()"
                    class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full hover:bg-emerald-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-5 md:p-8 overflow-y-auto overflow-x-hidden">
                <form id="upsertDataForm" enctype="multipart/form-data" class="space-y-4 md:space-y-5">
                    <input type="hidden" id="id" name="id" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        <div class="space-y-1 md:space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Nama Pengaju</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                    <i class="fas fa-user text-xs md:text-sm"></i>
                                </span>
                                <input type="text" name="nama_pengaju" id="nama_pengaju"
                                    placeholder="Contoh: Wayan Sudira"
                                    class="w-full pl-9 pr-4 py-2 md:py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                            </div>
                            <small class="text-red-500 text-[10px] text-danger" id="nama_pengaju-error"></small>
                        </div>

                        <div class="space-y-1 md:space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                    <i class="fab fa-whatsapp text-xs md:text-sm"></i>
                                </span>
                                <input type="text" name="no_hp" id="no_hp" placeholder="08123xxx"
                                    class="w-full pl-9 pr-4 py-2 md:py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                            </div>
                            <small class="text-red-500 text-[10px] text-danger" id="no_hp-error"></small>
                        </div>
                    </div>

                    <div class="space-y-1 md:space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Judul Kegiatan</label>
                        <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                            placeholder="Nama upacara/kegiatan"
                            class="w-full px-4 py-2 md:py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        <small class="text-red-500 text-[10px] text-danger" id="nama_kegiatan-error"></small>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        <div class="space-y-1 md:space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan"
                                class="w-full px-4 py-2 md:py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none transition text-slate-600">
                            <small class="text-red-500 text-[10px] text-danger" id="tanggal_kegiatan-error"></small>
                        </div>

                        <div class="space-y-1 md:space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Estimasi Anggaran</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 font-bold text-xs md:text-sm">Rp</span>
                                <input type="text" id="estimasi_biaya" placeholder="0"
                                    class="w-full pl-9 md:pl-10 pr-4 py-2 md:py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                            </div>
                            <small class="text-red-500 text-[10px] text-danger" id="estimasi_biaya-error"></small>
                        </div>
                    </div>

                    <div class="space-y-1 md:space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Dokumen Proposal (PDF)</label>
                        <input type="file" name="file_proposal" id="file_proposal" accept=".pdf"
                            class="w-full px-3 py-2 text-xs md:text-sm border-2 border-dashed border-slate-200 rounded-xl cursor-pointer">
                        <small class="text-red-500 text-[10px] text-danger" id="file_proposal-error"></small>
                    </div>
                </form>
            </div>

            <div
                class="flex justify-end gap-2 md:gap-3 px-5 py-4 md:px-8 md:py-5 bg-slate-50 border-t border-slate-100 flex-shrink-0">
                <button onclick="closeModal()"
                    class="px-4 md:px-6 py-2 md:py-2.5 text-sm rounded-xl font-semibold text-slate-600 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="button" id="simpanData"
                    class="px-5 md:px-8 py-2 md:py-2.5 text-sm rounded-xl font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg transition">
                    Kirim
                </button>
            </div>
        </div>
    </div>

    <script>
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        $('#estimasi_biaya').on('keyup', function() {
            this.value = formatRupiah(this.value);
        });

        // Modal Logic
        function openModal() {
            $('#upsertDataModal').removeClass('hidden').addClass('flex');
            $('body').css('overflow', 'hidden');
        }

        function closeModal() {
            $('#upsertDataModal').addClass('hidden').removeClass('flex');
            $('body').css('overflow', 'auto');
            $('#upsertDataForm')[0].reset();
            $('.text-danger').text('');
        }

        $('#estimasi_biaya').on('blur', function() {
            if (this.value === '') {
                this.value = '0';
            }
        });


        // Alert Helpers
        function loadingAllert() {
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        }

        function successAlert() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data telah disimpan.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function errorAlert() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan pada server.',
            });
        }

        function reloadBrowsers() {
            setTimeout(() => {
                location.reload();
            }, 1500);
        }

        // Core AJAX Function
        $(document).on('click', '#simpanData', function(e) {
            $('.text-danger').text('');
            e.preventDefault();

            let id = $('#id').val();
            let formData = new FormData($('#upsertDataForm')[0]);

            // Bersihkan format rupiah ke angka murni
            let estimasi_biaya = $('#estimasi_biaya').val().replace(/[^0-9]/g, '');
            formData.set('estimasi_biaya', estimasi_biaya);

            let url = id ? `/saw/kegiatan/update/${id}` : '/saw/kegiatan/create';
            let method = 'POST'; // Biasanya Laravel/Backend butuh POST untuk update dengan FormData

            loadingAllert();

            $.ajax({
                type: method,
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.close();
                    if (response.code === 422) {
                        let errors = response.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else if (response.code === 200) {
                        successAlert();
                        closeModal();
                        reloadBrowsers();
                    } else {
                        errorAlert();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.close();
                    errorAlert();
                }
            });
        });
    </script>
</body>

</html>
