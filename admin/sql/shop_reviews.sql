CREATE TABLE IF NOT EXISTS `shop_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `text` text,
  `answer` text NOT NULL,
  `accepted` tinyint(4) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` int(11),
  `answer_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;