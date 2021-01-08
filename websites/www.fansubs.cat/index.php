<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat: Les notícies dels fansubs en català';
$header_current_page='main';

//Are we processing a page?
if (isset($_GET['page']) && $_GET['page']!=NULL && is_numeric($_GET['page']) && $_GET['page']>1){
	$page = $_GET['page'];
}
else{
	$page = 1;
}

//Or a fansub? (or both)
if (isset($_GET['fansub_id']) && $_GET['fansub_id']!=NULL){
	$fansub_id = str_replace('/','',$_GET['fansub_id']);
}
else{
	$fansub_id = NULL;
}

//Check if the fansub exists
$result = mysqli_query($db_connection, "SELECT name FROM fansubs WHERE id='".mysqli_real_escape_string($db_connection, $fansub_id)."'") or crash(mysqli_error($db_connection));

if ($row = mysqli_fetch_assoc($result)){
	$fansub_name = $row['name'];
	$header_page_title='Fansubs.cat - '.$fansub_name;
}
else{
	//Just show all news
	$fansub_id = NULL;
}

mysqli_free_result($result);

require_once('header.inc.php');

//Begin ugly logic to handle user's cookie-selected fansubs
$result = mysqli_query($db_connection, "SELECT id,favicon_image,name FROM fansubs ORDER BY is_own ASC, is_visible DESC, name ASC") or crash(mysqli_error($db_connection));

$all_fansubs = array();
$selected_fansubs = array();

while ($row = mysqli_fetch_assoc($result)){
	$all_fansubs[]=$row['id'];
}
mysqli_data_seek($result, 0);

if (isset($_COOKIE['favorite_fansubs']) && $_COOKIE['favorite_fansubs']!=NULL){
	$selected_fansubs=array_intersect($all_fansubs,explode(',',$_COOKIE['favorite_fansubs']));
}
if (count($selected_fansubs)==0){
	$selected_fansubs=$all_fansubs;
}

$selected_fansubs_names = "";
while ($row = mysqli_fetch_assoc($result)){
	if (in_array($row['id'],$selected_fansubs)){
		if ($selected_fansubs_names!=''){
			$selected_fansubs_names.=', ';
		}
		$selected_fansubs_names.=$row['name'];
	}
}
mysqli_data_seek($result, 0);
?>
					<div id="filter">
						<span id="filter-toggle"><?php echo ((isset($fansub_id) && $fansub_id!=NULL) ? '<a href="/" style="font-weight: bold;">Torna a la pàgina principal</a>' : '↓ Canvia els teus preferits ↓'); ?></span>
						<span id="filter-title">Filtre: </span><span id="filter-data">
<?php
if (isset($fansub_id) && $fansub_id!=NULL){
	echo 'Es mostren només les notícies de: '.$fansub_name;
}
else if (count(array_diff($all_fansubs,array_intersect($all_fansubs, $selected_fansubs)))!=0){ //All fansubs and selected arrays have the same elements
	echo 'Es mostren només les notícies dels teus fansubs preferits.';
}
else{
	echo 'Es mostren les notícies de tots els fansubs.'; 
}
?></span>
						<div id="filter-details" style="display: none;">
						<div id="filter-explanation">Tria de quins fansubs vols veure notícies, i prem "Aplica els canvis" per a desar les teves preferències.</div>
<?php

while ($row = mysqli_fetch_assoc($result)){
?>
						<div class="filter-fansub<?php echo (in_array($row['id'], $selected_fansubs) ? ' filter-selected' : ''); ?>" id="filter-fansub-<?php echo $row['id']; ?>"><?php echo ($row['favicon_image']!=NULL ? '<img alt="" src="/images/fansubs/favicons/'.$row['favicon_image'].'" />' : ''); ?><span><?php echo $row['name']; ?></span></div>
<?php
}
mysqli_free_result($result);
?>
							<div id="filter-footer">
								<span class="filter-select-all">Selecciona'ls tots</span> / <span class="filter-select-none">Deselecciona'ls tots</span>
							</div>
						</div>
					</div>
<?php
$query_fansubs = implode("','",$selected_fansubs);

$result = mysqli_query($db_connection, "SELECT n.*,f.name fansub_name,f.url fansub_url,f.logo_image fansub_logo_image, f.is_visible, f.archive_url FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE fansub_id IN ('".($fansub_id!=NULL ? mysqli_real_escape_string($db_connection, $fansub_id) : $query_fansubs)."') ORDER BY date DESC LIMIT 20 OFFSET ".(($page-1)*20)) or crash(mysqli_error($db_connection));

if (mysqli_num_rows($result)==0){
?>	
					<div class="article">
						<h2 class="article-title">No hem trobat cap notícia!</h2>
						<p class="article-content">I que no hi hagi notícies són males notícies...</p>
					</div>
<?php
}
else{
	while ($row = mysqli_fetch_assoc($result)){
?>
					<div class="article">
<?php
		if ($row['fansub_logo_image']!=NULL){
?>
						<a class="article-logo" href="<?php echo ($row['fansub_url']!=NULL ? $row['fansub_url'] : $row['archive_url']); ?>" title="<?php echo $row['fansub_name']; ?>">
							<img src="/images/fansubs/logos/<?php echo $row['fansub_logo_image']; ?>" alt="<?php echo $row['fansub_name']; ?>" />
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
							<img class="article-image" src="/images/news/<?php echo $row['fansub_id']; ?>/<?php echo $row['image']; ?>" alt=""/>
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
<a id="nav-newer" href="<?php echo ($page==2 ? ($fansub_id!=NULL ? '/fansub/'.$fansub_id : '').'/' : ($fansub_id!=NULL ? '/fansub/'.$fansub_id : '').'/pagina/'.($page-1)); ?>">← Notícies més noves</a>
<?php
}
mysqli_free_result($result);

//Do the same query but for the next page, to know if it exists
$result = mysqli_query($db_connection, "SELECT n.*,f.name fansub_name,f.url fansub_url,f.logo_image fansub_logo_image FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE fansub_id IN ('".($fansub_id!=NULL ? mysqli_real_escape_string($db_connection, $fansub_id) : $query_fansubs)."') ORDER BY date DESC LIMIT 20 OFFSET ".(($page)*20)) or crash(mysqli_error($db_connection));

if (mysqli_num_rows($result)>0){
?>
<a id="nav-older" href="<?php echo ($fansub_id!=NULL ? '/fansub/'.$fansub_id : '').'/pagina/'.($page+1); ?>">Notícies més antigues →</a>
<?php
}
?>
</div>
<?php
mysqli_free_result($result);
require_once('footer.inc.php');
?>
