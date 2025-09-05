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
        /* Reset & Global */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #334155;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        /* Back Button */
        .back-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            z-index: 10;
            transition: all 0.2s ease;
        }

        .back-button:hover {
            background-color: #e2e8f0;
            color: #1e293b;
            transform: translateY(-1px);
        }

        /* Main Content Wrapper */
        .page-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body>
    <!-- Back Button -->
    <button id="back-button" class="back-button">
        ← Back
    </button>

    <!-- All other content -->
    <div class="page-content">
        <!-- Main Scanner Page -->
        <div class="main-container">
            <div class="scanner-card">
                <img src="{{ asset('img/scan.png') }}" alt="Scan QR Code" class="scanner-logo">

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
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0.88-.36 1.68-.93 2.25z"/>
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
                <button id="modal-print" class="modal-button">Print Ticket</button>
            </div>
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
            const modalPrint = document.getElementById('modal-print');
            const backButton = document.getElementById('back-button');

            let buffer = '';
            let timeout;
            let currentAppId = null;

            // === INACTIVITY TIMEOUT: Redirect to /kiosk after 2 minutes ===
            let inactivityTimeout;

            function resetInactivityTimer() {
                clearTimeout(inactivityTimeout);
                inactivityTimeout = setTimeout(() => {
                    console.log("No activity for 2 minutes. Redirecting to /kiosk");
                    window.location.href = '/kiosk';
                }, 120000); // 2 minutes
            }

            function setupInactivityListeners() {
                const events = ['mousedown', 'mousemove', 'keypress', 'touchstart', 'click', 'scroll', 'wheel'];
                events.forEach(event => {
                    document.addEventListener(event, resetInactivityTimer, true);
                });
            }

            // Start timer on load
            resetInactivityTimer();
            setupInactivityListeners();

            // Update status badge
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
                const match = url.match(/[?&]token=([^&]+)/i) ||
                              url.match(/[?&]t=([^&]+)/i);
                return match ? decodeURIComponent(match[1].trim()) : null;
            }

            // Show success modal
            function showQueueModal(name, queue, isPriority = false) {
                modalName.textContent = name;
                modalQueue.textContent = queue;

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

            // Process scanned token
            async function processToken(token) {
                updateStatus('processing');

                try {
                    const response = await fetch('{{ route("api.scan.qr") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ token })
                    });

                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    const data = await response.json();

                    if (data.success) {
                        updateStatus('success');
                        showQueueModal(data.name, data.queue_number, data.is_priority);
                        setTimeout(() => { modal.style.display = 'flex'; }, 600);
                        currentAppId = data.application_id;
                        resetInactivityTimer(); // User interacted
                    } else {
                        alert('Error: ' + (data.message || 'Invalid QR'));
                        updateStatus('ready');
                    }
                } catch (err) {
                    console.error('Scan failed:', err);
                    alert('Scan failed. Check console.');
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
                        alert('Invalid QR code format. Expected ?token=...');
                        updateStatus('ready');
                    }
                } else if (e.key.length === 1) {
                    buffer += e.key;
                    if (buffer.length > 5) updateStatus('processing');
                }

                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    buffer = '';
                    updateStatus('ready');
                }, 1000);
            });

            // Keep focus on hidden input
            input.focus();
            setInterval(() => {
                if (document.activeElement !== input) input.focus();
            }, 500);

            document.body.addEventListener('click', () => {
                setTimeout(() => input.focus(), 100);
            });

            // Print Ticket Button
            modalPrint.addEventListener('click', async () => {
                if (!currentAppId) {
                    alert('No ticket to print.');
                    modal.style.display = 'none';
                    updateStatus('ready');
                    input.focus();
                    resetInactivityTimer();
                    return;
                }

                let printSuccess = true;
                try {
                    const response = await fetch(`/user/online/print-ticket/${currentAppId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    });

                    const result = await response.json();
                    if (!result.success) {
                        printSuccess = false;
                        console.warn('Print failed:', result.message);
                        alert('Print failed. Please try again.');
                    }
                } catch (err) {
                    printSuccess = false;
                    console.error('Print request failed:', err);
                    alert('Network error. Print failed.');
                }

                // Close modal and reset
                modal.style.display = 'none';
                updateStatus('ready');
                input.focus();
                currentAppId = null;
                resetInactivityTimer();

                // ✅ Redirect to /kiosk after a short delay so user sees action
                setTimeout(() => {
                    window.location.href = '/kiosk';
                }, printSuccess ? 1500 : 3000); // 1.5s if success, 3s if error (to read message)
            });

            // Back Button Click
            backButton.addEventListener('click', () => {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/'; // fallback
                }
            });
        });
    </script>
</body>
</html>