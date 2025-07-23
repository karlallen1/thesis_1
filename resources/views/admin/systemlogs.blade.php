<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>System Logs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .font-georgia {
      font-family: Georgia, 'Times New Roman', Times, serif;
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
     
        </div>
      </div>
    </div>

    <!-- Navigation -->
<nav class="flex-1 px-8 py-6 space-y-6">
  <!-- Dashboard -->
  <a href="{{ route('admin.dashboard') }}"
     class="block text-xl font-georgia text-white transition 
            {{ request()->routeIs('admin.dashboard') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    Dashboard
  </a>

  <!-- User Management -->
  <a href="{{ route('admin.usermanagement') }}"
     class="block text-xl font-georgia text-white transition 
            {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    User Management
  </a>

  <!-- Queue Status -->
  <a href="{{ route('admin.queuestatus') }}"
     class="block text-xl font-georgia text-white transition 
            {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    Queue Status
  </a>

  <!-- System Logs -->
  <a href="{{ route('admin.systemlogs') }}"
     class="block text-xl font-georgia text-white transition 
            {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
    System Logs
  </a>
</nav>


    <!-- Logout Footer -->
    <div class="mt-auto px-6 py-1" style="background-color: #1B3C53;">
      <button onclick="logout()" class="w-full bg-[#244C66] hover:bg-[#183345] text-white py-1 rounded text-sm font-semibold transition">
        Logout
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col bg-[#c0c0ca]">
    <!-- Header -->
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


    <!-- System Logs Section -->
    <section class="px-8 py-6">
      <h1 class="font-georgia text-xl font-semibold text-gray-700 mb-4">System Logs</h1>

      <!-- Logs Table -->
      <div class="overflow-x-auto rounded shadow bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-[#1B3C53] text-white">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold">Date & Time</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">User</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Action</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Details</th>
            </tr>
          </thead>
          <tbody id="logsBody" class="bg-white divide-y divide-gray-200 text-gray-700">
            <!-- JS will populate -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
