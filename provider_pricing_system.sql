-- ============================================================
-- Provider & Pricing System for DataRecharge
-- Compatible with MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

-- -----------------------------------------------------------
-- 1. PROVIDERS TABLE
-- Matches what server.php, index.php, and dashboard.php expect
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `providers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('dataverify','airtime','data','cabletv','electricity','exam','bvn') NOT NULL DEFAULT 'dataverify',
    `display_name` VARCHAR(100) DEFAULT NULL,
    `description` TEXT,
    `endpoint_url` VARCHAR(500) DEFAULT NULL,
    `api_key` VARCHAR(500) DEFAULT NULL,
    `api_version` VARCHAR(20) DEFAULT '1.0',
    `timeout` INT DEFAULT 30,
    `supported_actions` TEXT COMMENT 'Comma-separated list of supported actions',
    `js_file` VARCHAR(255) DEFAULT NULL,
    `external_script` VARCHAR(500) DEFAULT NULL,
    `priority` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `config` JSON COMMENT 'Provider-specific JSON configuration (slip_types, etc.)',
    `start_date` DATE DEFAULT '1970-01-01',
    `end_date` DATE DEFAULT NULL,
    `last_check` DATETIME DEFAULT NULL,
    `ping_count` INT DEFAULT 0,
    `error_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type_active` (`type`, `is_active`),
    INDEX `idx_priority` (`priority` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 2. PROVIDER PRICING TABLE
-- Base pricing per provider per service type
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_pricing` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT NOT NULL,
    `service_type` VARCHAR(100) NOT NULL COMMENT 'e.g. generate_nin_slip, lookup_bvn, mtn_sme_1gb',
    `base_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Default selling price',
    `cost_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Admin cost / buying price',
    `urgency_multiplier` DECIMAL(3,2) DEFAULT 1.00,
    `priority_fee` DECIMAL(10,2) DEFAULT 0.00,
    `max_discount` DECIMAL(5,2) DEFAULT 0.00,
    `currency` VARCHAR(10) DEFAULT 'NGN',
    `is_urgent_available` TINYINT(1) DEFAULT 0,
    `is_express_available` TINYINT(1) DEFAULT 0,
    `min_processing_hours` INT DEFAULT 0,
    `max_processing_hours` INT DEFAULT 24,
    `plan_name` VARCHAR(255) DEFAULT NULL COMMENT 'Original plan name from dataplans/cableplans',
    `plan_id` VARCHAR(100) DEFAULT NULL COMMENT 'External plan ID from provider API',
    `plan_duration` VARCHAR(50) DEFAULT NULL COMMENT 'e.g. 7 days, 30 days',
    `network_id` INT DEFAULT NULL COMMENT 'FK to networkid.nId or cableid.cId',
    `is_percentage` TINYINT(1) DEFAULT 0 COMMENT '1 = fee is a percentage (airtime discounts)',
    `is_active` TINYINT(1) DEFAULT 1,
    `effective_start` DATE DEFAULT '1970-01-01',
    `effective_end` DATE DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE,
    INDEX `idx_provider_service` (`provider_id`, `service_type`),
    INDEX `idx_active_effective` (`is_active`, `effective_start`, `effective_end`),
    INDEX `idx_network` (`network_id`),
    UNIQUE KEY `uk_provider_service_type` (`provider_id`, `service_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3. PRICE OVERRIDES TABLE
-- User-type-specific pricing overrides (vip, premium, enterprise)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `price_overrides` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT NOT NULL,
    `service_type` VARCHAR(100) NOT NULL,
    `user_type` ENUM('user','agent','vendor','vip','premium','enterprise') NOT NULL DEFAULT 'user',
    `override_fee` DECIMAL(10,2) NOT NULL COMMENT 'Custom price for this user type',
    `is_active` TINYINT(1) DEFAULT 1,
    `start_date` DATE DEFAULT '1970-01-01',
    `end_date` DATE DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE,
    INDEX `idx_override_lookup` (`provider_id`, `service_type`, `user_type`, `is_active`),
    UNIQUE KEY `uk_override_unique` (`provider_id`, `service_type`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. PROVIDER LOGS TABLE (referenced by server.php)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `request_data` JSON,
    `response` JSON,
    `status` VARCHAR(50) DEFAULT NULL,
    `user_id` INT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE,
    INDEX `idx_provider_action` (`provider_id`, `action`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 5. PROVIDER PERFORMANCE METRICS TABLE
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provider_performance_metrics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `total_requests` INT DEFAULT 0,
    `successful_requests` INT DEFAULT 0,
    `failed_requests` INT DEFAULT 0,
    `avg_response_time_ms` DECIMAL(10,2) DEFAULT 0.00,
    `total_revenue` DECIMAL(12,2) DEFAULT 0.00,
    `total_cost` DECIMAL(12,2) DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_provider_date` (`provider_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED PROVIDER DATA
-- ============================================================

-- -----------------------------------------------------------
-- DataVerify Provider (NIN, BVN, Bank Account verification)
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`, `config`) VALUES
('DataVerify', 'dataverify', 'DataVerify ID Service', 'NIN, BVN, Bank Account, and IPE verification services', 100, 1,
 'verify_nin,verify_bvn,verify_bank_account,lookup_ipe,generate_nin_slip,generate_bvn_slip,advanced_bvn,verify_customer,verify_phone',
 '{
   "slip_types": {
     "generate_nin_slip": {"min_price": 300, "max_price": 50000, "description": "NIN Slip Generation"},
     "generate_bvn_slip": {"min_price": 500, "max_price": 50000, "description": "BVN Slip Generation"},
     "lookup_ipe": {"min_price": 200, "max_price": 20000, "description": "IPE Lookup"}
   },
   "endpoints": {
     "verify_nin": "/api/v1/nin/verify",
     "verify_bvn": "/api/v1/bvn/verify",
     "verify_bank_account": "/api/v1/bank/verify",
     "generate_nin_slip": "/api/v1/nin/slip",
     "generate_bvn_slip": "/api/v1/bvn/slip"
   }
 }');

-- -----------------------------------------------------------
-- Airtime Providers (one per network, using existing API config)
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('MTN Airtime', 'airtime', 'MTN VTU Airtime', 'MTN VTU airtime topup and Share & Sell', 90, 1, 'vtu,share_and_sell'),
('GLO Airtime', 'airtime', 'GLO VTU Airtime', 'GLO VTU airtime topup and Share & Sell', 85, 1, 'vtu,share_and_sell'),
('9MOBILE Airtime', 'airtime', '9MOBILE VTU Airtime', '9MOBILE VTU airtime topup and Share & Sell', 80, 1, 'vtu,share_and_sell'),
('AIRTEL Airtime', 'airtime', 'AIRTEL VTU Airtime', 'AIRTEL VTU airtime topup and Share & Sell', 85, 1, 'vtu,share_and_sell');

-- -----------------------------------------------------------
-- Data Providers (one per network)
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('MTN Data', 'data', 'MTN Data Plans', 'MTN SME, Gifting, Corporate, and Awoof data plans', 90, 1, 'sme,gifting,corporate,awoof,coupon,datapin'),
('GLO Data', 'data', 'GLO Data Plans', 'GLO SME, Gifting, and Corporate data plans', 80, 1, 'sme,gifting,corporate,datapin'),
('9MOBILE Data', 'data', '9MOBILE Data Plans', '9MOBILE SME, Gifting, and Corporate data plans', 75, 1, 'sme,gifting,corporate,datapin'),
('AIRTEL Data', 'data', 'AIRTEL Data Plans', 'AIRTEL SME, Gifting, and Corporate data plans', 85, 1, 'sme,gifting,corporate,datapin');

-- -----------------------------------------------------------
-- Cable TV Providers
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('GOtv', 'cabletv', 'GOtv Subscription', 'GOtv Max, Jolli, Jinja, Smallie, Supa plans', 70, 1, 'validate,subscribe'),
('DSTv', 'cabletv', 'DSTv Subscription', 'DSTv Compact, Yanga, Premium, Confam plans', 70, 1, 'validate,subscribe'),
('STARTIMES', 'cabletv', 'STARTIMES Subscription', 'STARTIMES Nova, Basic, Smart, Classic, Super plans', 65, 1, 'validate,subscribe');

-- -----------------------------------------------------------
-- Electricity Providers
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('Electricity', 'electricity', 'Electricity Bill Payment', 'Prepaid and postpaid electricity bill payment for all discos', 60, 1, 'validate_meter,pay_bill');

-- -----------------------------------------------------------
-- Exam Providers
-- -----------------------------------------------------------
INSERT INTO `providers` (`name`, `type`, `display_name`, `description`, `priority`, `is_active`, `supported_actions`) VALUES
('Exam PIN', 'exam', 'Exam PIN Purchase', 'WAEC, NECO, and NABTEB exam PIN purchases', 50, 1, 'purchase,verify');

-- ============================================================
-- SEED PROVIDER PRICING
-- ============================================================

-- -----------------------------------------------------------
-- DataVerify Pricing
-- Uses values from sitesettings (kycNinCharges=30, kycBvnCharges=5)
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

-- -----------------------------------------------------------
-- Airtime Pricing (percentage-based from airtime table)
-- Network IDs: 1=MTN, 2=GLO, 3=9MOBILE, 4=AIRTEL
-- Providers: 2=MTN Airtime, 3=GLO Airtime, 4=9MOBILE, 5=AIRTEL
-- -----------------------------------------------------------

-- MTN VTU (provider_id=2, network=1, aBuyDiscount=97, aUserDiscount=99)
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(2, 'mtn_vtu_1', 99.00, 97.00, 1, 1, 'instant', 1),
(2, 'mtn_sharesell_1', 99.00, 98.00, 1, 1, 'instant', 1);

-- GLO VTU (provider_id=3, network=2, aBuyDiscount=97, aUserDiscount=99)
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(3, 'glo_vtu_2', 99.00, 93.00, 1, 2, 'instant', 1),
(3, 'glo_sharesell_2', 99.00, 98.00, 1, 2, 'instant', 1);

-- 9MOBILE VTU (provider_id=4, network=3, aBuyDiscount=99, aUserDiscount=99)
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(4, '9mobile_vtu_3', 99.00, 99.00, 1, 3, 'instant', 1),
(4, '9mobile_sharesell_3', 99.00, 98.00, 1, 3, 'instant', 1);

-- AIRTEL VTU (provider_id=5, network=4, aBuyDiscount=97, aUserDiscount=99)
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `is_percentage`, `network_id`, `plan_duration`, `is_active`) VALUES
(5, 'airtel_vtu_4', 99.00, 97.00, 1, 4, 'instant', 1),
(5, 'airtel_sharesell_4', 99.00, 98.00, 1, 4, 'instant', 1);

