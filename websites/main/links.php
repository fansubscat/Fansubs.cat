<?php
define('PAGE_TITLE', 'Enllaços');
define('PAGE_PATH', '/enllacos');
define('PAGE_STYLE_TYPE', 'text');
define('PAGE_DESCRIPTION', 'Aquí trobaràs un recull d’enllaços als principals mitjans i divulgadors que fan contingut en català relacionat amb el manga i l’anime. Fansubs.cat no hi té cap mena de relació, però creiem convenient fer-ne promoció i et convidem a seguir-los i donar-los suport!');
define('PAGE_DISABLED_IF_HENTAI', TRUE);
require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-link"></i> Enllaços</h2>
						<div class="section-content">Aquí trobaràs un recull d’enllaços als principals mitjans i divulgadors que fan contingut en català relacionat amb el manga i l’anime. Fansubs.cat no hi té cap mena de relació, però creiem convenient fer-ne promoció i et convidem a seguir-los i donar-los suport!</div>
<?php
	$categories = array(
		'featured' => '<i class="fa fa-fw fa-star"></i> Destacats',
		'blogs' => '<i class="fa fa-fw fa-newspaper"></i> Blogs i notícies',
		'catalogs' => '<i class="fa fa-fw fa-signs-post"></i> Catàlegs',
		'art' => '<i class="fa fa-fw fa-palette"></i> Còmic i arts visuals',
		'forums' => '<i class="fa fa-fw fa-comments"></i> Comunitats i fòrums',
		'culture' => '<i class="fa fa-fw fa-torii-gate"></i> Cultura asiàtica',
		'creators' => '<i class="fa fa-fw fa-user"></i> Divulgadors',
		'dubbing' => '<i class="fa fa-fw fa-microphone"></i> Doblatge',
		'music' => '<i class="fa fa-fw fa-music"></i> Música i versions',
		'nostalgia' => '<i class="fa fa-fw fa-heart"></i> Nostàlgia',
		'podcasts' => '<i class="fa fa-fw fa-radio"></i> Pòdcasts',
		'preservation' => '<i class="fa fa-fw fa-landmark"></i> Preservació',
		'subtitles' => '<i class="fa fa-fw fa-message"></i> Subtítols',
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
require_once(__DIR__.'/../common/footer.inc.php');
?>
