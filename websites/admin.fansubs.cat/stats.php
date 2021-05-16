<?php
$header_title="Estadístiques - Anàlisi";
$page="analytics";
include("header.inc.php");
require_once("common.inc.php");

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
					<a class="nav-link active" id="fansub-tab" data-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Estadístiques <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Estadístiques totals</a>
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
			$result = query("SELECT (SELECT COUNT(*) FROM fansub) total_fansubs, (SELECT COUNT(*) FROM news) total_news, (SELECT COUNT(*) FROM series) total_series, (SELECT COUNT(*) FROM version) total_versions, (SELECT COUNT(*) FROM manga) total_manga, (SELECT COUNT(*) FROM manga_version) total_manga_versions, (SELECT COUNT(*) FROM link WHERE lost=0) total_links, (SELECT COUNT(*) FROM file WHERE original_filename IS NOT NULL) total_files, (SELECT COUNT(DISTINCT series_id) FROM version v WHERE EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_duplicity, (SELECT COUNT(DISTINCT manga_id) FROM manga_version v WHERE EXISTS (SELECT * FROM manga_version v2 WHERE v2.id<>v.id AND v2.manga_id=v.manga_id)) total_manga_duplicity, (SELECT IFNULL(SUM(clicks),0) FROM views) total_clicks, (SELECT IFNULL(SUM(views),0) FROM views) total_views, (SELECT IFNULL(SUM(time_spent),0) FROM views) total_time_spent, (SELECT IFNULL(SUM(clicks),0) FROM manga_views) total_manga_clicks, (SELECT IFNULL(SUM(views),0) FROM manga_views) total_reads, (SELECT IFNULL(SUM(pages_read),0) FROM manga_views) total_pages_read, (SELECT COUNT(DISTINCT episode_id) FROM link WHERE episode_id IS NOT NULL AND lost=0) total_linked_episodes, (SELECT COUNT(DISTINCT chapter_id) FROM file WHERE chapter_id IS NOT NULL AND original_filename IS NOT NULL) total_linked_chapters, (SELECT SUM(e.duration) FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE lost=0) total_duration, (SELECT SUM(f.number_of_pages) FROM file f WHERE f.original_filename IS NOT NULL) total_number_of_pages");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Nombre d'elements:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-2 text-center"><b>Animes:</b><br><?php echo $totals['total_series']; ?> <small>(duplicats: <?php echo $totals['total_duplicity']; ?>)</small></div>
										<div class="col-sm-2 text-center"><b>Versions d'anime:</b><br><?php echo $totals['total_versions']; ?></div>
										<div class="col-sm-2 text-center"><b>Mangues:</b><br><?php echo $totals['total_manga']; ?> <small>(duplicats: <?php echo $totals['total_manga_duplicity']; ?>)</small></div>
										<div class="col-sm-2 text-center"><b>Versions de manga:</b><br><?php echo $totals['total_manga_versions']; ?></div>
										<div class="col-sm-2 text-center"><b>Fansubs:</b><br><?php echo $totals['total_fansubs']; ?></div>
										<div class="col-sm-2 text-center"><b>Notícies:</b><br><?php echo $totals['total_news']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Anime:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols amb enllaç:</b><br><?php echo $totals['total_linked_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Enllaços totals:</b><br><?php echo $totals['total_links']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_duration']*60); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Clics sense visualitzar:</b><br><?php echo max(0, $totals['total_clicks']-$totals['total_views']); ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_time_spent']); ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Manga:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols amb fitxer:</b><br><?php echo $totals['total_linked_chapters']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals:</b><br><?php echo $totals['total_number_of_pages']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Lectures:</b><br><?php echo $totals['total_reads']; ?></div>
										<div class="col-sm-4 text-center"><b>Clics sense llegir:</b><br><?php echo max(0, $totals['total_manga_clicks']-$totals['total_reads']); ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals llegides:</b><br><?php echo $totals['total_pages_read']; ?></div>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució de l'anime</h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="true">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
