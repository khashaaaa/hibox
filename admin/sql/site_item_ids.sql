CREATE TABLE IF NOT EXISTS `site_item_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `out_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_out_id` (`out_id`),
  KEY `idx_item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;