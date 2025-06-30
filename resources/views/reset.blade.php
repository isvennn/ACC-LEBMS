<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/acclogo.png') }}">
    <title>Reset Password | {{ env('APP_NAME') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            background: url('{{ asset('dist/img/background.png') }}') no-repeat center center fixed;
            background-size: cover;
        }

        .form-container-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 60px;
        }

        .wrapper {
            overflow: hidden;
            max-width: 390px;
            width: 100%;
            background: #05b0c0;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            min-height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wrapper .title {
            font-size: 35px;
            font-weight: 600;
            text-align: center;
            color: #fff;
        }

        .form-container .form-inner form .field {
            height: 50px;
            width: 100%;
            margin-top: 20px;
        }

        .form-inner form .field input {
            height: 100%;
            width: 100%;
            outline: none;
            padding-left: 15px;
            border-radius: 5px;
            border: 1px solid lightgrey;
            border-bottom-width: 2px;
            font-size: 17px;
            transition: all 0.3s ease;
        }

        .form-inner form .field input:focus {
            border-color: #fc83bb;
        }

        .form-inner form .field input::placeholder {
            color: #999;
            transition: all 0.3s ease;
        }

        form .field input:focus::placeholder {
            color: #b3b3b3;
        }

        .form-links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .form-links a {
            color: rgb(224, 227, 239);
            text-decoration: none;
            font-weight: bold;
        }

        .form-links a:hover {
            color: #d71c1c;
        }

        form .btn {
            height: 50px;
            width: 100%;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        form .btn .btn-layer {
            height: 100%;
            width: 300%;
            position: absolute;
            left: -100%;
            background: -webkit-linear-gradient(right, #45b27a, #d2acbe, #45b27a, #d2acbe);
            border-radius: 5px;
            transition: all 0.4s ease;
        }

        form .btn:hover .btn-layer {
            left: 0;
        }

        form .btn input[type="submit"] {
            height: 100%;
            width: 100%;
            z-index: 1;
            position: relative;
            background: none;
            border: none;
            color: #fff;
            padding-left: 0;
            border-radius: 5px;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
        }

        #response-msg {
            margin-top: 15px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="form-container-wrapper">
        <div class="wrapper">
            <div class="title">Reset Password</div>
            <div class="form-container">
                <div class="form-inner">
                    <form id="resetPasswordForm" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">
                        <div id="response-msg"></div>
                        <div class="field">
                            <input type="password" name="password" id="password" placeholder="Enter new password"
                                required>
                        </div>
                        <div class="field">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Confirm new password" required>
                        </div>
                        <div class="field btn">
                            <div class="btn-layer"></div>
                            <input type="submit" value="Reset Password">
                        </div>
                        <div class="form-links">
                            <span><a href="{{ route('loginPage') }}">Back to Login</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#resetPasswordForm').submit(function(event) {
                event.preventDefault();
                let submitBtn = $('input[type="submit"]');
                submitBtn.prop('disabled', true).val('Processing...');

                $('#response-msg').empty();

                $.ajax({
                    method: 'POST',
                    url: '{{ route('password.update') }}',
                    data: $(this).serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        $('#response-msg').html('<div class="alert alert-success">' + response
                            .message + '</div>');
                        submitBtn.prop('disabled', false).val('Reset Password');
                        setTimeout(() => window.location.href = '/', 2000);
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).val('Reset Password');
                        let message;
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        } else {
                            message = xhr.responseJSON?.message ||
                                'Something went wrong. Please try again.';
                        }
                        $('#response-msg').html('<div class="alert alert-danger">' + message +
                            '</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>
