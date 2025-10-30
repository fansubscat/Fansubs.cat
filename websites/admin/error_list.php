<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.error_list.header');
$page="analytics";
include(__DIR__.'/header.inc.php');

function get_error_type($type) {
	switch ($type) {
		case 'mega-unknown':
			return lang('admin.error_list.error_type.mega_unknown');
		case 'mega-unavailable':
			return '<span class="text-danger">'.lang('admin.error_list.error_type.mega_unavailable').'</span>';
		case 'mega-quota-exceeded':
			return '<span class="text-primary">'.lang('admin.error_list.error_type.mega_quota_exceeded').'</span>';
		case 'mega-player-failed':
			return lang('admin.error_list.error_type.mega_player_failed');
		case 'mega-incompatible-browser':
			return lang('admin.error_list.error_type.mega_incompatible_browser');
		case 'mega-connection-error':
			return lang('admin.error_list.error_type.mega_connection_error');
		case 'mega-load-failed':
			return lang('admin.error_list.error_type.mega_load_failed');
		case 'direct-load-failed': //No longer exists, kept for old logs
			return lang('admin.error_list.error_type.direct_load_failed');
		case 'direct-player-failed':
			return lang('admin.error_list.error_type.direct_player_failed');
		case 'page-too-old':
			return lang('admin.error_list.error_type.page_too_old');
		case 'unknown':
			return lang('admin.error_list.error_type.unknown');
		default:
			return $type;
	}
}

function get_time($time) {
	return gmdate("H:i:s", $time);
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.error_list.title'); ?></h4>
					<hr>
					<p class="text-center"><?php echo sprintf(lang('admin.error_list.last_n_errors'), 100); ?></p>
					<div class="text-center pb-3">
						<a href="error_list.php" class="btn btn-primary"><span class="fa fa-redo pe-2"></span><?php echo lang('admin.generic.refresh'); ?></a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="width: 35%;"><?php echo lang('admin.error_list.content_and_episode'); ?></th>
								<th scope="col" style="width: 25%;" class="text-center"><?php echo lang('admin.error_list.error'); ?></th>
								<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.error_list.position'); ?></th>
								<th scope="col" style="width: 10%;" class="text-center"><?php echo lang('admin.error_list.user'); ?></th>
								<th scope="col" style="width: 20%;" class="text-center"><?php echo lang('admin.error_list.date'); ?></th>
								<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE vf.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT s.type series_type,
				v.title,
				GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') fansub_name,
				IF (f.episode_id IS NULL,
					CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IFNULL(et.title, v.title),
						IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
							CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
							CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
						)
					)
				) episode_title,
				re.date,
				re.ip,
				re.user_agent,
				re.type,
				re.text,
				f.id file_id,
				re.position,
				v.id version_id,
				re.user_id,
				re.anon_id
			FROM reported_error re
				LEFT JOIN file f ON re.file_id=f.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN rel_version_fansub vf ON vf.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
				LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id
				LEFT JOIN fansub fa ON vf.fansub_id=fa.id
			$where
			GROUP BY re.id
			ORDER BY date DESC LIMIT 100");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center"><?php echo lang('admin.error_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name'].' - '.$row['title']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></th>
								<td class="align-middle text-center"><span style="cursor: help;" title="<?php echo htmlentities($row['text']); ?>"><?php echo get_error_type($row['type']); ?></span></td>
								<td class="align-middle text-center"><?php echo get_time($row['position']); ?></td>
								<td class="align-middle text-center"><?php echo get_anonymized_username($row['user_id'], $row['anon_id']); ?></td>
								<td class="align-middle text-center"><strong><?php echo $row['date']; ?></strong></td>
								<td class="align-middle text-center text-nowrap">
<?php
		if (!empty($row['file_id'])) {
?>
<a href="version_edit.php?type=<?php echo $row['series_type']; ?>&id=<?php echo $row['version_id']; ?>" title="<?php echo lang('admin.error_list.edit_version.title'); ?>" class="fa fa-edit p-1"></a> 
<?php
			$resultli = query("SELECT * FROM link WHERE file_id=${row['file_id']}");
			$count=0;
			while ($link = mysqli_fetch_assoc($resultli)) {
				if ($count==0) {
					echo "<br>";
				}
				echo '	<a href="'.$link['url'].'" target="_blank" title="'.lang('admin.generic.link.title').'" class="fa fa-external-link-alt p-1 text-success"></a>';
				$count++;
			}
			mysqli_free_result($resultli);
		}
?></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
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