-- -----------------------------------------------------------
-- Data Plan Pricing (from dataplans table)
-- Migrates userprice as base_fee, price as cost_price
-- Providers: 6=MTN Data, 7=GLO Data, 8=9MOBILE Data, 9=AIRTEL Data
-- -----------------------------------------------------------
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `plan_name`, `plan_id`, `plan_duration`, `network_id`, `is_active`)
SELECT
    CASE dp.`datanetwork`
        WHEN 1 THEN 6   -- MTN
        WHEN 2 THEN 7   -- GLO
        WHEN 3 THEN 8   -- 9MOBILE
        WHEN 4 THEN 9   -- AIRTEL
    END AS `provider_id`,
    CONCAT(
        CASE dp.`datanetwork`
            WHEN 1 THEN 'mtn'
            WHEN 2 THEN 'glo'
            WHEN 3 THEN '9mobile'
            WHEN 4 THEN 'airtel'
        END,
        '_',
        LOWER(REPLACE(REPLACE(dp.`type`, ' ', '_'), '-', '_')),
        '_',
        dp.`planid`
    ) AS `service_type`,
    CAST(dp.`userprice` AS DECIMAL(10,2)) AS `base_fee`,
    CAST(dp.`price` AS DECIMAL(10,2)) AS `cost_price`,
    dp.`name` AS `plan_name`,
    dp.`planid` AS `plan_id`,
    CONCAT(dp.`day`, ' days') AS `plan_duration`,
    dp.`datanetwork` AS `network_id`,
    1 AS `is_active`
