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
    <title>Login | {{ env('APP_NAME') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
    html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100vh;
    background: url('dist/img/background.png') no-repeat center center fixed;
    background-size: cover;
}

        .form-container-wrapper {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: flex-end; /* aligns to the right */
    align-items: center;       /* centers vertically */
    padding-right: 60px;       /* adjust distance from right edge */
}
.wrapper {
    overflow: hidden;
    max-width: 390px;
    width: 100%;
    background: #05b0c0;
    padding: 30px;
    border-radius: 5px;
    box-shadow: 0px 15px 20px rgba(0,0,0,0.1);
    min-height: 450px; /* ✅ sets a nice minimum height */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

       ::selection{
            background: #fa4299;
            color: #fff;
        }
        .wrapper{
            overflow: hidden;
            max-width: 390px;
            background: #05b0c0;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 15px 20px rgba(0,0,0,0.1);
        }
        .wrapper .title-text{
            display: flex;
            width: 200%;
        }
        .wrapper .title{
            width: 50%;
            font-size: 35px;
            font-weight: 600;
            text-align: center;
            transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
        }
        .wrapper .slide-controls{
            position: relative;
            display: flex;
            height: 50px;
            width: 100%;
            overflow: hidden;
            margin: 30px 0 10px 0;
            justify-content: space-between;
            border: 1px solid lightgrey;
            border-radius: 5px;
        }
        .slide-controls .slide{
            height: 100%;
            width: 100%;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            text-align: center;
            line-height: 48px;
            cursor: pointer;
            z-index: 1;
            transition: all 0.6s ease;
        }
        .slide-controls label.signup{
            color: #000;
        }
        .slide-controls .slider-tab{
            position: absolute;
            height: 100%;
            width: 50%;
            left: 0;
            z-index: 0;
            border-radius: 5px;
            background: -webkit-linear-gradient(left, #45b27a, #d2acbe);
            transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
        }
        input[type="radio"]{
            display: none;
        }
        #signup:checked ~ .slider-tab{
            left: 50%;
        }
        #signup:checked ~ label.signup{
            color: #fff;
            cursor: default;
            user-select: none;
        }
        #signup:checked ~ label.login{
            color: #000;
        }
        #login:checked ~ label.signup{
            color: #000;
        }
        #login:checked ~ label.login{
            cursor: default;
            user-select: none;
        }
        .wrapper .form-container{
            width: 100%;
            overflow: hidden;
        }
        .form-container .form-inner{
            display: flex;
            width: 200%;
        }
        .form-container .form-inner form{
            width: 50%;
            transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
        }
        .form-inner form .field{
            height: 50px;
            width: 100%;
            margin-top: 20px;
        }
        .form-inner form .field input{
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
        .form-inner form .field input:focus{
            border-color: #fc83bb;
        /* box-shadow: inset 0 0 3px #fb6aae; */
        }
        .form-inner form .field input::placeholder{
            color: #999;
            transition: all 0.3s ease;
        }
        form .field input:focus::placeholder{
            color: #b3b3b3;
        }
        .form-inner form .pass-link{
            margin-top: 5px;
        }
        .form-inner form .signup-link{
            text-align: center;
            margin-top: 30px;
        }
        .form-inner form .pass-link a,
        .form-inner form .signup-link a{
            color: #fa4299;
            text-decoration: none;
        }
        .form-inner form .pass-link a:hover,
        .form-inner form .signup-link a:hover{
            text-decoration: underline;
        }
        form .btn{
            height: 50px;
            width: 100%;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        form .btn .btn-layer{
            height: 100%;
            width: 300%;
            position: absolute;
            left: -100%;
            background: -webkit-linear-gradient(right, #45b27a, #d2acbe, #45b27a, #d2acbe);
            border-radius: 5px;
            transition: all 0.4s ease;;
        }
        form .btn:hover .btn-layer{
            left: 0;
        }
        form .btn input[type="submit"]{
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
        /* Links styling */
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
     

        /* Media query for responsiveness */
        @media (max-width: 768px) {
            .left-content h1 {
                font-size: 2em;
            }

            .left-content p {
                font-size: 1em;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <!-- Main Section -->
    <div class="form-container-wrapper">
    <div class="wrapper">
         <div class="title-text">
            <div class="title login">
               Login Form
            </div>
         </div>
         <div class="form-container">
            <div class="form-inner">
                <form id="loginForm">
                <div id="response-msg"></div>
                   <div class="form-outline mb-2">
                    <label class="form-label" for="login">Email or Username <span
                            class="text-danger">*</span></label>
                    <input type="login" name="login" id="form1Example13" class="form-control form-control-lg" />
                  </div>

                <!-- Password input -->
                <div class="form-outline mb-2">
                    <label class="form-label" for="form1Example23">Password</label>
                    <input type="password" name="password" id="form1Example23" class="form-control form-control-lg" />
                </div>
                  <div class="field btn">
                     <div class="btn-layer"></div>
                     <input type="submit" value="Login">
                  </div>
                   <div class="mt-3 text-center">
                    <p>Don't have an account yet?</p>
                </div>
                <div class="form-links">
                    <span><a href="/register">Register</a></span>
                    <!-- <span><a href="#">Forgot password</a></span> -->
                </div>
               </form>
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

            $('#loginForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting right away
                $.ajax({
                    method: 'POST',
                    url: '{{ route('login') }}',
                    data: $('#loginForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            window.location.reload();
                        } else {
                            $('#response-msg').html('<div class="alert alert-danger">' +
                                response.msg + '</div>');
                        }
                    },
                    error: function(xhr, textStatus, error) {
                        $('#response-msg').html(
                            '<div class="alert alert-danger">Invalid credentials! Please try again.</div>'
                        );
                    }
                });
            });
        });

        
    </script>
</body>

</html>
