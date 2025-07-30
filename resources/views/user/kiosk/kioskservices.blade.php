<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>North Caloocan City Hall - Walk-in Services</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    @keyframes fadeSlideIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .fade-slide-in {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeSlideIn 0.8s ease-out forwards;
    }
    
    .service-card {
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }
    
    .service-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    .service-card:active {
      transform: translateY(-4px) scale(1.01);
    }
    
    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
      50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
    }
    
    .pulse-glow {
      animation: pulse-glow 2s infinite;
    }
  </style>
</head>

<body class="bg-white font-sans text-gray-800">

  <!-- Header -->
  <header class="flex flex-col md:flex-row items-center justify-between p-3 md:px-6 bg-amber-600 text-white shadow border-b border-white">

    <!-- Left -->
    <div class="flex items-center gap-3 mb-2 md:mb-0">
      <img src="{{ asset('img/mainlogo.png') }}" alt="City Hall Logo" class="w-10 h-10 object-contain" />
      <h2 class="text-center md:text-left text-sm md:text-base leading-tight">
        <u class="font-bold">REPUBLIC OF THE PHILIPPINES</u><br />
        <span class="font-bold">Caloocan City Hall</span>
      </h2>
    </div>

    <!-- Center - Kiosk Mode Indicator -->
    <div class="hidden md:flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full">
      <div class="w-3 h-3 bg-green-400 rounded-full pulse-glow"></div>
      <span class="text-sm font-medium">WALK-IN KIOSK</span>
    </div>

    <!-- Right (DateTime) -->
    <div class="flex items-center gap-2" x-data="datetimeDisplay()" x-init="init()">
      <p class="text-xs" x-text="datetime"></p>
      <img src="{{ asset('img/philogo.png') }}" alt="Philippines Logo" class="w-10 h-10 object-contain" />
    </div>
  </header>

  <!-- Services Section -->
  <section class="relative min-h-[calc(100vh-72px)] flex items-center justify-center overflow-hidden py-8">
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('{{ asset('img/bgbackground2.jpg') }}');"></div>
    <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>

    <!-- Foreground -->
    <div class="relative z-10 fade-slide-in px-4 text-white text-center max-w-6xl mx-auto">
      
      <!-- Header Text -->
      <div class="mb-12">
        <h1 class="text-3xl md:text-4xl font-bold font-serif mb-2">Welcome!</h1>
        <p class="text-xl md:text-2xl font-medium font-serif mb-4">SELECT A SERVICE TO GET STARTED:</p>
        <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full"></div>
      </div>

      <!-- Service Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 md:gap-8 justify-center">
        
        <!-- Tax Declaration Card -->
        <a href="/kiosk/requirements/tax-declaration?service_type=Tax Declaration"
           class="service-card group bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-gray-800 hover:bg-white transition-all duration-300 border border-white/20">
          <div class="flex flex-col items-center text-center space-y-4">
            <!-- Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-amber-600 transition-colors">
              Tax Declaration
            </h3>
            
            <!-- Subtitle -->
            <p class="text-sm text-gray-600 group-hover:text-gray-700">
              Community Tax Certificate (CTC)
            </p>
            
            <!-- Hover Arrow -->
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </a>

        <!-- Certificate of No Improvement Card -->
        <a href="/kiosk/requirements/no-improvement?service_type=Certificate of No Improvement"
           class="service-card group bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-gray-800 hover:bg-white transition-all duration-300 border border-white/20">
          <div class="flex flex-col items-center text-center space-y-4">
            <!-- Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
              No Improvement
            </h3>
            
            <!-- Subtitle -->
            <p class="text-sm text-gray-600 group-hover:text-gray-700">
              Certificate of No Improvement
            </p>
            
            <!-- Hover Arrow -->
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </a>

        <!-- Property Holdings Card -->
        <a href="/kiosk/requirements/property-holdings?service_type=Certificate of Property Holdings"
           class="service-card group bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-gray-800 hover:bg-white transition-all duration-300 border border-white/20">
          <div class="flex flex-col items-center text-center space-y-4">
            <!-- Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
              </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-green-600 transition-colors">
              Property Holdings
            </h3>
            
            <!-- Subtitle -->
            <p class="text-sm text-gray-600 group-hover:text-gray-700">
              Certificate of Property Holdings
            </p>
            
            <!-- Hover Arrow -->
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </a>

        <!-- Non-Property Holdings Card -->
        <a href="/kiosk/requirements/non-property-holdings?service_type=Certificate of Non-Property Holdings"
           class="service-card group bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-gray-800 hover:bg-white transition-all duration-300 border border-white/20">
          <div class="flex flex-col items-center text-center space-y-4">
            <!-- Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
              </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-600 transition-colors">
              Non-Property Holdings
            </h3>
            
            <!-- Subtitle -->
            <p class="text-sm text-gray-600 group-hover:text-gray-700">
              Certificate of Non-Property Holdings
            </p>
            
            <!-- Hover Arrow -->
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </a>

      </div>

      <!-- Footer Info -->
      <div class="mt-12 text-center">
        <p class="text-sm text-white/80 mb-2">
          👆 Touch any service above to start your application
        </p>
        <p class="text-xs text-white/60">
          Walk-in applicants will be directly added to the queue after form submission
        </p>
      </div>

    </div>
  </section>

  <!-- Alpine.js for DateTime -->
  <script>
    function datetimeDisplay() {
      return {
        datetime: '',
        init() {
          this.updateTime();
          setInterval(() => this.updateTime(), 1000);
        },
        updateTime() {
          const now = new Date();
          const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
            timeZone: 'Asia/Manila'
          };
          this.datetime = new Intl.DateTimeFormat('en-US', options).format(now);
        }
      };
    }
  </script>
</body>
</html>