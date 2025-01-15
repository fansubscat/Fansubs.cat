<?php
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'news');
}
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai_ajax();

if (isset($_GET['search'])) {
	define('PAGE_IS_SEARCH', TRUE);
}

//Are we processing a page?
if (!empty($_GET['page'])) {
	if (is_numeric($_GET['page']) && $_GET['page']>0){
		$page = $_GET['page'];
	} else {
		http_response_code(404);
		include(__DIR__.'/error.php');
		die();
	}
}
else{
	$page = 1;
}

$show_blacklisted_fansubs = FALSE;
$show_own_news = TRUE;
$show_only_own_news = FALSE;
$text = NULL;
$fansub_slug = NULL;
$min_month = NEWS_STARTING_MONTH;
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
		$show_only_own_news = TRUE;
	} else if (!empty($_POST['fansub']) && $_POST['fansub']>0) {
		$show_own_news = FALSE;
	}
	if (isset($_POST['min_month']) && isset($_POST['max_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_POST['min_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_POST['max_month'])) {
		$min_month = $_POST['min_month'];
		$max_month = $_POST['max_month'];
	}
	if (!empty($_POST['fansub']) && $_POST['fansub']!='-1' && $_POST['fansub']!='-2' && $_POST['fansub']!='-3') {
		$fansub_slug = $_POST['fansub'];
	}
}

$result = query_latest_news(!empty($user) ? $user : NULL, $text, $page, 20, $fansub_slug, $show_blacklisted_fansubs, $show_own_news, $show_only_own_news, $min_month, $max_month);

?>
<?php
if (mysqli_num_rows($result)==0){
	if (defined('PAGE_IS_SEARCH')) {
?>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo lang('news.search.header'); ?></h2>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('news.no_results_found'); ?></div></div>
						</div>
<?php
	} else {
?>
						<div class="section">
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('news.no_news_found'); ?></div></div>
						</div>
<?php
	}
}
else{
	if (defined('PAGE_IS_SEARCH')) {
?>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo lang('news.search.header'); ?></h2>
<?php
	}
	$first = TRUE;
	$last_date = NULL;
	while ($row = mysqli_fetch_assoc($result)){
		if (!defined('PAGE_IS_SEARCH')) {
			if ($last_date!=date("Y-m-d", strtotime($row['date']))) {
				$last_date = date("Y-m-d", strtotime($row['date']));
				if (!$first) {
?>
						</div>
<?php
				}
				$first = FALSE;
?>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo sprintf(lang('news.news_for_day'), get_custom_formatted_date(strtotime($row['date']))); ?></h2>
<?php
			}
		}
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
											<a class="news-fansub"<?php echo $url!==NULL ? ' href="'.$url.'" target="_blank"' : ''; ?>><img src="<?php echo $row['fansub_id']!==NULL ? STATIC_URL.'/images/icons/'.$row['fansub_id'].'.png' : STATIC_URL.'/images/site/default_fansub.png'; ?>" alt=""> <?php echo $row['fansub_id']!==NULL ? $row['fansub_name'] : MAIN_SITE_NAME; ?></a> • <span class="news-date"><?php echo date(lang('date.long_format_no_s'), strtotime($row['date'])); ?></span>
										</div>
										<div class="news-text">
											<!-- Begin article content -->
											<?php echo $row['contents']; ?>
											<!-- End article content -->
										</div>
<?php
		if ($row['image']!=NULL){
?>
										<img class="news-image-mobile" src="<?php echo STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt="">
<?php
		}
?>
<?php
		if ($row['url']!=NULL){
?>
										<div class="news-readmore">
											<a class="normal-button" href="<?php echo $row['url']; ?>" target="_blank"><?php echo sprintf(lang('news.go_to_fansub'), $row['fansub_name']); ?> ➔</a>
										</div>
<?php
		}
?>
									</div>
								</div>
<?php
		if ($row['image']!=NULL){
?>
								<img class="news-image" src="<?php echo STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt="">
<?php
		}
?>
							</div>
<?php
	}
?>
						</div>
<?php
}

$has_printed_navigation = FALSE;
if ($page>1 && mysqli_num_rows($result)>0){
?>
						<div id="bottom-navigation">
<?php
	$has_printed_navigation = TRUE;
?>
							<a id="nav-newer" class="normal-button"<?php echo (defined('PAGE_IS_SEARCH') ? ' onclick="loadSearchResults(currentPage-1);"' : ' href="'.($page==2 ? '/' : lang('url.news.page').'/'.($page-1)).'"'); ?>><i class="fa fa-fw fa-arrow-left"></i> <?php echo lang('news.newer_news'); ?></a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = query_latest_news(!empty($user) ? $user : NULL, $text, $page+1, 20, $fansub_slug, $show_blacklisted_fansubs, $show_own_news, $show_only_own_news, $min_month, $max_month);

if (mysqli_num_rows($result)>0){
	if (!$has_printed_navigation) {
		$has_printed_navigation = TRUE;
?>
						<div id="bottom-navigation">
<?php
	}
?>
							<a id="nav-older" class="normal-button"<?php echo (defined('PAGE_IS_SEARCH') ? ' onclick="loadSearchResults(currentPage+1);"' : ' href="'.lang('url.news.page').'/'.($page+1).'"'); ?>><?php echo lang('news.older_news'); ?> <i class="fa fa-fw fa-arrow-right"></i></a>
<?php
}
mysqli_free_result($result);

if ($has_printed_navigation) {
?>
						</div>
<?php
}

if (defined('PAGE_IS_SEARCH')) {
	require_once(__DIR__.'/../common/footer_text.inc.php');
}
?>
