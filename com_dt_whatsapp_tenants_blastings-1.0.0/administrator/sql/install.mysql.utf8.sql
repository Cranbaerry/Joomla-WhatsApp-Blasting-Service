CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_blastings` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`status` VARCHAR(255)  NULL  DEFAULT "QUEUED",
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`template_id` VARCHAR(255)  NULL  DEFAULT "",
`mode` VARCHAR(255)  NOT NULL  DEFAULT "INSTANT",
`scheduled_time` DATETIME NULL  DEFAULT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_blastings_status` ON `#__dt_whatsapp_tenants_blastings`(`status`);

CREATE INDEX `#__dt_whatsapp_tenants_blastings_template_id` ON `#__dt_whatsapp_tenants_blastings`(`template_id`);

CREATE INDEX `#__dt_whatsapp_tenants_blastings_mode` ON `#__dt_whatsapp_tenants_blastings`(`mode`);

CREATE INDEX `#__dt_whatsapp_tenants_blastings_scheduled_time` ON `#__dt_whatsapp_tenants_blastings`(`scheduled_time`);

CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_contacts` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`name` VARCHAR(255)  NOT NULL ,
`phone_number` VARCHAR(255)  NOT NULL ,
`keywords_tags` TEXT NULL ,
`last_updated` DATETIME NULL  DEFAULT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_contacts_name` ON `#__dt_whatsapp_tenants_contacts`(`name`);

CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_scheduled_messages` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`target_phone_number` VARCHAR(255)  NOT NULL ,
`type` VARCHAR(255)  NULL  DEFAULT "TEMPLATE",
`template_id` VARCHAR(255)  NOT NULL ,
`keyword_id` INT(10)  NULL  DEFAULT 0,
`keyword_message` TEXT NULL ,
`blasting_id` INT(10)  NULL  DEFAULT 0,
`status` VARCHAR(255)  NULL  DEFAULT "",
`raw_response` TEXT NULL ,
`scheduled_time` DATETIME NULL  DEFAULT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_scheduled_messages_template_id` ON `#__dt_whatsapp_tenants_scheduled_messages`(`template_id`);

CREATE INDEX `#__dt_whatsapp_tenants_scheduled_messages_keyword_id` ON `#__dt_whatsapp_tenants_scheduled_messages`(`keyword_id`);

CREATE INDEX `#__dt_whatsapp_tenants_scheduled_messages_blasting_id` ON `#__dt_whatsapp_tenants_scheduled_messages`(`blasting_id`);

CREATE INDEX `#__dt_whatsapp_tenants_scheduled_messages_status` ON `#__dt_whatsapp_tenants_scheduled_messages`(`status`);

CREATE INDEX `#__dt_whatsapp_tenants_scheduled_messages_scheduled_time` ON `#__dt_whatsapp_tenants_scheduled_messages`(`scheduled_time`);

CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_message_history` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`from` VARCHAR(255)  NOT NULL ,
`phone_number_id` VARCHAR(255)  NOT NULL ,
`timestamp` TIMESTAMP NOT NULL ,
`text` TEXT NULL ,
`type` VARCHAR(255)  NOT NULL ,
`media_caption` VARCHAR(255)  NULL  DEFAULT "",
`errors` TEXT NULL ,
`raw_response` TEXT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_message_history_from` ON `#__dt_whatsapp_tenants_message_history`(`from`);

CREATE INDEX `#__dt_whatsapp_tenants_message_history_phone_number_id` ON `#__dt_whatsapp_tenants_message_history`(`phone_number_id`);

CREATE INDEX `#__dt_whatsapp_tenants_message_history_timestamp` ON `#__dt_whatsapp_tenants_message_history`(`timestamp`);

CREATE INDEX `#__dt_whatsapp_tenants_message_history_type` ON `#__dt_whatsapp_tenants_message_history`(`type`);

CREATE INDEX `#__dt_whatsapp_tenants_message_history_media_caption` ON `#__dt_whatsapp_tenants_message_history`(`media_caption`);

CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_keywords` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`created_date` DATETIME NULL  DEFAULT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL  DEFAULT 1,
`scheduled_message_json` TEXT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
,KEY `idx_state` (`state`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_keywords_created_date` ON `#__dt_whatsapp_tenants_keywords`(`created_date`);

CREATE INDEX `#__dt_whatsapp_tenants_keywords_name` ON `#__dt_whatsapp_tenants_keywords`(`name`);

