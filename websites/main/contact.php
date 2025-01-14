<?php
require_once(__DIR__.'/../common/initialization.inc.php');

define('PAGE_TITLE', lang('main.contact_us.page_title'));
define('PAGE_PATH', lang('url.contact_us'));
define('PAGE_STYLE_TYPE', 'contact');
define('PAGE_DESCRIPTION', lang('main.contact_us.page_description'));

require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="text-page" id="contact-form">
						<h2 class="section-title"><i class="fa fa-fw fa-envelope-open-text"></i> <?php echo lang('main.contact_us.header'); ?></h2>
						<div class="section-content"><?php echo lang('main.contact_us.explanation'); ?></div>
						<form class="contact-form centered" onsubmit="return sendMail();" autocomplete="off" novalidate>
							<label for="contact_name"><?php echo lang('main.contact_us.name'); ?></label>
							<input id="contact_name" type="text" value="<?php echo !empty($user) ? $user['username'] : ''; ?>" oninput="removeValidation(this.id);">
							<div id="contact_name_validation" class="validation-message"></div>
							<label for="contact_email"><?php echo lang('main.contact_us.email'); ?></label>
							<input id="contact_email" type="email" value="<?php echo !empty($user) ? $user['email'] : ''; ?>" oninput="removeValidation(this.id);">
							<div id="contact_email_validation" class="validation-message"></div>
							<label for="contact_message"><?php echo lang('main.contact_us.message'); ?></label>
							<textarea id="contact_message" oninput="removeValidation(this.id);"></textarea>
							<div id="contact_message_validation" class="validation-message"></div>
							<label for="contact_question"><?php echo lang('main.contact_us.security_question'); ?><br><small><?php echo lang('main.contact_us.security_question_explanation'); ?></small></label>
							<input id="contact_question" type="text" oninput="removeValidation(this.id);">
							<div id="contact_question_validation" class="validation-message"></div>
							<div id="contact_generic_validation" class="validation-message-generic"></div>
							<button id="contact_submit" type="submit" class="normal-button"><?php echo lang('main.contact_us.send'); ?></button>
						</form>
					</div>
					<div class="text-page centered" id="contact-sent" style="display: none;">
						<h2 class="section-title"><?php echo lang('main.contact_us.sent'); ?></h2>
						<div class="section-content"><?php echo lang('main.contact_us.sent_explanation'); ?></div>
						<form class="contact-form" novalidate>
							<a class="normal-button" href="/"><?php echo lang('main.contact_us.go_back'); ?></a>
						</form>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
