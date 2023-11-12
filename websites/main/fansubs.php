<?php
define('PAGE_TITLE', 'Llista de fansubs en català');
define('PAGE_PATH', '/llista-de-fansubs');
define('PAGE_STYLE_TYPE', 'fansubs');
define('PAGE_DESCRIPTION', 'Consulta la llista de tots els fansubs en català a Fansubs.cat, el portal que en recopila tot el material!');
require_once("../common.fansubs.cat/header.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");
$show_hentai = (is_robot() || (!empty($user) && is_adult() && empty($user['hide_hentai_access'])));
?>
					<div class="fansubs-index">
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-user-group"></i> Fansubs actius</h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 1, $show_hentai);

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
		print_fansub($row, $show_hentai);
	}
}
?>
							</div>
						</div>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-landmark"></i> Fansubs històrics</h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 0, $show_hentai);

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
		print_fansub($row, $show_hentai);
	}
}
?>
							</div>
						</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
