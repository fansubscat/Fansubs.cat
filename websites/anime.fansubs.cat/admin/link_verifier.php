<?php
$header_title="Eines";
$page="tools";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<script>
			var links = [
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE l.url IS NOT NULL AND EXISTS (SELECT vf.version_id FROM rel_version_fansub vf WHERE vf.version_id=v.id AND vf.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$where = ' WHERE l.url IS NOT NULL';
	}

	if (!empty($_GET['version_id']) && is_numeric($_GET['version_id'])) {
		$where .= ' AND l.version_id='.$_GET['version_id'];
	}

	$resultl = query("SELECT l.*,s.name series_name, e.number episode_number, e.name episode_name, ss.number season_number FROM link l LEFT JOIN version v ON l.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN season ss ON e.season_id=ss.id$where ORDER BY s.name ASC, ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, extra_name ASC");
	while ($row = mysqli_fetch_assoc($resultl)) {
		if (!empty($row['episode_id'])){
			if (!empty($row['season_number'])){
				$chapter_name=$row['series_name'].' - Temporada '.$row['season_number'].' - ';
			} else {
				$chapter_name=$row['series_name'].' - Diversos'.' - ';
			}
			if (!empty($row['episode_number'])) {
				$chapter_name.='Capítol '.$row['episode_number'];
			} else {
				$chapter_name.=$row['episode_name'];
			}
		} else {
			$chapter_name = 'Extra - '.$row['extra_name'];
		}
?>
				{link: <?php echo json_encode(htmlspecialchars($row['url'])); ?>, text: <?php echo json_encode(htmlspecialchars($chapter_name)); ?>},
<?php
	}
	mysqli_free_result($resultl);
?>
			];
		</script>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Verificador d'enllaços</h4>
					<hr>
					<p class="text-center">El verificador d'enllaços comprova que tots els enllaços de MEGA i Google Drive estiguin disponibles. En executar-lo, es comprovaran els enllaços un a un i se n'obtindrà un resum. És un procés molt lent, tingues paciència.</p>
					<div class="text-center p-2">
						<button id="link-verifier-button" onclick="verifyLinks(0);" class="btn btn-primary">
							<span id="link-verifier-loading" class="d-none spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
							Verifica tots els enllaços
						</button>
						<div id="link-verifier-progress" class="d-none"></div>
					</div>
				</article>
			</div>
		</div>
		<div id="link-verifier-results" class="d-none container justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Resultats</h4>
					<hr>
					<div class="row text-success">
						<p class="text-right col-sm-6 mb-0 font-weight-bold">Vàlids:</p>
						<p class="text-left col-sm-6 mb-0 font-weight-bold" id="link-verifier-good-links">0</p>
					</div>
					<div class="row text-danger">
						<p class="text-right col-sm-6 mb-0 font-weight-bold">Invàlids:</p>
						<p class="text-left col-sm-6 mb-0 font-weight-bold" id="link-verifier-wrong-links">0</p>
					</div>
					<div class="row text-warning">
						<p class="text-right col-sm-6 mb-0 font-weight-bold">Desconeguts:</p>
						<p class="text-left col-sm-6 mb-0 font-weight-bold" id="link-verifier-failed-links">0</p>
					</div>
					<div class="row text-muted">
						<p class="text-right col-sm-6 mb-0 font-weight-bold">No verificables:</p>
						<p class="text-left col-sm-6 mb-0 font-weight-bold" id="link-verifier-unknown-links">0</p>
					</div>
					<div id="link-verifier-wrong-links-list" class="d-none mt-4">
						<h4 class="card-title text-center mb-4 mt-1">Enllaços invàlids</h4>
						<hr>
					</div>
					<div id="link-verifier-failed-links-list" class="d-none mt-4">
						<h4 class="card-title text-center mb-4 mt-1">Enllaços desconeguts</h4>
						<hr>
					</div>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
