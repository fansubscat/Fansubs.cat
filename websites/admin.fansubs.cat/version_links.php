<?php
$header_title="Enllaços de la versió d'anime - Anime";
$page="anime";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT v.*, s.name series_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN series s ON v.series_id=s.id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);

	$results = query("SELECT s.* FROM series s WHERE id=".$row['series_id']);
	$series = mysqli_fetch_assoc($results) or crash('Series not found');
	mysqli_free_result($results);

	$resultss = query("SELECT ss.* FROM season ss WHERE ss.series_id=".$row['series_id']." ORDER BY ss.number ASC");
	$seasons = array();
	while ($ssrow = mysqli_fetch_assoc($resultss)) {
		array_push($seasons, $ssrow);
	}
	mysqli_free_result($resultss);

	$resulte = query("SELECT e.*, et.title, ss.number season_number FROM episode e LEFT JOIN season ss ON e.season_id=ss.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".escape($_GET['id'])." WHERE e.series_id=".$row['series_id']." ORDER BY ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, e.name ASC");
	$episodes = array();
	while ($rowe = mysqli_fetch_assoc($resulte)) {
		array_push($episodes, $rowe);
	}
	mysqli_free_result($resulte);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços de la versió</h4>
					<hr>
					<p class="text-center">Aquests són els enllaços de la versió de "<b><?php echo htmlspecialchars($row['series_name']); ?></b>" feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
					<hr>
					<div class="text-center">
						<button onclick="copyToClipboard('<?php echo 'https://anime.fansubs.cat/'.($series['type']=='movie' ? "films" : "series").'/'.$series['slug']; ?>', $(this));" class="btn btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç a l'anime en general</button>
						<button onclick="copyToClipboard('<?php echo 'https://anime.fansubs.cat/'.($series['type']=='movie' ? "films" : "series").'/'.$series['slug']; ?>?v=<?php echo $_GET['id']; ?>', $(this));" class="btn btn-info"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç a la versió específica</button>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços per a incrustar de capítols concrets</h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead>
							<tr>
								<th>Capítol</th>
								<th class="text-center">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
	for ($i=0;$i<count($episodes);$i++) {
		$episode_name='';
		if (!empty($episodes[$i]['season_number'])) {
			$episode_name.='<strong>Temp. '.$episodes[$i]['season_number'].' - ';
		} else {
			$episode_name.='<strong>Altres - ';
		}
		if (!empty($episodes[$i]['number'])) {
			if (!empty($episodes[$i]['name'])) {
				$episode_name.='Cap. '.floatval($episodes[$i]['number']).':</strong> '.htmlspecialchars($episodes[$i]['title']);
			} else {
				$episode_name.='Cap. '.floatval($episodes[$i]['number']).'</strong>';
			}
		} else {
			$episode_name.=$episodes[$i]['name'].'</strong>';
		}

		$resultl = query("SELECT l.* FROM link l WHERE l.version_id=".escape($_GET['id'])." AND l.episode_id=".$episodes[$i]['id']." ORDER BY l.variant_name ASC, l.id ASC");
		$links = array();
		while ($rowl = mysqli_fetch_assoc($resultl)) {
			$resultli = query("SELECT li.* FROM link_instance li WHERE li.url IS NOT NULL AND li.link_id=".$rowl['id']." ORDER BY li.url ASC");
			$link_instances = array();
			while ($rowli = mysqli_fetch_assoc($resultli)) {
				array_push($link_instances, $rowli);
			}
			mysqli_free_result($resultli);

			if (!empty($link_instances)) {
?>
						<tr>
							<td style="width: 80%;"><?php echo $episode_name . ' (Variant "'.$rowl['variant_name'].'")'; ?></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo 'https://anime.fansubs.cat/embed/'.$rowl['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
						</tr>
<?php
			}
		}
		mysqli_free_result($resultl);
	}
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
	$resultex = query("SELECT l.* FROM link l WHERE l.version_id=".escape($_GET['id'])." AND l.episode_id IS NULL ORDER BY l.extra_name ASC, l.id ASC");
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
						<thead>
							<tr>
								<th>Material extra</th>
								<th class="text-center">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
		}
		$resultli = query("SELECT li.* FROM link_instance li WHERE li.link_id=".$rowex['id']." ORDER BY li.url ASC");
		$link_instances = array();
		while ($rowli = mysqli_fetch_assoc($resultli)) {
			array_push($link_instances, $rowli);
		}

		if (!empty($link_instances)) {
?>
					<tr>
						<td style="width: 80%;"><strong><?php echo $rowex['extra_name']; ?></strong></td>
						<td class="text-center"><button onclick="copyToClipboard('<?php echo 'https://anime.fansubs.cat/embed/'.$rowex['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
					</tr>
<?php
		}
		mysqli_free_result($resultli);
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

include("footer.inc.php");
?>
