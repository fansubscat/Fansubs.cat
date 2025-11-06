<?php
require_once(__DIR__.'/../common/initialization.inc.php');
require_once(__DIR__.'/../../common/libraries/preview_image_generator.php');

$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$page="anime";
		$header_title=lang('admin.series_edit.header.anime');
		$create_button_string=lang('admin.series_edit.create_button.anime');
		$create_title_string=lang('admin.series_edit.create_title.anime');
		$edit_title_string=lang('admin.series_edit.edit_title.anime');
		$external_provider='MyAnimeList';
		$division_help=lang('admin.series_edit.division.help.anime');
		$division_title_help=lang('admin.series_edit.division_title.help.anime');
	break;
	case 'manga':
		$page="manga";
		$header_title=lang('admin.series_edit.header.manga');
		$create_button_string=lang('admin.series_edit.create_button.manga');
		$create_title_string=lang('admin.series_edit.create_title.manga');
		$edit_title_string=lang('admin.series_edit.edit_title.manga');
		$external_provider='MyAnimeList';
		$division_help=lang('admin.series_edit.division.help.manga');
		$division_title_help=lang('admin.series_edit.division_title.help.manga');
	break;
	case 'liveaction':
		$page="liveaction";
		$header_title=lang('admin.series_edit.header.liveaction');
		$create_button_string=lang('admin.series_edit.create_button.liveaction');
		$create_title_string=lang('admin.series_edit.create_title.liveaction');
		$edit_title_string=lang('admin.series_edit.edit_title.liveaction');
		$external_provider='MyDramaList';
		$division_help=lang('admin.series_edit.division.help.liveaction');
		$division_title_help=lang('admin.series_edit.division_title.help.liveaction');
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		$external_ids = array('-1');
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash(lang('admin.error.name_missing'));
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
			crash(lang('admin.error.subtype_missing'));
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
					crash(lang('admin.error.genre_not_numeric'));
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
				crash(lang('admin.error.division_id_not_numeric'));
			}
			if ((!empty($_POST['form-division-list-number-'.$i]) || $_POST['form-division-list-number-'.$i]=='0') && is_numeric($_POST['form-division-list-number-'.$i])) {
				$division['number']=escape($_POST['form-division-list-number-'.$i]);
			} else {
				crash(lang('admin.error.division_number_not_numeric'));
			}
			if (!empty($_POST['form-division-list-name-'.$i])) {
				$division['name']=escape($_POST['form-division-list-name-'.$i]);
			} else {
				crash(lang('admin.error.division_name_missing'));
			}
			if ((!empty($_POST['form-division-list-number_of_episodes-'.$i]) && is_numeric($_POST['form-division-list-number_of_episodes-'.$i])) || $_POST['form-division-list-number_of_episodes-'.$i]==='0') {
				$division['number_of_episodes']=escape($_POST['form-division-list-number_of_episodes-'.$i]);
				$total_eps+=$_POST['form-division-list-number_of_episodes-'.$i];
			} else {
				crash(lang('admin.error.division_episodes_invalid'));
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
				crash(lang('admin.error.episode_id_not_numeric'));
			}
			if ((!empty($_POST['form-episode-list-division-'.$i]) || $_POST['form-episode-list-division-'.$i]=='0') && is_numeric($_POST['form-episode-list-division-'.$i])) {
				$episode['division']=escape($_POST['form-episode-list-division-'.$i]);
			} else {
				crash(lang('admin.error.episode_division_not_numeric'));
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
				crash(lang('admin.error.related_series_id_not_numeric'));
			}
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM series WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.series_edit_concurrency_error'));
			}
			
			$name_result = query("SELECT COUNT(*) cnt FROM series WHERE type='".$type."' AND name='".$data['name']."' AND id<>".$data['id']);
			$name_row = mysqli_fetch_assoc($name_result);
			if ($name_row['cnt']>0) {
				crash(lang('admin.error.series_edit_title_already_exists_error'));
			}
			
			$external_ids_result = query("SELECT COUNT(*) cnt FROM series s WHERE s.type='".$type."' AND (s.external_id IN ('".implode("', '", $external_ids)."') OR EXISTS (SELECT * FROM division d WHERE d.series_id=s.id AND d.external_id IN ('".implode("', '", $external_ids)."'))) AND s.id<>".$data['id']);
			$external_ids_row = mysqli_fetch_assoc($external_ids_result);
			if ($external_ids_row['cnt']>0) {
				crash(sprintf(lang('admin.error.series_edit_external_id_already_exists_error'), $external_provider));
			}
			
			log_action("update-series", "Series «".$_POST['name']."» (series id: ".$data['id'].") updated");
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

			$_SESSION['message']=lang('admin.generic.data_saved');
		}
		else {
			$name_result = query("SELECT COUNT(*) cnt FROM series WHERE type='".$type."' AND name='".$data['name']."'");
			$name_row = mysqli_fetch_assoc($name_result);
			if ($name_row['cnt']>0) {
				crash(lang('admin.error.series_edit_title_already_exists_error'));
			}
			
			$external_ids_result = query("SELECT COUNT(*) cnt FROM series s WHERE s.type='".$type."' AND (s.external_id IN ('".implode("', '", $external_ids)."') OR EXISTS (SELECT * FROM division d WHERE d.series_id=s.id AND d.external_id IN ('".implode("', '", $external_ids)."')))");
			$external_ids_row = mysqli_fetch_assoc($external_ids_result);
			if ($external_ids_row['cnt']>0) {
				crash(sprintf(lang('admin.error.series_edit_external_id_already_exists_error'), $external_provider));
			}
			
			log_action("create-series", "Series «".$_POST['name']."» created");
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

			$_SESSION['message']=lang('admin.generic.data_saved')."<br /><a class=\"btn btn-primary mt-2\" href=\"version_edit.php?type=$type&series_id=$inserted_id\"><span class=\"fa fa-plus pe-2\"></span>".lang('admin.series_edit.create_version_button')."</a>";
		}

		header("Location: series_list.php?type=$type");
		die();
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT s.* FROM series s WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.series_not_found'));
		mysqli_free_result($result);
		if ($row['type']!=$type) {
			crash(lang('admin.error.wrong_type_specified'));
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
		$row['id']='';
		$row['external_id']='';
		$row['score']='';
		$row['name']='';
		$row['updated']='';
		$row['alternate_names']='';
		$row['subtype']='';
		$row['rating']='';
		$row['studio']='';
		$row['author']='';
		$row['keywords']='';
		$row['comic_type']='';
		$row['reader_type']='';
		$row['has_licensed_parts']=0;
	}
