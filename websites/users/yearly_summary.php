<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".lang('url.login'));
	die();
}

define('PAGE_TITLE', lang('users.yearly_summary.page_title'));
define('PAGE_STYLE_TYPE', 'yearly-summary');

require_once(__DIR__.'/../common/header.inc.php');

if (is_yearly_summary_available()) {
	$result = query_yearly_summary_data_by_user_id($user['id'], date('Y')-1);
	$summary_data = mysqli_fetch_assoc($result);

	$result_anime = query_yearly_summary_anime_by_user_id($user['id'], date('Y')-1, 3);
	$result_manga = query_yearly_summary_manga_by_user_id($user['id'], date('Y')-1, 3);
	$result_liveaction = query_yearly_summary_liveaction_by_user_id($user['id'], date('Y')-1, 3);

	$anime = array();
	while ($data = mysqli_fetch_assoc($result_anime)) {
		array_push($anime, $data);
	}
	$manga = array();
	while ($data = mysqli_fetch_assoc($result_manga)) {
		array_push($manga, $data);
	}
	$liveaction = array();
	while ($data = mysqli_fetch_assoc($result_liveaction)) {
		array_push($liveaction, $data);
	}

	$anime_watched = $summary_data['anime_watched'];
	$manga_watched = $summary_data['manga_watched'];
	$liveaction_watched = $summary_data['liveaction_watched'];
	$anime_length = $summary_data['anime_length'];
	$manga_length = $summary_data['manga_length'];
	$liveaction_length = $summary_data['liveaction_length'];
	$comments_left = $summary_data['comments_left'];
	$most_commented_version = $summary_data['most_commented_version'];
	$anime_rank = $summary_data['anime_rank'];
	$manga_rank = $summary_data['manga_rank'];
	$liveaction_rank = $summary_data['liveaction_rank'];
	$total_users = $summary_data['total_users'];

	mysqli_free_result($result);

	if (!DISABLE_COMMUNITY && !SITE_IS_HENTAI) {
		//Get data via community API - assume zero if failed
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/get_user_yearly_stats');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 
			  json_encode(array(
			  	'username' => $user['username'],
			  	'year' => date('Y')-1,
			  	)));
		$output = curl_exec($curl);
		
		curl_close($curl);

		$result = json_decode($output);

		if (empty($result) || $result->status!='ok') {
			$community_posts = 0;
			$most_commented_post = '';
		} else {
			$community_posts = $result->number_of_posts;
			$most_commented_post = $result->most_posted_topic;
		}
	} else {
		$community_posts = 0;
		$most_commented_post = '';
	}

	$anime_length = intval($anime_length/3600);
	$liveaction_length = intval($liveaction_length/3600);
