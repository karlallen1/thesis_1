<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Confirmation - Welcome</title>
    <style>
        :root {
            --primary-color: #1a73e8; /* Google Blue */
            --success-color: #34a853; /* Google Green */
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

        .icon-success {
            font-size: 3rem;
            color: var(--success-color);
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

        .queue-number-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 2px;
            margin: 25px 0;
            padding: 15px;
            border: 2px dashed var(--primary-color);
            border-radius: var(--border-radius);
            display: inline-block;
            background-color: rgba(26, 115, 232, 0.05);
        }

        .service-type {
            font-weight: 600;
            color: var(--primary-color);
        }

        .instructions {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e8eaed;
        }

        @media (max-width: 600px) {
            .confirmation-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .queue-number-display {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="icon-success">✓</div>
        <h1>Welcome!</h1>
        <p class="subtitle">You have been added to the queue.</p>

        <div class="info-group">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $application->full_name }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Your Queue Number</span>
            <div class="queue-number-display">{{ $application->queue_number }}</div>
        </div>

        <div class="info-group">
            <span class="info-label">Service Requested</span>
            <span class="info-value service-type">{{ $application->service_type }}</span>
        </div>
        
        <p class="instructions">
            Please wait for your number to be called. You will be served in order.
            {{ $success_message }}
        </p>
    </div>
</body>
</html>