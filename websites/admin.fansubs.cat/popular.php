<?php
$header_title="Estadístiques - Els més populars";
$page="analytics";
include("header.inc.php");
require_once("common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	//Prepare list of months
	setlocale(LC_ALL, 'ca_ES.utf8');
	$months = array();

	$current_month = strtotime(date('Y-m-01'));
	$i=0;
	while (strtotime(date('2020-06-01')."+$i months")<=$current_month) {
		$months[date("Y-m", strtotime(date('2020-06-01')."+$i months"))]=ucfirst(str_replace('d’','', str_replace('de ','', strftime("%B %Y", strtotime(date('2020-06-01')."+$i months")))));
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
			$first_month = '2020-01';
			$last_month = date('Y-m');
		}
	}
	if (isset($_GET['amount']) && preg_match('/\d+/', $_GET['amount'])) {
		$amount = $_GET['amount'];
	} else {
		$amount = 10;
	}
	if (isset($_GET['type']) && $_GET['type']=='time_spent') {
		$type = 'time_spent';
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
					<h4 class="card-title text-center mb-4 mt-1">Els més populars</h4>
					<hr>
					<p class="text-center">Aquests són els animes i mangues més populars als portals.</p>

					<div class="row justify-content-center">
						<div class="form-group p-3 mb-0">
							<label for="month">Període:</label>
							<select id="month" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;hide_hentai='+($('#hide_hentai').prop('checked') ? 1 : 0)+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val();">
								<option value="ALL"<?php echo ($selected_all) ? ' selected' : ''; ?> style="font-weight: bold;">TOTAL 2020-<?php echo date('Y'); ?></option>
<?php
	$current_year=0;
	foreach ($months as $month => $values) {
		if (explode('-',$month)[0]!=$current_year) {
			$current_year=explode('-',$month)[0];
?>
								<option value="<?php echo $current_year; ?>"<?php echo (!$selected_all && $selected_year==$current_year) ? ' selected' : ''; ?> style="font-weight: bold;">Any complet <?php echo $current_year; ?></option>
<?php
		}
?>
								<option value="<?php echo $month; ?>"<?php echo (!$selected_all && empty($selected_year) && $selected_month==$month) ? ' selected' : ''; ?>><?php echo $values; ?></option>
<?php
	}
?>
							</select>
						</div>
						<div class="form-group p-3 mb-0">
							<label for="amount">Nombre d'elements:</label>
							<select id="amount" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;hide_hentai='+($('#hide_hentai').prop('checked') ? 1 : 0)+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val();">
								<option value="10"<?php echo ($amount==10) ? ' selected' : ''; ?>>10</option>
								<option value="25"<?php echo ($amount==25) ? ' selected' : ''; ?>>25</option>
								<option value="50"<?php echo ($amount==50) ? ' selected' : ''; ?>>50</option>
							</select>
						</div>
						<div class="form-group p-3 mb-0">
							<label for="type">Ordena per:</label>
							<select id="type" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;hide_hentai='+($('#hide_hentai').prop('checked') ? 1 : 0)+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val();">
								<option value="max_views"<?php echo ($type=='max_views') ? ' selected' : ''; ?>>Visualitzacions o lectures</option>
								<option value="time_spent"<?php echo ($type=='time_spent') ? ' selected' : ''; ?>>Temps o pàgines totals</option>
							</select>
						</div>
						<div class="form-group p-3 mb-0">
							<input type="checkbox" id="hide_hentai" value="1" onchange="location.href='popular.php?month='+$('#month').val()+'&amp;hide_hentai='+($('#hide_hentai').prop('checked') ? 1 : 0)+'&amp;amount='+$('#amount').val()+'&amp;type='+$('#type').val();"<?php echo $hide_hentai ? ' checked' : ''; ?>>
							<label for="hide_hentai">Amaga el hentai</label>
						</div>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Els <?php echo $amount; ?> animes més populars - <?php echo (!$selected_all && empty($selected_year)) ? ucfirst(str_replace('d’','', str_replace('de ','', strftime("%B %Y", strtotime(date($selected_month.'-01')))))) : (!$selected_all ? "Any complet ".$selected_year : 'Total 2020-'.date('Y')); ?></h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col" style="width: 6%;">Posició</th>
								<th scope="col" style="width: 40%;">Anime</th>
								<th scope="col" style="width: 40%;">Versions incloses<br><small>(amb alguna visualització)</small></th>
								<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? 'Visualitzacions<br><small>(capítol més vist)</small>' : 'Temps total<br><small>(tots els capítols)</small>'; ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.time_spent) time_spent, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.time_spent) time_spent, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=l.version_id) fansubs, SUM(vi.views) views, SUM(vi.time_spent) time_spent, l.version_id, l.episode_id, s.id series_id, s.name series_name, s.rating rating FROM link l LEFT JOIN views vi ON vi.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0".($hide_hentai ? " AND (s.rating IS NULL OR s.rating<>'XXX')" : '')." AND l.episode_id IS NOT NULL GROUP BY l.version_id, l.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY $type DESC, b.series_name ASC LIMIT $amount");

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
							<tr<?php echo $row['rating']=='XXX' ? ' class="text-danger"' : ''; ?>>
								<th scope="row" class="align-middle"><?php echo $position; ?></th>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars(implode(' / ',array_unique(explode(' / ',$row['fansubs'])))); ?></td>
								<td class="align-middle text-center"><?php echo htmlspecialchars($type=='time_spent' ? get_hours_or_minutes_formatted($row[$type]) : $row[$type]); ?></td>
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
					<h4 class="card-title text-center mb-4 mt-1">Els <?php echo $amount; ?> mangues més populars - <?php echo (!$selected_all && empty($selected_year)) ? ucfirst(str_replace('d’','', str_replace('de ','', strftime("%B %Y", strtotime(date($selected_month.'-01')))))) : (!$selected_all ? "Any complet ".$selected_year : 'Total 2020-'.date('Y')); ?></h4>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col" style="width: 6%;">Posició</th>
								<th scope="col" style="width: 40%;">Manga</th>
								<th scope="col" style="width: 40%;">Versions incloses<br><small>(amb alguna lectura)</small></th>
								<th scope="col" style="width: 14%;" class="text-center"><?php echo $type=='max_views' ? 'Lectures<br><small>(capítol més llegit)</small>' : 'Pàgines totals<br><small>(tots els capítols)</small>'; ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.manga_id, b.manga_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.time_spent) time_spent, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.manga_id, a.manga_name, a.chapter_id, SUM(a.views) total_views, SUM(a.pages_read) time_spent, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_manga_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.manga_version_id=fi.manga_version_id) fansubs, SUM(vi.views) views, SUM(vi.pages_read) pages_read, fi.manga_version_id, fi.chapter_id, m.id manga_id, m.name manga_name, m.rating rating FROM file fi LEFT JOIN manga_views vi ON vi.file_id=fi.id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN manga m ON c.manga_id=m.id LEFT JOIN rel_manga_version_fansub vf ON fi.manga_version_id=vf.manga_version_id LEFT JOIN fansub f ON f.id=vf.fansub_id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0".($hide_hentai ? " AND (m.rating IS NULL OR m.rating<>'XXX')" : '')." AND fi.chapter_id IS NOT NULL GROUP BY fi.manga_version_id, fi.chapter_id) a GROUP BY a.chapter_id) b GROUP BY b.manga_id ORDER BY $type DESC, b.manga_name ASC LIMIT $amount");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap manga llegit -</td>
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
							<tr<?php echo $row['rating']=='XXX' ? ' class="text-danger"' : ''; ?>>
								<th scope="row" class="align-middle"><?php echo $position; ?></th>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['manga_name']); ?></th>
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
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
