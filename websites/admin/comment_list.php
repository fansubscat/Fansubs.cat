<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.comment_list.header');
$page="analytics";

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_GET['delete_id']) && $_SESSION['admin_level']>=3) {
		if (!DISABLE_COMMUNITY) {
			delete_comment_from_community($_GET['delete_id']);
		}
		log_action("delete-comment", "Comment with id ".$_GET['delete_id']." and its replies (if any) was deleted");
		query("DELETE FROM comment WHERE reply_to_comment_id=".escape($_GET['delete_id']));
		query("DELETE FROM comment WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']=lang('admin.generic.delete_successful');
		if (!empty($_GET['source_version_id']) && !empty($_GET['source_type'])) {
			header('Location: version_stats.php?type='.$_GET['source_type'].'&id='.$_GET['source_version_id']);
			die();
		}
	}

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
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true"><?php echo sprintf(lang('admin.comment_list.comments_for_fansub'), $fansub['name']); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false"><?php echo lang('admin.comment_list.all_comments'); ?></a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.comment_list.last_n_comments'), $limit); ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 20%;"><?php echo lang('admin.comment_list.content'); ?></th>
												<th scope="col" style="width: 10%;"><?php echo lang('admin.comment_list.user'); ?></th>
												<th scope="col" style="width: 50%;"><?php echo lang('admin.comment_list.comment'); ?></th>
												<th scope="col" style="width: 10%;" class="text-center"><?php echo lang('admin.comment_list.date'); ?></th>
												<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.comment_list.spoiler'); ?></th>
												<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.comment_list.replied'); ?></th>
												<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, v.title, u.username, u.status, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user' ORDER BY c.created DESC LIMIT $limit");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="7" class="text-center"><?php echo lang('admin.comment_list.empty'); ?></td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['status']==1 ? 'shadowbanned' : ''; ?>">
												<td class="align-middle"><?php echo '<b>'.htmlspecialchars($row['fansubs']).' - '.htmlspecialchars($row['title']).'</b>'; ?></td>
												<td class="align-middle"><?php echo !empty($row['username']) ? htmlentities($row['username']) : lang('admin.generic.deleted_user'); ?></td>
												<td class="align-middle"><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>'.lang('admin.generic.deleted_comment').'</i>'; ?></td>
												<td class="align-middle text-center"><?php echo $row['created']; ?></td>
												<td class="align-middle text-center"><?php echo $row['has_spoilers']==1 ? lang('admin.generic.yes') : lang('admin.generic.no'); ?></td>
												<td class="align-middle text-center"><?php echo $row['last_replied']!=$row['created'] ? lang('admin.generic.yes') : lang('admin.generic.no'); ?></td>
												<td class="align-middle text-center text-nowrap">
													<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.reply.title'); ?>" class="fa fa-reply p-1"></a>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
													<a href="comment_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(lang('admin.comment_list.delete_confirm'))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
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
									<p class="text-center text-muted small"><?php echo lang('admin.comment_list.explanation'); ?></p>
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
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.comment_list.last_n_comments'), $limit); ?></h4>
								<hr>
								<div class="row">
									<table class="table table-hover table-striped">
										<thead class="table-dark">
											<tr>
												<th scope="col" style="width: 20%;"><?php echo lang('admin.comment_list.content'); ?></th>
												<th scope="col" style="width: 10%;"><?php echo lang('admin.comment_list.user'); ?></th>
												<th scope="col" style="width: 50%;"><?php echo lang('admin.comment_list.comment'); ?></th>
												<th scope="col" style="width: 10%;" class="text-center"><?php echo lang('admin.comment_list.date'); ?></th>
												<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.comment_list.spoiler'); ?></th>
												<th scope="col" style="width: 5%;" class="text-center"><?php echo lang('admin.comment_list.replied'); ?></th>
												<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
											</tr>
										</thead>
										<tbody>
<?php
$result = query("SELECT c.*, v.title, u.username, u.status, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user' AND v.id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=${fansub['id']}) ORDER BY c.created DESC LIMIT $limit");
if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="7" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
}
while ($row = mysqli_fetch_assoc($result)) {
?>
											<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['status']==1 ? 'shadowbanned' : ''; ?>">
												<td class="align-middle"><?php echo '<b>'.htmlspecialchars($row['fansubs']).' - '.htmlspecialchars($row['title']).'</b>'; ?></td>
												<td class="align-middle"><?php echo !empty($row['username']) ? htmlentities($row['username']) : lang('admin.generic.deleted_user'); ?></td>
												<td class="align-middle"><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>'.lang('admin.generic.deleted_comment').'</i>'; ?></td>
												<td class="align-middle text-center"><?php echo $row['created']; ?></td>
												<td class="align-middle text-center"><?php echo $row['has_spoilers']==1 ? lang('admin.generic.yes') : lang('admin.generic.no'); ?></td>
												<td class="align-middle text-center"><?php echo $row['last_replied']!=$row['created'] ? lang('admin.generic.yes') : lang('admin.generic.no'); ?></td>
												<td class="align-middle text-center text-nowrap">
													<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.reply.title'); ?>" class="fa fa-reply p-1"></a>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
													<a href="comment_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(lang('admin.comment_list.delete_confirm'))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
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
									<p class="text-center text-muted small"><?php echo lang('admin.comment_list.explanation'); ?></p>
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
