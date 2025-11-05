<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.link_list.header');
$page="other";
include(__DIR__.'/header.inc.php');

function get_category_name_by_id($id) {
	switch ($id) {
		case 'featured':
			return lang('admin.generic.link_category.featured');
		case 'blogs':
			return lang('admin.generic.link_category.blogs');
		case 'catalogs':
			return lang('admin.generic.link_category.catalogs');
		case 'art':
			return lang('admin.generic.link_category.art');
		case 'forums':
			return lang('admin.generic.link_category.forums');
		case 'culture':
			return lang('admin.generic.link_category.culture');
		case 'creators':
			return lang('admin.generic.link_category.creators');
		case 'dubbing':
			return lang('admin.generic.link_category.dubbing');
		case 'music':
			return lang('admin.generic.link_category.music');
		case 'nostalgia':
			return lang('admin.generic.link_category.nostalgia');
		case 'podcasts':
			return lang('admin.generic.link_category.podcasts');
		case 'preservation':
			return lang('admin.generic.link_category.preservation');
		case 'subtitles':
			return lang('admin.generic.link_category.subtitles');
		case 'others':
			return lang('admin.generic.link_category.others');
		default:
			return lang('admin.generic.link_category.unknown');
	}
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-link", "Link «".query_single("SELECT name FROM external_link WHERE id=".escape($_GET['delete_id']))."» (link id: ".$_GET['delete_id'].") deleted");
		query("DELETE FROM external_link WHERE id=".escape($_GET['delete_id']));
		@unlink(STATIC_DIRECTORY.'/images/communities/'.$_GET['delete_id'].'.png');
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.link_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.link_list.name'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.link_list.category'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT l.* FROM external_link l ORDER BY l.category='featured' DESC, l.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="8" class="text-center"><?php echo lang('admin.link_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo get_category_name_by_id($row['category']); ?></td>
								<td class="align-middle text-center text-nowrap"><a href="link_edit.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="link_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.link_list.delete_confirm'), $row['name']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="link_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.link_list.create_button'); ?></a>
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
