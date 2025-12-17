<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');
require_once(__DIR__.'/../../common/libraries/linebreaks4imagettftext.php');

define('IMAGE_WIDTH', 1920);
define('IMAGE_HEIGHT', 1080);
define('COVER_WIDTH', 110);
define('COVER_HEIGHT', 156);
define('TEXT_MARGIN_HORIZONTAL', 46);
define('TEXT_MARGIN_VERTICAL', 80);
define('AVATAR_WIDTH', 64);
define('AVATAR_HEIGHT', 64);
define('FONT_REGULAR', STATIC_DIRECTORY.'/fonts/lexend_deca_regular.ttf');
define('FONT_BOLD', STATIC_DIRECTORY.'/fonts/lexend_deca_bold.ttf');
define('FONT_LIGHT', STATIC_DIRECTORY.'/fonts/lexend_deca_light.ttf');

//This is just plain copied from preview_image_generator.php:

//Obtained from running: `fc-query --format='%{charset}\n' font.ttf`
define('SUPPORTED_CHARS_REGEX', '/[^\n\x{20}-\x{7e}\x{a0}-\x{17e}\x{18f}\x{192}\x{19d}\x{1a0}-\x{1a1}\x{1af}-\x{1b0}\x{1c4}-\x{1d4}\x{1e6}-\x{1e7}\x{1ea}-\x{1eb}\x{1f1}-\x{1f2}\x{1fa}-\x{21b}\x{22a}-\x{22d}\x{230}-\x{233}\x{237}\x{259}\x{272}\x{2bb}-\x{2bc}\x{2be}-\x{2bf}\x{2c6}-\x{2c8}\x{2cc}\x{2d8}-\x{2dd}\x{300}-\x{304}\x{306}-\x{30c}\x{30f}\x{311}-\x{312}\x{31b}\x{323}-\x{324}\x{326}-\x{328}\x{32e}\x{331}\x{335}\x{394}\x{3a9}\x{3bc}\x{3c0}\x{1e08}-\x{1e09}\x{1e0c}-\x{1e0f}\x{1e14}-\x{1e17}\x{1e1c}-\x{1e1d}\x{1e20}-\x{1e21}\x{1e24}-\x{1e25}\x{1e2a}-\x{1e2b}\x{1e2e}-\x{1e2f}\x{1e36}-\x{1e37}\x{1e3a}-\x{1e3b}\x{1e42}-\x{1e49}\x{1e4c}-\x{1e53}\x{1e5a}-\x{1e5b}\x{1e5e}-\x{1e69}\x{1e6c}-\x{1e6f}\x{1e78}-\x{1e7b}\x{1e80}-\x{1e85}\x{1e8e}-\x{1e8f}\x{1e92}-\x{1e93}\x{1e97}\x{1e9e}\x{1ea0}-\x{1ef9}\x{2007}-\x{200b}\x{2010}\x{2012}-\x{2015}\x{2018}-\x{201a}\x{201c}-\x{201e}\x{2020}-\x{2022}\x{2026}\x{2030}\x{2033}\x{2039}-\x{203a}\x{2044}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{20a1}\x{20a3}-\x{20a4}\x{20a6}-\x{20a7}\x{20a9}\x{20ab}-\x{20ad}\x{20b1}-\x{20b2}\x{20b5}\x{20b9}-\x{20ba}\x{20bc}-\x{20bd}\x{2113}\x{2116}\x{2122}\x{2126}\x{212e}\x{215b}-\x{215e}\x{2202}\x{2205}-\x{2206}\x{220f}\x{2211}-\x{2212}\x{2215}\x{2219}-\x{221a}\x{221e}\x{222b}\x{2248}\x{2260}\x{2264}-\x{2265}\x{25ca}\x{fb01}-\x{fb02}]/u');

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

function get_text_without_missing_glyphs($text) {
	return preg_replace(SUPPORTED_CHARS_REGEX, ' ', $text);
}

