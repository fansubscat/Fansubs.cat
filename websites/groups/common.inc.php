<?php
function print_fansub($row) {
?>
								<div class="team">
									<div class="team-content">
										<div class="team-text-wrapper">
											<h3 class="team-name">
<?php
		if ($row['url']!=NULL){
?>
											<a href="<?php echo $row['url']; ?>" target="_blank"><?php echo $row['name']; ?></a>
<?php
		}
		else{
?>
											<?php echo $row['name']; ?>
<?php
		}
?>
											</h3>
											<div class="team-info">
<?php
		if (!empty($row['fansub_url']) && empty($row['archive_url'])) {
			$url = $row['fansub_url'];
		} else if (!empty($row['archive_url'])) {
			$url = $row['archive_url'];
		} else {
			$url = NULL;
		}
?>
												<a class="team-fansub"<?php $url!==NULL ? ' href="'.$url.'" target="_blank"' : ''; ?>><img src="<?php echo $row['fansub_id']!==NULL ? STATIC_URL.'/images/icons/'.$row['fansub_id'].'.png' : '/favicon.png'; ?>" alt=""> <?php echo $row['fansub_name']; ?></a>
											</div>
											<div class="team-text">
												<?php echo $row['contents']; ?>
											</div>
<?php
		if ($row['url']!=NULL){
?>
											<div class="team-readmore">
												<a class="normal-button" href="<?php echo $row['url']; ?>" target="_blank"><?php echo "Vés a {$row['fansub_name']}"; ?> ➔</a>
											</div>
<?php
		}
?>
										</div>
									</div>
								</div>
<?php
}
?>
