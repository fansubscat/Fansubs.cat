CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `admin_user` (
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `admin_level` int(11) NOT NULL,
  `fansub_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `advent_calendar` (
  `year` int(11) NOT NULL,
  `position` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `advent_day` (
  `year` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `link_url` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `division` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `number_of_episodes` int(11) DEFAULT NULL,
  `external_id` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `episode` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `division_id` int(11) DEFAULT NULL,
  `number` decimal(10,2) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `episode_title` (
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `fansub` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'fansub',
  `slug` varchar(200) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `twitter_url` varchar(200) DEFAULT NULL,
  `twitter_handle` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `ping_token` varchar(200) DEFAULT NULL,
  `is_historical` tinyint(1) NOT NULL DEFAULT 0,
  `archive_url` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `variant_name` varchar(200) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `original_filename` varchar(200) DEFAULT NULL,
  `is_lost` tinyint(1) DEFAULT 0,
  `length` int(11) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `genre` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `external_name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `external_id_anime` int(11) DEFAULT NULL,
  `external_id_manga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `link` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `resolution` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news` (
  `fansub_id` int(11) DEFAULT NULL,
  `news_fetcher_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `original_contents` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `url` text DEFAULT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news_fetcher` (
  `id` int(11) NOT NULL,
  `fansub_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `method` varchar(255) NOT NULL,
  `fetch_type` varchar(255) NOT NULL DEFAULT 'periodic',
  `status` varchar(255) NOT NULL DEFAULT 'idle',
  `last_fetch_result` varchar(255) DEFAULT NULL,
  `last_fetch_date` timestamp NULL DEFAULT NULL,
  `last_fetch_increment` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pending_news` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `url` text NOT NULL,
  `image_url` text DEFAULT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recommendation` (
  `version_id` int(11) NOT NULL
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
  `fansub_id` int(11) NOT NULL,
  `downloads_url` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `remote_account` (
  `id` int(11) NOT NULL,
  `fansub_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `token` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `remote_folder` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `remote_account_id` int(11) NOT NULL,
  `folder` varchar(200) NOT NULL,
  `division_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `remote_folder_failed_files` (
  `id` int(11) NOT NULL,
  `remote_folder_id` int(11) NOT NULL,
  `file_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reported_error` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `ip` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `search_history` (
  `query` varchar(200) NOT NULL,
  `day` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alternate_names` varchar(200) DEFAULT NULL,
  `keywords` varchar(200) DEFAULT NULL,
  `type` varchar(200) NOT NULL,
  `subtype` varchar(200) NOT NULL,
  `publish_date` date DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `director` varchar(200) DEFAULT NULL,
  `studio` varchar(200) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `number_of_episodes` int(11) NOT NULL,
  `synopsis` text NOT NULL,
  `external_id` varchar(200) DEFAULT NULL,
  `tadaima_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `has_licensed_parts` tinyint(1) NOT NULL DEFAULT 0,
  `duration` varchar(200) DEFAULT NULL,
  `comic_type` varchar(200) DEFAULT NULL,
  `reader_type` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `birthdate` date NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `version` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `is_missing_episodes` tinyint(1) NOT NULL DEFAULT 0,
  `is_featurable` tinyint(1) NOT NULL DEFAULT 0,
  `is_always_featured` tinyint(1) NOT NULL DEFAULT 0,
  `featured_until` timestamp NULL DEFAULT NULL,
  `show_divisions` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_divisions` tinyint(1) NOT NULL DEFAULT 1,
  `show_episode_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `show_unavailable_episodes` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_extras` tinyint(1) NOT NULL DEFAULT 1,
  `order_type` int(11) NOT NULL DEFAULT 0,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `completed_date` timestamp NULL DEFAULT NULL,
  `storage_folder` varchar(2048) DEFAULT NULL,
  `storage_processing` int(11) DEFAULT NULL,
  `default_resolution` varchar(200) DEFAULT NULL,
  `version_author` varchar(200) DEFAULT NULL,
  `files_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `files_updated_by` varchar(200) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `views` (
  `file_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `time_spent` int(11) DEFAULT NULL,
  `pages_read` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `view_log` (
  `id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `file_id` int(11) NOT NULL,
  `ip` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` text DEFAULT NULL,
  `user_agent_read` text DEFAULT NULL,
  `view_type` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `view_session` (
  `id` varchar(24) NOT NULL,
  `method` varchar(200) NOT NULL,
  `file_id` int(11) NOT NULL,
  `time_spent` int(11) DEFAULT NULL,
  `total_time` int(11) DEFAULT NULL,
  `bytes_read` bigint(11) DEFAULT NULL,
  `total_bytes` bigint(11) DEFAULT NULL,
  `pages_read` int(11) DEFAULT NULL,
  `total_pages` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(200) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `user_agent_read` text DEFAULT NULL,
  `is_viewer_closed` tinyint(1) NOT NULL DEFAULT 0,
  `is_view_counted` tinyint(1) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `log` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`username`),
  ADD KEY `admin_user_ibfk_1` (`fansub_id`);

ALTER TABLE `advent_calendar`
  ADD PRIMARY KEY (`year`);

ALTER TABLE `advent_day`
  ADD PRIMARY KEY (`year`,`day`);

ALTER TABLE `division`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_ibfk_1` (`series_id`) USING BTREE;

ALTER TABLE `episode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `episode_ibfk_1` (`series_id`);

ALTER TABLE `episode_title`
  ADD PRIMARY KEY (`version_id`,`episode_id`),
  ADD KEY `episode_title_ibfk_1` (`episode_id`) USING BTREE;

ALTER TABLE `fansub`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_ibfk_1` (`episode_id`) USING BTREE,
  ADD KEY `file_ibfk_2` (`version_id`) USING BTREE;

ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_ibfk_1` (`file_id`) USING BTREE;

ALTER TABLE `news`
  ADD KEY `fk_news_fansub` (`fansub_id`),
  ADD KEY `fk_news_news_fetcher` (`news_fetcher_id`) USING BTREE;

ALTER TABLE `news_fetcher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fetcher_fansub` (`fansub_id`);

ALTER TABLE `pending_news`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `recommendation`
  ADD PRIMARY KEY (`version_id`);

ALTER TABLE `related_series`
  ADD PRIMARY KEY (`series_id`,`related_series_id`),
  ADD KEY `related_series_id` (`related_series_id`);

ALTER TABLE `rel_series_genre`
  ADD PRIMARY KEY (`series_id`,`genre_id`),
  ADD KEY `rel_series_genre_ibfk_1` (`genre_id`);

ALTER TABLE `rel_version_fansub`
  ADD PRIMARY KEY (`version_id`,`fansub_id`),
  ADD KEY `rel_version_fansub_ibfk_1` (`fansub_id`);

ALTER TABLE `remote_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remote_account_ibfk_1` (`fansub_id`);

ALTER TABLE `remote_folder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remote_folder_ibfk_1` (`remote_account_id`),
  ADD KEY `remote_folder_ibfk_2` (`version_id`),
  ADD KEY `remote_folder_ibfk_3` (`division_id`) USING BTREE;

ALTER TABLE `remote_folder_failed_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remote_folder_failed_files_ibfk_1` (`remote_folder_id`);

ALTER TABLE `reported_error`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

ALTER TABLE `version`
  ADD PRIMARY KEY (`id`),
  ADD KEY `version_ibfk_1` (`series_id`);

ALTER TABLE `views`
  ADD PRIMARY KEY (`file_id`,`day`),
  ADD KEY `type` (`type`);

ALTER TABLE `view_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

ALTER TABLE `view_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_archived` (`is_archived`),
  ADD KEY `created` (`created`),
  ADD KEY `view_session_ibfk_1` (`file_id`);

ALTER TABLE `admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `division`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `episode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `fansub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `news_fetcher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pending_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `remote_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `remote_folder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `remote_folder_failed_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reported_error`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `view_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `admin_user`
  ADD CONSTRAINT `admin_user_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `division`
  ADD CONSTRAINT `division_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `episode`
  ADD CONSTRAINT `episode_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `episode_title`
  ADD CONSTRAINT `episode_title_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `episode_title_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_fansub` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_fetcher` FOREIGN KEY (`news_fetcher_id`) REFERENCES `news_fetcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `news_fetcher`
  ADD CONSTRAINT `fk_fetcher_fansub` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recommendation`
  ADD CONSTRAINT `recommendation_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `related_series`
  ADD CONSTRAINT `related_series_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_series_ibfk_2` FOREIGN KEY (`related_series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `rel_series_genre`
  ADD CONSTRAINT `rel_series_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `rel_series_genre_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `rel_version_fansub`
  ADD CONSTRAINT `rel_version_fansub_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_version_fansub_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `remote_account`
  ADD CONSTRAINT `remote_account_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `remote_folder`
  ADD CONSTRAINT `remote_folder_ibfk_1` FOREIGN KEY (`remote_account_id`) REFERENCES `remote_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remote_folder_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remote_folder_ibfk_3` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `remote_folder_failed_files`
  ADD CONSTRAINT `remote_folder_failed_files_ibfk_1` FOREIGN KEY (`remote_folder_id`) REFERENCES `remote_folder` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `version`
  ADD CONSTRAINT `version_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