function draw_title_with_underscore(&$image, $font_size, $angle, $width, $height, $color, $font, $text, $underscore_color) {
	$bbox = imagettfbbox($font_size, $angle, $font, $text);
	$text_width = abs($bbox[2] - $bbox[0]);
	imagefttext($image, $font_size, $angle, $width, $height, $underscore_color, $font, $text);
	//imageroundedrectangle($image, $width, $height+8, $width+$text_width, $height+10, 1, $underscore_color);
}

function draw_centered_text(&$image, $font_size, $angle, $max_width, $x, $y, $color, $font, $text) {
	$bbox = imagettfbbox($font_size, $angle, $font, $text);
	$text_width = abs($bbox[2] - $bbox[0]);
	imagefttext($image, $font_size, $angle, $x+($max_width-$text_width)/2, $y, $color, $font, $text);
}

//End plain copy from preview_image_generator.php

if (!is_yearly_summary_available()) {
	header("Location: ".lang('url.login'));
	die();
}

//This is just plain copied from yearly_summary.php:
$result = query_yearly_summary_data_by_user_id($user['id'], date('Y'));
$summary_data = mysqli_fetch_assoc($result);

$result_anime = query_yearly_summary_anime_by_user_id($user['id'], date('Y'), 8);
$result_manga = query_yearly_summary_manga_by_user_id($user['id'], date('Y'), 8);
$result_liveaction = query_yearly_summary_liveaction_by_user_id($user['id'], date('Y'), 8);

$anime = array();
while ($data = mysqli_fetch_assoc($result_anime)) {
	array_push($anime, $data);
}
$manga = array();
while ($data = mysqli_fetch_assoc($result_manga)) {
	array_push($manga, $data);
}
$liveaction = array();
while ($data = mysqli_fetch_assoc($result_liveaction)) {
	array_push($liveaction, $data);
}

$anime_watched = $summary_data['anime_watched'];
$manga_watched = $summary_data['manga_watched'];
$liveaction_watched = $summary_data['liveaction_watched'];
$anime_length = $summary_data['anime_length'];
$manga_length = $summary_data['manga_length'];
$liveaction_length = $summary_data['liveaction_length'];
$comments_left = $summary_data['comments_left'];
$most_commented_version = $summary_data['most_commented_version'];
$anime_rank = $summary_data['anime_rank'];
$manga_rank = $summary_data['manga_rank'];
$liveaction_rank = $summary_data['liveaction_rank'];
$total_users = $summary_data['total_users'];

mysqli_free_result($result);

if (!DISABLE_COMMUNITY && !SITE_IS_HENTAI) {
	//Get data via community API - assume zero if failed
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/get_user_yearly_stats');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, 
		  json_encode(array(
		  	'username' => $user['username'],
		  	'year' => date('Y'),
		  	)));
	$output = curl_exec($curl);
	
	curl_close($curl);

	$result = json_decode($output);

	if (empty($result) || $result->status!='ok') {
		$community_posts = 0;
		$most_commented_post = '';
	} else {
		$community_posts = $result->number_of_posts;
		$most_commented_post = $result->most_posted_topic;
	}
} else {
	$community_posts = 0;
	$most_commented_post = '';
}

$anime_length = intval($anime_length/3600);
$liveaction_length = intval($liveaction_length/3600);

//End plain copy from yearly_summary.php

//Make this downloadable
header('Content-Disposition: attachment; filename="'.sprintf(lang('users.yearly_summary.image.filename'), date('Y'), CURRENT_SITE_NAME).'.jpg"');
header("Content-Type: image/jpeg");

//Create the image

$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

