<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');

validate_hentai();

//Get series by slug
$url_slug = !empty($_GET['slug']) ? str_replace(' ','+',$_GET['slug']) : '';
$result = query_series_by_slug($url_slug, !empty($_GET['show_hidden']));
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	//Retry by getting it from an old slug
	unset($failed);
	//First try by removing the slash and testing for a series with that
	$result = query_series_by_series_only_slug(explode('/',$url_slug)[0]);
	$new_slug = mysqli_fetch_assoc($result) or $failed=TRUE;
	mysqli_free_result($result);
	if (isset($failed)) {
		//Nothing found yet? Try with an old redirected slug (it should not have a slash)
		unset($failed);
		$result = query_series_by_old_slug($url_slug);
		$new_slug = mysqli_fetch_assoc($result) or $failed=TRUE;
		mysqli_free_result($result);
		if (isset($failed)) {
			//Impossible to find
			http_response_code(404);
			include(__DIR__.'/error.php');
			die();
		}
	}
	header("HTTP/1.1 301 Moved Permanently");
	if ($new_slug['type']=='liveaction') {
		header("Location: https://".LIVEACTION_SUBDOMAIN.".".MAIN_DOMAIN."/".$new_slug['slug']);
	} else if ($new_slug['type']=='manga') {
		header("Location: https://".MANGA_SUBDOMAIN.".".($new_slug['rating']!='XXX' ? MAIN_DOMAIN : HENTAI_DOMAIN)."/".$new_slug['slug']);
	} else {
		header("Location: https://".ANIME_SUBDOMAIN.".".($new_slug['rating']!='XXX' ? MAIN_DOMAIN : HENTAI_DOMAIN)."/".$new_slug['slug']);
	}
	die();
}

//Blocked series - currently disabled
$blocked_series = array();
if (in_array($url_slug, $blocked_series)) {
	http_response_code(451);
	define('COPYRIGHT_ISSUE', TRUE);
	include(__DIR__.'/error.php');
	die();
}

define('PAGE_STYLE_TYPE', 'catalogue');

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['version_synopsis']);

define('PAGE_TITLE', $series['version_title']);
define('PAGE_PATH', '/'.$series['version_slug']);
define('PAGE_DESCRIPTION', str_replace("\n", " ", strip_tags($synopsis)));
define('PAGE_PREVIEW_IMAGE', STATIC_URL.'/social/version_'.$series['version_id'].'.jpg');

define('PAGE_IS_SERIES', TRUE);
define('PAGE_EXTRA_BODY_CLASS', 'has-carousel is-series-page');

require_once(__DIR__.'/../common/header.inc.php');
?>
					<input id="autoopen_file_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['f']) ? (int)$_GET['f'] : ''); ?>">
					<input id="series_id" type="hidden" value="<?php echo htmlspecialchars($series['id']); ?>">
					<input id="catalogue_type" type="hidden" value="<?php echo SITE_INTERNAL_NAME; ?>">
					<input id="seen_behavior" type="hidden" value="<?php echo !empty($user) ? $user['previous_chapters_read_behavior'] : -1; ?>">
					<input id="show_comment_warning" type="hidden" value="<?php echo !empty($user) ? ($user['num_comments']>0 ? 0 : 1) : 1; ?>">
					<div class="series-header">
						<img class="background" src="<?php echo STATIC_URL; ?>/images/featured/version_<?php echo $series['version_id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['version_title']); ?>">
						<div class="series-data">
							<div class="series-titles">
								<h2 class="series-title"><?php echo htmlspecialchars($series['version_title']); ?></h2>
<?php
$alternate_names='';
if ($series['name']!=$series['version_title']) {
	$alternate_names=$series['name'];
}
if (!empty($series['alternate_names'])) {
	if ($alternate_names!='') {
		$alternate_names .= ', ';
	}
	$alternate_names .= $series['alternate_names'];
}
?>
								<div class="series-alternate-names<?php echo empty($alternate_names) ? ' hidden' : ''; ?>"><?php echo htmlspecialchars($alternate_names); ?></div>
