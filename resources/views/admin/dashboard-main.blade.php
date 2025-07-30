<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Admin Panel</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .font-georgia { font-family: Georgia, 'Times New Roman', Times, serif; }
        .live-indicator {
            width: 10px;
            height: 10px;
            background-color: #10B981;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .updating {
            animation: updatePulse 0.5s ease-in-out;
        }
        @keyframes updatePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans" x-data="dashboardApp()">

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

            <!-- Logout -->
            <div class="px-6 py-4 border-t border-[#244C66]">
                <form action="{{ route('admin.logout') }}" method="GET">
                    <button type="submit" class="w-full bg-[#244C66] hover:bg-[#183345] text-white py-2 rounded font-semibold transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-[#c0c0ca]">

            <!-- Header -->
            <header class="px-8 py-4 border-b border-gray-300 bg-[#afafb4] flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('img/philogo.png') }}" alt="PH Logo" class="w-12 h-12 object-contain" />
                    <h1 class="text-2xl font-georgia font-semibold text-gray-800">North Caloocan City Hall</h1>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Welcome, <span class="font-semibold">Head Admin</span></p>
                    <p x-text="currentDateTime" class="text-sm text-gray-500"></p>
                </div>
            </header>

            <!-- Dashboard Header -->
            <section class="px-8 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="font-georgia text-2xl font-semibold text-gray-700">Dashboard Overview</h1>
                        <p class="text-gray-500 text-sm" x-text="`Real-time statistics for ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}`"></p>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <div class="live-indicator"></div>
                        <span>Live Data</span>
                        <span x-text="lastUpdated" class="text-xs"></span>
                    </div>
                </div>
            </section>

          
            <!-- Dashboard Stats Cards -->
            <section class="px-8 py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Clients Served Today -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/served.png') }}" alt="Clients Served" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">Clients Served Today</p>
                                <h3 class="text-3xl font-bold" style="color: #1B3C53" x-text="stats.clients_served || 0"></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Pending -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/pending.png') }}" alt="Pending" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">Pending</p>
                                <h3 class="text-3xl font-bold" style="color: #F59E0B" x-text="stats.pending || 0"></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Cancelled Today -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/cancelled.png') }}" alt="Cancelled" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">Cancelled Today</p>
                                <h3 class="text-3xl font-bold" style="color: #EF4444" x-text="stats.cancelled || 0"></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Completed Today -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/complete.png') }}" alt="Completed" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">Completed Today</p>
                                <h3 class="text-3xl font-bold" style="color: #10B981" x-text="stats.completed || 0"></h3>
                            </div>
                        </div>
                    </div>
                    <!-- PWD Clients Today -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/PWD.png') }}" alt="PWD Clients" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">PWD Clients Today</p>
                                <h3 class="text-3xl font-bold" style="color: #8b5cf6" x-text="stats.pwd_clients || 0"></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Senior Clients Today -->
                    <div class="bg-white p-6 rounded-xl shadow stat-card" :class="{ 'updating': updating }">
                        <div class="flex items-center justify-between">
                            <div>
                                <img src="{{ asset('img/senior.png') }}" alt="Senior Clients" class="w-10 h-10 mb-3" />
                                <p class="text-sm text-gray-500 mb-1">Senior Clients Today</p>
                                <h3 class="text-3xl font-bold" style="color: #F97316" x-text="stats.senior_clients || 0"></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Chart Section -->
            <section class="px-8 py-6">
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-georgia text-xl font-semibold text-gray-700">Hourly Client Activity (7 AM - 5 PM)</h2>
                        <div class="text-sm text-gray-500" x-text="new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></div>
                    </div>
                    <canvas id="clientLineChart" height="120"></canvas>
                </div>
            </section>

        </main>
    </div>

    <script>
        function dashboardApp() {
            return {
                stats: @json($stats ?? []),
                updating: false,
                currentDateTime: '',
                lastUpdated: '',
                chart: null,

                init() {
                    this.updateDateTime();
                    this.initChart();
                    this.startAutoRefresh();
                    
                    // Update time every second
                    setInterval(() => {
                        this.updateDateTime();
                    }, 1000);

                    // Make refresh function globally available for queue status page
                    window.refreshDashboardStats = () => {
                        this.refreshStats();
                    };
                },

                updateDateTime() {
                    const now = new Date();
                    this.currentDateTime = now.toLocaleString("en-US", {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                },

                async refreshStats() {
                    try {
                        this.updating = true;
                        
                        const response = await fetch('/admin/dashboard-stats', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const data = await response.json();
                        
                        // Update stats with animation
                        this.stats = data;
                        
                        // Update last updated time
                        const now = new Date();
                        this.lastUpdated = 'Updated: ' + now.toLocaleTimeString('en-US', { hour12: true });
                        
                        console.log('Dashboard stats updated:', data);
                        
                    } catch (error) {
                        console.error('Failed to refresh dashboard stats:', error);
                    } finally {
                        setTimeout(() => {
                            this.updating = false;
                        }, 500);
                    }
                },

                startAutoRefresh() {
                    // Initial refresh
                    this.refreshStats();
                    
                    // Auto-refresh every 10 seconds
                    setInterval(() => {
                        this.refreshStats();
                    }, 10000);
                },

                initChart() {
                    const ctx = document.getElementById('clientLineChart').getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM'],
                            datasets: [{
                                label: 'Clients Served',
                                data: @json($hourlyData ?? []),
                                backgroundColor: 'rgba(27, 60, 83, 0.1)',
                                borderColor: '#1B3C53',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: '#1B3C53',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                            }]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    cornerRadius: 8,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.1)',
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6b7280',
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)',
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>

</body>
</html>