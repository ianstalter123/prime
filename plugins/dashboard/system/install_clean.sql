DROP TABLE IF EXISTS `plugin_dashboard_theme`;
CREATE TABLE IF NOT EXISTS `plugin_dashboard_theme` (
	`name` VARCHAR(255) NOT NULL,
	`value` TEXT,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

UPDATE `plugin` SET `tags`='ecommerce' WHERE `name` = 'dashboard';