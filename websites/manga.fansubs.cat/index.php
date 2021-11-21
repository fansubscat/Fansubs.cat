<?php
require_once("db.inc.php");
require_once("parsedown.inc.php");

$header_tab=(!empty($_GET['page']) ? $_GET['page'] : '');
$header_page_title='';

switch ($header_tab) {
	case 'oneshots':
		$header_page_title='One-shots';
		$header_url='one-shots/';
		break;
	case 'serialized':
		$header_page_title='Serialitzats';
		$header_url='serialitzats/';
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
	'title' => (!empty($header_page_title) ? $header_page_title.' | ' : '').'Fansubs.cat - Manga en català',
	'url' => 'https://manga.fansubs.cat'.$base_url.'/'.$header_url,
	'description' => "Aquí podràs llegir en línia tot el manga editat pels fansubs en català!",
	'image' => 'https://manga.fansubs.cat'.$base_url.'/style/og_image.jpg'
);

require_once('header.inc.php');

if (!empty($site_message) || !empty($is_fools_day)){
?>
				<div data-nosnippet class="section">
					<div class="site-message"><?php echo !empty($is_fools_day) ? 'Estem millorant el disseny de la pàgina. De moment hi hem afegit Comic Sans, que li donarà un toc més modern. <a href="/images/innocents.png" target="_blank" style="color: black">Més informació</a>' : $site_message; ?></div>
				</div>
<?php
}

if (is_robot()){
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/50)*50 cnt FROM manga m WHERE EXISTS (SELECT id FROM manga_version v WHERE v.manga_id=m.id AND v.hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
?>
				<div class="section">
					<div class="site-message">Fansubs.cat et permet llegir en línia més de <?php echo $totalnumber; ?> mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.</div>
				</div>
<?php
}

$max_items=24;

$cookie_viewed_files = get_cookie_viewed_files_ids();

