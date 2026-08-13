<?php

/**
 * NM System - Minimal PHP Server
 * Serves the Vue frontend and provides a JSON-file-based API
 * for local development when Laravel/MySQL are not available.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$ROOT = __DIR__;
$PUBLIC = $ROOT . '/public';
$DATA = $ROOT . '/storage/data';

if (!is_dir($DATA)) {
    mkdir($DATA, 0777, true);
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// API routes
if (preg_match('#^/api/#', $uri)) {
    header('Content-Type: application/json; charset=utf-8');

    $body = json_decode(file_get_contents('php://input'), true) ?: [];

    try {
        $response = handleApiRequest($uri, $method, $body);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        http_response_code($e->getCode() ?: 500);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Serve static files
$filePath = $PUBLIC . $uri;
if ($uri === '/' || $uri === '') {
    $filePath = $PUBLIC . '/index.html';
}

if (file_exists($filePath) && !is_dir($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $types = [
        'html' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript',
        'json' => 'application/json', 'svg' => 'image/svg+xml', 'png' => 'image/png',
        'jpg' => 'image/jpeg', 'ico' => 'image/x-icon', 'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    readfile($filePath);
    exit;
}

// SPA fallback - serve index.html for any non-file route
$indexFile = $PUBLIC . '/index.html';
if (file_exists($indexFile)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($indexFile);
    exit;
}

http_response_code(404);
echo 'Not Found';

// ============ API HANDLER ============

function handleApiRequest($uri, $method, $body) {
    $path = str_replace('/api/', '', $uri);

    // Auth endpoints
    if ($path === 'login' && $method === 'POST') {
        return login($body);
    }
    if ($path === 'me' && $method === 'GET') {
        return me();
    }
    if ($path === 'logout' && $method === 'POST') {
        session_start();
        session_destroy();
        return ['ok' => true];
    }

    // Dashboard stats
    if ($path === 'dashboard/stats' && $method === 'GET') {
        return dashboardStats();
    }

    // Customers CRUD
    if (preg_match('#^customers/?$#', $path)) {
        if ($method === 'GET') return listRecords('customers');
        if ($method === 'POST') return createRecord('customers', $body);
    }
    if (preg_match('#^customers/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('customers', $id);
        if ($method === 'PUT') return updateRecord('customers', $id, $body);
        if ($method === 'DELETE') return deleteRecord('customers', $id);
    }
    if (preg_match('#^customers/(\d+)/(suspend|activate|renew)$#', $path, $m)) {
        $id = (int)$m[1];
        $action = $m[2];
        return customerAction($id, $action, $body);
    }

    // Plans CRUD
    if (preg_match('#^plans/?$#', $path)) {
        if ($method === 'GET') return listRecords('plans');
        if ($method === 'POST') return createRecord('plans', $body);
    }
    if (preg_match('#^plans/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('plans', $id);
        if ($method === 'PUT') return updateRecord('plans', $id, $body);
        if ($method === 'DELETE') return deleteRecord('plans', $id);
    }

    // Invoices
    if (preg_match('#^invoices/?$#', $path)) {
        if ($method === 'GET') return listRecords('invoices');
        if ($method === 'POST') return createRecord('invoices', $body);
    }
    if (preg_match('#^invoices/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('invoices', $id);
        if ($method === 'PUT') return updateRecord('invoices', $id, $body);
        if ($method === 'DELETE') return deleteRecord('invoices', $id);
    }
    if (preg_match('#^invoices/(\d+)/pay$#', $path, $m)) {
        return invoicePay((int)$m[1]);
    }

    // Expenses
    if (preg_match('#^expenses/?$#', $path)) {
        if ($method === 'GET') return listRecords('expenses');
        if ($method === 'POST') return createRecord('expenses', $body);
    }
    if (preg_match('#^expenses/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('expenses', $id);
        if ($method === 'PUT') return updateRecord('expenses', $id, $body);
        if ($method === 'DELETE') return deleteRecord('expenses', $id);
    }

    // Employees
    if (preg_match('#^employees/?$#', $path)) {
        if ($method === 'GET') return listRecords('employees');
        if ($method === 'POST') return createRecord('employees', $body);
    }
    if (preg_match('#^employees/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('employees', $id);
        if ($method === 'PUT') return updateRecord('employees', $id, $body);
        if ($method === 'DELETE') return deleteRecord('employees', $id);
    }

    // Tickets
    if (preg_match('#^tickets/?$#', $path)) {
        if ($method === 'GET') return listRecords('tickets');
        if ($method === 'POST') return createRecord('tickets', $body);
    }
    if (preg_match('#^tickets/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('tickets', $id);
        if ($method === 'PUT') return updateRecord('tickets', $id, $body);
        if ($method === 'DELETE') return deleteRecord('tickets', $id);
    }
    if (preg_match('#^tickets/(\d+)/reply$#', $path, $m)) {
        return ticketReply((int)$m[1], $body);
    }

    // Routers
    if (preg_match('#^routers/?$#', $path)) {
        if ($method === 'GET') return listRecords('routers');
        if ($method === 'POST') return createRecord('routers', $body);
    }
    if (preg_match('#^routers/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET') return getRecord('routers', $id);
        if ($method === 'PUT') return updateRecord('routers', $id, $body);
        if ($method === 'DELETE') return deleteRecord('routers', $id);
    }
    if (preg_match('#^routers/(\d+)/test$#', $path, $m)) {
        return routerTest((int)$m[1]);
    }

    // Reports
    if ($path === 'reports' && $method === 'GET') return reports();

    // Logs
    if ($path === 'logs' && $method === 'GET') return listRecords('activity_logs');

    // Settings
    if ($path === 'settings' && $method === 'GET') return getSettings();
    if ($path === 'settings' && $method === 'PUT') return updateSettings($body);

    // Reference data
    if ($path === 'cities' && $method === 'GET') return listRecords('cities');
    if ($path === 'areas' && $method === 'GET') return listRecords('areas');
    if ($path === 'towers' && $method === 'GET') return listRecords('towers');
    if ($path === 'departments' && $method === 'GET') return listRecords('departments');
    if ($path === 'inventory/categories' && $method === 'GET') return listRecords('inventory_categories');
    if ($path === 'inventory/suppliers' && $method === 'GET') return listRecords('inventory_suppliers');
    if ($path === 'products' && $method === 'GET') return listRecords('inventory_products');

    throw new Exception("Route not found: {$method} {$path}", 404);
}

// ============ STORAGE ============

function loadData($table) {
    global $DATA;
    $file = $DATA . '/' . $table . '.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function saveData($table, $data) {
    global $DATA;
    $file = $DATA . '/' . $table . '.json';
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function nextId($table) {
    $records = loadData($table);
    $maxId = 0;
    foreach ($records as $r) {
        if (isset($r['id']) && $r['id'] > $maxId) $maxId = $r['id'];
    }
    return $maxId + 1;
}

function listRecords($table) {
    return loadData($table);
}

function getRecord($table, $id) {
    $records = loadData($table);
    foreach ($records as $r) {
        if ($r['id'] == $id) return $r;
    }
    throw new Exception("Record not found", 404);
}

function createRecord($table, $body) {
    $records = loadData($table);
    $id = nextId($table);
    $body['id'] = $id;
    $body['created_at'] = date('c');
    $body['updated_at'] = date('c');
    $records[] = $body;
    saveData($table, $records);
    logActivity('create', $table, "Created {$table} #{$id}");
    return $body;
}

function updateRecord($table, $id, $body) {
    $records = loadData($table);
    foreach ($records as &$r) {
        if ($r['id'] == $id) {
            $body['id'] = $id;
            $body['updated_at'] = date('c');
            $r = array_merge($r, $body);
            saveData($table, $records);
            logActivity('update', $table, "Updated {$table} #{$id}");
            return $r;
        }
    }
    throw new Exception("Record not found", 404);
}

function deleteRecord($table, $id) {
    $records = loadData($table);
    $filtered = array_values(array_filter($records, fn($r) => $r['id'] != $id));
    saveData($table, $filtered);
    logActivity('delete', $table, "Deleted {$table} #{$id}");
    return ['ok' => true];
}

// ============ AUTH ============

function login($body) {
    $email = $body['email'] ?? '';
    $password = $body['password'] ?? '';

    $users = loadData('users');
    foreach ($users as $u) {
        if ($u['email'] === $email && password_verify($password, $u['password'] ?? '')) {
            session_start();
            $_SESSION['user_id'] = $u['id'];
            unset($u['password']);
            return ['user' => $u, 'token' => session_id()];
        }
    }
    // Fallback for demo
    if ($email === 'admin@nm.iq' && $password === 'admin123') {
        session_start();
        $user = ['id' => 1, 'name' => 'مدير النظام', 'email' => 'admin@nm.iq', 'role' => 'super_admin'];
        $_SESSION['user_id'] = 1;
        return ['user' => $user, 'token' => session_id()];
    }
    throw new Exception('بيانات الدخول غير صحيحة', 401);
}

function me() {
    session_start();
    $users = loadData('users');
    $uid = $_SESSION['user_id'] ?? 1;
    foreach ($users as $u) {
        if ($u['id'] == $uid) {
            unset($u['password']);
            return $u;
        }
    }
    return ['id' => 1, 'name' => 'مدير النظام', 'email' => 'admin@nm.iq', 'role' => 'super_admin'];
}

// ============ DASHBOARD ============

function dashboardStats() {
    $customers = loadData('customers');
    $payments = loadData('payments');
    $expenses = loadData('expenses');
    $products = loadData('inventory_products');
    $tickets = loadData('tickets');

    $today = date('Y-m-d');
    $monthStart = date('Y-m-01');

    $todayIncome = 0;
    foreach ($payments as $p) {
        if (($p['paid_date'] ?? '') >= $today) $todayIncome += (float)($p['amount'] ?? 0);
    }
    $monthIncome = 0;
    foreach ($payments as $p) {
        if (($p['paid_date'] ?? '') >= $monthStart) $monthIncome += (float)($p['amount'] ?? 0);
    }
    $monthExpenses = 0;
    foreach ($expenses as $e) {
        if (($e['expense_date'] ?? '') >= $monthStart) $monthExpenses += (float)($e['amount'] ?? 0);
    }

    $active = count(array_filter($customers, fn($c) => ($c['status'] ?? '') === 'active'));
    $expired = count(array_filter($customers, fn($c) => ($c['status'] ?? '') === 'expired'));
    $suspended = count(array_filter($customers, fn($c) => ($c['status'] ?? '') === 'suspended'));
    $lowStock = count(array_filter($products, fn($p) => ($p['quantity'] ?? 0) <= ($p['min_quantity'] ?? 0)));
    $openTickets = count(array_filter($tickets, fn($t) => in_array($t['status'] ?? '', ['open', 'assigned', 'in_progress'])));

    return [
        'total' => count($customers),
        'active' => $active,
        'expired' => $expired,
        'suspended' => $suspended,
        'online' => 0,
        'todayIncome' => $todayIncome,
        'monthIncome' => $monthIncome,
        'expenses' => $monthExpenses,
        'profit' => $monthIncome - $monthExpenses,
        'lowStock' => $lowStock,
        'openTickets' => $openTickets,
    ];
}

// ============ CUSTOMER ACTIONS ============

function customerAction($id, $action, $body) {
    $customers = loadData('customers');
    foreach ($customers as &$c) {
        if ($c['id'] == $id) {
            if ($action === 'suspend') $c['status'] = 'suspended';
            elseif ($action === 'activate') $c['status'] = 'active';
            elseif ($action === 'renew') {
                $days = (int)($body['days'] ?? 30);
                $end = $c['subscription_end'] ?? date('Y-m-d');
                $c['subscription_end'] = date('Y-m-d', strtotime($end . " +{$days} days"));
                $c['status'] = 'active';
            }
            $c['updated_at'] = date('c');
            saveData('customers', $customers);
            logActivity($action, 'customers', "{$action} customer #{$id}");
            return $c;
        }
    }
    throw new Exception("Customer not found", 404);
}

// ============ INVOICE PAY ============

function invoicePay($id) {
    $invoices = loadData('invoices');
    foreach ($invoices as &$inv) {
        if ($inv['id'] == $id) {
            $inv['status'] = 'paid';
            $inv['updated_at'] = date('c');
            saveData('invoices', $invoices);

            $payments = loadData('payments');
            $payments[] = [
                'id' => nextId('payments'),
                'invoice_id' => $id,
                'customer_id' => $inv['customer_id'] ?? null,
                'amount' => $inv['total'] ?? 0,
                'method' => 'cash',
                'paid_date' => date('Y-m-d'),
                'created_at' => date('c'),
            ];
            saveData('payments', $payments);

            $cash = loadData('cash_box_transactions');
            $cash[] = [
                'id' => nextId('cash_box_transactions'),
                'type' => 'in',
                'amount' => $inv['total'] ?? 0,
                'source' => 'invoice',
                'reference' => $inv['invoice_number'] ?? '',
                'transaction_date' => date('Y-m-d'),
                'created_at' => date('c'),
            ];
            saveData('cash_box_transactions', $cash);

            return $inv;
        }
    }
    throw new Exception("Invoice not found", 404);
}

// ============ TICKET REPLY ============

function ticketReply($id, $body) {
    $replies = loadData('ticket_replies');
    $reply = [
        'id' => nextId('ticket_replies'),
        'ticket_id' => $id,
        'author_id' => 1,
        'message' => $body['message'] ?? '',
        'created_at' => date('c'),
    ];
    $replies[] = $reply;
    saveData('ticket_replies', $replies);

    $tickets = loadData('tickets');
    foreach ($tickets as &$t) {
        if ($t['id'] == $id && in_array($t['status'] ?? '', ['open', 'assigned'])) {
            $t['status'] = 'in_progress';
        }
    }
    saveData('tickets', $tickets);
    return $reply;
}

// ============ ROUTER TEST ============

function routerTest($id) {
    $routers = loadData('routers');
    foreach ($routers as &$r) {
        if ($r['id'] == $id) {
            $ok = !empty($r['ip']) && !empty($r['username']);
            $r['status'] = $ok ? 'online' : 'error';
            $r['last_checked'] = date('c');
            saveData('routers', $routers);
            return ['ok' => $ok, 'status' => $r['status']];
        }
    }
    throw new Exception("Router not found", 404);
}

// ============ REPORTS ============

function reports() {
    $customers = loadData('customers');
    $payments = loadData('payments');
    $expenses = loadData('expenses');
    $invoices = loadData('invoices');

    $revenue = array_sum(array_map(fn($p) => (float)($p['amount'] ?? 0), $payments));
    $exp = array_sum(array_map(fn($e) => (float)($e['amount'] ?? 0), $expenses));

    return [
        'stats' => [
            'customers' => count($customers),
            'revenue' => $revenue,
            'expenses' => $exp,
            'profit' => $revenue - $exp,
            'invoices' => count($invoices),
            'paid' => count(array_filter($invoices, fn($i) => ($i['status'] ?? '') === 'paid')),
        ],
    ];
}

// ============ SETTINGS ============

function getSettings() {
    $settings = loadData('settings');
    $result = [];
    foreach ($settings as $s) {
        $result[$s['key']] = $s['value'];
    }
    return $result;
}

function updateSettings($body) {
    $settings = loadData('settings');
    $newSettings = $body['settings'] ?? [];
    foreach ($newSettings as $key => $value) {
        $found = false;
        foreach ($settings as &$s) {
            if ($s['key'] === $key) {
                $s['value'] = $value;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $settings[] = ['id' => nextId('settings'), 'key' => $key, 'value' => $value, 'group_name' => 'general'];
        }
    }
    saveData('settings', $settings);
    return ['ok' => true];
}

// ============ LOGGING ============

function logActivity($action, $module, $description) {
    $logs = loadData('activity_logs');
    $logs[] = [
        'id' => nextId('activity_logs'),
        'user_id' => 1,
        'action' => $action,
        'module' => $module,
        'description' => $description,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'created_at' => date('c'),
    ];
    // Keep last 200 logs
    if (count($logs) > 200) $logs = array_slice($logs, -200);
    saveData('activity_logs', $logs);
}
