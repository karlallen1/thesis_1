<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8fafc; padding: 30px; border: 1px solid #e2e8f0; }
        .pin-box { background: white; border: 3px solid #10b981; border-radius: 8px; padding: 30px; text-align: center; margin: 20px 0; }
        .pin-code { font-size: 48px; font-weight: bold; color: #059669; letter-spacing: 10px; font-family: monospace; }
        .info-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
        .requirements-box { background: white; border: 2px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .requirements-list { list-style: none; padding: 0; margin: 15px 0; }
        .requirements-list li { padding: 10px 0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px; }
        .requirements-list li:last-child { border-bottom: none; }
        .check-icon { color: #10b981; font-size: 18px; }
        .note-box { background: #e0f2fe; border-left: 4px solid #0284c7; padding: 12px; margin: 15px 0; font-size: 14px; }
        .steps { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📬 Document Submission PIN</h1>
        <p>North Caloocan City Hall - IoT Mailbox System</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $submission->first_name }}!</h2>
        
        <p>Your document submission for <strong>{{ $submission->service_type }}</strong> has been registered.</p>
        
        <div class="pin-box">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">Your Mailbox PIN Code</p>
            <div class="pin-code">{{ $pinCode }}</div>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">Use this PIN to open the mailbox</p>
        </div>
        
        <div class="info-box">
            <strong>⏰ Important:</strong> This PIN expires on <strong>{{ $expiresAt->format('F d, Y - h:i A') }}</strong>
        </div>

        <!-- Requirements Section -->
        <div class="requirements-box">
            <h3 style="margin: 0 0 15px 0; color: #1e3a8a;">📋 Required Documents</h3>
            <p style="margin: 0 0 10px 0; color: #64748b;">Please prepare the following documents before visiting:</p>
            <ul class="requirements-list">
                @foreach($requirements as $requirement)
                <li>
                    <span class="check-icon">✓</span>
                    <span>{{ $requirement }}</span>
                </li>
                @endforeach
            </ul>
            @if($note)
            <div class="note-box">
                <strong>Note:</strong> {{ $note }}
            </div>
            @endif
        </div>
        
        <div class="steps">
            <h3>📋 How to Submit Your Documents:</h3>
            <ol>
                <li><strong>Prepare your documents</strong> - Make sure you have all required documents listed above</li>
                <li><strong>Visit the City Hall</strong> - Go to the document submission mailbox location</li>
                <li><strong>Enter your PIN:</strong> <span style="background: #fef3c7; padding: 5px 10px; border-radius: 4px; font-family: monospace; font-size: 18px; font-weight: bold;">{{ $pinCode }}</span></li>
                <li><strong>Place your documents</strong> - The mailbox will open automatically</li>
                <li><strong>Wait for confirmation</strong> - You'll receive an update once processed</li>
            </ol>
        </div>
        
        <h3>Application Details:</h3>
        <ul>
            <li><strong>Name:</strong> {{ $submission->full_name }}</li>
            <li><strong>Service:</strong> {{ $submission->service_type }}</li>
            <li><strong>Contact:</strong> {{ $submission->contact }}</li>
            <li><strong>Submission ID:</strong> #{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}</li>
            <li><strong>Date Registered:</strong> {{ $submission->created_at->format('F d, Y - h:i A') }}</li>
        </ul>
        
        <p style="margin-top: 20px; padding: 15px; background: #e0f2fe; border-radius: 8px;">
            <strong>Note:</strong> This PIN can only be used once. After you submit your documents, the PIN will become invalid.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
        <p>North Caloocan City Hall | IoT Mailbox System</p>
    </div>
</body>
</html>