<?php
$additional_data = '';
if (CATALOGUE_ITEM_TYPE=='manga' && !empty($series['author'])) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['author'];
}
if (CATALOGUE_ITEM_TYPE!='manga' && !empty($series['studio'])) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['studio'];
}
if (!empty($series['year'])) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['year'];
}
if ($series['divisions']>1 && $series['subtype']!='movie') {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=sprintf(CATALOGUE_SEASON_STRING_PLURAL, $series['divisions']);
}
if ($series['number_of_episodes']>1) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	if ($series['subtype']!='movie') {
		$additional_data.=sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes']);
	} else {
		$additional_data.=sprintf(lang('catalogue.generic.number_of_chapters.movie.short'), $series['number_of_episodes']);
	}
} else if ($series['subtype']=='movie') {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=lang('catalogue.generic.movie');
} else if ($series['comic_type']=='novel') {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=lang('catalogue.manga.light_novel');
} else if ($series['subtype']=='oneshot') {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=lang('catalogue.manga.oneshot');
}
if (!SITE_IS_HENTAI) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=get_rating($series['rating']);
}
if (!empty($series['score'])) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data .= number_format($series['score'],2,","," ").sprintf(lang('catalogue.series.at_external_site'), CATALOGUE_SCORE_SOURCE);
}
?>
								<div class="series-additional-data"><?php echo htmlspecialchars($additional_data); ?></div>
							</div>
							<div class="series-tags"><?php echo get_genres_for_featured($series['genres'], $series['type'], $series['rating']); ?></div>
						</div>
					</div>
					<div class="section series-subheader">
						<div class="series-thumbnail-holder">
							<img class="series-thumbnail" src="<?php echo STATIC_URL; ?>/images/covers/version_<?php echo $series['version_id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['version_title']); ?>">
<?php
if (in_array($series['id'], !empty($user) ? $user['series_list_ids'] : array())) {
?>
							<div class="normal-button remove-from-my-list"><i class="fas fa-fw fa-bookmark"></i> <?php echo lang('catalogue.series.in_my_list'); ?></div>
<?php
} else {
?>
							<div class="normal-button add-to-my-list"><i class="far fa-fw fa-bookmark"></i> <?php echo lang('catalogue.series.add_to_my_list'); ?></div>
<?php
}
?>
						</div>
						<div class="series-synopsis">
							<div class="series-synopsis-real expandable-content-default"><?php echo $synopsis; ?></div>
							<span class="show-more hidden"><span class="fa fa-fw fa-caret-down"></span> <?php echo lang('js.catalogue.series.show_more'); ?> <span class="fa fa-fw fa-caret-down"></span></span>
						</div>
					</div>
					<div class="section">
<?php
$result = query_series_data_for_series_page($user, $series['id']);

if ($series['has_licensed_parts']) {
?>
						<div class="series-licensed-parts"><span class="fa fa-fw fa-circle-info"></span> <?php echo lang('catalogue.series.partially_licensed'); ?></div>
<?php
}
?>
						<div class="version-tab-container">
