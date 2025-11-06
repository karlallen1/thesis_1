<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>North Caloocan City Hall - Walk-in Services</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap" rel="stylesheet">

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

        /* grid container */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
            width: 100%;
        }

        /* card (clickable) */
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 3rem 2rem;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 350px;
            width: 100%;
            text-decoration: none; /* for anchors */
            color: inherit;
            transform-origin: center;
            transition: transform 220ms cubic-bezier(.2,.8,.2,1), box-shadow 220ms;
            will-change: transform;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }

        /* hover lift */
        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        /* keyboard focus */
        .card:focus-visible {
            outline: 3px solid rgba(37,99,235,0.15);
            outline-offset: 6px;
        }

        /* impulse keyframes (used on click/tap/keyboard) */
        @keyframes impulse {
            0%   { transform: scale(1); }
            30%  { transform: scale(0.92); }
            60%  { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        /* add the animation class when triggered */
        .impulse {
            animation: impulse 320ms cubic-bezier(.2,.8,.2,1);
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
            text-decoration: none;
            color: white;
            text-align: center;
        }

        .btn-amber { background-color: #d97706; } /* amber-600 */
        .btn-blue  { background-color: #2563eb; } /* blue-600 */
        .btn-green { background-color: #16a34a; } /* green-600 */
        .btn-purple{ background-color: #9333ea; } /* purple-600 */

        h2 {
            font-family: 'Newsreader', sans-serif; 
            font-size: 3rem;
            font-weight: 600;                    
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
            font-family: 'Times New Roman', Times, serif;
            backdrop-filter: blur(4px);
            border: 1px solid #e5e7eb;
            pointer-events: none;
        }

        #datetime div { line-height: 1.2; }

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
                padding: 2.2rem 1.25rem;
                min-height: 300px;
            }
            .icon { width: 60px; height: 60px; }
            h3 { font-size: 1.3rem; }
            p { font-size: 1rem; }
            #datetime { padding: 0.5rem 1rem; font-size: 0.9rem; }
            #datetime .time { font-size: 1rem; }
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
            <h2>Choose a service to get started.</h2>
        </div>

        <!-- Card Container -->
        <div class="card-container">
            
            <!-- Tax Declaration -->
            <a href="/kiosk/requirements/tax-declaration?service_type=Tax Declaration" class="card" >
                <div class="icon bg-amber-100">
                    <svg class="w-10 h-10 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3>Tax Declaration</h3>
                <p>Community Tax Certificate (CTC)</p>
                <span class="btn btn-amber">Get Started</span>
            </a>

            <!-- No Improvement -->
            <a href="/kiosk/requirements/no-improvement?service_type=Certificate of No Improvement" class="card" >
                <div class="icon bg-blue-100">
                    <svg class="w-10 h-10 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3>No Improvement</h3>
                <p>Certificate of No Improvement</p>
                <span class="btn btn-blue">Get Started</span>
            </a>

            <!-- Property Holdings -->
            <a href="/kiosk/requirements/property-holdings?service_type=Certificate of Property Holdings" class="card" >
                <div class="icon bg-green-100">
                    <svg class="w-10 h-10 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
                    </svg>
                </div>
                <h3>Property Holdings</h3>
                <p>Certificate of Property Holdings</p>
                <span class="btn btn-green">Get Started</span>
            </a>

            <!-- Non-Property Holdings -->
            <a href="/kiosk/requirements/non-property-holdings?service_type=Certificate of Non-Property Holdings" class="card" >
                <div class="icon bg-purple-100">
                    <svg class="w-10 h-10 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                </div>
                <h3>Non-Property Holdings</h3>
                <p>Certificate of Non-Property Holdings</p>
                <span class="btn btn-purple">Get Started</span>
            </a>
        </div>
    </main>

    <!-- JavaScript for Time & Keyboard Shortcuts -->
    <script>
        function updateDateTime() {
            const now = new Date();

            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateStr = now.toLocaleDateString(undefined, dateOptions);
            const timeStr = now.toLocaleTimeString();

            document.querySelector('#datetime .date').textContent = dateStr;
            document.querySelector('#datetime .time').textContent = timeStr;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Keyboard Shortcuts (1-4)
        document.addEventListener('keydown', function(e) {
            if (e.key >= '1' && e.key <= '4') {
                e.preventDefault();
                const links = [
                    "/kiosk/requirements/tax-declaration?service_type=Tax Declaration",
                    "/kiosk/requirements/no-improvement?service_type=Certificate of No Improvement",
                    "/kiosk/requirements/property-holdings?service_type=Certificate of Property Holdings",
                    "/kiosk/requirements/non-property-holdings?service_type=Certificate of Non-Property Holdings"
                ];
                const index = parseInt(e.key) - 1;
                if (links[index]) {
                    window.location.href = links[index];
                }
            }
        });

        // --- Impulse click/touch/keyboard trigger for cards ---
        (function() {
            const cards = document.querySelectorAll('.card');

            function triggerImpulse(card) {
                // restart animation
                card.classList.remove('impulse');
                // force reflow to restart animation reliably
                void card.offsetWidth;
                card.classList.add('impulse');
            }

            cards.forEach(card => {
                // mouse / touch
                card.addEventListener('mousedown', () => triggerImpulse(card));
                card.addEventListener('touchstart', () => triggerImpulse(card), { passive: true });

                // keyboard (Enter or Space)
                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        triggerImpulse(card);
                    }
                });

                // cleanup after animation ends
                card.addEventListener('animationend', () => card.classList.remove('impulse'));
            });
        })();
    </script>
</body>
</html>
