<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $actionType === 'change-password' ? 'Confirm Password Change' : 'Reset Your Password' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1e3a8a;
            margin: 0;
            font-size: 24px;
        }
        .content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .content p {
            margin: 15px 0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            background-color: #1e3a8a;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #1e40af;
        }
        .token-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
            color: #666;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                @if($actionType === 'change-password')
                    Confirm Password Change
                @else
                    Reset Your Password
                @endif
            </h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            @if($actionType === 'change-password')
                <p>You requested to change your account password. Click the button below to create a new password:</p>
            @else
                <p>You requested a password reset. Click the button below to create a new password:</p>
            @endif

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>

            <p>Or copy and paste this link in your browser:</p>
            <div class="token-box">
                {{ $resetUrl }}
            </div>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> This link will expire in 1 hour for security reasons. If you didn't request this password reset, please ignore this email and your password will remain unchanged.
            </div>

            <p>If you have any issues, please contact our support team.</p>
            <p>Best regards,<br><strong>Titans Crest Team</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Titans Crest. All rights reserved.</p>
            <p>This is an automated email. Please do not reply directly.</p>
        </div>
    </div>
</body>
</html>