?>
<div class="yearly-summary theme-dark">
	<div class="yearly-summary-toggle-mute fa fa-volume-high"></div>
	<input id="anime_watched" type="hidden" value="<?php echo $anime_watched; ?>">
	<input id="manga_watched" type="hidden" value="<?php echo $manga_watched; ?>">
	<input id="liveaction_watched" type="hidden" value="<?php echo $liveaction_watched; ?>">
	<input id="hours_length" type="hidden" value="<?php echo $anime_length+$liveaction_length; ?>">
	<input id="pages_length" type="hidden" value="<?php echo $manga_length; ?>">
	<input id="comments_left" type="hidden" value="<?php echo $comments_left; ?>">
	<input id="community_posts" type="hidden" value="<?php echo $community_posts; ?>">
	<audio id="bg_audio" src="<?php echo STATIC_URL; ?>/various/earning_happiness_looped.mp3"></audio>
	<img class="background" id="background-main" src="<?php echo STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'; ?>" alt="">
	<img class="background" id="background-anime" src="<?php echo count($anime)>0 ? (STATIC_URL.'/images/featured/version_'.$anime[0]['id'].'.jpg') : (STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'); ?>" alt="" style="display: none;">
	<img class="background" id="background-manga" src="<?php echo count($manga)>0 ? (STATIC_URL.'/images/featured/version_'.$manga[0]['id'].'.jpg') : (STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'); ?>" alt="" style="display: none;">
	<img class="background" id="background-liveaction" src="<?php echo count($liveaction)>0 ? (STATIC_URL.'/images/featured/version_'.$liveaction[0]['id'].'.jpg') : (STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'); ?>" alt="" style="display: none;">
	<div id="yearly-summary-start" class="yearly-summary-slide">
		<img class="yearly-summary-avatar" src="<?php echo get_user_avatar_url($user); ?>">
		<span class="yearly-summary-header"><?php echo sprintf(lang('users.yearly_summary.main_header'), date('Y')-1, CURRENT_SITE_NAME); ?></span>
		<span class="yearly-summary-text"><?php echo lang('users.yearly_summary.main_explanation'); ?></span>
		<a id="yearly-summary-button-start" class="yearly-summary-button"><?php echo lang('users.yearly_summary.button_begin'); ?></a>
	</div>
	<div id="yearly-summary-totals" class="yearly-summary-slide" style="display: none;">
		<div class="yearly-summary-header"><?php echo sprintf(lang('users.yearly_summary.counters_header'), $user['username'], date('Y')-1); ?></div>
		<div class="yearly-summary-text yearly-summary-counters">
			<div class="yearly-summary-counter" id="yearly-summary-anime-counter" style="opacity: 0;">
				<div id="yearly-summary-anime-total">0</div>
				<div class="yearly-summary-counter-type"><?php echo $anime_watched==1 ? lang('users.yearly_summary.anime.single') : lang('users.yearly_summary.anime.plural'); ?></div>
			</div>
			<div class="yearly-summary-counter" id="yearly-summary-manga-counter" style="opacity: 0;">
				<div id="yearly-summary-manga-total">0</div>
				<div class="yearly-summary-counter-type"><?php echo $manga_watched==1 ? lang('users.yearly_summary.manga.single') : lang('users.yearly_summary.manga.plural'); ?></div>
			</div>
<?php
	if (!SITE_IS_HENTAI) {
?>
			<div class="yearly-summary-counter" id="yearly-summary-liveaction-counter" style="opacity: 0;">
				<div id="yearly-summary-liveaction-total">0</div>
				<div class="yearly-summary-counter-type"><?php echo $liveaction_watched==1 ? lang('users.yearly_summary.liveaction.single') : lang('users.yearly_summary.liveaction.plural'); ?></div>
			</div>
<?php
	}
?>
		</div>
		<div class="yearly-summary-text" id="yearly-summary-total-time-counters-header" style="opacity: 0;"><?php echo lang('users.yearly_summary.counters_total'); ?></div>
		<div class="yearly-summary-text yearly-summary-counters" id="yearly-summary-total-time-counters" style="opacity: 0;">
			<div class="yearly-summary-counter">
				<div id="yearly-summary-hours-total-length">0</div>
				<div class="yearly-summary-counter-type"><?php echo $anime_length+$liveaction_length==1 ? lang('users.yearly_summary.hours_seen.single') : lang('users.yearly_summary.hours_seen.plural'); ?></div>
			</div>
			<div class="yearly-summary-counter">
				<div id="yearly-summary-pages-total-length">0</div>
				<div class="yearly-summary-counter-type"><?php echo $manga_length==1 ? lang('users.yearly_summary.pages_read.single') : lang('users.yearly_summary.pages_read.plural'); ?></div>
			</div>
		</div>
	</div>
	<div id="yearly-summary-most-popular-anime" class="yearly-summary-slide" style="display: none;">
