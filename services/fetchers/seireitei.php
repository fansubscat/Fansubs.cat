<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('Seireitei no Fansub');
$TestFeed->setLink('http://seireiteinofansub.blogspot.com.es/');

$tidy_config = dirname(__FILE__) . "/tidy.conf";

$html_text = file_get_contents("http://seireiteinofansub.blogspot.com.es/") or exit(1);
$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
tidy_clean_repair($tidy);
$html = str_get_html(tidy_get_output($tidy));

$go_on = TRUE;
$feed_count = 0;

while ($go_on){
	//parse through the HTML and build up the RSS feed as we go along
	foreach($html->find('div.post') as $article) {
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();

		//Look up and add elements to the feed item   
		$title = $article->find('h3.post-title a', 0);
		if ($title!=NULL){
			$newItem->setTitle($title->innertext);
		}
		else{
			$newItem->setTitle('(Sense títol)');
		}

		$description = $article->find('div.post-body', 0)->innertext;

		//We remove the password string (seems to always be the same)
		$description = str_replace("<b>Contrasenya: snf</b>",'', $description);

		$newItem->setDescription($description);

		if ($title!=NULL){
			$newItem->setLink($title->href);
		}
		else{
			$newItem->setLink('http://seireiteinofansub.blogspot.com.es/');
		}
		$newItem->setDate($article->find('abbr.published', 0)->title);

		//Now add the feed item
		$TestFeed->addItem($newItem);
		$feed_count++;
	}

	$texts = $html->find('text');
	$go_on = FALSE;
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
				exit(1);
			}
			$go_on = TRUE;
			break;
		}
	}
	
}

if ($feed_count==0){
	//No error but no feeds, this is wrong
	exit(1);
}

$TestFeed->generateFeed();
?>
