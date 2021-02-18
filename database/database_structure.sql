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

CREATE TABLE `chapter` (
  `id` int(11) NOT NULL,
  `manga_id` int(11) NOT NULL,
  `volume_id` int(11) DEFAULT NULL,
  `number` decimal(10,2) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chapter_title` (
  `manga_version_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `episode` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  `number` decimal(10,2) DEFAULT NULL,
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
  `type` varchar(200) NOT NULL DEFAULT 'fansub',
  `url` varchar(200) DEFAULT NULL,
  `twitter_url` varchar(200) DEFAULT NULL,
  `twitter_handle` varchar(200) NOT NULL,
  `twitter_handle` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `ping_token` varchar(200) DEFAULT NULL,
  `historical` tinyint(1) NOT NULL DEFAULT 0,
  `archive_url` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `fetcher` (
  `id` int(11) NOT NULL,
  `fansub_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `method` varchar(255) NOT NULL,
  `fetch_type` varchar(255) NOT NULL DEFAULT 'periodic',
  `status` varchar(255) NOT NULL DEFAULT 'idle',
  `last_fetch_result` varchar(255) DEFAULT NULL,
  `last_fetch_date` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `manga_version_id` int(11) NOT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `variant_name` varchar(200) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `original_filename` varchar(200) DEFAULT NULL,
  `number_of_pages` int(11) NOT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
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
  `myanimelist_id_anime` int(11) DEFAULT NULL,
  `myanimelist_id_manga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `link` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `variant_name` varchar(200) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `lost` tinyint(1) DEFAULT 0,
  `comments` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `link_instance` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `resolution` varchar(200) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga` (
  `id` int(11) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alternate_names` varchar(200) DEFAULT NULL,
  `keywords` varchar(200) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'oneshot',
  `publish_date` timestamp NULL DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `chapters` int(11) NOT NULL,
  `synopsis` text NOT NULL,
  `myanimelist_id` int(11) DEFAULT NULL,
  `tadaima_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `reader_type` varchar(200) NOT NULL DEFAULT 'paged',
  `has_licensed_parts` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_version` (
  `id` int(11) NOT NULL,
  `manga_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `chapters_missing` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  `files_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `files_updated_by` varchar(200) NOT NULL,
  `is_featurable` tinyint(1) NOT NULL DEFAULT 0,
  `is_always_featured` tinyint(1) NOT NULL DEFAULT 0,
  `show_volumes` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_volumes` tinyint(1) NOT NULL DEFAULT 1,
  `show_chapter_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `show_unavailable_chapters` tinyint(1) NOT NULL DEFAULT 1,
  `order_type` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_recommendation` (
  `manga_version_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_search_history` (
  `query` varchar(200) NOT NULL,
  `day` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_view_log` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `ip` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_views` (
  `file_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `time_spent` int(11) NOT NULL DEFAULT 0,
  `pages_read` int(11) NOT NULL DEFAULT 0,
  `api_views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news` (
  `fansub_id` int(11) DEFAULT NULL,
  `fetcher_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `original_contents` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `url` text,
  `image` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pending_news` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `url` text NOT NULL,
  `image_url` text,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `play_session` (
  `play_id` varchar(20) NOT NULL,
  `link_id` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `last_update` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `read_session` (
  `read_id` varchar(20) NOT NULL,
  `file_id` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `pages_read` int(11) NOT NULL,
  `last_update` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recommendation` (
  `version_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_manga` (
  `series_id` int(11) NOT NULL,
  `related_manga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_manga_manga` (
  `manga_id` int(11) NOT NULL,
  `related_manga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_manga_anime` (
  `manga_id` int(11) NOT NULL,
  `related_anime_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_series` (
  `series_id` int(11) NOT NULL,
  `related_series_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rel_manga_genre` (
  `manga_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rel_manga_version_fansub` (
  `manga_version_id` int(11) NOT NULL,
  `fansub_id` int(11) NOT NULL,
  `downloads_url` varchar(200) DEFAULT NULL
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

CREATE TABLE `reported_error` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `play_time` int(11) NOT NULL,
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
  `myanimelist_id` int(11) DEFAULT NULL,
  `tadaima_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `has_licensed_parts` tinyint(1) NOT NULL DEFAULT 0,
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
  `episodes_missing` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(200) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(200) NOT NULL,
  `links_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `links_updated_by` varchar(200) NOT NULL,
  `is_featurable` tinyint(1) NOT NULL DEFAULT 0,
  `is_always_featured` tinyint(1) NOT NULL DEFAULT 0,
  `show_seasons` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_seasons` tinyint(1) NOT NULL DEFAULT 1,
  `show_episode_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `show_unavailable_episodes` tinyint(1) NOT NULL DEFAULT 1,
  `order_type` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `views` (
  `link_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `time_spent` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `view_log` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `ip` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `volume` (
  `id` int(11) NOT NULL,
  `manga_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `chapters` int(11) DEFAULT NULL,
  `myanimelist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_ibfk_1` (`fansub_id`);
ALTER TABLE `action_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_ibfk_1` (`manga_id`);
ALTER TABLE `chapter_title`
  ADD PRIMARY KEY (`manga_version_id`,`chapter_id`),
  ADD KEY `chapter_title_ibfk_1` (`chapter_id`) USING BTREE;
ALTER TABLE `episode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `episode_ibfk_1` (`series_id`);
ALTER TABLE `episode_title`
  ADD PRIMARY KEY (`version_id`,`episode_id`),
  ADD KEY `episode_title_ibfk_1` (`episode_id`) USING BTREE;
ALTER TABLE `fansub`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `fetcher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fetcher_fansub` (`fansub_id`);
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_ibfk_1` (`chapter_id`) USING BTREE,
  ADD KEY `file_ibfk_2` (`manga_version_id`) USING BTREE;
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
ALTER TABLE `link_instance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_instance_ibfk_1` (`link_id`) USING BTREE;
ALTER TABLE `manga`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `manga_recommendation`
  ADD PRIMARY KEY (`manga_version_id`);
ALTER TABLE `manga_version`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manga_version_ibfk_1` (`manga_id`);
ALTER TABLE `manga_views`
  ADD PRIMARY KEY (`file_id`,`day`);
ALTER TABLE `manga_view_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `news`
  ADD KEY `fk_news_fansub` (`fansub_id`),
  ADD KEY `fk_news_fetcher` (`fetcher_id`);
ALTER TABLE `play_session`
  ADD PRIMARY KEY (`play_id`);
ALTER TABLE `read_session`
  ADD PRIMARY KEY (`read_id`);
ALTER TABLE `recommendation`
  ADD PRIMARY KEY (`version_id`);
ALTER TABLE `related_manga`
  ADD PRIMARY KEY (`series_id`,`related_manga_id`),
  ADD KEY `related_manga_id` (`related_manga_id`);
ALTER TABLE `related_manga_manga`
  ADD PRIMARY KEY (`manga_id`,`related_manga_id`),
  ADD KEY `related_manga_id` (`related_manga_id`);
ALTER TABLE `related_manga_anime`
  ADD PRIMARY KEY (`manga_id`,`related_anime_id`),
  ADD KEY `related_anime_id` (`related_anime_id`);
ALTER TABLE `related_series`
  ADD PRIMARY KEY (`series_id`,`related_series_id`),
  ADD KEY `related_series_id` (`related_series_id`);
ALTER TABLE `rel_manga_genre`
  ADD PRIMARY KEY (`manga_id`,`genre_id`),
  ADD KEY `rel_manga_genre_ibfk_1` (`genre_id`);
ALTER TABLE `rel_manga_version_fansub`
  ADD PRIMARY KEY (`manga_version_id`,`fansub_id`),
  ADD KEY `rel_manga_version_fansub_ibfk_1` (`fansub_id`);
ALTER TABLE `rel_series_genre`
  ADD PRIMARY KEY (`series_id`,`genre_id`),
  ADD KEY `rel_series_genre_ibfk_1` (`genre_id`);
ALTER TABLE `rel_version_fansub`
  ADD PRIMARY KEY (`version_id`,`fansub_id`),
  ADD KEY `rel_version_fansub_ibfk_1` (`fansub_id`);
ALTER TABLE `reported_error`
  ADD PRIMARY KEY (`id`);
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
ALTER TABLE `view_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `volume`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volume_ibfk_1` (`manga_id`) USING BTREE;
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `action_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `episode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fansub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fetcher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `folder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `folder_failed_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `link_instance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga_view_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pending_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `reported_error`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `view_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `volume`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `chapter`
  ADD CONSTRAINT `chapter_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `chapter_title`
  ADD CONSTRAINT `chapter_title_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chapter_title_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode`
  ADD CONSTRAINT `episode_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode_title`
  ADD CONSTRAINT `episode_title_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `episode_title_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `fetcher`
  ADD CONSTRAINT `fk_fetcher_fansub` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `folder`
  ADD CONSTRAINT `folder_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `folder_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `folder_ibfk_3` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `folder_failed_files`
  ADD CONSTRAINT `folder_failed_files_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `link_ibfk_2` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `link_instance`
  ADD CONSTRAINT `link_instance_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `manga_recommendation`
  ADD CONSTRAINT `manga_recommendation_ibfk_1` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `manga_version`
  ADD CONSTRAINT `manga_version_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_fansub` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_fetcher` FOREIGN KEY (`fetcher_id`) REFERENCES `fetcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `play_session`
  ADD CONSTRAINT `play_session_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `read_session`
  ADD CONSTRAINT `read_session_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `recommendation`
  ADD CONSTRAINT `recommendation_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_manga`
  ADD CONSTRAINT `related_manga_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_manga_ibfk_2` FOREIGN KEY (`related_manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_manga_manga`
  ADD CONSTRAINT `related_manga_manga_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_manga_manga_ibfk_2` FOREIGN KEY (`related_manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_manga_anime`
  ADD CONSTRAINT `related_manga_anime_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_manga_anime_ibfk_2` FOREIGN KEY (`related_anime_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_series`
  ADD CONSTRAINT `related_series_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_series_ibfk_2` FOREIGN KEY (`related_series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_manga_genre`
  ADD CONSTRAINT `rel_manga_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `rel_manga_genre_ibfk_2` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_manga_version_fansub`
  ADD CONSTRAINT `rel_manga_version_fansub_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_manga_version_fansub_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
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
ALTER TABLE `volume`
  ADD CONSTRAINT `volume_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