FROM `dataplans` dp
WHERE CAST(dp.`userprice` AS DECIMAL(10,2)) > 0;

-- -----------------------------------------------------------
-- Data PIN Pricing (from datapins table)
-- -----------------------------------------------------------
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `plan_name`, `plan_id`, `plan_duration`, `network_id`, `is_active`)
SELECT
    CASE dp.`datanetwork`
        WHEN 1 THEN 6   -- MTN
        WHEN 2 THEN 7   -- GLO
        WHEN 3 THEN 8   -- 9MOBILE
        WHEN 4 THEN 9   -- AIRTEL
    END AS `provider_id`,
    CONCAT(
        CASE dp.`datanetwork`
            WHEN 1 THEN 'mtn'
            WHEN 2 THEN 'glo'
            WHEN 3 THEN '9mobile'
            WHEN 4 THEN 'airtel'
        END,
        '_datapin_',
        dp.`planid`
    ) AS `service_type`,
    CAST(dp.`userprice` AS DECIMAL(10,2)) AS `base_fee`,
    CAST(dp.`price` AS DECIMAL(10,2)) AS `cost_price`,
    dp.`name` AS `plan_name`,
    dp.`planid` AS `plan_id`,
    CONCAT(dp.`day`, ' days') AS `plan_duration`,
    dp.`datanetwork` AS `network_id`,
    1 AS `is_active`
