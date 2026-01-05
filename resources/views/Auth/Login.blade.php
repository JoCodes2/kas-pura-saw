<!DOCTYPE html>
<html lang="en">

<head>
    <title>SAW/LOGIN</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        *,
        *:before,
        *:after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #080710;
        }

        .background {
            width: 430px;
            height: 520px;
            position: absolute;
            transform: translate(-50%, -50%);
            left: 50%;
            top: 50%;
        }

        .background .shape {
            height: 200px;
            width: 200px;
            position: absolute;
            border-radius: 50%;
        }

        .shape:first-child {
            background: linear-gradient(#1845ad, #23a2f6);
            left: -80px;
            top: -80px;
        }

        .shape:last-child {
            background: linear-gradient(to right, #ff512f, #f09819);
            right: -30px;
            bottom: -80px;
        }

        form {
            height: auto;
            width: 400px;
            background-color: rgba(255, 255, 255, 0.13);
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 50px 35px;
            text-align: center;
        }

        form h3 {
            font-size: 32px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            font-size: 16px;
            font-weight: 500;
            margin-top: 20px;
        }

        input {
            width: 100%;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
            padding: 0 10px;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 300;
            color: #fff;
        }

        ::placeholder {
            color: #e5e5e5;
        }

        button {
            margin-top: 30px;
            width: 100%;
            background-color: #ffffff;
            color: #080710;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
        }

        .social-icons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid white;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
        }

        .text-danger {
            color: rgb(255, 5, 5) !important;
        }

        #email-error,
        #password-error {
            color: rgb(250, 9, 9) !important;
        }
    </style>
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <form id="formAuthentication" class="mb-3" method="POST" action="{{ url('saw/login') }}">
        <h3>Login Here</h3>

        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email </label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                autofocus="">
            <small id="email-error" class="text-danger"></small>
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
            </div>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="············"
                    aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            <small id="password-error" class="text-danger"></small>
        </div>
        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
        </div>

        <div class="social-icons">
            <a href="#"><img src="{{ asset('assets/img/LOGO.jpeg') }}" alt="Logo"></a>
            <a href="#"><img src="{{ asset('assets/img/20241107_171817.jpg') }}" alt="Logo"></a>
            <a href="#"><img src="{{ asset('assets/img/adiguna.png') }}" alt="Logo"></a>

        </div>
    </form>
    {{-- <script src="{{ asset('assets/assets/vendor/libs/jquery/jquery.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        function successAlert(message) {
            Swal.fire({
                title: 'Berhasil!',
                text: message,
                icon: 'success',
                showConfirmButton: false,
                timer: 1000,
            });
        }

        function errorAlert(message) {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                showConfirmButton: false,
                timer: 1500,
            });
        }

        function loadingAlert() {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#formAuthentication').on('submit', function(e) {
                e.preventDefault();

                loadingAlert();

                $.ajax({
                    type: 'POST',
                    url: "{{ url('saw/login') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    dataType: 'json',

                    success: function(response) {
                        Swal.close();
                        successAlert('Login berhasil');
                        window.location.href = '/master';
                    },

                    error: function(xhr) {
                        Swal.close();
                        errorAlert(xhr.responseJSON?.message ?? 'Email atau password salah');
                    }
                });
            });
        });
    </script>


</body>

</html>
