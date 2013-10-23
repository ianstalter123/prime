CREATE TABLE IF NOT EXISTS `plugin_netcontent_widget` (
  `widget_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8_unicode_ci,
  `publish` tinyint(1) DEFAULT NULL,
  `p2p` tinyint(1) NOT NULL DEFAULT '0',
  `modify_date` date DEFAULT NULL,
  PRIMARY KEY (`widget_name`,`p2p`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;