<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");

validate_hentai();

$result = query("SELECT s.*, YEAR(s.publish_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='".CATALOGUE_ITEM_TYPE."' AND slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}
define('PAGE_STYLE_TYPE', 'catalogue');

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

define('PAGE_TITLE', $series['name']);
define('PAGE_PATH', '/'.$series['slug']);
define('PAGE_DESCRIPTION', strip_tags($synopsis));
define('PAGE_PREVIEW_IMAGE', SITE_BASE_URL.'/preview/'.$series['slug'].'.jpg');

define('PAGE_IS_SERIES', TRUE);

require_once("../common.fansubs.cat/header.inc.php");
?>
				<input id="autoopen_version_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['v']) ? (int)$_GET['v'] : ''); ?>"></input>
				<input id="autoopen_file_id" type="hidden" value="<?php echo htmlspecialchars(isset($_GET['f']) ? (int)$_GET['f'] : ''); ?>"></input>
				<input id="seen_behavior" type="hidden" value="<?php echo 0; ?>"></input>
				<div class="series_header">
					<div class="img" style="background: url('<?php echo STATIC_URL; ?>/images/featured/<?php echo $series['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
					<div class="series_title_container">
						<h2 class="series_title"><?php echo htmlspecialchars($series['name']); ?></h2>
<?php
if (!empty($series['alternate_names'])) {
?>
						<div class="series_alternate_names"><?php echo htmlspecialchars($series['alternate_names']); ?></div>
<?php
}
?>
					</div>
				</div>
				<div class="flex mobilewrappable">
					<div class="series_sidebar">
						<div class="series_sidebar_inner">
							<div class="sidebar_thumbnail" style="background: url('<?php echo STATIC_URL; ?>/images/covers/<?php echo $series['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
							<h2 class="section-title">Fitxa tècnica</h2>
							<div class="sidebar_data">
<?php
if (!empty($series['publish_date'])) {
?>
								<div><span class="fa fa-fw fa-calendar dataicon"></span><strong>Any:</strong> <?php echo date('Y',strtotime($series['publish_date'])); ?></div>
<?php
}
if (!empty($series['author'])) {
?>
								<div><span class="fa fa-fw fa-book dataicon"></span><strong>Autor:</strong> <?php echo htmlspecialchars($series['author']); ?></div>
<?php
}
if (!empty($series['director'])) {
?>
								<div><span class="fa fa-fw fa-bullhorn dataicon"></span><strong>Director:</strong> <?php echo htmlspecialchars($series['director']); ?></div>
<?php
}
if (!empty($series['studio'])) {
?>
								<div><span class="fa fa-fw fa-video dataicon"></span><strong>Estudi:</strong> <?php echo htmlspecialchars($series['studio']); ?></div>
<?php
}
if (!empty($series['rating'])) {
?>
								<div><span class="fa fa-fw fa-star dataicon"></span><strong>Edat:</strong> <?php echo htmlspecialchars(get_rating($series['rating'])); ?></div>
<?php
}
if ($series['number_of_episodes']>1) {
?>
								<div><span class="fa fa-fw fa-ruler dataicon"></span><strong>Capítols:</strong> <?php echo $series['number_of_episodes'].' capítols'; ?></div>
<?php
}
if ($series['divisions']>1) {
	if (CATALOGUE_ITEM_TYPE=='manga') {
?>
								<div><span class="fa fa-fw fa-th-large dataicon"></span><strong>Volums:</strong> <?php echo $series['divisions'].' volums'; ?></div>
<?php
	} else {
?>
								<div><span class="fa fa-fw fa-th-large dataicon"></span><strong>Temporades:</strong> <?php echo $series['divisions'].' temporades'; ?></div>
<?php
	}
}
if (!empty($series['duration'])) {
?>
								<div><span class="fa fa-fw fa-clock dataicon"></span><strong>Durada:</strong> <?php echo $series['duration']; ?></div>
<?php
}
if (!empty($series['score'])) {
?>
								<div><span class="fa fa-fw fa-smile dataicon"></span><strong>Puntuació a MyAnimeList:</strong> <?php echo number_format($series['score'],2,","," "); ?>/10</div>
<?php
}
if (!empty($series['genres'])) {
?>
								<div><span class="fa fa-fw fa-tags dataicon"></span><strong>Gèneres:</strong> <?php echo htmlspecialchars($series['genres']); ?></div>
<?php
}
?>
							</div>
<?php
if (!empty($series['external_id'])) {
	if (CATALOGUE_ITEM_TYPE=='liveaction') {
?>
							<a class="mal-button" href="https://mydramalist.com/<?php echo $series['external_id']; ?>/" target="_blank"><span class="fa fa-th-list icon"></span>Mostra’n la fitxa a MyDramaList</a>
<?php
	} else {
?>
							<a class="mal-button" href="https://myanimelist.net/<?php echo $cat_config['items_type']; ?>/<?php echo $series['external_id']; ?>/" target="_blank"><span class="fa fa-th-list icon"></span>Mostra’n la fitxa a MyAnimeList</a>
<?php
	}
}
?>
							<a class="hitotsume-button" href="https://discord.com/invite/2Ksxb3wr3t" target="_blank"><span class="fab fa-discord icon"></span>Comenta-ho a HitotsumeCAT</a>
<?php
if (!empty($series['tadaima_id'])) {
?>
							<a class="tadaima-button" href="https://tadaima.cat/fil-t<?php echo $series['tadaima_id']; ?>.html" target="_blank"><span class="fa fa-comments icon"></span><?php echo get_tadaima_info($series['tadaima_id']); ?></a>
<?php
} else {
?>
							<a class="tadaima-button" href="https://tadaima.cat/posting.php?mode=post&f=<?php echo CATALOGUE_ITEM_TYPE=='manga' ? 9 : ($series['subtype']==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID ? 1 : 2 /* TODO */); ?>" target="_blank"><span class="fa fa-comments icon"></span>Comenta-ho a Tadaima.cat</a>
<?php
}
?>
						</div>
					</div>
					<div class="main_content">
						<div class="section">
							<h2 class="section-title">Sinopsi</h2>
							<div class="section-content">
								<div class="synopsis-content">
									<?php echo $synopsis; ?>

								</div>
								<div class="show-more hidden">
									<a>Mostra’n més...</a>
								</div>
							</div>
<?php
if ($series['has_licensed_parts']==1) {
?>
							<div class="section-content padding-top parts-licensed">
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Part d’aquest <?php echo $cat_config['items_string_s']; ?> ha estat llicenciat o editat oficialment en català. Se’n mostren només les parts no llicenciades.
							</div>
<?php
}
?>
						</div>
<?php
$result_unfiltered = query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE ".(!empty($_GET['show_hidden']) ? '1' : 'v.is_hidden=0')." AND v.series_id=".$series['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count_unfiltered = mysqli_num_rows($result_unfiltered);
mysqli_free_result($result_unfiltered);

$cookie_fansub_ids = array(); //TODO RESTORE THIS FROM v4: (empty($_GET['f']) ? get_cookie_blacklisted_fansub_ids() : array());

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot() && empty($_GET['f'])) ? " AND v.status<>5 AND v.status<>4" : "").((!empty($_COOKIE['show_missing']) || !empty($_GET['f'])) ? "" : " AND v.is_missing_episodes=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND (s.rating<>'XXX' OR s.rating IS NULL)" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE ".(!empty($_GET['show_hidden']) ? '1' : 'v.is_hidden=0')." AND v.series_id=".$series['id']."$cookie_extra_conditions GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count_unfiltered==0) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest <?php echo $cat_config['items_string_s']; ?> encara no disposa de cap versió editada en català. És probable que l’estiguem afegint ara mateix. Torna d’aquí a una estona!</div>
						</div>
