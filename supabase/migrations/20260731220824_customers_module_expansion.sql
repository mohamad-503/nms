/*
# Customers module expansion

## Overview
Extends the `customers` table with additional fields requested for the full
Customers Management module and adds a `cities` reference table. Also creates
a storage bucket for customer profile photos.

## Changes to `customers` table
New columns (all nullable / defaulted so existing rows remain valid):
- `national_id` text — national identity number
- `city_id` bigint — references `cities(id)` (city dropdown)
- `installation_date` date — date of physical installation
- `subscription_start` date — subscription start date
- `subscription_end` date — subscription end date
- `monthly_price` numeric(12,2) — monthly recurring price
- `profile_photo` text — URL of uploaded profile photo in storage

## New tables
- `cities` — reference list of cities (id, name, created_at)

## Storage
- Creates public bucket `customer-photos` for profile photo uploads.

## Security
- RLS enabled on `cities` with authenticated CRUD (internal staff app).
- Storage bucket policies: authenticated users can upload/read; public read.
- Existing `customers` policies already allow authenticated CRUD, so the new
  columns inherit that access automatically.

## Notes
1. `activation_date` and `expiration_date` are kept for backward compatibility;
   the UI uses `subscription_start`/`subscription_end` as the primary fields.
2. All additions are additive — no data is lost.
*/

-- ============ CITIES ============
CREATE TABLE IF NOT EXISTS public.cities (
  id bigserial PRIMARY KEY,
  name text NOT NULL,
  created_at timestamptz DEFAULT now()
);
ALTER TABLE public.cities ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "auth_select_cities" ON public.cities;
CREATE POLICY "auth_select_cities" ON public.cities FOR SELECT TO authenticated USING (true);
DROP POLICY IF EXISTS "auth_insert_cities" ON public.cities;
CREATE POLICY "auth_insert_cities" ON public.cities FOR INSERT TO authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "auth_update_cities" ON public.cities;
CREATE POLICY "auth_update_cities" ON public.cities FOR UPDATE TO authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "auth_delete_cities" ON public.cities;
CREATE POLICY "auth_delete_cities" ON public.cities FOR DELETE TO authenticated USING (true);

-- ============ ADD COLUMNS TO CUSTOMERS ============
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='national_id') THEN
    ALTER TABLE public.customers ADD COLUMN national_id text;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='city_id') THEN
    ALTER TABLE public.customers ADD COLUMN city_id bigint REFERENCES public.cities(id) ON DELETE SET NULL;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='installation_date') THEN
    ALTER TABLE public.customers ADD COLUMN installation_date date;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='subscription_start') THEN
    ALTER TABLE public.customers ADD COLUMN subscription_start date;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='subscription_end') THEN
    ALTER TABLE public.customers ADD COLUMN subscription_end date;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='monthly_price') THEN
    ALTER TABLE public.customers ADD COLUMN monthly_price numeric(12,2) DEFAULT 0;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name='customers' AND column_name='profile_photo') THEN
    ALTER TABLE public.customers ADD COLUMN profile_photo text;
  END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_customers_city ON public.customers(city_id);
CREATE INDEX IF NOT EXISTS idx_customers_national_id ON public.customers(national_id);

-- ============ STORAGE BUCKET ============
INSERT INTO storage.buckets (id, name, public)
VALUES ('customer-photos', 'customer-photos', true)
ON CONFLICT (id) DO NOTHING;

-- Storage policies: authenticated can upload/update/delete; public read
DROP POLICY IF EXISTS "customer_photos_read" ON storage.objects;
CREATE POLICY "customer_photos_read" ON storage.objects
  FOR SELECT TO public USING (bucket_id = 'customer-photos');

DROP POLICY IF EXISTS "customer_photos_insert" ON storage.objects;
CREATE POLICY "customer_photos_insert" ON storage.objects
  FOR INSERT TO authenticated WITH CHECK (bucket_id = 'customer-photos');

DROP POLICY IF EXISTS "customer_photos_update" ON storage.objects;
CREATE POLICY "customer_photos_update" ON storage.objects
  FOR UPDATE TO authenticated USING (bucket_id = 'customer-photos') WITH CHECK (bucket_id = 'customer-photos');

DROP POLICY IF EXISTS "customer_photos_delete" ON storage.objects;
CREATE POLICY "customer_photos_delete" ON storage.objects
  FOR DELETE TO authenticated USING (bucket_id = 'customer-photos');

-- ============ SEED CITIES ============
INSERT INTO public.cities (name) VALUES
  ('بغداد'),('البصرة'),('الموصل'),('أربيل'),('النجف'),('كربلاء'),
  ('كركوك'),('السليمانية'),('الناصرية'),('العمارة'),('الديوانية'),('الرمادي'),
  ('تكريت'),('الحلة'),('السماوة')
ON CONFLICT DO NOTHING;
