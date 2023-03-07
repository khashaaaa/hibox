CREATE TABLE IF NOT EXISTS `site_pages_parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `menu_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX idx_page_id (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;