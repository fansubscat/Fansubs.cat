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
		$header_title=lang('admin.series_choose.header.anime');
		$type_title=lang('admin.series_choose.title.anime');
		$empty_string=lang('admin.series_choose.empty.anime');
		$create_string=lang('admin.series_choose.create_button.anime');
	break;
	case 'manga':
		$page="manga";
		$header_title=lang('admin.series_choose.header.manga');
		$type_title=lang('admin.series_choose.title.manga');
		$empty_string=lang('admin.series_choose.empty.manga');
		$create_string=lang('admin.series_choose.create_button.manga');
	break;
	case 'liveaction':
		$page="liveaction";
		$header_title=lang('admin.series_choose.header.liveaction');
		$type_title=lang('admin.series_choose.title.liveaction');
		$empty_string=lang('admin.series_choose.empty.liveaction');
		$create_string=lang('admin.series_choose.create_button.liveaction');
	break;
}

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo $type_title; ?></h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col"><?php echo lang('admin.series_choose.title'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_choose.type'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_choose.divisions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_choose.episodes'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.series_choose.versions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.action'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,(SELECT GROUP_CONCAT(DISTINCT d.name ORDER BY d.number ASC, d.name ASC SEPARATOR '|||') FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL AND (d.external_id<>s.external_id".($type!='manga' ? ' OR (d.external_id IS NOT NULL AND d.name<>s.name COLLATE utf8mb4_bin)' : '').")) division_titles,(SELECT GROUP_CONCAT(DISTINCT v.title ORDER BY v.title ASC SEPARATOR ', ') FROM version v WHERE v.series_id=s.id AND v.title<>s.name COLLATE utf8mb4_bin) version_titles,(SELECT COUNT(DISTINCT v.id) FROM version v WHERE v.series_id=s.id) versions,(SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=1) divisions, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=0) fake_divisions FROM series s WHERE s.type='$type' GROUP BY s.id ORDER BY versions=0 DESC, s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center"><?php echo $empty_string; ?></td>
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
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<th scope="row" class="align-middle<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo htmlspecialchars($row['name']); ?><?php echo !empty($divisions_escaped) ? '<i><br>&nbsp;&nbsp;&nbsp┗ '.implode('<br>&nbsp;&nbsp;&nbsp;┗ ', $divisions_escaped).'</i>' : ''; ?></th>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo get_subtype_name($row['subtype']); ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['divisions']; ?><?php echo $row['fake_divisions']>0 ? '<small>+'.$row['fake_divisions'].'</small>' : ''; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['number_of_episodes']; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="version_edit.php?type=<?php echo $type; ?>&series_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.series_choose.create_version.title'); ?>" class="fa fa-plus-square p-1 text-success"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<div class="text-center">
						<a href="series_edit.php?type=<?php echo $type; ?>" class="btn btn-primary"><?php echo $create_string; ?></a>
					</div>
<?php
	}
?>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
