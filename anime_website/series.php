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

$result = query("SELECT s.*, YEAR(s.air_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT ss.id) FROM season ss WHERE ss.series_id=s.id) seasons FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY s.id");
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
	'title' => $series['name'].' - Fansubs.cat - Anime',
	'url' => 'https://anime.fansubs.cat/'.($series['type']=='movie' ? 'films/' : 'series/').$series['slug'],
	'description' => strip_tags($synopsis),
	'image' => 'https://anime.fansubs.cat/images/series/'.$series['id'].'.jpg'
);

$header_series_page=TRUE;

require_once('header.inc.php');
?>
				<div class="series_header">
<?php
if (file_exists('images/featured/'.$series['id'].'.jpg')) {
?>
					<img src="/images/featured/<?php echo $series['id']; ?>.jpg" alt="" />
<?php
} else {
?>
					<img src="/images/series/<?php echo $series['id']; ?>.jpg" alt="" />
<?php
}
?>
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
							<img class="sidebar_thumbnail" src="/images/series/<?php echo $series['id']; ?>.jpg" alt="<?php echo htmlspecialchars($series['name']); ?>">
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
if ($series['seasons']>1 && $series['show_seasons']==1) {
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
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Part d'aquesta obra ha estat llicenciada o editada en català. Se'n mostren només les parts no llicenciades.
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

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot() && empty($_GET['f'])) ? " AND v.status<>5 AND v.status<>4" : "").((!empty($_COOKIE['show_missing']) || !empty($_GET['f'])) ? "" : " AND v.episodes_missing=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND s.rating<>'XXX'" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE v.series_id=".$series['id']."$cookie_extra_conditions GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count_unfiltered==0) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquesta obra encara no disposa de cap versió amb subtítols en català. És probable que l'estiguem afegint ara mateix. Torna d'aquí a una estona!</div>
						</div>
