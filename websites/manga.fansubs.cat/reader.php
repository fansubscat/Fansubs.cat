<?php
require_once("db.inc.php");
require_once("common.inc.php");

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);

$result = query("SELECT f.*,m.reader_type, CONCAT(m.name,' - Volum ', vo.number, ' - ', IF(m.show_chapter_numbers AND c.number IS NOT NULL,CONCAT('Capítol ', c.number,': ',ct.title),ct.title)) chapter_name FROM file f LEFT JOIN manga_version v ON f.manga_version_id=v.id LEFT JOIN chapter c ON f.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN chapter_title ct ON c.id=ct.chapter_id AND v.id=ct.manga_version_id LEFT JOIN manga m ON v.manga_id=m.id WHERE f.id=$file_id AND f.original_filename IS NOT NULL");
$file = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

if (!empty($_COOKIE['force_reader_ltr'])){
	$direction = 'reader-ltr';
} else {
	$direction = 'reader-rtl';
}

if (!empty($_COOKIE['force_long_strip'])){
	$mode = 'strip';
	$direction = 'reader-ltr'; //Force direction (one element only)
} else {
	$mode = $file['reader_type'];
}

$base_path="images/storage/$file_id/";

if (!file_exists($base_path)) {
	http_response_code(404);
	include('error.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Fansubs.cat - Manga en català</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@1.10.0/dist/css/lightgallery.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="/js/lightgallery.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/lg-fullscreen@1.2.1/dist/lg-fullscreen.min.js"></script>
		<script src="/js/lg-zoom-custom.js"></script>
		<style>
			.lg-outer .lg-video-cont{
				padding: 0;
				height: 100%;
			}
			.lg-outer .lg-video{
				padding-bottom: 0;
				height: 100%;
			}
			.lg-toolbar{
				background-color: rgba(0,0,0,0);
			}
			.lg-toolbar .lg-icon{
				background-color: rgba(0,0,0,0.5);
				color: #DDD;
			}
			.lg-actions .lg-next, .lg-actions .lg-prev{
				background-color: rgba(0,0,0,0.5);
				color: #DDD;
			}

			.lg-outer.lg-hide-items .lg-toolbar {
				pointer-events: none;
			}
			#lg-counter{
				background-color: rgba(0,0,0,0.5);
				height: 47px;
				padding-right: 20px;
				padding-top: 15px;
				color: #FFF;
<?php
if ($mode=='strip'){
?>
				display: none;
<?php
}
?>
			}
			.lg-custom-zoom-out-icon:after {
				content: "\e312" !important;
				font-size: 24px !important;
			}
			.lg-custom-zoom-in-icon:after {
				content: "\e311" !important;
				font-size: 24px !important;
			}
		</style>
	</head>
	<body style="margin: 0;">
		<div id="overlay" class="<?php echo $direction; ?>">
		</div>
		<script>
			var barsTimeout=null;
			function hideBars() {
				$('.lg-outer').addClass('lg-hide-items');
				clearTimeout(barsTimeout);
			}
			function showBars() {
				$('.lg-outer').removeClass('lg-hide-items');
				clearTimeout(barsTimeout);
				barsTimeout = setTimeout(function () {
					hideBars();
				}, 3000);
			}
			function showOrHideBars() {
				if ($('.lg-outer').hasClass('lg-hide-items')) {
					showBars();
				} else {
					hideBars();
				}
			}
<?php
if ($mode!='strip'){
?>
			var firstTouch;
			var lastTouch;
			$(window).mousemove(function(m){
				showBars();
			});
			$(window).on('touchstart', function(m){
				firstTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
				lastTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
			});
			$(window).on('touchmove', function(m){
				lastTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
			});
			$(window).on('touchend', function(m){
				if (Math.abs(lastTouch.pageX-firstTouch.pageX)<10 && Math.abs(lastTouch.pageY-firstTouch.pageY)<10){
					showOrHideBars();
				}
				firstTouch=null;
				lastTouch=null;
			});
<?php
}
?>

			$(document).ready(function() {
				var lg = $('#overlay');
				lg.lightGallery({
<?php
if ($mode=='strip'){
?>
					zoom: false,
<?php
}
?>
					hideControlOnEnd: true,
					speed: 300,
					hideBarsDelay: 0,
					closable: false,
					loop: false,
					preload: 3,
					actualSize: $(window).width() >= 768,
					controls: $(window).width() >= 768,
					download: false,
					dynamic: true,
					dynamicEl: [
<?php
if ($mode=='strip'){
?>
						{"src": "strip_reader.php?file_id=<?php echo $file['id']; ?>", "iframe": true}
<?php
} else {
	$files = scandir($base_path);
	natsort($files);
	if ($direction=='reader-rtl') {
		$files = array_reverse($files);
	}

	$first = TRUE;
	foreach ($files as $file) {
		if ($file=='.' || $file=='..') {
			continue;
		}
		if (!$first) {
			echo ",\n";
		} else {
			$first=FALSE;
		}
		echo "\t\t\t\t\t\t".'{"src": "'.$base_path.$file.'"}';
	}
}
?>
],
					index: <?php echo $direction=='reader-rtl' ? (max(count($files)-3, 0)) : 0; ?>

				});
				showBars();
				lg.on('onCloseAfter.lg', function(event){
					window.parent.$('#overlay-close').click();
				})
<?php
if ($mode=='strip'){
?>
				window.parent.currentPagesRead=<?php echo $file['number_of_pages']; ?>;	
<?php
} else {
?>
				var viewedPages = [];
				lg.on('onAfterSlide.lg', function(event, prevIndex, index, fromTouch, fromThumb){
					if (viewedPages.indexOf(index) === -1) {
						viewedPages.push(index);
					}
					window.parent.currentPagesRead=viewedPages.length;
				})
<?php
}
?>
			});
		</script>
	</body>
</html>