FROM `datapins` dp
WHERE CAST(dp.`userprice` AS DECIMAL(10,2)) > 0;

-- -----------------------------------------------------------
-- Cable TV Pricing (from cableplans table)
-- Providers: 10=GOtv, 11=DSTv, 12=STARTIMES
-- -----------------------------------------------------------
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `plan_name`, `plan_id`, `plan_duration`, `network_id`, `is_active`)
SELECT
    CASE cp.`cableprovider`
        WHEN 1 THEN 10  -- GOtv
        WHEN 2 THEN 11  -- DSTv
        WHEN 3 THEN 12  -- STARTIMES
    END AS `provider_id`,
    CONCAT(
        CASE cp.`cableprovider`
            WHEN 1 THEN 'gotv'
            WHEN 2 THEN 'dstv'
            WHEN 3 THEN 'startimes'
        END,
        '_',
        LOWER(REPLACE(REPLACE(cp.`name`, ' ', '_'), '-', '_')),
        '_',
        cp.`planid`
    ) AS `service_type`,
    CAST(cp.`userprice` AS DECIMAL(10,2)) AS `base_fee`,
    CAST(cp.`price` AS DECIMAL(10,2)) AS `cost_price`,
    cp.`name` AS `plan_name`,
    cp.`planid` AS `plan_id`,
    CONCAT(cp.`day`, ' days') AS `plan_duration`,
    cp.`cableprovider` AS `network_id`,
    1 AS `is_active`
FROM `cableplans` cp
WHERE CAST(cp.`userprice` AS DECIMAL(10,2)) > 0;

-- -----------------------------------------------------------
-- Exam PIN Pricing (from examid table)
-- Provider: 13=Exam PIN
-- -----------------------------------------------------------
INSERT INTO `provider_pricing` (`provider_id`, `service_type`, `base_fee`, `cost_price`, `plan_name`, `plan_id`, `network_id`, `is_active`)
SELECT
    13 AS `provider_id`,
    CONCAT(LOWER(REPLACE(e.`provider`, ' ', '_')), '_pin') AS `service_type`,
    CAST(e.`price` AS DECIMAL(10,2)) AS `base_fee`,
    CAST(e.`buying_price` AS DECIMAL(10,2)) AS `cost_price`,
    e.`provider` AS `plan_name`,
    e.`examid` AS `plan_id`,
    e.`eId` AS `network_id`,
    1 AS `is_active`
