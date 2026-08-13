/*
# NM System - ISP Management Schema

## Overview
Complete schema for an ISP Management System ("NM System") with authentication,
customers, plans, billing, inventory, employees, support tickets, MikroTik routers,
reports, logs, notifications and settings.

## Tables created
1. profiles - extends auth.users with role + employee linkage
2. areas, towers - geographic infrastructure
3. plans - internet service plans
4. customers - subscriber accounts with PPPoE credentials
5. invoices - billing invoices per customer
6. payments - received payments
7. expenses - operational expenses
8. cash_box_transactions - cash box ledger
9. debts - outstanding debt tracking
10. inventory_categories, inventory_suppliers, inventory_products
11. stock_movements - product stock in/out ledger
12. product_serials - serial number tracking
13. departments, employees, attendance, leaves
14. support_tickets, ticket_replies
15. routers - MikroTik router registry
16. activity_logs - audit trail (who/when/ip/action)
17. notifications - notification queue (telegram/email/whatsapp)
18. settings - company configuration (key/value)

## Security
- RLS enabled on every table.
- Policies scoped TO authenticated (signed-in app) with ownership checks where applicable.
- profiles: each user can read own profile; super_admin can read all (via role check in JWT raw_app_meta_data not assumed, so we use a permissive authenticated read for staff app).
- Because this is a staff back-office app (all signed-in staff can manage data), most tables use authenticated-scoped CRUD with USING(true)/WITH CHECK(true) after RLS — staff are trusted users. This is intentional for an internal management tool.

## Notes
1. This is an internal staff application — every authenticated user is a trusted employee.
2. RLS is enabled to lock tables from anon access; authenticated staff have full CRUD.
3. Owner-style isolation is not required between staff members in this back-office context.
*/

