<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation #{{ $quote->reference_id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #4b5563; font-size: 9pt; line-height: 1.4; margin: 0; padding: 0; padding-bottom: 50px; background: #fafafa; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-white { color: #ffffff; }
        
        /* MODERN THEME COLORS */
        .text-brand { color: {{ $brandColor }}; }
        .bg-brand { background-color: {{ $brandColor }}; }
        .bg-brand-light { background-color: {{ $brandColor }}15; } /* 15% opacity trick usually ignored in DOMPDF except manually defined but works in mPDF */
        
        /* HEADER */
        .header-container { padding: 40px; background: #ffffff; margin-bottom: 20px; border-bottom: 3px solid {{ $brandColor }}; }
        .company-name { font-size: 24pt; font-weight: 800; color: #111827; margin: 0; letter-spacing: -0.5px; text-transform: uppercase; }
        .company-name span { color: {{ $brandColor }}; }
        .header-contact td { font-size: 8pt; color: #6b7280; padding-bottom: 5px; text-align: right; }
        .quote-badge { margin-top: 15px; font-size: 10pt; font-weight: 800; letter-spacing: 2px; color: {{ $brandColor }}; text-transform: uppercase; padding: 8px 16px; background: #f3f4f6; border-radius: 6px; display: inline-block; }

        /* INFO GRID */
        .info-grid { padding: 0 40px; width: 100%; margin-bottom: 30px; }
        .info-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
        .info-label { font-size: 8pt; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .info-value { font-size: 10pt; font-weight: 600; color: #1f2937; }
        .info-sub { font-size: 8.5pt; color: #6b7280; line-height: 1.5; margin-top: 4px; }

        /* TABLE */
        .items-section { padding: 0 40px; }
        .items-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
        .items-table th { background: #f9fafb; padding: 12px 20px; font-size: 8.5pt; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb; }
        .items-table td { padding: 15px 20px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .section-header td { background: {{ $brandColor }}10; color: {{ $brandColor }}; font-weight: bold; font-size: 9pt; text-transform: uppercase; padding: 10px 20px; border-bottom: 1px solid #e5e7eb; }
        
        .item-name { font-weight: 700; color: #111827; font-size: 10pt; margin-bottom: 4px; }
        .item-desc { font-size: 8.5pt; color: #6b7280; }
        .item-price, .item-qty, .item-total { font-weight: 600; color: #374151; }
        .item-img { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; }

        /* TOTALS */
        .bottom-section { padding: 30px 40px; width: 100%; }
        .summary-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; }
        .summary-row td { padding: 8px 0; font-size: 10pt; border-bottom: 1px dashed #e5e7eb; }
        .summary-row:last-child td { border-bottom: none; }
        .summary-label { color: #6b7280; }
        .summary-val { font-weight: 700; color: #111827; text-align: right; }
        .grand-total-row td { padding-top: 20px !important; }
        .grand-total-label { font-size: 11pt; font-weight: 800; color: #111827; text-transform: uppercase; }
        .grand-total-val { font-size: 16pt; font-weight: 800; color: {{ $brandColor }}; text-align: right; }

        /* EXTRA */
        .terms-box { background: #f9fafb; padding: 20px; border-radius: 12px; margin-top: 20px; border: 1px solid #e5e7eb; }
        .terms-title { font-size: 9pt; font-weight: bold; color: #111827; margin-bottom: 8px; text-transform: uppercase; }
        .rich-text { font-size: 8.5pt; color: #6b7280; line-height: 1.5; }
        .computer-footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 7.5pt; color: #9ca3af; }
    </style>
</head>
<body>

@php
    $currency = $companyProfile['currency_symbol'] ?? '₹';
    $taxConfig = $quote->tax_config_snapshot ?? \App\Models\CompanySetting::getTaxConfiguration();
    $isItemLevel = $quote->tax_mode === 'item_level';
    $taxLabel = $taxConfig['primary_label'] ?? 'Tax Vat';
    $isSplit = ($taxConfig['strategy'] ?? 'single') === 'split';
    
    $display = is_array($quote->display_settings) ? $quote->display_settings : [
        'show_images' => true, 'show_description' => true, 'show_tax' => true, 'show_sku' => false
    ];
    
    function getLocalPathModern($path) {
        if (empty($path)) return null;
        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/') || str_starts_with($cleanPath, 'images/')) return public_path($cleanPath);
        return public_path('storage/' . $cleanPath);
    }

    $groupedItems = [];
    foreach($quote->items->sortBy('sort_order') as $item) {
        $section = $item->section_name ?: 'Items';
        if (!isset($groupedItems[$section])) $groupedItems[$section] = [];
        $groupedItems[$section][] = $item;
    }
@endphp

    <div class="header-container">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="55%" valign="middle">
                    <h1 class="company-name">{{ explode(' ', $companyProfile['company_name'] ?? 'COMPANY')[0] }} <span>{{ implode(' ', array_slice(explode(' ', $companyProfile['company_name'] ?? 'COMPANY'), 1)) }}</span></h1>
                    <div class="quote-badge">
                        @if($quote->status === 'draft') QUOTATION (DRAFT)
                        @elseif($quote->status === 'sent') QUOTATION
                        @elseif($quote->status === 'accepted') ORDER CONFIRMATION
                        @else QUOTATION
                        @endif
                    </div>
                </td>
                <td width="45%" valign="middle">
                    <table class="header-contact" width="100%">
                        <tr><td>{{ $companyProfile['company_phone'] ?? '' }}</td></tr>
                        <tr><td>{{ $companyProfile['company_email'] ?? '' }}</td></tr>
                        @if(!empty($companyProfile['gstin']))<tr><td>GSTIN: {{ $companyProfile['gstin'] }}</td></tr>@endif
                        @if(!empty($companyProfile['company_address']))<tr><td style="color: #9ca3af; padding-top: 5px;">{{ strip_tags($companyProfile['company_address']) }}</td></tr>@endif
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td width="48%" valign="top">
                <div class="info-card" style="min-height: 120px;">
                    <div class="info-label">Billed To</div>
                    <div class="info-value">{{ $quote->customer_name }}</div>
                    <div class="info-sub">
                        @if($quote->customer_address){{ strip_tags($quote->customer_address) }}<br>@endif
                        @if($quote->customer_email){{ $quote->customer_email }}<br>@endif
                        @if($quote->customer_phone){{ $quote->customer_phone }}@endif
                    </div>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top">
                <div class="info-card" style="min-height: 120px;">
                    <table width="100%">
                        <tr><td class="info-label">Reference #</td><td class="info-value text-right">{{ $quote->reference_id }}</td></tr>
                        <tr><td class="info-label" style="padding-top: 15px;">Date Issued</td><td class="info-value text-right">{{ $quote->created_at->format('M d, Y') }}</td></tr>
                        @if($quote->valid_until)<tr><td class="info-label" style="padding-top: 15px;">Valid Until</td><td class="info-value text-right">{{ $quote->valid_until->format('M d, Y') }}</td></tr>@endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="items-section">
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="text-left" width="45%">Description</th>
                    <th class="text-right" width="15%">Price</th>
                    <th class="text-center" width="10%">Qty</th>
                    @if($isItemLevel && !empty($display['show_tax']))<th class="text-right" width="15%">Tax</th>@endif
                    <th class="text-right" width="15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedItems as $sectionName => $items)
                    @if($sectionName !== 'Items')
                        <tr class="section-header"><td colspan="5">{{ $sectionName }}</td></tr>
                    @endif
                    
                    @foreach($items as $item)
                        @php
                            $showImg = !empty($display['show_images']);
                            $showDesc = !empty($display['show_description']);
                            $imgLocalPath = getLocalPathModern($item->variant->image_path ?? $item->product->image_path ?? null);
                            $displayImgPath = ($imgLocalPath && file_exists($imgLocalPath)) ? $imgLocalPath : public_path('images/default_product.png');
                            $lineTotal = $item->price * $item->quantity;
                        @endphp
                        <tr>
                            <td class="text-left">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        @if($showImg)
                                            <td width="55" valign="top"><img src="{{ $displayImgPath }}" class="item-img"></td>
                                        @endif
                                        <td valign="top" style="{{ $showImg ? 'padding-left:15px;' : '' }}">
                                            <div class="item-name">{{ $item->product->name }} {{ $item->variant ? ' - ' . $item->variant->name : '' }}</div>
                                            @if($showDesc && !empty($item->product->description))
                                                <div class="item-desc">{{ substr(strip_tags($item->product->description), 0, 100) }}...</div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="item-price text-right">{{ $currency }}{{ number_format($item->price, 2) }}</td>
                            <td class="item-qty text-center">{{ $item->quantity }}</td>
                            @if($isItemLevel && !empty($display['show_tax']))
                                <td class="item-price text-right">{{ $item->tax_amount > 0 ? $currency.number_format($item->tax_amount, 2) : '-' }}</td>
                            @endif
                            <td class="item-total text-right">{{ $currency }}{{ number_format($lineTotal + ($isItemLevel ? $item->tax_amount : 0), 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="bottom-section" cellpadding="0" cellspacing="0">
        <tr>
            <td width="55%" valign="top">
                @if(!empty($quote->notes))
                <div class="terms-box" style="margin-top: 0; margin-bottom: 20px;">
                    <div class="terms-title">Additional Notes</div>
                    <div class="rich-text">{!! $quote->notes !!}</div>
                </div>
                @endif
                
                @if(!empty($quote->terms))
                <div class="terms-box" style="margin-top: 0;">
                    <div class="terms-title">Terms & Conditions</div>
                    <div class="rich-text">{!! $quote->terms !!}</div>
                </div>
                @endif
            </td>
            <td width="5%"></td>
            <td width="40%" valign="top">
                <div class="summary-card">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr class="summary-row"><td class="summary-label">Subtotal</td><td class="summary-val">{{ $currency }}{{ number_format($quote->subtotal, 2) }}</td></tr>
                        @if($quote->discount_amount > 0)
                            <tr class="summary-row"><td class="summary-label">Discount ({{ number_format($quote->discount_percentage, 1) }}%)</td><td class="summary-val" style="color:#ef4444">-{{ $currency }}{{ number_format($quote->discount_amount, 2) }}</td></tr>
                        @endif
                        @if($quote->tax_amount > 0)
                            <tr class="summary-row"><td class="summary-label">{{ $taxLabel }}</td><td class="summary-val">{{ $currency }}{{ number_format($quote->tax_amount, 2) }}</td></tr>
                        @endif

                        @if($quote->delivery_charge > 0)
                            <tr class="summary-row"><td class="summary-label">Delivery Charge</td><td class="summary-val">{{ $currency }}{{ number_format($quote->delivery_charge, 2) }}</td></tr>
                        @endif

                        @if($quote->additional_charge > 0)
                            <tr class="summary-row"><td class="summary-label">{{ $quote->additional_charge_label ?: 'Additional Charge' }}</td><td class="summary-val">{{ $currency }}{{ number_format($quote->additional_charge, 2) }}</td></tr>
                        @endif
                        <tr class="summary-row grand-total-row">
                            <td class="grand-total-label">Total Due</td>
                            <td class="grand-total-val">{{ $currency }}{{ number_format($quote->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="computer-footer">Generatied automatically by the System</div>
</body>
</html>
