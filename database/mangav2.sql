CREATE TABLE `chapter` (
  `id` int(11) NOT NULL,
  `manga_id` int(11) NOT NULL,
  `volume_id` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chapter_title` (
  `manga_version_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `manga_version_id` int(11) NOT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `extra_name` varchar(200) DEFAULT NULL,
  `original_filename` varchar(200) DEFAULT NULL,
  `number_of_pages` int(11) NOT NULL,
  `comments` varchar(200) DEFAULT NULL,
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
  `show_volumes` tinyint(1) NOT NULL DEFAULT 1,
  `show_expanded_volumes` tinyint(1) NOT NULL DEFAULT 1,
  `show_chapter_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `show_unavailable_chapters` tinyint(1) NOT NULL DEFAULT 1,
  `has_licensed_parts` tinyint(1) NOT NULL DEFAULT 0,
  `order_type` int(11) NOT NULL DEFAULT 0,
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
  `is_always_featured` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_recommendation` (
  `manga_version_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_manga_manga` (
  `manga_id` int(11) NOT NULL,
  `related_manga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `related_manga_anime` (
  `manga_id` int(11) NOT NULL,
  `related_anime_id` int(11) NOT NULL
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

CREATE TABLE `manga_search_history` (
  `query` varchar(200) NOT NULL,
  `day` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `volume` (
  `id` int(11) NOT NULL,
  `manga_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `chapters` int(11) DEFAULT NULL,
  `myanimelist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_views` (
  `file_id` int(11) NOT NULL,
  `day` varchar(200) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `time_spent` int(11) NOT NULL DEFAULT 0,
  `pages_read` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `manga_view_log` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `read_session` (
  `read_id` varchar(20) NOT NULL,
  `file_id` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `pages_read` int(11) NOT NULL,
  `last_update` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_ibfk_1` (`manga_id`);
ALTER TABLE `chapter_title`
  ADD PRIMARY KEY (`manga_version_id`,`chapter_id`),
  ADD KEY `chapter_title_ibfk_1` (`chapter_id`) USING BTREE;
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_ibfk_1` (`chapter_id`) USING BTREE,
  ADD KEY `file_ibfk_2` (`manga_version_id`) USING BTREE;
ALTER TABLE `manga_recommendation`
  ADD PRIMARY KEY (`manga_version_id`);
ALTER TABLE `related_manga_manga`
  ADD PRIMARY KEY (`manga_id`,`related_manga_id`),
  ADD KEY `related_manga_id` (`related_manga_id`);
ALTER TABLE `related_manga_anime`
  ADD PRIMARY KEY (`manga_id`,`related_anime_id`),
  ADD KEY `related_anime_id` (`related_anime_id`);
ALTER TABLE `rel_manga_genre`
  ADD PRIMARY KEY (`manga_id`,`genre_id`),
  ADD KEY `rel_manga_genre_ibfk_1` (`genre_id`);
ALTER TABLE `rel_manga_version_fansub`
  ADD PRIMARY KEY (`manga_version_id`,`fansub_id`),
  ADD KEY `rel_manga_version_fansub_ibfk_1` (`fansub_id`);
ALTER TABLE `volume`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volume_ibfk_1` (`manga_id`) USING BTREE;
ALTER TABLE `manga`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `manga_version`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manga_version_ibfk_1` (`manga_id`);
ALTER TABLE `manga_views`
  ADD PRIMARY KEY (`file_id`,`day`);
ALTER TABLE `manga_view_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `read_session`
  ADD PRIMARY KEY (`read_id`);

ALTER TABLE `chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `volume`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manga_view_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chapter`
  ADD CONSTRAINT `chapter_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `chapter_title`
  ADD CONSTRAINT `chapter_title_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chapter_title_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `manga_recommendation`
  ADD CONSTRAINT `manga_recommendation_ibfk_1` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_manga_manga`
  ADD CONSTRAINT `related_manga_manga_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_manga_manga_ibfk_2` FOREIGN KEY (`related_manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `related_manga_anime`
  ADD CONSTRAINT `related_manga_anime_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `related_manga_anime_ibfk_2` FOREIGN KEY (`related_anime_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_manga_genre`
  ADD CONSTRAINT `rel_manga_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  ADD CONSTRAINT `rel_manga_genre_ibfk_2` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `rel_manga_version_fansub`
  ADD CONSTRAINT `rel_manga_version_fansub_ibfk_1` FOREIGN KEY (`fansub_id`) REFERENCES `fansub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_manga_version_fansub_ibfk_2` FOREIGN KEY (`manga_version_id`) REFERENCES `manga_version` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `volume`
  ADD CONSTRAINT `volume_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `manga_version`
  ADD CONSTRAINT `manga_version_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `read_session`
  ADD CONSTRAINT `read_session_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

