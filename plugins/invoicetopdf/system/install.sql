CREATE TABLE IF NOT EXISTS `plugin_invoicetopdf_settings` (
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`value` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `template_type` (`id`, `title`) VALUES
('typeinvoice', 'Invoices');

UPDATE `plugin` SET `tags`='ecommerce' WHERE `name` = 'invoicetopdf';