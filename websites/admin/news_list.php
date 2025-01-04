<?php
$header_title="Llista de notícies - Notícies";
$page="news";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_GET['delete_id'])) {
		$todelete_result = query("SELECT n.*, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".escape($_GET['delete_id'])."'");
		if (mysqli_num_rows($todelete_result)>1) {
			crash("No es pot esborrar la notícia: més d’una notícia amb el mateix MD5!");
		} else if (mysqli_num_rows($todelete_result)==1) {
			$todelete_row = mysqli_fetch_assoc($todelete_result);
			log_action("delete-news", "S’ha suprimit la notícia «".$todelete_row['title']."» del fansub «".$todelete_row['fansub_name']."»");
			query("DELETE FROM news WHERE MD5(CONCAT(title, date))='".escape($_GET['delete_id'])."'");
		}
		$_SESSION['message']="S’ha suprimit correctament.";
	}

	if (!empty($_GET['delete_pending_id'])) {
		log_action("delete-pending-news", "S’ha suprimit la notícia proposada amb id. ".$_GET['delete_pending_id']);
		query("DELETE FROM pending_news WHERE id=".escape($_GET['delete_pending_id']));
		$_SESSION['message']="S’ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de notícies</h4>
					<hr>

<?php
	if (!empty($_SESSION['message'])) {
?>
					<p class="alert alert-success text-center"><?php echo $_SESSION['message']; ?></p>
<?php
		$_SESSION['message']=NULL;
	}

	$result = query("SELECT pn.* FROM pending_news pn ORDER BY pn.title ASC");
	if (mysqli_num_rows($result)>0) {
?>
					<h5 class="card-title text-center mb-4 mt-1">Hi ha notícies proposades pendents d’aprovar:</h5>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col">Títol</th>
								<th scope="col">Autor</th>
								<th scope="col">Contingut</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
		while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row"><?php echo htmlspecialchars($row['title']); ?></th>
								<td class="small"><?php echo htmlspecialchars($row['sender_name']); ?></td>
								<td class="small"><?php echo htmlspecialchars(substr($row['contents'], 0, 255).'...'); ?></td>
								<td class="align-middle text-center"><a href="news_edit.php?import_pending_id=<?php echo $row['id']; ?>" title="Notícia bona, importa-la" class="fa fa-thumbs-up p-1 text-success"></a> <a href="news_list.php?delete_pending_id=<?php echo $row['id']; ?>" title="Notícia dolenta, suprimeix-la" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la notícia proposada «".$row['title']."»? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-thumbs-down p-1 text-danger"></a></td>
							</tr>
<?php
		}
		mysqli_free_result($result);
?>
						</tbody>
					</table>
					<h5 class="card-title text-center mb-4 mt-4">Notícies ja aprovades:</h5>
<?php
	}
?>

					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col">Fansub</th>
								<th scope="col">Títol</th>
								<th class="text-center" scope="col">Data</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE n.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT MD5(CONCAT(n.title, n.date)) id, n.*, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id$where ORDER BY n.date DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap notícia -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo !empty($row['fansub_name']) ? htmlspecialchars($row['fansub_name']) : '(Notícia interna de Fansubs.cat)'; ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['title']); ?></td>
								<td class="align-middle text-center"><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></th>
								<td class="align-middle text-center text-nowrap"><a href="news_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="news_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la notícia «".$row['title']."»? Només s’hauria de fer en casos molt especials. L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="news_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una notícia a mà</a>
					</div>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
