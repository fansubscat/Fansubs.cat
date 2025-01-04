<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai_ajax();

$max_items=24;

$result = query_home_random($user, $max_items);
?>
					<div class="section-content swiper carousel">
						<div class="swiper-wrapper">
<?php
while ($row = mysqli_fetch_assoc($result)){
?>
							<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']) : ''; ?> swiper-slide">
<?php
	print_carousel_item($row, FALSE, FALSE);
?>
							</div>
<?php
}
?>
						</div>
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
<?php
mysqli_free_result($result);
?>
