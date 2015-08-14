<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('XOP Fansub');
$TestFeed->setLink('https://xopfansub.wordpress.com/');

$html = file_get_html("https://xopfansub.wordpress.com/") or exit(1);

$go_on = TRUE;

while ($go_on){
	//parse through the HTML and build up the RSS feed as we go along
	foreach($html->find('div.post') as $article) {
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();

		//Look up and add elements to the feed item   
		$title = $article->find('div.post-header h2 a', 0);
		if ($title!=NULL){
			$newItem->setTitle($title->innertext);
		}
		else{
			$newItem->setTitle('(Sense títol)');
		}

		$description = $article->find('div.entry', 0)->innertext;

		//We replace the sharer with an empty string to remove the share links
		foreach ($article->find('div.entry div#jp-post-flair') as $sharer){
			$description = str_replace($sharer->outertext, '', $description);
		}

		$newItem->setDescription($description);

		if ($title!=NULL){
			$newItem->setLink($title->href);
		}
		else{
			$newItem->setLink('https://xopfansub.wordpress.com/');
		}

		//The format is: març 5, 2013
		$datetext = $article->find('div.post-header div.date a', 0)->innertext;
			
		$datetext = str_replace('gener', 'January', $datetext);
		$datetext = str_replace('febrer', 'February', $datetext);
		$datetext = str_replace('març', 'March', $datetext);
		$datetext = str_replace('abril', 'April', $datetext);
		$datetext = str_replace('maig', 'May', $datetext);
		$datetext = str_replace('juny', 'June', $datetext);
		$datetext = str_replace('juliol', 'July', $datetext);
		$datetext = str_replace('agost', 'August', $datetext);
		$datetext = str_replace('setembre', 'September', $datetext);
		$datetext = str_replace('octubre', 'October', $datetext);
		$datetext = str_replace('novembre', 'November', $datetext);
		$datetext = str_replace('desembre', 'December', $datetext);
	
		$date = date_create_from_format('F d, Y H:i:s', $datetext . ' 00:00:00');

		$newItem->setDate($date->format('Y-m-d H:i:s'));

		//Now add the feed item
		$TestFeed->addItem($newItem);
	}

	$texts = $html->find('text');
	$go_on = FALSE;
	foreach ($texts as $text){
		if ($text->plaintext=='&laquo; Older Entries'){
			//Not sleeping, Wordpress.com does not appear to be rate-limited
			$html = file_get_html($text->parent->href) or exit(1);
			$go_on = TRUE;
			break;
		}
	}
	
}

$TestFeed->generateFeed();
?>