<?php
$i=0;
while ($version = mysqli_fetch_assoc($result)) {
	$is_blacklisted = is_any_fansub_blacklisted(get_prepared_versions($version['fansub_info']), $version['id']);
	$Parsedown = new Parsedown();
	$version_synopsis = $Parsedown->setBreaksEnabled(true)->line($version['synopsis']);
	$alternate_names='';
	if ($series['name']!=$version['title']) {
		$alternate_names=$series['name'];
	}
	if (!empty($series['alternate_names'])) {
		if ($alternate_names!='') {
			$alternate_names .= ', ';
		}
		$alternate_names .= $series['alternate_names'];
	}
?>
							<div class="version-tab<?php echo $is_blacklisted ? ' version-blacklisted' : ''; ?><?php echo $version['id']==$series['version_id'] ? ' version-tab-selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>" data-version-slug="<?php echo htmlspecialchars($version['slug']); ?>" data-version-title="<?php echo htmlspecialchars($version['title']); ?>" data-version-alternate-titles="<?php echo htmlspecialchars($alternate_names); ?>" data-version-synopsis="<?php echo htmlspecialchars($version_synopsis); ?>">
								<div class="version-fansub-icons"><?php echo get_fansub_icons($version['fansub_info'], get_prepared_versions($version['fansub_info']), $version['id']); ?></div>
								<div class="version-tab-text"><?php echo htmlspecialchars(lang('catalogue.series.version').get_fansub_preposition_name($version['fansub_name'])); ?><?php echo get_fansub_type(get_prepared_versions($version['fansub_info']), $version['id'])=='fandub' ? '<span class="fa fa-fw fa-microphone-lines" title="'.lang('catalogue.series.is_dubbed').'"></span>' : ''; ?><?php echo $is_blacklisted ? ' <span class="fa fa-fw fa-ban" title="'.lang('catalogue.series.in_your_blacklist').'"></span>' : ''; ?></div>
							</div>
<?php
	$i++;
}
mysqli_data_seek($result, 0);
?>
						</div>
<?php

