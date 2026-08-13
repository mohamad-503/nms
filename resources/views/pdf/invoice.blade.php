<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة - {{ $customer->full_name }}</title>
    <style>
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        body { padding: 40px; max-width: 700px; margin: auto; color: #333; }
        h1 { color: #f97316; text-align: center; }
        .inv { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px; border: 1px solid #ddd; }
        .ttl { background: #fff7ed; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header p { color: #666; margin: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NM System</h1>
        <p>فاتورة خدمة إنترنت</p>
    </div>
    <p>رقم الفاتورة: {{ $invoice?->invoice_number ?? 'INV-'.$customer->id }}</p>
    <p>العميل: {{ $customer->full_name }}</p>
    <p>الهاتف: {{ $customer->phone }}</p>
    <table class="inv">
        <tr><td>الباقة</td><td>{{ $customer->plan?->name }}</td></tr>
        <tr><td>السعر الشهري</td><td>{{ number_format($customer->monthly_price ?? $customer->plan?->price ?? 0) }} د.ع</td></tr>
        <tr class="ttl"><td>الإجمالي</td><td>{{ number_format($invoice?->total ?? $customer->monthly_price ?? $customer->plan?->price ?? 0) }} د.ع</td></tr>
    </table>
</body>
</html>
