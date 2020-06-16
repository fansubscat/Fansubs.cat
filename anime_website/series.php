<?php
require_once("db.inc.php");
require_once("parsedown.inc.php");

function get_fansub_with_url($fansub) {
	if ($fansub['name']=='Fansub independent') {
		return "un fansub independent";
	} else if (!empty($fansub['url'])) {
		return '<a href="'.htmlspecialchars($fansub['url']).'" target="_blank">'.htmlspecialchars($fansub['name']).'</a>';
	} else {
		return htmlspecialchars($fansub['name']);
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

$result = query("SELECT s.*, YEAR(s.air_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT ss.id) FROM season ss WHERE ss.series_id=s.id) seasons FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE slug='".escape($_GET['slug'])."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	header("Location: /error.php?code=404");
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
	'image' => $series['image']
);

$header_series_page=TRUE;

require_once('header.inc.php');
?>
				<div class="flex mobilewrappable">
					<div class="series_sidebar">
						<h2 class="section-title"><?php echo htmlspecialchars($series['name']); ?></h2>
<?php
if (!empty($series['alternate_names'])) {
?>
						<div class="sidebar_alternate_names"><?php echo htmlspecialchars($series['alternate_names']); ?></div>
<?php
}
?>
						<img class="sidebar_thumbnail" src="<?php echo htmlspecialchars($series['image']); ?>" alt="<?php echo htmlspecialchars($series['name']); ?>">
						<div class="sidebar_data flex wrappable">
<?php
if (!empty($series['air_date'])) {
?>
							<div><span title="Any"><span class="fa fa-calendar icon"></span><?php echo date('Y',strtotime($series['air_date'])); ?></span></div>
<?php
}
if (!empty($series['author'])) {
?>
							<div><span title="Autor"><span class="fa fa-book icon"></span><?php echo htmlspecialchars($series['author']); ?></span></div>
<?php
}
if (!empty($series['director'])) {
?>
							<div><span title="Director"><span class="fa fa-bullhorn icon"></span><?php echo htmlspecialchars($series['director']); ?></span></div>
<?php
}
if (!empty($series['studio'])) {
?>
							<div><span title="Estudi"><span class="fa fa-video icon"></span><?php echo htmlspecialchars($series['studio']); ?></span></div>
<?php
}
if (!empty($series['rating'])) {
?>
							<div><span title="Edat recomanada"><span class="fa fa-star icon"></span><?php echo htmlspecialchars(get_rating($series['rating'])); ?></span></div>
<?php
}
if ($series['episodes']>1) {
?>
							<div><span title="Nombre de capítols"><span class="fa fa-ruler icon"></span><?php echo $series['episodes'].' capítols'; ?></span></div>
<?php
}
if ($series['seasons']>1 && $series['show_seasons']==1) {
?>
							<div><span title="Nombre de temporades"><span class="fa fa-th-large icon"></span><?php echo $series['seasons'].' temporades'; ?></span></div>
<?php
}
if (!empty($series['duration'])) {
?>
							<div><span title="Durada"><span class="fa fa-clock icon"></span><?php echo $series['duration']; ?></span></div>
<?php
}
if (!empty($series['score'])) {
?>
							<div><span title="Puntuació a MyAnimeList"><span class="fa fa-smile icon"></span><?php echo number_format($series['score'],2,","," "); ?>/10</span></div>
<?php
}
if (!empty($series['genres'])) {
?>
							<div><span title="Gèneres"><span class="fa fa-tags icon"></span><?php echo htmlspecialchars($series['genres']); ?></span></div>
<?php
}
?>
						</div>
<?php
if (!empty($series['myanimelist_id'])) {
?>
						<a class="mal-button" href="https://myanimelist.net/anime/<?php echo $series['myanimelist_id']; ?>/" target="_blank"><span class="fa fa-th-list icon"></span>MyAnimeList</a>
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
								<span class="fa fa-exclamation-triangle icon"></span>Part d'aquesta obra ha estat llicenciada o editada en català. Se'n mostren només les parts no llicenciades.
							</div>
<?php
}
?>
						</div>
<?php
$result_unfiltered = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.series_id=".$series['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count_unfiltered = mysqli_num_rows($result_unfiltered);
mysqli_free_result($result_unfiltered);

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot()) ? " AND v.status<>5 AND v.status<>4" : "").(!empty($_COOKIE['hide_missing']) ? " AND v.episodes_missing=0" : "").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND s.rating<>'XXX'" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE v.series_id=".$series['id']."$cookie_extra_conditions GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count_unfiltered==0) {
?>
						<div class="section">
							<h2 class="section-title">Informació</h2>
							<div class="section-content">Aquesta obra encara no disposa de cap versió amb subtítols en català.</div>
						</div>