<?php
	$days = array();

	$current_day = strtotime(date('Y-m-d'));
	$i=$max_days;
	while (strtotime(date('Y-m-d')."-$i days")<=$current_day) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent FROM views v WHERE DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
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
										<canvas id="daily_chart"></canvas>
										<script>
											var ctx = document.getElementById('daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Visualitzacions reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense visualitzar',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$time_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent FROM views v GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
										<canvas id="monthly_chart"></canvas>
										<script>
											var ctx = document.getElementById('monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Visualitzacions reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense visualitzar',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$time_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
										<a class="nav-link active" id="manga-daily-tab" data-toggle="tab" href="#manga-daily" role="tab" aria-controls="daily" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="manga-monthly-tab" data-toggle="tab" href="#manga-monthly" role="tab" aria-controls="monthly" aria-selected="true">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="manga-daily" role="tabpanel" aria-labelledby="manga-daily-tab">
<?php
	$days = array();

	$current_day = strtotime(date('Y-m-d'));
	$i=$max_days;
	while (strtotime(date('Y-m-d')."-$i days")<=$current_day) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v WHERE DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
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
										<canvas id="manga_daily_chart"></canvas>
										<script>
											var ctx = document.getElementById('manga_daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Lectures reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense llegir',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$page_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
	while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
										<canvas id="manga_monthly_chart"></canvas>
										<script>
											var ctx = document.getElementById('manga_monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Lectures reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense llegir',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$page_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
								<h4 class="card-title text-center mb-4 mt-1">Els 10 animes més vistos (darrers 14 dies / sempre)</h4>
								<hr>
								<div class="row">
									<div class="w-50 pr-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Anime</th>
													<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
	$result = query("SELECT a.series_id, a.series_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id, s.name series_name FROM link l LEFT JOIN views vi ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' AND l.episode_id IS NOT NULL GROUP BY l.version_id, l.episode_id) a GROUP BY a.series_id ORDER BY max_views DESC, a.series_name ASC LIMIT 10");
	while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['series_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
	}
	mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
									<div class="w-50 pl-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Anime</th>
													<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
	$result = query("SELECT a.series_id, a.series_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id, s.name series_name FROM link l LEFT JOIN views vi ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE l.episode_id IS NOT NULL GROUP BY l.version_id, l.episode_id) a GROUP BY a.series_id ORDER BY max_views DESC, a.series_name ASC LIMIT 10");
	while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['series_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
	}
	mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Els 10 mangues més llegits (darrers 14 dies / sempre)</h4>
								<hr>
								<div class="row">
									<div class="w-50 pr-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Manga</th>
													<th class="text-center" scope="col" style="width: 28%;">Lectures<br /><small>(capítol més llegit)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
	$result = query("SELECT a.manga_id, a.manga_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.id manga_id, m.name manga_name FROM file fi LEFT JOIN manga_views vi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id WHERE vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' AND fi.chapter_id IS NOT NULL GROUP BY fi.manga_version_id, fi.chapter_id) a GROUP BY a.manga_id ORDER BY max_views DESC, a.manga_name ASC LIMIT 10");
	while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['manga_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
	}
	mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
									<div class="w-50 pl-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Manga</th>
													<th class="text-center" scope="col" style="width: 28%;">Lectures<br /><small>(capítol més llegit)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
	$result = query("SELECT a.manga_id, a.manga_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.id manga_id, m.name manga_name FROM file fi LEFT JOIN manga_views vi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id WHERE fi.chapter_id IS NOT NULL GROUP BY fi.manga_version_id, fi.chapter_id) a GROUP BY a.manga_id ORDER BY max_views DESC, a.manga_name ASC LIMIT 10");
	while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['manga_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
	}
	mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d'anime</h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v GROUP BY v.status ORDER BY status ASC");
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
								<canvas id="version_status_chart"></canvas>
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
											legend: {
												position: 'right'
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
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM manga_version v GROUP BY v.status ORDER BY status ASC");
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
								<canvas id="manga_version_status_chart"></canvas>
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
											legend: {
												position: 'right'
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
								<h4 class="card-title text-center mb-4 mt-1">Nombre d'enllaços d'anime per fansub</h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$link_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.link_count) link_count FROM (SELECT IF(COUNT(a.id)>20,a.fansub_name,'Altres') fansub_name, COUNT(a.id) link_count FROM (SELECT l.id, l.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE l.lost=0 GROUP BY l.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, link_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($link_count_values, $row['link_count']);
	}
	mysqli_free_result($result);
?>
								<canvas id="fansub_links_chart"></canvas>
								<script>
									var ctx = document.getElementById('fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$link_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											legend: {
												position: 'right'
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
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT IF(COUNT(a.id)>20,a.fansub_name,'Altres') fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.manga_version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_manga_version_fansub vf ON fi.manga_version_id = vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE fi.original_filename IS NOT NULL GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<canvas id="fansub_files_chart"></canvas>
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
											legend: {
												position: 'right'
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
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d'anime (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_log WHERE view_type='desktop' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_log WHERE view_type='mobile' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_log WHERE view_type='cast' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<canvas id="anime_origin_chart"></canvas>
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
											legend: {
												position: 'right'
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
	$result = query("SELECT (SELECT COUNT(*) FROM manga_view_log WHERE read_type='desktop' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM manga_view_log WHERE read_type='mobile' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM manga_view_log WHERE read_type='api' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') api");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['api']);
	mysqli_free_result($result);
?>
								<canvas id="manga_origin_chart"></canvas>
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
											legend: {
												position: 'right'
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
			$result = query("SELECT (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf WHERE fansub_id=".$fansub['id']." AND EXISTS (SELECT * FROM rel_version_fansub vf2 WHERE vf.version_id=vf2.version_id AND vf2.fansub_id<>".$fansub['id']."))+(SELECT COUNT(DISTINCT vf.manga_version_id) FROM rel_manga_version_fansub vf WHERE fansub_id=".$fansub['id']." AND EXISTS (SELECT * FROM rel_manga_version_fansub vf2 WHERE vf.manga_version_id=vf2.manga_version_id AND vf2.fansub_id<>".$fansub['id'].")) total_collabs, (SELECT COUNT(*) FROM news WHERE fansub_id=".$fansub['id'].") total_news, (SELECT COUNT(DISTINCT v.series_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id WHERE vf.fansub_id=".$fansub['id'].") total_series, (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf WHERE fansub_id=".$fansub['id'].") total_versions, (SELECT COUNT(DISTINCT v.manga_id) FROM rel_manga_version_fansub vf LEFT JOIN manga_version v ON vf.manga_version_id=v.id WHERE vf.fansub_id=".$fansub['id'].") total_manga, (SELECT COUNT(DISTINCT vf.manga_version_id) FROM rel_manga_version_fansub vf WHERE fansub_id=".$fansub['id'].") total_manga_versions, (SELECT COUNT(DISTINCT l.id) FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id=vf.version_id WHERE l.lost=0 AND vf.fansub_id=".$fansub['id'].") total_links, (SELECT COUNT(*) FROM file f LEFT JOIN rel_manga_version_fansub vf ON f.manga_version_id=vf.manga_version_id WHERE original_filename IS NOT NULL AND vf.fansub_id=".$fansub['id'].") total_files, (SELECT COUNT(DISTINCT series_id) FROM version v WHERE v.id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_duplicity, (SELECT COUNT(DISTINCT manga_id) FROM manga_version v WHERE v.id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM manga_version v2 WHERE v2.id<>v.id AND v2.manga_id=v.manga_id)) total_manga_duplicity, (SELECT IFNULL(SUM(clicks),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_clicks, (SELECT IFNULL(SUM(views),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_views, (SELECT IFNULL(SUM(time_spent),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_time_spent, (SELECT IFNULL(SUM(clicks),0) FROM manga_views v LEFT JOIN file f ON v.file_id=f.id WHERE f.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].")) total_manga_clicks, (SELECT IFNULL(SUM(views),0) FROM manga_views v LEFT JOIN file f ON v.file_id=f.id WHERE f.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].")) total_reads, (SELECT IFNULL(SUM(pages_read),0) FROM manga_views v LEFT JOIN file f ON v.file_id=f.id WHERE f.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].")) total_pages_read, (SELECT COUNT(DISTINCT episode_id) FROM link WHERE episode_id IS NOT NULL AND lost=0 AND version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_episodes, (SELECT COUNT(DISTINCT chapter_id) FROM file WHERE chapter_id IS NOT NULL AND original_filename IS NOT NULL AND manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_chapters, (SELECT SUM(e.duration) FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id=vf.version_id LEFT JOIN episode e ON l.episode_id=e.id WHERE l.lost=0 AND vf.fansub_id=".$fansub['id'].") total_duration, (SELECT SUM(f.number_of_pages) FROM file f LEFT JOIN rel_manga_version_fansub vf ON f.manga_version_id=vf.manga_version_id WHERE f.original_filename IS NOT NULL AND vf.fansub_id=".$fansub['id'].") total_number_of_pages");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Nombre d'elements:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-2 text-center"><b>Animes:</b><br><?php echo $totals['total_series']; ?> <small>(duplicats: <?php echo $totals['total_duplicity']; ?>)</small></div>
										<div class="col-sm-2 text-center"><b>Versions d'anime:</b><br><?php echo $totals['total_versions']; ?></div>
										<div class="col-sm-2 text-center"><b>Mangues:</b><br><?php echo $totals['total_manga']; ?> <small>(duplicats: <?php echo $totals['total_manga_duplicity']; ?>)</small></div>
										<div class="col-sm-2 text-center"><b>Versions de manga:</b><br><?php echo $totals['total_manga_versions']; ?></div>
										<div class="col-sm-2 text-center"><b>Col·laboracions:</b><br><?php echo $totals['total_collabs']; ?></div>
										<div class="col-sm-2 text-center"><b>Notícies:</b><br><?php echo $totals['total_news']; ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Anime:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols amb enllaç:</b><br><?php echo $totals['total_linked_episodes']; ?></div>
										<div class="col-sm-4 text-center"><b>Enllaços totals:</b><br><?php echo $totals['total_links']; ?></div>
										<div class="col-sm-4 text-center"><b>Durada total:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_duration']*60); ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Visualitzacions:</b><br><?php echo $totals['total_views']; ?></div>
										<div class="col-sm-4 text-center"><b>Clics sense visualitzar:</b><br><?php echo max(0, $totals['total_clicks']-$totals['total_views']); ?></div>
										<div class="col-sm-4 text-center"><b>Temps total visualitzat:</b><br><?php echo get_hours_or_minutes_formatted($totals['total_time_spent']); ?></div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-sm text-center pb-1"><h5>Manga:</h5></div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Capítols amb fitxer:</b><br><?php echo $totals['total_linked_chapters']; ?></div>
										<div class="col-sm-4 text-center"><b>Fitxers totals:</b><br><?php echo $totals['total_files']; ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals:</b><br><?php echo $totals['total_number_of_pages']; ?></div>
									</div>
									<div class="w-100 d-flex">
										<div class="col-sm-4 text-center"><b>Lectures:</b><br><?php echo $totals['total_reads']; ?></div>
										<div class="col-sm-4 text-center"><b>Clics sense llegir:</b><br><?php echo max(0, $totals['total_manga_clicks']-$totals['total_reads']); ?></div>
										<div class="col-sm-4 text-center"><b>Pàgines totals llegides:</b><br><?php echo $totals['total_pages_read']; ?></div>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Evolució de l'anime <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>

								<ul class="nav nav-tabs" id="chart_tabs_fansub" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="daily_fansub-tab" data-toggle="tab" href="#daily_fansub" role="tab" aria-controls="daily_fansub" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="monthly_fansub-tab" data-toggle="tab" href="#monthly_fansub" role="tab" aria-controls="monthly_fansub" aria-selected="false">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="daily_fansub" role="tabpanel" aria-labelledby="daily_fansub-tab">
<?php
	$days = array();

	$current_day = strtotime(date('Y-m-d'));
	$i=$max_days;
	while (strtotime(date('Y-m-d')."-$i days")<=$current_day) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
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
										<canvas id="daily_chart_fansub"></canvas>
										<script>
											var ctx = document.getElementById('daily_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Visualitzacions reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense visualitzar',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$time_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
													}
												}
											});
										</script>
									</div>
									<div class="tab-pane fade" id="monthly_fansub" role="tabpanel" aria-labelledby="monthly_fansub-tab">
<?php
		$months = array();

		$current_month = strtotime(date('Y-m-01'));
		$i=0;
		while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0);
			$i++;
		}

		$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
										<canvas id="monthly_chart_fansub"></canvas>
										<script>
											var ctx = document.getElementById('monthly_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Visualitzacions reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense visualitzar',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Temps de visualització (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$time_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
										<a class="nav-link active" id="manga_daily_fansub-tab" data-toggle="tab" href="#manga_daily_fansub" role="tab" aria-controls="daily_fansub" aria-selected="true">Evolució diària (darrers <?php echo $max_days; ?> dies)</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="manga_monthly_fansub-tab" data-toggle="tab" href="#manga_monthly_fansub" role="tab" aria-controls="manga_monthly_fansub" aria-selected="false">Evolució mensual (total)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade show active" id="manga_daily_fansub" role="tabpanel" aria-labelledby="manga_daily_fansub-tab">
<?php
	$days = array();

	$current_day = strtotime(date('Y-m-d'));
	$i=$max_days;
	while (strtotime(date('Y-m-d')."-$i days")<=$current_day) {
		$days[date("Y-m-d", strtotime(date('Y-m-d')."-$i days"))]=array(0, 0, 0);
		$i--;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v LEFT JOIN file f ON v.file_id=f.id WHERE f.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND DATE_FORMAT(v.day,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."' GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
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
										<canvas id="manga_daily_chart_fansub"></canvas>
										<script>
											var ctx = document.getElementById('manga_daily_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$day_values); ?>],
													datasets: [
													{
														label: 'Lectures reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense llegir',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$page_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
		while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0);
			$i++;
		}

		$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v LEFT JOIN file f ON v.file_id=f.id WHERE f.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
										<canvas id="manga_monthly_chart_fansub"></canvas>
										<script>
											var ctx = document.getElementById('manga_monthly_chart_fansub').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: 'Lectures reals',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>]
													},
													{
														label: 'Clics sense llegir',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>]
													},
													{
														label: 'Pàgines llegides',
														backgroundColor: 'rgb(167, 167, 69)',
														borderColor: 'rgb(167, 167, 69)',
														fill: false,
														data: [<?php echo implode(',',$page_values); ?>]
													}]
												},
												options: {
													legend: {
														position: 'bottom'
													},
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero: true
															}
														}]
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
								<h4 class="card-title text-center mb-4 mt-1">Els 10 animes més vistos <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers 14 dies / sempre)</h4>
								<hr>
								<div class="row">
									<div class="w-50 pr-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Anime</th>
													<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
		$result = query("SELECT a.series_id, a.series_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id, s.name series_name FROM link l LEFT JOIN views vi ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' AND l.episode_id IS NOT NULL AND l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY l.version_id, l.episode_id) a GROUP BY a.series_id ORDER BY max_views DESC, a.series_name ASC LIMIT 10");
		while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['series_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
		}
		mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
									<div class="w-50 pl-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Anime</th>
													<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
		$result = query("SELECT a.series_id, a.series_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, l.version_id, s.id series_id, s.name series_name FROM link l LEFT JOIN views vi ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE l.episode_id IS NOT NULL AND l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY l.version_id, l.episode_id) a GROUP BY a.series_id ORDER BY max_views DESC, a.series_name ASC LIMIT 10");
		while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['series_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
		}
		mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Els 10 mangues més llegits <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers 14 dies / sempre)</h4>
								<hr>
								<div class="row">
									<div class="w-50 pr-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Manga</th>
													<th class="text-center" scope="col" style="width: 28%;">Lectures<br /><small>(capítol més llegit)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
		$result = query("SELECT a.manga_id, a.manga_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.id manga_id, m.name manga_name FROM file fi LEFT JOIN manga_views vi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id WHERE vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' AND fi.chapter_id IS NOT NULL AND fi.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY fi.manga_version_id, fi.chapter_id) a GROUP BY a.manga_id ORDER BY max_views DESC, a.manga_name ASC LIMIT 10");
		while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['manga_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
		}
		mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
									<div class="w-50 pl-1">
										<table class="table table-hover table-striped">
											<thead class="thead-dark">
												<tr>
													<th scope="col">Manga</th>
													<th class="text-center" scope="col" style="width: 28%;">Lectures<br /><small>(capítol més llegit)</small></th>
												</tr>
											</thead>
											<tbody>
