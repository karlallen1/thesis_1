<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#F9F3EF] min-h-screen flex">
    <!-- Sidebar -->
    <aside class="text-white w-64 flex flex-col min-h-screen border-r border-gray-100" style="background-color: #1B3C53;">
            <!-- Top Section -->
            <div class="p-6 border-b border-[#244C66]">
                <h1 class="text-2xl font-georgia font-bold">
                        @if(session('role') === 'main_admin')
                            NCC Admin
                        @else
                            NCC Staff
                        @endif
                </h1>
                <p class="text-sm text-gray-300 mt-3">
                    Welcome, {{ session('username') }}<br>
                    <span class="text-xs bg-blue-600 px-2 py-1 rounded ml-2">
                        {{ str_replace('_', ' ', ucwords(session('role'))) }}
                    </span>
                </p>
            </div>


        <!-- Navigation Links -->
        <nav class="flex-1 px-8 py-6 space-y-6">
            <a href="{{ route('admin.dashboard-main') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.dashboard-main') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">Dashboard</a>
            @if(session('role') === 'main_admin')
            <a href="{{ route('admin.usermanagement') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">User Management</a>
            @endif
            <a href="{{ route('admin.queuestatus') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">Queue Status</a>
            <a href="{{ route('admin.systemlogs') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">System Logs</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col bg-[#c0c0ca]">
        <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4] flex justify-between items-center">
            <h1 class="text-2xl font-georgia font-semibold">User Management</h1>
            @if(session('role') === 'main_admin')
            <button id="openAddModalBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition">+ Add Account</button>
            @else
            <div class="text-sm text-gray-600 bg-yellow-100 px-3 py-2 rounded">
                <strong>Staff Access:</strong> View only - Contact Main Admin
            </div>
            @endif
        </header>

        <section class="px-8 py-6 space-y-4">
            <div class="flex justify-between items-center">
                <input type="text" id="searchInput" placeholder="Search by username..." class="px-4 py-2 border rounded w-1/2 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]" />
            </div>
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Username</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                            @if(session('role') === 'main_admin')
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="userTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="modals"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseURL = "{{ route('admin.users.index') }}"; // → /admin/users
        const currentUserRole = "{{ session('role') }}";

        async function safeFetch(url, options = {}) {
            try {
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...options.headers
                    },
                    credentials: 'same-origin'
                });

                const contentType = response.headers.get('content-type');
                if (contentType && !contentType.includes('application/json')) {
                    const text = await response.text();
                    if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        console.error('HTML response:', text.substring(0, 200));
                        throw new Error('Session expired or server error');
                    }
                }

                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || Object.values(errorData.errors || {}).flat().join(', ') || errorMessage;
                    } catch (e) {
                        // If we can't parse JSON, use the status text
                        if (response.status === 404) {
                            errorMessage = 'Resource not found';
                        } else if (response.status === 422) {
                            errorMessage = 'Invalid data provided';
                        } else if (response.status === 500) {
                            errorMessage = 'Server error occurred';
                        }
                    }
                    throw new Error(errorMessage);
                }

                return await response.json();
            } catch (error) {
                console.error('Fetch error:', error);
                throw error;
            }
        }

        function loadUsers() {
            @if(session('role') === 'main_admin')
            safeFetch(baseURL)
                .then(data => {
                    console.log('Loaded users:', data); // Debug log
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const filtered = data.filter(u => u.username.toLowerCase().includes(search));
                    const tbody = document.getElementById('userTableBody');
                    
                    if (filtered.length === 0) {
                        tbody.innerHTML = `
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <div class="text-lg font-georgia">No users found</div>
                                <div class="text-sm mt-2">Try adjusting your search</div>
                            </td></tr>`;
                        return;
                    }
                    
                    tbody.innerHTML = filtered.map(u => `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-3">${u.username}</td>
                            <td class="px-6 py-3">
                                <span class="capitalize px-2 py-1 rounded text-xs font-semibold
                                    ${u.role === 'main_admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'}">
                                    ${u.role.replace('_', ' ')}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-3">
                                <button onclick="openChangePw('${u.id}', '${u.username}')" class="text-blue-600 hover:underline text-sm">Change Password</button>
                                ${u.role !== 'main_admin' ? `
                                <button onclick="confirmDelete('${u.id}', '${u.username}')" class="text-red-600 hover:underline text-sm">Delete</button>
                                ` : '<span class="text-gray-400 text-sm">Protected</span>'}
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(err => {
                    console.error('Load failed:', err);
                    const tbody = document.getElementById('userTableBody');
                    tbody.innerHTML = `
                        <tr><td colspan="3" class="px-6 py-8 text-center text-red-500">
                            <div class="text-lg font-georgia">Error loading users</div>
                            <div class="text-sm mt-2">${err.message}</div>
                        </td></tr>`;
                    
                    if (err.message.includes('expired')) {
                        alert('Session expired. Please log in again.');
                        window.location.href = '{{ route("admin.login") }}';
                    } else {
                        showNotification('Load failed: ' + err.message, 'error');
                    }
                });
            @else
            document.getElementById('userTableBody').innerHTML = `
                <tr><td colspan="2" class="px-6 py-8 text-center text-gray-500">
                    <div class="text-lg font-georgia">Staff Access Limited</div>
                    <div class="text-sm mt-2">Contact Main Admin to manage accounts</div>
                </td></tr>`;
            @endif
        }

        function openChangePw(id, username) {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Change Password</h2>
                        <form onsubmit="event.preventDefault(); updatePassword('${id}');">
                            <p class="mb-4 text-sm text-gray-600">For: <strong>${username}</strong></p>
                            <input type="password" id="newPw" placeholder="New password (min 6)"
                                   class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-[#1B3C53]"
                                   minlength="6" required />
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">Cancel</button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Update</button>
                            </div>
                        </form>
                    </div>
                </div>`;
            document.getElementById("modals").innerHTML = html;
        }

        function updatePassword(id) {
            const pw = document.getElementById('newPw').value;
            if (pw.length < 6) return showNotification('Min 6 chars', 'error');

            // Use correct password update route
            safeFetch(`${baseURL}/${id}/password`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    password: pw
                })
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('Password updated!', 'success');
            })
            .catch(err => {
                console.error('Password update error:', err);
                showNotification('Update failed: ' + err.message, 'error');
            });
        }

        function confirmDelete(id, username) {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Delete Account</h2>
                        <p class="mb-6 text-gray-600">Delete <strong>${username}</strong>? This cannot be undone.</p>
                        <div class="flex justify-end gap-3">
                            <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">Cancel</button>
                            <button onclick="deleteUser('${id}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">Delete</button>
                        </div>
                    </div>
                </div>`;
            document.getElementById("modals").innerHTML = html;
        }

        function deleteUser(id) {
            safeFetch(`${baseURL}/${id}`, { 
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('User deleted!', 'success');
            })
            .catch(err => {
                console.error('Delete error:', err);
                showNotification('Delete failed: ' + err.message, 'error');
            });
        }

        function closeModal() {
            document.getElementById("modals").innerHTML = "";
        }

        @if(session('role') === 'main_admin')
        document.getElementById('openAddModalBtn').addEventListener('click', () => {
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn">
                    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Add New Account</h2>
                        <form onsubmit="event.preventDefault(); createUser();">
                            <div class="space-y-4">
                                <input type="text" id="addUsername" placeholder="Username" class="w-full border rounded px-3 py-2 focus:outline-none" required />
                                <input type="password" id="addPassword" placeholder="Password (min 6)" class="w-full border rounded px-3 py-2 focus:outline-none" minlength="6" required />
                                <select id="addRole" class="w-full border rounded px-3 py-2 focus:outline-none" required>
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="main_admin">Main Admin</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">Cancel</button>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Create</button>
                            </div>
                        </form>
                    </div>
                </div>`;
            document.getElementById("modals").innerHTML = html;
        });

        function createUser() {
            const username = document.getElementById('addUsername').value.trim();
            const password = document.getElementById('addPassword').value;
            const role = document.getElementById('addRole').value;

            if (password.length < 6) {
                return showNotification('Password too short', 'error');
            }

            safeFetch(baseURL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password, role })
            })
            .then(() => {
                closeModal();
                loadUsers();
                showNotification('User created!', 'success');
            })
            .catch(err => {
                showNotification('Create failed: ' + err.message, 'error');
            });
        }
        @endif

        document.getElementById('searchInput').addEventListener('input', loadUsers);
        window.onload = loadUsers;

        function showNotification(message, type = 'success') {
            const notif = document.createElement('div');
            notif.className = `fixed top-4 right-4 px-4 py-2 rounded text-white text-sm font-semibold z-50 transition-opacity duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            notif.textContent = message;
            document.body.appendChild(notif);
            setTimeout(() => notif.style.opacity = '0', 3000);
            setTimeout(() => notif.remove(), 3500);
        }
    </script>
</body>
</html>