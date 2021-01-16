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

function apply_sort($order_type, $chapters) {
	switch ($order_type){
		case 1: // Alphabetic strict sort
			$titles = array_column($chapters, 'title');
			$numbers = array_column($chapters, 'number');
			array_multisort($titles, SORT_ASC, SORT_LOCALE_STRING|SORT_FLAG_CASE, $numbers, SORT_ASC, SORT_NUMERIC, $chapters);

			// Move empty titles to last
			while(reset($titles) == '') {
				$k = key($titles);
				unset($titles[$k]); // remove empty element from beginning of array
				$titles[$k] = ''; // add it to end of array
				$v = reset($chapters);
				$k = key($chapters);
				unset($chapters[$k]);
				$chapters[$k] = $v;
			}
			return $chapters;
		case 2: // Alphabetic natural sort
			$titles = array_column($chapters, 'title');
			$numbers = array_column($chapters, 'number');
			array_multisort($titles, SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $numbers, SORT_ASC, SORT_NUMERIC, $chapters);

			// Move empty titles to last
			while(reset($titles) == '') {
				$k = key($titles);
				unset($titles[$k]); // remove empty element from beginning of array
				$titles[$k] = ''; // add it to end of array
				$v = reset($chapters);
				$k = key($chapters);
				unset($chapters[$k]);
				$chapters[$k] = $v;
			}
			return $chapters;
		case 0: //Normal sort - already sorted from the database
		default:
			return $chapters;
	}
}

$result = query("SELECT m.*, YEAR(m.publish_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT v.id) FROM volume v WHERE v.manga_id=m.id) volumes FROM manga m LEFT JOIN rel_manga_genre mg ON m.id=mg.manga_id LEFT JOIN genre g ON mg.genre_id = g.id WHERE slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY m.id");
$manga = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$header_page_title=$manga['name'];

$header_tab=$_GET['page'];

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($manga['synopsis']);

$header_social = array(
	'title' => $manga['name'].' | Fansubs.cat - Manga en català',
	'url' => 'https://manga.fansubs.cat/'.($manga['type']=='oneshot' ? 'one-shots/' : 'serialitzats/').$manga['slug'],
	'description' => strip_tags($synopsis),
	'image' => 'https://manga.fansubs.cat/images/manga/'.$manga['id'].'.jpg'
);

$header_series_page=TRUE;

require_once('header.inc.php');
?>
				<div class="series_header">
<?php
if (file_exists('images/featured/'.$manga['id'].'.jpg')) {
?>
					<div class="img" style="background: url('/images/featured/<?php echo $manga['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
<?php
} else {
?>
					<div class="img" style="background: url('/images/manga/<?php echo $manga['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
<?php
}
?>
					<div class="series_title_container">
						<h2 class="series_title"><?php echo htmlspecialchars($manga['name']); ?></h2>
<?php
if (!empty($manga['alternate_names'])) {
?>
						<div class="series_alternate_names"><?php echo htmlspecialchars($manga['alternate_names']); ?></div>
<?php
}
?>
					</div>
				</div>
				<div class="flex mobilewrappable">
					<div class="series_sidebar">
						<div class="series_sidebar_inner">
							<div class="sidebar_thumbnail" style="background: url('/images/manga/<?php echo $manga['id']; ?>.jpg') no-repeat center; background-size: cover;"></div>
							<h2 class="section-title">Fitxa tècnica</h2>
							<div class="sidebar_data">
