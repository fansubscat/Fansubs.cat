<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('Lluna Plena no Fansub');
$TestFeed->setLink('http://llunaplenanofansub.blogspot.com.es/');

$tidy_config = dirname(__FILE__) . "/tidy.conf";

$blog_urls = array("http://llunaplenanofansub.blogspot.com.es/", "http://cuadefada.blogspot.com.es/", "http://unapeca.blogspot.com.es/");

$feed_count = 0;

foreach ($blog_urls as $blog_url){
	$html_text = file_get_contents($blog_url) or exit(1);
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the RSS feed as we go along
		foreach($html->find('div.post') as $article) {
			//We only show news with tag "Notícies" from the main blog, or we will also show the series pages...
			if ($article->find('h3.post-title a', 0)!==NULL &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')>0) &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')>0)){
				//Create an empty FeedItem
				$newItem = $TestFeed->createNewItem();

				//Look up and add elements to the feed item   
				$title = $article->find('h3.post-title a', 0);
				$newItem->setTitle($title->innertext);

				$description = $article->find('div.post-body', 0)->innertext;

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

				$newItem->setDescription($description);

				$newItem->setLink($title->href);
				$newItem->setDate($article->parent->find('abbr.published', 0)->title);

				//Now add the feed item
				$TestFeed->addItem($newItem);
				$feed_count++;
			}
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
}

if ($feed_count==0){
	//No error but no feeds, this is wrong
	exit(1);
}

$TestFeed->generateFeed();
?>
