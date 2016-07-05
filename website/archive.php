<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Arxiu de notícies';
$header_current_page='archive';

require_once('header.inc.php');
?>
				<div class="page-title">
					<h2>Arxiu de notícies</h2>
				</div>
<?php
$result = mysqli_query($db_connection, "SELECT n.*,f.name fansub_name,f.url fansub_url,f.logo_image fansub_logo_image FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id ORDER BY date DESC") or crash(mysqli_error($db_connection));

if (mysqli_num_rows($result)==0){
?>	
				<div class="article">
					<h2 class="article-title">No hem trobat cap notícia!</h2>
					<p class="article-content">I que no hi hagi notícies són males notícies...</p>
				</div>
<?php
}
else{
	$today = array();
	$week = array();
	$month = array();
	$older = array();
	$now = time();

	while ($row = mysqli_fetch_assoc($result)){
		$age = ($now - date('U', strtotime($row['date']))) / (60*60*24);
		if ($age < 1) {
			$today[] = $row;
		} elseif ($age < 7) {
			$week[] = $row;
		} elseif ($age < 30) {
			$month[] = $row;
		} else {
			if (!isset($older[date('Y', strtotime($row['date']))])){
				$older[date('Y', strtotime($row['date']))] = array();
			}
			$older[date('Y', strtotime($row['date']))][] = $row;
		}
	}

	if (count($today)>0) {
?>
				<div class="article">
					<h2>Avui</h2>
					<ul>
<?php
		foreach ($today as $item) {
?>
						<li>
							<?php echo ($item['fansub_url']!=NULL ? '<a class="source" href="'.$item['fansub_url'].'">'.$item['fansub_name'].'</a>' : $item['fansub_name']); ?>:
							<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
						</li>
<?php
		}
?>
					</ul>
				</div>
<?php
	}

	if (count($week)>0) {
?>
				<div class="article">
					<h2>Fa menys d'una setmana</h2>
					<ul>
<?php
		foreach ($week as $item) {
?>
						<li>
							<?php echo ($item['fansub_url']!=NULL ? '<a class="source" href="'.$item['fansub_url'].'">'.$item['fansub_name'].'</a>' : $item['fansub_name']); ?>:
							<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
						</li>
<?php
		}
?>
					</ul>
				</div>
<?php
	}
?>

<?php
	if (count($month)>0) {
?>
				<div class="article">
					<h2>Fa menys d'un mes</h2>
					<ul>
<?php
		foreach ($month as $item) {
?>
						<li>
							<?php echo ($item['fansub_url']!=NULL ? '<a class="source" href="'.$item['fansub_url'].'">'.$item['fansub_name'].'</a>' : $item['fansub_name']); ?>:
							<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
						</li>
<?php
		}
?>
					</ul>
				</div>
<?php
	}

	if (count($older)>0) {
		foreach ($older as $year => $arritems){
?>
				<div class="article">
					<h2><?php echo $year; ?></h2>
					<ul>
<?php
			foreach ($arritems as $item) {
?>
						<li>
							<?php echo ($item['fansub_url']!=NULL ? '<a class="source" href="'.$item['fansub_url'].'">'.$item['fansub_name'].'</a>' : $item['fansub_name']); ?>:
							<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
						</li>
<?php
			}
?>
					</ul>
				</div>
<?php
		}
	}
}

mysqli_free_result($result);
require_once('footer.inc.php');
?>
