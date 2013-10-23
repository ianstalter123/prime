DROP TABLE IF EXISTS `plugin_newslog_configuration`;
CREATE TABLE `plugin_newslog_configuration` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/* Insertion default values */
INSERT INTO `plugin_newslog_configuration` (`name`, `value`) VALUES ('folder', 'news');
INSERT INTO `page_option` (`id`, `title`, `context`, `active`) VALUES ('option_newsindex', 'News index page', 'News system', 1);
INSERT INTO `page_option` (`id`, `title`, `context`, `active`) VALUES ('option_newspage', 'News page', 'News system', 1);
INSERT INTO `observers_queue` (`observable`, `observer`) VALUES ('Application_Model_Models_Page', 'Newslog_Tools_Watchdog_Page');
/* plugin_newslog_news table */
DROP TABLE IF EXISTS `plugin_newslog_news`;
CREATE TABLE `plugin_newslog_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned DEFAULT NULL,
  `metaData` text COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `teaser` text COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci,
  `broadcast` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('internal','external') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'internal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `external_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`),
  KEY `type` (`type`),
  KEY `external_id` (`external_id`),
  KEY `index_created_ad` (`created_at`),
  KEY `index_id_created_at` (`id`,`created_at`),
  CONSTRAINT `plugin_newslog_news_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Local author id';
/* plugin_newslog_news_has_tag table */
DROP TABLE IF EXISTS `plugin_newslog_news_has_tag`;
CREATE TABLE IF NOT EXISTS `plugin_newslog_news_has_tag` (
  `news_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`news_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/* plugin_newslog_pingservices table */
DROP TABLE IF EXISTS `plugin_newslog_pingservice`;
CREATE TABLE `plugin_newslog_pingservice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('enabled','disabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disabled',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `default` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `plugin_newslog_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
ALTER TABLE `plugin_newslog_news_has_tag` ADD CONSTRAINT `plugin_newslog_news_has_tag_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `plugin_newslog_news` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_newslog_news_has_tag` ADD CONSTRAINT `plugin_newslog_news_has_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `plugin_newslog_tag` (`id`) ON DELETE CASCADE;
/* inserting default services */
INSERT INTO `plugin_newslog_pingservice` (`id`, `url`, `status`, `is_default`) VALUES (3, 'http://rpc.weblogs.com/RPC2', 'enabled', 1), (4, 'http://ping.blo.gs/', 'enabled', 1), (5, 'http://rpc.pingomatic.com/RPC2', 'enabled', 1);
/* inserting new template type */
INSERT INTO `template_type` (`id`, `title`) VALUES ('type_news', 'News');


