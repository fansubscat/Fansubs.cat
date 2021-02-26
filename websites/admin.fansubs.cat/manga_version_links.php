<?php
$header_title="Enllaços de la versió de manga - Manga";
$page="manga";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT v.*, m.name manga_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM manga_version v LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);

	$resultm = query("SELECT m.* FROM manga m WHERE id=".$row['manga_id']);
	$manga = mysqli_fetch_assoc($resultm) or crash('Manga not found');
	mysqli_free_result($resultm);

	$resultv = query("SELECT v.* FROM volume v LEFT JOIN manga m ON v.manga_id=m.id WHERE v.manga_id=".$row['manga_id']." ORDER BY v.number ASC");
	$volumes = array();
	while ($rowv = mysqli_fetch_assoc($resultv)) {
		array_push($volumes, $rowv);
	}
	mysqli_free_result($resultv);

	$resultc = query("SELECT c.*, ct.title, v.number volume_number FROM chapter c LEFT JOIN volume v ON c.volume_id=v.id LEFT JOIN chapter_title ct ON c.id=ct.chapter_id AND ct.manga_version_id=".escape($_GET['id'])." WHERE c.manga_id=".$row['manga_id']." ORDER BY v.number IS NULL ASC, v.number ASC, c.number IS NULL ASC, c.number ASC, c.name ASC");
	$chapters = array();
	while ($rowc = mysqli_fetch_assoc($resultc)) {
		array_push($chapters, $rowc);
	}
	mysqli_free_result($resultc);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços de la versió</h4>
					<hr>
					<p class="text-center">Aquests són els enllaços de la versió de "<b><?php echo htmlspecialchars($row['manga_name']); ?></b>" feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
					<hr>
					<div class="text-center">
						<button onclick="copyToClipboard('<?php echo 'https://manga.fansubs.cat/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug']; ?>', $(this));" class="btn btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç al manga en general</button>
						<button onclick="copyToClipboard('<?php echo 'https://manga.fansubs.cat/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug']; ?>?v=<?php echo $_GET['id']; ?>', $(this));" class="btn btn-info"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç a la versió específica</button>
					</div>
				</article>
			</div>
		</div>
<?php
	if (count($volumes)>1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Enllaços a volums</h4>
					<hr>
					<input type="hidden" id="text_to_copy" value=""/>
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead>
							<tr>
								<th>Volum</th>
								<th class="text-center">Enllaços</th>
							</tr>
						</thead>
						<tbody>
<?php
		foreach ($volumes as $volume) {
?>
						<tr>
							<td style="width: 70%;"><strong><?php echo "Volum ".$volume['number'].(!empty($volume['name']) ? " (".$volume['name'].")" : ""); ?></strong></td>
							<td class="text-center"><button onclick="copyToClipboard('<?php echo 'https://manga.fansubs.cat/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].'#volum-'.$volume['number']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button> <button onclick="copyToClipboard('<?php echo 'https://manga.fansubs.cat/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].'?v='.$_GET['id'].'#volum-'.$volume['number']; ?>', $(this));" class="btn btn-sm btn-info"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
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
	for ($i=0;$i<count($chapters);$i++) {
		$chapter_name='';
		if (!empty($chapters[$i]['volume_number'])) {
			$chapter_name.='<strong>Vol. '.$chapters[$i]['volume_number'].' - ';
		} else {
			$chapter_name.='<strong>Altres - ';
		}
		if (!empty($chapters[$i]['number'])) {
			if (!empty($chapters[$i]['name'])) {
				$chapter_name.='Cap. '.floatval($chapters[$i]['number']).'</strong> '.htmlspecialchars($chapters[$i]['title']);
			} else {
				$chapter_name.='Cap. '.floatval($chapters[$i]['number']).'</strong>';
			}
		} else {
			$chapter_name.=$chapters[$i]['name'].'</strong>';
		}

		$resultf = query("SELECT f.* FROM file f WHERE f.original_filename IS NOT NULL AND f.manga_version_id=".escape($_GET['id'])." AND f.chapter_id=".$chapters[$i]['id']." ORDER BY f.variant_name ASC, f.id ASC");
		$files = array();
		while ($rowf = mysqli_fetch_assoc($resultf)) {
			array_push($files, $rowf);
?>
							<tr>
								<td style="width: 85%;"><?php echo $chapter_name . ' (Variant "'.$rowf['variant_name'].'")'; ?></td>
								<td class="text-center"><button onclick="copyToClipboard('<?php echo 'https://manga.fansubs.cat/embed/'.$rowf['id']; ?>', $(this));" class="btn btn-sm btn-primary"><span class="fa fa-clipboard pr-2"></span>Copia l'enllaç</button></td>
							</tr>
<?php
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
						<td style="width: 85%;"><strong><?php echo $rowex['extra_name']; ?></strong></td>
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
