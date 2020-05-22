<?php
$header_title="Eines";
$page="tools";
include("header.inc.php");
require_once("../common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_SESSION['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_SESSION['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	}
?>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="totals-tab" data-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false">Estadístiques totals</a>
				</li>
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link" id="fansub-tab" data-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true">Estadístiques <?php echo get_fansub_preposition_name($fansub['name']); ?></a>
				</li>
<?php
	}
?>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade show active" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estadístiques totals</h4>
								<hr>
		<?php
			$result = query("SELECT (SELECT COUNT(*) FROM fansub) total_fansubs, (SELECT COUNT(*) FROM series) total_series, (SELECT COUNT(*) FROM version) total_versions, (SELECT COUNT(*) FROM link WHERE url IS NOT NULL) total_links, (SELECT COUNT(DISTINCT series_id) FROM version v WHERE EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_duplicity, (SELECT SUM(clicks) FROM views) total_clicks, (SELECT SUM(views) FROM views) total_views, (SELECT SUM(time_spent) FROM views) total_time_spent, (SELECT COUNT(DISTINCT episode_id) FROM link WHERE episode_id IS NOT NULL AND url IS NOT NULL) total_linked_episodes, (SELECT SUM(e.duration) FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.url IS NOT NULL) total_duration");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm-4 text-center"><b>Fansubs:</b> <?php echo $totals['total_fansubs']; ?></div>
									<div class="col-sm-4 text-center"><b>Sèries:</b> <?php echo $totals['total_series']; ?> <small>(duplicades: <?php echo $totals['total_duplicity']; ?>)</small></div>
									<div class="col-sm-4 text-center"><b>Versions:</b> <?php echo $totals['total_versions']; ?></div>
								</div>
								<div class="row">
									<div class="col-sm-4 text-center"><b>Episodis amb enllaç:</b> <?php echo $totals['total_linked_episodes']; ?></div>
									<div class="col-sm-4 text-center"><b>Enllaços:</b> <?php echo $totals['total_links']; ?></div>
									<div class="col-sm-4 text-center"><b>Durada total:</b> <?php echo get_hours_or_minutes_formatted($totals['total_duration']*60); ?></div>
								</div>
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
	while (strtotime(date('2020-03-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-03-01')."+$i months"))]=array(0, 0, 0);
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
										options: {
											legend: {
												position: 'bottom'
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
								<h4 class="card-title text-center mb-4 mt-1">Les 10 sèries més vistes</h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="thead-dark">
										<tr>
											<th scope="col">Sèrie</th>
											<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
										</tr>
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
									</thead>
									<tbody>
									</tbody>
								</table>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions</h4>
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
								<h4 class="card-title text-center mb-4 mt-1">Nombre d'enllaços per fansub</h4>
								<hr>
<?php
	$fansub_values=array();
	$fansub_colors=array();
	$link_count_values=array();
	$result = query("SELECT b.fansub_name,SUM(b.link_count) link_count FROM (SELECT IF(COUNT(a.id)>20,a.fansub_name,'Altres') fansub_name, COUNT(a.id) link_count FROM (SELECT l.id, l.version_id, IF(COUNT(DISTINCT vf.fansub_id)>1,'Diversos fansubs',f.name) fansub_name FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id = vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id GROUP BY l.id) a GROUP BY fansub_name) b GROUP BY b.fansub_name ORDER BY fansub_name='Diversos fansubs' ASC, fansub_name='Altres' ASC, link_count DESC");
	mt_srand(0); //To always get the same values from the random colors
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($fansub_values, "'".$row['fansub_name']."'");
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
				</div>
<?php
	if (!empty($fansub)) {
?>
				<div class="tab-pane fade" id="fansub" role="tabpanel" aria-labelledby="fansub-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estadístiques <?php echo get_fansub_preposition_name($fansub['name']); ?></h4>
								<hr>
		<?php
			$result = query("SELECT (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf WHERE fansub_id=".$fansub['id']." AND EXISTS (SELECT * FROM rel_version_fansub vf2 WHERE vf.version_id=vf2.version_id AND vf2.fansub_id<>".$fansub['id'].")) total_collabs, (SELECT COUNT(DISTINCT v.series_id) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id WHERE vf.fansub_id=".$fansub['id'].") total_series, (SELECT COUNT(DISTINCT vf.version_id) FROM rel_version_fansub vf WHERE fansub_id=".$fansub['id'].") total_versions, (SELECT COUNT(DISTINCT l.id) FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id=vf.version_id WHERE l.url IS NOT NULL AND vf.fansub_id=".$fansub['id'].") total_links, (SELECT COUNT(DISTINCT series_id) FROM version v WHERE v.id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") AND EXISTS (SELECT * FROM version v2 WHERE v2.id<>v.id AND v2.series_id=v.series_id)) total_duplicity, (SELECT IFNULL(SUM(clicks),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_clicks, (SELECT IFNULL(SUM(views),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_views, (SELECT IFNULL(SUM(time_spent),0) FROM views v LEFT JOIN link l ON v.link_id=l.id WHERE l.version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_time_spent, (SELECT COUNT(DISTINCT episode_id) FROM link WHERE episode_id IS NOT NULL AND url IS NOT NULL AND version_id IN (SELECT DISTINCT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")) total_linked_episodes, (SELECT SUM(e.duration) FROM link l LEFT JOIN rel_version_fansub vf ON l.version_id=vf.version_id LEFT JOIN episode e ON l.episode_id=e.id WHERE l.url IS NOT NULL AND vf.fansub_id=".$fansub['id'].") total_duration");
			$totals = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		?>
								<div class="row">
									<div class="col-sm-4 text-center"><b>Col·laboracions:</b> <?php echo $totals['total_collabs']; ?></div>
									<div class="col-sm-4 text-center"><b>Sèries:</b> <?php echo $totals['total_series']; ?> <small>(duplicades: <?php echo $totals['total_duplicity']; ?>)</small></div>
									<div class="col-sm-4 text-center"><b>Versions:</b> <?php echo $totals['total_versions']; ?></div>
								</div>
								<div class="row">
									<div class="col-sm-4 text-center"><b>Episodis amb enllaç:</b> <?php echo $totals['total_linked_episodes']; ?></div>
									<div class="col-sm-4 text-center"><b>Enllaços:</b> <?php echo $totals['total_links']; ?></div>
									<div class="col-sm-4 text-center"><b>Durada total:</b> <?php echo get_hours_or_minutes_formatted($totals['total_duration']*60); ?></div>
								</div>
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
		while (strtotime(date('2020-03-01')."+$i months")<=$current_month) {
			$months[date("Y-m", strtotime(date('2020-03-01')."+$i months"))]=array(0, 0, 0);
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
										options: {
											legend: {
												position: 'bottom'
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
								<h4 class="card-title text-center mb-4 mt-1">Les 10 sèries més vistes</h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="thead-dark">
										<tr>
											<th scope="col">Sèrie</th>
											<th class="text-center" scope="col" style="width: 20%;">Visualitzacions<br /><small>(capítol més vist)</small></th>
										</tr>
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
									</thead>
									<tbody>
									</tbody>
								</table>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1">Estat de les versions</h4>
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
