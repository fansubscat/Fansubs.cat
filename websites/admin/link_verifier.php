<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.link_verifier.header');
$page="tools";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<script>
			var links = [
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE s.has_licensed_parts<3 AND EXISTS (SELECT vf.version_id FROM rel_version_fansub vf WHERE vf.version_id=v.id AND vf.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$where = ' WHERE s.has_licensed_parts<3';
	}

	if (!empty($_GET['version_id']) && is_numeric($_GET['version_id'])) {
		$where .= ' AND f.version_id='.$_GET['version_id'];
	}

	if (!empty($_GET['type']) && $_GET['type']=='storage') {
		$where .= " AND l.url LIKE 'storage://%'";
	} else if (!empty($_GET['type']) && $_GET['type']=='mega') {
		$where .= " AND l.url LIKE 'https://mega.nz/%'";
	}

	$resultl = query("SELECT l.*,
				v.title,
				GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') fansub_name,
				IF (f.episode_id IS NULL,
					CONCAT(v.title, ' - ".lang('admin.query.extra_division')." - ', f.extra_name),
					IF(s.subtype='movie' OR s.subtype='oneshot',
						IFNULL(et.title, v.title),
						IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
							CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
							CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
						)
					)
				) episode_title
			FROM link l
				LEFT JOIN file f ON l.file_id=f.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
				LEFT JOIN episode_title et ON et.episode_id=f.episode_id AND et.version_id=v.id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub fa ON vf.fansub_id=fa.id
			$where
			GROUP BY l.id
			ORDER BY fansub_name, v.title ASC, e.number IS NULL ASC, d.number ASC, e.number ASC, extra_name ASC");
	while ($row = mysqli_fetch_assoc($resultl)) {
?>
				{link: <?php echo json_encode(htmlspecialchars($row['url'], ENT_COMPAT)); ?>, text: <?php echo json_encode('<b>'.htmlspecialchars($row['fansub_name'].' - '.$row['title'], ENT_COMPAT).'</b><br><small class="fw-normal">'.htmlspecialchars($row['episode_title'], ENT_COMPAT).'</small>'); ?>},
<?php
	}
	mysqli_free_result($resultl);
?>
			];
		</script>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.link_verifier.title'); ?></h4>
					<hr>
					<p class="text-center"><?php echo lang('admin.link_verifier.explanation'); ?></p>
					<div class="text-center p-2">
						<button id="link-verifier-button" onclick="verifyLinks(0);" class="btn btn-primary">
							<span id="link-verifier-loading" class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
							<?php echo lang('admin.link_verifier.verify_all'); ?>
						</button>
						<div id="link-verifier-progress" class="d-none"></div>
					</div>
				</article>
			</div>
		</div>
		<div id="link-verifier-results" class="d-none container justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.link_verifier.results'); ?></h4>
					<hr>
					<div class="row text-success">
						<p class="text-end col-sm-6 mb-0 fw-bold"><?php echo lang('admin.link_verifier.results.valid'); ?></p>
						<p class="text-start col-sm-6 mb-0 fw-bold" id="link-verifier-good-links">0</p>
					</div>
					<div class="row text-danger">
						<p class="text-end col-sm-6 mb-0 fw-bold"><?php echo lang('admin.link_verifier.results.invalid'); ?></p>
						<p class="text-start col-sm-6 mb-0 fw-bold" id="link-verifier-wrong-links">0</p>
					</div>
					<div class="row text-warning">
						<p class="text-end col-sm-6 mb-0 fw-bold"><?php echo lang('admin.link_verifier.results.unknown'); ?></p>
						<p class="text-start col-sm-6 mb-0 fw-bold" id="link-verifier-failed-links">0</p>
					</div>
					<div class="row text-muted">
						<p class="text-end col-sm-6 mb-0 fw-bold"><?php echo lang('admin.link_verifier.results.unverifiable'); ?></p>
						<p class="text-start col-sm-6 mb-0 fw-bold" id="link-verifier-unknown-links">0</p>
					</div>
					<div id="link-verifier-wrong-links-list" class="d-none mt-4">
						<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.link_verifier.results.invalid_list'); ?></h4>
						<hr>
					</div>
					<div id="link-verifier-failed-links-list" class="d-none mt-4">
						<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.link_verifier.results.unknown_list'); ?></h4>
						<hr>
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