//Load bg and scale it as needed
$background = imagecreatefromjpeg(STATIC_DIRECTORY.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg');
$background = scale_smallest_side($background, IMAGE_WIDTH, IMAGE_HEIGHT);

//Darken bg
$semi_transparent = imagecolorallocatealpha($background,0,0,0,30);
imagefilledrectangle($background, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $semi_transparent);

//Paste into canvas
imagecopy($image, $background, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);

//Colors
$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$gray = imagecolorallocate($image, 0xAA, 0xAA, 0xAA);
if (SITE_IS_HENTAI) {
	$primary_color = imagecolorallocatealpha($image, 0xD9, 0x18, 0x83, 0);
} else {
	$primary_color = imagecolorallocatealpha($image, 0x6A, 0xA0, 0xF8, 0);
}
$secondary_color = imagecolorallocate($image, 0xF9, 0xC0, 0x2B);

//Title
$bbox = imagettfbbox(42, 0, FONT_BOLD, sprintf(lang('users.yearly_summary.image.header'), date('Y'), CURRENT_SITE_NAME));
$title_width = abs($bbox[2] - $bbox[0]);
imagefttext($image, 42, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL, $white, FONT_BOLD, sprintf(lang('users.yearly_summary.image.header'), date('Y'), CURRENT_SITE_NAME));
imageroundedrectangle($image, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+8, TEXT_MARGIN_HORIZONTAL+$title_width, TEXT_MARGIN_VERTICAL+8+4, 2, $primary_color);

//User avatar
imageroundedrectangle($image, IMAGE_WIDTH-AVATAR_WIDTH-TEXT_MARGIN_HORIZONTAL-2, AVATAR_HEIGHT/2-2, IMAGE_WIDTH-TEXT_MARGIN_HORIZONTAL+2, AVATAR_HEIGHT/2+AVATAR_HEIGHT+2, 34, $primary_color);

if (!empty($user['avatar_filename'])) {
	$avatar_path=STATIC_DIRECTORY.'/images/avatars/'.$user['avatar_filename'];
	$type='png';
}
else if (!empty($user['fansub_id'])) {
	$avatar_path=STATIC_DIRECTORY.'/images/icons/'.$user['fansub_id'].'.png';
	$type='png';
}
else {
	$avatar_path=STATIC_DIRECTORY.'/images/site/default_avatar.jpg';
	$type='jpg';
}

if ($type=='jpg') {
	$avatar = imagecreatefromjpeg($avatar_path);
} else {
	$avatar = imagecreatefrompng($avatar_path);
}
$avatar = scale_smallest_side($avatar, AVATAR_WIDTH, AVATAR_HEIGHT);
$avatar = round_corners($avatar, 32);
imagecopy($image, $avatar, IMAGE_WIDTH-AVATAR_WIDTH-TEXT_MARGIN_HORIZONTAL, AVATAR_HEIGHT/2, 0, 0, AVATAR_WIDTH, AVATAR_HEIGHT);

//Username
$text = \andrewgjohnson\linebreaks4imagettftext(42, 0, FONT_BOLD, get_text_without_missing_glyphs($user['username']), IMAGE_WIDTH-TEXT_MARGIN_HORIZONTAL*3-$title_width-AVATAR_WIDTH, 1);
$bbox = imagettfbbox(42, 0, FONT_BOLD, $text);
$text_width = abs($bbox[2] - $bbox[0]);
imagefttext($image, 42, 0, IMAGE_WIDTH-$text_width-AVATAR_WIDTH-TEXT_MARGIN_HORIZONTAL*1.5, TEXT_MARGIN_VERTICAL, $white, FONT_BOLD, $text);

//Section titles
draw_title_with_underscore($image, 28, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+80, $white, FONT_BOLD, lang('users.yearly_summary.image.this_year'), $primary_color);
draw_title_with_underscore($image, 28, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+380, $white, FONT_BOLD, lang('users.yearly_summary.image.totals'), $primary_color);
if (!empty($comments_left) || (!empty($community_posts) && !SITE_IS_HENTAI && !DISABLE_COMMUNITY)) {
	draw_title_with_underscore($image, 28, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+680, $white, FONT_BOLD, lang('users.yearly_summary.image.community'), $primary_color);
}
draw_title_with_underscore($image, 28, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+80, $white, FONT_BOLD, lang('users.yearly_summary.image.my_anime'), $primary_color);
draw_title_with_underscore($image, 28, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+335, $white, FONT_BOLD, lang('users.yearly_summary.image.my_manga'), $primary_color);
if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
	draw_title_with_underscore($image, 28, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+590, $white, FONT_BOLD, lang('users.yearly_summary.image.my_liveaction'), $primary_color);
}

