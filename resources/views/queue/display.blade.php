<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Queue Display - North Caloocan City Hall</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a; /* dark slate */
            color: white;
        }
        .orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .pulse {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .number-card {
            @apply bg-gray-800 hover:bg-gray-700 rounded-xl p-4 shadow-lg transition-all duration-300 transform hover:scale-105 text-center;
        }
        .highlight-number {
            @apply text-6xl md:text-8xl orbitron text-green-400 font-bold drop-shadow-lg;
        }
    </style>
    <script>
        // Prevent unwanted interactions
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (['F12', 'F5', 'F11', 'I'].includes(e.key)) e.preventDefault();
        });
    </script>
</head>
<body class="min-h-screen overflow-hidden text-white">
    <div class="container mx-auto px-6 py-8">

        <!-- Header: "Please monitor..." on left, "Now Serving" number on right -->
        <header class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div class="flex-1">
                <p class="text-2xl md:text-3xl text-gray-300">
                    Please monitor your queue number
                </p>
                <p class="text-gray-400 text-sm mt-1" id="current-time"></p>
            </div>

            <!-- Now Serving Number (Large, on the right) -->
            <div class="text-right mt-4 md:mt-0">
                <p id="inline-now-serving" class="highlight-number">—</p>
                <p class="text-gray-400 text-sm">Now Serving</p>
            </div>
        </header>

        <!-- Full Now Serving Section (with name) -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-3xl shadow-2xl p-8 mb-10">
            <h2 class="text-3xl font-semibold text-white text-center mb-4">Currently Being Served</h2>
            <div id="now-serving" class="text-center">
                <p class="text-7xl orbitron pulse text-white">—</p>
                <p class="text-2xl mt-2 text-white">No one being served</p>
            </div>
        </section>

        <!-- Next in Queue -->
        <section class="bg-gray-800 rounded-2xl shadow-xl p-6">
            <h2 class="text-2xl font-semibold text-white text-center mb-6">Next in Queue</h2>
            <div id="next-queue" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <p class="text-gray-400 text-lg col-span-full text-center py-8">Loading next applicants...</p>
            </div>
        </section>
    </div>

    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleString();
        }
        setInterval(updateTime, 1000);
        updateTime();

        const nowServingEl = document.getElementById('now-serving');
        const inlineNowServingEl = document.getElementById('inline-now-serving');
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

                    nowServingEl.innerHTML = `
                        <p class="text-7xl orbitron pulse text-white">${queueNum}</p>
                        <p class="text-2xl mt-2 text-white">${name}</p>
                    `;
                    inlineNowServingEl.textContent = queueNum;
                } else {
                    nowServingEl.innerHTML = `
                        <p class="text-7xl orbitron pulse text-white">—</p>
                        <p class="text-2xl mt-2 text-white">No one being served</p>
                    `;
                    inlineNowServingEl.textContent = '—';
                }

                // Next in Queue (priorities first)
                const nextList = [...(data.priority || []), ...(data.regular || [])].slice(0, 6);
                if (nextList.length > 0) {
                    nextQueueEl.innerHTML = nextList.map(app => `
                        <div class="number-card fade-in">
                            <p class="text-5xl orbitron text-blue-400">${app.queue_number}</p>
                            <p class="text-sm font-medium text-white">${app.name}</p>
                        </div>
                    `).join('');
                } else {
                    nextQueueEl.innerHTML = `
                        <p class="text-gray-400 text-lg col-span-full text-center py-8">No upcoming queues</p>
                    `;
                }
            } catch (error) {
                console.error('Fetch error:', error);
                nextQueueEl.innerHTML = `
                    <p class="text-red-400 text-lg col-span-full text-center py-8">Connection failed. Retrying...</p>
                `;
            }
        }

        // Load and refresh
        document.addEventListener('DOMContentLoaded', () => {
            fetchQueueData();
            setInterval(fetchQueueData, 3000);
        });
    </script>
</body>
</html>