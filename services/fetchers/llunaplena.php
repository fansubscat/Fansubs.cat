<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('Lluna Plena no Fansub');
$TestFeed->setLink('http://llunaplenanofansub.blogspot.com.es/');

$blog_urls = array("http://llunaplenanofansub.blogspot.com.es/", "http://cuadefada.blogspot.com.es/", "http://unapeca.blogspot.com.es/");

foreach ($blog_urls as $blog_url){
	$html = file_get_html($blog_url) or exit(1);

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the RSS feed as we go along
		foreach($html->find('div.post') as $article) {
			//We only show news with tag "Notícies" from the main blog, or we will also show the series pages...
			if ($blog_url!='http://llunaplenanofansub.blogspot.com.es/' || strpos($article->find('span.post-labels', 0)->innertext, 'Notícies')!==FALSE){
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

				if ($title!=NULL){
					$newItem->setLink($title->href);
				}
				else{
					$newItem->setLink($blog_url);
				}
				$newItem->setDate($article->find('abbr.published', 0)->title);

				//Now add the feed item
				$TestFeed->addItem($newItem);
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		foreach ($texts as $text){
			if ($text->plaintext=='Missatges més antics'){
				sleep(2); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
				$html = file_get_html($text->parent->href) or exit(1);
				$go_on = TRUE;
				break;
			}
		}
	
	}
}

$TestFeed->generateFeed();
?>
