<?php
$header_title="Pàgina principal";
$page="main";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
<div class="container d-flex justify-content-center p-4">
	<div class="card w-100">
		<article class="card-body">
			<h4 class="card-title text-center mb-4 mt-1">Tauler d'administració</h4>
			<hr>
			<p class="text-center"><strong>Et donem la benvinguda al tauler. Aquí pots administrar el contingut dels diferents webs de Fansubs.cat.</strong></p>
			<p class="text-center">Cada <strong>anime</strong> o <strong>manga</strong> conté la fitxa genèrica de l'anime (sèrie o film) o manga i els seus capítols (o volums).<br />Les <strong>versions</strong> corresponen a una versió editada per un o més fansubs amb els enllaços corresponents.<br />Per a afegir un anime o manga nou, primer cal crear-ne la fitxa genèrica, i després la versió amb els enllaços o fitxers.</p>
			<p class="text-center">L'apartat de <strong>notícies</strong> permet configurar l'obtenció automàtica de notícies dels webs o blogs dels diferents fansubs.<br />Excepte en casos molt concrets, no és necessari afegir, modificar ni suprimir notícies a mà.</p>
<?php
	if ($_SESSION['admin_level']<2) {
?>
			<p class="text-center alert alert-warning">No tens permisos per a crear fitxes d'anime ni de manga. Si et cal, demana a algú altre que ho faci.</p>
<?php
	}
?>
			<h4 class="card-title text-center mb-4 mt-4">Accions habituals</h4>
			<hr>
			<div class="container">
				<div class="row">
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Anime</h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un anime nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php" class="btn btn-primary"><span class="fa fa-edit pr-2"></span>Edita una versió existent</a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Manga</h5>
<!--
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="javascript:alert('Encara no està disponible.');" class="btn btn-primary disabled"><span class="fa fa-plus pr-2"></span>Afegeix un manga nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="javascript:alert('Encara no està disponible.');" class="btn btn-primary disabled"><span class="fa fa-plus pr-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="javascript:alert('Encara no està disponible.');" class="btn btn-primary disabled"><span class="fa fa-edit pr-2"></span>Edita una versió existent</a>
						</div>
-->
					<p class="text-center alert alert-warning"><span class="fa fa-exclamation-triangle mb-3" style="font-size: 1.5em;"></span><br />L'edició de manga encara no està disponible en aquesta versió del tauler d'administració.<br />Pots gestionar el manga <a href="https://manga.fansubs.cat/admin.php">aquí</a>.</p>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Notícies</h5>
						<div class="text-center p-2">
							<a href="news_list.php" class="btn btn-primary"><span class="fa fa-bars pr-2"></span>Mostra la llista de notícies</a>
						</div>
						<div class="text-center p-2">
							<a href="news_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una notícia a mà</a> 
						</div>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="fetcher_list.php" class="btn btn-primary"><span class="fa fa-edit pr-2"></span>Edita els recollidors</a>
						</div>
<?php
	}
?>
					</div>
				</div>
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
