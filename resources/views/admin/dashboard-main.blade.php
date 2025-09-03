<!DOCTYPE html>
<html lang="en" x-data="dashboardApp()" x-init="init()">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Admin Panel</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">

    <!-- Tailwind CSS & JS Bundle (includes Chart.js via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>

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
        
        .header-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.9);
        }
        
        /* Content text should use sans-serif */
        .content-text {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        
        /* Numbers in stat cards should use sans-serif */
        .stat-number {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 700;
        }

        #chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        #chart-container canvas {
            width: 100% !important;
            height: 100% !important;
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
            
            <!-- Logout -->
            <div class="px-4 py-4 border-t border-white/10">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-xl font-georgia font-semibold transition">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </div>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col ml-64">
            <!-- Header -->
            <header class="header-glass sticky top-0 z-30 px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('img/philogo.png') }}" alt="PH Logo" class="w-12 h-12 object-contain drop-shadow-lg" />
                        <div>
                            <h1 class="text-2xl font-georgia font-bold text-gray-900">North Caloocan City Hall</h1>
                            <p class="text-sm text-gray-600">Administrative Dashboard</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-georgia font-semibold text-gray-900">{{ session('username') }}</p>
                        <p x-text="currentDateTime" class="text-xs text-gray-500"></p>
                    </div>
                </div>
            </header>

            <!-- Dashboard Overview -->
            <section class="px-8 py-6">
                <div>
                    <h2 class="text-3xl font-georgia font-bold text-gray-900 mb-2">Dashboard Overview</h2>
                    <p class="text-gray-600 mb-8 content-text" x-text="`Real-time statistics for ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}`"></p>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Clients Served Today -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/served.png') }}" alt="Clients Served" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <h3 class="text-2xl stat-number text-gray-900" x-text="stats.clients_served || 0"></h3>
                                        <p class="text-sm font-medium text-gray-600 content-text">Entered Queue Today</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/pending.png') }}" alt="Pending" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 content-text">Pending</p>
                                        <h3 class="text-2xl stat-number text-yellow-600" x-text="stats.pending || 0"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancelled Today -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/cancelled.png') }}" alt="Cancelled" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 content-text">Cancelled Today</p>
                                        <h3 class="text-2xl stat-number text-red-600" x-text="stats.cancelled || 0"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Today -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/complete.png') }}" alt="Completed" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 content-text">Completed Today</p>
                                        <h3 class="text-2xl stat-number text-green-600" x-text="stats.completed || 0"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PWD Clients Today -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/PWD.png') }}" alt="PWD Clients" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 content-text">PWD Clients Today</p>
                                        <h3 class="text-2xl stat-number text-purple-600" x-text="stats.pwd_clients || 0"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Senior Clients Today -->
                    <div class="stat-card rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                        <img src="{{ asset('img/senior.png') }}" alt="Senior Clients" class="w-6 h-6 brightness-0 invert" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 content-text">Senior Clients Today</p>
                                        <h3 class="text-2xl stat-number text-orange-600" x-text="stats.senior_clients || 0"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Chart Section -->
            <section class="px-8 py-6">
                <div class="chart-container rounded-2xl shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-georgia font-bold text-gray-900">Hourly Client Activity</h3>
                            <p class="text-sm text-gray-600 content-text">Service hours: 7 AM - 5 PM</p>
                        </div>
                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full content-text" x-text="new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></div>
                    </div>
                        <div id="chart-container" style="height: 300px; position: relative;">
                           <canvas id="hourlyChart" data-data="{{ json_encode($hourlyData) }}"></canvas>
                        </div>
                </div>
            </section>
        </main>
    </div>
    
    <script>
function dashboardApp() {
    return {
        stats: @json($stats ?? []),
        hourlyData: @json($hourlyData ?? []),
        currentDateTime: '',
        chart: null,
        chartInitialized: false,

        init() {
            this.updateDateTime();
            
            // Initialize chart once only
            this.waitForChart().then(() => {
                if (!this.chartInitialized) {
                    this.initStaticChart();
                    this.chartInitialized = true;
                }
            });
            
            // Start auto-refresh for stats only (not chart)
            setTimeout(() => {
                this.startStatsRefresh();
            }, 2000);

            // Update time every second
            setInterval(() => {
                this.updateDateTime();
            }, 1000);
        },

        waitForChart() {
            return new Promise((resolve) => {
                let attempts = 0;
                const maxAttempts = 50;
                
                const checkChart = () => {
                    attempts++;
                    
                    if (typeof window.Chart !== 'undefined') {
                        console.log('Chart.js is available');
                        resolve();
                    } else if (attempts < maxAttempts) {
                        setTimeout(checkChart, 100);
                    } else {
                        console.error('Chart.js failed to load');
                        resolve();
                    }
                };
                checkChart();
            });
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

        async refreshStatsOnly() {
            try {
                const response = await fetch('/admin/dashboard-stats', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                console.log('Stats updated:', data);

                // Only update the stats, not the chart
                this.stats = {
                    clients_served: data.clients_served || 0,
                    pending: data.pending || 0,
                    cancelled: data.cancelled || 0,
                    completed: data.completed || 0,
                    pwd_clients: data.pwd_clients || 0,
                    senior_clients: data.senior_clients || 0
                };

                // Store hourly data but don't update chart
                if (data.hourlyData) {
                    this.hourlyData = data.hourlyData;
                }

            } catch (error) {
                console.error('Failed to refresh stats:', error);
            }
        },

        startStatsRefresh() {
            // Refresh stats only
            this.refreshStatsOnly();
            
            // Continue refreshing stats every 15 seconds
            setInterval(() => {
                this.refreshStatsOnly();
            }, 15000);
        },

        initStaticChart() {
            if (typeof window.Chart === 'undefined') {
                console.error('Chart.js not available');
                return;
            }

            const canvas = document.getElementById('hourlyChart');
            if (!canvas) {
                console.error('Canvas not found');
                return;
            }

            // Prepare static data
            let chartData = Array.isArray(this.hourlyData) ? [...this.hourlyData] : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            
            // Ensure exactly 11 elements
            while (chartData.length < 11) chartData.push(0);
            if (chartData.length > 11) chartData = chartData.slice(0, 11);

            console.log('Creating static chart with data:', chartData);

            try {
                // Create chart with minimal, safe configuration
                this.chart = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM'],
                        datasets: [{
                            data: chartData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#3B82F6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Clients: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        }
                    }
                });

                console.log('Static chart created successfully');

            } catch (error) {
                console.error('Static chart creation failed:', error);
                
                // Fallback: create simple bar chart
                try {
                    this.chart = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['7AM', '8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM'],
                            datasets: [{
                                data: chartData,
                                backgroundColor: '#3B82F6'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                    console.log('Fallback bar chart created');
                } catch (fallbackError) {
                    console.error('All chart creation attempts failed:', fallbackError);
                }
            }
        },

        // Manual chart refresh button (optional)
        refreshChart() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
            this.chartInitialized = false;
            setTimeout(() => {
                this.initStaticChart();
                this.chartInitialized = true;
            }, 500);
        }
    };
}
    </script>
</body>
</html>