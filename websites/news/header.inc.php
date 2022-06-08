<?php
ob_start();
require_once("db.inc.php");
require_once('common.inc.php');
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#888888" />

		<title><?php echo $header_page_title; ?></title>

<?php
$is_fools_day = (date('d')==28 && date('m')==12);
if ($is_fools_day){
?>
		<link rel="stylesheet" media="screen" type="text/css" href="/style/fansubscat_28dec.css" />
<?php
}
else{
?>
		<link rel="stylesheet" media="screen" type="text/css" href="/style/fansubscat.css" />
<?php
}
?>
		<link rel="stylesheet" type="text/css" media="screen" href="/style/magnific-popup-1.1.0.css" />

<?php
if ($header_current_page=='main'){
?>
		<link rel="alternate" type="application/rss+xml" title="RSS" href="/rss" />
<?php
}
?>
		<link rel="shortcut icon" href="/favicon.png" />

<?php
if ($header_current_page=='main'){
?>
		<meta property="og:title" content="Fansubs.cat - Les notícies dels fansubs en català" />
		<meta property="og:url" content="https://www.fansubs.cat/" />
		<meta property="og:description" content="A Fansubs.cat trobareu les darreres notícies de tots els fansubs en català! Les notícies s'obtenen automàticament dels diferents webs dels fansubs. Per a accedir a cada notícia, només cal que hi facis clic!" />
		<meta property="og:image" content="https://www.fansubs.cat/style/images/header2.jpg" />
<?php
}
?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
		<script src="/js/jquery.magnific-popup-1.1.0.min.js"></script>
		<script src="/js/js.cookie-2.1.2.min.js"></script>
		<script src="/js/common.js"></script>
<?php
if ($header_current_page=='stats'){
	$result = mysqli_query($db_connection, "SELECT name FROM fansub ORDER BY name ASC");
	$fansubs_names = "";
	while ($row = mysqli_fetch_assoc($result)){
		if ($fansubs_names!=""){
			$fansubs_names.=',';
		}
		$fansubs_names.="'".str_replace("'","\\'",$row['name'])."'";
	}
	mysqli_free_result($result);
?>
		<script src="https://www.gstatic.com/charts/loader.js"></script>
		<script>
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart);
			function drawChart() {
			var data = google.visualization.arrayToDataTable([
			  ['Any', <?php echo $fansubs_names; ?>],
<?php
	for ($i=2003;$i<=date('Y');$i++){
		$result = mysqli_query($db_connection, "SELECT f.name, COUNT(n.fansub_id) count FROM fansub f LEFT JOIN news n ON f.id=n.fansub_id AND n.date>='$i-01-01 00:00:00' AND n.date<='$i-12-31 23:59:59' GROUP BY f.id ORDER BY f.name ASC");
		$fansubs_data = "";
		while ($row = mysqli_fetch_assoc($result)){
			if ($fansubs_data!=""){
				$fansubs_data.=',';
			}
			$fansubs_data.=$row['count'];
		}
		mysqli_free_result($result);
		if ($i==date('Y')){
			echo "			  ['$i',$fansubs_data]\n";
		}
		else{
			echo "			  ['$i',$fansubs_data],\n";
		}
	}
?>
			]);

			var options = {
			  title: 'Nombre de notícies per anys',
                          titlePosition: 'none',
			  hAxis: {title: 'Any',  titleTextStyle: {color: '#333'}},
			  vAxis: {minValue: 0},
                          chartArea: {width: '90%', height: '75%'},
                          legend: {position: 'bottom'}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
			chart.draw(data, options);
			}
		</script>
<?php
}
?>
	</head>

	<body>
		<div id="page">
			<div id="header">
				<div id="mobilemenu" onclick="toggleMobileMenu();"><img src="/style/images/menu.png" alt="Obre el menú"></div>
				<div id="top" style="background-image: url('/style/images/header<?php echo rand(1, 8); ?>.jpg')">
					<div id="toppadding">
						<div class="page-title-block">
							<a class="page-title-top" href="/">Fansubs.cat</a>
							<div class="page-links">
								<a href="https://anime.fansubs.cat/">Anime</a> | <a href="https://manga.fansubs.cat/">Manga</a> | <b>Notícies</b>
							</div>
						</div>
<?php
if ($is_fools_day){
?>
						<h2><a href="/">Les notícies dels fansubs en català (ara, amb més Comic Sans)</a></h2>
<?php
}
else{
?>
						<h2><a href="/">Les notícies dels fansubs en català</a></h2>
<?php
}
?>
					</div>
				</div>
			</div>
			<div id="content">

<?php
if ((!isset($_COOKIE['welcome_closed']) || $_COOKIE['welcome_closed']!='1') && $header_current_page=='main'){
?>
				<div id="welcome">
					<img id="girl" src="/style/images/welcome.png" alt="" />
					<img id="close" src="/style/images/close.png" alt="Amaga aquest missatge" />
					<div id="welcometext">
						<div id="realwelcometext">
							<strong>Et donem la benvinguda a Fansubs.cat!</strong> Aquí trobaràs les darreres notícies de tots els fansubs en català!<br />
							Les notícies s'obtenen automàticament dels diferents webs dels fansubs.<br />
							Per a accedir a cada notícia, només cal que hi facis clic!
						</div>
					</div>
				</div>
<?php
}

//Show app layout if we are being accessed from an Android browser (rely on the User-Agent)
if ((!isset($_COOKIE['app_closed']) || $_COOKIE['app_closed']!='1') && $header_current_page=='main' && stripos(strtolower($_SERVER['HTTP_USER_AGENT']),'android')!==FALSE){
?>
				<div id="app">
					<img id="appicon" src="/style/images/app.png" alt="" />
					<img id="appclose" src="/style/images/close.png" alt="Amaga aquest missatge" />
					<div id="apptext">
						<div id="realapptext">
							<strong>Ara tenim aplicació per a Android!</strong><br /><strong><a href="/app_fansubs_cat.apk">Baixa-la ara</a></strong> i, a més a més de poder consultar les darreres notícies, podràs rebre notificacions quan hi hagi notícies noves.
						</div>
					</div>
				</div>
<?php
}
?>
				<div id="real_content">

