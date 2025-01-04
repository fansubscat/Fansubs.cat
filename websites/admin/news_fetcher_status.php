<?php
$header_title="Estat dels recollidors de notícies - Eines";
$page="tools";
include(__DIR__.'/header.inc.php');

//Helper functions to show better strings for possible values on the DB

function show_fetch_type($fetch_type){
	switch ($fetch_type){
		case 'periodic':
			return 'Periòdic<br />(cada 15&nbsp;min.)';
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
			return 'Obtenint&nbsp;dades';
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
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;Correcte</span>';
			}
			else if ($last_increment==0){
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;Correcte&nbsp;(±0)</span>';
			}
			else if ($last_increment>0){
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;Correcte&nbsp;(+'.$last_increment.')</span>';
			}
			else{
				return '<span style="color: '.$ok_color.'"><span class="fa fa-check"></span>&nbsp;Correcte&nbsp;('.$last_increment.')</span>';
			}
		case 'error_mysql':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;Error&nbsp;(BD)</span>';
		case 'error_empty':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;Error&nbsp;(buit)</span>';
		case 'error_connect':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;Error&nbsp;(connexió)</span>';
		case 'error_invalid_method':
			return '<span style="color: '.$ko_color.'"><span class="fa fa-times"></span>&nbsp;Error&nbsp;(desconegut)</span>';
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
					<h4 class="card-title text-center mb-4 mt-1">Estat dels recollidors de notícies</h4>
					<hr>
					<p class="text-center">Aquí es mostra l’estat dels recollidors de notícies i quan se n’han obtingut dades per darrer cop.<br />Les notícies s’obtenen automàticament dels diferents recollidors cada 15 minuts.</p>
					<div class="text-center pb-3">
						<a href="news_fetcher_status.php" class="btn btn-primary"><span class="fa fa-redo pe-2"></span>Refresca</a>
					</div>
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="width: 18%;">Fansub / URL</th>
								<th scope="col" style="width: 12%;" class="text-center">Freqüència</th>
								<th scope="col" style="width: 12%;" class="text-center">Estat</th>
								<th scope="col" style="width: 12%;" class="text-center">Darrera connexió</th>
								<th scope="col" style="width: 12%;" class="text-center">Darrer resultat</th>
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
								<td colspan="5" class="text-center">- No hi ha cap recollidor -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><strong><?php echo $row['name']; ?></strong><br />&nbsp;&nbsp;&nbsp;<small><?php echo $row['url']; ?></small></th>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo show_fetch_type($row['fetch_type']); ?></td>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo show_status($row['status']); ?></td>
								<td class="align-middle text-center<?php echo ($row['fetch_type']=='periodic' || $row['fetch_type']=='onrequest') ? '' : ' text-muted'; ?>"><?php echo ($row['last_fetch_date']!=NULL ? relative_time(strtotime($row['last_fetch_date'])) : 'Mai'); ?></td>
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
