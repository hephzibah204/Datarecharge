-- SQLite-compatible schema for Provider & Pricing System
-- Usage: sqlite3 database/providers.db < database/schema.sqlite.sql

PRAGMA foreign_keys = ON;

-- -----------------------------------------------------------
-- 1. PROVIDERS TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `providers` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `type` TEXT NOT NULL DEFAULT 'dataverify',
    `display_name` TEXT,
    `description` TEXT,
    `endpoint_url` TEXT,
    `api_key` TEXT,
    `api_version` TEXT DEFAULT '1.0',
    `timeout` INTEGER DEFAULT 30,
    `supported_actions` TEXT,
    `js_file` TEXT,
    `external_script` TEXT,
    `priority` INTEGER DEFAULT 0,
    `is_active` INTEGER DEFAULT 1,
    `config` TEXT,
    `start_date` TEXT DEFAULT '1970-01-01',
    `end_date` TEXT,
    `last_check` TEXT,
    `ping_count` INTEGER DEFAULT 0,
    `error_count` INTEGER DEFAULT 0,
    `created_at` TEXT DEFAULT (datetime('now','localtime')),
    `updated_at` TEXT DEFAULT (datetime('now','localtime'))
);

CREATE INDEX IF NOT EXISTS `idx_providers_type_active` ON `providers` (`type`, `is_active`);
CREATE INDEX IF NOT EXISTS `idx_providers_priority` ON `providers` (`priority` DESC);

-- -----------------------------------------------------------
-- 2. PROVIDER PRICING TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_pricing` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `provider_id` INTEGER NOT NULL,
    `service_type` TEXT NOT NULL,
    `base_fee` REAL NOT NULL DEFAULT 0.00,
    `cost_price` REAL DEFAULT 0.00,
    `urgency_multiplier` REAL DEFAULT 1.00,
    `priority_fee` REAL DEFAULT 0.00,
    `max_discount` REAL DEFAULT 0.00,
    `currency` TEXT DEFAULT 'NGN',
    `is_urgent_available` INTEGER DEFAULT 0,
    `is_express_available` INTEGER DEFAULT 0,
    `min_processing_hours` INTEGER DEFAULT 0,
    `max_processing_hours` INTEGER DEFAULT 24,
    `plan_name` TEXT,
    `plan_id` TEXT,
    `plan_duration` TEXT,
    `network_id` INTEGER DEFAULT NULL,
    `is_percentage` INTEGER DEFAULT 0,
    `is_active` INTEGER DEFAULT 1,
    `effective_start` TEXT DEFAULT '1970-01-01',
    `effective_end` TEXT DEFAULT NULL,
    `created_at` TEXT DEFAULT (datetime('now','localtime')),
    `updated_at` TEXT DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS `idx_pricing_provider_service` ON `provider_pricing` (`provider_id`, `service_type`);
CREATE INDEX IF NOT EXISTS `idx_pricing_active` ON `provider_pricing` (`is_active`, `effective_start`, `effective_end`);
CREATE UNIQUE INDEX IF NOT EXISTS `idx_pricing_unique` ON `provider_pricing` (`provider_id`, `service_type`);

-- -----------------------------------------------------------
-- 3. PRICE OVERRIDES TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `price_overrides` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `provider_id` INTEGER NOT NULL,
    `service_type` TEXT NOT NULL,
    `user_type` TEXT NOT NULL DEFAULT 'user',
    `override_fee` REAL NOT NULL,
    `is_active` INTEGER DEFAULT 1,
    `start_date` TEXT DEFAULT '1970-01-01',
    `end_date` TEXT DEFAULT NULL,
    `created_at` TEXT DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS `idx_overrides_lookup` ON `price_overrides` (`provider_id`, `service_type`, `user_type`, `is_active`);
CREATE UNIQUE INDEX IF NOT EXISTS `idx_overrides_unique` ON `price_overrides` (`provider_id`, `service_type`, `user_type`);

-- -----------------------------------------------------------
-- 4. PROVIDER LOGS TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_logs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `provider_id` INTEGER NOT NULL,
    `action` TEXT NOT NULL,
    `request_data` TEXT,
    `response` TEXT,
    `status` TEXT,
    `user_id` INTEGER DEFAULT NULL,
    `ip_address` TEXT,
    `user_agent` TEXT,
    `created_at` TEXT DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS `idx_logs_provider_action` ON `provider_logs` (`provider_id`, `action`);
CREATE INDEX IF NOT EXISTS `idx_logs_created` ON `provider_logs` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_logs_status` ON `provider_logs` (`status`);

