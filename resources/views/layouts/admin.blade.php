<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans leading-normal tracking-normal">
  <nav class="bg-white shadow p-4">
    <div class="container mx-auto flex justify-between">
      <span class="text-xl font-semibold text-blue-700">Admin Panel</span>
      <span class="text-gray-600">Logged in as: {{ session('username') }} ({{ session('role') }})</span>
    </div>
  </nav>

  <main class="container mx-auto py-8">
    @yield('content')
  </main>
</body>
</html>
