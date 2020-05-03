<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Error!';
$header_current_page='error';

http_response_code((isset($_GET['code']) && $_GET['code']!=NULL) ? $_GET['code'] : 404);

require_once('header.inc.php');
?>
					<div class="page-title">
						<h2>Error!</h2>
					</div>
					<div class="article">
						<div style="text-align: center; width: 100%;">
							<p><?php if (isset($_GET['code']) && $_GET['code']==403){
 ?><strong>No tens permisos per a accedir a aquesta adreça!</strong><br /><br />Et recomanem que tornis a la pàgina principal de Fansubs.cat i provis de trobar el que cerques allà!<?php } else { ?><strong>La pàgina no existeix!</strong><br /><br />És possible que hagis seguit un enllaç antic. Et recomanem que tornis a la pàgina principal de Fansubs.cat i provis de trobar el que cerques allà!<?php } ?><br /><br /><strong><a href="/">Torna a la pàgina principal</a></strong></p>
						</div>
					</div>
<?php
require_once('footer.inc.php');
?>