//Counters:
if (SITE_IS_HENTAI || DISABLE_LIVE_ACTION) {
	$counter_number=2;
} else {
	$counter_number=3;
}
draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*0, TEXT_MARGIN_VERTICAL+80+180, $white, FONT_BOLD, $anime_watched==1 ? lang('users.yearly_summary.anime.single') : lang('users.yearly_summary.anime.plural'));
draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*0, TEXT_MARGIN_VERTICAL+80+130, $secondary_color, FONT_BOLD, $anime_watched);
draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*1, TEXT_MARGIN_VERTICAL+80+180, $white, FONT_BOLD, $manga_watched==1 ? lang('users.yearly_summary.manga.single') : lang('users.yearly_summary.manga.plural'));
draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*1, TEXT_MARGIN_VERTICAL+80+130, $secondary_color, FONT_BOLD, $manga_watched);
if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
	draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*2, TEXT_MARGIN_VERTICAL+80+180, $white, FONT_BOLD, $liveaction_watched==1 ? lang('users.yearly_summary.liveaction.single') : lang('users.yearly_summary.liveaction.plural'));
	draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*2, TEXT_MARGIN_VERTICAL+80+130, $secondary_color, FONT_BOLD, $liveaction_watched);
}

draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2)*0, TEXT_MARGIN_VERTICAL+380+180, $white, FONT_BOLD, $$anime_length+$liveaction_length==1 ? lang('users.yearly_summary.hours_seen.single') : lang('users.yearly_summary.hours_seen.plural'));
draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2)*0, TEXT_MARGIN_VERTICAL+380+130, $secondary_color, FONT_BOLD, $anime_length+$liveaction_length);
draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2)*1, TEXT_MARGIN_VERTICAL+380+180, $white, FONT_BOLD, $manga_length==1 ? lang('users.yearly_summary.pages_read.single') : lang('users.yearly_summary.pages_read.plural'));
draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/2)*1, TEXT_MARGIN_VERTICAL+380+130, $secondary_color, FONT_BOLD, $manga_length);

//Community:

if (!empty($comments_left) || (!empty($community_posts) && !SITE_IS_HENTAI && !DISABLE_COMMUNITY)) {
	if (!SITE_IS_HENTAI && !DISABLE_COMMUNITY) {
		$counter_number=2;
	} else {
		$counter_number=1;
	}

	draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*0, TEXT_MARGIN_VERTICAL+680+155, $white, FONT_BOLD, $comments_left==1 ? lang('users.yearly_summary.image.comments.single') : lang('users.yearly_summary.image.comments.plural'));
	draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*0, TEXT_MARGIN_VERTICAL+680+105, $secondary_color, FONT_BOLD, $comments_left);
	if (!SITE_IS_HENTAI && !DISABLE_COMMUNITY) {
		draw_centered_text($image, 22, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*1, TEXT_MARGIN_VERTICAL+680+155, $white, FONT_BOLD, $community_posts==1 ? lang('users.yearly_summary.image.posts.single') : lang('users.yearly_summary.image.posts.plural'));
		draw_centered_text($image, 52, 0, (IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number, ((IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2)/$counter_number)*1, TEXT_MARGIN_VERTICAL+680+105, $secondary_color, FONT_BOLD, $community_posts);
	}

	$extra_height = 0;
	if (!empty($comments_left)) {
		$text = \andrewgjohnson\linebreaks4imagettftext(22, 0, FONT_REGULAR, get_text_without_missing_glyphs(lang('users.yearly_summary.image.most_commented').' '.$most_commented_version), IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2, 1);
		imagefttext($image, 22, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+915+$extra_height, $white, FONT_REGULAR, $text);
		$extra_height+=35;
	}
	if (!empty($community_posts) && !SITE_IS_HENTAI && !DISABLE_COMMUNITY) {
		$text = \andrewgjohnson\linebreaks4imagettftext(22, 0, FONT_REGULAR, get_text_without_missing_glyphs(lang('users.yearly_summary.image.most_posted').' '.$most_commented_post), IMAGE_WIDTH/2-TEXT_MARGIN_HORIZONTAL*2, 1);
		imagefttext($image, 22, 0, TEXT_MARGIN_HORIZONTAL, TEXT_MARGIN_VERTICAL+915+$extra_height, $white, FONT_REGULAR, $text);
		$extra_height+=35;
	}
}

