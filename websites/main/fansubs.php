<?php
define('PAGE_TITLE', 'Llista de fansubs en català');
define('PAGE_PATH', '/llista-de-fansubs');
define('PAGE_STYLE_TYPE', 'fansubs');
require_once("../common.fansubs.cat/header.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");
?>
					<div class="fansubs-index">
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-user-group"></i> Fansubs actius</h2>
<?php
$result = query_fansubs($user, 1);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No hem trobat cap grup actiu!</div></div>
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
$result = query_fansubs($user, 0);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No hem trobat cap grup històric!</div></div>
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
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>