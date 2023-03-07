-- release 1.1.5
ALTER table `reviews` change `review_id` `review_id` int(11) unsigned not null AUTO_INCREMENT;
ALTER table `reviews` change `item_id` `item_id` varchar(30) not null;
ALTER table `reviews` change `category_id` `category_id` varchar(50) not null;
ALTER table `reviews` change `name` `name` varchar(150) not null default '';
ALTER table `reviews` change `email` `email` varchar(100) not null default '';
ALTER table `reviews` change `accepted` `accepted` tinyint(1) not null default 0;
ALTER table `reviews` ADD KEY idx_item_category (`item_id`, `category_id`);
ALTER table `userhistory` ADD `promo_price` varchar(20) not null default '0';

ALTER table `referral_users` add `added` int(11) default null;
ALTER table `pages` ADD `is_service` tinyint(1) DEFAULT '0';

ALTER table `site_support` ADD `userlogin` varchar(100) NOT NULL DEFAULT '';

-- release 1.2.2
UPDATE `pages` SET `is_service` = 1 WHERE `alias` IN ("paymentsuccess", "robo_success", "paymentfail", "robo_fail", "depositsuccess", "depositfail", "site_unavailable");

ALTER table `shop_reviews` ADD `answer_date` timestamp DEFAULT 0;

ALTER table `newsletters` CHANGE COLUMN `content` `content` LONGTEXT;
ALTER table `newsletters_mails` ADD COLUMN `status` VARCHAR(2048) NOT NULL DEFAULT '';
ALTER table `newsletters_mails` ADD INDEX `idx_status` (`status`);

ALTER TABLE `site_vendors_images` ADD COLUMN `lang` TEXT NOT NULL  AFTER `image_path` ;

