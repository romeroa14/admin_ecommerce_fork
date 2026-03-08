-- MySQL updates for Guest Checkout

-- 1. Make user_id nullable in addresses
ALTER TABLE addresses MODIFY user_id BIGINT UNSIGNED NULL;

-- 2. Make user_id nullable in orders
ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL;

-- 3. Make postal_code nullable in addresses (some guests might not have it depending on region)
ALTER TABLE addresses MODIFY postal_code VARCHAR(255) NULL;

-- 4. Add email column to addresses
ALTER TABLE addresses ADD COLUMN email VARCHAR(255) NULL AFTER user_id;
