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
        .status-badge { display: inline-block; padding: 6px 14px; background: #f1f5f9; color: #475569; border-radius: 99px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .verified { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .assessed { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
        .returned { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
        .approved { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .rejected { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
        h2 { color: #0f172a; font-size: 20px; font-weight: 700; margin-top: 0; }
        p { margin-bottom: 20px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; padding: 12px 24px; background: #0d9488; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 14px; }
        .info-card { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border: 1px solid #e2e8f0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
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
            <div class="status-badge {{ $application->workflow_status }}">
                {{ $statusLabel }}
            </div>
            
            <h2>Application Update</h2>
            <p>Hello <strong>{{ $application->client->full_name }}</strong>,</p>
            
            <p>Your business permit application has moved to a new stage. Below are the details:</p>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Application #:</span>
                    <span class="info-value">{{ $application->application_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Business:</span>
                    <span class="info-value">{{ $application->business->business_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">New Status:</span>
                    <span class="info-value" style="text-transform: capitalize;">{{ $statusLabel }}</span>
                </div>
            </div>

            @if($customMessage)
                <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
                    <p style="margin: 0; font-size: 14px; color: #92400e;"><strong>Remark from Officer:</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; line-height: 1.4;">{{ $customMessage }}</p>
                </div>
            @endif

            <p>Please log in to your portal to see the latest updates and take any necessary actions.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/client/applications') }}" class="btn">View My Application</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} GReAT System. This is an automated notification.
        </div>
    </div>
</body>
</html>
