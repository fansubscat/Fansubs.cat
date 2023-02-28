<?php
	if (empty($skip_footer)) {
?>
				<div class="footer">
<?php
		if ($style_type!='contact') {
?>
					<a class="contact-button" href="<?php echo $main_url; ?>/contacta-amb-nosaltres/">Contacta amb nosaltres</a>
<?php
		}
?>
					<div class="footer-text">Tots els drets dels textos, imatges i obres esmentades pertanyen a llurs propietaris.<br>Codi i disseny 2015-<?php echo date('Y'); ?> Fansubs.cat • <a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank">Versió <?php echo $version; ?></a> • <a href="<?php echo $main_url; ?>/politica-de-privadesa/">Política de privadesa</a> • <a href="https://github.com/fansubscat/Fansubs.cat" target="_blank">Som de codi obert</a></div>
				</div>
<?php
	}
?>
