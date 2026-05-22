-- ============================================================
-- MIGRATION: Add is_archived & archived_at to grade tables
-- Run this ONCE on your existing eusebia database
-- before using the Grade Promotion feature.
-- ============================================================

ALTER TABLE `tbl_seven`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_eight`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_nine`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_ten`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_eleven`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_twelve`
    ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL;

-- ============================================================
-- Verify (optional)
-- ============================================================
-- SELECT TABLE_NAME, COLUMN_NAME
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_SCHEMA = 'eusebia'
--   AND COLUMN_NAME IN ('is_archived','archived_at')
-- ORDER BY TABLE_NAME;
