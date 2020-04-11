CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fansub_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `session_id` varchar(200) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fansub_id` (`fansub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(200) NOT NULL,
  `entity` varchar(200) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `episode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `episode_title` (
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`version_id`,`episode_id`),
  KEY `episode_title_ibfk_1` (`episode_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fansub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `folder` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `myanimelist_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `url` varchar(200) NOT NULL,
  `resolution` varchar(200) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `episode_id` (`episode_id`) USING BTREE,
  KEY `version_id` (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rel_series_genre` (
  `series_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  PRIMARY KEY (`series_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rel_version_fansub` (
  `version_id` int(11) NOT NULL,
  `fansub_id` int(11) NOT NULL,
  PRIMARY KEY (`version_id`,`fansub_id`),
  KEY `rel_version_fansub_ibfk_2` (`fansub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alternate_names` varchar(200) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'movie',
  `air_date` timestamp NULL DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `director` varchar(200) DEFAULT NULL,
  `studio` varchar(200) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `episodes` int(11) NOT NULL,
  `synopsis` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `myanimelist_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `admin_level` int(11) NOT NULL,
  `fansub_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `fansub_id` (`fansub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `default_resolution` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `views` (
  `link_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`link_id`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode`
  ADD CONSTRAINT `episode_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`);
ALTER TABLE `episode_title`
  ADD CONSTRAINT `episode_title_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `episode_title_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`);
ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `link_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_series_genre`
  ADD CONSTRAINT `rel_series_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `rel_series_genre_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`);
ALTER TABLE `rel_version_fansub`
  ADD CONSTRAINT `rel_version_fansub_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_version_fansub_ibfk_2` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`);
ALTER TABLE `version`
  ADD CONSTRAINT `version_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`);
COMMIT;

