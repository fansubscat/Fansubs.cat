<?php
$count = 0;
$today = Array();
$week = Array();
$month = Array();
$older = Array();
$now = time();

foreach ($items as $item) {
    $age = ($now - $item->get_date('U')) / (60*60*24);
    if ($age < 1) {
        $today[] = $item;
    } elseif ($age < 7) {
        $week[] = $item;
    } elseif ($age < 30) {
        $month[] = $item;
    } else {
	if (!isset($older[$item->get_date('Y')])){
		$older[$item->get_date('Y')] = array();
	}
        $older[$item->get_date('Y')][] = $item;
    }
}
header('Content-type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#888888" />

		<title><?php echo $PlanetConfig->getName(); ?> - Arxiu de notícies</title>
<?php include(dirname(__FILE__).'/head.tpl.php'); ?>
	</head>
	<body>
		<div id="page">
<?php include(dirname(__FILE__).'/top.tpl.php'); ?>
			<div id="content">
<?php
if (0 == count($items)) {
?>
				<div class="article">
					<h2 class="article-title">No hi ha notícies</h2>
					<p class="article-content">Això no són bones notícies!</p>
				</div>
<?php
}

if (count($today)) {
?>
				<div class="article">
					<h2>Avui</h2>
					<ul>
<?php
	foreach ($today as $item) {
		$feed = $item->get_feed();
?>
						<li>
							<a href="<?php echo $feed->getWebsite() ?>" class="source"><?php echo $feed->getName() ?></a>:
							<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
						</li>
<?php
	}
?>
					</ul>
				</div>
<?php
}

if (count($week)) {
?>
				<div class="article">
					<h2>Fa menys d'una setmana</h2>
					<ul>
<?php
	foreach ($week as $item) {
	 	$feed = $item->get_feed();
?>
						<li>
							<a href="<?php echo $feed->getWebsite() ?>" class="source"><?php echo $feed->getName() ?></a>:
							<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
						</li>
<?php
	}
?>
					</ul>
				</div>
<?php
}
?>

<?php
if (count($month)) {
?>
				<div class="article">
					<h2>Fa menys d'un mes</h2>
					<ul>
<?php
	foreach ($month as $item) {
		$feed = $item->get_feed();
?>
						<li>
							<a href="<?php echo $feed->getWebsite() ?>" class="source"><?php echo $feed->getName() ?></a>:
							<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
						</li>
<?php
	}
?>
					</ul>
				</div>
<?php
}

if (count($older)) {
	foreach ($older as $year => $arritems){
?>
				<div class="article">
					<h2><?php echo $year; ?></h2>
					<ul>
<?php
		foreach ($arritems as $item) {
			$feed = $item->get_feed();
?>
						<li>
							<a href="<?php echo $feed->getWebsite() ?>" class="source"><?php echo $feed->getName() ?></a>:
							<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
						</li>
<?php
		}
?>
					</ul>
				</div>
<?php
	}
}
?>
			</div>

<?php include_once(dirname(__FILE__).'/sidebar.tpl.php'); ?>

<?php include(dirname(__FILE__).'/footer.tpl.php'); ?>
		</div>
	</body>
</html>
