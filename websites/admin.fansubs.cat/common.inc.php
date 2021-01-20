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

function get_fansub_preposition_name($text){
	$first = substr($text, 0, 1);
	if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
		return "d'$text";
	}
	return "de $text";
}

function get_hours_or_minutes_formatted($time){
	if ($time>=3600) {
		$hours = floor($time/3600);
		$time = $time-$hours*3600;
		echo $hours." h ".round($time/60)." min";
	} else {
		echo round($time/60)." min";
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
	if (file_exists("../manga.fansubs.cat/images/storage/$file_id/")) {
		rrmdir("../manga.fansubs.cat/images/storage/$file_id/");
	}

	mkdir("../manga.fansubs.cat/images/storage/$file_id/");

	$directory = new RecursiveDirectoryIterator($temp_path);
	$iterator = new RecursiveIteratorIterator($directory);
	foreach ($iterator as $file){
		$ext = pathinfo(strtolower(basename($file)), PATHINFO_EXTENSION);
		if (strpos($file, '__MACOSX')===FALSE && ($ext=='jpg' || $ext=='jpeg' || $ext=='png')) {
			copy($file, "../manga.fansubs.cat/images/storage/$file_id/".preg_replace('/[^0-9a-zA-Z_\.]/u','_', strtolower(basename($file))));
		}
	}
	rrmdir($temp_path);
}

function decompress_manga_file($file_id, $temporary_filename, $original_filename){
	log_action("debug-log", "Descomprimint el fitxer $original_filename i movent-lo al directori amb id: $file_id");
	$temp_path="/tmp/decompress_$file_id/";
	$extension = pathinfo($original_filename, PATHINFO_EXTENSION);
	if ($extension=='rar'){
		//Extract RAR
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
?>