<?php
	if ($anime_watched==0) {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.animes_header'); ?></div>
		<span class="yearly-summary-text yearly-summary-empty-list" id="yearly-summary-anime-rank" style="opacity: 0;"><i class="fa fa-face-sad-tear"></i> <?php echo sprintf(lang('users.yearly_summary.animes_empty'), date('Y')); ?></span>
<?php
	} else {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.animes_header'); ?></div>
<?php
		$i=1;
		foreach ($anime as $data) {
?>
		<div class="yearly-summary-text yearly-summary-series" id="anime-series-<?php echo $i; ?>">
			<img src="<?php echo STATIC_URL.'/images/covers/version_'.$data['id'].'.jpg'; ?>">
			<div class="yearly-summary-series-data">
				<div class="yearly-summary-series-title"><?php echo htmlspecialchars($data['title']); ?></div>
				<div class="yearly-summary-series-counter"><?php echo get_relative_time_spent($data['total_length']); ?></div>
			</div>
		</div>
<?php
			$i++;
		}
?>
		<span class="yearly-summary-text" id="yearly-summary-anime-rank" style="opacity: 0;"><i class="fa fa-award"></i> <?php echo sprintf(lang('users.yearly_summary.animes_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$anime_rank)/$total_users*100, 1))); ?></span>
<?php
	}
?>
	</div>
	<div id="yearly-summary-most-popular-manga" class="yearly-summary-slide" style="display: none;">
<?php
	if ($manga_watched==0) {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.mangas_header'); ?></div>
		<span class="yearly-summary-text yearly-summary-empty-list" id="yearly-summary-manga-rank" style="opacity: 0;"><i class="fa fa-face-sad-tear"></i> <?php echo sprintf(lang('users.yearly_summary.mangas_empty'), date('Y')); ?></span>
<?php
	} else {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.mangas_header'); ?></div>
<?php
		$i=1;
		foreach ($manga as $data) {
?>
		<div class="yearly-summary-text yearly-summary-series" id="manga-series-<?php echo $i; ?>">
			<img src="<?php echo STATIC_URL.'/images/covers/version_'.$data['id'].'.jpg'; ?>">
			<div class="yearly-summary-series-data">
				<div class="yearly-summary-series-title"><?php echo htmlspecialchars($data['title']); ?></div>
				<div class="yearly-summary-series-counter"><?php echo get_relative_pages_read($data['total_length']); ?></div>
			</div>
		</div>
<?php
			$i++;
		}
?>
		<span class="yearly-summary-text" id="yearly-summary-manga-rank" style="opacity: 0;"><i class="fa fa-award"></i> <?php echo sprintf(lang('users.yearly_summary.mangas_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$manga_rank)/$total_users*100, 1))); ?></span>
<?php
	}
?>
	</div>
	<div id="yearly-summary-most-popular-liveaction" class="yearly-summary-slide" style="display: none;">
<?php
	if (SITE_IS_HENTAI) {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.liveactions_header.hentai_filler'); ?></div>
		<span class="yearly-summary-text yearly-summary-empty-list" id="yearly-summary-liveaction-rank" style="opacity: 0;"><?php echo sprintf(lang('users.yearly_summary.liveactions_empty.hentai_filler'), MAIN_SITE_NAME); ?></span>
<?php
	} else if ($liveaction_watched==0) {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.liveactions_header'); ?></div>
		<span class="yearly-summary-text yearly-summary-empty-list" id="yearly-summary-liveaction-rank" style="opacity: 0;"><i class="fa fa-face-sad-tear"></i> <?php echo sprintf(lang('users.yearly_summary.liveactions_empty'), date('Y')); ?></span>
<?php
	} else {
?>
		<div class="yearly-summary-header"><?php echo lang('users.yearly_summary.liveactions_header'); ?></div>
<?php
		$i=1;
		foreach ($liveaction as $data) {
?>
		<div class="yearly-summary-text yearly-summary-series" id="liveaction-series-<?php echo $i; ?>">
			<img src="<?php echo STATIC_URL.'/images/covers/version_'.$data['id'].'.jpg'; ?>">
			<div class="yearly-summary-series-data">
				<div class="yearly-summary-series-title"><?php echo htmlspecialchars($data['title']); ?></div>
				<div class="yearly-summary-series-counter"><?php echo get_relative_time_spent($data['total_length']); ?></div>
			</div>
		</div>
<?php
			$i++;
		}
?>
		<span class="yearly-summary-text" id="yearly-summary-liveaction-rank" style="opacity: 0;"><i class="fa fa-award"></i> <?php echo sprintf(lang('users.yearly_summary.liveactions_rank'), str_replace('.',lang('generic.decimal_point'),round(($total_users-$liveaction_rank)/$total_users*100, 1))); ?></span>
<?php
	}
?>
	</div>
	<div id="yearly-summary-community" class="yearly-summary-slide" style="display: none;">
		<span class="yearly-summary-header"><?php echo lang('users.yearly_summary.community_header'); ?></span>
		<div class="yearly-summary-text yearly-summary-counters">
			<div class="yearly-summary-counter" id="yearly-summary-comments-counter" style="opacity: 0;">
				<div id="yearly-summary-comments-total">0</div>
				<div class="yearly-summary-counter-type"><?php echo $comments_left==1 ? lang('users.yearly_summary.comments_left.single') : lang('users.yearly_summary.comments_left.plural'); ?></div>
			</div>
<?php
	if (!DISABLE_COMMUNITY && !SITE_IS_HENTAI) {
?>
			<div class="yearly-summary-counter" id="yearly-summary-community-counter" style="opacity: 0;">
				<div id="yearly-summary-community-total">0</div>
				<div class="yearly-summary-counter-type"><?php echo $community_posts==1 ? lang('users.yearly_summary.community_posts.single') : lang('users.yearly_summary.community_posts.plural'); ?></div>
			</div>
<?php
	}
?>
		</div>
<?php
	if ($comments_left==0) {
?>
		<span class="yearly-summary-text" id="yearly-summary-community-extra-series" style="opacity: 0;"><i class="fa  fa-face-sad-tear"></i> <?php echo lang('users.yearly_summary.community_comments_empty'); ?></span>
<?php
	} else {
?>
		<span class="yearly-summary-text" id="yearly-summary-community-extra-series" style="opacity: 0;"><i class="fa fa-star"></i> <?php echo lang('users.yearly_summary.community_most_commented'); ?> <strong><?php echo $most_commented_version; ?></strong></span>
<?php
	}
	if (!DISABLE_COMMUNITY && !SITE_IS_HENTAI && $community_posts==0) {
?>
		<span class="yearly-summary-text" id="yearly-summary-community-extra-topic" style="opacity: 0;"><i class="fa fa-face-sad-tear"></i> <?php echo lang('users.yearly_summary.community_posts_empty'); ?></span>
<?php
	} else if (!DISABLE_COMMUNITY && !SITE_IS_HENTAI) {
?>
		<span class="yearly-summary-text" id="yearly-summary-community-extra-topic" style="opacity: 0;"><i class="fa fa-comments"></i> <?php echo lang('users.yearly_summary.community_most_posts'); ?> <strong><?php echo $most_commented_post; ?></strong></span>
<?php
	}
?>
	</div>
	<div id="yearly-summary-restart" class="yearly-summary-slide" style="display: none;">
		<span class="yearly-summary-header"><?php echo sprintf(lang('users.yearly_summary.end_header'), date('Y')); ?></span>
		<span class="yearly-summary-text"><?php echo sprintf(lang('users.yearly_summary.end_explanation'), date('Y')-1); ?></span>
		<div><a id="yearly-summary-button-restart" class="yearly-summary-button"><span class="fa fa-rotate-left"></span> <?php echo lang('users.yearly_summary.button_rewatch'); ?></a></div>
		<div><a id="yearly-summary-button-download" class="yearly-summary-button" href="get_user_yearly_summary_image.php"><span class="fa fa-download"></span> <?php echo lang('users.yearly_summary.button_download'); ?></a></div>
		<div class="yearly-summary-note"><?php echo lang('users.yearly_summary.end_disclaimer'); ?></div>
	</div>
</div>
<?php
} else {
?>
<div class="yearly-summary theme-dark">
	<img class="background" id="background-main" src="<?php echo STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'; ?>" alt="">
	<div id="yearly-summary-start" class="yearly-summary-slide">
		<span class="yearly-summary-text"><?php echo lang('users.yearly_summary.unavailable'); ?></span>
	</div>
</div>
<?php
}
require_once(__DIR__.'/../common/footer.inc.php');
?>
