<?php

/**
 * Seed demo data for the file-based API.
 */

$DATA = __DIR__ . '/../storage/data';
if (!is_dir($DATA)) mkdir($DATA, 0777, true);

function save($table, $records) {
    global $DATA;
    file_put_contents($DATA . '/' . $table . '.json', json_encode($records, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// Users
save('users', [
    ['id' => 1, 'name' => 'مدير النظام', 'email' => 'admin@nm.iq', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'super_admin', 'is_active' => true, 'phone' => '07701110000'],
]);

// Cities
save('cities', [
    ['id' => 1, 'name' => 'بغداد', 'created_at' => date('c')],
    ['id' => 2, 'name' => 'البصرة', 'created_at' => date('c')],
    ['id' => 3, 'name' => 'الموصل', 'created_at' => date('c')],
    ['id' => 4, 'name' => 'أربيل', 'created_at' => date('c')],
    ['id' => 5, 'name' => 'النجف', 'created_at' => date('c')],
    ['id' => 6, 'name' => 'كربلاء', 'created_at' => date('c')],
]);

// Areas
save('areas', [
    ['id' => 1, 'name' => 'المنطقة الشمالية', 'created_at' => date('c')],
    ['id' => 2, 'name' => 'المنطقة الجنوبية', 'created_at' => date('c')],
    ['id' => 3, 'name' => 'المنطقة الشرقية', 'created_at' => date('c')],
]);

// Towers
save('towers', [
    ['id' => 1, 'area_id' => 1, 'name' => 'برج النور', 'ip' => '10.0.0.1', 'created_at' => date('c')],
    ['id' => 2, 'area_id' => 1, 'name' => 'برج الفجر', 'ip' => '10.0.0.2', 'created_at' => date('c')],
    ['id' => 3, 'area_id' => 2, 'name' => 'برج السلام', 'ip' => '10.0.1.1', 'created_at' => date('c')],
]);

// Plans
save('plans', [
    ['id' => 1, 'name' => 'الباقة الأساسية', 'price' => 25000, 'download_speed' => 10, 'upload_speed' => 5, 'burst' => 'نعم', 'validity' => 30, 'is_active' => true, 'created_at' => date('c')],
    ['id' => 2, 'name' => 'الباقة الفضية', 'price' => 50000, 'download_speed' => 20, 'upload_speed' => 10, 'burst' => 'نعم', 'validity' => 30, 'is_active' => true, 'created_at' => date('c')],
    ['id' => 3, 'name' => 'الباقة الذهبية', 'price' => 80000, 'download_speed' => 40, 'upload_speed' => 20, 'burst' => 'نعم', 'validity' => 30, 'is_active' => true, 'created_at' => date('c')],
    ['id' => 4, 'name' => 'الباقة البلاتينية', 'price' => 120000, 'download_speed' => 80, 'upload_speed' => 40, 'burst' => 'نعم', 'validity' => 30, 'is_active' => true, 'created_at' => date('c')],
]);

// Departments
save('departments', [
    ['id' => 1, 'name' => 'الإدارة', 'created_at' => date('c')],
    ['id' => 2, 'name' => 'المالية', 'created_at' => date('c')],
    ['id' => 3, 'name' => 'الدعم الفني', 'created_at' => date('c')],
    ['id' => 4, 'name' => 'التقنية', 'created_at' => date('c')],
]);

// Employees
save('employees', [
    ['id' => 1, 'full_name' => 'محمد المدير', 'phone' => '07701112233', 'department_id' => 1, 'position' => 'مدير عام', 'salary' => 1500000, 'hire_date' => '2024-01-01', 'status' => 'active', 'created_at' => date('c')],
    ['id' => 2, 'full_name' => 'فاطمة المحاسبة', 'phone' => '07802223344', 'department_id' => 2, 'position' => 'محاسب', 'salary' => 800000, 'hire_date' => '2024-03-15', 'status' => 'active', 'created_at' => date('c')],
    ['id' => 3, 'full_name' => 'علي الفني', 'phone' => '07903334455', 'department_id' => 3, 'position' => 'فني صيانة', 'salary' => 500000, 'hire_date' => '2025-06-01', 'status' => 'active', 'created_at' => date('c')],
]);

// Customers
$now = date('c');
save('customers', [
    ['id' => 1, 'full_name' => 'أحمد علي حسين', 'phone' => '07701234567', 'national_id' => '12345678901', 'address' => 'الحي العسكري', 'city_id' => 1, 'area_id' => 1, 'tower_id' => 1, 'pppoe_username' => 'ahmed01', 'pppoe_password' => 'pass123', 'plan_id' => 1, 'download_speed' => 10, 'upload_speed' => 5, 'static_ip' => '', 'mac_address' => 'AA:BB:CC:DD:EE:01', 'installation_date' => '2026-01-15', 'subscription_start' => '2026-01-15', 'subscription_end' => '2026-08-15', 'monthly_price' => 25000, 'status' => 'active', 'notes' => 'عميل مميز', 'balance' => 0, 'profile_photo' => '', 'created_at' => $now],
    ['id' => 2, 'full_name' => 'سارة محمد جاسم', 'phone' => '07802345678', 'national_id' => '12345678902', 'address' => 'شارع 20', 'city_id' => 2, 'area_id' => 3, 'tower_id' => 3, 'pppoe_username' => 'sara02', 'pppoe_password' => 'pass456', 'plan_id' => 2, 'download_speed' => 20, 'upload_speed' => 10, 'static_ip' => '', 'mac_address' => 'AA:BB:CC:DD:EE:02', 'installation_date' => '2026-02-01', 'subscription_start' => '2026-02-01', 'subscription_end' => '2026-07-30', 'monthly_price' => 50000, 'status' => 'active', 'notes' => '', 'balance' => 0, 'profile_photo' => '', 'created_at' => $now],
    ['id' => 3, 'full_name' => 'عمر خالد إبراهيم', 'phone' => '07903456789', 'national_id' => '12345678903', 'address' => 'الحي التجاري', 'city_id' => 1, 'area_id' => 2, 'tower_id' => 2, 'pppoe_username' => 'omar03', 'pppoe_password' => 'pass789', 'plan_id' => 3, 'download_speed' => 40, 'upload_speed' => 20, 'static_ip' => '', 'mac_address' => 'AA:BB:CC:DD:EE:03', 'installation_date' => '2026-01-20', 'subscription_start' => '2026-01-20', 'subscription_end' => '2026-06-20', 'monthly_price' => 80000, 'status' => 'expired', 'notes' => '', 'balance' => 0, 'profile_photo' => '', 'created_at' => $now],
    ['id' => 4, 'full_name' => 'نور الهدى سعيد', 'phone' => '07714567890', 'national_id' => '12345678904', 'address' => 'قرب البرج', 'city_id' => 1, 'area_id' => 1, 'tower_id' => 1, 'pppoe_username' => 'noor04', 'pppoe_password' => 'passabc', 'plan_id' => 1, 'download_speed' => 10, 'upload_speed' => 5, 'static_ip' => '', 'mac_address' => 'AA:BB:CC:DD:EE:04', 'installation_date' => '2026-03-10', 'subscription_start' => '2026-03-10', 'subscription_end' => '2026-09-10', 'monthly_price' => 25000, 'status' => 'active', 'notes' => '', 'balance' => 0, 'profile_photo' => '', 'created_at' => $now],
    ['id' => 5, 'full_name' => 'حسن عبد الله', 'phone' => '07825678901', 'national_id' => '12345678905', 'address' => 'المنطقة الصناعية', 'city_id' => 2, 'area_id' => 3, 'tower_id' => 3, 'pppoe_username' => 'hassan05', 'pppoe_password' => 'passdef', 'plan_id' => 4, 'download_speed' => 80, 'upload_speed' => 40, 'static_ip' => '', 'mac_address' => 'AA:BB:CC:DD:EE:05', 'installation_date' => '2025-12-15', 'subscription_start' => '2025-12-15', 'subscription_end' => '2026-06-15', 'monthly_price' => 120000, 'status' => 'suspended', 'notes' => 'مراجعة الحساب', 'balance' => 0, 'profile_photo' => '', 'created_at' => $now],
]);

// Invoices
save('invoices', [
    ['id' => 1, 'invoice_number' => 'INV-100001', 'customer_id' => 1, 'plan_id' => 1, 'amount' => 25000, 'tax' => 0, 'total' => 25000, 'status' => 'paid', 'issued_date' => '2026-01-15', 'due_date' => '2026-02-15', 'notes' => '', 'created_at' => $now],
    ['id' => 2, 'invoice_number' => 'INV-100002', 'customer_id' => 2, 'plan_id' => 2, 'amount' => 50000, 'tax' => 0, 'total' => 50000, 'status' => 'paid', 'issued_date' => '2026-02-01', 'due_date' => '2026-03-01', 'notes' => '', 'created_at' => $now],
    ['id' => 3, 'invoice_number' => 'INV-100003', 'customer_id' => 3, 'plan_id' => 3, 'amount' => 80000, 'tax' => 0, 'total' => 80000, 'status' => 'unpaid', 'issued_date' => '2026-01-20', 'due_date' => '2026-02-20', 'notes' => '', 'created_at' => $now],
    ['id' => 4, 'invoice_number' => 'INV-100004', 'customer_id' => 4, 'plan_id' => 1, 'amount' => 25000, 'tax' => 0, 'total' => 25000, 'status' => 'paid', 'issued_date' => '2026-03-10', 'due_date' => '2026-04-10', 'notes' => '', 'created_at' => $now],
    ['id' => 5, 'invoice_number' => 'INV-100005', 'customer_id' => 5, 'plan_id' => 4, 'amount' => 120000, 'tax' => 0, 'total' => 120000, 'status' => 'partial', 'issued_date' => '2025-12-15', 'due_date' => '2026-01-15', 'notes' => '', 'created_at' => $now],
]);

// Payments
save('payments', [
    ['id' => 1, 'invoice_id' => 1, 'customer_id' => 1, 'amount' => 25000, 'method' => 'cash', 'paid_date' => '2026-01-15', 'notes' => '', 'created_at' => $now],
    ['id' => 2, 'invoice_id' => 2, 'customer_id' => 2, 'amount' => 50000, 'method' => 'cash', 'paid_date' => '2026-02-01', 'notes' => '', 'created_at' => $now],
    ['id' => 3, 'invoice_id' => 4, 'customer_id' => 4, 'amount' => 25000, 'method' => 'transfer', 'paid_date' => '2026-03-10', 'notes' => '', 'created_at' => $now],
]);

// Expenses
save('expenses', [
    ['id' => 1, 'category' => 'إيجار', 'amount' => 15000, 'expense_date' => '2026-07-01', 'description' => 'إيجار المكتب', 'created_at' => $now],
    ['id' => 2, 'category' => 'صيانة', 'amount' => 30000, 'expense_date' => '2026-07-15', 'description' => 'صيانة أبراج', 'created_at' => $now],
    ['id' => 3, 'category' => 'رواتب', 'amount' => 200000, 'expense_date' => '2026-07-25', 'description' => 'رواتب الموظفين', 'created_at' => $now],
]);

// Cash box
save('cash_box_transactions', [
    ['id' => 1, 'type' => 'in', 'amount' => 25000, 'source' => 'invoice', 'reference' => 'INV-100001', 'transaction_date' => '2026-01-15', 'notes' => '', 'created_at' => $now],
    ['id' => 2, 'type' => 'in', 'amount' => 50000, 'source' => 'invoice', 'reference' => 'INV-100002', 'transaction_date' => '2026-02-01', 'notes' => '', 'created_at' => $now],
    ['id' => 3, 'type' => 'out', 'amount' => 15000, 'source' => 'expense', 'reference' => '', 'transaction_date' => '2026-07-01', 'notes' => '', 'created_at' => $now],
    ['id' => 4, 'type' => 'out', 'amount' => 30000, 'source' => 'expense', 'reference' => '', 'transaction_date' => '2026-07-15', 'notes' => '', 'created_at' => $now],
]);

// Tickets
save('tickets', [
    ['id' => 1, 'customer_id' => 1, 'subject' => 'انقطاع الاتصال', 'description' => 'الاتصال متقطع منذ الصباح', 'priority' => 'high', 'status' => 'open', 'assigned_to' => null, 'created_at' => $now],
    ['id' => 2, 'customer_id' => 3, 'subject' => 'بطء في التصفح', 'description' => 'السرعة أقل من المعتاد', 'priority' => 'medium', 'status' => 'assigned', 'assigned_to' => 1, 'created_at' => $now],
    ['id' => 3, 'customer_id' => 5, 'subject' => 'طلب إعادة تفعيل', 'description' => 'يرغب بإعادة تفعيل الخدمة', 'priority' => 'urgent', 'status' => 'in_progress', 'assigned_to' => 1, 'created_at' => $now],
]);

// Routers
save('routers', [
    ['id' => 1, 'name' => 'الراوتر الرئيسي', 'ip' => '192.168.88.1', 'port' => 8728, 'username' => 'admin', 'password' => 'admin123', 'use_ssl' => false, 'status' => 'online', 'last_checked' => date('c'), 'notes' => '', 'created_at' => $now],
    ['id' => 2, 'name' => 'راوتر الفرع', 'ip' => '10.0.0.1', 'port' => 8728, 'username' => 'admin', 'password' => 'pass456', 'use_ssl' => false, 'status' => 'offline', 'last_checked' => null, 'notes' => '', 'created_at' => $now],
]);

// Inventory
save('inventory_categories', [
    ['id' => 1, 'name' => 'راوترات', 'created_at' => $now],
    ['id' => 2, 'name' => 'كابلات', 'created_at' => $now],
    ['id' => 3, 'name' => 'أجهزة استقبال', 'created_at' => $now],
    ['id' => 4, 'name' => 'ملحقات', 'created_at' => $now],
]);

save('inventory_suppliers', [
    ['id' => 1, 'name' => 'مورد التقنية', 'phone' => '0770000001', 'email' => 'supplier1@nm.iq', 'address' => '', 'created_at' => $now],
    ['id' => 2, 'name' => 'شركة الكابل', 'phone' => '0770000002', 'email' => 'supplier2@nm.iq', 'address' => '', 'created_at' => $now],
]);

save('inventory_products', [
    ['id' => 1, 'category_id' => 1, 'supplier_id' => 1, 'name' => 'راوتر TP-Link', 'sku' => 'TL-WR840N', 'cost_price' => 35000, 'sale_price' => 50000, 'quantity' => 12, 'min_quantity' => 5, 'unit' => 'piece', 'created_at' => $now],
    ['id' => 2, 'category_id' => 2, 'supplier_id' => 2, 'name' => 'كابل شبكة 1م', 'sku' => 'CAB-1M', 'cost_price' => 2000, 'sale_price' => 5000, 'quantity' => 3, 'min_quantity' => 10, 'unit' => 'piece', 'created_at' => $now],
    ['id' => 3, 'category_id' => 3, 'supplier_id' => 1, 'name' => 'مستقبل لاسلكي', 'sku' => 'RB-LHG-5AC', 'cost_price' => 60000, 'sale_price' => 95000, 'quantity' => 8, 'min_quantity' => 3, 'unit' => 'piece', 'created_at' => $now],
]);

// Settings
save('settings', [
    ['id' => 1, 'key' => 'company_name', 'value' => 'NM System', 'group_name' => 'general'],
    ['id' => 2, 'key' => 'currency', 'value' => 'IQD', 'group_name' => 'general'],
    ['id' => 3, 'key' => 'tax_rate', 'value' => '0', 'group_name' => 'general'],
    ['id' => 4, 'key' => 'language', 'value' => 'ar', 'group_name' => 'general'],
    ['id' => 5, 'key' => 'smtp_host', 'value' => '', 'group_name' => 'email'],
    ['id' => 6, 'key' => 'smtp_port', 'value' => '587', 'group_name' => 'email'],
    ['id' => 7, 'key' => 'telegram_bot_token', 'value' => '', 'group_name' => 'notifications'],
    ['id' => 8, 'key' => 'whatsapp_api_key', 'value' => '', 'group_name' => 'notifications'],
]);

// Activity logs
save('activity_logs', [
    ['id' => 1, 'user_id' => 1, 'action' => 'login', 'module' => 'auth', 'description' => 'تسجيل دخول', 'ip_address' => '127.0.0.1', 'created_at' => $now],
    ['id' => 2, 'user_id' => 1, 'action' => 'create', 'module' => 'customers', 'description' => 'إضافة مشترك جديد', 'ip_address' => '127.0.0.1', 'created_at' => $now],
    ['id' => 3, 'user_id' => 1, 'action' => 'payment', 'module' => 'billing', 'description' => 'تحصيل فاتورة INV-100001', 'ip_address' => '127.0.0.1', 'created_at' => $now],
]);

// Empty tables
save('ticket_replies', []);
save('debts', []);
save('attendance', []);
save('leaves', []);
save('stock_movements', []);
save('product_serials', []);

echo "Demo data seeded successfully.\n";
echo "Login: admin@nm.iq / admin123\n";
