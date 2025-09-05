<!DOCTYPE html>
<html>
<head>
    <title>Queue Ticket</title>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px 10px;
            background: #fff;
            text-align: center;
            color: #000;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
        }

        .header {
            font-size: 36px;
            font-weight: bold;
            margin: 5px 0;
        }

        .subheader {
            font-size: 16px;
            margin: 5px 0;
        }

        .ticket-title {
            font-size: 40px;
            font-weight: bold;
            margin: 15px 0;
        }

        .queue-number {
            font-size: 100px;
            font-weight: bold;
            margin: 25px 0;
            letter-spacing: 2px;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 20px 0;
            width: 100%;
        }

        .info {
            font-size: 20px;
            line-height: 2.4;
            text-align: left;
            max-width: 270px;
            margin: 0 auto;
            font-weight: bold;
        }

        .info strong {
            display: inline-block;
            width: 90px;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>

    <!-- Logo: Embedded as Base64 -->
    @php
        $logoPath = public_path('img/mainlogo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $logoContent = file_get_contents($logoPath);
            $logoData = 'data:image/png;base64,' . base64_encode($logoContent);
        } else {
            $logoData = ''; // Fallback
        }
    @endphp

    @if($logoData)
        <img src="{{ $logoData }}" class="logo" alt="Logo">
    @endif

    <!-- Header -->
    <div class="header">NORTH CALOOCAN</div>
    <div class="subheader">City Hall</div>

    <!-- Title -->
    <div class="ticket-title">QUEUE</div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Queue Number -->
    <div class="queue-number">{{ $application->queue_number }}</div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Info -->
    <div class="info">
        <strong>Name:</strong> {{ $application->full_name }}<br>
        <strong>Service:<br>
        </strong> {{ $application->service_type }}
    </div>

</body>
</html>