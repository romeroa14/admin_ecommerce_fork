-- =========================================================================================
-- MIGRACIÓN PARA NUEVO TIPO DE BANNER (home_middle_2)
-- =========================================================================================
-- Este archivo contiene las instrucciones SQL necesarias para cambiar la columna
-- 'position' de la tabla 'banners' para que ya no use un tipo ENUM estricto y en su lugar 
-- sea un VARCHAR estándar. Esto permite agregar nuevas ubicaciones (como el 
-- Banner Mediano Central) fácilmente sin romper la estructura de la base de datos.
-- =========================================================================================


-- -----------------------------------------------------------------------------------------
-- OPCIÓN A: POSTGRESQL (Entorno Local/Producción)
-- -----------------------------------------------------------------------------------------
-- Eliminar la restricción CHECK que se crea por defecto al usar enum() en Laravel PostgreSQL
ALTER TABLE banners DROP CONSTRAINT IF EXISTS "banners_position_check";

-- Convertir la columna para que permita cualquier texto
ALTER TABLE banners ALTER COLUMN position TYPE VARCHAR(255);

-- Opcional: Si el sistema usó Enum con un tipo personalizado en Postgres, tal vez debas agregar:
-- ALTER TYPE banners_position_enum ADD VALUE 'home_middle_2';


-- -----------------------------------------------------------------------------------------
-- OPCIÓN B: MYSQL / MARIADB (Entorno Producción Típico)
-- -----------------------------------------------------------------------------------------
-- Cambiar el tipo de datos para que sea un campo de texto estándar y sea flexible a escalabilidad
ALTER TABLE banners MODIFY position VARCHAR(255) NOT NULL DEFAULT 'home_hero';
