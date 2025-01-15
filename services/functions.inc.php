<?php
require_once(__DIR__.'/../common/libraries/simple_html_dom.php');
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/config.inc.php');
require_once(__DIR__.'/vendor/autoload.php');

use LanguageDetection\Language;

//Check if the provided text is Catalan or Spanish (Catalan=true, Spanish=false)
function is_catalan($text, $only_beginning=TRUE){
	$ld = new Language(['ca', 'es']);
	$text = preg_replace('!\s+!', ' ', $text);
	if ($only_beginning){
		$text = implode(' ', array_slice(explode(' ', $text), 0, 30));
	}
	$result = $ld->detect($text)->close();
	return $result['ca']>=$result['es'];
}

//Get last occurrence in a string
function strrstr($h, $n, $before = false) {
    $rpos = strrpos($h, $n);
    if($rpos === false) return false;
    if($before == false) return substr($h, $rpos);
    else return substr($h, 0, $rpos);
}

//Removes the unwanted tags, double enters, etc. from descriptions
function parse_description($description){
	$description = strip_tags($description, '<br><b><strong><em><i><ul><li><ol><hr><sub><sup><u><tt><p>');
	$description = str_replace('&nbsp;',' ', $description);
	$description = str_replace(' & ','&amp;', $description);
	$description = str_replace('<br>','<br />', $description);
	$description = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br /><br />', $description);
	return preg_replace('/(?:<br\s*\/?>\s*)+$/', '', preg_replace('/^(?:<br\s*\/?>\s*)+/', '', trim($description)));
}

function get_prepositioned_text($text, $twitter=FALSE){
	$first = substr($text, $twitter ? 1 : 0, 1);
	if ($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U' || $first == 'a' || $first == 'e' || $first == 'i' || $first == 'o' || $first == 'u'){
		return "d'$text";
	}
	return "de $text";
}

//Gets the first image in the news content that is not a SVG, if available.
//Then copies it to our website directory
function fetch_and_parse_image($fansub_slug, $url, $description){
	preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $description, $matches);

	$first_image_url=NULL;
	if (isset($matches) && isset($matches[1])){
		for ($i=0;$i<count($matches[1]);$i++){
			if (strpos($matches[1][$i], '.svg')===FALSE){
				$first_image_url = $matches[1][$i];
				break;
			}
		}
	}

	if ($first_image_url!=NULL){
		if (substr($first_image_url,0,2)=="//"){
			$first_image_url="https:".$first_image_url;
		}
		if (strpos($first_image_url,"://")===FALSE){
			$first_image_url=$url.$first_image_url;
		}
		if (!is_dir(STATIC_DIRECTORY."/images/news/$fansub_slug/")){
			mkdir(STATIC_DIRECTORY."/images/news/$fansub_slug/");
		}
		if (@copy($first_image_url, STATIC_DIRECTORY."/images/news/$fansub_slug/".slugify_short($first_image_url))){
			return slugify_short($first_image_url);
		}
		else if (file_exists(STATIC_DIRECTORY."/images/news/$fansub_slug/".slugify_short($first_image_url))){
			//This means that the file is no longer accessible, but we already have it locally!
			return slugify_short($first_image_url);
		}
	}
	return NULL;
}

