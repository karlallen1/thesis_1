<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Queue Display - North Caloocan City Hall</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts for better visibility -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a; /* dark background */
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
            @apply bg-gray-800 hover:bg-gray-700 rounded-lg p-4 shadow transition;
        }
    </style>
    <script>
        // Prevent right-click and other interactions
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (['F12', 'F5', 'F11', 'I'].includes(e.key)) e.preventDefault();
        });
    </script>
</head>
<body class="min-h-screen overflow-hidden">
    <div class="container mx-auto px-12 py-10 text-center">
        <!-- Header -->
        <header class="mb-10">
            <p class="text-2xl text-gray-300 mt-2">Please monitor your queue number</p>
            <div class="mt-4 text-lg text-gray-400" id="current-time"></div>
        </header>

        <!-- Now Serving -->
        <section class="bg-gradient-to-r from-green-600 to-green-800 rounded-3xl shadow-2xl p-12 mb-12 transform transition-all duration-300">
            <h2 class="text-3xl font-semibold text-white mb-6">Now Serving</h2>
            <div id="now-serving" class="text-white">
                <p class="text-7xl orbitron pulse">—</p>
                <p class="text-2xl mt-2">No one being served</p>
            </div>
        </section>

        <!-- Next in Queue -->
        <section class="bg-gray-800 rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Next in Queue</h2>
            <div id="next-queue" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <p class="text-gray-400 text-xl col-span-full text-center">Loading next applicants...</p>
            </div>
        </section>
    </div>

    <script>
        // Update time every second
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleString();
        }
        setInterval(updateTime, 1000);
        updateTime();

        // DOM elements
        const nowServingEl = document.getElementById('now-serving');
        const nextQueueEl = document.getElementById('next-queue');

        // Fetch queue data
        async function fetchQueueData() {
            try {
                const res = await fetch('{{ route("queue.display-data") }}');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                // Update Now Serving
                if (data.now_serving) {
                    nowServingEl.innerHTML = `
                        <p class="text-7xl orbitron pulse text-white">${data.now_serving.queue_number}</p>
                        <p class="text-2xl mt-2 text-white">${data.now_serving.full_name}</p>
                    `;
                } else {
                    nowServingEl.innerHTML = `
                        <p class="text-7xl orbitron pulse">—</p>
                        <p class="text-2xl mt-2">No one being served</p>
                    `;
                }

                // Combine and show next 6 people (priorities first)
                const nextList = [...(data.priority || []), ...(data.regular || [])].slice(0, 6);
                if (nextList.length > 0) {
                    nextQueueEl.innerHTML = nextList.map(app => `
                        <div class="number-card fade-in">
                            <p class="text-4xl orbitron text-blue-400">${app.queue_number}</p>
                            <p class="text-lg text-white">${app.name}</p>
                        </div>
                    `).join('');
                } else {
                    nextQueueEl.innerHTML = `
                        <p class="text-gray-400 text-xl col-span-full text-center">No one in queue</p>
                    `;
                }
            } catch (error) {
                console.error('Failed to fetch queue data:', error);
                nextQueueEl.innerHTML = `
                    <p class="text-red-400 text-xl col-span-full text-center">Connection error. Retrying...</p>
                `;
            }
        }

        // Initial load and refresh every 3 seconds
        document.addEventListener('DOMContentLoaded', () => {
            fetchQueueData();
            setInterval(fetchQueueData, 3000);
        });
    </script>
</body>
</html>