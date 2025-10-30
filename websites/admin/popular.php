<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.popular.header');
$page="analytics";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_SESSION['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_SESSION['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	} else if ($_SESSION['admin_level']>=3 && !empty($_GET['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_GET['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	}
	
	//Prepare list of months
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date(STARTING_DATE)."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date(STARTING_DATE)."+$i months"))]=get_clean_month_name(strftime("%B %Y", strtotime(date(STARTING_DATE)."+$i months")));
		$i++;
	}
	$months = array_reverse($months, TRUE);

	$selected_month = date('Y-m');
	$first_month = $selected_month;
	$last_month = $selected_month;
	$selected_year = FALSE;
	$selected_all = FALSE;
	if (isset($_GET['month'])) {
		if (preg_match('/^\d\d\d\d$/', $_GET['month'])) {
			$selected_year = $_GET['month'];
			$first_month = $_GET['month'].'-01';
			$last_month = $_GET['month'].'-12';
		} else if (preg_match('/^\d\d\d\d-\d\d$/', $_GET['month'])) {
			$selected_month = $_GET['month'];
			$first_month = $selected_month;
			$last_month = $selected_month;
		} else if ($_GET['month']=='ALL') {
			$selected_all = TRUE;
			$first_month = date('Y-m', date_timestamp_get(date_create_from_format('Y-m-d', STARTING_DATE)));
			$last_month = date('Y-m');
		}
	}
	if (isset($_GET['amount']) && preg_match('/\d+/', $_GET['amount'])) {
		$amount = $_GET['amount'];
	} else {
		$amount = 10;
	}
	if (isset($_GET['type']) && $_GET['type']=='total_length') {
		$type = 'total_length';
	} else {
		$type = 'max_views';
	}
	$hide_hentai = FALSE;
	if (isset($_GET['hide_hentai']) && $_GET['hide_hentai']==1) {
		$hide_hentai = TRUE;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.popular.title'); ?></h4>
					<hr>
					<p class="text-center"><?php echo sprintf(lang('admin.popular.description'), MAIN_SITE_NAME); ?></p>

					<div class="d-flex justify-content-center">
						<div class="mb-3 p-3 mb-0">
							<label for="month"><?php echo lang('admin.popular.timespan'); ?></label>
							<select id="month" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val()<?php echo !empty($fansub) ? "+'&amp;fansub_id=".$fansub['id']."'" : ''; ?>;">
								<option value="ALL"<?php echo ($selected_all) ? ' selected' : ''; ?> style="font-weight: bold;"><?php echo sprintf(lang('admin.popular.total_years'), STARTING_YEAR, date('Y')); ?></option>
<?php
	$current_year=0;
	foreach ($months as $month => $values) {
		if (explode('-',$month)[0]!=$current_year) {
			$current_year=explode('-',$month)[0];
?>
								<option value="<?php echo $current_year; ?>"<?php echo (!$selected_all && $selected_year==$current_year) ? ' selected' : ''; ?> style="font-weight: bold;"><?php echo sprintf(lang('admin.popular.full_year'), $current_year); ?></option>
<?php
		}
?>
								<option value="<?php echo $month; ?>"<?php echo (!$selected_all && empty($selected_year) && $selected_month==$month) ? ' selected' : ''; ?>><?php echo $values; ?></option>
<?php
	}
?>
							</select>
						</div>
						<div class="mb-3 p-3 mb-0">
							<label for="amount"><?php echo lang('admin.popular.number_of_elements'); ?></label>
							<select id="amount" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val()<?php echo !empty($fansub) ? "+'&amp;fansub_id=".$fansub['id']."'" : ''; ?>;">
								<option value="10"<?php echo ($amount==10) ? ' selected' : ''; ?>>10</option>
								<option value="25"<?php echo ($amount==25) ? ' selected' : ''; ?>>25</option>
								<option value="50"<?php echo ($amount==50) ? ' selected' : ''; ?>>50</option>
							</select>
						</div>
						<div class="mb-3 p-3 mb-0">
							<label for="type"><?php echo lang('admin.popular.sort_by'); ?></label>
							<select id="type" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val()<?php echo !empty($fansub) ? "+'&amp;fansub_id=".$fansub['id']."'" : ''; ?>;">
								<option value="max_views"<?php echo ($type=='max_views') ? ' selected' : ''; ?>><?php echo lang('admin.popular.sort_by.views'); ?></option>
								<option value="total_length"<?php echo ($type=='total_length') ? ' selected' : ''; ?>><?php echo lang('admin.popular.sort_by.time_pages'); ?></option>
							</select>
						</div>
					</div>
				</article>
			</div>
		</div>
		<div class="container justify-content-center p-4">
			<ul class="nav nav-tabs" id="stats_tabs" role="tablist">
<?php
	if (!empty($fansub)) {
?>
				<li class="nav-item">
					<a class="nav-link active" id="fansub-tab" data-bs-toggle="tab" href="#fansub" role="tab" aria-controls="fansub" aria-selected="true"><?php echo sprintf(lang('admin.popular.contents_by_fansub'), get_fansub_preposition_name($fansub['name'])); ?></a>
				</li>
<?php
	}
?>
				<li class="nav-item">
					<a class="nav-link<?php echo empty($fansub) ? ' active' : ''; ?>" id="totals-tab" data-bs-toggle="tab" href="#totals" role="tab" aria-controls="totals" aria-selected="false"><?php echo lang('admin.popular.all_contents'); ?></a>
				</li>
			</ul>
			<div class="tab-content" id="stats_tabs_content" style="border: 1px solid #dee2e6; border-top: none;">
				<div class="tab-pane fade<?php echo empty($fansub) ? ' show active' : ''; ?>" id="totals" role="tabpanel" aria-labelledby="totals-tab">
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_animes'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.anime'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='anime' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

	if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center">- No hi ha cap anime vist -</td>
										</tr>
<?php
	}
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row[$type]!=$prev_views) {
			$prev_views = $row[$type];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_mangas'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.manga'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help.manga'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'), $type=='max_views' ? lang('admin.popular.reads.help') : lang('admin.popular.total_pages.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='manga' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");
	if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_mangas.empty'); ?></td>
										</tr>
<?php
	}
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row[$type]!=$prev_views) {
			$prev_views = $row[$type];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_liveactions'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.liveaction'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='liveaction' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

	if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_liveactions.empty'); ?></td>
										</tr>
<?php
	}
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row[$type]!=$prev_views) {
			$prev_views = $row[$type];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_animes.hentai'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.anime'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating='XXX' AND f.episode_id IS NOT NULL AND s.type='anime' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

	if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_animes.hentai.empty'); ?></td>
										</tr>
<?php
	}
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row[$type]!=$prev_views) {
			$prev_views = $row[$type];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}
