<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>North Caloocan City Hall - Services</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @keyframes staggerFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .stagger-1 { animation-delay: 0.2s; }
        .stagger-2 { animation-delay: 0.4s; }
        .stagger-3 { animation-delay: 0.6s; }
        .stagger-4 { animation-delay: 0.8s; }

        .animated {
            opacity: 0;
            animation: staggerFadeIn 0.7s ease-out forwards;
        }

        .service-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .service-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        .service-card:focus {
            outline: 2px solid white;
            outline-offset: 2px;
        }

        .bg-gradient-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.8) 100%);
        }

        .font-serif-thin {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 300;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased">

  <!-- Header -->
  <header class="flex flex-col sm:flex-row items-center justify-between px-4 py-3 bg-amber-600 text-white shadow-lg">
    <div class="flex items-center gap-3 mb-2 sm:mb-0">
      <img src="{{ asset('img/mainlogo.png') }}" alt="City Hall Logo" class="w-12 h-12 object-contain" />
      <div class="text-center sm:text-left leading-tight">
        <p class="text-xs sm:text-sm opacity-90">REPUBLIC OF THE PHILIPPINES</p>
        <p class="font-bold text-sm sm:text-base">North Caloocan City Hall</p>
      </div>
    </div>

    <!-- Date & Time -->
    <div class="flex items-center gap-3 text-center" x-data="datetimeDisplay()" x-init="init()">
      <img src="{{ asset('img/philogo.png') }}" alt="Philippine Flag" class="w-10 h-8 object-contain" />
      <p class="text-sm sm:text-base font-medium" x-text="datetime"></p>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center z-0"
         style="background-image: url('{{ asset('img/bgbackground2.jpg') }}');">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-overlay z-10"></div>

    <!-- Content -->
    <div class="relative z-20 text-center px-6 max-w-4xl mx-auto">
      <!-- Title -->
      <p class="text-xl sm:text-2xl text-white/90 mb-12 max-w-2xl mx-auto leading-relaxed animated stagger-1">
        Select a service to begin your pre-registration process.
      </p>

      <!-- Services Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8 max-w-4xl mx-auto">
        <!-- Tax Declaration -->
        <a href="{{ url('/requirements/tax-declaration') }}?service_type=Tax Declaration"
           class="service-card animated stagger-2 bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold py-4 px-6 rounded-xl border border-white/30 shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/50">
          Tax Declaration (CTC)
        </a>

        <!-- No Improvement -->
        <a href="{{ url('/requirements/no-improvement') }}?service_type=Certificate of No Improvement"
           class="service-card animated stagger-3 bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold py-4 px-6 rounded-xl border border-white/30 shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/50">
          Certificate of No Improvement
        </a>

        <!-- Property Holdings -->
        <a href="{{ url('/requirements/property-holdings') }}?service_type=Certificate of Property Holdings"
           class="service-card animated stagger-3 bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold py-4 px-6 rounded-xl border border-white/30 shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/50">
          Certificate of Property Holdings
        </a>

        <!-- Non-Property Holdings -->
        <a href="{{ url('/requirements/non-property-holdings') }}?service_type=Certificate of Non-Property Holdings"
           class="service-card animated stagger-4 bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold py-4 px-6 rounded-xl border border-white/30 shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/50">
          Certificate of Non-Property Holdings
        </a>
      </div>
    </div>
  </section>

  <!-- Alpine.js: DateTime -->
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