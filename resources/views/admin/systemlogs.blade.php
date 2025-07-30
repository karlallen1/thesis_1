<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>System Logs - Admin Panel</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        select.no-arrow { appearance: none; -webkit-appearance: none; -moz-appearance: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="systemLogs()" x-init="loadLogs()">

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
                <h1 class="text-2xl font-georgia font-semibold">System Logs</h1>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <div class="w-2 h-2 bg-green-500 rounded-full pulse"></div>
                    <span>Live Data</span>
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
                                    <td class="px-6 py-4 text-sm" x-text="log.details"></td>
                                    <td class="px-6 py-4">
                                        <span :class="getStatusColor(log.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="log.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody x-show="displayedLogs.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No logs found</td>
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

    <!-- Logger & Alpine Components -->
    <script>
        // System Logger - Core logging functionality
        window.SystemLogger = {
            logs: [],

            addLog(type, user, action, details, status = 'SUCCESS') {
                const log = {
                    id: Date.now() + Math.random(),
                    date: new Date(),
                    type: type.toUpperCase(),
                    user: user,
                    action: action,
                    details: details,
                    status: status.toUpperCase()
                };
                this.logs.unshift(log);
                this.saveToStorage();
                this.notifyComponents();
                return log;
            },

            // Account logging
            logAccountCreated(userId, email) {
                return this.addLog('ACCOUNT', userId, 'ACCOUNT_CREATED', `New account created for ${email}`);
            },
            logPasswordChanged(userId, email) {
                return this.addLog('ACCOUNT', userId, 'PASSWORD_CHANGED', `Password changed for ${email}`);
            },

            // Queue logging
            logQueueRequested(queueId, userId, service) {
                return this.addLog('QUEUE', userId, 'QUEUE_REQUESTED', `Queue ${queueId} requested for ${service}`, 'PENDING');
            },
            logQueueSuccess(queueId, userId, service) {
                return this.addLog('QUEUE', userId, 'QUEUE_COMPLETED', `Queue ${queueId} completed for ${service}`, 'SUCCESS');
            },
            logQueueFailed(queueId, userId, error) {
                return this.addLog('QUEUE', userId, 'QUEUE_FAILED', `Queue ${queueId} failed: ${error}`, 'FAILED');
            },

            // System logging
            logSystemEvent(action, details, status = 'SUCCESS') {
                return this.addLog('SYSTEM', 'SYSTEM', action, details, status);
            },

            getAllLogs() { return [...this.logs]; },
            filterLogs(type = 'all', startDate = null, endDate = null) {
                let filtered = [...this.logs];
                if (type !== 'all') {
                    filtered = filtered.filter(log => log.type.toLowerCase() === type.toLowerCase());
                }
                if (startDate) {
                    const start = new Date(startDate);
                    filtered = filtered.filter(log => new Date(log.date) >= start);
                }
                if (endDate) {
                    const end = new Date(endDate);
                    end.setHours(23, 59, 59, 999);
                    filtered = filtered.filter(log => new Date(log.date) <= end);
                }
                return filtered;
            },

            exportToCSV(logs = null) {
                const logsToExport = logs || this.logs;
                if (logsToExport.length === 0) return null;
                const headers = ['Date & Time', 'Type', 'User', 'Action', 'Details', 'Status'];
                const csvData = logsToExport.map(log => [
                    new Date(log.date).toLocaleString(),
                    log.type,
                    log.user,
                    log.action,
                    log.details,
                    log.status
                ]);
                const csv = [headers, ...csvData].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
                return csv;
            },

            createMonthlyArchive() {
                const now = new Date();
                const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const endOfLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                const archiveLogs = this.logs.filter(log => {
                    const logDate = new Date(log.date);
                    return logDate >= lastMonth && logDate <= endOfLastMonth;
                });

                if (archiveLogs.length === 0) {
                    alert('No logs found for last month to archive.');
                    return;
                }

                const archiveKey = `${lastMonth.getFullYear()}-${String(lastMonth.getMonth() + 1).padStart(2, '0')}`;
                const archives = JSON.parse(localStorage.getItem('logArchives') || '{}');
                archives[archiveKey] = {
                    month: lastMonth.getMonth() + 1,
                    year: lastMonth.getFullYear(),
                    logs: archiveLogs,
                    createdAt: new Date()
                };
                localStorage.setItem('logArchives', JSON.stringify(archives));

                this.logs = this.logs.filter(log => {
                    const logDate = new Date(log.date);
                    return !(logDate >= lastMonth && logDate <= endOfLastMonth);
                });

                this.saveToStorage();
                this.notifyComponents();
                alert(`Archived ${archiveLogs.length} logs from ${archiveKey}`);
            },

            saveToStorage() {
                try {
                    localStorage.setItem('systemLogs', JSON.stringify(this.logs));
                } catch (e) {
                    console.warn('Could not save logs to localStorage:', e);
                }
            },

            loadFromStorage() {
                try {
                    const stored = localStorage.getItem('systemLogs');
                    if (stored) {
                        this.logs = JSON.parse(stored);
                    }
                } catch (e) {
                    console.warn('Could not load logs from localStorage:', e);
                    this.logs = [];
                }
            },

            notifyComponents() {
                window.dispatchEvent(new CustomEvent('logs-updated'));
            }
        };

        // Alpine Component: Log Controls
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

                applyFilters() {
                    window.dispatchEvent(new CustomEvent('apply-filters', {
                        detail: { type: this.filterType, startDate: this.startDate, endDate: this.endDate }
                    }));
                },

                exportLogs() {
                    const csv = SystemLogger.exportToCSV();
                    if (!csv) {
                        alert('No logs to export');
                        return;
                    }
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `system_logs_${new Date().toISOString().split('T')[0]}.csv`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                },

                createArchive() {
                    SystemLogger.createMonthlyArchive();
                }
            };
        }

        // Alpine Component: System Logs Display
        function systemLogs() {
            return {
                logs: [],
                displayedLogs: [],
                currentPage: 1,
                logsPerPage: 10,

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
                    window.addEventListener('logs-updated', () => {
                        this.loadLogs();
                    });

                    window.addEventListener('apply-filters', (e) => {
                        this.applyFilters(e.detail);
                    });
                },

                loadLogs() {
                    SystemLogger.loadFromStorage();
                    if (SystemLogger.logs.length === 0) {
                        // Optional: Remove this if you don't want sample data
                        // SystemLogger.generateSampleData();
                    }
                    this.logs = SystemLogger.getAllLogs();
                    this.displayedLogs = [...this.logs];
                    this.currentPage = 1;
                },

                applyFilters(filters) {
                    this.displayedLogs = SystemLogger.filterLogs(filters.type, filters.startDate, filters.endDate);
                    this.currentPage = 1;
                },

                previousPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                formatDate(date) {
                    return new Date(date).toLocaleString('en-US', {
                        year: 'numeric', month: '2-digit', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                        hour12: false
                    });
                },

                getTypeColor(type) {
                    const colors = {
                        'ACCOUNT': 'bg-blue-100 text-blue-800',
                        'QUEUE': 'bg-purple-100 text-purple-800',
                        'SYSTEM': 'bg-indigo-100 text-indigo-800'
                    };
                    return colors[type] || 'bg-gray-100 text-gray-800';
                },

                getStatusColor(status) {
                    const colors = {
                        'SUCCESS': 'bg-green-100 text-green-800',
                        'PENDING': 'bg-yellow-100 text-yellow-800',
                        'FAILED': 'bg-red-100 text-red-800',
                        'CANCELLED': 'bg-gray-100 text-gray-800'
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                }
            };
        }
    </script>
</body>
</html>