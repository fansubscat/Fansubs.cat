<?php
require_once("db.inc.php");
require_once("parsedown.inc.php");

function get_fansub_with_url($fansub) {
	if ($fansub['name']=='Fansub independent') {
		return "<strong>un fansub independent</strong>";
	} else if (!empty($fansub['url'])) {
		return '<strong><a href="'.htmlspecialchars($fansub['url']).'" target="_blank">'.htmlspecialchars($fansub['name']).'</a></strong>';
	} else {
		return '<strong>'.htmlspecialchars($fansub['name']).'</strong>';
	}
}

function apply_sort($order_type, $episodes) {
	switch ($order_type){
		case 1: // Alphabetic strict sort
			$titles = array_column($episodes, 'title');
			$numbers = array_column($episodes, 'number');
			array_multisort($titles, SORT_ASC, SORT_LOCALE_STRING|SORT_FLAG_CASE, $numbers, SORT_ASC, SORT_NUMERIC, $episodes);

			// Move empty titles to last
			while(reset($titles) == '') {
				$k = key($titles);
				unset($titles[$k]); // remove empty element from beginning of array
				$titles[$k] = ''; // add it to end of array
				$v = reset($episodes);
				$k = key($episodes);
				unset($episodes[$k]);
				$episodes[$k] = $v;
			}
			return $episodes;
		case 2: // Alphabetic natural sort
			$titles = array_column($episodes, 'title');
			$numbers = array_column($episodes, 'number');
			array_multisort($titles, SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $numbers, SORT_ASC, SORT_NUMERIC, $episodes);

			// Move empty titles to last
			while(reset($titles) == '') {
				$k = key($titles);
				unset($titles[$k]); // remove empty element from beginning of array
				$titles[$k] = ''; // add it to end of array
				$v = reset($episodes);
				$k = key($episodes);
				unset($episodes[$k]);
				$episodes[$k] = $v;
			}
			return $episodes;
		case 0: //Normal sort - already sorted from the database
		default:
			return $episodes;
	}
}

