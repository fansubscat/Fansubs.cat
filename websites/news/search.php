<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Resultats de la cerca';
$header_current_page='search';

//Are we processing a page?
if (isset($_GET['page']) && $_GET['page']!=NULL && is_numeric($_GET['page']) && $_GET['page']>1){
	$page = $_GET['page'];
}
else{
	$page = 1;
}

//Or a fansub? (or both)
if (isset($_GET['query']) && $_GET['query']!=NULL){
	$query = $_GET['query'];
}
else{
	$query = '';
}

require_once('header.inc.php');
?>
					<div id="filter">
						<span id="filter-toggle"><a href="/" style="font-weight: bold;">Torna a la pàgina principal</a></span>
						<span id="filter-title">Resultats de la cerca: </span><span id="filter-data">Es mostren només les notícies que contenen "<?php echo $query; ?>"</span>
					</div>
<?php
$result = mysqli_query($db_connection, "SELECT n.*, f.name fansub_name, f.slug fansub_slug, f.url fansub_url, IF(f.name='Fansub independent' OR n.fansub_id IS NULL, 0, 1) is_visible, f.archive_url FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE n.contents LIKE '%" . mysqli_real_escape_string($db_connection, $query) . "%' OR n.title LIKE '%" . mysqli_real_escape_string($db_connection, $query) . "%' ORDER BY n.date DESC LIMIT 20 OFFSET ".(($page-1)*20)) or crash(mysqli_error($db_connection));

if (mysqli_num_rows($result)==0){
?>	
					<div class="article">
						<h2 class="article-title">No hi ha resultats!</h2>
						<p class="article-content">No hem trobat cap notícia que contingui "<?php echo $query; ?>"</p>
					</div>
<?php
}
else{
	while ($row = mysqli_fetch_assoc($result)){
?>
					<div class="article">
<?php
		if (file_exists('/srv/websites/static.fansubs.cat/images/logos/'.$row['fansub_id'].'.png')){
?>
						<a class="article-logo" href="<?php echo (!empty($row['fansub_url']) && empty($row['archive_url']) ? $row['fansub_url'] : (!empty($row['archive_url']) ? $row['archive_url'] : '#')); ?>" title="<?php echo $row['fansub_name']; ?>">
							<img src="https://static.fansubs.cat/images/logos/<?php echo $row['fansub_id']; ?>.png" alt="<?php echo $row['fansub_name']; ?>" />
						</a>
<?php
		}
?>
						<h2 class="article-title">
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
						</h2>
						<p class="article-info">
							<span class="date" title="<?php echo date("d/m/Y \\a \\l\\e\\s H:i:s", strtotime($row['date'])); ?>"><?php echo relative_time(strtotime($row['date'])); ?></span>
						</p>
						<div class="article-content">
<?php
		if ($row['image']!=NULL){
?>
							<img class="article-image" src="https://static.fansubs.cat/images/news/<?php echo $row['fansub_slug']; ?>/<?php echo $row['image']; ?>" alt=""/>
<?php
		}
?>
							<!-- Begin article content -->
							<?php echo $row['contents']; ?>

							<!-- End article content -->
						</div>
<?php
		if ($row['url']!=NULL){
?>
						<div class="article-readmore">
							<a href="<?php echo $row['url']; ?>"><?php echo ($row['is_visible']==1 ? "Vés a {$row['fansub_name']}" : "Vés al web de la notícia"); ?> ➔</a>
						</div>
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
<a id="nav-newer" href="<?php echo ($page==2 ? '/cerca/' . $query . '/' : '/cerca/' . $query . '/pagina/' . ($page-1)); ?>">← Notícies més noves</a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = mysqli_query($db_connection, "SELECT n.* FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE n.contents LIKE '%" . mysqli_real_escape_string($db_connection, $query) . "%' OR n.title LIKE '%" . mysqli_real_escape_string($db_connection, $query) . "%' ORDER BY n.date DESC LIMIT 20 OFFSET ".(($page)*20)) or crash(mysqli_error($db_connection));

if (mysqli_num_rows($result)>0){
?>
<a id="nav-older" href="<?php echo '/cerca/' . $query . '/pagina/' . ($page+1); ?>">Notícies més antigues →</a>
<?php
}
?>
</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
