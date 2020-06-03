<?php
require_once("db.inc.php");
require_once("common.inc.php");

$result = query("SELECT * FROM link WHERE id=".escape($_GET['link_id']).' AND url IS NOT NULL');
$link = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	header("Location: /error.php?code=404");
	die();
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Fansubs.cat - Anime</title>
		<link rel="stylesheet" media="screen" type="text/css" href="/style/anime.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="/js/js.cookie-2.1.2.min.js"></script>
		<script src="/js/common.js"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-link-id" value="<?php echo $link['id']; ?>" />
		<input type="hidden" id="data-method" value="<?php echo htmlspecialchars(get_display_method($link['url'])); ?>" />
		<input type="hidden" id="data-url" value="<?php echo htmlspecialchars(base64_encode(get_display_url($link['url']))); ?>" />
		<div id="overlay">
			<div id="overlay-mask"></div>
			<div id="overlay-content"></div>
		</div>
	</body>
</html>
