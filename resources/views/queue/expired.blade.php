<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Expired</title>
    <style>
        :root {
            --primary-color: #d93025; /* Google Red */
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --text-primary: #202124;
            --text-secondary: #5f6368;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .confirmation-container {
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .icon-error {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
        }

        .info-group {
            margin-bottom: 25px;
            text-align: left;
            background-color: #f1f3f4;
            padding: 20px;
            border-radius: var(--border-radius);
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .instructions {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e8eaed;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .btn:hover {
            background-color: #c5221f;
        }

        .additional-info {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        @media (max-width: 600px) {
            .confirmation-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="icon-error">✗</div>
        <h1>QR Code Expired</h1>
        <p class="subtitle">Unfortunately, this QR code is no longer valid.</p>

        @if(isset($application) && $application)
        <div class="info-group">
            <span class="info-label">Applicant Name</span>
            <span class="info-value">{{ $applicantName ?? $application->full_name }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Service Type</span>
            <span class="info-value">{{ $serviceType ?? $application->service_type }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Application Submitted</span>
            <span class="info-value">{{ isset($submittedAt) ? $submittedAt->format('F j, Y g:i A') : $application->created_at->format('F j, Y g:i A') }}</span>
        </div>
        @endif

        <div class="info-group">
            <span class="info-label">QR Code Expired At</span>
            <span class="info-value">{{ $expiredAt->format('F j, Y g:i A') }}</span>
        </div>

        @if(isset($expiredAt))
        <div class="additional-info">
            <strong>Expired:</strong> {{ $expiredAt->diffForHumans() }}
        </div>
        @endif

        <p class="instructions">
            Your QR code has expired after 24 hours. Please submit a new application to receive a fresh QR code, or visit the registration desk for assistance.
        </p>

        @if(Route::has('pre.registration'))
        <a href="{{ route('pre.registration') }}" class="btn">Submit New Application</a>
        @else
        <a href="/" class="btn">Return to Home</a>
        @endif

        @if(app()->environment(['local', 'testing']) && isset($token))
        <div class="additional-info" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8eaed;">
            <strong>Debug Info (Testing Only):</strong><br>
            Token: {{ substr($token, 0, 8) }}...<br>
            Current Time: {{ now()->format('F j, Y g:i A') }}
        </div>
        @endif
    </div>

    <script>
        // Auto-refresh every 30 seconds to update "expired X ago" text
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>