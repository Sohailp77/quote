<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Summary</title>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background: #0f172a; padding: 40px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; font-size: 14px; opacity: 0.7; font-weight: 600; }
        .content { padding: 40px; }
        .section-title { font-size: 12px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        .stat-grid { display: grid; grid-template-cols: 1fr 1fr; gap: 20px; margin-bottom: 32px; }
        .stat-card { background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9; }
        .stat-label { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 4px; }
        .stat-value { font-size: 20px; font-weight: 800; color: #0f172a; }
        .low-stock-list { background: #fff1f2; border-radius: 16px; padding: 20px; border: 1px solid #ffe4e6; }
        .low-stock-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #fecdd3; }
        .low-stock-item:last-child { border-bottom: none; }
        .item-name { font-size: 14px; font-weight: 700; color: #9f1239; }
        .item-qty { font-size: 13px; font-weight: 800; color: #e11d48; background: #ffffff; padding: 2px 8px; rounded: 6px; }
        .footer { padding: 32px; text-align: center; font-size: 12px; color: #94a3b8; background: #f8fafc; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 800; font-size: 14px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daily Business Summary</h1>
            <p>{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="content">
            <div class="section-title">Today's Performance</div>
            <div style="margin-bottom: 32px;">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%" style="padding-right: 10px;">
                            <div class="stat-card">
                                <div class="stat-label">New Quotations</div>
                                <div class="stat-value">{{ $data['quotes_count'] }}</div>
                            </div>
                        </td>
                        <td width="50%" style="padding-left: 10px;">
                            <div class="stat-card">
                                <div class="stat-label">Total Quotation Value</div>
                                <div class="stat-value">{{ $data['currency'] }}{{ number_format($data['quotes_total'], 2) }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding-right: 10px; padding-top: 20px;">
                            <div class="stat-card">
                                <div class="stat-label">Purchase Orders</div>
                                <div class="stat-value">{{ $data['pos_count'] }}</div>
                            </div>
                        </td>
                        <td width="50%" style="padding-left: 10px; padding-top: 20px;">
                            <div class="stat-card">
                                <div class="stat-label">Active Customers</div>
                                <div class="stat-value">{{ $data['customers_count'] }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            @if(count($data['low_stock_items']) > 0)
                <div class="section-title" style="color: #e11d48;">Inventory Alerts</div>
                <div class="low-stock-list">
                    @foreach($data['low_stock_items'] as $item)
                        <div class="low-stock-item">
                            <span class="item-name">{{ $item['name'] }}</span>
                            <span class="item-qty">{{ $item['stock'] }} left</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <center>
                <a href="{{ config('app.url') }}/dashboard" class="btn">View Full Dashboard</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            Sent to you because you are a business owner.
        </div>
    </div>
</body>
</html>
