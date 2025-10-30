<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.admin_log.header');
$page="tools";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['filter'])) {
		$filter = escape($_GET['filter']);
	} else {
		$filter = NULL;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.admin_log.title'); ?></h4>
					<hr>
					<p class="text-center"><?php echo sprintf(lang('admin.admin_log.description'), 200); ?></p>
					<div class="d-flex justify-content-center pb-3">
						<select class="form-select" style="width: unset;" onchange="location.href='admin_log.php?filter='+this.value;">
							<option value=""><?php echo lang('admin.admin_log.show_only_relevant'); ?></option>
							<option value="ALL"<?php echo (!empty($_GET['filter']) && $_GET['filter']=='ALL') ? ' selected' : ''; ?>><?php echo lang('admin.admin_log.show_all'); ?></option>
<?php
	$result = query("SELECT action, COUNT(*) count FROM admin_log GROUP BY action ORDER BY action ASC");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<option value="<?php echo $row['action']; ?>"<?php echo (!empty($_GET['filter']) && $_GET['filter']==$row['action']) ? ' selected' : ''; ?>><?php echo sprintf(lang('admin.admin_log.show_only'), $row['action'], $row['count']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
						</select>
					</div>
					<div class="text-center pb-3">
						<a href="admin_log.php<?php echo (!empty($filter) ? "?filter=$filter" : '') ?>" class="btn btn-primary"><span class="fa fa-redo pe-2 fa-width-auto"></span><?php echo lang('admin.generic.refresh'); ?></a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="width: 18%;"><?php echo lang('admin.admin_log.datetime'); ?></th>
								<th scope="col" style="width: 12%;"><?php echo lang('admin.admin_log.user'); ?></th>
								<th scope="col" style="width: 18%;"><?php echo lang('admin.admin_log.action'); ?></th>
								<th scope="col"><?php echo lang('admin.admin_log.text'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT * FROM admin_log".(!empty($filter) ? ( $filter=='ALL' ? '' : " WHERE action='$filter'") : " WHERE action NOT IN ('fetch-news-finished', 'fetch-news-started', 'cron-updater-finished', 'cron-updater-started', 'lost-views-updater-finished', 'lost-views-updater-started', 'cron-scores-finished', 'cron-scores-started', 'cron-recommendations-started', 'cron-recommendations-finished')")." ORDER BY id DESC LIMIT 200");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center"><?php echo lang('admin.admin_log.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['author']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['action']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars($row['text']); ?></td>
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
