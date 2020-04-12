<?php
require_once("db.inc.php");

$result = query("SELECT s.*, YEAR(s.air_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE slug='".escape($_GET['slug'])."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	header("Location: /error.php?code=404");
	die();
}

$header_page_title=$series['name'];

$header_tab=$_GET['page'];
$header_hide_options=TRUE;

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
							<div><span class="year" title="Any"><?php echo date('Y',strtotime($series['air_date'])); ?></span></div>
<?php
}
if (!empty($series['author'])) {
?>
							<div><span class="author" title="Autor"><?php echo htmlspecialchars($series['author']); ?></span></div>
<?php
}
if (!empty($series['director'])) {
?>
							<div><span class="director" title="Director"><?php echo htmlspecialchars($series['director']); ?></span></div>
<?php
}
if (!empty($series['studio'])) {
?>
							<div><span class="studio" title="Estudi"><?php echo htmlspecialchars($series['studio']); ?></span></div>
<?php
}
if (!empty($series['rating'])) {
?>
							<div><span class="rating" title="Edat recomanada"><?php echo htmlspecialchars(get_rating($series['rating'])); ?></span></div>
<?php
}
if (!empty($series['episodes']) && $series['episodes']>1) {
?>
							<div><span class="numepisodes" title="Nombre de capítols"><?php echo $series['episodes'].' capítols'; ?></span></div>
<?php
}
if (!empty($series['duration'])) {
?>
							<div><span class="duration" title="Durada"><?php echo $series['duration']; ?></span></div>
<?php
}
if (!empty($series['genres'])) {
?>
							<div><span class="genres" title="Gèneres"><?php echo htmlspecialchars($series['genres']); ?></span></div>
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
								<div class="version_tab_text"><?php echo htmlspecialchars(get_fansub_version_title($version['fansub_name'])); ?></div>
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
		while ($fansub = mysqli_fetch_assoc($resultf)) {
?>
							<div class="fansub_data">Aquesta obra ha estat subtitulada per 
<?php
			if (!empty($fansub['url'])) {
?>
								<a href="<?php echo htmlspecialchars($fansub['url']); ?>" target="_blank"><?php echo htmlspecialchars($fansub['name']); ?></a>.
<?php
			}
			else{
				echo htmlspecialchars($fansub['name']).'.';
			}
			if ($fansub['status']==1){
?>
								Si vols veure-la amb màxima qualitat, al seu web trobaràs enllaços per a baixar-la. Si t'ha agradat, no oblidis deixar-los un comentari!
<?php
			}
?>
							</div>
<?php
		}
		mysqli_free_result($resultf);

		if (FALSE/*!empty($version['general_url'])*/) {
?>
							<div class="section">
								<h2 class="section-title">Contingut</h2>
								<div class="section-content">Trobaràs tot el contingut a <a href="<?php echo htmlspecialchars($version['general_url']); ?>" target="blank"><?php echo htmlspecialchars($version['general_url']); ?></a></div>
							</div>
<?php
		} else {
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
