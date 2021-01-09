<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Estat del sistema';
$header_current_page='status';

//Helper functions to show better strings for possible values on the DB

function show_fetch_type($fetch_type){
	switch ($fetch_type){
		case 'periodic':
			return 'Periòdic<br />(15&nbsp;min.)';
		case 'onrequest':
			return 'A&nbsp;petició';
		case 'onetime_retired':
			return 'Una&nbsp;vegada<br />(retirat)';
		case 'onetime_inactive':
			return 'Una&nbsp;vegada<br />(inactiu)';
		default:
			return $fetch_type;
	}
}

function show_status($status){
	switch ($status){
		case 'idle':
			return 'En&nbsp;repòs';
		case 'fetching':
			return 'Obtenint dades';
		default:
			return $status;
	}
}

function show_last_result($last_result, $last_increment){
	switch ($last_result){
		case 'ok':
			if ($last_increment===NULL){
				return '<span style="color: #008800">✔ Correcte</span>';
			}
			else if ($last_increment==0){
				return '<span style="color: #008800">✔ Correcte (±0)</span>';
			}
			else if ($last_increment>0){
				return '<span style="color: #008800">✔ Correcte (+'.$last_increment.')</span>';
			}
			else{
				return '<span style="color: #008800">✔ Correcte ('.$last_increment.')</span>';
			}
		case 'error_mysql':
			return '<span style="color: #880000">✖ Error<br />(BD)</span>';
		case 'error_empty':
			return '<span style="color: #880000">✖ Error<br />(buit)</span>';
		case 'error_connect':
			return '<span style="color: #880000">✖ Error<br />(connexió)</span>';
		case 'error_invalid_method':
			return '<span style="color: #880000">✖ Error<br />(desconegut)</span>';
		case '':
			return "-";
		default:
			return $last_result;
	}
}

require_once('header.inc.php');
?>
					<div class="page-title">
						<h2>Estat del sistema</h2>
					</div>
<?php
$result = mysqli_query($db_connection, "SELECT fe.*,fa.name FROM fetcher fe LEFT JOIN fansub fa ON fe.fansub_id=fa.id ORDER BY fetch_type DESC, fa.name ASC, fe.url ASC") or crash(mysqli_error($db_connection));
?>
					<div class="article">
						<p style="margin-top: 0px;">Aquí pots veure l'estat del sistema d'obtenció de dades dels diferents fansubs i quan s'han obtingut les dades per darrer cop.<br />Les dades s'obtenen automàticament dels diferents fansubs cada 15 minuts. En alguns casos, els fansubs notifiquen que hi ha hagut un canvi i llavors el refresc és quasi immediat.</p>
						<table class="status">
							<thead>
								<th>Fansub / URL</th>
								<th>Tipus</th>
								<th>Estat</th>
								<th>Darrera connexió</th>
								<th>Darrer resultat</th>
							</thead>
							<tbody>
<?php
while ($row = mysqli_fetch_assoc($result)){
?>
								<tr>
									<td><strong><?php echo $row['name']; ?></strong><br />&nbsp;&nbsp;&nbsp;<small><?php echo $row['url']; ?></small></td>
									<td><?php echo show_fetch_type($row['fetch_type']); ?></td>
									<td><?php echo show_status($row['status']); ?></td>
									<td><?php echo ($row['last_fetch_date']!=NULL ? relative_time(strtotime($row['last_fetch_date'])) : 'Mai'); ?></td>
									<td><strong><?php echo show_last_result($row['last_fetch_result'], $row['last_fetch_increment']); ?></strong></td>
								</tr>
<?php
}
mysqli_free_result($result);
?>
							</tbody>
						</table>
					</div>
<?php
require_once('footer.inc.php');
?>
