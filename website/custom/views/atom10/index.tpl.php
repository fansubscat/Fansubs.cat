<?php
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

header('Content-Type: text/plain; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?><feed xmlns="http://www.w3.org/2005/Atom">
    <title><?php echo $PlanetConfig->getName(); ?></title>
    <subtitle>Les notícies dels fansubs en català</subtitle>
    <id><?php echo $PlanetConfig->getUrl(); ?></id>
    <link rel="self" type="application/atom+xml" href="<?php echo $PlanetConfig->getUrl(); ?>?type=atom10" />
    <link rel="alternate" type="text/html" href="<?php echo $PlanetConfig->getUrl(); ?>" />
    <updated><?php echo date("Y-m-d\TH:i:s\Z") ?></updated>
    <author><name>Fansubs.cat</name></author>
  
<?php $count = 0; ?>
<?php foreach ($items as $item): ?>
    <entry xmlns="http://www.w3.org/2005/Atom">
        <title type="html"><?php echo htmlspecialchars($item->get_feed()->getName()); ?>: <?php echo htmlspecialchars($item->get_title());?></title>
        <id><?php echo htmlspecialchars($item->get_permalink());?></id>
        <link rel="alternate" href="<?php echo htmlspecialchars($item->get_permalink());?>"/>
        <published><?php echo $item->get_date('Y-m-d\\TH:i:s+00:00'); ?></published>
	<updated><?php echo $item->get_date('Y-m-d\\TH:i:s+00:00'); ?></updated>
        
        <content type="html"><![CDATA[<?php

$content = $item->get_content();
$content = strip_tags($content, '<br><b><strong><em><i><ul><li><ol><hr><sub><sup><u><tt><p>');
$content = str_replace('&nbsp;',' ', $content);
$content = str_replace(' & ','&amp;', $content);
$content = str_replace('<br>','<br />', $content);
$content = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br /><br />', $content);
$content = preg_replace('/(?:<br\s*\/?>\s*)+$/', '', preg_replace('/^(?:<br\s*\/?>\s*)+/', '', trim($content)));

echo $content;?>]]></content>
    </entry>
    <?php if (++$count == $limit) { break; } ?>
    <?php endforeach; ?>
</feed>
