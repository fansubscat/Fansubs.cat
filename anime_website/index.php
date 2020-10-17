<?php
require_once("db.inc.php");
require_once("parsedown.inc.php");

function exists_more_than_one_version($series_id){
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

$header_tab=(!empty($_GET['page']) ? $_GET['page'] : '');
$header_page_title='';

switch ($header_tab) {
	case 'movies':
		$header_page_title='Films';
		$header_url='films/';
		break;
	case 'series':
		$header_page_title='Sèries';
		$header_url='series/';
		break;
	case 'search':
		$header_page_title='Resultats de la cerca';
		$header_url='cerca/'.(!empty($_GET['query']) ? urlencode(urlencode($_GET['query'])) : '');
		break;
	default:
		$header_page_title='';
		$header_url='';
		break;
}

$header_social = array(
	'title' => (!empty($header_page_title) ? $header_page_title.' - ' : '').'Fansubs.cat - Anime',
	'url' => 'https://anime.fansubs.cat'.$base_url.'/'.$header_url,
	'description' => "Aquí podràs veure en línia tot l'anime subtitulat pels fansubs en català!",
	'image' => 'https://anime.fansubs.cat'.$base_url.'/style/og_image.jpg'
);

require_once('header.inc.php');

if (!empty($site_message)){
?>
				<div data-nosnippet class="section">
					<div class="site-message"><?php echo $site_message; ?></div>
				</div>
<?php
}

if (is_robot()){
?>
				<div class="section">
					<div class="site-message">Fansubs.cat et permet veure en streaming més de 250 films i sèries d'anime subtitulades en català. Ara pots gaudir de tot l'anime de tots els fansubs en català en un únic lloc.</div>
				</div>
<?php
}

$max_items=24;

$cookie_viewed_links = get_cookie_viewed_links_ids();

$base_query="SELECT s.*, v.id version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.links_updated) last_updated, (SELECT COUNT(ss.id) FROM season ss WHERE ss.series_id=s.id) seasons, s.episodes episodes, (SELECT MAX(ls.created) FROM link ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND ls.id NOT IN (".(count($cookie_viewed_links)>0 ? implode(',',$cookie_viewed_links) : '0').")) last_link_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot()) ? " AND v.status<>5 AND v.status<>4" : "").(!empty($_COOKIE['show_missing']) ? "" : " AND v.episodes_missing=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND s.rating<>'XXX'" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

switch ($header_tab){
	case 'movies':
		$sections=array("Catàleg de films");
		$descriptions=array("Tria i remena entre un catàleg de %%% films! Prepara les crispetes!");
		$queries=array(
			$base_query . " WHERE s.type='movie'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('films-catalog');
		break;
	case 'series':
		$sections=array("Catàleg de sèries");
		$descriptions=array("Tria i remena entre un catàleg de %%% sèries! Compte, que enganxen!");
		$queries=array(
			$base_query . " WHERE s.type='series'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('series-catalog');
		break;
	case 'search':
		$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
		if (!empty($query)){
			query("INSERT INTO search_history (query,day) VALUES ('".escape($_GET['query'])."','".date('Y-m-d')."')");
		}
		$sections=array("Resultats de la cerca");
		$descriptions=array("La cerca de \"$query\" ha obtingut %%% resultats.");
		$query = str_replace(" ", "%", $query);
		$queries=array(
			$base_query . " WHERE (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.keywords LIKE '%$query%') GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('search-results');
		break;
	default:
		$result = query("SELECT a.series_id
FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id FROM views vi LEFT JOIN link l ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE l.episode_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' GROUP BY l.version_id, l.episode_id) a
GROUP BY a.series_id
ORDER BY MAX(a.views) DESC, a.series_id ASC");
		$in_clause='0';
		while ($row = mysqli_fetch_assoc($result)){
			$in_clause.=','.$row['series_id'];
		}
		mysqli_free_result($result);
		$sections=array("<span class=\"iconsm fa fa-fw fa-star\"></span> Obres destacades", "<span class=\"iconsm fa fa-fw fa-clock\"></span> Darreres actualitzacions", "<span class=\"iconsm fa fa-fw fa-dice\"></span> A l'atzar", "<span class=\"iconsm fa fa-fw fa-fire\"></span> Més populars", "<span class=\"iconsm fa fa-fw fa-stopwatch\"></span> Més actuals", "<span class=\"iconsm fa fa-fw fa-heart\"></span> Més ben valorades");
		$descriptions=array("Aquí tens la tria d'obres de qualitat d'aquesta quinzena! T'animes a mirar-ne alguna?", "Aquestes són les darreres novetats d'anime subtitulades en català pels diferents fansubs.", "T'agrada provar sort? Aquí tens un seguit d'obres triades a l'atzar. Si no te'n convenç cap, actualitza la pàgina i torna-hi!", "Aquestes són les obres que més han vist els nostres usuaris durant la darrera quinzena.", "T'agrada l'anime d'actualitat? Aquestes són les obres més noves que tenim subtitulades.", "Les obres més ben puntuades pels usuaris de MyAnimeList amb versió subtitulada en català.");
		$queries=array(
			$base_query . " WHERE v.id IN (SELECT version_id FROM recommendation UNION SELECT id FROM version WHERE is_always_featured=1)$cookie_extra_conditions GROUP BY s.id ORDER BY RAND()",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY RAND() LIMIT $max_items",
			$base_query . " WHERE s.id IN ($in_clause)$cookie_extra_conditions GROUP BY s.id ORDER BY FIELD(s.id,$in_clause) LIMIT $max_items",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.air_date DESC LIMIT $max_items",
			$base_query . " WHERE 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
		$specific_version=array(TRUE, TRUE, FALSE, FALSE, FALSE, FALSE);
		$type=array('recommendations', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel');
		$tracking_classes=array('featured', 'latest', 'random', 'popular', 'newest', 'toprated');
		break;
}

for ($i=0;$i<count($sections);$i++){
	$result = query($queries[$i]);
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<h3 class="section-subtitle"><?php echo str_replace("%%%", mysqli_num_rows($result), $descriptions[$i]); ?></h3>
<?php
	if (mysqli_num_rows($result)==0){
		if ($sections[$i]=='Resultats de la cerca'){
?>
					<div class="section-content fake-carousel"><div><span class="fa fa-search empty-icon"></span><br><br>No s'ha trobat cap element. Prova una altra cerca o explora el catàleg a les pestanyes superiors.</div></div>
<?php
		} else {
?>
					<div class="section-content fake-carousel"><div><span class="fa fa-ban empty-icon"></span><br><br>No s'ha trobat cap element. Prova de reduir el filtre a les opcions de la part superior dreta.</div></div>
<?php
		}
	} else {
		if ($type[$i]=='static') {
			$genres = array();
			while ($row = mysqli_fetch_assoc($result)) {
				if (!empty($row['genres'])) {
					$dbgenres = explode(',',$row['genres']);
					foreach ($dbgenres as $dbgenre) {
						if (!empty($genres[$dbgenre])) {
							$genres[$dbgenre]=$genres[$dbgenre]+1;
						} else {
							$genres[$dbgenre]=1;
						}
					}
				}
			}
			mysqli_data_seek($result, 0);

?>
					<div class="section-content genres-carousel">
						<div>
							<div class="select-genre select-genre-selected" data-genre-id="-1">
								<a>Mostra-ho tot</a>
							</div>
						</div>
<?php
			arsort($genres);
			$resultg = query("SELECT g.id, g.name FROM genre g WHERE g.id IN (0".implode(',',array_keys($genres)).") ORDER BY FIELD(g.id,0".implode(',',array_keys($genres)).")");
			while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
						<div>
							<div class="select-genre" data-genre-id="<?php echo $rowg['id']; ?>">
								<a><?php echo $rowg['name'].' ('.$genres[$rowg['id']].')'; ?></a>
							</div>
						</div>
<?php
			}
			mysqli_free_result($resultg);
?>
					</div>
<?php
		}
?>
					<div class="section-content<?php echo $type[$i]=='carousel' ? ' carousel' : ($type[$i]=='recommendations' ? ' recommendations' : ' flex wrappable catalog'); ?>">
<?php
		while ($row = mysqli_fetch_assoc($result)){
			if (!empty($row['genres']) && $type[$i]=='static') {
				$genres = ' genre-'.implode(' genre-', explode(',',$row['genres']));
			} else {
				$genres = "";
			}
?>
						<div class="status-<?php echo get_status($row['best_status']); ?><?php echo $genres; ?>">
<?php
			if ($type[$i]=='recommendations') {
?>
							<a class="recommendation trackable-<?php echo $tracking_classes[$i]; ?>" data-series-id="<?php echo $row['slug']; ?>" href="<?php echo $base_url; ?>/<?php echo $row['type']=='movie' ? "films" : "series"; ?>/<?php echo $row['slug']; ?><?php echo ($specific_version[$i] && exists_more_than_one_version($row['id'])) ? "?v=".$row['version_id'] : ""?>">
								<div class="status" title="<?php echo get_status_description($row['best_status']); ?>"><?php echo get_status_description_short($row['best_status']); ?></div>
<?php
				if (!empty($row['last_link_created']) && $row['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
									<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>
<?php
				}
?>
<?php
				if ($row['type']=='movie' && $row['episodes']>1) {
?>
									<div class="seasons"><?php echo $row['episodes']; ?> films</div>
<?php
				} else if ($row['type']=='movie') {
?>
									<div class="seasons">Film</div>
<?php
				} else if ($row['seasons']>1 && $row['show_seasons']==1) {
?>
									<div class="seasons">Sèrie de <?php echo $row['seasons']; ?> temporades, <?php echo $row['episodes']==-1 ? 'en emissió' : $row['episodes'].' capítols'; ?></div>
<?php
				} else {
?>
									<div class="seasons">Sèrie <?php echo $row['episodes']==-1 ? 'en emissió' : 'de '.$row['episodes'].' capítols'; ?></div>
<?php
				}
?>
								<img src="/images/versions/<?php echo $row['version_id']; ?>.jpg" alt="<?php echo $row['name']; ?>" />
								<div class="watchbutton">
									<span class="fa fa-fw fa-play"></span> Mira-la ara
								</div>
								<div class="infoholder">
									<div class="title">
										<?php echo $row['name']; ?>
									</div>
									<div class="synopsis">
<?php
				$Parsedown = new Parsedown();
				$synopsis = $Parsedown->setBreaksEnabled(false)->line($row['synopsis']);
?>
										<?php echo $synopsis; ?>
									</div>
								</div>
								<div class="fansub"><?php echo strpos($row['fansub_name'],"|")!==FALSE ? 'Subtítols de diversos fansubs' : 'Subtítols '.get_fansub_preposition_name($row['fansub_name']); ?></div>
							</a>
<?php			
			} else {
?>
							<a class="thumbnail trackable-<?php echo $tracking_classes[$i]; ?>" data-series-id="<?php echo $row['slug']; ?>" href="<?php echo $base_url; ?>/<?php echo $row['type']=='movie' ? "films" : "series"; ?>/<?php echo $row['slug']; ?><?php echo ($specific_version[$i] && exists_more_than_one_version($row['id'])) ? "?v=".$row['version_id'] : ""?>">
								<div class="status-indicator" title="<?php echo get_status_description($row['best_status']); ?>"></div>
								<img src="/images/series/<?php echo $row['id']; ?>.jpg" alt="<?php echo $row['name']; ?>" />
								<div class="infoholder">
<?php
				if (!empty($row['last_link_created']) && $row['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
									<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat</div>
<?php
				}
?>
<?php
				if ($row['type']=='movie' && $row['episodes']>1) {
?>
									<div class="seasons"><?php echo $row['episodes']; ?> films</div>
<?php
				} else if ($row['seasons']>1 && $row['show_seasons']==1) {
?>
									<div class="seasons"><?php echo $row['seasons']; ?> temporades</div>
<?php
				}
?>
									<div class="title">
										<div class="ellipsized-title"><?php echo $row['name']; ?></div>
									</div>
								</div>
								<div class="fansub"><?php echo strpos($row['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $row['fansub_name']; ?></div>
							</a>
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
	mysqli_free_result($result);
?>
				</div>
<?php
}

require_once('footer.inc.php');
?>