<?php
} else if ($count==0) {
	if ($series['rating']=='XXX' && empty($_COOKIE['show_hentai'])) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest <?php echo $cat_config['items_string_s']; ?> conté contingut pornogràfic i és només per a majors d’edat. Si ets major d’edat i vols veure’l, activa l’opció de mostrar hentai a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	} else {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest <?php echo $cat_config['items_string_s']; ?> disposa d’alguna versió editada en català, però el teu filtre d’usuari impedeix mostrar-la. Pots canviar el filtre a la icona de configuració de la part superior de la pàgina, o mostrar-la temporalment.</div>
							<a class="force-display" href="?f=1">Mostra-la</a>
						</div>
<?php
	}
} else {
	if ($count!=$count_unfiltered) {
?>
						<div class="section warning-small">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Hi ha alguna altra versió d’aquest <?php echo $cat_config['items_string_s']; ?>. La pots veure canviant el teu filtre d’usuari a la part superior de la pàgina, o mostrar-la temporalment.</div>
							<a class="force-display" href="?f=1">Mostra-la</a>
						</div>
<?php
	}
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
						<div class="version_tab_container">
<?php
		$i=0;
		while ($version = mysqli_fetch_assoc($result)) {
?>
							<div class="version_tab<?php echo ($version_found ? $version['id']==$passed_version : $i==0) ? ' version_tab_selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
								<div class="status-<?php echo get_status($version['status']); ?> status-indicator-tab" title="<?php echo get_status_description($version['status']); ?>"></div>
								<div class="version_tab_text"><?php echo htmlspecialchars('Versió '.get_fansub_preposition_name($version['fansub_name'])); ?></div>
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
						<div class="version_content<?php echo $count>1 ? ' version_content_multi' : ''; ?><?php echo ($version_found ? $version['id']!=$passed_version : $i>0) ? ' hidden' : ''; ?>" id="version_content_<?php echo $version['id']; ?>">
<?php
		$resultf = query("SELECT f.*, vf.downloads_url, v.version_author FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON vf.version_id=v.id WHERE vf.version_id=".$version['id']." ORDER BY f.name ASC");
		$fansubs = array();
		while ($fansub = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, $fansub);
		}
		mysqli_free_result($resultf);

		$is_fandub = FALSE;
		$has_web = FALSE;
		$has_download = FALSE;
		foreach ($fansubs as $fansub) {
			if ($fansub['type']=='fandub') {
				$is_fandub = TRUE;
			}
			if (($fansub['is_historical']==0 && !empty($fansub['url'])) || ($fansub['is_historical']==1 && !empty($fansub['archive_url']))) {
				$has_web = TRUE;
			}
			if (!empty($fansub['downloads_url'])) {
				$has_download = TRUE;
			}
		}
		
		

		$plurals = array(
				"active" => array("Aquest web només recopila el material editat. L’autoria de la versió en català és del ".($is_fandub ? 'fandub' : 'fansub')." següent.".($has_web ? " Al seu web també trobaràs els fitxers originals amb màxima qualitat. Si t’agrada la seva feina, deixa-hi un comentari d’agraïment!" : ($has_download ? " Si vols, també pots baixar-ne els fitxers originals amb màxima qualitat." : "")), "Aquest web només recopila el material editat. L’autoria de la versió en català és dels ".($is_fandub ? 'fandub' : 'fansub')."s següents.".($has_web ? " Als seus webs també trobaràs els fitxers originals amb màxima qualitat. Si t’agrada la seva feina, deixa-hi un comentari d’agraïment!" : ($has_download ? " Si vols, també pots baixar-ne els fitxers originals amb màxima qualitat." : ""))),
				"abandoned" => array("Aquest ".$cat_config['items_string_s']." es considera abandonat pel ".($is_fandub ? 'fandub' : 'fansub').", segurament no se’n llançaran més capítols.","Aquest ".$cat_config['items_string_s']." es considera abandonat pels ".($is_fandub ? 'fandub' : 'fansub')."s, segurament no se’n llançaran més capítols."),
				"cancelled" => array("Aquest ".$cat_config['items_string_s']." ha estat cancel·lat pel ".($is_fandub ? 'fandub' : 'fansub').", no se’n llançaran més capítols.","Aquest ".$cat_config['items_string_s']." ha estat cancel·lat pels ".($is_fandub ? 'fandub' : 'fansub')."s, no se’n llançaran més capítols.")
		);
?>
							<div class="section">
								<div class="section-content fansub-info">
									<div><?php echo count($fansubs)>1 ? $plurals['active'][1] : $plurals['active'][0];?></div>
									<table class="fansub-list">
										<tbody>
<?php
		foreach ($fansubs as $fansub) {
?>
											<tr>
												<td class="fansub-icon"><img src="<?php echo $static_url; ?>/images/icons/<?php echo $fansub['id']; ?>.png" alt="" /></td>
												<td class="fansub-name"><?php echo !empty($fansub['url'] && $fansub['is_historical']==0) ? ('<a href="'.$fansub['url'].'" target="_blank">'.$fansub['name'].(($fansub['id']==28 && !empty($fansub['version_author'])) ? ' ('.$fansub['version_author'].')' : '').'</a>') : $fansub['name'].(($fansub['id']==28 && !empty($fansub['version_author'])) ? ' ('.$fansub['version_author'].')' : ''); ?><?php if ($fansub['status']==0 && $fansub['id']!=28) { echo '<span class="fansub-inactive"> (actualment inactiu)</span>'; } ?></td>
												<td class="fansub-links">
<?php
			if (!empty($fansub['downloads_url'])) {
				$url_arr=explode(';', $fansub['downloads_url']);
				foreach ($url_arr as $url) {
					if (preg_match(REGEXP_DL_LINK,$url)) {
						echo ' <a class="fansub-downloads" data-url="'.htmlspecialchars(base64_encode($url)).'"><span class="fa fa-fw fa-download mobileicon"></span><span class="mobilehide">'.(preg_match(REGEXP_MEGA,$url) ? 'Baixada' : 'Baixades').'</span></a>';
					} else {
						echo ' <a class="fansub-series-page" href="'.$url.'" target="_blank"><span class="fa fa-fw fa-download mobileicon"></span><span class="mobilehide">Baixades</span></a>';
					}
				}
			}
			if ($fansub['is_historical']==0 && !empty($fansub['url'])) {
				echo ' <a class="fansub-website" href="'.$fansub['url'].'" target="_blank"><span class="fa fa-fw fa-globe mobileicon"></span><span class="mobilehide">Web</span></a>';
			} else if ($fansub['is_historical']==1 && !empty($fansub['archive_url'])) {
				echo ' <a class="fansub-website" href="'.$fansub['archive_url'].'" target="_blank"><span class="fa fa-fw fa-globe mobileicon"></span><span class="mobilehide">Web històrica</span></a>';
			}
			if (!empty($fansub['discord_url'])) {
				echo ' <a class="fansub-discord" href="'.$fansub['discord_url'].'" target="_blank"><span class="fab fa-fw fa-discord mobileicon"></span><span class="mobilehide">Discord</span></a>';
			}
			if (!empty($fansub['mastodon_url'])) {
				echo ' <a class="fansub-mastodon" href="'.$fansub['mastodon_url'].'" target="_blank"><span class="fab fa-fw fa-mastodon mobileicon"></span><span class="mobilehide">Mastodon</span></a>';
			}
			if (!empty($fansub['twitter_url'])) {
				echo ' <a class="fansub-twitter" href="'.$fansub['twitter_url'].'" target="_blank"><span class="fab fa-fw fa-twitter mobileicon"></span><span class="mobilehide">Twitter</span></a>';
			}
?>
												</td>
											</tr>
<?php
		}
?>
										</tbody>
									</table>
								</div>
							</div>
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
							<div class="section">
								<h2 class="section-title">Contingut</h2>
<?php
			if ($version['status']==4) {
?>
								<div class="section-content padding-bottom cancelled-warning">
									<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span><?php echo count($fansubs)>1 ? $plurals['abandoned'][1] : $plurals['abandoned'][0]; ?>

								</div>
<?php
			} else if ($version['status']==5) {
?>
								<div class="section-content padding-bottom cancelled-warning">
									<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span><?php echo count($fansubs)>1 ? $plurals['cancelled'][1] : $plurals['cancelled'][0]; ?>

								</div>
<?php
			}
			if ($version['is_missing_episodes']==1) {
?>
								<div class="section-content padding-bottom episodes-missing">
									<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Hi ha capítols editats que no tenen cap enllaç vàlid. Si els tens o saps on trobar-los, <a class="version-missing-links-link">contacta amb nosaltres</a>.
								</div>
<?php
			}
			if ($fansub['type']=='fandub') {
?>
								<div class="section-content padding-bottom fandub-warning">
									<span class="fa fa-fw fa-microphone icon-pr"></span>Aquest <?php echo $cat_config['items_string_s']; ?> ha estat doblat per fans. L’àudio és únicament en català i no disposa de subtítols.
								</div>
<?php
			}
			if ($series['number_of_episodes']==-1) {
?>
							<div class="section-content padding-bottom series-on-air">
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Aquest <?php echo $cat_config['items_string_s']; ?> està <?php echo $cat_config['being_published']; ?>. Hi ha la possibilitat que acabi tenint més capítols que els que hi ha a la llista.
							</div>
<?php
			}
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
										<thead>
											<tr>
												<th class="episode-seen-head">Vist</th>
												<th>Títol</th>
<?php
					if ($cat_config['items_type']!='manga') {
?>
													<th class="episode-info-head right">Notes</th></tr>
<?php
					}
?>
										</thead>
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
											<a onclick="$(this.parentNode.parentNode).find('.division').removeClass('hidden');$(this.parentNode.parentNode).find('.empty-divisions').addClass('hidden');"><?php echo $cat_config['more_divisions_available']; ?></a>
										</div>
	<?php
					}
