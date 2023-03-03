<?php
$style_type='news';
require_once("../common.fansubs.cat/header.inc.php");
require_once("common.inc.php");

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

if (!empty($user)) {
	$blacklist_condition="f.id NOT IN (SELECT ufbl.fansub_id FROM user_fansub_blacklist ufbl WHERE ufbl.user_id=".$user['id'].")";
} else {
	$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
	if (count($cookie_blacklisted_fansub_ids)>0) {
		$blacklist_condition="f.id NOT IN (".implode(',',$cookie_blacklisted_fansub_ids).")";
	} else {
		$blacklist_condition="1";
	}
}

if (isset($_GET['query']) && $_GET['query']!='') {
	$search_condition = "(n.title LIKE '%".escape(urldecode($_GET['query']))."%' OR n.contents LIKE '%".escape(urldecode($_GET['query']))."%')";
}
else{
	$search_condition = '1';
}

$result = query("SELECT n.*, f.name fansub_name, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.url fansub_url, f.archive_url FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE $blacklist_condition AND $search_condition ORDER BY n.date DESC LIMIT 20 OFFSET ".(($page-1)*20));
?>
					<div class="section">
						<h2 class="section-title-main"><i class="fa fa-fw fa-newspaper"></i> <?php echo (isset($_GET['query']) && $_GET['query']!='') ? 'Resultats de la cerca' : 'Darreres notícies'; ?></h2>
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
										<a href="<?php echo $row['url']; ?>"><?php echo $row['title']; ?></a>
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
										<a class="news-fansub" href="<?php echo (!empty($row['fansub_url']) && empty($row['archive_url']) ? $row['fansub_url'] : (!empty($row['archive_url']) ? $row['archive_url'] : '#')); ?>" title="<?php echo $row['fansub_name']; ?>"><img src="<?php echo $static_url.'/images/icons/'.$row['fansub_id'].'.png'; ?>" alt="<?php echo $row['fansub_name']; ?>"> <?php echo $row['fansub_name']; ?></a> • <span class="news-date" title="<?php echo date("d/m/Y \\a \\l\\e\\s H:i:s", strtotime($row['date'])); ?>"><?php echo relative_time(strtotime($row['date'])); ?></span>
									</div>
									<div class="news-text">
										<!-- Begin article content -->
										<?php echo $row['contents']; ?>
										<!-- End article content -->
									</div>
<?php
		if ($row['image']!=NULL){
?>
							<img class="news-image-mobile" src="<?php echo $static_url.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt=""/>
<?php
		}
?>
<?php
		if ($row['url']!=NULL){
?>
									<div class="news-readmore">
										<a class="normal-button" href="<?php echo $row['url']; ?>"><?php echo "Vés a {$row['fansub_name']}"; ?> ➔</a>
									</div>
<?php
		}
?>
								</div>
							</div>
<?php
		if ($row['image']!=NULL){
?>
							<img class="news-image" src="<?php echo $static_url.'/images/news/'.$row['fansub_slug'].'/'.$row['image']; ?>" alt=""/>
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
<a id="nav-newer" href="<?php echo ((isset($_GET['query']) && $_GET['query']!='') ? '/cerca/'.urlencode(urlencode(urldecode($_GET['query']))) : '') . ($page==2 ? '' : '/pagina/'.($page-1)); ?>">← Notícies més noves</a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = query("SELECT n.*, f.name fansub_name, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.url fansub_url, f.archive_url FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE $blacklist_condition AND $search_condition ORDER BY n.date DESC LIMIT 20 OFFSET ".(($page)*20));

if (mysqli_num_rows($result)>0){
?>
<a id="nav-older" href="<?php echo ((isset($_GET['query']) && $_GET['query']!='') ? '/cerca/'.urlencode(urlencode(urldecode($_GET['query']))) : '') . '/pagina/'.($page+1); ?>">Notícies més antigues →</a>
<?php
}
?>
</div>
<?php
mysqli_free_result($result);
require_once("../common.fansubs.cat/footer.inc.php");
?>
