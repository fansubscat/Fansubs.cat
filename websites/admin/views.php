<?php
$page="analytics";
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Darreres visualitzacions d’anime - Anàlisi";
	break;
	case 'manga':
		$header_title="Darreres visualitzacions de manga - Anàlisi";
	break;
	case 'liveaction':
		$header_title="Darreres visualitzacions d’imatge real - Anàlisi";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$content_uc="Anime";
		$content_prep="d’anime";
		$view_name="Visualitzacions";
		$view_name_lc="visualitzacions";
		$division_name="Temporada";
	break;
	case 'manga':
		$content_uc="Manga";
		$content_prep="de manga";
		$view_name="Lectures";
		$view_name_lc="lectures";
		$division_name="Volum";
	break;
	case 'liveaction':
		$content_uc="Contingut d’imatge real";
		$content_prep="de contingut d’imatge real";
		$view_name="Visualitzacions";
		$view_name_lc="visualitzacions";
		$division_name="Temporada";
	break;
}

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

	$limit = 25;
	if (!empty($_GET['limit']) && is_numeric($_GET['limit'])){
		$limit = escape($_GET['limit']);
	}
?>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Visualitzacions <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Visualitzacions globals</a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo $view_name; ?> <?php echo $content_prep; ?> en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-redo pe-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 20%;"><?php echo $content_uc; ?></th>
												<th scope="col" style="width: 45%;">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" class="text-center" style="width: 20%;">Progrés</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',TRIM(e.number)+0,': '),''),et.title),e.description),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),'Capítol ',TRIM(e.number)+0),IF(f.episode_id IS NULL,CONCAT('Extra: ', f.extra_name), '(Capítol sense nom)'))) episode_name, ps.user_id, ps.anon_id, (ps.progress/ps.length)*100 progress, UNIX_TIMESTAMP(ps.updated) updated, ps.source, ps.ip, ps.user_agent, ps.is_casted, ps.view_counted, s.rating FROM view_session ps LEFT JOIN file f ON ps.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id WHERE s.type='$type' AND UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)." ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['updated']<date('U')-120 ? "bg-primary" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center"><div<?php echo !empty($row['view_counted']) ? ' class="fa fa-thumbs-up" style="color: green;" title="Comptada com a visualització"' : ' class="fa fa-thumbs-down" class="hentai" title="De moment no compta com a visualització"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Depenent de la tecnologia utilitzada, algunes <?php echo $view_name_lc; ?> no mostren el progrés i apareixen immediatament com a completades.</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres <?php echo $limit; ?> <?php echo $view_name_lc; ?> <?php echo $content_prep; ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo $content_uc; ?></th>
												<th scope="col">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',TRIM(e.number)+0,': '),''),et.title),e.description),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),'Capítol ',TRIM(e.number)+0),IF(f.episode_id IS NULL,CONCAT('Extra: ', f.extra_name), '(Capítol sense nom)'))) episode_name, ps.user_id, ps.anon_id, (ps.progress/ps.length)*100 progress, UNIX_TIMESTAMP(ps.updated) updated, ps.source, ps.ip, ps.user_agent, ps.is_casted, UNIX_TIMESTAMP(ps.view_counted) view_counted, s.rating FROM view_session ps LEFT JOIN file f ON ps.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id WHERE s.type='$type' AND ps.view_counted IS NOT NULL ORDER BY ps.view_counted DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center" class="text-center"><?php echo date('Y-m-d H:i:s', $row['view_counted']); ?></td>
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
				<div class="tab-pane fade show active" id="fansub" role="tabpanel" aria-labelledby="fansub-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo $view_name; ?> <?php echo $content_prep; ?> en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-redo pe-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 20%;"><?php echo $content_uc; ?></th>
												<th scope="col" style="width: 45%;">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" class="text-center" style="width: 20%;">Progrés</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php

$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',TRIM(e.number)+0,': '),''),et.title),e.description),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),'Capítol ',TRIM(e.number)+0),IF(f.episode_id IS NULL,CONCAT('Extra: ', f.extra_name), '(Capítol sense nom)'))) episode_name, ps.user_id, ps.anon_id, (ps.progress/ps.length)*100 progress, UNIX_TIMESTAMP(ps.updated) updated, ps.source, ps.ip, ps.user_agent, ps.is_casted, ps.view_counted, s.rating FROM view_session ps LEFT JOIN file f ON ps.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id WHERE s.type='$type' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)." ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['updated']<date('U')-120 ? "bg-primary" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center"><div<?php echo !empty($row['view_counted']) ? ' class="fa fa-thumbs-up" style="color: green;" title="Comptada com a visualització"' : ' class="fa fa-thumbs-down" class="hentai" title="De moment no compta com a visualització"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Depenent de la tecnologia utilitzada, algunes <?php echo $view_name_lc; ?> no mostren el progrés i apareixen immediatament com a completades.</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres <?php echo $limit; ?> <?php echo $view_name_lc; ?> <?php echo $content_prep; ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo $content_uc; ?></th>
												<th scope="col">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(s.name, '(enllaç esborrat)') series_name,IF(et.title IS NOT NULL,IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),IF(s.show_episode_numbers=1,CONCAT('Capítol ',TRIM(e.number)+0,': '),''),et.title),e.description),IF(e.number IS NOT NULL,CONCAT(IFNULL(IF(d.name IS NULL,NULL,CONCAT(d.name,' - ')),IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id)>1,CONCAT('$division_name ', TRIM(d.number)+0, ' - '),'')),'Capítol ',TRIM(e.number)+0),IF(f.episode_id IS NULL,CONCAT('Extra: ', f.extra_name), '(Capítol sense nom)'))) episode_name, ps.user_id, ps.anon_id, (ps.progress/ps.length)*100 progress, UNIX_TIMESTAMP(ps.updated) updated, ps.source, ps.ip, ps.user_agent, ps.is_casted, UNIX_TIMESTAMP(ps.view_counted) view_counted, s.rating FROM view_session ps LEFT JOIN file f ON ps.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id WHERE s.type='$type' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND ps.view_counted IS NOT NULL ORDER BY ps.view_counted DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<td scope="col"><?php echo $row['series_name']; ?></td>
												<td scope="col"><?php echo $row['episode_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center" class="text-center"><?php echo date('Y-m-d H:i:s', $row['view_counted']); ?></td>
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
