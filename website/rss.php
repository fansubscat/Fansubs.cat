<?php
require_once("db.inc.php");

//Ugly GUID generator, based on URL and date
//Improves RSS feed compatibility
function generate_guid($url, $date){
	return md5($url . "-" . $date);
}

header('Content-Type: application/rss+xml; charset=utf-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Fansubs.cat</title>
		<link>http://www.fansubs.cat/</link>
		<description>Les notícies dels fansubs en català</description>
		<atom:link href="http://www.fansubs.cat/rss" rel="self" type="application/rss+xml" />
<?php

$result = mysqli_query($db_connection, "SELECT n.*,f.name fansub_name,f.url fansub_url,f.logo_image fansub_logo_image FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id ORDER BY date DESC LIMIT 20") or crash(mysqli_error($db_connection));
while ($row = mysqli_fetch_assoc($result)){
?>
		<item>
			<title><?php echo $row['fansub_name']; ?>: <?php echo $row['title']; ?></title>
			<link><?php echo $row['url']!=NULL ? $row['url'] : 'http://www.fansubs.cat/'; ?></link>
			<description><![CDATA[<?php
	if ($row['image']!=NULL){
			echo '<img src="http://www.fansubs.cat/images/news/'.$row['fansub_id'].'/'.$row['image'].'" alt="" /><br /><br />';
	}
	echo $row['contents']; 
?>]]></description>
			<guid isPermaLink="false"><?php echo generate_guid($row['url'], $row['date']); ?></guid>
			<pubDate><?php echo date('r', strtotime($row['date'])); ?></pubDate>
		</item>
<?php
}
?>
	</channel>
</rss>
<?php
mysqli_free_result($result);
mysqli_close($db_connection);
?>
