CREATE TABLE IF NOT EXISTS `digest` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `category_id` varchar(50),
    `alias` varchar(255) NOT NULL,
    `brief` text,
    `content` longtext,
    `image` varchar(255),
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
