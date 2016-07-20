<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Estat del sistema';
$header_current_page='status';

//Helper functions to show better strings for possible values on the DB

function show_fetch_type($fetch_type){
	switch ($fetch_type){
		case 'periodic':
			return 'Periòdic<br />(15 min.)';
		case 'onrequest':
			return 'A petició';
		case 'onetime_retired':
			return 'Única vegada<br />(retirat)';
		default:
			return $fetch_type;
	}
}

function show_method($method){
	switch ($method){
		case 'blogspot':
			return 'Blogspot';
		case 'blogspot_2nf':
			return 'Blogspot<br />(variant 2NF)';
		case 'blogspot_dnf':
			return 'Blogspot<br />(variant DNF)';
		case 'blogspot_llpnf':
			return 'Blogspot<br />(variant LlPnF)';
		case 'blogspot_snf':
			return 'Blogspot<br />(variant SNF)';
		case 'blogspot_tnf':
			return 'Blogspot<br />(variant TNF)';
		case 'catsub':
			return 'CatSub';
		case 'phpbb_dnf':
			return 'phpBB<br />(variant DNF)';
		case 'phpbb_llpnf':
			return 'phpBB<br />(variant LlPnF)';
		case 'weebly_rnnf':
			return 'Weebly<br />(variant RNNF)';
		case 'wordpress_ddc':
			return 'WordPress<br />(variant DDC)';
		case 'wordpress_xf':
			return 'WordPress<br />(variant XF)';
		case 'wordpress_ynf':
			return 'WordPress<br />(variant YNF)';
		default:
			return $method;
	}
}

function show_status($status){
	switch ($status){
		case 'idle':
			return 'En repòs';
		case 'fetching':
			return 'Obtenint dades';
		default:
			return $status;
	}
}

function show_last_result($last_result){
	switch ($last_result){
		case 'ok':
			return '<span style="color: #008800">✔ Correcte</span>';
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
$result = mysqli_query($db_connection, "SELECT fe.*,fa.name FROM fetchers fe LEFT JOIN fansubs fa ON fe.fansub_id=fa.id ORDER BY fetch_type DESC, fa.name ASC, fe.url ASC") or crash(mysqli_error($db_connection));
?>
					<div class="article">
						<p style="margin-top: 0px;">Aquí pots veure l'estat del sistema d'obtenció de dades dels diferents fansubs i quan s'han obtingut les dades per últim cop.<br />Les dades s'obtenen automàticament dels diferents fansubs cada 15 minuts. En alguns casos, els fansubs notifiquen que hi ha hagut un canvi i llavors el refresc és quasi immediat.</p>
						<table class="status">
							<thead>
								<th>Fansub / URL</th>
								<th>Tipus</th>
								<th>Mètode</th>
								<th>Estat</th>
								<th>Última connexió</th>
								<th>Últim resultat</th>
							</thead>
							<tbody>
<?php
while ($row = mysqli_fetch_assoc($result)){
?>
								<tr>
									<td><strong><?php echo $row['name']; ?></strong><br />&nbsp;&nbsp;&nbsp;<?php echo $row['url']; ?></td>
									<td><?php echo show_fetch_type($row['fetch_type']); ?></td>
									<td><?php echo show_method($row['method']); ?></td>
									<td><?php echo show_status($row['status']); ?></td>
									<td><?php echo ($row['last_fetch_date']!=NULL ? relative_time(strtotime($row['last_fetch_date'])) : 'Mai'); ?></td>
									<td><strong><?php echo show_last_result($row['last_fetch_result']); ?></strong></td>
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
