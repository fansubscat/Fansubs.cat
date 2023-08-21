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
					<input id="autoopen_version_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['v']) ? (int)$_GET['v'] : ''); ?>">
					<input id="autoopen_file_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['f']) ? (int)$_GET['f'] : ''); ?>">
					<input id="seen_behavior" type="hidden" value="<?php echo 0; ?>">
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
	$additional_data.=$series['divisions'].(CATALOGUE_ITEM_TYPE=='manga' ? ' volums' : ' temporades');
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
	$additional_data .= number_format($series['score'],2,","," ").' a '.(CATALOGUE_ITEM_TYPE=='liveaction' ? 'MyDramaList' : 'MyAnimeList');
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
						</div>
						<div class="series-synopsis"><?php echo $synopsis; ?></div>
					</div>
					<div class="section">
<?php
$result = query_series_data_for_series_page($series['id']);

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
?>
							<div class="version-tab<?php echo ($version_found ? $version['id']==$passed_version : $i==0) ? ' version-tab-selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
								<div class="version-fansub-icons"><?php echo get_fansub_icons($version['fansub_info'], get_prepared_versions($version['fansub_info']), $version['id']); ?></div>
								<div class="version-tab-text"><?php echo htmlspecialchars('Versió '.get_fansub_preposition_name($version['fansub_name'])); ?></div>
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
						<div class="version-content<?php echo $count>1 ? ' version-content-multi' : ''; ?><?php echo ($version_found ? $version['id']!=$passed_version : $i>0) ? ' hidden' : ''; ?>" id="version-content-<?php echo $version['id']; ?>">
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
						'division_number' => $last_division_number,
						'division_name' => $last_division_name,
						'episodes' => $current_division_episodes
					));
				}
				$last_division_number=$row['division_number'];
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
								<h2 class="section-title-main<?php if (count($divisions)>1) { echo " section-title-with-table"; } ?>">Contingut</h2>
<?php

		if (count($divisions)<2) {
			foreach ($divisions as $division) {
?>
								<table class="episode-table">
									<tbody>
<?php
				foreach ($division['episodes'] as $episode) {
					print_episode($version['fansub_name'], $episode, $version['id'], $series, $version, $position);
					$position++;
				}
?>
									</tbody>
								</table>
<?php
			}
		} else { //Multiple divisions
			foreach ($divisions as $index => $division) {
				$is_inside_empty_batch = ($division_available_episodes[$index]==0 && (($index>0 && $division_available_episodes[$index-1]==0) || ($index<(count($division_available_episodes)-1) && $division_available_episodes[$index+1]==0)));
				$is_first_in_empty_batch = $is_inside_empty_batch && ($index==0 || ($index>0 && $division_available_episodes[$index-1]!=0));

				if ($is_first_in_empty_batch && $version['show_unavailable_episodes']==1) {
?>
								<div class="empty-divisions"<?php echo ($index==0 ? ' style="margin-top: 0;"' : '') ?>>
									<a onclick="$(this.parentNode.parentNode).find('.division').removeClass('hidden');$(this.parentNode.parentNode).find('.empty-divisions').addClass('hidden');">Hi ha més temporades/volums sense capítols disponibles. Prem aquí per a mostrar-les totes.</a>
								</div>
<?php
				}
?>
								<details id="version-<?php echo $version['id']; ?>-division-<?php echo !empty($division['division_number']) ? floatval($division['division_number']) : 'altres'; ?>" class="division<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>"<?php echo ($version['show_expanded_divisions']==1 && $division_available_episodes[$index]>0) ? ' open' : ''; ?>>
<?php
				if (CATALOGUE_ITEM_TYPE=='manga') {
?>
									<summary class="division-header<?php echo $division_available_episodes[$index]>0 ? '' : ' division-unavailable'; ?>"><div class="division-header-inner"><img class="division-cover" src="<?php echo file_exists(STATIC_DIRECTORY.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg') ? STATIC_URL.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg' : STATIC_URL.'/images/covers/'.$series['id'].'.jpg'; ?>"><div class="division-title"><div class="division-title-collapsable"><?php echo !empty($division['division_number']) ? (($version['show_divisions']!=1 || (count($divisions)==2 && empty($last_division_number))) ? 'Volum únic' : (!empty($division['division_name']) ? $division['division_name'] : (count($divisions)>1 ? 'Volum '.floatval($division['division_number']) : 'Volum únic'))) : 'Altres'; ?><i class="division-arrow fa fa-fw fa-angle-right"></i></div><span class="division-elements"><?php echo $division_available_episodes[$index]>0 ? ($division_available_episodes[$index]==1 ? '1 capítol disponible' : $division_available_episodes[$index].' capítols disponibles') : 'No hi ha cap capítol disponible'; ?></span></div></div></summary>
<?php
				} else {
?>
									<summary class="division-header<?php echo $division_available_episodes[$index]>0 ? '' : ' division-unavailable'; ?>"><div class="division-header-inner"><img class="division-cover" src="<?php echo file_exists(STATIC_DIRECTORY.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg') ? STATIC_URL.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg' : STATIC_URL.'/images/covers/'.$series['id'].'.jpg'; ?>"></i><div class="division-title"><div class="division-title-collapsable"><?php echo !empty($division['division_number']) ? (($version['show_divisions']!=1 || (count($divisions)==2 && empty($last_division_number))) ? 'Capítols normals' : (!empty($division['division_name']) ? $division['division_name'] : 'Temporada '.floatval($division['division_number']))) : 'Altres'; ?><i class="division-arrow fa fa-fw fa-angle-right"></i></div><span class="division-elements"><?php echo $division_available_episodes[$index]>0 ? ($division_available_episodes[$index]==1 ? '1 capítol disponible' : $division_available_episodes[$index].' capítols disponibles') : 'No hi ha cap capítol disponible'; ?></span></div></div></summary>
<?php
				}
?>
									<div class="division-container">
<?php
				if (file_exists($static_directory.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg')) {
?>
										<div class="division-image-container">
											<img class="division-cover" src="<?php echo $static_url.'/images/divisions/'.$version['id'].'_'.$division['division_id'].'.jpg'; ?>" alt="">
										</div>
<?php
				}
				if ($division_available_episodes[$index]>0 || $version['show_unavailable_episodes']==1) {
?>
										<div style="width: 100%;">
											<table class="episode-table">
												<tbody>
<?php
					foreach ($division['episodes'] as $episode) {
						print_episode($version['fansub_name'], $episode, $version['id'], $series, $version, $position);
						$position++;
					}
?>
												</tbody>
											</table>
										</div>
									</div>
<?php

				}
?>
								</details>
<?php
			}
		}
	}
	$resulte = query_extras_by_version_id($version['id']);
	$extras = array();
	while ($row = mysqli_fetch_assoc($resulte)) {
		array_push($extras, $row);
	}
	mysqli_free_result($resulte);
