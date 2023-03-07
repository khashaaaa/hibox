CREATE TABLE `countries_for_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `param_step` int(11) NOT NULL DEFAULT '0',
  `param_start` float(9,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`delivery_id`,`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;