<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: -webkit-linear-gradient(right, #45b27a, #d2acbe);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
        }

        .button:hover {
            background: -webkit-linear-gradient(left, #45b27a, #d2acbe);
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="{{ url('dist/img/acclogo.png') }}" alt="ACC Logo" style="width: 100px;">
            <h2>ACC - Laboratory Password Reset</h2>
        </div>

        <p>Dear {{ $user->full_name }},</p>

        <p>We received a request to reset your password. Click the button below to reset your password:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>

        <p>If you did not request a password reset, please ignore this email or contact our support team.</p>

        <p>This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.</p>

        <p>Best regards,<br>ACC Laboratory Team</p>

        <div class="footer">
            <p>This is an automated email, please do not reply directly to this message.</p>
        </div>
    </div>
</body>

</html>
