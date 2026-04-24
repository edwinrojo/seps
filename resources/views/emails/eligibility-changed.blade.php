<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligibility Status Changed</title>
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
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 3px solid;
        }
        .header.eligible {
            border-color: #16a34a;
        }
        .header.ineligible {
            border-color: #ea580c;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header.eligible h1 {
            color: #16a34a;
        }
        .header.ineligible h1 {
            color: #ea580c;
        }
        .content {
            margin-bottom: 30px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .alert.eligible {
            background-color: #dcfce7;
            border-color: #16a34a;
        }
        .alert.ineligible {
            background-color: #ffedd5;
            border-color: #ea580c;
        }
        .alert-text {
            margin: 0;
            font-weight: 600;
        }
        .alert.eligible .alert-text {
            color: #166534;
        }
        .alert.ineligible .alert-text {
            color: #9a3412;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin: 15px 0;
        }
        .status-badge.eligible {
            background-color: #dcfce7;
            color: #166534;
            border: 2px solid #16a34a;
        }
        .status-badge.ineligible {
            background-color: #ffedd5;
            color: #9a3412;
            border: 2px solid #ea580c;
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
        .reasons {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .reasons h4 {
            margin: 0 0 10px 0;
            color: #92400e;
        }
        .reasons ul {
            margin: 0;
            padding-left: 20px;
        }
        .reasons li {
            margin: 5px 0;
            color: #78350f;
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
        <div class="header {{ $status }}">
            <h1>Eligibility Status Changed</h1>
        </div>

        <div class="content">
            <p>Hello {{ $supplier->business_name }},</p>

            <div class="alert {{ $status }}">
                <p class="alert-text">
                    @if ($isNowEligible)
                        ✓ Congratulations! Your supplier account is now eligible for procurement.
                    @else
                        Your supplier account eligibility status has changed to ineligible.
                    @endif
                </p>
            </div>

            <div class="status-badge {{ $status }}">
                Status: {{ ucfirst($status) }}
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Business Name:</span>
                    <span class="detail-value">{{ $supplier->business_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Supplier Type:</span>
                    <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $supplier->supplier_type->value)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Updated:</span>
                    <span class="detail-value">{{ now()->format('F d, Y') }}</span>
                </div>
            </div>

            @if (!empty($reasons))
                <div class="reasons">
                    <h4>{{ $isNowEligible ? 'Eligibility Requirements Met:' : 'Reason(s) for Ineligibility:' }}</h4>
                    <ul>
                        @foreach ($reasons as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($isNowEligible)
                <p>
                    You are now able to participate in government procurement activities. To maintain your eligibility status,
                    ensure that all your required documents are kept up-to-date and that all information in your profile remains accurate.
                </p>
            @else
                <p>
                    To regain eligibility, please review the requirements and ensure that all necessary documents are uploaded and validated,
                    and that all required information is complete and accurate in your profile.
                </p>
            @endif

            <a href="{{ route('filament.supplier.pages.business-profile') }}" class="button">
                View Your Profile
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
