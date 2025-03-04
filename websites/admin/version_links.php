<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Enllaços de la versió d’anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Enllaços de la versió de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Enllaços de la versió d’imatge real - Imatge real";
		$page="liveaction";
	break;
}

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$link_url=ANIME_URL;
		break;
	case 'liveaction':
		$link_url=LIVEACTION_URL;
		break;
	case 'manga':
		$link_url=MANGA_URL;
		break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT v.*, s.name series_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN series s ON v.series_id=s.id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);

	$results = query("SELECT s.* FROM series s WHERE id=".$row['series_id']);
	$series = mysqli_fetch_assoc($results) or crash('Series not found');
	mysqli_free_result($results);

	$resultd = query("SELECT IFNULL(vd.title,d.name) name, d.number number FROM division d LEFT JOIN version_division vd ON d.id=vd.division_id AND vd.version_id=".escape($_GET['id'])." WHERE d.series_id=".$row['series_id']." AND d.number_of_episodes>0 ORDER BY d.number ASC");
	$divisions = array();
	while ($drow = mysqli_fetch_assoc($resultd)) {
		array_push($divisions, $drow);
	}
	mysqli_free_result($resultd);

	$resulte = query("SELECT e.*,
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				) episode_title
			FROM version v
			LEFT JOIN episode e ON v.series_id=e.series_id
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=v.id 
			WHERE v.id=".escape($_GET['id'])." 
			ORDER BY d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
	$episodes = array();
	while ($rowe = mysqli_fetch_assoc($resulte)) {
		array_push($episodes, $rowe);
	}
	mysqli_free_result($resulte);
	if ($series['rating']=='XXX') {
		$link_url = str_replace(MAIN_DOMAIN, HENTAI_DOMAIN, $link_url);
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços de la versió</h4>
					<hr>
					<p class="text-center">Aquests són els enllaços de la versió de «<b><?php echo htmlspecialchars($row['title']); ?></b>» feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
					<hr>
					<div class="text-center">
						<button onclick="copyToClipboard('<?php echo $link_url.'/'.$row['slug']; ?>', $(this));" class="btn btn-primary"><span class="fa fa-copy pe-2"></span>Copia l’enllaç a la fitxa de la versió</button>
					</div>
				</article>
			</div>
		</div>
<?php
	if (count($divisions)>1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços a divisions concretes</h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead class="table-dark">
							<tr>
								<th>Divisió</th>
								<th class="text-center">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
		foreach ($divisions as $division) {
?>
						<tr>
							<td style="width: 70%;"><strong><?php echo $division['name']; ?></strong></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/'.$row['slug']."#'+string_to_slug('".htmlspecialchars($division['name']); ?>'), $(this));" class="btn btn-sm btn-primary"><span class="fa fa-copy pe-2"></span>Copia l’enllaç</button></td>
						</tr>
<?php
		}
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços per a incrustar de capítols concrets</h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead class="table-dark">
							<tr>
								<th>Capítol</th>
								<th class="text-center">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
	for ($i=0;$i<count($episodes);$i++) {
		$resultf = query("SELECT f.*,(SELECT COUNT(*) FROM file f2 WHERE f2.version_id=f.version_id AND f2.episode_id=f.episode_id) variant_count FROM file f WHERE f.version_id=".escape($_GET['id'])." AND f.episode_id=".$episodes[$i]['id']." ORDER BY f.variant_name ASC, f.id ASC");
		while ($rowf = mysqli_fetch_assoc($resultf)) {
			$is_valid = FALSE;
			if ($type=='manga') {
				$is_valid=!empty($rowf['original_filename']);
			} else {
				$resultli = query("SELECT l.* FROM link l WHERE l.url IS NOT NULL AND l.file_id=".$rowf['id']." ORDER BY l.url ASC");
				$links = array();
				while ($rowli = mysqli_fetch_assoc($resultli)) {
					array_push($links, $rowli);
				}
				mysqli_free_result($resultli);

				$is_valid=!empty($links);
			}

			if ($is_valid) {
?>
						<tr>
							<td style="width: 85%;"><?php echo $episodes[$i]['episode_title'] . ($rowf['variant_count']>1 ? ' (variant «'.$rowf['variant_name'].'»)' : ''); ?></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/embed/'.$rowf['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-copy pe-2"></span>Copia l’enllaç</button></td>
						</tr>
<?php
			}
		}
		mysqli_free_result($resultf);
	}
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
	$resultex = query("SELECT f.* FROM file f WHERE f.version_id=".escape($_GET['id'])." AND f.episode_id IS NULL ORDER BY f.extra_name ASC, f.id ASC");
	$first = TRUE;
	while ($rowex = mysqli_fetch_assoc($resultex)) {
		if ($first) {
			$first = FALSE;
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços per a incrustar de material extra</h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-hover table-sm">
						<thead class="table-dark">
							<tr>
								<th>Material extra</th>
								<th class="text-center">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
		}
		$is_valid = FALSE;
		if ($type=='manga') {
			$is_valid=!empty($rowex['original_filename']);
		} else {
			$resultli = query("SELECT l.* FROM link l WHERE l.url IS NOT NULL AND l.file_id=".$rowex['id']." ORDER BY l.url ASC");
			$links = array();
			while ($rowli = mysqli_fetch_assoc($resultli)) {
				array_push($links, $rowli);
			}
			mysqli_free_result($resultli);

			$is_valid=!empty($links);
		}

		if ($is_valid) {
?>
					<tr>
						<td style="width: 85%;"><strong><?php echo $rowex['extra_name']; ?></strong></td>
						<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/embed/'.$rowex['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-copy pe-2"></span>Copia l’enllaç</button></td>
					</tr>
<?php
		}
	}
	if (!$first) {
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
	}
	mysqli_free_result($resultex);
?>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
