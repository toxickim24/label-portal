<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CMA Report - {{ $subject['address'] ?? 'Property' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            padding-bottom: 100px;
        }
        .content-wrapper {
            padding-left: 40px;
            padding-right: 40px;
        }
        .header {
            background-color: #B49106;
            color: white;
            padding: 30px 40px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 12pt;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 16pt;
            font-weight: bold;
            color: #B49106;
            border-bottom: 2px solid #B49106;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .property-details {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .property-details table {
            width: 100%;
        }
        .property-details td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .property-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .comparables-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .comparables-table th {
            background-color: #B49106;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10pt;
        }
        .comparables-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }
        .comparables-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .valuation-box {
            background-color: #FFFBEB;
            border: 2px solid #B49106;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }
        .valuation-box .main-value {
            font-size: 32pt;
            font-weight: bold;
            color: #B49106;
            margin: 10px 0;
        }
        .valuation-box .range {
            font-size: 12pt;
            color: #6b7280;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #B49106;
            color: white;
            padding: 20px 40px;
            border-top: 3px solid #8B7005;
            font-size: 9pt;
        }
        .footer-content {
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }
        .footer-center {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        .footer-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .footer-agent-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            display: block;
        }
        .page-number:after {
            content: counter(page);
        }
        .disclaimer {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #FCD34D;
        }
        .agent-info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        .confidence-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 10px;
        }
        .confidence-very-high {
            background-color: #059669;
            color: white;
        }
        .confidence-high {
            background-color: #10B981;
            color: white;
        }
        .confidence-moderate {
            background-color: #F59E0B;
            color: white;
        }
        .confidence-low {
            background-color: #EF4444;
            color: white;
        }
        .valuation-summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #F3F4F6;
            border-left: 4px solid #B49106;
            font-size: 10pt;
            line-height: 1.6;
            text-align: justify;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comparative Market Analysis</h1>
        <div class="subtitle">{{ $branding['app_name'] ?? config('app.name') }}</div>
        <div class="subtitle">Prepared: {{ $generatedDate }}</div>
    </div>

    <div class="content-wrapper">
    <!-- Executive Summary -->
    <div class="section">
        <h2 class="section-title">Executive Summary</h2>
        @if($report->valuation_avg)
        <div class="valuation-box">
            <div style="font-size: 14pt; margin-bottom: 5px;">Estimated Market Value</div>
            <div class="main-value">${{ number_format($report->valuation_avg, 0) }}</div>
            @if($report->valuation_low && $report->valuation_high)
            <div class="range">
                Value Range: ${{ number_format($report->valuation_low, 0) }} - ${{ number_format($report->valuation_high, 0) }}
            </div>
            @endif
        </div>

        @if(isset($confidenceScore) && $confidenceScore)
        <div style="text-align: center; margin-top: 15px;">
            <span style="font-weight: bold; font-size: 11pt;">Confidence Level:</span>
            <span class="confidence-badge confidence-{{ strtolower(str_replace(' ', '-', $confidenceScore['level'])) }}">
                {{ $confidenceScore['level'] }} ({{ $confidenceScore['score'] }}/5.0)
            </span>
        </div>
        @endif
        @endif

        @if(isset($valuationSummary) && $valuationSummary)
        <div class="valuation-summary">
            {{ $valuationSummary }}
        </div>
        @else
        <p>This Comparative Market Analysis (CMA) was prepared to provide an estimated market value for the subject property. The valuation is based on recent comparable sales in the area, adjusted for differences in property characteristics.</p>
        @endif
    </div>

    <!-- Subject Property -->
    <div class="section">
        <h2 class="section-title">Subject Property</h2>
        <div class="property-details">
            <table>
                <tr>
                    <td>Address:</td>
                    <td><strong>{{ $subject['address'] ?? 'N/A' }}</strong></td>
                </tr>
                @if(isset($subject['square_feet']))
                <tr>
                    <td>Square Feet:</td>
                    <td>{{ number_format($subject['square_feet']) }} sq ft</td>
                </tr>
                @endif
                @if(isset($subject['bedrooms']))
                <tr>
                    <td>Bedrooms:</td>
                    <td>{{ $subject['bedrooms'] }}</td>
                </tr>
                @endif
                @if(isset($subject['bathrooms']))
                <tr>
                    <td>Bathrooms:</td>
                    <td>{{ $subject['bathrooms'] }}</td>
                </tr>
                @endif
                @if(isset($subject['lot_size']))
                <tr>
                    <td>Lot Size:</td>
                    <td>{{ number_format($subject['lot_size']) }} sq ft</td>
                </tr>
                @endif
                @if(isset($subject['year_built']))
                <tr>
                    <td>Year Built:</td>
                    <td>{{ $subject['year_built'] }}</td>
                </tr>
                @endif
                @if(isset($subject['property_type']))
                <tr>
                    <td>Property Type:</td>
                    <td>{{ ucfirst($subject['property_type']) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Comparable Properties -->
    @if(count($comparables) > 0)
    <div class="section">
        <h2 class="section-title">Comparable Properties</h2>
        <p style="margin-bottom: 15px;">The following properties were used as comparables for this analysis:</p>

        <table class="comparables-table">
            <thead>
                <tr>
                    <th>Address</th>
                    <th>Sale Price</th>
                    <th>Sq Ft</th>
                    <th>$/Sq Ft</th>
                    <th>Beds</th>
                    <th>Baths</th>
                    <th>Distance</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comparables as $index => $comp)
                <tr>
                    <td>{{ $comp['data']['address'] ?? 'N/A' }}</td>
                    <td>${{ number_format($comp['data']['sale_price'] ?? 0) }}</td>
                    <td>{{ number_format($comp['data']['square_feet'] ?? 0) }}</td>
                    <td>{{ $comp['price_per_sqft'] ? '$' . number_format($comp['price_per_sqft'], 2) : 'N/A' }}</td>
                    <td>{{ $comp['data']['bedrooms'] ?? '-' }}</td>
                    <td>{{ $comp['data']['bathrooms'] ?? '-' }}</td>
                    <td>{{ isset($comp['data']['distance']) ? number_format($comp['data']['distance'], 1) . ' mi' : '-' }}</td>
                    <td>{{ isset($comp['data']['sale_date']) ? date('m/d/Y', strtotime($comp['data']['sale_date'])) : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Adjustments -->
    <div class="section">
        <h2 class="section-title">Adjustments & Adjusted Values</h2>
        <p style="margin-bottom: 15px;">Adjustments were made to comparable properties to account for differences with the subject property:</p>

        <table class="comparables-table">
            <thead>
                <tr>
                    <th>Comparable</th>
                    <th>Sale Price</th>
                    <th>Total Adjustments</th>
                    <th>Adjusted Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comparables as $index => $comp)
                <tr>
                    <td>{{ $comp['data']['address'] ?? 'Comp #' . ($index + 1) }}</td>
                    <td>${{ number_format($comp['data']['sale_price'] ?? 0) }}</td>
                    <td style="color: {{ $comp['total_adjustment'] >= 0 ? '#059669' : '#DC2626' }}">
                        {{ $comp['total_adjustment'] >= 0 ? '+' : '' }}${{ number_format($comp['total_adjustment']) }}
                    </td>
                    <td><strong>${{ number_format($comp['adjusted_price']) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Valuation Summary -->
    @if($report->valuation_avg)
    <div class="section">
        <h2 class="section-title">Valuation Summary</h2>
        <p>Based on the analysis of {{ count($comparables) }} comparable properties, the estimated market value for the subject property is:</p>

        <table style="width: 100%; margin: 20px 0;">
            <tr style="background-color: #f9fafb;">
                <td style="padding: 15px; border: 1px solid #e5e7eb;"><strong>Low Estimate:</strong></td>
                <td style="padding: 15px; border: 1px solid #e5e7eb; text-align: right;">${{ number_format($report->valuation_low, 0) }}</td>
            </tr>
            <tr style="background-color: #FFFBEB;">
                <td style="padding: 15px; border: 1px solid #e5e7eb;"><strong>Average Estimate:</strong></td>
                <td style="padding: 15px; border: 1px solid #e5e7eb; text-align: right; font-size: 14pt; color: #B49106;"><strong>${{ number_format($report->valuation_avg, 0) }}</strong></td>
            </tr>
            <tr style="background-color: #f9fafb;">
                <td style="padding: 15px; border: 1px solid #e5e7eb;"><strong>High Estimate:</strong></td>
                <td style="padding: 15px; border: 1px solid #e5e7eb; text-align: right;">${{ number_format($report->valuation_high, 0) }}</td>
            </tr>
        </table>

        @if($report->notes)
        <div style="margin-top: 20px;">
            <strong>Notes:</strong>
            <p style="margin-top: 10px;">{{ $report->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Agent Information -->
    @if($agent)
    <div class="agent-info">
        <h3 style="margin-bottom: 10px;">Prepared By:</h3>
        <p><strong>{{ $agent->name }}</strong></p>
        <p>{{ $agent->email }}</p>
        @if($contact)
        <p style="margin-top: 15px;">
            <strong>Prepared For:</strong><br>
            {{ $contact->full_name }}<br>
            {{ $contact->email }}
        </p>
        @endif
    </div>
    @endif

    <!-- Disclaimer -->
    <div class="disclaimer">
        <strong>Disclaimer:</strong> This Comparative Market Analysis is an opinion of value and is not an appraisal. The estimated value is based on recent comparable sales and current market conditions. Actual market value may vary based on market changes, property condition, and other factors. This report is intended for informational purposes only and should not be used as the sole basis for any financial or real estate decisions.
    </div>
    </div><!-- End content-wrapper -->

    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                @if(file_exists(public_path('images/tim-mcmullen.png')))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/tim-mcmullen.png'))) }}" alt="Tim McMullen" class="footer-agent-photo">
                @endif
            </div>
            <div class="footer-center">
                <div style="font-weight: bold; margin-bottom: 3px;">Tim McMullen · REALTOR®</div>
                <div style="margin-bottom: 3px;">McMullen Properties</div>
                <div style="margin-bottom: 3px;">tim@mcmullen.properties · 415.691.9272</div>
                <div style="font-size: 8pt; opacity: 0.9; margin-top: 5px;">Information derived from public records and MLS data. Measurements are approximate and not guaranteed.</div>
            </div>
            <div class="footer-right">
                Page <span class="page-number"></span>
            </div>
        </div>
    </div>
</body>
</html>
