<?php
require_once("db.inc.php");

$header_tab=(!empty($_GET['page']) ? $_GET['page'] : '');

require_once('header.inc.php');

$max_items=24;

$base_query="SELECT s.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, MIN(v.status) best_status, MAX(v.updated) last_updated FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = (empty($_COOKIE['show_cancelled']) ? " AND v.status<>4 AND v.status<>3" : "").(empty($_COOKIE['show_hentai']) ? " AND s.rating<>'XXX'" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

switch ($header_tab){
	case 'movies':
		$sections=array("Darreres actualitzacions", "Tot el catàleg de films");
		$queries=array(
			$base_query . " WHERE s.type='movie'$cookie_extra_conditions GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE s.type='movie'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$carousel=array(TRUE, FALSE);
		break;
	case 'series':
		$sections=array("Darreres actualitzacions", "Tot el catàleg de sèries");
		$queries=array(
			$base_query . " WHERE s.type='series'$cookie_extra_conditions GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE s.type='series'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$carousel=array(TRUE, FALSE);
		break;
	case 'search':
		$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
		$sections=array("Resultats de la cerca");
		$queries=array(
			$base_query . " WHERE (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%')$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$carousel=array(FALSE);
		break;
	default:
		$result = query("SELECT a.series_id
FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id FROM views vi LEFT JOIN link l ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE l.episode_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-1 month"))."' GROUP BY l.version_id, l.episode_id) a
GROUP BY a.series_id
ORDER BY MAX(a.views) DESC, a.series_id ASC");
		$in_clause='0';
		while ($row = mysqli_fetch_assoc($result)){
			$in_clause.=','.$row['series_id'];
		}
		mysqli_free_result($result);
		$sections=array("Darreres actualitzacions", "Més populars", "Més actuals", "Més ben valorades");
		$queries=array(
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE s.id IN ($in_clause)$cookie_extra_conditions GROUP BY s.id ORDER BY FIELD(s.id,$in_clause) LIMIT $max_items",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.air_date DESC LIMIT $max_items",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
		$carousel=array(TRUE, TRUE, TRUE, TRUE);
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
						<div class="section-content<?php echo $carousel[$i] ? ' carousel' : ' flex wrappable'; ?>">
<?php
		while ($row = mysqli_fetch_assoc($result)){
?>
							<div>
								<a class="thumbnail" href="/<?php echo $row['type']=='movie' ? "films" : "series"; ?>/<?php echo $row['slug']; ?>">
									<div class="status-<?php echo get_status($row['best_status']); ?>" title="<?php echo get_status_description($row['best_status']); ?>"></div>
									<img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" />
									<div class="title"><?php echo $row['name']; ?></div>
									<div class="fansub"><?php echo strpos($row['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $row['fansub_name']; ?></div>
								</a>
							</div>
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
