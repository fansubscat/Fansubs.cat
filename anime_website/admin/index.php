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
			<p class="text-center">Aquí pots administrar el contingut d'anime.fansubs.cat.</p>
			<p class="text-center">Les <strong>sèries</strong> contenen les fitxes de cada sèrie/film i els seus capítols.<br />Les <strong>versions</strong> corresponen a una versió subtitulada d'una sèrie amb els enllaços corresponents.</p>
			<p class="text-center">Per a afegir una sèrie nova, primer cal crear-ne la fitxa de sèrie, i després la versió amb els enllaços.</p>
<?php
	if ($_SESSION['admin_level']<2) {
?>
			<p class="text-center text-danger">No tens permisos per a crear noves sèries, si et cal, demana a algú altre que ho faci.</p>
<?php
	}
?>
			<h4 class="card-title text-center mb-4 mt-4">Accions habituals</h4>
			<hr>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
			<div class="text-center p-2">
				<a href="series_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una sèrie nova</a>
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
		</article>
	</div>
</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
