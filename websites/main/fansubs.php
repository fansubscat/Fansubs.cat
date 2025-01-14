<?php
require_once(__DIR__.'/../common/initialization.inc.php');

if (SITE_IS_HENTAI) {
	define('PAGE_TITLE', lang('main.fansubs.page_title.hentai'));
	define('PAGE_DESCRIPTION', sprintf(lang('main.fansubs.page_description.hentai'), CURRENT_SITE_NAME));
} else {
	define('PAGE_TITLE', lang('main.fansubs.page_title'));
	define('PAGE_DESCRIPTION', sprintf(lang('main.fansubs.page_description'), CURRENT_SITE_NAME));
}
define('PAGE_PATH', lang('url.fansubs'));
define('PAGE_STYLE_TYPE', 'fansubs');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');
?>
					<div class="fansubs-index">
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-user-group"></i> <?php echo lang('main.fansubs.active'); ?></h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 1);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('main.fansubs.no_active_fansubs'); ?></div></div>
<?php
}
else{
?>
							<div class="fansubs-grouping">
<?php
	while ($row = mysqli_fetch_assoc($result)){
		print_fansub($row);
	}
}
?>
							</div>
						</div>
						<div class="section">
							<h2 class="section-title-main"><i class="fa fa-fw fa-landmark"></i> <?php echo lang('main.fansubs.historical'); ?></h2>
<?php
$result = query_fansubs(!empty($user) ? $user : NULL, 0);

if (mysqli_num_rows($result)==0){
?>
							<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('main.fansubs.no_historical_fansubs'); ?></div></div>
<?php
}
else{
?>
							<div class="fansubs-grouping historical-fansubs">
<?php
	while ($row = mysqli_fetch_assoc($result)){
		print_fansub($row);
	}
}
?>
							</div>
						</div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