<?php
if (!empty($manga['publish_date'])) {
?>
								<div><span class="fa fa-fw fa-calendar dataicon"></span><strong>Any:</strong> <?php echo date('Y',strtotime($manga['publish_date'])); ?></div>
<?php
}
if (!empty($manga['author'])) {
?>
								<div><span class="fa fa-fw fa-book dataicon"></span><strong>Autor:</strong> <?php echo htmlspecialchars($manga['author']); ?></div>
<?php
}
if (!empty($manga['rating'])) {
?>
								<div><span class="fa fa-fw fa-star dataicon"></span><strong>Edat:</strong> <?php echo htmlspecialchars(get_rating($manga['rating'])); ?></div>
<?php
}
if ($manga['chapters']>1) {
?>
								<div><span class="fa fa-fw fa-ruler dataicon"></span><strong>Capítols:</strong> <?php echo $manga['chapters'].' capítols'; ?></div>
<?php
}
if ($manga['volumes']>1 && $manga['show_volumes']==1) {
?>
								<div><span class="fa fa-fw fa-th-large dataicon"></span><strong>Volums:</strong> <?php echo $manga['volumes'].' volums'; ?></div>
<?php
}
if (!empty($manga['score'])) {
?>
								<div><span class="fa fa-fw fa-smile dataicon"></span><strong>Puntuació a MyAnimeList:</strong> <?php echo number_format($manga['score'],2,","," "); ?>/10</div>
<?php
}
if (!empty($manga['genres'])) {
?>
								<div><span class="fa fa-fw fa-tags dataicon"></span><strong>Gèneres:</strong> <?php echo htmlspecialchars($manga['genres']); ?></div>
<?php
}
?>
							</div>
<?php
if (!empty($manga['myanimelist_id'])) {
?>
							<a class="mal-button" href="https://myanimelist.net/manga/<?php echo $manga['myanimelist_id']; ?>/" target="_blank"><span class="fa fa-th-list icon"></span>Mostra'n la fitxa a MyAnimeList</a>
<?php
}
if (!empty($manga['tadaima_id'])) {
?>
							<a class="tadaima-button" href="https://tadaima.cat/fil-t<?php echo $manga['tadaima_id']; ?>.html" target="_blank"><span class="fa fa-comments icon"></span><?php echo get_tadaima_info($manga['tadaima_id']); ?></a>
<?php
} else {
?>
							<a class="tadaima-button" href="https://tadaima.cat/posting.php?mode=post&f=9" target="_blank"><span class="fa fa-comments icon"></span>Comenta-ho a Tadaima.cat</a>
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
									<a>Mostra'n més...</a>
								</div>
							</div>
<?php
if ($manga['has_licensed_parts']==1) {
?>
							<div class="section-content padding-top parts-licensed">
								<span class="fa fa-fw fa-exclamation-triangle icon-pr"></span>Part d'aquest manga ha estat llicenciat o editat oficialment en català. Se'n mostren només les parts no llicenciades.
							</div>
<?php
}
?>
						</div>
<?php
$result_unfiltered = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM manga_version v LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.manga_id=".$manga['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count_unfiltered = mysqli_num_rows($result_unfiltered);
mysqli_free_result($result_unfiltered);

$cookie_fansub_ids = (empty($_GET['f']) ? get_cookie_fansub_ids() : array());

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot() && empty($_GET['f'])) ? " AND v.status<>5 AND v.status<>4" : "").((!empty($_COOKIE['show_missing']) || !empty($_GET['f'])) ? "" : " AND v.chapters_missing=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND m.rating<>'XXX'" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM manga_version v2 LEFT JOIN rel_manga_version_fansub vf2 ON v2.id=vf2.manga_version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.manga_version_id FROM rel_manga_version_fansub vf3 WHERE vf3.manga_version_id=vf2.manga_version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM manga_version v LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN manga m ON v.manga_id=m.id WHERE v.manga_id=".$manga['id']."$cookie_extra_conditions GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count_unfiltered==0) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest manga encara no disposa de cap versió editada en català. És probable que l'estiguem afegint ara mateix. Torna d'aquí a una estona!</div>
						</div>
