<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>North Caloocan City Hall Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .font-georgia {
      font-family: Georgia, 'Times New Roman', Times, serif;
    }
    .active-link {
      font-weight: bold;
      text-decoration: underline;
      opacity: 1 !important;
      filter: none !important;
    }
    select.no-arrow {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
    }
  </style>
</head>
<body class="bg-[#F9F3EF] min-h-screen flex">

  <!-- Sidebar -->
  <aside class="text-white w-64 flex flex-col min-h-screen border-r border-gray-100" style="background-color: #57768c;">
    <div class="flex items-center px-6 h-20 bg-[#1B3C53]">
      <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
        <img src="{{ asset('img/admin.png') }}" alt="Admin Icon" class="w-8 h-8" />
      </div>
      <div class="ml-4 flex flex-col leading-tight relative">
        <span class="text-base font-semibold">Head Admin</span>

        </div>
      </div>
    </div>

    <!-- Navigation -->
   <nav class="flex-1 px-8 py-6 space-y-6">
  @php
    $routes = [
      'admin.dashboard' => 'Dashboard',
      'admin.usermanagement' => 'User Management',
      'admin.queuestatus' => 'Queue Status',
      'admin.systemlogs' => 'System Logs',
    ];
  @endphp

  @foreach ($routes as $routeName => $label)
    <a href="{{ route($routeName) }}"
       class="block text-xl font-georgia transition text-white 
              {{ request()->routeIs($routeName) ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
      {{ $label }}
    </a>
  @endforeach
</nav>

    <!-- Logout -->
    <div class="mt-auto px-6 py-1 bg-[#1B3C53]">
      <form action="{{ route('admin.logout') }}" method="GET">
        <button type="submit" class="w-full bg-[#244C66] hover:bg-[#183345] text-white py-1 rounded text-sm font-semibold transition">
          Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col bg-[#c0c0ca]">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-center px-4 py-4 md:h-20 gap-4 shadow border-b border-gray-100 bg-[#afafb4]">
      <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 text-center md:text-left">
        <img src="{{ asset('img/mainlogo.png') }}" alt="Main Logo" class="w-16 h-16 object-contain" />
        <div>
          <div class="font-georgia font-bold text-base md:text-xl underline">REPUBLIC OF THE PHILIPPINES</div>
          <div class="font-georgia text-base md:text-xl font-semibold">North Caloocan City Hall</div>
        </div>
      </div>
      <div class="flex items-center space-x-3">
        <div class="text-right text-gray-700">
          <div id="datetime" class="text-sm md:text-base font-semibold"></div>
        </div>
        <img src="{{ asset('img/philogo.png') }}" alt="PH Logo" class="w-12 h-12 object-contain" />
      </div>
    </header>

    <!-- Dashboard -->
    <section class="px-8 py-6">
      <h1 class="font-georgia text-xl font-semibold text-gray-700 mb-4">Dashboard</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
          $stats = [
            ['img' => 'served.png', 'label' => 'Clients Served', 'value' => 128, 'color' => '#1B3C53'],
            ['img' => 'pending.png', 'label' => 'Pending', 'value' => 14, 'color' => '#eab308'],
            ['img' => 'cancelled.png', 'label' => 'Cancelled', 'value' => 5, 'color' => '#dc2626'],
            ['img' => 'complete.png', 'label' => 'Completed', 'value' => 109, 'color' => '#16a34a'],
            ['img' => 'PWD.png', 'label' => 'PWD Clients', 'value' => 10, 'color' => '#8b5cf6'],
            ['img' => 'senior.png', 'label' => 'Senior Clients', 'value' => 12, 'color' => '#6b21a8'],
            ['img' => 'avgtime.png', 'label' => 'Avg. Wait Time', 'value' => '5 min', 'color' => '#1B3C53'],
            ['img' => 'longtime.png', 'label' => 'Longest Wait', 'value' => '12 min', 'color' => '#1B3C53'],
          ];
        @endphp
        @foreach ($stats as $stat)
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/' . $stat['img']) }}" alt="{{ $stat['label'] }}" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
            <h3 class="text-2xl font-bold" style="color: {{ $stat['color'] }}">{{ $stat['value'] }}</h3>
          </div>
        @endforeach
      </div>
    </section>

    <!-- Chart Section -->
    <section class="px-8 py-6">
      <h2 class="font-georgia text-xl font-semibold text-gray-700 mb-4">Hourly Client Count (7 AM - 5 PM)</h2>
      <div class="bg-white p-6 rounded-xl shadow">
        <canvas id="clientLineChart" height="120"></canvas>
      </div>
    </section>
  </main>

  <script>
    function updateDateTime() {
      const now = new Date();
      document.getElementById("datetime").textContent = now.toLocaleString("en-US", {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
      });
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
  </script>

  <script>
    const ctx = document.getElementById('clientLineChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM'],
        datasets: [{
          label: 'Clients',
          data: [2, 5, 9, 13, 8, 6, 10, 15, 11, 9, 4],
          borderColor: '#1B3C53',
          backgroundColor: 'rgba(27, 60, 83, 0.2)',
          fill: true,
          tension: 0.3,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 2 } } }
      }
    });
  </script>

</body>
</html>
