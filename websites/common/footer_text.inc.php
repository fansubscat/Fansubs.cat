<?php
	if (!defined('SKIP_FOOTER')) {
?>
				<div class="footer">
<?php
		if (PAGE_STYLE_TYPE!='contact') {
?>
					<a class="contact-button" href="<?php echo MAIN_URL; ?>/contacta-amb-nosaltres">Contacta amb nosaltres</a>
<?php
		}
?>
					<div class="footer-text">Tots els drets dels textos, imatges i obres esmentades pertanyen a llurs propietaris.<br>Codi i disseny 2015-<?php echo date('Y'); ?> <?php echo CURRENT_SITE_NAME; ?> • <a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank">Versió <?php echo VERSION; ?></a> • <a href="<?php echo MAIN_URL; ?>/politica-de-privadesa">Política de privadesa</a> • <a href="https://github.com/fansubscat/Fansubs.cat" target="_blank">És codi obert</a></div>
				</div>
<?php
	}
?>
