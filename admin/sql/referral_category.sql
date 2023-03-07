CREATE TABLE IF NOT EXISTS `referral_category` (
  `groupName` varchar(255) NOT NULL,
  `catId` int(11) NOT NULL AUTO_INCREMENT,
  `minOverallPayment` double NOT NULL,
  `profitPercent` double NOT NULL,
  PRIMARY KEY (`catId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
