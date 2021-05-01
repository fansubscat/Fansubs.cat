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

function get_browser_icon_by_type($user_agent, $user_agent_read) {
	if (!empty($user_agent_read) && $user_agent!=$user_agent_read) {
		return 'class="fab fa-chromecast" style="color: #007bff;" title="Google Cast o dispositiu similar"';
	}
	if(preg_match('/android|bb\d+|meego|mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$user_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($user_agent,0,4))){
		return 'class="fa fa-mobile-alt" style="color: #17a2b8;" title="Mòbil o tauleta"';
	}
	return 'class="fa fa-laptop" style="color: #28a745;" title="Ordinador"';
}

function get_anonymized_username($ip, $ua) {
	if (empty($ua)) {
		$ua = ""; //Fix nulls
	}
	if (empty($ip)) {
		return '<span style="color: #000000;" title="' . $ua . '">N/A</span>';
	} else if ($ip=='(recovered view)') {
		return '<span style="color: #333333;" title="' . $ua . '">N/D</span>';
	}
	srand(crc32($ip));
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < 8; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	$colors = '012345678';
	$colorsLength = strlen($colors);
	$randomColor = '';
	for ($i = 0; $i < 6; $i++) {
		$randomColor .= $colors[rand(0, $colorsLength - 1)];
	}
	return '<span style="color: #' . $randomColor . ';" title="' . $ua . '">' . $randomString . '</span>';
}
?>
