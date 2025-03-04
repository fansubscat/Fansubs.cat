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

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$viewed_content_divide=3600;
		$series_a='Visualitzacions';
		$series_b='Clics';
		$series_c='Temps de visualització';
		$series_a_graph='Visualitzacions';
		$series_b_graph='Clics';
		$series_c_graph='Temps de visualització (h)';
		$series_a_short='Visualitzacions';
		$series_b_short='Clics';
		$series_c_short='Temps total';
		$series_c_color='rgb(40, 167, 69)';
		$start_date=STARTING_DATE;
		$link_url=ANIME_URL;
	break;
	case 'liveaction':
		$viewed_content_divide=3600;
		$series_a='Visualitzacions';
		$series_b='Clics';
		$series_c='Temps de visualització';
		$series_a_graph='Visualitzacions';
		$series_b_graph='Clics';
		$series_c_graph='Temps de visualització (h)';
		$series_a_short='Visualitzacions';
		$series_b_short='Clics';
		$series_c_short='Temps total';
		$series_c_color='rgb(40, 167, 69)';
		$start_date=STARTING_DATE;
		$link_url=LIVEACTION_URL;
	break;
	case 'manga':
		$viewed_content_divide=1;
		$series_a='Lectures';
		$series_b='Clics';
		$series_c='Pàgines llegides';
		$series_a_graph='Lectures';
		$series_b_graph='Clics';
		$series_c_graph='Pàgines llegides';
		$series_a_short='Lectures';
		$series_b_short='Clics';
		$series_c_short='Pàg. totals';
		$series_c_color='rgb(167, 167, 69)';
		$start_date=STARTING_DATE;
		$link_url=MANGA_URL;
	break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$max_days=60;
	if (!empty($_GET['max_days']) && is_numeric($_GET['max_days'])) {
		$max_days = intval($_GET['max_days']);
	}
	$result = query("SELECT v.*, s.name series_name, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN series s ON v.series_id=s.id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id=".escape($_GET['id'])." GROUP BY v.id");
	$row = mysqli_fetch_assoc($result) or crash('Version not found');
	$slug = $row['slug'];
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Estadístiques de la versió</h4>
					<hr>
					<p class="text-center">Aquestes són les estadístiques de la versió de «<b><?php echo htmlspecialchars($row['title']); ?></b>» feta per <?php echo htmlspecialchars($row['fansub_name']); ?>.</p>