?>
		<div class="modal fade" id="add-related-series-modal" tabindex="-1" role="dialog" aria-labelledby="add-related-series-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="add-related-series-modal-title"><?php echo lang('admin.series_edit.add_related_series_modal_title'); ?></h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo lang('admin.generic.close'); ?>">
							<span aria-hidden="true" class="fa fa-times"></span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo lang('admin.series_edit.add_related_series_modal_explanation'); ?>
						<input id="add-related-series-query" type="text" placeholder="<?php echo lang('admin.generic.search_ellipsis'); ?>" class="form-control mt-3" oninput="searchSeries();" />
						<hr>
						<div id="add-related-series-results"></div>
					</div>
					
					<div class="align-self-center">
						<button type="button" data-bs-dismiss="modal" class="btn btn-secondary m-2"><?php echo lang('admin.generic.cancel'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? $edit_title_string : $create_title_string; ?></h4>
					<hr>
					<form method="post" action="series_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfEpisodes()">
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-external_id"><?php echo sprintf(lang('admin.series_edit.external_provider_id'), $external_provider); ?></label> <?php print_helper_box(sprintf(lang('admin.series_edit.external_provider_id'), $external_provider), ($external_provider=='MyDramaList' ? lang('admin.series_edit.external_provider_id.help.mydramalist') : lang('admin.series_edit.external_provider_id.help.myanimelist'))); ?>
									<div style="display: flex;">
										<input class="form-control" name="external_id" id="form-external_id"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> value="<?php echo $row['external_id']; ?>">
									<button type="button" id="import-from-mal" class="btn btn-primary ms-2">
										<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm me-1 fa-width-auto" role="status" aria-hidden="true"></span>
										<span id="import-from-mal-not-loading" class="fa fa-cloud-arrow-down pe-2 fa-width-auto"></span><?php echo lang('admin.series_edit.import_from_external.short'); ?>
									</button>
									</div>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-score"><?php echo sprintf(lang('admin.series_edit.external_provider_score'), $external_provider); ?></label> <?php print_helper_box(sprintf(lang('admin.series_edit.external_provider_score'), $external_provider), sprintf(lang('admin.series_edit.external_provider_score.help'), $external_provider)); ?>
									<input class="form-control" name="score" id="form-score" type="number" value="<?php echo $row['score']; ?>" step=".01" readonly>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-licensed_status"><?php echo lang('admin.series_edit.licensed_status'); ?><span class="mandatory"></span></label> <?php print_helper_box(lang('admin.series_edit.licensed_status'), lang('admin.series_edit.licensed_status.help')); ?>
									<select class="form-select" name="has_licensed_parts" id="form-licensed_status" required>
										<option value="0"<?php echo $row['has_licensed_parts']==0 ? " checked" : ""; ?>><?php echo lang('admin.series_edit.licensed_status.no_parts_licensed'); ?></option>
										<option value="1"<?php echo $row['has_licensed_parts']==1 ? " selected" : ""; ?>><?php echo lang('admin.series_edit.licensed_status.parts_licensed'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div id="import-from-mal-done" class="col-sm mb-3 alert alert-warning d-none">
							<span class="fa fa-exclamation-triangle pe-2"></span><?php echo sprintf(lang('admin.series_edit.external_provider_import_complete'), $external_provider); ?>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-name-with-autocomplete"><?php echo lang('admin.series_edit.title_in_original_language'); ?><span class="mandatory"></span></label> <?php print_helper_box(lang('admin.series_edit.title_in_original_language'), lang('admin.series_edit.title_in_original_language.help')); ?>
									<input class="form-control" name="name" id="form-name-with-autocomplete" data-old-value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>" placeholder="<?php echo lang('admin.series_edit.title_in_original_language.placeholder'); ?>" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
									<input type="hidden" id="type" value="<?php echo $type; ?>">
									<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-alternate_names"><?php echo lang('admin.series_edit.alternate_titles'); ?></label> <?php print_helper_box(lang('admin.series_edit.alternate_titles'), lang('admin.series_edit.alternate_titles.help')); ?>
									<input class="form-control" name="alternate_names" id="form-alternate_names" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_names'])); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-type" class="mandatory"><?php echo lang('admin.series_edit.type_of_work'); ?></label> <?php print_helper_box(lang('admin.series_edit.type_of_work'), ($type=='manga' ? lang('admin.series_edit.type_of_work.help.manga') : lang('admin.series_edit.type_of_work.help'))); ?>
									<select class="form-select" name="subtype" id="form-subtype" required oninput="recalculateDivisionNames();">
										<option value=""><?php echo lang('admin.series_edit.type_of_work.select'); ?></option>
<?php
	if ($type=='manga') {
?>
										<option value="oneshot"<?php echo $row['subtype']=='oneshot' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.type_of_work.oneshot'); ?></option>
										<option value="serialized"<?php echo $row['subtype']=='serialized' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.type_of_work.serialized'); ?></option>
<?php
	} else {
?>
										<option value="movie"<?php echo $row['subtype']=='movie' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.type_of_work.movie'); ?></option>
										<option value="series"<?php echo $row['subtype']=='series' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.type_of_work.series'); ?></option>
<?php
	}
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-publish_date"><?php echo lang('admin.series_edit.launch_date'); ?></label> <?php print_helper_box(lang('admin.series_edit.launch_date'), lang('admin.series_edit.launch_date.help')); ?>
									<input class="form-control" name="publish_date" type="date" id="form-publish_date" maxlength="200" value="<?php echo !empty($row['publish_date']) ? date('Y-m-d', strtotime($row['publish_date'])) : ""; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-rating" class="mandatory"><?php echo lang('admin.series_edit.rating'); ?></label> <?php print_helper_box(lang('admin.series_edit.rating'), lang('admin.series_edit.rating.help')); ?>
									<select class="form-select" name="rating" id="form-rating" required>
										<option value=""><?php echo lang('admin.series_edit.rating.select'); ?></option>
										<option value="TP"<?php echo $row['rating']=='TP' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.everyone'); ?></option>
										<option value="+7"<?php echo $row['rating']=='+7' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.seven'); ?></option>
										<option value="+13"<?php echo $row['rating']=='+13' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.thirteen'); ?></option>
										<option value="+16"<?php echo $row['rating']=='+16' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.sixteen'); ?></option>
										<option value="+18"<?php echo $row['rating']=='+18' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.eighteen'); ?></option>
										<option value="XXX"<?php echo $row['rating']=='XXX' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.rating.porn'); ?></option>
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
									<label for="form-comic_type" class="mandatory"><?php echo lang('admin.series_edit.comic_type'); ?></label> <?php print_helper_box(lang('admin.series_edit.comic_type'), lang('admin.series_edit.comic_type.help')); ?>
									<select class="form-select" name="comic_type" id="form-comic_type" required oninput="recalculateDivisionNames();">
										<option value=""><?php echo lang('admin.series_edit.comic_type.select'); ?></option>
										<option value="manga"<?php echo $row['comic_type']=='manga' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.comic_type.manga'); ?></option>
										<option value="manhua"<?php echo $row['comic_type']=='manhua' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.comic_type.manhua'); ?></option>
										<option value="manhwa"<?php echo $row['comic_type']=='manhwa' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.comic_type.manhwa'); ?></option>
										<option value="novel"<?php echo $row['comic_type']=='novel' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.comic_type.lightnovel'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-reader_type" class="mandatory"><?php echo lang('admin.series_edit.reader_type'); ?></label> <?php print_helper_box(lang('admin.series_edit.reader_type'), lang('admin.series_edit.reader_type.help')); ?>
									<select class="form-select" name="reader_type" id="form-reader_type" required>
										<option value=""><?php echo lang('admin.series_edit.reader_type.select'); ?></option>
										<option value="rtl"<?php echo $row['reader_type']=='rtl' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.reader_type.rtl'); ?></option>
										<option value="ltr"<?php echo $row['reader_type']=='ltr' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.reader_type.ltr'); ?></option>
										<option value="strip"<?php echo $row['reader_type']=='strip' ? " selected" : ""; ?>><?php echo lang('admin.series_edit.reader_type.strip'); ?></option>
									</select>
								</div>
							</div>
<?php
	} else {
?>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-studio"><?php echo lang('admin.series_edit.studio'); ?></label> <?php print_helper_box(lang('admin.series_edit.studio'), lang('admin.series_edit.studio.help')); ?>
									<input class="form-control" name="studio" id="form-studio" maxlength="200" value="<?php echo htmlspecialchars($row['studio']); ?>">
								</div>
							</div>
<?php
	}
?>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-author"><?php echo lang('admin.series_edit.author'); ?></label> <?php print_helper_box(lang('admin.series_edit.author'), lang('admin.series_edit.author.help')); ?>
									<input class="form-control" name="author" id="form-author" maxlength="200" value="<?php echo htmlspecialchars($row['author']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-keywords"><?php echo lang('admin.series_edit.keywords'); ?></label> <?php print_helper_box(lang('admin.series_edit.keywords'), lang('admin.series_edit.keywords.help')); ?>
									<input class="form-control" name="keywords" id="form-keywords" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['keywords'])); ?>">
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-demographics"><?php echo lang('admin.series_edit.demographics'); ?></label> <?php print_helper_box(lang('admin.series_edit.demographics'), lang('admin.series_edit.demographics.help')); ?>
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
							<label for="form-genres"><?php echo lang('admin.series_edit.genres'); ?></label> <?php print_helper_box(lang('admin.series_edit.genres'), lang('admin.series_edit.genres.help')); ?>
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
							<label for="form-explicit"><?php echo lang('admin.series_edit.explicit_level'); ?></label> <?php print_helper_box(lang('admin.series_edit.explicit_level'), lang('admin.series_edit.explicit_level.help')); ?>
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
							<label for="form-themes"><?php echo lang('admin.series_edit.themes'); ?></label> <?php print_helper_box(lang('admin.series_edit.themes'), lang('admin.series_edit.themes.help')); ?>
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
							<label for="form-division-list"><?php echo lang('admin.series_edit.divisions'); ?></label> <?php print_helper_box(lang('admin.series_edit.divisions'), $division_help); ?>
							<div class="container" id="form-division-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="division-list-table" data-count="<?php echo max(count($divisions),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;"><?php echo lang('admin.series_edit.division_number'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.series_edit.division_number'), lang('admin.series_edit.division_number.help')); ?></th>
													<th><?php echo lang('admin.series_edit.division_title'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.series_edit.division_title'), $division_title_help); ?></th>
													<th style="width: 15%;"><?php echo lang('admin.series_edit.division_episodes.short'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.series_edit.division_episodes'), sprintf(lang('admin.series_edit.division_episodes.help'), $external_provider)); ?></th>
													<th style="width: 15%;"><?php echo sprintf(lang('admin.series_edit.division_external_id.short'), $external_provider); ?> <?php print_helper_box(sprintf(lang('admin.series_edit.division_external_id'), $external_provider), sprintf(lang('admin.series_edit.division_external_id.help'), $external_provider)); ?></th>
													<th style="width: 5%;"><?php echo lang('admin.series_edit.division_real'); ?> <?php print_helper_box(lang('admin.series_edit.division_real'), ($type=='manga' ? lang('admin.series_edit.division_real.help.manga') : lang('admin.series_edit.division_real.help'))); ?></th>
													<th class="text-center" style="width: 5%;"><?php echo lang('admin.generic.action'); ?></th>
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
														<input id="form-division-list-name-<?php echo $i+1; ?>" name="form-division-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($divisions[$i]['name']); ?>" placeholder="<?php echo lang('js.admin.series_edit.division.name_placeholder'); ?>" required/>
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
														<input id="form-division-list-name-1" name="form-division-list-name-1" type="text" class="form-control" value="<?php echo $type=='manga' ? lang('js.admin.generic.volume_prefix').'1' : ''; ?>" placeholder="<?php echo lang('js.admin.series_edit.division.name_placeholder'); ?>" required/>
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
									<div class="w-100 text-center"><button onclick="addDivisionRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.series_edit.add_division_button'); ?></button></div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-episode-list"><?php echo lang('admin.series_edit.episodes'); ?></label> <?php print_helper_box(lang('admin.series_edit.episodes'), lang('admin.series_edit.episodes.help')); ?>
							<div class="container" id="form-episode-list">
								<div class="row">
									<div class="w-100 column">
<?php
		if ($type!='manga') {
?>
										<select id="form-episode-list-linked_episode_id-XXX" name="form-episode-list-linked_episode_id-XXX" class="form-select d-none">
											<option value=""><?php echo lang('admin.series_edit.linked_movie.select'); ?></option>
<?php
			$resultle = query("SELECT e.id, IF(e.number IS NULL, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''), ' - ', e.description), IF(s.number_of_episodes=1, s.name, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''),' - ".lang('generic.query.movie_space')."', TRIM(e.number)+0))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
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
													<th style="width: 10%;"><?php echo lang('admin.series_edit.episode_division.short'); ?> <?php print_helper_box(lang('admin.series_edit.episode_division'), lang('admin.series_edit.episode_division.help')); ?></th>
													<th style="width: 10%;"><?php echo lang('admin.series_edit.episode_number.short'); ?> <?php print_helper_box(lang('admin.series_edit.episode_number'), lang('admin.series_edit.episode_number.help')); ?></th>
													<th><?php echo $type!='manga' ? lang('admin.series_edit.episode_special_name') : lang('admin.series_edit.episode_special_name.manga'); ?> <?php print_helper_box(($type!='manga' ? lang('admin.series_edit.episode_special_name') : lang('admin.series_edit.episode_special_name.manga')), ($type!='manga' ? lang('admin.series_edit.episode_special_name.help') : lang('admin.series_edit.episode_special_name.help.manga'))); ?></th>
													<th class="text-center" style="width: 5%;"><?php echo lang('admin.generic.action'); ?></th>
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
														<input id="form-episode-list-division-<?php echo $i+1; ?>" name="form-episode-list-division-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo floatval($episodes[$i]['division']); ?>" step="any" required/>
													</td>
													<td>
														<input id="form-episode-list-num-<?php echo $i+1; ?>" oninput="checkEpisodeRow(<?php echo $i+1; ?>);" name="form-episode-list-num-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['number']!=NULL ? floatval($episodes[$i]['number']) : ''; ?>" placeholder="<?php echo lang('js.admin.series_edit.episode.number_placeholder'); ?>" step="any"/>
														<input id="form-episode-list-id-<?php echo $i+1; ?>" name="form-episode-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['id']; ?>"/>
														<input id="form-episode-list-has_version-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['has_version']; ?>"/>
													</td>
													<td>
<?php
		if (!empty($episodes[$i]['linked_episode_id'])) {
?>
														<select id="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" name="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" class="form-select" required>
															<option value=""><?php echo lang('admin.series_edit.linked_movie.select'); ?></option>
<?php
			$resultle = query("SELECT e.id, IF(e.number IS NULL, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''), ' - ', e.description), IF(s.number_of_episodes=1, s.name, CONCAT(s.name,IF(d.name<>s.name,CONCAT(' - ',d.name),''),' - ".lang('generic.query.movie_space')."', TRIM(e.number)+0))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
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
														<input id="form-episode-list-description-<?php echo $i+1; ?>" name="form-episode-list-description-<?php echo $i+1; ?>" type="text" class="form-control<?php echo $episodes[$i]['number']!=NULL ? ' d-none' : ''; ?>" value="<?php echo htmlspecialchars($episodes[$i]['description']); ?>" placeholder="<?php echo lang('js.admin.series_edit.episode.description_placeholder'); ?>" maxlength="500"/>
<?php
		}
?>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-<?php echo $i+1; ?>" onclick="deleteEpìsodeRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger<?php echo $episodes[$i]['has_version'] ? ' disabled' : ''; ?>"></button>
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
														<input id="form-episode-list-num-1" oninput="checkEpisodeRow(1);" name="form-episode-list-num-1" type="number" class="form-control" value="1" placeholder="<?php echo lang('js.admin.series_edit.episode.number_placeholder'); ?>" step="any"/>
														<input id="form-episode-list-id-1" name="form-episode-list-id-1" type="hidden" value="-1"/>
														<input id="form-episode-list-has_version-1" type="hidden" value="0"/>
													</td>
													<td>
														<input id="form-episode-list-description-1" name="form-episode-list-description-1" type="text" class="form-control d-none" value="" placeholder="<?php echo lang('js.admin.series_edit.episode.description_placeholder'); ?>" maxlength="500"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-1" onclick="deleteEpìsodeRow(1);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="d-flex">
										<button onclick="addEpisodeRow(false, false);" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.series_edit.add_episode_button'); ?></button>
										<button onclick="addEpisodeRow(true, false);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.series_edit.add_special_button'); ?></button>
<?php
	if ($type!='manga') {
?>
										<button onclick="addEpisodeRow(true, true);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.series_edit.add_linked_movie_button'); ?></button>
<?php
	}
?>
										<span style="flex-grow: 1;"></span>
										<button type="button" id="generate-episodes" class="btn btn-primary btn-sm ms-2<?php echo $some_has_version ? ' disabled' : ''; ?>">
											<span class="fa fa-sort-numeric-down pe-2"></span>
											<?php echo lang('admin.series_edit.autogenerate_episodes'); ?>
										</button>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-related-list"><?php echo lang('admin.series_edit.related_content'); ?></label> <?php print_helper_box(lang('admin.series_edit.related_content'), lang('admin.series_edit.related_content.help')); ?>
							<div class="container" id="form-related-list">
<?php

	if (!empty($row['id'])) {
		$resultrs = query("SELECT DISTINCT t.series_id, CONCAT(IF(s.type='manga','".lang('admin.query.search_series.type_manga')."',IF(s.type='liveaction','".lang('admin.query.search_series.type_liveaction')."','".lang('admin.query.search_series.type_anime')."')),' - ',s.name) name FROM (SELECT rs.related_series_id series_id FROM related_series rs WHERE rs.series_id=".escape($_GET['id'])." UNION SELECT rs.series_id series_id FROM related_series rs WHERE rs.related_series_id=".escape($_GET['id']).") t LEFT JOIN series s ON s.id=t.series_id ORDER BY s.type ASC, s.name ASC");
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
										<table class="table table-bordered table-hover table-sm" id="related-list-table" data-count="<?php echo count($related_series); ?>">
											<thead>
												<tr>
													<th><?php echo lang('admin.series_edit.related_content_element'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.series_edit.related_content_element'), lang('admin.series_edit.related_content_element.help')); ?></th>
													<th class="text-center" style="width: 5%;"><?php echo lang('admin.generic.action'); ?></th>
												</tr>
											</thead>
											<tbody>
												<tr id="related-list-table-empty" class="<?php echo count($related_series)>0 ? 'd-none' : ''; ?>">
													<td colspan="2" class="text-center"><?php echo lang('admin.series_edit.related_content.empty'); ?></td>
												</tr>
<?php
	for ($j=0;$j<count($related_series);$j++) {
?>
												<tr id="form-related-list-row-<?php echo $j+1; ?>">
													<td>
														<input type="hidden" id="form-related-list-related_series_id-<?php echo $j+1; ?>" name="form-related-list-related_series_id-<?php echo $j+1; ?>" value="<?php echo $related_series[$j]['series_id']; ?>"/>
														<b><?php echo $related_series[$j]['name']; ?></b>
													</td>
													<td class="text-center align-middle">
														<button id="form-related-list-delete-<?php echo $j+1; ?>" onclick="deleteRelatedSeriesRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button>
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
											<button data-bs-toggle="modal" data-bs-target="#add-related-series-modal" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.series_edit.add_related_content_button'); ?></button>
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
									<label for="form-comic_type" class="mandatory"><?php echo lang('admin.series_edit.default_version'); ?></label> <?php print_helper_box(lang('admin.series_edit.default_version'), lang('admin.series_edit.default_version.help')); ?>
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
							<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : $create_button_string; ?></button>
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
