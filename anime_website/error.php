<?php
require_once("db.inc.php");

$header_page_title='Error';
$header_tab='error';

http_response_code(!empty($_GET['code']) ? $_GET['code'] : 404);

require_once('header.inc.php');
?>	
				<div class="section" style="text-align: center;">
					<h2 class="section-title">S'ha produït un error</h2>
					<div class="section-content">
<?php
if (isset($_GET['code']) && $_GET['code']==403){
?>
						<strong>No tens permisos per a accedir a aquesta adreça!</strong><br /><br />Et recomanem que tornis a la pàgina principal i provis de trobar el que cerques allà!
<?php
} else {
?>
						<strong>La pàgina no existeix!</strong><br /><br />És possible que hagis seguit un enllaç antic. Et recomanem que tornis a la pàgina principal i provis de trobar el que cerques allà!
<?php
}
?>
					</div>
				</div>
<?php
require_once('footer.inc.php');
?>
