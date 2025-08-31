<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCC Admin</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
    <!-- Tailwind CSS via CDN -->
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-georgia { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 35px -10px rgba(0, 0, 0, 0.12);
            border-color: rgba(0, 0, 0, 0.1);
        }
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.8s, height 0.8s;
        }
        .btn-ripple:active::after {
            width: 300px;
            height: 300px;
        }
        @media (min-width: 1024px) {
            h2, h3 { font-size: clamp(1.5rem, 4vw, 2.5rem); }
        }
    </style>
</head>

<!-- ✅ Flex layout to prevent footer float -->
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-amber-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-5">
            <div class="flex items-center justify-center space-x-3">
                <img src="{{ asset('img/mainlogo.png') }}" alt="City Hall Logo" class="w-12 h-12 object-contain" />
                <div class="text-center">
                    <h1 class="text-2xl sm:text-3xl font-georgia font-bold">NCC Admin</h1>
                    <p class="text-amber-100 text-sm sm:text-base">North Caloocan City Hall</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-12 sm:px-8 lg:px-12 max-w-7xl">
        
        <!-- Welcome -->
        <div class="text-center mb-14 max-w-4xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-georgia font-bold text-gray-900 mb-4 leading-tight">
                Administrative Tools
            </h2>
            <p class="text-gray-600 text-lg sm:text-xl leading-relaxed">
                Choose your desired tool to get started. Access the admin panel, monitor live queues, or launch kiosk mode.
            </p>
        </div>

        <!-- 3-Column Grid -->
        <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">

            <!-- Admin Panel -->
            <a href="{{ route('admin.login') }}"
               class="block bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center card-hover hover:border-blue-200 transition-all duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform hover:scale-105">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3">Admin Panel</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Manage users, queues, and system settings securely.
                </p>
                <span class="inline-block bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Access Admin Panel
                </span>
            </a>

            <!-- Live Queue -->
            <a href="{{ route('queue.display') }}" target="_blank"
               class="block bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center card-hover hover:border-green-200 transition-all duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform hover:scale-105">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3">Live Queue Monitor</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    View real-time queue status and current service numbers.
                </p>
                <span class="inline-block bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-green-200">
                    View Queue Display
                </span>
            </a>

            <!-- Kiosk Mode -->
            <a href="/kiosk"
               class="block bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center card-hover hover:border-yellow-200 transition-all duration-300">
                <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform hover:scale-105">
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3">Kiosk Mode</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Direct walk-in service entry for citizens.
                </p>
                <span class="inline-block bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-yellow-200">
                    Enter Kiosk Mode
                </span>
            </a>
        </div>
    </main>

    <!-- ✅ Full-width, grounded footer -->
    <footer class="bg-gray-800 text-white py-8 w-full border-t border-gray-700">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-300 font-medium text-lg">
                North Caloocan City Hall - Queue Management System
            </p>
            <p class="text-gray-400 text-sm mt-1">
                &copy; 2025 NCC | All rights reserved
            </p>
        </div>
    </footer>

    <!-- Keyboard Shortcuts -->
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === '1') {
                e.preventDefault();
                window.location.href = "{{ route('admin.login') }}";
            } else if (e.ctrlKey && e.key === '2') {
                e.preventDefault();
                window.open("{{ route('queue.display') }}", '_blank');
            } else if (e.ctrlKey && e.key === '3') {
                e.preventDefault();
                window.location.href = "/kiosk";
            }
        });
    </script>
</body>
</html>