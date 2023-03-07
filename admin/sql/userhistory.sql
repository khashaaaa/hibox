CREATE TABLE IF NOT EXISTS `userhistory` (
  `user` text NOT NULL,
  `id` text NOT NULL,
  `name` text NOT NULL,
  `price` text NOT NULL,
  `promo_price` text NOT NULL,
  `pic` text NOT NULL,
  `tme` int(11) NOT NULL,
  KEY idx_user (`user`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
