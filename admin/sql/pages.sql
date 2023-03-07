CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_h1` varchar(255) NOT NULL DEFAULT '',
  `is_service` tinyint(1) NOT NULL DEFAULT 0,
  `is_index` int(11) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;