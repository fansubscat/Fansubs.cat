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
						<h2 class="article-title">Obtenció de dades dels fansubs</h2>
						<p>Les dades s'obtenen automàticament dels diferents fansubs cada 15 minuts.<br />En alguns casos, els fansubs notifiquen que hi ha hagut un canvi i llavors el refresc és quasi immediat.<br />Aquí pots veure l'estat actual del sistema i quan s'han obtingut les dades per últim cop.</p>
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
					<div class="article">
						<h2 class="article-title">Estadístiques</h2>
						<p><strong>Nombre total de fansubs en català:</strong> 
<?php 
$resultfansubs = mysqli_query($db_connection, "SELECT COUNT(*) count FROM fansubs WHERE is_visible=1") or crash(mysqli_error($db_connection));
$row = mysqli_fetch_assoc($resultfansubs);
echo $row['count'];
mysqli_free_result($resultfansubs);
?>
						<br />
						<strong>Nombre total de notícies de fansubs:</strong> 
<?php 
$resultnews = mysqli_query($db_connection, "SELECT COUNT(*) count FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE is_own=0") or crash(mysqli_error($db_connection));
$row = mysqli_fetch_assoc($resultnews);
echo $row['count'];
mysqli_free_result($resultnews);
?>
						</p>
						<p>El fansub més actiu d'aquest mes és <strong>
<?php 
$resultactive = mysqli_query($db_connection, "SELECT COUNT(*) count,f.name,f.url FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE f.is_visible=1 AND date>'".date('Y-m')."' GROUP BY fansub_id ORDER BY count DESC, f.name ASC LIMIT 1") or crash(mysqli_error($db_connection));
if ($row = mysqli_fetch_assoc($resultactive)){
?>
							<a href="<?php echo $row['url']; ?>"><?php echo $row['name']; ?></a></strong>. L'enhorabona!
<?php
}
else{
?>
							de moment cap</strong>. A esforçar-s'hi!
<?php
}
?>
						</p>
						<p><strong>Gràfic del nombre de notícies per fansub i any:</strong></p>
						<div id="chart_div" style="width: 100%; height: 400px;"></div>
				</div>
<?php
mysqli_free_result($resultactive);

require_once('footer.inc.php');
?>
