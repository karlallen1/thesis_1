<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>North Caloocan City Hall</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .font-georgia {
      font-family: Georgia, 'Times New Roman', Times, serif;
    }
  </style>
</head>
<body class="relative bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex flex-col">
  <!-- Background Image -->
  <img src="{{ asset('img/bg1.jpg') }}" alt="Background" class="absolute inset-0 w-full h-full object-cover z-0 opacity-20 pointer-events-none" />

  <!-- Header -->
  <header class="relative z-10 bg-white shadow p-4 md:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <!-- Left Side -->
      <div class="flex items-center space-x-3">
        <img src="{{ asset('img/mainlogo.png') }}" alt="Logo1" class="w-12 h-12 sm:w-16 sm:h-16 object-contain" />
        <h2 class="font-georgia text-base sm:text-lg md:text-xl font-bold text-gray-800">
          <u>REPUBLIC OF THE PHILIPPINES</u><br>
          <span class="font-semibold">Caloocan City Hall</span>
        </h2>
      </div>
      <!-- Right Side -->
      <div class="flex items-center space-x-3">
        <p id="datetime" class="text-xs sm:text-sm text-gray-600"></p>
        <img src="{{ asset('img/philogo.png') }}" alt="Logo2" class="w-12 h-12 sm:w-16 sm:h-16 object-contain" />
      </div>
    </div>
  </header>

  <!-- Login Card -->
  <div class="relative z-10 flex flex-1 items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
      <p class="font-georgia text-2xl font-bold text-center text-blue-700 mb-2">Welcome!</p>
      <p class="font-georgia text-center text-gray-600 mb-6">Sign in to continue</p>

      @if(session('error'))
        <div class="bg-red-100 text-red-700 text-sm p-2 mb-4 rounded">
          {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
        @csrf

        <!-- USERNAME -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username:</label>
          <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400" />
          @error('username')
          <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
          <div class="relative">
            <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 pr-10" />
            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-600">
              <span id="toggleIcon">Show</span>
            </button>
          </div>
          @error('password')
          <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-200">
          LOGIN
        </button>
      </form>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleIcon.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    function updateDateTime() {
      const now = new Date();
      document.getElementById("datetime").textContent = now.toLocaleString("en-US", {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', hour12: true
      });
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
  </script>
</body>
</html>
