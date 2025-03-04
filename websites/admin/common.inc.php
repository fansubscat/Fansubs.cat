<?php
function get_status_description_short($id){
	switch ($id){
		case 1:
			return "Completada";
		case 2:
			return "En procés";
		case 3:
			return "Parcialment completada";
		case 4:
			return "Abandonada";
		case 5:
			return "Cancel·lada";
		default:
			return "Estat desconegut";
	}
}

function get_subtype_name($subtype){
	switch ($subtype){
		case 'movie':
			return "Film";
		case 'series':
			return "Sèrie";
		case 'oneshot':
			return "One-shot";
		case 'serialized':
			return "Serialitzat";
		default:
			return "Desconegut";
	}
}

function get_fansub_preposition_name($text){
	$first = substr($text, 0, 1);
	if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
		return "d’$text";
	}
	return "de $text";
}

function get_hours_or_minutes_formatted($time){
	if ($time>=3600) {
		$hours = floor($time/3600);
		$time = $time-$hours*3600;
		echo $hours." h ".min(59,round($time/60))." min";
	} else {
		echo min(59,round($time/60))." min";
	}
}

function convert_to_hh_mm_ss($seconds) {
	if (!empty($seconds) && is_numeric($seconds)) {
		return sprintf('%02d:%02d:%02d', $seconds/3600, ($seconds % 3600) / 60, $seconds % 60);
	} else {
		return "00:00:00";
	}
}

function convert_from_hh_mm_ss($string) {
	if (strpos($string,':')!==FALSE) {
		sscanf($string, "%d:%d:%d", $hours, $minutes, $seconds);
		return $hours * 3600 + $minutes * 60 + $seconds;
	} else {
		return $string;
	}
}

function get_relative_date($time) {
	if (time()-$time<60) {
		return "ara mateix";
	}
	if (time()-$time<3600) {
		$minutes = intval((time()-$time)/60);
		if ($minutes==1) {
			return "fa 1 minut";
		} else {
			return "fa $minutes minuts";
		}
	} else if (time()-$time<3600*24) {
		$hours = intval((time()-$time)/3600);
		if ($hours==1) {
			return "fa 1 hora";
		} else {
			return "fa $hours hores";
		}
	}
	else if (time()-$time<3600*24*30) {
		$days = intval((time()-$time)/(3600*24));
		if ($days==1) {
			return "fa 1 dia";
		} else {
			return "fa $days dies";
		}
	}
	else {
		return date('d/m/Y', $time);
	}
}

//Returns a Catalan approximate representation of the elapsed time since a date
function relative_time($date){
        $ago = (int)(time() - date('U', $date));

	if ($ago==1){
		$ago = "fa 1 segon";
	}
	else if ($ago<60){
		$ago = "fa $ago segons";
	}
	else if ($ago<3600){
		$ago = (int)($ago/60);
		if ($ago==1){
                        $ago = "fa ". $ago . " minut";
		}
		else{
			$ago = "fa ". $ago . " minuts";
		}
        }
	else if ($ago<86400){
		$ago = (int)($ago/3600);
		if ($ago==1){
                        $ago = "fa ". $ago . " hora";
		}
		else{
			$ago = "fa ". $ago . " hores";
		}
        }
	else if ($ago<2678400){
		$ago = (int)($ago/86400);
		if ($ago==1){
                        $ago = "fa ". $ago . " dia";
		}
		else{
			$ago = "fa ". $ago . " dies";
		}
        }
	else{
		$ago = date('d/m/Y \a \l\e\s H:i:s', $date);
	}
	return $ago;
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") {
					rrmdir($dir."/".$object);
				}
				else {
					unlink($dir."/".$object);
				}
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function flatten_directories_and_move_to_storage($file_id, $temp_path){
	$cleaned_path = "/srv/fansubscat/temporary/cleaned_$file_id/";
	log_action("debug-log", "Creating cleaned directory $cleaned_path for file $file_id");
	mkdir($cleaned_path);
	log_action("debug-log", "Flattening directories for file $file_id from $temp_path into $cleaned_path");
	$directory = new RecursiveDirectoryIterator($temp_path);
	$iterator = new RecursiveIteratorIterator($directory);
	foreach ($iterator as $file){
		$ext = pathinfo(strtolower(basename($file)), PATHINFO_EXTENSION);
		if (strpos($file, '__MACOSX')===FALSE && ($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='mp3' || $ext=='ogg')) {
			copy($file, $cleaned_path.preg_replace('/[^0-9a-zA-Z_\.]/u','_', strtolower(basename($file))));
		}
	}
	//Clean temporary directory
	log_action("debug-log", "Removing temporary directory $temp_path for file $file_id");
	rrmdir($temp_path);
	//Create remote directory
	//IMPORTANT: SSH keys must be available for the www-data user, or this will fail silently
	log_action("debug-log", "Running: ssh root@".STORAGES[0]." mkdir -p /home/storage/Manga/$file_id/");
	exec("ssh root@".STORAGES[0]." mkdir -p /home/storage/Manga/$file_id/", $output, $result_code);
	log_action("debug-log", "Result ($result_code): ".print_r($output, TRUE));
	//Copy to remote directory
	//IMPORTANT: SSH keys must be available for the www-data user, or this will fail silently
	log_action("debug-log", "Running: rsync -avzhW --chmod=u=rwX,go=rX $cleaned_path root@".STORAGES[0].":/home/storage/Manga/$file_id/ --delete");
	exec("rsync -avzhW --chmod=u=rwX,go=rX $cleaned_path root@".STORAGES[0].":/home/storage/Manga/$file_id/ --delete", $output, $result_code);
	log_action("debug-log", "Result ($result_code): ".print_r($output, TRUE));
	//Copy first file as preview
	log_action("debug-log", "Copying first image from $cleaned_path as preview for file $file_id");
	unset($output);
	exec("ls -1v $cleaned_path | grep -v \".mp3\" | grep -v \".ogg\" | head -n1 | xargs -I {} convert $cleaned_path{} -resize 240x -background black -gravity center -extent 240x240 -format jpeg ".STATIC_DIRECTORY.'/images/files/'."$file_id.jpg", $output, $result_code);
	log_action("debug-log", "Result ($result_code): ".print_r($output, TRUE));
	//Clean cleaned directory
	log_action("debug-log", "Removing cleaned directory $cleaned_path for file $file_id");
	rrmdir($cleaned_path);
}

