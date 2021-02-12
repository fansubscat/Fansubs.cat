<?php
$header_title="Edició de versions d'anime - Anime";
$page="anime";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['fansub_1']) && is_numeric($_POST['fansub_1'])) {
			$data['fansub_1']=escape($_POST['fansub_1']);
		} else {
			crash("Dades invàlides: manca fansub_1");
		}
		if (!empty($_POST['series_id']) && is_numeric($_POST['series_id'])) {
			$data['series_id']=escape($_POST['series_id']);
		} else {
			crash("Dades invàlides: manca series_id");
		}
		if (!empty($_POST['fansub_2']) && is_numeric($_POST['fansub_2'])) {
			$data['fansub_2']=escape($_POST['fansub_2']);
		} else {
			$data['fansub_2']=NULL;
		}
		if (!empty($_POST['fansub_3']) && is_numeric($_POST['fansub_3'])) {
			$data['fansub_3']=escape($_POST['fansub_3']);
		} else {
			$data['fansub_3']=NULL;
		}
		if (!empty($_POST['downloads_url_1'])) {
			$data['downloads_url_1']="'".escape($_POST['downloads_url_1'])."'";
		} else {
			$data['downloads_url_1']="NULL";
		}
		if (!empty($_POST['downloads_url_2'])) {
			$data['downloads_url_2']="'".escape($_POST['downloads_url_2'])."'";
		} else {
			$data['downloads_url_2']="NULL";
		}
		if (!empty($_POST['downloads_url_3'])) {
			$data['downloads_url_3']="'".escape($_POST['downloads_url_3'])."'";
		} else {
			$data['downloads_url_3']="NULL";
		}
		if (!empty($_POST['default_resolution'])) {
			$data['default_resolution']="'".escape($_POST['default_resolution'])."'";
		} else {
			$data['default_resolution']="NULL";
		}
		if (!empty($_POST['status']) && is_numeric($_POST['status'])) {
			$data['status']=escape($_POST['status']);
		} else {
			crash("Dades invàlides: manca status");
		}
		if (!empty($_POST['is_featurable'])){
			$data['is_featurable']=1;
		} else {
			$data['is_featurable']=0;
		}
		if (!empty($_POST['is_always_featured'])){
			$data['is_always_featured']=1;
		} else {
			$data['is_always_featured']=0;
		}

		$links=array();
		$episodes=array();

		$resulte = query("SELECT e.* FROM episode e WHERE e.series_id=".$data['series_id']);

		$data['episodes_missing']=0; //By default.. will be calculated depending on links
		
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			$episode_id=$rowe['id'];

			$episode = array();
			$episode['id'] = $episode_id;

			if (!empty($_POST['form-links-list-'.$episode_id.'-title'])) {
				$episode['title'] = "'".escape($_POST['form-links-list-'.$episode_id.'-title'])."'";
			} else {
				$episode['title'] = "NULL";
			}
			array_push($episodes, $episode);
			
			$i=1;
			while (!empty($_POST['form-links-list-'.$episode_id.'-id-'.$i])) {
				$link = array();
				if (is_numeric($_POST['form-links-list-'.$episode_id.'-id-'.$i])) {
					$link['id']=escape($_POST['form-links-list-'.$episode_id.'-id-'.$i]);
				} else {
					crash("Dades invàlides: manca id del capítol");
				}
				if (!empty($_POST['form-links-list-'.$episode_id.'-link-'.$i])) {
					$link['url']="'".escape($_POST['form-links-list-'.$episode_id.'-link-'.$i])."'";
				} else {
					$link['url']="NULL";
				}
				if (!empty($_POST['form-links-list-'.$episode_id.'-resolution-'.$i])) {
					$link['resolution']="'".escape($_POST['form-links-list-'.$episode_id.'-resolution-'.$i])."'";
				} else {
					$link['resolution']="NULL";
				}
				if (!empty($_POST['form-links-list-'.$episode_id.'-comments-'.$i])) {
					$link['comments']="'".escape($_POST['form-links-list-'.$episode_id.'-comments-'.$i])."'";
				} else {
					$link['comments']="NULL";
				}
				$link['episode_id']=$episode_id;

				if ($link['url']=="NULL" && !empty($_POST['form-links-list-'.$episode_id.'-lost-'.$i])) {
					$data['episodes_missing']=1;
				}

				if ($link['url']!="NULL" || !empty($_POST['form-links-list-'.$episode_id.'-lost-'.$i])) {
					array_push($links, $link);
				}
				$i++;
			}
		}
		mysqli_free_result($resulte);

		$extras=array();
		$i=1;
		while (!empty($_POST['form-extras-list-id-'.$i])) {
			$extra = array();
			if (is_numeric($_POST['form-extras-list-id-'.$i])) {
				$extra['id']=escape($_POST['form-extras-list-id-'.$i]);
			} else {
				crash("Dades invàlides: manca id de l'extra");
			}
			if (!empty($_POST['form-extras-list-name-'.$i])) {
				$extra['name']=escape($_POST['form-extras-list-name-'.$i]);
			} else {
				crash("Dades invàlides: manca name de l'extra");
			}
			if (!empty($_POST['form-extras-list-link-'.$i])) {
				$extra['url']=escape($_POST['form-extras-list-link-'.$i]);
			} else {
				crash("Dades invàlides: manca url de l'extra");
			}
			if (!empty($_POST['form-extras-list-resolution-'.$i])) {
				$extra['resolution']="'".escape($_POST['form-extras-list-resolution-'.$i])."'";
			} else {
				$extra['resolution']="NULL";
			}
			if (!empty($_POST['form-extras-list-comments-'.$i])) {
				$extra['comments']="'".escape($_POST['form-extras-list-comments-'.$i])."'";
			} else {
				$extra['comments']="NULL";
			}
			array_push($extras, $extra);
			$i++;
		}

		$folders=array();
		$i=1;
		while (!empty($_POST['form-folders-list-id-'.$i])) {
			$folder = array();
			if (is_numeric($_POST['form-folders-list-id-'.$i])) {
				$folder['id']=escape($_POST['form-folders-list-id-'.$i]);
			} else {
				crash("Dades invàlides: manca id de la carpeta");
			}
			if (!empty($_POST['form-folders-list-account_id-'.$i])) {
				$folder['account_id']=escape($_POST['form-folders-list-account_id-'.$i]);
			} else {
				crash("Dades invàlides: manca account_id de la carpeta");
			}
			if (!empty($_POST['form-folders-list-folder-'.$i])) {
				$folder['folder']=escape($_POST['form-folders-list-folder-'.$i]);
			} else {
				crash("Dades invàlides: manca folder de la carpeta");
			}
			if (!empty($_POST['form-folders-list-season_id-'.$i])) {
				$folder['season_id']=escape($_POST['form-folders-list-season_id-'.$i]);
			} else {
				$folder['season_id']="NULL";
			}
			if (!empty($_POST['form-folders-list-active-'.$i]) && $_POST['form-folders-list-active-'.$i]==1) {
				$folder['active']=1;
			} else {
				$folder['active']=0;
			}
			array_push($folders, $folder);
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-version", "S'ha actualitzat la versió de l'anime (id. d'anime: ".$data['series_id'].") (id. de versió: ".$data['id'].")");
			query("UPDATE version SET status=".$data['status'].",default_resolution=".$data['default_resolution'].",episodes_missing=".$data['episodes_missing'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."',is_featurable=".$data['is_featurable'].",is_always_featured=".$data['is_always_featured']." WHERE id=".$data['id']);
			query("DELETE FROM rel_version_fansub WHERE version_id=".$data['id']);
			query("DELETE FROM episode_title WHERE version_id=".$data['id']);
			if ($data['fansub_1']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_1'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_2']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_2'].",".$data['downloads_url_2'].")");
			}
			if ($data['fansub_3']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_3'].",".$data['downloads_url_3'].")");
			}

			foreach ($episodes as $episode) {
				if ($episode['title']!="NULL") {
					query("INSERT INTO episode_title (version_id,episode_id,title) VALUES (".$data['id'].",".$episode['id'].",".$episode['title'].")");
				}
			}

			$ids=array();
			foreach ($links as $link) {
				if ($link['id']!=-1) {
					array_push($ids,$link['id']);
				}
			}
			//Views will be removed too because their FK is set to cascade
			query("DELETE FROM link WHERE version_id=".$data['id']." AND episode_id IS NOT NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			//We do not count removing links as updating them, only insertions and real updates
			foreach ($links as $link) {
				if ($link['id']==-1) {
					query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments,created) VALUES (".$data['id'].",".$link['episode_id'].",NULL,".$link['url'].",".$link['resolution'].",".$link['comments'].",CURRENT_TIMESTAMP)");
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					$resultcr = query("SELECT * FROM link WHERE id=".$link['id']);
					if ($current_link = mysqli_fetch_assoc($resultcr)) {
						query("UPDATE link SET url=".$link['url'].",resolution=".$link['resolution'].",comments=".$link['comments']." WHERE id=".$link['id']);
						if (empty($_POST['do_not_count_as_update']) && (empty($current_link['url']) ? "NULL" : "'".escape($current_link['url'])."'")!=$link['url']) {
							query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
							if (empty($current_link['url']) && $link['url']!='NULL') {
								query("UPDATE link SET created=CURRENT_TIMESTAMP WHERE id=".$link['id']);
							}
						}
					}
					mysqli_free_result($resultcr);
				}
			}

			$ids=array();
			foreach ($extras as $extra) {
				if ($extra['id']!=-1) {
					array_push($ids,$extra['id']);
				}
			}
			//Views will be removed too because their FK is set to cascade
			query("DELETE FROM link WHERE version_id=".$data['id']." AND episode_id IS NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($extras as $extra) {
				if ($extra['id']==-1) {
					query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments,created) VALUES (".$data['id'].",NULL,'".$extra['name']."','".$extra['url']."',".$extra['resolution'].",".$extra['comments'].",CURRENT_TIMESTAMP)");
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					$resultcr = query("SELECT * FROM link WHERE id=".$extra['id']);
					if ($current_extra = mysqli_fetch_assoc($resultcr)) {
						query("UPDATE link SET extra_name='".$extra['name']."',url='".$extra['url']."',resolution=".$extra['resolution'].",comments=".$extra['comments']." WHERE id=".$extra['id']);
						if (empty($_POST['do_not_count_as_update']) && escape($current_extra['url'])!=$extra['url']) {
							query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
						}
					}
					mysqli_free_result($resultcr);
				}
			}

			$ids=array();
			foreach ($folders as $folder) {
				if ($folder['id']!=-1) {
					array_push($ids,$folder['id']);
				}
			}
			query("DELETE FROM folder WHERE version_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($folders as $folder) {
				if ($folder['id']==-1) {
					query("INSERT INTO folder (version_id,account_id,folder,season_id,active) VALUES (".$data['id'].",".$folder['account_id'].",'".$folder['folder']."',".$folder['season_id'].",".$folder['active'].")");
				} else {
					query("UPDATE folder SET account_id=".$folder['account_id'].",folder='".$folder['folder']."',season_id=".$folder['season_id'].",active=".$folder['active']." WHERE id=".$folder['id']);
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-version", "S'ha creat una versió de l'anime (id. d'anime: ".$data['series_id'].")");
			query("INSERT INTO version (series_id,status,default_resolution,episodes_missing,created,created_by,updated,updated_by,links_updated,links_updated_by,is_featurable,is_always_featured) VALUES (".$data['series_id'].",".$data['status'].",".$data['default_resolution'].",".$data['episodes_missing'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',".$data['is_featurable'].",".$data['is_always_featured'].")");
			$inserted_id=mysqli_insert_id($db_connection);
			if ($data['fansub_1']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_1'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_2']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_2'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_3']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_3'].",".$data['downloads_url_1'].")");
			}
			foreach ($episodes as $episode) {
				if ($episode['title']!="NULL") {
					query("INSERT INTO episode_title (version_id,episode_id,title) VALUES (".$inserted_id.",".$episode['id'].",".$episode['title'].")");
				}
			}
			foreach ($links as $link) {
				query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments,created) VALUES (".$inserted_id.",".$link['episode_id'].",NULL,".$link['url'].",".$link['resolution'].",".$link['comments'].",CURRENT_TIMESTAMP)");
			}
			foreach ($extras as $extra) {
				query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments,created) VALUES (".$inserted_id.",NULL,'".$extra['name']."','".$extra['url']."',".$extra['resolution'].",".$extra['comments'].",CURRENT_TIMESTAMP)");
			}
			foreach ($folders as $folder) {
				query("INSERT INTO folder (version_id,account_id,folder,season_id,active) VALUES (".$inserted_id.",".$folder['account_id'].",'".$folder['folder']."',".$folder['season_id'].",".$folder['active'].")");
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}

		header("Location: version_list.php");
		die();
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT v.* FROM version v WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Version not found');
		mysqli_free_result($result);

		$results = query("SELECT s.* FROM series s WHERE id=".$row['series_id']);
		$series = mysqli_fetch_assoc($results) or crash('Series not found');
		mysqli_free_result($results);

		$resultf = query("SELECT fansub_id, downloads_url FROM rel_version_fansub vf WHERE vf.version_id=".$row['id']);
		$fansubs = array();
		while ($rowf = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, array($rowf['fansub_id'], $rowf['downloads_url']));
		}
		mysqli_free_result($resultf);

		$resulte = query("SELECT e.*, et.title, ss.number season_number FROM episode e LEFT JOIN season ss ON e.season_id=ss.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".escape($_GET['id'])." WHERE e.series_id=".$row['series_id']." ORDER BY ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, e.name ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else if (!empty($_GET['series_id']) && is_numeric($_GET['series_id'])) {
		$row = array();

		$results = query("SELECT s.* FROM series s WHERE id=".escape($_GET['series_id']));
		$series = mysqli_fetch_assoc($results) or crash('Series not found');
		mysqli_free_result($results);

		$fansubs = array();

		$resulte = query("SELECT e.*, NULL title, ss.number season_number FROM episode e LEFT JOIN season ss ON e.season_id=ss.id WHERE e.series_id=".escape($_GET['series_id'])." ORDER BY ss.number IS NULL ASC, ss.number ASC, e.number IS NULL ASC, e.number ASC, e.name ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la versió" : "Afegeix una versió"; ?></h4>
					<hr>
					<form method="post" action="version_edit.php" onsubmit="return checkNumberOfLinks()">
						<div class="form-group">
							<label for="form-series" class="mandatory">Anime</label>
							<div id="form-series" class="font-weight-bold form-control"><?php echo htmlspecialchars($series['name']); ?></div>
							<input name="series_id" type="hidden" value="<?php echo $series['id']; ?>"/>
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-1" class="mandatory">Fansub</label>
									<select name="fansub_1" class="form-control" id="form-fansub-1" required>
										<option value="">- Selecciona un fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>0 && $fansubs[0][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-2">Fansub 2 <small class="text-muted">(en cas que sigui una col·laboració)</small></label>
									<select name="fansub_2" class="form-control" id="form-fansub-2">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>1 && $fansubs[1][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-3">Fansub 3 <small class="text-muted">(en cas que sigui una col·laboració)</small></label>
									<select name="fansub_3" class="form-control" id="form-fansub-3">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>2 && $fansubs[2][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_1">Enllaç de baixada dels fitxers originals 1<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_1" name="downloads_url_1" type="url" class="form-control" value="<?php echo (count($fansubs)>0 ? htmlspecialchars($fansubs[0][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_2">Enllaç de baixada dels fitxers originals 2<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_2" name="downloads_url_2" type="url" class="form-control" value="<?php echo (count($fansubs)>1 ? htmlspecialchars($fansubs[1][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_3">Enllaç de baixada dels fitxers originals 3<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_3" name="downloads_url_3" type="url" class="form-control" value="<?php echo (count($fansubs)>2 ? htmlspecialchars($fansubs[2][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-status" class="mandatory">Estat</label>
									<select class="form-control" name="status" id="form-status" required>
										<option value="">- Selecciona un estat -</option>
										<option value="1"<?php echo $row['status']==1 ? " selected" : ""; ?>>Completada</option>
										<option value="2"<?php echo $row['status']==2 ? " selected" : ""; ?>>En procés</option>
										<option value="3"<?php echo $row['status']==3 ? " selected" : ""; ?>>Parcialment completada (alguna temporada completada)</option>
										<option value="4"<?php echo $row['status']==4 ? " selected" : ""; ?>>Abandonada</option>
										<option value="5"<?php echo $row['status']==5 ? " selected" : ""; ?>>Cancel·lada</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-default_resolution">Resolució per defecte <small class="text-muted">(per a enllaços automàtics)</small></label>
									<input id="form-default_resolution" name="default_resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($row['default_resolution']); ?>" maxlength="200" placeholder="- Selecciona o introdueix una resolució -"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-featurable_check">Recomanacions</label>
									<div id="form-featurable_check" class="row pl-3 pr-3">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_featurable" id="form-is_featurable" value="1"<?php echo $row['is_featurable']==1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_featurable">Té qualitat per a ser recomanada</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_always_featured" id="form-is_always_featured" value="1"<?php echo $row['is_always_featured']==1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_always_featured">Mostra-la sempre com a recomanada</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-folders-list">Carpetes d'emmagatzematge <small class="text-muted">(per a l'obtenció automàtica d'enllaços)</small></label>
							<div class="container" id="form-folders-list">
<?php

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$resultfo = query("SELECT f.* FROM folder f WHERE f.version_id=".escape($_GET['id'])." ORDER BY f.id ASC");
		$folders = array();
		while ($rowfo = mysqli_fetch_assoc($resultfo)) {
			array_push($folders, $rowfo);
		}
		mysqli_free_result($resultfo);
	} else {
		$folders=array();
	}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-folders-list-account_id-XXX" name="form-folders-list-account_id-XXX" class="form-control d-none">
											<option value="">- Selecciona un compte -</option>
<?php
		if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
			$where = ' WHERE a.fansub_id='.$_SESSION['fansub_id'].' OR a.fansub_id IS NULL';
		} else {
			$where = '';
		}

		$resulta = query("SELECT a.* FROM account a$where ORDER BY a.type ASC, a.name ASC");
		while ($arow = mysqli_fetch_assoc($resulta)) {
?>
											<option value="<?php echo $arow['id']; ?>"><?php echo ($arow['type']=='mega' ? 'MEGA' : 'Google Drive').': '.htmlspecialchars($arow['name']); ?></option>
<?php
		}
		mysqli_free_result($resulta);
?>
										</select>
										<select id="form-folders-list-season_id-XXX" name="form-folders-list-season_id-XXX" class="form-control d-none">
											<option value="">- Qualsevol -</option>
<?php
		$resultss = query("SELECT ss.* FROM season ss WHERE ss.series_id=".$series['id']." ORDER BY ss.number ASC");
		while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
											<option value="<?php echo $ssrow['id']; ?>"><?php echo htmlspecialchars($ssrow['number'].(!empty($ssrow['name']) ? ' ('.$ssrow['name'].')' : '')); ?></option>
<?php
		}
		mysqli_free_result($resultss);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="folders-list-table" data-count="<?php echo count($folders); ?>">
											<thead>
												<tr>
													<th style="width: 25%;" class="mandatory">Compte</th>
													<th class="mandatory">Carpeta (MEGA) / Id. de carpeta (Google Drive)</th>
													<th style="width: 15%;">Temporada</th>
													<th class="text-center" style="width: 10%;">Sincronitza</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="folders-list-table-empty" class="<?php echo count($folders)>0 ? 'd-none' : ''; ?>">
													<td colspan="5" class="text-center">- No hi ha configurada cap carpeta -</td>
												</tr>
<?php
	for ($j=0;$j<count($folders);$j++) {
?>
												<tr id="form-folders-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-folders-list-account_id-<?php echo $j+1; ?>" name="form-folders-list-account_id-<?php echo $j+1; ?>" class="form-control" required>
															<option value="">- Selecciona un compte -</option>
<?php
		if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
			$where = ' WHERE a.fansub_id='.$_SESSION['fansub_id'].' OR a.fansub_id IS NULL';
		} else {
			$where = '';
		}

		$resulta = query("SELECT a.* FROM account a$where ORDER BY a.type ASC, a.name ASC");
		while ($arow = mysqli_fetch_assoc($resulta)) {
?>
															<option value="<?php echo $arow['id']; ?>"<?php echo $folders[$j]['account_id']==$arow['id'] ? " selected" : ""; ?>><?php echo ($arow['type']=='mega' ? 'MEGA' : 'Google Drive').': '.htmlspecialchars($arow['name']); ?></option>
<?php
		}
		mysqli_free_result($resulta);
?>
														</select>
														<input id="form-folders-list-id-<?php echo $j+1; ?>" name="form-folders-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $folders[$j]['id']; ?>"/>
													</td>
													<td>
														<input id="form-folders-list-folder-<?php echo $j+1; ?>" name="form-folders-list-folder-<?php echo $j+1; ?>" class="form-control" value="<?php echo htmlspecialchars($folders[$j]['folder']); ?>" maxlength="200" required/>
													</td>
													<td>
														<select id="form-folders-list-season_id-<?php echo $j+1; ?>" name="form-folders-list-season_id-<?php echo $j+1; ?>" class="form-control">
															<option value="">- Qualsevol -</option>
<?php
		$resultss = query("SELECT ss.* FROM season ss WHERE ss.series_id=".$series['id']." ORDER BY ss.number ASC");
		while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
															<option value="<?php echo $ssrow['id']; ?>"<?php echo $folders[$j]['season_id']==$ssrow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($ssrow['number'].(!empty($ssrow['name']) ? ' ('.$ssrow['name'].')' : '')); ?></option>
<?php
		}
		mysqli_free_result($resultss);
?>
														</select>
														<input id="form-folders-list-id-<?php echo $j+1; ?>" name="form-folders-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $folders[$j]['id']; ?>"/>
													</td>
													<td class="text-center align-middle">
														<input id="form-folders-list-active-<?php echo $j+1; ?>" name="form-folders-list-active-<?php echo $j+1; ?>" type="checkbox" value="1"<?php echo $folders[$j]['active']==1? " checked" : ""; ?>/>
													</td>
													<td class="text-center align-middle">
														<button id="form-folders-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionFolderRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="form-group row w-100 ml-0">
										<div class="col-sm text-left" style="padding-left: 0; padding-right: 0">
											<button onclick="addVersionFolderRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix una carpeta</button>
										</div>
										<div class="col-sm text-right" style="padding-left: 0; padding-right: 0">
											<select id="import-type" class="form-control form-control-sm form-inline" title="Indica el tipus de streaming preferit en aquesta actualització d'enllaços. Si trieu un tipus de compte, només s'utilitzarà aquell tipus. Si no n'hi ha cap d'aquell tipus, s'utilitzaran tots. Si hi ha alguns capítols a MEGA i altres a Google Drive, cal marcar 'Utilitza tots els comptes'." style="width: auto; display: inline; font-size: 78%;">
												<option value="all">Utilitza tots els comptes</option>
												<option value="googledrive">Prefereix Google Drive</option>
												<option value="mega" selected>Prefereix MEGA</option>
												<option value="sync">Només sincronitzats</option>
											</select> →
											<button type="button" id="import-from-mega" class="btn btn-primary btn-sm">
												<span id="import-from-mega-loading" class="d-none spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
												<span id="import-from-mega-not-loading" class="fa fa-redo pr-2"></span>Actualitza els enllaços ara
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-none alert alert-warning" id="import-failed-results">
							<div><span class="fa fa-exclamation-triangle mr-2"></span> Els següents elements no s'han importat perquè no tenen el format correcte o perquè els capítols no existeixen a la fitxa de l'anime. Afegeix-los a mà on correspongui. Recorda que els fitxers només s'importen automàticament si tenen el format "<i>text</i><u><b> - 123</b></u><i>text</i>.mp4".</div>
							<table class="table-hover table-sm mt-2 small w-100" id="import-failed-results-table">
								<thead>
									<tr>
										<th>Fitxer</th>
										<th>Enllaç</th>
										<th>Motiu</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="form-group">
							<label for="form-episode-list">Capítols i enllaços</label>
							<div class="container" id="form-episode-list">
								<datalist id="resolution-options">
									<option value="1080p">
									<option value="720p">
									<option value="480p">
									<option value="360p">
								</datalist>
<?php
	if ($series['show_episode_numbers']==0 && $series['order_type']!=0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest anime <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.<br /><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $series['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
<?php
	} else if ($series['show_episode_numbers']==0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest anime <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.</div>
								</div>
<?php
	} else if ($series['order_type']!=0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $series['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
<?php
	}
	for ($i=0;$i<count($episodes);$i++) {
		$episode_name='';
		if (!empty($episodes[$i]['season_number'])) {
			$episode_name.='Temporada '.$episodes[$i]['season_number'].' - ';
		} else {
			$episode_name.='Altres - ';
		}
		if (!empty($episodes[$i]['number'])) {
			if (!empty($episodes[$i]['name'])) {
				$episode_name.='Capítol '.floatval($episodes[$i]['number']).' <small class="text-muted">(Títol intern: '.htmlspecialchars($episodes[$i]['name']).')</small>';
			} else {
				$episode_name.='Capítol '.floatval($episodes[$i]['number']);
			}
		} else {
			$episode_name.=$episodes[$i]['name'].' <small class="text-muted">(Aquest títol NO és intern: es mostrarà si no introdueixes cap títol!)</small>';
		}

		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$resultl = query("SELECT l.* FROM link l WHERE l.version_id=".escape($_GET['id'])." AND l.episode_id=".$episodes[$i]['id']." ORDER BY l.id ASC");
			$links = array();
			while ($rowl = mysqli_fetch_assoc($resultl)) {
				array_push($links, $rowl);
			}
			mysqli_free_result($resultl);
		} else {
			$links=array();
		}
?>
								<div class="form-group">
									<label for="form-links-list-<?php echo $episodes[$i]['id']; ?>-title"><span class="fa fa-caret-square-right pr-2 text-primary"></span><?php echo $episode_name; ?></label>
									<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-title" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-title" type="text" class="form-control" value="<?php echo htmlspecialchars($episodes[$i]['title']); ?>" maxlength="200" placeholder="(Sense títol)"/>
									<div class="container" id="form-links-list-<?php echo $episodes[$i]['id']; ?>">
										<div class="row mb-3">
											<div class="w-100 column">
												<table class="table table-bordered table-hover table-sm" id="links-list-table-<?php echo $episodes[$i]['id']; ?>" data-count="<?php echo max(count($links),1); ?>">
													<thead>
														<tr>
															<th>Enllaç de streaming</th>
															<th style="width: 15%;">Resolució</th>
															<th style="width: 15%;">Comentaris</th>
															<th class="text-center" style="width: 5%;">Perdut</th>
															<th class="text-center" style="width: 5%;">Acció</th>
														</tr>
													</thead>
													<tbody>
<?php
		for ($j=0;$j<count($links);$j++) {
?>
														<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-<?php echo $j+1; ?>">
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-link-<?php echo $j+1; ?>" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-link-<?php echo $j+1; ?>" type="url" class="form-control" value="<?php echo htmlspecialchars($links[$j]['url']); ?>" maxlength="2048" placeholder="(Sense enllaç)"/>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $links[$j]['id']; ?>"/>
															</td>
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-resolution-<?php echo $j+1; ?>" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-resolution-<?php echo $j+1; ?>" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($links[$j]['resolution']); ?>" maxlength="200" placeholder="- Tria -"/>
															</td>
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-comments-<?php echo $j+1; ?>" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($links[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-lost-<?php echo $j+1; ?>" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-lost-<?php echo $j+1; ?>" type="checkbox" value="1""<?php echo empty($links[$j]['url']) ? ' checked' : ''; ?>/>
															</td>
															<td class="text-center align-middle">
																<button id="form-links-list-<?php echo $episodes[$i]['id']; ?>-delete-<?php echo $j+1; ?>" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
		}
		if (count($links)==0) {
?>
														<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1">
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-link-1" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-link-1" type="url" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)"/>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-id-1" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-id-1" type="hidden" value="-1"/>
															</td>
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-resolution-1" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-resolution-1" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/>
															</td>
															<td>
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-comments-1" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-comments-1" type="text" class="form-control" value="" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<input id="form-links-list-<?php echo $episodes[$i]['id']; ?>-lost-1" name="form-links-list-<?php echo $episodes[$i]['id']; ?>-lost-1" type="checkbox" value="1"/>
															</td>
															<td class="text-center align-middle">
																<button id="form-links-list-<?php echo $episodes[$i]['id']; ?>-delete-1" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
		}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un altre enllaç</button></div>
										</div>
									</div>
								</div>
<?php
	}
?>
							</div>
						</div>
						<div class="form-group">
							<label for="form-extras-list">Enllaços extra</label>
							<div class="container" id="form-extras-list">
<?php

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$resultex = query("SELECT l.* FROM link l WHERE l.version_id=".escape($_GET['id'])." AND l.episode_id IS NULL ORDER BY l.extra_name ASC, l.resolution ASC");
		$extras = array();
		while ($rowex = mysqli_fetch_assoc($resultex)) {
			array_push($extras, $rowex);
		}
		mysqli_free_result($resultex);
	} else {
		$extras=array();
	}
?>
								<div class="form-group">
									<div class="container" id="form-extras-list">
										<div class="row mb-3">
											<div class="w-100 column">
												<table class="table table-bordered table-hover table-sm" id="extras-list-table" data-count="<?php echo count($extras); ?>">
													<thead>
														<tr>
															<th style="width: 20%;" class="mandatory">Nom</th>
															<th class="mandatory">Enllaç de streaming</th>
															<th style="width: 15%;">Resolució</th>
															<th style="width: 15%;">Comentaris</th>
															<th class="text-center" style="width: 5%;">Acció</th>
														</tr>
													</thead>
													<tbody>
														<tr id="extras-list-table-empty" class="<?php echo count($extras)>0 ? 'd-none' : ''; ?>">
															<td colspan="5" class="text-center">- No hi ha cap extra -</td>
														</tr>
<?php
	for ($j=0;$j<count($extras);$j++) {
?>
														<tr id="form-extras-list-row-<?php echo $j+1; ?>">
															<td>
																<input id="form-extras-list-name-<?php echo $j+1; ?>" name="form-extras-list-name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['extra_name']); ?>" maxlength="200" required placeholder="- Introdueix un nom -"/>
																<input id="form-extras-list-id-<?php echo $j+1; ?>" name="form-extras-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['id']; ?>"/>
															</td>
															<td>
																<input id="form-extras-list-link-<?php echo $j+1; ?>" name="form-extras-list-link-<?php echo $j+1; ?>" type="url" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['url']); ?>" maxlength="2048" required placeholder="- Introdueix un enllaç -"/>
															</td>
															<td>
																<input id="form-extras-list-resolution-<?php echo $j+1; ?>" name="form-extras-list-resolution-<?php echo $j+1; ?>" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($extras[$j]['resolution']); ?>" maxlength="200" placeholder="- Tria -"/>
															</td>
															<td>
																<input id="form-extras-list-comments-<?php echo $j+1; ?>" name="form-extras-list-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<button id="form-extras-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionExtraRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
	}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionExtraRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un altre enllaç extra</button></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group text-center pt-2">
<?php
	if (!empty($row['id'])) {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_count_as_update" id="form-do_not_count_as_update" value="1">
								<label class="form-check-label" for="form-do_not_count_as_update">No moguis a "Darreres actualitzacions"</label>
							</div>
							<br />
<?php
	}
?>
							<button type="submit" name="action" value="<?php echo $row['id']!=NULL? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la versió"; ?></button>
						</div>
					</form>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
