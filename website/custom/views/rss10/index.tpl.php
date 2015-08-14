<?php
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?><rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:cc="http://web.resource.org/cc/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns="http://purl.org/rss/1.0/">

    <channel rdf:about="<?php echo $PlanetConfig->getUrl(); ?>">
        <title><?php echo $PlanetConfig->getName(); ?></title>
        <description>Les notícies dels fansubs en català</description>
        <link><?php echo $PlanetConfig->getUrl(); ?></link>
        <dc:language>ca</dc:language>
        <dc:creator></dc:creator>
        <dc:rights></dc:rights>
        <dc:date><?php echo date('Y-m-d\\TH:i:s+00:00'); ?></dc:date>
        <admin:generatorAgent rdf:resource="http://moonmoon.inertie.org/" />

        <items>
        <rdf:Seq>
            <?php foreach ($items as $item): ?>
            <rdf:li rdf:resource="<?php echo $item->get_permalink(); ?>"/>
            <?php if (++$count == $limit) { break; } ?>
            <?php endforeach; ?>
        </rdf:Seq>
        </items>
    </channel>

<?php $count = 0; ?>
<?php foreach ($items as $item): ?>
    <item rdf:about="<?php echo $item->get_permalink();?>">
        <title><?php echo htmlspecialchars($item->get_feed()->getName()) ?>: <?php echo htmlspecialchars($item->get_title());?></title>
        <link><?php echo htmlspecialchars($item->get_permalink());?></link>
        <dc:date><?php echo date('Y-m-d\\TH:i:s+00:00',$item->get_date('U')); ?></dc:date>
        <description><?php
$content = $item->get_content();
$content = strip_tags($content, '<br><b><strong><em><i><ul><li><ol><hr><sub><sup><u><tt><p>');
$content = str_replace('&nbsp;',' ', $content);
$content = str_replace(' & ','&amp;', $content);
$content = str_replace('<br>','<br />', $content);
$content = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br /><br />', $content);
$content = preg_replace('/(?:<br\s*\/?>\s*)+$/', '', preg_replace('/^(?:<br\s*\/?>\s*)+/', '', trim($content)));

echo htmlspecialchars($content);
?></description>
        <content:encoded><![CDATA[<?php echo $content;?>]]></content:encoded>
    </item>
    <?php if (++$count == $limit) { break; } ?>
    <?php endforeach; ?>
    
</rdf:RDF>
