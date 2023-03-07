CREATE TABLE IF NOT EXISTS `site_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `langid` int(11) NOT NULL,
  `key` int(11) NOT NULL,
  `translation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key_lang` (`key`,`langid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=830;