<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");
require_once("queries.inc.php");

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'La meva llista');
define('PAGE_PATH', '/la-meva-llista');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'list');

require_once("../common.fansubs.cat/header.inc.php");

$res = query_my_list_total_items($user);
$cnt = mysqli_fetch_assoc($res)['cnt'];

if ($cnt>0) {
	$sections = array();
	if (SITE_IS_HENTAI) {
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista d’animes hentai',
			'result' => query_my_list_by_type($user, 'anime', TRUE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista de mangues hentai',
			'result' => query_my_list_by_type($user, 'manga', TRUE)
		));
	} else {
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista d’animes',
			'result' => query_my_list_by_type($user, 'anime', FALSE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista de mangues',
			'result' => query_my_list_by_type($user, 'manga', FALSE)
		));
		array_push($sections, array(
			'title' => '<i class="fa fa-fw fa-bookmark"></i>La meva llista de continguts d’imatge real',
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
	<h2 class="section-title-main"><i class="fa fa-fw fa-bookmark"></i> La meva llista</h2>
	<div class="section-content section-empty"><div><i class="fa far fa-fw fa-bookmark"></i><br><?php echo SITE_IS_HENTAI ? 'No tens cap anime ni manga hentai desat a la llista.' : 'No tens cap anime, manga ni contingut d’imatge real desat a la llista.'; ?><br>Pots afegir-n’hi fent clic a la icona de punt de llibre a cadascun dels portals.</div></div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

