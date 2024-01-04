<?php
$header_title="Darrers comentaris";
$page="analytics";

include("header.inc.php");

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
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Comentaris per a <?php echo $fansub['name']; ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Tots els comentaris</a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Darrers comentaris</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 15%;">Data</th>
												<th scope="col" style="width: 20%;">Anime</th>
												<th scope="col" style="width: 15%;">Usuari</th>
												<th scope="col" style="width: 50%;">Comentari</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, s.name series_name, u.username, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id ORDER BY c.created DESC");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td><?php echo $row['created']; ?></td>
												<td><?php echo $row['series_name'].'<br>('.$row['fansubs'].')'; ?></td>
												<td><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></td>
												<td><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></td>
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
								<h4 class="card-title text-center mb-4 mt-1">Darrers comentaris</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 15%;">Data</th>
												<th scope="col" style="width: 20%;">Anime</th>
												<th scope="col" style="width: 15%;">Usuari</th>
												<th scope="col" style="width: 50%;">Comentari</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, s.name series_name, u.username, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=${fansub['id']}) ORDER BY c.created DESC");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr>
												<td><?php echo $row['created']; ?></td>
												<td><?php echo $row['series_name'].'<br>('.$row['fansubs'].')'; ?></td>
												<td><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></td>
												<td><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></td>
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
