<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.index.header');
$page="main";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
<div class="container d-flex justify-content-center p-4">
	<div class="card w-100">
		<div class="position-absolute align-self-end">
			<a id="welcome-refresh" href="/" class="btn btn-tertiary fa fa-redo p-2 fa-width-auto<?php echo empty($_SESSION['default_view']) || $_SESSION['default_view']==1 ? ' d-none' : ''; ?>" title="<?php echo lang('admin.generic.refresh'); ?>"></a>
			<button class="btn btn-tertiary fa fa-right-left p-2 fa-width-auto" title="<?php echo lang('admin.index.change_view'); ?>" onclick="toggleWelcomeView();"></button>
		</div>
		<article id="welcome-view" class="card-body<?php echo !empty($_SESSION['default_view']) && $_SESSION['default_view']==2 ? ' d-none' : ''; ?>">
			<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.index.intro'); ?></h4>
			<hr>
			<p class="text-center"><?php echo sprintf(lang('admin.index.welcome'), MAIN_SITE_NAME); ?></p>
			<p class="text-center"><?php echo lang('admin.index.explanation_content'); ?></p>
<?php
	if (!DISABLE_NEWS) {
?>
			<p class="text-center"><?php echo lang('admin.index.explanation_news'); ?></p>
<?php
	}
?>
			<p class="text-center"><?php echo lang('admin.index.explanation_analysis'); ?></p>
			<p class="text-center"><?php echo lang('admin.index.explanation_doubts'); ?></p>
			<p class="text-center"><?php echo lang('admin.index.explanation_toggle'); ?></p>
<?php
	if ($_SESSION['admin_level']<2) {
?>
			<p class="text-center alert alert-warning"><?php echo lang('admin.index.limited_permissions'); ?></p>
<?php
	}
?>
			<h4 class="card-title text-center mb-4 mt-4"><?php echo lang('admin.index.common_actions'); ?></h4>
			<hr>
			<div class="container">
				<div class="row">
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2"><?php echo lang('admin.index.anime'); ?></h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.anime.add'); ?></a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.generic.add_version'); ?></a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=anime" class="btn btn-primary"><span class="fa fa-edit pe-2"></span><?php echo lang('admin.index.generic.edit_version'); ?></a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2"><?php echo lang('admin.index.manga'); ?></h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.manga.add'); ?></a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.generic.add_version'); ?></a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=manga" class="btn btn-primary"><span class="fa fa-edit pe-2"></span><?php echo lang('admin.index.generic.edit_version'); ?></a>
						</div>
					</div>
<?php
	if (!DISABLE_LIVE_ACTION) {
?>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2"><?php echo lang('admin.index.liveaction'); ?></h5>
<?php
		if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.liveaction.add'); ?></a> 
						</div>
<?php
		}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.index.generic.add_version'); ?></a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=liveaction" class="btn btn-primary"><span class="fa fa-edit pe-2"></span><?php echo lang('admin.index.generic.edit_version'); ?></a>
						</div>
					</div>
<?php
	}
?>
				</div>
			</div>
		</article>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$extra_where = ' AND EXISTS (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$extra_where = '';
	}
?>
		<article id="latest-view" class="card-body<?php echo empty($_SESSION['default_view']) || $_SESSION['default_view']==1 ? ' d-none' : ''; ?>">
			<div class="row">
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.index.views_in_progress'); ?></h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
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
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)."$extra_where
		ORDER BY ps.created DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="1" class="text-center"><?php echo lang('admin.index.views_in_progress.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<td scope="row" class="align-middle"><b><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['title']); ?></b> • <?php echo sprintf(lang('admin.index.views_in_progress.percent_completed'), str_replace('.',',',min(100,round($row['progress'],1)))); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.index.latest_comments'); ?></h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT c.*, UNIX_TIMESTAMP(c.created) created_timestamp, v.title, u.username, u.status, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user'$extra_where ORDER BY c.created DESC LIMIT 5");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="2" class="text-center"><?php echo lang('admin.index.latest_comments.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['status']==1 ? 'shadowbanned' : ''; ?>">
								<td class="align-middle"><b><?php echo !empty($row['username']) ? htmlentities($row['username']) : lang('admin.generic.deleted_user'); ?></b> a <?php echo '<b>'.htmlspecialchars($row['title']).'</b>'.(empty($_SESSION['fansub_id']) ? sprintf(lang('admin.index.latest_comments.by_fansub'), htmlspecialchars($row['fansubs'])) : '').' • '.get_relative_date($row['created_timestamp']); ?><?php echo $row['last_replied']!=$row['created'] ? ' • <b>'.lang('admin.comment_list.replied').'</b>' : ''; ?><br><small><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>'.lang('admin.generic.deleted_comment').'</i>'; ?></small></td>
								<td class="align-middle text-center">
									<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.reply.title'); ?>" class="fa fa-reply p-1"></a>
								</td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.index.latest_views'); ?></h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT IFNULL(v.title, '".lang('admin.query.link_deleted')."') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
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
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE ps.view_counted IS NOT NULL$extra_where
		ORDER BY ps.view_counted DESC LIMIT 5");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="1" class="text-center"><?php echo lang('admin.index.latest_views.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<td scope="row" class="align-middle"><b><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['title']); ?></b> • <?php echo get_relative_date($row['view_counted']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.index.latest_edits'); ?></h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, v.title version_title, s.rating series_rating, s.type, s.name series_name, v.*, COUNT(DISTINCT fi.id) files, (SELECT COUNT(*) FROM user_version_rating WHERE rating=1 AND version_id=v.id) good_ratings, (SELECT COUNT(*) FROM user_version_rating WHERE rating=-1 AND version_id=v.id) bad_ratings, (SELECT COUNT(*) FROM comment WHERE type='user' AND version_id=v.id) num_comments, s.rating FROM version v LEFT JOIN file fi ON v.id=fi.version_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE 1$extra_where GROUP BY v.id ORDER BY v.updated DESC LIMIT 8");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="3" class="text-center"><?php echo lang('admin.index.latest_edits.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		$link_url=get_public_site_url($row['type'], $row['slug'], $row['series_rating']=='XXX');
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<th style="width: 70%;" scope="row" class="align-middle<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['version_title']); ?></th>
								<td class="align-middle text-center text-nowrap<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['good_ratings']>0 ? $row['good_ratings'] : '0'; ?> <span title="<?php echo lang('admin.index.latest_edits.good_ratings'); ?>" class="fa far fa-thumbs-up"></span>&nbsp;&nbsp;<?php echo $row['bad_ratings']>0 ? $row['bad_ratings'] : '0'; ?> <span title="<?php echo lang('admin.index.latest_edits.bad_ratings'); ?>" class="fa far fa-thumbs-down"></span>&nbsp;&nbsp;<?php echo $row['num_comments']>0 ? $row['num_comments'] : '0'; ?> <span title="<?php echo lang('admin.index.latest_edits.comments'); ?>" class="fa far fa-comment"></span></td>
								<td class="align-middle text-center text-nowrap"><a href="<?php echo $link_url; ?>" title="<?php echo lang('admin.index.latest_edits.public_view'); ?>" target="_blank" class="fa fa-up-right-from-square p-1 text-warning"></a> <a href="version_stats.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.index.latest_edits.stats_and_comments'); ?>" class="fa fa-chart-line p-1 text-success"></a> <a href="version_edit.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a></td>
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
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
