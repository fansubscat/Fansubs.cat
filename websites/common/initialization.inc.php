<?php
require_once(__DIR__.'/../../common/config/config.inc.php');

function lang($string) {
	if (!array_key_exists($string, LANGUAGE_STRINGS)) {
		die('Missing string: '.$string);
	}
	return LANGUAGE_STRINGS[$string];
}

setlocale(LC_ALL, SITE_LOCALE);

//Use this if language does not have all strings available:
//$fallback_language = json_decode(file_get_contents(__DIR__.'/../../common/languages/lang_en.json'),TRUE) or die('Cannot load English language');
//$default_language = json_decode(file_get_contents(__DIR__.'/../../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE) or die('Cannot load default language');
//$merged_language = array_merge($fallback_language, $default_language);
//define('LANGUAGE_STRINGS', $merged_language);

//Use this if language has all strings available:
//define('LANGUAGE_STRINGS', json_decode(file_get_contents(__DIR__.'/../../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE));
define('LANGUAGE_STRINGS', json_decode(file_get_contents(__DIR__.'/../../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE));

if (str_ends_with(strtolower($_SERVER['HTTP_HOST']), HENTAI_DOMAIN)) {
	define('CURRENT_DOMAIN', HENTAI_DOMAIN);
	define('OTHER_DOMAIN', MAIN_DOMAIN);
	define('CURRENT_SITE_NAME', HENTAI_SITE_NAME);
	define('CURRENT_SITE_NAME_ACCOUNT', sprintf(lang('generic.two_words_conjugation'), HENTAI_SITE_NAME, MAIN_SITE_NAME));
	define('SITE_IS_HENTAI', TRUE);
	define('SOCIAL_LINK_BLUESKY', HENTAI_SOCIAL_LINK_BLUESKY);
	define('SOCIAL_LINK_MASTODON', HENTAI_SOCIAL_LINK_MASTODON);
	define('SOCIAL_LINK_TELEGRAM', HENTAI_SOCIAL_LINK_TELEGRAM);
	define('SOCIAL_LINK_X', HENTAI_SOCIAL_LINK_X);
} else {
	define('CURRENT_DOMAIN', MAIN_DOMAIN);
	define('OTHER_DOMAIN', HENTAI_DOMAIN);
	define('CURRENT_SITE_NAME', MAIN_SITE_NAME);
	define('CURRENT_SITE_NAME_ACCOUNT', MAIN_SITE_NAME);
	define('SITE_IS_HENTAI', FALSE);
	define('SOCIAL_LINK_BLUESKY', MAIN_SOCIAL_LINK_BLUESKY);
	define('SOCIAL_LINK_MASTODON', MAIN_SOCIAL_LINK_MASTODON);
	define('SOCIAL_LINK_TELEGRAM', MAIN_SOCIAL_LINK_TELEGRAM);
	define('SOCIAL_LINK_X', MAIN_SOCIAL_LINK_X);
}

//Website URLs (no final slash)
//These URLs do not change depending on the host, they are always on the main domain:
define('ADVENT_URL', 'https://'.ADVENT_SUBDOMAIN.'.'.MAIN_DOMAIN);
define('ADMIN_URL', 'https://'.ADMIN_SUBDOMAIN.'.'.MAIN_DOMAIN);
define('RESOURCES_URL', 'https://'.RESOURCES_SUBDOMAIN.'.'.MAIN_DOMAIN);
define('STATUS_URL', 'https://'.STATUS_SUBDOMAIN.'.'.MAIN_DOMAIN);
//These URLS *DO* change depending on the host:
define('MAIN_URL', 'https://'.MAIN_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('ANIME_URL', 'https://'.ANIME_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('MANGA_URL', 'https://'.MANGA_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('LIVEACTION_URL', 'https://'.LIVEACTION_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('NEWS_URL', 'https://'.NEWS_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('USERS_URL', 'https://'.USERS_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('STATIC_URL', 'https://'.STATIC_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('API_URL', 'https://'.API_SUBDOMAIN.'.'.CURRENT_DOMAIN);

//Cookie params
define('COOKIE_DOMAIN', '.'.CURRENT_DOMAIN);


//We set parameters depending on the hostname used to display the site
//This allows customization but keeping the same codebase
$server_url = 'https://' . (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown');

define('SITE_BASE_URL', $server_url);
switch (strtolower($server_url)) {
	case NEWS_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', lang('news.page_title.hentai').' | '.HENTAI_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('news.page_description.hentai'), HENTAI_SITE_NAME));
		} else {
			define('SITE_TITLE', lang('news.page_title').' | '.MAIN_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('news.page_description'), MAIN_SITE_NAME));
		}
		define('SITE_INTERNAL_NAME', 'news');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'news');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case USERS_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', HENTAI_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('main.page_description.hentai'), HENTAI_SITE_NAME));
		} else {
			define('SITE_TITLE', MAIN_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('main.page_description'), MAIN_SITE_NAME));
		}
		define('SITE_INTERNAL_NAME', 'users');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'users');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case ANIME_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', lang('catalogue.page_title.anime.hentai').' | '.HENTAI_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('catalogue.page_description.anime.hentai'), HENTAI_SITE_NAME));
			define('CATALOGUE_ROBOT_MESSAGE', sprintf(lang('catalogue.robot_message.anime.hentai'), HENTAI_SITE_NAME));
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', lang('catalogue.anime.common_themes_same_type.hentai'));
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', lang('catalogue.anime.common_themes_other_type.hentai'));
			define('CATALOGUE_HAS_FANDUBS', FALSE);
		} else {
			define('SITE_TITLE', lang('catalogue.page_title.anime').' | '.MAIN_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('catalogue.page_description.anime'), MAIN_SITE_NAME));
			define('CATALOGUE_ROBOT_MESSAGE', sprintf(lang('catalogue.robot_message.anime'), MAIN_SITE_NAME));
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', lang('catalogue.anime.common_themes_same_type'));
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', lang('catalogue.anime.common_themes_other_type'));
			define('CATALOGUE_HAS_FANDUBS', TRUE);
		}
		define('SITE_INTERNAL_NAME', 'anime');
		define('SITE_PREVIEW_IMAGE', 'anime');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'anime');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-film');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-tv');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', lang('catalogue.generic.movies'));
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', lang('catalogue.generic.series'));
		define('CATALOGUE_NO_RESULTS_FOUND_STRING', lang('catalogue.anime.no_results_found'));
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', lang('catalogue.generic.first_broadcast_year'));
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', lang('catalogue.generic.continue_watching'));
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', lang('catalogue.generic.series_completed_recently'));
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', lang('catalogue.generic.movies_completed_recently'));
		define('CATALOGUE_MOST_RECENT_STRING', lang('catalogue.anime.recently_published'));
		define('CATALOGUE_BEST_SERIALIZED_STRING', lang('catalogue.generic.good_rated_series'));
		define('CATALOGUE_BEST_SINGLE_STRING', lang('catalogue.generic.good_rated_movies'));
		define('CATALOGUE_FEATURED_SINGLE_STRING', lang('catalogue.anime.featured_series'));
		define('CATALOGUE_SEASON_STRING_PLURAL', lang('catalogue.generic.number_of_seasons'));
		define('CATALOGUE_SEASONAL_SERIES_STRING', lang('catalogue.anime.seasonal_series'));
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case MANGA_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', lang('catalogue.page_title.manga.hentai').' | '.HENTAI_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('catalogue.page_description.manga.hentai'), HENTAI_SITE_NAME));
			define('CATALOGUE_ROBOT_MESSAGE', sprintf(lang('catalogue.robot_message.manga.hentai'), HENTAI_SITE_NAME));
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', lang('catalogue.manga.common_themes_same_type.hentai'));
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', lang('catalogue.manga.common_themes_other_type.hentai'));
		} else {
			define('SITE_TITLE', lang('catalogue.page_title.manga').' | '.MAIN_SITE_NAME);
			define('SITE_DESCRIPTION', sprintf(lang('catalogue.page_description.manga'), MAIN_SITE_NAME));
			define('CATALOGUE_ROBOT_MESSAGE', sprintf(lang('catalogue.robot_message.manga'), MAIN_SITE_NAME));
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', lang('catalogue.manga.common_themes_same_type'));
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', lang('catalogue.manga.common_themes_other_type'));
		}
		define('SITE_INTERNAL_NAME', 'manga');
		define('SITE_PREVIEW_IMAGE', 'manga');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'manga');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', lang('catalogue.manga.oneshots'));
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', lang('catalogue.manga.serialized'));
		define('CATALOGUE_NO_RESULTS_FOUND_STRING', lang('catalogue.manga.no_results_found'));
		define('CATALOGUE_MINIMUM_DURATION', 1);
		define('CATALOGUE_MAXIMUM_DURATION', 100);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'pages');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', lang('catalogue.manga.first_publish_year'));
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', TRUE);
		define('CATALOGUE_HAS_FANDUBS', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-book-open');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', lang('catalogue.manga.continue_reading'));
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', lang('catalogue.manga.series_completed_recently'));
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', lang('catalogue.manga.oneshots_completed_recently'));
		define('CATALOGUE_MOST_RECENT_STRING', lang('catalogue.manga.recently_published'));
		define('CATALOGUE_BEST_SERIALIZED_STRING', lang('catalogue.manga.good_rated_series'));
		define('CATALOGUE_BEST_SINGLE_STRING', lang('catalogue.manga.good_rated_oneshots'));
		define('CATALOGUE_FEATURED_SINGLE_STRING', lang('catalogue.manga.featured_series'));
		define('CATALOGUE_SEASON_STRING_PLURAL', lang('catalogue.manga.number_of_volumes'));
		define('CATALOGUE_SEASONAL_SERIES_STRING', lang('catalogue.manga.seasonal_series'));
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case LIVEACTION_URL:
		define('SITE_TITLE', lang('catalogue.page_title.liveaction').' | '.MAIN_SITE_NAME);
		define('SITE_DESCRIPTION', sprintf(lang('catalogue.page_description.liveaction'), MAIN_SITE_NAME));
		define('CATALOGUE_ROBOT_MESSAGE', sprintf(lang('catalogue.robot_message.liveaction'), MAIN_SITE_NAME));
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', lang('catalogue.liveaction.common_themes_same_type'));
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', lang('catalogue.liveaction.common_themes_other_type'));
		define('SITE_INTERNAL_NAME', 'liveaction');
		define('SITE_PREVIEW_IMAGE', 'liveaction');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'liveaction');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-film');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-tv');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', lang('catalogue.generic.movies'));
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', lang('catalogue.generic.series'));
		define('CATALOGUE_NO_RESULTS_FOUND_STRING', lang('catalogue.anime.no_results_found'));
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyDramaList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', lang('catalogue.generic.first_broadcast_year'));
		define('CATALOGUE_HAS_DEMOGRAPHIES', FALSE);
		define('CATALOGUE_HAS_FANDUBS', FALSE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 25);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', lang('catalogue.generic.continue_watching'));
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', lang('catalogue.generic.series_completed_recently'));
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', lang('catalogue.generic.movies_completed_recently'));
		define('CATALOGUE_MOST_RECENT_STRING', lang('catalogue.liveaction.recently_published'));
		define('CATALOGUE_BEST_SERIALIZED_STRING', lang('catalogue.generic.good_rated_series'));
		define('CATALOGUE_BEST_SINGLE_STRING', lang('catalogue.generic.good_rated_movies'));
		define('CATALOGUE_FEATURED_SINGLE_STRING', lang('catalogue.liveaction.featured_series'));
		define('CATALOGUE_SEASON_STRING_PLURAL', lang('catalogue.generic.number_of_seasons'));
		define('CATALOGUE_SEASONAL_SERIES_STRING', lang('catalogue.liveaction.seasonal_series'));
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case ADVENT_URL:
		define('SITE_TITLE', MAIN_SITE_NAME);
		define('SITE_DESCRIPTION', lang('advent.page_description'));
		define('SITE_INTERNAL_NAME', 'advent');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case API_URL:
		break;
	case MAIN_URL:
	default:
		if (!defined('SITE_TITLE_OVERRIDE')) {
			//It is already defined at the main index page
			if (SITE_IS_HENTAI) {
				define('SITE_TITLE', HENTAI_SITE_NAME);
			} else {
				define('SITE_TITLE', MAIN_SITE_NAME);
			}
		} else {
			if (SITE_IS_HENTAI) {
				define('SITE_TITLE', sprintf(lang('main.page_title.hentai'), HENTAI_SITE_NAME));
			} else {
				define('SITE_TITLE', sprintf(lang('main.page_title'), MAIN_SITE_NAME));
			}
		}
		if (SITE_IS_HENTAI) {
			define('SITE_DESCRIPTION', sprintf(lang('main.page_description.hentai'), HENTAI_SITE_NAME));
		} else {
			define('SITE_DESCRIPTION', sprintf(lang('main.page_description'), MAIN_SITE_NAME));
		}
		define('SITE_INTERNAL_NAME', 'main');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'main');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
}
?>
