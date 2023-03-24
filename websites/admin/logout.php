<?php
$header_title="S’ha tancat la sessió";
$skip_navbar=TRUE;
require_once("header.inc.php");
session_destroy();
?>
		<div class="container d-flex h-100 justify-content-center align-items-center">
			<div class="card">
				<article class="card-body text-center">
					<h4 class="card-title text-center mb-4 mt-1">A reveure!</h4>
					<hr>
					<p class="text-center text-success">S’ha tancat la sessió correctament.</p>
					<a href="/" class="btn btn-primary btn-block">Torna a la pàgina principal</a>
				</article>
			</div>
		</div>
<?php
require_once("footer.inc.php");
?>
