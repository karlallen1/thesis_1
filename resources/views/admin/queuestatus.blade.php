<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Queue Status - Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .font-georgia {
      font-family: Georgia, 'Times New Roman', Times, serif;
    }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#F9F3EF] min-h-screen flex relative">

  <!-- Modal -->
  <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded shadow-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Confirmation</h2>
      <p id="modalMessage" class="mb-6">Are you sure?</p>
      <div class="flex justify-end space-x-2">
        <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
        <button id="confirmBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Confirm</button>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <aside class="text-white w-64 flex flex-col min-h-screen border-r border-gray-100" style="background-color: #57768c;">
    <div class="flex items-center px-6 h-20 bg-[#1B3C53]">
      <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
        <img src="{{ asset('img/admin.png') }}" alt="Admin Icon" class="w-8 h-8" />
      </div>
      <div class="ml-4 flex flex-col leading-tight">
        <span class="text-base font-semibold">Head Admin</span>
      </div>
    </div>

    <nav class="flex-1 px-8 py-6 space-y-6">
      <a href="{{ route('admin.dashboard') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.dashboard') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">Dashboard</a>
      <a href="{{ route('admin.usermanagement') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">User Management</a>
      <a href="{{ route('admin.queuestatus') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">Queue Status</a>
      <a href="{{ route('admin.systemlogs') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">System Logs</a>
    </nav>

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
    <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4]">
      <h1 class="text-2xl font-georgia font-semibold">Queue Status</h1>
    </header>

    <!-- Queue Section -->
    <section class="px-8 py-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded shadow text-center">
          <p class="font-bold text-gray-600">Now Serving</p>
          <p class="text-3xl text-green-600 font-semibold" id="nowServing">--</p>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
          <p class="font-bold text-gray-600">Next</p>
          <p class="text-3xl text-blue-600 font-semibold" id="nextServing">--</p>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
          <p class="font-bold text-gray-600">Cancelled</p>
          <p class="text-3xl text-red-600 font-semibold" id="cancelledCount">--</p>
        </div>
      </div>

      <div class="flex justify-end space-x-2">
        <button onclick="showModal('Proceed to call the next number?', markNextAsServed)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Next Number</button>
        <button onclick="showModal('Are you sure you want to cancel the current number?', cancelNowServing)" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Cancel Now Serving</button>
        <button onclick="showModal('Requeue the current number to a later position?', requeueNowServing)" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Requeue Now Serving</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h2 class="text-xl font-georgia font-semibold mb-2">PWD / Senior Queue</h2>
          <table class="w-full bg-white rounded shadow overflow-hidden">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Queue #</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Service</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody id="priorityQueueBody"></tbody>
          </table>
        </div>

        <div>
          <h2 class="text-xl font-georgia font-semibold mb-2">Regular Queue</h2>
          <table class="w-full bg-white rounded shadow overflow-hidden">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Queue #</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Service</th>
                <th class="px-4 py-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody id="regularQueueBody"></tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script>
    let confirmAction = null;

    function showModal(message, action) {
      document.getElementById('modalMessage').textContent = message;
      document.getElementById('confirmationModal').classList.remove('hidden');
      document.getElementById('confirmationModal').classList.add('flex');
      confirmAction = action;
    }

    function closeModal() {
      document.getElementById('confirmationModal').classList.add('hidden');
      document.getElementById('confirmationModal').classList.remove('flex');
      confirmAction = null;
    }

    document.getElementById('confirmBtn').addEventListener('click', () => {
      if (confirmAction) confirmAction();
      closeModal();
    });

    function fetchQueueData() {
      fetch("/admin/queue")
        .then(res => res.json())
        .then(data => {
          document.getElementById("nowServing").textContent = data.now_serving?.queue_number ?? '--';
          document.getElementById("nextServing").textContent = data.next ?? '--';
          document.getElementById("cancelledCount").textContent = data.cancelled ?? 0;

          document.getElementById("priorityQueueBody").innerHTML = data.priority.map(p => `
            <tr class="border-t">
              <td class="px-4 py-2">${p.queue_number}</td>
              <td class="px-4 py-2">${p.name}</td>
              <td class="px-4 py-2">${p.service_type}</td>
              <td class="px-4 py-2">${p.is_pwd ? 'PWD' : 'Senior'}</td>
              <td class="px-4 py-2 space-x-2">
                <button onclick="showModal('Mark this applicant as completed?', () => complete(${p.id}))" class="text-green-600 hover:underline">Complete</button>
                <button onclick="showModal('Cancel this applicant?', () => cancel(${p.id}))" class="text-red-600 hover:underline">Cancel</button>
                <button onclick="showModal('Requeue this applicant?', () => requeue(${p.id}))" class="text-yellow-600 hover:underline">Requeue</button>
              </td>
            </tr>`).join('');

          document.getElementById("regularQueueBody").innerHTML = data.regular.map(r => `
            <tr class="border-t">
              <td class="px-4 py-2">${r.queue_number}</td>
              <td class="px-4 py-2">${r.name}</td>
              <td class="px-4 py-2">${r.service_type}</td>
              <td class="px-4 py-2 space-x-2">
                <button onclick="showModal('Mark this applicant as completed?', () => complete(${r.id}))" class="text-green-600 hover:underline">Complete</button>
                <button onclick="showModal('Cancel this applicant?', () => cancel(${r.id}))" class="text-red-600 hover:underline">Cancel</button>
                <button onclick="showModal('Requeue this applicant?', () => requeue(${r.id}))" class="text-yellow-600 hover:underline">Requeue</button>
              </td>
            </tr>`).join('');
        });
    }

    function markNextAsServed() {
      fetch("/admin/queue/next", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    function cancelNowServing() {
      fetch("/admin/queue/cancel-now", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    function requeueNowServing() {
      fetch("/admin/queue/requeue-now", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    function complete(id) {
      fetch(`/admin/queue/${id}/complete`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    function cancel(id) {
      fetch(`/admin/queue/${id}/cancel`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    function requeue(id) {
      fetch(`/admin/queue/${id}/requeue`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      }).then(fetchQueueData);
    }

    window.onload = fetchQueueData;
    setInterval(fetchQueueData, 5000);
  </script>
</body>
</html>
