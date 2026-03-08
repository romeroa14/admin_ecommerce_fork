-- PostgreSQL updates for Guest Checkout

-- 1. Make user_id nullable in addresses
ALTER TABLE addresses ALTER COLUMN user_id DROP NOT NULL;

-- 2. Make user_id nullable in orders
ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL;

-- 3. Make postal_code nullable in addresses (some guests might not have it depending on region)
ALTER TABLE addresses ALTER COLUMN postal_code DROP NOT NULL;

-- 4. Add email column to addresses
ALTER TABLE addresses ADD COLUMN email VARCHAR(255) NULL;
