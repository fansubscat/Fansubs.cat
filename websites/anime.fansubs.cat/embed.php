<?php
require_once("db.inc.php");
require_once("common.inc.php");

$link_id = (!empty($_GET['link_id']) ? intval($_GET['link_id']) : 0);

$result = query("SELECT * FROM link WHERE id=$link_id AND url IS NOT NULL");
$link = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
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
		<title>Fansubs.cat - Anime en català</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" />
		<link rel="stylesheet" href="<?php echo $base_url; ?>/style/anime.css?v=16" media="screen" />
		<link rel="stylesheet" href="https://cdn.plyr.io/3.6.4/plyr.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script src="https://cdn.plyr.io/3.6.4/plyr.js"></script>
		<script src="<?php echo $base_url; ?>/js/common.js?v=16"></script>
		<script src="<?php echo $base_url; ?>/js/megajs.js"></script>
		<script src="<?php echo $base_url; ?>/js/videostream.js"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-title" value="<?php echo 'Títol de prova'; ?>" />
		<input type="hidden" id="data-link-id" value="<?php echo $link['id']; ?>" />
		<input type="hidden" id="data-method" value="<?php echo htmlspecialchars(get_display_method($link['url'])); ?>" />
		<input type="hidden" id="data-url" value="<?php echo htmlspecialchars(base64_encode(get_display_url($link['url']))); ?>" />
		<div id="overlay">
			<div id="overlay-content"></div>
		</div>
	</body>
</html>
