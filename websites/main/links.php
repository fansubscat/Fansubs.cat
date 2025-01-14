<?php
require_once(__DIR__.'/../common/initialization.inc.php');

define('PAGE_TITLE', lang('main.links.page_title'));
define('PAGE_PATH', lang('url.links'));
define('PAGE_STYLE_TYPE', 'text');
define('PAGE_DESCRIPTION', sprintf(lang('main.links.page_description'), CURRENT_SITE_NAME));
define('PAGE_DISABLED_IF_HENTAI', TRUE);
require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-link"></i> <?php echo lang('main.links.header'); ?></h2>
						<div class="section-content"><?php echo sprintf(lang('main.links.explanation'), CURRENT_SITE_NAME); ?></div>
<?php
	$categories = array(
		'featured' => '<i class="fa fa-fw fa-star"></i> '.lang('main.links.category.featured'),
		'blogs' => '<i class="fa fa-fw fa-newspaper"></i> '.lang('main.links.category.blogs'),
		'catalogs' => '<i class="fa fa-fw fa-signs-post"></i> '.lang('main.links.category.catalogues'),
		'art' => '<i class="fa fa-fw fa-palette"></i> '.lang('main.links.category.comics'),
		'forums' => '<i class="fa fa-fw fa-comments"></i> '.lang('main.links.category.communities'),
		'culture' => '<i class="fa fa-fw fa-torii-gate"></i> '.lang('main.links.category.asian_culture'),
		'creators' => '<i class="fa fa-fw fa-user"></i> '.lang('main.links.category.influencers'),
		'dubbing' => '<i class="fa fa-fw fa-microphone"></i> '.lang('main.links.category.dubbing'),
		'music' => '<i class="fa fa-fw fa-music"></i> '.lang('main.links.category.music'),
		'nostalgia' => '<i class="fa fa-fw fa-heart"></i> '.lang('main.links.category.nostalgia'),
		'podcasts' => '<i class="fa fa-fw fa-radio"></i> '.lang('main.links.category.podcasts'),
		'preservation' => '<i class="fa fa-fw fa-landmark"></i> '.lang('main.links.category.preservation'),
		'subtitles' => '<i class="fa fa-fw fa-message"></i> '.lang('main.links.category.subtitles'),
		'others' => '<i class="fa fa-fw fa-maximize"></i> '.lang('main.links.category.other'),
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
