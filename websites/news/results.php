<?php
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'news');
}
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

if (isset($_GET['search'])) {
	define('PAGE_IS_SEARCH', TRUE);
}

//Are we processing a page?
if (!empty($_GET['page'])) {
	if (is_numeric($_GET['page']) && $_GET['page']>0){
		$page = $_GET['page'];
	} else {
		http_response_code(404);
		include('error.php');
		die();
	}
}
else{
	$page = 1;
}

$show_blacklisted_fansubs = FALSE;
$show_own_news = FALSE;
$text = NULL;
$fansub_slug = NULL;
$min_month = '2003-05';
$max_month = date('Y-m');

if (defined('PAGE_IS_SEARCH')) {
	$text = (isset($_GET['query']) ? $_GET['query'] : "");
	$show_blacklisted_fansubs = TRUE;
	if (!empty($_POST['fansub'])) {
		if ($_POST['fansub']=='-1') {
			$show_blacklisted_fansubs = TRUE;
		} else {
			$show_blacklisted_fansubs = FALSE;
		}
	}
	if (!empty($_POST['fansub']) && $_POST['fansub']=='-3') {
		$show_own_news = TRUE;
	}
	if (isset($_POST['min_month']) && isset($_POST['max_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_POST['min_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_POST['max_month'])) {
		$min_month = $_POST['min_month'];
		$max_month = $_POST['max_month'];
	}
	if (!empty($_POST['fansub']) && $_POST['fansub']!='-1' && $_POST['fansub']!='-2' && $_POST['fansub']!='-3') {
		$fansub_slug = $_POST['fansub'];
	}
}

$result = query_latest_news($user, $text, $page, 20, $fansub_slug, $show_blacklisted_fansubs, $show_own_news, $min_month, $max_month);

?>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo defined('PAGE_IS_SEARCH') ? 'Resultats de la cerca' : 'Darreres notícies'; ?></h2>
<?php
if (mysqli_num_rows($result)==0){
	if (defined('PAGE_IS_SEARCH')) {
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No s’ha trobat cap contingut per a aquesta cerca. Prova de reduir la cerca o fes-ne una altra.</div></div>
<?php
	} else {
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No hem trobat cap notícia! I que no hi hagi cap notícia és una mala notícia...</div></div>
<?php
	}
}
else{
	while ($row = mysqli_fetch_assoc($result)){
?>
							<div class="news-article">
								<div class="news-content">
									<div class="news-text-wrapper">
										<h3 class="news-title">
<?php
		if ($row['url']!=NULL){
?>
										<a href="<?php echo $row['url']; ?>" target="_blank"><?php echo $row['title']; ?></a>
<?php
		}
		else{
?>
										<?php echo $row['title']; ?>
<?php
		}
?>
										</h3>
										<div class="news-info">
<?php
		if (!empty($row['fansub_url']) && empty($row['archive_url'])) {
			$url = $row['fansub_url'];
		} else if (!empty($row['archive_url'])) {
			$url = $row['archive_url'];
		} else {
			$url = NULL;
		}
?>
											<a class="news-fansub"<?php $url!==NULL ? ' href="'.$url.'" target="_blank"' : ''; ?>><img src="<?php echo $row['fansub_id']!==NULL ? STATIC_URL.'/images/icons/'.$row['fansub_id'].'.png' : STATIC_URL.'/images/site/default_fansub.png'; ?>" alt=""> <?php echo $row['fansub_id']!==NULL ? $row['fansub_name'] : 'Fansubs.cat'; ?></a> • <span class="news-date" title="<?php echo date("d/m/Y \\a \\l\\e\\s H:i:s", strtotime($row['date'])); ?>"><?php echo relative_time(strtotime($row['date'])); ?></span>
										</div>
										<div class="news-text">
											<!-- Begin article content -->
											<?php echo $row['contents']; ?>
											<!-- End article content -->
										</div>
<?php
		if ($row['image']!=NULL){
?>
										<img class="news-image-mobile" src="<?php echo STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt=""/>
<?php
		}
?>
<?php
		if ($row['url']!=NULL){
?>
										<div class="news-readmore">
											<a class="normal-button" href="<?php echo $row['url']; ?>" target="_blank"><?php echo "Vés a {$row['fansub_name']}"; ?> ➔</a>
										</div>
<?php
		}
?>
									</div>
								</div>
<?php
		if ($row['image']!=NULL){
?>
								<img class="news-image" src="<?php echo STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt=""/>
<?php
		}
?>
							</div>
<?php
	}
}
?>
							<div id="bottom-navigation">
<?php
if ($page>1 && mysqli_num_rows($result)>0){
?>
								<a id="nav-newer" class="normal-button"<?php echo (defined('PAGE_IS_SEARCH') ? ' onclick="loadSearchResults(currentPage-1);"' : ' href="'.($page==2 ? '/' : '/pagina/'.($page-1)).'"'); ?>"><i class="fa fa-fw fa-arrow-left"></i> Notícies més noves</a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = query_latest_news($user, $text, $page+1, 20, $fansub_slug, $show_blacklisted_fansubs, $show_own_news, $min_month, $max_month);

if (mysqli_num_rows($result)>0){
?>
								<a id="nav-older" class="normal-button"<?php echo (defined('PAGE_IS_SEARCH') ? ' onclick="loadSearchResults(currentPage+1);"' : ' href="/pagina/'.($page+1).'"'); ?>">Notícies més antigues <i class="fa fa-fw fa-arrow-right"></i></a>
<?php
}
?>
							</div>
						</div>
<?php
mysqli_free_result($result);

if (defined('PAGE_IS_SEARCH')) {
	require_once("../common.fansubs.cat/footer_text.inc.php");
}
?>