<?php
	$result = query("SELECT IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0) total_length, (SELECT COUNT(*) FROM user_version_rating WHERE rating=1 AND version_id=".escape($_GET['id']).") good_ratings, (SELECT COUNT(*) FROM user_version_rating WHERE rating=-1 AND version_id=".escape($_GET['id']).") bad_ratings, (SELECT COUNT(*) FROM comment WHERE type='user' AND version_id=".escape($_GET['id']).") num_comments FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id']));
	$totals = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
?>
					<div class="row">
						<div class="col-sm-4 text-center"><b><?php echo $series_a; ?>:</b> <?php echo $totals['total_views']; ?></div>
						<div class="col-sm-4 text-center"><b><?php echo $series_b; ?>:</b> <?php echo $totals['total_clicks']; ?></div>
						<div class="col-sm-4 text-center"><b><?php echo $series_c; ?>:</b> <?php echo $type=='manga' ? $totals['total_length'] : get_hours_or_minutes_formatted($totals['total_length']); ?></div>
					</div>
					<div class="row">
						<div class="col-sm-4 text-center"><b>Valoracions positives:</b> <?php echo $totals['good_ratings']; ?></div>
						<div class="col-sm-4 text-center"><b>Valoracions negatives:</b> <?php echo $totals['bad_ratings']; ?></div>
						<div class="col-sm-4 text-center"><b>Comentaris d’usuaris:</b> <?php echo $totals['num_comments']; ?></div>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Comentaris dels usuaris</h4>
					<hr>
					<div class="row">
						<table class="table table-hover table-striped">
							<thead class="table-dark">
								<tr>
									<th scope="col" style="width: 10%;">Usuari</th>
									<th scope="col" style="width: 65%;">Comentari</th>
									<th scope="col" style="width: 10%;" class="text-center">Data</th>
									<th scope="col" style="width: 5%;" class="text-center">Espòiler</th>
									<th scope="col" style="width: 5%;" class="text-center">Respost</th>
									<th class="text-center" scope="col">Accions</th>
								</tr>
							</thead>
							<tbody>
<?php
$result = query("SELECT c.*, s.name series_name, u.username, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.version_id=".escape($_GET['id'])." AND c.type='user' ORDER BY c.created DESC");
if (mysqli_num_rows($result)==0) {
?>
								<tr>
									<td colspan="6" class="text-center">- No hi ha cap comentari -</td>
								</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
								<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
									<td class="align-middle"><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></td>
									<td class="align-middle"><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></td>
									<td class="align-middle text-center"><?php echo $row['created']; ?></td>
									<td class="align-middle text-center"><?php echo $row['has_spoilers']==1 ? 'Sí' : 'No'; ?></td>
									<td class="align-middle text-center"><?php echo $row['last_replied']!=$row['created'] ? 'Sí' : 'No'; ?></td>
									<td class="align-middle text-center text-nowrap">
										<a href="comment_reply.php?id=<?php echo $row['id']; ?>&source_version_id=<?php echo $_GET['id']; ?>&source_type=<?php echo $type; ?>" title="Respon" class="fa fa-reply p-1"></a>
<?php
if ($_SESSION['admin_level']>=3) {
?>
										<a href="comment_list.php?delete_id=<?php echo $row['id']; ?>&source_version_id=<?php echo $_GET['id']; ?>&source_type=<?php echo $type; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el comentari seleccionat? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
<?php
}
?>
									</td>
								</tr>
<?php
}
mysqli_free_result($result);
?>
							</tbody>
						</table>
						<p class="text-center text-muted small">En aquesta llista no es mostren els comentaris ni les respostes dels fansubs. Ho pots veure tot a la <a href="<?php echo $link_url.'/'.$slug; ?>" target="_blank">fitxa pública</a>.</p>
						<div class="text-center">
							<a href="comment_reply.php?source_version_id=<?php echo $_GET['id']; ?>&source_type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix un comentari del fansub</a>
						</div>
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

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m') month, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/$viewed_content_divide total_length FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m') ORDER BY DATE_FORMAT(v.day,'%Y-%m') ASC");
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
														label: '<?php echo $series_b_graph; ?>',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_a_graph; ?>',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
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

	$result = query("SELECT DATE_FORMAT(v.day,'%Y-%m-%d') day, IFNULL(SUM(clicks),0) total_clicks, IFNULL(SUM(views),0) total_views, IFNULL(SUM(total_length),0)/$viewed_content_divide total_length FROM views v LEFT JOIN file f ON v.file_id=f.id WHERE f.version_id=".escape($_GET['id'])." GROUP BY DATE_FORMAT(v.day,'%Y-%m-%d') ORDER BY DATE_FORMAT(v.day,'%Y-%m-%d') ASC");
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
														label: '<?php echo $series_b_graph; ?>',
														backgroundColor: 'rgb(220, 53, 69)',
														borderColor: 'rgb(220, 53, 69)',
														hidden: true,
														data: [<?php echo implode(',',$click_values); ?>],
														tension: 0.2
													},
													{
														label: '<?php echo $series_a_graph; ?>',
														backgroundColor: 'rgb(0, 123, 255)',
														borderColor: 'rgb(0, 123, 255)',
														hidden: true,
														data: [<?php echo implode(',',$view_values); ?>],
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
	$result = query("SELECT f.episode_id,
				e.number,
				e.description,
				et.title,
				f.extra_name,
				s.number_of_episodes,
				s.name series_name,
				IFNULL(SUM(clicks),0) total_clicks,
				IFNULL(SUM(views),0) total_views,
				IFNULL(SUM(total_length),0) total_length,
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				) episode_title
			FROM file f
			LEFT JOIN version v ON f.version_id=v.id
			LEFT JOIN views vi ON f.id=vi.file_id
			LEFT JOIN episode e ON f.episode_id=e.id
			LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=f.version_id
			LEFT JOIN division d ON e.division_id=d.id
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN series s ON e.series_id=s.id
			WHERE f.version_id=".escape($_GET['id'])."
			GROUP BY IFNULL(f.episode_id,CONCAT('extra-',f.extra_name))
			ORDER BY d.number ASC, f.episode_id IS NULL ASC, e.number IS NULL ASC, e.number ASC, et.title ASC, f.extra_name ASC");
	if (mysqli_num_rows($result)==0) {
?>
								<tr>
									<td colspan="4" class="text-center">- No hi ha cap capítol -</td>
								</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		if (!empty($row['episode_id'])) {
			$episode_title=$row['episode_title'];
		} else {
			$episode_title=$row['extra_name'];
		}
?>
							<tr>
								<td scope="col"><?php echo $episode_title; ?></td>
								<td class="text-center"><?php echo $row['total_views']; ?></td>
								<td class="text-center"><?php echo $row['total_clicks']; ?></td>
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

include(__DIR__.'/footer.inc.php');
?>
