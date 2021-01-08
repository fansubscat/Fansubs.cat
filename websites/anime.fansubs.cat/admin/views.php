<?php
$header_title="Eines";
$page="tools";
include("header.inc.php");
require_once("../common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_SESSION['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_SESSION['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	} else if ($_SESSION['admin_level']>=3 && !empty($_GET['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_GET['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	}
?>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="totals-tab" data-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Visualitzacions globals</a>
				</li>
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link" id="fansub-tab" data-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Visualitzacions <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade show active" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Visualitzacions en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php" class="btn btn-primary"><span class="fa fa-redo pr-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Sèrie</th>
												<th scope="col">Capítol</th>
												<th scope="col" style="width: 30%;">Progrés</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',e.number,': '),''),et.title),e.name),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),'Capítol ',e.number),'(Capítol sense nom)')) episode_name, ((ps.time_spent/60)/IF(IFNULL(e.duration,1)=0,1,IFNULL(e.duration,1)))*100 progress, UNIX_TIMESTAMP(ps.last_update) last_update FROM play_session ps LEFT JOIN link l ON ps.link_id=l.id LEFT JOIN version v ON l.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN season se ON e.season_id=se.id LEFT JOIN episode_title et ON l.version_id=et.version_id AND l.episode_id=et.episode_id ORDER BY UNIX_TIMESTAMP(ps.last_update)<".(date('U')-120)." ASC, ps.play_id ASC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['last_update']<date('U')-120 ? "bg-info" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Les visualitzacions que romanguin encallades durant més de 2 hores seran descartades (&lt;50%) o incorporades com a visualització real (≥50%).</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres 25 visualitzacions</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Sèrie</th>
												<th scope="col">Capítol</th>
												<th scope="col" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',e.number,': '),''),et.title),e.name),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),'Capítol ',e.number),'(Capítol sense nom)')) episode_name, vl.date FROM view_log vl LEFT JOIN link l ON vl.link_id=l.id LEFT JOIN version v ON l.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN season se ON e.season_id=se.id LEFT JOIN episode_title et ON l.version_id=et.version_id AND l.episode_id=et.episode_id ORDER BY vl.date DESC LIMIT 25");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td class="text-center"><?php echo $row['date']; ?></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
							</article>
						</div>
					</div>
				</div>
<?php
	if (!empty($fansub)) {
?>
				<div class="tab-pane fade" id="fansub" role="tabpanel" aria-labelledby="fansub-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Visualitzacions en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php" class="btn btn-primary"><span class="fa fa-redo pr-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Sèrie</th>
												<th scope="col">Capítol</th>
												<th scope="col" style="width: 30%;">Progrés</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',e.number,': '),''),et.title),e.name),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),'Capítol ',e.number),'(Capítol sense nom)')) episode_name, ((ps.time_spent/60)/IF(IFNULL(e.duration,1)=0,1,IFNULL(e.duration,1)))*100 progress FROM play_session ps LEFT JOIN link l ON ps.link_id=l.id LEFT JOIN version v ON l.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN season se ON e.season_id=se.id LEFT JOIN episode_title et ON l.version_id=et.version_id AND l.episode_id=et.episode_id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") ORDER BY UNIX_TIMESTAMP(ps.last_update)<".(date('U')-120)." ASC, ps.play_id ASC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['last_update']<date('U')-120 ? "bg-info" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Les visualitzacions que romanguin encallades durant més de 2 hores seran descartades (&lt;50%) o incorporades com a visualització real (≥50%).</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres 25 visualitzacions</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Sèrie</th>
												<th scope="col">Capítol</th>
												<th scope="col" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',e.number,': '),''),et.title),e.name),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(se.name IS NULL,NULL,CONCAT(se.name,' - ')),IF(s.show_seasons=1 AND (SELECT COUNT(*) FROM season se2 WHERE se2.series_id=s.id)>1,CONCAT('Temporada ', se.number, ' - '),'')),'Capítol ',e.number),'(Capítol sense nom)')) episode_name, vl.date FROM view_log vl LEFT JOIN link l ON vl.link_id=l.id LEFT JOIN version v ON l.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN season se ON e.season_id=se.id LEFT JOIN episode_title et ON l.version_id=et.version_id AND l.episode_id=et.episode_id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") ORDER BY vl.date DESC LIMIT 25");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td class="text-center"><?php echo $row['date']; ?></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
							</article>
						</div>
					</div>
				</div>
<?php
	}
?>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
