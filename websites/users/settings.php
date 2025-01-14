<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

define('PAGE_TITLE', lang('users.settings.page_title'));
define('PAGE_PATH', lang('url.settings'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'settings');

require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/queries.inc.php');

if (!empty($user)) {
	$show_cancelled = $user['show_cancelled_projects'];
	$show_lost = $user['show_lost_projects'];
	$show_hentai = empty($user['hide_hentai_access']);
	$mark_as_seen = $user['previous_chapters_read_behavior'];
	$episode_sort_order = $user['episode_sort_order'];
	$reader_type = $user['manga_reader_type'];
	$blacklisted_fansub_ids = $user['blacklisted_fansub_ids'];
} else {
	$show_cancelled = !empty($_COOKIE['show_cancelled_projects']);
	$show_lost = !empty($_COOKIE['show_lost_projects']);
	$episode_sort_order = !empty($_COOKIE['episode_sort_order']);
	$reader_type = (isset($_COOKIE['manga_reader_type']) ? $_COOKIE['manga_reader_type'] : 0);
	$blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
}
?>
<div class="section">
	<h2 class="section-title-main"><i class="fa fa-fw fa-gear"></i> <?php echo lang('users.settings.header'); ?></h2>
	<div class="profile-layout">
		<div class="content-layout settings-page">
			<div class="settings-display settings-section">
				<div class="settings-section-header"><?php echo lang('users.settings.view_options'); ?></div>
				<div class="settings-section-content">
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('users.settings.show_cancelled.title'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('users.settings.show_cancelled.subtitle'); ?></div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-cancelled"<?php echo $show_cancelled ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('users.settings.show_lost.title'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('users.settings.show_lost.subtitle'); ?></div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-lost"<?php echo $show_lost ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
<?php
if (!empty($user) && is_adult()) {
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo sprintf(lang('users.settings.show_hentai_button.title'), HENTAI_SITE_NAME); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo sprintf(lang('users.settings.show_hentai_button.subtitle'), HENTAI_SITE_NAME, MAIN_SITE_NAME); ?></div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-hentai"<?php echo $show_hentai ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
<?php
}
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('users.settings.reverse_sort.title'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('users.settings.reverse_sort.subtitle'); ?></div>
							</div>
							<label class="switch">
								<input type="checkbox" id="episode-sort-order"<?php echo $episode_sort_order ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('users.settings.blacklist.title'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('users.settings.blacklist.subtitle'); ?></div>
							</div>
							<div class="settings-blacklist-chooser">
								<button class="normal-button edit-blacklisted-fansubs"><?php echo lang('users.settings.blacklist.edit'); ?></button>
<?php
$fansubs = query_all_fansubs();
$all_fansubs = array();
$blacklisted_fansubs = array();
foreach ($fansubs as $fansub) {
	array_push($all_fansubs, array(
		'id' => (int)$fansub['id'],
		'name' => $fansub['name']
		));
	if (in_array($fansub['id'], $blacklisted_fansub_ids)) {
		array_push($blacklisted_fansubs, $fansub);
	}
}

if (count($blacklisted_fansubs)==1) {
?>
								<div class="blacklisted-fansubs-list-number"><?php echo lang('users.settings.blacklist.blocked_fansubs_one'); ?></div>
<?php
} else {
?>
								<div class="blacklisted-fansubs-list-number"><?php echo sprintf(lang('users.settings.blacklist.blocked_fansubs_many'), count($blacklisted_fansubs)); ?></div>
<?php
}
?>
								<input id="blacklisted-fansubs-ids" type="hidden" value="<?php echo implode(',',$blacklisted_fansub_ids);?>">
								<input id="all-fansubs-json" type="hidden" value="<?php echo htmlspecialchars(json_encode($all_fansubs));?>">
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="settings-seen-settings settings-section">
				<div class="settings-section-header"><?php echo lang('users.settings.functionality'); ?></div>
				<div class="settings-section-content">
<?php
if (!empty($user)) {
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('users.settings.mark_previous_as_seen.title'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('users.settings.mark_previous_as_seen.subtitle'); ?></div>
							</div>
							<select id="mark-previous-as-seen" class="settings-combo" onchange="saveSettings();">
								<option value="0"<?php echo $mark_as_seen==0 ? ' selected' : ''; ?>><?php echo lang('users.settings.mark_previous_as_seen.ask'); ?></option>
								<option value="1"<?php echo $mark_as_seen==1 ? ' selected' : ''; ?>><?php echo lang('users.settings.mark_previous_as_seen.always'); ?></option>
								<option value="2"<?php echo $mark_as_seen==2 ? ' selected' : ''; ?>><?php echo lang('users.settings.mark_previous_as_seen.never'); ?></option>
							</select>
						</div>
					</div>
<?php
}
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title"><?php echo lang('js.catalogue.reader.select'); ?></div>
								<div class="settings-section-data-header-subtitle"><?php echo lang('js.catalogue.reader.explanation'); ?></div>
							</div>
							<select id="reader-type" class="settings-combo" onchange="saveSettings();">
								<option value="0"<?php echo $reader_type==0 ? ' selected' : ''; ?>><?php echo lang('js.catalogue.reader.recommended_option'); ?></option>
								<option value="1"<?php echo $reader_type==1 ? ' selected' : ''; ?>><?php echo lang('js.catalogue.reader.western_style'); ?></option>
								<option value="2"<?php echo $reader_type==2 ? ' selected' : ''; ?>><?php echo lang('js.catalogue.reader.eastern_style'); ?></option>
								<option value="3"<?php echo $reader_type==3 ? ' selected' : ''; ?>><?php echo lang('js.catalogue.reader.long_strip'); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
