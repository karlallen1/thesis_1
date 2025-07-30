<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        .active-link {
            font-weight: bold;
            text-decoration: underline;
            opacity: 1 !important;
            filter: none !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#F9F3EF] min-h-screen flex">

    <!-- Sidebar -->
<!-- Sidebar -->
<aside class="text-white w-64 flex flex-col min-h-screen border-r border-gray-100" style="background-color: #1B3C53;">
    <!-- Top Section with Admin Icon -->
<div class="flex items-center px-6 h-20 bg-[#1B3C53] border-b border-[#244C66]">
            <div class="p-6 border-b border-[#244C66]">
                <h1 class="text-2xl font-georgia font-bold">NCC Admin</h1>
            </div>
</div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-8 py-6 space-y-6">
        <a href="{{ route('admin.dashboard-main') }}"
           class="block text-xl font-georgia text-white transition
           {{ request()->routeIs('admin.dashboard-main') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.usermanagement') }}"
           class="block text-xl font-georgia text-white transition
           {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
            User Management
        </a>
        <a href="{{ route('admin.queuestatus') }}"
           class="block text-xl font-georgia text-white transition
           {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
            Queue Status
        </a>
        <a href="{{ route('admin.systemlogs') }}"
           class="block text-xl font-georgia text-white transition
           {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
            System Logs
        </a>
    </nav>


</aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-[#c0c0ca]">

        <!-- Header -->
        <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4] flex justify-between items-center">
            <h1 class="text-2xl font-georgia font-semibold">User Management</h1>
            <button id="openAddModalBtn"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition">
                + Add Account
            </button>
        </header>

        <!-- Search & Table -->
        <section class="px-8 py-6 space-y-4">
            <div class="flex justify-between items-center">
                <input type="text" id="searchInput"
                       placeholder="Search by username..."
                       class="px-4 py-2 border rounded w-1/2 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]" />
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Username</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- Fetched dynamically -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modals Container -->
    <div id="modals"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseURL = "/admin/users";

        function loadUsers() {
            fetch(baseURL)
                .then(res => res.json())
                .then(data => {
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const filtered = data.filter(user => user.username.toLowerCase().includes(search));

                    const tbody = document.getElementById('userTableBody');
                    tbody.innerHTML = filtered.map(user => `
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-3">${user.username}</td>
                            <td class="px-6 py-3 capitalize">${user.role.replace('_', ' ')}</td>
                            <td class="px-6 py-3 space-x-3">
                                <button onclick="openChangePw('${user.id}', '${user.username}')"
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium">
                                    Change Password
                                </button>
                                <button onclick="confirmDelete('${user.id}', '${user.username}')"
                                        class="text-red-600 hover:text-red-800 hover:underline text-sm font-medium">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(err => console.error('Failed to load users:', err));
        }

        function openChangePw(id, username) {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Change Password</h2>
                        <form onsubmit="event.preventDefault(); updatePassword('${id}');">
                            <p class="mb-4 text-sm text-gray-600">Change password for: <strong>${username}</strong></p>
                            <input type="password" id="newPw" placeholder="New password"
                                   class="w-full border rounded-lg px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]"
                                   required />
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="closeModal()"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm font-semibold transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold transition">
                                    Update
                                </button>
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
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ password: pw })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('Password updated successfully!', 'success');
            })
            .catch(err => {
                console.error('Update failed:', err);
                showNotification('Update failed. Please try again.', 'error');
            });
        }

        function confirmDelete(id, username) {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Delete Account</h2>
                        <p class="mb-6 text-gray-600">Are you sure you want to delete <strong>${username}</strong>? This action cannot be undone.</p>
                        <div class="flex justify-end gap-3">
                            <button onclick="closeModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm font-semibold transition">
                                Cancel
                            </button>
                            <button onclick="deleteUser('${id}')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-semibold transition">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>`;
            document.getElementById("modals").innerHTML = html;
        }

        function deleteUser(id) {
            fetch(`${baseURL}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('User deleted successfully!', 'success');
            })
            .catch(err => {
                console.error('Delete failed:', err);
                showNotification('Delete failed. Please try again.', 'error');
            });
        }

        function closeModal() {
            document.getElementById("modals").innerHTML = "";
        }

        // Add Account Modal
        document.getElementById('openAddModalBtn').addEventListener('click', () => {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Add New Account</h2>
                        <form onsubmit="event.preventDefault(); createUser();">
                            <div class="space-y-4">
                                <input type="text" id="addUsername" placeholder="Username"
                                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]" required />
                                <input type="password" id="addPassword" placeholder="Password"
                                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]" required />
                                <select id="addRole" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]" required>
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="main_admin">Main Admin</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" onclick="closeModal()"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm font-semibold transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>`;
            document.getElementById("modals").innerHTML = html;
        });

        function createUser() {
            const username = document.getElementById('addUsername').value;
            const password = document.getElementById('addPassword').value;
            const role = document.getElementById('addRole').value;

            fetch(baseURL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ username, password, role })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('User created successfully!', 'success');
            })
            .catch(err => {
                console.error('Create failed:', err);
                showNotification('Failed to create user.', 'error');
            });
        }

        // Search Filter
        document.getElementById('searchInput').addEventListener('input', loadUsers);

        // Load users on page load
        window.onload = loadUsers;

        // Optional: Notification function
        function showNotification(message, type = 'success') {
            const notif = document.createElement('div');
            notif.className = `fixed top-4 right-4 px-4 py-2 rounded text-white text-sm font-semibold z-50 transition-opacity duration-300 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            notif.textContent = message;
            document.body.appendChild(notif);
            setTimeout(() => { notif.style.opacity = '0'; }, 2000);
            setTimeout(() => { notif.remove(); }, 2500);
        }
    </script>
</body>
</html>