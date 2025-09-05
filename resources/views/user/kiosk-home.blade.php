<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Kiosk Home</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-georgia { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-color: #f9fafb;
            margin: 0;
            min-height: 100vh;
        }
        .logo {
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin: 0 auto 2rem;
            display: block;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 6rem 4rem;
            background-color: white;
            box-shadow: none;
            transition: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 500px;
            max-width: 700px;
            margin: 0 auto;
        }
        .card-container {
            display: flex;
            gap: 3rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .icon {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-bottom: 2.5rem;
        }
        .btn {
            padding: 0.8rem 2rem;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: none;
            margin-top: 2.5rem;
            width: fit-content;
        }
        .btn-blue {
            background-color: #0057d4;
            color: white;
        }
        .btn-green {
            background-color: #008000;
            color: white;
        }
        h2 {
            font-size: 2.5rem;
            margin-bottom: 2.5rem;
            text-align: center;
            color: #000;
        }
        h3 {
            font-size: 1.6rem;
            margin: 0;
            color: #1f2937;
            font-weight: bold;
        }
        p {
            font-size: 1.1rem;
            color: #555;
            text-align: center;
            margin: 0.75rem 0 0 0;
        }


        #datetime {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            font-family:  'Times New Roman', Times, serif;
            backdrop-filter: blur(4px);
            border: 1px solid #e5e7eb;
            pointer-events: none;
        }
        #datetime div {
            line-height: 1.2;
        }
        #datetime .date {
            font-size: 0.95rem;
            color: #4b5563;
            font-weight: normal;
        }
        #datetime .time {
            font-size: 1.1rem;
            color: #1f2937;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .card {
                padding: 3rem 2rem;
                min-height: 400px;
            }
            .card-container {
                flex-direction: column;
                gap: 2rem;
            }
            .icon {
                width: 60px;
                height: 60px;
            }
            h3 {
                font-size: 1.3rem;
            }
            p {
                font-size: 1rem;
            }
            #datetime {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            #datetime .time {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Time and Date Display (Top Right Corner) -->
    <div id="datetime" class="absolute top-4 right-6 text-right z-50">
        <div class="date">Loading...</div>
        <div class="time">--:--:--</div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-12 sm:px-8 lg:px-12 max-w-5xl flex flex-col items-center">

        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <img 
                src="{{ asset('img/mainlogo.png') }}" 
                alt="NCC Logo" 
                class="logo"
                onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2ZmZiIvPjwvc3ZnPg=='"
            >
            <h2 class="text-3xl font-georgia font-bold text-gray-900">Choose a service to get started.</h2>
        </div>

        <!-- Card Container -->
        <div class="card-container">
            <!-- Scan QR Code Card -->
            <div class="card">
                <div class="icon bg-blue-100">
                    <img src="{{ asset('img/scanner.png') }}" alt="Scan QR Code" width="36" height="36">
                </div>
                <h3>Scan QR Code</h3>
                <p>Scan your pre-registered QR code to proceed quickly.</p>
                <a href="/scan-qr" class="btn btn-blue">Start Scanning</a>
            </div>

            <!-- Kiosk Application Card -->
            <div class="card">
                <div class="icon bg-green-100">
                    <img src="{{ asset('img/kiosk.png') }}" alt="Kiosk Application" width="36" height="36">
                </div>
                <h3>Kiosk Application</h3>
                <p>Apply directly using the kiosk for walk-in services.</p>
                <a href="/kiosk-services" class="btn btn-green">Start Application</a>
            </div>
        </div>
    </main>

    <!-- JavaScript for Time & Keyboard Shortcuts -->
    <script>
        function updateDateTime() {
            const now = new Date();

            // Format date: e.g., "July 5, 2024"
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const dateStr = now.toLocaleDateString(undefined, dateOptions);

            // Format time: e.g., "10:30:15"
            const timeStr = now.toLocaleTimeString();

            document.querySelector('#datetime .date').textContent = dateStr;
            document.querySelector('#datetime .time').textContent = timeStr;
        }

        // Update immediately and every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === '1') {
                e.preventDefault();
                window.location.href = "/scan-qr";
            } else if (e.ctrlKey && e.key === '2') {
                e.preventDefault();
                window.location.href = "/kiosk-services";
            }
        });
    </script>
</body>
</html>