-- ============ PROFILES ============
CREATE TABLE IF NOT EXISTS public.profiles (
  id uuid PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  full_name text NOT NULL,
  phone text,
  role text NOT NULL DEFAULT 'employee' CHECK (role IN ('super_admin','manager','accountant','technician','employee')),
  employee_id bigint,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "profiles_select_authenticated" ON public.profiles;
CREATE POLICY "profiles_select_authenticated" ON public.profiles FOR SELECT TO authenticated USING (true);
DROP POLICY IF EXISTS "profiles_update_own" ON public.profiles;
CREATE POLICY "profiles_update_own" ON public.profiles FOR UPDATE TO authenticated USING (auth.uid() = id) WITH CHECK (auth.uid() = id);
DROP POLICY IF EXISTS "profiles_insert_own" ON public.profiles;
CREATE POLICY "profiles_insert_own" ON public.profiles FOR INSERT TO authenticated WITH CHECK (auth.uid() = id);
DROP POLICY IF EXISTS "profiles_admin_insert" ON public.profiles;
CREATE POLICY "profiles_admin_insert" ON public.profiles FOR INSERT TO authenticated WITH CHECK (true);

-- ============ AREAS & TOWERS ============
CREATE TABLE IF NOT EXISTS public.areas (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.areas ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.towers (
  id bigserial PRIMARY KEY,
  area_id bigint REFERENCES public.areas(id) ON DELETE SET NULL,
  name text NOT NULL,
  ip text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.towers ENABLE ROW LEVEL SECURITY;

-- ============ PLANS ============
CREATE TABLE IF NOT EXISTS public.plans (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  price numeric(12,2) NOT NULL DEFAULT 0,
  download_speed int NOT NULL DEFAULT 0,
  upload_speed int NOT NULL DEFAULT 0,
  burst text,
  validity int NOT NULL DEFAULT 30,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.plans ENABLE ROW LEVEL SECURITY;

-- ============ CUSTOMERS ============
CREATE TABLE IF NOT EXISTS public.customers (
  id bigserial PRIMARY KEY,
  full_name text NOT NULL,
  phone text,
  address text,
  area_id bigint REFERENCES public.areas(id) ON DELETE SET NULL,
  tower_id bigint REFERENCES public.towers(id) ON DELETE SET NULL,
  pppoe_username text UNIQUE,
  pppoe_password text,
  plan_id bigint REFERENCES public.plans(id) ON DELETE SET NULL,
  download_speed int DEFAULT 0,
  upload_speed int DEFAULT 0,
  static_ip text,
  mac_address text,
  activation_date date,
  expiration_date date,
  status text NOT NULL DEFAULT 'active' CHECK (status IN ('active','suspended','expired','inactive')),
  notes text,
  balance numeric(12,2) NOT NULL DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE public.customers ENABLE ROW LEVEL SECURITY;
CREATE INDEX IF NOT EXISTS idx_customers_status ON public.customers(status);
CREATE INDEX IF NOT EXISTS idx_customers_plan ON public.customers(plan_id);

-- ============ INVOICES ============
CREATE TABLE IF NOT EXISTS public.invoices (
  id bigserial PRIMARY KEY,
  invoice_number text UNIQUE,
  customer_id bigint REFERENCES public.customers(id) ON DELETE CASCADE,
  plan_id bigint REFERENCES public.plans(id) ON DELETE SET NULL,
  amount numeric(12,2) NOT NULL DEFAULT 0,
  tax numeric(12,2) NOT NULL DEFAULT 0,
  total numeric(12,2) NOT NULL DEFAULT 0,
  status text NOT NULL DEFAULT 'unpaid' CHECK (status IN ('paid','unpaid','partial','cancelled')),
  issued_date date DEFAULT current_date,
  due_date date,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.invoices ENABLE ROW LEVEL SECURITY;
CREATE INDEX IF NOT EXISTS idx_invoices_customer ON public.invoices(customer_id);

-- ============ PAYMENTS ============
CREATE TABLE IF NOT EXISTS public.payments (
  id bigserial PRIMARY KEY,
  invoice_id bigint REFERENCES public.invoices(id) ON DELETE SET NULL,
  customer_id bigint REFERENCES public.customers(id) ON DELETE CASCADE,
  amount numeric(12,2) NOT NULL DEFAULT 0,
  method text NOT NULL DEFAULT 'cash' CHECK (method IN ('cash','card','transfer','online')),
  paid_date date DEFAULT current_date,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.payments ENABLE ROW LEVEL SECURITY;

-- ============ EXPENSES ============
CREATE TABLE IF NOT EXISTS public.expenses (
  id bigserial PRIMARY KEY,
  category text,
  amount numeric(12,2) NOT NULL DEFAULT 0,
  expense_date date DEFAULT current_date,
  description text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.expenses ENABLE ROW LEVEL SECURITY;

-- ============ CASH BOX ============
CREATE TABLE IF NOT EXISTS public.cash_box_transactions (
  id bigserial PRIMARY KEY,
  type text NOT NULL CHECK (type IN ('in','out')),
  amount numeric(12,2) NOT NULL DEFAULT 0,
  source text,
  reference text,
  transaction_date date DEFAULT current_date,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.cash_box_transactions ENABLE ROW LEVEL SECURITY;

-- ============ DEBTS ============
CREATE TABLE IF NOT EXISTS public.debts (
  id bigserial PRIMARY KEY,
  customer_id bigint REFERENCES public.customers(id) ON DELETE CASCADE,
  amount numeric(12,2) NOT NULL DEFAULT 0,
  paid_amount numeric(12,2) NOT NULL DEFAULT 0,
  status text NOT NULL DEFAULT 'open' CHECK (status IN ('open','settled')),
  due_date date,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.debts ENABLE ROW LEVEL SECURITY;

-- ============ INVENTORY ============
CREATE TABLE IF NOT EXISTS public.inventory_categories (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.inventory_categories ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.inventory_suppliers (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  phone text,
  email text,
  address text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.inventory_suppliers ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.inventory_products (
  id bigserial PRIMARY KEY,
  category_id bigint REFERENCES public.inventory_categories(id) ON DELETE SET NULL,
  supplier_id bigint REFERENCES public.inventory_suppliers(id) ON DELETE SET NULL,
  name text NOT NULL,
  sku text UNIQUE,
  cost_price numeric(12,2) DEFAULT 0,
  sale_price numeric(12,2) DEFAULT 0,
  quantity int NOT NULL DEFAULT 0,
  min_quantity int NOT NULL DEFAULT 0,
  unit text DEFAULT 'piece',
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.inventory_products ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.stock_movements (
  id bigserial PRIMARY KEY,
  product_id bigint REFERENCES public.inventory_products(id) ON DELETE CASCADE,
  type text NOT NULL CHECK (type IN ('in','out','adjust')),
  quantity int NOT NULL DEFAULT 0,
  reference text,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.stock_movements ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.product_serials (
  id bigserial PRIMARY KEY,
  product_id bigint REFERENCES public.inventory_products(id) ON DELETE CASCADE,
  serial text NOT NULL,
  status text NOT NULL DEFAULT 'in_stock' CHECK (status IN ('in_stock','sold','returned')),
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.product_serials ENABLE ROW LEVEL SECURITY;

-- ============ EMPLOYEES ============
CREATE TABLE IF NOT EXISTS public.departments (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.departments ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.employees (
  id bigserial PRIMARY KEY,
  full_name text NOT NULL,
  phone text,
  department_id bigint REFERENCES public.departments(id) ON DELETE SET NULL,
  position text,
  salary numeric(12,2) DEFAULT 0,
  hire_date date DEFAULT current_date,
  status text NOT NULL DEFAULT 'active' CHECK (status IN ('active','inactive')),
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.employees ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.attendance (
  id bigserial PRIMARY KEY,
  employee_id bigint REFERENCES public.employees(id) ON DELETE CASCADE,
  date date NOT NULL DEFAULT current_date,
  check_in timestamptz,
  check_out timestamptz,
  status text NOT NULL DEFAULT 'present' CHECK (status IN ('present','absent','late','leave')),
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.attendance ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.leaves (
  id bigserial PRIMARY KEY,
  employee_id bigint REFERENCES public.employees(id) ON DELETE CASCADE,
  start_date date,
  end_date date,
  type text NOT NULL DEFAULT 'annual' CHECK (type IN ('annual','sick','unpaid','emergency')),
  status text NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','approved','rejected')),
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.leaves ENABLE ROW LEVEL SECURITY;

-- ============ SUPPORT TICKETS ============
CREATE TABLE IF NOT EXISTS public.support_tickets (
  id bigserial PRIMARY KEY,
  customer_id bigint REFERENCES public.customers(id) ON DELETE SET NULL,
  subject text NOT NULL,
  description text,
  priority text NOT NULL DEFAULT 'medium' CHECK (priority IN ('low','medium','high','urgent')),
  status text NOT NULL DEFAULT 'open' CHECK (status IN ('open','assigned','in_progress','resolved','closed')),
  assigned_to uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE public.support_tickets ENABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.ticket_replies (
  id bigserial PRIMARY KEY,
  ticket_id bigint REFERENCES public.support_tickets(id) ON DELETE CASCADE,
  author_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  message text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.ticket_replies ENABLE ROW LEVEL SECURITY;

-- ============ ROUTERS (MikroTik) ============
CREATE TABLE IF NOT EXISTS public.routers (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  ip text NOT NULL,
  port int NOT NULL DEFAULT 8728,
  username text NOT NULL,
  password text,
  use_ssl boolean NOT NULL DEFAULT false,
  status text NOT NULL DEFAULT 'offline' CHECK (status IN ('online','offline','error')),
  last_checked timestamptz,
  notes text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.routers ENABLE ROW LEVEL SECURITY;

-- ============ ACTIVITY LOGS ============
CREATE TABLE IF NOT EXISTS public.activity_logs (
  id bigserial PRIMARY KEY,
  user_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  action text NOT NULL,
  module text,
  description text,
  ip_address text,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.activity_logs ENABLE ROW LEVEL SECURITY;

-- ============ NOTIFICATIONS ============
CREATE TABLE IF NOT EXISTS public.notifications (
  id bigserial PRIMARY KEY,
  channel text NOT NULL CHECK (channel IN ('telegram','email','whatsapp')),
  recipient text NOT NULL,
  subject text,
  message text NOT NULL,
  status text NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','sent','failed')),
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.notifications ENABLE ROW LEVEL SECURITY;

-- ============ SETTINGS ============
CREATE TABLE IF NOT EXISTS public.settings (
  id bigserial PRIMARY KEY,
  key text UNIQUE NOT NULL,
  value text,
  group_name text NOT NULL DEFAULT 'general',
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.settings ENABLE ROW LEVEL SECURITY;

-- ============ Generic authenticated CRUD policies for staff tables ============
-- For internal staff tables, authenticated users have full CRUD.
-- We add per-table policies in a loop-friendly manner.

DO $$
DECLARE
  t text;
  tables text[] := ARRAY[
    'areas','towers','plans','customers','invoices','payments','expenses',
    'cash_box_transactions','debts','inventory_categories','inventory_suppliers',
    'inventory_products','stock_movements','product_serials','departments',
    'employees','attendance','leaves','support_tickets','ticket_replies',
    'routers','activity_logs','notifications','settings'
  ];
BEGIN
  FOREACH t IN ARRAY tables LOOP
    EXECUTE format('DROP POLICY IF EXISTS "auth_select_%s" ON public.%I;', t, t);
    EXECUTE format('CREATE POLICY "auth_select_%s" ON public.%I FOR SELECT TO authenticated USING (true);', t, t);
    EXECUTE format('DROP POLICY IF EXISTS "auth_insert_%s" ON public.%I;', t, t);
    EXECUTE format('CREATE POLICY "auth_insert_%s" ON public.%I FOR INSERT TO authenticated WITH CHECK (true);', t, t);
    EXECUTE format('DROP POLICY IF EXISTS "auth_update_%s" ON public.%I;', t, t);
    EXECUTE format('CREATE POLICY "auth_update_%s" ON public.%I FOR UPDATE TO authenticated USING (true) WITH CHECK (true);', t, t);
    EXECUTE format('DROP POLICY IF EXISTS "auth_delete_%s" ON public.%I;', t, t);
    EXECUTE format('CREATE POLICY "auth_delete_%s" ON public.%I FOR DELETE TO authenticated USING (true);', t, t);
  END LOOP;
END $$;

-- updated_at trigger for customers and tickets
CREATE OR REPLACE FUNCTION public.touch_updated_at()
RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_customers_touch ON public.customers;
CREATE TRIGGER trg_customers_touch BEFORE UPDATE ON public.customers
  FOR EACH ROW EXECUTE FUNCTION public.touch_updated_at();

DROP TRIGGER IF EXISTS trg_tickets_touch ON public.support_tickets;
CREATE TRIGGER trg_tickets_touch BEFORE UPDATE ON public.support_tickets
  FOR EACH ROW EXECUTE FUNCTION public.touch_updated_at();
