<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>North Caloocan City Hall – Assessment Portal</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    html, body {
      height: 100%;
      margin: 0;
      scroll-behavior: smooth;
    }
    main {
      height: 100vh;
      overflow-y: scroll;
      scroll-snap-type: y mandatory;
    }
    section {
      scroll-snap-align: start;
    }
  </style>
</head>
<body class="bg-white text-gray-900 relative">

  <!-- SCROLL INDICATOR -->
  <div id="scroll-indicator" class="fixed top-0 left-0 h-1 bg-amber-600 z-50 transition-all duration-200 ease-out" style="width: 0%"></div>

  <!-- HEADER -->
  <header class="absolute top-0 left-0 w-full z-10 px-6 py-4 flex justify-between items-center text-white">

  </header>

  <main>

    <!-- HERO SECTION -->
    <section class="relative h-screen w-full flex items-center justify-center bg-cover bg-center" style="background-image: url('/img/bg1.jpg');">
      <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>
      <div class="z-10 text-center text-white px-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-4 drop-shadow">Welcome to North Caloocan</h1>
        <p class="text-lg sm:text-xl font-light mb-8 drop-shadow">City Assessor's Online Services Portal</p>
        <button onclick="location.href='{{ url('/services') }}'"
          class="bg-amber-600 hover:bg-amber-700 px-8 py-4 text-lg font-semibold rounded-2xl shadow-lg 
                 transition duration-300 transform hover:scale-105 flex items-center justify-center gap-2 mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
          GET STARTED
        </button>
      </div>
    </section>

    <!-- HOW TO USE SECTION -->
    <section class="py-16 px-6 bg-white">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">How to Use This Portal</h2>
        <p class="text-lg text-gray-700 leading-relaxed">
          Select a service below to request documents from the City Assessor's Office. You can submit applications online and receive your documents through email with a tracking number and QR code.
          Ensure your information is accurate and all required fields are completed before submission.
        </p>
      </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="py-16 px-6 bg-gray-100">
      <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-10 text-gray-800">Available Services</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

          <!-- Service Cards -->
          @foreach ([
            ['icon' => 'M9 17v-2h6v2a2 2 0 002 2h-2a2 2 0 01-2-2zM7 7h10v4H7V7z', 'title' => 'Tax Declaration', 'desc' => 'Serves as the city\'s official record for property owners. Certified true copies or certifications can be requested here.'],
            ['icon' => 'M5 13l4 4L19 7', 'title' => 'No Improvement', 'desc' => 'Certifies that no improvements or structures are declared on a given property.'],
            ['icon' => 'M9 12l2 2 4-4', 'title' => 'Property Holding', 'desc' => 'Lists all real property holdings under the owner\'s name as declared in the municipality.'],
            ['icon' => 'M13 16h-1v-4h-1m1-4h.01', 'title' => 'Non-Property Holding', 'desc' => 'Certification confirming that the person holds no real property under their name.']
          ] as $service)
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:scale-105 cursor-pointer">
              <div class="text-amber-600 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="{{ $service['icon'] }}" />
                </svg>
              </div>
              <p class="text-lg font-medium text-gray-800 mb-2">{{ $service['title'] }}</p>
              <p class="text-sm text-gray-600">{{ $service['desc'] }}</p>
            </div>
          @endforeach

        </div>
      </div>
    </section>

  </main>

  <!-- SCROLL INDICATOR SCRIPT -->
  <script>
    const indicator = document.getElementById('scroll-indicator');
    const main = document.querySelector('main');

    main.addEventListener('scroll', () => {
      const scrollTop = main.scrollTop;
      const scrollHeight = main.scrollHeight - main.clientHeight;
      const scrollPercent = (scrollTop / scrollHeight) * 100;
      indicator.style.width = scrollPercent + "%";
    });
  </script>

</body>
</html>
