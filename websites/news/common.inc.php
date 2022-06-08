<?php
//Taken from: http://stackoverflow.com/a/2955878/1254846
function slugify($text){
$table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
    );
  $text = strtr($text, $table);

  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
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

?>
