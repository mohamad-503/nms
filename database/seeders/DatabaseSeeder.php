<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\City;
use App\Models\Area;
use App\Models\Tower;
use App\Models\Plan;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\SupportTicket;
use App\Models\ActivityLog;
use App\Models\Router;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@nm.iq',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin',
            'is_active' => true,
            'phone' => '07701110000',
        ]);

        $settings = [
            ['company_name','NM System','general'],['currency','IQD','general'],['tax_rate','0','general'],
            ['language','ar','general'],['smtp_host','','email'],['smtp_port','587','email'],
            ['telegram_bot_token','','notifications'],['whatsapp_api_key','','notifications'],
        ];
        foreach ($settings as $s) Setting::create(['key'=>$s[0],'value'=>$s[1],'group_name'=>$s[2]]);

        $cities = ['بغداد','البصرة','الموصل','أربيل','النجف','كربلاء'];
        foreach ($cities as $c) City::create(['name'=>$c]);
        $areas = ['المنطقة الشمالية','المنطقة الجنوبية','المنطقة الشرقية'];
        foreach ($areas as $a) Area::create(['name'=>$a]);
        Tower::create(['area_id'=>1,'name'=>'برج النور','ip'=>'10.0.0.1']);
        Tower::create(['area_id'=>1,'name'=>'برج الفجر','ip'=>'10.0.0.2']);
        Tower::create(['area_id'=>2,'name'=>'برج السلام','ip'=>'10.0.1.1']);

        $plans = [
            ['الباقة الأساسية',25000,10,5,30],['الباقة الفضية',50000,20,10,30],
            ['الباقة الذهبية',80000,40,20,30],['الباقة البلاتينية',120000,80,40,30],
        ];
        foreach ($plans as $p) Plan::create(['name'=>$p[0],'price'=>$p[1],'download_speed'=>$p[2],'upload_speed'=>$p[3],'validity'=>$p[4],'burst'=>'نعم','is_active'=>true]);

        $depts = ['الإدارة','المالية','الدعم الفني','التقنية'];
        foreach ($depts as $d) Department::create(['name'=>$d]);
        Employee::create(['full_name'=>'محمد المدير','phone'=>'07701112233','department_id'=>1,'position'=>'مدير عام','salary'=>1500000,'hire_date'=>'2024-01-01']);
        Employee::create(['full_name'=>'فاطمة المحاسبة','phone'=>'07802223344','department_id'=>2,'position'=>'محاسب','salary'=>800000,'hire_date'=>'2024-03-15']);

        $customers = [
            ['أحمد علي حسين','07701234567','الحي العسكري',1,1,1,'ahmed01','pass123',1,10,5,'2026-01-15','2026-08-15','active',25000],
            ['سارة محمد جاسم','07802345678','شارع 20',2,3,2,'sara02','pass456',2,20,10,'2026-02-01','2026-07-30','active',50000],
            ['عمر خالد إبراهيم','07903456789','الحي التجاري',1,2,1,'omar03','pass789',3,40,20,'2026-01-20','2026-06-20','expired',80000],
            ['نور الهدى سعيد','07714567890','قرب البرج',1,1,1,'noor04','passabc',1,10,5,'2026-03-10','2026-09-10','active',25000],
            ['حسن عبد الله','07825678901','المنطقة الصناعية',2,3,2,'hassan05','passdef',4,80,40,'2025-12-15','2026-06-15','suspended',120000],
        ];
        foreach ($customers as $c) Customer::create([
            'full_name'=>$c[0],'phone'=>$c[1],'address'=>$c[2],'city_id'=>$c[3],'area_id'=>$c[4],'tower_id'=>$c[5],
            'pppoe_username'=>$c[6],'pppoe_password'=>$c[7],'plan_id'=>$c[8],'download_speed'=>$c[9],'upload_speed'=>$c[10],
            'subscription_start'=>$c[11],'subscription_end'=>$c[12],'status'=>$c[13],'monthly_price'=>$c[14],
            'installation_date'=>$c[11],
        ]);

        Invoice::create(['invoice_number'=>'INV-100001','customer_id'=>1,'plan_id'=>1,'amount'=>25000,'total'=>25000,'status'=>'paid','issued_date'=>'2026-01-15']);
        Invoice::create(['invoice_number'=>'INV-100002','customer_id'=>2,'plan_id'=>2,'amount'=>50000,'total'=>50000,'status'=>'paid','issued_date'=>'2026-02-01']);
        Invoice::create(['invoice_number'=>'INV-100003','customer_id'=>3,'plan_id'=>3,'amount'=>80000,'total'=>80000,'status'=>'unpaid','issued_date'=>'2026-01-20']);
        Payment::create(['invoice_id'=>1,'customer_id'=>1,'amount'=>25000,'method'=>'cash','paid_date'=>'2026-01-15']);
        Payment::create(['invoice_id'=>2,'customer_id'=>2,'amount'=>50000,'method'=>'cash','paid_date'=>'2026-02-01']);
        Expense::create(['category'=>'إيجار','amount'=>15000,'expense_date'=>'2026-07-01','description'=>'إيجار المكتب']);
        Expense::create(['category'=>'صيانة','amount'=>30000,'expense_date'=>'2026-07-15','description'=>'صيانة أبراج']);
        SupportTicket::create(['customer_id'=>1,'subject'=>'انقطاع الاتصال','description'=>'الاتصال متقطع','priority'=>'high','status'=>'open']);
        SupportTicket::create(['customer_id'=>3,'subject'=>'بطء في التصفح','priority'=>'medium','status'=>'assigned']);
        Router::create(['name'=>'الراوتر الرئيسي','ip'=>'192.168.88.1','port'=>8728,'username'=>'admin','password'=>'admin123','use_ssl'=>false,'status'=>'online']);
        ActivityLog::create(['user_id'=>1,'action'=>'login','module'=>'auth','description'=>'تسجيل دخول','ip_address'=>'127.0.0.1']);
    }
}