FROM `examid` e
WHERE CAST(e.`price` AS DECIMAL(10,2)) > 0;

-- ============================================================
-- SEED PRICE OVERRIDES
-- Maps existing user/agent/vendor prices from dataplans
-- ============================================================

-- Agent price overrides from dataplans
INSERT INTO `price_overrides` (`provider_id`, `service_type`, `user_type`, `override_fee`, `is_active`)
SELECT
    CASE dp.`datanetwork`
        WHEN 1 THEN 6
        WHEN 2 THEN 7
        WHEN 3 THEN 8
        WHEN 4 THEN 9
    END AS `provider_id`,
    CONCAT(
        CASE dp.`datanetwork`
            WHEN 1 THEN 'mtn'
            WHEN 2 THEN 'glo'
            WHEN 3 THEN '9mobile'
            WHEN 4 THEN 'airtel'
        END,
        '_',
        LOWER(REPLACE(REPLACE(dp.`type`, ' ', '_'), '-', '_')),
        '_',
        dp.`planid`
    ) AS `service_type`,
    'agent' AS `user_type`,
    CAST(dp.`agentprice` AS DECIMAL(10,2)) AS `override_fee`,
    1 AS `is_active`
FROM `dataplans` dp
WHERE CAST(dp.`agentprice` AS DECIMAL(10,2)) > 0
  AND CAST(dp.`agentprice` AS DECIMAL(10,2)) != CAST(dp.`userprice` AS DECIMAL(10,2));

-- Vendor price overrides from dataplans
INSERT INTO `price_overrides` (`provider_id`, `service_type`, `user_type`, `override_fee`, `is_active`)
SELECT
    CASE dp.`datanetwork`
        WHEN 1 THEN 6
        WHEN 2 THEN 7
        WHEN 3 THEN 8
        WHEN 4 THEN 9
    END AS `provider_id`,
    CONCAT(
        CASE dp.`datanetwork`
            WHEN 1 THEN 'mtn'
            WHEN 2 THEN 'glo'
            WHEN 3 THEN '9mobile'
            WHEN 4 THEN 'airtel'
        END,
        '_',
        LOWER(REPLACE(REPLACE(dp.`type`, ' ', '_'), '-', '_')),
        '_',
        dp.`planid`
    ) AS `service_type`,
    'vendor' AS `user_type`,
    CAST(dp.`vendorprice` AS DECIMAL(10,2)) AS `override_fee`,
    1 AS `is_active`
FROM `dataplans` dp
WHERE CAST(dp.`vendorprice` AS DECIMAL(10,2)) > 0
  AND CAST(dp.`vendorprice` AS DECIMAL(10,2)) != CAST(dp.`userprice` AS DECIMAL(10,2));

-- DataVerify VIP/Premium overrides (example - admin to adjust)
INSERT INTO `price_overrides` (`provider_id`, `service_type`, `user_type`, `override_fee`, `is_active`) VALUES
(1, 'verify_nin', 'enterprise', 25.00, 1),
(1, 'verify_bvn', 'enterprise', 4.00, 1),
(1, 'generate_nin_slip', 'premium', 450.00, 1),
(1, 'generate_nin_slip', 'enterprise', 400.00, 1),
(1, 'generate_bvn_slip', 'premium', 750.00, 1),
(1, 'generate_bvn_slip', 'enterprise', 700.00, 1);

-- ============================================================
-- VIEW: provider_pricing_with_overrides
-- Combines provider pricing with active user-type overrides
-- Fixed to avoid MySQL #1054 by using direct table references
-- ============================================================

CREATE OR REPLACE VIEW `provider_pricing_with_overrides` AS
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
    -- Use override fee if available, otherwise use base fee
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
    WHERE po1.is_active = TRUE
      AND po1.start_date <= CURRENT_DATE
      AND (po1.end_date IS NULL OR po1.end_date >= CURRENT_DATE)
    GROUP BY po1.provider_id, po1.service_type
) po ON po.provider_id = pp.provider_id AND po.service_type = pp.service_type
WHERE pp.is_active = TRUE
ORDER BY p.priority DESC, p.name, pp.service_type;

