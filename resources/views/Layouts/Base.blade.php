<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kas Pura</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
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
