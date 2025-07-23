<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Management - Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .font-georgia {
      font-family: Georgia, 'Times New Roman', Times, serif;
    }
  </style>
</head>
<body class="bg-[#F9F3EF] min-h-screen flex">
  <!-- Sidebar -->
  <aside class="text-white w-64 flex flex-col min-h-screen border-r border-gray-100" style="background-color: #57768c;">
    <div class="flex items-center px-6 h-20" style="background-color: #1B3C53;">
      <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
        <img src="{{ asset('img/admin.png') }}" alt="Admin Icon" class="w-8 h-8" />
      </div>
      <div class="ml-4">
        <span class="text-base font-semibold text-white">Main Admin</span>
      </div>
    </div>

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

    <div class="mt-auto px-6 py-1" style="background-color: #1B3C53;">
      <form action="{{ route('admin.logout') }}" method="GET">
        <button type="submit" class="w-full bg-[#244C66] hover:bg-[#183345] text-white py-1 rounded text-sm font-semibold transition">
          Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col bg-[#c0c0ca]">
    <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4]">
      <h1 class="text-2xl font-georgia font-semibold">User Management</h1>
    </header>

    <section class="px-8 py-6">
      <div class="flex justify-between mb-4">
        <input type="text" id="searchInput" placeholder="Search by username..." class="px-4 py-2 border rounded w-1/2" />
        <button id="openAddModalBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Account</button>
      </div>

      <table class="w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 text-left">Username</th>
            <th class="px-4 py-2 text-left">Role</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="userTableBody">
          <!-- Fetched dynamically from /admin/users -->
        </tbody>
      </table>
    </section>
  </main>

  <!-- Add/Edit/Delete Modals (JS handles visibility) -->
  <div id="modals"></div>

  <script>
    const baseURL = "/admin/users";

    function loadUsers() {
      fetch(baseURL)
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('userTableBody');
          tbody.innerHTML = data.map(user => `
            <tr class="border-t">
              <td class="px-4 py-2">${user.username}</td>
              <td class="px-4 py-2 capitalize">${user.role.replace('_', ' ')}</td>
              <td class="px-4 py-2 space-x-2">
                <button onclick="openChangePw('${user.id}', '${user.username}')" class="text-blue-600 hover:underline">Change Password</button>
                <button onclick="confirmDelete('${user.id}', '${user.username}')" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>`).join('');
        });
    }

    function openChangePw(id, username) {
      const html = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow-xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>
            <form onsubmit="event.preventDefault(); updatePassword('${id}');">
              <p class="mb-2 text-sm">Change password for: <strong>${username}</strong></p>
              <input type="password" id="newPw" placeholder="New password" class="w-full border rounded p-2 mb-4" required />
              <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
              </div>
            </form>
          </div>
        </div>`;
      document.getElementById("modals").innerHTML = html;
    }

    function updatePassword(id) {
      const pw = document.getElementById('newPw').value;
      fetch(`${baseURL}/${id}/password`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ password: pw })
      }).then(loadUsers).then(closeModal);
    }

    function confirmDelete(id, username) {
      const html = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow-xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Delete Account</h2>
            <p class="mb-4">Delete <strong>${username}</strong>? This cannot be undone.</p>
            <div class="flex justify-end gap-2">
              <button onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
              <button onclick="deleteUser('${id}')" class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
            </div>
          </div>
        </div>`;
      document.getElementById("modals").innerHTML = html;
    }

    function deleteUser(id) {
      fetch(`${baseURL}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
      }).then(loadUsers).then(closeModal);
    }

    function closeModal() {
      document.getElementById("modals").innerHTML = "";
    }

    document.getElementById('openAddModalBtn').addEventListener('click', () => {
      const html = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow-xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Add New Account</h2>
            <form onsubmit="event.preventDefault(); createUser();">
              <input type="text" id="addUsername" placeholder="Username" class="w-full border rounded p-2 mb-2" required />
              <input type="password" id="addPassword" placeholder="Password" class="w-full border rounded p-2 mb-2" required />
              <select id="addRole" class="w-full border rounded p-2 mb-4" required>
                <option value="" disabled selected>Select Role</option>
                <option value="main_admin">Main Admin</option>
                <option value="staff">Staff</option>
              </select>
              <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create</button>
              </div>
            </form>
          </div>
        </div>`;
      document.getElementById("modals").innerHTML = html;
    });

    function createUser() {
      fetch(baseURL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          username: document.getElementById('addUsername').value,
          password: document.getElementById('addPassword').value,
          role: document.getElementById('addRole').value
        })
      }).then(loadUsers).then(closeModal);
    }

    // Load users on page load
    window.onload = loadUsers;
  </script>
</body>
</html>