function decompress_manga_file($file_id, $temporary_filename, $original_filename){
	//log_action("debug-log", "Descomprimint el fitxer $original_filename i movent-lo al directori amb id: $file_id");
	$temp_path="/srv/fansubscat/temporary/decompress_$file_id/";
	$extension = pathinfo($original_filename, PATHINFO_EXTENSION);
	if ($extension=='rar' || $extension=='cbr'){
		//Extract RAR/CBR
		$rar = RarArchive::open($temporary_filename);
		if ($rar!==FALSE) {
			$entries = $rar->getEntries();
			if ($entries!==FALSE) {
				foreach($entries as $entry) {
					$entry->extract($temp_path);
				}
				$rar->close();
				flatten_directories_and_move_to_storage($file_id, $temp_path);
			} else {
				crash("RAR extract error when extracting: $original_filename");
			}
		} else {
			crash("RAR extract error when extracting: $original_filename");
		}
	} else if ($extension=='zip' || $extension=='cbz') {
		//Extract ZIP/CBZ
		$zip = new ZipArchive;
		if ($zip->open($temporary_filename) === TRUE) {
			$zip->extractTo($temp_path);
			$zip->close();
			flatten_directories_and_move_to_storage($file_id, $temp_path);
		} else {
			crash("ZIP extract error when extracting: $original_filename");
		}
	} else {
		crash("Unknown file type uploaded to manga version: $original_filename");
	}
}

function get_browser_icon_by_source_type($source, $is_casted) {
	if ($is_casted) {
		return 'class="fab fa-chromecast" style="color: #007bff;" title="Google Cast o dispositiu similar"';
	}
	if ($source=='api') {
		return 'class="fa fa-book" style="color: #007bff;" title="Tachiyomi (via API)"';
	}
	if($source=='mobile'){
		return 'class="fa fa-mobile-alt" style="color: #17a2b8;" title="Mòbil o tauleta"';
	}
	return 'class="fa fa-laptop" style="color: #28a745;" title="Ordinador"';
}

function get_anonymized_username($user_id, $anon_id) {
	$value = (!empty($user_id) ? $user_id : $anon_id);
	srand(crc32($value));
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < 16; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return '<img src="https://api.dicebear.com/9.x/dylan/svg?seed='.$randomString.'" style="width: 2rem; height: 2rem; margin: -1rem;">';
}

function get_public_site_url($type, $slug, $is_hentai) {
	$link_url='';
	switch ($type) {
		case 'anime':
			$link_url=ANIME_URL;
			break;
		case 'liveaction':
			$link_url=LIVEACTION_URL;
			break;
		case 'manga':
			$link_url=MANGA_URL;
			break;
	}
	if ($is_hentai) {
		$link_url = str_replace(MAIN_DOMAIN, HENTAI_DOMAIN, $link_url);
	}
	return $link_url.'/'.$slug;
}

function print_helper_box($title, $description, $white=FALSE) {
	echo '<small title="Fes clic per a més informació" data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button"'.($white ? ' style="color: white !important;"' : '').' data-bs-title="'.htmlspecialchars($title).'" data-bs-contents="'.htmlspecialchars($description).'"></small>';
}
?>