//Achievements:

$total_achievements = 0;
if (!empty($anime_rank)) {
	$total_achievements++;
}
if (!empty($manga_rank)) {
	$total_achievements++;
}
if (!empty($liveaction_rank) && !SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
	$total_achievements++;
}

if ($total_achievements>0) {
	if (SITE_IS_HENTAI) {
		$extra_height = 590;
	} else {
		$extra_height = 840;
	}
	draw_title_with_underscore($image, 28, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+$extra_height, $white, FONT_BOLD, lang('users.yearly_summary.image.achievements'), $primary_color);
	if (!empty($anime_rank)) {
		imagefttext($image, 22, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+40+$extra_height, $white, FONT_REGULAR, sprintf(lang('users.yearly_summary.image.anime_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$anime_rank)/$total_users*100, 1))));
		$extra_height+=35;
	}
	if (!empty($manga_rank)) {
		imagefttext($image, 22, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+40+$extra_height, $white, FONT_REGULAR, sprintf(lang('users.yearly_summary.image.manga_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$manga_rank)/$total_users*100, 1))));
		$extra_height+=35;
	}
	if (!empty($liveaction_rank) && !SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
		imagefttext($image, 22, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+40+$extra_height, $white, FONT_REGULAR, sprintf(lang('users.yearly_summary.image.liveaction_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$liveaction_rank)/$total_users*100, 1))));
		$extra_height+=35;
	}
}

//Draw series data
$i=0;
foreach ($anime as $data) {
	//Load cover and scale it as needed
	$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/version_".$data['id'].".jpg");
	$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
	$cover = round_corners($cover, 12);
	imagecopy($image, $cover, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+80+22, 0, 0, COVER_WIDTH, COVER_HEIGHT);
	draw_centered_text($image, 14, 0, COVER_WIDTH, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+80+22+COVER_HEIGHT+22, $white, FONT_REGULAR, get_relative_time_spent_short($data['total_length']));
	$i++;
}
if ($i==0) {
	imagefttext($image, 20, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+80+40, $gray, FONT_REGULAR, lang('users.yearly_summary.image.my_anime_empty'));
}

$i=0;
foreach ($manga as $data) {
	//Load cover and scale it as needed
	$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/version_".$data['id'].".jpg");
	$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
	$cover = round_corners($cover, 12);
	imagecopy($image, $cover, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+335+22, 0, 0, COVER_WIDTH, COVER_HEIGHT);
	draw_centered_text($image, 14, 0, COVER_WIDTH, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+335+22+COVER_HEIGHT+22, $white, FONT_REGULAR, get_relative_pages_read_short($data['total_length']));
	$i++;
}
if ($i==0) {
	imagefttext($image, 20, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+335+40, $gray, FONT_REGULAR, lang('users.yearly_summary.image.my_manga_empty'));
}

if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
	$i=0;
	foreach ($liveaction as $data) {
		//Load cover and scale it as needed
		$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/version_".$data['id'].".jpg");
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
		$cover = round_corners($cover, 12);
		imagecopy($image, $cover, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+590+22, 0, 0, COVER_WIDTH, COVER_HEIGHT);
		draw_centered_text($image, 14, 0, COVER_WIDTH, IMAGE_WIDTH/2+(COVER_WIDTH+5)*$i, TEXT_MARGIN_VERTICAL+590+22+COVER_HEIGHT+22, $white, FONT_REGULAR, get_relative_time_spent_short($data['total_length']));
		$i++;
	}
	if ($i==0) {
		imagefttext($image, 20, 0, IMAGE_WIDTH/2, TEXT_MARGIN_VERTICAL+590+40, $gray, FONT_REGULAR, lang('users.yearly_summary.image.my_liveaction_empty'));
	}
}

//Generate image
imagejpeg($image, NULL, 90);
imagedestroy($image);
?>
