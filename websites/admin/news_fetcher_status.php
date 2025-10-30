<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.news_fetcher_status.header');
$page="tools";
include(__DIR__.'/header.inc.php');

//Helper functions to show better strings for possible values on the DB

function show_fetch_type($fetch_type){
	switch ($fetch_type){
		case 'periodic':
			return lang('admin.generic.fetch_type.periodic.nowrap');
		case 'onrequest':
			return lang('admin.generic.fetch_type.by_request.nowrap');
		case 'onetime_retired':
			return lang('admin.generic.fetch_type.single_retired.nowrap');
		case 'onetime_inactive':
			return lang('admin.generic.fetch_type.single_inactive.nowrap');
		default:
			return $fetch_type;
	}
}

function show_status($status){
	switch ($status){
		case 'idle':
			return lang('admin.generic.fetch_status.resting.nowrap');
		case 'fetching':
			return lang('admin.generic.fetch_status.obtaining.nowrap');
		default:
			return $status;
	}
}

function show_last_result($last_result, $last_increment, $fetch_type){
	$ok_color = '#008800';
	$ko_color = '#880000';
	
	if ($fetch_type!='periodic' && $fetch_type!='onrequest') {
		$ok_color = '#88BB88';
		$ko_color = '#BB8888';
	}

	switch ($last_result){
		case 'ok':
			if ($last_increment===NULL){
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;'.lang('admin.generic.fetch_result.ok.nowrap').'</span>';
			}
			else if ($last_increment==0){
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;'.lang('admin.generic.fetch_result.ok.nowrap').'&nbsp;(±0)</span>';
			}
			else if ($last_increment>0){
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;'.lang('admin.generic.fetch_result.ok.nowrap').'&nbsp;(+'.$last_increment.')</span>';
			}
			else{
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;'.lang('admin.generic.fetch_result.ok.nowrap').'&nbsp;('.$last_increment.')</span>';
			}
		case 'error_mysql':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;'.lang('admin.generic.fetch_result.error_db.nowrap').'</span>';
		case 'error_empty':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;'.lang('admin.generic.fetch_result.error_empty.nowrap').'</span>';
		case 'error_connect':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;'.lang('admin.generic.fetch_result.error_connect.nowrap').'</span>';
		case 'error_invalid_method':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;'.lang('admin.generic.fetch_result.error_unknown.nowrap').'</span>';
		case '':
			return "-";
		default:
			return $last_result;
	}
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.news_fetcher_status.title'); ?></h4>
					<hr>
					<p class="text-center"><?php echo lang('admin.news_fetcher_status.explanation'); ?></p>
					<div class="text-center pb-3">
						<a href="news_fetcher_status.php" class="btn btn-primary"><span class="fa fa-redo pe-2 fa-width-auto"></span><?php echo lang('admin.generic.refresh'); ?></a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="width: 18%;"><?php echo lang('admin.news_fetcher_status.fansub_and_url'); ?></th>
								<th scope="col" style="width: 12%;" class="text-center"><?php echo lang('admin.news_fetcher_status.frequency'); ?></th>
								<th scope="col" style="width: 12%;" class="text-center"><?php echo lang('admin.news_fetcher_status.status'); ?></th>
								<th scope="col" style="width: 12%;" class="text-center"><?php echo lang('admin.news_fetcher_status.last_connection'); ?></th>
								<th scope="col" style="width: 12%;" class="text-center"><?php echo lang('admin.news_fetcher_status.last_result'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE fe.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT fe.*,fa.name FROM news_fetcher fe LEFT JOIN fansub fa ON fe.fansub_id=fa.id$where ORDER BY fetch_type DESC, fa.name ASC, fe.url ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center"><?php echo lang('admin.news_fetcher_status.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo htmlspecialchars($row['name']).'<br><small>'.htmlspecialchars($row['url']).'</small>'; ?></th>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo show_fetch_type($row['fetch_type']); ?></td>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo show_status($row['status']); ?></td>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo ($row['last_fetch_date']!=NULL ? relative_time(strtotime($row['last_fetch_date'])) : lang('admin.generic.date_never')); ?></td>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><strong><?php echo show_last_result($row['last_fetch_result'], $row['last_fetch_increment'], $row['fetch_type']); ?></strong></td>
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
