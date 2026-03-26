<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMA Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px 20px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .property-info {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .agent-info {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comparative Market Analysis Report</h1>
    </div>

    <div class="content">
        <p>Hello @if($contact){{ $contact->first_name }}@endif,</p>

        <p>@if($agent){{ $agent->name }}@endif has prepared a Comparative Market Analysis (CMA) report for you.</p>

        @if($report->subject_property && isset($report->subject_property['address']))
        <div class="property-info">
            <strong>Property Address:</strong><br>
            {{ $report->subject_property['address'] }}
        </div>
        @endif

        @if($report->valuation_avg)
        <div class="property-info">
            <strong>Estimated Value:</strong><br>
            ${{ number_format($report->valuation_avg, 0) }}
            @if($report->valuation_low && $report->valuation_high)
                <br><small style="color: #6b7280;">Range: ${{ number_format($report->valuation_low, 0) }} - ${{ number_format($report->valuation_high, 0) }}</small>
            @endif
        </div>
        @endif

        <p>The complete CMA report is attached as a PDF document. This report includes:</p>
        <ul>
            <li>Detailed property information</li>
            <li>Comparable properties analysis</li>
            <li>Market adjustments</li>
            <li>Valuation summary</li>
        </ul>

        <p>If you have any questions about this report, please don't hesitate to reach out.</p>

        @if($agent)
        <div class="agent-info">
            <strong>Your Agent:</strong><br>
            {{ $agent->name }}<br>
            <a href="mailto:{{ $agent->email }}">{{ $agent->email }}</a>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>
            This email was sent from {{ config('app.name') }}<br>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
