<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.news_list.header');
$page="news";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_GET['delete_id'])) {
		$todelete_result = query("SELECT n.*, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".escape($_GET['delete_id'])."'");
		if (mysqli_num_rows($todelete_result)>1) {
			crash(lang('admin.error.news_list_delete_same_hash_error'));
		} else if (mysqli_num_rows($todelete_result)==1) {
			$todelete_row = mysqli_fetch_assoc($todelete_result);
			log_action("delete-news", "News «".$todelete_row['title']."» for fansub «".$todelete_row['fansub_name']."» deleted");
			query("DELETE FROM news WHERE MD5(CONCAT(title, date))='".escape($_GET['delete_id'])."'");
		}
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.news_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.news_list.fansub'); ?></th>
								<th scope="col"><?php echo lang('admin.news_list.title_column'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.news_list.date'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE n.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT MD5(CONCAT(n.title, n.date)) id, n.*, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id$where ORDER BY n.date DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center"><?php echo lang('admin.news_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo !empty($row['fansub_name']) ? htmlspecialchars($row['fansub_name']) : sprintf(lang('admin.news_list.internal_news'), MAIN_SITE_NAME); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['title']); ?></td>
								<td class="align-middle text-center"><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></th>
								<td class="align-middle text-center text-nowrap"><a href="news_edit.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="news_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.news_list.delete_confirm'), $row['title']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="news_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.news_list.create_button'); ?></a>
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
