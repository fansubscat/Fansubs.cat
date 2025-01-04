<?php
if (str_ends_with($_SERVER['HTTP_HOST'], 'hentai.cat')) {
	define('PAGE_TITLE', 'Llista de fansubs de hentai en català');
	define('PAGE_DESCRIPTION', 'Consulta la llista de tots els fansubs de hentai en català a Hentai.cat, el portal que en recopila tot el material!');
} else {
	define('PAGE_TITLE', 'Llista de fansubs en català');
	define('PAGE_DESCRIPTION', 'Consulta la llista de tots els fansubs en català a Fansubs.cat, el portal que en recopila tot el material!');
}
define('PAGE_PATH', '/llista-de-fansubs');
define('PAGE_STYLE_TYPE', 'fansubs');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');
?>
					<div class="fansubs-index">
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-user-group"></i> Fansubs actius</h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 1);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No hem trobat cap fansub actiu!</div></div>
<?php
}
else{
?>
							<div class="fansubs-grouping">
<?php
	while ($row = mysqli_fetch_assoc($result)){
		print_fansub($row);
	}
}
?>
							</div>
						</div>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-landmark"></i> Fansubs històrics</h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 0);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No hem trobat cap fansub històric!</div></div>
<?php
}
else{
?>
							<div class="fansubs-grouping historical-fansubs">
<?php
	while ($row = mysqli_fetch_assoc($result)){
		print_fansub($row);
	}
}
?>
							</div>
						</div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
