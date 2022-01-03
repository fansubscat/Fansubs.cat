<?php
require_once('config.inc.php');

$year = 2021;

function is_day_ready($day) {
	global $releases, $year;
	$today = date('Y-m-d H:i:s');
	if (!empty($_GET['twitter']) && !empty($_GET['currentday'])) {
		$today = $year.'-12-'.sprintf('%02d', intval($_GET['currentday'])).' 12:00:00';
	}
	$target = $year.'-12-'.sprintf('%02d', $day).' 12:00:00';
	return (strcmp($today,$target)>=0 && (!empty($releases[$day]) || !empty($_GET['twitter'])));
}

if (!empty($_COOKIE['advent_'.$year])) {
	$cookie=explode(',',$_COOKIE['advent_'.$year]);
} else {
	$cookie=array();
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8">
		<title>Calendari d'advent <?php echo $year; ?> - Fansubs.cat</title>
		<link href="https://fonts.googleapis.com/css?family=Kalam" rel="stylesheet">
		<link rel="shortcut icon" href="/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#888888" />
		<meta property="og:title" content="Calendari d'advent <?php echo $year; ?> - Fansubs.cat" />
		<meta property="og:url" content="https://nadal.fansubs.cat/" />
		<meta property="og:description" content="Segueix el calendari d'advent dels fansubs en català! Cada dia hi trobaràs un petit regalet en forma d'anime o manga editat en català!" />
		<meta property="og:image" content="https://nadal.fansubs.cat/images/preview.jpg" />
		<meta name="twitter:card" content="summary_large_image" />
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-628107-13"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', 'UA-628107-13');

			$(document).ready(function() {
				$('input').change(function() {
					var openedDays = $.map($('.checkavailable:checked'), function(n, i){
						return n.value;
					}).join(',');
					Cookies.set('advent_<?php echo $year; ?>', openedDays, { expires: 3650, path: '/', domain: 'fansubs.cat' });
<?php
if (!empty($_GET['twitter'])) {
?>
					setTimeout(function(that){
						var par=$(that).parent().parent();
						var tc = $(window).height() / 2 - $(par).height() * <?php echo $_GET['currentday']==24 ? 3.175 : 7; ?> / 2 - $(par).offset().top;
						var lc = $(window).width() / 2 - $(par).width() * <?php echo $_GET['currentday']==24 ? 3.175 : 7; ?> / 2 - $(par).offset().left;

						//Ugly as fuck, but it works
						var style = document.createElement('style');
						var keyFrames = '\
						@keyframes expand {\
						  from   {width: 100%; height: 100%; left: 0px; top: 0px;}\
						  to {width: <?php echo $_GET['currentday']==24 ? 317.5 : 700; ?>%; height: <?php echo $_GET['currentday']==24 ? 317.5 : 700; ?>%; left: LEFTVALUEpx; top:TOPVALUEpx;}\
						}';
						style.innerHTML = keyFrames.replace(/LEFTVALUE/g, lc).replace(/TOPVALUE/g, tc);
						document.getElementsByTagName('head')[0].appendChild(style);

						$(par).css({
							"backface-visibility": "hidden",
							"z-index": "999",
							"animation": "expand 1s forwards"
						});
					}, 1000, this);
<?php
}
?>
				});
			});
		</script>
		<style>
			html, body {
				min-height: 100vh;
			}
			body {
				background-image: url(images/background.jpg);
				background-position: center center;
				background-repeat: no-repeat;
				background-color: #d7d7d7;
				background-size: cover;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
				margin: 0;
				display: flex;
			}

			.container {
				width: 100%;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				box-sizing: border-box;
				padding: 0 8px;
			}

			/* title graphic */
			.title {
				display: flex;
				align-items: end;
				justify-content: center;
			}

			.title img {
				width: 100%;
				height: auto;
				margin-bottom: 0;
				margin-top: auto;
			}

			/* mobile first grid layout */
			.grid-1 {
				display: grid;
				width: 96%;
				max-width: 900px;
				margin: 2em auto;

				grid-template-columns: repeat(3, 1fr);
				grid-template-rows: auto;
				grid-gap: 25px;

				grid-template-areas:    "t        t       t"
					"d23      d20     d12"
					"d2       d14     d4"
					"d5       d22     d16"
					"d1       d7      d9"
					"d10      d11     d18"
					"d13      d3      d15"
					"d6       d17     d8"
					"d19      d24     d21";
			}

			/* media query */
			@media only screen and (min-width: 720px) {
				body {
					background-size: unset;
				}
				.grid-1 {
					grid-template-columns: repeat(6, 1fr);
					grid-template-areas: "d9      d23      d15     t     t     t"
						"d8      d18     d11     t     t     t"
						"d16     d12      d17     t     t     t"
						"d3    d14    d24   d24     d21     d6"
						"d5   d10   d24   d24     d4      d20"
						"d19    d7   d2   d13     d1     d22";
				}

			}

			.title {
				grid-area: t;
			}

			.grid-1 input {
				display: none;
			}

			label {
				perspective: 1000px;
				transform-style: preserve-3d;
				cursor: pointer;

				display: flex;
				min-height: 100%;
				width: 100%;
				height: 120px;
			}

			.door {
				width: 100%;
				transform-style: preserve-3d;
				transition: all 300ms;
				border: 3px solid transparent;
				border-radius: 8px;
			}

			.door span {
				position: absolute;
				height: 100%;
				width: 100%;
				backface-visibility: hidden;
				border-radius: 6px;
				display: flex;
				align-items: center;
				justify-content: center;
				font-family: 'Kalam', cursive;
				color: white;
				font-size: 2.5em;
				font-weight: bold;
				text-shadow: 0 0 3px rgba(0, 0, 0, 1);
			}

			.door .front {
				background-color: rgba(255,255,255,0.2);
			}

			.door .front:active {
				background-color: rgba(255,0,0,0.2);
				color: #FF8080;
			}

			.front.available, .front.available:active {
				background-color: rgba(255, 255, 255, 0.6);
				color: #FFFFFF;
			}

			.door .back {
				background-size: cover;
				background-position: center center;
				background-repeat: no-repeat;
				background-color: black;
				transform: rotateY(180deg);
				display: flex;
				overflow: hidden;
			}

			label .door {
				border-color: rgba(0,0,0,0.2);
			}

			label:hover .door {
				border-color: rgba(255,255,255,0.5);
			}

			label:hover .door.dooravailable {
				border-color: rgba(255,255,255,0.7);
			}

			:checked + .door {
				transform: rotateY(180deg);
			}

			.link{
				text-decoration: none;
				font-size:0.4em;
				color:black;
				align-self: flex-end;
				width: 100%;
				height: 100%;
				text-align: center;
			}

			.link:hover{
				color: #222266;
			}

			.previous{
				text-align: center;
				color: white;
				font-family: sans-serif;
				font-size: 1em;
				font-weight: bold;
				text-shadow: 0.1em 0.1em black;
				padding-bottom: 8px;
			}

			.previous a{
				color: white;
			}

			.previous a:hover{
				color: #DDDDDD;
			}

<?php
for ($i=1;$i<25;$i++){
?>
			.day-<?php echo $i; ?> {
				position: relative;
				grid-area: d<?php echo $i; ?>;
			}
			.day-<?php echo $i; ?> .back {
				background-image: url(<?php echo is_day_ready($i) ? 'covers/'.$i.'.jpg' : 'images/empty.png'; ?>);
			}
<?php
}
?>
		</style>
	</head>
	<body>
		<div class="container">
			<div class="grid-1">
				<div class="title">
					<img src="images/logo.png" alt="Calendari d'advent dels fansubs en català">
				</div>
<?php
for ($i=1;$i<25;$i++){
?>
				<div class="day-<?php echo $i; ?>">
					<label>
						<input type="checkbox"<?php echo is_day_ready($i) ? ' class="checkavailable"' : 'disabled'; ?> value="<?php echo $i; ?>"<?php echo ((is_day_ready($i) && in_array($i,$cookie) && empty($_GET['currentday'])) || !empty($_GET['twitter']) && $_GET['currentday']>$i) ? ' checked' : ''; ?> />
						<span class="door<?php echo is_day_ready($i) ? ' dooravailable' : ''; ?>">
							<span class="front<?php echo is_day_ready($i) ? ' available' : ''; ?>"><?php echo $i; ?></span>
							<span class="back" id="<?php echo $i; ?>">
<?php
	if (is_day_ready($i)) {
?>
								<a class="link" href="<?php echo empty($_GET['twitter']) ? $releases[$i] : '#'; ?>"<?php echo (empty($_GET['twitter']) || strpos($releases[$day],'javascript:')!==0) ? ' target="_blank"' : ''; ?>></a>
<?php
	}
?>
							</span>
						</span>
					</label>
				</div>
<?php
}
?>
			</div>
<?php
if (empty($_GET['twitter'])){
?>
			<div class="previous">
				Edicions anteriors: <a href="/2020/">2020</a>
			</div>
<?php
}
?>
		</div>
	</body>
</html>
