<?php
$releases = array(
	'1' => '',
	'2' => '',
	'3' => '',
	'4' => '',
	'5' => '',
	'6' => '',
	'7' => '',
	'8' => '',
	'9' => '',
	'10' => '',
	'11' => '',
	'12' => '',
	'13' => '',
	'14' => '',
	'15' => '',
	'16' => '',
	'17' => '',
	'18' => '',
	'19' => '',
	'20' => '',
	'21' => '',
	'22' => '',
	'23' => '',
	'24' => ''
);

function is_day_ready($day) {
	global $releases;
	$today = date('Y-m-d H:i:s');
	if (!empty($_GET['currentday'])) {
		$today = '2020-12-'.sprintf('%02d', intval($_GET['currentday'])).' 12:00:00';
	}
	$target = '2020-12-'.sprintf('%02d', $day).' 12:00:00';
	return (strcmp($today,$target)>=0 && !empty($releases[$day]));
}

if (!empty($_COOKIE['advent_2020'])) {
	$cookie=explode(',',$_COOKIE['advent_2020']);
} else {
	$cookie=array();
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8">
		<title>Calendari d'advent - Fansubs.cat</title>
		<link href="https://fonts.googleapis.com/css?family=Kalam" rel="stylesheet">
		<link rel="shortcut icon" href="/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#888888" />
		<meta property="og:title" content="Calendari d'advent - Fansubs.cat" />
		<meta property="og:url" content="https://www.fansubs.cat/nadal/" />
		<meta property="og:description" content="" />
		<meta property="og:image" content="" />
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-628107-13"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script>
			$(document).ready(function() {
				$('input').change(function() {
					var openedDays = $.map($('.checkavailable:checked'), function(n, i){
						return n.value;
					}).join(',');
					Cookies.set('advent_2020', openedDays, { expires: 3650, path: '/', domain: 'fansubs.cat' });
				});
<?php
if (!empty($_GET['currentday'])) {
?>
				$('.link').click(function () {
					var par=$(this).parent().parent().parent().parent();
					var tc = $(window).height() / 2 - $(par).height() / 2 - $(par).offset().top;
					var lc = $(window).width() / 2 - $(par).width() / 2 - $(par).offset().left;

					$(par).css({
						"z-index": "999",
						"transition": "1s linear",
						"transform": "translate("+lc+"px,"+tc+"px) scale(7)"
					});
				});
			});
<?php
}
?>
		</script>
		<style>
			html, body {
				min-height: 100%;
			}
			body {
				background-image: url(images/background.jpg);
				background-position: center center;
				background-repeat: no-repeat;
				background-color: #275e98;
				background-size: cover;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}

			/* title graphic */
			.title {
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.title img {
				width: 100%;
				height: auto;
			}

			/* mobile first grid layout */
			.grid-1 {
				display: grid;
				width: 96%;
				max-width: 900px;
				margin: 2% auto;

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
				@media only screen and (min-width: 600px) {

				.grid-1 {
				grid-template-columns: repeat(6, 1fr);
				grid-template-areas: "t     t     t     d2      d7      d8"
					"t     t     t     d4      d11     d12"
					"t     t     t     d19     d9      d13"
					"d6    d1    d24   d24     d21     d20"
					"d17   d18   d24   d24     d5      d22"
					"d3    d23   d16   d14     d10     d15";
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
				border: 2px solid transparent;
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
		<div class="grid-1">
			<div class="title">
				<img src="images/logo.png" alt="Bon Nadal!">
			</div>
<?php
for ($i=1;$i<25;$i++){
?>
			<div class="day-<?php echo $i; ?>">
				<label>
					<input type="checkbox"<?php echo is_day_ready($i) ? ' class="checkavailable"' : 'disabled'; ?> value="<?php echo $i; ?>"<?php echo (is_day_ready($i) && in_array($i,$cookie) && empty($_GET['currentday'])) ? ' checked' : ''; ?> />
					<span class="door<?php echo is_day_ready($i) ? ' dooravailable' : ''; ?>">
						<span class="front<?php echo is_day_ready($i) ? ' available' : ''; ?>"><?php echo $i; ?></span>
						<span class="back" id="<?php echo $i; ?>">
<?php
	if (is_day_ready($i)) {
?>
							<a class="link" href="<?php echo empty($_GET['currentday']) ? $releases[$i] : '#'; ?>"<?php echo empty($_GET['currentday']) ? ' target="_blank"' : ''; ?>></a>
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
	</body>
</html>
