<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");

validate_hentai();

$result = query_series_by_slug(!empty($_GET['slug']) ? $_GET['slug'] : '');
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
?>
							</div>
							<div class="series-tags"><?php echo htmlspecialchars($series['genres']); ?></div>
						</div>
					</div>
					<div class="section series-subheader">
						<div class="series-thumbnail-holder">
							<img class="series-thumbnail" src="<?php echo STATIC_URL; ?>/images/covers/<?php echo $series['id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['name']); ?>">
						</div>
						<div class="series-synopsis"><?php echo $synopsis; ?></div>
					</div>
					<div class="section">
						<h2 class="section-title-main section-title-with-table">Contingut</h2>
<?php
$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE ".(!empty($_GET['show_hidden']) ? '1' : 'v.is_hidden=0')." AND v.series_id=".$series['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count==0) {
?>
						<div class="warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest <?php echo $cat_config['items_string_s']; ?> encara no disposa de cap versió editada en català. És probable que l’estiguem afegint ara mateix. Torna d’aquí a una estona!</div>
						</div>
<?php
} else {
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

	if ($count>1) {
?>
						<div class="version-tab-container">
<?php
		$i=0;
		while ($version = mysqli_fetch_assoc($result)) {
?>
							<div class="version-tab<?php echo ($version_found ? $version['id']==$passed_version : $i==0) ? ' version-tab-selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
								<div class="status-<?php echo get_status($version['status']); ?> status-indicator-tab" title="<?php echo get_status_description($version['status']); ?>"></div>
								<div class="version-tab-text"><?php echo htmlspecialchars('Versió '.get_fansub_preposition_name($version['fansub_name'])); ?></div>
							</div>
<?php
			$i++;
		}
		mysqli_data_seek($result, 0);
?>
						</div>
<?php
	}

	$i=0;
	while ($version = mysqli_fetch_assoc($result)) {
?>
						<div class="version-content<?php echo $count>1 ? ' version-content-multi' : ''; ?><?php echo ($version_found ? $version['id']!=$passed_version : $i>0) ? ' hidden' : ''; ?>" id="version-content-<?php echo $version['id']; ?>">
<?php
		$position = 1;
		$resulte = query("SELECT e.*, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title, d.number division_number, d.name division_name FROM episode e LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".$version['id']." LEFT JOIN division d ON e.division_id=d.id WHERE e.series_id=".$series['id']." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, IFNULL(et.title,e.description) ASC");
		$episodes = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $row);
		}
		mysqli_free_result($resulte);

		if (count($episodes)>0) {
?>
							<div class="section-content">
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
				$result_episodes = query("SELECT f.* FROM file f WHERE ((f.episode_id IN (".implode(',',$ids).") AND f.version_id=".$version['id'].") OR (f.episode_id IN (".implode(',',$linked_ids).") AND f.version_id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=${version['id']})))) AND f.is_lost=0 ORDER BY f.id ASC");
				$division_available_episodes[] = mysqli_num_rows($result_episodes);
				mysqli_free_result($result_episodes);
			}

			if ($cat_config['items_type']!='manga' && count($divisions)<2) {
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
									<a onclick="$(this.parentNode.parentNode).find('.division').removeClass('hidden');$(this.parentNode.parentNode).find('.empty-divisions').addClass('hidden');">AAAA<?php echo $cat_config['more_divisions_available']; ?></a>
								</div>
<?php
					}
?>
								<details id="<?php echo $version['id'].'-'.$cat_config['division_name_lc']; ?>-<?php echo !empty($division['division_number']) ? floatval($division['division_number']) : 'altres'; ?>" class="division<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>"<?php echo ($version['show_expanded_divisions']==1 && $division_available_episodes[$index]>0) ? ' open' : ''; ?>>
<?php
					if ($cat_config['items_type']=='manga') {
?>
									<summary class="division_name"><?php echo !empty($division['division_number']) ? (($version['show_divisions']!=1 || (count($divisions)==2 && empty($last_division_number))) ? 'Volum únic' : (!empty($division['division_name']) ? $division['division_name'] : (count($divisions)>1 ? 'Volum '.floatval($division['division_number']) : 'Volum únic'))) : 'Altres'; ?><?php echo $division_available_episodes[$index]>0 ? '' : ' <small style="color: #888;">(no hi ha contingut disponible)</small>'; ?></summary>
<?php
					} else {
?>
									<summary class="division_name"><?php echo !empty($division['division_number']) ? (($version['show_divisions']!=1 || (count($divisions)==2 && empty($last_division_number))) ? 'Capítols normals' : (!empty($division['division_name']) ? $division['division_name'] : $cat_config['division_name'].' '.floatval($division['division_number']))) : 'Altres'; ?><?php echo $division_available_episodes[$index]>0 ? '' : ' <small style="color: #888;">(no hi ha contingut disponible)</small>'; ?></summary>
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
		$resulte = query("SELECT DISTINCT f.extra_name FROM file f WHERE version_id=".$version['id']." AND f.episode_id IS NULL ORDER BY extra_name ASC");
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
								<h2 class="section-title-main section-title-with-table">Contingut extra</h2>
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
								<div class="version-fansub-info">
									Aquesta versió ha estat subtitulada per <?php echo $version['fansub_name']; ?>.
								</div>
							</div>
						</div>
<?php
		$i++;
	}
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
