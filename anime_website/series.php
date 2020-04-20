<?php
require_once("db.inc.php");

function get_fansub_with_url($fansub) {
	if (!empty($fansub['url'])) {
		return '<a href="'.htmlspecialchars($fansub['url']).'" target="_blank">'.htmlspecialchars($fansub['name']).'</a>';
	} else {
		return htmlspecialchars($fansub['name']);
	}
}

$result = query("SELECT s.*, YEAR(s.air_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE slug='".escape($_GET['slug'])."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	header("Location: /error.php?code=404");
	die();
}

$header_page_title=$series['name'];

$header_tab=$_GET['page'];

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
if (!empty($series['episodes']) && $series['episodes']>1) {
?>
							<div><span title="Nombre de capítols"><span class="fa fa-ruler icon"></span><?php echo $series['episodes'].' capítols'; ?></span></div>
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
					</div>
					<div class="main_content">
						<div class="section">
							<h2 class="section-title">Sinopsi</h2>
							<div class="section-content"><?php echo htmlspecialchars($series['synopsis']); ?></div>
						</div>
<?php
$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id ON v.id=vf.version_id WHERE v.series_id=".$series['id']." GROUP BY v.id ORDER BY v.status ASC, v.created ASC");
$count = mysqli_num_rows($result);

if ($count==0) {
?>
						<div class="section">
							<h2 class="section-title">Informació</h2>
							<div class="section-content">Aquesta obra encara no disposa de cap versió amb subtítols en català.</div>
						</div>
<?php
} else{
	if ($count>1) {
?>
						<div class="version_tab_container">
<?php
		$i=0;
		while ($version = mysqli_fetch_assoc($result)) {
?>
							<div class="version_tab<?php echo $i==0 ? ' version_tab_selected' : ''; ?>" data-version-id="<?php echo $version['id']; ?>">
								<div class="status-<?php echo get_status($version['status']); ?>" title="<?php echo get_status_description($version['status']); ?>"></div>
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

		$fansub_buttons = '';
		for ($j=0;$j<count($fansubs);$j++) {
			if (!empty($fansubs[$j]['url'])) {
				$fansub_buttons.='<a class="fansub-website" href="'.$fansubs[$j]['url'].'" target="_blank"><span class="fa fa-globe icon"></span>Web '.get_fansub_preposition_name($fansubs[$j]['name']).'</a>';
			}
			if (!empty($fansubs[$j]['twitter_url'])) {
				$fansub_buttons.='<a class="fansub-twitter" href="'.$fansubs[$j]['twitter_url'].'" target="_blank"><span class="fab fa-twitter icon"></span>Twitter '.get_fansub_preposition_name($fansubs[$j]['name']).'</a>';
			}
		}
?>
							<div class="section">
								<h2 class="section-title"><?php echo count($fansubs)>1 ? 'Fansubs' : 'Fansub'; ?></h2>
								<div class="section-content">Aquesta obra ha estat subtitulada per <?php echo $conjunctioned_names; ?>. <?php echo $any_active ? "Si vols veure-la amb màxima qualitat, al seu web trobaràs enllaços per a baixar-la. Si t'ha agradat, no oblidis deixar-los un comentari!" : ''; ?></div>
<?php
		if (!empty($fansub_buttons)) {
?>
								<div class="flex wrappable fansub-buttons"><?php echo $fansub_buttons; ?></div>
<?php
		}
?>
							</div>
<?php
		$resulte = query("SELECT e.*, et.title FROM episode e LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".$version['id']." WHERE series_id=".$series['id']." ORDER BY number IS NULL ASC, number ASC, IFNULL(et.title,e.name) ASC");
		$episodes = array();
		while ($row = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $row);
		}
		mysqli_free_result($resulte);

		if (count($episodes)>0) {
?>
							<div class="section">
								<h2 class="section-title">Contingut</h2>
								<div class="section-content flex wrappable">
<?php
				foreach ($episodes as $row) {
					print_episode($row, $version['id'], $series);
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
								<div class="section-content flex wrappable">
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
?>
					</div>
				</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
