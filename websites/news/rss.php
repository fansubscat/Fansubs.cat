<?php
require_once("../common.fansubs.cat/db.inc.php");

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
		<title>Fansubs.cat - Notícies</title>
		<link>https://noticies.fansubs.cat/</link>
		<description>Totes les notícies dels fansubs en català</description>
		<atom:link href="https://noticies.fansubs.cat/rss" rel="self" type="application/rss+xml" />
<?php
$result = query("SELECT n.*, f.name fansub_name, f.slug fansub_slug, f.url fansub_url FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id ORDER BY n.date DESC LIMIT 20");
while ($row = mysqli_fetch_assoc($result)){
?>
		<item>
			<title><?php echo $row['fansub_name']; ?>: <?php echo $row['title']; ?></title>
			<link><?php echo $row['url']!=NULL ? $row['url'] : 'https://noticies.fansubs.cat/'; ?></link>
			<description><![CDATA[<?php
	if ($row['image']!=NULL){
			echo '<img src="https://static.fansubs.cat/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" alt="" /><br /><br />';
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
