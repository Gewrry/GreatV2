<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f7f6; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #0d9488; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 40px; text-align: center; }
        .code-container { background-color: #f0fdfa; border: 2px dashed #0d9488; border-radius: 8px; padding: 20px; margin: 30px 0; display: inline-block; }
        .code { font-size: 36px; font-weight: 800; color: #0d9488; letter-spacing: 10px; margin: 0; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
        p { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $client->first_name }}</strong>,</p>
            <p>Thank you for registering with the BPLS Online Portal. To complete your registration, please use the following verification code:</p>
            
            <div class="code-container">
                <h2 class="code">{{ $code }}</h2>
            </div>
            
            <p>This code will expire in 60 minutes. If you did not request this, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} BPLS Online Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
