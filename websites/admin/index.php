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
			<p class="text-center">Cada <strong>anime</strong>, <strong>manga</strong> o <strong>contingut d'acció real</strong> conté una fitxa genèrica amb les seves temporades (o volums) i capítols.<br />Les <strong>versions</strong> corresponen a l'edició d'un o més fansubs i inclouen els enllaços o fitxers corresponents.<br />Per a afegir un contingut nou, primer cal crear-ne la fitxa genèrica, i després la versió amb els enllaços o fitxers.</p>
			<p class="text-center">L'apartat de <strong>notícies</strong> permet gestionar les notícies dels webs o blogs dels diferents fansubs.<br />Excepte en casos molt concrets, no és necessari afegir, modificar ni suprimir notícies a mà.</p>
			<p class="text-center">Al menú d'<strong>anàlisi</strong> trobaràs un seguit d'opcions per a veure quin és el consum del material.</p>
			<p class="text-center">Si tens dubtes, consulta l'<strong>ajuda</strong> que tens a la part superior dreta o contacta amb els administradors.</p>
<?php
	if ($_SESSION['admin_level']<2) {
?>
			<p class="text-center alert alert-warning">No tens permisos per a crear fitxes d'anime, manga o acció real. Si et cal, demana a algú altre que ho faci.</p>
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
							<a href="series_edit.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un anime nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=anime" class="btn btn-primary"><span class="fa fa-edit pr-2"></span>Edita una versió existent</a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Manga</h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un manga nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=manga" class="btn btn-primary"><span class="fa fa-edit pr-2"></span>Edita una versió existent</a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Acció real</h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un contingut nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=liveaction" class="btn btn-primary"><span class="fa fa-edit pr-2"></span>Edita una versió existent</a>
						</div>
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
