<!DOCTYPE html>
<body data-page="dashboard">
<html lang="en">
<head>
  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>North Caloocan City Hall Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>

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
    <!-- Sidebar Header -->
    <div class="flex items-center px-6 h-20" style="background-color: #1B3C53;">
      <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
        <img src="{{ asset('img/admin.png') }}" alt="Admin Icon" class="w-8 h-8" />
      </div>
      <div class="ml-4 flex flex-col leading-tight relative">
        <span class="text-base font-semibold text-white">Head Admin</span>
        <div class="flex items-center relative">
          <select id="statusSelect" class="text-sm font-normal bg-transparent appearance-none pr-6 text-white focus:outline-none no-arrow">
            <option value="online" selected>Online</option>
            <option value="afk">Away From Keyboard</option>
            <option value="break">On Break</option>
            <option value="offline">Offline</option>
          </select>
          <div class="absolute right-0 pointer-events-none text-xs ml-1 text-white">▼</div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
<nav class="flex-1 px-8 py-6 space-y-6">
  <a href="{{ url('/admin/dashboard') }}"
     class="block text-xl font-georgia text-white transition 
            {{ Request::is('admin/dashboard') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    Dashboard
  </a>

  <a href="{{ url('/admin/usermanagement') }}"
     class="block text-xl font-georgia text-white transition 
            {{ Request::is('admin/usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    User Management
  </a>

  <a href="{{ url('/admin/queuestatus') }}"
     class="block text-xl font-georgia text-white transition 
            {{ Request::is('admin/queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    Queue Status
  </a>

  <a href="{{ url('/admin/systemlogs') }}"
     class="block text-xl font-georgia text-white transition 
            {{ Request::is('admin/systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    System Logs
  </a>
</nav>


    <!-- Logout Button -->
    <div class="mt-auto px-6 py-1" style="background-color: #1B3C53;">
      <button
        onclick="logout()"
        class="w-full bg-[#244C66] hover:bg-[#183345] text-white py-1 rounded text-sm font-semibold transition">
        Logout
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col bg-[#c0c0ca]">
    <!-- Main Header -->
    <header class="flex flex-col md:flex-row justify-between items-center px-4 py-4 md:h-20 gap-4 md:gap-0 shadow border-b border-gray-100" style="background-color: #afafb4;">
      <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3 text-center md:text-left">
        <img src="{{ asset('img/mainlogo.png') }}" alt="Main Logo" class="w-16 h-16 object-contain" />
        <div>
          <div class="font-georgia font-bold text-base md:text-xl"><u>REPUBLIC OF THE PHILIPPINES</u></div>
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

    <!-- Main Sections -->
    <div id="mainSections">
      <!-- Dashboard Section -->
      <section class="px-8 py-6">
        <h1 class="font-georgia text-xl font-semibold text-gray-700 mb-4">Dashboard</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/served.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Clients Served</p>
            <h3 class="text-2xl font-bold text-[#1B3C53]">128</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/pending.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Pending</p>
            <h3 class="text-2xl font-bold text-yellow-500">14</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/cancelled.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Cancelled</p>
            <h3 class="text-2xl font-bold text-red-600">5</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/complete.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Completed</p>
            <h3 class="text-2xl font-bold text-green-600">109</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/PWD.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">PWD Clients</p>
            <h3 class="text-2xl font-bold text-purple-500">10</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/senior.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Senior Clients</p>
            <h3 class="text-2xl font-bold text-purple-700">12</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/avgtime.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Avg. Wait Time</p>
            <h3 class="text-2xl font-bold text-[#1B3C53]">5 min</h3>
          </div>
          <div class="bg-white p-5 rounded-xl shadow text-center">
            <img src="{{ asset('img/longtime.png') }}" alt="Clients Icon" class="w-8 h-8 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Longest Wait</p>
            <h3 class="text-2xl font-bold text-[#1B3C53]">12 min</h3>
          </div>
        </div>
      </section>

      <section class="px-8 py-6">
        <h2 class="font-georgia text-xl font-semibold text-gray-700 mb-4">Hourly Client Count (7 AM - 5 PM)</h2>
        <div class="bg-white p-6 rounded-xl shadow">
          <canvas id="clientLineChart" height="120"></canvas>
        </div>
      </section>
    </div>
  </main>

  <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
