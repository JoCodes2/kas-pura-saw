<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title') | Kas Pura - Manajemen Kas & Pendukung Keputusan SAW</title>
    <meta name="description" content="Aplikasi Kas Pura: Solusi manajemen kas masuk, kas keluar, dan sistem pendukung keputusan kegiatan menggunakan metode SAW.">
    <meta name="keywords" content="Kas Pura, Manajemen Kas, Sistem Pendukung Keputusan, SAW, Kas Pura App">
    <meta name="author" content="Kas Pura Team">
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

    {{-- Logo / Favicon --}}
    <link rel="icon" href="{{ asset('assets/img/LOGO.jpeg') }}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ asset('assets/img/LOGO.jpeg') }}">

    {{-- Styles --}}
    @include('Layouts.styles')

    <script>
        let appUrl = '{{ env('APP_URL') }}';
    </script>
</head>
<body>
    <div class="wrapper">
        {{-- navbar --}}
        @include('Layouts.Navbar')
        {{-- end navbar --}}
        <!-- Sidebar -->
        @include('Layouts.Sidebar')
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="content">
                @yield('content')
            </div>
            @include('Layouts.Footer')
        </div>
    </div>
    @include('Layouts.scripts')
    @yield('script')

</body>

</html>
