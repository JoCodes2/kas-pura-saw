@extends('Layouts.Base')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"><i class="fas fa-list-alt pr-2"></i>Dashboard</h4>
        </div>

        <!-- Card Section -->
        <div class="row">
            <!-- Card Total -->
            <div class="col-sm-4">
                <div class="card text-white mb-3 card-hover" style="background-color: #0d47a1;">
                    <div class="card-body">
                        <h5 class="card-title">Total</h5>
                        <p class="card-text">Jumlah Total Tailor</p>
                        <h3 class="card-text" id="tailor-count">123</h3>
                        <!-- Ganti dengan data dinamis jika diperlukan -->
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center row">
                <div class="col-sm-7">
                    <div class="card-body ">
                        <h1 class=" text-primary fw-bold">Selamat Datang! 🎉 <span> @auth
                                {{ auth()->user()->name }}
                            @endauth</span></h1>
                        <br>
                        <h4 class="mb-5">GeoTailor PALU, Sistem Informasi Geografis Tailor DiKota Palu.</h4>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-start">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('Image/loginn.png') }}" class="img-fluid" alt="View Badge User"

                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png">
                    </div>
                </div>
            </div>


        </div>
        <!-- End of Card Section -->
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '/count-dashboard', // Ganti dengan endpoint yang benar
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#tailor-count').text(response.tailor_count);

                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    </script>
@endsection
