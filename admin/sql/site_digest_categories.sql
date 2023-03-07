CREATE TABLE IF NOT EXISTS `site_digest_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `lang_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;