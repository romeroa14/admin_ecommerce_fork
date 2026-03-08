-- =========================================================================================
-- MIGRACIÓN PARA COMPRAS COMO INVITADO (GUEST CHECKOUT)
-- =========================================================================================
-- Este archivo contiene las instrucciones SQL necesarias para adaptar la base de datos
-- y permitir que usuarios no registrados (invitados) puedan realizar compras.
-- Por favor, ejecuta las instrucciones correspondientes a tu motor de base de datos.
-- =========================================================================================


-- -----------------------------------------------------------------------------------------
-- OPCIÓN A: POSTGRESQL (Entorno Local/Producción)
-- -----------------------------------------------------------------------------------------
-- 1. Hacer que 'user_id' sea opcional en la tabla de direcciones
ALTER TABLE addresses ALTER COLUMN user_id DROP NOT NULL;

-- 2. Hacer que 'user_id' sea opcional en la tabla de pedidos
ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL;

-- 3. Hacer que 'postal_code' sea opcional (hay países o zonas sin código postal obligatorio)
ALTER TABLE addresses ALTER COLUMN postal_code DROP NOT NULL;

-- 4. Añadir columna 'email' para poder contactar al invitado
ALTER TABLE addresses ADD COLUMN IF NOT EXISTS email VARCHAR(255) NULL;


-- -----------------------------------------------------------------------------------------
-- OPCIÓN B: MYSQL / MARIADB (Entorno Producción Típico)
-- -----------------------------------------------------------------------------------------
-- 1. Hacer que 'user_id' sea opcional en la tabla de direcciones (Asumiendo BIGINT UNSIGNED)
ALTER TABLE addresses MODIFY user_id BIGINT UNSIGNED NULL;

-- 2. Hacer que 'user_id' sea opcional en la tabla de pedidos
ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL;

-- 3. Hacer que 'postal_code' sea opcional
ALTER TABLE addresses MODIFY postal_code VARCHAR(255) NULL;

-- 4. Añadir columna 'email'
-- Nota: Si el campo email ya existe en tu DB de Producción, ignora este comando.
ALTER TABLE addresses ADD COLUMN email VARCHAR(255) NULL AFTER user_id;
