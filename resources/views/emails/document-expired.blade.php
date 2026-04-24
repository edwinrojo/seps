<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Expired Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .alert {
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-text {
            color: #991b1b;
            margin: 0;
        }
        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
        }
        .detail-value {
            color: #6b7280;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Document Expiration Notice</h1>
        </div>

        <div class="content">
            <p>Hello {{ $supplier->business_name }},</p>

            <div class="alert">
                <p class="alert-text">
                    <strong>Alert:</strong> Your <strong>{{ $document->title }}</strong> document has expired.
                </p>
            </div>

            <p>One of your required documents has reached its expiration date. This may affect your supplier eligibility status if not updated promptly.</p>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Document Type:</span>
                    <span class="detail-value">{{ $document->title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Expired Date:</span>
                    <span class="detail-value">{{ $validityDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Business Name:</span>
                    <span class="detail-value">{{ $supplier->business_name }}</span>
                </div>
            </div>

            <p><strong>What should you do?</strong></p>
            <ul>
                <li>Log in to your supplier dashboard</li>
                <li>Navigate to your business profile</li>
                <li>Upload a new/renewed copy of the {{ $document->title }} document</li>
                <li>Your eligibility status will be reassessed once the new document is validated</li>
            </ul>

            <a href="{{ route('filament.supplier.pages.business-profile') }}" class="button">
                Update Document Now
            </a>
        </div>

        <div class="footer">
            <p>
                This is an automated notification from the SEPS (Supplier Eligibility Pre-Qualification System) platform.
                Please do not reply to this email. For assistance, please contact the system administrator.
            </p>
            <p>© {{ date('Y') }} SEPS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
