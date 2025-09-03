<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>System Logs - Admin Panel</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
    <!-- Tailwind CSS -->
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
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
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
        
        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
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
        
        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
        }
        
        .log-card {
            transition: all 0.3s ease;
        }
        .log-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Content text should use sans-serif */
        .content-text {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 sidebar-glass text-white flex flex-col fixed h-full z-40">
            <!-- Top Section -->
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
            
            <!-- Navigation -->
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
        <main class="flex-1 flex flex-col ml-64" x-data="systemLogs()">
            
            <!-- Controls Section -->
            <section class="px-8 py-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Filter Type -->
                        <div class="flex flex-col flex-1 min-w-[200px]">
                            <label class="text-sm font-medium text-gray-700 mb-1 content-text">Filter Type</label>
                            <select x-model="filterType" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 content-text">
                                <option value="all">All Logs</option>
                                <option value="daily_summary">Daily Summary</option>
                                <option value="account">Account Changes</option>
                                <option value="queue">Queue Operations</option>
                                <option value="system">System Events</option>
                            </select>
                        </div>
                        
                        <!-- Single Date Picker -->
                        <div class="flex flex-col flex-1 min-w-[200px]">
                            <label class="text-sm font-medium text-gray-700 mb-1 content-text">Date</label>
                            <input type="date" x-model="selectedDate" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 content-text" />
                        </div>
                        
                        <!-- Export & Archive -->
                        <div class="flex items-end space-x-3 ml-auto">
                            <button @click="exportLogs()" class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-medium transition shadow-md flex items-center content-text">
                                <i class="fas fa-file-export mr-2"></i> Export CSV
                            </button>
                            <button @click="createArchive()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition shadow-md flex items-center content-text">
                                <i class="fas fa-archive mr-2"></i> Archive
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Logs Section -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="font-georgia text-xl font-semibold text-gray-700 content-text">System Logs</h2>
                        <div class="text-sm text-gray-600 content-text">
                            Showing <span x-text="paginationStart"></span> to <span x-text="paginationEnd"></span> of <span x-text="displayedLogs.length"></span> logs
                        </div>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Date & Time</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Type</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">User</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Action</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Details</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider content-text">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" x-show="displayedLogs.length > 0">
                                <template x-for="log in displayedLogs.slice((currentPage - 1) * logsPerPage, currentPage * logsPerPage)" :key="log.id">
                                    <tr class="log-card hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 content-text" x-text="formatDate(log.date)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getTypeColor(log.type)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium content-text" x-text="log.type"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 content-text" x-text="log.user"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 content-text" x-text="log.action"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate content-text" x-text="JSON.stringify(log.details)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusColor(log.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium content-text" x-text="log.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tbody x-show="displayedLogs.length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 content-text" x-text="loading ? 'Loading logs...' : 'No logs found'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center" x-show="displayedLogs.length > 0">
                        <div class="text-sm text-gray-600 content-text">
                            Showing <span x-text="paginationStart"></span> to <span x-text="paginationEnd"></span> of <span x-text="displayedLogs.length"></span> results
                        </div>
                        <div class="flex space-x-2">
                            <button @click="previousPage()" :disabled="currentPage <= 1" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed content-text">
                                <i class="fas fa-chevron-left mr-1"></i> Previous
                            </button>
                            <span x-text="`Page ${currentPage} of ${totalPages}`" class="px-4 py-2 text-sm text-gray-600 content-text"></span>
                            <button @click="nextPage()" :disabled="currentPage >= totalPages" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed content-text">
                                Next <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('systemLogs', () => ({
                logs: [],
                displayedLogs: [],
                currentPage: 1,
                logsPerPage: 10,
                loading: false,
                filterType: 'all',
                selectedDate: '',
                currentDateTime: '',
                get totalPages() {
                    return Math.ceil(this.displayedLogs.length / this.logsPerPage);
                },
                get paginationStart() {
                    return this.displayedLogs.length > 0 ? (this.currentPage - 1) * this.logsPerPage + 1 : 0;
                },
                get paginationEnd() {
                    return Math.min(this.currentPage * this.logsPerPage, this.displayedLogs.length);
                },
                init() {
                    this.updateDateTime();
                    const today = new Date();
                    this.selectedDate = today.toISOString().split('T')[0];
                    setInterval(() => {
                        this.updateDateTime();
                    }, 1000);
                    this.loadLogs();
                },
                updateDateTime() {
                    const now = new Date();
                    this.currentDateTime = now.toLocaleString("en-US", {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                },
                async loadLogs() {
                    this.loading = true;
                    try {
                        const response = await fetch("{{ route('admin.systemlogs.api') }}", {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (response.url.includes('/admin/login') || response.status === 401) {
                            alert('Session expired. Please log in again.');
                            window.location.href = '{{ route("admin.login") }}';
                            return;
                        }
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await response.text();
                            console.error('Expected JSON but got:', text.substring(0, 200) + '...');
                            throw new Error('Server returned non-JSON response');
                        }
                        const logs = await response.json();
                        this.logs = logs.map(log => ({
                            id: log.id,
                            date: log.date,
                            type: log.type || 'SYSTEM', 
                            user: log.user || 'System',
                            action: log.action,
                            details: log.details,
                            status: log.status || 'SUCCESS'
                        }));
                        this.displayedLogs = [...this.logs];
                        this.currentPage = 1;
                    } catch (error) {
                        console.error("Failed to load logs:", error);
                        alert("Could not load logs. Please check your connection and try again.");
                    } finally {
                        this.loading = false;
                    }
                },
                applyFilters() {
                    this.loading = true;
                    const filters = {
                        type: this.filterType,
                        date: this.selectedDate
                    };
                    this.applyFiltersWithParams(filters);
                },
                async applyFiltersWithParams(filters) {
                    try {
                        const url = new URL("{{ route('admin.systemlogs.api') }}", window.location.origin);
                        if (filters.type && filters.type !== 'all') url.searchParams.append('type', filters.type);
                        if (filters.date) url.searchParams.append('date', filters.date);
                        
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (response.url.includes('/admin/login') || response.status === 401) {
                            alert('Session expired. Please log in again.');
                            window.location.href = '{{ route("admin.login") }}';
                            return;
                        }
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const logs = await response.json();
                        this.displayedLogs = logs.map(log => ({
                            id: log.id,
                            date: log.date,
                            type: log.type || 'SYSTEM',
                            user: log.user || 'System',
                            action: log.action,
                            details: log.details,
                            status: log.status || 'SUCCESS'
                        }));
                        this.currentPage = 1;
                    } catch (error) {
                        console.error("Filter failed:", error);
                        alert("Filter failed. Please try again.");
                    } finally {
                        this.loading = false;
                    }
                },
                previousPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                formatDate(date) {
                    return new Date(date).toLocaleString();
                },
                getTypeColor(type) {
                    const colors = {
                        'ACCOUNT': 'bg-blue-100 text-blue-800',
                        'QUEUE': 'bg-purple-100 text-purple-800',
                        'SYSTEM': 'bg-indigo-100 text-indigo-800',
                        'DAILY_SUMMARY': 'bg-green-100 text-green-800'
                    };
                    return colors[type] || 'bg-gray-100 text-gray-800';
                },
                getStatusColor(status) {
                    const colors = {
                        'SUCCESS': 'bg-green-100 text-green-800',
                        'PENDING': 'bg-yellow-100 text-yellow-800',
                        'FAILED': 'bg-red-100 text-red-800'
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                },
                async exportLogs() {
                    const url = new URL("{{ route('admin.systemlogs.api') }}", window.location.origin);
                    if (this.filterType !== 'all') url.searchParams.append('type', this.filterType);
                    if (this.selectedDate) url.searchParams.append('date', this.selectedDate);
                    
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (response.url.includes('/admin/login') || response.status === 401) {
                            alert('Session expired. Please log in again.');
                            window.location.href = '{{ route("admin.login") }}';
                            return;
                        }
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const logs = await response.json();
                        const filteredLogs = logs.map(log => [
                            new Date(log.date).toLocaleString(),
                            log.type,
                            log.user,
                            log.action,
                            JSON.stringify(log.details),
                            log.status
                        ]);
                        const headers = ['Date & Time', 'Type', 'User', 'Action', 'Details', 'Status'];
                        const csv = [headers, ...filteredLogs].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
                        const blob = new Blob([csv], { type: 'text/csv' });
                        const urlBlob = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = urlBlob;
                        a.download = `system_logs_${new Date().toISOString().split('T')[0]}.csv`;
                        a.click();
                        window.URL.revokeObjectURL(urlBlob);
                    } catch (error) {
                        console.error("Export failed:", error);
                        alert("Failed to export logs. Please try again.");
                    }
                },
                createArchive() {
                    alert("Monthly archiving not yet implemented on server.");
                }
            }));
        });
    </script>
</body>
</html>