-- ============================================================
-- VIEW: user_pricing
-- Returns pricing for a specific user type
-- application-level filter on price_overrides.user_type
-- ============================================================

CREATE OR REPLACE VIEW `user_pricing` AS
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
    AND po_u.is_active = TRUE
LEFT JOIN price_overrides po_a ON po_a.provider_id = pp.provider_id
    AND po_a.service_type = pp.service_type
    AND po_a.user_type = 'agent'
    AND po_a.is_active = TRUE
LEFT JOIN price_overrides po_v ON po_v.provider_id = pp.provider_id
    AND po_v.service_type = pp.service_type
    AND po_v.user_type = 'vendor'
    AND po_v.is_active = TRUE
WHERE pp.is_active = TRUE
ORDER BY p.priority DESC, p.name, pp.service_type;

-- ============================================================
-- TRIGGERS: Auto-log provider changes
-- ============================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `trg_providers_after_insert`
AFTER INSERT ON `providers`
FOR EACH ROW
BEGIN
    INSERT INTO `provider_logs` (`provider_id`, `action`, `request_data`, `status`, `created_at`)
    VALUES (NEW.id, 'provider_created',
        JSON_OBJECT('name', NEW.name, 'type', NEW.type, 'is_active', NEW.is_active),
        'success', NOW());
END//

CREATE TRIGGER IF NOT EXISTS `trg_providers_after_update`
AFTER UPDATE ON `providers`
FOR EACH ROW
BEGIN
    IF OLD.is_active != NEW.is_active OR OLD.name != NEW.name OR OLD.priority != NEW.priority THEN
        INSERT INTO `provider_logs` (`provider_id`, `action`, `request_data`, `status`, `created_at`)
        VALUES (NEW.id, 'provider_updated',
            JSON_OBJECT('name', NEW.name, 'type', NEW.type, 'is_active', NEW.is_active,
                        'priority', NEW.priority),
            'success', NOW());
    END IF;
END//

CREATE TRIGGER IF NOT EXISTS `trg_provider_pricing_after_insert`
AFTER INSERT ON `provider_pricing`
FOR EACH ROW
BEGIN
    INSERT INTO `provider_logs` (`provider_id`, `action`, `request_data`, `status`, `created_at`)
    VALUES (NEW.provider_id, 'pricing_created',
        JSON_OBJECT('service_type', NEW.service_type, 'base_fee', NEW.base_fee),
        'success', NOW());
END//

CREATE TRIGGER IF NOT EXISTS `trg_provider_pricing_after_update`
AFTER UPDATE ON `provider_pricing`
FOR EACH ROW
BEGIN
    IF OLD.base_fee != NEW.base_fee OR OLD.is_active != NEW.is_active THEN
        INSERT INTO `provider_logs` (`provider_id`, `action`, `request_data`, `status`, `created_at`)
        VALUES (NEW.provider_id, 'pricing_updated',
            JSON_OBJECT('service_type', NEW.service_type, 'old_base_fee', OLD.base_fee,
                        'new_base_fee', NEW.base_fee, 'is_active', NEW.is_active),
            'success', NOW());
    END IF;
END//

DELIMITER ;

-- ============================================================
-- INDEXES for performance
-- ============================================================

-- Already created inline with table definitions above
-- Additional cross-table indexes
CREATE INDEX IF NOT EXISTS `idx_provider_pricing_lookup` ON `provider_pricing` (`provider_id`, `is_active`, `network_id`);
CREATE INDEX IF NOT EXISTS `idx_price_overrides_active` ON `price_overrides` (`provider_id`, `service_type`, `is_active`, `user_type`);
CREATE INDEX IF NOT EXISTS `idx_provider_logs_date` ON `provider_logs` (`created_at`, `provider_id`);
