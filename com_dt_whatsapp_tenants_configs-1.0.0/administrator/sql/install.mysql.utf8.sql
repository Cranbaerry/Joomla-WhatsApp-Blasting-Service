CREATE TABLE IF NOT EXISTS `#__dt_whatsapp_tenants_configs` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`callback_url` VARCHAR(255)  NOT NULL ,
`forward_url` VARCHAR(255)  NOT NULL ,
`app_id` VARCHAR(255)  NOT NULL ,
`phone_number_id` VARCHAR(255)  NOT NULL ,
`business_account_id` VARCHAR(255)  NOT NULL ,
`token` VARCHAR(255)  NOT NULL ,
`phone_number` VARCHAR(255)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`dreamztrack_endpoint` VARCHAR(255)  NOT NULL  DEFAULT "PRODUCTION",
`dreamztrack_branch` VARCHAR(255)  NOT NULL  DEFAULT "HQ Branch",
`dreamztrack_key` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__dt_whatsapp_tenants_configs_callback_url` ON `#__dt_whatsapp_tenants_configs`(`callback_url`);

CREATE INDEX `#__dt_whatsapp_tenants_configs_forward_url` ON `#__dt_whatsapp_tenants_configs`(`forward_url`);

CREATE INDEX `#__dt_whatsapp_tenants_configs_business_account_id` ON `#__dt_whatsapp_tenants_configs`(`business_account_id`);

CREATE INDEX `#__dt_whatsapp_tenants_configs_token` ON `#__dt_whatsapp_tenants_configs`(`token`);

CREATE INDEX `#__dt_whatsapp_tenants_configs_phone_number` ON `#__dt_whatsapp_tenants_configs`(`phone_number`);

CREATE INDEX `#__dt_whatsapp_tenants_configs_user_id` ON `#__dt_whatsapp_tenants_configs`(`user_id`);