$i=0;
while ($version = mysqli_fetch_assoc($result)) {
?>
						<div class="version-content<?php echo $version['id']!=$series['version_id'] ? ' hidden' : ''; ?>" id="version-content-<?php echo $version['id']; ?>">
<?php
	$resulte = query_episodes_for_series_version($series['id'], $version['id']);
	$episodes = array();
	while ($row = mysqli_fetch_assoc($resulte)) {
		array_push($episodes, $row);
	}
	mysqli_free_result($resulte);

	if (count($episodes)>0) {
?>
							<div class="section-content extra-content series-file-lists<?php echo (!empty($user) && $user['episode_sort_order']) || (empty($user) && !empty($_COOKIE['episode_sort_order'])) ? ' series-file-lists-reversed' : ''; ?>">
<?php
		$divisions = array();
		$last_division_number = -1;
		$last_division_id = -1;
		$last_division_name = "";
		$last_division_number_of_episodes = -1;
		$current_division_episodes = array();
		$position = 1;
		foreach ($episodes as $row) {
			if (floatval($row['division_number'])!=$last_division_number){
				if ($last_division_number!=-1) {
					array_push($divisions, array(
						'division_id' => $last_division_id,
						'division_number' => $last_division_number,
						'division_name' => $last_division_name,
						'division_number_of_episodes' => $last_division_number_of_episodes,
						'episodes' => $current_division_episodes
					));
				}
				$last_division_number=floatval($row['division_number']);
				$last_division_id=$row['division_id'];
				$last_division_name=$row['division_name'];
				$last_division_number_of_episodes = $row['division_number_of_episodes'];
				$current_division_episodes = array();
			}

			$row['position'] = $position;
			array_push($current_division_episodes, $row);
			$position++;
		}
		array_push($divisions, array(
			'division_id' => $last_division_id,
			'division_number' => $last_division_number,
			'division_name' => $last_division_name,
			'division_number_of_episodes' => $last_division_number_of_episodes,
			'episodes' => $current_division_episodes
		));

		foreach ($divisions as $index => $division) {
			$ids=array(-1);
			$linked_ids=array(-1);
			foreach ($division['episodes'] as $episode) {
				if (!empty($episode['linked_episode_id'])) {
					$linked_ids[]=$episode['linked_episode_id'];
				} else {
					$ids[]=$episode['id'];
				}
			}
			$result_episodes = query_available_files_in_version($version['id'], $ids, $linked_ids);
			$divisions[$index]['available_episodes'] = mysqli_num_rows($result_episodes);
			mysqli_free_result($result_episodes);

			if (!empty($user)) {
				$result_seen_episodes = query_available_seen_files_in_version($user['id'], $version['id'], $ids, $linked_ids);
				$divisions[$index]['available_seen_episodes'] = mysqli_num_rows($result_seen_episodes);
				mysqli_free_result($result_seen_episodes);
			} else {
				$divisions[$index]['available_seen_episodes'] = 0;
			}
		}

		//Add extras
		$resulte = query_extras_by_version_id($version['id']);
		$extras = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			$row['position'] = $position;
			array_push($extras, $row);
			$position++;
		}
		mysqli_free_result($resulte);
		if (count($extras)>0) {
			array_push($divisions, array(
				'division_id' => 'extras',
				'division_number' => 'extras',
				'division_name' => ($series['type']!='manga' ? $version['title'].' - ' : '').lang('catalogue.generic.extra_division'),
				'division_number_of_episodes' => count($extras),
				'episodes' => $extras,
				'available_episodes' => count($extras),
				'available_seen_episodes' => 0 //Irrelevant, extras never get preselected
			));
		}
?>
								<h2 class="section-title-main section-title-with-table"><i class="fa fa-fw <?php echo $series['subtype']==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID ? CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON : CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON; ?>"></i> <?php echo mysqli_num_rows($result)>1 ? lang('catalogue.series.content_header.multiple') : lang('catalogue.series.content_header.single'); ?>
<?php
		//false: ascending, true: descending
		$sort_order = (!empty($user) && $user['episode_sort_order']) || (empty($user) && !empty($_COOKIE['episode_sort_order']));
		if ($sort_order) {
			//We need to resort the lists:
			//First we separate normal divisions and special divisions, then reverse sort normal divisions, then append everything again.
			$new_divisions = array();
			$special_divisions = array();
			foreach ($divisions as $index => $division) {
				if ($division['division_number']!='extras') {
					$division['episodes'] = array_reverse($division['episodes']);
					array_push($new_divisions, $division);
				} else {
					array_push($special_divisions, $division);
				}
			}
			$unsorted_divisions = $divisions;
			$divisions = array_merge(array_reverse($new_divisions), $special_divisions);
?>
									<span class="sort-order sort-descending"><span class="fa fa-fw fa-arrow-down-wide-short"></span> <span class="sort-description"><?php echo lang('catalogue.series.sort_last_to_first'); ?></span></span>
<?php
		} else {
			$unsorted_divisions = $divisions;
			//Already sorted, no need to resort
?>
									<span class="sort-order sort-ascending"><span class="fa fa-fw fa-arrow-down-short-wide"></span> <span class="sort-description"><?php echo lang('catalogue.series.sort_first_to_last'); ?></span></span>
<?php
		}
?>
								</h2>
<?php
		if (count($divisions)==1 && CATALOGUE_ITEM_TYPE!='manga') {
?>
								<div class="episode-table<?php echo CATALOGUE_ITEM_TYPE=='manga' ? ' episode-table-manga' : ''; ?>">
<?php
			if ($version['status']!=1 && $sort_order) {
					print_fake_episode($version['status']);
			}
			foreach ($divisions[0]['episodes'] as $episode) {
				if ($divisions[0]['division_id']=='extras') {
					print_extra($version['fansub_name'], $episode, $version['id'], $series, $series['type']!='manga' ? $division['division_name'] : $version['title'], $episode['position']);
				} else {
					print_episode($version['fansub_name'], $episode, $version['id'], $series, $series['type']!='manga' ? $division['division_name'] : $version['title'], $version, $episode['position']);
				}
			}
			if ($version['status']!=1 && !$sort_order) {
					print_fake_episode($version['status']);
			}
?>
								</div>
<?php
		} else { //Multiple divisions (or manga)
			if (CATALOGUE_ITEM_TYPE=='manga') {
?>
								<div class="division-list">
<?php
			}
			else {
				$selected_division_id = $divisions[0]['division_id'];
				$all_seen = FALSE;
				foreach ($divisions as $index => $division) {
					if ($division['available_episodes']>0 && $division['division_id']!='extras') {
						$selected_division_id=$division['division_id'];
						if ($division['available_seen_episodes']<$division['available_episodes']) {
							$all_seen = FALSE;
							break;
						} else {
							$all_seen = TRUE;
						}
					}
				}
				if ($all_seen && $version['status']==1) {
					$selected_division_id = $divisions[0]['division_id'];
				}
				//Print list of seasons
?>
									<select class="season-chooser">
<?php
				foreach ($unsorted_divisions as $index => $division) {
?>
									<option value="<?php echo $division['division_id']; ?>" data-title="<?php echo htmlspecialchars($division['division_name']); ?>" <?php echo $division['available_episodes']==0 ? ' class="season-unavailable"' : ''; ?><?php echo $division['division_id']==$selected_division_id ? ' selected' : ''; ?>><?php echo htmlspecialchars($division['division_name']).' ('.$division['available_episodes'].'/'.$division['division_number_of_episodes'].')'; ?></option>
<?php
				}
?>
									</select>
<?php
			}
			foreach ($divisions as $index => $division) {
				if (CATALOGUE_ITEM_TYPE=='manga') {
					//Special case for displaying divisions without a chooser
					$is_inside_empty_batch = ($division['available_episodes']==0);
					$is_first_in_empty_batch = $is_inside_empty_batch && ($index==0 || ($index>0 && $divisions[$index-1]['available_episodes']!=0));

					if ($is_first_in_empty_batch) {
?>
									<div class="empty-divisions"><?php echo lang('catalogue.series.more_volumes_available'); ?></div>
<?php
					}
?>
									<div id="version-<?php echo $version['id']; ?>-division-<?php echo $division['division_number']; ?>" data-title="<?php echo htmlspecialchars($division['division_name']); ?>" class="division<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>">
										<div class="division-header<?php echo $division['available_episodes']>0 ? '' : ' division-unavailable'; ?>">
											<img class="division-cover" src="<?php echo file_exists(STATIC_DIRECTORY.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg') ? STATIC_URL.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg' : STATIC_URL.'/images/covers/version_'.$version['id'].'.jpg'; ?>">
											<div class="division-title"><?php echo $division['division_name']; ?></div>
										</div>
<?php
				}
?>
										<div class="division-container<?php echo $division['available_episodes']>0 ? '' : ' division-unavailable'; ?><?php echo CATALOGUE_ITEM_TYPE=='manga' ? '' : ($division['division_id']==$selected_division_id ? '' : ' hidden'); ?>" id="division-container-<?php echo $version['id'].'-'.$division['division_id'];?>">
											<div class="episode-table<?php echo CATALOGUE_ITEM_TYPE=='manga' ? ' episode-table-manga' : ''; ?>">
<?php
				if ($division['available_episodes']>0) {
					if ($version['status']!=1 && $sort_order && $division['available_episodes']<count($division['episodes'])) {
						print_fake_episode($version['status']);
					}
					foreach ($division['episodes'] as $episode) {
						if ($division['division_id']=='extras') {
							print_extra($version['fansub_name'], $episode, $version['id'], $series, $series['type']!='manga' ? $division['division_name'] : $version['title'], $episode['position']);
						} else {
							print_episode($version['fansub_name'], $episode, $version['id'], $series, $series['type']!='manga' ? $division['division_name'] : $version['title'], $version, $episode['position']);
						}
					}
					if ($version['status']!=1 && !$sort_order && $division['available_episodes']<count($division['episodes'])) {
						print_fake_episode($version['status']);
					}
				} else {
					print_fake_episode($version['status']);
				}
?>
											</div>
										</div>
<?php
				if (CATALOGUE_ITEM_TYPE=='manga') {
?>
									</div>
<?php
				}
			}
			if (CATALOGUE_ITEM_TYPE=='manga') {
?>
								</div>
<?php
			}
		}
	}
