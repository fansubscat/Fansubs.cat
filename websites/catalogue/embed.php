<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');

validate_hentai();

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);
$result = query_series_by_file_id($file_id);
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include(__DIR__.'/error.php');
	die();
}

define('PAGE_STYLE_TYPE', 'embed');

$Parsedown = new Parsedown();
$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

define('PAGE_TITLE', $series['name']);
define('PAGE_PATH', '/'.$series['slug']);
define('PAGE_DESCRIPTION', str_replace("\n", " ", strip_tags($synopsis)));
define('PAGE_PREVIEW_IMAGE', SITE_BASE_URL.'/preview/'.$series['slug'].'.jpg');

require_once(__DIR__.'/../common/header.inc.php');
?>
<span class="embed-data" data-file-id="<?php echo $file_id; ?>" data-title="S’està carregant..."></span>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

