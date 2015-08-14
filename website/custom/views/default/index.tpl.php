<?php
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

header('Content-type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html lang="ca">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#888888" />

		<title><?php echo $PlanetConfig->getName(); ?> - Les notícies dels fansubs en català</title>

<?php include(dirname(__FILE__).'/head.tpl.php'); ?>
	</head>

	<body>
		<div id="page">
<?php include(dirname(__FILE__).'/top.tpl.php'); ?>
			<div id="content">
<?php if ($_COOKIE['benvinguda']!='0'){ ?>
				<div id="benvinguda">
					<img id="noia" src="custom/img/benvinguts.png" alt="" />
					<img id="tanca" src="custom/img/tanca.png" alt="Amaga aquest missatge" onclick="document.cookie='benvinguda=0';document.getElementById('benvinguda').style.display='none';" />
					<div id="textbenvinguda">
						<div id="textbenvingudareal">
							<strong>Benvinguts a Fansubs.cat!</strong> Aquí hi trobareu les últimes notícies de tots els fansubs en català!<br />
							Les notícies s'obtenen automàticament dels diferents webs dels fansubs.<br />
							Per accedir a cada notícia, només cal que hi feu clic!
						</div>
					</div>
				</div>
<?php
}
?>
<?php if (0 == count($items)) : ?>
				<div class="article">
					<h2 class="article-title">No hi ha notícies</h2>
					<p class="article-content">Això no són bones notícies!</p>
				</div>
<?php else : ?>
<?php foreach ($items as $item):
	$arParsedUrl = parse_url($item->get_feed()->get_link());
	$host = preg_replace('/[^a-zA-Z]/i', '-', $arParsedUrl['host']);
?>
				<div class="article <?php echo $host; ?>">
					<a class="article-logo" href="<?php echo $item->get_feed()->get_link(); ?>" title="<?php echo $item->get_feed()->name; ?>">
						<img src="custom/img/logo-<?php echo $host; ?>.png" alt="<?php echo $item->get_feed()->name; ?>" />
					</a>
					<h2 class="article-title">
						<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
					</h2>
					<p class="article-info">
						<?php
                        $ago = (int)(time() - $item->get_date('U'));

			if ($ago<60){
				$ago = "fa menys d'un minut";
			}
			else if ($ago<3600){
				$ago = (int)($ago/60);
				if ($ago==1){
	                                $ago = "fa ". $ago . " minut";
				}
				else{
					$ago = "fa ". $ago . " minuts";
				}
                        }
			else if ($ago<86400){
				$ago = (int)($ago/3600);
				if ($ago==1){
	                                $ago = "fa ". $ago . " hora";
				}
				else{
					$ago = "fa ". $ago . " hores";
				}
                        }
			else if ($ago<2678400){
				$ago = (int)($ago/86400);
				if ($ago==1){
	                                $ago = "fa ". $ago . " dia";
				}
				else{
					$ago = "fa ". $ago . " dies";
				}
                        }
			else{
				$ago = $item->get_date('d/m/Y \a \l\e\s H:i');
			}

                            echo '<span id="post'.$item->get_date('U').'" class="date" title="'.$item->get_date('d/m/Y \a \l\e\s H:i') . '">' . $ago . "</span>\n";
                            ?>
					</p>
					<div class="article-content">
<?php

$content = $item->get_content();

preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $content, $matches);
//preg_match_all('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
$first_image_url = count($matches>1) ? $matches[1][0] : '';

if ($first_image_url!=''){

?>
						<img class="article-image" src="<?php echo $first_image_url; ?>" alt=""/>
						<!-- Begin feed content -->
						<?php
}

$content = strip_tags($content, '<br><b><strong><em><i><ul><li><ol><hr><sub><sup><u><tt><p>');
$content = str_replace('&nbsp;',' ', $content);
$content = str_replace(' & ','&amp;', $content);
$content = str_replace('<br>','<br />', $content);
$content = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br /><br />', $content);
echo preg_replace('/(?:<br\s*\/?>\s*)+$/', '', preg_replace('/^(?:<br\s*\/?>\s*)+/', '', trim($content)));
?>

						<!-- End feed content -->
					</div>
					<div class="article-readmore">
						<a href="<?php echo $item->get_permalink(); ?>">Vés a <?php echo $item->get_feed()->name; ?> ➔</a>
					</div>
				</div>
<?php if (++$count == $limit) { break; } ?>
<?php endforeach; ?>
<?php endif; ?>
			</div>

<?php include_once(dirname(__FILE__).'/sidebar.tpl.php'); ?>

<?php include(dirname(__FILE__).'/footer.tpl.php'); ?>
		</div>
	</body>
</html>
