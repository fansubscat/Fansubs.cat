<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Estadístiques';
$header_current_page='stats';

require_once('header.inc.php');
?>
					<div class="page-title">
						<h2>Estadístiques</h2>
					</div>
					<div class="article">
						<h2 class="article-title">Quantitat de dades i fansub del mes</h2>
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
						<p style="margin-bottom: 0px;">El fansub més actiu d'aquest mes és <strong>
<?php 
$resultactive = mysqli_query($db_connection, "SELECT COUNT(*) count,f.name,f.url FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE f.is_visible=1 AND CAST(date AS CHAR)>'".date('Y-m')."' GROUP BY fansub_id ORDER BY count DESC, f.name ASC LIMIT 1") or crash(mysqli_error($db_connection));
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
mysqli_free_result($resultactive);
?>
						</p>
					</div>
					<div class="article">
						<h2 class="article-title">Nombre de notícies per fansub i any</h2>
						<div id="chart_div" style="width: 100%; height: 400px;"></div>
					</div>
					<div class="article">
						<h2 class="article-title" style="margin-bottom: 20px;">Els tres fansubs més actius per any</h2>
						<table class="top_three">
							<thead>
								<th>Any</th>
								<th>1r lloc</th>
								<th>2n lloc</th>
								<th>3r lloc</th>
							</thead>
							<tbody>
<?php
for ($y=date('Y');$y>2002;$y--){
?>
								<tr>
									<td><strong><?php echo $y; ?></strong></td>
<?php
	$i=0;
	$result = mysqli_query($db_connection, "SELECT COUNT(*) count,f.name FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE f.is_visible=1 AND CAST(date AS CHAR)>='$y' AND CAST(date AS CHAR)<'".($y+1)."' GROUP BY fansub_id ORDER BY count DESC, f.name ASC LIMIT 3") or crash(mysqli_error($db_connection));
	while ($row = mysqli_fetch_assoc($result)){
?>									<td><b><?php echo $row['name']; ?></b><br /><span style="font-size: 0.9em;">(<?php echo ($row['count'])==1 ? '1 notícia' : $row['count'].' notícies'; ?>)</span></td>
<?php
		$i++;
	}
	
	mysqli_free_result($result);

	//Case for when less than 3 fansubs are in a specific year
	while ($i<3){
		echo "<td><b>-</b></td>";
		$i++;
	}
?>
								</tr>
<?php
}
?>
							</tbody>
						</table>
					</div>
<?php
require_once('footer.inc.php');
?>
