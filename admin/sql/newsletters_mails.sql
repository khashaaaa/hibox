CREATE TABLE IF NOT EXISTS `newsletters_mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletterId` int(11) NOT NULL,
  `subscriberId` int(11) NOT NULL,
  `sent` datetime NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  INDEX `idx_newsletter_id`(`newsletterId`),
  INDEX `idx_subscriber_id`(`subscriberId`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`newsletterId`) REFERENCES newsletters(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subscriberId`) REFERENCES newsletters_subscribers(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
