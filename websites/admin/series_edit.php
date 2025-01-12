<?php
require_once(__DIR__.'/libraries/preview_image_generator.php');
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Edició d’anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Edició de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Edició de contingut d’imatge real - Imatge real";
		$page="liveaction";
	break;
}

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$division_name_explanation='Les divisions són agrupacions de capítols. Podríem dir que són com les temporades, tot i que no necessàriament ho han de ser: també poden ser arcs, conjunts de capítols especials, OVAs, etc.\n\nCada anime ha de tenir una o més divisions.\n\nSi es tracta d’un únic film, ha de tenir una sola divisió que contindrà un sol capítol.\n\nSi es tracta d’un conjunt de films, normalment tindrà una divisió que unificarà tots els capítols, i addicionalment, una divisió sense capítols per a cada fitxa a MyAnimeList. Si, per exemple, hi ha una agrupació d’especials relacionats amb el film, poden incloure’s en una altra divisió.\n\nLes sèries de més d’una temporada han de tenir-les totes informades com a divisions, fins i tot si no està previst editar-les, però poden tenir divisions addicionals (per exemple, capítols especials, OVAs o films enllaçats).\n\nSi vols, pots donar d’alta divisions fictícies amb 0 capítols perquè s’utilitzi el seu identificador de MyAnimeList per a fer mitjana amb la resta de divisions (és útil quan les divisions no encaixen amb les fitxes a MyAnimeList).\n\nRevisa l’ajuda de cada camp per a més informació.';
		$division_help='Si la divisió equival a una temporada, normalment s’hi introdueix el títol de la temporada en la llengua original (no pas en català), sempre fent servir la versió en alfabet occidental (no s’admeten kanji ni hanja).\n\nTambé s’hi pot introduir un títol intern si es tracta d’una divisió per a agrupar OVAs, especials, films enllaçats o similars.\n\nS’utilitza per a identificar la divisió internament.\n\nA la fitxa de la versió se n’introdueix el nom localitzat.';
		$content_apos="l’anime";
		$content_one="un anime";
		$external_provider='MyAnimeList';
		break;
	case 'manga':
		$division_name_explanation='Les divisions són agrupacions de capítols. Podríem dir que són com els volums, tot i que no necessàriament ho han de ser: també poden ser conjunts de capítols especials, volums especials, etc.\n\nCada manga ha de tenir una o més divisions.\n\nSi es tracta d’un one-shot, ha de tenir una sola divisió que contindrà un sol capítol.\n\nSi es tracta d’un conjunt de one-shots, normalment tindrà una sola divisió (si és de volum únic) amb diversos capítols, llevat que, per exemple, hi hagi una agrupació d’especials que valgui la pena classificar a banda, que podria ser una segona divisió.\n\nEls mangues serialitzats de més d’un volum han de tenir-los tots informats com a divisions, fins i tot si no està previst editar-los, però poden tenir divisions addicionals (per exemple, capítols especials).\n\nSi vols, pots donar d’alta divisions fictícies amb 0 capítols perquè s’utilitzi el seu identificador de MyAnimeList per a fer mitjana amb la resta de divisions (és útil quan les divisions no encaixen amb les fitxes a MyAnimeList).\n\nRevisa l’ajuda de cada camp per a més informació.';
		$division_help='Si la divisió equival a un volum, normalment s’hi introdueix «Volum X».\n\nTambé s’hi pot introduir un títol intern si es tracta d’una divisió per a agrupar capítols especials.\n\nS’utilitza per a identificar la divisió internament.\n\nA la fitxa de la versió se n’introdueix el nom localitzat.';
		$content_apos="el manga";
		$content_one="un manga";
		$external_provider='MyAnimeList';
		break;
	case 'liveaction':
		$division_name_explanation='Les divisions són agrupacions de capítols. Podríem dir que són com les temporades, tot i que no necessàriament ho han de ser: també poden ser arcs, conjunts de capítols especials, OVAs, etc.\n\nCada contingut d’imatge real ha de tenir una o més divisions.\n\nSi es tracta d’un únic film, ha de tenir una sola divisió que contindrà un sol capítol.\n\nSi es tracta d’un conjunt de films, normalment tindrà una divisió que unificarà tots els capítols, i addicionalment, una divisió sense capítols per a cada fitxa a MyDramaList. Si, per exemple, hi ha una agrupació d’especials relacionats amb el film, poden incloure’s en una altra divisió.\n\nLes sèries de més d’una temporada han de tenir-les totes informades com a divisions, fins i tot si no està previst editar-les, però poden tenir divisions addicionals (per exemple, capítols especials, OVAs o films enllaçats).\n\nSi vols, pots donar d’alta divisions fictícies amb 0 capítols perquè s’utilitzi el seu identificador de MyDramaList per a fer mitjana amb la resta de divisions (és útil quan les divisions no encaixen amb les fitxes a MyDramaList).\n\nRevisa l’ajuda de cada camp per a més informació.';
		$division_help='NSi la divisió equival a una temporada, normalment s’hi introdueix el títol de la temporada en la llengua original (no pas en català), sempre fent servir la versió en alfabet occidental (no s’admeten kanji ni hanja).\n\nTambé s’hi pot introduir un títol intern si es tracta d’una divisió per a agrupar OVAs, especials, films enllaçats o similars.\n\nS’utilitza per a identificar la divisió internament.\n\nA la fitxa de la versió se n’introdueix el nom localitzat';
		$content_apos="el contingut d’imatge real";
		$content_one="un contingut d’imatge real";
		$external_provider='MyDramaList';
		break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		$external_ids = array('-1');
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash("Dades invàlides: manca name");
		}
		if (!empty($_POST['alternate_names'])) {
			$data['alternate_names']="'".escape($_POST['alternate_names'])."'";
		} else {
			$data['alternate_names']="NULL";
		}
		if (!empty($_POST['keywords'])) {
			$data['keywords']="'".escape($_POST['keywords'])."'";
		} else {
			$data['keywords']="NULL";
		}
		$data['type']=$type;
		if (!empty($_POST['subtype'])) {
			$data['subtype']=escape($_POST['subtype']);
		} else {
			crash("Dades invàlides: manca subtype");
		}
		if (!empty($_POST['publish_date'])) {
			$data['publish_date']="'".date('Y-m-d', strtotime($_POST['publish_date']))."'";
		} else {
			$data['publish_date']="NULL";
		}
		if (!empty($_POST['author'])) {
			$data['author']="'".escape($_POST['author'])."'";
		} else {
			$data['author']="NULL";
		}
		if (!empty($_POST['studio'])) {
			$data['studio']="'".escape($_POST['studio'])."'";
		} else {
			$data['studio']="NULL";
		}
		if (!empty($_POST['rating'])) {
			$data['rating']="'".escape($_POST['rating'])."'";
		} else {
			$data['rating']="NULL";
		}
		if (!empty($_POST['external_id'])) {
			$data['external_id']="'".escape($_POST['external_id'])."'";
			array_push($external_ids, escape($_POST['external_id']));
		} else {
			$data['external_id']="NULL";
		}
		if (!empty($_POST['score']) && is_numeric($_POST['score'])) {
			$data['score']=escape($_POST['score']);
		} else {
			$data['score']="NULL";
		}
		if (!empty($_POST['has_licensed_parts'])){
			$data['has_licensed_parts']=1;
		} else {
			$data['has_licensed_parts']=0;
		}
		if (!empty($_POST['comic_type'])) {
			$data['comic_type']="'".escape($_POST['comic_type'])."'";
		} else {
			$data['comic_type']="NULL";
		}
		if (!empty($_POST['comic_type'])) {
			$data['reader_type']="'".escape($_POST['reader_type'])."'";
		} else {
			$data['reader_type']="NULL";
		}
		if (!empty($_POST['default_version_id']) && is_numeric($_POST['default_version_id'])) {
			$data['default_version_id']=$_POST['default_version_id'];
		} else {
			$data['default_version_id']="NULL";
		}

		$genres=array();
		if (!empty($_POST['genres'])) {
			foreach ($_POST['genres'] as $genre) {
				if (is_numeric($genre)) {
					array_push($genres, escape($genre));
				}
				else{
					crash("Dades invàlides: genre no numèric");
				}
			}
		}

		$divisions=array();
		$i=1;
		$total_eps=0;
		while (!empty($_POST['form-division-list-id-'.$i])) {
			$division = array();
			if (is_numeric($_POST['form-division-list-id-'.$i])) {
				$division['id']=escape($_POST['form-division-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de divisió no numèric");
			}
			if ((!empty($_POST['form-division-list-number-'.$i]) || $_POST['form-division-list-number-'.$i]=='0') && is_numeric($_POST['form-division-list-number-'.$i])) {
				$division['number']=escape($_POST['form-division-list-number-'.$i]);
			} else {
				crash("Dades invàlides: número de divisió no numèric");
			}
			if (!empty($_POST['form-division-list-name-'.$i])) {
				$division['name']=escape($_POST['form-division-list-name-'.$i]);
			} else {
				crash("Dades invàlides: manca nom de divisió");
			}
			if ((!empty($_POST['form-division-list-number_of_episodes-'.$i]) && is_numeric($_POST['form-division-list-number_of_episodes-'.$i])) || $_POST['form-division-list-number_of_episodes-'.$i]==='0') {
				$division['number_of_episodes']=escape($_POST['form-division-list-number_of_episodes-'.$i]);
				$total_eps+=$_POST['form-division-list-number_of_episodes-'.$i];
			} else {
				crash("Dades invàlides: número de capítols buit o no numèric");
			}
			if (!empty($_POST['form-division-list-external_id-'.$i])) {
				$division['external_id']="'".escape($_POST['form-division-list-external_id-'.$i])."'";
				array_push($external_ids, escape($_POST['form-division-list-external_id-'.$i]));
			} else {
				$division['external_id']="NULL";
			}
			if (!empty($_POST['form-division-list-is_real-'.$i])) {
				$division['is_real']="1";
			} else {
				$division['is_real']="0";
			}
			array_push($divisions, $division);
			$i++;
		}

		$data['number_of_episodes']=$total_eps;

		$episodes=array();
		$i=1;
		while (!empty($_POST['form-episode-list-id-'.$i])) {
			$episode = array();
			if (is_numeric($_POST['form-episode-list-id-'.$i])) {
				$episode['id']=escape($_POST['form-episode-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de capítol no numèric");
			}
			if (!empty($_POST['form-episode-list-division-'.$i]) && is_numeric($_POST['form-episode-list-division-'.$i])) {
				$episode['division']=escape($_POST['form-episode-list-division-'.$i]);
			} else {
				$episode['division']="NULL";
			}
			if ((!empty($_POST['form-episode-list-num-'.$i]) || $_POST['form-episode-list-num-'.$i]=='0') && is_numeric($_POST['form-episode-list-num-'.$i])) {
				$episode['number']=escape($_POST['form-episode-list-num-'.$i]);
			} else {
				$episode['number']="NULL";
			}
			if (!empty($_POST['form-episode-list-description-'.$i])) {
				$episode['description']="'".escape($_POST['form-episode-list-description-'.$i])."'";
			} else {
				$episode['description']="NULL";
			}
			if (!empty($_POST['form-episode-list-linked_episode_id-'.$i]) && is_numeric($_POST['form-episode-list-linked_episode_id-'.$i])) {
				$episode['linked_episode_id']=escape($_POST['form-episode-list-linked_episode_id-'.$i]);
			} else {
				$episode['linked_episode_id']="NULL";
			}
			array_push($episodes, $episode);
			$i++;
		}

		$related_series=array();
		$i=1;
		while (!empty($_POST['form-related-list-related_series_id-'.$i])) {
			if (is_numeric($_POST['form-related-list-related_series_id-'.$i])) {
				array_push($related_series,escape($_POST['form-related-list-related_series_id-'.$i]));
			} else {
				crash("Dades invàlides: id d’element relacionat no numèric");
			}
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM series WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash("Algú altre ha actualitzat la sèrie mentre tu l’editaves. Hauràs de tornar a fer els canvis.");
			}
			
			$name_result = query("SELECT COUNT(*) cnt FROM series WHERE type='".$type."' AND name='".$data['name']."' AND id<>".$data['id']);
			$name_row = mysqli_fetch_assoc($name_result);
			if ($name_row['cnt']>0) {
				crash("Ja hi ha una sèrie amb aquest títol. Revisa que no l’hagis afegida per duplicat i, en cas contrari, afegeix una diferenciació a totes dues (per exemple, l’any d’emissió o l’autor entre parèntesi).");
			}
			
			$external_ids_result = query("SELECT COUNT(*) cnt FROM series s WHERE s.type='".$type."' AND (s.external_id IN ('".implode("', '", $external_ids)."') OR EXISTS (SELECT * FROM division d WHERE d.series_id=s.id AND d.external_id IN ('".implode("', '", $external_ids)."'))) AND s.id<>".$data['id']);
			$external_ids_row = mysqli_fetch_assoc($external_ids_result);
			if ($external_ids_row['cnt']>0) {
				crash("Ja hi ha una sèrie que conté un dels identificadors de ".($type=='liveaction' ? 'MyDramaList' : 'MyAnimeList')." a la fitxa de la sèrie o en una divisió. Revisa que no estiguis afegint una obra que ja existeix i en cas de dubte contacta amb un administrador.");
			}
			
			log_action("update-series", "S’ha actualitzat la sèrie «".$_POST['name']."» (id. de sèrie: ".$data['id'].")");
			query("UPDATE series SET name='".$data['name']."',alternate_names=".$data['alternate_names'].",keywords=".$data['keywords'].",subtype='".$data['subtype']."',publish_date=".$data['publish_date'].",author=".$data['author'].",studio=".$data['studio'].",rating=".$data['rating'].",number_of_episodes=".$data['number_of_episodes'].",external_id=".$data['external_id'].",score=".$data['score'].",has_licensed_parts=".$data['has_licensed_parts'].",comic_type=".$data['comic_type'].",reader_type=".$data['reader_type'].",default_version_id=".$data['default_version_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
			query("DELETE FROM rel_series_genre WHERE series_id=".$data['id']);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$data['id'].",".$genre.")");
			}
			$ids=array();
			foreach ($divisions as $division) {
				if ($division['id']!=-1) {
					array_push($ids,$division['id']);
				}
			}
			query("DELETE FROM division WHERE series_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($divisions as $division) {
				if ($division['id']==-1) {
					query("INSERT INTO division (series_id,number,name,number_of_episodes,external_id,is_real) VALUES (".$data['id'].",".$division['number'].",'".$division['name']."',".$division['number_of_episodes'].",".$division['external_id'].",".$division['is_real'].")");
				} else {
					query("UPDATE division SET number=".$division['number'].",name='".$division['name']."',number_of_episodes=".$division['number_of_episodes'].",external_id=".$division['external_id'].",is_real=".$division['is_real']." WHERE id=".$division['id']);
				}
			}
			$ids=array();
			foreach ($episodes as $episode) {
				if ($episode['id']!=-1) {
					array_push($ids,$episode['id']);
				}
			}
			//Links and episode_titles will be removed too because their FK is set to cascade
			//Views will NOT be removed in order to keep consistent stats history
			query("DELETE FROM episode WHERE series_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($episodes as $episode) {
				if ($episode['id']==-1) {
					query("INSERT INTO episode (series_id,division_id,number,description,linked_episode_id,created,created_by,updated,updated_by) VALUES (".$data['id'].",(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$data['id']."),".$episode['number'].",".$episode['description'].",".$episode['linked_episode_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
				} else {
					query("UPDATE episode SET division_id=(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$data['id']."),number=".$episode['number'].",description=".$episode['description'].",linked_episode_id=".$episode['linked_episode_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$episode['id']);
				}
			}
			query("DELETE FROM related_series WHERE series_id=".$data['id']." OR related_series_id=".$data['id']);
			foreach ($related_series as $related_series_id) {
				query("REPLACE INTO related_series (series_id,related_series_id) VALUES (".$data['id'].",".$related_series_id.")");
				query("REPLACE INTO related_series (series_id,related_series_id) VALUES (".$related_series_id.",".$data['id'].")");
			}

			//Update previews for all versions - they may be affected by these changes
			
			$resultv = query("SELECT id FROM version WHERE series_id=".$data['id']);
			while ($rowv = mysqli_fetch_assoc($resultv)) {
				update_version_preview($rowv['id']);
			}
			mysqli_free_result($resultv);

			$_SESSION['message']="S’han desat les dades correctament.";
		}
		else {
			$name_result = query("SELECT COUNT(*) cnt FROM series WHERE type='".$type."' AND name='".$data['name']."'");
			$name_row = mysqli_fetch_assoc($name_result);
			if ($name_row['cnt']>0) {
				crash("Ja hi ha una sèrie amb aquest títol. Revisa que no l’hagis afegida per duplicat i, en cas contrari, afegeix una diferenciació a totes dues (per exemple, l’any d’emissió o l’autor entre parèntesi).");
			}
			
			$external_ids_result = query("SELECT COUNT(*) cnt FROM series s WHERE s.type='".$type."' AND (s.external_id IN ('".implode("', '", $external_ids)."') OR EXISTS (SELECT * FROM division d WHERE d.series_id=s.id AND d.external_id IN ('".implode("', '", $external_ids)."')))");
			$external_ids_row = mysqli_fetch_assoc($external_ids_result);
			if ($external_ids_row['cnt']>0) {
				crash("Ja hi ha una sèrie que conté un dels identificadors de ".($type=='liveaction' ? 'MyDramaList' : 'MyAnimeList')." a la fitxa de la sèrie o en una divisió. Revisa que no estiguis afegint una obra que ja existeix i en cas de dubte contacta amb un administrador.");
			}
			
			log_action("create-series", "S’ha creat la sèrie «".$_POST['name']."»");
			query("INSERT INTO series (name,alternate_names,keywords,type,subtype,publish_date,author,studio,rating,number_of_episodes,external_id,score,has_licensed_parts,comic_type,reader_type,default_version_id,created,created_by,updated,updated_by) VALUES ('".$data['name']."',".$data['alternate_names'].",".$data['keywords'].",'".$data['type']."','".$data['subtype']."',".$data['publish_date'].",".$data['author'].",".$data['studio'].",".$data['rating'].",".$data['number_of_episodes'].",".$data['external_id'].",".$data['score'].",".$data['has_licensed_parts'].",".$data['comic_type'].",".$data['reader_type'].",".$data['default_version_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$inserted_id.",".$genre.")");
			}
			foreach ($divisions as $division) {
				query("INSERT INTO division (series_id,number,name,number_of_episodes,external_id,is_real) VALUES (".$inserted_id.",".$division['number'].",'".$division['name']."',".$division['number_of_episodes'].",".$division['external_id'].",".$division['is_real'].")");
			}
			foreach ($episodes as $episode) {
				query("INSERT INTO episode (series_id,division_id,number,description,linked_episode_id,created,created_by,updated,updated_by) VALUES (".$inserted_id.",(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$inserted_id."),".$episode['number'].",".$episode['description'].",".$episode['linked_episode_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			}
			foreach ($related_series as $related_series_id) {
				query("INSERT INTO related_series (series_id,related_series_id) VALUES (".$inserted_id.",".$related_series_id.")");
				query("INSERT INTO related_series (series_id,related_series_id) VALUES (".$related_series_id.",".$inserted_id.")");
			}

			$_SESSION['message']="S’han desat les dades correctament.<br /><a class=\"btn btn-primary mt-2\" href=\"version_edit.php?type=$type&series_id=$inserted_id\"><span class=\"fa fa-plus pe-2\"></span>Crea’n una versió</a>";
		}

		header("Location: series_list.php?type=$type");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT s.* FROM series s WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Series not found');
		mysqli_free_result($result);
		if ($row['type']!=$type) {
			crash('Wrong type specified');
		}

		$resultg = query("SELECT sg.* FROM rel_series_genre sg WHERE sg.series_id=".escape($_GET['id']));
		$genres = array();
		while ($rowg = mysqli_fetch_assoc($resultg)) {
			array_push($genres, $rowg['genre_id']);
		}
		mysqli_free_result($resultg);

		$resultd = query("SELECT d.* FROM division d WHERE d.series_id=".escape($_GET['id'])." ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$resulte = query("SELECT e.*,d.number division, EXISTS(SELECT * FROM file f WHERE f.episode_id=e.id AND f.is_lost=0) has_version FROM episode e LEFT JOIN division d ON e.division_id=d.id WHERE e.series_id=".escape($_GET['id'])." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else {
		$row = array();
		$genres = array();
		$divisions = array();
		$episodes = array();
		$row['has_licensed_parts']=0;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita ".$content_apos : "Afegeix ".$content_one; ?></h4>
					<hr>
					<form method="post" action="series_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfEpisodes()">
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-external_id">Identificador de <?php echo $external_provider; ?></label> <?php print_helper_box('Identificador de '.$external_provider, 'Correspon a l’identificador numèric'.($external_provider=='MyDramaList' ? ' seguit d’un guionet i el text' : '').' que hi ha a l’URL de '.$external_provider.'.\n\nPots importar-lo automàticament si prems el botó «Importa» i hi escrius l’URL sencer de '.$external_provider.'.\n\nNo és obligatori, però si està informat, permet saber la valoració que en fan els usuaris i per tant es podrà mostrar a «Més ben valorats», la cerca per puntuació, etc.'); ?>
									<div style="display: flex;">
										<input class="form-control" name="external_id" id="form-external_id"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> value="<?php echo $row['external_id']; ?>">
									<button type="button" id="import-from-mal" class="btn btn-primary ms-2">
										<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
										<span id="import-from-mal-not-loading" class="fa fa-cloud-arrow-down pe-2"></span>Importa
									</button>
									</div>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-score">Puntuació a <?php echo $external_provider; ?></label> <?php print_helper_box('Puntuació a '.$external_provider, 'Es mostra al web i s’utilitza per a permetre filtrar continguts per puntuació i per a recomanar material de qualitat.\n\nNo es pot modificar perquè s’obté automàticament de '.$external_provider.'.'); ?>
									<input class="form-control" name="score" id="form-score" type="number" value="<?php echo $row['score']; ?>" step=".01" readonly>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-licensed_status">Estat de llicència<span class="mandatory"></span></label> <?php print_helper_box('Estat de llicència', 'Si hi ha part de l’obra (per exemple, alguna temporada, algun film o algun volum) que ha estat editada en català de manera oficial, cal seleccionar «Té parts llicenciades».\n\nSi se selecciona «Té parts llicenciades», es mostra un missatge informatiu a la fitxa del contingut.\n\nRecorda que al portal no admetem en cap cas contingut llicenciat.'); ?>
									<select class="form-select" name="has_licensed_parts" id="form-licensed_status" required>
										<option value="0"<?php echo $row['has_licensed_parts']==0 ? " checked" : ""; ?>>No té cap part llicenciada</option>
										<option value="1"<?php echo $row['has_licensed_parts']==1 ? " selected" : ""; ?>>Té parts llicenciades</option>
									</select>
								</div>
							</div>
						</div>
						<div id="import-from-mal-done" class="col-sm mb-3 alert alert-warning d-none">
							<span class="fa fa-exclamation-triangle pe-2"></span>S’ha importat la fitxa de <?php echo $external_provider; ?>. Revisa que les dades siguin correctes, omple els camps buits, afegeix-hi les divisions que hi faltin i omple la llista de capítols, si s’escau.
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-name-with-autocomplete">Títol en la llengua original<span class="mandatory"></span></label> <?php print_helper_box('Títol en la llengua original', 'S’hi ha d’introduir el títol en la llengua original (no pas en català), sempre fent servir la versió en alfabet occidental (no s’admeten kanji ni hanja).\n\nPosteriorment, a la fitxa de cada versió s’hi introduirà un títol localitzat, que serà el que es faci servir de manera principal al web.\n\nRecorda que cada contingut pot tenir més d’una temporada (o volum): no donis d’alta una fitxa diferent per a cada temporada o volum del mateix contingut.'); ?>
									<input class="form-control" name="name" id="form-name-with-autocomplete" data-old-value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>" placeholder="- Introdueix un títol -" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
									<input type="hidden" id="type" value="<?php echo $type; ?>">
									<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-alternate_names">Altres títols</label> <?php print_helper_box('Altres títols', 'S’hi poden introduir altres títols que tingui la sèrie (tant en la llengua original com en anglès).\n\nNo es permet introduir-hi títols en altres llengües que no siguin l’original ni l’anglès.\n\nSi n’hi ha més d’un, se separen per comes.'); ?>
									<input class="form-control" name="alternate_names" id="form-alternate_names" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_names'])); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-type" class="mandatory">Tipus d’obra</label> <?php print_helper_box('Tipus d’obra', ($type=='manga' ? 'Els mangues que són únicament un capítol han de marcar-se com a «One-shot», però si són recopilacions de one-shots, cal marcar-hi «Serialitzat».\n\nQualsevol manga de més d’un capítol és «Serialitzat».' : 'Les sèries s’han de marcar com a «Sèrie», i els films, com a «Film» (independentment de si són conjunts de films o només un de sol).\n\nEls curts també són films.\n\nSi una fitxa conté únicament capítols especials, també es consideraran films.\n\nLes col·leccions d’OVAs (i les OVAs soles) les considerem també films, llevat que siguin clarament una sèrie independent (en aquest cas, serien una sèrie).')); ?>
									<select class="form-select" name="subtype" id="form-subtype" required oninput="recalculateDivisionNames();">
										<option value="">- Selecciona un tipus -</option>
<?php
	if ($type=='manga') {
?>
										<option value="oneshot"<?php echo $row['subtype']=='oneshot' ? " selected" : ""; ?>>One-shot</option>
										<option value="serialized"<?php echo $row['subtype']=='serialized' ? " selected" : ""; ?>>Serialitzat</option>
<?php
	} else {
?>
										<option value="movie"<?php echo $row['subtype']=='movie' ? " selected" : ""; ?>>Film</option>
										<option value="series"<?php echo $row['subtype']=='series' ? " selected" : ""; ?>>Sèrie</option>
<?php
	}
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-publish_date">Data d’estrena</label> <?php print_helper_box('Data d’estrena', 'Data d’estrena del contingut, si és coneguda. Permet filtrar per any de publicació a la cerca.\n\nSi no se’n coneix el mes o el dia, introdueix «01» a les caselles corresponents.'); ?>
									<input class="form-control" name="publish_date" type="date" id="form-publish_date" maxlength="200" value="<?php echo !empty($row['publish_date']) ? date('Y-m-d', strtotime($row['publish_date'])) : ""; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-rating" class="mandatory">Valoració per edats</label> <?php print_helper_box('Valoració per edats', 'Edat recomanada per a veure el contingut.\n\nUtilitzem els valors proporcionats en webs aliens (MyAnimeList, MyDramaList, etcètera).\n\nL’usuari pot filtrar per valoració, però sempre pot triar veure-ho independent de la seva edat.\n\nEn el cas de marcar «Contingut pornogràfic», implica que el contingut no es mostrarà al portal general sinó al de hentai.\n\nNo s’admet en cap cas material d’imatge real amb contingut pornogràfic.'); ?>
									<select class="form-select" name="rating" id="form-rating" required>
										<option value="">- Selecciona una valoració -</option>
										<option value="TP"<?php echo $row['rating']=='TP' ? " selected" : ""; ?>>Tots els públics</option>
										<option value="+7"<?php echo $row['rating']=='+7' ? " selected" : ""; ?>>Majors de 7 anys</option>
										<option value="+13"<?php echo $row['rating']=='+13' ? " selected" : ""; ?>>Majors de 13 anys</option>
										<option value="+16"<?php echo $row['rating']=='+16' ? " selected" : ""; ?>>Majors de 16 anys</option>
										<option value="+18"<?php echo $row['rating']=='+18' ? " selected" : ""; ?>>Majors de 18 anys</option>
										<option value="XXX"<?php echo $row['rating']=='XXX' ? " selected" : ""; ?>>Contingut pornogràfic</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
<?php
	if ($type=='manga') {
?>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-comic_type" class="mandatory">Tipus de còmic</label> <?php print_helper_box('Tipus de còmic', 'Cal seleccionar el tipus de còmic: manga, manhua, manhwa o novel·la lleugera.'); ?>
									<select class="form-select" name="comic_type" id="form-comic_type" required oninput="recalculateDivisionNames();">
										<option value="">- Selecciona un tipus de còmic -</option>
										<option value="manga"<?php echo $row['comic_type']=='manga' ? " selected" : ""; ?>>Manga (còmic japonès)</option>
										<option value="manhua"<?php echo $row['comic_type']=='manhua' ? " selected" : ""; ?>>Manhua (còmic xinès)</option>
										<option value="manhwa"<?php echo $row['comic_type']=='manhwa' ? " selected" : ""; ?>>Manhwa (còmic coreà)</option>
										<option value="novel"<?php echo $row['comic_type']=='novel' ? " selected" : ""; ?>>Novel·la lleugera</option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-reader_type" class="mandatory">Tipus de lectura</label> <?php print_helper_box('Tipus de lectura', 'Normalment, el manga sol ser paginat i se sol llegir de dreta a esquerra, llevat que s’hagi occidentalitzat (en aquest cas, es llegiria d’esquerra a dreta).\n\nEls webtoons se solen llegir desplaçant-se per una tira vertical.\n\nL’usuari pot llegir els còmics paginats en mode tira vertical si li és més còmode, però els còmics pensats per a ser llegits en una tira vertical no es poden llegir mai paginats.'); ?>
									<select class="form-select" name="reader_type" id="form-reader_type" required>
										<option value="">- Selecciona un tipus de lectura -</option>
										<option value="rtl"<?php echo $row['reader_type']=='rtl' ? " selected" : ""; ?>>Paginada (de dreta a esquerra)</option>
										<option value="ltr"<?php echo $row['reader_type']=='ltr' ? " selected" : ""; ?>>Paginada (d’esquerra a dreta)</option>
										<option value="strip"<?php echo $row['reader_type']=='strip' ? " selected" : ""; ?>>Tira vertical</option>
									</select>
								</div>
							</div>
<?php
	} else {
?>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-studio">Estudi</label> <?php print_helper_box('Estudi', 'Estudi d’animació o productora de l’obra.\n\nSi n’hi ha més d’un, se separen per comes.'); ?>
									<input class="form-control" name="studio" id="form-studio" maxlength="200" value="<?php echo htmlspecialchars($row['studio']); ?>">
								</div>
							</div>
<?php
	}
?>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-author">Autor</label> <?php print_helper_box('Autor', 'Nom complet de l’autor original.\n\nL’ordre dels noms en totes les llengües és primer el nom i després el cognom, llevat del coreà, en què és primer el cognom i després el nom.\n\nSi hi ha més d’un autor, se separen per comes.'); ?>
									<input class="form-control" name="author" id="form-author" maxlength="200" value="<?php echo htmlspecialchars($row['author']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-keywords">Paraules clau</label> <?php print_helper_box('Paraules clau', 'És un camp opcional on es poden introduir paraules clau relacionades amb la sèrie.\n\nPer exemple, si volem que en cercar «Goku» aparegui la sèrie «Bola de Drac», hi posarem «Goku» a les paraules clau.\n\nSi n’hi ha més d’una, se separen per espais.\n\nNo en feu un ús abusiu.'); ?>
									<input class="form-control" name="keywords" id="form-keywords" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['keywords'])); ?>">
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-demographics">Demografia</label> <?php print_helper_box('Demografia', 'Públic al qual va orientat del contingut.\n\nNormalment, se’n selecciona com a màxim una.\n\nEn continguts d’imatge real, no cal especificar-la.'); ?>
							<div id="form-demographics" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='demographics' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-genres">Gèneres</label> <?php print_helper_box('Gèneres', 'Selecciona tots els gèneres amb els quals encaixi l’argument principal del contingut.'); ?>
							<div id="form-genres" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='genre' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-explicit">Nivells d’erotisme</label> <?php print_helper_box('Nivells d’erotisme', 'Si hi ha erotisme d’alguna mena, marca l’opció més adequada.\n\nActualment només es mostra com a etiqueta al web.\n\nAquestes etiquetes no influeixen en si un contingut apareix al portal general o al de hentai, això es fa en funció de si la valoració per edats és «Contingut pornogràfic» o no.'); ?>
							<div id="form-explicit" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='explicit' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-themes">Temàtiques</label> <?php print_helper_box('Temàtiques', 'Selecciona totes les temàtiques amb les quals encaixi l’argument principal del contingut.'); ?>
							<div id="form-themes" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='theme' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-3">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-division-list">Divisions</label> <?php print_helper_box('Divisions', $division_name_explanation); ?>
							<div class="container" id="form-division-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="division-list-table" data-count="<?php echo max(count($divisions),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;">Número<span class="mandatory"></span> <?php print_helper_box('Número de divisió', 'S’utilitza internament per a fer referència a la divisió i assignar-hi capítols, però no es mostra mai públicament.\n\nSi cal, s’hi poden fer servir decimals (per exemple: 1,5) o el número 0.'); ?></th>
													<th>Títol <span class="mandatory"></span> <?php print_helper_box('Títol', $division_help); ?></th>
													<th style="width: 15%;">Capítols<span class="mandatory"></span> <?php print_helper_box('Capítols de la divisió', 'Cal introduir-hi el nombre total de capítols de la divisió, incloent-hi els capítols especials i films enllaçats (si s’escau), però no els extres (que es donaran d’alta a la versió i no aquí).\n\nLes divisions amb 0 capítols no apareixeran mai a la fitxa pública, però el seu identificador de '.$external_provider.' es farà servir per a fer mitjana amb la resta.\n\nSi una divisió està inacabada (en publicació o emissió), cal que hi introdueixis el nombre de capítols que està previst que tingui (i els donis d’alta), o bé editar-ho a cada nou capítol que hi hagi (podràs fer-ho també directament a la fitxa de la versió).'); ?></th>
													<th style="width: 15%;">Id. <?php echo $external_provider; ?> <?php print_helper_box('Identificador de '.$external_provider, 'La puntuació dels usuaris mostrada al web es calcula fent la mitjana de tots els diferents identificadors de '.$external_provider.' (incloent-hi els de les divisions i el de la sèrie).'); ?></th>
													<th style="width: 5%;">Real <?php print_helper_box('Real', 'Defineix si aquesta divisió compta com a '.($type=='manga' ? 'volum' : 'temporada').'.\n\nPer exemple, es pot desmarcar en una divisió d’especials perquè els especials no comptin com a '.($type=='manga' ? 'volum' : 'temporada').'.\n\nAl web públic, es mostra que l’obra té '.($type=='manga' ? 'tants volums' : 'tantes temporades').' com divisions tenen marcada la casella «Real».'); ?></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	for ($i=0;$i<count($divisions);$i++) {
?>
												<tr id="form-division-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-division-list-number-<?php echo $i+1; ?>" name="form-division-list-number-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $divisions[$i]['number']!=NULL ? floatval($divisions[$i]['number']) : ''; ?>" step="any" required oninput="recalculateDivisionNames();"/>
														<input id="form-division-list-id-<?php echo $i+1; ?>" name="form-division-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $divisions[$i]['id']; ?>"/>
													</td>
													<td>
														<input id="form-division-list-name-<?php echo $i+1; ?>" name="form-division-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($divisions[$i]['name']); ?>" placeholder="- Introdueix un títol -" required/>
													</td>
													<td>
														<input id="form-division-list-number_of_episodes-<?php echo $i+1; ?>" name="form-division-list-number_of_episodes-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $divisions[$i]['number_of_episodes']; ?>" required/>
													</td>
													<td>
														<input id="form-division-list-external_id-<?php echo $i+1; ?>" name="form-division-list-external_id-<?php echo $i+1; ?>"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> class="form-control" value="<?php echo $divisions[$i]['external_id']; ?>"/>
													</td>
													<td class="text-center" style="padding-top: .75rem;">
														<input id="form-division-list-is_real-<?php echo $i+1; ?>" name="form-division-list-is_real-<?php echo $i+1; ?>" type="checkbox" value="1"<?php echo $divisions[$i]['is_real'] ? ' checked' : ''; ?>/>
													</td>
													<td class="text-center align-middle">
														<button id="form-division-list-delete-<?php echo $i+1; ?>" onclick="deleteDivisionRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
	if (count($divisions)==0) {
?>
												<tr id="form-division-list-row-1">
													<td>
														<input id="form-division-list-number-1" name="form-division-list-number-1" type="number" class="form-control" value="1" step="any" required oninput="recalculateDivisionNames();"/>
														<input id="form-division-list-id-1" name="form-division-list-id-1" type="hidden" value="-1"/>
													</td>
													<td>
														<input id="form-division-list-name-1" name="form-division-list-name-1" type="text" class="form-control" value="<?php echo $type=='manga' ? 'Volum 1' : ''; ?>" placeholder="- Introdueix un títol -" required/>
													</td>
													<td>
														<input id="form-division-list-number_of_episodes-1" name="form-division-list-number_of_episodes-1" type="number" class="form-control" value="" required/>
													</td>
													<td>
														<input id="form-division-list-external_id-1" name="form-division-list-external_id-1"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> class="form-control" value=""/>
													</td>
													<td class="text-center" style="padding-top: .75rem;">
														<input id="form-division-list-is_real-1" name="form-division-list-is_real-1" type="checkbox" value="1" checked/>
													<td class="text-center align-middle">
														<button id="form-division-list-delete-1" onclick="deleteDivisionRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="w-100 text-center"><button onclick="addDivisionRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix una divisió</button></div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-episode-list">Capítols</label> <?php print_helper_box('Capítols', 'Cal donar d’alta tots els capítols existents de l’obra i assignar-los a la divisió corresponent.\n\nPots generar els capítols automàticament si has informat el nombre de capítols de cada divisió i prems el botó «Genera els capítols automàticament».\n\nRevisa l’ajuda de cada camp per a més informació.'); ?>
							<div class="container" id="form-episode-list">
								<div class="row">
									<div class="w-100 column">
<?php
		if ($type!='manga') {
?>
										<select id="form-episode-list-linked_episode_id-XXX" name="form-episode-list-linked_episode_id-XXX" class="form-select d-none">
											<option value="">- Selecciona un film extern -</option>
<?php
			$resultle = query("SELECT e.id, IF(e.number IS NULL, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''), ' - ', e.description), IF(s.number_of_episodes=1, s.name, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''),' - Film ', TRIM(e.number)+0))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
			while ($lerow = mysqli_fetch_assoc($resultle)) {
?>
											<option value="<?php echo $lerow['id']; ?>"><?php echo htmlspecialchars($lerow['description']); ?></option>
<?php
			}
			mysqli_free_result($resultle);
?>
										</select>
<?php
		}
?>
										<table class="table table-bordered table-hover table-sm" id="episode-list-table" data-count="<?php echo max(count($episodes),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;">Divisió <?php print_helper_box('Número de divisió', 'Especifica a quina divisió pertany el capítol.\n\nSi cal, s’hi poden fer servir decimals (per exemple: 1,5) o el número 0.'); ?></th>
													<th style="width: 10%;">Número <?php print_helper_box('Número de capítol', 'Si cal, s’hi poden fer servir decimals (per exemple: 1,5) o el número 0.\n\nSi es deixa en blanc, el capítol es considera un capítol especial no numerat. En aquest cas, cal introduir-hi un nom d’especial perquè sigui possible saber a què fa referència.'); ?></th>
													<th>Nom de l’especial<?php echo $type!='manga' ? ' o film enllaçat' : ''; ?> <?php print_helper_box('Nom de l’especial'.($type!='manga' ? ' o film enllaçat' : ''), 'Aquest camp només es pot omplir en capítols especials (és a dir, si no introdueixes número de capítol)'.($type!='manga' ? ' o per a especificar un film enllaçat' : '').'.\n\nS’utilitza internament per a identificar l’especial, ja que no es pot fer amb el número perquè no en té.'.($type!='manga' ? '\n\nEls films enllaçats són films que tenen una fitxa independent (si són films, n’han de tenir i no pertànyer a la fitxa de la sèrie), però que també interessa mostrar-los a la fitxa de la sèrie.\n\nEnllaçar un film implica que es considerarà un capítol més dins d’una divisió concreta, però en lloc d’introduir-ne els enllaços a la fitxa d’aquesta obra, caldrà fer-ho a la fitxa del film corresponent.\n\nA la fitxa de la versió caldrà donar un títol als films enllaçats.' : '')); ?></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	$some_has_version = FALSE;
	for ($i=0;$i<count($episodes);$i++) {
		if ($episodes[$i]['has_version']) {
			$some_has_version = TRUE;
		}
?>
												<tr id="form-episode-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-episode-list-division-<?php echo $i+1; ?>" name="form-episode-list-division-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['division']!=NULL ? floatval($episodes[$i]['division']) : ''; ?>" step="any" required/>
													</td>
													<td>
														<input id="form-episode-list-num-<?php echo $i+1; ?>" oninput="checkEpisodeRow(<?php echo $i+1; ?>);" name="form-episode-list-num-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['number']!=NULL ? floatval($episodes[$i]['number']) : ''; ?>" placeholder="Especial" step="any"/>
														<input id="form-episode-list-id-<?php echo $i+1; ?>" name="form-episode-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['id']; ?>"/>
														<input id="form-episode-list-has_version-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['has_version']; ?>"/>
													</td>
													<td>
<?php
		if (!empty($episodes[$i]['linked_episode_id'])) {
?>
														<select id="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" name="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" class="form-select" required>
															<option value="">- Selecciona un film extern -</option>
<?php
			$resultle = query("SELECT e.id, IF(e.number IS NULL, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''), ' - ', e.description), IF(s.number_of_episodes=1, s.name, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''),' - Film ', TRIM(e.number)+0))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
			while ($lerow = mysqli_fetch_assoc($resultle)) {
?>
															<option value="<?php echo $lerow['id']; ?>"<?php echo $episodes[$i]['linked_episode_id']==$lerow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($lerow['description']); ?></option>
<?php
			}
			mysqli_free_result($resultle);
?>
														</select>
<?php
		} else {
?>
														<input id="form-episode-list-description-<?php echo $i+1; ?>" name="form-episode-list-description-<?php echo $i+1; ?>" type="text" class="form-control<?php echo $episodes[$i]['number']!=NULL ? ' d-none' : ''; ?>" value="<?php echo htmlspecialchars($episodes[$i]['description']); ?>" placeholder="- Introdueix un nom -" maxlength="500"/>
<?php
		}
?>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-<?php echo $i+1; ?>" onclick="deleteEpìsodeRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger<?php echo $episodes[$i]['has_version'] ? ' disabled' : ''; ?>"></button>
													</td>
												</tr>
<?php
	}
	if (count($episodes)==0) {
?>
												<tr id="form-episode-list-row-1">
													<td>
														<input id="form-episode-list-division-1" name="form-episode-list-division-1" type="number" class="form-control" value="1" step="any" required/>
													</td>
													<td>
														<input id="form-episode-list-num-1" oninput="checkEpisodeRow(1);" name="form-episode-list-num-1" type="number" class="form-control" value="1" placeholder="Especial" step="any"/>
														<input id="form-episode-list-id-1" name="form-episode-list-id-1" type="hidden" value="-1"/>
														<input id="form-episode-list-has_version-1" type="hidden" value="0"/>
													</td>
													<td>
														<input id="form-episode-list-description-1" name="form-episode-list-description-1" type="text" class="form-control d-none" value="" placeholder="- Introdueix un nom -" maxlength="500"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-1" onclick="deleteEpìsodeRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="d-flex">
										<button onclick="addEpisodeRow(false, false);" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix capítol</button>
										<button onclick="addEpisodeRow(true, false);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span>Afegeix especial</button>
<?php
	if ($type!='manga') {
?>
										<button onclick="addEpisodeRow(true, true);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span>Afegeix film enllaçat</button>
<?php
	}
?>
										<span style="flex-grow: 1;"></span>
										<button type="button" id="generate-episodes" class="btn btn-primary btn-sm ms-2<?php echo $some_has_version ? ' disabled' : ''; ?>">
											<span class="fa fa-sort-numeric-down pe-2"></span>
											Genera els capítols automàticament
										</button>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-related-list">Contingut relacionat</label> <?php print_helper_box('Contingut relacionat', 'Aquí es poden afegir continguts relacionats amb l’obra que estàs editant.\n\nAlguns exemples de continguts relacionats serien films o one-shots de la mateixa obra que tinguin fitxa a banda, o universos paral·lels on apareguin els mateixos personatges.\n\nNo hi afegeixis obres que no tinguin cap mena de relació, fins i tot si són del mateix autor, estudi o temàtica.'); ?>
							<div class="container" id="form-related-list">
<?php

	if (!empty($row['id'])) {
		$resultrs = query("SELECT DISTINCT t.series_id, CONCAT(s.type,' - ',s.name) name FROM (SELECT rs.related_series_id series_id FROM related_series rs WHERE rs.series_id=".escape($_GET['id'])." UNION SELECT rs.series_id series_id FROM related_series rs WHERE rs.related_series_id=".escape($_GET['id']).") t LEFT JOIN series s ON s.id=t.series_id ORDER BY s.type ASC, s.name ASC");
		$related_series = array();
		while ($rowrs = mysqli_fetch_assoc($resultrs)) {
			array_push($related_series, $rowrs);
		}
		mysqli_free_result($resultrs);
	} else {
		$related_series=array();
	}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-related-list-related_series_id-XXX" name="form-related-list-related_series_id-XXX" class="form-select d-none">
											<option value="">- Selecciona un element -</option>
<?php
		$results = query("SELECT s.id, CONCAT(IF(s.type='anime','Anime',IF(s.type='manga','Manga','Imatge real')),' - ',s.name) name FROM series s WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY s.type ASC, s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
											<option value="<?php echo $srow['id']; ?>"><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="related-list-table" data-count="<?php echo count($related_series); ?>">
											<thead>
												<tr>
													<th class="mandatory">Element</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="related-list-table-empty" class="<?php echo count($related_series)>0 ? 'd-none' : ''; ?>">
													<td colspan="2" class="text-center">- No hi ha cap element relacionat -</td>
												</tr>
<?php
	for ($j=0;$j<count($related_series);$j++) {
?>
												<tr id="form-related-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-related-list-related_series_id-<?php echo $j+1; ?>" name="form-related-list-related_series_id-<?php echo $j+1; ?>" class="form-select" required>
															<option value="">- Selecciona un element -</option>
<?php
		$results = query("SELECT s.id, CONCAT(IF(s.type='anime','Anime',IF(s.type='manga','Manga','Imatge real')),' - ',s.name) name FROM series s WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY s.type ASC, s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
															<option value="<?php echo $srow['id']; ?>"<?php echo $related_series[$j]['series_id']==$srow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<button id="form-related-list-delete-<?php echo $j+1; ?>" onclick="deleteRelatedSeriesRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="mb-3 row w-100 ms-0">
										<div class="col-sm text-center" style="padding-left: 0; padding-right: 0">
											<button onclick="addRelatedSeriesRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix un element relacionat</button>
										</div>
									</div>
								</div>
							</div>
						</div>
<?php
	if (!empty($row['id'])) {
?>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-comic_type" class="mandatory">Versió per defecte</label> <?php print_helper_box('Versió per defecte', 'Aquesta és la versió que s’obrirà per defecte en fer clic a una sèrie en llocs on no se’n mostri una versió específica.\n\nNormalment, és la primera versió que se n’ha fet, però es pot canviar per la de més qualitat.\n\nAbans de fer aquest canvi, consulta-ho amb un administrador.'); ?>
									<select class="form-select" name="default_version_id" id="form-default_version_id">
<?php

		$results = query("SELECT v.id, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansubs, v.title FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.series_id=".$row['id']." GROUP BY v.id ORDER BY fansubs ASC, v.title ASC");
		while ($vrow = mysqli_fetch_assoc($results)) {
?>
										<option value="<?php echo $vrow['id']; ?>"<?php echo $row['default_version_id']==$vrow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($vrow['fansubs'].' - '.$vrow['title']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
									</select>
								</div>
							</div>
						</div>
<?php
	}
?>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix ".$content_apos; ?></button>
						</div>
					</form>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