?>
							</div>
							<div class="section-content extra-content">
								<h2 class="section-title-main"><i class="fa fa-fw fa-user-group"></i> <?php echo lang('catalogue.series.author_header'); ?></h2>
<?php
	$fansubs = query_fansubs_by_version_id($version['id']);
?>
								<div class="version-fansub-list-and-rating">
									<div class="version-fansub-list">
<?php
	foreach ($fansubs as $fansub) {
?>
										<div class="version-fansub-element">
											<img class="version-fansub-image" src="<?php echo STATIC_URL.'/images/icons/'.$fansub['id'].'.png'; ?>" alt="">
											<div class="version-fansub-data">
												<div class="version-fansub-name"><?php echo $fansub['name'].($fansub['type']=='fandub' ? '<span class="fa fa-fw fa-microphone-lines" title="'.lang('catalogue.series.is_fandub').'"></span>' : ''); ?></div>
												<div class="version-fansub-links">
<?php
		if (!empty(!empty($fansub['archive_url']) ? $fansub['archive_url'] : $fansub['url'])) {
?>
													<a class="fa fa-fw fa-earth-europe web-link" title="<?php echo lang('generic.web_link.alt'); ?>" href="<?php echo !empty($fansub['archive_url']) ? $fansub['archive_url'] : $fansub['url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['bluesky_url'])) {
?>
													<a class="fab fa-fw fa-bluesky bluesky-link" title="<?php echo lang('generic.bluesky_link.alt'); ?>" href="<?php echo $fansub['bluesky_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['discord_url'])) {
?>
													<a class="fab fa-fw fa-discord discord-link" title="<?php echo lang('generic.discord_link.alt'); ?>" href="<?php echo $fansub['discord_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['mastodon_url'])) {
?>
													<a class="fab fa-fw fa-mastodon mastodon-link" title="<?php echo lang('generic.mastodon_link.alt'); ?>" href="<?php echo $fansub['mastodon_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['twitter_url'])) {
?>
													<a class="fab fa-fw fa-x-twitter twitter-link" title="<?php echo lang('generic.x_link.alt'); ?>" href="<?php echo $fansub['twitter_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['downloads_url'])) {
			$url_arr=explode(';', $fansub['downloads_url']);
			foreach ($url_arr as $url) {
				if (preg_match(REGEXP_DL_LINK,$url)) {
					echo ' <a class="fa fa-fw fa-cloud-arrow-down web-link fansub-downloads" title="'.lang('catalogue.series.download.alt').'" data-url="'.htmlspecialchars(base64_encode($url)).'"></a>';
				} else {
					echo ' <a class="fa fa-fw fa-cloud-arrow-down web-link" href="'.$url.'" title="'.lang('catalogue.series.download.alt').'" target="_blank"></a>';
				}
			}
		}
?>
												</div>
											</div>
										</div>
<?php
	}
