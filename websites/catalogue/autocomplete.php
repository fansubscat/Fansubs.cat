<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

function get_series_type_summary_for_autocomplete($series) {
	$text='';
	if ($series['type']=='manga') {
		if ($series['comic_type']=='novel') {
			$text = lang('catalogue.autocomplete.light_novel');
		} else if ($series['subtype']=='oneshot') {
			$text = get_type_depending_on_catalogue($series).lang('catalogue.autocomplete.oneshot');
		} else if ($series['divisions']>1) {
			$text = get_type_depending_on_catalogue($series).sprintf(lang('catalogue.autocomplete.several_volumes'), $series['divisions']);
		} else {
			$text = get_type_depending_on_catalogue($series).lang('catalogue.autocomplete.one_volume');
		}
	} else {
		if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
			$text = get_type_depending_on_catalogue($series).sprintf(lang('catalogue.autocomplete.movie_pack'), $series['number_of_episodes']);
		} else if ($series['subtype']=='movie') {
			$text = get_type_depending_on_catalogue($series).lang('catalogue.autocomplete.movie');
		} else if ($series['divisions']>1) {
			$text = get_type_depending_on_catalogue($series).lang('catalogue.autocomplete.series')." • ".sprintf(lang('catalogue.autocomplete.several_seasons'), $series['divisions']);
		} else {
			$text = get_type_depending_on_catalogue($series).lang('catalogue.autocomplete.series');
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
			'result' => query_autocomplete($user, $text, 'liveaction')
		));
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'manga')
		));
		break;
	case 'manga':
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'manga')
		));
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'liveaction')
		));
		break;
	case 'anime':
	default:
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'anime')
		));
		array_push($sections, array(
			'result' => query_autocomplete($user, $text, 'manga')
		));
		array_push($sections, array(
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
						<a class="autocomplete-item" href="<?php echo get_base_url_from_type_and_rating($row['type'],$row['rating']).'/'.$row['default_version_slug']; ?>">
							<?php echo '<img class="autocomplete-image" src="'.STATIC_URL.'/images/covers/version_'.$row['default_version_id'].'.jpg" alt="'.htmlspecialchars($row['default_version_title']).'">'; ?>
							<div class="autocomplete-data">
								<div class="autocomplete-name"><?php echo htmlspecialchars($row['default_version_title']); ?></div>
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
						<a class="autocomplete-item autocomplete-more" href="<?php echo SITE_BASE_URL.lang('url.search').'/'.urlencode($_GET['query']); ?>"><?php echo sprintf(lang('catalogue.autocomplete.show_all_results'), $total_elements); ?> <i class="fa fa-fw fa-arrow-right"></i></a>
<?php
} else if ($total_elements==0) {
?>
						<span class="autocomplete-item autocomplete-empty"><?php echo lang('catalogue.autocomplete.no_results'); ?></span>
<?php
}
?>