<?php
		$result = query("SELECT a.manga_id, a.manga_name, IFNULL(MAX(a.views),0) max_views FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.id manga_id, m.name manga_name FROM file fi LEFT JOIN manga_views vi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id WHERE fi.chapter_id IS NOT NULL AND fi.manga_version_id IN (SELECT DISTINCT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY fi.manga_version_id, fi.chapter_id) a GROUP BY a.manga_id ORDER BY max_views DESC, a.manga_name ASC LIMIT 10");
		while ($row = mysqli_fetch_assoc($result)) {
?>
												<tr>
													<td scope="col"><?php echo $row['manga_name']; ?></td>
													<td class="text-center"><?php echo $row['max_views']; ?></td>
												</tr>
<?php
		}
		mysqli_free_result($result);
?>
											</tbody>
										</table>
									</div>
								</div>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions d'anime <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$status_values=array();
	$status_colors=array();
	$status_count_values=array();
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM version v WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY v.status ORDER BY status ASC");
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
								<canvas id="fansub_version_status_chart"></canvas>
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
											legend: {
												position: 'right'
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
	$result = query("SELECT v.status, COUNT(v.id) version_count FROM manga_version v WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY v.status ORDER BY status ASC");
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
								<canvas id="fansub_manga_version_status_chart"></canvas>
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
											legend: {
												position: 'right'
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
								<h4 class="card-title text-center mb-4 mt-1">Nombre d'enllaços d'anime amb participació <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$link_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.link_count) link_count FROM (SELECT a.fansub_name, COUNT(a.id) link_count FROM (SELECT l.id, l.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE l.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND l.lost=0 GROUP BY l.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, link_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($link_count_values, $row['link_count']);
	}
	mysqli_free_result($result);
?>
								<canvas id="fansub_fansub_links_chart"></canvas>
								<script>
									var ctx = document.getElementById('fansub_fansub_links_chart').getContext('2d');
									var chart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [<?php echo implode(',',$fansub_values); ?>],
											datasets: [
											{
												data: [<?php echo implode(',',$link_count_values); ?>],
												backgroundColor: [<?php echo implode(',',$fansub_colors); ?>]
											}]
										},
										options: {
											legend: {
												position: 'right'
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
	$result = query("SELECT b.fansub_name,SUM(b.file_count) file_count FROM (SELECT a.fansub_name, COUNT(a.id) file_count FROM (SELECT fi.id, fi.manga_version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM file fi LEFT JOIN rel_manga_version_fansub vf ON fi.manga_version_id = vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE fi.manga_version_id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND fi.original_filename IS NOT NULL GROUP BY fi.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, file_count DESC");
	while ($row = mysqli_fetch_assoc($result)) {
		mt_srand(crc32($row['fansub_name'])*1714); //To always get the same values for colors
		array_push($fansub_values, "'".str_replace("&#039;", "\\'", htmlspecialchars($row['fansub_name'], ENT_QUOTES))."'");
		array_push($fansub_colors, "'".sprintf('#%06X', mt_rand(0, 0xFFFFFF))."'");
		array_push($file_count_values, $row['file_count']);
	}
	mysqli_free_result($result);
?>
								<canvas id="fansub_fansub_files_chart"></canvas>
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
											legend: {
												position: 'right'
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
								<h4 class="card-title text-center mb-4 mt-1">Origen de les visualitzacions d'anime <?php echo get_fansub_preposition_name($fansub['name']); ?> (darrers <?php echo $max_days; ?> dies)</h4>
								<hr>
<?php
	$origin_labels=array("'Ordinador'","'Mòbil o tauleta'","'Google Cast'");
	$origin_colors=array("'#28a745'","'#17a2b8'","'#007bff'");
	$result = query("SELECT (SELECT COUNT(*) FROM view_log vl LEFT JOIN link l ON vl.link_id=l.id LEFT JOIN version v ON l.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND view_type='desktop' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM view_log vl LEFT JOIN link l ON vl.link_id=l.id LEFT JOIN version v ON l.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND view_type='mobile' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM view_log vl LEFT JOIN link l ON vl.link_id=l.id LEFT JOIN version v ON l.version_id=v.id WHERE v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND view_type='cast' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') cast");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['cast']);
	mysqli_free_result($result);
?>
								<canvas id="fansub_anime_origin_chart"></canvas>
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
											legend: {
												position: 'right'
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
	$result = query("SELECT (SELECT COUNT(*) FROM manga_view_log vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN manga_version v ON f.manga_version_id=v.id WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND read_type='desktop' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') desktop, (SELECT COUNT(*) FROM manga_view_log vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN manga_version v ON f.manga_version_id=v.id WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND read_type='mobile' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') mobile, (SELECT COUNT(*) FROM manga_view_log vl LEFT JOIN file f ON vl.file_id=f.id LEFT JOIN manga_version v ON f.manga_version_id=v.id WHERE v.id IN (SELECT manga_version_id FROM rel_manga_version_fansub WHERE fansub_id=".$fansub['id'].") AND read_type='api' AND DATE_FORMAT(date,'%Y-%m-%d')>='".date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."') api");
	$row = mysqli_fetch_assoc($result);
	$origin_values=array($row['desktop'], $row['mobile'], $row['api']);
	mysqli_free_result($result);
?>
								<canvas id="fansub_manga_origin_chart"></canvas>
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
											legend: {
												position: 'right'
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

include("footer.inc.php");
?>