<?php
} else if ($count==0) {
	if ($series['rating']=='XXX' && empty($_COOKIE['show_hentai'])) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquesta obra és només per a majors d'edat. Si ets major d'edat i vols veure-la, activa l'opció de mostrar hentai a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	} else {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquesta obra disposa d'alguna versió amb subtítols en català, però el teu filtre d'usuari impedeix mostrar-la. Pots canviar el filtre a la icona de configuració de la part superior de la pàgina, o mostrar-la temporalment.</div>
							<a class="force-display" href="?f=1">Mostra-la</a>
						</div>
<?php
	}
} else {
	if ($count!=$count_unfiltered) {
?>
						<div class="section warning-small">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Hi ha alguna altra versió d'aquesta obra. La pots veure canviant el teu filtre d'usuari a la part superior de la pàgina, o mostrar-la temporalment.</div>
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
				"active" => array("Aquest web només recopila el material editat. L'autoria dels subtítols és del grup següent. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! També pots baixar-ne els fitxers originals amb màxima qualitat.", "Aquest web només recopila el material editat. L'autoria dels subtítols és dels grups següents. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! També pots baixar-ne els fitxers originals amb màxima qualitat."),
				"abandoned" => array("Aquesta obra es considera abandonada pel fansub, segurament no se'n llançaran més capítols.","Aquesta obra es considera abandonada pels fansubs, segurament no se'n llançaran més capítols."),
				"cancelled" => array("Aquesta obra ha estat cancel·lada pel fansub, no se'n llançaran més capítols.","Aquesta obra ha estat cancel·lada pels fansubs, no se'n llançaran més capítols.")
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
												<td class="fansub-icon"><img src="/images/fansubs/<?php echo $fansub['id']; ?>.png" alt="" /></td>
												<td class="fansub-name"><?php echo !empty($fansub['url']) ? ('<a href="'.$fansub['url'].'" target="_blank">'.$fansub['name'].'</a>') : $fansub['name']; ?><?php if ($fansub['status']==0 && $fansub['name']!='Fansub independent') { echo '<span class="fansub-inactive"> (actualment inactiu)</span>'; } ?></td>
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
			if (!empty($fansub['url'])) {
				echo ' <a class="fansub-website" href="'.$fansub['url'].'" target="_blank"><span class="fa fa-fw fa-globe mobileicon"></span><span class="mobilehide">Web</span></a>';
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
		$resulte = query("SELECT e.*, et.title, ss.number season_number, ss.name season_name FROM episode e LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".$version['id']." LEFT JOIN season ss ON e.season_id=ss.id WHERE e.series_id=".$series['id']." ORDER BY ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, IFNULL(et.title,e.name) ASC");
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
									<span class="fa fa-exclamation-triangle icon-pr"></span><?php echo count($fansubs)>1 ? $plurals['abandoned'][1] : $plurals['abandoned'][0]; ?>

								</div>
<?php
			} else if ($version['status']==5) {
?>
								<div class="section-content padding-bottom cancelled-warning">
									<span class="fa fa-exclamation-triangle icon-pr"></span><?php echo count($fansubs)>1 ? $plurals['cancelled'][1] : $plurals['cancelled'][0]; ?>

								</div>
<?php
			}
			if ($version['episodes_missing']==1) {
?>
								<div class="section-content padding-bottom episodes-missing">
									<span class="fa fa-exclamation-triangle icon-pr"></span>Hi ha capítols subtitulats que no tenen cap enllaç vàlid. Si els tens o saps on trobar-los, <a class="version-missing-links-link">contacta'ns</a>.
								</div>
<?php
			}
			if ($series['episodes']==-1) {
?>
							<div class="section-content padding-bottom series-on-air">
								<span class="fa fa-exclamation-triangle icon-pr"></span>Aquesta sèrie encara està en emissió. És possible que tingui més capítols que els que hi ha a la llista.
							</div>
<?php
			}
?>
								<div class="section-content">
<?php
			$seasons = array();
			$last_season_number = -1;
			$last_season_name = "";
			$current_season_episodes = array();
			foreach ($episodes as $row) {
				if ($row['season_number']!=$last_season_number && ($series['show_seasons']==1 || empty($row['season_number']))){
					if ($last_season_number!=-1) {
						array_push($seasons, array(
							'season_number' => $last_season_number,
							'season_name' => $last_season_name,
							'episodes' => apply_sort($series['order_type'],$current_season_episodes)
						));
					}
					$last_season_number=$row['season_number'];
					$last_season_name=$row['season_name'];
					$current_season_episodes = array();
				}

				array_push($current_season_episodes, $row);
			}
			array_push($seasons, array(
				'season_number' => $last_season_number,
				'season_name' => $last_season_name,
				'episodes' => apply_sort($series['order_type'],$current_season_episodes)
			));

//print_r($seasons);

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
						print_episode($episode, $version['id'], $series);
					}
?>
										</tbody>
									</table>
<?php
				}
			} else { //Multiple seasons
				foreach ($seasons as $season) {
?>
									<details class="season"<?php echo $series['show_expanded_seasons']==1 ? ' open' : ''; ?>>
										<summary class="season_name"><?php echo !empty($season['season_number']) ? (($series['show_seasons']!=1 || (count($seasons)==2 && empty($last_season_number))) ? 'Capítols normals' : ('Temporada '.$season['season_number'].(!empty($season['season_name']) ? ': '.$season['season_name'] : ''))) : 'Altres'; ?></summary>
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
						print_episode($episode, $version['id'], $series);
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
									<details class="extra-content<?php echo count($seasons)<2 ? ' extra-content-single-season' : ''; ?>">
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
				print_extra($row, $version['id']);
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

$resultrs = query("SELECT s.* FROM related_series rs LEFT JOIN series s ON rs.related_series_id=s.id WHERE rs.series_id=".$series['id']." ORDER BY s.name ASC");

if (mysqli_num_rows($resultrs)>0) {
?>
						<div class="section" style="padding-top: 1em;">
							<h2 class="section-title">Anime relacionat</h2>
							<div class="section-content">
<?php
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($resultrs)) {
		if (!$first) {
			echo ", ";
		} else {
			echo "\t\t\t\t\t\t\t\t";
			$first = FALSE;
		}
		echo '<a class="trackable-related-anime" data-series-id="'.$row['slug'].'" href="'.$base_url.'/'.($row['type']=='movie' ? 'films' : 'series').'/'.$row['slug'].'">'.$row['name'].'</a>';
	}
?>

							</div>
						</div>
<?php
}

mysqli_free_result($resultrs);

$resultrm = query("SELECT rm.* FROM related_manga rm WHERE rm.series_id=".$series['id']." ORDER BY rm.name ASC");

if (mysqli_num_rows($resultrm)>0) {
?>
						<div class="section" style="padding-top: 1em;">
							<h2 class="section-title">Manga relacionat</h2>
							<div class="section-content">
<?php
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($resultrm)) {
		if (!$first) {
			echo ", ";
		} else {
			echo "\t\t\t\t\t\t\t\t";
			$first = FALSE;
		}
		echo '<a class="trackable-related-manga" data-name="'.$row['name'].'" href="'.$row['url'].'">'.$row['name'].'</a>';
	}
?>

							</div>
						</div>
<?php
}

mysqli_free_result($resultrm);
?>
					</div>
				</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
