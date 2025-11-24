<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.fansub_list.header');
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id']) && $_SESSION['admin_level']>=3) {
		log_action("delete-fansub", "Fansub «".query_single("SELECT name FROM fansub WHERE id=".escape($_GET['delete_id']))."» (fansub id: ".$_GET['delete_id'].") deleted");
		query("DELETE FROM fansub WHERE id=".escape($_GET['delete_id']));
		@unlink(STATIC_DIRECTORY.'/images/icons/'.$_GET['delete_id'].'.jpg');
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.fansub_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.fansub_list.name'); ?></th>
								<th scope="col"><?php echo lang('admin.fansub_list.links'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.fansub_list.status'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.fansub_list.news'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.fansub_list.anime_versions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.fansub_list.manga_versions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.fansub_list.liveaction_versions'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT f.*, (SELECT COUNT(*) FROM news WHERE fansub_id=f.id) news, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND vf.fansub_id=f.id) anime_versions, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND vf.fansub_id=f.id) manga_versions, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND vf.fansub_id=f.id) liveaction_versions FROM fansub f".(($_SESSION['admin_level']<3 && !empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) ? ' WHERE f.id='.$_SESSION['fansub_id'] : '')." ORDER BY f.status DESC, f.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="8" class="text-center"><?php echo lang('admin.fansub_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle<?php echo $row['status']==1 ? '' : ' text-muted'; ?>">
<?php

		$links = '';
		if (!empty($row['url'])) {
			$links.='<a href="'.htmlspecialchars($row['url']) . '" target="_blank">'.($row['is_historical']==1 ? lang('admin.fansub_list.web_dead') : lang('admin.fansub_list.web')) . '</a>';
		}
		if (!empty($row['bluesky_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['bluesky_url']).'" target="_blank">'.lang('admin.fansub_list.bluesky').'</a>';
		}
		if (!empty($row['discord_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['discord_url']).'" target="_blank">'.lang('admin.fansub_list.discord').'</a>';
		}
		if (!empty($row['facebook_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['facebook_url']).'" target="_blank">'.lang('admin.fansub_list.facebook').'</a>';
		}
		if (!empty($row['instagram_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['instagram_url']).'" target="_blank">'.lang('admin.fansub_list.instagram').'</a>';
		}
		if (!empty($row['linktree_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['linktree_url']).'" target="_blank">'.lang('admin.fansub_list.linktree').'</a>';
		}
		if (!empty($row['mastodon_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['mastodon_url']).'" target="_blank">'.lang('admin.fansub_list.mastodon').'</a>';
		}
		if (!empty($row['telegram_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['telegram_url']).'" target="_blank">'.lang('admin.fansub_list.telegram').'</a>';
		}
		if (!empty($row['threads_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['threads_url']).'" target="_blank">'.lang('admin.fansub_list.threads').'</a>';
		}
		if (!empty($row['twitter_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['twitter_url']).'" target="_blank">'.lang('admin.fansub_list.twitter').'</a>';
		}
		if (!empty($row['youtube_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['youtube_url']).'" target="_blank">'.lang('admin.fansub_list.youtube').'</a>';
		}
		if (!empty($row['archive_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['archive_url']).'" target="_blank">'.lang('admin.fansub_list.archive_site').'</a>';
		}
		echo $links;
?>
								</td>
								<td class="align-middle text-center<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo $row['status']==1 ? lang('admin.fansub_list.status.active') : lang('admin.fansub_list.status.inactive'); ?></td>
								<td class="align-middle text-center<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo $row['news']; ?></td>
								<td class="align-middle text-center<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo $row['anime_versions']; ?></td>
								<td class="align-middle text-center<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo $row['manga_versions']; ?></td>
								<td class="align-middle text-center<?php echo $row['status']==1 ? '' : ' text-muted'; ?>"><?php echo $row['liveaction_versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="fansub_edit.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a>
<?php
		if (empty($_SESSION['fansub_id']) || (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id']) && $_SESSION['fansub_id']!=$row['id']))  {
?>
<a href="fansub_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.fansub_list.delete_confirm'), $row['name']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a>
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
<?php
	if ($_SESSION['admin_level']>=3) {
?>
					<div class="text-center">
						<a href="fansub_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.fansub_list.create_button'); ?></a>
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
