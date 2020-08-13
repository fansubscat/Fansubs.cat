<?php
$header_title="Versions";
$page="version";
include("header.inc.php");
require_once("../common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT v.*, s.name series_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN series s ON v.series_id=s.id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Estadístiques de la versió</h4>
					<hr>
					<p class="text-center">Aquestes són les estadístiques de la versió de "<b><?php echo htmlspecialchars($row['series_name']); ?></b>" feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
<?php
	$result = query("SELECT IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0) total_time_spent FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id=".escape($_GET['id']));
	$totals = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
?>
					<div class="row">
						<div class="col-sm-4 text-center"><b>Visualitzacions reals:</b> <?php echo $totals['total_views']; ?></div>
						<div class="col-sm-4 text-center"><b>Clics sense visualitzar:</b> <?php echo max(0, $totals['total_clicks']-$totals['total_views']); ?></div>
						<div class="col-sm-4 text-center"><b>Temps de visualització:</b> <?php echo get_hours_or_minutes_formatted($totals['total_time_spent']); ?></div>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Evolució mensual</h4>
					<hr>
<?php
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=array(0, 0, 0);
		$i++;
	}

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, GREATEST(IFNULL(SUM(clicks),0)-IFNULL(SUM(views),0),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0)/3600 total_time_spent FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
									data: [<?php echo implode(',',$view_values); ?>]
								},
								{
									label: 'Clics sense visualitzar',
									backgroundColor: 'rgb(220, 53, 69)',
									borderColor: 'rgb(220, 53, 69)',
									fill: false,
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
							options: {}
						});
					</script>
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
								<th class="text-center" scope="col" style="width: 12%;">Visualitzacions</th>
								<th class="text-center" scope="col" style="width: 12%;">Clics sense v.</th>
								<th class="text-center" scope="col" style="width: 12%;">Temps total</th>
								<th class="text-center" scope="col" style="width: 12%;">Temps mitjà</th>
							</tr>
<?php
	$result = query("SELECT l.episode_id, e.number, e.name, et.title, l.extra_name, s.episodes series_episodes, s.name series_name, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(time_spent),0) total_time_spent FROM link l LEFT JOIN views v ON l.id=v.link_id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=l.version_id LEFT JOIN series s ON e.series_id=s.id WHERE l.version_id=".escape($_GET['id'])." GROUP BY IFNULL(l.episode_id,l.extra_name) ORDER BY l.episode_id IS NULL ASC, e.number IS NULL ASC, e.number ASC, l.extra_name ASC");
	while ($row = mysqli_fetch_assoc($result)) {
		$episode_title='';
		
		if (!empty($row['episode_id'])) {
			if (!empty($row['number'])) {
				if (!empty($row['title'])){
					if ($row['series_episodes']==1){
						$episode_title.=htmlspecialchars($row['title']);
					} else {
						$episode_title.='Capítol '.$row['number'].': '.htmlspecialchars($row['title']);
					}
				}
				else {
					if ($row['series_episodes']==1){
						$episode_title.=htmlspecialchars($row['series_name']);
					} else {
						$episode_title.='Capítol '.$row['number'];
					}
				}
			} else {
				if (!empty($row['title'])){
					$episode_title.=htmlspecialchars($row['title']);
				}
				else {
					$episode_title.=$row['name'];
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
								<td class="text-center"><?php echo get_hours_or_minutes_formatted($row['total_time_spent']); ?></td>
								<td class="text-center"><?php echo $row['total_views']!=0 ? get_hours_or_minutes_formatted($row['total_time_spent']/$row['total_views']) : 0; ?></td>
							</tr>
<?php
	}
?>
						</thead>
						<tbody>
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
