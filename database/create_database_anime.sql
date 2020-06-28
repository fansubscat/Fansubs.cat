CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `fansub_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `session_id` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `action_log` (
  `id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `episode` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `episode_title` (
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `fansub` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `twitter_url` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `folder` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `folder` varchar(200) NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `folder_failed_files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `file_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `genre` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `myanimelist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `link` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `resolution` varchar(200) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_series` (
  `series_id` int(11) NOT NULL,
  `related_series_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rel_series_genre` (
  `series_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rel_version_fansub` (
  `version_id` int(11) NOT NULL,
  `fansub_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `search_history` (
  `query` varchar(200) NOT NULL,
  `day` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `season` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `episodes` int(11) DEFAULT NULL,
  `myanimelist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alternate_names` varchar(200) DEFAULT NULL,
  `keywords` varchar(200) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'movie',
  `air_date` timestamp NULL DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `director` varchar(200) DEFAULT NULL,
  `studio` varchar(200) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `episodes` int(11) NOT NULL,
  `synopsis` text NOT NULL,
  `duration` varchar(200) DEFAULT NULL,
  `image` varchar(200) NOT NULL,
  `myanimelist_id` int(11) DEFAULT NULL,
  `tadaima_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `show_seasons` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_seasons` tinyint(1) NOT NULL DEFAULT 1,
  `show_episode_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `show_unavailable_episodes` tinyint(1) NOT NULL DEFAULT 1,
  `has_licensed_parts` tinyint(1) NOT NULL DEFAULT 0,
  `order_type` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `admin_level` int(11) NOT NULL,
  `fansub_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `version` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `default_resolution` varchar(200) DEFAULT NULL,
  `downloads_url` varchar(200) DEFAULT NULL,
  `episodes_missing` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  `links_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `links_updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `views` (
  `link_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `time_spent` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_ibfk_1` (`fansub_id`);
ALTER TABLE `action_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `episode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `episode_ibfk_1` (`series_id`);
ALTER TABLE `episode_title`
  ADD PRIMARY KEY (`version_id`,`episode_id`),
  ADD KEY `episode_title_ibfk_1` (`episode_id`) USING BTREE;
ALTER TABLE `fansub`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `folder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_ibfk_1` (`account_id`),
  ADD KEY `folder_ibfk_2` (`version_id`),
  ADD KEY `folder_ibfk_3` (`season_id`) USING BTREE;
ALTER TABLE `folder_failed_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_failed_files_ibfk_1` (`folder_id`);
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_ibfk_1` (`episode_id`) USING BTREE,
  ADD KEY `link_ibfk_2` (`version_id`) USING BTREE;
ALTER TABLE `related_series`
  ADD PRIMARY KEY (`series_id`,`related_series_id`),
  ADD KEY `related_series_id` (`related_series_id`);
ALTER TABLE `rel_series_genre`
  ADD PRIMARY KEY (`series_id`,`genre_id`),
  ADD KEY `rel_series_genre_ibfk_1` (`genre_id`);
ALTER TABLE `rel_version_fansub`
  ADD PRIMARY KEY (`version_id`,`fansub_id`),
  ADD KEY `rel_version_fansub_ibfk_1` (`fansub_id`);
ALTER TABLE `season`
  ADD PRIMARY KEY (`id`),
  ADD KEY `season_ibfk_1` (`series_id`) USING BTREE;
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD KEY `user_ibfk_1` (`fansub_id`);
ALTER TABLE `version`
  ADD PRIMARY KEY (`id`),
  ADD KEY `version_ibfk_1` (`series_id`);
ALTER TABLE `views`
  ADD PRIMARY KEY (`link_id`,`day`);
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `action_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `episode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fansub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `folder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `folder_failed_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode`
  ADD CONSTRAINT `episode_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode_title`
  ADD CONSTRAINT `episode_title_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `episode_title_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `folder`
  ADD CONSTRAINT `folder_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `folder_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `folder_ibfk_3` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `folder_failed_files`
  ADD CONSTRAINT `folder_failed_files_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `link_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_series`
  ADD CONSTRAINT `related_series_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_series_ibfk_2` FOREIGN KEY (`related_series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_series_genre`
  ADD CONSTRAINT `rel_series_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `rel_series_genre_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_version_fansub`
  ADD CONSTRAINT `rel_version_fansub_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_version_fansub_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `season`
  ADD CONSTRAINT `season_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `version`
  ADD CONSTRAINT `version_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `views`
  ADD CONSTRAINT `views_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

