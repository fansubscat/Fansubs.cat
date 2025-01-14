<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL.lang('url.login'));
	die();
}

define('PAGE_TITLE', lang('users.my_list.page_title'));
define('PAGE_PATH', lang('url.my_list'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'list');

require_once(__DIR__.'/../common/header.inc.php');

$res = query_my_list_total_items($user);
$cnt = mysqli_fetch_assoc($res)['cnt'];

if ($cnt>0) {
	$sections = array();
	if (SITE_IS_HENTAI) {
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> '.lang('users.my_list.anime.header.hentai'),
			'result' => query_my_list_by_type($user, 'anime', TRUE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> '.lang('users.my_list.manga.header.hentai'),
			'result' => query_my_list_by_type($user, 'manga', TRUE)
		));
	} else {
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> '.lang('users.my_list.anime.header'),
			'result' => query_my_list_by_type($user, 'anime', FALSE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> '.lang('users.my_list.manga.header'),
			'result' => query_my_list_by_type($user, 'manga', FALSE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> '.lang('users.my_list.liveaction.header'),
			'result' => query_my_list_by_type($user, 'liveaction', FALSE)
		));
	}

	foreach($sections as $section) {
		$result = $section['result'];
		if (mysqli_num_rows($result)>0){
	?>
					<div class="section">
						<h2 class="section-title-main"><?php echo $section['title']; ?></h2>
						<div class="section-content catalogue">
	<?php
			while ($row = mysqli_fetch_assoc($result)){
	?>
							<div<?php echo isset($row['best_status']) ? ' class="status-'.get_status($row['best_status']).'"' : ''; ?>>
	<?php
				print_carousel_item($row, FALSE, FALSE);
	?>
							</div>
	<?php
			}
	?>
						</div>
					</div>
	<?php
		}
	}
}
?>
<div class="section empty-list<?php echo $cnt>0 ? ' hidden' : ''; ?>">
	<h2 class="section-title-main"><i class="fa fa-fw fa-bookmark"></i> <?php echo lang('users.my_list.empty.header'); ?></h2>
	<div class="section-content section-empty"><div><i class="fa far fa-fw fa-bookmark"></i><br><?php echo SITE_IS_HENTAI ? lang('users.my_list.empty.explanation.hentai') : lang('users.my_list.empty.explanation'); ?></div></div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

