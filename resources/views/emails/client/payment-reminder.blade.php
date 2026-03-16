<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        .header { background: #0d9488; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; }
        .content { padding: 40px; }
        .reminder-icon { display: block; width: 64px; height: 64px; margin: 0 auto 20px; background: #fef3c7; border-radius: 50%; padding: 16px; box-sizing: border-box; }
        h2 { color: #0f172a; font-size: 20px; font-weight: 700; margin-top: 0; text-align: center; }
        p { margin-bottom: 20px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; padding: 12px 24px; background: #0d9488; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .payment-box { background: #f0fdfa; border: 2px dashed #99f6e4; border-radius: 12px; padding: 25px; margin: 25px 0; text-align: center; }
        .amount { font-size: 32px; font-weight: 800; color: #0f172a; margin: 10px 0; }
        .label { font-size: 12px; font-weight: 700; color: #14b8a6; text-transform: uppercase; letter-spacing: 0.1em; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .info-label { font-weight: 600; color: #64748b; }
        .info-value { font-weight: 700; color: #0f172a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BPLS Online Portal</h1>
        </div>
        <div class="content">
            <div class="reminder-icon">
                <svg fill="#f59e0b" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            
            <h2>Payment Reminder</h2>
            <p>Hello <strong>{{ $application->client->full_name }}</strong>,</p>
            
            <p>This is a friendly reminder regarding your pending payment for business permit application <strong>{{ $application->application_number }}</strong>. Your assessment is complete and awaiting settlement.</p>
            
            <div class="payment-box">
                <div class="label">Total Assessment Amount</div>
                <div class="amount">₱{{ number_format($application->assessment_amount, 2) }}</div>
                <div style="font-size: 12px; color: #64748b;">Mode: {{ ucfirst($application->mode_of_payment) }}</div>
            </div>

            <div style="margin: 20px 0;">
                <div class="info-row">
                    <span class="info-label">Application #:</span>
                    <span class="info-value">{{ $application->application_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Business Name:</span>
                    <span class="info-value">{{ $application->business->business_name }}</span>
                </div>
            </div>

            <p>To avoid delays in processing your permit, please settle your payment at the Treasury Office or through our available online payment channels.</p>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ url('/client/applications') }}" class="btn">View & Pay Online</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} GReAT System. This is an automated reminder.
        </div>
    </div>
</body>
</html>
