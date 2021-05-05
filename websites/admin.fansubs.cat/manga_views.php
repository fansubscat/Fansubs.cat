<?php
$header_title="Darreres lectures de manga - Anàlisi";
$page="analytics";
include("header.inc.php");
require_once("common.inc.php");

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
					<a class="nav-link active" id="fansub-tab" data-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Lectures de manga <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Lectures globals</a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Lectures de manga en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="manga_views.php" class="btn btn-primary"><span class="fa fa-redo pr-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col" style="width: 20%;">Manga</th>
												<th scope="col" style="width: 45%;">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" class="text-center" style="width: 20%;">Progrés</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(m.name, '(enllaç esborrat)') manga_name,IF(ct.title IS NOT NULL,IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),IF(v.show_chapter_numbers=1,CONCAT('Capítol ',TRIM(c.number)+0,': '),''),ct.title),c.name),IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),'Capítol ',TRIM(c.number)+0),IF(fi.chapter_id IS NULL,CONCAT('Extra: ', fi.extra_name), '(Capítol sense nom)'))) chapter_name, (ps.pages_read/fi.number_of_pages)*100 progress, UNIX_TIMESTAMP(ps.last_update) last_update, ps.ip, ps.user_agent, ps.read_counted FROM read_session ps LEFT JOIN file fi ON ps.file_id=fi.id LEFT JOIN manga_version v ON fi.manga_version_id=v.id LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN chapter_title ct ON fi.manga_version_id=ct.manga_version_id AND fi.chapter_id=ct.chapter_id WHERE ps.archived=0 AND ps.reader_closed=0 AND UNIX_TIMESTAMP(ps.last_update)>=".(date('U')-120)." ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['manga_name']; ?></td>
												<td scope="col"><?php echo $row['chapter_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['ip'], $row['user_agent']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['last_update']<date('U')-120 ? "bg-info" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_type_manga($row['user_agent']); ?>></div></td>
												<td class="text-center"><div<?php echo $row['read_counted']==1 ? ' class="fa fa-thumbs-up" style="color: green;" title="Comptada com a lectura"' : ' class="fa fa-thumbs-down" style="color: red;" title="De moment no compta com a lectura"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Aquesta llista només inclou de manera fiable les lectures de navegadors, la resta poden fluctuar i s'incorporaran més tard a les estadístiques. El progrés és sols orientatiu.</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres <?php echo $limit; ?> lectures de manga</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Manga</th>
												<th scope="col">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(m.name, '(enllaç esborrat)') manga_name,IF(ct.title IS NOT NULL,IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),IF(v.show_chapter_numbers=1,CONCAT('Capítol ',TRIM(c.number)+0,': '),''),ct.title),c.name),IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),'Capítol ',TRIM(c.number)+0),IF(fi.chapter_id IS NULL,CONCAT('Extra: ', fi.extra_name), '(Capítol sense nom)'))) chapter_name, vl.date, vl.ip, vl.user_agent FROM manga_view_log vl LEFT JOIN file fi ON vl.file_id=fi.id LEFT JOIN manga_version v ON fi.manga_version_id=v.id LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN chapter_title ct ON fi.manga_version_id=ct.manga_version_id AND fi.chapter_id=ct.chapter_id ORDER BY vl.date DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['manga_name']; ?></td>
												<td scope="col"><?php echo $row['chapter_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['ip'], $row['user_agent']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_type_manga($row['user_agent']); ?>></div></td>
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
				<div class="tab-pane fade show active" id="fansub" role="tabpanel" aria-labelledby="fansub-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Lectures de manga en curs</h4>
								<hr>
								<div class="text-center pb-3">
									<a href="manga_views.php" class="btn btn-primary"><span class="fa fa-redo pr-2"></span>Refresca</a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col" style="width: 20%;">Manga</th>
												<th scope="col" style="width: 45%;">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" class="text-center" style="width: 20%;">Progrés</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(m.name, '(enllaç esborrat)') manga_name,IF(ct.title IS NOT NULL,IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),IF(v.show_chapter_numbers=1,CONCAT('Capítol ',TRIM(c.number)+0,': '),''),ct.title),c.name),IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),'Capítol ',TRIM(c.number)+0),IF(fi.chapter_id IS NULL,CONCAT('Extra: ', fi.extra_name), '(Capítol sense nom)'))) chapter_name, (ps.pages_read/fi.number_of_pages)*100 progress, UNIX_TIMESTAMP(ps.last_update) last_update, ps.ip, ps.user_agent, ps.read_counted FROM read_session ps LEFT JOIN file fi ON ps.file_id=fi.id LEFT JOIN manga_version v ON fi.manga_version_id=v.id LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN chapter_title ct ON fi.manga_version_id=ct.manga_version_id AND fi.chapter_id=ct.chapter_id WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND ps.archived=0 AND ps.reader_closed=0 AND UNIX_TIMESTAMP(ps.last_update)>=".(date('U')-120)." ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['manga_name']; ?></td>
												<td scope="col"><?php echo $row['chapter_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['ip'], $row['user_agent']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['last_update']<date('U')-120 ? "bg-info" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_type_manga($row['user_agent']); ?>></div></td>
												<td class="text-center"><div<?php echo $row['read_counted']==1 ? ' class="fa fa-thumbs-up" style="color: green;" title="Comptada com a lectura"' : ' class="fa fa-thumbs-down" style="color: red;" title="De moment no compta com a lectura"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small">Aquesta llista només inclou de manera fiable les lectures de navegadors, la resta poden fluctuar i s'incorporaran més tard a les estadístiques. El progrés és sols orientatiu.</p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darreres <?php echo $limit; ?> lectures de manga</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Manga</th>
												<th scope="col">Capítol</th>
												<th scope="col" class="text-center" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;">Data</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(m.name, '(enllaç esborrat)') manga_name,IF(ct.title IS NOT NULL,IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),IF(v.show_chapter_numbers=1,CONCAT('Capítol ',TRIM(c.number)+0,': '),''),ct.title),c.name),IF(c.number IS NOT NULL,CONCAT(IFNULL(IF(vo.name IS NULL,NULL,CONCAT(vo.name,' - ')),IF(v.show_volumes=1 AND (SELECT COUNT(*) FROM volume vo2 WHERE vo2.manga_id=m.id)>1,CONCAT('Volum ', vo.number, ' - '),'')),'Capítol ',TRIM(c.number)+0),IF(fi.chapter_id IS NULL,CONCAT('Extra: ', fi.extra_name), '(Capítol sense nom)'))) chapter_name, vl.date, vl.ip, vl.user_agent FROM manga_view_log vl LEFT JOIN file fi ON vl.file_id=fi.id LEFT JOIN manga_version v ON fi.manga_version_id=v.id LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN chapter_title ct ON fi.manga_version_id=ct.manga_version_id AND fi.chapter_id=ct.chapter_id WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") ORDER BY vl.date DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td scope="col"><?php echo $row['manga_name']; ?></td>
												<td scope="col"><?php echo $row['chapter_name']; ?></td>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['ip'], $row['user_agent']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_type_manga($row['user_agent']); ?>></div></td>
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
