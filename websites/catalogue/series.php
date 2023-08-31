<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");

validate_hentai();

$result = query_series_by_slug(!empty($_GET['slug']) ? $_GET['slug'] : '', !empty($_GET['show_hidden']));
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

//Blocked series - currently disabled
$blocked_series = array();
if (in_array($_GET['slug'], $blocked_series)) {
	http_response_code(451);
	define('COPYRIGHT_ISSUE', TRUE);
	include('error.php');
	die();
}

define('PAGE_STYLE_TYPE', 'catalogue');

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

define('PAGE_TITLE', $series['name']);
define('PAGE_PATH', '/'.$series['slug']);
define('PAGE_DESCRIPTION', str_replace("\n", " ", strip_tags($synopsis)));
define('PAGE_PREVIEW_IMAGE', SITE_BASE_URL.'/preview/'.$series['slug'].'.jpg');

define('PAGE_IS_SERIES', TRUE);
define('PAGE_EXTRA_BODY_CLASS', 'has-carousel is-series-page');

require_once("../common.fansubs.cat/header.inc.php");
?>
					<input id="autoopen_file_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['f']) ? (int)$_GET['f'] : ''); ?>">
					<input id="series_id" type="hidden" value="<?php echo htmlspecialchars($series['id']); ?>">
					<input id="seen_behavior" type="hidden" value="<?php echo !empty($user) ? $user['previous_chapters_read_behavior'] : -1; ?>">
					<div class="series-header">
						<img class="background" src="<?php echo STATIC_URL; ?>/images/featured/<?php echo $series['id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['name']); ?>">
						<div class="series-data">
							<div class="series-titles">
								<h2 class="series-title"><?php echo htmlspecialchars($series['name']); ?></h2>
<?php
if (!empty($series['alternate_names'])) {
?>
								<div class="series-alternate-names"><?php echo htmlspecialchars($series['alternate_names']); ?></div>
<?php
}

$additional_data = '';
if (!empty($series['year'])) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['year'];
}
if ($series['divisions']>1) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['divisions'].' '.CATALOGUE_SEASON_STRING_PLURAL;
}
if ($series['number_of_episodes']>1) {
	if ($additional_data!='') {
		$additional_data.=' • ';
	}
	$additional_data.=$series['number_of_episodes'].' capítols';
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
	$additional_data .= number_format($series['score'],2,","," ").' a '.CATALOGUE_SCORE_SOURCE;
}
?>
								<div class="series-additional-data"><?php echo htmlspecialchars($additional_data); ?></div>
							</div>
							<div class="series-tags"><?php echo get_genres_for_featured($series['genres'], $series['type'], $series['rating']); ?></div>
						</div>
					</div>
					<div class="section series-subheader">
						<div class="series-thumbnail-holder">
							<img class="series-thumbnail" src="<?php echo STATIC_URL; ?>/images/covers/<?php echo $series['id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['name']); ?>">
<?php
if (in_array($series['id'], !empty($user) ? $user['series_list_ids'] : array())) {
?>
							<div class="normal-button remove-from-my-list"><i class="fas fa-fw fa-bookmark"></i> A la meva llista</div>
<?php
} else {
?>
							<div class="normal-button add-to-my-list"><i class="far fa-fw fa-bookmark"></i> Afegeix a la meva llista</div>
<?php
}
?>
						</div>
						<div class="series-synopsis">
							<div class="series-synopsis-real expandable-content-default"><?php echo $synopsis; ?></div>
							<span class="show-more hidden"><span class="fa fa-fw fa-caret-down"></span> Mostra’n més <span class="fa fa-fw fa-caret-down"></span></span>
						</div>
					</div>
					<div class="section">
<?php
$result = query_series_data_for_series_page($user, $series['id']);

//Check if specified version exists
$version_found = FALSE;
$passed_version = NULL;
if (isset($_GET['version'])) {
	$passed_version = $_GET['version'];
} else if (isset($_GET['v'])) {
	$passed_version = $_GET['v'];
}
while ($version = mysqli_fetch_assoc($result)) {
	if ($version['id']==$passed_version){
		$version_found = TRUE;
		break;
	}
}
mysqli_data_seek($result, 0);
?>
						<div class="version-tab-container">
