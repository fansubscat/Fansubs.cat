<?php
$header_title="Pàgina principal";
$page="main";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
<div class="container d-flex justify-content-center p-4">
	<div class="card w-100">
		<div class="position-absolute align-self-end">
			<a id="welcome-refresh" href="/" class="btn btn-tertiary fa fa-redo p-2<?php echo empty($_SESSION['default_view']) || $_SESSION['default_view']==1 ? ' d-none' : ''; ?>" title="Refresca"></a>
			<button class="btn btn-tertiary fa fa-right-left p-2" title="Canvia la visualització inicial" onclick="toggleWelcomeView();"></button>
		</div>
		<article id="welcome-view" class="card-body<?php echo !empty($_SESSION['default_view']) && $_SESSION['default_view']==2 ? ' d-none' : ''; ?>">
			<h4 class="card-title text-center mb-4 mt-1">Introducció</h4>
			<hr>
			<p class="text-center"><strong>Et donem la benvinguda al tauler d’administració. Aquí pots gestionar el contingut dels diferents webs de Fansubs.cat.</strong></p>
			<p class="text-center">Cada <strong>anime</strong>, <strong>manga</strong> o <strong>contingut d’imatge real</strong> conté una fitxa genèrica amb les seves divisions (temporades o volums) i capítols.<br />Les <strong>versions</strong> corresponen a l’edició d’un o més fansubs i inclouen els enllaços o fitxers corresponents.<br />Per a afegir un contingut nou, primer cal crear-ne la fitxa genèrica, i després la versió amb els enllaços o fitxers.</p>
			<p class="text-center">L’apartat de <strong>notícies</strong> permet gestionar les notícies dels webs o blogs dels diferents fansubs.<br />Excepte en casos molt concrets, no és necessari afegir, modificar ni suprimir notícies a mà.</p>
			<p class="text-center">Al menú d’<strong>anàlisi</strong> trobaràs un seguit d’opcions per a veure quin és el consum del material.</p>
			<p class="text-center">Si tens dubtes, consulta l’<strong>ajuda</strong> que tens a la part superior dreta o contacta amb un administrador.</p>
			<p class="text-center">Si prefereixes que la pàgina d’inici del tauler no sigui aquesta sinó un resum del teu fansub, fes clic a la icona de la part superior dreta.</p>
<?php
	if ($_SESSION['admin_level']<2) {
?>
			<p class="text-center alert alert-warning">No tens permisos per a crear fitxes d’anime, manga o imatge real. Si et cal, demana a algú altre que ho faci.</p>
<?php
	}
?>
			<h4 class="card-title text-center mb-4 mt-4">Accions habituals</h4>
			<hr>
			<div class="container">
				<div class="row">
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Anime</h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix un anime nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=anime" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=anime" class="btn btn-primary"><span class="fa fa-edit pe-2"></span>Edita una versió existent</a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Manga</h5>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix un manga nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=manga" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=manga" class="btn btn-primary"><span class="fa fa-edit pe-2"></span>Edita una versió existent</a>
						</div>
					</div>
					<div class="col-sm">
						<h5 class="card-title text-center mb-3 mt-2">Imatge real</h5>
<?php
	if ($_SESSION['admin_level']>=2 && !DISABLE_LIVE_ACTION) {
?>
						<div class="text-center p-2">
							<a href="series_edit.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix un contingut nou</a> 
						</div>
<?php
	}
?>
						<div class="text-center p-2">
							<a href="series_choose.php?type=liveaction" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una versió nova</a>
						</div>
						<div class="text-center p-2">
							<a href="version_list.php?type=liveaction" class="btn btn-primary"><span class="fa fa-edit pe-2"></span>Edita una versió existent</a>
						</div>
					</div>
				</div>
			</div>
		</article>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$extra_where = ' AND EXISTS (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$extra_where = '';
	}
