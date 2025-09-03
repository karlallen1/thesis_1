<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Queue Display - North Caloocan City Hall</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Use clean, readable font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a; /* Dark blue-gray background */
            color: #e2e8f0; /* Soft light gray for readability */
        }
        .text-large {
            font-size: clamp(3rem, 10vw, 12rem);
            font-weight: 700;
        }
        .card {
            background-color: #1e293b;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .priority {
            background-color: #c2410c; /* Warm orange for priority */
            color: white;
            font-weight: bold;
        }
        .header {
            background-color: #1e3a8a;
            border-radius: 1rem;
            padding: 1rem 2rem;
        }
        .time {
            font-family: 'Roboto', monospace;
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
    <script>
        // Prevent unwanted interactions
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (['', 'F5', '', 'I'].includes(e.key)) e.preventDefault();
        });
    </script>
</head>
<body class="min-h-screen text-white leading-none">
    <div class="container mx-auto px-6 py-6">

        <!-- Header -->
        <header class="header flex justify-between items-center mb-8">
            <div class="flex items-center space-x-4">
                <img src="/img/mainlogo.png" alt="City Hall Logo" class="w-16 h-16 object-contain rounded-full bg-white p-1">
                <div>
                    <h1 class="text-3xl font-bold">North Caloocan City Hall</h1>
                    <p class="text-lg text-blue-200">Queue Management System</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xl" id="current-date"></p>
                <p class="text-2xl time" id="current-time"></p>
            </div>
        </header>

        <!-- Main Display -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-[calc(100vh-200px)]">

            <!-- Next in Queue -->
            <div class="lg:col-span-1">
                <div class="card">
                    <h2 class="text-2xl font-bold mb-6 text-blue-300 border-b pb-2">Next in Queue</h2>
                    <div id="next-queue" class="space-y-4 max-h-[500px] overflow-y-auto">
                        <div class="text-center py-10 text-gray-400">
                            <p>Loading next applicants...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Now Serving (Large Display) -->
            <div class="lg:col-span-2 flex flex-col">
                <div class="card flex-1 flex flex-col justify-center items-center text-center p-10">
                    <div class="text-blue-400 text-2xl font-semibold mb-4">NOW SERVING</div>
                    <div id="now-serving-display">
                        <div class="text-large text-white mb-6">—</div>
                        <div class="text-4xl font-medium text-gray-300">No one being served</div>
                        <div class="text-2xl text-gray-400 mt-2">Please wait for your turn</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };

            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        const nowServingDisplay = document.getElementById('now-serving-display');
        const nextQueueEl = document.getElementById('next-queue');

        async function fetchQueueData() {
            try {
                const res = await fetch('{{ route("queue.display-data") }}');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                // Update Now Serving
                if (data.now_serving) {
                    const queueNum = data.now_serving.queue_number;
                    const name = data.now_serving.full_name;

                    nowServingDisplay.innerHTML = `
                        <div class="text-large text-white mb-6">${queueNum}</div>
                        <div class="text-4xl font-medium text-white">${name}</div>
                        <div class="text-2xl text-green-400 mt-2">Please proceed to the Window # 1</div>
                    `;
                } else {
                    nowServingDisplay.innerHTML = `
                        <div class="text-large text-gray-500 mb-6">—</div>
                        <div class="text-4xl font-medium text-gray-300">No one being served</div>
                        <div class="text-2xl text-gray-400 mt-2">Please wait for your turn</div>
                    `;
                }

                // Update Next Queue
                const nextList = [...(data.priority || []), ...(data.regular || [])].slice(0, 8);
                if (nextList.length > 0) {
                    nextQueueEl.innerHTML = nextList.map((app, index) => {
                        const isPriority = data.priority && data.priority.includes(app);
                        const badge = isPriority ? '<span class="priority px-2 py-1 rounded text-xs">PRIORITY</span>' : '';
                        return `
                            <div class="bg-gray-700 rounded-lg p-3 text-center relative">
                                ${badge}
                                <div class="text-2xl font-bold text-white">${app.queue_number}</div>
                            </div>
                        `;
                    }).join('');
                } else {
                    nextQueueEl.innerHTML = `
                        <div class="text-center py-10 text-gray-400">
                            <p>No upcoming queues</p>
                            <p class="text-sm mt-1">All caught up for now!</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Fetch error:', error);
                nextQueueEl.innerHTML = `
                    <div class="text-center py-10 text-red-400">
                        <p>Connection failed</p>
                        <p class="text-sm mt-1">Retrying...</p>
                    </div>
                `;
            }
        }

        // Load and refresh data
        document.addEventListener('DOMContentLoaded', () => {
            fetchQueueData();
            setInterval(fetchQueueData, 3000);
        });
    </script>
</body>
</html>