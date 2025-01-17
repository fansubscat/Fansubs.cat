<?php
ob_start();
require_once(__DIR__.'/../common/db.inc.php');

function seededShuffle(array &$array, $seed) {
	mt_srand($seed);
	$size = count($array);
	for ($i = 0; $i < $size; ++$i) {
		list($chunk) = array_splice($array, mt_rand(0, $size-1), 1);
		array_push($array, $chunk);
	}
}

if (!empty($_GET['year']) && is_numeric($_GET['year'])) {
	$result = query("SELECT * FROM advent_calendar ac WHERE year=".escape($_GET['year']));
	$row = mysqli_fetch_assoc($result) or $failed=TRUE;
	mysqli_free_result($result);
	if (!empty($failed)) {
		http_response_code(404);
		die();
	}
} else {
	$result = query("SELECT * FROM advent_calendar ac ORDER BY year DESC LIMIT 1");
	$row = mysqli_fetch_assoc($result) or $failed=TRUE;
	mysqli_free_result($result);
	if (!empty($failed)) {
		http_response_code(404);
		die();
	}
}
	
$days = array();
$resultd = query("SELECT * FROM advent_day WHERE year=".escape($row['year'])." ORDER BY day ASC");
while ($rowd = mysqli_fetch_assoc($resultd)) {
	$days[$rowd['day']]=$rowd;
}
mysqli_free_result($resultd);

function is_day_ready($day) {
	global $days, $row;
	$today = date('Y-m-d H:i:s');
	if (!empty($_GET['twitter']) && !empty($_GET['currentday'])) {
		$today = $row['year'].'-12-'.sprintf('%02d', intval($_GET['currentday'])).' 12:00:00';
	}
	$target = $row['year'].'-12-'.sprintf('%02d', $day).' 12:00:00';
	return (strcmp($today,$target)>=0 && (!empty($days[$day]['link_url']) || !empty($_GET['twitter'])));
}

if (!empty($_COOKIE['advent_'.$row['year']])) {
	$cookie=explode(',',$_COOKIE['advent_'.$row['year']]);
} else {
	$cookie=array();
}

define('PAGE_TITLE', sprintf(lang('advent.page_title'), $row['year']));