?>
		<article id="latest-view" class="card-body<?php echo empty($_SESSION['default_view']) || $_SESSION['default_view']==1 ? ' d-none' : ''; ?>">
			<div class="row">
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1">Visualitzacions i lectures en curs</h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT IFNULL(v.title, '(enllaç esborrat)') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - Contingut extra - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE UNIX_TIMESTAMP(ps.updated)>=".(date('U')-60)."$extra_where
		ORDER BY ps.created DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="1" class="text-center">- No hi ha ningú mirant res -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<td scope="row" class="align-middle"><b><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['title']); ?></b> • <?php echo str_replace('.',',',min(100,round($row['progress'],1))); ?>% completat<br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1">Darrers comentaris</h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT c.*, UNIX_TIMESTAMP(c.created) created_timestamp, v.title, u.username, u.status, (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=c.version_id) fansubs, s.rating FROM comment c LEFT JOIN user u ON c.user_id=u.id LEFT JOIN version v ON c.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE c.type='user'$extra_where ORDER BY c.created DESC LIMIT 5");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="2" class="text-center">- No hi ha cap comentari -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr class="<?php echo $row['rating']=='XXX' ? 'hentai' : ''; ?><?php echo $row['status']==1 ? 'shadowbanned' : ''; ?>">
								<td class="align-middle"><b><?php echo !empty($row['username']) ? htmlentities($row['username']) : 'Usuari eliminat'; ?></b> a <?php echo '<b>'.htmlspecialchars($row['title']).'</b>'.(empty($_SESSION['fansub_id']) ? ' de <b>'.htmlspecialchars($row['fansubs']).'</b>' : '').' • '.get_relative_date($row['created_timestamp']); ?><?php echo $row['last_replied']!=$row['created'] ? ' • <b>Respost</b>' : ''; ?><br><small><?php echo !empty($row['text']) ? str_replace("\n", "<br>", htmlentities($row['text'])) : '<i>- Comentari eliminat -</i>'; ?></small></td>
								<td class="align-middle text-center">
									<a href="comment_reply.php?id=<?php echo $row['id']; ?>" title="Respon" class="fa fa-reply p-1"></a>
								</td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1">Darreres visualitzacions i lectures</h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT IFNULL(v.title, '(enllaç esborrat)') title,
			(SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') FROM rel_version_fansub vf LEFT JOIN fansub fa ON vf.fansub_id=fa.id WHERE vf.version_id=v.id GROUP BY vf.version_id) fansub_name,
			IF (f.episode_id IS NULL,
				CONCAT(v.title, ' - Contingut extra - ', f.extra_name),
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IFNULL(et.title, v.title),
					IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
						CONCAT(IFNULL(vd.title,d.name), ' - Capítol ', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
						CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description))
					)
				)
			) episode_title,
			ps.user_id,
			ps.anon_id,
			(ps.progress/ps.length)*100 progress,
			UNIX_TIMESTAMP(ps.updated) updated,
			ps.source,
			ps.ip,
			ps.user_agent,
			ps.is_casted,
			UNIX_TIMESTAMP(ps.view_counted) view_counted,
			s.rating
		FROM view_session ps 
			LEFT JOIN file f ON ps.file_id=f.id 
			LEFT JOIN version v ON f.version_id=v.id 
			LEFT JOIN series s ON v.series_id=s.id 
			LEFT JOIN episode e ON f.episode_id=e.id 
			LEFT JOIN division d ON e.division_id=d.id 
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN episode_title et ON f.version_id=et.version_id AND f.episode_id=et.episode_id 
		WHERE ps.view_counted IS NOT NULL$extra_where
		ORDER BY ps.view_counted DESC LIMIT 5");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="1" class="text-center">- No hi ha cap visualització -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<td scope="row" class="align-middle"><b><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['title']); ?></b> • <?php echo get_relative_date($row['view_counted']); ?><br /><small class="fw-normal"><?php echo $row['episode_title']; ?></small></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
				<div class="col-sm">
					<h4 class="card-title text-center mb-4 mt-1">Darreres versions modificades</h4>
					<hr>
					<table class="table table-welcome table-hover table-striped">
						<tbody>
<?php
	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, v.title version_title, s.rating series_rating, s.type, s.name series_name, v.*, COUNT(DISTINCT fi.id) files, (SELECT COUNT(*) FROM user_version_rating WHERE rating=1 AND version_id=v.id) good_ratings, (SELECT COUNT(*) FROM user_version_rating WHERE rating=-1 AND version_id=v.id) bad_ratings, (SELECT COUNT(*) FROM comment WHERE type='user' AND version_id=v.id) num_comments, s.rating FROM version v LEFT JOIN file fi ON v.id=fi.version_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE 1$extra_where GROUP BY v.id ORDER BY v.updated DESC LIMIT 8");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="3" class="text-center">- No hi ha cap versió -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		$link_url=get_public_site_url($row['type'], $row['slug'], $row['series_rating']=='XXX');
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<th style="width: 70%;" scope="row" class="align-middle<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo empty($_SESSION['fansub_id']) ? htmlspecialchars($row['fansub_name']).' - ' : ''; ?><?php echo htmlspecialchars($row['version_title']); ?></th>
								<td class="align-middle text-center text-nowrap<?php echo $row['files']==0 ? ' text-muted' : ''; ?>"><?php echo $row['good_ratings']>0 ? $row['good_ratings'] : '0'; ?> <span title="Valoracions positives dels usuaris" class="fa far fa-thumbs-up"></span>&nbsp;&nbsp;<?php echo $row['bad_ratings']>0 ? $row['bad_ratings'] : '0'; ?> <span title="Valoracions negatives dels usuaris" class="fa far fa-thumbs-down"></span>&nbsp;&nbsp;<?php echo $row['num_comments']>0 ? $row['num_comments'] : '0'; ?> <span title="Comentaris dels usuaris" class="fa far fa-comment"></span></td>
								<td class="align-middle text-center text-nowrap"><a href="<?php echo $link_url; ?>" title="Fitxa al web públic" target="_blank" class="fa fa-up-right-from-square p-1 text-warning"></a> <a href="version_stats.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" title="Estadístiques i comentaris" class="fa fa-chart-line p-1 text-success"></a> <a href="version_edit.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</div>
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
