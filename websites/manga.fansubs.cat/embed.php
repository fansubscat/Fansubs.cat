<?php
require_once("db.inc.php");
require_once("common.inc.php");

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);

$result = query("SELECT * FROM file WHERE id=$file_id AND original_filename IS NOT NULL");
$file = mysqli_fetch_assoc($result) or $failed=TRUE;
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
		<title>Fansubs.cat - Manga en català</title>
		<link rel="stylesheet" href="<?php echo $base_url; ?>/style/anime.css?v=1" media="screen" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script src="<?php echo $base_url; ?>/js/common.js?v=1"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-file-id" value="<?php echo $file['id']; ?>" />
		<div id="overlay">
			<div id="overlay-mask"></div>
			<div id="overlay-content"></div>
		</div>
	</body>
</html>
