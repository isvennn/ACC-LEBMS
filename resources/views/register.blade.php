<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Abuyog Community College Equipment Borrowing System">
    <meta name="keywords" content="Abuyog Community College, Equipment Borrowing, Borrower Registration">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration | {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('dist/img/acclogo.png') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- SweetAlert2 -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@5/bootstrap-4.min.css">
    <!-- AdminLTE Theme -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <style>
        body {
            background: url('{{ asset('dist/img/acc_campus.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-box {
            width: 100%;
            max-width: 900px;
            margin: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(90deg, #28a745, #218838);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: none;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
            border-color: #28a745;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 10px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .text-maroon {
            color: #800000;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .register-box {
                margin: 10px;
                max-width: 95%;
            }

            .card-body {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .card-header h3 {
                font-size: 1.5rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .btn-success {
                padding: 8px;
            }
        }
    </style>
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <h3 class="text-bold">BORROWER REGISTRATION</h3>
            </div>
            <div class="card-body">
                <form id="registerForm">
                    <div id="response-msg"></div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="middle_name" name="middle_name">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="extension_name">Extension Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="extension_name"
                                        name="extension_name">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="contact_no">Contact Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="contact_no" name="contact_no"
                                        data-inputmask="'mask': '(+63) 999-999-9999'" placeholder="(+63) 999-999-9999"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="username" name="username"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-block">Register</button>
                        </div>
                    </div>
                </form>
                <div class="row mt-3">
                    <div class="col-12 text-right">
                        <p>Already have an account? <a href="{{ route('loginPage') }}" class="text-maroon">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jQuery Validation -->
    <script src="{{ asset('plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- InputMask --><script src="https://cdn.jsdelivr.net/npm/inputmask@5/dist/jquery.inputmask.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script>
        function showSuccessMessage(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: msg,
                timer: 3000,
                showConfirmButton: false
            });
        }

        function showErrorMessage(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg,
                confirmButtonColor: '#d33'
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize InputMask
            $('[data-inputmask]').inputmask();

            $('#registerForm').validate({
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 30
                    },
                    middle_name: {
                        maxlength: 30
                    },
                    last_name: {
                        required: true,
                        maxlength: 30
                    },
                    extension_name: {
                        maxlength: 5
                    },
                    contact_no: {
                        required: true,
                        maxlength: 20,
                        remote: {
                            url: "{{ route('checkContact') }}",
                            type: "POST",
                            data: {
                                contact_no: function() {
                                    return $('#contact_no').val();
                                }
                            }
                        }
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255,
                        remote: {
                            url: "{{ route('checkEmail') }}",
                            type: "POST",
                            data: {
                                email: function() {
                                    return $('#email').val();
                                }
                            }
                        }
                    },
                    username: {
                        required: true,
                        minlength: 8,
                        maxlength: 255,
                        remote: {
                            url: "{{ route('checkUsername') }}",
                            type: "POST",
                            data: {
                                username: function() {
                                    return $('#username').val();
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        regex: /^(?=.*[A-Z])(?=.*[0-9]).+$/
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    first_name: {
                        required: "First Name is required.",
                        maxlength: "First Name cannot exceed 30 characters."
                    },
                    middle_name: {
                        maxlength: "Middle Name cannot exceed 30 characters."
                    },
                    last_name: {
                        required: "Last Name is required.",
                        maxlength: "Last Name cannot exceed 30 characters."
                    },
                    extension_name: {
                        maxlength: "Extension Name cannot exceed 5 characters."
                    },
                    contact_no: {
                        required: "Contact Number is required.",
                        remote: "This contact number is already taken."
                    },
                    email: {
                        required: "Email is required.",
                        email: "Please enter a valid email address.",
                        remote: "This email is already taken."
                    },
                    username: {
                        required: "Username is required.",
                        minlength: "Username must be at least 8 characters long.",
                        remote: "This username is already taken."
                    },
                    password: {
                        required: "Password is required.",
                        minlength: "Password must be at least 8 characters long.",
                        regex: "Password must contain at least one uppercase letter and one number."
                    },
                    password_confirmation: {
                        required: "Please confirm your password.",
                        equalTo: "Passwords do not match."
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Custom regex method for password
            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || new RegExp(regexp).test(value);
            });

            $('#registerForm').submit(function(event) {
                event.preventDefault();
                const $submitButton = $(this).find('button[type=submit]');
                $submitButton.prop('disabled', true);

                if ($(this).valid()) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Please confirm your registration details.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Proceed',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                method: 'POST',
                                url: '{{ route('registerPage') }}',
                                data: $(this).serialize(),
                                dataType: 'JSON',
                                success: function(response) {
                                    if (response.valid) {
                                        showSuccessMessage(response.msg);
                                        setTimeout(() => {
                                            window.location.href = response
                                                .redirect;
                                        }, 1500);
                                    } else {
                                        showErrorMessage(response.msg);
                                    }
                                },
                                error: function(jqXHR) {
                                    let errorMsg = jqXHR.responseJSON?.msg ||
                                        'An error occurred.';
                                    if (jqXHR.responseJSON?.errors) {
                                        errorMsg += '\n' + Object.values(jqXHR
                                            .responseJSON.errors).flat().join('\n');
                                    }
                                    showErrorMessage(errorMsg);
                                },
                                complete: function() {
                                    $submitButton.prop('disabled', false);
                                }
                            });
                        } else {
                            $submitButton.prop('disabled', false);
                        }
                    });
                } else {
                    $submitButton.prop('disabled', false);
                }
            });
        });
    </script>
</body>

</html>
