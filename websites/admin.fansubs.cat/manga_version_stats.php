<?php
$header_title="Estadístiques de la versió de manga - Manga";
$page="manga";
include("header.inc.php");
require_once("common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$max_days=60;
	if (!empty($_GET['max_days']) && is_numeric($_GET['max_days'])) {
		$max_days = intval($_GET['max_days']);
	}
	$result = query("SELECT v.*, m.name manga_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM manga_version v LEFT JOIN manga m ON v.manga_id=m.id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Estadístiques de la versió</h4>
					<hr>
					<p class="text-center">Aquestes són les estadístiques de la versió de "<b><?php echo htmlspecialchars($row['manga_name']); ?></b>" feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
<?php
	$result = query("SELECT IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0) total_time_spent, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v LEFT JOIN file fi ON v.file_id=fi.id WHERE fi.manga_version_id=".escape($_GET['id']));
	$totals = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
?>
					<div class="row">
						<div class="col-sm-3 text-center"><b>Lectures reals:</b> <?php echo $totals['total_views']; ?></div>
						<div class="col-sm-3 text-center"><b>Clics sense llegir:</b> <?php echo max(0, $totals['total_clicks']-$totals['total_views']); ?></div>
						<div class="col-sm-3 text-center"><b>Temps de lectura:</b> <?php echo get_hours_or_minutes_formatted($totals['total_time_spent']); ?></div>
						<div class="col-sm-3 text-center"><b>Pàgines llegides:</b> <?php echo $totals['total_pages_read']; ?></div>
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
							<a class="nav-link active" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="true">Mensualment (total)</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="false">Diàriament (darrers <?php echo $max_days; ?> dies)</a>
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
	while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v LEFT JOIN file fi ON v.file_id=fi.id WHERE fi.manga_version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$months[date("Y-m", strtotime($row['month'].'-01'))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent'], $row['total_pages_read']);
	}
	mysqli_free_result($result);

	$month_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();
	$page_values=array();

	foreach ($months as $month => $values) {
		array_push($month_values, "'".$month."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
		array_push($page_values, $values[3]);
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
														label: 'Temps de lectura (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$time_values); ?>]
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
		$days[date("Y-m-d", strtotime(date("Y-m-d", strtotime(date('Y-m-d')."-$max_days days"))."+$i days"))]=array(0, 0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent, IFNULL(SUM(pages_read),0) total_pages_read FROM manga_views v LEFT JOIN file fi ON v.file_id=fi.id WHERE fi.manga_version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		if (array_key_exists(date("Y-m-d", strtotime($row['day'])), $days)) {
			$days[date("Y-m-d", strtotime($row['day']))]=array($row['total_clicks'], $row['total_views'], $row['total_time_spent'], $row['total_pages_read']);
		}
	}
	mysqli_free_result($result);

	$days_values=array();
	$click_values=array();
	$view_values=array();
	$time_values=array();
	$page_values=array();

	foreach ($days as $day => $values) {
		array_push($days_values, "'".$day."'");
		array_push($click_values, $values[0]);
		array_push($view_values, $values[1]);
		array_push($time_values, $values[2]);
		array_push($page_values, $values[3]);
	}
?>
										<canvas id="daily_chart"></canvas>
										<script>
											var ctx = document.getElementById('daily_chart').getContext('2d');
											var chart = new Chart(ctx, {
												type: 'line',
												data: {
													labels: [<?php echo implode(',',$days_values); ?>],
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
														label: 'Temps de lectura (h)',
														backgroundColor: 'rgb(40, 167, 69)',
														borderColor: 'rgb(40, 167, 69)',
														fill: false,
														hidden: true,
														data: [<?php echo implode(',',$time_values); ?>]
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
					<h4 class="card-title text-center mb-4 mt-1">Dades per capítol</h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Capítol</th>
								<th class="text-center" scope="col" style="width: 12%;">Lectures</th>
								<th class="text-center" scope="col" style="width: 12%;">Clics sense ll.</th>
								<th class="text-center" scope="col" style="width: 12%;">Temps total</th>
								<th class="text-center" scope="col" style="width: 12%;">Temps mitjà</th>
								<th class="text-center" scope="col" style="width: 12%;">Pàg. totals</th>
								<th class="text-center" scope="col" style="width: 12%;">Pàg. mitjanes</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT fi.chapter_id, c.number, c.name, ct.title, fi.extra_name, m.chapters manga_chapters, m.name manga_name, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0) total_time_spent, IFNULL(SUM(pages_read),0) total_pages_read FROM file fi LEFT JOIN manga_views v ON fi.id=v.file_id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN chapter_title ct ON c.id=ct.chapter_id AND ct.manga_version_id=fi.manga_version_id LEFT JOIN manga m ON c.manga_id=m.id WHERE fi.manga_version_id=".escape($_GET['id'])." GROUP BY IFNULL(fi.chapter_id,fi.extra_name) ORDER BY fi.chapter_id IS NULL ASC, c.number IS NULL ASC, c.number ASC, fi.extra_name ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$chapter_title='';
		
		if (!empty($row['chapter_id'])) {
			if (!empty($row['number'])) {
				if (!empty($row['title'])){
					if ($row['manga_episodes']==1){
						$chapter_title.=htmlspecialchars($row['title']);
					} else {
						$chapter_title.='Capítol '.floatval($row['number']).': '.htmlspecialchars($row['title']);
					}
				}
				else {
					if ($row['manga_episodes']==1){
						$chapter_title.=htmlspecialchars($row['series_name']);
					} else {
						$chapter_title.='Capítol '.floatval($row['number']);
					}
				}
			} else {
				if (!empty($row['title'])){
					$chapter_title.=htmlspecialchars($row['title']);
				}
				else {
					$chapter_title.=$row['name'];
				}
			}
		} else {
			$chapter_title.=$row['extra_name'];
		}
?>
							<tr>
								<td scope="col"><?php echo $chapter_title; ?></td>
								<td class="text-center"><?php echo $row['total_views']; ?></td>
								<td class="text-center"><?php echo max(0, $row['total_clicks']-$row['total_views']); ?></td>
								<td class="text-center"><?php echo get_hours_or_minutes_formatted($row['total_time_spent']); ?></td>
								<td class="text-center"><?php echo $row['total_views']!=0 ? get_hours_or_minutes_formatted($row['total_time_spent']/$row['total_views']) : 0; ?></td>
								<td class="text-center"><?php echo $row['total_pages_read']; ?></td>
								<td class="text-center"><?php echo $row['total_views']!=0 ? intval(round($row['total_pages_read']/$row['total_views'])) : 0; ?></td>
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
