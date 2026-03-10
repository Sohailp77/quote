<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation #{{ $quote->reference_id }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 9pt; line-height: 1.2; margin: 0; padding: 0; padding-bottom: 50px; background: #fff; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-white { color: #ffffff; }
        
        /* BRAND COLORS */
        .bg-dark { background-color: #222222; }
        .bg-brand { background-color: {{ $brandColor }}; }
        .text-brand { color: {{ $brandColor }}; }
        .text-gray { color: #777777; }

        /* HEADER */
        .header-table { background-color: #222222; color: #ffffff; padding: 25px 35px; width: 100%; }
        .company-name { font-size: 20pt; font-weight: bold; color: {{ $brandColor }}; margin: 0; line-height: 1.1; text-transform: uppercase; }
        .company-slogan { font-size: 9pt; letter-spacing: 2px; color: #cccccc; text-transform: uppercase; margin-top: 5px; }
        .header-contact { font-size: 8pt; color: #aaaaaa; text-align: right; }
        .header-contact td { padding-left: 15px; padding-bottom: 3px; }
        .icon { color: {{ $brandColor }}; font-size: 10pt; display: inline-block; width: 15px; text-align: center; }

        /* TITLE BADGE */
        .title-container { width: 100%; text-align: right; padding-right: 35px; margin-top: 20px; }
        .title-badge { background-color: {{ $brandColor }}; color: #ffffff; font-size: 16pt; font-weight: bold; text-transform: uppercase; padding: 5px 40px; border-radius: 20px; display: inline-block; letter-spacing: 2px; }

        /* INFO SECTION */
        .info-section { padding: 15px 35px 25px 35px; width: 100%; }
        .info-title { font-size: 11pt; font-weight: bold; color: #333; margin-bottom: 5px; }
        .info-name { font-size: 13pt; font-weight: bold; color: {{ $brandColor }}; text-transform: uppercase; margin-bottom: 8px; }
        .info-details { font-size: 8.5pt; }
        .info-details td { vertical-align: top; padding-bottom: 2px; }
        .info-label { width: 60px; color: #555; text-transform: uppercase; }
        .info-value { color: #333; }
        
        /* QUOTE META */
        .invoice-details-title { font-size: 14pt; font-weight: bold; color: #333; border-bottom: 2px solid #555; display: inline-block; padding-bottom: 3px; margin-bottom: 15px; }
        .invoice-meta td { font-size: 9pt; padding-bottom: 4px; }
        .meta-label { color: #777; width: 80px; }
        .meta-value { font-weight: bold; color: #333; }

        /* ITEMS TABLE */
        .items-container { padding: 0 35px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { padding: 10px 15px; font-size: 10pt; font-weight: bold; color: #ffffff; }
        .th-desc { background-color: #444; text-align: left; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
        .th-price { background-color: {{ $brandColor }}; text-align: center; }
        .th-qty { background-color: #444; text-align: center; }
        .th-total { background-color: {{ $brandColor }}; text-align: center; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
        .items-table td { padding: 15px; font-size: 9pt; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        
        .section-header td { background-color: #f8f9fa; font-weight: bold; color: #555; padding: 8px 15px; font-size: 10pt; text-transform: uppercase; border-bottom: 2px solid #ddd; }
        
        .item-name { font-weight: bold; color: #333; font-size: 10pt; }
        .item-desc { font-size: 8pt; color: #777; margin-top: 3px; }
        .item-price, .item-qty, .item-total { font-weight: bold; color: #333; }
        .item-img { width: 40px; height: 40px; border: 1px solid #eee; padding: 2px; object-fit: cover; }

        /* TOTALS */
        .totals-container { padding: 20px 35px; width: 100%; }
        .payment-title { font-weight: bold; font-size: 10pt; margin-bottom: 8px; border-bottom: 1px solid #333; display: inline-block; padding-bottom: 2px; }
        .payment-table td { padding: 2px 0; font-size: 8pt; }
        .pay-label { font-weight: bold; color: #333; width: 80px; }
        .totals-calc { font-size: 10pt; }
        .totals-calc td { padding: 6px 15px; }
        .calc-label { color: #333; }
        .calc-value { font-weight: bold; color: #333; text-align: right; }
        .grand-total { background-color: {{ $brandColor }}; color: #ffffff; font-size: 11pt; font-weight: bold; }
        .grand-total td { padding: 8px 15px; }
        
        /* FOOTER */
        .notes-section { padding: 20px 35px; margin-top: 10px; }
        .notes-title { font-weight: bold; font-size: 11pt; color: #333; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .rich-text { font-size: 8.5pt; color: #555; line-height: 1.4; }
        .rich-text p { margin-top: 0; margin-bottom: 8px; }
        .rich-text ul, .rich-text ol { margin-top: 0; padding-left: 20px; }

        .computer-footer { position: fixed; bottom: 10px; left: 35px; right: 35px; border-top: 1px solid #e0e0e0; padding-top: 8px; text-align: center; font-size: 7.5pt; color: #aaa; letter-spacing: 0.5px; }
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
    
    function getLocalPath($path) {
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

    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%" valign="top">
                <div class="company-name">{{ explode(' ', $companyProfile['company_name'] ?? 'COMPANY')[0] }} <span class="text-white">{{ implode(' ', array_slice(explode(' ', $companyProfile['company_name'] ?? 'COMPANY'), 1)) }}</span></div>
            </td>
            <td width="50%" valign="top" align="right">
                <table class="header-contact">
                    <tr>
                        @if(!empty($companyProfile['company_phone']))<td valign="middle"><span class="icon">📞</span> {{ $companyProfile['company_phone'] }}</td>@endif
                        @if(!empty($companyProfile['company_email']))<td valign="middle"><span class="icon">✉</span> {{ $companyProfile['company_email'] }}</td>@endif
                    </tr>
                    <tr>
                        @if(!empty($companyProfile['gstin']))<td valign="middle"><span class="icon">#</span> GSTIN: {{ $companyProfile['gstin'] }}</td>@endif
                        @if(!empty($companyProfile['company_address']))<td valign="middle"><span class="icon">📍</span> {{ str_replace(["\r", "\n"], " ", substr(strip_tags($companyProfile['company_address']), 0, 40)) }}</td>@endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title-container">
        <div class="title-badge">
            @if($quote->status === 'draft') QUOTATION (DRAFT)
            @elseif($quote->status === 'sent') QUOTATION
            @elseif($quote->status === 'accepted') ORDER CONFIRMATION
            @else QUOTATION
            @endif
        </div>
    </div>

    <table class="info-section">
        <tr>
            <td width="55%" valign="top">
                <div class="info-title">BILL TO:</div>
                <div class="info-name">{{ $quote->customer_name }}</div>
                <table class="info-details">
                    @if($quote->customer_phone)<tr><td class="info-label">PHONE</td><td class="info-value">: {{ $quote->customer_phone }}</td></tr>@endif
                    @if($quote->customer_email)<tr><td class="info-label">EMAIL</td><td class="info-value">: {{ $quote->customer_email }}</td></tr>@endif
                    @if($quote->customer_address)<tr><td class="info-label">ADDRESS</td><td class="info-value">: {{ strip_tags($quote->customer_address) }}</td></tr>@endif
                </table>
            </td>
            <td width="45%" valign="top">
                <div align="center"><div class="invoice-details-title">Details</div></div>
                <table class="invoice-meta" width="100%">
                    <tr><td class="meta-label">Quote #</td><td class="meta-value">: {{ $quote->reference_id }}</td></tr>
                    <tr><td class="meta-label">Date</td><td class="meta-value">: {{ $quote->created_at->format('d/m/Y') }}</td></tr>
                    @if($quote->valid_until)<tr><td class="meta-label">Valid Until</td><td class="meta-value">: {{ $quote->valid_until->format('d/m/Y') }}</td></tr>@endif
                </table>
            </td>
        </tr>
    </table>

    <div class="items-container">
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="th-desc" width="40%">Description</th>
                    <th class="th-price" width="15%">Price</th>
                    <th class="th-qty" width="15%">Quantity</th>
                    @if($isItemLevel && !empty($display['show_tax']))<th class="th-price" width="15%">Tax</th>@endif
                    <th class="th-total" width="15%">Line Total</th>
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
                            
                            $imgLocalPath = getLocalPath($item->variant->image_path ?? $item->product->image_path ?? null);
                            $displayImgPath = ($imgLocalPath && file_exists($imgLocalPath)) ? $imgLocalPath : public_path('images/default_product.png');
                            $lineTotal = $item->price * $item->quantity;
                        @endphp
                        <tr>
                            <td class="text-left">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        @if($showImg)
                                            <td width="55" valign="top"><img src="{{ $displayImgPath }}" class="item-img"></td>
                                        @endif
                                        <td valign="top" style="{{ $showImg ? 'padding-left:10px;' : '' }}">
                                            <div class="item-name">{{ $item->product->name }} {{ $item->variant ? ' - ' . $item->variant->name : '' }}</div>
                                            @if($showDesc && !empty($item->product->description))
                                                <div class="item-desc">{{ substr(strip_tags($item->product->description), 0, 100) }}...</div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="item-price text-center">{{ $currency }}{{ number_format($item->price, 2) }}</td>
                            <td class="item-qty text-center">{{ $item->quantity }}</td>
                            @if($isItemLevel && !empty($display['show_tax']))
                                <td class="item-price text-center">
                                    @if($item->tax_amount > 0)
                                        {{ $currency }}{{ number_format($item->tax_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td class="item-total text-right" style="padding-right: 25px;">
                                {{ $currency }}{{ number_format($lineTotal + ($isItemLevel ? $item->tax_amount : 0), 2) }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="totals-container">
        <tr>
            <td width="50%" valign="top">
                @if(!empty($quote->terms))
                <div style="padding-right: 20px;">
                    <div class="payment-title">Terms & Conditions</div>
                    <div class="rich-text">{!! $quote->terms !!}</div>
                </div>
                @else
                <div class="payment-info">
                    <div class="payment-title">Payment Info</div>
                    <table class="payment-table">
                        <tr><td class="pay-label">Account #</td><td>: {{ $companyProfile['bank_account_number'] ?? 'N/A' }}</td></tr>
                        <tr><td class="pay-label">A/C Name</td><td>: {{ $companyProfile['bank_name'] ?? 'N/A' }}</td></tr>
                        <tr><td class="pay-label">Bank IFSC</td><td>: {{ $companyProfile['bank_ifsc'] ?? 'N/A' }}</td></tr>
                    </table>
                </div>
                @endif
            </td>
            <td width="50%" valign="top" align="right">
                <table class="totals-calc" width="100%" cellpadding="0" cellspacing="0">
                    <tr><td class="calc-label" width="50%">Sub Total</td><td class="calc-value" width="50%">{{ $currency }}{{ number_format($quote->subtotal, 2) }}</td></tr>
                    @if($quote->discount_amount > 0)
                        <tr><td class="calc-label">Discount ({{ number_format($quote->discount_percentage, 1) }}%)</td><td class="calc-value">-{{ $currency }}{{ number_format($quote->discount_amount, 2) }}</td></tr>
                    @endif

                    @if($isSplit && $quote->tax_amount > 0)
                        @foreach(($taxConfig['secondary_labels'] ?? ['CGST', 'SGST']) as $label)
                            <tr><td class="calc-label">{{ $label }}</td><td class="calc-value">{{ $currency }}{{ number_format($quote->tax_amount / count($taxConfig['secondary_labels']), 2) }}</td></tr>
                        @endforeach
                    @elseif($quote->tax_amount > 0)
                        <tr><td class="calc-label">{{ $taxLabel }}</td><td class="calc-value">{{ $currency }}{{ number_format($quote->tax_amount, 2) }}</td></tr>
                    @endif

                    @if($quote->delivery_charge > 0)
                        <tr><td class="calc-label">Delivery Charge</td><td class="calc-value">{{ $currency }}{{ number_format($quote->delivery_charge, 2) }}</td></tr>
                    @endif

                    @if($quote->additional_charge > 0)
                        <tr><td class="calc-label">{{ $quote->additional_charge_label ?: 'Additional Charge' }}</td><td class="calc-value">{{ $currency }}{{ number_format($quote->additional_charge, 2) }}</td></tr>
                    @endif

                    <tr class="grand-total">
                        <td class="gt-label">GRAND TOTAL</td>
                        <td class="gt-value text-right" style="padding-right: 25px;">{{ $currency }}{{ number_format($quote->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(!empty($quote->notes))
    <div class="notes-section">
        <div class="notes-title">Notes</div>
        <div class="rich-text">{!! $quote->notes !!}</div>
    </div>
    @endif

<div class="computer-footer">
    &#128187; This is a computer generated document and does not require a physical signature.
</div>
</body>
</html>
