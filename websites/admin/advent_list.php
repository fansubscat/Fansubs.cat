<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.advent_list.header');
$page="other";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_year'])) {
		log_action("delete-advent-calendar", "Advent calendar for ".$_GET['delete_year']." deleted");
		query("DELETE FROM advent_calendar WHERE year=".escape($_GET['delete_year']));
		@unlink(STATIC_DIRECTORY.'/images/advent/background_'.$_GET['delete_year'].'.jpg');
		@unlink(STATIC_DIRECTORY.'/images/advent/preview_'.$_GET['delete_year'].'.jpg');
		for ($i=1;$i<25;$i++) {
			@unlink(STATIC_DIRECTORY.'/images/advent/image_'.$_GET['delete_year'].'_'.$i.'.jpg');
		}
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.advent_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.advent_list.advent_calendar'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.advent_list.created_and_published'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$year = STARTING_YEAR;
	$years_subquery = "SELECT ".STARTING_YEAR." year";
	while ($year < date('Y')) {
		$year++;
		$years_subquery .= " UNION SELECT $year year";
	}
	$result = query("SELECT y.year,COUNT(DISTINCT ac.year) is_created FROM ($years_subquery) y LEFT JOIN advent_calendar ac ON y.year=ac.year GROUP BY y.year ORDER BY y.year DESC;");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo sprintf(lang('admin.advent_list.advent_calendar_for_year'), $row['year']); ?></th>
								<td class="align-middle text-center"><?php echo $row['is_created']>0 ? lang('admin.generic.yes') : lang('admin.generic.no'); ?></td>
								<td class="align-middle text-center text-nowrap"><a href="advent_edit.php?year=<?php echo $row['year']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="advent_list.php?delete_year=<?php echo $row['year']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.advent_list.delete_confirm'), $row['year']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
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