?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="theme-color" content="#6aa0f8">
		<meta name="referrer" content="origin">
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-config" content="<?php echo STATIC_URL; ?>/favicons/advent/browserconfig.xml">
		<meta property="og:title" content="<?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?>" />
		<meta property="og:url" content="<?php echo ADVENT_URL; ?>/" />
		<meta property="og:description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION); ?>" />
		<meta property="og:image" content="<?php echo STATIC_URL; ?>/images/advent/preview_<?php echo $row['year']; ?>.jpg" />
		<meta property="og:image:type" content="image/jpeg">
		<title><?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?></title>
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon-16x16.png">
		<link rel="manifest" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/site.webmanifest">
		<link rel="mask-icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/safari-pinned-tab.svg" color="#6aa0f8">
		<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon.ico">
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/common.css?v=<?php echo VERSION; ?>">
		<link href="https://fonts.googleapis.com/css?family=Kalam" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
		<script>
			$(document).ready(function() {
				$('input').change(function() {
					var openedDays = $.map($('.checkavailable:checked'), function(n, i){
						return n.value;
					}).join(',');
					Cookies.set('advent_<?php echo $row['year']; ?>', openedDays, { expires: 3650, path: '/', domain: '<?php echo COOKIE_DOMAIN; ?>' });
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

switch ($row['year']) {
	case 2020:
		$grid_desktop = array(9,23,15,8,18,11,16,12,17,3,14,21,6,5,10,4,20,19,7,2,13,1,22);
		$grid_mobile = array(23,20,12,2,14,4,5,22,16,1,7,9,10,11,18,13,3,15,6,17,8,19,24,21);
		break;
	case 2021:
		$grid_desktop = array(9,23,15,8,18,11,16,12,17,3,14,21,6,5,10,4,20,19,7,2,13,1,22);
		$grid_mobile = array(23,20,12,2,14,4,5,22,16,1,7,9,10,11,18,13,3,15,6,17,8,19,24,21);
		break;
	default:
		//Desktop has one less because 24 is in a fixed position
		$grid_desktop = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
		seededShuffle($grid_desktop, $row['year']);
		$grid_mobile = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);
		seededShuffle($grid_mobile, $row['year']+10000);
		
}
?>
				});
<?php
if (!empty($_GET['twitter'])) {
?>
				setTimeout(function() {
					$('.day-<?php echo $_GET['currentday']; ?> .dooravailable').addClass('forcehover');
					setTimeout(function() {
						$('.day-<?php echo $_GET['currentday']; ?> input').click();
						setTimeout(function() {
							$('.day-<?php echo $_GET['currentday']; ?> .description').addClass('makevisible').css('opacity', '0').animate({opacity: 1}, 1000);
						}, 2000);
					}, 1000);
				}, 1000);
<?php
}
?>
			});
		</script>
		<style>
			html, body {
				min-height: 100vh;
			}
			body {
				background-image: url("<?php echo STATIC_URL; ?>/images/advent/background_<?php echo $row['year']; ?>.jpg");
<?php
	if ($row['year']=='2022') {
?>
				background-position: 88% 50%;
<?php
	} else {
?>
				background-position: center center;
<?php
	}
?>
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
				grid-gap: 1em;

				grid-template-areas:    "t        t       t"
					"d<?php echo $grid_mobile[0]; ?>      d<?php echo $grid_mobile[1]; ?>     d<?php echo $grid_mobile[2]; ?>"
					"d<?php echo $grid_mobile[3]; ?>       d<?php echo $grid_mobile[4]; ?>     d<?php echo $grid_mobile[5]; ?>"
					"d<?php echo $grid_mobile[6]; ?>       d<?php echo $grid_mobile[7]; ?>     d<?php echo $grid_mobile[8]; ?>"
					"d<?php echo $grid_mobile[9]; ?>       d<?php echo $grid_mobile[10]; ?>      d<?php echo $grid_mobile[11]; ?>"
					"d<?php echo $grid_mobile[12]; ?>      d<?php echo $grid_mobile[13]; ?>     d<?php echo $grid_mobile[14]; ?>"
					"d<?php echo $grid_mobile[15]; ?>      d<?php echo $grid_mobile[16]; ?>      d<?php echo $grid_mobile[17]; ?>"
					"d<?php echo $grid_mobile[18]; ?>       d<?php echo $grid_mobile[19]; ?>     d<?php echo $grid_mobile[20]; ?>"
					"d<?php echo $grid_mobile[21]; ?>      d<?php echo $grid_mobile[22]; ?>     d<?php echo $grid_mobile[23]; ?>";
			}

			/* media query */
			@media only screen and (min-width: 720px) {
<?php
	if ($row['year']==2021) {
?>
				body {
					background-size: unset;
				}
<?php
	} else if ($row['year']==2022) {
?>
				body {
					background-position: center center;
				}
<?php
	}
?>
				.grid-1 {
					grid-template-columns: repeat(6, 1fr);
<?php
	if ($row['position']=='right') {
?>
					grid-template-areas: "d<?php echo $grid_desktop[0]; ?>      d<?php echo $grid_desktop[1]; ?>      d<?php echo $grid_desktop[2]; ?>     t     t     t"
						"d<?php echo $grid_desktop[3]; ?>      d<?php echo $grid_desktop[4]; ?>     d<?php echo $grid_desktop[5]; ?>     t     t     t"
						"d<?php echo $grid_desktop[6]; ?>     d<?php echo $grid_desktop[7]; ?>      d<?php echo $grid_desktop[8]; ?>     t     t     t"
						"d<?php echo $grid_desktop[9]; ?>    d<?php echo $grid_desktop[10]; ?>    d24   d24     d<?php echo $grid_desktop[11]; ?>     d<?php echo $grid_desktop[12]; ?>"
						"d<?php echo $grid_desktop[13]; ?>   d<?php echo $grid_desktop[14]; ?>   d24   d24     d<?php echo $grid_desktop[15]; ?>      d<?php echo $grid_desktop[16]; ?>"
						"d<?php echo $grid_desktop[17]; ?>    d<?php echo $grid_desktop[18]; ?>   d<?php echo $grid_desktop[19]; ?>   d<?php echo $grid_desktop[20]; ?>     d<?php echo $grid_desktop[21]; ?>     d<?php echo $grid_desktop[22]; ?>";
<?php
	} else {
?>
					grid-template-areas: "t     t     t     d<?php echo $grid_desktop[0]; ?>      d<?php echo $grid_desktop[1]; ?>      d<?php echo $grid_desktop[2]; ?>"
						"t     t     t     d<?php echo $grid_desktop[3]; ?>      d<?php echo $grid_desktop[4]; ?>     d<?php echo $grid_desktop[5]; ?>"
						"t     t     t     d<?php echo $grid_desktop[6]; ?>     d<?php echo $grid_desktop[7]; ?>      d<?php echo $grid_desktop[8]; ?>"
						"d<?php echo $grid_desktop[9]; ?>    d<?php echo $grid_desktop[10]; ?>    d24   d24     d<?php echo $grid_desktop[11]; ?>     d<?php echo $grid_desktop[12]; ?>"
						"d<?php echo $grid_desktop[13]; ?>   d<?php echo $grid_desktop[14]; ?>   d24   d24     d<?php echo $grid_desktop[15]; ?>      d<?php echo $grid_desktop[16]; ?>"
						"d<?php echo $grid_desktop[17]; ?>    d<?php echo $grid_desktop[18]; ?>   d<?php echo $grid_desktop[19]; ?>   d<?php echo $grid_desktop[20]; ?>     d<?php echo $grid_desktop[21]; ?>     d<?php echo $grid_desktop[22]; ?>";
<?php
	}
?>
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
				display: block;
				width: 100%;
				height: 100%;
				transform-style: preserve-3d;
				transition: all 300ms;
				border: 0.3rem solid transparent;
				border-radius: 1rem;
			}

			.door > span {
				position: absolute;
				height: 100%;
				width: 100%;
				backface-visibility: hidden;
				border-radius: 0.7rem;
				display: flex;
				align-items: center;
				justify-content: center;
				font-family: 'Kalam', cursive;
				color: white;
				font-size: 4rem;
				font-weight: bold;
				text-shadow: 0 0 0.2rem rgba(0, 0, 0, 1);
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
				flex-direction: column;
				overflow: hidden;
			}

			label .door {
				border-color: rgba(0,0,0,0.2);
			}

			label:hover .door {
				border-color: rgba(255,255,255,0.5);
			}

			label:hover .door.dooravailable, label .door.dooravailable.forcehover {
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

			.description {
				display: none;
				background: rgb(var(--primary-color));
				font-family: lexend_deca;
				text-shadow: none;
				font-size: 4rem;
				width: 100%;
				padding: 2rem 1rem;
				align-items: center;
				justify-content: center;
				text-align: center;
				flex-direction: column;
			}

			.description.makevisible {
				display: flex;
			}

			.description .fansub {
				font-size: 2.5rem;
			}

			.previous{
				text-align: center;
				color: white;
				font-size: 2rem;
				font-weight: bold;
				text-shadow: 0 0 0.4rem black;
				margin: 0.8rem 0;
			}

			.previous a{
				color: white;
				text-decoration: underline;
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
				background-image: url("<?php echo is_day_ready($i) ? STATIC_URL.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg' : STATIC_URL.'/images/advent/empty.png'; ?>");
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
					<img src="<?php echo STATIC_URL; ?>/images/advent/logo.png" alt="<?php echo lang('advent.logo.alt'); ?>">
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
								<a class="link" href="<?php echo empty($_GET['twitter']) ? $days[$i]['link_url'] : '#'; ?>"<?php echo empty($_GET['twitter']) ? ' target="_blank"' : ''; ?>></a>
<?php
		if (!empty($_GET['twitter'])) {
?>
								<div class="description"><?php echo explode('|',$days[$i]['description'])[1]; ?><br><span class="fansub"><?php echo explode('|',$days[$i]['description'])[0]; ?></span></div>
<?php
		}
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
				<?php echo lang('advent.other_editions'); ?> 
<?php
	$resulto = query("SELECT * FROM advent_calendar WHERE year<>".escape($row['year'])." ORDER BY year DESC");
	while ($rowo = mysqli_fetch_assoc($resulto)) {
		echo ' <a href="/'.$rowo['year'].'/">'.$rowo['year'].'</a>';
	}
	mysqli_free_result($resulto);
?>
			</div>
<?php
}
?>
		</div>
	</body>
</html>
<?php
ob_flush();
mysqli_close($db_connection);
?>
