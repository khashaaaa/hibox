CREATE TABLE IF NOT EXISTS `set2categories` (
  `ContentType` text NOT NULL,
  `itemRatingTypeId` text NOT NULL,
  `categories` text NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;