<?php
$i=0;
while ($version = mysqli_fetch_assoc($result)) {
	$is_blacklisted = is_any_fansub_blacklisted(get_prepared_versions($version['fansub_info']), $version['id']);
?>
							<div class="version-tab<?php echo $is_blacklisted ? ' version-blacklisted' : ''; ?><?php echo ($version_found ? $version['id']==$passed_version : $i==0) ? ' version-tab-selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
								<div class="version-fansub-icons"><?php echo get_fansub_icons($version['fansub_info'], get_prepared_versions($version['fansub_info']), $version['id']); ?></div>
								<div class="version-tab-text"><?php echo htmlspecialchars('Versió '.get_fansub_preposition_name($version['fansub_name'])); ?><?php echo get_fansub_type(get_prepared_versions($version['fansub_info']), $version['id'])=='fandub' ? '<span class="fa fa-fw fa-microphone-lines" title="Versió doblada"></span>' : ''; ?><?php echo $is_blacklisted ? ' <span class="fa fa-fw fa-ban" title="És d’un fansub a la teva llista negra"></span>' : ''; ?></div>
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
						<div class="version-content<?php echo ($version_found ? $version['id']!=$passed_version : $i>0) ? ' hidden' : ''; ?>" id="version-content-<?php echo $version['id']; ?>">
<?php
	$position = 1;
	$resulte = query_episodes_for_series_version($series['id'], $version['id']);
	$episodes = array();
	while ($row = mysqli_fetch_assoc($resulte)) {
		array_push($episodes, $row);
	}
	mysqli_free_result($resulte);

	if (count($episodes)>0) {
?>
							<div class="section-content extra-content">
<?php
		//TODO REMOVE THIS
		$version['show_unavailable_episodes']=0;
		$version['show_expanded_divisions']=1;
		$divisions = array();
		$last_division_number = -1;
		$last_division_id = -1;
		$last_division_name = "";
		$current_division_episodes = array();
		foreach ($episodes as $row) {
			if ($row['division_number']!=$last_division_number && ($version['show_divisions']==1 || empty($row['division_number']))){
				if ($last_division_number!=-1) {
					array_push($divisions, array(
						'division_id' => $last_division_id,
						'division_number' => floatval($last_division_number),
						'division_name' => $last_division_name,
						'episodes' => $current_division_episodes
					));
				}
				$last_division_number=floatval($row['division_number']);
				$last_division_id=$row['division_id'];
				$last_division_name=$row['division_name'];
				$current_division_episodes = array();
			}

			array_push($current_division_episodes, $row);
		}
		array_push($divisions, array(
			'division_id' => $last_division_id,
			'division_number' => $last_division_number,
			'division_name' => $last_division_name,
			'episodes' => $current_division_episodes
		));

		$division_available_episodes=array();

		foreach ($divisions as $division) {
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
			$division_available_episodes[] = mysqli_num_rows($result_episodes);
			mysqli_free_result($result_episodes);
		}
?>
								<h2 class="section-title-main<?php if (count($divisions)>1) { echo " section-title-with-table"; } ?>">Contingut<div class="sort-order sort-ascending"><span class="fa fa-fw fa-arrow-down-short-wide"></span> <span class="sort-description">De més antic a més nou</span></div></h2>
<?php

		if (count($divisions)<2) {
			foreach ($divisions as $division) {
?>
								<div class="episode-table">
<?php
				foreach ($division['episodes'] as $episode) {
					print_episode($version['fansub_name'], $episode, $version['id'], $series, $version, $position);
					$position++;
				}
?>
								</div>
<?php
			}
		} else { //Multiple divisions
			foreach ($divisions as $index => $division) {
				$is_inside_empty_batch = ($division_available_episodes[$index]==0 && (($index>0 && $division_available_episodes[$index-1]==0) || ($index<(count($division_available_episodes)-1) && $division_available_episodes[$index+1]==0)));
				$is_first_in_empty_batch = $is_inside_empty_batch && ($index==0 || ($index>0 && $division_available_episodes[$index-1]!=0));

				if ($is_first_in_empty_batch) {
?>
								<div class="empty-divisions"><?php echo CATALOGUE_MORE_SEASONS_AVAILABLE; ?></div>
<?php
				}
?>
								<details id="version-<?php echo $version['id']; ?>-division-<?php echo !empty($division['division_number']) ? $division['division_number'] : 'altres'; ?>" class="division<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>"<?php echo ($version['show_expanded_divisions']==1 && $division_available_episodes[$index]>0) ? ' open' : ''; ?>>
									<summary class="division-header<?php echo $division_available_episodes[$index]>0 ? '' : ' division-unavailable'; ?>"><div class="division-header-inner"><img class="division-cover" src="<?php echo file_exists(STATIC_DIRECTORY.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg') ? STATIC_URL.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg' : STATIC_URL.'/images/covers/'.$series['id'].'.jpg'; ?>"><div class="division-title"><div class="division-title-collapsable"><?php echo !empty($division['division_number']) ? (($version['show_divisions']!=1 || (count($divisions)==2 && empty($last_division_number))) ? CATALOGUE_SEASON_STRING_UNIQUE : (!empty($division['division_name']) ? $division['division_name'] : (count($divisions)>1 ? CATALOGUE_SEASON_STRING_SINGULAR_CAPS.' '.$division['division_number'] : CATALOGUE_SEASON_STRING_UNIQUE))) : 'Altres'; ?><?php echo ($division_available_episodes[$index]>0 || $version['show_unavailable_episodes']==1) ? '<i class="division-arrow fa fa-fw fa-angle-right"></i>' : ''; ?></div><span class="division-elements"><?php echo $division_available_episodes[$index]>0 ? ($division_available_episodes[$index]==1 ? '1 element disponible' : $division_available_episodes[$index].' elements disponibles') : 'No hi ha cap element disponible'; ?></span></div></div></summary>
									<div class="division-container">
<?php
				if ($division_available_episodes[$index]>0 || $version['show_unavailable_episodes']==1) {
?>
										<div class="episode-table">
<?php
					foreach ($division['episodes'] as $episode) {
						print_episode($version['fansub_name'], $episode, $version['id'], $series, $version, $position);
						$position++;
					}
?>
										</div>
									</div>
<?php

				}
?>
								</details>
<?php
			}
		}

		//Add extras
		$resulte = query_extras_by_version_id($version['id']);
		$extras = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			array_push($extras, $row);
		}
		mysqli_free_result($resulte);
?>
<?php

		if (count($extras)>0) {
?>
								<details id="version-<?php echo $version['id']; ?>-division-extras" class="division">
									<summary class="division-header"><div class="division-header-inner"><img class="division-cover" src="<?php echo file_exists(STATIC_DIRECTORY.'/images/divisions/'.$version['id'].'_extras.jpg') ? STATIC_URL.'/images/divisions/'.$version['id'].'_extras.jpg' : STATIC_URL.'/images/covers/'.$series['id'].'.jpg'; ?>"><div class="division-title"><div class="division-title-collapsable">Extres<i class="division-arrow fa fa-fw fa-angle-right"></i></div><span class="division-elements"><?php echo count($extras)==1 ? '1 element disponible' : count($extras).' elements disponibles'; ?></span></div></div></summary>
									<div class="division-container">
										<div class="episode-table">
<?php
			foreach ($extras as $episode) {
				print_extra($version['fansub_name'], $episode, $version['id'], $series, $position);
				$position++;
			}
?>
										</div>
									</div>
								</details>
<?php

		}
	}
