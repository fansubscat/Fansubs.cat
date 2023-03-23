<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Enllaços de la versió d'anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Enllaços de la versió de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Enllaços de la versió d'acció real - Acció real";
		$page="liveaction";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$divisions_name = "Temporada";
		$divisions_short = "Temp.";
		$divisions_plural = "temporades";
		$divisions_anchor = "temporada";
		$link_url=ANIME_URL;
		break;
	case 'liveaction':
		$divisions_name = "Temporada";
		$divisions_short = "Temp.";
		$divisions_plural = "temporades";
		$divisions_anchor = "temporada";
		$link_url=LIVEACTION_URL;
		break;
	case 'manga':
		$divisions_name = "Volum";
		$divisions_short = "Vol.";
		$divisions_plural = "volums";
		$divisions_anchor = "volum";
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

	$resultd = query("SELECT d.* FROM division d WHERE d.series_id=".$row['series_id']." AND d.number_of_episodes>0 ORDER BY d.number ASC");
	$divisions = array();
	while ($drow = mysqli_fetch_assoc($resultd)) {
		array_push($divisions, $drow);
	}
	mysqli_free_result($resultd);

	$resulte = query("SELECT e.*, et.title, d.number division_number, d.name division_name FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".escape($_GET['id'])." WHERE e.series_id=".$row['series_id']." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
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
						<button onclick="copyToClipboard('<?php echo $link_url.'/'.get_hentai_slug($series).$series['slug']; ?>', $(this));" class="btn btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç a la fitxa en general</button>
						<button onclick="copyToClipboard('<?php echo $link_url.'/'.get_hentai_slug($series).$series['slug']; ?>?v=<?php echo $_GET['id']; ?>', $(this));" class="btn btn-info"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç a la versió específica</button>
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
					<h4 class="card-title text-center mb-4 mt-1">Enllaços a <?php echo $divisions_plural; ?></h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead>
							<tr>
								<th><?php echo $divisions_name; ?></th>
								<th class="text-center">Enllaços</th>
							</tr>
						</thead>
						<tbody>
<?php
		foreach ($divisions as $division) {
?>
						<tr>
							<td style="width: 70%;"><strong><?php echo !empty($division['name']) ? $division['name'] : $divisions_name." ".$division['number']; ?></strong></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/'.get_hentai_slug($series).$series['slug'].'#'.$divisions_anchor.'-'.$division['number']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button> <button onclick="copyToClipboard('<?php echo $link_url.'/'.get_hentai_slug($series).$series['slug'].'?v='.$_GET['id'].'#'.$divisions_anchor.'-'.$division['number']; ?>', $(this));" class="btn btn-sm btn-info"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
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
		if (!empty($episodes[$i]['division_name'])) {
			$episode_name.='<strong>'.$episodes[$i]['division_name'].' - ';
		} else if (!empty($episodes[$i]['division_number'])) {
			$episode_name.='<strong>'.$divisions_short.' '.$episodes[$i]['division_number'].' - ';
		} else {
			$episode_name.='<strong>Altres - ';
		}
		if (!empty($episodes[$i]['number'])) {
			if (!empty($episodes[$i]['title'])) {
				$episode_name.='Cap. '.floatval($episodes[$i]['number']).':</strong> '.htmlspecialchars($episodes[$i]['title']);
			} else {
				$episode_name.='Cap. '.floatval($episodes[$i]['number']).'</strong>';
			}
		} else {
			$episode_name.=$episodes[$i]['description'].'</strong>';
		}

		$resultf = query("SELECT f.* FROM file f WHERE f.version_id=".escape($_GET['id'])." AND f.episode_id=".$episodes[$i]['id']." ORDER BY f.variant_name ASC, f.id ASC");
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
							<td style="width: 85%;"><?php echo $episode_name . ' (Variant "'.$rowf['variant_name'].'")'; ?></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/embed/'.$rowf['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
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
						<thead>
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
						<td class="text-center"><button onclick="copyToClipboard('<?php echo $link_url.'/embed/'.$rowex['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
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