?>
							</div>
<?php

	if (count($extras)>0) {
?>
							<div class="section-content extra-content">
								<h2 class="section-title-main section-title-with-table">Extres</h2>
								<div style="width: 100%;">
									<table class="episode-table">
										<tbody>
<?php
		foreach ($extras as $row) {
			print_extra($version['fansub_name'], $row, $version['id'], $series, $position);
			$position++;
		}
?>
										</tbody>
									</table>
								</div>
							</div>
<?php
	}
?>
							<div class="section-content extra-content">
								<h2 class="section-title-main">Quant a aquesta versió</h2>
								<div class="version-fansub-info">Aquesta versió en català ha estat possible gràcies als fansubs següents. No t’oblidis d’agrair-los la feina.</div>
								<div class="version-fansub-list-and-rating">
									<div class="version-fansub-list">
<?php
	$fansubs = query_fansubs_by_version_id($version['id']);
	foreach ($fansubs as $fansub) {
?>
									<div class="version-fansub-element">
										<img class="version-fansub-image">
										<div class="version-fansub-name"><?php echo $fansub['name']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['url']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['twitter_url']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['mastodon_url']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['discord_url']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['archive_url']; ?></div>
										<div class="version-fansub-name"><?php echo $fansub['type']; ?></div>
									</div>
<?php
	}
?>
									</div>
									<div class="version-fansub-rating">
										<div class="version-fansub-rating-title">Valora aquesta versió</div>
										<div class="version-fansub-rating-buttons">
											<span class="version-fansub-rating-positive fa fa-fw fa-thumbs-up"></span>
											<span class="version-fansub-rating-negative fa fa-fw fa-thumbs-down"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
<?php
	$i++;
}
?>
					</div>
<?php
if (!empty($series['tadaima_id'])) {
	$tadaima_posts = get_tadaima_info($series['tadaima_id']);
?>
					<div class="section">
						<h2 class="section-title-main">Comentaris a Tadaima.cat</h2>
						<div class="section-content">
<?php
	$num_posts = count($tadaima_posts);
	if ($num_posts>5) {
		$tadaima_posts = array_slice($tadaima_posts, -5);
	}
?>
							<div class="tadaima-explanation">Hi ha <b><?php echo $num_posts==1 ? '1 missatge' : $num_posts.' missatges'; ?></b> sobre aquest contingut a Tadaima.cat.<?php echo $num_posts>5 ? ' Aquests en són els darrers, però pots veure’ls tots prement el botó inferior.' : ''; ?> Recorda que la comunitat de Tadaima.cat no té cap vinculació amb Fansubs.cat.</div>
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
							<a class="normal-button tadaima-cta" href="https://tadaima.cat/fil-t<?php echo $series['tadaima_id']; ?>.html" target="_blank"><i class="fa fa-fw fa-comment-dots"></i> Digues-hi la teva a Tadaima.cat</a>
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
						<h2 class="section-title-main"><?php echo CATALOGUE_ITEM_TYPE=='liveaction' ? 'Continguts d’imatge real amb temàtiques en comú' : (CATALOGUE_ITEM_TYPE=='anime' ? 'Animes'.(SITE_IS_HENTAI ? ' hentai' : '').' amb temàtiques en comú' : 'Mangues'.(SITE_IS_HENTAI ? ' hentai' : '').' amb temàtiques en comú'); ?></h2>
						<div class="section-content carousel swiper">
							<div class="swiper-wrapper">
<?php
	while ($row = mysqli_fetch_assoc($resultra)) {
?>
								<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']) : ''; ?> swiper-slide">
<?php
		print_carousel_item($row, FALSE);
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
						<h2 class="section-title-main"><?php echo SITE_IS_HENTAI ? (CATALOGUE_ITEM_TYPE=='anime' ? 'Mangues hentai amb temàtiques en comú' : 'Animes hentai amb temàtiques en comú') : "Altres continguts amb temàtiques en comú"; ?></h2>
						<div class="section-content carousel swiper">
							<div class="swiper-wrapper">
<?php
	while ($row = mysqli_fetch_assoc($resultrm)) {
?>
								<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']) : ''; ?> swiper-slide">
<?php
		print_carousel_item($row, FALSE);
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
