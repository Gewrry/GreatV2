<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your GReAT System Account</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f0f4f8;
            color: #2d3748;
            padding: 32px 16px;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #1a5c3a 0%, #0e9f6e 100%);
            border-radius: 16px 16px 0 0;
            padding: 40px 40px 32px;
            text-align: center;
        }

        .header .lgu-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.65);
            margin-bottom: 10px;
        }

        .header .seal {
            font-size: 40px;
            line-height: 1;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .header .tagline {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
        }

        /* ── Card body ── */
        .card {
            background: #ffffff;
            border-radius: 0 0 16px 16px;
            padding: 40px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        .greeting {
            font-size: 17px;
            font-weight: 800;
            color: #1a5c3a;
            margin-bottom: 14px;
        }

        .intro {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.75;
            margin-bottom: 28px;
        }

        /* ── Business reference pill ── */
        .business-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }

        .business-pill .icon {
            font-size: 22px;
            flex-shrink: 0;
        }

        .business-pill .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #93c5fd;
        }

        .business-pill .name {
            font-size: 14px;
            font-weight: 800;
            color: #1e40af;
        }

        /* ── Credentials box ── */
        .credentials-box {
            background: #f0fdf4;
            border: 2px solid #6ee7b7;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
        }

        .credentials-box .box-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #059669;
            margin-bottom: 18px;
        }

        .credential-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .credential-row:last-child {
            margin-bottom: 0;
        }

        .credential-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            width: 110px;
            flex-shrink: 0;
        }

        .credential-value {
            font-size: 14px;
            font-weight: 800;
            color: #065f46;
            font-family: 'Courier New', Courier, monospace;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            padding: 6px 14px;
            border-radius: 8px;
            letter-spacing: 0.8px;
        }

        /* ── Warning box ── */
        .warning-box {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            border-radius: 0 10px 10px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
            font-size: 13px;
            color: #7c2d12;
            line-height: 1.65;
        }

        .warning-box .warning-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #ea580c;
            margin-bottom: 6px;
        }

        /* ── Steps ── */
        .steps {
            margin-bottom: 32px;
        }

        .steps-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #374151;
            line-height: 1.55;
        }

        .step-num {
            background: #1a5c3a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            min-width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── CTA button ── */
        .cta-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #1a5c3a 0%, #0e9f6e 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 900;
            padding: 16px 44px;
            border-radius: 12px;
            letter-spacing: 0.3px;
            box-shadow: 0 6px 20px rgba(14, 159, 110, 0.4);
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            padding-top: 8px;
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.8;
        }

        .footer strong {
            color: #6b7280;
        }

        /* ── Responsive ── */
        @media only screen and (max-width: 480px) {

            .card,
            .header {
                padding: 28px 24px;
            }

            .credentials-box {
                padding: 18px 20px;
            }

            .credential-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .credential-label {
                width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- ── Header ── --}}
        <div class="header">
            <p class="lgu-label">Municipal Government &mdash; BPLS Office</p>
            <div class="seal">🏛️</div>
            <h1>Your GReAT System Account is Ready!</h1>
            <p class="tagline">Government Revenue &amp; Assessment Tool — Client Portal</p>
        </div>

        {{-- ── Body card ── --}}
        <div class="card">

            <p class="greeting">Good day, {{ $clientName }}!</p>

            <p class="intro">
                Your business permit application has been assessed by our BPLO officer.
                We have automatically created a <strong>Client Portal account</strong> for you
                in the <strong>GReAT System</strong> so you can track your application status,
                receive updates, and monitor your payment — anytime, anywhere.
            </p>

            {{-- Business reference --}}
            <div class="business-pill">
                <span class="icon">🏢</span>
                <div>
                    <p class="label">Registered Business</p>
                    <p class="name">{{ $businessName }}</p>
                </div>
            </div>

            {{-- Credentials --}}
            <div class="credentials-box">
                <p class="box-label">🔑 Your Login Credentials</p>

                <div class="credential-row">
                    <span class="credential-label">Email / Username</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>

                <div class="credential-row">
                    <span class="credential-label">Temp Password</span>
                    <span class="credential-value">{{ $tempPassword }}</span>
                </div>
            </div>

            {{-- Security warning --}}
            <div class="warning-box">
                <p class="warning-title">⚠️ Security Notice — Change Your Password</p>
                This is a <strong>system-generated temporary password</strong>.
                Please log in and change it immediately from your profile settings.
                Never share your credentials with anyone, including BPLO staff.
            </div>

            {{-- Steps --}}
            <div class="steps">
                <p class="steps-title">Getting Started</p>

                <div class="step-item">
                    <div class="step-num">1</div>
                    <span>Click <strong>"Go to Client Portal"</strong> below to open the login page.</span>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <span>Sign in with your <strong>email</strong> and the <strong>temporary password</strong>
                        above.</span>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <span>Go to your <strong>Profile Settings</strong> and update your password to something
                        secure.</span>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <span>Check your application status and proceed to the <strong>payment stage</strong>.</span>
                </div>
            </div>

            {{-- CTA ── --}}
            <div class="cta-section">
                <a href="{{ $portalUrl }}" class="cta-button">
                    Go to Client Portal &rarr;
                </a>
            </div>

            <hr class="divider">

            <p style="font-size:12px; color:#9ca3af; text-align:center; line-height:1.7;">
                If you did not apply for a business permit with our office, please disregard this email
                or contact us immediately so we can investigate.
            </p>

        </div>

        {{-- ── Footer ── --}}
        <div class="footer">
            <strong>BPLS Municipal Government — GReAT System</strong><br>
            This is an automated message. Please do not reply to this email.<br>
            For assistance, visit the BPLO office during business hours.
        </div>

    </div>
</body>

</html>
