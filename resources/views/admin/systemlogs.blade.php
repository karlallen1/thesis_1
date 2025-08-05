<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>System Logs - Admin Panel</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        select.no-arrow { appearance: none; -webkit-appearance: none; -moz-appearance: none; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="systemLogs()" x-init="loadLogs()">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#1B3C53] text-white flex flex-col fixed h-full">
            
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

            <!-- Navigation -->
            <nav class="flex-1 px-8 py-6 space-y-6">
                <a href="{{ route('admin.dashboard-main') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.dashboard-main') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    Dashboard
                </a>
                @if(session('role') === 'main_admin')
                    <a href="{{ route('admin.usermanagement') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.usermanagement') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                        User Management
                    </a>
                @endif
                <a href="{{ route('admin.queuestatus') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.queuestatus') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    Queue Status
                </a>
                <a href="{{ route('admin.systemlogs') }}" class="block text-xl font-georgia text-white transition {{ request()->routeIs('admin.systemlogs') ? 'opacity-100 grayscale-0 underline' : 'opacity-50 grayscale hover:underline' }}">
                    System Logs
                </a>
            </nav>

        </aside>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-[#c0c0ca] ml-64">
            <!-- Header -->
            <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4] flex justify-between items-center">
                <h1 class="text-2xl font-georgia font-semibold">System Logs</h1>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <div class="live-indicator"></div>
                    <span>Live Data</span>
                    <span x-text="lastUpdated" class="text-xs"></span>
                </div>
            </header>
            <!-- Controls Section -->
            <section class="px-8 py-4 bg-white border-b border-gray-200" x-data="logControls()">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <!-- Filter Type -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Filter:</label>
                        <select x-model="filterType" @change="applyFilters()" class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]">
                            <option value="all">All Logs</option>
                            <option value="daily_summary">Daily Summary</option>
                            <option value="account">Account Changes</option>
                            <option value="queue">Queue Operations</option>
                            <option value="system">System Events</option>
                        </select>
                    </div>
                    <!-- Date Range -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">From:</label>
                        <input type="date" x-model="startDate" @change="applyFilters()" class="px-2 py-1 border border-gray-300 rounded text-sm" />
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">To:</label>
                        <input type="date" x-model="endDate" @change="applyFilters()" class="px-2 py-1 border border-gray-300 rounded text-sm" />
                    </div>
                    <!-- Export & Archive -->
                    <div class="flex items-center space-x-2 ml-auto">
                        <button @click="exportLogs()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold transition">
                            Export CSV
                        </button>
                        <button @click="createArchive()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold transition">
                            Monthly Archive
                        </button>
                    </div>
                </div>
            </section>
            <!-- Logs Section -->
            <section class="px-8 py-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-georgia text-xl font-semibold text-gray-700">System Logs</h2>
                    <div class="text-sm text-gray-600">Total: <span x-text="logs.length" class="font-semibold"></span> logs</div>
                </div>
                <!-- Logs Table -->
                <div class="overflow-x-auto rounded-xl shadow bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#1B3C53] text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Date & Time</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">User</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Action</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Details</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" x-show="displayedLogs.length > 0">
                            <template x-for="log in displayedLogs.slice((currentPage - 1) * logsPerPage, currentPage * logsPerPage)" :key="log.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm" x-text="formatDate(log.date)"></td>
                                    <td class="px-6 py-4">
                                        <span :class="getTypeColor(log.type)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="log.type"></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm" x-text="log.user"></td>
                                    <td class="px-6 py-4 text-sm" x-text="log.action"></td>
                                    <td class="px-6 py-4 text-sm" x-text="JSON.stringify(log.details)"></td>
                                    <td class="px-6 py-4">
                                        <span :class="getStatusColor(log.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="log.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody x-show="displayedLogs.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500" x-text="loading ? 'Loading logs...' : 'No logs found'"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4" x-show="displayedLogs.length > 0">
                    <div class="text-sm text-gray-600">
                        Showing <span x-text="paginationStart"></span> to <span x-text="paginationEnd"></span> of <span x-text="displayedLogs.length"></span> results
                    </div>
                    <div class="flex space-x-2">
                        <button @click="previousPage()" :disabled="currentPage <= 1" class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 disabled:opacity-50">
                            Previous
                        </button>
                        <span x-text="`Page ${currentPage} of ${totalPages}`" class="px-3 py-1 text-sm text-gray-600"></span>
                        <button @click="nextPage()" :disabled="currentPage >= totalPages" class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script>
        // Expose systemLogs component globally for logControls to access
        window.systemLogsComponent = null;
        function logControls() {
            return {
                filterType: 'all',
                startDate: '',
                endDate: '',
                init() {
                    const today = new Date();
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    this.startDate = weekAgo.toISOString().split('T')[0];
                    this.endDate = today.toISOString().split('T')[0];
                },
                async applyFilters() {
                    await window.systemLogsComponent?.applyFilters({
                        type: this.filterType,
                        startDate: this.startDate,
                        endDate: this.endDate
                    });
                },
                async exportLogs() {
                    const url = new URL("{{ route('admin.systemlogs.api') }}", window.location.origin);
                    if (this.filterType !== 'all') url.searchParams.append('type', this.filterType);
                    if (this.startDate) url.searchParams.append('start_date', this.startDate);
                    if (this.endDate) url.searchParams.append('end_date', this.endDate);
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
                        // Check for authentication redirect
                        if (response.url.includes('/admin/login') || response.status === 401) {
                            alert('Session expired. Please log in again.');
                            window.location.href = '{{ route("admin.login") }}';
                            return;
                        }
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const logs = await response.json();
                        if (logs.length === 0) {
                            alert('No logs to export');
                            return;
                        }
                        const headers = ['Date & Time', 'Type', 'User', 'Action', 'Details', 'Status'];
                        const csvData = logs.map(log => [
                            new Date(log.date).toLocaleString(),
                            log.type,
                            log.user,
                            log.action,
                            JSON.stringify(log.details),
                            log.status
                        ]);
                        const csv = [headers, ...csvData].map(row => row.map(cell => `"${cell}"`).join(',')).join('
');
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
            };
        }
        function systemLogs() {
            window.systemLogsComponent = this;
            return {
                logs: [],
                displayedLogs: [],
                currentPage: 1,
                logsPerPage: 10,
                loading: false,
                get totalPages() {
                    return Math.ceil(this.displayedLogs.length / this.logsPerPage);
                },
                get paginationStart() {
                    return this.displayedLogs.length > 0 ? (this.currentPage - 1) * this.logsPerPage + 1 : 0;
                },
                get paginationEnd() {
                    return Math.min(this.currentPage * this.logsPerPage, this.displayedLogs.length);
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
                        // Check for authentication redirect
                        if (response.url.includes('/admin/login') || response.status === 401) {
                            alert('Session expired. Please log in again.');
                            window.location.href = '{{ route("admin.login") }}';
                            return;
                        }
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        // Verify we got JSON
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
                async applyFilters(filters) {
                    this.loading = true;
                    try {
                        const url = new URL("{{ route('admin.systemlogs.api') }}", window.location.origin);
                        if (filters.type && filters.type !== 'all') url.searchParams.append('type', filters.type);
                        if (filters.startDate) url.searchParams.append('start_date', filters.startDate);
                        if (filters.endDate) url.searchParams.append('end_date', filters.endDate);
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
                }
            };
        }
    </script>
</body>
</html>