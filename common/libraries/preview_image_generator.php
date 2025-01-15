<?php
require_once(__DIR__.'/../config/config.inc.php');
require_once(__DIR__.'/linebreaks4imagettftext.php');

define('IMAGE_WIDTH', 1200);
define('IMAGE_HEIGHT', 628);
define('COVER_WIDTH', 444);
define('COVER_HEIGHT', 628);
define('TEXT_MARGIN_HORIZONTAL', 46);
define('TEXT_MARGIN_VERTICAL', 80);
define('FANSUB_LOGO_WIDTH', 36);
define('FANSUB_LOGO_HEIGHT', 36);
define('FONT_REGULAR', STATIC_DIRECTORY.'/fonts/lexend_deca_regular.ttf');
define('FONT_BOLD', STATIC_DIRECTORY.'/fonts/lexend_deca_bold.ttf');
define('FONT_LIGHT', STATIC_DIRECTORY.'/fonts/lexend_deca_light.ttf');

//Obtained from running: `fc-query --format='%{charset}\n' font.ttf`
define('SUPPORTED_CHARS_REGEX', '/[^\n\x{20}-\x{7e}\x{a0}-\x{17e}\x{18f}\x{192}\x{19d}\x{1a0}-\x{1a1}\x{1af}-\x{1b0}\x{1c4}-\x{1d4}\x{1e6}-\x{1e7}\x{1ea}-\x{1eb}\x{1f1}-\x{1f2}\x{1fa}-\x{21b}\x{22a}-\x{22d}\x{230}-\x{233}\x{237}\x{259}\x{272}\x{2bb}-\x{2bc}\x{2be}-\x{2bf}\x{2c6}-\x{2c8}\x{2cc}\x{2d8}-\x{2dd}\x{300}-\x{304}\x{306}-\x{30c}\x{30f}\x{311}-\x{312}\x{31b}\x{323}-\x{324}\x{326}-\x{328}\x{32e}\x{331}\x{335}\x{394}\x{3a9}\x{3bc}\x{3c0}\x{1e08}-\x{1e09}\x{1e0c}-\x{1e0f}\x{1e14}-\x{1e17}\x{1e1c}-\x{1e1d}\x{1e20}-\x{1e21}\x{1e24}-\x{1e25}\x{1e2a}-\x{1e2b}\x{1e2e}-\x{1e2f}\x{1e36}-\x{1e37}\x{1e3a}-\x{1e3b}\x{1e42}-\x{1e49}\x{1e4c}-\x{1e53}\x{1e5a}-\x{1e5b}\x{1e5e}-\x{1e69}\x{1e6c}-\x{1e6f}\x{1e78}-\x{1e7b}\x{1e80}-\x{1e85}\x{1e8e}-\x{1e8f}\x{1e92}-\x{1e93}\x{1e97}\x{1e9e}\x{1ea0}-\x{1ef9}\x{2007}-\x{200b}\x{2010}\x{2012}-\x{2015}\x{2018}-\x{201a}\x{201c}-\x{201e}\x{2020}-\x{2022}\x{2026}\x{2030}\x{2033}\x{2039}-\x{203a}\x{2044}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{20a1}\x{20a3}-\x{20a4}\x{20a6}-\x{20a7}\x{20a9}\x{20ab}-\x{20ad}\x{20b1}-\x{20b2}\x{20b5}\x{20b9}-\x{20ba}\x{20bc}-\x{20bd}\x{2113}\x{2116}\x{2122}\x{2126}\x{212e}\x{215b}-\x{215e}\x{2202}\x{2205}-\x{2206}\x{220f}\x{2211}-\x{2212}\x{2215}\x{2219}-\x{221a}\x{221e}\x{222b}\x{2248}\x{2260}\x{2264}-\x{2265}\x{25ca}\x{fb01}-\x{fb02}]/u');

function query_version_data_for_preview_image_by_id($id) {
	$id = intval($id);
	$final_query = "SELECT v.title,
				v.status,
				GROUP_CONCAT(DISTINCT f.name
					ORDER BY f.name
					SEPARATOR ' + '
				) fansub_name,
				GROUP_CONCAT(DISTINCT f.id
					ORDER BY f.name
					SEPARATOR ' + '
				) fansub_id,
				s.*,
				YEAR(s.publish_date) year,
				GROUP_CONCAT(DISTINCT g.name
					ORDER BY g.name
					SEPARATOR ', '
					) genres,
				(SELECT COUNT(DISTINCT d.id)
					FROM division d
					WHERE d.series_id=s.id
					AND d.number_of_episodes>0
					AND d.is_real=1
				) divisions
			FROM version v
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE v.id=$id
				AND v.is_hidden=0
			GROUP BY v.id";
	return query($final_query);
}

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

