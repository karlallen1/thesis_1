<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>North Caloocan City Hall - Services</title>

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

    <!-- Right (DateTime) -->
    <div class="flex items-center gap-2" x-data="datetimeDisplay()" x-init="init()">
      <p class="text-xs" x-text="datetime"></p>
      <img src="{{ asset('img/philogo.png') }}" alt="Philippines Logo" class="w-10 h-10 object-contain" />
    </div>
  </header>

  <!-- Services Section -->
  <section class="relative h-[calc(100vh-72px)] flex items-center justify-center overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('{{ asset('img/bgbackground2.jpg') }}');"></div>
    <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>

    <!-- Foreground -->
    <div class="relative z-10 fade-slide-in px-4 text-white text-center">
      <p class="text-2xl md:text-3xl font-medium font-serif mb-8">SELECT SERVICE:</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 md:gap-8 justify-center">
        <a href="{{ url('/requirements/tax-declaration') }}?service_type=Tax Declaration"
           class="w-64 h-14 flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold rounded-lg border border-white transition-all duration-300 hover:-translate-y-1 shadow-lg focus:outline-none focus:ring-2 focus:ring-white">
          Tax Declaration (CTC)
        </a>

        <a href="{{ url('/requirements/no-improvement') }}?service_type=Certificate of No Improvement"
           class="w-64 h-14 flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold rounded-lg border border-white transition-all duration-300 hover:-translate-y-1 shadow-lg focus:outline-none focus:ring-2 focus:ring-white">
          Certificate of No Improvement
        </a>

        <a href="{{ url('/requirements/property-holdings') }}?service_type=Certificate of Property Holdings"
           class="w-64 h-14 flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold rounded-lg border border-white transition-all duration-300 hover:-translate-y-1 shadow-lg focus:outline-none focus:ring-2 focus:ring-white">
          Certificate of Property Holdings
        </a>

        <a href="{{ url('/requirements/non-property-holdings') }}?service_type=Certificate of Non-Property Holdings"
           class="w-64 h-14 flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white text-lg font-semibold rounded-lg border border-white transition-all duration-300 hover:-translate-y-1 shadow-lg focus:outline-none focus:ring-2 focus:ring-white">
          Certificate of Non-Property Holdings
        </a>
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
