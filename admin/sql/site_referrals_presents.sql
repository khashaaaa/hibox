CREATE TABLE `site_referrals_presents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `referral_order_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referral_order_id` (`referral_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;