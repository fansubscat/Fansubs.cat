<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".lang('url.settings'));
	die();
}

define('PAGE_TITLE', lang('users.my_profile.page_title'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once(__DIR__.'/../common/header.inc.php');

$result = query_user_seen_data_by_user_id($user['id']);
$seen_data = mysqli_fetch_assoc($result);

$total_manga_seen = $seen_data['total_manga_seen'];
$total_liveaction_seen = $seen_data['total_liveaction_seen'];
$total_comments_left = $seen_data['total_comments_left'];
$total_ratings_left = $seen_data['total_ratings_left'];

if ($seen_data['total_anime_seen']>=3600) {
	$total_anime_seen = intval($seen_data['total_anime_seen']/3600);
	if ($total_anime_seen==1) {
		$total_anime_seen=lang('users.my_profile.hours_one');
	} else {
		$total_anime_seen=sprintf(lang('users.my_profile.hours_many'), $total_anime_seen);
	}
} else {
	$total_anime_seen = intval($seen_data['total_anime_seen']/60);
	if ($total_anime_seen==1) {
		$total_anime_seen=lang('users.my_profile.minutes_one');
	} else {
		$total_anime_seen=sprintf(lang('users.my_profile.minutes_many'), $total_anime_seen);
	}
}

if ($seen_data['total_liveaction_seen']>=3600) {
	$total_liveaction_seen = intval($seen_data['total_liveaction_seen']/3600);
	if ($total_liveaction_seen==1) {
		$total_liveaction_seen=lang('users.my_profile.hours_one');
	} else {
		$total_liveaction_seen=sprintf(lang('users.my_profile.minutes_many'), $total_liveaction_seen);
	}
} else {
	$total_liveaction_seen = intval($seen_data['total_liveaction_seen']/60);
	if ($total_liveaction_seen==1) {
		$total_liveaction_seen=lang('users.my_profile.minutes_one');
	} else {
		$total_liveaction_seen=sprintf(lang('users.my_profile.minutes_many'), $total_liveaction_seen);
	}
}

if ($total_manga_seen==1) {
	$total_manga_seen=lang('users.my_profile.pages_one');
} else {
	$total_manga_seen=sprintf(lang('users.my_profile.pages_many'), $total_manga_seen);
}

mysqli_free_result($result);
?>
<div class="section">
	<h2 class="section-title-main"><i class="fa fa-fw fa-user"></i> El meu perfil</h2>
	<div class="profile-layout">
		<div class="profile-section">
			<div class="profile-avatar-name">
				<div class="profile-avatar">
					<img alt="<?php echo lang('users.edit_profile.profile_image.alt'); ?>" class="profile-avatar-image" src="<?php echo get_user_avatar_url($user); ?>">
				</div>
				<div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
			</div>
			<div class="profile-links">
				<a class="profile-button" href="<?php echo lang('url.edit_profile'); ?>"><i class="fa fa-fw fa-pen-to-square"></i> <?php echo lang('users.my_profile.edit_profile'); ?></a>
				<a class="profile-button" href="<?php echo lang('url.change_password'); ?>"><i class="fa fa-fw fa-key"></i> <?php echo lang('users.my_profile.change_password'); ?></a>
			</div>
		</div>
		<div class="content-layout profile-page">
			<div class="profile-basic-info profile-details-section">
				<div class="profile-section-header"><?php echo lang('users.my_profile.basic_info'); ?></div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo lang('users.my_profile.email'); ?></div>
						<div class="profile-section-data-info"><?php echo $user['email']; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo lang('users.my_profile.birth_date'); ?></div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), lang('date.birthdate_format')); ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo lang('users.my_profile.pronoun'); ?></div>
						<div class="profile-section-data-info"><?php echo $user['pronoun']=='male' ? lang('users.my_profile.pronoun.male') : ($user['pronoun']=='female' ? lang('users.my_profile.pronoun.female') : ($user['pronoun']=='nonbinary' ? lang('users.my_profile.pronoun.other') : lang('users.my_profile.pronoun.none'))); ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo sprintf(lang('users.my_profile.join_date'), CURRENT_SITE_NAME_ACCOUNT); ?></div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d H:i:s', $user['created']), lang('date.birthdate_format')); ?></div>
					</div>
				</div>
			</div>
			<div class="profile-statistics profile-details-section">
				<div class="profile-section-header"><?php echo lang('users.my_profile.stats'); ?></div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo SITE_IS_HENTAI ? lang('users.my_profile.anime_seen.hentai') : lang('users.my_profile.anime_seen'); ?></div>
						<div class="profile-section-data-info"><?php echo $total_anime_seen; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo SITE_IS_HENTAI ? lang('users.my_profile.manga_seen.hentai') : lang('users.my_profile.manga_seen'); ?></div>
						<div class="profile-section-data-info"><?php echo $total_manga_seen; ?></div>
					</div>
<?php
if (!SITE_IS_HENTAI) {
?>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo lang('users.my_profile.liveaction_seen'); ?></div>
						<div class="profile-section-data-info"><?php echo $total_liveaction_seen; ?></div>
					</div>
<?php
}
?>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo SITE_IS_HENTAI ? sprintf(lang('users.my_profile.comments.hentai'), CURRENT_SITE_NAME) : lang('users.my_profile.comments'); ?></div>
						<div class="profile-section-data-info"><?php echo $total_comments_left; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header"><?php echo SITE_IS_HENTAI ? sprintf(lang('users.my_profile.ratings.hentai'), CURRENT_SITE_NAME) : lang('users.my_profile.ratings'); ?></div>
						<div class="profile-section-data-info"><?php echo $total_ratings_left; ?></div>
					</div>
				</div>
			</div>
			<a class="profile-button remove-profile" href="<?php echo lang('url.delete_profile'); ?>"><?php echo lang('users.my_profile.delete_profile'); ?></a>
		</div>
	</div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>
