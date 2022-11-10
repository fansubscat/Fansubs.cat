<?php
require_once("db.inc.php");
require_once("common.inc.php");

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);

$result = query("SELECT f.* FROM file f WHERE f.id=$file_id AND f.original_filename IS NOT NULL");
$file = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$base_path=get_storage_url("storage://Manga/$file_id/", TRUE);
$files = list_remote_files($base_path);

if (count($files)<1) {
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
		<title><?php echo $config['site_title']; ?></title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<style>
			* {
				-webkit-user-select: none;
				-moz-user-select: moz-none;
				-ms-user-select: none;
				user-select: none;
				-webkit-user-drag: none;
				-moz-user-drag: moz-none;
				-ms-user-drag: none;
				user-drag: none;
				cursor: grab;
			}
		</style>
		<script>
			var curDown = false;
			var curYPos = 0;
			var curXPos = 0;

			$(window).mousemove(function(m){
				if(curDown){
					window.scrollBy(curXPos - m.pageX, curYPos - m.pageY)
				}
				window.parent.showBars();
			});

			$(window).mousedown(function(m){
				curYPos = m.pageY;
				curXPos = m.pageX;
				curDown = true;
			});

			$(window).mouseup(function(){
				curDown = false;
			});

			var firstTouch;
			var lastTouch;

			$(window).on('touchstart', function(m){
				firstTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
				lastTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
			});
			$(window).on('touchmove', function(m){
				lastTouch=m.originalEvent.touches[0] || m.originalEvent.changedTouches[0];
			});
			$(window).on('touchend', function(m){
				if (Math.abs(lastTouch.pageX-firstTouch.pageX)<10 && Math.abs(lastTouch.pageY-firstTouch.pageY)<10){
					window.parent.showOrHideBars();
				}
				firstTouch=null;
				lastTouch=null;
				m.preventDefault(); //do not click
			});
		</script>
	</head>
	<body style="margin: 0;">
		<div id="root" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
<?php
	natsort($files);

	foreach ($files as $file) {
		echo "\t\t\t".'<img style="display: block; max-width: 100%; width: auto; height: auto;" src="'.$file.'" alt="" />';
	}
?>
		</div>
	</body>
</html>
