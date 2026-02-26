<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation #{{ $quote->reference_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333333;
            font-size: 9pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .w-full {
            width: 100%;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-white {
            color: #ffffff;
        }

        /* Matching colors from User's Image */
        .bg-dark {
            background-color: #222222;
        }

        .bg-blue {
            background-color:
                {{ $brandColor }}
            ;
        }

        /* Adjusted to match the specific blue logo */
        .text-blue {
            color:
                {{ $brandColor }}
            ;
        }

        .text-gray {
            color: #777777;
        }

        /* HEADER SECTION */
        .header-table {
            background-color: #222222;
            color: #ffffff;
            padding: 25px 35px;
            width: 100%;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color:
                {{ $brandColor }}
            ;
            /* Blue brand text */
            margin: 0;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .company-slogan {
            font-size: 9pt;
            letter-spacing: 2px;
            color: #cccccc;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .header-contact {
            font-size: 8pt;
            color: #aaaaaa;
            text-align: right;
        }

        .header-contact td {
            padding-left: 15px;
            padding-bottom: 3px;
        }

        .icon {
            color:
                {{ $brandColor }}
            ;
            font-size: 10pt;
            display: inline-block;
            width: 15px;
            text-align: center;
        }

        /* INVOICE TITLE BADGE */
        .title-container {
            width: 100%;
            text-align: right;
            padding-right: 35px;
            margin-top: 20px;
        }

        .title-badge {
            background-color:
                {{ $brandColor }}
            ;
            color: #ffffff;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 40px;
            border-radius: 20px;
            display: inline-block;
            letter-spacing: 2px;
        }

        /* INFO SECTION */
        .info-section {
            padding: 15px 35px 25px 35px;
            width: 100%;
        }

        .info-title {
            font-size: 11pt;
            font-weight: bold;
            color: #333333;
            margin-bottom: 5px;
        }

        .info-name {
            font-size: 13pt;
            font-weight: bold;
            color:
                {{ $brandColor }}
            ;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .info-details {
            font-size: 8.5pt;
        }

        .info-details td {
            vertical-align: top;
            padding-bottom: 2px;
        }

        .info-label {
            width: 60px;
            color: #555555;
            text-transform: uppercase;
        }

        .info-value {
            color: #333333;
        }

        .invoice-details-title {
            font-size: 14pt;
            font-weight: bold;
            color: #333333;
            border-bottom: 2px solid #555555;
            display: inline-block;
            padding-bottom: 3px;
            margin-bottom: 15px;
        }

        .invoice-meta td {
            font-size: 9pt;
            padding-bottom: 4px;
        }

        .meta-label {
            color: #777777;
            width: 80px;
        }

        .meta-value {
            font-weight: bold;
            color: #333333;
        }

        /* ITEMS TABLE */
        .items-container {
            padding: 0 35px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 10px 15px;
            font-size: 10pt;
            font-weight: bold;
            color: #ffffff;
        }

        /* As per image: Item Description is Dark grey, others are Blue */
        .th-desc {
            background-color: #444444;
            text-align: left;
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        .th-price {
            background-color:
                {{ $brandColor }}
            ;
            text-align: center;
        }

        .th-qty {
            background-color: #444444;
            text-align: center;
        }

        .th-total {
            background-color:
                {{ $brandColor }}
            ;
            text-align: center;
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .items-table td {
            padding: 18px 15px;
            font-size: 9pt;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        .item-name {
            font-weight: bold;
            color: #333333;
            font-size: 10pt;
        }

        .item-price,
        .item-qty,
        .item-total {
            font-weight: bold;
            color: #333333;
        }

        .item-image {
            width: 40px;
            height: 40px;
            border: 1px solid #eeeeee;
            padding: 2px;
        }

        /* TOTALS SECTION */
        .totals-container {
            padding: 20px 35px;
            width: 100%;
        }

        .payment-info {
            font-size: 8pt;
        }

        .payment-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 8px;
            border-bottom: 1px solid #333333;
            display: inline-block;
            padding-bottom: 2px;
        }

        .payment-table td {
            padding: 2px 0;
        }

        .pay-label {
            font-weight: bold;
            color: #333333;
            width: 80px;
        }

        .totals-calc {
            font-size: 10pt;
        }

        .totals-calc td {
            padding: 6px 15px;
        }

        .totals-calc .calc-label {
            color: #333333;
        }

        .totals-calc .calc-value {
            font-weight: bold;
            color: #333333;
            text-align: right;
        }

        .grand-total {
            background-color:
                {{ $brandColor }}
            ;
            color: #ffffff;
            font-size: 11pt;
            font-weight: bold;
        }

        .grand-total td {
            padding: 8px 15px;
        }

        .grand-total .gt-label {
            text-transform: uppercase;
        }

        .grand-total .gt-value {
            text-align: right;
        }

        /* FOOTER SECTION */
        .footer-section {
            padding: 30px 35px;
            width: 100%;
        }

        .thank-you {
            background-color: #333333;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 25px;
            border-radius: 15px;
            display: inline-block;
            font-size: 10pt;
        }

        .terms-conditions {
            margin-top: 20px;
            font-size: 8pt;
            color: #777777;
            line-height: 1.5;
            width: 250px;
        }

        .terms-title {
            font-weight: bold;
            color: #333333;
            font-size: 9pt;
            margin-bottom: 5px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333333;
            width: 200px;
            margin: 0 auto;
            margin-top: 40px;
            padding-top: 5px;
        }

        .sig-name {
            font-size: 12pt;
            font-weight: bold;
            color: #333333;
            text-transform: uppercase;
        }

        .sig-title {
            font-size: 8pt;
            color: #777777;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    @php
        $currency = $companyProfile['currency_symbol'] ?? '$';
        $taxConfig = $quote->tax_config_snapshot ?? \App\Models\CompanySetting::getTaxConfiguration();
        $isItemLevel = $quote->tax_mode === 'item_level';
        $taxLabel = $taxConfig['primary_label'] ?? 'Tax Vat';
        $isSplit = ($taxConfig['strategy'] ?? 'single') === 'split';

        function getLocalPath($path)
        {
            if (empty($path))
                return null;
            $cleanPath = ltrim($path, '/');
            return str_starts_with($cleanPath, 'storage/') ? public_path($cleanPath) : public_path('storage/' . $cleanPath);
        }
    @endphp

    <!-- Top Dark Header Area -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%" valign="top">
                <div class="company-name">{{ explode(' ', $companyProfile['company_name'] ?? 'BRAND NAME')[0] }} <span
                        class="text-white">{{ implode(' ', array_slice(explode(' ', $companyProfile['company_name'] ?? 'BRAND NAME'), 1)) }}</span>
                </div>
                <div class="company-slogan"></div>
            </td>
            <td width="50%" valign="top" align="right">
                <table class="header-contact">
                    <tr>
                        @if(!empty($companyProfile['company_phone']))
                            <td valign="middle"><span class="icon">📞</span> {{ $companyProfile['company_phone'] }}</td>
                        @endif
                        @if(!empty($companyProfile['company_email']))
                            <td valign="middle"><span class="icon">✉</span> {{ $companyProfile['company_email'] }}</td>
                        @endif
                    </tr>
                    <tr>
                        @if(!empty($companyProfile['gstin']))
                            <td valign="middle"><span class="icon">#</span> GSTIN: {{ $companyProfile['gstin'] }}</td>
                        @endif
                        @if(!empty($companyProfile['company_address']))
                            <td valign="middle"><span class="icon">📍</span>
                                {{ str_replace(["\r", "\n"], " ", substr(strip_tags($companyProfile['company_address']), 0, 40)) }}
                            </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Title Badge -->
    <div class="title-container">
        <div class="title-badge">{{ $quote->status === 'draft' ? 'QUOTATION' : 'INVOICE' }}</div>
    </div>

    <!-- Info Section (Bill To & Details) -->
    <table class="info-section">
        <tr>
            <td width="55%" valign="top">
                <div class="info-title">INVOICE TO:</div>
                <div class="info-name">{{ $quote->customer_name }}</div>

                <table class="info-details">
                    @if($quote->customer_phone)
                        <tr>
                            <td class="info-label">PHONE</td>
                            <td class="info-value">: {{ $quote->customer_phone }}</td>
                        </tr>
                    @endif
                    @if($quote->customer_email)
                        <tr>
                            <td class="info-label">EMAIL</td>
                            <td class="info-value">: {{ $quote->customer_email }}</td>
                        </tr>
                    @endif
                    @if($quote->customer_address)
                        <tr>
                            <td class="info-label">ADDRESS</td>
                            <td class="info-value">:
                                {{ str_replace(["\r", "\n"], " ", strip_tags($quote->customer_address)) }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
            <td width="45%" valign="top">
                <div align="center">
                    <div class="invoice-details-title">Invoice Details</div>
                </div>
                <table class="invoice-meta" width="100%">
                    <tr>
                        <td class="meta-label">Invoice</td>
                        <td class="meta-value">: #{{ $quote->reference_id }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Account</td>
                        <td class="meta-value">: {{ $companyProfile['bank_account_number'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Invoice Date</td>
                        <td class="meta-value">: {{ $quote->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @if($quote->valid_until)
                        <tr>
                            <td class="meta-label">Valid Until</td>
                            <td class="meta-value">: {{ $quote->valid_until->format('d/m/Y') }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <div class="items-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th class="th-desc" width="{{ $isItemLevel ? '35%' : '45%' }}">Item Description</th>
                    <th class="th-price" width="18%">Unit Price</th>
                    <th class="th-qty" width="12%">Quantity</th>
                    @if($isItemLevel)
                        <th class="th-price" width="10%">Tax</th>
                    @endif
                    <th class="th-total" width="{{ $isItemLevel ? '25%' : '25%' }}"
                        style="text-align: right; padding-right: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                    @php
                        $itemImgPath = $item->variant->image_path ?? $item->product->image_path ?? null;
                        $imgLocalPath = getLocalPath($itemImgPath);
                        $lineTotal = $item->price * $item->quantity;
                    @endphp
                    <tr>
                        <td class="text-left">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    @if($imgLocalPath && file_exists($imgLocalPath))
                                        <td width="55" valign="middle">
                                            <img src="{{ $imgLocalPath }}" class="item-image">
                                        </td>
                                    @endif
                                    <td valign="middle">
                                        <div class="item-name">{{ $item->product->name }}</div>
                                        @if($item->variant)
                                            <div style="font-size: 8pt; color: #777; margin-top: 2px;">
                                                {{ $item->variant->name }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="item-price text-center" style="vertical-align: middle;">
                            {{ $currency }}{{ number_format($item->price, 2) }}
                        </td>
                        <td class="item-qty text-center" style="vertical-align: middle;">
                            {{ str_pad($item->quantity, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        @if($isItemLevel)
                            <td class="item-price text-center" style="vertical-align: middle;">
                                @if($item->tax_amount > 0)
                                    @php
                                        $displayRate = $item->tax_rate > 0 ? $item->tax_rate : (($item->tax_amount / max(0.01, $lineTotal)) * 100);
                                    @endphp
                                    {{ $currency }}{{ number_format($item->tax_amount, 2) }}
                                    <br><span style="font-size: 7.5pt; color: #888;">({{ number_format($displayRate, 1) }}%)</span>
                                @else
                                    <span style="color: #ccc;">-</span>
                                @endif
                            </td>
                        @endif
                        <td class="item-total text-right" style="vertical-align: middle; padding-right: 25px;">
                            {{ $currency }}{{ number_format($lineTotal + ($isItemLevel ? $item->tax_amount : 0), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals Section -->
    <table class="totals-container">
        <tr>
            <td width="50%" valign="top">
                <div class="payment-info">
                    <div class="payment-title">Payment Method</div>
                    <table class="payment-table">
                        <tr>
                            <td class="pay-label">Account #</td>
                            <td>: {{ $companyProfile['bank_account_number'] ?? '0000 0000 0000' }}</td>
                        </tr>
                        <tr>
                            <td class="pay-label">A/C Name</td>
                            <td>: {{ $companyProfile['bank_name'] ?? 'Company Bank Name' }}</td>
                        </tr>
                        <tr>
                            <td class="pay-label">Bank IFSC</td>
                            <td>: {{ $companyProfile['bank_ifsc'] ?? 'XXX-XXX' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="50%" valign="top" align="right">
                <table class="totals-calc" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="calc-label" width="50%">Sub Total</td>
                        <td class="calc-value" width="50%">{{ $currency }}{{ number_format($quote->subtotal, 2) }}</td>
                    </tr>
                    @if($quote->discount_amount > 0)
                        <tr>
                            <td class="calc-label">Discount ({{ number_format($quote->discount_percentage, 1) }}%)</td>
                            <td class="calc-value">-{{ $currency }}{{ number_format($quote->discount_amount, 2) }}</td>
                        </tr>
                    @endif

                    @if($isSplit && $quote->tax_amount > 0)
                        @php
                            $secondaryLabels = $taxConfig['secondary_labels'] ?? ['CGST', 'SGST'];
                            $splitAmount = $quote->tax_amount / max(1, count($secondaryLabels));
                        @endphp
                        @foreach($secondaryLabels as $label)
                            <tr>
                                <td class="calc-label">{{ $label }}</td>
                                <td class="calc-value">{{ $currency }}{{ number_format($splitAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif($quote->tax_amount > 0)
                        <tr>
                            <td class="calc-label">{{ $taxLabel }}</td>
                            <td class="calc-value">{{ $currency }}{{ number_format($quote->tax_amount, 2) }}</td>
                        </tr>
                    @endif

                    <tr class="grand-total">
                        <td class="gt-label">GRAND TOTAL</td>
                        <td class="gt-value">{{ $currency }}{{ number_format($quote->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Section -->
    <table class="footer-section">
        <tr>
            <td width="60%" valign="bottom">
                <div class="thank-you">Thank You For Your Business!</div>
                @if($quote->notes)
                    <div class="terms-conditions">
                        <div class="terms-title">Terms & Conditions:</div>
                        {!! nl2br(e($quote->notes)) !!}
                    </div>
                @endif
            </td>
            <td width="40%" valign="bottom" align="center">
                <div class="signature-box">
                    <!-- Dummy signature styled like script logic could go here -->
                    <div
                        style="font-family: 'Times New Roman', serif; font-size: 24pt; color: #333; margin-bottom: -15px; font-style: italic;">
                        {{ explode(' ', $companyProfile['company_name'] ?? 'Authorized Signatory')[0] }}
                    </div>
                    <div class="signature-line">
                        <div class="sig-name">{{ $companyProfile['company_name'] ?? 'AUTHORIZED SIGNATURE' }}</div>
                        <div class="sig-title">COMPANY MANAGER</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>