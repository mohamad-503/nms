<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $customer->full_name }} - عقد اشتراك</title>
    <style>
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        body { padding: 40px; max-width: 700px; margin: auto; color: #333; }
        h1 { color: #f97316; text-align: center; border-bottom: 2px solid #f97316; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px; border: 1px solid #ddd; }
        .lbl { background: #f9fafb; font-weight: bold; width: 40%; }
        .sig { margin-top: 60px; display: flex; justify-content: space-between; }
        .header { text-align: center; margin-bottom: 20px; }
        .header p { color: #666; margin: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NM System</h1>
        <p>عقد اشتراك خدمة الإنترنت</p>
    </div>
    <table>
        <tr><td class="lbl">الاسم الكامل</td><td>{{ $customer->full_name }}</td></tr>
        <tr><td class="lbl">رقم الهاتف</td><td>{{ $customer->phone }}</td></tr>
        <tr><td class="lbl">الرقم الوطني</td><td>{{ $customer->national_id }}</td></tr>
        <tr><td class="lbl">العنوان</td><td>{{ $customer->address }} - {{ $customer->city?->name }}</td></tr>
        <tr><td class="lbl">اسم مستخدم PPPoE</td><td>{{ $customer->pppoe_username }}</td></tr>
        <tr><td class="lbl">كلمة المرور</td><td>{{ $customer->pppoe_password }}</td></tr>
        <tr><td class="lbl">الباقة</td><td>{{ $customer->plan?->name }} ({{ number_format($customer->monthly_price) }} د.ع)</td></tr>
        <tr><td class="lbl">سرعة التحميل/الرفع</td><td>{{ $customer->download_speed }} / {{ $customer->upload_speed }} Mbps</td></tr>
        <tr><td class="lbl">تاريخ التركيب</td><td>{{ $customer->installation_date }}</td></tr>
        <tr><td class="lbl">بداية الاشتراك</td><td>{{ $customer->subscription_start }}</td></tr>
        <tr><td class="lbl">نهاية الاشتراك</td><td>{{ $customer->subscription_end }}</td></tr>
    </table>
    <div class="sig">
        <div>توقيع المشترك: ___________</div>
        <div>توقيع الشركة: ___________</div>
    </div>
</body>
</html>
