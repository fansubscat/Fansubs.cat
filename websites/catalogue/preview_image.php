<?php
const IMAGE_WIDTH = 1200;
const IMAGE_HEIGHT = 628;
const COVER_WIDTH = 444;
const COVER_HEIGHT = 627;
const TEXT_MARGIN = 48;
const FONT = 'style/patinio_neue.ttf';

ob_start();
require_once("db.inc.php");
require_once('libraries/linebreaks4imagettftext.php');

function scale_smallest_side($image, $desired_width, $desired_height) {
	$width = imagesx($image);
	$height = imagesy($image);

	if ($width/$height < $desired_width/$desired_height) {
		$output_width = $desired_width;
		$output_height = $desired_width*$height/$width;
		$image = imagescale($image, $output_width, $output_height, IMG_BILINEAR_FIXED);
		$image = imagecrop($image, ['x' => 0, 'y' => ($output_height-$desired_height)/2, 'width' => $desired_width, 'height' => $desired_height]);
	} else {
		$output_width = $desired_height*$width/$height;
		$output_height = $desired_height;
		$image = imagescale($image, $output_width, $output_height, IMG_BILINEAR_FIXED);
		$image = imagecrop($image, ['x' => ($output_width-$desired_width)/2, 'y' => 0, 'width' => $desired_width, 'height' => $desired_height]);
	}
	
	return $image;
}

function get_status_color($image, $id){
	switch ($id){
		case 1:
			return imagecolorallocate($image, 0x00, 0x80, 0x00);
		case 2:
			return imagecolorallocate($image, 0xFF, 0xFF, 0x00);
		case 3:
			return imagecolorallocate($image, 0xAD, 0xFF, 0x2F);
		case 4:
		case 5:
			return imagecolorallocate($image, 0xFF, 0x00, 0x00);
		default:
			return imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	}
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return 'Manga';
		case 'manhwa':
			return 'Manhwa';
		case 'manhua':
			return 'Manhua';
		default:
			return 'Còmic';
	}
}

