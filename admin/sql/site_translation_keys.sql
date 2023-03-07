CREATE TABLE IF NOT EXISTS `site_translation_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY idx_unique_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;