function round_corners($source, $radius) {
	$ws = imagesx($source);
	$hs = imagesy($source);

	$corner = $radius + 2;
	$s = $corner*2;

	$src = imagecreatetruecolor($s, $s);
	imagecopy($src, $source, 0, 0, 0, 0, $corner, $corner);
	imagecopy($src, $source, $corner, 0, $ws - $corner, 0, $corner, $corner);
	imagecopy($src, $source, $corner, $corner, $ws - $corner, $hs - $corner, $corner, $corner);
	imagecopy($src, $source, 0, $corner, 0, $hs - $corner, $corner, $corner);

	$q = 8; # change this if you want
	$radius *= $q;

	# find unique color
	do {
		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
	} while (imagecolorexact($src, $r, $g, $b) < 0);

	$ns = $s * $q;

	$img = imagecreatetruecolor($ns, $ns);
	$alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
	imagealphablending($img, false);
	imagefilledrectangle($img, 0, 0, $ns, $ns, $alphacolor);

	imagefill($img, 0, 0, $alphacolor);
	imagecopyresampled($img, $src, 0, 0, 0, 0, $ns, $ns, $s, $s);
	imagedestroy($src);

	imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
	imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
	imagearc($img, $ns - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
	imagefilltoborder($img, $ns - 1, 0, $alphacolor, $alphacolor);
	imagearc($img, $radius - 1, $ns - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
	imagefilltoborder($img, 0, $ns - 1, $alphacolor, $alphacolor);
	imagearc($img, $ns - $radius, $ns - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
	imagefilltoborder($img, $ns - 1, $ns - 1, $alphacolor, $alphacolor);
	imagealphablending($img, true);
	imagecolortransparent($img, $alphacolor);

	# resize image down
	$dest = imagecreatetruecolor($s, $s);
	imagealphablending($dest, false);
	imagefilledrectangle($dest, 0, 0, $s, $s, $alphacolor);
	imagecopyresampled($dest, $img, 0, 0, 0, 0, $s, $s, $ns, $ns);
	imagedestroy($img);

	# output image
	imagealphablending($source, false);
	imagecopy($source, $dest, 0, 0, 0, 0, $corner, $corner);
	imagecopy($source, $dest, $ws - $corner, 0, $corner, 0, $corner, $corner);
	imagecopy($source, $dest, $ws - $corner, $hs - $corner, $corner, $corner, $corner, $corner);
	imagecopy($source, $dest, 0, $hs - $corner, 0, $corner, $corner, $corner);
	imagealphablending($source, true);
	imagedestroy($dest);

	return $source;
}

function get_status_color($image, $id){
	switch ($id){
		case 1:
			return imagecolorallocate($image, 0x19, 0x98, 0x00);
		case 2:
			return imagecolorallocate($image, 0xD1, 0xAD, 0x00);
		case 3:
			return imagecolorallocate($image, 0x3C, 0x8E, 0xB1);
		case 4:
			return imagecolorallocate($image, 0xB3, 0x6E, 0x07);
		case 5:
			return imagecolorallocate($image, 0xBB, 0x13, 0x04);
		default:
			return imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	}
}

function get_status_icon_code($id){
	switch ($id){
		case 1:
			return "";
		case 2:
			return "";
		case 3:
			return "";
		case 4:
			return "";
		case 5:
			return "";
		default:
			return "";
	}
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return lang('social.manga');
		case 'manhwa':
			return lang('social.manhwa');
		case 'manhua':
			return lang('social.manhua');
		default:
			return lang('social.comic');
	}
}

//Taken from (and modified): https://gist.github.com/mistic100/9301c0eebaef047bfdc8?permalink_comment_id=3043071#gistcomment-3043071
function imageroundedrectangle(&$img, $x1, $y1, $x2, $y2, $r, $color) {
	$r = min($r, floor(min(($x2 - $x1) / 2, ($y2 - $y1) / 2)));
	// render corners
	imagefilledarc($img, $x1 + $r-1, $y1 + $r-1, $r * 2, $r * 2, 180, 270, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x2 - $r+1, $y1 + $r-1, $r * 2, $r * 2, 270, 0, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x2 - $r+1, $y2 - $r, $r * 2, $r * 2, 0, 90, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x1 + $r-1, $y2 - $r, $r * 2, $r * 2, 90, 180, $color, IMG_ARC_PIE);
	imagefilledrectangle($img, $x1+$r, $y1-1, $x2-$r, $y2, $color);
}

//Taken from (and modified): https://www.php.net/manual/en/function.imagefilledrectangle.php#65557
function gradient_region(&$img, $x, $y, $width, $height,$src_color, $dest_color, $src_alpha, $dest_alpha){
	$src_red = ($src_color & 0xFF0000) >> 16;
	$src_green = ($src_color & 0x00FF00) >> 8;
	$src_blue = ($src_color & 0x0000FF);

	$dest_red = ($dest_color & 0xFF0000) >> 16;
	$dest_green = ($dest_color & 0x00FF00) >> 8;
	$dest_blue = ($dest_color & 0x0000FF);

	$inc_alpha = ($dest_alpha - $src_alpha) / $width;
	$inc_red = ($dest_red - $src_red)/$width;
	$inc_green = ($dest_green - $src_green)/$width;
	$inc_blue = ($dest_blue - $src_blue)/$width;

	// If you need more performance, the step can be increased
	for ($i=0;$i<$width;$i++) {
		$src_alpha += $inc_alpha;
		$src_blue += $inc_blue;
		$src_green += $inc_green;
		$src_red += $inc_red;
		imagefilledrectangle($img, $x+$i,$y, $x+$i,$y+$height, imagecolorallocatealpha($img, $src_red,$src_green,$src_blue,$src_alpha));
	}
}

function get_text_without_missing_glyphs($text) {
	return preg_replace(SUPPORTED_CHARS_REGEX, ' ', $text);
}

function get_series_type_summary($series) {
	$text='';
	if ($series['type']=='manga') {
		if ($series['subtype']=='oneshot') {
			if ($series['comic_type']=='novel') {
				$text = lang('social.novel');
			} else {
				$text = get_comic_type($series['comic_type'])." • ".lang('social.oneshot');
			}
		} else if ($series['divisions']>1) {
			$text = get_comic_type($series['comic_type'])." • ".lang('social.serialized')." • ".sprintf(lang('social.volumes'), $series['number_of_episodes'])." • ".sprintf(lang('social.episodes'), $series['number_of_episodes']);
		} else {
			$text = get_comic_type($series['comic_type'])." • ".lang('social.serialized')." • ".lang('social.volume')." • ".sprintf(lang('social.episodes'), $series['number_of_episodes']);
		}
	} else {
		if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
			$text = ($series['type']=='anime' ? lang('social.anime') : lang('social.liveaction'))." • ".sprintf(lang('social.movies'), $series['number_of_episodes']);
		} else if ($series['subtype']=='movie') {
			$text = ($series['type']=='anime' ? lang('social.anime') : lang('social.liveaction'))." • ".lang('social.movie');
		} else if ($series['divisions']>1) {
			$text = ($series['type']=='anime' ? lang('social.anime') : lang('social.liveaction'))." • ".lang('social.series')." • ".sprintf(lang('social.seasons'), $series['divisions'])." • ".sprintf(lang('social.episodes'), $series['number_of_episodes']);
		} else {
			$text = ($series['type']=='anime' ? lang('social.anime') : lang('social.liveaction'))." • ".lang('social.series')." • ".$series['number_of_episodes']." capítols";
		}
	}
	return $text;
}

function update_version_preview($id) {
	$result = query_version_data_for_preview_image_by_id($id);
	$version = mysqli_fetch_assoc($result) or $failed=TRUE;
	mysqli_free_result($result);

	if (empty($failed)) {
		//Empty canvas - we will draw here
		$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

		//Load cover and scale it as needed
		$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/$id.jpg");
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);

		//Load bg and scale it as needed
		$background = imagecreatefromjpeg(STATIC_DIRECTORY."/images/featured/$id.jpg");
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

		$gradient_from_color = imagecolorallocatealpha($image,0,0,0,100);
		$gradient_to_color = imagecolorallocatealpha($image,0,0,0,0);
		gradient_region($image, IMAGE_WIDTH-COVER_WIDTH-80, 0, 80, IMAGE_HEIGHT, $gradient_from_color, $gradient_to_color, 128, 0);

		$current_height = TEXT_MARGIN_VERTICAL+18;

		//Type
		$text = get_series_type_summary($version);

		$gray = imagecolorallocate($image, 0xDC, 0xDC, 0xDC);
		imagefttext($image, 23.5, 0, TEXT_MARGIN_HORIZONTAL, $current_height, $gray, FONT_REGULAR, get_text_without_missing_glyphs($text));
		$current_height = $current_height+72;

		//Name
		$text = \andrewgjohnson\linebreaks4imagettftext(42, 0, FONT_BOLD, get_text_without_missing_glyphs($version['title']), IMAGE_WIDTH-COVER_WIDTH-(TEXT_MARGIN_HORIZONTAL+4)*2, 3);
		$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		for ($i=0;$i<=substr_count($text, "\n");$i++) {
			imagefttext($image, 42, 0, TEXT_MARGIN_HORIZONTAL-4, $current_height, $white, FONT_BOLD, explode("\n", get_text_without_missing_glyphs($text))[$i]);
			$current_height = $current_height+54;
		}
			$current_height = $current_height-2;

		//Alternate names
		$alternate_names = '';
		if (!empty($version['name']) && $version['title']!=$version['name']) {
			$alternate_names = $version['name'];
		}
		if (!empty($version['alternate_names'])) {
			if (!empty($alternate_names)) {
				$alternate_names .= ', ';
			}
			$alternate_names .= $version['alternate_names'];
		}
		if (!empty($alternate_names)) {
			$text = \andrewgjohnson\linebreaks4imagettftext(23.5, 0, FONT_LIGHT, get_text_without_missing_glyphs($alternate_names), IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL*2, 2);
			$yellow = imagecolorallocate($image, 0xA3, 0xA3, 0xA3);
			for ($i=0;$i<=substr_count($text, "\n");$i++) {
				imagefttext($image, 23.5, 0, TEXT_MARGIN_HORIZONTAL, $current_height, $yellow, FONT_LIGHT, explode("\n", get_text_without_missing_glyphs($text))[$i]);
				$current_height = $current_height+38;
			}
		} else {
			$current_height = $current_height-12;
		}

		//Score
		$score=$version['score'];

		if (!empty($score)) {
			$orange = imagecolorallocate($image, 0xFF, 0xC1, 0x00);
			imagefttext($image, 48, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL-130, IMAGE_HEIGHT-TEXT_MARGIN_VERTICAL, $orange, FONT_BOLD, number_format($score, 2, ',',' '));
			//imagefttext($image, 17, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL-10, IMAGE_HEIGHT-TEXT_MARGIN_HORIZONTAL-14, $white, FONT_REGULAR, "/10");
		}

		//Genres
		$current_width = TEXT_MARGIN_HORIZONTAL+10;
		$genres = !empty($version['genres']) ? explode(', ',$version['genres']) : array();

		$tag_bg_color = imagecolorallocatealpha($image, 0xFF, 0xFF, 0xFF, 95);
		$lines = 1;
		$usable_genres = array();
		//First iteration to know the number of lines
		foreach ($genres as $genre) {
			$bbox = imagettfbbox(14, 0, FONT_LIGHT, $genre);
			$fits = ($current_width+$bbox[2])<(IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL*2-8*2-100);
			if (!$fits) {
				if ($lines==3) {
					//Max 3 lines of genres
					break;
				}
				$current_width = TEXT_MARGIN_HORIZONTAL+10;
				$lines++;
			}
			array_push($usable_genres, $genre);
			$current_width+=$bbox[2]+6*2+12;
		}

		$current_height = IMAGE_HEIGHT-TEXT_MARGIN_VERTICAL-9-36*($lines-1);
		$current_width = TEXT_MARGIN_HORIZONTAL+10;
		foreach ($usable_genres as $genre) {
			$bbox = imagettfbbox(14, 0, FONT_LIGHT, $genre);
			$fits = ($current_width+$bbox[2])<(IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL*2-8*2-100);
			if (!$fits) {
				$current_width = TEXT_MARGIN_HORIZONTAL+10;
				$current_height = $current_height+36;
			}
			imageroundedrectangle($image, $current_width-8, $current_height-20, $current_width+$bbox[2]+8, $current_height+8, 14, $tag_bg_color);
			imagefttext($image, 14, 0, $current_width+2, $current_height, $white, FONT_LIGHT, get_text_without_missing_glyphs($genre));
			$current_width+=$bbox[2]+6*2+12;
		}

		//Fansubs
		$current_height = $current_height-36*($lines-1)-50;
		
		$fansub_names = explode(' + ', $version['fansub_name']);
		$fansub_ids = explode(' + ', $version['fansub_id']);
		
		$current_width = TEXT_MARGIN_HORIZONTAL;
		
		for ($i=0;$i<count($fansub_names);$i++) {
			//Load fansub logo
			$fansub_logo = imagecreatefrompng(STATIC_DIRECTORY."/images/icons/".$fansub_ids[$i].".png");
			$fansub_logo = scale_smallest_side($fansub_logo, FANSUB_LOGO_WIDTH, FANSUB_LOGO_HEIGHT);
			$fansub_logo = round_corners($fansub_logo, 18);
			imagecopy($image, $fansub_logo, $current_width, $current_height-28, 0, 0, FANSUB_LOGO_WIDTH, FANSUB_LOGO_HEIGHT);
			$current_width+=FANSUB_LOGO_WIDTH-5;
		}
			
		$text = \andrewgjohnson\linebreaks4imagettftext(20, 0, FONT_REGULAR, get_text_without_missing_glyphs($version['fansub_name']), IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN_HORIZONTAL-$current_width-10, 1);
		imagefttext($image, 20, 0, $current_width+10, $current_height, $white, FONT_REGULAR, get_text_without_missing_glyphs($text));

		imagejpeg($image, STATIC_DIRECTORY."/social/version_".$id.".jpg", 80);
		imagedestroy($image);
	}
}
?>