<?php
} else if ($count==0) {
	if ($series['rating']=='XXX' && empty($_COOKIE['show_hentai'])) {
?>
						<div class="section">
							<h2 class="section-title">Informació</h2>
							<div class="section-content">Aquesta obra és només per a majors d'edat. Si ets major d'edat i vols veure-la, activa l'opció de mostrar hentai a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	} else {
?>
						<div class="section">
							<h2 class="section-title">Informació</h2>
							<div class="section-content">Aquesta obra disposa d'alguna versió amb subtítols en català, però el teu filtre d'usuari impedeix mostrar-la. Si vols veure-la, canvia el filtre a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	}
} else {
	if ($count>1) {
?>
						<div class="version_tab_container">
<?php
		$i=0;
		while ($version = mysqli_fetch_assoc($result)) {
?>
							<div class="version_tab<?php echo $i==0 ? ' version_tab_selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
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
						<div class="version_content<?php echo $count>1 ? ' version_content_multi' : ''; ?><?php echo $i>0 ? ' hidden' : ''; ?>" id="version_content_<?php echo $version['id']; ?>">
<?php
		$resultf = query("SELECT f.* FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.version_id=".$version['id']." ORDER BY f.name ASC");
		$fansubs = array();
		while ($fansub = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, $fansub);
		}
		mysqli_free_result($resultf);

		$any_active = ($fansubs[0]['status']==1);
		$conjunctioned_names = get_fansub_with_url($fansubs[0]);
		for ($j=1;$j<count($fansubs);$j++) {
			if ($j==count($fansubs)-1) {
				$conjunctioned_names.=' i ';
			} else {
				$conjunctioned_names.=', ';
			}
			$conjunctioned_names.=get_fansub_with_url($fansubs[$j]);
			if ($fansubs[$j]['status']==1) {
				$any_active = TRUE;
			}
		}

		$web_buttons = '';
		$web_buttons_count=0;
		for ($j=0;$j<count($fansubs);$j++) {
			if (!empty($fansubs[$j]['url'])) {
				$web_buttons.='<a class="fansub-website" href="'.$fansubs[$j]['url'].'" target="_blank"><span class="fa fa-globe icon"></span>Web '.get_fansub_preposition_name($fansubs[$j]['name']).'</a>';
				$web_buttons_count++;
			}
		}
		$twitter_buttons = '';
		$twitter_buttons_count=0;
		for ($j=0;$j<count($fansubs);$j++) {
			if (!empty($fansubs[$j]['twitter_url'])) {
				$twitter_buttons.='<a class="fansub-twitter" href="'.$fansubs[$j]['twitter_url'].'" target="_blank"><span class="fab fa-twitter icon"></span>Twitter '.get_fansub_preposition_name($fansubs[$j]['name']).'</a>';
				$twitter_buttons_count++;
			}
		}

		$fansub_buttons='';
		if ($web_buttons!='' && $twitter_buttons!='' && ($web_buttons_count>1 || $twitter_buttons_count>1)) {
			if (!empty($version['downloads_url'])) {
				$fansub_buttons.='<br /><a class="fansub-downloads" data-url="'.htmlspecialchars(base64_encode($version['downloads_url'])).'"><span class="fa fa-download icon"></span>'.(preg_match(REGEXP_DL_LINK,$version['downloads_url']) ? 'Carpeta de baixades' : 'Fitxa al fansub (baixades)').'</a>';
			}
			$fansub_buttons.=$web_buttons.'<br />'.$twitter_buttons;
		} else {
			if (!empty($version['downloads_url'])) {
				$fansub_buttons.='<a class="fansub-downloads" data-url="'.htmlspecialchars(base64_encode($version['downloads_url'])).'"><span class="fa fa-download icon"></span>'.(preg_match(REGEXP_DL_LINK,$version['downloads_url']) ? 'Carpeta de baixades' : 'Fitxa al fansub (baixades)').'</a>';
			}
			$fansub_buttons.=$web_buttons.$twitter_buttons;
		}

		$plurals = array(
				"active" => array("Si la vols veure amb màxima qualitat, al seu lloc web trobaràs la manera de baixar-la. Si t'ha agradat, no oblidis deixar-los un comentari!","Si la vols veure amb màxima qualitat, als seus llocs web trobaràs la manera de baixar-la. Si t'ha agradat, no oblidis deixar-los un comentari!"),
				"inactive" => array("Actualment, aquest fansub ja no està actiu.","Actualment, aquests fansubs ja no estan actius."),
				"abandoned" => array("Aquesta obra es considera abandonada, segurament no se'n llançaran més capítols.","Aquesta obra es considera abandonada, segurament no se'n llançaran més capítols."),
				"cancelled" => array("Tingues en compte que aquesta obra ha estat cancel·lada, no se'n llançaran més capítols.","Tingues en compte que aquesta obra ha estat cancel·lada, no se'n llançaran més capítols.")
		);
?>
							<div class="section">
								<h2 class="section-title"><?php echo count($fansubs)>1 ? 'Fansubs' : 'Fansub'; ?></h2>
								<div class="section-content">
									Aquesta obra ha estat subtitulada per <?php echo $conjunctioned_names; ?>. <?php echo $any_active ? (count($fansubs)>1 ? $plurals['active'][1] : $plurals['active'][0]) : (count($fansubs)>1 ? $plurals['inactive'][1] : ($fansubs[0]['name']=='Fansub independent' ? '' : $plurals['inactive'][0]));?>

								</div>
<?php
		if (!empty($fansub_buttons)) {
?>
								<div class="fansub-buttons"><?php echo $fansub_buttons; ?></div>
<?php
		}
?>
<?php
		if ($version['status']==4) {
?>
								<div class="section-content padding-top">
									<?php echo count($fansubs)>1 ? $plurals['abandoned'][1] : $plurals['abandoned'][0]; ?>

								</div>
<?php
		} else if ($version['status']==5) {
?>
								<div class="section-content padding-top">
									<?php echo count($fansubs)>1 ? $plurals['cancelled'][1] : $plurals['cancelled'][0]; ?>

								</div>
<?php
		}
?>
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

			if ($version['episodes_missing']==1) {
?>
								<div class="section-content padding-bottom episodes-missing">
									<span class="fa fa-exclamation-triangle icon"></span>Hi ha capítols subtitulats que no tenen cap enllaç vàlid. Si els tens o saps on trobar-los, <a class="version-missing-links-link">contacta'ns</a>.
								</div>
<?php
			}
			if ($series['episodes']==-1) {
?>
							<div class="section-content padding-bottom series-on-air">
								<span class="fa fa-exclamation-triangle icon"></span>Aquesta sèrie encara està en emissió. És possible que la llista de capítols no estigui actualitzada.
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
					foreach ($season['episodes'] as $episode) {
						print_episode($episode, $version['id'], $series);
					}
				}
			} else { //Multiple seasons
				foreach ($seasons as $season) {
?>
									<details class="season"<?php echo $series['show_expanded_seasons']==1 ? ' open' : ''; ?>>
										<summary class="season_name"><?php echo !empty($season['season_number']) ? (($series['show_seasons']!=1 || (count($seasons)==2 && empty($last_season_number))) ? 'Capítols normals' : ('Temporada '.$season['season_number'].(!empty($season['season_name']) ? ': '.$season['season_name'] : ''))) : 'Altres'; ?></summary>
<?php
					foreach ($season['episodes'] as $episode) {
						print_episode($episode, $version['id'], $series);
					}
?>
									</details>
<?php
				}
			}
?>
								</div>
							</div>
<?php
		}
		$resulte = query("SELECT DISTINCT l.extra_name FROM link l WHERE version_id=".$version['id']." AND l.episode_id IS NULL ORDER BY extra_name ASC");
		$extras = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			array_push($extras, $row);
		}
		mysqli_free_result($resulte);

		if (count($extras)>0) {
?>
							<div class="section">
								<h2 class="section-title">Contingut extra</h2>
								<div class="section-content">
<?php
			foreach ($extras as $row) {
				print_extra($row, $version['id']);
			}
?>
								</div>
							</div>
<?php
		}
?>
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
		echo '<a href="/'.($row['type']=='movie' ? 'films' : 'series').'/'.$row['slug'].'">'.$row['name'].'</a>';
	}
?>

							</div>
						</div>
<?php
}

mysqli_free_result($resultrs);
?>
					</div>
				</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