?>
									<details id="<?php echo $cat_config['division_name_lc']; ?>-<?php echo !empty($division['division_number']) ? floatval($division['division_number']) : 'altres'; ?>" class="division<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>"<?php echo ($version['show_expanded_divisions']==1 && $division_available_episodes[$index]>0) ? ' open' : ''; ?>>
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
											<table class="episode-table" rules="rows">
												<thead>
													<tr>
														<th class="episode-seen-head">Vist</th>
														<th>Títol</th>
<?php
						if ($cat_config['items_type']!='manga') {
?>
														<th class="episode-info-head right">Notes</th></tr>
<?php
						}
?>
												</thead>
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
?>
										</div>
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

		if (count($extras)>0) {
?>
									<details class="extra-content<?php echo count($divisions)<2 ? ' extra-content-single-division' : ''; ?>"<?php echo $version['show_expanded_extras']==1 ? ' open' : ''; ?>>
										<summary class="division_name">Contingut extra</summary>
										<div style="width: 100%;">
											<table class="episode-table" rules="rows">
												<thead>
													<tr>
														<th class="episode-seen-head">Vist</th>
														<th>Nom</th>
<?php
			if ($cat_config['items_type']!='manga') {
?>
														<th class="episode-info-head right">Notes</th></tr>
<?php
			}
?>
												</thead>
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
									</details>
<?php
		}
