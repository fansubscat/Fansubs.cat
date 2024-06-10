<?php
$header_title="Darrers comentaris";
$page="analytics";

include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_GET['delete_id']) && $_SESSION['admin_level']>=3) {
		log_action("delete-comment", "S’ha suprimit el comentari amb identificador ".$_GET['delete_id']." i les seves respostes (si n’hi havia)");
		query("DELETE FROM comment WHERE id=".escape($_GET['delete_id'])." OR reply_to_comment_id=".escape($_GET['delete_id']));
		$_SESSION['message']="S’ha suprimit correctament.";
		if (!empty($_GET['source_version_id']) && !empty($_GET['source_type'])) {
			header('Location: version_stats.php?type='.$_GET['source_type'].'&id='.$_GET['source_version_id']);
			die();
		}
	}

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
								<h4 class="card-title text-center mb-4 mt-1">Darrers <?php echo $limit; ?> comentaris</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 10%;" class="text-center">Data</th>
												<th scope="col" style="width: 20%;">Contingut</th>
												<th scope="col" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 50%;">Comentari</th>
												<th scope="col" style="width: 5%;" class="text-center">Respost</th>
												<th class="text-center" scope="col">Accions</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, s.name series_name, u.username, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user' ORDER BY c.created DESC LIMIT $limit");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<th scope="row" class="align-middle text-center"><?php echo $row['created']; ?></th>
												<td class="align-middle"><?php echo $row['series_name'].'<br>('.$row['fansubs'].')'; ?></td>
												<td class="align-middle"><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></td>
												<td class="align-middle"><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></td>
												<td class="align-middle text-center"><?php echo $row['last_replied']!=$row['created'] ? 'Sí' : 'No'; ?></td>
												<td class="align-middle text-center text-nowrap">
													<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="Respon" class="fa fa-reply p-1"></a>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
													<a href="comment_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el comentari seleccionat? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
<?php
	}
?>
												</td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
									<p class="text-center text-muted small">En aquesta llista no es mostren els comentaris ni les respostes dels fansubs. Ho pots veure tot a la fitxa pública de cada contingut.</p>
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
								<h4 class="card-title text-center mb-4 mt-1">Darrers <?php echo $limit; ?> comentaris</h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 10%;" class="text-center">Data</th>
												<th scope="col" style="width: 20%;">Contingut</th>
												<th scope="col" style="width: 10%;">Usuari</th>
												<th scope="col" style="width: 50%;">Comentari</th>
												<th scope="col" style="width: 5%;" class="text-center">Respost</th>
												<th class="text-center" scope="col">Accions</th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, s.name series_name, u.username, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=${fansub['id']}) ORDER BY c.created DESC LIMIT $limit");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
												<th scope="row" class="align-middle text-center"><?php echo $row['created']; ?></th>
												<td class="align-middle"><?php echo $row['series_name'].'<br>('.$row['fansubs'].')'; ?></td>
												<td class="align-middle"><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></td>
												<td class="align-middle"><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></td>
												<td class="align-middle text-center"><?php echo $row['last_replied']!=$row['created'] ? 'Sí' : 'No'; ?></td>
												<td class="align-middle text-center text-nowrap">
													<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="Respon" class="fa fa-reply p-1"></a>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
													<a href="comment_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el comentari seleccionat? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
<?php
	}
?>
												</td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
									<p class="text-center text-muted small">En aquesta llista no es mostren els comentaris ni les respostes dels fansubs. Ho pots veure tot a la fitxa pública de cada contingut.</p>
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
