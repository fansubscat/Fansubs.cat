CREATE TABLE IF NOT EXISTS `fansubs` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `favicon_image` varchar(255) DEFAULT NULL,
  `ping_token` varchar(255) DEFAULT NULL,
  `is_historical` int(11) NOT NULL DEFAULT '0',
  `is_visible` int(11) NOT NULL DEFAULT '1',
  `is_own` int(11) NOT NULL DEFAULT '0',
  `archive_url` text,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `fetchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fansub_id` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `method` varchar(255) NOT NULL,
  `fetch_type` varchar(255) NOT NULL DEFAULT 'periodic',
  `status` varchar(255) NOT NULL DEFAULT 'idle',
  `last_fetch_result` varchar(255) DEFAULT NULL,
  `last_fetch_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `news` (
  `fansub_id` varchar(255) DEFAULT NULL,
  `fetcher_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `original_contents` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` text,
  `image` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pending_news` (
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `url` text NOT NULL,
  `image_url` text,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `fetchers` ADD KEY `fk_fetchers_fansubs` (`fansub_id`);
ALTER TABLE `news` ADD KEY `fk_news_fansubs` (`fansub_id`);
ALTER TABLE `news` ADD KEY `fk_news_fetchers` (`fetcher_id`);

ALTER TABLE `fetchers` ADD CONSTRAINT `fk_fetchers_fansubs` FOREIGN KEY (`fansub_id`) REFERENCES `fansubs` (`id`) ON UPDATE CASCADE;
ALTER TABLE `news` ADD CONSTRAINT `fk_news_fansubs` FOREIGN KEY (`fansub_id`) REFERENCES `fansubs` (`id`) ON UPDATE CASCADE;
ALTER TABLE `news` ADD CONSTRAINT `fk_news_fetchers` FOREIGN KEY (`fetcher_id`) REFERENCES `fetchers` (`id`) ON UPDATE CASCADE;

