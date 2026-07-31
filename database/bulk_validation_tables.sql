-- -----------------------------------------------------------
-- BULK NIN VALIDATION TABLES
-- -----------------------------------------------------------

-- Bulk Validation Requests (Parent table for batch submissions)
CREATE TABLE IF NOT EXISTS `bulk_nin_validation_requests` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `sId` INTEGER NOT NULL,
    `ref` TEXT NOT NULL UNIQUE,
    `validation_type` TEXT NOT NULL,
    `total_nins` INTEGER NOT NULL,
    `price_per_nin` REAL NOT NULL,
    `total_amount` REAL NOT NULL,
    `status` TEXT DEFAULT 'pending',
    `date_created` TEXT DEFAULT (datetime('now','localtime')),
    `date_processed` TEXT
);

-- Individual NIN Validation Items (Child table for each NIN in a batch)
CREATE TABLE IF NOT EXISTS `bulk_nin_validation_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `batch_id` INTEGER NOT NULL,
    `sId` INTEGER NOT NULL,
    `nin` TEXT NOT NULL,
    `validation_type` TEXT NOT NULL,
    `status` TEXT DEFAULT 'pending',
    `admin_reply` TEXT,
    `result_document` TEXT,
    `date_created` TEXT DEFAULT (datetime('now','localtime')),
    `date_updated` TEXT,
    FOREIGN KEY (`batch_id`) REFERENCES `bulk_nin_validation_requests`(`id`) ON DELETE CASCADE
);

-- IPE Clearance Requests
CREATE TABLE IF NOT EXISTS `ipe_clearance_requests` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `sId` INTEGER NOT NULL,
    `ref` TEXT NOT NULL UNIQUE,
    `tracking_ids` TEXT NOT NULL, -- JSON array of tracking IDs
    `total_tracking_ids` INTEGER NOT NULL,
    `price_per_tracking` REAL NOT NULL,
    `total_amount` REAL NOT NULL,
    `status` TEXT DEFAULT 'pending',
    `date_created` TEXT DEFAULT (datetime('now','localtime')),
    `date_processed` TEXT
);

-- IPE Clearance Items
CREATE TABLE IF NOT EXISTS `ipe_clearance_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `batch_id` INTEGER NOT NULL,
    `sId` INTEGER NOT NULL,
    `tracking_id` TEXT NOT NULL,
    `status` TEXT DEFAULT 'pending',
    `result_data` TEXT,
    `result_document` TEXT,
    `date_created` TEXT DEFAULT (datetime('now','localtime')),
    `date_updated` TEXT,
    FOREIGN KEY (`batch_id`) REFERENCES `ipe_clearance_requests`(`id`) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS `idx_bulk_nin_batch_user` ON `bulk_nin_validation_requests`(`sId`);
CREATE INDEX IF NOT EXISTS `idx_bulk_nin_items_batch` ON `bulk_nin_validation_items`(`batch_id`);
CREATE INDEX IF NOT EXISTS `idx_bulk_nin_items_nin` ON `bulk_nin_validation_items`(`nin`);
CREATE INDEX IF NOT EXISTS `idx_ipe_batch_user` ON `ipe_clearance_requests`(`sId`);
CREATE INDEX IF NOT EXISTS `idx_ipe_items_batch` ON `ipe_clearance_items`(`batch_id`);
CREATE INDEX IF NOT EXISTS `idx_ipe_items_tracking` ON `ipe_clearance_items`(`tracking_id`);

-- Pricing for bulk validation types (add to sitesettings)
-- These will be added via ALTER TABLE in the application