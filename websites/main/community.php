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
						<div class="section-content">A banda dels diferents fansubs en català, la comunitat otaku en la nostra llengua és molt extensa. Hem cregut convenient fer un recull dels principals mitjans i creadors que fan contingut en català. No tenen cap mena de relació amb Fansubs.cat, però volem fer-ne promoció i et convidem a seguir-los i donar-los suport!</div>
<?php
	$categories = array(
		'featured' => '<i class="fa fa-fw fa-star"></i> Destacats',
		'blogs' => '<i class="fa fa-fw fa-newspaper"></i> Blogs i notícies',
		'catalogs' => '<i class="fa fa-fw fa-signs-post"></i> Catàlegs',
		'art' => '<i class="fa fa-fw fa-palette"></i> Còmic i arts visuals',
		'forums' => '<i class="fa fa-fw fa-comments"></i> Comunitats i fòrums',
		'creators' => '<i class="fa fa-fw fa-user"></i> Creadors',
		'culture' => '<i class="fa fa-fw fa-torii-gate"></i> Cultura asiàtica',
		'dubbing' => '<i class="fa fa-fw fa-microphone"></i> Doblatge',
		'music' => '<i class="fa fa-fw fa-music"></i> Música i versions',
		'nostalgia' => '<i class="fa fa-fw fa-heart"></i> Nostàlgia',
		'podcasts' => '<i class="fa fa-fw fa-radio"></i> Pòdcasts',
		'preservation' => '<i class="fa fa-fw fa-landmark"></i> Preservació',
		'others' => '<i class="fa fa-fw fa-maximize"></i> Altres',
	);
	foreach ($categories as $id => $title) {
		$result = query_communities_by_category($id);
		if (mysqli_num_rows($result)>0) {
?>
						<h2 class="section-title"><?php echo $title; ?></h2>
						<div class="section-content community-container<?php echo $id=='featured' ? ' community-featured' : ''; ?>">
<?php
			while ($row = mysqli_fetch_assoc($result)){
				print_community($row);
			}
?>
						</div>
<?php
		}
	}
?>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
