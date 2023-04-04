<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'La meva llista');
define('PAGE_PATH', '/la-meva-llista');
define('PAGE_STYLE_TYPE', 'users');

require_once("../common.fansubs.cat/header.inc.php");

$max_items=24;

$sections = array();

array_push($sections, array(
	'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista d’anime',
	'result' => query_my_list_by_type($user, 'anime', FALSE),
	'show_empty' => TRUE,
	'empty_message' => '<i class="fa fa-fw fa-exclamation"></i><br>No tens cap anime desat. Pots afegir-ne fent clic a la icona de punt de llibre al portal d’anime.'
));
array_push($sections, array(
	'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista de manga',
	'result' => query_my_list_by_type($user, 'manga', FALSE),
	'show_empty' => TRUE,
	'empty_message' => '<i class="fa fa-fw fa-exclamation"></i><br>No tens cap manga desat. Pots afegir-ne fent clic a la icona de punt de llibre al portal de manga.'
));
array_push($sections, array(
	'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista d’imatge real',
	'result' => query_my_list_by_type($user, 'liveaction', FALSE),
	'show_empty' => TRUE,
	'empty_message' => '<i class="fa fa-fw fa-exclamation"></i><br>No tens cap contingut d’imatge real desat. Pots afegir-ne fent clic a la icona de punt de llibre al portal d’imatge real.'
));
if (is_adult()) {
	array_push($sections, array(
		'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista d’anime hentai',
		'result' => query_my_list_by_type($user, 'anime', TRUE),
		'show_empty' => FALSE
	));
	array_push($sections, array(
		'title' => '<i class="fa fa-fw fa-bookmark"></i> La meva llista de manga hentai',
		'result' => query_my_list_by_type($user, 'manga', TRUE),
		'show_empty' => FALSE,
	));
}

foreach($sections as $section) {
	$result = $section['result'];
	if ((mysqli_num_rows($result)==0 && $section['show_empty']) || mysqli_num_rows($result)>0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $section['title']; ?></h2>
					<div class="section-content section-empty<?php echo mysqli_num_rows($result)>0 ? ' hidden' : ''; ?>"><div><?php echo $section['empty_message']; ?></div></div>
					<div class="section-content catalogue<?php echo mysqli_num_rows($result)==0 ? ' hidden' : ''; ?>">
<?php
		while ($row = mysqli_fetch_assoc($result)){
?>
						<div<?php echo isset($row['best_status']) ? ' class="status-'.get_status($row['best_status']).'"' : ''; ?>>
<?php
			print_carousel_item($row, FALSE);
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

require_once("../common.fansubs.cat/footer.inc.php");
?>
