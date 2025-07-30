<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Queue Status - Admin Panel</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        .pulse { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-[#1B3C53] text-white flex flex-col">
            <!-- Top Section -->
            <div class="flex items-center px-6 h-20 border-b border-[#244C66]">
                <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
                    <img src="{{ asset('img/admin.png') }}" alt="Admin Icon" class="w-8 h-8" />
                </div>
                <div class="ml-4 flex flex-col leading-tight">
                    <span class="text-sm font-semibold">NCC Admin</span>
                    <span class="text-xs text-gray-200">Head Admin</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-8 py-6 space-y-6">
                <a href="{{ route('admin.dashboard-main') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.dashboard-main') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.usermanagement') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    User Management
                </a>
                <a href="{{ route('admin.queuestatus') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    Queue Status
                </a>
                <a href="{{ route('admin.systemlogs') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    System Logs
                </a>
            </nav>


        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-[#c0c0ca]">

            <!-- Header -->
            <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4] flex justify-between items-center">
                <h1 class="text-2xl font-georgia font-semibold">Queue Status</h1>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <div class="w-2 h-2 bg-green-500 rounded-full pulse"></div>
                    <span>Live Data</span>
                </div>
            </header>

            <!-- Now Serving -->
            <section class="px-8 py-6">
                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-xl font-georgia font-semibold mb-4">Now Serving</h2>
                    <div id="nowServing" class="text-gray-500">No one being served</div>

                    <!-- Now Serving Details Section -->
                    <div id="nowServingDetailsSection" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border">
                        <h3 class="font-georgia font-semibold mb-3">Client Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>ID:</strong> <span id="detailId">--</span></div>
                            <div><strong>Queue #:</strong> <span id="detailQueueNumber">--</span></div>
                            <div><strong>Name:</strong> <span id="detailFullName">--</span></div>
                            <div><strong>Service:</strong> <span id="detailServiceType">--</span></div>
                            <div><strong>Email:</strong> <span id="detailEmail">--</span></div>
                            <div><strong>Contact:</strong> <span id="detailContact">--</span></div>
                            <div><strong>Birthdate:</strong> <span id="detailBirthdate">--</span></div>
                            <div><strong>Age:</strong> <span id="detailAge">--</span></div>
                            <div><strong>PWD:</strong> <span id="pwdStatusBadge" class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">No</span></div>
                            <div><strong>PWD ID:</strong> <span id="detailPwdId">--</span></div>
                            <div><strong>Senior ID:</strong> <span id="detailSeniorId">--</span></div>
                            <div><strong>Priority Type:</strong> <span id="priorityTypeBadge" class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">Regular</span></div>
                            <div><strong>Entry Type:</strong> <span id="detailEntryType">--</span></div>
                            <div><strong>Entered At:</strong> <span id="detailEnteredAt">--</span></div>
                            <div><strong>Status:</strong> <span id="detailStatus">--</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Queue Actions -->
            <section class="px-8 py-2 flex flex-wrap gap-3">
                <button id="nextBtn" onclick="markNextAsServed()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold transition">Next Number</button>
                <button id="completeNowBtn" onclick="completeNowServing()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold transition">Complete Now</button>
                <button id="cancelNowBtn" onclick="cancelNowServing()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition">Cancel Now</button>
                <button id="requeueNowBtn" onclick="requeueNowServing()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded font-semibold transition">Requeue Now</button>
            </section>

            <!-- Queue Counts -->
            <section class="px-8 py-2 text-sm text-gray-600">
                <span>Priority: <strong id="priorityCount">0</strong></span>
                <span class="ml-4">Regular: <strong id="regularCount">0</strong></span>
                <span class="ml-4">Cancelled Today: <strong id="cancelledCount">0</strong></span>
            </section>

            <!-- Priority Queue -->
            <section class="px-8 py-6">
                <h2 class="text-xl font-georgia font-semibold mb-4">Priority Queue</h2>
                <table class="w-full bg-white rounded-xl shadow overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Queue #</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Service</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="priorityQueueBody"></tbody>
                </table>
            </section>

            <!-- Regular Queue -->
            <section class="px-8 py-6">
                <h2 class="text-xl font-georgia font-semibold mb-4">Regular Queue</h2>
                <table class="w-full bg-white rounded-xl shadow overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Queue #</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Service</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="regularQueueBody"></tbody>
                </table>
            </section>

        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Confirm Action</h3>
            <p class="mb-6">Are you sure you want to perform this action?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-sm font-semibold transition">Cancel</button>
                <button id="confirmBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-semibold transition">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="hidden fixed top-4 right-4 px-4 py-2 rounded text-white text-sm font-semibold z-50 transition-opacity duration-300">
        Action successful!
    </div>

    <!-- JavaScript -->
    <script>
        // Global variable to store the action to confirm
        let confirmAction = null;

        // Show confirmation modal
        function showConfirmation(action) {
            confirmAction = action;
            document.getElementById('confirmationModal').classList.remove('hidden');
            document.getElementById('confirmationModal').classList.add('flex');
        }

        // Close modal
        function closeModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
            document.getElementById('confirmationModal').classList.remove('flex');
            confirmAction = null;
        }

        // Attach event listener to confirm button
        document.getElementById('confirmBtn').addEventListener('click', () => {
            if (confirmAction) confirmAction();
            closeModal();
        });

        // Helper function for safe DOM updates
        function safeUpdateElementText(elementId, textContent) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = textContent ?? '--';
            } else {
                console.warn(`Element with ID '${elementId}' not found.`);
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notif = document.getElementById('notification');
            notif.textContent = message;
            notif.className = `fixed top-4 right-4 px-4 py-2 rounded text-white text-sm font-semibold z-50 transition-opacity duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            notif.classList.remove('hidden');
            setTimeout(() => { notif.style.opacity = '0'; }, 2000);
            setTimeout(() => { notif.classList.add('hidden'); notif.style.opacity = '1'; }, 2500);
        }

        // CSRF Token (only if present)
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

        if (!csrfToken) {
            console.warn('CSRF token not found. API requests may fail.');
        }

        // ✅ ENHANCED: Make request with error handling AND dashboard sync
        async function makeRequest(url, options = {}) {
            const config = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                ...options
            };

            // Add CSRF token only if available
            if (csrfToken) {
                config.headers['X-CSRF-TOKEN'] = csrfToken;
            }

            try {
                const response = await fetch(url, config);
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
                const data = await response.json();

                // ✅ Refresh queue data immediately
                await fetchQueueData();
                
                // ✅ IMPORTANT: Sync with dashboard in real-time
                if (typeof window.refreshDashboardStats === 'function') {
                    console.log('Syncing dashboard stats after queue action...');
                    window.refreshDashboardStats();
                } else {
                    // If dashboard function not available, try direct API call
                    console.log('Dashboard function not found, triggering manual refresh...');
                    try {
                        await fetch('/admin/dashboard-stats');
                    } catch (err) {
                        console.warn('Dashboard manual refresh failed:', err);
                    }
                }

                showNotification(data.message || 'Action successful!');
                return data;
            } catch (error) {
                console.error('Request failed:', error);
                showNotification('Action failed. Please try again.', 'error');
                await fetchQueueData(); // Refresh anyway
                throw error;
            }
        }

        // Queue Actions - Enhanced with dashboard sync
        function markNextAsServed() { 
            console.log('Calling next person...');
            makeRequest("/admin/queue/next"); 
        }
        
        function completeNowServing() { 
            console.log('Completing current service...');
            makeRequest("/admin/queue/complete-now"); 
        }
        
        function cancelNowServing() { 
            showConfirmation(() => {
                console.log('Cancelling current service...');
                makeRequest("/admin/queue/cancel-now");
            }); 
        }
        
        function requeueNowServing() { 
            showConfirmation(() => {
                console.log('Requeueing current service...');
                makeRequest("/admin/queue/requeue-now");
            }); 
        }

        function complete(id) { 
            showConfirmation(() => {
                console.log('Completing application ID:', id);
                makeRequest(`/admin/queue/${id}/complete`);
            }); 
        }
        
        function cancel(id) { 
            showConfirmation(() => {
                console.log('Cancelling application ID:', id);
                makeRequest(`/admin/queue/${id}/cancel`);
            }); 
        }
        
        function requeue(id) { 
            showConfirmation(() => {
                console.log('Requeueing application ID:', id);
                makeRequest(`/admin/queue/${id}/requeue`);
            }); 
        }

        // Fetch queue data
        async function fetchQueueData() {
            try {
                const res = await fetch('/admin/queue');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                // Update Now Serving
                const nowServingEl = document.getElementById('nowServing');
                if (data.now_serving) {
                    nowServingEl.textContent = `Queue #${data.now_serving.queue_number} - ${data.now_serving.full_name}`;
                    nowServingEl.classList.add('text-blue-600', 'font-semibold');
                    document.getElementById('nowServingDetailsSection').classList.remove('hidden');

                    // Update details
                    safeUpdateElementText("detailId", data.now_serving.id ?? '--');
                    safeUpdateElementText("detailQueueNumber", data.now_serving.queue_number ?? '--');
                    safeUpdateElementText("detailFullName", data.now_serving.full_name ?? '--');
                    safeUpdateElementText("detailServiceType", data.now_serving.service_type ?? '--');
                    safeUpdateElementText("detailEmail", data.now_serving.email ?? '--');
                    safeUpdateElementText("detailContact", data.now_serving.contact ?? '--');
                    safeUpdateElementText("detailBirthdate", data.now_serving.birthdate ?? '--');
                    safeUpdateElementText("detailAge", data.now_serving.age ?? '--');
                    safeUpdateElementText("detailPwdId", data.now_serving.pwd_id ?? '--');
                    safeUpdateElementText("detailSeniorId", data.now_serving.senior_id ?? '--');
                    safeUpdateElementText("detailEntryType", data.now_serving.entry_type ?? '--');
                    safeUpdateElementText("detailEnteredAt", data.now_serving.queue_entered_at ?? '--');
                    safeUpdateElementText("detailStatus", data.now_serving.status ?? '--');

                    // PWD Badge
                    const pwdBadge = document.getElementById("pwdStatusBadge");
                    if (pwdBadge) {
                        pwdBadge.textContent = data.now_serving.is_pwd ? "Yes" : "No";
                        pwdBadge.className = data.now_serving.is_pwd 
                            ? "px-2 py-1 rounded-full text-xs bg-green-100 text-green-800" 
                            : "px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800";
                    }

                    // Priority Type Badge
                    const priorityBadge = document.getElementById("priorityTypeBadge");
                    const priorityType = data.now_serving.priority_type;
                    if (priorityBadge && priorityType) {
                        priorityBadge.textContent = priorityType;
                        priorityBadge.className = priorityType === 'PWD'
                            ? "px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800"
                            : priorityType === 'Senior Citizen'
                                ? "px-2 py-1 rounded-full text-xs bg-green-100 text-green-800"
                                : "px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800";
                    }
                } else {
                    nowServingEl.textContent = 'No one being served';
                    nowServingEl.classList.remove('text-blue-600', 'font-semibold');
                    document.getElementById('nowServingDetailsSection').classList.add('hidden');
                }

                // Update counts
                safeUpdateElementText("cancelledCount", data.cancelled ?? 0);
                safeUpdateElementText("priorityCount", data.priority?.length ?? 0);
                safeUpdateElementText("regularCount", data.regular?.length ?? 0);

                // Update priority queue table
                const priorityBody = document.getElementById("priorityQueueBody");
                if (priorityBody && data.priority && Array.isArray(data.priority)) {
                    priorityBody.innerHTML = data.priority.map(p => `
                        <tr class="border-t fade-in">
                            <td class="px-6 py-3">${p.queue_number}</td>
                            <td class="px-6 py-3">${p.name}</td>
                            <td class="px-6 py-3">${p.service_type}</td>
                            <td class="px-6 py-3 space-x-2">
                                <button onclick="complete('${p.id}')" class="text-green-600 hover:underline text-sm">Complete</button>
                                <button onclick="cancel('${p.id}')" class="text-red-600 hover:underline text-sm">Cancel</button>
                                <button onclick="requeue('${p.id}')" class="text-yellow-600 hover:underline text-sm">Requeue</button>
                            </td>
                        </tr>
                    `).join('');
                } else if (priorityBody) {
                    priorityBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No one in priority queue</td></tr>';
                }

                // Update regular queue table
                const regularBody = document.getElementById("regularQueueBody");
                if (regularBody && data.regular && Array.isArray(data.regular)) {
                    regularBody.innerHTML = data.regular.map(r => `
                        <tr class="border-t fade-in">
                            <td class="px-6 py-3">${r.queue_number}</td>
                            <td class="px-6 py-3">${r.name}</td>
                            <td class="px-6 py-3">${r.service_type}</td>
                            <td class="px-6 py-3 space-x-2">
                                <button onclick="complete('${r.id}')" class="text-green-600 hover:underline text-sm">Complete</button>
                                <button onclick="cancel('${r.id}')" class="text-red-600 hover:underline text-sm">Cancel</button>
                                <button onclick="requeue('${r.id}')" class="text-yellow-600 hover:underline text-sm">Requeue</button>
                            </td>
                        </tr>
                    `).join('');
                } else if (regularBody) {
                    regularBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No one in regular queue</td></tr>';
                }

            } catch (error) {
                console.error('Failed to fetch queue data:', error);
            }
        }

        // ✅ Make queue refresh function globally available for dashboard
        window.refreshQueueData = fetchQueueData;

        // Initial load and auto-refresh
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Queue Status page loaded, starting data fetch...');
            fetchQueueData();
            setInterval(fetchQueueData, 5000); // Refresh every 5 seconds
        });
    </script>
</body>
</html>