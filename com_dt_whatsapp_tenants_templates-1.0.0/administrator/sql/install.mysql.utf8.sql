CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_templates` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`header_media_handle` VARCHAR(255)  NULL  DEFAULT "",
`modified_by` INT(11)  NULL  DEFAULT 0,
`status` VARCHAR(255)  NULL  DEFAULT "",
`template_id` VARCHAR(255)  NULL  DEFAULT "",
`name` VARCHAR(255)  NOT NULL ,
`language` VARCHAR(255)  NOT NULL  DEFAULT "en_US",
`category` VARCHAR(255)  NOT NULL  DEFAULT "MARKETING",
`header_type` VARCHAR(255)  NULL  DEFAULT "TEXT",
`header_text` VARCHAR(255)  NOT NULL ,
`header_media` TEXT NULL ,
`body` TEXT NOT NULL ,
`footer` VARCHAR(255)  NULL  DEFAULT "",
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_templates_template_id` ON `#__dt_whatsapp_tenants_templates`(`template_id`);

CREATE INDEX `#__dt_whatsapp_tenants_templates_name` ON `#__dt_whatsapp_tenants_templates`(`name`);

CREATE INDEX `#__dt_whatsapp_tenants_templates_language` ON `#__dt_whatsapp_tenants_templates`(`language`);

CREATE INDEX `#__dt_whatsapp_tenants_templates_category` ON `#__dt_whatsapp_tenants_templates`(`category`);