-- -----------------------------------------------------------
-- 5. PROVIDER PERFORMANCE METRICS TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_performance_metrics` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `provider_id` INTEGER NOT NULL,
    `date` TEXT NOT NULL,
    `total_requests` INTEGER DEFAULT 0,
    `successful_requests` INTEGER DEFAULT 0,
    `failed_requests` INTEGER DEFAULT 0,
    `avg_response_time_ms` REAL DEFAULT 0.00,
    `total_revenue` REAL DEFAULT 0.00,
    `total_cost` REAL DEFAULT 0.00,
    `created_at` TEXT DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_metrics_provider_date` ON `provider_performance_metrics` (`provider_id`, `date`);

-- ============================================================
-- VIEWS
-- ============================================================

DROP VIEW IF EXISTS `provider_pricing_with_overrides`;
CREATE VIEW `provider_pricing_with_overrides` AS
SELECT
    p.id,
    p.name,
    p.type,
    pp.provider_id,
    pp.service_type,
    pp.base_fee,
    pp.cost_price,
    pp.urgency_multiplier,
    pp.priority_fee,
    pp.max_discount,
    pp.currency,
    pp.is_percentage,
    pp.plan_name,
    pp.plan_id,
    pp.plan_duration,
    pp.network_id,
    COALESCE(po.override_fee, pp.base_fee) AS final_fee,
    pp.is_urgent_available,
    pp.is_express_available,
    pp.min_processing_hours,
    pp.max_processing_hours,
    pp.is_active
FROM providers p
JOIN provider_pricing pp ON p.id = pp.provider_id
LEFT JOIN (
    SELECT po1.provider_id, po1.service_type, po1.override_fee
    FROM price_overrides po1
    WHERE po1.is_active = 1
      AND po1.start_date <= date('now')
      AND (po1.end_date IS NULL OR po1.end_date >= date('now'))
    GROUP BY po1.provider_id, po1.service_type
) po ON po.provider_id = pp.provider_id AND po.service_type = pp.service_type
WHERE pp.is_active = 1
ORDER BY p.priority DESC, p.name, pp.service_type;

DROP VIEW IF EXISTS `user_pricing`;
CREATE VIEW `user_pricing` AS
SELECT
    p.id,
    p.name,
    p.type,
    pp.service_type,
    pp.base_fee,
    pp.cost_price,
    COALESCE(po_u.override_fee, pp.base_fee) AS user_fee,
    COALESCE(po_a.override_fee, pp.base_fee) AS agent_fee,
    COALESCE(po_v.override_fee, pp.base_fee) AS vendor_fee,
    pp.plan_name,
    pp.plan_id,
    pp.plan_duration,
    pp.network_id,
    pp.is_percentage,
    pp.is_active
FROM providers p
JOIN provider_pricing pp ON p.id = pp.provider_id
LEFT JOIN price_overrides po_u ON po_u.provider_id = pp.provider_id
    AND po_u.service_type = pp.service_type
    AND po_u.user_type = 'user'
    AND po_u.is_active = 1
LEFT JOIN price_overrides po_a ON po_a.provider_id = pp.provider_id
    AND po_a.service_type = pp.service_type
    AND po_a.user_type = 'agent'
    AND po_a.is_active = 1
LEFT JOIN price_overrides po_v ON po_v.provider_id = pp.provider_id
    AND po_v.service_type = pp.service_type
    AND po_v.user_type = 'vendor'
    AND po_v.is_active = 1
WHERE pp.is_active = 1
ORDER BY p.priority DESC, p.name, pp.service_type;

-- ============================================================
-- SEED DATA
-- ============================================================

BEGIN TRANSACTION;