?>
										<tr class="hentai">
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_mangas.hentai'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.manga'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help.manga'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'), $type=='max_views' ? lang('admin.popular.reads.help') : lang('admin.popular.total_pages.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating='XXX' AND f.episode_id IS NOT NULL AND s.type='manga' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");
	if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_mangas.hentai.empty'); ?></td>
										</tr>
<?php
	}
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row[$type]!=$prev_views) {
			$prev_views = $row[$type];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}
?>
										<tr class="hentai">
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.popular.social_images'); ?></h4>
								<div class="text-center">
									<a href="twitter_image.php?type=anime&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.anime'); ?></a>
									<a href="twitter_image.php?type=manga&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.manga'); ?></a>
									<a href="twitter_image.php?type=liveaction&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.liveaction'); ?></a>
									<a href="twitter_image.php?type=anime&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;is_hentai=1" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.anime.hentai'); ?></a>
									<a href="twitter_image.php?type=manga&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;is_hentai=1" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.manga.hentai'); ?></a>
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
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_animes'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.anime'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
		$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='anime' AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

		if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center">- No hi ha cap anime vist -</td>
										</tr>
<?php
		}
		$prev_views = 0;
		$position = 0;
		$current_positions = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row[$type]!=$prev_views) {
				$prev_views = $row[$type];
				$position=$position+$current_positions+1;
				$current_positions = 0;
			} else {
				$current_positions++;
			}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_mangas'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.manga'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help.manga'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'), $type=='max_views' ? lang('admin.popular.reads.help') : lang('admin.popular.total_pages.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
		$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='manga' AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");
		if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_mangas.empty'); ?></td>
										</tr>
<?php
		}
		$prev_views = 0;
		$position = 0;
		$current_positions = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row[$type]!=$prev_views) {
				$prev_views = $row[$type];
				$position=$position+$current_positions+1;
				$current_positions = 0;
			} else {
				$current_positions++;
			}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_liveactions'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.liveaction'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
		$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating<>'XXX' AND f.episode_id IS NOT NULL AND s.type='liveaction' AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

		if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_liveactions.empty'); ?></td>
										</tr>