?>
							<div class="version-status-explanation">
<?php
	echo '<div class="version-status status-'.get_status($version['status']).' '.get_status_css_icons($version['status']).'" title="'.htmlspecialchars(get_status_description($version['status'])).'"></div>';
	echo '<div class="version-status-explanation-text">'.htmlspecialchars(get_status_description_long($version['status'])).'</div>';
?>
							</div>
							</div>
							<div class="section-content extra-content">
								<h2 class="section-title-main">Autoria d’aquesta versió</h2>
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
												<div class="version-fansub-name"><?php echo $fansub['name'].($fansub['type']=='fandub' ? '<span class="fa fa-fw fa-microphone-lines" title="És un fandub: fa doblatges."></span>' : ''); ?></div>
												<div class="version-fansub-links">
<?php
		if (!empty(!empty($fansub['archive_url']) ? $fansub['archive_url'] : $fansub['url'])) {
?>
													<a class="fa fa-fw fa-earth-europe web-link" href="<?php echo !empty($fansub['archive_url']) ? $fansub['archive_url'] : $fansub['url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['discord_url'])) {
?>
													<a class="fab fa-fw fa-discord discord-link" href="<?php echo $fansub['discord_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['mastodon_url'])) {
?>
													<a class="fab fa-fw fa-mastodon mastodon-link" href="<?php echo $fansub['mastodon_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['twitter_url'])) {
?>
													<a class="fab fa-fw fa-x-twitter twitter-link" href="<?php echo $fansub['twitter_url']; ?>" target="_blank"></a>
<?php
		}
		if (!empty($fansub['downloads_url'])) {
			$url_arr=explode(';', $fansub['downloads_url']);
			foreach ($url_arr as $url) {
				if (preg_match(REGEXP_DL_LINK,$url)) {
					echo ' <a class="fa fa-fw fa-cloud-arrow-down web-link fansub-downloads" data-url="'.htmlspecialchars(base64_encode($url)).'"></a>';
				} else {
					echo ' <a class="fa fa-fw fa-cloud-arrow-down web-link" href="'.$url.'" target="_blank"></a>';
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
<?php
	if (!empty($user)) {
?>
									<div class="version-fansub-rating">
										<div class="version-fansub-rating-title">Valora aquesta versió</div>
										<div class="version-fansub-rating-buttons">
											<span class="version-fansub-rating-positive fa fa-fw fa-thumbs-up<?php echo $version['user_rating']==1 ? ' version-fansub-rating-selected' : ''; ?>"></span>
											<span class="version-fansub-rating-negative fa fa-fw fa-thumbs-down<?php echo $version['user_rating']==-1 ? ' version-fansub-rating-selected' : ''; ?>"></span>
										</div>
									</div>
<?php
	}
?>
								</div>
							</div>
						</div>
<?php
	$i++;
}
?>
					</div>
<?php
if (!SITE_IS_HENTAI) {
?>
					<div class="section">
						<h2 class="section-title-main">Darrers comentaris a Tadaima.cat</h2>
						<div class="section-content">
<?php
	if (!empty($series['tadaima_id'])) {
		$tadaima_posts = get_tadaima_info($series['tadaima_id']);
?>
<?php
		$num_posts = count($tadaima_posts);
		if ($num_posts>3) {
			$tadaima_posts = array_slice($tadaima_posts, -3);
		}
?>
							<div class="tadaima-posts">
<?php
		foreach ($tadaima_posts as $post) {
?>
								<div class="tadaima-post">
									<div class="tadaima-message"><?php echo strip_tags(str_replace('<br>',"\n",$post->text)); ?></div>
									<div class="tadaima-author">
										<img class="tadaima-avatar" src="<?php echo $post->user->avatar; ?>">
										<span class="tadaima-user"><?php echo $post->user->name; ?></span> • <span class="tadaima-date"><?php echo get_relative_date($post->time); ?></span>
									</div>
								</div>
<?php
		}
?>
							</div>
							<div class="tadaima-explanation">En total hi ha <b><?php echo $num_posts==1 ? '1 missatge' : $num_posts.' missatges'; ?></b>. Et vols unir a la conversa?<br><span>(recorda que Tadaima.cat no és qui edita aquest contingut)</span></div>
							<a class="normal-button tadaima-cta" href="https://tadaima.cat/fil-t<?php echo $series['tadaima_id']; ?>.html" target="_blank"><i class="fa fa-fw fa-comment-dots"></i> Digues-hi la teva a Tadaima.cat</a>
<?php
	} else {
?>
							<div class="tadaima-explanation">Encara no hi ha cap fil sobre aquest contingut a Tadaima.cat. T’animem a crear-ne un!<br><span>(recorda que Tadaima.cat no és qui edita aquest contingut)</span></div>
							<a class="normal-button tadaima-cta" href="https://tadaima.cat/<?php echo CATALOGUE_ITEM_TYPE=='anime' ? 'anime-f10' : (CATALOGUE_ITEM_TYPE=='manga' ? 'manga-f9' : 'series-i-pel-licules-f14'); ?>.html" target="_blank"><i class="fa fa-fw fa-comment-dots"></i> Digues-hi la teva a Tadaima.cat</a>
<?php
	}
?>
						</div>
					</div>
<?php
}

$num_of_genres = count(explode(', ', $series['genres']));
$num_of_genres_in_common = max(intval(round($num_of_genres/2)),1);

$resultra = query_related_series($user, $series['id'], $series['author'], $num_of_genres_in_common, 24, TRUE);

if (mysqli_num_rows($resultra)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><?php echo CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE; ?></h2>
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
						<h2 class="section-title-main"><?php echo CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE; ?></h2>
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
require_once("../common.fansubs.cat/footer.inc.php");
?>
