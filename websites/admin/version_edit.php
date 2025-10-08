<?php
require_once(__DIR__.'/../../common/libraries/preview_image_generator.php');
$type='anime';
$link_url_pattern = "(https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_\-]+)|storage:\/\/.*)";

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Edició de versions d’anime - Anime";
		$page="anime";
		$external_source="MyAnimeList";
	break;
	case 'manga':
		$header_title="Edició de versions de manga - Manga";
		$page="manga";
		$external_source="MyAnimeList";
	break;
	case 'liveaction':
		$header_title="Edició de versions d’imatge real - Imatge real";
		$page="liveaction";
		$external_source="MyDramaList";
	break;
}

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$content="anime";
		$content_uc="Títol original de l’anime";
		$content_prep="de l’anime";
		$division_titles="Títols i portades de cada temporada";
		$division_some_completed="alguna temporada completada";
		break;
	case 'manga':
		$content="manga";
		$content_uc="Títol original del manga";
		$content_prep="del manga";
		$division_titles="Títols i portades de cada volum";
		$division_some_completed="algun volum completat";
		break;
	case 'liveaction':
		$content="contingut d’imatge real";
		$content_uc="Títol original del contingut d’imatge real";
		$content_prep="del contingut d’imatge real";
		$division_titles="Títols i portades de cada temporada";
		$division_some_completed="alguna temporada completada";
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
		if (!empty($_POST['featurable_status'])){
			$data['featurable_status']=escape($_POST['featurable_status']);
		} else {
			$data['featurable_status']=0;
		}
		if (!empty($_POST['storage_folder'])) {
			$data['storage_folder']="'".escape($_POST['storage_folder'])."'";
		} else if ($type!='manga') {
			crash("Dades invàlides: manca storage_folder");
		} else {
			$data['storage_folder']="NULL";
		}
		if (!empty($_POST['storage_processing'])) {
			$data['storage_processing']=escape($_POST['storage_processing']);
		} else {
			$data['storage_processing']=0;
		}
		if (!empty($_POST['show_episode_numbers'])){
			$data['show_episode_numbers']=1;
		} else {
			$data['show_episode_numbers']=0;
		}
		if (!empty($_POST['title'])) {
			$data['title']=escape($_POST['title']);
		} else {
			crash("Dades invàlides: manca title");
		}
		if (!empty($_POST['alternate_titles'])) {
			$data['alternate_titles']="'".escape($_POST['alternate_titles'])."'";
		} else {
			$data['alternate_titles']="NULL";
		}
		if (!empty($_POST['slug'])) {
			$data['slug']=escape($_POST['slug']);
		} else {
			crash("Dades invàlides: manca slug");
		}
		if (!empty($_POST['synopsis'])) {
			$data['synopsis']=escape($_POST['synopsis']);
		} else {
			crash("Dades invàlides: manca synopsis");
		}

		$divisions=array();

		$resultd = query("SELECT d.* FROM division d WHERE d.number_of_episodes>0 AND d.series_id=".$data['series_id']);

		while ($rowd = mysqli_fetch_assoc($resultd)) {
			if (!empty($_POST['form-division-title-'.$rowd['id']])){
				$rowd['version_division_title']=escape($_POST['form-division-title-'.$rowd['id']]);
			} else {
				crash("Dades invàlides: manca títol de la divisió");
			}
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
				if (!empty($_FILES['form-files-list-'.$episode_id.'-file-'.$i]) && is_uploaded_file($_FILES['form-files-list-'.$episode_id.'-file-'.$i]['tmp_name'])) {
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
						$link['url']="'".escape(trim($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-url']))."'";
					} else {
						$link['url']="NULL";
					}
					if (!empty($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-resolution'])) {
						$link['resolution']="'".escape(trim($_POST['form-files-list-'.$episode_id.'-file-'.$i.'-link-'.$j.'-resolution']))."'";
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
				crash("Dades invàlides: manca id de l’extra");
			}
			if (!empty($_POST['form-extras-list-name-'.$i])) {
				$extra['name']=escape($_POST['form-extras-list-name-'.$i]);
			} else {
				crash("Dades invàlides: manca name de l’extra");
			}
			if (!empty($_FILES['form-extras-list-file-'.$i]) && is_uploaded_file($_FILES['form-extras-list-file-'.$i]['tmp_name'])) {
				$extra['original_filename']=escape($_FILES['form-extras-list-file-'.$i]["name"]);
				$extra['original_filename_unescaped']=$_FILES['form-extras-list-file-'.$i]['name'];
				$extra['temporary_filename']=$_FILES['form-extras-list-file-'.$i]['tmp_name'];
			} else if ($type=='manga' && $extra['id']==-1) {
				crash("Dades invàlides: manca fitxer de l’extra");
			}
			if (!empty($_POST['form-extras-list-length-'.$i])) {
				//This works for manga too because if the format is not in HH:MM:SS, the value is returned directly
				$extra['length']=escape(convert_from_hh_mm_ss($_POST['form-extras-list-length-'.$i]));
			} else {
				crash("Dades invàlides: manca length de l’extra");
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
					$link['url']="'".escape(trim($_POST['form-extras-list-'.$i.'-link-'.$j.'-url']))."'";
				} else {
					$link['url']="NULL";
				}
				if (!empty($_POST['form-extras-list-'.$i.'-link-'.$j.'-resolution'])) {
					$link['resolution']="'".escape(trim($_POST['form-extras-list-'.$i.'-link-'.$j.'-resolution']))."'";
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
			if (!empty($_POST['form-remote_folders-list-default_resolution-'.$i])) {
				$remote_folder['default_resolution']=escape($_POST['form-remote_folders-list-default_resolution-'.$i]);
			} else {
				crash("Dades invàlides: manca default_resolution de la carpeta remota");
			}
			if (!empty($_POST['form-remote_folders-list-default_duration-'.$i])) {
				$remote_folder['default_duration']=escape(convert_from_hh_mm_ss($_POST['form-remote_folders-list-default_duration-'.$i]));
			} else {
				crash("Dades invàlides: manca default_duration de la carpeta remota");
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
		
		$current_timestamp = date('Y-m-d H:i:s');
		if ($_POST['action']=='edit') {
			//Completed version: check if we are completing it now
			//If it is being completed now, set the date to now,
			//otherwise keep the date if it's complete, or set it to null if not completed
			$completed_date = 'NULL';
			if ($data['status']==1) {
				$result_old=query("SELECT completed_date FROM version WHERE id=".$data['id']);
				if ($rowov = mysqli_fetch_assoc($result_old)) {
					$completed_date = empty($rowov['completed_date']) ? "'$current_timestamp'" : "'".$rowov['completed_date']."'";
				}
				mysqli_free_result($result_old);
			}
			
			$old_result = query("SELECT * FROM version WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash("Algú altre ha actualitzat la versió mentre tu l’editaves. Hauràs de tornar a fer els canvis.");
			}
			
			$slug_result = query("SELECT COUNT(*) cnt FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.slug='".$data['slug']."' AND s.type='$type' AND v.id<>".$data['id']);
			$slug_row = mysqli_fetch_assoc($slug_result);
			if ($slug_row['cnt']>0) {
				crash("Ja hi ha una versió amb aquest identificador. Revisa que no l’hagis afegida per duplicat i, en cas contrari, canvia’n l’identificador.");
			}

			log_action("update-version", "S’ha actualitzat una versió de «".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."» (id. de versió: ".$data['id'].")");
			query("UPDATE version SET status=".$data['status'].",is_missing_episodes=".$data['is_missing_episodes'].",featurable_status=".$data['featurable_status'].",show_episode_numbers=".$data['show_episode_numbers'].",is_hidden=".$data['is_hidden'].",completed_date=$completed_date,storage_folder=".$data['storage_folder'].",storage_processing=".$data['storage_processing'].",slug='".$data['slug']."',title='".$data['title']."',alternate_titles=".$data['alternate_titles'].",synopsis='".$data['synopsis']."',updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
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
					query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,is_lost,created,created_by,updated,updated_by) VALUES (".$data['id'].",".$file['episode_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['length'].",".$file['comments'].",".$file['is_lost'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
					$inserted_file_id=mysqli_insert_id($db_connection);
					if ($type=='manga') {
						if ($file['original_filename']!='NULL') {
							decompress_manga_file($inserted_file_id, $file['temporary_filename'], $file['original_filename_unescaped']);
						}
					} else {
						foreach ($file['links'] as $link) {
							query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES ($inserted_file_id,".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
						}
					}
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET files_updated='$current_timestamp',files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					query("UPDATE file SET ".($file['original_filename']!='NULL' ? "original_filename=".$file['original_filename']."," : "")."variant_name=".$file['variant_name'].",length=".$file['length'].",comments=".$file['comments'].",is_lost=".$file['is_lost'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$file['id']);

					if ($type=='manga') {
						$resultcr = query("SELECT * FROM file WHERE id=".$file['id']);
						if ($current_file = mysqli_fetch_assoc($resultcr)) {
							$has_updated_files=($file['original_filename']!='NULL' && (empty($current_file['original_filename']) ? "NULL" : "'".escape($current_file['original_filename'])."'")!=$file['original_filename']);
						}
						mysqli_free_result($resultcr);
						if ($file['original_filename']!='NULL') {
							decompress_manga_file($file['id'], $file['temporary_filename'], $file['original_filename_unescaped']);
							query("UPDATE file SET updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$file['id']);
						}
					} else {
						$has_updated_files = FALSE;
						$link_ids=array();
						$has_updated_mega_link=FALSE;
						$has_updated_storage_link=FALSE;
						foreach ($file['links'] as $link) {
							if ($link['id']==-1) {
								query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$file['id'].",".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
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
									query("UPDATE link SET url=".$link['url'].",resolution=".$link['resolution'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$link['id']);
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
						query("UPDATE version SET files_updated='$current_timestamp',files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
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
					query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,created,created_by,updated,updated_by) VALUES (".$data['id'].",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['length'].",".$extra['comments'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
					$inserted_file_id=mysqli_insert_id($db_connection);
					if ($type=='manga') {
						decompress_manga_file($inserted_file_id, $extra['temporary_filename'], $extra['original_filename_unescaped']);
					} else {
						foreach ($extra['links'] as $link) {
							query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES ($inserted_file_id,".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
						}
					}
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE version SET files_updated='$current_timestamp',files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					query("UPDATE file SET extra_name='".$extra['name']."',".((isset($extra['original_filename']) && $extra['original_filename']!=NULL) ? "original_filename='".$extra['original_filename']."'," : "")."length=".$extra['length'].",comments=".$extra['comments'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$extra['id']);

					if ($type=='manga') {
						$resultcr = query("SELECT * FROM file WHERE id=".$extra['id']);
						if ($current_extra = mysqli_fetch_assoc($resultcr)) {
							query("UPDATE file SET extra_name='".$extra['name']."',".((isset($extra['original_filename']) && $extra['original_filename']!=NULL) ? "original_filename='".$extra['original_filename']."'," : "")."length=".$extra['length'].",comments=".$extra['comments'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$extra['id']);
						}
						mysqli_free_result($resultcr);
						if (isset($extra['original_filename']) && $extra['original_filename']!=NULL) {
							decompress_manga_file($extra['id'], $extra['temporary_filename'], $extra['original_filename_unescaped']);
						}
					} else {
						$link_ids=array();
						$has_updated_mega_link=FALSE;
						$has_updated_storage_link=FALSE;
						foreach ($extra['links'] as $link) {
							if ($link['id']==-1) {
								query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$extra['id'].",".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
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
									query("UPDATE link SET url=".$link['url'].",resolution=".$link['resolution'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$link['id']);
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
					query("INSERT INTO remote_folder (version_id,remote_account_id,folder,default_resolution,default_duration,division_id,is_active,created,created_by,updated,updated_by) VALUES (".$data['id'].",".$remote_folder['remote_account_id'].",'".$remote_folder['folder']."','".$remote_folder['default_resolution']."',".$remote_folder['default_duration'].",".$remote_folder['division_id'].",".$remote_folder['is_active'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
				} else {
					query("UPDATE remote_folder SET remote_account_id=".$remote_folder['remote_account_id'].",folder='".$remote_folder['folder']."',default_resolution='".$remote_folder['default_resolution']."',default_duration=".$remote_folder['default_duration'].",division_id=".$remote_folder['division_id'].",is_active=".$remote_folder['is_active'].",updated='$current_timestamp',updated_by='".escape($_SESSION['username'])."' WHERE id=".$remote_folder['id']);
				}
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/covers/version_'.$data['id'].'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'], STATIC_DIRECTORY.'/images/covers/version_'.$data['id'].'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], STATIC_DIRECTORY.'/images/featured/version_'.$data['id'].'.jpg');
			}

			query("DELETE FROM version_division WHERE version_id=".$data['id']);
			foreach ($divisions as $division) {
				if (!empty($_FILES['division_cover_'.$division['id']]) && is_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'], STATIC_DIRECTORY."/images/divisions/".$data['id']."_".$division['id'].".jpg");
				}
				query("INSERT INTO version_division (division_id, version_id, title) VALUES (".$division['id'].",".$data['id'].", '".$division['version_division_title']."')");
			}

			update_version_preview($data['id']);
			if (!DISABLE_COMMUNITY) {
				add_or_update_topic_to_community($data['id']);
			}

			$_SESSION['message']="S’han desat les dades correctament.";
		}
		else {
			$slug_result = query("SELECT COUNT(*) cnt FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.slug='".$data['slug']."' AND s.type='$type'");
			$slug_row = mysqli_fetch_assoc($slug_result);
			if ($slug_row['cnt']>0) {
				crash("Ja hi ha una versió amb aquest identificador. Revisa que no l’hagis afegida per duplicat i, en cas contrari, canvia’n l’identificador.");
			}
			log_action("create-version", "S’ha creat una versió de «".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."»");
			query("INSERT INTO version (series_id,status,is_missing_episodes,featurable_status,show_episode_numbers,is_hidden,completed_date,storage_folder,storage_processing,slug,title,alternate_titles,synopsis,files_updated,files_updated_by,created,created_by,updated,updated_by) VALUES (".$data['series_id'].",".$data['status'].",".$data['is_missing_episodes'].",".$data['featurable_status'].",".$data['show_episode_numbers'].",".$data['is_hidden'].",".($data['status']==1 ? "'$current_timestamp'" : 'NULL').",".$data['storage_folder'].",".$data['storage_processing'].",'".$data['slug']."','".$data['title']."',".$data['alternate_titles'].",'".$data['synopsis']."','$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			//Set as default version if none is set
			query("UPDATE series SET default_version_id=$inserted_id WHERE id=".$data['series_id']." AND default_version_id IS NULL");
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
				query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,is_lost,created,created_by,updated,updated_by) VALUES (".$inserted_id.",".$file['episode_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['length'].",".$file['comments'].",".$file['is_lost'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
				$inserted_file_id=mysqli_insert_id($db_connection);
				if ($type=='manga') {
					if ($file['original_filename']!='NULL') {
						decompress_manga_file($inserted_file_id, $file['temporary_filename'], $file['original_filename_unescaped']);
					}
				} else {
					foreach ($file['links'] as $link) {
						query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$inserted_file_id.",".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
					}
				}
			}
			foreach ($extras as $extra) {
				query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,original_filename,length,comments,created,created_by,updated,updated_by) VALUES (".$inserted_id.",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['length'].",".$extra['comments'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
				$inserted_file_id=mysqli_insert_id($db_connection);
				if ($type=='manga') {
					decompress_manga_file($inserted_file_id, $extra['temporary_filename'], $extra['original_filename_unescaped']);
				} else {
					foreach ($extra['links'] as $link) {
						query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES (".$inserted_file_id.",".$link['url'].",".$link['resolution'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
					}
				}
			}
			foreach ($remote_folders as $remote_folder) {
				query("INSERT INTO remote_folder (version_id,remote_account_id,folder,default_resolution,default_duration,division_id,is_active,created,created_by,updated,updated_by) VALUES (".$inserted_id.",".$remote_folder['remote_account_id'].",'".$remote_folder['folder']."','".$remote_folder['default_resolution']."',".$remote_folder['default_duration'].",".$remote_folder['division_id'].",".$remote_folder['is_active'].",'$current_timestamp','".escape($_SESSION['username'])."','$current_timestamp','".escape($_SESSION['username'])."')");
			}

			foreach ($divisions as $division) {
				if (!empty($_FILES['division_cover_'.$division['id']]) && is_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['division_cover_'.$division['id']]['tmp_name'], STATIC_DIRECTORY."/images/divisions/".$inserted_id."_".$division['id'].".jpg");
				}
				query("INSERT INTO version_division (division_id, version_id, title) VALUES (".$division['id'].",".$inserted_id.", '".$division['version_division_title']."')");
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/covers/version_'.$inserted_id.'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'],STATIC_DIRECTORY.'/images/covers/version_'.$inserted_id.'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], STATIC_DIRECTORY.'/images/featured/version_'.$inserted_id.'.jpg');
			}

			update_version_preview($inserted_id);
			if (!DISABLE_COMMUNITY) {
				add_or_update_topic_to_community($inserted_id);
			}

			$_SESSION['message']="S’han desat les dades correctament.";
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

		$resultd = query("SELECT vd.title, d.id, d.series_id, TRIM(d.number)+0 number, d.name, d.number_of_episodes, d.external_id FROM division d LEFT JOIN version_division vd ON d.id=vd.division_id AND vd.version_id=".escape($_GET['id'])." WHERE d.series_id=".$row['series_id']." AND d.number_of_episodes>0 ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$fansubs = array();

		$resultf = query("SELECT fansub_id, downloads_url FROM rel_version_fansub vf WHERE vf.version_id=".$row['id']);

		while ($rowf = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, array($rowf['fansub_id'], $rowf['downloads_url']));
		}
		mysqli_free_result($resultf);

		$resulte = query("SELECT e.*,
					REPLACE(TRIM(e.number)+0, '.', ',') formatted_number,
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IF(e.number IS NOT NULL,
							IF(s.number_of_episodes=1,
								s.name,
								CONCAT(d.name, ' - Film ', REPLACE(TRIM(e.number)+0, '.', ','))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ',')),
							CONCAT(d.name, ' - ', e.description)
						)
					) episode_title,
					et.title,
					d.name division_name
				FROM episode e
				LEFT JOIN series s ON e.series_id=s.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=".escape($_GET['id'])."
				WHERE e.series_id=".$row['series_id']."
				ORDER BY d.number IS NULL ASC,
					d.number ASC,
					e.number IS NULL ASC,
					e.number ASC,
					e.description ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else if (!empty($_GET['series_id']) && is_numeric($_GET['series_id'])) {
		$row = array();
		$row['storage_processing']=$_SESSION['default_storage_processing'];
		$row['featurable_status']=1;

		$results = query("SELECT s.* FROM series s WHERE id=".escape($_GET['series_id']));
		$series = mysqli_fetch_assoc($results) or crash('Series not found');
		mysqli_free_result($results);

		if ($series['subtype']!='movie' && $series['subtype']!='oneshot') {
			$row['show_episode_numbers']=1;
		} else {
			$row['show_episode_numbers']=0;
		}

		$fansubs = array();

		$resultd = query("SELECT d.id, d.series_id, TRIM(d.number)+0 number, d.name, d.number_of_episodes, d.external_id FROM division d WHERE d.series_id=".escape($_GET['series_id'])." AND d.number_of_episodes>0 ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			if ($type=='manga') {
				$rowd['title']=$rowd['name'];
			}
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$resulte = query("SELECT e.*,
					REPLACE(TRIM(e.number)+0, '.', ',') formatted_number,
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IF(e.number IS NOT NULL,
							IF(s.number_of_episodes=1,
								s.name,
								CONCAT(d.name, ' - Film ', REPLACE(TRIM(e.number)+0, '.', ','))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ',')),
							CONCAT(d.name, ' - ', e.description)
						)
					) episode_title,
					NULL title,
					d.name division_name
				FROM episode e
				LEFT JOIN series s ON e.series_id=s.id
				LEFT JOIN division d ON e.division_id=d.id
				WHERE e.series_id=".escape($_GET['series_id'])."
				ORDER BY d.number IS NULL ASC,
					d.number ASC,
					e.number IS NULL ASC,
					e.number ASC,
					e.description ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else {
		crash("Dades invàlides: manca series_id<br>POST values: ".print_r($_POST, TRUE));
	}
	//This is extremely ugly, but avoids rewriting the HTML code or copying it to the JS file
	$fake_episode = array(
				'episode_title' => '{episode_title}',
				'linked_episode_id' => NULL,
				'number' => 'TEMPLATE',
				'formatted_number' => '{formatted_number}',
				'id' => '{template_id}',
				'title' => NULL,
				'division_id' => NULL,
				'division_name' => NULL,
			);
	array_push($episodes, $fake_episode);
?>
		<div class="modal fade" id="add-episode-from-version-modal" tabindex="-1" role="dialog" aria-labelledby="add-episode-from-version-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="add-episode-from-version-modal-title">Afegeix un capítol inexistent</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true" class="fa fa-times"></span>
						</button>
					</div>
					<div class="modal-body">
						Afegiràs immediatament un capítol a la sèrie corresponent. Si has de donar d’alta una divisió nova o diversos capítols, surt d’aquesta versió, edita la sèrie, i després torna a editar aquesta versió.
						<div class="mt-3">
							<label for="add-episode-from-version-division-id">Divisió</label>
							<select id="add-episode-from-version-division-id" class="form-select">
<?php
			$resultss = query("SELECT d.* FROM division d WHERE d.series_id=".$series['id']." AND d.number_of_episodes>0 ORDER BY d.number ASC");
			while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
								<option value="<?php echo $ssrow['id']; ?>"><?php echo htmlspecialchars($ssrow['name']); ?></option>
<?php
			}
			mysqli_free_result($resultss);
?>
							</select>
						</div>
						<div class="mt-3">
							<label for="add-episode-from-version-number">Número <small class="text-muted">(deixa-ho en blanc per a capítols especials)</small></label>
							<input class="form-control" id="add-episode-from-version-number" type="number" step="any" placeholder="Especial" oninput="if($(this).val()==''){$('#add-episode-from-version-special-name').removeClass('d-none');} else {$('#add-episode-from-version-special-name').addClass('d-none');$('#add-episode-from-version-description').val('');}">
						</div>
						<div class="mt-3 d-none" id="add-episode-from-version-special-name">
							<label for="add-episode-from-version-description">Nom de l’especial</label>
							<input class="form-control" id="add-episode-from-version-description" placeholder="- Introdueix un nom -">
						</div>
					</div>
					
					<div class="align-self-center">
						<button type="button" class="btn btn-primary m-2" onclick="addEpisodeFromVersion();">Afegeix a la sèrie</button> <button type="button" data-bs-dismiss="modal" class="btn btn-secondary m-2">Cancel·la</button>
					</div>
				</div>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la versió" : "Afegeix una versió"; ?></h4>
					<hr>
					<form method="post" action="version_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfLinks()">
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-title-with-autocomplete">Títol localitzat<span class="mandatory"></span></label> <?php print_helper_box('Títol localitzat', 'Aquest títol és el que es mostrarà públicament a la fitxa i correspon al títol que el teu fansub dóna a aquesta obra.\n\nRecomanem que sigui el títol en català, però evitant fer traduccions innecessàries.'); ?>
									<input class="form-control" name="title" id="form-title-with-autocomplete" placeholder="- Introdueix un títol -" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['title'])); ?>" data-old-value="<?php echo htmlspecialchars(html_entity_decode($row['title'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
									<input type="hidden" id="type" value="<?php echo $type; ?>">
									<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-series" class="mandatory"><?php echo $content_uc; ?></label> <?php print_helper_box($content_uc, 'Indica l’obra a què està associada aquesta versió.\n\nNo es pot canviar perquè se selecciona únicament abans de crear la versió.'); ?>
									<input id="form-series" class="form-control" readonly value="<?php echo htmlspecialchars($series['name']); ?>"></input>
									<input name="series_id" type="hidden" value="<?php echo $series['id']; ?>"/>
									<input id="form-external_id" type="hidden" value="<?php echo $series['external_id']; ?>"/>
									<input id="series_subtype" type="hidden" value="<?php echo $series['subtype']; ?>"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-slug">Identificador<span class="mandatory"></span></label> <?php print_helper_box('Identificador', 'Aquest identificador autogenerat formarà part de l’URL del contingut al portal.\n\nEs modifica automàticament en canviar el títol o els fansubs de la versió.\n\nÉs un URL amigable amb el format «nom-de-la-serie/fansubs-que-la-fan».\n\nSi es modifica una vegada la versió ja està creada, notifica-ho a un administrador o els enllaços antics deixaran de funcionar.'); ?>
									<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
									<input type="hidden" id="form-old_slug" value="<?php echo htmlspecialchars($row['slug']); ?>">
								</div>
							</div>
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-alternate_titles">Títols localitzats alternatius</label> <?php print_helper_box('Títols localitzats alternatius', 'S’hi poden introduir títols localitzats alternatius de la versió. Per exemple, si es manté preferentment el títol en japonès o anglès, se’n pot afegir la traducció aquí. D’aquesta manera, si algú cerca algun d’aquests títols alternatius, també trobarà la versió corresponent.\n\nNomés es permet introduir-hi títols en català.\n\nSi n’hi ha més d’un, se separen per comes.'); ?>
									<input class="form-control" name="alternate_titles" id="form-alternate_titles" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_titles'])); ?>">
								</div>
							</div>
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-fansub-1" class="mandatory">Fansub</label> <?php print_helper_box('Fansub', 'Fansub que edita aquesta versió.\n\nSi no hi ha el fansub que vols, demana a un administrador que l’afegeixi.\n\nNo afegeixis versions de fansubs aliens al teu amb un usuari associat al teu fansub, o no les podràs veure un cop desades.'); ?>
									<select name="fansub_1" class="form-select" id="form-fansub-1" required>
										<option value="">- Selecciona un fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option data-slug="<?php echo htmlspecialchars($frow['slug']); ?>" value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>0 && $fansubs[0][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-fansub-2">Fansub 2</label> <?php print_helper_box('Fansub 2', 'Segon fansub que edita aquesta versió.\n\nS’utilitza únicament en cas de col·laboracions entre diversos fansubs.\n\nSi no hi ha el fansub que vols, demana a un administrador que l’afegeixi.'); ?>
									<select name="fansub_2" class="form-select" id="form-fansub-2">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option data-slug="<?php echo htmlspecialchars($frow['slug']); ?>" value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>1 && $fansubs[1][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-fansub-3">Fansub 3</label> <?php print_helper_box('Fansub 3', 'Tercer fansub que edita aquesta versió.\n\nS’utilitza únicament en cas de col·laboracions entre diversos fansubs.\n\nSi no hi ha el fansub que vols, demana a un administrador que l’afegeixi.'); ?>
									<select name="fansub_3" class="form-select" id="form-fansub-3">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option data-slug="<?php echo htmlspecialchars($frow['slug']); ?>" value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>2 && $fansubs[2][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
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
								<div class="mb-3">
									<label for="form-downloads_url_1" class="mandatory">Enllaç de baixada dels fitxers originals</label> <?php print_helper_box('Enllaç de baixada dels fitxers originals', 'Enllaç (URL) a la fitxa del web del fansub o bé a una carpeta de MEGA on es puguin baixar els fitxers originals del fansub.\n\nEs mostra a la fitxa i és útil per a evitar que el públic es baixi les versions recomprimides per a streaming.\n\nSe’n pot afegir més d’un separant-los per un punt i coma (sense espai al darrere).'); ?>
									<input id="form-downloads_url_1" name="downloads_url_1" type="url" class="form-control" value="<?php echo (count($fansubs)>0 ? htmlspecialchars($fansubs[0][1]) : ''); ?>" maxlength="200" required/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-downloads_url_2">Enllaç de baixada dels fitxers originals 2</label> <?php print_helper_box('Enllaç de baixada dels fitxers originals 2', 'Enllaç (URL) a la fitxa del web del segon fansub o bé a una carpeta de MEGA on es puguin baixar els fitxers originals del segon fansub.\n\nEs mostra a la fitxa i és útil per a evitar que el públic es baixi les versions recomprimides per a streaming.\n\nSe’n pot afegir més d’un separant-los per un punt i coma (sense espai al darrere).'); ?>
									<input id="form-downloads_url_2" name="downloads_url_2" type="url" class="form-control" value="<?php echo (count($fansubs)>1 ? htmlspecialchars($fansubs[1][1]) : ''); ?>" maxlength="200" required <?php echo (count($fansubs)>1 ? '' : ' disabled'); ?>/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-downloads_url_3">Enllaç de baixada dels fitxers originals 3</label> <?php print_helper_box('Enllaç de baixada dels fitxers originals 3', 'Enllaç (URL) a la fitxa del web del tercer fansub o bé a una carpeta de MEGA on es puguin baixar els fitxers originals del tercer fansub.\n\nEs mostra a la fitxa i és útil per a evitar que el públic es baixi les versions recomprimides per a streaming.\n\nSe’n pot afegir més d’un separant-los per un punt i coma (sense espai al darrere).'); ?>
									<input id="form-downloads_url_3" name="downloads_url_3" type="url" class="form-control" value="<?php echo (count($fansubs)>2 ? htmlspecialchars($fansubs[2][1]) : ''); ?>" maxlength="200" required <?php echo (count($fansubs)>2 ? '' : ' disabled'); ?>/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-status" class="mandatory">Estat</label> <?php print_helper_box('Estat', 'Estat de publicació de la versió per part del fansub.\n\n• Completada: Obra finalitzada amb tot el contingut penjat.\n• En procés: Obra en publicació per part del fansub.\n• Parcialment completada: N’hi ha '.$division_some_completed.', però no està previst continuar l’obra.\n• Abandonada: Obra inacabada durant molt de temps sense que hi hagi confirmació de cancel·lació per part del fansub.\n• Cancel·lada: Obra inacabada i cancel·lada formalment pel fansub.\n\nEl sistema impedirà desar una versió si es marca com a completada sense penjar-ne tots els capítols, o viceversa: marcar-la com a «En procés» havent-ne penjat tots els capítols.'); ?>
									<select class="form-select" name="status" id="form-status" required>
										<option value="">- Selecciona un estat -</option>
										<option value="1"<?php echo $row['status']==1 ? " selected" : ""; ?>>Completada</option>
										<option value="2"<?php echo $row['status']==2 ? " selected" : ""; ?>>En procés</option>
										<option value="3"<?php echo $row['status']==3 ? " selected" : ""; ?>>Parcialment completada</option>
										<option value="4"<?php echo $row['status']==4 ? " selected" : ""; ?>>Abandonada</option>
										<option value="5"<?php echo $row['status']==5 ? " selected" : ""; ?>>Cancel·lada</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-status" class="mandatory">Números i títols dels capítols</label> <?php print_helper_box('Números i títols dels capítols', 'Indica com es mostraran els capítols al web públic.\n\nSi es mostren els números, tots els capítols numerats mostraran «Capítol X:» abans del títol del capítol (si n’hi ha).\n\nSi només es mostren els títols, serà obligatori introduir un títol per a cada capítol penjat, i aquest títol serà l’únic que es mostrarà.\n\nLes versions de continguts de tipus «Film» o «One-shot» no permeten mai mostrar els números.\n\nSi l’obra que edites utilitza una altra paraula en lloc de «Capítol» (per exemple, «Cançó» o «Història»), cal que seleccionis l’opció de mostrar únicament el títol i incloguis «Cançó X:» abans de cada títol de capítol.'); ?>
									<select class="form-select" name="show_episode_numbers" id="form-show_episode_numbers" required>
<?php
	if ($series['subtype']!='movie' && $series['subtype']!='oneshot') {
?>
										<option value="1"<?php echo $row['show_episode_numbers']==1 ? " selected" : ""; ?>>Mostra el número i el títol dels capítols</option>
<?php
	}
?>
										<option value="0"<?php echo $row['show_episode_numbers']==0 ? " selected" : ""; ?>>Mostra només el títol dels capítols</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-storage_processing">Recomanacions</label> <?php print_helper_box('Recomanacions', 'Indica al sistema de recomanacions si pot recomanar aquesta versió.\n\n• No la recomanis mai: No es mostrarà mai a les recomanacions destacades.\n• Recomana-la aleatòriament: Pot aparèixer a les recomanacions destacades.\n• Recomana-la sempre (cas especial): Apareixerà sempre a les recomanacions destacades. Està pensat per a quan s’acabi una obra molt destacable i no hauria de romandre en aquest estat més de 4 setmanes.\n• Recomana-la sempre (obra de temporada): Apareixerà sempre a les recomanacions destacades. Està pensat per a obres en procés de publicació que estiguin en emissió. Quan deixin d’estar en emissió, cal tornar a marcar «Recomana-la aleatòriament».\n\nEn cas de dubte, selecciona «Recomana-la aleatòriament».'); ?>
									<select name="featurable_status" class="form-select">
										<option value="0"<?php echo $row['featurable_status']==0 ? " selected" : ""; ?>>No la recomanis mai</option>
										<option value="1"<?php echo $row['featurable_status']==1 ? " selected" : ""; ?>>Recomana-la aleatòriament</option>
										<option value="2"<?php echo $row['featurable_status']==2 ? " selected" : ""; ?>>Recomana-la sempre (cas especial)</option>
										<option value="3"<?php echo $row['featurable_status']==3 ? " selected" : ""; ?>>Recomana-la sempre (obra de temporada)</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="mb-3">
									<label>Imatge de portada<span class="mandatory"></span> <?php print_helper_box('Imatge de portada', 'Aquesta imatge s’utilitzarà al web per a identificar aquesta versió.\n\nLa imatge ha de ser en format JPEG, fer 300x400 píxels o més i ocupar menys de 150 KiB.\n\nSi ocupa més, redueix-ne la resolució o la qualitat de la compressió JPEG.\n\nSi en fas una versió localitzada en català, el resultat serà força més bonic.'); ?><br><small class="text-muted">(JPEG, ≥300x400, ≤150 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/covers/version_'.$row['id'].'.jpg');
?>
									<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="form-control d-none" name="image" type="file" id="form-image" accept="image/jpeg" value="" onchange="checkImageUpload(this, 153600, 'image/jpeg', 300, 400, 4096, 4096, 'form-image-preview', 'form-image-preview-link','form-image_url');">
									<input class="form-control" name="image_url" type="hidden" id="form-image_url" value="">
								</div>
							</div>
							<div class="col-sm-1">
								<div class="mb-3">
									<a id="form-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/covers/version_'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/covers/version_'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-image-preview" style="width: 71px; height: 100px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/covers/version_'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/covers/version_'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="mb-3">
									<label>Imatge de capçalera<span class="mandatory"></span> <?php print_helper_box('Imatge de capçalera', 'Aquesta imatge s’utilitzarà a la capçalera de la fitxa de la versió i també de fons a l’apartat de recomanacions.\n\nLa imatge ha de ser en format JPEG, fer 1920x400 píxels o més i ocupar menys de 300 KiB.\n\nSi ocupa més, redueix-ne la resolució o la qualitat de la compressió JPEG.'); ?><br><small class="text-muted">(JPEG, ≥1920x400, ≤300 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/featured/version_'.$row['id'].'.jpg');
?>
									<label for="form-featured_image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="d-none" name="featured_image" type="file" accept="image/jpeg" id="form-featured_image" onchange="checkImageUpload(this, 307200, 'image/jpeg', 1920, 400, 4096, 4096, 'form-featured-image-preview', 'form-featured-image-preview-link');">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<a id="form-featured-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/featured/version_'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/featured/version_'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-featured-image-preview" style="width: 480px; height: 100px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/featured/version_'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/featured/version_'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
						</div>
						<div class="row m-0 mb-3">
							<label class="col-sm p-0" for="form-synopsis">Sinopsi<span class="mandatory"></span> <?php print_helper_box('Sinopsi', 'Resum de l’argument de l’obra en un màxim de 5 o 6 línies.\n\nSi t’és útil, pots copiar-la d’altres webs o importar-la de '.$external_source.', però cal que la tradueixis al català.\n\nS’hi admet **negreta** i __cursiva__.'); ?></label>
							<button type="button" id="import-from-mal" class="btn btn-primary btn-sm col-sm-3 mb-1">
								<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm me-1 fa-width-auto" role="status" aria-hidden="true"></span>
								<span id="import-from-mal-not-loading" class="fa fa-cloud-arrow-down pe-2 fa-width-auto"></span>Importa la portada i la sinopsi de <?php echo $external_source; ?>
							</button>
							<textarea class="form-control" name="synopsis" id="form-synopsis" required style="height: 150px;" oninput="synopsisChanged=true;"><?php echo htmlspecialchars(str_replace('&#039;',"'",html_entity_decode($row['synopsis']))); ?></textarea>
						</div>
						<div class="mb-3">
							<label for="form-division-list">Títols i portades de cada divisió<span class="mandatory"></span> <?php print_helper_box($division_titles, 'Introdueix el títol de cada divisió i, opcionalment, una imatge de portada.'.($type!='manga' ? '\n\nAquest títol es farà servir per a identificar la divisió, però també per a anunciar les novetats en les publicacions a les xarxes.\n\nEl títol no ha de ser mai un nom genèric com «Temporada X», «Capítols especials» ni «OVAs», perquè en alguns llocs del web i a les xarxes socials es fa servir el títol de la divisió sense esmentar-ne la sèrie. Per tant, en tot cas, caldria posar-hi «Nom de la sèrie - Temporada X», «Nom de la sèrie - Especials» o «Nom de la sèrie - OVAs».' : '\n\nAquest títol es farà servir per a identificar la divisió.').'\n\nSi no es penja una imatge de portada específica de la divisió, s’hi mostrarà la imatge de portada de la versió.\n\nLes imatges han de ser en format JPEG, fer 300x400 píxels o més i ocupar menys de 150 KiB.\n\nSi ocupen més, redueix-ne la resolució o la qualitat de la compressió JPEG.\n\nSi en fas una versió localitzada en català, el resultat serà força més bonic.'); ?> <small class="text-muted">(JPEG, ≥300x400, ≤150 KiB)</small></label>
							<div class="row flex" id="form-division-list">
<?php
		foreach ($divisions as $division) {
?>
								<div class="col-sm-2 text-center pe-1 ps-1 align-self-end">
										<label for="form-division-title-<?php echo $division['id']; ?>" style="font-style: italic;"><?php echo htmlspecialchars($division['name']); ?></label>
										<input id="form-division-title-<?php echo $division['id']; ?>" name="form-division-title-<?php echo $division['id']; ?>" class="form-control text-center" value="<?php echo htmlspecialchars($division['title']); ?>" maxlength="200" placeholder="- Introdueix un títol -" required/>
									<br>
<?php
		$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg');
?>
										<img id="form-division_cover_<?php echo $division['id']; ?>_preview" style="width: 128px; height: 180px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px; margin-bottom: 0.5em;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg" data-original="'.STATIC_URL.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg"' : ''; ?> alt=""><br />
										<label for="form-division_cover_<?php echo $division['id']; ?>" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
										<input id="form-division_cover_<?php echo $division['id']; ?>" name="division_cover_<?php echo $division['id']; ?>" type="file" class="d-none" accept="image/jpeg" onchange="checkImageUpload(this, 153600, 'image/jpeg', 300, 400, 4096, 4096, 'form-division_cover_<?php echo $division['id']; ?>_preview');"/>
								</div>
<?php
		}
?>
							</div>
						</div>
<?php
	if ($type!='manga') {
?>
						<div class="row">
							<div class="col-sm-8">
								<div class="mb-3">
									<label for="form-storage_folder"><span class="mandatory">Directori d’emmagatzematge</span></label> <?php print_helper_box('Directori d’emmagatzematge', 'Camp informatiu i no modificable que indica el directori dels servidors d’emmagatzematge i de streaming on es copiaran els fitxers.'); ?>
									<input id="form-storage_folder" name="storage_folder" type="text" class="form-control" value="<?php echo htmlspecialchars($row['storage_folder']); ?>" maxlength="200" required readonly<?php echo (!empty($row['id']) && empty($row['is_hidden'])) ? ' data-is-set="1"' : '' ; ?>/>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-storage_processing"><span class="mandatory">Processament de fitxers</span></label> <?php print_helper_box('Processament de fitxers', 'Camp que defineix què es fa amb els fitxers: si s’importen mantenint-ne una còpia al servidor d’emmagatzematge o no es desen i es copien directament al servidor de streaming.\n\nEn cas de dubte, i si un administrador no t’indica el contrari, deixa marcada l’opció per defecte.'); ?>
									<select name="storage_processing" class="form-select" onchange="if(!confirm('Llevat que un administrador t’ho indiqui, no hauries de modificar aquesta opció, ja que afectarà la importació de fitxers. Segur que vols fer aquest canvi?')) this.selectedIndex=0;">
										<option value="1"<?php echo $row['storage_processing']==1 ? " selected" : ""; ?>>Desa una còpia dels fitxers originals</option>
										<option value="5"<?php echo $row['storage_processing']==5 ? " selected" : ""; ?>>No desis cap còpia dels fitxers originals</option>
									</select>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-remote_folders-list">Carpetes remotes</label> <?php print_helper_box('Carpetes remotes', 'Aquest apartat és opcional i permet definir comptes i carpetes de MEGA per a obtenir-ne automàticament els capítols una vegada s’hi copiïn i no haver d’editar-ne la versió a Fansubs.cat.\n\nL’obtenció es fa cada hora recorrent aquestes carpetes i comprovant si hi ha nous fitxers amb noms de fitxer que encaixin amb un patró concret i associant-los al capítol corresponent.\n\nNo és recomanable fer servir aquest sistema si no coneixes en detall com funciona.\n\nConsulta’n més informació al manual del tauler d’administració i demana ajuda a un administrador si tens dubtes.'); ?>
							<div class="container" id="form-remote_folders-list">
<?php

		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$resultfo = query("SELECT f.* FROM remote_folder f LEFT JOIN remote_account ra ON f.remote_account_id=ra.id WHERE f.version_id=".escape($_GET['id'])." ORDER BY f.id ASC");
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
									<div class="w-100 column ps-0 pe-0">
										<select id="form-remote_folders-list-remote_account_id-XXX" name="form-remote_folders-list-remote_account_id-XXX" onchange="if ($(this).find('option:selected').eq(0).hasClass('not-syncable')){$('#form-remote_folders-list-is_active-XXX').prop('disabled',true);} else { $('#form-remote_folders-list-is_active-XXX').prop('disabled',false); }" class="form-select d-none">
											<option value="">- Selecciona un compte remot -</option>
<?php
		if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
			$where = 'a.fansub_id='.$_SESSION['fansub_id'];
		} else {
			$where = '1';
		}

		$resulta = query("SELECT a.* FROM remote_account a LEFT JOIN fansub f ON a.fansub_id=f.id WHERE $where ORDER BY a.fansub_id IS NULL DESC, f.name ASC, NATURAL_SORT_KEY(a.name) ASC");
		while ($arow = mysqli_fetch_assoc($resulta)) {
?>
											<option value="<?php echo $arow['id']; ?>"><?php echo htmlspecialchars($arow['name']); ?></option>
<?php
		}
		mysqli_free_result($resulta);
?>
										</select>
										<select id="form-remote_folders-list-division_id-XXX" name="form-remote_folders-list-division_id-XXX" class="form-select d-none">
											<option value="">- Qualsevol -</option>
<?php
		$resultss = query("SELECT d.id, d.series_id, TRIM(d.number)+0 number, d.name, d.number_of_episodes, d.external_id FROM division d WHERE d.series_id=".$series['id']." AND d.number_of_episodes>0 ORDER BY d.number ASC");
		while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
											<option value="<?php echo $ssrow['id']; ?>"><?php echo htmlspecialchars($ssrow['name']); ?></option>
<?php
		}
		mysqli_free_result($resultss);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="remote_folders-list-table" data-count="<?php echo count($remote_folders); ?>">
											<thead>
												<tr>
													<th style="width: 20%;">Compte<span class="mandatory"></span> <?php print_helper_box('Compte', 'Selecciona el compte de MEGA (prèviament donat d’alta amb el seu identificador de sessió) on es consultaran els capítols penjats.'); ?></th>
													<th>Carpeta<span class="mandatory"></span> <?php print_helper_box('Carpeta', 'Introdueix el camí complet a la carpeta del compte (per exemple, «Sèries/Nom de la sèrie/Temporada 1/1080p».'); ?></th>
													<th style="width: 10%;">Resolució<span class="mandatory"></span> <?php print_helper_box('Resolució', 'Resolució per defecte que s’associarà als nous capítols que es trobin en aquesta carpeta.\n\nUna vegada convertits per a streaming, es corregirà al valor exacte.'); ?></th>
													<th style="width: 10%;">Durada<span class="mandatory"></span> <?php print_helper_box('Durada', 'Durada per defecte que s’associarà als nous capítols que es trobin en aquesta carpeta.\n\nUna vegada convertits per a streaming, es corregirà al valor exacte.'); ?></th>
													<th style="width: 15%;">Divisió <?php print_helper_box('Divisió', 'Divisió a què s’associaran els capítols que es trobin en aquesta carpeta.\n\nÉs necessari informar-la si la numeració es reinicia a cada divisió, perquè si no, seria impossible saber a quina divisió pertany el capítol.'); ?></th>
													<th class="text-center" style="width: 10%;">Sincronitza <?php print_helper_box('Sincronitza', 'Indica que la sincronització d’aquesta carpeta està activada i que, per tant, es comprovarà cada hora si hi ha nous fitxers.\n\nSi es desactiva, no es faran les comprovacions.\n\nPot deixar-se desactivada si únicament se’n vol fer una importació manual amb el botó «Actualitza els enllaços ara».'); ?></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="remote_folders-list-table-empty" class="<?php echo count($remote_folders)>0 ? 'd-none' : ''; ?>">
													<td colspan="7" class="text-center">- No hi ha configurada cap carpeta -</td>
												</tr>
<?php
		for ($j=0;$j<count($remote_folders);$j++) {
?>
												<tr id="form-remote_folders-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" name="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" onchange="if ($(this).find('option:selected').eq(0).hasClass('not-syncable')){$('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',true);} else { $('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',false); }" class="form-select" required>
															<option value="">- Selecciona un compte remot -</option>
<?php
			if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
				$where = 'a.id='.$remote_folders[$j]['remote_account_id'].' OR a.fansub_id='.$_SESSION['fansub_id'];
			} else {
				$where = '1';
			}

			$resulta = query("SELECT a.* FROM remote_account a LEFT JOIN fansub f ON a.fansub_id=f.id WHERE $where ORDER BY a.fansub_id IS NULL DESC, f.name ASC, NATURAL_SORT_KEY(a.name) ASC");
			while ($arow = mysqli_fetch_assoc($resulta)) {
?>
															<option value="<?php echo $arow['id']; ?>"<?php echo $remote_folders[$j]['remote_account_id']==$arow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($arow['name']); ?></option>
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
														<input id="form-remote_folders-list-default_resolution-<?php echo $j+1; ?>" name="form-remote_folders-list-default_resolution-<?php echo $j+1; ?>" class="form-control" value="<?php echo htmlspecialchars($remote_folders[$j]['default_resolution']); ?>" maxlength="200" required placeholder="- Tria -" list="resolution-options"/>
													</td>
													<td>
														<input id="form-remote_folders-list-default_duration-<?php echo $j+1; ?>" name="form-remote_folders-list-default_duration-<?php echo $j+1; ?>" type="time" step="1" class="form-control" value="<?php echo convert_to_hh_mm_ss($remote_folders[$j]['default_duration']); ?>" required/>
													</td>
													<td>
														<select id="form-remote_folders-list-division_id-<?php echo $j+1; ?>" name="form-remote_folders-list-division_id-<?php echo $j+1; ?>" class="form-select">
															<option value="">- Qualsevol -</option>
<?php
			$resultss = query("SELECT d.* FROM division d WHERE d.series_id=".$series['id']." ORDER BY d.number ASC");
			while ($ssrow = mysqli_fetch_assoc($resultss)) {
?>
															<option value="<?php echo $ssrow['id']; ?>"<?php echo $remote_folders[$j]['division_id']==$ssrow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($ssrow['name']); ?></option>
<?php
			}
			mysqli_free_result($resultss);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<input id="form-remote_folders-list-is_active-<?php echo $j+1; ?>" name="form-remote_folders-list-is_active-<?php echo $j+1; ?>" type="checkbox" value="1"<?php echo $remote_folders[$j]['is_active']==1? " checked" : ""; ?>/>
													</td>
													<td class="text-center align-middle">
														<button id="form-remote_folders-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionRemoteFolderRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button>
													</td>
												</tr>
<?php
		}
?>
											</tbody>
										</table>
									</div>
									<div class="mb-3 row w-100 ms-0">
										<div class="col-sm text-start" style="padding-left: 0; padding-right: 0">
											<button onclick="addVersionRemoteFolderRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix una carpeta</button>
										</div>
										<div class="col-sm text-end" style="padding-left: 0; padding-right: 0">
											<select id="import-type" class="form-select form-control-sm form-inline" title="Indica el tipus de sincronització desitjada en aquesta actualització d’enllaços: tots els comptes o només els marcats." style="width: auto; display: inline; font-size: 78%;">
												<option value="all" selected>Utilitza tots els comptes</option>
												<option value="sync">Només els sincronitzats</option>
											</select> →
											<button type="button" id="import-from-mega" class="btn btn-primary btn-sm">
												<span id="import-from-mega-loading" class="d-none spinner-border spinner-border-sm me-1 fa-width-auto" role="status" aria-hidden="true"></span>
												<span id="import-from-mega-not-loading" class="fa fa-redo pe-2 fa-width-auto"></span>Actualitza els enllaços ara
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-none alert alert-warning" id="import-failed-results">
							<div><span class="fa fa-exclamation-triangle me-2"></span> Els següents elements no s’han importat perquè no tenen el format correcte o perquè els capítols no existeixen a la fitxa <?php echo $content_prep; ?>. Afegeix-los a mà on correspongui. Recorda que els fitxers només s’importen automàticament si tenen el format «<i>text</i><u><b> - 123</b></u><i>text</i>.mp4».</div>
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
?>
						<div class="mb-3">
							<label for="form-episode-list">Capítols, variants i <?php echo $type=='manga' ? 'fitxers' : 'enllaços'; ?></label> <?php print_helper_box('Capítols, variants i '.($type=='manga' ? 'fitxers' : 'enllaços'), 'En aquest apartat s’especifiquen els títols de cada capítol, les variants que té i els enllaços a MEGA de cadascuna de les variants, juntament amb la seva resolució i durada.\n\nNormalment, els capítols sols tenen una única variant, anomenada «Única», però se’n poden afegir més i canviar-ne el nom (per exemple, si s’edita el mateix capítol en dialectes diferents, una versió censurada i una sense, etcètera).\n\nCal especificar un enllaç de MEGA en cada variant i assignar-li una resolució, una durada i, opcionalment, un comentari.'); ?>
							<div class="container" id="form-episode-list">
								<datalist id="resolution-options">
									<option value="1080p">
									<option value="720p">
									<option value="480p">
									<option value="360p">
								</datalist>
								<div id="warning-no-numbers" class="alert alert-warning<?php echo ($row['show_episode_numbers']==0 && $series['subtype']!='movie' && $series['subtype']!='oneshot') ? '' : ' d-none'; ?>">
									<div><span class="fa fa-exclamation-triangle me-2"></span>Aquest <?php echo $content; ?> <b>NO</b> mostra els números de capítols a la fitxa pública. Si vols mostrar els números de manera diferent a la per defecte, afegeix-los on calguin.</div>
								</div>
								<div class="accordion" id="accordion">
									<div class="accordion-item">
										<h2 class="accordion-header">
											<button class="accordion-button<?php echo count($divisions)>2 ? ' collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#division-collapse-<?php echo $episodes[0]['division_id']; ?>" aria-expanded="<?php echo count($divisions)>2 ? 'false' : 'true'; ?>" aria-controls="division-collapse-<?php echo $episodes[0]['division_id']; ?>"><b>Capítols de «<?php echo $episodes[0]['division_name']; ?>»</b></button>
										</h2>
										<div class="accordion-collapse collapse<?php echo count($divisions)>2 ? '' : ' show'; ?>" id="division-collapse-<?php echo $episodes[0]['division_id']; ?>">
											<div class="accordion-body">
<?php
	$previous_division_id=$episodes[0]['division_id'];
	for ($i=0;$i<count($episodes);$i++) {
		if ($previous_division_id!=$episodes[$i]['division_id'] && $episodes[$i]['id']!='{template_id}') {
			$previous_division_id = $episodes[$i]['division_id'];
?>
											</div>
										</div>
									</div>
									<div class="accordion-item">
										<h2 class="accordion-header">
											<button class="accordion-button<?php echo count($divisions)>2 ? ' collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#division-collapse-<?php echo $episodes[$i]['division_id']; ?>" aria-expanded="<?php echo count($divisions)>2 ? 'false' : 'true'; ?>" aria-controls="division-collapse-<?php echo $episodes[$i]['division_id']; ?>"><b>Capítols de «<?php echo $episodes[$i]['division_name']; ?>»</b></button>
										</h2>
										<div class="accordion-collapse collapse<?php echo count($divisions)>2 ? '' : ' show'; ?>" id="division-collapse-<?php echo $episodes[$i]['division_id']; ?>">
											<div class="accordion-body">
<?php
		}
		$episode_name=$episodes[$i]['episode_title'];
		if (!empty($episodes[$i]['linked_episode_id'])){
			$resultle=query("SELECT e.id,
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IF(e.number IS NOT NULL,
							IF(s.number_of_episodes=1,
								s.name,
								CONCAT(d.name, ' - Film ', REPLACE(TRIM(e.number)+0, '.', ','))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ',')),
							CONCAT(d.name, ' - ', e.description)
						)
					) episode_title
					FROM episode e
					LEFT JOIN division d ON e.division_id=d.id
					LEFT JOIN series s ON e.series_id=s.id
					WHERE s.type='$type' AND s.subtype='movie' AND e.id=".$episodes[$i]['linked_episode_id']);
			$linked_episode = mysqli_fetch_assoc($resultle);
			mysqli_free_result($resultle);
			$episode_name=$linked_episode['episode_title'].' <i>[FILM ENLLAÇAT]</i>';
		}

		if (!empty($_GET['id']) && is_numeric($_GET['id']) && $episodes[$i]['id']!='{template_id}') {
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
		
		if ($episodes[$i]['id']=='{template_id}') {
?>
												<script id="add-episode-from-version-template" type="text/plain">
<?php
		}
?>
												<div class="mb-3 episode-container<?php echo !empty($episodes[$i]['linked_episode_id']) ? ' linked-episode-container' : ''; ?>">
													<label><span class="fa <?php echo $type=='manga' ? 'fa-book-open' : (!empty($episodes[$i]['linked_episode_id']) ? 'fa-link' : 'fa-film'); ?> pe-2 text-primary"></span><?php echo $episode_name; ?></label><br>
													<label for="form-files-list-<?php echo $episodes[$i]['id']; ?>-title">Títol del capítol</label> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Títol del capítol" data-bs-contents="Títol que es mostrarà al públic al web per a aquest capítol.\n\nSi no es mostren els números dels capítols, cal que hi introdueixis sempre un títol.\n\nSi es mostren els números i és un capítol numerat, no és necessari introduir-hi el títol, però si en té, cal fer-ho.\n\nSi és un capítol no numerat, cap introduir-ne sempre el títol."></small>
													<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" type="text" class="form-control episode-title-input<?php echo (!empty($episodes[$i]['number']) && empty($episodes[$i]['linked_episode_id'])) ? ' episode-title-input-numbered' : ''; ?>" value="<?php echo htmlspecialchars($episodes[$i]['title']); ?>" maxlength="500" data-episode-number="<?php echo $episodes[$i]['formatted_number']; ?>" placeholder="<?php echo (!empty($episodes[$i]['number']) && empty($episodes[$i]['linked_episode_id']) && $row['show_episode_numbers']==1) ? 'Capítol '.$episodes[$i]['formatted_number'] : '- Introdueix un títol -'; ?>"<?php echo !empty($episodes[$i]['linked_episode_id']) ? ' required' : ''; ?>/>
<?php
		if (empty($episodes[$i]['linked_episode_id'])) {
?>
													<div class="container mt-2" id="form-files-list-<?php echo $episodes[$i]['id']; ?>">
														<div class="row mb-3">
															<div class="w-100 column ps-0 pe-0">
																<table class="table table-bordered table-hover table-sm" id="files-list-table-<?php echo $episodes[$i]['id']; ?>" data-count="<?php echo max(count($files),1); ?>">
																	<thead>
																		<tr>
																			<th style="width: 8%;">Variant<span class="mandatory"></span> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Variant" data-bs-contents="Cada capítol pot tenir diferents variants (per dialectes, estils, etc.). Cada variant es mostra com un capítol diferent a la fitxa pública i amb el nom de variant indicat.\nEn condicions normals, només n’hi sol haver una, titulada «Única».\nSi només hi ha una sola variant, el nom de la variant no es mostra enlloc."></small></th>
<?php
			if ($type=='manga') {
?>
																			<th>Arxiu<span class="mandatory"></span> <?php print_helper_box('Arxiu', 'Indica l’arxiu que ja hi ha pujat d’aquest capítol, o els detalls de l’arxiu que se seleccioni per a pujar-lo.'); ?></th>
																			<th style="width: 16%;">Pujada <?php print_helper_box('Pujada', 'Permet seleccionar un arxiu local (ZIP, RAR o CBZ) amb les imatges d’aquest capítol per a pujar-lo.\n\nEl contingut de l’arxiu es descomprimirà i penjarà al servidor de fitxers.\n\nL’arxiu ha de contenir fitxers d’imatge JPEG o PNG i, opcionalment, un fitxer d’àudio MP3 o OGG que es reproduirà com a música de fons.'); ?></th>
<?php
			} else {
?>
																			<th>Enllaços de streaming / Resolució<span class="mandatory"></span> <?php print_helper_box('Enllaços de streaming / Resolució', 'Cal especificar un enllaç de MEGA amb el capítol amb el format adequat (MP4, H264, AAC i subtítols cremats al vídeo) i la seva resolució. Una vegada el fitxer s’hagi convertit i copiat al servidor de streaming, el sistema hi crearà automàticament un altre enllaç començat per «storage://», que no s’ha d’editar ni esborrar.\n\nSi es vol canviar el fitxer, sols cal canviar-ne l’enllaç de MEGA i el sistema ja detectarà que ha canviat, esborrarà l’enllaç propi, el tornarà a baixar i convertir, i finalment el tornarà a afegir.'); ?></th>
																			<th style="width: 10%;">Durada<span class="mandatory"></span> <?php print_helper_box('Durada', 'S’hi ha d’especificar la durada del capítol en hores, minuts i segons.\n\nDepenent de la configuració regional, és possible que el teu navegador mostri un selector d’hores en format AM/PM. Les 00 hores corresponen a les 12 AM, les 01 a les 01 AM, i així successivament.'); ?></th>
<?php
			}
?>
																			<th style="width: 15%;">Comentaris <?php print_helper_box('Comentaris', 'Normalment se sol deixar buit, però es pot fer servir en cas que es desitgi per a aportar informació addicional a la fitxa pública (per exemple, per a indicar que hi ha algun problema amb el fitxer).'); ?></th>
																			<th class="text-center" style="width: 8%;">Perduda <?php print_helper_box('Perduda', 'S’utilitza per a indicar que aquesta variant es va editar, però s’ha perdut amb el pas del temps.\n\nS’utilitza únicament en material d’antics fansubs anteriors a la creació de Fansubs.cat.\n\nEn situacions normals, cal deixar-ho sempre desmarcat.'); ?></th>
																			<th class="text-center" style="width: 8%;">Accions</th>
																		</tr>
																	</thead>
																	<tbody>
<?php
			for ($j=0;$j<count($files);$j++) {
?>
																		<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-<?php echo $j+1; ?>">
																			<td>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['variant_name']); ?>" maxlength="200" placeholder="- Nom -" required/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['id']; ?>"/>
																			</td>
<?php
				if ($type=='manga') {
?>
																			<td class="align-middle">
																				<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($files[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check"></span> Ja hi ha pujat l’arxiu <strong>'.htmlspecialchars($files[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times"></span> No hi ha cap arxiu pujat.</span>'; ?></div>
																			</td>
																			<td class="align-middle">
																				<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($files[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pe-2"></span><?php echo !empty($files[$j]['original_filename']) ? 'Canvia l’arxiu...' : 'Puja un arxiu...' ; ?></label>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz,.cbr" class="form-control d-none" onchange="uncompressFile(this);"/>
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
																							<td class="ps-0 pt-0 pb-0 border-0">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['id']); ?>"/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="- Tria -"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																							</td>
																						</tr>
<?php
					}
					if (count($files[$j]['links'])==0) {
?>
																						<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1-1" style="background: none;">
																							<td class="ps-0 pt-0 pb-0 border-0">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value=""/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																							</td>
																						</tr>
<?php
					}
?>
																					</tbody>
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
																				<button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn text-primary btn-sm fa p-1 fa-width-auto fa-arrows-split-up-and-left fa-rotate-180" title="Afegeix una variant addicional"></button>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-<?php echo $j+1; ?>" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="Afegeix un enllaç addicional"></button>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-<?php echo $j+1; ?>" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="Suprimeix les dades d’aquest fitxer"></button>
																			</td>
																		</tr>
<?php
			}
			if (count($files)==0) {
?>
																		<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-1">
																			<td>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" type="text" class="form-control" value="Única" maxlength="200" placeholder="- Nom -" required/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" type="hidden" value="-1"/>
																			</td>
<?php
				if ($type=='manga') {
?>
																			<td class="align-middle">
																				<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-1" class="small"><span style="color: gray;"><span class="fa fa-times"></span> No hi ha cap arxiu pujat.</span></div>
																			</td>
																			<td class="align-middle">
																				<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" class="btn btn-sm btn-primary w-100"><span class="fa fa-upload pe-2"></span>Puja un arxiu...</label>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" type="file" accept=".zip,.rar,.cbz,.cbr" class="form-control d-none" onchange="uncompressFile(this);"/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-length-1" type="hidden" value="0"/>
																			</td>
<?php
				} else {
?>
																			<td>
																				<table class="w-100" id="links-list-table-<?php echo $episodes[$i]['id']; ?>-1" data-count="1">
																					<tbody>
																						<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1-1" style="background: none;">
																							<td class="ps-0 pt-0 pb-0 border-0">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value="-1"/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																							</td>
																						</tr>
																					</tbody>
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
																				<button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn text-primary btn-sm fa p-1 fa-width-auto fa-arrows-split-up-and-left fa-rotate-180" title="Afegeix una variant addicional"></button>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-1" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="Afegeix un enllaç addicional"></button>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-1" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="Suprimeix les dades d’aquest fitxer"></button>
																			</td>
																		</tr>
<?php
			}
?>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
<?php
		}
?>
												</div>
<?php
		if ($episodes[$i]['id']=='{template_id}') {
?>
												</script>
<?php
		}
	}
?>
											</div>
										</div>
									</div>
								</div>
								<div class="w-100 text-center pt-3">
									<button data-bs-toggle="modal" data-bs-target="#add-episode-from-version-modal" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix un capítol inexistent</button>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-extras-list">Material extra</label> <?php print_helper_box('Material extra', 'El material extra són elements addicionals de l’obra que no es consideren capítols com a tals, generalment de curta durada: openings, tràilers, material addicional, notes, etc.\n\nEl funcionament del material extra és el mateix que el dels capítols normals, però en aquest cas, no existeixen les variants (si n’hi ha, es poden afegir com a múltiples extres). A banda dels mateixos camps que amb els capítols normals, també hi ha el camp «Títol», que és el títol que tindrà aquell extra en concret.\n\nSe’n poden afegir més prement el botó «Afegeix un altre material extra».'); ?>
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
								<div class="mb-3">
									<div class="container" id="form-extras-list">
										<div class="row mb-3">
											<div class="w-100 column ps-0 pe-0">
												<table class="table table-bordered table-hover table-sm" id="extras-list-table" data-count="<?php echo count($extras); ?>">
													<thead>
														<tr>
															<th style="width: 20%;">Títol<span class="mandatory"></span> <?php print_helper_box('Títol', 'Títol que tindrà aquest contingut extra al web públic.'); ?></th>
<?php
		if ($type=='manga') {
?>
															<th>Arxiu<span class="mandatory"></span> <?php print_helper_box('Arxiu', 'Indica l’arxiu que ja hi ha pujat d’aquest extra, o els detalls de l’arxiu que se seleccioni per a pujar-lo.'); ?></th>
															<th style="width: 16%;">Pujada <?php print_helper_box('Pujada', 'Permet seleccionar un arxiu local (ZIP, RAR o CBZ) amb les imatges d’aquest capítol per a pujar-lo.\n\nEl contingut de l’arxiu es descomprimirà i penjarà al servidor de fitxers.\n\nL’arxiu ha de contenir fitxers d’imatge JPEG o PNG i, opcionalment, un fitxer d’àudio MP3 o OGG que es reproduirà com a música de fons.'); ?></th>
<?php
		} else {
?>
															<th>Enllaços de streaming / Resolució<span class="mandatory"></span> <?php print_helper_box('Enllaços de streaming / Resolució', 'Cal especificar un enllaç de MEGA amb el contingut amb el format adequat (MP4, H264, AAC i subtítols cremats al vídeo) i la seva resolució. Una vegada el fitxer s’hagi convertit i copiat al servidor de streaming, el sistema hi crearà automàticament un altre enllaç començat per «storage://», que no s’ha d’editar ni esborrar.\n\nSi es vol canviar el fitxer, sols cal canviar-ne l’enllaç de MEGA i el sistema ja detectarà que ha canviat, esborrarà l’enllaç propi, el tornarà a baixar i convertir, i finalment el tornarà a afegir.'); ?></th>
															<th style="width: 10%;">Durada<span class="mandatory"></span> <?php print_helper_box('Durada', 'S’hi ha d’especificar la durada del contingut extra en hores, minuts i segons.\n\nDepenent de la configuració regional, és possible que el teu navegador mostri un selector d’hores en format AM/PM. Les 00 hores corresponen a les 12 AM, les 01 a les 01 AM, i així successivament.'); ?></th>
<?php
		}
?>
															<th style="width: 15%;">Comentaris <?php print_helper_box('Comentaris', 'Normalment se sol deixar buit, però es pot fer servir en cas que es desitgi per a aportar informació addicional a la fitxa pública (per exemple, per a indicar que hi ha algun problema amb el fitxer).'); ?></th>
															<th class="text-center" style="width: 8%;">Accions</th>
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
																<input id="form-extras-list-name-<?php echo $j+1; ?>" name="form-extras-list-name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['extra_name']); ?>" maxlength="200" required placeholder="- Introdueix un títol -"/>
																<input id="form-extras-list-id-<?php echo $j+1; ?>" name="form-extras-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['id']; ?>"/>
															</td>
<?php
		if ($type=='manga') {
?>
															<td class="align-middle">
																<div id="form-extras-list-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($extras[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check"></span> Ja hi ha pujat l’arxiu <strong>'.htmlspecialchars($extras[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times"></span> No hi ha cap arxiu pujat.</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-extras-list-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($extras[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pe-2"></span><?php echo !empty($extras[$j]['original_filename']) ? 'Canvia l’arxiu...' : 'Puja un arxiu...' ; ?></label>
																<input id="form-extras-list-file-<?php echo $j+1; ?>" name="form-extras-list-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz,.cbr" class="form-control d-none" onchange="uncompressFile(this);"/>
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
																			<td class="ps-0 pt-0 pb-0 border-0">
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="- Introdueix un enllaç -" oninput="$(this).attr('value',$(this).val());" required/>
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['id']); ?>"/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="- Tria -" required/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteExtraLinkRow(<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button>
																			</td>
																		</tr>
<?php
			}
?>
																	</tbody>
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
																<button id="form-extras-list-add_link-<?php echo $j+1; ?>" onclick="addExtraLinkRow(<?php echo $j+1; ?>);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="Afegeix un enllaç addicional"></button>
																<button id="form-extras-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionExtraRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="Suprimeix les dades d’aquest fitxer"></button>
															</td>
														</tr>
<?php
	}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionExtraRow();" type="button" class="btn btn-primary btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix un altre material extra</button></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3 text-center pt-2">
<?php
	if (!empty($row['id'])) {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_count_as_update" id="form-do_not_count_as_update" value="1">
								<label class="form-check-label" for="form-do_not_count_as_update">No moguis a «Darreres actualitzacions»</label>
							</div>
							<br />
<?php
		if ($_SESSION['username']=='Administrador' && $type!='manga') {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_recreate_storage_links" id="form-do_not_recreate_storage_links" value="1" onchange="if($(this).prop('checked')){if (confirm('IMPORTANT, LLEGEIX-ME:\nAquesta opció actualitzarà només els enllaços de MEGA, però els fitxers NO es baixaran al servidor de streaming i els usuaris finals no notaran el canvi. Si no has parlat amb cap administrador o no entens ben bé què vol dir això, si us plau, parla-hi abans d’activar aquesta opció.')) {$('#form-do_not_count_as_update').prop('checked',true);} else {$(this).prop('checked',false);}}">
								<label class="form-check-label" for="form-do_not_recreate_storage_links">No recreïs els enllaços d’emmagatzematge</label>
							</div>
							<br />
<?php
		}
	}
?>
							<button type="submit" name="action" value="<?php echo $row['id']!=NULL? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la versió"; ?></button>
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