//This function does the actual fetching:
//Decides depending on the method and then processes the returned feed items, inserting them to database
//These methods must return an array of items, where each item is an array with these values at each position:
//  0 - title
//  1 - description (unparsed)
//  2 - description (parsed)
//  3 - date (formatted as Y-m-d H:i:s)
//  4 - url
//  5 - image URL
function fetch_fansub_fetcher($fansub_id, $fansub_slug, $fetcher_id, $method, $url, $last_fetched_item_date){
	global $db_connection;
	query("UPDATE news_fetcher SET status='fetching' WHERE id=$fetcher_id");
	$old_count_result = query("SELECT COUNT(*) count FROM news WHERE news_fetcher_id=$fetcher_id");
	$old_count = mysqli_fetch_assoc($old_count_result)['count'];
	switch($method){
		case 'animugen':
			$result = fetch_via_animugen($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot':
			$result = fetch_via_blogspot($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_2nf':
			$result = fetch_via_blogspot_2nf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_as':
			$result = fetch_via_blogspot_as($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_bsc':
			$result = fetch_via_blogspot_bsc($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_dnf':
			$result = fetch_via_blogspot_dnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_llpnf':
			$result = fetch_via_blogspot_llpnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_mnf':
			$result = fetch_via_blogspot_mnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_pnm':
			$result = fetch_via_blogspot_pnm($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_snf':
			$result = fetch_via_blogspot_snf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_shinsengumi':
			$result = fetch_via_blogspot_shinsengumi($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_teqma':
			$result = fetch_via_blogspot_teqma($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_tnf':
			$result = fetch_via_blogspot_tnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'blogspot_uto':
			$result = fetch_via_blogspot_uto($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'catsub':
			$result = fetch_via_catsub($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'espurnaescarlata':
			$result = fetch_via_espurnaescarlata($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'mangadex_edcec':
			$result = fetch_via_mangadex_edcec($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'ouferrat':
			$result = fetch_via_ouferrat($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'phpbb_dnf':
			$result = fetch_via_phpbb_dnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'phpbb_llpnf':
			$result = fetch_via_phpbb_llpnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'roninfansub':
			$result = fetch_via_roninfansub($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'weebly_rnnf':
			$result = fetch_via_weebly_rnnf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_arf':
			$result = fetch_via_wordpress_arf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_ddc':
			$result = fetch_via_wordpress_ddc($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_mdcf':
			$result = fetch_via_wordpress_mdcf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_xf':
			$result = fetch_via_wordpress_xf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_ynf':
			$result = fetch_via_wordpress_ynf($fansub_slug, $url, $last_fetched_item_date);
			break;
		case 'wordpress_ys':
			$result = fetch_via_wordpress_ys($fansub_slug, $url, $last_fetched_item_date);
			break;
		default:
			$result = array('error_invalid_method',array());
	}

	//Store news with fetcher and fansub id
	if ($result[0]=='ok'){
		if (count($result[1])>0){

			//We get all elements
			$elements = $result[1];
			
			//Empty the original array
			$result[1]=array();

			//We get the lower item date available and reuse last_fetched_item_date
			foreach ($elements as $element){
				if ($element[3]<$last_fetched_item_date){
					$last_fetched_item_date = $element[3];
				}
			}
		
			//We copy to the array ONLY the ones which are higher than the date (NOT equal)
			foreach ($elements as $element){
				if ($element[3]>$last_fetched_item_date){
					$result[1][] = $element;
				}
			}

			//We delete the old ones (higher than the last one date)
			//We use the native functions because we need to be able to rollback
			mysqli_autocommit($db_connection, FALSE);
			mysqli_query($db_connection, "DELETE FROM news WHERE news_fetcher_id=$fetcher_id AND date>'$last_fetched_item_date'") or (mysqli_rollback($db_connection) && $result[0]='error_mysql');

			//And then insert them if everything goes well
			if ($result[0]=='ok'){
				foreach ($result[1] as $element){
					mysqli_query($db_connection, "INSERT INTO news (fansub_id, news_fetcher_id, title, original_contents, contents, date, url, image) VALUES ($fansub_id, $fetcher_id, '".mysqli_real_escape_string($db_connection, $element[0])."','".mysqli_real_escape_string($db_connection, $element[1])."','".mysqli_real_escape_string($db_connection, $element[2])."','".$element[3]."','".mysqli_real_escape_string($db_connection, $element[4])."',".($element[5]!=NULL ? "'".mysqli_real_escape_string($db_connection, $element[5])."'" : 'NULL').")") or (mysqli_rollback($db_connection) && $result[0]='error_mysql');
				}
			}
			
			mysqli_autocommit($db_connection, TRUE);
		}
		else{
			//The feed was empty, don't treat as success
			$result[0]='error_empty';
		}
	}
	
	$increment=NULL;

	if ($result[0]=='ok'){
		$new_count_result = query("SELECT COUNT(*) count FROM news WHERE news_fetcher_id=$fetcher_id");
		$new_count = mysqli_fetch_assoc($new_count_result)['count'];
		$increment = $new_count-$old_count;
	}
	
	//Update fetch status
	query("UPDATE news_fetcher SET status='idle',last_fetch_result='".$result[0]."',last_fetch_date='".date('Y-m-d H:i:s')."',last_fetch_increment=".($increment!==NULL ? $increment : 'NULL')." WHERE id=$fetcher_id");

	if ($increment>0){
		//TODO: In the future, do things here, i.e, post to Twitter/Facebook accounts indicating that we have news from a certain fansub
		//We can assume that the X most recent items will be the new ones.
		//In case of a decrement, we won't be able to delete news, but this is unlikely...

		//Hello 2016, we are now in the future 2018. We will send it via FCM so the Android app receives it ;)
		//Hello 2018, we are now in the future 2020. We will send some tweets too... ;)
		//Hello 2020, 2020 still. Maybe we will disable the news tweets, they are a bit annoying... And replace them with a cron job that tweets new manga/anime added to the DB.
		//Hello 2020, we are now in the future 2021. We will report the increments to the action log
		//Hello 2021, we are now in the future 2023. Just doing some refactors in preparation for v5...
		
		log_action('fetch-news-changes', "Found $increment new posts for fansub $fansub_slug");

		$push_result = query("SELECT n.title, f.slug fansub_slug, n.url, f.name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE n.news_fetcher_id=$fetcher_id ORDER BY n.date DESC LIMIT $increment");
		while ($push_row = mysqli_fetch_assoc($push_result)){
			$notification = array(
				'to' => '/topics/all',
				'data' => array(
					'title' => $push_row['title'],
					'fansub' => $push_row['name'],
					'fansub_id' => $push_row['fansub_slug']
				)
			);
			$headers = array('Content-Type: application/json', 'Authorization: key=' . FIREBASE_API_KEY);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"POST");
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($notification));
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			//Send the request
			curl_exec($curl) or die('FCM Send Error: ' . curl_error($curl));
			//Close request
			curl_close($curl);
		}
	}
}

/** BELOW HERE ARE ALL INDIVIDUAL METHODS OF FETCHING **/
/** THE CODE IS ULTRA UGLY AND HACKY, BUT IT WORKS (as of July 2016). BEWARE! **/

function fetch_via_animugen($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		$cur_count = 0;
		foreach($html->find('.mg-posts-sec-inner article') as $article) {
			$tries=1;
			while ($tries<=3){
				$error=FALSE;

				$article_html_text = file_get_contents($article->find('a', 0)->href) or $error=TRUE;

				if (!$error){
					$article_tidy = tidy_parse_string($article_html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($article_tidy);
					$article_html = str_get_html(tidy_get_output($article_tidy));
					//Create an empty item
					$item = array();

					//Look up and add elements to the item
					$title = $article_html->find('h1.title a', 0);
					$item[0]=$title->innertext;
					$item[1]=$article_html->find('article', 0)->innertext;

					//This is the description before the image (normally only Catalan, sometimes Cat/Spa)
					$description = explode("<figure", $article_html->find('article', 0)->innertext)[0];
					
					$item[2]=parse_description($description);
					$datetext = $article->find('span.mg-blog-date a', 0)->innertext;

					//News have no time, so we assume 00:00:00, if there are any other news on the same day, these will show below them...
					$date = date_create_from_format('d/m/Y H:i:s', $datetext . ' 00:00:00');
					$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
					$item[3]= $date->format('Y-m-d H:i:s');
					$item[4]=$article->find('a', 0)->href;
					if ($article_html->find('article figure', 0)!==NULL){
						$item[5]=fetch_and_parse_image($fansub_slug, $url, $article_html->find('article figure', 0)->innertext);
					}
					else{
						$item[5]=NULL;
					}

					//If the text is empty, we assume Spanish
					if ($item[2]!='' && is_catalan($item[2])){
						$elements[]=$item;
					}
					break;
				}
				else{
					$tries++;
				}
			}
			if ($tries>3){
				return array('error_connect',array());
			}
		}

		$go_on = FALSE;
	}
	return array('ok', $elements);
}

function fetch_via_blogspot($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_2nf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
			
				$description=$article->find('div.post-body', 0)->innertext;
				$item[1]=$description;
				
				$description = preg_replace("/\<p>(.*)Descarrega el capítol!(.*)\<\/p\>/i", '', $description);
				
				$item[2]=parse_description($description);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_as($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.mobile-post-outer a') as $article) {
			$tries=1;
			while ($tries<=3){
				sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
				$error=FALSE;

				$article_html_text = file_get_contents($article->href) or $error=TRUE;

				if (!$error){
					$article_tidy = tidy_parse_string($article_html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($article_tidy);
					$article_html = str_get_html(tidy_get_output($article_tidy));
					//Create an empty item
					$item = array();

					//Look up and add elements to the item
					$title = $article_html->find('h3.post-title', 0);
					$item[0]=$title->innertext;
			
					$description=$article_html->find('div.post-body', 0)->innertext;
					$item[1]=$description;
				
					$description = preg_replace("/Links de Descàrrega(.*)/i", '</span>', $description);
				
					$item[2]=parse_description($description);
					$date = date_create_from_format('Y-m-d\TH:i:sP', $article_html->find('abbr.published', 0)->title);
					$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
					$item[3]= $date->format('Y-m-d H:i:s');
					$item[4]=str_replace('?m=1','',$article->href);
					$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

					$elements[]=$item;
					break;
				}
				else{
					$tries++;
				}
			}
			if ($tries>3){
				return array('error_connect',array());
			}
		}
		$go_on = FALSE; //We do this for now, because we don't know how to access next pages
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_bsc($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL && stripos($article->find('h3.post-title a', 0),'Bleach')!==FALSE){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Entradas antiguas'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_dnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
			
				$description=$article->find('div.post-body', 0)->innertext;
				$item[1]=$description;
				
				$description = parse_description($description);
				$description = preg_replace("/Submanga: http(.*)\<br \/\>/i", '', $description);
				$description = preg_replace("/MegaUpload: http(.*)\<br \/\>/i", '', $description);
				$description = preg_replace("/MegaUpload: http(.*)/i", '', $description);
				$description = preg_replace("/Subamanga: http(.*)\<br \/\>/i", '', $description);
				
				$item[2]=$description;
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_llpnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			//We only show news which don't start with [LlPnF or [DnF from the main blog, or we will also show the series pages...
			//Could be improved, of course...
			if ($article->find('h3.post-title a', 0)!==NULL &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')>0) &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')>0)){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item   
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.post-body', 0)->innertext;
				$item[1]=$description;

				//We replace the notiwrapper with an empty string to remove the download links
				foreach ($article->find('div.post-body div.noti_wrapper') as $notiwrapper){
					$description = str_replace($notiwrapper->outertext, '', $description);
				}

				//This helps with the layout here: http://llunaplenanofansub.blogspot.com.es/2015/08/anime-kokoro-connect-01-02-03-i-04.html
				$description = str_replace('</center>','</center><br /><br />', $description);

				//We replace headers with bold text so it doesn't crash our layout
				$description = str_replace('<h1>','<b>', $description);
				$description = str_replace('</h1>','</b><br />', $description);
				$description = str_replace('<h2>','<b>', $description);
				$description = str_replace('</h2>','</b><br />', $description);
				$description = str_replace('<h3>','<b>', $description);
				$description = str_replace('</h3>','</b><br />', $description);

				//Fix for big text style
				$description = str_replace(' style="font-size: xx-large;"','', $description);

				$item[2]=parse_description($description);

				if ($article->parent->find('abbr.published', 0)!==NULL){
					$date = date_create_from_format('Y-m-d\TH:i:sP', $article->parent->find('abbr.published', 0)->title);
				}
				else if ($article->parent->parent->find('abbr.published', 0)!==NULL){
					//We will be here if the article has broken links that cause the date to be
					//parsed onto its parent element...
					//Previously, it crashed the script.
					//This WILL be inconsistent if this happens on a day with several posts, 
					//and the post will get its date overwritten by the newest post of the day.
					//Also, if the HTML is broken, the article contents may not be loaded properly.
					$date = date_create_from_format('Y-m-d\TH:i:sP', $article->parent->parent->find('abbr.published', 0)->title);
				}
				else{
					//Something is very broken, just skip the article so we don't crash
					continue;
				}
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;
						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_mnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL && stripos($article->find('h3.post-title a', 0),'Cicle de signatures')===FALSE){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_pnm($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Filter news with the "Fansub" tag"
				$tags = $article->find('a[rel="tag"]');
				$is_valid=FALSE;
				if (count($tags)>0) {
					foreach ($tags as $tag) {
						if ($tag->innertext=='Fansub') {
							$is_valid=TRUE;
						}
					}
				}
				if (!$is_valid){
					continue;
				}

				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);

				//Date is: 25 de juny 2021
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_shinsengumi($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('h3.post-title a') as $article) {
			//Create an empty item
			$item = array();

			//Look up and add elements to the item
			$url = $article->href;
			$title = $article->innertext;
			$tries=1;
			while ($tries<=3){
				sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
				$error=FALSE;

				$html_text = file_get_contents($url) or $error=TRUE;

				if (!$error){
					$inner_tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($inner_tidy);
					$inner_html = str_get_html(tidy_get_output($inner_tidy));
					$item[0]=$title;
					$item[1]=$inner_html->find('div.post-body', 0)->innertext;
					$item[2]=parse_description($inner_html->find('div.post-body', 0)->innertext);
					$date = date_create_from_format('Y-m-d\TH:i:sP', $inner_html->find('time.published', 0)->title);
					$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
					$item[3]=$date->format('Y-m-d H:i:s');
					$item[4]=$url;
					$item[5]=fetch_and_parse_image($fansub_slug, $url, $inner_html->find('div.post-body', 0)->innertext);
					break;
				}
				else{
					$tries++;
				}
			}
			if ($tries>3){
				return array('error_connect',array());
			}

			$elements[]=$item;
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_snf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.post-body', 0)->innertext;
				$item[1]=$description;

				//We remove the password string (seems to always be the same)
				$description = str_replace("<b>Contrasenya: snf</b>",'', $description);

				$item[2]=parse_description($description);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_teqma($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			//This is terribly unoptimal... Try to find a better way!
			if ($article->find('h3.post-title a', 0)!==NULL && (stripos($article->find('h3.post-title a', 0),'fansub')!==FALSE || (stripos($article->find('h3.post-title a', 0),'Matsuri Special')!==FALSE && stripos($article->find('h3.post-title a', 0),'castellano')===FALSE))){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)==0 || (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date)){
			foreach ($texts as $text){
				if ($text->plaintext=='Entradas antiguas'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_tnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			//This is terribly unoptimal... Try to find a better way!
			if ($article->find('h3.post-title a', 0)!==NULL && ((stripos($article->find('h3.post-title a', 0),'CAPÍTOL')!==FALSE && stripos($article->find('h3.post-title a', 0),'CATALÀ')!==FALSE) || stripos($article->find('h3.post-title a', 0),'NARUTO MANGA')!==FALSE || stripos($article->find('h3.post-title a', 0),'DETECTIU CONAN CAPÍTOL')!==FALSE || stripos($article->find('h3.post-title a', 0),'Naruto: Capítol manga')!==FALSE || stripos($article->find('h3.post-title a', 0),'QUI SOM?')!==FALSE || stripos($article->find('h3.post-title a', 0),'BEELZEBUB CAPÍTOL')!==FALSE || stripos($article->find('h3.post-title a', 0),'Fairy Tail Manga')!==FALSE)){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Entradas antiguas'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

//This is now Kukafera no Fansub, no longer Un Tortosí Otaku
function fetch_via_blogspot_uto($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('h3.post-title a') as $article) {
			//Create an empty item
			$item = array();

			//Look up and add elements to the item
			$url = $article->href;
			$title = $article->innertext;
			$tries=1;
			while ($tries<=3){
				sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
				$error=FALSE;

				$html_text = file_get_contents($url) or $error=TRUE;

				if (!$error){
					$inner_tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($inner_tidy);
					$inner_html = str_get_html(tidy_get_output($inner_tidy));
					$item[0]=$title;
					$item[1]=$inner_html->find('div.post-body', 0)->innertext;
					$item[2]=parse_description($inner_html->find('div.post-body', 0)->innertext);
					$date = date_create_from_format('Y-m-d\TH:i:sP', $inner_html->find('abbr.published', 0)->title);
					$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
					$item[3]=$date->format('Y-m-d H:i:s');
					$item[4]=$url;
					$item[5]=fetch_and_parse_image($fansub_slug, $url, $inner_html->find('div.post-body', 0)->innertext);
					break;
				}
				else{
					$tries++;
				}
			}
			if ($tries>3){
				return array('error_connect',array());
			}

			$elements[]=$item;
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_catsub($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.cs_news') as $article) {
			//Create an empty item
			$item = array();

			//Look up and add elements to the item   
			$item[0]=$article->find('div.cs_newstitle a', 0)->innertext;

			$description = $article->find('div.cs_newscontent', 0)->innertext;
			$item[1]=$description;

			//Remove the post-screenshot text
			if (strpos($description, 'cs_newsimage')!==0){
				$description = preg_replace("/\<span class=\\\"note\\\"\>(.*)\<\/span\>$/i", '', trim($description));
			}

			$item[2]=parse_description($description);

			//We have to explode because the format is: 05/07/2015 a les 19:48 / Ereza
			$datetext = explode(' / ', $article->find('div.cs_date', 0)->innertext)[0];

			$date = date_create_from_format('d/m/Y \a \l\e\s H.i', $datetext);

			$item[3]=$date->format('Y-m-d H:i:s');
			$item[4]=$url . substr($article->find('div.cs_newstitle a', 0)->href, 1);
			$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

			$elements[]=$item;
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				//Compare last date to $last_fetched_item_date, if lower, stop paging
				if ($text->plaintext=='Notícies més antigues &gt;'){
					$html_text = file_get_contents($url . $text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_espurnaescarlata($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	//parse through the HTML and build up the elements feed as we go along
	foreach($html->find('.item-head h2 a') as $article) {
		//Create an empty item
		$item = array();

		//Look up and add elements to the item
		$real_url = substr($url, 0,strrpos($url, '/')) . $article->href;
		$title = $article->find('strong')[0]->innertext;
		$error=FALSE;

		$html_text = file_get_contents($real_url) or $error=TRUE;

		if (!$error){
			$inner_tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
			tidy_clean_repair($inner_tidy);
			$inner_html = str_get_html(tidy_get_output($inner_tidy));
			$item[0]=$title;
			$item[1]=$inner_html->find('.cw-c.cf', 0)->innertext;
			$item[2]=parse_description($inner_html->find('.cw-c.cf', 0)->innertext);
			//News have no time, so we assume 00:00:00, if there are any other news on the same day, these will show below them...
			$date = date_create_from_format('d.m.Y H:i:s', $inner_html->find('.s-bdh-d .ld-c', 0)->innertext.' 00:00:00');
			$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
			$item[3]=$date->format('Y-m-d H:i:s');
			$item[4]=$real_url;
			$item[5]=fetch_and_parse_image($fansub_slug, $real_url, $inner_html->find('.b-img', 0)->innertext);
		}
		else{
			return array('error_connect',array());
		}

		$elements[]=$item;
	}
	return array('ok', $elements);
}

function fetch_via_mangadex_edcec($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$error_connect=FALSE;

	$curl = curl_init();
	$headers = array('User-Agent: Fansubscat-NewsFetcher/5.0');
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$json_text = curl_exec($curl) or $error_connect=TRUE;
	curl_close($curl);

	if ($error_connect){
		return array('error_connect',array());
	}

	$json = json_decode($json_text);

	//parse through the JSON and build up the elements feed as we go along
	foreach($json->data as $article) {
		//Create an empty item
		$item = array();

		//Look up and add elements to the item
		$item[0]='El Detectiu Conan - Vol. ' . $article->attributes->volume . ' Cap. ' . $article->attributes->chapter . ': ' . $article->attributes->title;
		$item[1]=$article->attributes->chapter . ': ' . $article->attributes->title;
		$item[2]='El Detectiu Conan - Volum ' . $article->attributes->volume . ' - Capítol ' . $article->attributes->chapter . ': ' . $article->attributes->title . ".<br />Capítol disponible a MangaDex.";
		$datetext = $article->attributes->publishAt;

		$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
		$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
		$item[3]= $date->format('Y-m-d H:i:s');
		$item[4]='https://mangadex.org/chapter/'.$article->id;
		$item[5]=NULL;
		
		//If the item is older than 2018-11-23 (switch from Facebook), reject it
		if ($item[3]<'2018-11-23 00:00:00'){
			break;
		} else {
			$elements[]=$item;
		}
	}
	return array('ok', $elements);
}

function fetch_via_ouferrat($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	//parse through the HTML and build up the elements feed as we go along
	foreach($html->find('article') as $article) {
		//Create an empty item
		$item = array();

		//Look up and add elements to the item   
		$item[0]=preg_replace("/<br\W*?\/>/", " - ", $article->find('header h2 a', 0)->innertext);

		$description = $article->innertext;
		$description = preg_replace("/\<header(.*)\<\/header\>/i", '', trim($description));
		$item[1]=$description;

		$item[2]=parse_description($description);

		$date = date_create_from_format('Y-m-d H:i', $article->find('time', 0)->innertext);

		$item[3]=$date->format('Y-m-d H:i:s');
		$item[4]=$url . substr($article->find('header h2 a', 0)->href, 1);
		$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

		$elements[]=$item;
	}
	return array('ok', $elements);
}

function fetch_via_phpbb_dnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$base_url=substr($url,0,strrpos($url,'/'));
	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('a.topictitle') as $topic) {
			$html_text_topic = file_get_contents($base_url.$topic->href) or $error_connect=TRUE;
			if ($error_connect){
				return array('error_connect',array());
			}
			$tidy_topic = tidy_parse_string($html_text_topic, $tidy_config, 'UTF8');
			tidy_clean_repair($tidy_topic);
			$html_topic = str_get_html(tidy_get_output($tidy_topic));

			//Create an empty item
			$item = array();

			//Look up and add elements to the item
			$title=substr($html_topic->find('h1.cattitle', 0)->innertext,6);
			$item[0]=$title;

			$description=$html_topic->find('div.postbody div', 0)->innertext;

			$item[1]=$description;
			$item[2]=parse_description($description);

			$datetext = $html_topic->find('span.postdetails', 1)->innertext;
			//We now have this: <img class="sprite-icon_post_target" src="http://illiweb.com/fa/empty.gif" alt="Missatge" title="Missatge" border="0" />Assumpte: Novetats] Fusió de DnF i LlPnF!&nbsp; &nbsp;<img src="http://illiweb.com/fa/empty.gif" alt="" border="0" />Ds Maig 15, 2010 9:49 pm
			$datetext = substr(strrchr($datetext, ">"), 1);

			$datetext = str_ireplace('Gen', 'January', $datetext);
			$datetext = str_ireplace('Feb', 'February', $datetext);
			$datetext = str_ireplace('Mar', 'March', $datetext);
			$datetext = str_ireplace('Abr', 'April', $datetext);
			$datetext = str_ireplace('Maig', 'May', $datetext);
			$datetext = str_ireplace('Mai', 'May', $datetext);
			$datetext = str_ireplace('Jun', 'June', $datetext);
			$datetext = str_ireplace('Jul', 'July', $datetext);
			$datetext = str_ireplace('Ago', 'August', $datetext);
			$datetext = str_ireplace('Set', 'September', $datetext);
			$datetext = str_ireplace('Oct', 'October', $datetext);
			$datetext = str_ireplace('Nov', 'November', $datetext);
			$datetext = str_ireplace('Des', 'December', $datetext);

			$datetext = str_ireplace('Dl', 'Mon', $datetext);
			$datetext = str_ireplace('Dt', 'Tue', $datetext);
			$datetext = str_ireplace('Dc', 'Wed', $datetext);
			$datetext = str_ireplace('Dj', 'Thu', $datetext);
			$datetext = str_ireplace('Dv', 'Fri', $datetext);
			$datetext = str_ireplace('Ds', 'Sat', $datetext);
			$datetext = str_ireplace('Dg', 'Sun', $datetext);

			$date = date_create_from_format('D F d, Y H:i a', $datetext);
			$item[3]= $date->format('Y-m-d H:i:s');
			$item[4]=$base_url.$topic->href;
			$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

			$elements[]=$item;
		}

		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			if ($html->find('img.sprite-arrow_prosilver_right', 0)!==NULL){
				$html_text = file_get_contents($base_url . $html->find('img.sprite-arrow_prosilver_right', 0)->parent->href) or $error_connect=TRUE;
				if ($error_connect){
						return array('error_connect',array());
				}
				$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
				tidy_clean_repair($tidy);
				$html = str_get_html(tidy_get_output($tidy));
				$go_on = TRUE;
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_phpbb_llpnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$base_url=substr($url,0,strrpos($url,'/'));
	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('a.topictitle') as $topic) {
			if (strpos($topic->innertext,'Com funciona Lluna Plena no Fansub?')===FALSE 
				&& strpos($topic->innertext,'Tret de sortida dels regals!')===FALSE
				&& strpos($topic->innertext,'Nou funcionament del blog i les notícies')===FALSE
				&& strpos($topic->innertext,'Necessitem gent!')===FALSE
				&& strpos($topic->innertext,'Kokoro Connect 01, 02, 03 i 04')===FALSE
				&& strpos($topic->innertext,'One Piece #732')===FALSE
				&& strpos($topic->innertext,'Fairy Tail 131, 132 i 133')===FALSE
				&& strpos($topic->innertext,'Lovely Complex 19')===FALSE
				&& strpos($topic->innertext,'Temporada d\'anime tardor-hivern a LlPnF')===FALSE
				&& strpos($topic->innertext,'Sora no Otoshimono #5')===FALSE
				&& strpos($topic->innertext,'Més enllà dels núvols, el lloc promès')===FALSE){
				$html_text_topic = file_get_contents($base_url.$topic->href) or $error_connect=TRUE;
				if ($error_connect){
					return array('error_connect',array());
				}
				$tidy_topic = tidy_parse_string($html_text_topic, $tidy_config, 'UTF8');
				tidy_clean_repair($tidy_topic);
				$html_topic = str_get_html(tidy_get_output($tidy_topic));

				$is_blogspot = FALSE;
				foreach($html_topic->find('div.post div.content div a img') as $linked_image) {
					if (strpos($linked_image->parent->href,'.blogspot.')!==FALSE){
						$is_blogspot = TRUE;
					}
				}

				if (!$is_blogspot){
					//Create an empty item
					$item = array();

					//Look up and add elements to the item
					$title=$html_topic->find('h2.topic-title a', 0)->innertext;
					$item[0]=$title;

					$description=$html_topic->find('div.post div.content div', 0)->innertext;

					$item[1]=$description;
					$item[2]=parse_description($description);

					$datetext = $html_topic->find('div.post p.author', 0)->plaintext;
					//We now have this (only the text): <img class="sprite-icon_post_target" src="http://illiweb.com/fa/empty.gif" alt="Missatge" title="Missatge">&nbsp;&nbsp;<a href="/u227"><span style="color:#7F0985"><strong>bombillero</strong></span></a> el Dt 05 Jul 2016, 15:08
					$datetext = substr(strrstr($datetext, " el "), 4);

					$datetext = str_ireplace('Gen', 'January', $datetext);
					$datetext = str_ireplace('Feb', 'February', $datetext);
					$datetext = str_ireplace('Mar', 'March', $datetext);
					$datetext = str_ireplace('Abr', 'April', $datetext);
					$datetext = str_ireplace('Maig', 'May', $datetext);
					$datetext = str_ireplace('Mai', 'May', $datetext);
					$datetext = str_ireplace('Jun', 'June', $datetext);
					$datetext = str_ireplace('Jul', 'July', $datetext);
					$datetext = str_ireplace('Ago', 'August', $datetext);
					$datetext = str_ireplace('Set', 'September', $datetext);
					$datetext = str_ireplace('Oct', 'October', $datetext);
					$datetext = str_ireplace('Nov', 'November', $datetext);
					$datetext = str_ireplace('Des', 'December', $datetext);

					$datetext = str_ireplace('Dl', 'Mon', $datetext);
					$datetext = str_ireplace('Dt', 'Tue', $datetext);
					$datetext = str_ireplace('Dc', 'Wed', $datetext);
					$datetext = str_ireplace('Dj', 'Thu', $datetext);
					$datetext = str_ireplace('Dv', 'Fri', $datetext);
					$datetext = str_ireplace('Ds', 'Sat', $datetext);
					$datetext = str_ireplace('Dg', 'Sun', $datetext);

					$date = date_create_from_format('D d F Y, H:i', $datetext);
					$item[3]= $date->format('Y-m-d H:i:s');
					$item[4]= $base_url.$topic->href;
					$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

					$elements[]=$item;
				}
			}
		}

		$go_on = FALSE;
	
		//This fetching method has no support for pre-existing data. All data is fetched again each time.
		//If not used as onetime, will lead to duplicates
		if ($html->find('img.sprite-arrow_prosilver_right', 0)!==NULL){
			$html_text = file_get_contents($base_url . $html->find('img.sprite-arrow_prosilver_right', 0)->parent->href) or $error_connect=TRUE;
			if ($error_connect){
					return array('error_connect',array());
			}
			$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
			tidy_clean_repair($tidy);
			$html = str_get_html(tidy_get_output($tidy));
			$go_on = TRUE;
		}
	}
	return array('ok', $elements);
}

function fetch_via_roninfansub($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	//parse through the HTML and build up the elements feed as we go along
	foreach($html->find('div.titolpublicacio') as $article) {
		//Fix for elements not containing a date
		if ($article->next_sibling()->find('div.datapublicacio', 0)!==NULL){
			//Create an empty item
			$item = array();

			//Look up and add elements to the item
			$item[0]=$article->find('a', 0)->innertext;

			$description = $article->next_sibling()->innertext;
			$item[1]=$description;

			//We have to explode because the format is: 05/07/2015
			$datetext = $article->next_sibling()->find('div.datapublicacio', 0)->innertext;

			$item[2]=parse_description(str_replace($datetext, '', $description));

			$date = date_create_from_format('d/m/Y H:i', $datetext);

			$item[3]=$date->format('Y-m-d H:i:s');
			$item[4]=$url . ($article->find('a', 0)!==NULL ? $article->find('a', 0)->href : '');
			$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

			$elements[]=$item;
		}
	}
	return array('ok', $elements);
}

function fetch_via_weebly_rnnf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.blog-post') as $article) {
			if ($article->find('h2.blog-title a', 0)!==NULL && stripos($article->find('h2.blog-title a', 0),'audio en Català')===FALSE && stripos($article->find('h2.blog-title a', 0),'audio en catala')===FALSE && stripos($article->find('h2.blog-title a', 0),'Bleach 001')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 396, 397 i 298')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 399, 400 i 401')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 402, 403 i 404')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece 405')===FALSE && stripos($article->find('h2.blog-title a', 0),'InuYasha')===FALSE){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h2.blog-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.blog-content', 0)->innertext;

				$item[1]=$article->find('div.blog-content', 0)->innertext;
				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('p.blog-date span', 0)->innertext;

				$date = date_create_from_format('!m/d/Y', $datetext);

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$url . substr($title->href, 1);
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='&lt;&lt; Previous'){
					//Not sleeping, Weebly does not appear to be rate-limited
					$html_text = file_get_contents($url . substr($text->parent->href, 1)) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_arf($fansub_slug, $url, $last_fetched_item_date){
	//To be used only for initial import: this theme uses Ajax
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url.'page/1/') or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;
	$current_page=1;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('.post') as $article) {
			if ($article->find('h2.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h2.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-content', 0)->innertext;

				$description = $article->find('div.post-content', 0)->innertext;

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('p.post-date', 0)->innertext;

				$datetext = str_ireplace('gener', 'January', $datetext);
				$datetext = str_ireplace('febrer', 'February', $datetext);
				$datetext = str_ireplace('març', 'March', $datetext);
				$datetext = str_ireplace('abril', 'April', $datetext);
				$datetext = str_ireplace('maig', 'May', $datetext);
				$datetext = str_ireplace('juny', 'June', $datetext);
				$datetext = str_ireplace('juliol', 'July', $datetext);
				$datetext = str_ireplace('agost', 'August', $datetext);
				$datetext = str_ireplace('setembre', 'September', $datetext);
				$datetext = str_ireplace('octubre', 'October', $datetext);
				$datetext = str_ireplace('novembre', 'November', $datetext);
				$datetext = str_ireplace('desembre', 'December', $datetext);

				$date = date_create_from_format('F d, Y H:i:s', $datetext . ' 00:00:00');
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$go_on = FALSE;
		$current_page++;
		if ($current_page<=15){
			//Not sleeping, Wordpress.com does not appear to be rate-limited
			$html_text = file_get_contents($url.'page/'.$current_page.'/') or $error_connect=TRUE;
			if ($error_connect){
				return array('error_connect',array());
			}
			$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
			tidy_clean_repair($tidy);
			$html = str_get_html(tidy_get_output($tidy));
			$go_on = TRUE;
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_ddc($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('article') as $article) {
			if ($article->find('h1.entry-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h1.entry-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.entry-content', 0)->innertext;

				$description = str_replace("text-align:center;","",$article->find('div.entry-content', 0)->innertext);
				$description = preg_replace("/\<img (.*)submanga(.*)w=190\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)mediafire(.*)w=190\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)submanga(.*)w=560\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)mediafire(.*)w=560\" \/\>/i", '', $description);

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('time', 0)->datetime;

				$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext==' Entradas anteriores'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_mdcf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('div.main-title h3 a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('div.main-title h3 a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.entry', 0)->innertext;
				$item[1]=$description;

				$item[2]=parse_description($description);

				//The format is: març 5, 2013
				$datetext = $article->find('div.post-date span', 0)->innertext;

				$datetext = str_ireplace('gener', 'January', $datetext);
				$datetext = str_ireplace('febrer', 'February', $datetext);
				$datetext = str_ireplace('març', 'March', $datetext);
				$datetext = str_ireplace('abril', 'April', $datetext);
				$datetext = str_ireplace('maig', 'May', $datetext);
				$datetext = str_ireplace('juny', 'June', $datetext);
				$datetext = str_ireplace('juliol', 'July', $datetext);
				$datetext = str_ireplace('agost', 'August', $datetext);
				$datetext = str_ireplace('setembre', 'September', $datetext);
				$datetext = str_ireplace('octubre', 'October', $datetext);
				$datetext = str_ireplace('novembre', 'November', $datetext);
				$datetext = str_ireplace('desembre', 'December', $datetext);

				$date = date_create_from_format('F d, Y H:i:s', $datetext . ' 00:00:00');

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='« Older Entries'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_xf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('div.post-header h2 a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('div.post-header h2 a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.entry', 0)->innertext;
				$item[1]=$description;

				//We replace the sharer with an empty string to remove the share links
				foreach ($article->find('div.entry div#jp-post-flair') as $sharer){
					$description = str_replace($sharer->outertext, '', $description);
				}

				$item[2]=parse_description($description);

				//The format is: març 5, 2013
				$datetext = $article->find('div.post-header div.date a', 0)->innertext;

				$datetext = str_ireplace('gener', 'January', $datetext);
				$datetext = str_ireplace('febrer', 'February', $datetext);
				$datetext = str_ireplace('març', 'March', $datetext);
				$datetext = str_ireplace('abril', 'April', $datetext);
				$datetext = str_ireplace('maig', 'May', $datetext);
				$datetext = str_ireplace('juny', 'June', $datetext);
				$datetext = str_ireplace('juliol', 'July', $datetext);
				$datetext = str_ireplace('agost', 'August', $datetext);
				$datetext = str_ireplace('setembre', 'September', $datetext);
				$datetext = str_ireplace('octubre', 'October', $datetext);
				$datetext = str_ireplace('novembre', 'November', $datetext);
				$datetext = str_ireplace('desembre', 'December', $datetext);

				$date = date_create_from_format('F d, Y H:i:s', $datetext . ' 00:00:00');

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='« Older Entries'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_ynf($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('article') as $article) {
			if ($article->find('h1.entry-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h1.entry-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.entry-content', 0)->innertext;

				$description = str_replace("text-align:center;","",$article->find('div.entry-content', 0)->innertext);

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('time', 0)->datetime;

				$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext==' Entrades més antigues'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_ys($fansub_slug, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('article') as $article) {
			if ($article->find('h2.entry-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h2.entry-title a', 0);
				$item[0]=$title->innertext;

				$html_text_article = file_get_contents($title->href) or $error_connect=TRUE;
				if ($error_connect){
					return array('error_connect',array());
				}
				$tidy = tidy_parse_string($html_text_article, $tidy_config, 'UTF8');
				tidy_clean_repair($tidy);
				$html_article = str_get_html(tidy_get_output($tidy));

				$item[1]=$html_article->find('div.entry-content', 0)->innertext;

				$description = $html_article->find('div.entry-content', 0)->innertext;

				$parts = explode('<style', $description);
				if (count($parts)>1) {
					$description = $parts[0];
				}

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('time', 0)->datetime;

				$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_slug, $url, $description);

				$elements[]=$item;
			}
		}

		//This is untested: no paging as of now, copy/paste from another fetcher
		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext==' Entrades més antigues'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}
?>
