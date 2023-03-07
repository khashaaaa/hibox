CREATE TABLE `my_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `my_category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `properties` text,
  PRIMARY KEY (`id`),
  KEY `idx_my_category_id` (`my_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;