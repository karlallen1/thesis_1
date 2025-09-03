<!DOCTYPE html>
<html>
<head>
    <title>Queue Ticket</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 15px 10px;
            background: #fff;
            font-size: 12px;
            text-align: center;
        }
        .header {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 8px;
        }
        .subheader {
            font-size: 0.9em;
            font-weight: normal;
        }
        .ticket-title {
            font-size: 1.3em;
            font-weight: bold;
            margin: 10px 0;
            text-decoration: underline;
        }
        .queue-number {
            font-size: 36px;
            font-weight: bold;
            color: #000000;
            margin: 15px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 12px 0;
            width: 100%;
        }
        .info {
            line-height: 1.8;
            text-align: left;
            max-width: 240px;
            margin: 0 auto;
            font-size: 13px;
        }
        .footer {
            margin-top: 15px;
            font-size: 11px;
            color: #333;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            // Optional: close after print (only works in kiosk mode)
            // setTimeout(window.close, 5000);
        };
    </script>
</head>
<body>

    <!-- Header -->
    <div class="header">
        NORTH CALOOCAN CITY HALL
        <div class="subheader">Office of the Local Tax Assessor</div>
    </div>

    <!-- Title -->
    <div class="ticket-title">QUEUE TICKET</div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Queue Number -->
    <div class="queue-number">{{ $application->queue_number }}</div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Info Section -->
    <div class="info">
        <strong>NAME:</strong> {{ $application->full_name }}<br>
        <strong>SERVICE:</strong> {{ $application->service_type }}
    </div>

    <!-- Footer -->
    <div class="footer">
        Please be ready when your number is called.
    </div>

</body>
</html>