<?php
		}
		$prev_views = 0;
		$position = 0;
		$current_positions = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row[$type]!=$prev_views) {
				$prev_views = $row[$type];
				$position=$position+$current_positions+1;
				$current_positions = 0;
			} else {
				$current_positions++;
			}
?>
										<tr>
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_animes.hentai'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<hr>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.anime'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.views') : lang('admin.popular.total_time'), $type=='max_views' ? lang('admin.popular.views.help') : lang('admin.popular.total_time.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
		$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating='XXX' AND f.episode_id IS NOT NULL AND s.type='anime' AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");

		if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_animes.hentai.empty'); ?></td>
										</tr>
<?php
		}
		$prev_views = 0;
		$position = 0;
		$current_positions = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row[$type]!=$prev_views) {
				$prev_views = $row[$type];
				$position=$position+$current_positions+1;
				$current_positions = 0;
			} else {
				$current_positions++;
			}
?>
										<tr class="hentai">
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($type=='total_length' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo sprintf(lang('admin.popular.most_popular_mangas.hentai'), $amount, ((!$selected_all && empty($selected_year)) ? get_clean_month_name(strftime("%B %Y", strtotime(date($selected_month.'-01')))) : (!$selected_all ? sprintf(lang('admin.popular.full_year'), $selected_year) : sprintf(lang('admin.popular.total_years_lowercase'), STARTING_YEAR, date('Y'))))); ?></h4>
								<table class="table table-hover table-striped">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="width: 6%;"><?php echo lang('admin.popular.position'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.manga'); ?></th>
											<th scope="col" style="width: 40%;"><?php echo lang('admin.popular.included_versions'); ?> <?php print_helper_box(lang('admin.popular.included_versions'), lang('admin.popular.included_versions.help.manga'), TRUE); ?></th>
											<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'); ?> <?php print_helper_box($type=='max_views' ? lang('admin.popular.reads') : lang('admin.popular.total_pages'), $type=='max_views' ? lang('admin.popular.reads.help') : lang('admin.popular.total_pages.help'), TRUE); ?></th>
										</tr>
									</thead>
									<tbody>
<?php
		$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0 AND s.rating='XXX' AND f.episode_id IS NOT NULL AND s.type='manga' AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].") GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, total_length DESC, b.series_name ASC LIMIT $amount");
		if (mysqli_num_rows($result)==0) {
?>
										<tr>
											<td colspan="4" class="text-center"><?php echo lang('admin.popular.most_popular_mangas.hentai.empty'); ?></td>
										</tr>
<?php
		}
		$prev_views = 0;
		$position = 0;
		$current_positions = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row[$type]!=$prev_views) {
				$prev_views = $row[$type];
				$position=$position+$current_positions+1;
				$current_positions = 0;
			} else {
				$current_positions++;
			}
?>
										<tr class="hentai">
											<th scope="row" class="align-middle"><?php echo $position; ?></th>
											<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
											<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
											<td class="align-middle text-center"><?php echo htmlspecialchars($row[$type]); ?></td>
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
					<div class="container d-flex justify-content-center p-4">
						<div class="card w-100">
							<article class="card-body">
								<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.popular.social_images'); ?></h4>
								<div class="text-center">
									<a href="twitter_image.php?type=anime&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;fansub_id=<?php echo $fansub['id']; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.anime'); ?></a>
									<a href="twitter_image.php?type=manga&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;fansub_id=<?php echo $fansub['id']; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.manga'); ?></a>
									<a href="twitter_image.php?type=liveaction&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;fansub_id=<?php echo $fansub['id']; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.liveaction'); ?></a>
									<a href="twitter_image.php?type=anime&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;is_hentai=1&amp;fansub_id=<?php echo $fansub['id']; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.anime.hentai'); ?></a>
									<a href="twitter_image.php?type=manga&amp;mode=<?php echo $selected_all ? "all" : (!empty($selected_year) ? 'year' : 'month'); ?>&amp;first_month=<?php echo $first_month; ?>&amp;last_month=<?php echo $last_month; ?>&amp;is_hentai=1&amp;fansub_id=<?php echo $fansub['id']; ?>" target="_blank" class="btn btn-primary"><?php echo lang('admin.popular.social_images.manga.hentai'); ?></a>
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