?>
									</div>
									<div class="version-fansub-rating">
										<div class="version-fansub-rating-title"><?php echo lang('catalogue.series.rate_header'); ?></div>
										<div class="version-fansub-rating-buttons">
											<span class="version-fansub-rating-positive fa fa-fw fa-thumbs-up<?php echo $version['user_rating']==1 ? ' version-fansub-rating-selected' : ''; ?>"></span>
											<span class="version-fansub-rating-negative fa fa-fw fa-thumbs-down<?php echo $version['user_rating']==-1 ? ' version-fansub-rating-selected' : ''; ?>"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="section-content extra-content">
								<h2 class="section-title-main"><a name="<?php echo lang('url.comments_anchor'); ?>"></a><i class="fa fa-fw fa-comment"></i> <?php echo lang('catalogue.series.comments_header'); ?></h2>
<?php

	$resultcom = query_version_comments($version['id'], $user);
	$total_comments = mysqli_num_rows($resultcom);
?>
								<div class="comments-list">
									<div class="comment comment-fake">
										<img class="comment-avatar" src="<?php echo get_user_avatar_url($user); ?>" alt="">
										<div class="comment-message comment-input">
											<div class="comment-send-form">
												<div class="grow-wrap">
													<textarea placeholder="<?php echo lang('catalogue.series.comments.placeholder'); ?>" onfocus="checkCommentPossible(this);" oninput="this.parentNode.dataset.replicatedValue=this.value;checkForAutoSpoilers(this);" rows="1" required></textarea>
													<div class="comment-checkbox">
														<input class="comment-has-spoiler" id="comment-has-spoiler-<?php echo $version['id']; ?>" type="checkbox">
														<label for="comment-has-spoiler-<?php echo $version['id']; ?>" class="for-checkbox"><?php echo lang('catalogue.series.comments.contains_spoilers'); ?></label>
													</div>
												</div>
												<button class="normal-button comment-send" title="<?php echo lang('catalogue.series.comments.send'); ?>"><i class="fa fa-fw fa-paper-plane"></i></button>
											</div>
										</div>
									</div>
