<?php
require_once(__DIR__.'/../common/initialization.inc.php');
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
		$page="anime";
		$header_title=lang('admin.version_edit.header.anime');
		$original_title_string=lang('admin.version_edit.original_title.anime');
		$not_imported_string=lang('admin.version_edit.not_imported.anime');
		$episode_list_string=lang('admin.version_edit.episode_list');
		$episode_list_help_string=lang('admin.version_edit.episode_list.help');
		$division_titles_and_covers_help_string=lang('admin.version_edit.division_titles_and_covers.help');
		$status_help_string=lang('admin.version_edit.status.help');
		$not_showing_numbers_string=lang('admin.version_edit.not_showing_numbers.anime');
		$external_source="MyAnimeList";
	break;
	case 'manga':
		$page="manga";
		$header_title=lang('admin.version_edit.header.manga');
		$original_title_string=lang('admin.version_edit.original_title.manga');
		$not_imported_string=lang('admin.version_edit.not_imported.manga');
		$episode_list_string=lang('admin.version_edit.episode_list.manga');
		$episode_list_help_string=lang('admin.version_edit.episode_list.help.manga');
		$division_titles_and_covers_help_string=lang('admin.version_edit.division_titles_and_covers.help.manga');
		$status_help_string=lang('admin.version_edit.status.help.manga');
		$not_showing_numbers_string=lang('admin.version_edit.not_showing_numbers.manga');
		$external_source="MyAnimeList";
	break;
	case 'liveaction':
		$page="liveaction";
		$header_title=lang('admin.version_edit.header.liveaction');
		$original_title_string=lang('admin.version_edit.original_title.liveaction');
		$not_imported_string=lang('admin.version_edit.not_imported.liveaction');
		$episode_list_string=lang('admin.version_edit.episode_list');
		$episode_list_help_string=lang('admin.version_edit.episode_list.help');
		$division_titles_and_covers_help_string=lang('admin.version_edit.division_titles_and_covers.help');
		$status_help_string=lang('admin.version_edit.status.help');
		$not_showing_numbers_string=lang('admin.version_edit.not_showing_numbers.liveaction');
		$external_source="MyDramaList";
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['series_id']) && is_numeric($_POST['series_id'])) {
			$data['series_id']=escape($_POST['series_id']);
		} else {
			crash(lang('admin.error.series_id_missing'));
		}
		if (!empty($_POST['status']) && is_numeric($_POST['status'])) {
			$data['status']=escape($_POST['status']);
		} else {
			crash(lang('admin.error.status_missing'));
		}
		if (!empty($_POST['fansub_1']) && is_numeric($_POST['fansub_1'])) {
			$data['fansub_1']=escape($_POST['fansub_1']);
		} else {
			crash(lang('admin.error.fansub_1_missing'));
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
			crash(lang('admin.error.storage_folder_missing'));
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
			crash(lang('admin.error.title_missing'));
		}
		if (!empty($_POST['alternate_titles'])) {
			$data['alternate_titles']="'".escape($_POST['alternate_titles'])."'";
		} else {
			$data['alternate_titles']="NULL";
		}
		if (!empty($_POST['slug'])) {
			$data['slug']=escape($_POST['slug']);
		} else {
			crash(lang('admin.error.slug_missing'));
		}
		if (!empty($_POST['synopsis'])) {
			$data['synopsis']=escape($_POST['synopsis']);
		} else {
			crash(lang('admin.error.synopsis_missing'));
		}

		$divisions=array();

		$resultd = query("SELECT d.* FROM division d WHERE d.number_of_episodes>0 AND d.series_id=".$data['series_id']);

		while ($rowd = mysqli_fetch_assoc($resultd)) {
			if (!empty($_POST['form-division-title-'.$rowd['id']])){
				$rowd['version_division_title']=escape($_POST['form-division-title-'.$rowd['id']]);
			} else {
				crash(lang('admin.error.version_division_title_missing'));
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
					crash(lang('admin.error.file_id_missing'));
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
					crash(lang('admin.error.file_length_missing'));
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
				crash(lang('admin.error.extra_id_missing'));
			}
			if (!empty($_POST['form-extras-list-name-'.$i])) {
				$extra['name']=escape($_POST['form-extras-list-name-'.$i]);
			} else {
				crash(lang('admin.error.extra_name_missing'));
			}
			if (!empty($_FILES['form-extras-list-file-'.$i]) && is_uploaded_file($_FILES['form-extras-list-file-'.$i]['tmp_name'])) {
				$extra['original_filename']=escape($_FILES['form-extras-list-file-'.$i]["name"]);
				$extra['original_filename_unescaped']=$_FILES['form-extras-list-file-'.$i]['name'];
				$extra['temporary_filename']=$_FILES['form-extras-list-file-'.$i]['tmp_name'];
			} else if ($type=='manga' && $extra['id']==-1) {
				crash(lang('admin.error.extra_file_missing'));
			}
			if (!empty($_POST['form-extras-list-length-'.$i])) {
				//This works for manga too because if the format is not in HH:MM:SS, the value is returned directly
				$extra['length']=escape(convert_from_hh_mm_ss($_POST['form-extras-list-length-'.$i]));
			} else {
				crash(lang('admin.error.extra_length_missing'));
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
				crash(lang('admin.error.remote_folder_id_missing'));
			}
			if (!empty($_POST['form-remote_folders-list-remote_account_id-'.$i])) {
				$remote_folder['remote_account_id']=escape($_POST['form-remote_folders-list-remote_account_id-'.$i]);
			} else {
				crash(lang('admin.error.remote_folder_remote_account_id_missing'));
			}
			if (!empty($_POST['form-remote_folders-list-folder-'.$i])) {
				$remote_folder['folder']=escape($_POST['form-remote_folders-list-folder-'.$i]);
			} else {
				crash(lang('admin.error.remote_folder_folder_missing'));
			}
			if (!empty($_POST['form-remote_folders-list-default_resolution-'.$i])) {
				$remote_folder['default_resolution']=escape($_POST['form-remote_folders-list-default_resolution-'.$i]);
			} else {
				crash(lang('admin.error.remote_folder_default_resolution_missing'));
			}
			if (!empty($_POST['form-remote_folders-list-default_duration-'.$i])) {
				$remote_folder['default_duration']=escape(convert_from_hh_mm_ss($_POST['form-remote_folders-list-default_duration-'.$i]));
			} else {
				crash(lang('admin.error.remote_folder_default_duration_missing'));
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
				crash(lang('admin.error.version_edit_concurrency_error'));
			}
			
			$slug_result = query("SELECT COUNT(*) cnt FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.slug='".$data['slug']."' AND s.type='$type' AND v.id<>".$data['id']);
			$slug_row = mysqli_fetch_assoc($slug_result);
			if ($slug_row['cnt']>0) {
				crash(lang('admin.error.version_edit_slug_already_exists_error'));
			}

			log_action("update-version", "Version of «".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."» (version id: ".$data['id'].") updated");
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

			$_SESSION['message']=lang('admin.generic.data_saved');
		}
		else {
			$slug_result = query("SELECT COUNT(*) cnt FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.slug='".$data['slug']."' AND s.type='$type'");
			$slug_row = mysqli_fetch_assoc($slug_result);
			if ($slug_row['cnt']>0) {
				crash(lang('admin.error.version_edit_slug_already_exists_error'));
			}
			log_action("create-version", "Version of «".query_single("SELECT name FROM series WHERE id=".$data['series_id'])."» created");
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

			$_SESSION['message']=lang('admin.generic.data_saved');
		}

		header("Location: version_list.php?type=$type");
		die();
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT v.* FROM version v WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.version_not_found'));
		mysqli_free_result($result);

		$results = query("SELECT s.* FROM series s WHERE id=".$row['series_id']);
		$series = mysqli_fetch_assoc($results) or crash(lang('admin.error.series_not_found'));
		mysqli_free_result($results);
		if ($series['type']!=$type) {
			crash(lang('admin.error.wrong_type_specified'));
		}

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
					REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."') formatted_number,
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IF(e.number IS NOT NULL,
							IF(s.number_of_episodes=1,
								s.name,
								CONCAT(d.name, ' - ".lang('generic.query.movie_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."')),
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
		$row['id']='';
		$row['title']='';
		$row['updated']='';
		$row['slug']='';
		$row['alternate_titles']='';
		$row['status']='';
		$row['synopsis']='';
		$row['storage_folder']='';
		$row['storage_processing']=$_SESSION['default_storage_processing'];
		$row['featurable_status']=1;

		$results = query("SELECT s.* FROM series s WHERE id=".escape($_GET['series_id']));
		$series = mysqli_fetch_assoc($results) or crash(lang('admin.error.series_not_found'));
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
			} else {
				$rowd['title']='';
			}
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$resulte = query("SELECT e.*,
					REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."') formatted_number,
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IF(e.number IS NOT NULL,
							IF(s.number_of_episodes=1,
								s.name,
								CONCAT(d.name, ' - ".lang('generic.query.movie_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."')),
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
		crash(lang('admin.error.series_id_missing')."<br>POST values: ".print_r($_POST, TRUE));
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
						<h5 class="modal-title" id="add-episode-from-version-modal-title"><?php echo lang('admin.version_edit.add_episode_modal.title'); ?></h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo lang('admin.generic.close'); ?>">
							<span aria-hidden="true" class="fa fa-times"></span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo lang('admin.version_edit.add_episode_modal.explanation'); ?>
						<div class="mt-3">
							<label for="add-episode-from-version-division-id"><?php echo lang('admin.version_edit.add_episode_modal.division'); ?></label>
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
							<label for="add-episode-from-version-number"><?php echo lang('admin.version_edit.add_episode_modal.number'); ?> <small class="text-muted"><?php echo lang('admin.version_edit.add_episode_modal.number.explanation'); ?></small></label>
							<input class="form-control" id="add-episode-from-version-number" type="number" step="any" placeholder="<?php echo lang('js.admin.series_edit.episode.number_placeholder'); ?>" oninput="if($(this).val()==''){$('#add-episode-from-version-special-name').removeClass('d-none');} else {$('#add-episode-from-version-special-name').addClass('d-none');$('#add-episode-from-version-description').val('');}">
						</div>
						<div class="mt-3 d-none" id="add-episode-from-version-special-name">
							<label for="add-episode-from-version-description"><?php echo lang('admin.version_edit.add_episode_modal.special_name'); ?></label>
							<input class="form-control" id="add-episode-from-version-description" placeholder="<?php echo lang('js.admin.series_edit.episode.description_placeholder'); ?>">
						</div>
					</div>
					
					<div class="align-self-center">
						<button type="button" class="btn btn-primary m-2" onclick="addEpisodeFromVersion();"><?php echo lang('admin.version_edit.add_episode_modal.add_to_series_button'); ?></button> <button type="button" data-bs-dismiss="modal" class="btn btn-secondary m-2"><?php echo lang('admin.generic.cancel'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.version_edit.edit_title') : lang('admin.version_edit.create_title'); ?></h4>
					<hr>
					<form method="post" action="version_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfLinks()">
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-title-with-autocomplete"><?php echo lang('admin.version_edit.localized_title'); ?><span class="mandatory"></span></label> <?php print_helper_box(lang('admin.version_edit.localized_title'), lang('admin.version_edit.localized_title.help')); ?>
									<input class="form-control" name="title" id="form-title-with-autocomplete" placeholder="<?php echo lang('admin.version_edit.localized_title.placeholder'); ?>" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['title'])); ?>" data-old-value="<?php echo htmlspecialchars(html_entity_decode($row['title'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
									<input type="hidden" id="type" value="<?php echo $type; ?>">
									<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-series" class="mandatory"><?php echo $original_title_string; ?></label> <?php print_helper_box($original_title_string, lang('admin.version_edit.original_title.help')); ?>
									<input id="form-series" class="form-control" readonly value="<?php echo htmlspecialchars($series['name']); ?>"></input>
									<input name="series_id" type="hidden" value="<?php echo $series['id']; ?>"/>
									<input id="form-external_id" type="hidden" value="<?php echo $series['external_id']; ?>"/>
									<input id="series_subtype" type="hidden" value="<?php echo $series['subtype']; ?>"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-slug"><?php echo lang('admin.version_edit.slug'); ?><span class="mandatory"></span></label> <?php print_helper_box(lang('admin.version_edit.slug'), lang('admin.version_edit.slug.help')); ?>
									<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
									<input type="hidden" id="form-old_slug" value="<?php echo htmlspecialchars($row['slug']); ?>">
								</div>
							</div>
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-alternate_titles"><?php echo lang('admin.version_edit.alternate_localized_titles'); ?></label> <?php print_helper_box(lang('admin.version_edit.alternate_localized_titles'), lang('admin.version_edit.alternate_localized_titles.help')); ?>
									<input class="form-control" name="alternate_titles" id="form-alternate_titles" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_titles'])); ?>">
								</div>
							</div>
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-fansub-1" class="mandatory"><?php echo lang('admin.version_edit.fansub_1'); ?></label> <?php print_helper_box(lang('admin.version_edit.fansub_1'), lang('admin.version_edit.fansub_1.help')); ?>
									<select name="fansub_1" class="form-select" id="form-fansub-1" required>
										<option value=""><?php echo lang('admin.version_edit.fansub.select'); ?></option>
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
									<label for="form-fansub-2"><?php echo lang('admin.version_edit.fansub_2'); ?></label> <?php print_helper_box(lang('admin.version_edit.fansub_2'), lang('admin.version_edit.fansub_2.help')); ?>
									<select name="fansub_2" class="form-select" id="form-fansub-2">
										<option value=""><?php echo lang('admin.version_edit.fansub.select_additional'); ?></option>
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
									<label for="form-fansub-3"><?php echo lang('admin.version_edit.fansub_3'); ?></label> <?php print_helper_box(lang('admin.version_edit.fansub_3'), lang('admin.version_edit.fansub_3.help')); ?>
									<select name="fansub_3" class="form-select" id="form-fansub-3">
										<option value=""><?php echo lang('admin.version_edit.fansub.select_additional'); ?></option>
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
									<label for="form-downloads_url_1" class="mandatory"><?php echo lang('admin.version_edit.download_url_fansub_1'); ?></label> <?php print_helper_box(lang('admin.version_edit.download_url_fansub_1'), lang('admin.version_edit.download_url_fansub_1.help')); ?>
									<input id="form-downloads_url_1" name="downloads_url_1" type="url" class="form-control" value="<?php echo (count($fansubs)>0 ? htmlspecialchars($fansubs[0][1]) : ''); ?>" maxlength="200" required/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-downloads_url_2"><?php echo lang('admin.version_edit.download_url_fansub_2'); ?></label> <?php print_helper_box(lang('admin.version_edit.download_url_fansub_2'), lang('admin.version_edit.download_url_fansub_2.help')); ?>
									<input id="form-downloads_url_2" name="downloads_url_2" type="url" class="form-control" value="<?php echo (count($fansubs)>1 ? htmlspecialchars($fansubs[1][1]) : ''); ?>" maxlength="200" required <?php echo (count($fansubs)>1 ? '' : ' disabled'); ?>/>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-downloads_url_3"><?php echo lang('admin.version_edit.download_url_fansub_3'); ?></label> <?php print_helper_box(lang('admin.version_edit.download_url_fansub_3'), lang('admin.version_edit.download_url_fansub_3.help')); ?>
									<input id="form-downloads_url_3" name="downloads_url_3" type="url" class="form-control" value="<?php echo (count($fansubs)>2 ? htmlspecialchars($fansubs[2][1]) : ''); ?>" maxlength="200" required <?php echo (count($fansubs)>2 ? '' : ' disabled'); ?>/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-status" class="mandatory"><?php echo lang('admin.version_edit.status'); ?></label> <?php print_helper_box(lang('admin.version_edit.status'), $status_help_string); ?>
									<select class="form-select" name="status" id="form-status" required>
										<option value=""><?php echo lang('admin.version_edit.status.select'); ?></option>
										<option value="1"<?php echo $row['status']==1 ? " selected" : ""; ?>><?php echo lang('status.complete.private.short'); ?></option>
										<option value="2"<?php echo $row['status']==2 ? " selected" : ""; ?>><?php echo lang('status.inprogress.private.short'); ?></option>
										<option value="3"<?php echo $row['status']==3 ? " selected" : ""; ?>><?php echo lang('status.partiallycomplete.private.short'); ?></option>
										<option value="4"<?php echo $row['status']==4 ? " selected" : ""; ?>><?php echo lang('status.abandoned.private.short'); ?></option>
										<option value="5"<?php echo $row['status']==5 ? " selected" : ""; ?>><?php echo lang('status.cancelled.private.short'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-show_episode_numbers" class="mandatory"><?php echo lang('admin.version_edit.show_episode_numbers'); ?></label> <?php print_helper_box(lang('admin.version_edit.show_episode_numbers'), lang('admin.version_edit.show_episode_numbers.help')); ?>
									<select class="form-select" name="show_episode_numbers" id="form-show_episode_numbers" required>
<?php
	if ($series['subtype']!='movie' && $series['subtype']!='oneshot') {
?>
										<option value="1"<?php echo $row['show_episode_numbers']==1 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.show_episode_numbers.number_and_title'); ?></option>
<?php
	}
?>
										<option value="0"<?php echo $row['show_episode_numbers']==0 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.show_episode_numbers.only_title'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-featurable_status"><?php echo lang('admin.version_edit.recommendations'); ?></label> <?php print_helper_box(lang('admin.version_edit.recommendations'), lang('admin.version_edit.recommendations.help')); ?>
									<select name="featurable_status" class="form-select" id="form-featurable_status">
										<option value="0"<?php echo $row['featurable_status']==0 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.recommendations.never'); ?></option>
										<option value="1"<?php echo $row['featurable_status']==1 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.recommendations.randomly'); ?></option>
										<option value="2"<?php echo $row['featurable_status']==2 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.recommendations.always_special'); ?></option>
										<option value="3"<?php echo $row['featurable_status']==3 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.recommendations.always_season'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="mb-3">
									<label><?php echo lang('admin.version_edit.cover_image'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.cover_image'), lang('admin.version_edit.cover_image.help')); ?><br><small class="text-muted"><?php echo lang('admin.version_edit.cover_image.requirements'); ?></small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/covers/version_'.$row['id'].'.jpg');
?>
									<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
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
									<label><?php echo lang('admin.version_edit.header_image'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.header_image'), lang('admin.version_edit.header_image.help')); ?><br><small class="text-muted"><?php echo lang('admin.version_edit.header_image.requirements'); ?></small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/featured/version_'.$row['id'].'.jpg');
?>
									<label for="form-featured_image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
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
							<label class="col-sm p-0" for="form-synopsis"><?php echo lang('admin.version_edit.synopsis'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.synopsis'), sprintf(lang('admin.version_edit.synopsis.help'), $external_source)); ?></label>
							<button type="button" id="import-from-mal" class="btn btn-primary btn-sm col-sm-3 mb-1">
								<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm me-1 fa-width-auto" role="status" aria-hidden="true"></span>
								<span id="import-from-mal-not-loading" class="fa fa-cloud-arrow-down pe-2 fa-width-auto"></span><?php echo sprintf(lang('admin.version_edit.synopsis.import_button'), $external_source); ?>
							</button>
							<textarea class="form-control" name="synopsis" id="form-synopsis" required style="height: 150px;" oninput="synopsisChanged=true;"><?php echo htmlspecialchars(str_replace('&#039;',"'",html_entity_decode($row['synopsis']))); ?></textarea>
						</div>
						<div class="mb-3">
							<label for="form-division-list"><?php echo lang('admin.version_edit.division_titles_and_covers'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.division_titles_and_covers'), $division_titles_and_covers_help_string); ?> <small class="text-muted"><?php echo lang('admin.version_edit.cover_image.requirements'); ?></small></label>
							<div class="row flex" id="form-division-list">
<?php
		foreach ($divisions as $division) {
?>
								<div class="col-sm-2 text-center pe-1 ps-1 align-self-end">
										<label for="form-division-title-<?php echo $division['id']; ?>" style="font-style: italic;"><?php echo htmlspecialchars($division['name']); ?></label>
										<input id="form-division-title-<?php echo $division['id']; ?>" name="form-division-title-<?php echo $division['id']; ?>" class="form-control text-center" value="<?php echo htmlspecialchars($division['title']); ?>" maxlength="200" placeholder="<?php echo lang('admin.version_edit.division_title.placeholder'); ?>" required/>
									<br>
<?php
		$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg');
?>
										<img id="form-division_cover_<?php echo $division['id']; ?>_preview" style="width: 128px; height: 180px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px; margin-bottom: 0.5em;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg" data-original="'.STATIC_URL.'/images/divisions/'.$row['id'].'_'.$division['id'].'.jpg"' : ''; ?> alt=""><br />
										<label for="form-division_cover_<?php echo $division['id']; ?>" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
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
									<label for="form-storage_folder"><span class="mandatory"><?php echo lang('admin.version_edit.storage_folder'); ?></span></label> <?php print_helper_box(lang('admin.version_edit.storage_folder'), lang('admin.version_edit.storage_folder.help')); ?>
									<input id="form-storage_folder" name="storage_folder" type="text" class="form-control" value="<?php echo htmlspecialchars($row['storage_folder']); ?>" maxlength="200" required readonly<?php echo (!empty($row['id']) && empty($row['is_hidden'])) ? ' data-is-set="1"' : '' ; ?>/>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-storage_processing"><span class="mandatory"><?php echo lang('admin.version_edit.storage_processing'); ?></span></label> <?php print_helper_box(lang('admin.version_edit.storage_processing'), lang('admin.version_edit.storage_processing.help')); ?>
									<select name="storage_processing" class="form-select" onchange="if(!confirm('<?php echo lang('admin.version_edit.storage_processing.confirm'); ?>')) this.selectedIndex=0;">
										<option value="1"<?php echo $row['storage_processing']==1 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.storage_processing.save_copy'); ?></option>
										<option value="5"<?php echo $row['storage_processing']==5 ? " selected" : ""; ?>><?php echo lang('admin.version_edit.storage_processing.no_copy'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-remote_folders-list"><?php echo lang('admin.version_edit.remote_folders'); ?></label> <?php print_helper_box(lang('admin.version_edit.remote_folders'), sprintf(lang('admin.version_edit.remote_folders.help'), MAIN_SITE_NAME)); ?>
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
											<option value=""><?php echo lang('admin.version_edit.remote_folders.select'); ?></option>
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
											<option value=""><?php echo lang('admin.version_edit.remote_folders.any_division'); ?></option>
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
													<th style="width: 20%;"><?php echo lang('admin.version_edit.remote_folders.account'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.remote_folders.account'), lang('admin.version_edit.remote_folders.account.help')); ?></th>
													<th><?php echo lang('admin.version_edit.remote_folders.folder'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.remote_folders.folder'), lang('admin.version_edit.remote_folders.folder.help')); ?></th>
													<th style="width: 10%;"><?php echo lang('admin.version_edit.remote_folders.resolution'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.remote_folders.resolution'), lang('admin.version_edit.remote_folders.resolution.help')); ?></th>
													<th style="width: 10%;"><?php echo lang('admin.version_edit.remote_folders.duration'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.remote_folders.duration'), lang('admin.version_edit.remote_folders.duration.help')); ?></th>
													<th style="width: 15%;"><?php echo lang('admin.version_edit.remote_folders.division'); ?> <?php print_helper_box(lang('admin.version_edit.remote_folders.division'), lang('admin.version_edit.remote_folders.division.help')); ?></th>
													<th class="text-center" style="width: 10%;"><?php echo lang('admin.version_edit.remote_folders.sync'); ?> <?php print_helper_box(lang('admin.version_edit.remote_folders.sync'), lang('admin.version_edit.remote_folders.sync.help')); ?></th>
													<th class="text-center" style="width: 5%;"><?php echo lang('admin.generic.action'); ?></th>
												</tr>
											</thead>
											<tbody>
												<tr id="remote_folders-list-table-empty" class="<?php echo count($remote_folders)>0 ? 'd-none' : ''; ?>">
													<td colspan="7" class="text-center"><?php echo lang('admin.version_edit.remote_folders.empty'); ?></td>
												</tr>
<?php
		for ($j=0;$j<count($remote_folders);$j++) {
?>
												<tr id="form-remote_folders-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" name="form-remote_folders-list-remote_account_id-<?php echo $j+1; ?>" onchange="if ($(this).find('option:selected').eq(0).hasClass('not-syncable')){$('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',true);} else { $('#form-remote_folders-list-is_active-<?php echo $j+1; ?>').prop('disabled',false); }" class="form-select" required>
															<option value=""><?php echo lang('admin.version_edit.remote_folders.select'); ?></option>
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
														<input id="form-remote_folders-list-default_resolution-<?php echo $j+1; ?>" name="form-remote_folders-list-default_resolution-<?php echo $j+1; ?>" class="form-control" value="<?php echo htmlspecialchars($remote_folders[$j]['default_resolution']); ?>" maxlength="200" required placeholder="<?php echo lang('js.admin.version_edit.episode.resolution_placeholder'); ?>" list="resolution-options"/>
													</td>
													<td>
														<input id="form-remote_folders-list-default_duration-<?php echo $j+1; ?>" name="form-remote_folders-list-default_duration-<?php echo $j+1; ?>" type="time" step="1" class="form-control" value="<?php echo convert_to_hh_mm_ss($remote_folders[$j]['default_duration']); ?>" required/>
													</td>
													<td>
														<select id="form-remote_folders-list-division_id-<?php echo $j+1; ?>" name="form-remote_folders-list-division_id-<?php echo $j+1; ?>" class="form-select">
															<option value=""><?php echo lang('admin.version_edit.remote_folders.any_division'); ?></option>
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
											<button onclick="addVersionRemoteFolderRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.version_edit.remote_folders.add_button'); ?></button>
										</div>
										<div class="col-sm text-end" style="padding-left: 0; padding-right: 0">
											<select id="import-type" class="form-select form-control-sm form-inline" title="<?php echo lang('admin.version_edit.remote_folders.sync_now_type.title'); ?>" style="width: auto; display: inline; font-size: 78%;">
												<option value="all" selected><?php echo lang('admin.version_edit.remote_folders.sync_now_type.all'); ?></option>
												<option value="sync"><?php echo lang('admin.version_edit.remote_folders.sync_now_type.only_marked'); ?></option>
											</select> →
											<button type="button" id="import-from-mega" class="btn btn-primary btn-sm">
												<span id="import-from-mega-loading" class="d-none spinner-border spinner-border-sm me-1 fa-width-auto" role="status" aria-hidden="true"></span>
												<span id="import-from-mega-not-loading" class="fa fa-redo pe-2 fa-width-auto"></span><?php echo lang('admin.version_edit.remote_folders.sync_now_button'); ?>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-none alert alert-warning" id="import-failed-results">
							<div><span class="fa fa-exclamation-triangle me-2"></span> <?php echo $not_imported_string; ?></div>
							<table class="table-hover table-sm mt-2 small w-100" id="import-failed-results-table">
								<thead>
									<tr>
										<th><?php echo lang('admin.version_edit.remote_folders.sync_failed.file'); ?></th>
										<th><?php echo lang('admin.version_edit.remote_folders.sync_failed.link'); ?></th>
										<th><?php echo lang('admin.version_edit.remote_folders.sync_failed.reason'); ?></th>
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
							<label for="form-episode-list"><?php echo $episode_list_string; ?></label> <?php print_helper_box($episode_list_string, $episode_list_help_string); ?>
							<div class="container" id="form-episode-list">
								<datalist id="resolution-options">
									<option value="1080p">
									<option value="720p">
									<option value="480p">
									<option value="360p">
								</datalist>
								<div id="warning-no-numbers" class="alert alert-warning<?php echo ($row['show_episode_numbers']==0 && $series['subtype']!='movie' && $series['subtype']!='oneshot') ? '' : ' d-none'; ?>">
									<div><span class="fa fa-exclamation-triangle me-2"></span><?php echo $not_showing_numbers_string; ?></div>
								</div>
								<div class="accordion" id="accordion">
									<div class="accordion-item">
										<h2 class="accordion-header">
											<button class="accordion-button<?php echo count($divisions)>2 ? ' collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#division-collapse-<?php echo $episodes[0]['division_id']; ?>" aria-expanded="<?php echo count($divisions)>2 ? 'false' : 'true'; ?>" aria-controls="division-collapse-<?php echo $episodes[0]['division_id']; ?>"><b><?php echo sprintf(lang('admin.version_edit.episode_list.episodes_for_division'), $episodes[0]['division_name']); ?></b></button>
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
											<button class="accordion-button<?php echo count($divisions)>2 ? ' collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#division-collapse-<?php echo $episodes[$i]['division_id']; ?>" aria-expanded="<?php echo count($divisions)>2 ? 'false' : 'true'; ?>" aria-controls="division-collapse-<?php echo $episodes[$i]['division_id']; ?>"><b><?php echo sprintf(lang('admin.version_edit.episode_list.episodes_for_division'), $episodes[$i]['division_name']); ?></b></button>
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
								CONCAT(d.name, ' - ".lang('generic.query.movie_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'))
							),
							e.description
						),
						IF(e.number IS NOT NULL,
							CONCAT(d.name, ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."')),
							CONCAT(d.name, ' - ', e.description)
						)
					) episode_title
					FROM episode e
					LEFT JOIN division d ON e.division_id=d.id
					LEFT JOIN series s ON e.series_id=s.id
					WHERE s.type='$type' AND s.subtype='movie' AND e.id=".$episodes[$i]['linked_episode_id']);
			$linked_episode = mysqli_fetch_assoc($resultle);
			mysqli_free_result($resultle);
			$episode_name=$linked_episode['episode_title'].' <i>'.lang('admin.version_edit.episode_list.linked_movie').'</i>';
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
													<label for="form-files-list-<?php echo $episodes[$i]['id']; ?>-title"><?php echo lang('admin.version_edit.episode_list.episode_title'); ?></label> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="<?php echo lang('admin.version_edit.episode_list.episode_title'); ?>" data-bs-contents="<?php echo lang('admin.version_edit.episode_list.episode_title.help'); ?>"></small>
													<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-title" type="text" class="form-control episode-title-input<?php echo (!empty($episodes[$i]['number']) && empty($episodes[$i]['linked_episode_id'])) ? ' episode-title-input-numbered' : ''; ?>" value="<?php echo htmlspecialchars($episodes[$i]['title']); ?>" maxlength="500" data-episode-number="<?php echo $episodes[$i]['formatted_number']; ?>" placeholder="<?php echo (!empty($episodes[$i]['number']) && empty($episodes[$i]['linked_episode_id']) && $row['show_episode_numbers']==1) ? lang('js.admin.generic.episode_prefix').$episodes[$i]['formatted_number'] : lang('js.admin.version_edit.episode.title_placeholder'); ?>"<?php echo !empty($episodes[$i]['linked_episode_id']) ? ' required' : ''; ?>/>
<?php
		if (empty($episodes[$i]['linked_episode_id'])) {
?>
													<div class="container mt-2" id="form-files-list-<?php echo $episodes[$i]['id']; ?>">
														<div class="row mb-3">
															<div class="w-100 column ps-0 pe-0">
																<table class="table table-bordered table-hover table-sm" id="files-list-table-<?php echo $episodes[$i]['id']; ?>" data-count="<?php echo max(count($files),1); ?>">
																	<thead>
																		<tr>
																			<th style="width: 8%;"><?php echo lang('admin.version_edit.episode_list.variant'); ?><span class="mandatory"></span> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="<?php echo lang('admin.version_edit.episode_list.variant'); ?>" data-bs-contents="<?php echo lang('admin.version_edit.episode_list.variant.help'); ?>"></small></th>
<?php
			if ($type=='manga') {
?>
																			<th><?php echo lang('admin.version_edit.episode_list.archive'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.episode_list.archive'), lang('admin.version_edit.episode_list.archive.help')); ?></th>
																			<th style="width: 16%;"><?php echo lang('admin.version_edit.episode_list.upload'); ?> <?php print_helper_box(lang('admin.version_edit.episode_list.upload'), lang('admin.version_edit.episode_list.upload.help')); ?></th>
<?php
			} else {
?>
																			<th><?php echo lang('admin.version_edit.episode_list.link_and_resolution'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.episode_list.link_and_resolution'), lang('admin.version_edit.episode_list.link_and_resolution.help')); ?></th>
																			<th style="width: 10%;"><?php echo lang('admin.version_edit.episode_list.duration'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.episode_list.duration'), lang('admin.version_edit.episode_list.duration.help')); ?></th>
<?php
			}
?>
																			<th style="width: 15%;"><?php echo lang('admin.version_edit.episode_list.comments'); ?> <?php print_helper_box(lang('admin.version_edit.episode_list.comments'), lang('admin.version_edit.episode_list.comments.help')); ?></th>
																			<th class="text-center" style="width: 8%;"><?php echo lang('admin.version_edit.episode_list.lost'); ?> <?php print_helper_box(lang('admin.version_edit.episode_list.lost'), sprintf(lang('admin.version_edit.episode_list.lost.help'), MAIN_SITE_NAME)); ?></th>
																			<th class="text-center" style="width: 8%;"><?php echo lang('admin.generic.actions'); ?></th>
																		</tr>
																	</thead>
																	<tbody>
<?php
			for ($j=0;$j<count($files);$j++) {
?>
																		<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-<?php echo $j+1; ?>">
																			<td>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['variant_name']); ?>" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.variant_placeholder'); ?>" required/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['id']; ?>"/>
																			</td>
<?php
				if ($type=='manga') {
?>
																			<td class="align-middle">
																				<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($files[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check"></span> '.sprintf(lang('admin.version_edit.episode_list.archive_already_uploaded'), htmlspecialchars($files[$j]['original_filename'])).'</span>' : '<span style="color: gray;"><span class="fa fa-times"></span> '.lang('admin.version_edit.episode_list.no_archive_uploaded').'</span>'; ?></div>
																			</td>
																			<td class="align-middle">
																				<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($files[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pe-2"></span><?php echo !empty($files[$j]['original_filename']) ? lang('admin.common.change_archive') : lang('admin.common.upload_archive') ; ?></label>
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
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="<?php echo lang('js.admin.version_edit.episode.link_placeholder'); ?>" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['id']); ?>"/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($files[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.resolution_placeholder'); ?>"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-times p-1 text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_link_title'); ?>"></button>
																							</td>
																						</tr>
<?php
					}
					if (count($files[$j]['links'])==0) {
?>
																						<tr id="form-links-list-<?php echo $episodes[$i]['id']; ?>-row-1-1" style="background: none;">
																							<td class="ps-0 pt-0 pb-0 border-0">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="" maxlength="2048" placeholder="<?php echo lang('js.admin.version_edit.episode.link_placeholder'); ?>" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value=""/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.resolution_placeholder'); ?>"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-times p-1 text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_link_title'); ?>"></button>
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
																				<button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn text-primary btn-sm fa p-1 fa-width-auto fa-arrows-split-up-and-left fa-rotate-180" title="<?php echo lang('js.admin.version_edit.episode.add_file_variant_title'); ?>"></button>
<?php
				if ($type!='manga') {
?>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-<?php echo $j+1; ?>" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="<?php echo lang('js.admin.version_edit.episode.add_file_link_title'); ?>"></button>
<?php
				}
?>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-<?php echo $j+1; ?>" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_file_title'); ?>"></button>
																			</td>
																		</tr>
<?php
			}
			if (count($files)==0) {
?>
																		<tr id="form-files-list-<?php echo $episodes[$i]['id']; ?>-row-1">
																			<td>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-variant_name-1" type="text" class="form-control" value="<?php echo lang('admin.version_edit.episode_list.variant_single'); ?>" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.variant_placeholder'); ?>" required/>
																				<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-id-1" type="hidden" value="-1"/>
																			</td>
<?php
				if ($type=='manga') {
?>
																			<td class="align-middle">
																				<div id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file_details-1" class="small"><span style="color: gray;"><span class="fa fa-times"></span> <?php echo lang('admin.version_edit.episode_list.no_archive_uploaded'); ?></span></div>
																			</td>
																			<td class="align-middle">
																				<label style="margin-bottom: 0;" for="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1" class="btn btn-sm btn-primary w-100"><span class="fa fa-upload pe-2"></span><?php echo lang('admin.common.upload_archive'); ?></label>
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
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="" maxlength="2048" placeholder="<?php echo lang('js.admin.version_edit.episode.link_placeholder'); ?>" oninput="$(this).attr('value',$(this).val());"/>
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-id" type="hidden" value="-1"/>
																							</td>
																							<td class="pt-0 pb-0 border-0" style="width: 22%;">
																								<input id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" name="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.resolution_placeholder'); ?>"/>
																							</td>
																							<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																								<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-file-1-link-1-delete" onclick="deleteLinkRow(<?php echo $episodes[$i]['id']; ?>,1,1);" type="button" class="btn fa fa-times p-1 text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_link_title'); ?>"></button>
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
																				<button onclick="addVersionRow(<?php echo $episodes[$i]['id']; ?>);" type="button" class="btn text-primary btn-sm fa p-1 fa-width-auto fa-arrows-split-up-and-left fa-rotate-180" title="<?php echo lang('js.admin.version_edit.episode.add_file_variant_title'); ?>"></button>
<?php
				if ($type!='manga') {
?>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-add_link-1" onclick="addLinkRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="<?php echo lang('js.admin.version_edit.episode.add_file_link_title'); ?>"></button>
<?php
				}
?>
																				<button id="form-files-list-<?php echo $episodes[$i]['id']; ?>-delete-1" onclick="deleteVersionRow(<?php echo $episodes[$i]['id']; ?>,1);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_file_title'); ?>"></button>
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
									<button data-bs-toggle="modal" data-bs-target="#add-episode-from-version-modal" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.version_edit.episode_list.add_episode_button'); ?></button>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-extras-list"><?php echo lang('admin.version_edit.extra_content'); ?></label> <?php print_helper_box(lang('admin.version_edit.extra_content'), lang('admin.version_edit.extra_content.help')); ?>
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
															<th style="width: 20%;"><?php echo lang('admin.version_edit.extra_content.title'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.extra_content.title'), lang('admin.version_edit.extra_content.title.help')); ?></th>
<?php
		if ($type=='manga') {
?>
															<th><?php echo lang('admin.version_edit.extra_content.archive'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.extra_content.archive'), lang('admin.version_edit.extra_content.archive.help')); ?></th>
															<th style="width: 16%;"><?php echo lang('admin.version_edit.extra_content.upload'); ?> <?php print_helper_box(lang('admin.version_edit.extra_content.upload'), lang('admin.version_edit.extra_content.upload.help')); ?></th>
<?php
		} else {
?>
															<th><?php echo lang('admin.version_edit.extra_content.link_and_resolution'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.extra_content.link_and_resolution'), lang('admin.version_edit.extra_content.link_and_resolution.help')); ?></th>
															<th style="width: 10%;"><?php echo lang('admin.version_edit.extra_content.duration'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.version_edit.extra_content.duration'), lang('admin.version_edit.extra_content.duration.help')); ?></th>
<?php
		}
?>
															<th style="width: 15%;"><?php echo lang('admin.version_edit.extra_content.comments'); ?> <?php print_helper_box(lang('admin.version_edit.extra_content.comments'), lang('admin.version_edit.extra_content.comments.help')); ?></th>
															<th class="text-center" style="width: 8%;"><?php echo lang('admin.generic.actions'); ?></th>
														</tr>
													</thead>
													<tbody>
														<tr id="extras-list-table-empty" class="<?php echo count($extras)>0 ? 'd-none' : ''; ?>">
															<td colspan="5" class="text-center"><?php echo lang('admin.version_edit.extra_content.empty'); ?></td>
														</tr>
<?php
	for ($j=0;$j<count($extras);$j++) {
?>
														<tr id="form-extras-list-row-<?php echo $j+1; ?>">
															<td>
																<input id="form-extras-list-name-<?php echo $j+1; ?>" name="form-extras-list-name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['extra_name']); ?>" maxlength="200" required placeholder="<?php echo lang('js.admin.version_edit.episode.title_placeholder'); ?>"/>
																<input id="form-extras-list-id-<?php echo $j+1; ?>" name="form-extras-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['id']; ?>"/>
															</td>
<?php
		if ($type=='manga') {
?>
															<td class="align-middle">
																<div id="form-extras-list-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($extras[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check"></span> '.sprintf(lang('admin.version_edit.episode_list.archive_already_uploaded'), htmlspecialchars($extras[$j]['original_filename'])).'</span>' : '<span style="color: gray;"><span class="fa fa-times"></span> '.lang('admin.version_edit.episode_list.no_archive_uploaded').'</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-extras-list-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($extras[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pe-2"></span><?php echo !empty($extras[$j]['original_filename']) ? lang('admin.common.change_archive') : lang('admin.common.upload_archive') ; ?></label>
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
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-url" type="text" pattern="<?php echo $link_url_pattern; ?>" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['url']); ?>" maxlength="2048" placeholder="<?php echo lang('js.admin.version_edit.episode.link_placeholder_extra'); ?>" oninput="$(this).attr('value',$(this).val());" required/>
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-id" type="hidden" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['id']); ?>"/>
																			</td>
																			<td class="pt-0 pb-0 border-0" style="width: 22%;">
																				<input id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" name="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-resolution" type="text" class="form-control" list="resolution-options" value="<?php echo htmlspecialchars($extras[$j]['links'][$k]['resolution']); ?>" maxlength="200" placeholder="<?php echo lang('js.admin.version_edit.episode.resolution_placeholder'); ?>" required/>
																			</td>
																			<td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;">
																				<button id="form-extras-list-<?php echo $j+1; ?>-link-<?php echo $k+1; ?>-delete" onclick="deleteExtraLinkRow(<?php echo $j+1; ?>,<?php echo $k+1; ?>);" type="button" class="btn fa fa-times p-1 text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_link_title'); ?>"></button>
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
<?php
		if ($type!='manga') {
?>
																<button id="form-extras-list-add_link-<?php echo $j+1; ?>" onclick="addExtraLinkRow(<?php echo $j+1; ?>);" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="<?php echo lang('js.admin.version_edit.episode.add_file_link_title'); ?>"></button>
<?php
		}
?>
																<button id="form-extras-list-delete-<?php echo $j+1; ?>" onclick="deleteVersionExtraRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="<?php echo lang('js.admin.version_edit.episode.delete_file_title'); ?>"></button>
															</td>
														</tr>
<?php
	}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addVersionExtraRow();" type="button" class="btn btn-primary btn-sm"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.version_edit.extra_content.add_button'); ?></button></div>
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
								<label class="form-check-label" for="form-do_not_count_as_update"><?php echo lang('admin.version_edit.do_not_count_as_update'); ?></label>
							</div>
							<br />
<?php
		if ($_SESSION['admin_level']>=4 && $type!='manga') {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_recreate_storage_links" id="form-do_not_recreate_storage_links" value="1" onchange="if($(this).prop('checked')){if (confirm('<?php echo lang('admin.version_edit.do_not_recreate_storage_links.confirm'); ?>')) {$('#form-do_not_count_as_update').prop('checked',true);} else {$(this).prop('checked',false);}}">
								<label class="form-check-label" for="form-do_not_recreate_storage_links"><?php echo lang('admin.version_edit.do_not_recreate_storage_links'); ?></label>
							</div>
							<br />
<?php
		}
	}
?>
							<button type="submit" name="action" value="<?php echo $row['id']!=NULL? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.version_edit.create_button'); ?></button>
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
