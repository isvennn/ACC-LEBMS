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

    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background: url('{{ asset('dist/img/acc_campus.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .register-box {
            width: 100%;
            max-width: 960px;
            margin: 40px 15px;
            padding: 30px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #1e7e34, #28a745);
            color: white;
            padding: 25px;
            text-align: center;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        .card-body {
            padding: 40px;
        }

        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-right: none;
            color: #495057;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.2);
        }

        .btn-primary {
            background-color: #28a745;
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-toggle-password {
            cursor: pointer;
            background: none;
            border: none;
            padding: 0 10px;
            color: #495057;
        }

        .btn-toggle-password:hover {
            color: #28a745;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .text-maroon {
            color: #800000;
            font-weight: 600;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #1e7e34, #28a745);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .modal-title {
            font-weight: 600;
        }

        #previewImage {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }

        @media (max-width: 768px) {
            .register-box {
                margin: 20px;
                max-width: 90%;
            }

            .card-body {
                padding: 25px;
            }

            .card-header h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-header h3 {
                font-size: 1.3rem;
            }

            .form-group label {
                font-size: 0.85rem;
            }

            .btn-primary,
            .btn-secondary {
                font-size: 0.95rem;
                padding: 10px;
            }
        }
    </style>
</head>

<body class="hold-transition register-page">
    <div class="container py-5">
        <div class="register-box mx-auto">
            <div class="card card-outline card-success">
                <div class="card-header text-center">
                    <h3 class="text-bold">BORROWER REGISTRATION</h3>
                </div>
                <div class="card-body">
                    <form id="registerForm" enctype="multipart/form-data">
                        <div id="response-msg"></div>
                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            required pattern="^[A-Za-z\s\-]+$"
                                            title="First name must contain only letters, spaces, or hyphens.">
                                    </div>
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                                            pattern="^[A-Za-z\s\-]+$"
                                            title="Middle name must contain only letters, spaces, or hyphens.">
                                    </div>
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            required pattern="^[A-Za-z\s\-]+$"
                                            title="Last name must contain only letters, spaces, or hyphens.">
                                    </div>
                                </div>
                            </div>

                            <!-- Extension Name -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="extension_name">Extension Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="extension_name"
                                            name="extension_name" pattern="^[A-Za-z]+$"
                                            title="Extension name must contain only letters (e.g., Jr, Sr, II).">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="contact_no">Contact Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                            placeholder="(+63) 999-999-9999"
                                            data-inputmask="'mask': '(+63) 999-999-9999'" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Course -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="course">Course <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="fas fa-graduation-cap"></i></span>
                                        </div>
                                        <select name="course" id="course" class="form-control" required>
                                            <option value="" selected disabled>-- Select Course --</option>
                                            <option value="BSIT">BSIT</option>
                                            <option value="BSED">BSED</option>
                                            <option value="BEED">BEED</option>
                                            <option value="BSCRIM">BSCRIM</option>
                                            <option value="BSHM">BSHM</option>
                                            <option value="BSENTREP">BSENTREP</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="username" name="username"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn-toggle-password"
                                                data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn-toggle-password"
                                                data-target="password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-primary w-100"
                                    id="validateForm">Register</button>
                            </div>
                        </div>
                    </form>

                    <!-- Link to Login -->
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p>Already have an account?
                                <a href="{{ route('loginPage') }}" class="text-maroon font-weight-bold">Sign In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID Upload Modal -->
    <div class="modal fade" id="idUploadModal" tabindex="-1" role="dialog" aria-labelledby="idUploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="idUploadModalLabel">Upload School ID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="school_id_image">Upload your School ID <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="school_id_image" name="school_id_image"
                            accept="image/*" required>
                        <img id="previewImage" src="#" alt="ID Preview" class="img-fluid mt-3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitRegistration">Submit
                        Registration</button>
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
    <!-- InputMask -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5/dist/jquery.inputmask.min.js"></script>
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

            // Show/Hide Password
            $('.btn-toggle-password').on('click', function() {
                const targetId = $(this).data('target');
                const input = $('#' + targetId);
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Image preview
            $('#school_id_image').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#previewImage').hide();
                }
            });

            // Define custom regex method for jQuery Validation
            $.validator.addMethod('regex', function(value, element, param) {
                return this.optional(element) || param.test(value);
            }, 'Please enter a value matching the pattern.');

            // Form validation
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
                    course: {
                        required: true
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
                    course: {
                        required: "Course is required."
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

            // Validate form before showing modal
            $('#validateForm').on('click', function() {
                const $submitButton = $(this);
                $submitButton.prop('disabled', true);

                if ($('#registerForm').valid()) {
                    $('#idUploadModal').modal('show');
                } else {
                    showErrorMessage('Please fill out all required fields correctly.');
                }
                $submitButton.prop('disabled', false);
            });

            // Modal submission
            $('#submitRegistration').on('click', function() {
                if ($('#school_id_image').val() === '') {
                    showErrorMessage('Please upload your School ID.');
                    return;
                }

                const formData = new FormData($('#registerForm')[0]);
                formData.append('school_id_image', $('#school_id_image')[0].files[0]);

                $.ajax({
                    method: 'POST',
                    url: '{{ route('register') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.valid) {
                            $('#idUploadModal').modal('hide');
                            showSuccessMessage(response.msg);
                            setTimeout(() => {
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            showErrorMessage(response.msg);
                        }
                    },
                    error: function(jqXHR) {
                        let errorMsg = jqXHR.responseJSON?.msg || 'An error occurred.';
                        if (jqXHR.responseJSON?.errors) {
                            errorMsg += '\n' + Object.values(jqXHR.responseJSON.errors).flat()
                                .join('\n');
                        }
                        showErrorMessage(errorMsg);
                    }
                });
            });
        });
    </script>
</body>

</html>