?>
								</div>
							</div>
						</div>
<?php
		$i++;
	}
}
?>
					</div>
				</div>
				<div class="bottom-recommendations">
<?php
$num_of_genres = count(explode(', ', $series['genres']));
$num_of_genres_in_common = max(intval(round($num_of_genres/2)),1);

$resultra = query_related_series($user, $series['id'], $series['author'], $num_of_genres_in_common, 24, TRUE);

if (mysqli_num_rows($resultra)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><?php echo CATALOGUE_ITEM_TYPE=='liveaction' ? 'Continguts d’imatge real amb temàtiques en comú' : (CATALOGUE_ITEM_TYPE=='anime' ? 'Animes'.(SITE_IS_HENTAI ? ' hentai' : '').' amb temàtiques en comú' : 'Mangues'.(SITE_IS_HENTAI ? ' hentai' : '').' amb temàtiques en comú'); ?></h2>
						<div class="section-content carousel">
<?php
	while ($row = mysqli_fetch_assoc($resultra)) {
?>
							<div<?php echo isset($row['best_status']) ? ' class="status-'.get_status($row['best_status']).'"' : ''; ?>>
<?php
		print_carousel_item($row, FALSE);
?>
							</div>
<?php
	}
?>

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
						<div class="section-content carousel">
<?php
	while ($row = mysqli_fetch_assoc($resultrm)) {
?>
							<div<?php echo isset($row['best_status']) ? ' class="status-'.get_status($row['best_status']).'"' : ''; ?>>
<?php
		print_carousel_item($row, FALSE);
?>
							</div>
<?php
	}
?>

						</div>
					</div>
<?php
}

mysqli_free_result($resultrm);
?>
				</div>
<?php
mysqli_free_result($result);
require_once("../common.fansubs.cat/footer.inc.php");
?>