$base_query="SELECT s.*, (SELECT nv.id FROM manga_version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.manga_id=s.id AND nv.hidden=0 LIMIT 1) manga_version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(ss.id) FROM volume ss WHERE ss.manga_id=s.id) volumes, s.chapters, (SELECT MAX(ls.created) FROM file ls LEFT JOIN manga_version vs ON ls.manga_version_id=vs.id WHERE vs.manga_id=s.id AND vs.hidden=0 AND ls.id NOT IN (".(count($cookie_viewed_files)>0 ? implode(',',$cookie_viewed_files) : '0').")) last_link_created FROM manga s LEFT JOIN manga_version v ON s.id=v.manga_id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_manga_genre sg ON s.id=sg.manga_id LEFT JOIN genre g ON sg.genre_id = g.id";
$query_portion_limit_to_non_hidden = "(SELECT COUNT(*) FROM manga_version v WHERE v.manga_id=s.id AND v.hidden=0)>0";

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot()) ? " AND v.status<>5 AND v.status<>4" : "").(!empty($_COOKIE['show_missing']) ? "" : " AND v.chapters_missing=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND (s.rating<>'XXX' OR s.rating IS NULL)" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM manga_version v2 LEFT JOIN rel_manga_version_fansub vf2 ON v2.id=vf2.manga_version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.manga_version_id FROM rel_manga_version_fansub vf3 WHERE vf3.manga_version_id=vf2.manga_version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

switch ($header_tab){
	case 'oneshots':
		$sections=array("Catàleg de one-shots");
		$descriptions=array("Tria i remena entre un catàleg de %%% mangues de curta durada!");
		$queries=array(
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND s.type='oneshot'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('oneshots-catalog');
		break;
	case 'serialized':
		$sections=array("Catàleg de mangues serialitzats");
		$descriptions=array("Tria i remena entre un catàleg de %%% mangues serialitzats!");
		$queries=array(
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND s.type='serialized'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('serialized-catalog');
		break;
	case 'search':
		$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
		if (!empty($query)){
			query("INSERT INTO manga_search_history (query,day) VALUES ('".escape($_GET['query'])."','".date('Y-m-d')."')");
		}
		$sections=array("Resultats de la cerca");
		$descriptions=array("La cerca de \"$query\" ha obtingut %%% resultats a la nostra base de dades de manga.");
		$spaced_query = $query;
		$query = str_replace(" ", "%", $query);
		$queries=array(
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT mg.manga_id FROM rel_manga_genre mg LEFT JOIN genre g ON mg.genre_id=g.id WHERE g.name='$spaced_query')) GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		$tracking_classes=array('search-results');
		break;
	default:
		$result = query("SELECT a.manga_id
FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.id manga_id FROM manga_views vi LEFT JOIN file fi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id WHERE (SELECT COUNT(*) FROM manga_version mv WHERE mv.manga_id=m.id AND mv.hidden=0)>0 AND fi.chapter_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' GROUP BY fi.manga_version_id, fi.chapter_id) a
GROUP BY a.manga_id
ORDER BY MAX(a.views) DESC, a.manga_id ASC");
		$in_clause='0';
		while ($row = mysqli_fetch_assoc($result)){
			$in_clause.=','.$row['manga_id'];
		}
		mysqli_free_result($result);
		$sections=array("<span class=\"iconsm fa fa-fw fa-gift\"></span> Calendari d'advent", "<span class=\"iconsm fa fa-fw fa-star\"></span> Mangues destacats", "<span class=\"iconsm fa fa-fw fa-clock\"></span> Darreres actualitzacions", "<span class=\"iconsm fa fa-fw fa-dice\"></span> A l'atzar", "<span class=\"iconsm fa fa-fw fa-fire\"></span> Més populars", "<span class=\"iconsm fa fa-fw fa-stopwatch\"></span> Més actuals", "<span class=\"iconsm fa fa-fw fa-heart\"></span> Més ben valorats");
		$descriptions=array("Enguany, tots els fansubs en català s'uneixen per a dur-vos cada dia una novetat! Bones festes!", "Aquí tens la tria de mangues recomanats d'aquesta setmana! T'animes a llegir-ne algun?", "Aquestes són les darreres novetats de manga editat en català pels diferents fansubs.", "T'agrada provar sort? Aquí tens un seguit de mangues triats a l'atzar. Si no te'n convenç cap, actualitza la pàgina i torna-hi!", "Aquests són els mangues que més han vist els nostres usuaris durant la darrera quinzena.", "T'agrada el manga d'actualitat? Aquests són els mangues més nous que tenim editats.", "Els mangues més ben puntuats pels usuaris de MyAnimeList amb versió editada en català.");
		$recommendations_subquery = "SELECT manga_version_id FROM manga_recommendation";
		if ($is_fools_day) {
			$sections[1]="<span class=\"iconsm fa fa-fw fa-star\"></span> Obres mestres";
			$descriptions[1]="Que no t'enganyin, aquests són els millors mangues de la història. Si encara no els has llegit, què esperes?";
			$fools_day_result = query("SELECT vr.id FROM manga_version vr LEFT JOIN manga sr ON vr.manga_id=sr.id WHERE (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status IN (1,3) AND sr.score IS NOT NULL AND vr.chapters_missing=0 ORDER BY sr.score ASC LIMIT 10");
			$fools_day_items = array();
			while ($rowfd = mysqli_fetch_assoc($fools_day_result)) {
				$fools_day_items[] = $rowfd['id'];
			}
			$recommendations_subquery = implode(',',$fools_day_items);
		} else if (date('m-d')=='04-23') { // Sant Jordi
			$sections[1]="<span class=\"iconsm fa fa-fw fa-star\"></span> Especial Sant Jordi";
			$descriptions[1]="Mangues ben valorats amb components romàntics. T'animes a llegir-ne algun?";
			$sant_jordi_result = query("SELECT DISTINCT vr.id FROM manga_version vr LEFT JOIN manga sr ON vr.manga_id=sr.id WHERE (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status=1 AND vr.is_featurable=1 AND sr.score IS NOT NULL AND vr.chapters_missing=0 AND sr.id IN (SELECT rsg.manga_id FROM rel_manga_genre rsg WHERE rsg.genre_id IN (7,16,23)) ORDER BY sr.score DESC LIMIT 10");
			$sant_jordi_items = array();
			while ($rowsj = mysqli_fetch_assoc($sant_jordi_result)) {
				$sant_jordi_items[] = $rowsj['id'];
			}
			$recommendations_subquery = implode(',',$sant_jordi_items);
		}
		$queries=array(
			NULL,
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND v.id IN ($recommendations_subquery)$cookie_extra_conditions GROUP BY s.id ORDER BY RAND()",
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY RAND() LIMIT $max_items",
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND s.id IN ($in_clause)$cookie_extra_conditions GROUP BY s.id ORDER BY FIELD(s.id,$in_clause) LIMIT $max_items",
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.publish_date DESC LIMIT $max_items",
			$base_query . " WHERE $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
		$specific_version=array(FALSE, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE);
		$type=array('advent','recommendations', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel');
		$tracking_classes=array('advent', 'featured', 'latest', 'random', 'popular', 'newest', 'toprated');
		break;
}

for ($i=0;$i<count($sections);$i++){
	if ($type[$i]=='advent') {
		if (strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 23:59:59')<=0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<h3 class="section-subtitle"><?php echo $descriptions[$i]; ?></h3>
					<div class="section-content fake-carousel">
						<a class="advent trackable-advent" href="https://www.fansubs.cat/nadal/" target="_blank"><img src="/images/advent.jpg" alt="Calendari d'advent dels fansubs en català" /></a>
					</div>
				</div>
<?php
		}
		continue;
	}
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
							<a class="recommendation trackable-<?php echo $tracking_classes[$i]; ?>" data-manga-id="<?php echo $row['slug']; ?>" href="<?php echo $base_url; ?>/<?php echo $row['type']=='oneshot' ? "one-shots" : "serialitzats"; ?>/<?php echo $row['slug']; ?><?php echo ($specific_version[$i] && exists_more_than_one_version($row['id'])) ? "?v=".$row['manga_version_id'] : ""?>">
								<img src="/images/featured/<?php echo $row['id']; ?>.jpg" alt="<?php echo $row['name']; ?>" />
								<div class="status" title="<?php echo get_status_description($row['best_status']); ?>"><?php echo get_status_description_short($row['best_status']); ?></div>
<?php
				if (!empty($row['last_link_created']) && $row['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
									<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>
<?php
				}
?>
<?php
				if ($row['type']=='oneshot') {
?>
									<div class="seasons">One-shot</div>
<?php
				} else if ($row['volumes']>1) {
?>
									<div class="seasons">Manga <?php echo $row['volumes']==1 ? "d'1 volum" : ($row['volumes']==11 ? "d'11 volums" : 'de '.$row['volumes'].' volums'); ?>, <?php echo $row['chapters']==-1 ? 'en edició' : $row['chapters'].' capítols'; ?></div>
<?php
				} else {
?>
									<div class="seasons">Manga <?php echo $row['chapters']==-1 ? 'en edició' : ($row['volumes']==1 ? "d'1 volum" : ($row['volumes']==11 ? "d'11 volums" : 'de '.$row['volumes'].' volums')); ?></div>
<?php
				}
?>
								<div class="watchbutton">
									<span class="fa fa-fw fa-book-open"></span> Llegeix-lo ara
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
								<div class="fansub"><?php echo strpos($row['fansub_name'],"|")!==FALSE ? 'Editat per diversos fansubs' : 'Editat per '.$row['fansub_name']; ?></div>
							</a>
<?php			
			} else {
				print_carousel_item_manga($row, $tracking_classes[$i], $specific_version[$i]);
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
	//Search case: add anime
	if ($header_tab=='search') {
		$result = query("SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.links_updated=MAX(v.links_updated) AND v.series_id=s.id AND nv.hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT f.type ORDER BY f.type SEPARATOR '|') fansub_type, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.links_updated) last_updated, (SELECT COUNT(ss.id) FROM season ss WHERE ss.series_id=s.id AND ss.episodes>0) seasons, s.episodes episodes, (SELECT MAX(ls.created) FROM link ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.hidden=0) last_link_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.hidden=0)>0 AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name='$spaced_query')) GROUP BY s.id ORDER BY s.name ASC");
		if (mysqli_num_rows($result)>0){
?>
				<div class="section">
					<h2 class="section-title-main">Altres resultats</h2>
					<h3 class="section-subtitle"><?php echo mysqli_num_rows($result)==1 ? str_replace("%%%", mysqli_num_rows($result), "Hem trobat %%% anime que coincideix amb la cerca.") : str_replace("%%%", mysqli_num_rows($result), "Hem trobat %%% animes que coincideixen amb la cerca."); ?></h3>
					<div class="section-content flex wrappable catalog">
<?php
			while ($row = mysqli_fetch_assoc($result)){
?>
						<div class="status-<?php echo get_status($row['best_status']); ?>">
<?php
				print_carousel_item_anime($row, 'trackable-search-results-anime', FALSE, FALSE);
?>
						</div>
<?php
			}
		}
?>
					</div>
				</div>
<?php
		mysqli_free_result($result);
	}
}

require_once('footer.inc.php');
?>
