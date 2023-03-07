CREATE TABLE `site_referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(128) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `awaiting_money` double(15,3) NOT NULL DEFAULT '0.000',
  `balance` double(15,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY idx_parent_id (`parent_id`),
  KEY idx_user_id (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;