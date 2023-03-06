<?php
define('PAGE_STYLE_TYPE', 'news');
require_once("../common.fansubs.cat/header.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

//Are we processing a page?
if (!empty($_GET['page'])) {
	if (is_numeric($_GET['page']) && $_GET['page']>1){
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

if (isset($_GET['query']) && $_GET['query']!='') {
	$search_query = urldecode($_GET['query']);
}
else{
	$search_query = NULL;
}

$result = query_latest_news($user, $search_query, $page, 20);
?>
					<div class="section">
						<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo $search_query!==NULL ? 'Resultats de la cerca' : 'Darreres notícies'; ?></h2>
<?php
if (mysqli_num_rows($result)==0){
?>
						<h2 class="article-title">No hem trobat cap notícia!</h2>
						<div class="article-content">I que no hi hagi notícies són males notícies...</div>
<?php
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
										<a class="news-fansub" href="<?php echo (!empty($row['fansub_url']) && empty($row['archive_url']) ? $row['fansub_url'] : (!empty($row['archive_url']) ? $row['archive_url'] : '#')); ?>" target="_blank"><img src="<?php echo STATIC_URL.'/images/icons/'.$row['fansub_id'].'.png'; ?>" alt=""> <?php echo $row['fansub_name']; ?></a> • <span class="news-date" title="<?php echo date("d/m/Y \\a \\l\\e\\s H:i:s", strtotime($row['date'])); ?>"><?php echo relative_time(strtotime($row['date'])); ?></span>
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
?>
					</div>
<?php
}
?>
					<div id="bottom-navigation">
<?php
if ($page>1 && mysqli_num_rows($result)>0){
?>
						<a id="nav-newer" class="normal-button" href="<?php echo ($search_query!==NULL ? '/cerca/'.urlencode(urlencode($search_query)) : '') . ($page==2 ? '' : '/pagina/'.($page-1)); ?>">← Notícies més noves</a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = query_latest_news($user, $search_query, $page+1, 20);

if (mysqli_num_rows($result)>0){
?>
						<a id="nav-older" class="normal-button" href="<?php echo ($search_query!==NULL ? '/cerca/'.urlencode(urlencode($search_query)) : '') . '/pagina/'.($page+1); ?>">Notícies més antigues →</a>
<?php
}
?>
					</div>
<?php
mysqli_free_result($result);
require_once("../common.fansubs.cat/footer.inc.php");
?>
