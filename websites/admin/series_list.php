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
		$header_title=lang('admin.series_list.header.anime');
		$main_title_string=lang('admin.series_list.title.anime');
		$delete_confirm_string=lang('admin.series_list.delete_confirm.anime');
		$create_button_string=lang('admin.series_list.create_button.anime');
		$empty_list_string=lang('admin.series_list.empty.anime');
	break;
	case 'manga':
		$page="manga";
		$header_title=lang('admin.series_list.header.manga');
		$main_title_string=lang('admin.series_list.title.manga');
		$delete_confirm_string=lang('admin.series_list.delete_confirm.manga');
		$create_button_string=lang('admin.series_list.create_button.manga');
		$empty_list_string=lang('admin.series_list.empty.manga');
	break;
	case 'liveaction':
		$page="liveaction";
		$header_title=lang('admin.series_list.header.liveaction');
		$main_title_string=lang('admin.series_list.title.liveaction');
		$delete_confirm_string=lang('admin.series_list.delete_confirm.liveaction');
		$create_button_string=lang('admin.series_list.create_button.liveaction');
		$empty_list_string=lang('admin.series_list.empty.liveaction');
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-series", "Series «".query_single("SELECT name FROM series WHERE id=".escape($_GET['delete_id']))."» (series id: ".$_GET['delete_id'].") deleted");
		query("DELETE FROM rel_series_genre WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM episode WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM version WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM series WHERE id=".escape($_GET['delete_id']));
		@unlink(STATIC_DIRECTORY.'/images/series/'.$_GET['delete_id'].'.jpg');
		@unlink(STATIC_DIRECTORY.'/social/series_'.$_GET['delete_id'].'.jpg');
		//Cascaded deletions: file, link, rel_version_fansub
		//Views will NOT be removed in order to keep consistent stats history
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo $main_title_string; ?></h4>
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
								<th scope="col"><?php echo lang('admin.series_list.title'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_list.type'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_list.divisions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_list.episodes'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_list.versions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,(SELECT GROUP_CONCAT(DISTINCT d.name ORDER BY d.number ASC, d.name ASC SEPARATOR '|||') FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL AND (d.external_id<>s.external_id".($type!='manga' ? ' OR (d.external_id IS NOT NULL AND d.name<>s.name COLLATE utf8mb4_bin)' : '').")) division_titles,(SELECT GROUP_CONCAT(DISTINCT v.title ORDER BY v.title ASC SEPARATOR ', ') FROM version v WHERE v.series_id=s.id AND v.title<>s.name COLLATE utf8mb4_bin) version_titles,(SELECT COUNT(DISTINCT v.id) FROM version v WHERE v.series_id=s.id) versions,(SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=1) divisions, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=0) fake_divisions FROM series s WHERE s.type='$type' GROUP BY s.id ORDER BY s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center"><?php echo $empty_list_string; ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		$divisions = !empty($row['division_titles']) ? explode('|||', $row['division_titles']) : array();
		$divisions_escaped = array();
		foreach ($divisions as $division) {
			array_push($divisions_escaped, htmlspecialchars($division));
		}
?>
							<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['has_licensed_parts']>1 ? ' licensed' : ''; ?>">
								<th scope="row" class="align-middle<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo htmlspecialchars($row['name']); ?><?php echo !empty($divisions_escaped) ? '<i><br>&nbsp;&nbsp;&nbsp┗ '.implode('<br>&nbsp;&nbsp;&nbsp;┗ ', $divisions_escaped).'</i>' : ''; ?><?php echo !empty($row['version_titles']) ? '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i style="font-weight: normal;" class="text-muted">('.htmlspecialchars($row['version_titles']).')</i>' : ''; ?></th>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo get_subtype_name($row['subtype']); ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['divisions']; ?><?php echo $row['fake_divisions']>0 ? '<small>+'.$row['fake_divisions'].'</small>' : ''; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['number_of_episodes']; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="version_edit.php?type=<?php echo $type; ?>&series_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.series_list.create_version.title'); ?>" class="fa fa-plus p-1 text-success"></a> <a href="series_edit.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="series_list.php?type=<?php echo $type; ?>&delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf($delete_confirm_string, $row['name']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_edit.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo $create_button_string; ?></a>
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
