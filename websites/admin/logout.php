<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.logout.header');
$skip_navbar=TRUE;
require_once(__DIR__.'/header.inc.php');
session_destroy();
?>
		<div class="container d-flex h-100 justify-content-center align-items-center">
			<div class="card">
				<article class="card-body text-center">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.logout.bye'); ?></h4>
					<hr>
					<p class="text-center text-success"><?php echo lang('admin.logout.success'); ?></p>
					<a href="/" class="btn btn-primary btn-block"><?php echo lang('admin.logout.go_home'); ?></a>
				</article>
			</div>
		</div>
<?php
require_once(__DIR__.'/footer.inc.php');
?>
