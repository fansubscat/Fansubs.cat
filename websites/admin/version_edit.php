<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Edició de versions d'anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Edició versions de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Edició de versions d'acció real - Acció real";
		$page="liveaction";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$content="anime";
		$content_uc="Anime";
		$content_prep="de l'anime";
		$division_name="Temporada";
		$division_prep="de les temporades";
		$division_some_completed="alguna temporada completada";
		$division_one="una";
		$division_many="moltes";
		$division_pl="temporades";
		$division_pl_expanded="les temporades desplegades";
		$series_name="sèries";
		break;
	case 'manga':
		$content="manga";
		$content_uc="Manga";
		$content_prep="del manga";
		$division_name="Volum";
		$division_prep="dels volums";
		$division_some_completed="algun volum completat";
		$division_one="un";
		$division_many="molts";
		$division_pl="volums";
		$division_pl_expanded="els volums desplegats";
		$series_name="serialitzats";
		break;
	case 'liveaction':
		$content="contingut d'acció real";
		$content_uc="Contingut d'acció real";
		$content_prep="del contingut d'acció real";
		$division_name="Temporada";
		$division_prep="de les temporades";
		$division_some_completed="alguna temporada completada";
		$division_one="una";
		$division_many="moltes";
		$division_pl="temporades";
		$division_pl_expanded="les temporades desplegades";
		$series_name="sèries";
		break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['series_id']) && is_numeric($_POST['series_id'])) {
			$data['series_id']=escape($_POST['series_id']);
		} else {
			crash("Dades invàlides: manca series_id");
		}
		if (!empty($_POST['status']) && is_numeric($_POST['status'])) {
			$data['status']=escape($_POST['status']);
		} else {
			crash("Dades invàlides: manca status");
		}
		if (!empty($_POST['fansub_1']) && is_numeric($_POST['fansub_1'])) {
			$data['fansub_1']=escape($_POST['fansub_1']);
		} else {
			crash("Dades invàlides: manca fansub_1");
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
		if (!empty($_POST['show_divisions'])){
			$data['show_divisions']=1;
		} else {
			$data['show_divisions']=0;
		}
		if (!empty($_POST['show_expanded_divisions'])){
			$data['show_expanded_divisions']=1;
		} else {
			$data['show_expanded_divisions']=0;
		}
		if (!empty($_POST['show_episode_numbers'])){
			$data['show_episode_numbers']=1;
		} else {
			$data['show_episode_numbers']=0;
		}
		if (!empty($_POST['show_unavailable_episodes'])){
			$data['show_unavailable_episodes']=1;
		} else {
			$data['show_unavailable_episodes']=0;
		}
		if (!empty($_POST['show_expanded_extras'])){
			$data['show_expanded_extras']=1;
		} else {
			$data['show_expanded_extras']=0;
		}
		if (!empty($_POST['order_type'])){
			$data['order_type']=escape($_POST['order_type']);
		} else {
			$data['order_type']=0;
		}
		if (!empty($_POST['storage_folder'])) {
			$data['storage_folder']=escape($_POST['storage_folder']);
		} else if ($type!='manga') {
			crash("Dades invàlides: manca storage_folder");
		}
		if (!empty($_POST['storage_processing'])) {
			$data['storage_processing']=escape($_POST['storage_processing']);
		} else {
			$data['storage_processing']=0;
		}
		if (!empty($_POST['default_resolution'])) {
			$data['default_resolution']="'".escape($_POST['default_resolution'])."'";
		} else {
			$data['default_resolution']="NULL";
		}
		if (!empty($_POST['version_author'])) {
			$data['version_author']="'".escape($_POST['version_author'])."'";
		} else {
			$data['version_author']="NULL";
		}

		$divisions=array();

		$resultd = query("SELECT d.* FROM division d WHERE d.series_id=".$data['series_id']);

		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}

		mysqli_free_result($resultd);

		$files=array();
		$episodes=array();

		$resulte = query("SELECT e.* FROM episode e WHERE e.series_id=".$data['series_id']);

		$data['is_missing_episodes']=0; //By default.. will be calculated depending on files
		
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			$episode_id=$rowe['id'];

			$episode = array();
			$episode['id'] = $episode_id;

			if (!empty($_POST['form-files-list-'.$episode_id.'-title'])) {
				$episode['title'] = "'".escape($_POST['form-files-list-'.$episode_id.'-title'])."'";
			} else {
				$episode['title'] = "NULL";
			}
			array_push($episodes, $episode);
			
			$i=1;
			while (!empty($_POST['form-files-list-'.$episode_id.'-id-'.$i])) {
				$file = array();
				if (is_numeric($_POST['form-files-list-'.$episode_id.'-id-'.$i])) {
					$file['id']=escape($_POST['form-files-list-'.$episode_id.'-id-'.$i]);
				} else {
					crash("Dades invàlides: manca id del fitxer");
				}
				if (is_uploaded_file($_FILES['form-files-list-'.$episode_id.'-file-'.$i]['tmp_name'])) {
					$file['original_filename']="'".escape($_FILES['form-files-list-'.$episode_id.'-file-'.$i]["name"])."'";
					$file['original_filename_unescaped']=$_FILES['form-files-list-'.$episode_id.'-file-'.$i]['name'];
					$file['temporary_filename']=$_FILES['form-files-list-'.$episode_id.'-file-'.$i]['tmp_name'];
				} else {
					$file['original_filename']='NULL';
				}
				if (!empty($_POST['form-files-list-'.$episode_id.'-variant_name-'.$i])) {
					$file['variant_name']="'".escape($_POST['form-files-list-'.$episode_id.'-variant_name-'.$i])."'";
				} else {
					$file['variant_name']="NULL";
				}
				if (!empty($_POST['form-files-list-'.$episode_id.'-comments-'.$i])) {
					$file['comments']="'".escape($_POST['form-files-list-'.$episode_id.'-comments-'.$i])."'";
				} else {
					$file['comments']="NULL";
				}
				if (!empty($_POST['form-files-list-'.$episode_id.'-length-'.$i])) {
					//This works for manga too because if the format is not in HH:MM:SS, the value is returned directly
					$file['length']=escape(convert_from_hh_mm_ss($_POST['form-files-list-'.$episode_id.'-length-'.$i]));
				} else {
					$file['length']="NULL";
				}
				$file['episode_id']=$episode_id;

				$file['links'] = array();
				$j=1;
				$has_url=FALSE;
				while (!empty($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-id'])) {
					$link = array();
					$link['id'] = $_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-id'];
					if (!empty($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-url'])) {
						$link['url']="'".escape($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-url'])."'";
					} else {
						$link['url']="NULL";
					}
					if (!empty($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-resolution'])) {
						$link['resolution']="'".escape($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-resolution'])."'";
					} else {
						$link['resolution']="NULL";
					}

					if ($link['url']!="NULL") {
						array_push($file['links'], $link);
						$has_url=TRUE;
					}
					$j++;
				}

				if (!empty($_POST['form-files-list-'.$episode_id.'-is_lost-'.$i]) && !($has_url || ($type=='manga' && $file['length']!='NULL'))) {
					$file['is_lost']=1;
					$file['length']='NULL';
					$data['is_missing_episodes']=1;
				} else {
					$file['is_lost']=0;
				}

				if (($has_url || $file['original_filename']!='NULL') && $file['length']=="NULL"){
					crash("Dades invàlides: manca length del fitxer");
				}

				if ($has_url || ($type=='manga' && ($file['original_filename']!='NULL' || $file['id']!=-1)) || $file['is_lost']==1) {
					array_push($files, $file);
				}
				$i++;
			}
		}
		if (!empty($files)) {
			$data['is_hidden']=0;
		} else {
			$data['is_hidden']=1;
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
			if (is_uploaded_file($_FILES['form-extras-list-file-'.$i]['tmp_name'])) {
				$extra['original_filename']=escape($_FILES['form-extras-list-file-'.$i]["name"]);
				$extra['original_filename_unescaped']=$_FILES['form-extras-list-file-'.$i]['name'];
				$extra['temporary_filename']=$_FILES['form-extras-list-file-'.$i]['tmp_name'];
			} else if ($type=='manga' && $extra['id']==-1) {
				crash("Dades invàlides: manca fitxer de l'extra");
			}
			if (!empty($_POST['form-extras-list-length-'.$i])) {
				//This works for manga too because if the format is not in HH:MM:SS, the value is returned directly
				$extra['length']=escape(convert_from_hh_mm_ss($_POST['form-extras-list-length-'.$i]));
			} else {
				crash("Dades invàlides: manca length de l'extra");
			}
			if (!empty($_POST['form-extras-list-comments-'.$i])) {
				$extra['comments']="'".escape($_POST['form-extras-list-comments-'.$i])."'";
			} else {
				$extra['comments']="NULL";
			}

			$extra['links'] = array();
			$j=1;
			$has_url=FALSE;
			while (!empty($_POST['form-extras-list-'.$i.'-link-'.$j.'-id'])) {
				$link = array();
				$link['id'] = $_POST['form-extras-list-'.$i.'-link-'.$j.'-id'];
				if (!empty($_POST['form-extras-list-'.$i.'-link-'.$j.'-id'])) {
					$link['url']="'".escape($_POST['form-extras-list-'.$i.'-link-'.$j.'-url'])."'";
				} else {
					$link['url']="NULL";
				}
				if (!empty($_POST['form-extras-list-'.$i.'-link-'.$j.'-resolution'])) {
					$link['resolution']="'".escape($_POST['form-extras-list-'.$i.'-link-'.$j.'-resolution'])."'";
				} else {
					$link['resolution']="NULL";
				}

				if ($link['url']!="NULL") {
					array_push($extra['links'], $link);
					$has_url=TRUE;
				}
				$j++;
			}

			if ($has_url || ($type=='manga' && ($extra['original_filename']!=NULL || $extra['id']!=-1))) {
				array_push($extras, $extra);
			}
			$i++;
		}

		$remote_folders=array();
		$i=1;
		while (!empty($_POST['form-remote_folders-list-id-'.$i])) {
			$remote_folder = array();
			if (is_numeric($_POST['form-remote_folders-list-id-'.$i])) {
				$remote_folder['id']=escape($_POST['form-remote_folders-list-id-'.$i]);
			} else {
				crash("Dades invàlides: manca id de la carpeta remota");
			}
			if (!empty($_POST['form-remote_folders-list-remote_account_id-'.$i])) {
				$remote_folder['remote_account_id']=escape($_POST['form-remote_folders-list-remote_account_id-'.$i]);
			} else {
				crash("Dades invàlides: manca remote_account_id de la carpeta");
			}
			if (!empty($_POST['form-remote_folders-list-folder-'.$i])) {
				$remote_folder['folder']=escape($_POST['form-remote_folders-list-folder-'.$i]);
			} else {
				crash("Dades invàlides: manca folder de la carpeta remota");
			}
			if (!empty($_POST['form-remote_folders-list-division_id-'.$i])) {
				$remote_folder['division_id']=escape($_POST['form-remote_folders-list-division_id-'.$i]);
			} else {
				$remote_folder['division_id']="NULL";
			}
			if (!empty($_POST['form-remote_folders-list-is_active-'.$i]) && $_POST['form-remote_folders-list-is_active-'.$i]==1) {
				$remote_folder['is_active']=1;
			} else {
				$remote_folder['is_active']=0;
			}
			array_push($remote_folders, $remote_folder);
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			//Completed version: check if we are completing it now
			//If it is being completed now, set the date to now,
			//otherwise keep the date if it's complete, or set it to null if not completed
			$completed_date = 'NULL';
			if ($data['status']==1) {
				$result_old=query("SELECT completed_date FROM version WHERE id=".$data['id']);
				if ($rowov = mysqli_fetch_assoc($result_old)) {
					$completed_date = empty($rowov['completed_date']) ? 'CURRENT_TIMESTAMP' : "'".$rowov['completed_date']."'";
				}
				mysqli_free_result($result_old);
			}

			log_action("update-version", "S'ha actualitzat una versió de '".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."' (id. de versió: ".$data['id'].")");
			query("UPDATE version SET status=".$data['status'].",is_missing_episodes=".$data['is_missing_episodes'].",is_featurable=".$data['is_featurable'].",is_always_featured=".$data['is_always_featured'].",show_divisions=".$data['show_divisions'].",show_expanded_divisions=".$data['show_expanded_divisions'].",show_episode_numbers=".$data['show_episode_numbers'].",show_unavailable_episodes=".$data['show_unavailable_episodes'].",show_expanded_extras=".$data['show_expanded_extras'].",order_type=".$data['order_type'].",is_hidden=".$data['is_hidden'].",completed_date=$completed_date,storage_folder='".$data['storage_folder']."',storage_processing=".$data['storage_processing'].",default_resolution=".$data['default_resolution'].",version_author=".$data['version_author'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
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
			foreach ($files as $file) {
				if ($file['id']!=-1) {
					array_push($ids,$file['id']);
				}
			}
			//Links will be removed too because their FK is set to cascade
			//Views will NOT be removed in order to keep consistent stats history
			query("DELETE FROM file WHERE version_id=".$data['id']." AND episode_id IS NOT NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			//We do not count removing files as updating them, only insertions and real updates
			foreach ($files as $file) {
				if ($file['id']==-1) {
					query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,is_lost,created,created_by,updated,updated_by) VALUES (".$data['id'].",".$file['episode_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['length'].",".$file['comments'].",".$file['is_lost'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
					$inserted_file_id=mysqli_insert_id($db_connection);
					if ($type=='manga') {
						if ($file['original_filename']!='NULL') {
							decompress_manga_file($inserted_file_id, $file['temporary_filename'], $file['original_filename_unescaped']);
						}
					} else {
						foreach ($file['links'] as $link) {
							query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES ($inserted_file_id,".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
						}
					}
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					query("UPDATE file SET ".($file['original_filename']!='NULL' ? "original_filename=".$file['original_filename']."," : "")."variant_name=".$file['variant_name'].",length=".$file['length'].",comments=".$file['comments'].",is_lost=".$file['is_lost'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$file['id']);

					if ($type=='manga') {
						$resultcr = query("SELECT * FROM file WHERE id=".$file['id']);
						if ($current_file = mysqli_fetch_assoc($resultcr)) {
							$has_updated_files=($file['original_filename']!='NULL' && (empty($current_file['original_filename']) ? "NULL" : "'".escape($current_file['original_filename'])."'")!=$file['original_filename']);
						}
						mysqli_free_result($resultcr);
						if ($file['original_filename']!='NULL') {
							decompress_manga_file($file['id'], $file['temporary_filename'], $file['original_filename_unescaped']);
							query("UPDATE file SET updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$file['id']);
						}
					} else {
						$has_updated_files = FALSE;
						$link_ids=array();
						$has_updated_mega_link=FALSE;
						$has_updated_storage_link=FALSE;
						foreach ($file['links'] as $link) {
							if ($link['id']==-1) {
								query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$file['id'].",".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
								array_push($link_ids,mysqli_insert_id($db_connection));
								$has_updated_files = TRUE;
								if (strpos($link['url'], 'https://mega.nz/')!==FALSE) {
									$has_updated_mega_link=TRUE;
								} else if (strpos($link['url'], 'storage://')!==FALSE) {
									$has_updated_storage_link=TRUE;
								} 
							} else {
								$resoi = query("SELECT * FROM link WHERE id=".$link['id']);
								$old_link = mysqli_fetch_assoc($resoi);
								mysqli_free_result($resoi);
								if ($old_link) {
									query("UPDATE link SET url=".$link['url'].",resolution=".$link['resolution'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$link['id']);
									array_push($link_ids,$link['id']);
									if ("'".escape($old_link['url'])."'"!=$link['url']) {
										if (strpos($link['url'], 'https://mega.nz/')!==FALSE) {
											$has_updated_mega_link=TRUE;
										} else if (strpos($link['url'], 'storage://')!==FALSE) {
											$has_updated_storage_link=TRUE;
										} 
									}
								}
							}
						}

						//If there is any new MEGA link and storage has been updated (no new link or no changes), delete all storages so they are recreated
						if (empty($_POST['do_not_recreate_storage_links']) && $has_updated_mega_link && !$has_updated_storage_link) {
							query("DELETE FROM link WHERE file_id=".$file['id']." AND url LIKE 'storage://%'");
						}

						//Remove the ones that are no more in the form
						query("DELETE FROM link WHERE file_id=".$file['id']." AND id NOT IN (".(count($link_ids)>0 ? implode(',',$link_ids) : "-1").")");
					}

					if (empty($_POST['do_not_count_as_update']) && $has_updated_files) {
						query("UPDATE version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				}
			}

			$ids=array();
			foreach ($extras as $extra) {
				if ($extra['id']!=-1) {
					array_push($ids,$extra['id']);
				}
			}
			//Views and links will be removed too because their FK is set to cascade
			query("DELETE FROM file WHERE version_id=".$data['id']." AND episode_id IS NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($extras as $extra) {
				if ($extra['id']==-1) {
					query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,created,created_by,updated,updated_by) VALUES (".$data['id'].",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['length'].",".$extra['comments'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
					$inserted_file_id=mysqli_insert_id($db_connection);
					if ($type=='manga') {
						decompress_manga_file($inserted_file_id, $extra['temporary_filename'], $extra['original_filename_unescaped']);
					} else {
						foreach ($extra['links'] as $link) {
							query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES ($inserted_file_id,".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
						}
					}
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					query("UPDATE file SET extra_name='".$extra['name']."',".($extra['original_filename']!=NULL ? "original_filename='".$extra['original_filename']."'," : "")."length=".$extra['length'].",comments=".$extra['comments'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$extra['id']);

					if ($type=='manga') {
						$resultcr = query("SELECT * FROM file WHERE id=".$extra['id']);
						if ($current_extra = mysqli_fetch_assoc($resultcr)) {
							query("UPDATE file SET extra_name='".$extra['name']."',".($extra['original_filename']!=NULL ? "original_filename='".$extra['original_filename']."'," : "")."length=".$extra['length'].",comments=".$extra['comments'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$extra['id']);
						}
						mysqli_free_result($resultcr);
						if ($extra['original_filename']!=NULL) {
							decompress_manga_file($extra['id'], $extra['temporary_filename'], $extra['original_filename_unescaped']);
						}
					} else {
						$link_ids=array();
						$has_updated_mega_link=FALSE;
						$has_updated_storage_link=FALSE;
						foreach ($extra['links'] as $link) {
							if ($link['id']==-1) {
								query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$extra['id'].",".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
								array_push($link_ids,mysqli_insert_id($db_connection));
								if (strpos($link['url'], 'https://mega.nz/')!==FALSE) {
									$has_updated_mega_link=TRUE;
								} else if (strpos($link['url'], 'storage://')!==FALSE) {
									$has_updated_storage_link=TRUE;
								} 
							} else {
								$resoi = query("SELECT * FROM link WHERE id=".$link['id']);
								$old_link = mysqli_fetch_assoc($resoi);
								mysqli_free_result($resoi);
								if ($old_link) {
									query("UPDATE link SET url=".$link['url'].",resolution=".$link['resolution'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$link['id']);
									array_push($link_ids,$link['id']);
									if ("'".escape($old_link['url'])."'"!=$link['url']) {
										if (strpos($link['url'], 'https://mega.nz/')!==FALSE) {
											$has_updated_mega_link=TRUE;
										} else if (strpos($link['url'], 'storage://')!==FALSE) {
											$has_updated_storage_link=TRUE;
										} 
									}
								}
							}
						}

						//If there is any new MEGA link and storage has been updated (no new link or no changes), delete all storages so they are recreated
						if ($has_updated_mega_link && !$has_updated_storage_link) {
							query("DELETE FROM link WHERE file_id=".$extra['id']." AND url LIKE 'storage://%'");
						}

						//Remove the ones that are no more in the form
						query("DELETE FROM link WHERE file_id=".$extra['id']." AND id NOT IN (".(count($link_ids)>0 ? implode(',',$link_ids) : "-1").")");
					}
				}
			}

			$ids=array();
			foreach ($remote_folders as $remote_folder) {
				if ($remote_folder['id']!=-1) {
					array_push($ids,$remote_folder['id']);
				}
			}
			query("DELETE FROM remote_folder WHERE version_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($remote_folders as $remote_folder) {
				if ($remote_folder['id']==-1) {
					query("INSERT INTO remote_folder (version_id,remote_account_id,folder,division_id,is_active,created,created_by,updated,updated_by) VALUES (".$data['id'].",".$remote_folder['remote_account_id'].",'".$remote_folder['folder']."',".$remote_folder['division_id'].",".$remote_folder['is_active'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
				} else {
					query("UPDATE remote_folder SET remote_account_id=".$remote_folder['remote_account_id'].",folder='".$remote_folder['folder']."',division_id=".$remote_folder['division_id'].",is_active=".$remote_folder['is_active'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$remote_folder['id']);
				}
			}

			foreach ($divisions as $division) {
				if (is_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'], $static_directory."/images/divisions/".$data['id']."_".$division['id'].".jpg");
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-version", "S'ha creat una versió de '".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."'");
			query("INSERT INTO version (series_id,status,is_missing_episodes,is_featurable,is_always_featured,show_divisions,show_expanded_divisions,show_episode_numbers,show_unavailable_episodes,show_expanded_extras,order_type,is_hidden,completed_date,storage_folder,storage_processing,default_resolution,version_author,files_updated,files_updated_by,created,created_by,updated,updated_by) VALUES (".$data['series_id'].",".$data['status'].",".$data['is_missing_episodes'].",".$data['is_featurable'].",".$data['is_always_featured'].",".$data['show_divisions'].",".$data['show_expanded_divisions'].",".$data['show_episode_numbers'].",".$data['show_unavailable_episodes'].",".$data['show_expanded_extras'].",".$data['order_type'].",".$data['is_hidden'].",".($data['status']==1 ? 'CURRENT_TIMESTAMP' : 'NULL').",'".$data['storage_folder']."',".$data['storage_processing'].",".$data['default_resolution'].",".$data['version_author'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			if ($data['fansub_1']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_1'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_2']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_2'].",".$data['downloads_url_2'].")");
			}
			if ($data['fansub_3']!=NULL) {
				query("INSERT INTO rel_version_fansub (version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_3'].",".$data['downloads_url_3'].")");
			}
			foreach ($episodes as $episode) {
				if ($episode['title']!="NULL") {
					query("INSERT INTO episode_title (version_id,episode_id,title) VALUES (".$inserted_id.",".$episode['id'].",".$episode['title'].")");
				}
			}
			foreach ($files as $file) {
				query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,is_lost,created,created_by,updated,updated_by) VALUES (".$inserted_id.",".$file['episode_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['length'].",".$file['comments'].",".$file['is_lost'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
				$inserted_file_id=mysqli_insert_id($db_connection);
				if ($type=='manga') {
					if ($file['original_filename']!='NULL') {
						decompress_manga_file($inserted_file_id, $file['temporary_filename'], $file['original_filename_unescaped']);
					}
				} else {
					foreach ($file['links'] as $link) {
						query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$inserted_file_id.",".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
					}
				}
			}
			foreach ($extras as $extra) {
				query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,created,created_by,updated,updated_by) VALUES (".$inserted_id.",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['length'].",".$extra['comments'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
				$inserted_file_id=mysqli_insert_id($db_connection);
				if ($type=='manga') {
					decompress_manga_file($inserted_file_id, $extra['temporary_filename'], $extra['original_filename_unescaped']);
				} else {
					foreach ($extra['links'] as $link) {
						query("INSERT INTO link (file_id,url,resolution,created) VALUES (".$inserted_file_id.",".$link['url'].",".$link['resolution'].",CURRENT_TIMESTAMP)");
					}
				}
			}
			foreach ($remote_folders as $remote_folder) {
				query("INSERT INTO remote_folder (version_id,remote_account_id,folder,division_id,is_active,created,created_by,updated,updated_by) VALUES (".$inserted_id.",".$remote_folder['remote_account_id'].",'".$remote_folder['folder']."',".$remote_folder['division_id'].",".$remote_folder['is_active'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			}

			foreach ($divisions as $division) {
				if (is_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'], $static_directory."/images/divisions/".$inserted_id."_".$volume['id'].".jpg");
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}

		header("Location: version_list.php?type=$type");
		die();
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT v.* FROM version v WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Version not found');
		mysqli_free_result($result);

		$results = query("SELECT s.* FROM series s WHERE id=".$row['series_id']);
		$series = mysqli_fetch_assoc($results) or crash('Series not found');
		mysqli_free_result($results);

		$resultd = query("SELECT d.* FROM division d WHERE d.series_id=".$row['series_id']." ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$has_independent_fansub = FALSE;
		$fansubs = array();

		$resultf = query("SELECT fansub_id, downloads_url FROM rel_version_fansub vf WHERE vf.version_id=".$row['id']);

		while ($rowf = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, array($rowf['fansub_id'], $rowf['downloads_url']));
			if ($rowf['fansub_id']==$default_fansub_id) {
				$has_independent_fansub = TRUE;
			}
		}
		mysqli_free_result($resultf);

		$resulte = query("SELECT e.*, et.title, TRIM(d.number)+0 division_number, d.name division_name FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".escape($_GET['id'])." WHERE e.series_id=".$row['series_id']." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
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

		if ($series['subtype']=='movie' || $series['subtype']=='oneshot') {
			$row['show_divisions']=1;
			$row['show_expanded_divisions']=1;
			$row['show_expanded_extras']=1;
			$row['show_episode_numbers']=0;
			$row['show_unavailable_episodes']=1;
			$row['order_type']=0;
			$row['storage_processing']=1;
		} else {
			$row['show_divisions']=1;
			$row['show_expanded_divisions']=1;
			$row['show_expanded_extras']=1;
			$row['show_episode_numbers']=1;
			$row['show_unavailable_episodes']=1;
			$row['order_type']=0;
			$row['storage_processing']=1;
		}

		$has_independent_fansub = FALSE;
		$fansubs = array();

		$resultd = query("SELECT d.id, d.series_id, TRIM(d.number)+0 number, d.name, d.number_of_episodes, d.external_id FROM division d WHERE d.series_id=".escape($_GET['series_id'])." ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$resulte = query("SELECT e.*, NULL title, TRIM(d.number)+0 division_number, d.name division_name FROM episode e LEFT JOIN division d ON e.division_id=d.id WHERE e.series_id=".escape($_GET['series_id'])." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
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
					<form method="post" action="version_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfLinks()">
						<div class="form-group">
							<label for="form-series" class="mandatory"><?php echo $content_uc; ?></label>
							<div id="form-series" class="font-weight-bold form-control"><?php echo htmlspecialchars($series['name']); ?></div>
							<input name="series_id" type="hidden" value="<?php echo $series['id']; ?>"/>
							<input id="series_subtype" type="hidden" value="<?php echo $series['subtype']; ?>"/>
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
							<input type="hidden" id="type" value="<?php echo $type; ?>">
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
									<input id="form-downloads_url_2" name="downloads_url_2" type="url" class="form-control" value="<?php echo (count($fansubs)>1 ? htmlspecialchars($fansubs[1][1]) : ''); ?>" maxlength="200" <?php echo (count($fansubs)>1 ? '' : ' disabled'); ?>/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_3">Enllaç de baixada dels fitxers originals 3<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_3" name="downloads_url_3" type="url" class="form-control" value="<?php echo (count($fansubs)>2 ? htmlspecialchars($fansubs[2][1]) : ''); ?>" maxlength="200" <?php echo (count($fansubs)>2 ? '' : ' disabled'); ?>/>
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
										<option value="3"<?php echo $row['status']==3 ? " selected" : ""; ?>>Parcialment completada (<?php echo $division_some_completed; ?>)</option>
										<option value="4"<?php echo $row['status']==4 ? " selected" : ""; ?>>Abandonada</option>
										<option value="5"<?php echo $row['status']==5 ? " selected" : ""; ?>>Cancel·lada</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-version_author">Autor de la versió <small class="text-muted">(per a fansubs independents)</small></label>
									<input id="form-version_author" name="version_author" type="text" class="form-control" value="<?php echo htmlspecialchars($row['version_author']); ?>" maxlength="200"<?php echo $has_independent_fansub ? '' : ' disabled'; ?>/>
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
											<input class="form-check-input" type="checkbox" name="is_always_featured" id="form-is_always_featured" value="1"<?php echo $row['is_always_featured']==1? " checked" : ""; ?> onchange="if (this.checked) {if (!confirm('Recorda que aquesta opció és només per a sèries de temporada o casos especials. No tindrà efecte fins al pròxim dilluns. Segur que la vols marcar com a “recomanada sempre“?')) this.checked=''; };">
											<label class="form-check-label" for="form-is_always_featured">Mostra-la sempre com a recomanada</label>
										</div>
									</div>
								</div>
							</div>
						</div>
<?php
	if ($type!='manga') {
?>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-storage_folder"><span class="mandatory">Carpeta d'emmagatzematge</span><br /><small class="text-muted">(modifica-la només si saps què fas; s'hi copiaran els fitxers)</small></label>
									<input id="form-storage_folder" name="storage_folder" type="text" class="form-control" value="<?php echo $row['storage_folder']; ?>" maxlength="200" required<?php echo (!empty($row['id']) && empty($row['is_hidden'])) ? ' readonly' : '' ; ?>/>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-default_resolution">Resolució per defecte<br /><small class="text-muted">(per a la importació automàtica d'enllaços)</small></label>
									<input id="form-default_resolution" name="default_resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($row['default_resolution']); ?>" maxlength="200" placeholder="- Selecciona o introdueix una resolució -"/>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-storage_processing"><span class="mandatory">Processament previ</span><br /><small class="text-muted">(com s'importen els fitxers a l'emmagatzematge)</small></label>
									<select name="storage_processing" class="form-control" onchange="if(!confirm('Modificar aquesta opció pot provocar que els vídeos no es puguin reproduir correctament. Canvia-la només si tens el permís d‘un administrador. En cas contrari, deixa-la a “Recomprimeix el vídeo i l’àudio”. Vols mantenir el canvi?')) this.selectedIndex=0;">
										<option value="1"<?php echo $row['storage_processing']==1 ? " selected" : ""; ?>>Recomprimeix el vídeo i l'àudio</option>
										<option value="0"<?php echo $row['storage_processing']==0 ? " selected" : ""; ?>>Recomprimeix el vídeo, copia l'àudio</option>
										<option value="2"<?php echo $row['storage_processing']==2 ? " selected" : ""; ?>>Recomprimeix l'àudio, copia el vídeo</option>
										<option value="3"<?php echo $row['storage_processing']==3 ? " selected" : ""; ?>>No recomprimeixis res (regenera l'MP4)</option>
										<option value="4"<?php echo $row['storage_processing']==4 ? " selected" : ""; ?>>Copia sense cap canvi (còpia 1:1)</option>
										<option value="5"<?php echo $row['storage_processing']==5 ? " selected" : ""; ?>>Omet l'emmagatzematge local (còpia 1:1)</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-remote_folders-list">Carpetes remotes <small class="text-muted">(per a l'obtenció automàtica d'enllaços)</small></label>
							<div class="container" id="form-remote_folders-list">
<?php

		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$resultfo = query("SELECT f.*, ra.type FROM remote_folder f LEFT JOIN remote_account ra ON f.remote_account_id=ra.id WHERE f.version_id=".escape($_GET['id'])." ORDER BY f.id ASC");
			$remote_folders = array();
			while ($rowfo = mysqli_fetch_assoc($resultfo)) {
				array_push($remote_folders, $rowfo);
			}
			mysqli_free_result($resultfo);
		} else {
			$remote_folders=array();
		}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-remote_folders-list-remote_account_id-XXX" name="form-remote_folders-list-remote_account_id-XXX" onchange="if ($(this).find('option:selected').eq(0).hasClass('not-syncable')){$('#form-remote_folders-list-is_active-XXX').prop('disabled',true);} else { $('#form-remote_folders-list-is_active-XXX').prop('disabled',false); }" class="form-control d-none">
											<option value="">- Selecciona un compte remot -</option>
<?php
		if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
			$extra_where = ' AND a.fansub_id='.$_SESSION['fansub_id'].' OR a.fansub_id IS NULL';
		} else {
			$extra_where = '';
		}

		$resulta = query("SELECT a.* FROM remote_account a WHERE a.type<>'storage'$extra_where ORDER BY a.type='storage' DESC, a.name ASC");
		while ($arow = mysqli_fetch_assoc($resulta)) {
?>
											<option value="<?php echo $arow['id']; ?>"<?php echo $arow['type']=='storage' ? ' class="not-syncable"' : ''; ?>><?php echo ($arow['type']=='mega' ? 'MEGA' : 'Emmagatzematge').': '.htmlspecialchars($arow['name']); ?></option>
<?php
		}
		mysqli_free_result($resulta);
?>
										</select>
										<select id="form-remote_folders-list-division_id-XXX" name="form-remote_folders-list-division_id-XXX" class="form-control d-none">
											<option value="">- Qualsevol -</option>
<?php
		$resultss = query("SELECT d.id, d.series_id, TRIM(d.number)+0 number, d.name, d.number_of_episodes, d.external_id FROM division d WHERE d.series_id=".$series['id']." ORDER BY d.number ASC");
		while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
											<option value="<?php echo $ssrow['id']; ?>"><?php echo htmlspecialchars($ssrow['number'].(!empty($ssrow['name']) ? ' ('.$ssrow['name'].')' : '')); ?></option>
<?php
		}
		mysqli_free_result($resultss);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="remote_folders-list-table" data-count="<?php echo count($remote_folders); ?>">
											<thead>
												<tr>
													<th style="width: 25%;" class="mandatory">Compte</th>
													<th class="mandatory">Carpeta</th>
													<th style="width: 15%;"><?php echo $division_name; ?></th>
													<th class="text-center" style="width: 10%;">Sincronitza</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="remote_folders-list-table-empty" class="<?php echo count($remote_folders)>0 ? 'd-none' : ''; ?>">
													<td colspan="5" class="text-center">- No hi ha configurada cap carpeta -</td>
												</tr>
<?php
		for ($j=0;$j<count($remote_folders);$j++) {
?>
												<tr id="form-remote_folders-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" name="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" onchange="if ($(this).find('option:selected').eq(0).hasClass('not-syncable')){$('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',true);} else { $('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',false); }" class="form-control" required>
															<option value="">- Selecciona un compte remot -</option>
<?php
			if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
				$extra_where = ' AND a.fansub_id='.$_SESSION['fansub_id'].' OR a.fansub_id IS NULL';
			} else {
				$extra_where = '';
			}

			$resulta = query("SELECT a.* FROM remote_account a WHERE a.type<>'storage'$extra_where ORDER BY a.type='storage' DESC, a.name ASC");
			while ($arow = mysqli_fetch_assoc($resulta)) {
?>
															<option value="<?php echo $arow['id']; ?>"<?php echo $remote_folders[$j]['remote_account_id']==$arow['id'] ? " selected" : ""; ?><?php echo $arow['type']=='storage' ? ' class="not-syncable"' : ''; ?>><?php echo ($arow['type']=='mega' ? 'MEGA' : 'Emmagatzematge').': '.htmlspecialchars($arow['name']); ?></option>
<?php
			}
			mysqli_free_result($resulta);
?>
														</select>
														<input id="form-remote_folders-list-id-<?php echo $j+1; ?>" name="form-remote_folders-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $remote_folders[$j]['id']; ?>"/>
													</td>
													<td>
														<input id="form-remote_folders-list-folder-<?php echo $j+1; ?>" name="form-remote_folders-list-folder-<?php echo $j+1; ?>" class="form-control" value="<?php echo htmlspecialchars($remote_folders[$j]['folder']); ?>" maxlength="200" required/>
													</td>
													<td>
														<select id="form-remote_folders-list-division_id-<?php echo $j+1; ?>" name="form-remote_folders-list-division_id-<?php echo $j+1; ?>" class="form-control">
															<option value="">- Qualsevol -</option>
<?php
			$resultss = query("SELECT d.*, TRIM(d.number)+0 number_formatted FROM division d WHERE d.series_id=".$series['id']." ORDER BY d.number ASC");
			while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
															<option value="<?php echo $ssrow['id']; ?>"<?php echo $remote_folders[$j]['division_id']==$ssrow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($ssrow['number_formatted'].(!empty($ssrow['name']) ? ' ('.$ssrow['name'].')' : '')); ?></option>
<?php
			}
			mysqli_free_result($resultss);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<input id="form-remote_folders-list-is_active-<?php echo $j+1; ?>" name="form-remote_folders-list-is_active-<?php echo $j+1; ?>" type="checkbox" value="1"<?php echo $remote_folders[$j]['is_active']==1? " checked" : ""; ?><?php echo $remote_folders[$j]['type']=='storage'? " disabled" : ""; ?>/>
													</td>
													<td class="text-center align-middle">
														<button id="form-remote_folders-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionRemoteFolderRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
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
											<button onclick="addVersionRemoteFolderRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix una carpeta</button>
										</div>
										<div class="col-sm text-right" style="padding-left: 0; padding-right: 0">
											<select id="import-type" class="form-control form-control-sm form-inline" title="Indica el tipus de sinronització desitjada en aquesta actualització d'enllaços: tots els comptes o només els marcats." style="width: auto; display: inline; font-size: 78%;">
												<option value="all" selected>Utilitza tots els comptes</option>
												<option value="sync">Només els sincronitzats</option>
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
							<div><span class="fa fa-exclamation-triangle mr-2"></span> Els següents elements no s'han importat perquè no tenen el format correcte o perquè els capítols no existeixen a la fitxa <?php echo $content_prep; ?>. Afegeix-los a mà on correspongui. Recorda que els fitxers només s'importen automàticament si tenen el format "<i>text</i><u><b> - 123</b></u><i>text</i>.mp4".</div>
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
<?php
	}
	if ($type=='manga') {
?>
						<div class="form-group">
							<label for="form-division-list">Portades <?php echo $division_prep; ?> <small class="text-muted">(JPEG, ~156x220, ≤300x400, ≤100 KiB)</small></label>
							<div class="row flex" id="form-division-list">
<?php
		foreach ($divisions as $division) {
?>
								<div class="col-sm-2 text-center pr-1 pl-1">
										<label><?php echo $division_name." ".$division['number'].(!empty($division['name']) ? " (".$division['name'].")" : ""); ?>:</label>
<?php
		$file_exists = !empty($row['id']) && file_exists($static_directory.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg');
?>
										<img id="form-division_cover_<?php echo $division['id']; ?>_preview" style="width: 128px; height: 180px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px; margin-bottom: 0.5em;"<?php echo $file_exists ? ' src="'.$static_url.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg" data-original="'.$static_url.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg"' : ''; ?> alt=""><br />
										<label for="form-division_cover_<?php echo $division['id']; ?>" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
										<input id="form-division_cover_<?php echo $division['id']; ?>" name="division_cover_<?php echo $division['id']; ?>" type="file" class="d-none" accept="image/jpeg" onchange="checkImageUpload(this, 102400, 'form-division_cover_<?php echo $division['id']; ?>_preview');"/>
								</div>
<?php
		}
?>
							</div>
						</div>
<?php
	}
?>
						<div class="form-group">
							<label for="form-episode-list">Capítols, variants i <?php echo $type=='manga' ? 'fitxers' : 'enllaços'; ?></label>
							<div class="container" id="form-episode-list">
								<datalist id="resolution-options">
									<option value="1080p">
									<option value="720p">
									<option value="480p">
									<option value="360p">
								</datalist>
								<div id="warning-no-numbers-and-sort" class="alert alert-warning<?php echo $row['show_episode_numbers']==0 && $row['order_type']!=0 ? '' : ' d-none'; ?>">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest <?php echo $content; ?> <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.<br /><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $row['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
								<div id="warning-no-numbers" class="alert alert-warning<?php echo $row['show_episode_numbers']==0 && $row['order_type']==0 ? '' : ' d-none'; ?>">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest <?php echo $content; ?> <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.</div>
								</div>
								<div id="warning-sort" class="alert alert-warning<?php echo $row['order_type']!=0 && $row['show_episode_numbers']!=0 ? '' : ' d-none'; ?>">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $row['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
<?php
	for ($i=0;$i<count($episodes);$i++) {
		$episode_name='';
		if (!empty($episodes[$i]['division_name'])) {
			$episode_name.=$episodes[$i]['division_name'].' - ';
		} else if (!empty($episodes[$i]['division_number'])) {
			$episode_name.=$division_name.' '.$episodes[$i]['division_number'].' - ';
		} else {
			$episode_name.='Altres - ';
		}
		if (!empty($episodes[$i]['linked_episode_id'])){
			$resultle=query("SELECT e.id, CONCAT(s.name, ' - ', IF(e.division_id IS NULL,'Altres',IFNULL(d.name,CONCAT('Temporada ',TRIM(d.number)+0))), ' - ', IF(e.number IS NULL,'Extra',CONCAT('Capítol ',TRIM(e.number)+0)),IF(e.description IS NULL,'',CONCAT(': ', e.description))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' AND e.id=".$episodes[$i]['linked_episode_id']);
			$linked_episode = mysqli_fetch_assoc($resultle);
			mysqli_free_result($resultle);
			$episode_name.=$linked_episode['description'].' [FILM ENLLAÇAT] <span class="mandatory"></span> <small class="text-muted">(És obligatori introduir-ne el títol!)</small>';
		} else if (!empty($episodes[$i]['number'])) {
			if (!empty($episodes[$i]['description'])) {
				$episode_name.='Capítol '.floatval($episodes[$i]['number']).' <small class="text-muted">(Descripció interna: '.htmlspecialchars($episodes[$i]['description']).')</small>';
			} else {
				$episode_name.='Capítol '.floatval($episodes[$i]['number']);
			}
		} else {
			$episode_name.=$episodes[$i]['description'].' <small class="text-muted">(Aquesta descripció NO és interna: es mostrarà si no introdueixes cap títol!)</small>';
		}

		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$resultl = query("SELECT f.* FROM file f WHERE f.version_id=".escape($_GET['id'])." AND f.episode_id=".$episodes[$i]['id']." ORDER BY f.variant_name ASC, f.id ASC");
			$files = array();
			while ($rowl = mysqli_fetch_assoc($resultl)) {
				$resultli = query("SELECT l.* FROM link l WHERE l.file_id=".$rowl['id']." ORDER BY l.url ASC");
				$links = array();
				while ($rowli = mysqli_fetch_assoc($resultli)) {
					array_push($links, $rowli);
				}
				$rowl['links']=$links;
				array_push($files, $rowl);
				mysqli_free_result($resultli);
			}
			mysqli_free_result($resultl);
		} else {
			$files=array();
		}
?>
								<div class="form-group">
									<label for="form-files-list-<?php echo $episodes[$i]['id']; ?>-title"><span class="fa fa-caret-square-right pr-2 text-primary"></span><?php echo $episode_name; ?></label>
									<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" type="text" class="form-control" value="<?php echo htmlspecialchars($episodes[$i]['title']); ?>" maxlength="200" placeholder="(Sense títol)"<?php echo !empty($episodes[$i]['linked_episode_id']) ? ' required' : ''; ?>/>
<?php
		if (empty($episodes[$i]['linked_episode_id'])) {
?>
									<div class="container" id="form-files-list-<?php echo $episodes[$i]['id']; ?>">
										<div class="row mb-3">
											<div class="w-100 column">
												<table class="table table-bordered table-hover table-sm" id="files-list-table-<?php echo $episodes[$i]['id']; ?>" data-count="<?php echo max(count($files),1); ?>">
													<thead>
														<tr>
															<th style="width: 12%;"><span class="mandatory">Variant</span> <span class="fa fa-question-circle small text-secondary" style="cursor: help;" title="Cada capítol pot tenir diferents variants (per dialectes, estils, etc.), però normalment només n'hi ha una ('Única')"></span></th>
<?php
			if ($type=='manga') {
?>
															<th>Fitxer</th>
															<th style="width: 16%;">Pujada</th>
<?php
			} else {
?>
															<th>Enllaços de streaming / Resolució</th>
															<th style="width: 10%;"><span class="mandatory">Durada</span></th>
<?php
			}
?>
															<th style="width: 15%;">Comentaris</th>
															<th class="text-center" style="width: 5%;">Perduda</th>
															<th class="text-center" style="width: 5%;">Acció</th>
														</tr>
													</thead>
													<tbody>
<?php
			for ($j=0;$j<count($files);$j++) {
?>
														<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-<?php echo $j+1; ?>">
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['variant_name']); ?>" maxlength="200" placeholder="- Variant -" required/>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['id']; ?>"/>
															</td>
<?php
				if ($type=='manga') {
?>
															<td class="align-middle">
																<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($files[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check fa-fw"></span> Ja hi ha pujat el fitxer <strong>'.htmlspecialchars($files[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($files[$j]['original_filename']) ? 'warning' : 'info' ; ?> w-100"><span class="fa fa-upload pr-2"></span><?php echo !empty($files[$j]['original_filename']) ? 'Canvia el fitxer...' : 'Puja un fitxer...' ; ?></label>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['length']; ?>"/>
															</td>
<?php
				} else {
?>
															<td>
																<table class="w-100" id="links-list-table-<?php echo $episodes[$i]['id']; ?>-<?php echo $j+1; ?>" data-count="<?php echo max(count($files[$j]['links']),1); ?>">
																	<tbody>
<?php
					for ($k=0;$k<count($files[$j]['links']);$k++) {
?>
																		<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-<?php echo $j+1; ?>-<?php echo $k+1; ?>" style="background: none;">
																			<td class="pl-0 pt-0 pb-0 border-0">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="url" class="form-control" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['id']); ?>"/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="- Tria -"/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																			</td>
																		</tr>
<?php
					}
					if (count($files[$j]['links'])==0) {
?>
																		<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1-1" style="background: none;">
																			<td class="pl-0 pt-0 pb-0 border-0">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="url" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value=""/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																			</td>
																		</tr>
<?php
					}
?>
																	</tbody>
																	<tfoot>
																		<tr style="background: none;">
																			<td colspan="3" class="text-center p-0 border-0">
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-<?php echo $j+1; ?>" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn btn-success btn-sm" style="margin-top: 0.25em;"><span class="fa fa-fw fa-plus pr-2"></span>Afegeix un altre enllaç</button>
																			</td>
																		</tr>
																	</tfoot>
																</table>
															</td>
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-<?php echo $j+1; ?>" type="time" step="1" class="form-control" value="<?php echo convert_to_hh_mm_ss($files[$j]['length']); ?>"/>
															</td>
<?php
				}
?>
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-comments-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center" style="padding-top: .75rem;">
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-is_lost-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-is_lost-<?php echo $j+1; ?>" type="checkbox" value="1"<?php echo $files[$j]['is_lost'] ? ' checked' : ''; ?>/>
															</td>
															<td class="text-center pt-2">
																<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-<?php echo $j+1; ?>" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
			}
			if (count($files)==0) {
?>
														<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-1">
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" type="text" class="form-control" value="Única" maxlength="200" placeholder="- Variant -" required/>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" type="hidden" value="-1"/>
															</td>
<?php
				if ($type=='manga') {
?>
															<td class="align-middle">
																<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-1" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" type="hidden" value="0"/>
															</td>
<?php
				} else {
?>
															<td>
																<table class="w-100" id="links-list-table-<?php echo $episodes[$i]['id']; ?>-1" data-count="1">
																	<tbody>
																		<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1-1" style="background: none;">
																			<td class="pl-0 pt-0 pb-0 border-0">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="url" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value="-1"/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																			</td>
																		</tr>
																	</tbody>
																	<tfoot>
																		<tr style="background: none;">
																			<td colspan="3" class="text-center p-0 border-0">
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-1" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn btn-success btn-sm" style="margin-top: 0.25em;"><span class="fa fa-fw fa-plus pr-2"></span>Afegeix un altre enllaç</button>
																			</td>
																		</tr>
																	</tfoot>
																</table>
															</td>
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" type="time" step="1" class="form-control"/>
															</td>
<?php
				}
?>
															<td>
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-comments-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-comments-1" type="text" class="form-control" value="" maxlength="200"/>
															</td>
															<td class="text-center" style="padding-top: .75rem;">
																<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-is_lost-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-is_lost-1" type="checkbox" value="1"/>
															</td>
															<td class="text-center pt-2">
																<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-1" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
			}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn btn-info btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix una altra variant per a aquest capítol</button></div>
										</div>
									</div>
<?php
		} else {
?>
									<div class="alert alert-warning">
										<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest capítol és un film enllaçat. No se'n mostrarà el número de capítol, i el títol que es mostrarà serà el que introdueixis aquí.</div>
									</div>
<?php
		}
?>
								</div>
<?php
	}
?>
							</div>
						</div>
						<div class="form-group">
							<label for="form-extras-list">Material extra</label>
							<div class="container" id="form-extras-list">
<?php

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$resultex = query("SELECT f.* FROM file f WHERE f.version_id=".escape($_GET['id'])." AND f.episode_id IS NULL ORDER BY f.extra_name ASC, f.id ASC");
		$extras = array();
		while ($rowex = mysqli_fetch_assoc($resultex)) {
			$resultli = query("SELECT l.* FROM link l WHERE l.file_id=".$rowex['id']." ORDER BY l.url ASC");
			$links = array();
			while ($rowli = mysqli_fetch_assoc($resultli)) {
				array_push($links, $rowli);
			}
			$rowex['links']=$links;
			array_push($extras, $rowex);
			mysqli_free_result($resultli);
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
<?php
		if ($type=='manga') {
?>
															<th>Fitxer</th>
															<th style="width: 16%;">Pujada</th>
<?php
		} else {
?>
															<th>Enllaços de streaming / Resolució</th>
															<th style="width: 10%;"><span class="mandatory">Durada</span></th>
<?php
		}
?>
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
<?php
		if ($type=='manga') {
?>
															<td class="align-middle">
																<div id="form-extras-list-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($extras[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check fa-fw"></span> Ja hi ha pujat el fitxer <strong>'.htmlspecialchars($extras[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-extras-list-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($extras[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pr-2"></span><?php echo !empty($extras[$j]['original_filename']) ? 'Canvia el fitxer...' : 'Puja un fitxer...' ; ?></label>
																<input id="form-extras-list-file-<?php echo $j+1; ?>" name="form-extras-list-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
																<input id="form-extras-list-length-<?php echo $j+1; ?>" name="form-extras-list-length-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['length']; ?>"/>
															</td>
<?php
		} else {
?>
															<td>
																<table class="w-100" id="extras-links-list-table-<?php echo $j+1; ?>" data-count="<?php echo max(count($extras[$j]['links']),1); ?>">
																	<tbody>
<?php
			for ($k=0;$k<count($extras[$j]['links']);$k++) {
?>
																		<tr id="form-links-extras-list-row-<?php echo $j+1; ?>-<?php echo $k+1; ?>" style="background: none;">
																			<td class="pl-0 pt-0 pb-0 border-0">
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="url" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="- Introdueix un enllaç -" oninput="$(this).attr('value',$(this).val());" required/>
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['id']); ?>"/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="- Tria -" required/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteExtraLinkRow(<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																			</td>
																		</tr>
<?php
			}
?>
																	</tbody>
																	<tfoot>
																		<tr style="background: none;">
																			<td colspan="3" class="text-center p-0 border-0">
																				<button id="form-extras-list-add_link-<?php echo $j+1; ?>" onclick="addExtraLinkRow(<?php echo $j+1; ?>);" type="button" class="btn btn-success btn-sm" style="margin-top: 0.25em;"><span class="fa fa-fw fa-plus pr-2"></span>Afegeix un altre enllaç</button>
																			</td>
																		</tr>
																	</tfoot>
																</table>
															</td>
															<td>
																<input id="form-extras-list-length-<?php echo $j+1; ?>" name="form-extras-list-length-<?php echo $j+1; ?>" type="time" step="1" class="form-control" value="<?php echo convert_to_hh_mm_ss($extras[$j]['length']); ?>" required/>
															</td>
<?php
		}
?>
															<td>
																<input id="form-extras-list-comments-<?php echo $j+1; ?>" name="form-extras-list-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center pt-2">
																<button id="form-extras-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionExtraRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
	}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionExtraRow();" type="button" class="btn btn-info btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un altre material extra</button></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-view-options">Opcions de visualització de la fitxa pública</label>
							<div id="form-view-options" class="row pl-3 pr-3">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_episode_numbers" id="form-show_episode_numbers" value="1"<?php echo $row['show_episode_numbers']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_episode_numbers">Mostra el número dels capítols <small class="text-muted">(normalment activat només en <?php echo $series_name; ?>; afegeix "Capítol X: " davant del nom dels capítols no especials)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_divisions" id="form-show_divisions" value="1"<?php echo $row['show_divisions']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_divisions">Separa per <?php echo $division_pl; ?> i mostra'n els noms <small class="text-muted">(normalment activat; si només n'hi ha <?php echo $division_one; ?>, no es mostrarà)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_expanded_divisions" id="form-show_expanded_divisions" value="1"<?php echo $row['show_expanded_divisions']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_expanded_divisions">Mostra <?php echo $division_pl_expanded; ?> per defecte <small class="text-muted">(normalment activat; si n'hi ha <?php echo $division_many; ?>, es pot desmarcar)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_expanded_extras" id="form-show_expanded_extras" value="1"<?php echo $row['show_expanded_extras']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_expanded_extras">Mostra els extres desplegats per defecte <small class="text-muted">(normalment activat; si n'hi ha molts o poc rellevants, es pot desmarcar)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_unavailable_episodes" id="form-show_unavailable_episodes" value="1"<?php echo $row['show_unavailable_episodes']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_unavailable_episodes">Mostra els capítols que no tinguin cap enllaç <small class="text-muted">(normalment activat; apareixen en gris)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_standard" value="0"<?php echo $row['order_type']==0 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_standard">Aplica l'ordenació estàndard <small class="text-muted">(primer capítols normals per ordre numèric, després especials per ordre alfabètic estricte)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_alphabetic" value="1"<?php echo $row['order_type']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_alphabetic">Aplica l'ordenació alfabètica estricta <small class="text-muted">(capítols i especials barrejats, ordre: 1, 10, 11, 12..., 2, 3...)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_natural" value="2"<?php echo $row['order_type']==2 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_natural">Aplica l'ordenació alfabètica natural <small class="text-muted">(capítols i especials barrejats, ordre: 1, 2, 3... 10, 11, 12...)</small></label>
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
		if ($_SESSION['username']=='Administrador' && $type!='manga') {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_recreate_storage_links" id="form-do_not_recreate_storage_links" value="1" onchange="if($(this).prop('checked')){$('#form-do_not_count_as_update').prop('checked',true);}">
								<label class="form-check-label" for="form-do_not_recreate_storage_links">No recreïs els enllaços d'emmagatzematge</label>
							</div>
							<br />
<?php
		}
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