$result = query("SELECT s.*, YEAR(s.publish_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='${config['items_type']}' AND slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);

if (empty($failed)) {
	$id = $series['id'];
	$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE v.is_hidden=0 AND v.series_id=$id GROUP BY v.id ORDER BY v.status DESC, v.created DESC");
	$versions = array();
	while ($version = mysqli_fetch_assoc($result)) {
		$versions[] = $version;
	}
	mysqli_free_result($result);

	//Empty canvas - we will draw here
	$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

	//Load cover and scale it as needed
	$cover = imagecreatefromjpeg($static_directory."/images/covers/$id.jpg");
	$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);

	//Load bg and scale it as needed
	$background = imagecreatefromjpeg($static_directory."/images/featured/$id.jpg");
	$background = scale_smallest_side($background, IMAGE_WIDTH, IMAGE_HEIGHT);

	//Darken and blur bg
	$semi_transparent = imagecolorallocatealpha($background,0,0,0,30);
	imagefilledrectangle($background, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $semi_transparent);
	for ($i=0; $i<15; $i++) {
		imagefilter($background, IMG_FILTER_GAUSSIAN_BLUR);
	}

	//Paste into canvas
	imagecopy($image, $background, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);
	imagecopy($image, $cover, IMAGE_WIDTH-COVER_WIDTH, 0, 0, 0, COVER_WIDTH, IMAGE_HEIGHT);

	$current_height = TEXT_MARGIN+24;

	//Type
	if ($series['type']=='manga') {
		if ($series['subtype']=='oneshot') {
			$text = get_comic_type($series['comic_type'])." • One-shot";
		} else if ($series['divisions']>1) {
			$text = get_comic_type($series['comic_type'])." • Serialitzat • ".$series['divisions']." volums • ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes'].' capítols');
		} else {
			$text = get_comic_type($series['comic_type'])." • Serialitzat • 1 volum • ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes'].' capítols');
		}
	} else {
		if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
			$text = $config['preview_prefix']." • Conjunt de ".$series['number_of_episodes']." films";
		} else if ($series['subtype']=='movie') {
			$text = $config['preview_prefix']." • Film";
		} else if ($series['divisions']>1) {
			$text = $config['preview_prefix']." • Sèrie • ".$series['divisions']." temporades • ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols');
		} else {
			$text = $config['preview_prefix']." • Sèrie • ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols');
		}
	}

	$gray = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
	imagefttext($image, 24, 0, TEXT_MARGIN, $current_height, $gray, FONT, $text);
	$current_height = $current_height+72;

	//Name
	$text = \andrewgjohnson\linebreaks4imagettftext(54, 0, FONT, $series['name'], IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2);
	if (substr_count($text, "\n")>2) {
		$text = mb_substr(implode("\n",array_slice(explode("\n", $text), 0, 3)),0,-3).'…';
	}
	$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	for ($i=0;$i<=substr_count($text, "\n");$i++) {
		imagefttext($image, 54, 0, TEXT_MARGIN, $current_height, $white, FONT, explode("\n", $text)[$i]);
		$current_height = $current_height+72;
	}
	$current_height = $current_height-12;

	//Alternate names
	if (!empty($series['alternate_names'])) {
		$text = \andrewgjohnson\linebreaks4imagettftext(30, 0, FONT, $series['alternate_names'], IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2);
		if (substr_count($text, "\n")>1) {
			$text = mb_substr(implode("\n",array_slice(explode("\n", $text), 0, 2)),0,-3).'…';
		}
		$yellow = imagecolorallocate($image, 0xFF, 0xFF, 0xBB);
		for ($i=0;$i<=substr_count($text, "\n");$i++) {
			imagefttext($image, 30, 0, TEXT_MARGIN, $current_height, $yellow, FONT, explode("\n", $text)[$i]);
			$current_height = $current_height+48;
		}
	} else {
		$current_height = $current_height-12;
	}

	//Genres
	$text = \andrewgjohnson\linebreaks4imagettftext(21, 0, FONT, implode(' • ', explode(', ',$series['genres'])), IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2);
	$blue = imagecolorallocate($image, 0xBB, 0xBB, 0xFF);
	for ($i=0;$i<=substr_count($text, "\n");$i++) {
		imagefttext($image, 21, 0, TEXT_MARGIN, $current_height, $blue, FONT, explode("\n", $text)[$i]);
		$current_height = $current_height+36;
	}

	//Note
	$note=$series['score'];

	if (!empty($note)) {
		$orange = imagecolorallocate($image, 0xF1, 0xDD, 0x6A);
		imagefttext($image, 48, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-174, IMAGE_HEIGHT-TEXT_MARGIN-6, $orange, FONT, '★');
		imagefttext($image, 48, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-138, IMAGE_HEIGHT-TEXT_MARGIN, $orange, FONT, number_format($note, 2, ',',' '));
		imagefttext($image, 21, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-24, IMAGE_HEIGHT-TEXT_MARGIN, $orange, FONT, "/10");
	}

	//Fansubs
	$current_height = IMAGE_HEIGHT-12;
	$current_fansub_line = 0;

	foreach ($versions as $version) {
		$text = \andrewgjohnson\linebreaks4imagettftext(30, 0, FONT, $version['fansub_name'], IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-($current_fansub_line<2 ? 174 : 0));
		$current_height = $current_height - 28;
		imagefttext($image, 54, 0, TEXT_MARGIN-6, $current_height+15, get_status_color($image, $version['status']), FONT, "•");
		if (substr_count($text, "\n")>0) {
			$text = mb_substr(implode("\n",array_slice(explode("\n", $text), 0, 1)),0,-3).'…';
		}
		imagefttext($image, 24, 0, TEXT_MARGIN+33, $current_height, $white, FONT, $text);
		$current_fansub_line++;
	}

	header('Content-Type: image/jpeg');
	imagejpeg($image, NULL, 80);
} else {
	echo "Aquest element no existeix.";
}

ob_flush();
mysqli_close($db_connection);
?>
