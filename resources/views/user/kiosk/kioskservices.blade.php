<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>North Caloocan City Hall - Walk-in Services</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .font-georgia { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
        }
        
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: url('{{ asset('img/bgbackground2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        
        .bg-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }
        
        .header-glass {
            background: rgba(245, 158, 11, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
        }
        
        .service-card:hover::before {
            left: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .service-card:hover .service-icon {
            transform: scale(1.15) rotate(5deg);
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
        
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-slide-in {
            animation: fadeSlideIn 0.8s ease-out forwards;
        }
        
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @media (min-width: 1024px) {
            h1 { font-size: clamp(2.5rem, 5vw, 4rem); }
            h3 { font-size: clamp(1.25rem, 2vw, 1.5rem); }
        }
    </style>
</head>

<body class="min-h-screen">

    <!-- Background Overlay -->
    <div class="fixed inset-0 bg-overlay z-0"></div>

    <!-- Header -->
    <header class="header-glass sticky top-0 z-50 relative">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <!-- Left: NCC Logo + Text -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('img/mainlogo.png') }}" alt="North Caloocan City Hall" class="w-16 h-16 object-contain drop-shadow-lg" />
                    <div class="text-white">
                        <p class="text-sm font-medium opacity-90">REPUBLIC OF THE PHILIPPINES</p>
                        <p class="text-xl font-georgia font-bold">North Caloocan City Hall</p>
                    </div>
                </div>

                <!-- Right: Date, Time, PH Logo -->
                <div class="flex items-center gap-6" x-data="datetimeDisplay()" x-init="init()">
                    <div class="text-right text-white">
                        <p class="text-lg font-georgia font-semibold whitespace-nowrap" x-text="date"></p>
                        <p class="text-base font-medium opacity-90 whitespace-nowrap" x-text="time"></p>
                    </div>
                    <img src="{{ asset('img/philogo.png') }}" alt="Philippines Logo" class="w-16 h-16 object-contain drop-shadow-lg" />
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16 sm:px-8 lg:px-12 max-w-7xl relative z-10">
        
        <!-- Welcome Header -->
        <div class="text-center mb-16 max-w-4xl mx-auto fade-slide-in">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-georgia font-bold text-white mb-6 leading-tight drop-shadow-2xl">
                <span class="text-white">Welcome!</span>
            </h1>
            <p class="text-xl sm:text-2xl font-georgia font-medium text-white mb-6 drop-shadow-lg">
                SELECT A SERVICE TO GET STARTED
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-amber-400 to-amber-600 mx-auto rounded-full shadow-lg"></div>
        </div>

        <!-- Service Cards Grid -->
        <div class="grid sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 lg:gap-10">
            
            <!-- Tax Declaration -->
            <a href="/kiosk/requirements/tax-declaration?service_type=Tax Declaration"
               class="service-card stagger-1 fade-slide-in block bg-white rounded-2xl shadow-lg p-8 text-center hover:border-amber-200 transition-all duration-300 group">
                <div class="flex flex-col items-center h-full">
                    <div class="service-icon w-20 h-20 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3 group-hover:text-amber-600 transition-colors">
                                Tax Declaration
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                Community Tax Certificate (CTC)
                            </p>
                        </div>
                        <span class="inline-block bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-amber-200 w-full mt-auto">
                            Get Started
                        </span>
                    </div>
                </div>
            </a>

            <!-- No Improvement -->
            <a href="/kiosk/requirements/no-improvement?service_type=Certificate of No Improvement"
               class="service-card stagger-2 fade-slide-in block bg-white rounded-2xl shadow-lg p-8 text-center hover:border-blue-200 transition-all duration-300 group">
                <div class="flex flex-col items-center h-full">
                    <div class="service-icon w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                No Improvement
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                Certificate of No Improvement
                            </p>
                        </div>
                        <span class="inline-block bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-blue-200 w-full mt-auto">
                            Get Started
                        </span>
                    </div>
                </div>
            </a>

            <!-- Property Holdings -->
            <a href="/kiosk/requirements/property-holdings?service_type=Certificate of Property Holdings"
               class="service-card stagger-3 fade-slide-in block bg-white rounded-2xl shadow-lg p-8 text-center hover:border-green-200 transition-all duration-300 group">
                <div class="flex flex-col items-center h-full">
                    <div class="service-icon w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center shadow-lg mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
                        </svg>
                    </div>
                    <div class="flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">
                                Property Holdings
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                Certificate of Property Holdings
                            </p>
                        </div>
                        <span class="inline-block bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-green-200 w-full mt-auto">
                            Get Started
                        </span>
                    </div>
                </div>
            </a>

            <!-- Non-Property Holdings -->
            <a href="/kiosk/requirements/non-property-holdings?service_type=Certificate of Non-Property Holdings"
               class="service-card stagger-4 fade-slide-in block bg-white rounded-2xl shadow-lg p-8 text-center hover:border-purple-200 transition-all duration-300 group">
                <div class="flex flex-col items-center h-full">
                    <div class="service-icon w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-georgia font-bold text-gray-900 mb-3 group-hover:text-purple-600 transition-colors">
                                Non-Property Holdings
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                Certificate of Non-Property Holdings
                            </p>
                        </div>
                        <span class="inline-block bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-georgia font-semibold py-3 px-6 rounded-xl transition-all duration-200 btn-ripple focus:outline-none focus:ring-4 focus:ring-purple-200 w-full mt-auto">
                            Get Started
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Additional Info Section -->
        <div class="mt-16 text-center fade-slide-in">
            <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8 max-w-3xl mx-auto">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-georgia font-bold text-gray-900 mb-4">Need Assistance?</h3>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Our friendly staff is here to help you navigate through the process. 
                    If you need guidance selecting the right service, please don't hesitate to ask.
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-500">
                    <span class="bg-gray-100 px-4 py-2 rounded-full">✓ Fast Processing</span>
                    <span class="bg-gray-100 px-4 py-2 rounded-full">✓ Digital Queue System</span>
                    <span class="bg-gray-100 px-4 py-2 rounded-full">✓ Professional Service</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/90 backdrop-blur-sm text-white py-8 mt-16 relative z-10 border-t border-white/10">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-300 font-georgia font-medium text-lg">
                North Caloocan City Hall - Queue Management System
            </p>
            <p class="text-gray-400 text-sm mt-2">
                &copy; 2025 NCC | Serving the Community with Excellence
            </p>
        </div>
    </footer>

    <!-- Alpine.js for DateTime -->
    <script>
        function datetimeDisplay() {
            return {
                date: '',
                time: '',
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                },
                updateTime() {
                    const now = new Date();
                    
                    // Format Date
                    const dateFormatter = new Intl.DateTimeFormat('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    this.date = dateFormatter.format(now);

                    // Format Time
                    const timeFormatter = new Intl.DateTimeFormat('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true,
                        timeZone: 'Asia/Manila'
                    });
                    this.time = timeFormatter.format(now);
                }
            };
        }

        // Add keyboard shortcuts for accessibility
        document.addEventListener('keydown', function(e) {
            if (e.key >= '1' && e.key <= '4') {
                const links = document.querySelectorAll('a[href*="/kiosk/requirements"]');
                const index = parseInt(e.key) - 1;
                if (links[index]) {
                    e.preventDefault();
                    links[index].click();
                }
            }
        });
    </script>
</body>
</html>