<?php
define('PAGE_TITLE', 'Contacta amb nosaltres');
define('PAGE_PATH', '/contacta-amb-nosaltres');
define('PAGE_STYLE_TYPE', 'contact');
define('PAGE_DESCRIPTION', 'Omple el següent formulari per a contactar amb nosaltres. Et respondrem tan aviat com ens sigui possible.');

require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="text-page" id="contact-form">
						<h2 class="section-title"><i class="fa fa-fw fa-envelope-open-text"></i> Formulari de contacte</h2>
						<div class="section-content">Omple el següent formulari per a contactar amb nosaltres. Et respondrem tan aviat com ens sigui possible.</div>
						<form class="contact-form centered" onsubmit="return sendMail();" autocomplete="off" novalidate>
							<label for="contact_name">Nom</label>
							<input id="contact_name" type="text" value="<?php echo !empty($user) ? $user['username'] : ''; ?>" oninput="removeValidation(this.id);">
							<div id="contact_name_validation" class="validation-message"></div>
							<label for="contact_email">Adreça electrònica</label>
							<input id="contact_email" type="email" value="<?php echo !empty($user) ? $user['email'] : ''; ?>" oninput="removeValidation(this.id);">
							<div id="contact_email_validation" class="validation-message"></div>
							<label for="contact_message">Missatge</label>
							<textarea id="contact_message" oninput="removeValidation(this.id);"></textarea>
							<div id="contact_message_validation" class="validation-message"></div>
							<label for="contact_question">Qüestió de seguretat<br><small>Escriu el nom de la llengua en què està escrita aquesta pàgina. És necessari per a evitar correu brossa.</small></label>
							<input id="contact_question" type="text" oninput="removeValidation(this.id);">
							<div id="contact_question_validation" class="validation-message"></div>
							<div id="contact_generic_validation" class="validation-message-generic"></div>
							<button id="contact_submit" type="submit" class="normal-button">Envia el missatge</button>
						</form>
					</div>
					<div class="text-page centered" id="contact-sent" style="display: none;">
						<h2 class="section-title">El missatge s’ha enviat!</h2>
						<div class="section-content">Gràcies per enviar-nos el teu comentari. Et respondrem tan aviat com ens sigui possible.</div>
						<form class="contact-form" novalidate>
							<a class="normal-button" href="/">Torna a la pàgina principal</a>
						</form>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