$result = query("SELECT s.*, YEAR(s.air_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT ss.id) FROM season ss WHERE ss.series_id=s.id AND ss.episodes>0) seasons FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$header_page_title=$series['name'];

$header_tab=$_GET['page'];

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

$header_social = array(
	'title' => $series['name'].' | Fansubs.cat - Anime en català',
	'url' => 'https://anime.fansubs.cat/'.($series['type']=='movie' ? 'films/' : 'series/').$series['slug'],
	'description' => strip_tags($synopsis),
	'image' => 'https://anime.fansubs.cat/images/series/'.$series['id'].'.jpg'
);

$header_series_page=TRUE;

require_once('header.inc.php');
?>
				<div class="series_header">
					<div class="img" style="background: url('/images/featured/<?php echo $series['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
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
							<div class="sidebar_thumbnail" style="background: url('/images/series/<?php echo $series['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
							<h2 class="section-title">Fitxa tècnica</h2>
							<div class="sidebar_data">
<?php
if (!empty($series['air_date'])) {
?>
								<div><span class="fa fa-fw fa-calendar dataicon"></span><strong>Any:</strong> <?php echo date('Y',strtotime($series['air_date'])); ?></div>
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
if ($series['episodes']>1) {
?>
								<div><span class="fa fa-fw fa-ruler dataicon"></span><strong>Capítols:</strong> <?php echo $series['episodes'].' capítols'; ?></div>
<?php
}
if ($series['seasons']>1) {
?>
								<div><span class="fa fa-fw fa-th-large dataicon"></span><strong>Temporades:</strong> <?php echo $series['seasons'].' temporades'; ?></div>
<?php
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
if (!empty($series['myanimelist_id'])) {
?>
							<a class="mal-button" href="https://myanimelist.net/anime/<?php echo $series['myanimelist_id']; ?>/" target="_blank"><span class="fa fa-th-list icon"></span>Mostra'n la fitxa a MyAnimeList</a>
<?php
}
if (!empty($series['tadaima_id'])) {
?>
							<a class="tadaima-button" href="https://tadaima.cat/fil-t<?php echo $series['tadaima_id']; ?>.html" target="_blank"><span class="fa fa-comments icon"></span><?php echo get_tadaima_info($series['tadaima_id']); ?></a>
<?php
} else {
	if ($series['type']=='movie') {
?>
							<a class="tadaima-button" href="https://tadaima.cat/posting.php?mode=post&f=14" target="_blank"><span class="fa fa-comments icon"></span>Comenta-ho a Tadaima.cat</a>
<?php
	} else {
?>
							<a class="tadaima-button" href="https://tadaima.cat/posting.php?mode=post&f=10" target="_blank"><span class="fa fa-comments icon"></span>Comenta-ho a Tadaima.cat</a>
<?php
	}
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
									<a>Mostra'n més...</a>
								</div>
							</div>
<?php
if ($series['has_licensed_parts']==1) {
?>
							<div class="section-content padding-top parts-licensed">
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Part d'aquest anime ha estat llicenciat o editat oficialment en català. Se'n mostren només les parts no llicenciades.
							</div>
<?php
}
?>
						</div>
<?php
$result_unfiltered = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.series_id=".$series['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count_unfiltered = mysqli_num_rows($result_unfiltered);
mysqli_free_result($result_unfiltered);

$cookie_fansub_ids = (empty($_GET['f']) ? get_cookie_fansub_ids() : array());

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot() && empty($_GET['f'])) ? " AND v.status<>5 AND v.status<>4" : "").((!empty($_COOKIE['show_missing']) || !empty($_GET['f'])) ? "" : " AND v.episodes_missing=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND (s.rating<>'XXX' OR s.rating IS NULL)" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE v.series_id=".$series['id']."$cookie_extra_conditions GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count_unfiltered==0) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest anime encara no disposa de cap versió editada en català. És probable que l'estiguem afegint ara mateix. Torna d'aquí a una estona!</div>
						</div>
<?php
} else if ($count==0) {
	if ($series['rating']=='XXX' && empty($_COOKIE['show_hentai'])) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest anime és només per a majors d'edat. Si ets major d'edat i vols veure'l, activa l'opció de mostrar hentai a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	} else {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest anime disposa d'alguna versió editada en català, però el teu filtre d'usuari impedeix mostrar-la. Pots canviar el filtre a la icona de configuració de la part superior de la pàgina, o mostrar-la temporalment.</div>
							<a class="force-display" href="?f=1">Mostra-la</a>
						</div>
<?php
	}
} else {
	if ($count!=$count_unfiltered) {
?>
						<div class="section warning-small">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Hi ha alguna altra versió d'aquest anime. La pots veure canviant el teu filtre d'usuari a la part superior de la pàgina, o mostrar-la temporalment.</div>
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
		$resultf = query("SELECT f.*, vf.downloads_url FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.version_id=".$version['id']." ORDER BY f.name ASC");
		$fansubs = array();
		while ($fansub = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, $fansub);
		}
		mysqli_free_result($resultf);

		$plurals = array(
				"active" => array("Aquest web només recopila el material editat. L'autoria de la versió en català és del grup següent. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! Al seu web també trobaràs els fitxers originals amb màxima qualitat.", "Aquest web només recopila el material editat. L'autoria de la versió en català és dels grups següents. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! Al seu web també trobaràs els fitxers originals amb màxima qualitat."),
				"abandoned" => array("Aquest anime es considera abandonat pel fansub, segurament no se'n llançaran més capítols.","Aquest anime es considera abandonat pels fansubs, segurament no se'n llançaran més capítols."),
				"cancelled" => array("Aquest anime ha estat cancel·lat pel fansub, no se'n llançaran més capítols.","Aquest anime ha estat cancel·lat pels fansubs, no se'n llançaran més capítols.")
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
												<td class="fansub-icon"><img src="https://www.fansubs.cat/images/fansub_icons/<?php echo $fansub['id']; ?>.png" alt="" /></td>
												<td class="fansub-name"><?php echo !empty($fansub['url'] && $fansub['historical']==0) ? ('<a href="'.$fansub['url'].'" target="_blank">'.$fansub['name'].'</a>') : $fansub['name']; ?><?php if ($fansub['status']==0 && $fansub['name']!='Fansub independent') { echo '<span class="fansub-inactive"> (actualment inactiu)</span>'; } ?></td>
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
			if ($fansub['historical']==0 && !empty($fansub['url'])) {
				echo ' <a class="fansub-website" href="'.$fansub['url'].'" target="_blank"><span class="fa fa-fw fa-globe mobileicon"></span><span class="mobilehide">Web</span></a>';
			} else if ($fansub['historical']==1 && !empty($fansub['archive_url'])) {
				echo ' <a class="fansub-website" href="'.$fansub['archive_url'].'" target="_blank"><span class="fa fa-fw fa-globe mobileicon"></span><span class="mobilehide">Web històrica</span></a>';
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
		$resulte = query("SELECT e.*, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.name,et.title)) title, ss.number season_number, ss.name season_name FROM episode e LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".$version['id']." LEFT JOIN season ss ON e.season_id=ss.id WHERE e.series_id=".$series['id']." ORDER BY ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, IFNULL(et.title,e.name) ASC");
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
			if ($version['episodes_missing']==1) {
?>
								<div class="section-content padding-bottom episodes-missing">
									<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Hi ha capítols editats que no tenen cap enllaç vàlid. Si els tens o saps on trobar-los, <a class="version-missing-links-link">contacta'ns</a>.
								</div>
<?php
			}
			if ($fansub['type']=='fandub') {
?>
								<div class="section-content padding-bottom fandub-warning">
									<span class="fa fa-fw fa-microphone icon-pr"></span>Aquest anime ha estat doblat per fans. L'àudio és únicament en català i no disposa de subtítols.
								</div>
<?php
			}
			if ($series['episodes']==-1) {
?>
							<div class="section-content padding-bottom series-on-air">
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Aquesta sèrie encara està en emissió. És possible que tingui més capítols que els que hi ha a la llista.
							</div>
<?php
			}
?>
								<div class="section-content">
<?php
			$seasons = array();
			$last_season_number = -1;
			$last_season_id = -1;
			$last_season_name = "";
			$current_season_episodes = array();
			foreach ($episodes as $row) {
				if ($row['season_number']!=$last_season_number && ($version['show_seasons']==1 || empty($row['season_number']))){
					if ($last_season_number!=-1) {
						array_push($seasons, array(
							'season_id' => $last_season_id,
							'season_number' => $last_season_number,
							'season_name' => $last_season_name,
							'episodes' => apply_sort($version['order_type'],$current_season_episodes)
						));
					}
					$last_season_number=$row['season_number'];
					$last_season_id=$row['season_id'];
					$last_season_name=$row['season_name'];
					$current_season_episodes = array();
				}

				array_push($current_season_episodes, $row);
			}
			array_push($seasons, array(
				'season_id' => $last_season_id,
				'season_number' => $last_season_number,
				'season_name' => $last_season_name,
				'episodes' => apply_sort($version['order_type'],$current_season_episodes)
			));

			$season_available_episodes=array();

			foreach ($seasons as $season) {
				$ids=array(-1);
				foreach ($season['episodes'] as $episode) {
					$ids[]=$episode['id'];
				}
				$result_episodes = query("SELECT l.* FROM link l WHERE l.episode_id IN (".implode(',',$ids).") AND l.lost=0 AND l.version_id=".$version['id']." ORDER BY l.id ASC");
				$season_available_episodes[] = mysqli_num_rows($result_episodes);
				mysqli_free_result($result_episodes);
			}

			if (count($seasons)<2) {
				foreach ($seasons as $season) {
?>
									<table class="episode-table">
										<thead>
											<tr>
												<th class="episode-seen-head">Vist</th>
												<th>Nom</th>
												<th class="episode-info-head right">Notes</th></tr>
										</thead>
										<tbody>
<?php
					foreach ($season['episodes'] as $episode) {
						print_episode($version['fansub_name'], $episode, $version['id'], $series, $version);
					}
?>
										</tbody>
									</table>
<?php
				}
			} else { //Multiple seasons

				foreach ($seasons as $index => $season) {
					$is_inside_empty_batch = ($season_available_episodes[$index]==0 && (($index>0 && $season_available_episodes[$index-1]==0) || ($index<(count($season_available_episodes)-1) && $season_available_episodes[$index+1]==0)));
					$is_first_in_empty_batch = $is_inside_empty_batch && ($index==0 || ($index>0 && $season_available_episodes[$index-1]!=0));

					if ($is_first_in_empty_batch && $version['show_unavailable_episodes']==1) {
	?>
										<div class="empty-seasons"<?php echo ($index==0 ? ' style="margin-top: 0;"' : '') ?>>
											<a onclick="$(this.parentNode.parentNode).find('.season').removeClass('hidden');$(this.parentNode.parentNode).find('.empty-seasons').addClass('hidden');">Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.</a>
										</div>
	<?php
					}
?>
									<details class="season<?php echo $is_inside_empty_batch ? ' hidden' : ''; ?>"<?php echo ($version['show_expanded_seasons']==1 && $season_available_episodes[$index]>0) ? ' open' : ''; ?>>
										<summary class="season_name"><?php echo !empty($season['season_number']) ? (($version['show_seasons']!=1 || (count($seasons)==2 && empty($last_season_number))) ? 'Capítols normals' : ('Temporada '.$season['season_number'].(!empty($season['season_name']) ? ': '.$season['season_name'] : ''))) : 'Altres'; ?><?php echo $season_available_episodes[$index]>0 ? '' : ' <small style="color: #888;">(no hi ha contingut disponible)</small>'; ?></summary>
										<table class="episode-table" rules="rows">
											<thead>
												<tr>
													<th class="episode-seen-head">Vist</th>
													<th>Nom</th>
													<th class="episode-info-head right">Notes</th></tr>
											</thead>
											<tbody>
<?php
					foreach ($season['episodes'] as $episode) {
						print_episode($version['fansub_name'], $episode, $version['id'], $series, $version);
					}
?>
											</tbody>
										</table>
									</details>
<?php
				}
			}
		}
		$resulte = query("SELECT DISTINCT l.extra_name FROM link l WHERE version_id=".$version['id']." AND l.episode_id IS NULL ORDER BY extra_name ASC");
		$extras = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			array_push($extras, $row);
		}
		mysqli_free_result($resulte);

		if (count($extras)>0) {
?>
									<details class="extra-content<?php echo count($seasons)<2 ? ' extra-content-single-season' : ''; ?>"<?php echo $version['show_expanded_extras']==1 ? ' open' : ''; ?>>
										<summary class="season_name">Contingut extra</summary>
										<table class="episode-table" rules="rows">
											<thead>
												<tr>
													<th class="episode-seen-head">Vist</th>
													<th>Nom</th>
													<th class="episode-info-head right">Notes</th></tr>
											</thead>
											<tbody>
<?php
			foreach ($extras as $row) {
				print_extra($version['fansub_name'], $row, $version['id'], $series);
			}
?>
											</tbody>
										</table>
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
//Begin copy from index.php
$max_items=24;
$base_query="SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.links_updated=MAX(v.links_updated) AND v.series_id=s.id LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT f.type ORDER BY f.type SEPARATOR '|') fansub_type, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.links_updated) last_updated, (SELECT COUNT(ss.id) FROM season ss WHERE ss.series_id=s.id) seasons, s.episodes episodes, (SELECT MAX(ls.created) FROM link ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id) last_link_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";
//End copy from index.php
//1. Related, 2. Same author, 3. Half of genres or more in common
$num_of_genres = count(explode(', ', $series['genres']));
$related_query="SELECT rs.related_series_id id, s.name FROM related_series rs LEFT JOIN series s ON rs.related_series_id=s.id WHERE rs.series_id=".$series['id']." UNION SELECT id, NULL FROM series WHERE id<>".$series['id']." AND author='".escape($series['author'])."' UNION (SELECT series_id id, NULL FROM rel_series_genre WHERE series_id<>".$series['id']." GROUP BY series_id HAVING COUNT(CASE WHEN genre_id IN (SELECT genre_id FROM rel_series_genre WHERE series_id=".$series['id'].") THEN 1 END)>=".max($num_of_genres>=2 ? 2 : 1,intval(round($num_of_genres/2))).") ORDER BY name IS NULL ASC, name ASC, RAND() LIMIT $max_items";
$resultin = query($related_query);
$in = array(-1);
while ($row = mysqli_fetch_assoc($resultin)) {
	$in[]=$row['id'];
}
mysqli_free_result($resultin);
$resultra = query($base_query . " WHERE s.id IN (".implode(',',$in).") GROUP BY s.id ORDER BY FIELD(s.id,".implode(',',$in).") ASC");

if (mysqli_num_rows($resultra)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><span class="iconsm fa fa-fw fa-tv"></span> Animes recomanats</h2>
						<h3 class="section-subtitle">Si t'agrada aquest anime, és possible que també t'agradin els d'aquesta llista:</h3>
						<div class="section-content carousel">
<?php
	while ($row = mysqli_fetch_assoc($resultra)) {
?>
							<div class="status-<?php echo get_status($row['best_status']); ?>">
<?php
		print_carousel_item_anime($row, 'related-anime', FALSE, FALSE);
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

//Begin copy from index.php
$max_items=24;
$base_query="SELECT s.*, (SELECT nv.id FROM manga_version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.manga_id=s.id LIMIT 1) manga_version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(ss.id) FROM volume ss WHERE ss.manga_id=s.id) volumes, s.chapters, (SELECT MAX(ls.created) FROM file ls LEFT JOIN manga_version vs ON ls.manga_version_id=vs.id WHERE vs.manga_id=s.id) last_link_created FROM manga s LEFT JOIN manga_version v ON s.id=v.manga_id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_manga_genre sg ON s.id=sg.manga_id LEFT JOIN genre g ON sg.genre_id = g.id";
//End copy from index.php
//1. Related, 2. Same author, 3. Half of genres or more in common
$num_of_genres = count(explode(', ', $series['genres']));
$related_query="SELECT rm.related_manga_id id, m.name FROM related_manga rm LEFT JOIN manga m ON rm.related_manga_id=m.id WHERE rm.series_id=".$series['id']." UNION SELECT id, NULL FROM manga WHERE author='".escape($series['author'])."' UNION (SELECT manga_id id, NULL FROM rel_manga_genre GROUP BY manga_id HAVING COUNT(CASE WHEN genre_id IN (SELECT genre_id FROM rel_series_genre WHERE series_id=".$series['id'].") THEN 1 END)>=".max($num_of_genres>=2 ? 2 : 1,intval(round($num_of_genres/2))).") ORDER BY name IS NULL ASC, name ASC, RAND() LIMIT $max_items";
$resultin = query($related_query);
$in = array(-1);
while ($row = mysqli_fetch_assoc($resultin)) {
	$in[]=$row['id'];
}
mysqli_free_result($resultin);
$resultrm = query($base_query . " WHERE s.id IN (".implode(',',$in).") GROUP BY s.id ORDER BY FIELD(s.id,".implode(',',$in).") ASC");

if (mysqli_num_rows($resultrm)>0) {
?>
					<div class="section">
						<h2 class="section-title-main"><span class="iconsm fa fa-fw fa-book-open"></span> Mangues recomanats</h2>
						<h3 class="section-subtitle">Si t'agrada aquest anime, és possible que també t'agradin aquests mangues:</h3>
						<div class="section-content carousel">
<?php
	while ($row = mysqli_fetch_assoc($resultrm)) {
?>
							<div class="status-<?php echo get_status($row['best_status']); ?>">
<?php
		print_carousel_item_manga($row, 'related-manga', FALSE, FALSE);
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
require_once('footer.inc.php');
?>
