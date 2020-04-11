<?php
require_once("db.inc.php");

$header_tab=(!empty($_GET['page']) ? $_GET['page'] : '');

if (!empty($_GET['query'])){
	$header_hide_options=TRUE;
}

require_once('header.inc.php');

$base_query="SELECT s.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, MIN(v.status) status, MAX(v.updated) last_updated FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";

switch ($header_tab){
	case 'movies':
		$sections=array("Catàleg de films");
		$queries=array(
			$base_query . " WHERE s.type='movie' GROUP BY s.id ORDER BY s.name ASC");
		break;
	case 'series':
		$sections=array("Catàleg de sèries");
		$queries=array(
			$base_query . " WHERE s.type='series' GROUP BY s.id ORDER BY s.name ASC");
		break;
	case 'search':
		$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
		$sections=array("Resultats de la cerca");
		$queries=array(
			$base_query . " WHERE s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' GROUP BY s.id ORDER BY s.name ASC");
		break;
	default:
		$result = query("SELECT a.series_id
FROM (SELECT SUM(vi.counter) counter, l.version_id, s.id series_id FROM views vi LEFT JOIN link l ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE l.episode_id IS NOT NULL AND  vi.day>='".date("Y-m-d",strtotime("-1 month"))."' GROUP BY l.version_id, l.episode_id) a
GROUP BY a.series_id
ORDER BY MAX(a.counter) DESC
LIMIT 10");
		$in_clause='0';
		while ($row = mysqli_fetch_assoc($result)){
			$in_clause.=','.$row['series_id'];
		}
		mysqli_free_result($result);
		$sections=array("Darreres actualitzacions", "Més populars", "Més actuals");
		$queries=array(
			$base_query . " GROUP BY s.id ORDER BY last_updated DESC LIMIT 10",
			$base_query . " WHERE s.id IN ($in_clause) GROUP BY s.id ORDER BY FIELD(s.id,$in_clause)",
			$base_query . " GROUP BY s.id ORDER BY s.air_date DESC LIMIT 10");
		break;
}

for ($i=0;$i<count($sections);$i++){
	$result = query($queries[$i]);
?>	
					<div class="section">
						<h2 class="section-title"><?php echo $sections[$i]; ?></h2>
<?php
	if (mysqli_num_rows($result)==0){
?>
						<div class="section-content">No s'ha trobat cap element.</div>
<?php
	} else {
?>
						<div class="section-content flex wrappable">
<?php
		while ($row = mysqli_fetch_assoc($result)){
?>
							<a class="thumbnail<?php echo (empty($_COOKIE['show_cancelled']) && $row['status']==4 && empty($_GET['query'])) ? ' hidden' : ''; ?>" href="/<?php echo $row['type']=='movie' ? "films" : "series"; ?>/<?php echo $row['slug']; ?>">
								<div class="status-<?php echo get_status($row['status']); ?>" title="<?php echo get_status_description($row['status']); ?>"></div>
								<img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" />
								<div class="title"><?php echo $row['name']; ?></div>
								<div class="fansub"><?php echo strpos($row['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $row['fansub_name']; ?></div>
							</a>
<?php
		}
?>
						</div>
<?php
	}
	mysqli_free_result($result);
?>
					</div>
<?php
}

require_once('footer.inc.php');
?>
