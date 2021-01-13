<?php
$header_title="Manteniment - Eines";
$page="tools";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['action'])) {
		switch ($_GET['action']) {
			case 'clear-views':
				log_action("clear-views","S'han buidat totes les visualitzacions i lectures");
				query("DELETE FROM views");
				query("DELETE FROM manga_views");
				$_SESSION['message']="S'han buidat les visualitzacions i lectures correctament.";
				break;
			case 'clear-search-history':
				log_action("clear-search-history","S'han buidat els historials de cerques");
				query("DELETE FROM search_history");
				query("DELETE FROM manga_search_history");
				$_SESSION['message']="S'han buidat els historials de cerques correctament.";
				break;
			case 'clear-logs':
				query("DELETE FROM action_log");
				log_action("clear-logs","S'ha buidat el registre d'accions");
				$_SESSION['message']="S'ha buidat el registre d'accions correctament.";
				break;
		}
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Manteniment</h4>
					<hr>

<?php
	if (!empty($_SESSION['message'])) {
?>
					<p class="alert alert-success text-center"><?php echo $_SESSION['message']; ?></p>
<?php
		$_SESSION['message']=NULL;
	}
?>
					<div class="text-center p-2">
						<a href="maintenance.php?action=clear-logs" class="btn btn-warning" onclick="return confirm('Segur que vols buidar el registre d\'accions? Es perdrà l\'historial de canvis. L\'acció no es pot desfer.')" onauxclick="return false;">Buida el registre d'accions</a>
					</div>
					<div class="text-center p-2">
						<a href="maintenance.php?action=clear-search-history" class="btn btn-warning" onclick="return confirm('Segur que vols buidar l\'historial de cerques? L\'acció no es pot desfer.')" onauxclick="return false;">Buida els historials de cerques</a>
					</div>
					<div class="text-center p-2">
						<a href="maintenance.php?action=clear-views" class="btn btn-danger" onclick="return confirm('Segur que vols esborrar totes les visualitzacions i lectures? Es perdran totes les estadístiques i l\'apartat \'Més populars\' apareixerà buit fins que no hi hagi visualitzacions o lectures noves. L\'acció no es pot desfer.')" onauxclick="return false;">Esborra totes les visualitzacions i lectures</a>
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
