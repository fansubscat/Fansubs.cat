<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('CatSub');
$TestFeed->setLink('http://www.catsub.net/');

$tidy_config = dirname(__FILE__) . "/tidy.conf";

$html_text = file_get_contents("http://www.catsub.net/") or exit(1);
$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
tidy_clean_repair($tidy);
$html = str_get_html(tidy_get_output($tidy));

$go_on = TRUE;
$feed_count = 0;

while ($go_on){
	//parse through the HTML and build up the RSS feed as we go along
	foreach($html->find('div.cs_news') as $article) {
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();

		//Look up and add elements to the feed item   
		$newItem->setTitle($article->find('div.cs_newstitle a', 0)->innertext);

		$description = $article->find('div.cs_newscontent', 0)->innertext;

		//Remove the download icon, or it will be the first image of the feed
		$description = preg_replace("/\<img (.*)dlicon(.*)Descàrregues\" \/\>/i", '', $description);

		//Remove the post-screenshot text
		if (strpos($description, 'cs_newsimage')!==0){
			$description = preg_replace("/\<strong\>(.*)\<\/strong\>$/i", '', trim($description));
		}

		$newItem->setDescription($description);
		$newItem->setLink("http://www.catsub.net/" . $article->find('div.cs_newstitle a', 0)->href);

		//We have to explode because the format is: 05/07/2015 a les 19:48 / Ereza
		$datetext = explode(' / ', $article->find('div.cs_date', 0)->innertext)[0];

		$date = date_create_from_format('d/m/Y \a \l\e\s H:i', $datetext);

		$newItem->setDate($date->format('Y-m-d H:i:s'));

		//Now add the feed item
		$TestFeed->addItem($newItem);
		$feed_count++;
	}

	$texts = $html->find('text');
	$go_on = FALSE;
	foreach ($texts as $text){
		if ($text->plaintext=='Notícies més antigues &gt;'){
			$html_text = file_get_contents("http://www.catsub.net/" . $text->parent->href) or exit(1);
			$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
			tidy_clean_repair($tidy);
			$html = str_get_html(tidy_get_output($tidy));
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
