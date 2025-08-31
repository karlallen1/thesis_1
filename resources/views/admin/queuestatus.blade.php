<!DOCTYPE html>
<html lang="en" x-data="queueApp()">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Queue Status - Admin Panel</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">

    <!-- Tailwind CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .font-georgia { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
        }
        
        .font-sans {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 50%, #e5e7eb 100%);
        }
        
        .sidebar-glass {
            background: rgba(27, 60, 83, 0.98);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 8px 0 32px rgba(0, 0, 0, 0.1);
        }
        
        .header-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .nav-link {
            border-radius: 12px;
            padding: 12px 16px;
            margin: 4px 0;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #F59E0B;
            font-weight: 600;
        }
        
        .queue-card {
            transition: all 0.3s ease;
        }
        .queue-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .content-text {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 sidebar-glass text-white flex flex-col fixed h-full z-40">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('img/mainlogo.png') }}" alt="City Hall Logo" class="w-10 h-10 object-contain" />
                    <h1 class="text-xl font-georgia font-bold">
                        @if(session('role') === 'admin' || session('role') === 'super_admin')
                            NCC Admin
                        @else
                            NCC Staff
                        @endif
                    </h1>
                </div>
                <div class="text-sm text-gray-300">
                    <p class="font-medium">Welcome, {{ session('username') }}</p>
                    <span class="inline-block text-xs bg-amber-600 px-3 py-1 rounded-full mt-2 font-medium">
                        {{ ucfirst(str_replace('_', ' ', session('role'))) }}
                    </span>
                </div>
            </div>
            
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard-main') }}" 
                   class="nav-link block font-georgia text-white {{ request()->routeIs('admin.dashboard-main') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Dashboard</span>
                    </div>
                </a>
                
                @if(session('role') === 'admin' || session('role') === 'super_admin')
                <a href="{{ route('admin.usermanagement') }}" 
                   class="nav-link block font-georgia text-white {{ request()->routeIs('admin.usermanagement') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>User Management</span>
                    </div>
                </a>
                @endif
                
                <a href="{{ route('admin.queuestatus') }}" 
                   class="nav-link block font-georgia text-white {{ request()->routeIs('admin.queuestatus') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Queue Status</span>
                    </div>
                </a>
                
                @if(session('role') === 'admin' || session('role') === 'super_admin')
                <a href="{{ route('admin.systemlogs') }}" 
                   class="nav-link block font-georgia text-white {{ request()->routeIs('admin.systemlogs') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>System Logs</span>
                    </div>
                </a>
                @endif
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col ml-64">
            <header class="header-glass sticky top-0 z-30 px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('img/philogo.png') }}" alt="PH Logo" class="w-12 h-12 object-contain drop-shadow-lg" />
                        <div>
                            <h1 class="text-2xl font-georgia font-bold text-gray-900">North Caloocan City Hall</h1>
                            <p class="text-sm text-gray-600">Queue Status Management</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-georgia font-semibold text-gray-900">{{ session('username') }}</p>
                        <p id="currentDateTime" class="text-xs text-gray-500"></p>
                    </div>
                </div>
            </header>
            
            <section class="px-8 py-6 flex flex-col gap-6">
                <!-- Top Row -->
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1 bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-georgia font-semibold mb-4">Now Serving</h2>
                        <div id="nowServing" class="text-gray-500">No one being served</div>
                        
                        <!-- Client Details Section -->
                        <div id="nowServingDetailsSection" class="hidden mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <h3 class="font-georgia font-semibold mb-3">Client Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm content-text">
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
                    
                    <!-- Button Group -->
                    <div class="flex flex-col gap-4 w-48">
                        <button id="nextBtn" @click="markNextAsServed()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition flex items-center justify-center text-sm content-text">
                            <i class="fas fa-step-forward mr-1"></i> Next Number
                        </button>
                        
                        <button id="completeNowBtn" @click="completeNowServing()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition flex items-center justify-center text-sm content-text">
                            <i class="fas fa-check-circle mr-1"></i> Complete Now
                        </button>

                        <button id="recallBtn" 
                                @click="recallNowServing()" 
                                class="bg-yellow-400 hover:bg-yellow-500 hover:shadow-lg shadow-md text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 flex items-center justify-center text-sm content-text"
                                x-bind:disabled="!nowServingActive">
                            <i class="fas fa-redo-alt mr-1"></i> Recall Number
                        </button>

                        <button id="requeueNowBtn" @click="requeueNowServing()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg font-medium transition flex items-center justify-center text-sm content-text">
                            <i class="fas fa-retweet mr-1"></i> Requeue Now
                        </button>

                        <button id="cancelNowBtn" @click="cancelNowServing()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-medium transition flex items-center justify-center text-sm content-text">
                            <i class="fas fa-times-circle mr-1"></i> Cancel Now
                        </button>
                    </div>
                </div>
                
                <!-- Bottom Row - Expanded Queue Tables -->
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Priority Queue -->
                    <div class="flex-1 bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="font-georgia text-xl font-semibold text-gray-700">Priority Queue</h2>
                            <div class="text-sm text-gray-600 content-text">
                                <span id="priorityCountDisplay">0</span> clients
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Queue #</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Name</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Service</th>
                                    </tr>
                                </thead>
                                <tbody id="priorityQueueBody" class="bg-white divide-y divide-gray-200 content-text">
                                    <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Regular Queue -->
                    <div class="flex-1 bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="font-georgia text-xl font-semibold text-gray-700">Regular Queue</h2>
                            <div class="text-sm text-gray-600 content-text">
                                <span id="regularCountDisplay">0</span> clients
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Queue #</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Name</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Service</th>
                                    </tr>
                                </thead>
                                <tbody id="regularQueueBody" class="bg-white divide-y divide-gray-200 content-text">
                                    <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 hidden flex items-center justify-center z-50">
        <div class="modal-overlay absolute inset-0" @click="closeModal()"></div>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md relative z-10 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-georgia font-semibold">Confirm Action</h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="mb-6 text-gray-700 content-text">Are you sure you want to perform this action?</p>
            <div class="flex justify-end gap-3">
                <button @click="closeModal()" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition content-text">
                    Cancel
                </button>
                <button @click="confirmPendingAction()" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition content-text">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="fixed top-4 right-4 hidden px-4 py-2 rounded-lg text-white text-sm font-semibold z-50 shadow-lg transition-opacity duration-300 content-text">
        Action successful!
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('queueApp', () => ({
                currentDateTime: '',
                pendingAction: null,
                nowServingActive: false,

                init() {
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);
                    this.fetchQueueData();
                    setInterval(() => this.fetchQueueData(), 5000);
                },

                updateDateTime() {
                    const now = new Date();
                    this.currentDateTime = now.toLocaleString("en-US", {
                        weekday: 'short', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                    document.getElementById('currentDateTime').textContent = this.currentDateTime;
                },

                async fetchQueueData() {
                    try {
                        const res = await fetch('/admin/queue');
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();

                        const nowServingEl = document.getElementById('nowServing');
                        if (data.now_serving) {
                            nowServingEl.textContent = `Queue #${data.now_serving.queue_number} - ${data.now_serving.full_name}`;
                            nowServingEl.classList.add('text-blue-600', 'font-semibold');
                            document.getElementById('nowServingDetailsSection').classList.remove('hidden');
                            this.nowServingActive = true;

                            // 👉 Populate all client details
                            Object.entries({
                                detailId: data.now_serving.id,
                                detailQueueNumber: data.now_serving.queue_number,
                                detailFullName: data.now_serving.full_name,
                                detailServiceType: data.now_serving.service_type,
                                detailEmail: data.now_serving.email,
                                detailContact: data.now_serving.contact,
                                detailBirthdate: data.now_serving.birthdate,
                                detailAge: data.now_serving.age,
                                detailPwdId: data.now_serving.pwd_id,
                                detailSeniorId: data.now_serving.senior_id,
                                detailEntryType: data.now_serving.entry_type,
                                detailEnteredAt: data.now_serving.queue_entered_at,
                                detailStatus: data.now_serving.status,
                            }).forEach(([id, value]) => {
                                const el = document.getElementById(id);
                                if (el) el.textContent = value || '--';
                            });

                            // Update PWD badge
                            const pwdBadge = document.getElementById("pwdStatusBadge");
                            if (pwdBadge) {
                                pwdBadge.textContent = data.now_serving.is_pwd ? "Yes" : "No";
                                pwdBadge.className = data.now_serving.is_pwd 
                                    ? "px-2 py-1 rounded-full text-xs bg-green-100 text-green-800" 
                                    : "px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800";
                            }

                            // Update Priority Type badge
                            const priorityBadge = document.getElementById("priorityTypeBadge");
                            if (priorityBadge && data.now_serving.priority_type) {
                                const type = data.now_serving.priority_type;
                                priorityBadge.textContent = type;
                                priorityBadge.className = 
                                    type === 'PWD'
                                        ? "px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800"
                                        : type === 'Senior'
                                            ? "px-2 py-1 rounded-full text-xs bg-green-100 text-green-800"
                                            : "px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800";
                            }
                        } else {
                            nowServingEl.textContent = 'No one being served';
                            nowServingEl.classList.remove('text-blue-600', 'font-semibold');
                            document.getElementById('nowServingDetailsSection').classList.add('hidden');
                            this.nowServingActive = false;
                        }

                        // Update queue counts
                        const priorityCount = data.priority?.length ?? 0;
                        const regularCount = data.regular?.length ?? 0;
                        document.getElementById("priorityCountDisplay").textContent = priorityCount;
                        document.getElementById("regularCountDisplay").textContent = regularCount;

                        // Update Priority Queue Table
                        const priorityBody = document.getElementById("priorityQueueBody");
                        if (priorityBody) {
                            priorityBody.innerHTML = priorityCount > 0
                                ? data.priority.map(p => `
                                    <tr class="queue-card hover:bg-gray-50">
                                        <td class="px-6 py-4">${p.queue_number}</td>
                                        <td class="px-6 py-4">${p.name}</td>
                                        <td class="px-6 py-4">${p.service_type}</td>
                                    </tr>
                                `).join('')
                                : '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No one in priority queue</td></tr>';
                        }

                        // Update Regular Queue Table
                        const regularBody = document.getElementById("regularQueueBody");
                        if (regularBody) {
                            regularBody.innerHTML = regularCount > 0
                                ? data.regular.map(r => `
                                    <tr class="queue-card hover:bg-gray-50">
                                        <td class="px-6 py-4">${r.queue_number}</td>
                                        <td class="px-6 py-4">${r.name}</td>
                                        <td class="px-6 py-4">${r.service_type}</td>
                                    </tr>
                                `).join('')
                                : '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No one in regular queue</td></tr>';
                        }
                    } catch (error) {
                        console.error('Failed to fetch queue data:', error);
                        this.showNotification('Failed to load queue data', 'error');
                    }
                },

                showNotification(message, type = 'success') {
                    const notif = document.getElementById('notification');
                    notif.textContent = message;
                    notif.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-semibold z-50 shadow-lg transition-opacity duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
                    notif.classList.remove('hidden');
                    setTimeout(() => notif.style.opacity = '0', 2000);
                    setTimeout(() => { notif.classList.add('hidden'); notif.style.opacity = '1'; }, 2500);
                },

                async makeRequest(url, options = {}) {
                    const config = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        ...options
                    };

                    try {
                        const response = await fetch(url, config);
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        const data = await response.json();
                        await this.fetchQueueData();
                        if (typeof window.refreshDashboardStats === 'function') window.refreshDashboardStats();

                        if (url === "/admin/queue/next" && data.success && data.now_serving?.queue_number) {
                            const queueNum = String(data.now_serving.queue_number).padStart(2, '0');
                            const message = `NUMBER ${queueNum} PLEASE PROCEED TO WINDOW NUMBER ONE`;

                            const utterance = new SpeechSynthesisUtterance(message);
                            utterance.rate = 0.9;
                            utterance.pitch = 1;
                            utterance.volume = 1;
                            utterance.onend = () => {
                                const r1 = new SpeechSynthesisUtterance(message);
                                r1.rate = 0.9; r1.pitch = 1; r1.volume = 1;
                                r1.onend = () => {
                                    const r2 = new SpeechSynthesisUtterance(message);
                                    r2.rate = 0.9; r2.pitch = 1; r2.volume = 1;
                                    window.speechSynthesis.speak(r2);
                                };
                                window.speechSynthesis.speak(r1);
                            };
                            window.speechSynthesis.speak(utterance);
                        }

                        this.showNotification(data.message || 'Action successful!');
                        return data;
                    } catch (error) {
                        console.error('Request failed:', error);
                        this.showNotification('Action failed. Please try again.', 'error');
                        await this.fetchQueueData();
                    }
                },

                recallNowServing() {
                    const nowServingEl = document.getElementById('nowServing');
                    if (this.nowServingActive) {
                        const queueNumMatch = nowServingEl.textContent.match(/Queue #(\d+)/);
                        if (queueNumMatch) {
                            const queueNum = queueNumMatch[1].padStart(2, '0');
                            const message = `NUMBER ${queueNum} PLEASE PROCEED TO WINDOW NUMBER ONE`;

                            const utterance = new SpeechSynthesisUtterance(message);
                            utterance.rate = 0.9;
                            utterance.pitch = 1;
                            utterance.volume = 1;
                            utterance.onend = () => {
                                const r1 = new SpeechSynthesisUtterance(message);
                                r1.rate = 0.9; r1.pitch = 1; r1.volume = 1;
                                r1.onend = () => {
                                    const r2 = new SpeechSynthesisUtterance(message);
                                    r2.rate = 0.9; r2.pitch = 1; r2.volume = 1;
                                    window.speechSynthesis.speak(r2);
                                };
                                window.speechSynthesis.speak(r1);
                            };
                            window.speechSynthesis.speak(utterance);

                            this.showNotification(`Recalled Queue #${queueNum}`, 'success');
                        }
                    } else {
                        this.showNotification('No one is currently being served', 'error');
                    }
                },

                markNextAsServed() { this.makeRequest("/admin/queue/next"); },
                completeNowServing() { this.makeRequest("/admin/queue/complete-now"); },
                cancelNowServing() {
                    this.pendingAction = () => this.makeRequest("/admin/queue/cancel-now");
                    this.openModal();
                },
                requeueNowServing() {
                    this.pendingAction = () => this.makeRequest("/admin/queue/requeue-now");
                    this.openModal();
                },
                openModal() {
                    document.getElementById('confirmationModal').classList.remove('hidden');
                },
                closeModal() {
                    document.getElementById('confirmationModal').classList.add('hidden');
                },
                confirmPendingAction() {
                    if (this.pendingAction) {
                        this.pendingAction();
                    }
                    this.closeModal();
                }
            }));
        });
    </script>
</body>
</html>