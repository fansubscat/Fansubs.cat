<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function get_series_type_summary_for_autocomplete($series) {
	$text='';
	if ($series['type']=='manga') {
		if ($series['subtype']=='oneshot') {
			$text = get_type_depending_on_catalogue($series)."One-shot";
		} else if ($series['divisions']>1) {
			$text = get_type_depending_on_catalogue($series).$series['divisions']." volums";
		} else {
			$text = get_type_depending_on_catalogue($series)."1 volum";
		}
	} else {
		if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
			$text = get_type_depending_on_catalogue($series).$series['number_of_episodes']." films";
		} else if ($series['subtype']=='movie') {
			$text = get_type_depending_on_catalogue($series)."Film";
		} else if ($series['divisions']>1) {
			$text = get_type_depending_on_catalogue($series)."Sèrie • ".$series['divisions']." temporades";
		} else {
			$text = get_type_depending_on_catalogue($series)."Sèrie";
		}
	}

	return $text;
}

validate_hentai_ajax();

$text = (isset($_GET['query']) ? $_GET['query'] : "");

$sections=array();
	
switch(CATALOGUE_ITEM_TYPE) {
	case 'liveaction':
		array_push($sections, array(
			'name' => 'Acció real',
			'result' => query_autocomplete($user, $text, 'liveaction')
		));
		array_push($sections, array(
			'name' => 'Anime',
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'name' => 'Manga',
			'result' => query_autocomplete($user, $text, 'manga')
		));
		break;
	case 'manga':
		array_push($sections, array(
			'name' => 'Manga',
			'result' => query_autocomplete($user, $text, 'manga')
		));
		array_push($sections, array(
			'name' => 'Anime',
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'name' => 'Acció real',
			'result' => query_autocomplete($user, $text, 'liveaction')
		));
		break;
	case 'anime':
	default:
		array_push($sections, array(
			'name' => 'Anime',
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'name' => 'Manga',
			'result' => query_autocomplete($user, $text, 'manga')
		));
		array_push($sections, array(
			'name' => 'Acció real',
			'result' => query_autocomplete($user, $text, 'liveaction')
		));
		break;
}

$total_elements=0;
$max_elements=6;
foreach($sections as $section){
	$total_elements+=mysqli_num_rows($section['result']);
}

$i=0;

foreach($sections as $section){
	$result = $section['result'];
	while ($i<$max_elements && $row = mysqli_fetch_assoc($result)){
?>
						<a class="autocomplete-item" href="<?php echo ($row['type']=='liveaction' ? LIVEACTION_URL : ($row['type']=='anime' ? ANIME_URL : MANGA_URL)).'/'.($row['rating']=='XXX' ? 'hentai/' : '').$row['slug']; ?>">
							<?php echo '<img class="autocomplete-image" src="'.STATIC_URL.'/images/covers/'.$row['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'">'; ?>
							<div class="autocomplete-data">
								<div class="autocomplete-name"><?php echo htmlspecialchars($row['name']); ?></div>
								<div class="autocomplete-type"><?php echo htmlspecialchars(get_series_type_summary_for_autocomplete($row, TRUE)); ?></div>
							</div>
						</a>
<?php
		$i++;
	}
	mysqli_free_result($result);
}
if ($max_elements<$total_elements) {
?>
						<a class="autocomplete-item autocomplete-more" href="<?php echo SITE_BASE_URL.'/'.(SITE_IS_HENTAI ? 'hentai/' : '').'cerca/'.urlencode($_GET['query']); ?>">Mostra tots els resultats (<?php echo $total_elements; ?>) <i class="fa fa-fw fa-arrow-right"></i></a>
<?php
} else if ($total_elements==0) {
?>
						<span class="autocomplete-item autocomplete-empty">No hi ha cap resultat.</span>
<?php
}
?>
