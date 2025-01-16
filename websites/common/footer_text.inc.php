<?php
	if (!defined('SKIP_FOOTER')) {
?>
				<div class="footer">
<?php
		if (PAGE_STYLE_TYPE!='contact') {
?>
					<a class="contact-button" href="<?php echo MAIN_URL.lang('url.contact_us'); ?>"><?php echo lang('main.button.contact_us'); ?></a>
<?php
		}
?>
					<div class="footer-text"><?php echo lang('main.footer.copyright'); ?><br><?php echo sprintf(lang('main.footer.code'), date('Y'), 'Fansubs.cat'); ?> • <a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank"><?php echo sprintf(lang('main.footer.version'), VERSION); ?></a> • <a href="<?php echo MAIN_URL.lang('url.privacy_policy'); ?>"><?php echo lang('main.footer.privacy_policy'); ?></a><?php if (!DISABLE_STATUS) { ?> • <a href="<?php echo STATUS_URL; ?>" target="_blank"><?php echo lang('main.footer.service_status'); ?></a><?php } ?> • <a href="https://github.com/fansubscat/Fansubs.cat" target="_blank"><?php echo lang('main.footer.open_source'); ?></a></div>
				</div>
<?php
	}
?>