<?php
	$i=0;
	while ($comment = mysqli_fetch_assoc($resultcom)) {
		print_comment($comment, $i>=3);
		$resultrep = query_comment_replies($comment['id']);
		while ($reply = mysqli_fetch_assoc($resultrep)) {
			print_comment($reply, $i>=3);
		}
		mysqli_free_result($resultrep);
?>
<?php
		$i++;
	}
?>
									<div class="comments-buttons">
<?php
	if ($total_comments>3) {
?>
										<a class="normal-button load-all-comments"><i class="fa fa-fw fa-angles-down"></i> <?php echo ($total_comments-3)==1 ? lang('catalogue.series.comments.show_more_one') : sprintf(lang('catalogue.series.comments.show_more_many'), ($total_comments-3)); ?></a>
<?php
	}
	if (FALSE && !DISABLE_COMMUNITY && !empty($version['forum_topic_id']) && $total_comments>0) {
?>
										<a class="normal-button" href="<?php echo COMMUNITY_URL.'/viewtopic.php?t='.$version['forum_topic_id']; ?>"><i class="fa fa-fw fa-comment"></i> <?php echo lang('catalogue.series.comments.join_community'); ?></a>
<?php
	}
?>
									</div>
								</div>
<?php
	mysqli_free_result($resultcom);
?>
							</div>
						</div>
<?php
	$i++;
}
?>
					</div>
<?php
$num_of_genres = count(explode(', ', $series['genres']));
$num_of_genres_in_common = max(intval(round($num_of_genres/2)),1);

$resultra = query_related_series($user, $series['id'], $series['author'], $num_of_genres_in_common, 24, TRUE);

if (mysqli_num_rows($resultra)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><i class="fa fa-fw fa-arrow-right-arrow-left"></i> <?php echo CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE; ?></h2>
						<div class="section-content carousel swiper">
							<div class="swiper-wrapper">
<?php
	while ($row = mysqli_fetch_assoc($resultra)) {
?>
								<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']) : ''; ?> swiper-slide">
<?php
		print_carousel_item($row, FALSE, FALSE);
?>
								</div>
<?php
	}
?>
							</div>
							<div class="swiper-button-prev"></div>
							<div class="swiper-button-next"></div>
						</div>
					</div>
<?php
}

mysqli_free_result($resultra);

$resultrm = query_related_series($user, $series['id'], $series['author'], $num_of_genres_in_common, 24, FALSE);

if (mysqli_num_rows($resultrm)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><i class="fa fa-fw fa-arrows-turn-right fa-flip-vertical"></i> <?php echo CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE; ?></h2>
						<div class="section-content carousel swiper">
							<div class="swiper-wrapper">
<?php
	while ($row = mysqli_fetch_assoc($resultrm)) {
?>
								<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']) : ''; ?> swiper-slide">
<?php
		print_carousel_item($row, FALSE, FALSE);
?>
								</div>
<?php
	}
?>
							</div>
							<div class="swiper-button-prev"></div>
							<div class="swiper-button-next"></div>
						</div>
					</div>
<?php
}

mysqli_free_result($resultrm);
mysqli_free_result($result);
require_once(__DIR__.'/../common/footer.inc.php');
?>
