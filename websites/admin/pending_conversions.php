<?php
$header_title="Conversions pendents - Eines";
$page="tools";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Conversions pendents</h4>
					<hr>
					<div class="text-center pb-3">
						<a href="pending_conversions.php" class="btn btn-primary"><span class="fa fa-redo pe-2"></span>Refresca</a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="width: 12%;">Id. fitxer</th>
								<th scope="col" style="width: 30%;">Carpeta interna</th>
								<th scope="col">Enllaç</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT l.*,
				s.type,
				v.storage_folder,
				v.storage_processing,
				IF(f.extra_name IS NULL, FALSE, TRUE) is_extra
			FROM link l
				LEFT JOIN file f ON l.file_id=f.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE url NOT LIKE 'storage://%'
				AND NOT EXISTS (SELECT * FROM link l2 WHERE l2.file_id=l.file_id AND l2.url LIKE 'storage://%')
			ORDER BY f.id DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="3" class="text-center">- No hi ha cap conversió pendent -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<td class="align-middle"><?php echo htmlspecialchars($row['file_id']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['storage_folder']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['url']); ?></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
