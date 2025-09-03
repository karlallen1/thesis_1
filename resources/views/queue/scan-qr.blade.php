<!-- resources/views/queue/scan-qr.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scan QR to Join Queue</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: white;
            text-align: center;
            padding: 40px;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 600px;
            background: #1e293b;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        p {
            color: #cbd5e1;
            margin-bottom: 2rem;
        }
        #scanner-input {
            width: 1px;
            height: 1px;
            opacity: 0;
            position: absolute;
            left: -9999px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Scan Your QR Code</h1>
        <p>Present your QR code to the scanner.</p>
        <input type="text" id="scanner-input" autocomplete="off" autofocus />
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'scanner-input';
    input.style.position = 'absolute';
    input.style.opacity = 0;
    document.body.appendChild(input);
    input.focus();

    let buffer = '';
    let timeout;

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const scannedUrl = buffer.trim();
            if (scannedUrl && /^https?:\/\//.test(scannedUrl)) {
                window.location.href = scannedUrl;
            }
            buffer = '';
        } else if (e.key.length === 1) {
            buffer += e.key;
        }

        clearTimeout(timeout);
        timeout = setTimeout(() => { buffer = ''; }, 1000);
    });

    setInterval(() => input.focus(), 500);
});
</script>
</body>
</html>