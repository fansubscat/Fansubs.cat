<?php
$header_title="Estadístiques - Anàlisi";
$page="analytics";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	$max_days = 60;
	if (!empty($_SESSION['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_SESSION['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	} else if ($_SESSION['admin_level']>=3 && !empty($_GET['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_GET['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	}

	if (!empty($_GET['max_days']) && is_numeric($_GET['max_days'])) {
		$max_days = intval($_GET['max_days']);
	}
?>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Estadístiques <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Estadístiques totals</a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estadístiques totals</h4>
								<hr>
		<?php
			$result = query("SELECT (SELECT COUNT(*) FROM fansub) total_fansubs, (SELECT COUNT(*) FROM news) total_news, (SELECT COUNT(*) FROM comment) total_comments, (SELECT COUNT(DISTINCT user_id) FROM view_session WHERE view_counted IS NOT NULL) total_consuming_users, (SELECT COUNT(*) FROM user) total_users, (SELECT COUNT(*) FROM user_version_rating WHERE rating=1) total_positive_ratings, (SELECT COUNT(*) FROM user_version_rating WHERE rating=-1) total_negative_ratings, (SELECT COUNT(*) FROM series WHERE type='anime') total_anime, (SELECT COUNT(*) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime') total_anime_versions, (SELECT COUNT(*) FROM series WHERE type='manga') total_manga, (SELECT COUNT(*) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga') total_manga_versions, (SELECT COUNT(*) FROM series WHERE type='liveaction') total_liveaction, (SELECT COUNT(*) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction') total_liveaction_versions, (SELECT COUNT(*) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.is_lost=0 AND s.type='anime') total_anime_files, (SELECT COUNT(*) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.is_lost=0 AND s.type='manga') total_manga_files, (SELECT COUNT(*) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.is_lost=0 AND s.type='liveaction') total_liveaction_files, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_anime_duplicity, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_manga_duplicity, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_liveaction_duplicity, (SELECT IFNULL(SUM(clicks),0) FROM views WHERE type='anime') total_anime_clicks, (SELECT IFNULL(SUM(views),0) FROM views WHERE type='anime') total_anime_views, (SELECT IFNULL(SUM(total_length),0) FROM views WHERE type='anime') total_anime_time_spent, (SELECT IFNULL(SUM(clicks),0) FROM views WHERE type='manga') total_manga_clicks, (SELECT IFNULL(SUM(views),0) FROM views WHERE type='manga') total_manga_views, (SELECT IFNULL(SUM(total_length),0) FROM views WHERE type='manga') total_manga_pages_read, (SELECT IFNULL(SUM(clicks),0) FROM views WHERE type='liveaction') total_liveaction_clicks, (SELECT IFNULL(SUM(views),0) FROM views WHERE type='liveaction') total_liveaction_views, (SELECT IFNULL(SUM(total_length),0) FROM views WHERE type='liveaction') total_liveaction_time_spent, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND f.episode_id IS NOT NULL AND f.is_lost=0) total_linked_anime_episodes, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.episode_id IS NOT NULL AND f.is_lost=0) total_linked_manga_chapters, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND f.episode_id IS NOT NULL AND f.is_lost=0) total_linked_liveaction_episodes, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND f.is_lost=0) total_anime_duration, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.is_lost=0) total_manga_pages, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND f.is_lost=0) total_liveaction_duration");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Nombre d’elements:</h5></div>

									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Animes:</b><br><?php echo $totals['total_anime']; ?> <small>(duplicats: <?php echo $totals['total_anime_duplicity']; ?>)</small></div>
										<div class="col-sm-4 text-center"><b>Mangues:</b><br><?php echo $totals['total_manga']; ?> <small>(duplicats: <?php echo $totals['total_manga_duplicity']; ?>)</small></div>
										<div class="col-sm-4 text-center"><b>Contingut d’imatge real:</b><br><?php echo $totals['total_liveaction']; ?> <small>(duplicats: <?php echo $totals['total_liveaction_duplicity']; ?>)</small></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Versions d’anime:</b><br><?php echo $totals['total_anime_versions']; ?></div>
										<div class="col-sm-4 text-center"><b>Versions de manga:</b><br><?php echo $totals['total_manga_versions']; ?></div>
										<div class="col-sm-4 text-center"><b>Versions d’imatge real:</b><br><?php echo $totals['total_liveaction_versions']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Fansubs:</b><br><?php echo $totals['total_fansubs']; ?></div>
										<div class="col-sm-4 text-center"><b>Notícies:</b><br><?php echo $totals['total_news']; ?></div>
										<div class="col-sm-4 text-center"><b>Comentaris:</b><br><?php echo $totals['total_comments']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Usuaris visualitzadors:</b><br><?php echo $totals['total_consuming_users']; ?> de <?php echo $totals['total_users']; ?></div>
										<div class="col-sm-4 text-center"><b>Valoracions positives:</b><br><?php echo $totals['total_positive_ratings']; ?></div>
										<div class="col-sm-4 text-center"><b>Valoracions negatives:</b><br><?php echo $totals['total_negative_ratings']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Anime:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_anime_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_anime_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_anime_duration']); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo max(0, $totals['total_anime_clicks']); ?></div>
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_anime_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_anime_time_spent']); ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Manga:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_manga_chapters']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_manga_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals:</b><br><?php echo $totals['total_manga_pages']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo max(0, $totals['total_manga_clicks']); ?></div>
										<div class="col-sm-4 text-center"><b>Lectures:</b><br><?php echo $totals['total_manga_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals llegides:</b><br><?php echo $totals['total_manga_pages_read']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Imatge real:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_liveaction_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_liveaction_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_liveaction_duration']); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo max(0, $totals['total_liveaction_clicks']); ?></div>
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_liveaction_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_liveaction_time_spent']); ?></div>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució de l’anime</h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs_anime" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="anime-daily-tab" data-bs-toggle="tab" href="#anime-daily" role="tab" aria-controls="daily" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="anime-monthly-tab" data-bs-toggle="tab" href="#anime-monthly" role="tab" aria-controls="monthly" aria-selected="true">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="anime-daily" role="tabpanel" aria-labelledby="anime-daily-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v WHERE v.type='anime' AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="anime_daily_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('anime_daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="anime-monthly" role="tabpanel" aria-labelledby="anime-monthly-tab">
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v WHERE v.type='anime' GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$month_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($months as $month => $values) {
		array_push($month_values, "'".$month."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="anime_monthly_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('anime_monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució del manga</h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs_manga" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="manga-daily-tab" data-bs-toggle="tab" href="#manga-daily" role="tab" aria-controls="daily" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="manga-monthly-tab" data-bs-toggle="tab" href="#manga-monthly" role="tab" aria-controls="monthly" aria-selected="true">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="manga-daily" role="tabpanel" aria-labelledby="manga-daily-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_pages_read FROM views v WHERE v.type='manga' AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_pages_read']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$page_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($page_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="manga_daily_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('manga_daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Lectures',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														data: [<?php echo implode(',',$page_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="manga-monthly" role="tabpanel" aria-labelledby="manga-monthly-tab">
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_pages_read FROM views v WHERE v.type='manga' GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_pages_read']);
	}
	mysqli_free_result($result);

	$month_values=array();
	$click_values=array();
	$view_values=array();
	$page_values=array();

	foreach ($months as $month => $values) {
		array_push($month_values, "'".$month."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($page_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="manga_monthly_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('manga_monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Lectures',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														data: [<?php echo implode(',',$page_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució del contingut d’imatge real</h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs_liveaction" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="liveaction-daily-tab" data-bs-toggle="tab" href="#liveaction-daily" role="tab" aria-controls="daily" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="liveaction-monthly-tab" data-bs-toggle="tab" href="#liveaction-monthly" role="tab" aria-controls="monthly" aria-selected="true">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="liveaction-daily" role="tabpanel" aria-labelledby="liveaction-daily-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v WHERE v.type='liveaction' AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="liveaction_daily_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('liveaction_daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="liveaction-monthly" role="tabpanel" aria-labelledby="liveaction-monthly-tab">
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v WHERE v.type='liveaction' GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$month_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($months as $month => $values) {
		array_push($month_values, "'".$month."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="liveaction_monthly_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('liveaction_monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d’anime</h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions de manga</h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="manga_version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('manga_version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d’imatge real</h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="liveaction_version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('liveaction_version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers d’anime per fansub</h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT IF(COUNT(a.id)>=25,a.fansub_name,'Altres') fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_links_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers de manga per fansub</h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT IF(COUNT(a.id)>=25,a.fansub_name,'Altres') fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_files_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_files_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers d’imatge real per fansub</h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT IF(COUNT(a.id)>=2,a.fansub_name,'Altres') fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="liveaction_fansub_links_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('liveaction_fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d’anime (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session WHERE type='anime' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session WHERE type='anime' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session WHERE type='anime' AND source='cast' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="anime_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('anime_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les lectures de manga (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Tachiyomi'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session WHERE type='manga' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session WHERE type='manga' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session WHERE type='manga' AND source='api' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') api");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['api']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="manga_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('manga_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d’imatge real (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session WHERE type='liveaction' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session WHERE type='liveaction' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session WHERE type='liveaction' AND source='cast' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="liveaction_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('liveaction_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
				</div>
<?php
	if (!empty($fansub)) {
?>
				<div class="tab-pane fade show active" id="fansub" role="tabpanel" aria-labelledby="fansub-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estadístiques <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
		<?php
			$result = query("SELECT (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf WHERE fansub_id=".$fansub['id']." AND EXISTS (SELECT * FROM rel_version_fansub vf2 WHERE vf.version_id=vf2.version_id AND vf2.fansub_id<>".$fansub['id'].")) total_collabs, (SELECT COUNT(*) FROM news WHERE fansub_id=".$fansub['id'].") total_news, (SELECT COUNT(*) FROM comment c LEFT JOIN rel_version_fansub vf ON c.version_id=vf.version_id WHERE vf.fansub_id=".$fansub['id'].") total_comments, (SELECT COUNT(DISTINCT user_id) FROM view_session vs LEFT JOIN file f ON vs.file_id=f.id LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id WHERE view_counted IS NOT NULL AND vf.fansub_id=".$fansub['id'].") total_consuming_users, (SELECT COUNT(*) FROM user) total_users, (SELECT COUNT(*) FROM user_version_rating vr LEFT JOIN rel_version_fansub vf ON vr.version_id=vf.version_id WHERE rating=1 AND vf.fansub_id=".$fansub['id'].") total_positive_ratings, (SELECT COUNT(*) FROM user_version_rating vr LEFT JOIN rel_version_fansub vf ON vr.version_id=vf.version_id WHERE rating=-1 AND vf.fansub_id=".$fansub['id'].") total_negative_ratings, (SELECT COUNT(DISTINCT v.series_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND vf.fansub_id=".$fansub['id'].") total_anime, (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND fansub_id=".$fansub['id'].") total_anime_versions, (SELECT COUNT(DISTINCT v.series_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND vf.fansub_id=".$fansub['id'].") total_manga, (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND fansub_id=".$fansub['id'].") total_manga_versions, (SELECT COUNT(DISTINCT v.series_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND vf.fansub_id=".$fansub['id'].") total_liveaction, (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND fansub_id=".$fansub['id'].") total_liveaction_versions, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_anime_files, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_manga_files, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_liveaction_files, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND v.id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_anime_duplicity, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND v.id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_manga_duplicity, (SELECT COUNT(DISTINCT series_id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND v.id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_liveaction_duplicity, (SELECT IFNULL(SUM(clicks),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='anime' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_anime_clicks, (SELECT IFNULL(SUM(views),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='anime' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_anime_views, (SELECT IFNULL(SUM(total_length),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='anime' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_anime_time_spent, (SELECT IFNULL(SUM(clicks),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='manga' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_manga_clicks, (SELECT IFNULL(SUM(views),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='manga' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_manga_views, (SELECT IFNULL(SUM(total_length),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='manga' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_manga_pages_read, (SELECT IFNULL(SUM(clicks),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='liveaction' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_liveaction_clicks, (SELECT IFNULL(SUM(views),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='liveaction' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_liveaction_views, (SELECT IFNULL(SUM(total_length),0) FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='liveaction' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_liveaction_time_spent, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND f.episode_id IS NOT NULL AND f.is_lost=0 AND version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_anime_episodes, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.episode_id IS NOT NULL AND f.is_lost=0 AND version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_manga_chapters, (SELECT COUNT(DISTINCT episode_id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND f.episode_id IS NOT NULL AND f.is_lost=0 AND version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_liveaction_episodes, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_anime_duration, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_manga_pages, (SELECT IFNULL(SUM(f.length),0) FROM file f LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND f.is_lost=0 AND vf.fansub_id=".$fansub['id'].") total_liveaction_duration");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Nombre d’elements:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Animes:</b><br><?php echo $totals['total_anime']; ?> <small>(duplicats: <?php echo $totals['total_anime_duplicity']; ?>)</small></div>
										<div class="col-sm-4 text-center"><b>Mangues:</b><br><?php echo $totals['total_manga']; ?> <small>(duplicats: <?php echo $totals['total_manga_duplicity']; ?>)</small></div>
										<div class="col-sm-4 text-center"><b>Contingut d’imatge real:</b><br><?php echo $totals['total_liveaction']; ?> <small>(duplicats: <?php echo $totals['total_liveaction_duplicity']; ?>)</small></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Versions d’anime:</b><br><?php echo $totals['total_anime_versions']; ?></div>
										<div class="col-sm-4 text-center"><b>Versions de manga:</b><br><?php echo $totals['total_manga_versions']; ?></div>
										<div class="col-sm-4 text-center"><b>Versions d’imatge real:</b><br><?php echo $totals['total_liveaction_versions']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Col·laboracions:</b><br><?php echo $totals['total_collabs']; ?></div>
										<div class="col-sm-4 text-center"><b>Notícies:</b><br><?php echo $totals['total_news']; ?></div>
										<div class="col-sm-4 text-center"><b>Comentaris:</b><br><?php echo $totals['total_comments']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Usuaris visualitzadors:</b><br><?php echo $totals['total_consuming_users']; ?> de <?php echo $totals['total_users']; ?></div>
										<div class="col-sm-4 text-center"><b>Valoracions positives:</b><br><?php echo $totals['total_positive_ratings']; ?></div>
										<div class="col-sm-4 text-center"><b>Valoracions negatives:</b><br><?php echo $totals['total_negative_ratings']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Anime:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_anime_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_anime_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_anime_duration']); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo $totals['total_anime_clicks']; ?></div>
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_anime_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_anime_time_spent']); ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Manga:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_manga_chapters']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_manga_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals:</b><br><?php echo $totals['total_manga_pages']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo $totals['total_manga_clicks']; ?></div>
										<div class="col-sm-4 text-center"><b>Lectures:</b><br><?php echo $totals['total_manga_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals llegides:</b><br><?php echo $totals['total_manga_pages_read']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Imatge real:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols editats:</b><br><?php echo $totals['total_linked_liveaction_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_liveaction_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_liveaction_duration']); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Clics:</b><br><?php echo $totals['total_liveaction_clicks']; ?></div>
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_liveaction_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_liveaction_time_spent']); ?></div>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució de l’anime <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>

								<ul class="nav nav-tabs" id="anime_chart_tabs_fansub" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="anime-daily_fansub-tab" data-bs-toggle="tab" href="#anime-daily_fansub" role="tab" aria-controls="anime-daily_fansub" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="anime-monthly_fansub-tab" data-bs-toggle="tab" href="#anime-monthly_fansub" role="tab" aria-controls="anime-monthly_fansub" aria-selected="false">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="anime-daily_fansub" role="tabpanel" aria-labelledby="anime-daily_fansub-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='anime' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="anime_daily_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('anime_daily_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="anime-monthly_fansub" role="tabpanel" aria-labelledby="anime-monthly_fansub-tab">
<?php
		$months = array();

		$current_month = strtotime(date('Y-m-01'));
		$i=0;
		while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
			$i++;
		}

		$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='anime' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
		while ($row = mysqli_fetch_assoc($result)) {
			$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
		}
		mysqli_free_result($result);

		$month_values=array();
		$click_values=array();
		$view_values=array();
		$time_values=array();

		foreach ($months as $month => $values) {
			array_push($month_values, "'".$month."'");
			array_push($click_values, $values[0]);
			array_push($view_values, $values[1]);
			array_push($time_values, $values[2]);
		}
?>
										<div class="graph-container"><canvas id="anime_monthly_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('anime_monthly_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució del manga <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs_fansub_manga" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="manga_daily_fansub-tab" data-bs-toggle="tab" href="#manga_daily_fansub" role="tab" aria-controls="daily_fansub" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="manga_monthly_fansub-tab" data-bs-toggle="tab" href="#manga_monthly_fansub" role="tab" aria-controls="manga_monthly_fansub" aria-selected="false">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="manga_daily_fansub" role="tabpanel" aria-labelledby="manga_daily_fansub-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_pages_read FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='manga' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_pages_read']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$page_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($page_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="manga_daily_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('manga_daily_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Lectures',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														data: [<?php echo implode(',',$page_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="manga_monthly_fansub" role="tabpanel" aria-labelledby="manga_monthly_fansub-tab">
<?php
		$months = array();

		$current_month = strtotime(date('Y-m-01'));
		$i=0;
		while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
			$i++;
		}

		$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_pages_read FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='manga' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
		while ($row = mysqli_fetch_assoc($result)) {
			$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_pages_read']);
		}
		mysqli_free_result($result);

		$month_values=array();
		$click_values=array();
		$view_values=array();
		$page_values=array();

		foreach ($months as $month => $values) {
			array_push($month_values, "'".$month."'");
			array_push($click_values, $values[0]);
			array_push($view_values, $values[1]);
			array_push($page_values, $values[2]);
		}
?>
										<div class="graph-container"><canvas id="manga_monthly_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('manga_monthly_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Lectures',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														data: [<?php echo implode(',',$page_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució del contingut d’imatge real <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>

								<ul class="nav nav-tabs" id="liveaction_chart_tabs_fansub" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="liveaction-daily_fansub-tab" data-bs-toggle="tab" href="#liveaction-daily_fansub" role="tab" aria-controls="liveaction-daily_fansub" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="liveaction-monthly_fansub-tab" data-bs-toggle="tab" href="#liveaction-monthly_fansub" role="tab" aria-controls="liveaction-monthly_fansub" aria-selected="false">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="liveaction-daily_fansub" role="tabpanel" aria-labelledby="liveaction-daily_fansub-tab">
<?php
	$days = array();

	$i=$max_days;
	while ($i>=0) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='liveaction' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
	}
	mysqli_free_result($result);

	$day_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($days as $day => $values) {
		array_push($day_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="liveaction_daily_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('liveaction_daily_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="liveaction-monthly_fansub" role="tabpanel" aria-labelledby="liveaction-monthly_fansub-tab">
<?php
		$months = array();

		$current_month = strtotime(date('Y-m-01'));
		$i=0;
		while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=array(0, 0, 0);
			$i++;
		}

		$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/3600 total_time_spent FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE v.type='liveaction' AND f.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
		while ($row = mysqli_fetch_assoc($result)) {
			$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent']);
		}
		mysqli_free_result($result);

		$month_values=array();
		$click_values=array();
		$view_values=array();
		$time_values=array();

		foreach ($months as $month => $values) {
			array_push($month_values, "'".$month."'");
			array_push($click_values, $values[0]);
			array_push($view_values, $values[1]);
			array_push($time_values, $values[2]);
		}
?>
										<div class="graph-container"><canvas id="liveaction_monthly_chart_fansub"></canvas></div>
										<script>
											var ctx = document.getElementById('liveaction_monthly_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Clics',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.4
													},
													{
														label: 'Visualitzacions',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.4
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.4
													}]
												},
												options: {
													responsive: true,
													maintainAspectRatio: false,
													plugins: {
														legend: {
															position: 'bottom'
														}
													},
													scales: {
														y: {
															beginAtZero: true
														}
													}
												}
											});
										</script>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d’anime <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions de manga <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_manga_version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_manga_version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d’imatge real <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY v.status ORDER BY status ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['status']) {
			case 5:
				array_push($status_values, "'Cancel·lada'");
				array_push($status_colors, "'red'");
				break;
			case 4:
				array_push($status_values, "'Abandonada'");
				array_push($status_colors, "'coral'");
				break;
			case 3:
				array_push($status_values, "'Parcialment completada'");
				array_push($status_colors, "'greenyellow'");
				break;
			case 2:
				array_push($status_values, "'En procés'");
				array_push($status_colors, "'yellow'");
				break;
			default:
				array_push($status_values, "'Completada'");
				array_push($status_colors, "'green'");
				break;
		}
		array_push($status_count_values, $row['version_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_liveaction_version_status_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_liveaction_version_status_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$status_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$status_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$status_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers d’anime amb participació <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT a.fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND fi.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_fansub_links_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers de manga amb participació <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT a.fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND fi.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_fansub_files_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_fansub_files_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Nombre de fitxers d’imatge real amb participació <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$file_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT a.fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_version_fansub vf ON fi.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v ON fi.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND fi.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND fi.is_lost=0 GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_liveactiobn_fansub_links_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_liveactiobn_fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$file_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d’anime <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='anime' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='anime' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='anime' AND source='cast' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_anime_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_anime_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les lectures de manga <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Tachiyomi'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='manga' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='manga' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='manga' AND source='api' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') api");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['api']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_manga_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_manga_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d’imatge real <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='liveaction' AND source='desktop' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='liveaction' AND source='mobile' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_session vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN version v ON f.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND type='liveaction' AND source='cast' AND DATE_FORMAT(view_counted,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<div class="graph-container"><canvas id="fansub_liveaction_origin_chart"></canvas></div>
								<script>
									var ctx = document.getElementById('fansub_liveaction_origin_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$origin_labels); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$origin_values); ?>],
												backgroundColor: [<?php echo implode(',',$origin_colors); ?>]
											}]
										},
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {
												legend: {
													position: 'right'
												}
											}
										}
									});
								</script>
							</article>
						</div>
					</div>
				</div>
<?php
	}
?>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
