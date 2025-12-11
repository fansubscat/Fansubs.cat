<?php
require_once(__DIR__.'/../common/initialization.inc.php');

$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$page="anime";
		$header_title=lang('admin.version_list.header.anime');
		$title_string=lang('admin.version_list.title.anime');
		$name_column_string=lang('admin.version_list.name_column.anime');
	break;
	case 'manga':
		$page="manga";
		$header_title=lang('admin.version_list.header.manga');
		$title_string=lang('admin.version_list.title.manga');
		$name_column_string=lang('admin.version_list.name_column.manga');
	break;
	case 'liveaction':
		$page="liveaction";
		$header_title=lang('admin.version_list.header.liveaction');
		$title_string=lang('admin.version_list.title.liveaction');
		$name_column_string=lang('admin.version_list.name_column.liveaction');
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		if (query_single("SELECT COUNT(*) cnt FROM file f WHERE version_id=".escape($_GET['delete_id']))==0 || $_SESSION['admin_level']>=4) {
			log_action("delete-version", "Version of «".query_single("SELECT s.name FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.id=".escape($_GET['delete_id']))."» (version id: ".$_GET['delete_id'].") deleted");
			query("DELETE FROM file WHERE version_id=".escape($_GET['delete_id']));
			query("DELETE FROM remote_folder WHERE version_id=".escape($_GET['delete_id']));
			query("DELETE FROM episode_title WHERE version_id=".escape($_GET['delete_id']));
			query("DELETE FROM rel_version_fansub WHERE version_id=".escape($_GET['delete_id']));
			query("DELETE FROM version_division WHERE version_id=".escape($_GET['delete_id']));
			query("DELETE FROM version WHERE id=".escape($_GET['delete_id']));
			@unlink(STATIC_DIRECTORY.'/images/covers/version_'.$_GET['delete_id'].'.jpg');
			@unlink(STATIC_DIRECTORY.'/images/featured/version_'.$_GET['delete_id'].'.jpg');
			@unlink(STATIC_DIRECTORY.'/social/version_'.$_GET['delete_id'].'.jpg');
			//Views will NOT be removed in order to keep consistent stats history
			$_SESSION['message']=lang('admin.generic.delete_successful');
		}
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo $title_string; ?></h4>
					<hr>

<?php
	if (!empty($_SESSION['message'])) {
?>
					<p class="alert alert-success text-center"><?php echo $_SESSION['message']; ?></p>
<?php
		$_SESSION['message']=NULL;
	}
?>

					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col"><?php echo lang('admin.version_list.fansub'); ?></th>
								<th scope="col"><?php echo $name_column_string; ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.version_list.status'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.version_list.files'); ?></th>
								<th class="text-center" scope="col"><span title="<?php echo lang('admin.version_list.recommendable'); ?>" class="fa fa-star"></span></th>
								<th class="text-center" scope="col"><span title="<?php echo lang('admin.version_list.positive_ratings'); ?>" class="fa fa-thumbs-up"></span></th>
								<th class="text-center" scope="col"><span title="<?php echo lang('admin.version_list.negative_ratings'); ?>" class="fa fa-thumbs-down"></span></th>
								<th class="text-center" scope="col"><span title="<?php echo lang('admin.version_list.user_comments'); ?>" class="fa fa-comment"></span></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$extra_where = ' AND EXISTS (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$extra_where = '';
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, v.title version_title, s.rating series_rating, s.name series_name, s.has_licensed_parts, v.*, COUNT(DISTINCT fi.id) files, (SELECT COUNT(*) FROM user_version_rating WHERE rating=1 AND version_id=v.id) good_ratings, (SELECT COUNT(*) FROM user_version_rating WHERE rating=-1 AND version_id=v.id) bad_ratings, (SELECT COUNT(*) FROM comment WHERE type='user' AND version_id=v.id) num_comments, s.rating FROM version v LEFT JOIN file fi ON v.id=fi.version_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='$type'$extra_where GROUP BY v.id ORDER BY fansub_name, v.title");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="9" class="text-center"><?php echo lang('admin.version_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		$link_url=get_public_site_url($type, $row['slug'], $row['series_rating']=='XXX');
?>
							<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
								<th scope="row" class="align-middle<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo htmlspecialchars($row['fansub_name']); ?></th>
								<td class="align-middle<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><b><?php echo htmlspecialchars($row['version_title']); ?></b><?php echo $row['version_title']!=$row['series_name'] ? '<br><i style="font-weight: normal;" class="text-muted small">('.htmlspecialchars($row['series_name']).')</i>' : ''; ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo get_status_description_short($row['status']); ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['files']; ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['featurable_status']==3 ? lang('admin.version_list.recommendable.always_season') : ($row['featurable_status']==2 ? lang('admin.version_list.recommendable.always_special') : ($row['featurable_status']==1 ? lang('admin.version_list.recommendable.randomly') : lang('admin.version_list.recommendable.never'))); ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['good_ratings']>0 ? $row['good_ratings'] : '-'; ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['bad_ratings']>0 ? $row['bad_ratings'] : '-'; ?></td>
								<td class="align-middle text-center<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['num_comments']>0 ? $row['num_comments'] : '-'; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="<?php echo $link_url; ?>" title="<?php echo lang('admin.version_list.public_page.title'); ?>" target="_blank" class="fa fa-up-right-from-square p-1 text-warning"></a> <a href="version_links.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.version_list.links.title'); ?>" class="fa fa-link p-1 text-info"></a> <a href="version_stats.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.version_list.stats.title'); ?>" class="fa fa-chart-line p-1 text-success"></a> <a href="version_edit.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a><?php if ($row['files']==0 || $_SESSION['admin_level']>=4) { ?> <a href="version_list.php?type=<?php echo $type; ?>&delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.version_list.delete_confirm'), $row['series_name'], $row['fansub_name']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a><?php } else { ?> <span class="fa fa-trash p-1 text-danger opacity-25"></span><?php } ?></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_choose.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.version_list.create_button'); ?></a>
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
