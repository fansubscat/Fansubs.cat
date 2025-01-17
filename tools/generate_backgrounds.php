<?php
define('IMAGE_WIDTH', 5000);
define('IMAGE_HEIGHT', 3000);
define('COVER_WIDTH', 300);
define('COVER_HEIGHT', 424);
define('MARGIN', 21);
define('MODE', 'dark');
define('ROUND_CORNER', 16);

function scale_smallest_side($image, $desired_width, $desired_height) {
	$width = imagesx($image);
	$height = imagesy($image);

	if ($width/$height < $desired_width/$desired_height) {
		$output_width = $desired_width;
		$output_height = $desired_width*$height/$width;
		$image = imagescale($image, $output_width, $output_height, IMG_QUADRATIC);
		$image = imagecrop($image, ['x' => 0, 'y' => ($output_height-$desired_height)/2, 'width' => $desired_width, 'height' => $desired_height]);
	} else {
		$output_width = $desired_height*$width/$height;
		$output_height = $desired_height;
		$image = imagescale($image, $output_width, $output_height, IMG_QUADRATIC);
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

//Empty canvas - we will draw here
$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);
$bg = imagecolorallocate($image, MODE=='light' ? 0xFF : 0x2B, MODE=='light' ? 0xFF : 0x2B, MODE=='light' ? 0xFF : 0x2B);
imagefill($image, 0, 0, $bg);

$current_height = 0;
$current_width = 0;

//First, apply shadows
$shadow = imagecreatetruecolor(COVER_WIDTH, COVER_HEIGHT);
imagefill($shadow, 0, 0, imagecolorallocate($shadow, MODE=='light' ? 0xA0 : 0x00, MODE=='light' ? 0xA0 : 0x00, MODE=='light' ? 0xA0 : 0x00));
$shadow = round_corners($shadow, ROUND_CORNER);

for ($i=0; $i<6; $i++) {
	for ($j=0; $j<15; $j++) {
		imagecopy($image, $shadow, $current_width+5, $current_height+5, 0, 0, COVER_WIDTH, COVER_HEIGHT);
		$current_width = $current_width+COVER_WIDTH+MARGIN;
	}
	if ($i % 2 == 1) {
		$current_width = 0;
	} else {
		$current_width = -(COVER_WIDTH+MARGIN)/2;
	}
	$current_height = $current_height+COVER_HEIGHT+MARGIN;
}

//Blur shadows
for ($i=0; $i<30; $i++) {
	imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
}

//Now apply covers
$current_height = 0;
$current_width = 0;

//FANSUBS
$type='fansubs';
$covers = array(
	array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 135, 159, 550, 690),
	array(0, 0, 0, 0, 0, 3922, 606, 4187, 345, 3897, 597, 562, 599, 461, 267),
	array(0, 0, 0, 0, 0, 652, 3880, 4093, 136, 4119, 673, 3338, 4177, 298, 2088),
	array(0, 0, 0, 0, 0, 410, 4270, 3934, 3881, 4163, 3383, 552, 4058, 541, 3812),
	array(0, 0, 0, 0, 0, 0, 515, 4049, 4272, 119, 3975, 622, 341, 161, 3962)
);

//HENTAI
$type='hentai';
$covers = array(
	array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4095, 3980, 4178, 4188),
	array(0, 0, 0, 0, 0, 4211, 3973, 4021, 3998, 4184, 545, 3942, 4015, 3911, 4063),
	array(0, 0, 0, 0, 0, 4030, 3915, 285, 4240, 4001, 4073, 642, 4285, 624, 4107),
	array(0, 0, 0, 0, 0, 3979, 4241, 3918, 598, 601, 591, 4083, 3040, 522, 4268),
	array(0, 0, 0, 0, 0, 0, 4009, 4108, 4014, 675, 4004, 3967, 4183, 284, 499)
);

for ($i=0; $i<6; $i++) {
	for ($j=0; $j<15; $j++) {
		if (file_exists('../websites/static/images/covers/version_'.$covers[$i][$j].'.jpg')) {
			$cover = imagecreatefromjpeg('../websites/static/images/covers/version_'.$covers[$i][$j].'.jpg');
		} else {
			$cover = imagecreatetruecolor(COVER_WIDTH, COVER_HEIGHT);
			imagefill($cover, 0, 0, imagecolorallocate($cover, MODE=='light' ? 0xA0 : 0x2B, MODE=='light' ? 0xA0 : 0x2B, MODE=='light' ? 0xA0 : 0x2B));
		}
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
		$cover = round_corners($cover, ROUND_CORNER);
		imagecopy($image, $cover, $current_width, $current_height, 0, 0, COVER_WIDTH, COVER_HEIGHT);
		$current_width = $current_width+COVER_WIDTH+MARGIN;
	}
	if ($i % 2 == 1) {
		$current_width = 0;
	} else {
		$current_width = -(COVER_WIDTH+MARGIN)/2;
	}
	$current_height = $current_height+COVER_HEIGHT+MARGIN;
}

imagepng($image, 'tmp.png', 9);
imagedestroy($image);

$im = new Imagick();
$im->readImage('tmp.png');
$im->setImageFormat('png');
$im->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
//$gamma=$im->getImageGamma();
//$im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
//$im->setImageMatte(true);
$controlPoints = array( 0, 0, 
                        0, -500,

                        0, $im->getImageHeight(),
                        -850, $im->getImageHeight() - 50,

                        $im->getImageWidth(), 0,
                        $im->getImageWidth() + 1250, -520,

                        $im->getImageWidth(), $im->getImageHeight(),
                        $im->getImageWidth() + 900, $im->getImageHeight()+ 1000);                    
$im->distortImage(Imagick::DISTORTION_PERSPECTIVE, $controlPoints, true);
//$im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
//$im->setImageGamma($gamma);
$im->cropImage(3840, 2160, 1480, 590);
$im->writeImage($type.'_'.MODE.'.png');
unlink('tmp.png');
?>
