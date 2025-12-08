<?php
require_once(__DIR__.'/../common/initialization.inc.php');

$page="analytics";
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title=lang('admin.views.header.anime');
		$extra_info_string=lang('admin.views.extra_info');
		$last_views_string=lang('admin.views.last_views.anime');
		$in_progress_string=lang('admin.views.in_progress.anime');
	break;
	case 'manga':
		$header_title=lang('admin.views.header.manga');
		$extra_info_string=lang('admin.views.extra_info.manga');
		$last_views_string=lang('admin.views.last_views.manga');
		$in_progress_string=lang('admin.views.in_progress.manga');
	break;
	case 'liveaction':
		$header_title=lang('admin.views.header.liveaction');
		$extra_info_string=lang('admin.views.extra_info');
		$last_views_string=lang('admin.views.last_views.liveaction');
		$in_progress_string=lang('admin.views.in_progress.liveaction');
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_SESSION['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_SESSION['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	} else if ($_SESSION['admin_level']>=3 && !empty($_GET['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_GET['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	}

	$limit = 25;
	if (!empty($_GET['limit']) && is_numeric($_GET['limit'])){
		$limit = escape($_GET['limit']);
	}
?>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true"><?php echo sprintf(lang('admin.views.fansub_tab'), get_fansub_preposition_name($fansub['name'])); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false"><?php echo lang('admin.views.global_tab'); ?></a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo $in_progress_string; ?></h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-redo pe-2 fa-width-auto"></span><?php echo lang('admin.generic.refresh'); ?></a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo lang('admin.views.content_and_episode'); ?></th>
												<th scope="col" class="text-center" style="width: 10%;"><?php echo lang('admin.views.user'); ?></th>
												<th scope="col" class="text-center" style="width: 20%;"><?php echo lang('admin.views.progress'); ?></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			ps.view_counted,
			s.rating,
			s.has_licensed_parts
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE s.type='$type' 
			AND UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)." 
		ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
												<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name'].' - '.$row['title']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></th>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['updated']<date('U')-120 ? "bg-primary" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center"><div<?php echo !empty($row['view_counted']) ? ' class="fa fa-thumbs-up" style="color: green;" title="'.lang('admin.views.counted').'"' : ' class="fa fa-thumbs-down hentai" title="'.lang('admin.views.not_counted').'"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small"><?php echo $extra_info_string; ?></p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf($last_views_string, $limit); ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo lang('admin.views.content_and_episode'); ?></th>
												<th scope="col" class="text-center" style="width: 10%;"><?php echo lang('admin.views.user'); ?></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;"><?php echo lang('admin.views.date'); ?></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating,
			s.has_licensed_parts
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE s.type='$type' 
			AND ps.view_counted IS NOT NULL  
		ORDER BY ps.view_counted DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
												<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name'].' - '.$row['title']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></th>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center" class="text-center"><?php echo date('Y-m-d H:i:s', $row['view_counted']); ?></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
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
								<h4 class="card-title text-center mb-4 mt-1"><?php echo $in_progress_string; ?></h4>
								<hr>
								<div class="text-center pb-3">
									<a href="views.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-redo pe-2"></span><?php echo lang('admin.generic.refresh'); ?></a>
								</div>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo lang('admin.views.content_and_episode'); ?></th>
												<th scope="col" class="text-center" style="width: 10%;"><?php echo lang('admin.views.user'); ?></th>
												<th scope="col" class="text-center" style="width: 20%;"><?php echo lang('admin.views.progress'); ?></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-thumbs-up"></span></th>
											</tr>
										</thead>
										<tbody>
<?php

$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			ps.view_counted,
			s.rating,
			s.has_licensed_parts
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE s.type='$type' 
			AND UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)."
			AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")
		ORDER BY ps.created DESC");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
												<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name'].' - '.$row['title']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></th>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div class="progress"><div class="progress-bar progress-bar-striped <?php echo $row['updated']<date('U')-120 ? "bg-primary" : "progress-bar-animated"; ?>" role="progressbar" style="width: <?php echo min(100,$row['progress']); ?>%;" aria-valuenow="<?php echo min(100,$row['progress']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo min(100,round($row['progress'],1)); ?>%</div></div></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center"><div<?php echo !empty($row['view_counted']) ? ' class="fa fa-thumbs-up" style="color: green;" title="'.lang('admin.views.counted').'"' : ' class="fa fa-thumbs-down hentai" title="'.lang('admin.views.not_counted').'"'; ?>></div></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
								<p class="text-center text-muted small"><?php echo $extra_info_string; ?></p>
							</article>
						</div>
					</div>
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf($last_views_string, $limit); ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col"><?php echo lang('admin.views.content_and_episode'); ?></th>
												<th scope="col" class="text-center" style="width: 10%;"><?php echo lang('admin.views.user'); ?></th>
												<th scope="col" style="width: 5%; text-align: center;"><span class="far fa-eye"></span></th>
												<th scope="col" class="text-center" style="width: 20%;"><?php echo lang('admin.views.date'); ?></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating,
			s.has_licensed_parts
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE s.type='$type' 
			AND ps.view_counted IS NOT NULL
			AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")
		ORDER BY ps.view_counted DESC LIMIT $limit");
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
												<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name'].' - '.$row['title']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></th>
												<td scope="col" class="text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
												<td class="text-center"><div <?php echo get_browser_icon_by_source_type($row['source'], $row['is_casted']); ?>></div></td>
												<td class="text-center" class="text-center"><?php echo date('Y-m-d H:i:s', $row['view_counted']); ?></td>
											</tr>
<?php
}
mysqli_free_result($result);
?>
										</tbody>
									</table>
								</div>
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
