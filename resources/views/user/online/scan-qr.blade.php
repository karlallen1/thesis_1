<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scan QR to Join Queue – North Caloocan City Hall</title>
    <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main Content */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .scanner-card {
            background: white;
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 2px solid #f1f5f9;
            max-width: 500px;
            width: 100%;
        }

        .scanner-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 0 auto 2rem;
            display: block;
        }

        .scanner-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .scanner-description {
            color: #64748b;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #dcfce7;
            color: #166534;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .instructions {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: left;
        }

        .instructions h3 {
            color: #f59e0b;
            margin: 0 0 0.75rem 0;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions ul {
            margin: 0;
            padding-left: 1.25rem;
            color: #64748b;
        }

        .instructions li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal-content {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .modal-info {
            font-size: 1.1rem;
            color: #475569;
            line-height: 1.8;
        }

        .modal-button {
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .modal-button:hover {
            background-color: #1d4ed8;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .main-container {
                padding: 1rem;
            }

            .scanner-card {
                padding: 2rem 1.5rem;
            }

            .scanner-title {
                font-size: 1.5rem;
            }

            .scanner-logo {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Main Scanner Page -->
    <div class="main-container">
        <div class="scanner-card">
            <img src="{{ asset('img/scanner.png') }}" alt="Scan QR Code" class="scanner-logo">

            <h1 class="scanner-title">Scan Your QR Code</h1>
            <p class="scanner-description">
                Present your QR code to the scanner to join the queue and receive updates about your application status.
            </p>

            <!-- Status Badge -->
            <div class="status-badge">
                <div class="status-dot"></div>
                <span id="status-text">Scanner Ready</span>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                    </svg>
                    How to Use
                </h3>
                <ul>
                    <li>Hold your QR code steady in front of the scanner</li>
                    <li>Wait for automatic detection and processing</li>
                    <li>You will see your name and queue number</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Hidden Input for QR Scanner -->
    <input type="text" id="scanner-input" style="position: absolute; left: -9999px; opacity: 0;" autofocus>

    <!-- Success Modal -->
<div id="queue-modal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Welcome!</h2>
        <div class="modal-info">
            <p><strong>Name:</strong> <span id="modal-name">Loading...</span></p>
            <p><strong>Queue #:</strong> <span id="modal-queue">-</span></p>
        </div>
        <button id="modal-close" class="modal-button">Got It</button>
        <!-- Optional: Print Again Button -->
        <button id="modal-print" class="modal-button" style="background-color: #10b981; margin-top: 8px;">
            Print Ticket Again
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('scanner-input');
        const statusText = document.getElementById('status-text');
        const statusBadge = document.querySelector('.status-badge');
        const modal = document.getElementById('queue-modal');
        const modalName = document.getElementById('modal-name');
        const modalQueue = document.getElementById('modal-queue');
        const modalClose = document.getElementById('modal-close');
        const modalPrint = document.getElementById('modal-print'); // Optional print again button

        let buffer = '';
        let timeout;
        let currentAppId = null; // ✅ Declare here to store ID

        const updateStatus = (status) => {
            switch(status) {
                case 'ready':
                    statusText.textContent = 'Scanner Ready';
                    statusBadge.style.background = '#dcfce7';
                    statusBadge.style.color = '#166534';
                    break;
                case 'processing':
                    statusText.textContent = 'Processing...';
                    statusBadge.style.background = '#fef3c7';
                    statusBadge.style.color = '#92400e';
                    break;
                case 'success':
                    statusText.textContent = 'QR Code Detected!';
                    statusBadge.style.background = '#dbeafe';
                    statusBadge.style.color = '#1e40af';
                    break;
            }
        };

        // Extract token from URL
        function extractToken(url) {
            const match = url.match(/[?&]token=([^&]+)/i);
            return match ? match[1] : null;
        }

        // Show modal with data
        function showQueueModal(name, queue, isPriority = false) {
            modalName.textContent = name;
            modalQueue.textContent = queue;

            // Clear any existing badge
            const existing = modalName.parentNode.querySelector('.priority-badge');
            if (existing) modalName.parentNode.removeChild(existing);

            if (isPriority) {
                const badge = document.createElement('span');
                badge.className = 'priority-badge';
                badge.textContent = ' (Priority)';
                badge.style.color = '#d97706';
                badge.style.fontWeight = 'bold';
                modalName.parentNode.appendChild(badge);
            }
        }

        // Send token to backend
        async function processToken(token) {
            updateStatus('processing');

            try {
                const response = await fetch('{{ route("api.scan.qr") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ token })
                });

                const data = await response.json(); // ✅ `data` defined here

                if (data.success) {
                    updateStatus('success');

                    // Update modal
                    showQueueModal(data.name, data.queue_number, data.is_priority);

                    // Show modal
                    setTimeout(() => {
                        modal.style.display = 'flex';
                    }, 600);

                    // ✅ Store application ID for printing
                    currentAppId = data.application_id;

                    // ✅ Open print ticket in new tab
                    setTimeout(() => {
                        const printUrl = `{{ route('user.online.queue-ticket', '') }}/${currentAppId}`;
                        const printWindow = window.open(printUrl, 'PrintTicket', 'width=350,height=400');
                    }, 1000);
                } else {
                    alert('Error: ' + data.message);
                    updateStatus('ready');
                }
            } catch (err) {
                console.error('Scan failed:', err);
                alert('Scan failed. Please try again.');
                updateStatus('ready');
            }
        }

        // Keyboard input handler
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const scannedUrl = buffer.trim();
                buffer = '';

                if (!scannedUrl) return;

                const token = extractToken(scannedUrl);
                if (token) {
                    processToken(token);
                } else {
                    alert('Invalid QR code format.');
                    updateStatus('ready');
                }
            } else if (e.key.length === 1) {
                buffer += e.key;
                if (buffer.length > 5) {
                    updateStatus('processing');
                }
            }

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                buffer = '';
                updateStatus('ready');
            }, 1000);
        });

        // Keep focus
        setInterval(() => input.focus(), 500);
        input.focus();

        // Close modal
        modalClose.addEventListener('click', () => {
            modal.style.display = 'none';
            updateStatus('ready');
            input.focus();
        });

        // Optional: Print again button
        if (modalPrint) {
            modalPrint.addEventListener('click', () => {
                if (currentAppId) {
                    const printUrl = `{{ route('user.online.queue-ticket', '') }}/${currentAppId}`;
                    window.open(printUrl, '_blank');
                } else {
                    alert('No ticket to print.');
                }
            });
        }
    });
</script>
</body>
</html>