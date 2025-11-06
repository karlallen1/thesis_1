<!DOCTYPE html>
<html lang="en" x-data="mailboxApp()">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mailbox Submissions - Admin Panel</title>
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

        /* Status Badges */
        .badge-pending { @apply px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800; }
        .badge-approved { @apply px-2 py-1 rounded-full text-xs bg-green-100 text-green-800; }
        .badge-disapproved { @apply px-2 py-1 rounded-full text-xs bg-red-100 text-red-800; }

        /* PWD/Senior badges */
        .badge-pwd { @apply px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800; }
        .badge-senior { @apply px-2 py-1 rounded-full text-xs bg-green-100 text-green-800; }
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

                <!-- NEW: MAILBOX TAB -->
                <a href="{{ route('admin.mailbox') }}" 
                   class="nav-link block font-georgia text-white {{ request()->routeIs('admin.mailbox') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <span>MAILBOX</span>
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
                            <p class="text-sm text-gray-600">IoT Mailbox Document Submissions</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-georgia font-semibold text-gray-900">{{ session('username') }}</p>
                        <p id="currentDateTime" class="text-xs text-gray-500"></p>
                    </div>
                </div>
            </header>
            
            <section class="px-8 py-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="font-georgia text-xl font-semibold text-gray-700">MAILBOX Submissions</h2>
                        <div class="text-sm text-gray-600 content-text">
                            <span id="totalCountDisplay">0</span> submissions
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Name</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">PIN</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Service</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Contact</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Used At</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Status</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mailboxBody" class="bg-white divide-y divide-gray-200 content-text">
                                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading...</td></tr>
                            </tbody>
                        </table>
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
                <h3 class="text-xl font-georgia font-semibold" x-text="modalTitle">Confirm Action</h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="mb-6 text-gray-700 content-text" x-text="modalMessage">Are you sure you want to perform this action?</p>
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
            Alpine.data('mailboxApp', () => ({
                currentDateTime: '',
                pendingAction: null,
                modalTitle: 'Confirm Action',
                modalMessage: 'Are you sure you want to perform this action?',

                init() {
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);
                    this.fetchMailboxData();
                    setInterval(() => this.fetchMailboxData(), 30000); // every 30 sec
                },

                updateDateTime() {
                    const now = new Date();
                    this.currentDateTime = now.toLocaleString("en-US", {
                        weekday: 'short', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                    document.getElementById('currentDateTime').textContent = this.currentDateTime;
                },

                async fetchMailboxData() {
                    try {
                        const res = await fetch("{{ route('admin.mailbox.data') }}");
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();

                        document.getElementById("totalCountDisplay").textContent = data.length;

                        const tbody = document.getElementById("mailboxBody");
                        if (!tbody) return;

                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No submissions yet</td></tr>';
                            return;
                        }

                        tbody.innerHTML = data.map(s => {
                            const statusBadge = s.admin_status === 'approved'
                                ? '<span class="badge-approved">Approved</span>'
                                : s.admin_status === 'disapproved'
                                    ? '<span class="badge-disapproved">Disapproved</span>'
                                    : '<span class="badge-pending">Pending</span>';

                            const usedAt = new Date(s.used_at).toLocaleString();

                            const actions = s.admin_status === 'pending'
                                ? `
                                  <button @click="() => confirmApprove(${s.id}, '${s.full_name}')" 
                                          class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 text-xs rounded mr-1">Approve</button>
                                  <button @click="() => confirmDisapprove(${s.id}, '${s.full_name}')" 
                                          class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded">Disapprove</button>
                                  `
                                : '-';

                            return `
                              <tr class="queue-card hover:bg-gray-50">
                                <td class="px-6 py-4">${s.full_name}</td>
                                <td class="px-6 py-4"><code>${s.pin_code}</code></td>
                                <td class="px-6 py-4">${s.service_type}</td>
                                <td class="px-6 py-4">${s.contact}</td>
                                <td class="px-6 py-4">${usedAt}</td>
                                <td class="px-6 py-4">${statusBadge}</td>
                                <td class="px-6 py-4">${actions}</td>
                              </tr>
                            `;
                        }).join('');
                    } catch (error) {
                        console.error('Failed to load mailbox data:', error);
                        this.showNotification('Failed to load data.', 'error');
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

                confirmApprove(id, name) {
                    this.modalTitle = 'Approve Requirements';
                    this.modalMessage = `Approve requirements for ${name}?`;
                    this.pendingAction = () => this.submitAction(`/admin/mailbox/${id}/approve`, 'approved');
                    this.openModal();
                },

                confirmDisapprove(id, name) {
                    this.modalTitle = 'Disapprove Requirements';
                    this.modalMessage = `Disapprove requirements for ${name}?`;
                    this.pendingAction = () => this.submitAction(`/admin/mailbox/${id}/disapprove`, 'disapproved');
                    this.openModal();
                },

                async submitAction(url, status) {
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await res.json();
                        this.showNotification(data.message || `Submission ${status}.`);
                        await this.fetchMailboxData();
                    } catch (err) {
                        this.showNotification('Action failed.', 'error');
                    }
                },

                openModal() {
                    document.getElementById('confirmationModal').classList.remove('hidden');
                },
                closeModal() {
                    document.getElementById('confirmationModal').classList.add('hidden');
                },
                confirmPendingAction() {
                    if (this.pendingAction) this.pendingAction();
                    this.closeModal();
                }
            }));
        });
    </script>
</body>
</html>