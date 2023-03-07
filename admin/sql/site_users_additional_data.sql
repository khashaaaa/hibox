CREATE TABLE IF NOT EXISTS `site_users_additional_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `attribute_title` varchar(255) NOT NULL,
  `attribute_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY idx_userid (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;