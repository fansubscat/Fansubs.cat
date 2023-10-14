<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Estadístiques de la versió d’anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Estadístiques de la versió de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Estadístiques de la versió d’imatge real - Imatge real";
		$page="liveaction";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$viewed_content_divide=3600;
		$series_a='Visualitzacions reals';
		$series_b='Clics sense visualitzar';
		$series_c='Temps de visualització';
		$series_a_graph='Visualitzacions reals';
		$series_b_graph='Clics sense visualitzar';
		$series_c_graph='Temps de visualització (h)';
		$series_a_short='Visualitzacions';
		$series_b_short='Clics sense v.';
		$series_c_short='Temps total';
		$series_c_color='rgb(40, 167, 69)';
		$start_date='2020-06-01';
	break;
	case 'liveaction':
		$viewed_content_divide=3600;
		$series_a='Visualitzacions reals';
		$series_b='Clics sense visualitzar';
		$series_c='Temps de visualització';
		$series_a_graph='Visualitzacions reals';
		$series_b_graph='Clics sense visualitzar';
		$series_c_graph='Temps de visualització (h)';
		$series_a_short='Visualitzacions';
		$series_b_short='Clics sense v.';
		$series_c_short='Temps total';
		$series_c_color='rgb(40, 167, 69)';
		$start_date='2022-06-01';
	break;
	case 'manga':
		$viewed_content_divide=1;
		$series_a='Lectures reals';
		$series_b='Clics sense llegir';
		$series_c='Pàgines llegides';
		$series_a_graph='Lectures reals';
		$series_b_graph='Clics sense llegir';
		$series_c_graph='Pàgines llegides';
		$series_a_short='Lectures';
		$series_b_short='Clics sense ll.';
		$series_c_short='Pàg. totals';
		$series_c_color='rgb(167, 167, 69)';
		$start_date='2021-01-01';
	break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$max_days=60;
	if (!empty($_GET['max_days']) && is_numeric($_GET['max_days'])) {
		$max_days = intval($_GET['max_days']);
	}
	$result = query("SELECT v.*, s.name series_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN series s ON v.series_id=s.id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Estadístiques de la versió</h4>
					<hr>
					<p class="text-center">Aquestes són les estadístiques de la versió de «<b><?php echo htmlspecialchars($row['series_name']); ?></b>» feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
<?php
	$result = query("SELECT IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_length FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id']));
	$totals = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
?>
					<div class="row">
						<div class="col-sm-4 text-center"><b><?php echo $series_a; ?>:</b> <?php echo $totals['total_views']; ?></div>
						<div class="col-sm-4 text-center"><b><?php echo $series_b; ?>:</b> <?php echo max(0, $totals['total_clicks']-$totals['total_views']); ?></div>
						<div class="col-sm-4 text-center"><b><?php echo $series_c; ?>:</b> <?php echo $type=='manga' ? $totals['total_length'] : get_hours_or_minutes_formatted($totals['total_length']); ?></div>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Evolució</h4>
					<hr>
					<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="monthly-tab" data-bs-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="true">Mensualment (total)</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="daily-tab" data-bs-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="false">Diàriament (darrers <?php echo $max_days; ?> dies)</a>
						</li>
					</ul>
					<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
						<div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
							<div class="container d-flex justify-content-center p-4">
								<div class="card w-100">
									<article class="card-body">
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date($start_date)."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date($start_date)."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/$viewed_content_divide total_length FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_length']);
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
										<div class="graph-container"><canvas id="monthly_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('monthly_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$month_values); ?>],
													datasets: [
													{
														label: '<?php echo $series_a_graph; ?>',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_b_graph; ?>',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_c_graph; ?>',
														backgroundColor: '<?php echo $series_c_color; ?>',
														borderColor: '<?php echo $series_c_color; ?>',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.2
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
									</article>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="daily" role="tabpanel" aria-labelledby="daily-tab">
							<div class="container d-flex justify-content-center p-4">
								<div class="card w-100">
									<article class="card-body">
<?php
	$days = array();

	$current_date = strtotime(date('Y-m-d'));
	$i=0;
	while (strtotime(date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."+$i days")<=$current_date) {
		$days[date("Y-m-d", strtotime(date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."+$i days"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/$viewed_content_divide total_length FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		if (array_key_exists(date("Y-m-d", strtotime($row['day'])), $days)) {
			$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_length']);
		}
	}
	mysqli_free_result($result);

	$days_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();

	foreach ($days as $day => $values) {
		array_push($days_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
	}
?>
										<div class="graph-container"><canvas id="daily_chart"></canvas></div>
										<script>
											var ctx = document.getElementById('daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$days_values); ?>],
													datasets: [
													{
														label: '<?php echo $series_a_graph; ?>',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_b_graph; ?>',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_c_graph; ?>',
														backgroundColor: '<?php echo $series_c_color; ?>',
														borderColor: '<?php echo $series_c_color; ?>',
														data: [<?php echo implode(',',$time_values); ?>],
														tension: 0.2
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
									</article>
								</div>
							</div>
						</div>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Dades totals per capítol</h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col">Capítol</th>
								<th class="text-center" scope="col" style="width: 12%;"><?php echo $series_a_short; ?></th>
								<th class="text-center" scope="col" style="width: 12%;"><?php echo $series_b_short; ?></th>
								<th class="text-center" scope="col" style="width: 12%;"><?php echo $series_c_short; ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT f.episode_id, e.number, e.description, et.title, f.extra_name, s.number_of_episodes, s.name series_name, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_length FROM file f LEFT JOIN views v ON f.id=v.file_id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=f.version_id LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE f.version_id=".escape($_GET['id'])." GROUP BY IFNULL(f.episode_id,f.extra_name) ORDER BY d.number IS NULL ASC, d.number ASC, f.episode_id IS NULL ASC, e.number IS NULL ASC, e.number ASC, et.title ASC, f.extra_name ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$episode_title='';
		
		if (!empty($row['episode_id'])) {
			if (!empty($row['number'])) {
				if (!empty($row['title'])){
					if ($row['number_of_episodes']==1){
						$episode_title.=htmlspecialchars($row['title']);
					} else {
						$episode_title.='Capítol '.floatval($row['number']).': '.htmlspecialchars($row['title']);
					}
				}
				else {
					if ($row['number_of_episodes']==1){
						$episode_title.=htmlspecialchars($row['series_name']);
					} else {
						$episode_title.='Capítol '.floatval($row['number']);
					}
				}
			} else {
				if (!empty($row['title'])){
					$episode_title.=htmlspecialchars($row['title']);
				}
				else {
					$episode_title.=$row['description'];
				}
			}
		} else {
			$episode_title.=$row['extra_name'];
		}
?>
							<tr>
								<td scope="col"><?php echo $episode_title; ?></td>
								<td class="text-center"><?php echo $row['total_views']; ?></td>
								<td class="text-center"><?php echo max(0, $row['total_clicks']-$row['total_views']); ?></td>
								<td class="text-center"><?php echo $type=='manga' ? $row['total_length'] : get_hours_or_minutes_formatted($row['total_length']); ?></td>
							</tr>
<?php
	}
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
