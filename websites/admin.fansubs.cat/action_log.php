<?php
$header_title="Registre d'accions - Eines";
$page="tools";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Registre d'accions</h4>
					<hr>
					<p class="text-center">Es mostren les darreres 200 accions (les més noves primer).</p>
					<div class="text-center pb-3">
						<a href="action_log.php" class="btn btn-primary"><span class="fa fa-redo pr-2"></span>Refresca</a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col" style="width: 18%;">Data i hora</th>
								<th scope="col" style="width: 12%;">Usuari</th>
								<th scope="col" style="width: 18%;">Acció</th>
								<th scope="col">Text</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT * FROM action_log ORDER BY id DESC LIMIT 200");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center">- No hi ha cap acció -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['author']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['action']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['text']); ?></td>
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
