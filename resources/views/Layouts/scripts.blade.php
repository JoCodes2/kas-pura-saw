<!-- Fonts and icons -->
<script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
<script>
    WebFont.load({
        google: {
            "families": ["Lato:300,400,700,900"]
        },
        custom: {
            "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands",
                "simple-line-icons"
            ],
            urls: ['assets/css/fonts.min.css']
        },
        active: function() {
            sessionStorage.fonts = true;
        }
    });
</script>

{{-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script> --}}


<!--   Core JS Files   -->
<script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- jQuery UI -->
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>


<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets/js/plugin/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jqvmap/maps/jquery.vmap.world.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Atlantis JS -->
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>

<!-- Atlantis DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets/js/setting-demo.js') }}"></script>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Axios untuk reverse geocoding -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>

<script>
    moment.locale('id');
</script>
<script>
    const urlLogout = 'saw/logout'
    $(document).ready(function() {
        $('#iconLogout').click(function(e) {
            Swal.fire({
                title: '<span style="font-size: 22px"> Konfirmasi!</span>',
                text: 'Anda yakin?',
                showCancelButton: true,
                showConfirmButton: true,
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya',
                reverseButtons: true,
                confirmButtonColor: '#48ABF7',
                cancelButtonColor: '#EFEFEF',
                cancelButtonText: 'Tidak',
                customClass: {
                    cancelButton: 'text-dark'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('saw/logout') }}",

                        method: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log(response);
                            window.location.href = '/login';
                        },
                        error: function(xhr, status, error) {
                            alert('Error: Failed to logout. Please try again.');
                        }
                    });
                }
            });
        });
    });
</script>
