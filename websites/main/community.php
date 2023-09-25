<?php
define('PAGE_TITLE', 'Comunitat');
define('PAGE_PATH', '/comunitat');
define('PAGE_STYLE_TYPE', 'text');
require_once("../common.fansubs.cat/header.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-people-group"></i> Comunitat</h2>
						<div class="section-content">A banda dels diferents fansubs en català, la comunitat otaku en la nostra llengua és molt extensa. Hem cregut convenient fer un recull dels principals mitjans i creadors que fan contingut en català. Us convidem a seguir-los i donar-los suport!</div>
						<h2 class="section-title"><i class="fa fa-fw fa-star"></i> Destacats</h2>
						<div class="section-content community-container community-featured">
<?php
	$result = query_communities_by_category('featured');
	while ($row = mysqli_fetch_assoc($result)){
		print_community($row);
	}
?>
						</div>
						<h2 class="section-title"><i class="fa fa-fw fa-user"></i> Creadors</h2>
						<div class="section-content community-container">
<?php
	$result = query_communities_by_category('creators');
	while ($row = mysqli_fetch_assoc($result)){
		print_community($row);
	}
?>
						</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
