<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Edició de versions - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Edició de versions - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Edició de versions - Imatge real";
		$page="liveaction";
	break;
}

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$content="anime";
	break;
	case 'manga':
		$content="manga";
	break;
	case 'liveaction':
		$content="contingut d’imatge real";
	break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">De quin <?php echo $content; ?> vols crear una versió?</h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col">Nom</th>
								<th class="text-center" scope="col">Tipus</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Acció</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,COUNT(DISTINCT v.id) versions FROM series s LEFT JOIN version v ON s.id=v.series_id WHERE type='$type' GROUP BY s.id ORDER BY s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center">- No hi ha cap <?php echo $content; ?> -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo get_subtype_name($row['subtype']); ?></td>
								<td class="align-middle text-center"><?php echo $row['number_of_episodes']; ?></td>
								<td class="align-middle text-center"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center"><a href="version_edit.php?type=<?php echo $type; ?>&series_id=<?php echo $row['id']; ?>" title="Crea’n una versió" class="fa fa-plus-square p-1 text-success"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<div class="text-center">
						<a href="series_edit.php?type=<?php echo $type; ?>" class="btn btn-primary">No hi és? Afegeix un <?php echo $content; ?> nou</a>
					</div>
<?php
	}
?>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