-- DataVerify (NIN/BVN slips, bank account, IPE clearance)
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`, `config`, `endpoint_url`, `api_key`)
VALUES ('DataVerify', 'dataverify', 'DataVerify ID Service', 'NIN, BVN, Bank Account, and IPE verification services', 100, 1,
 'verify_nin,verify_bvn,verify_bank,ipe,ipe_status',
 '{
   "slip_types": {
     "generate_nin_slip": {"min_price": 300, "max_price": 50000, "description": "NIN Slip Generation"},
     "generate_bvn_slip": {"min_price": 500, "max_price": 50000, "description": "BVN Slip Generation"},
     "lookup_ipe": {"min_price": 200, "max_price": 20000, "description": "IPE Lookup"}
   },
   "endpoints": {
     "verify_nin": "/developers/nin_slips/nin_premium",
     "verify_nin_phone": "/developers/nin_slips/nin_by_phone.php",
     "verify_nin_demo": "/developers/nin_slips/nin_premium_demo.php",
     "verify_bvn": "/developers/bvn_slip/bvn_premium.php",
     "verify_bank": "/developers/nin_slips/bank_account_verify.php",
     "ipe": "/api/developers/ipe2.php",
     "ipe_status": "/api/developers/ipe_status2.php"
   }
 }',
 'https://dataverify.com.ng',
 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM');

-- Airtime
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('MTN Airtime', 'airtime', 'MTN VTU Airtime', 'MTN VTU airtime topup and Share & Sell', 90, 1, 'vtu,share_and_sell'),
('GLO Airtime', 'airtime', 'GLO VTU Airtime', 'GLO VTU airtime topup and Share & Sell', 85, 1, 'vtu,share_and_sell'),
('9MOBILE Airtime', 'airtime', '9MOBILE VTU Airtime', '9MOBILE VTU airtime topup and Share & Sell', 80, 1, 'vtu,share_and_sell'),
('AIRTEL Airtime', 'airtime', 'AIRTEL VTU Airtime', 'AIRTEL VTU airtime topup and Share & Sell', 85, 1, 'vtu,share_and_sell');

-- Data
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('MTN Data', 'data', 'MTN Data Plans', 'MTN SME, Gifting, Corporate, and Awoof data plans', 90, 1, 'sme,gifting,corporate,awoof,coupon,datapin'),
('GLO Data', 'data', 'GLO Data Plans', 'GLO SME, Gifting, and Corporate data plans', 80, 1, 'sme,gifting,corporate,datapin'),
('9MOBILE Data', 'data', '9MOBILE Data Plans', '9MOBILE SME, Gifting, and Corporate data plans', 75, 1, 'sme,gifting,corporate,datapin'),
('AIRTEL Data', 'data', 'AIRTEL Data Plans', 'AIRTEL SME, Gifting, and Corporate data plans', 85, 1, 'sme,gifting,corporate,datapin');

-- Cable TV
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('GOtv', 'cabletv', 'GOtv Subscription', 'GOtv Max, Jolli, Jinja, Smallie, Supa plans', 70, 1, 'validate,subscribe'),
('DSTv', 'cabletv', 'DSTv Subscription', 'DSTv Compact, Yanga, Premium, Confam plans', 70, 1, 'validate,subscribe'),
('STARTIMES', 'cabletv', 'STARTIMES Subscription', 'STARTIMES Nova, Basic, Smart, Classic, Super plans', 65, 1, 'validate,subscribe');

-- Electricity
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('Electricity', 'electricity', 'Electricity Bill Payment', 'Prepaid and postpaid electricity bill payment for all discos', 60, 1, 'validate_meter,pay_bill');

-- Exam PIN
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('Exam PIN', 'exam', 'Exam PIN Purchase', 'WAEC, NECO, and NABTEB exam PIN purchases', 50, 1, 'purchase,verify');

-- -----------------------------------------------------------
-- DataVerify Pricing (provider_id=1)
-- -----------------------------------------------------------
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `urgency_multiplier`, `priority_fee`, `max_discount`, `is_urgent_available`, `is_express_available`, `min_processing_hours`, `max_processing_hours`, `is_active`) VALUES
(1, 'verify_nin', 30.00, 20.00, 1.50, 10.00, 5.00, 1, 1, 0, 2, 1),
(1, 'verify_bvn', 5.00, 3.00, 1.50, 5.00, 2.00, 1, 0, 0, 1, 1),
(1, 'verify_bank_account', 50.00, 35.00, 1.50, 10.00, 5.00, 1, 0, 0, 1, 1),
(1, 'lookup_ipe', 100.00, 70.00, 2.00, 20.00, 10.00, 1, 1, 0, 2, 1),
(1, 'generate_nin_slip', 500.00, 350.00, 2.00, 100.00, 20.00, 1, 1, 1, 24, 1),
(1, 'generate_bvn_slip', 800.00, 600.00, 2.00, 150.00, 20.00, 1, 1, 1, 24, 1),
(1, 'advanced_bvn', 300.00, 200.00, 1.50, 50.00, 10.00, 1, 0, 0, 2, 1),
(1, 'verify_customer', 50.00, 30.00, 1.50, 10.00, 5.00, 1, 0, 0, 1, 1),
(1, 'verify_phone', 20.00, 10.00, 1.50, 5.00, 3.00, 1, 0, 0, 1, 1);

-- Airtime (percentage-based)
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(2, 'mtn_vtu_1', 99.00, 97.00, 1, 1, 'instant', 1),
(3, 'glo_vtu_2', 99.00, 93.00, 1, 2, 'instant', 1),
(4, '9mobile_vtu_3', 99.00, 99.00, 1, 3, 'instant', 1),
(5, 'airtel_vtu_4', 99.00, 97.00, 1, 4, 'instant', 1);

-- Airime Share & Sell
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(2, 'mtn_sharesell_1', 99.00, 98.00, 1, 1, 'instant', 1),
(3, 'glo_sharesell_2', 99.00, 98.00, 1, 2, 'instant', 1),
(4, '9mobile_sharesell_3', 99.00, 98.00, 1, 3, 'instant', 1),
(5, 'airtel_sharesell_4', 99.00, 98.00, 1, 4, 'instant', 1);

-- Price overrides for DataVerify
INSERT INTO `price_overrides` (`provider_id`, `service_type`, `user_type`, `override_fee`, `is_active`) VALUES
(1, 'verify_nin', 'enterprise', 25.00, 1),
(1, 'verify_bvn', 'enterprise', 4.00, 1),
(1, 'generate_nin_slip', 'premium', 450.00, 1),
(1, 'generate_nin_slip', 'enterprise', 400.00, 1),
(1, 'generate_bvn_slip', 'premium', 750.00, 1),
(1, 'generate_bvn_slip', 'enterprise', 700.00, 1);

-- -----------------------------------------------------------
-- 11. NIN MODIFICATIONS TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `nin_modifications` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `sId` INTEGER NOT NULL,
    `ref` TEXT NOT NULL UNIQUE,
    `type` TEXT NOT NULL,
    `new_value` TEXT,
    `reason` TEXT,
    `fee` REAL DEFAULT 0,
    `status` TEXT DEFAULT 'pending',
    `date_created` TEXT,
    `date_reviewed` TEXT,
    `reviewed_by` INTEGER DEFAULT 0,
    `admin_notes` TEXT,
    `documents` TEXT DEFAULT '[]',
    `result_document` TEXT
);

-- -----------------------------------------------------------
-- 12. NIN REQUESTS TABLE (verification & modification requests)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `nin_requests` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `sId` INTEGER NOT NULL,
    `ref` TEXT NOT NULL UNIQUE,
    `type` TEXT NOT NULL,
    `new_value` TEXT,
    `reason` TEXT,
    `fee` REAL DEFAULT 0,
    `status` TEXT DEFAULT 'pending',
    `date_created` TEXT,
    `date_reviewed` TEXT,
    `reviewed_by` INTEGER DEFAULT 0,
    `admin_notes` TEXT,
    `documents` TEXT DEFAULT '[]',
    `result_document` TEXT
);

-- -----------------------------------------------------------
-- 13. NIN PRICE TABLE (slip pricing for mobile app)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `nin_price` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `slip_name` TEXT NOT NULL UNIQUE,
    `buying_price` REAL DEFAULT 0,
    `user_price` REAL DEFAULT 0
);

INSERT OR IGNORE INTO `nin_price` (`slip_name`, `buying_price`, `user_price`) VALUES
('RegularSlip', 350, 500),
('StandardSlip', 700, 1000),
('PremiumSlip', 1050, 1500);

-- -----------------------------------------------------------
-- NIN Fee columns for sitesettings (created by MySQL dump)
-- -----------------------------------------------------------
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_name_mod` REAL DEFAULT 5000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_phone_mod` REAL DEFAULT 5000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_address_mod` REAL DEFAULT 4000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_email_mod` REAL DEFAULT 4000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_dob_mod` REAL DEFAULT 28574;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_lga_mod` REAL DEFAULT 3000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_gender_mod` REAL DEFAULT 8000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_marital_mod` REAL DEFAULT 6000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_nin_verification` REAL DEFAULT 1000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_affidavit` REAL DEFAULT 5000;
ALTER TABLE IF EXISTS `sitesettings` ADD COLUMN `fee_birth_certificate` REAL DEFAULT 10000;

-- -----------------------------------------------------------
-- NIN API Configuration
-- -----------------------------------------------------------
INSERT OR IGNORE INTO `apiconfigs` (`name`, `value`) VALUES
('ninApi', '7b5c41954df297ef02e878e6ace8d373e09ee0aa646555cd04fa70d3dd05ad79'),
('ninProvider', 'https://ambverify.com.ng/api/v1'),
('ninStatus', 'On'),
('dataVerifyApi', 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM'),
('dataVerifyProvider', 'https://dataverify.com.ng');

COMMIT;