<?php
} else if ($count==0) {
	if ($manga['rating']=='XXX' && empty($_COOKIE['show_hentai'])) {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest manga és només per a majors d'edat. Si ets major d'edat i vols veure'l, activa l'opció de mostrar hentai a la icona de configuració de la part superior de la pàgina.</div>
						</div>
<?php
	} else {
?>
						<div class="section warning">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Aquest manga disposa d'alguna versió editada en català, però el teu filtre d'usuari impedeix mostrar-la. Pots canviar el filtre a la icona de configuració de la part superior de la pàgina, o mostrar-la temporalment.</div>
							<a class="force-display" href="?f=1">Mostra-la</a>
						</div>
<?php
	}
} else {
	if ($count!=$count_unfiltered) {
?>
						<div class="section warning-small">
							<span class="fa fa-fw fa-exclamation-triangle"></span>
							<div class="section-content">Hi ha alguna altra versió d'aquest manga. La pots veure canviant el teu filtre d'usuari a la part superior de la pàgina, o mostrar-la temporalment.</div>
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
		$resultf = query("SELECT f.*, vf.downloads_url FROM rel_manga_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.manga_version_id=".$version['id']." ORDER BY f.name ASC");
		$fansubs = array();
		while ($fansub = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, $fansub);
		}
		mysqli_free_result($resultf);

		$plurals = array(
				"active" => array("Aquest web només recopila el material editat. L'autoria de l'edició és del grup següent. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! També pots baixar-ne els fitxers originals amb màxima qualitat.", "Aquest web només recopila el material editat. L'autoria de l'edició és dels grups següents. Si t'agrada la seva feina, deixa'ls un comentari d'agraïment! També pots baixar-ne els fitxers originals amb màxima qualitat."),
				"abandoned" => array("Aquest manga es considera abandonat pel fansub, segurament no se'n llançaran més capítols.","Aquest manga es considera abandonat pels fansubs, segurament no se'n llançaran més capítols."),
				"cancelled" => array("Aquest manga ha estat cancel·lat pel fansub, no se'n llançaran més capítols.","Aquest manga ha estat cancel·lat pels fansubs, no se'n llançaran més capítols.")
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
		$resultc = query("SELECT c.*, ct.title, v.id volume_id, v.number volume_number, v.name volume_name FROM chapter c LEFT JOIN chapter_title ct ON c.id=ct.chapter_id AND ct.manga_version_id=".$version['id']." LEFT JOIN volume v ON c.volume_id=v.id WHERE c.manga_id=".$manga['id']." ORDER BY v.number IS NULL ASC, v.number ASC, c.number IS NULL ASC, c.number ASC, IFNULL(ct.title,c.name) ASC");
		$chapters = array();
		while ($row = mysqli_fetch_assoc($resultc)) {
			array_push($chapters, $row);
		}
		mysqli_free_result($resultc);

		if (count($chapters)>0) {
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
			if ($version['chapters_missing']==1) {
?>
								<div class="section-content padding-bottom episodes-missing">
									<span class="fa fa-exclamation-triangle icon-pr"></span>Hi ha capítols editats que no hem pogut recuperar. Si els tens o saps on trobar-los, <a class="version-missing-links-link">contacta'ns</a>.
								</div>
<?php
			}
			if ($manga['chapters']==-1) {
?>
							<div class="section-content padding-bottom series-on-air">
								<span class="fa fa-exclamation-triangle icon-pr"></span>Aquest manga encara està en publicació. És possible que tingui més capítols que els que hi ha a la llista.
							</div>
<?php
			}
?>
								<div class="section-content">
<?php
			$volumes = array();
			$last_volume_number = -1;
			$last_volume_id = -1;
			$last_volume_name = "";
			$current_volume_chapters = array();
			foreach ($chapters as $row) {
				if ($row['volume_number']!=$last_volume_number && ($manga['show_volumes']==1 || empty($row['volume_number']))){
					if ($last_volume_number!=-1) {
						array_push($volumes, array(
							'volume_id' => $last_volume_id,
							'volume_number' => $last_volume_number,
							'volume_name' => $last_volume_name,
							'chapters' => apply_sort($manga['order_type'],$current_volume_chapters)
						));
					}
					$last_volume_number=$row['volume_number'];
					$last_volume_id=$row['volume_id'];
					$last_volume_name=$row['volume_name'];
					$current_volume_chapters = array();
				}

				array_push($current_volume_chapters, $row);
			}
			array_push($volumes, array(
				'volume_id' => $last_volume_id,
				'volume_number' => $last_volume_number,
				'volume_name' => $last_volume_name,
				'chapters' => apply_sort($manga['order_type'],$current_volume_chapters)
			));

			foreach ($volumes as $volume) {
?>
									<details class="season"<?php echo $manga['show_expanded_volumes']==1 ? ' open' : ''; ?>>
										<summary class="season_name"><?php echo !empty($volume['volume_number']) ? (($manga['show_volumes']!=1 || (count($volumes)==2 && empty($last_volume_number))) ? 'Volum únic' : (!empty($volume['volume_name']) ? $volume['volume_name'] : (count($volumes)>1 ? 'Volum '.$volume['volume_number'] : 'Volum únic'))) : 'Altres'; ?></summary>
										<div class="volume-container">
<?php
				if (file_exists('images/covers/'.$version['id'].'_'.$volume['volume_id'].'.jpg')) {
?>
											<div class="volume-image-container">
												<img class="volume-cover" src="<?php echo '/images/covers/'.$version['id'].'_'.$volume['volume_id'].'.jpg'; ?>" alt="">
											</div>
<?php
				}
?>
											<table class="episode-table" rules="rows">
												<thead>
													<tr>
														<th class="episode-seen-head">Vist</th>
														<th>Títol</th>
												</thead>
												<tbody>
<?php
				foreach ($volume['chapters'] as $chapter) {
					print_chapter($chapter, $version['id'], $manga);
				}
?>
												</tbody>
											</table>
										</div>
									</details>
<?php
			}
		}
		$resultc = query("SELECT DISTINCT f.extra_name FROM file f WHERE manga_version_id=".$version['id']." AND f.chapter_id IS NULL ORDER BY extra_name ASC");
		$extras = array();
		while ($row = mysqli_fetch_assoc($resultc)) {
			array_push($extras, $row);
		}
		mysqli_free_result($resultc);

		if (count($extras)>0) {
?>
									<details class="extra-content<?php echo count($volumes)<2 ? ' extra-content-single-season' : ''; ?>">
										<summary class="season_name">Contingut extra</summary>
										<table class="episode-table" rules="rows">
											<thead>
												<tr>
													<th class="episode-seen-head">Vist</th>
													<th>Nom</th>
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

$resultrm = query("SELECT m.* FROM related_manga_manga rm LEFT JOIN manga m ON rm.related_manga_id=m.id WHERE rm.manga_id=".$manga['id']." ORDER BY m.name ASC");

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
		echo '<a class="trackable-related-manga" data-series-id="'.$row['slug'].'" href="'.$base_url.'/'.($row['type']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug'].'">'.$row['name'].'</a>';
	}
?>

							</div>
						</div>
<?php
}

mysqli_free_result($resultrm);

$resultra = query("SELECT a.* FROM related_manga_anime ra LEFT JOIN series a ON ra.related_anime_id=a.id WHERE ra.manga_id=".$manga['id']." ORDER BY a.name ASC");

if (mysqli_num_rows($resultra)>0) {
?>
						<div class="section" style="padding-top: 1em;">
							<h2 class="section-title">Anime relacionat</h2>
							<div class="section-content">
<?php
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($resultra)) {
		if (!$first) {
			echo ", ";
		} else {
			echo "\t\t\t\t\t\t\t\t";
			$first = FALSE;
		}
		echo '<a class="trackable-related-anime" data-series-id="'.$row['slug'].'" href="https://anime.fansubs.cat/'.($row['type']=='movie' ? 'films' : 'series').'/'.$row['slug'].'">'.$row['name'].'</a>';
	}
?>

							</div>
						</div>
<?php
}

mysqli_free_result($resultra);
?>
					</div>
				</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
