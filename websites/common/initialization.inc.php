<?php
require_once(__DIR__.'/../../common/config.inc.php');

if (str_ends_with(strtolower($_SERVER['HTTP_HOST']), HENTAI_DOMAIN)) {
	define('CURRENT_DOMAIN', HENTAI_DOMAIN);
	define('OTHER_DOMAIN', MAIN_DOMAIN);
	define('CURRENT_SITE_NAME', HENTAI_SITE_NAME);
	define('CURRENT_SITE_NAME_ACCOUNT', HENTAI_SITE_NAME.' i '.MAIN_SITE_NAME);
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
//These URLS *DO* change depending on the host:
define('MAIN_URL', 'https://'.MAIN_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('ANIME_URL', 'https://'.ANIME_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('MANGA_URL', 'https://'.MANGA_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('LIVEACTION_URL', 'https://'.LIVEACTION_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('NEWS_URL', 'https://'.NEWS_SUBDOMAIN.'.'.CURRENT_DOMAIN);
define('RESOURCES_URL', 'https://'.RESOURCES_SUBDOMAIN.'.'.CURRENT_DOMAIN);
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
			define('SITE_TITLE', 'Notícies del hentai dels fansubs en català | Hentai.cat');
			define('SITE_DESCRIPTION', 'Consulta les darreres notícies del hentai dels fansubs en català a Hentai.cat, el portal que en recopila totes les novetats!');
		} else {
			define('SITE_TITLE', 'Notícies dels fansubs en català | Fansubs.cat');
			define('SITE_DESCRIPTION', 'Consulta les darreres notícies dels fansubs en català a Fansubs.cat, el portal que en recopila totes les novetats!');
		}
		define('SITE_INTERNAL_NAME', 'news');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'news');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case USERS_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', 'Hentai.cat');
			define('SITE_DESCRIPTION', 'Hentai.cat és el portal on podràs gaudir del hentai dels fansubs en català en format anime o manga!');
		} else {
			define('SITE_TITLE', 'Fansubs.cat');
			define('SITE_DESCRIPTION', 'Fansubs.cat és el portal on podràs gaudir de l’anime, del manga i de tota la resta de contingut dels fansubs en català!');
		}
		define('SITE_INTERNAL_NAME', 'users');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'users');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case ANIME_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', 'Anime hentai en català | Hentai.cat');
			define('SITE_DESCRIPTION', 'Gaudeix de l’anime hentai en català a Hentai.cat, el portal que recopila tot l’anime hentai subtitulat pels diferents fansubs en català!');
			define('CATALOGUE_ROBOT_MESSAGE', 'Hentai.cat et permet veure en streaming més de %d animes hentai subtitulats en català. Ara pots gaudir de tot l’anime hentai de tots els fansubs en català en un únic lloc.');
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Animes hentai amb temàtiques en comú');
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Mangues hentai amb temàtiques en comú');
			define('CATALOGUE_HAS_FANDUBS', FALSE);
		} else {
			define('SITE_TITLE', 'Anime en català | Fansubs.cat');
			define('SITE_DESCRIPTION', 'Gaudeix de l’anime en català a Fansubs.cat, el portal que recopila tot l’anime subtitulat pels diferents fansubs en català!');
			define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d animes subtitulats en català. Ara pots gaudir de tot l’anime de tots els fansubs en català en un únic lloc.');
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Animes amb temàtiques en comú');
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
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
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'anime');
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera emissió');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua mirant');
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', 'Sèries completades fa poc');
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', 'Films completats fa poc');
		define('CATALOGUE_MOST_RECENT_STRING', 'Animes d’estrena recent');
		define('CATALOGUE_BEST_SERIALIZED_STRING', 'Sèries ben valorades');
		define('CATALOGUE_BEST_SINGLE_STRING', 'Els millors films');
		define('CATALOGUE_FEATURED_SINGLE_STRING', 'Anime destacat');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'temporades');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Capítols normals');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'Capítol únic');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Temporada');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més temporades sense elements disponibles. Prem aquí per a mostrar-les totes.');
		define('CATALOGUE_SEASONAL_SERIES_STRING', 'Anime de temporada');
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case MANGA_URL:
		if (SITE_IS_HENTAI) {
			define('SITE_TITLE', 'Manga hentai en català | Hentai.cat');
			define('SITE_DESCRIPTION', 'Gaudeix del manga hentai en català a Hentai.cat, el portal que recopila tot el manga hentai editat pels diferents fansubs en català!');
			define('CATALOGUE_ROBOT_MESSAGE', 'Hentai.cat et permet llegir en línia més de %d mangues hentai editats en català. Ara pots gaudir de tot el manga hentai de tots els fansubs en català en un únic lloc.');
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Mangues hentai amb temàtiques en comú');
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Animes hentai amb temàtiques en comú');
		} else {
			define('SITE_TITLE', 'Manga en català | Fansubs.cat');
			define('SITE_DESCRIPTION', 'Gaudeix del manga en català a Fansubs.cat, el portal que recopila tot el manga editat pels diferents fansubs en català!');
			define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet llegir en línia més de %d mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.');
			define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Mangues amb temàtiques en comú');
			define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
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
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'manga');
		define('CATALOGUE_MINIMUM_DURATION', 1);
		define('CATALOGUE_MAXIMUM_DURATION', 100);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'pages');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera publicació');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', TRUE);
		define('CATALOGUE_HAS_FANDUBS', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-book-open');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua llegint');
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', 'Mangues completats fa poc');
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', 'One-shots completats fa poc');
		define('CATALOGUE_MOST_RECENT_STRING', 'Mangues d’estrena recent');
		define('CATALOGUE_BEST_SERIALIZED_STRING', 'Mangues ben valorats');
		define('CATALOGUE_BEST_SINGLE_STRING', 'Els millors one-shots');
		define('CATALOGUE_FEATURED_SINGLE_STRING', 'Manga destacat');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'volums');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Volum únic');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'One-shot');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Volum');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més volums sense elements disponibles. Prem aquí per a mostrar-los tots.');
		define('CATALOGUE_SEASONAL_SERIES_STRING', 'Manga en publicació');
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case LIVEACTION_URL:
		define('SITE_TITLE', 'Imatge real en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Gaudeix de contingut d’imatge real en català a Fansubs.cat, el portal que recopila tot el «live action» subtitulat pels diferents fansubs en català!');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d continguts d’imatge real subtitulats en català. Ara pots gaudir de tot el contingut d’imatge real de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Continguts d’imatge real amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
		define('SITE_INTERNAL_NAME', 'liveaction');
		define('SITE_PREVIEW_IMAGE', 'liveaction');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'liveaction');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-film');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-tv');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'contingut d’imatge real');
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyDramaList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera emissió');
		define('CATALOGUE_HAS_DEMOGRAPHIES', FALSE);
		define('CATALOGUE_HAS_FANDUBS', FALSE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 25);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua mirant');
		define('CATALOGUE_LAST_FINISHED_SERIALIZED_STRING', 'Sèries completades fa poc');
		define('CATALOGUE_LAST_FINISHED_SINGLE_STRING', 'Films completats fa poc');
		define('CATALOGUE_MOST_RECENT_STRING', 'Continguts d’estrena recent');
		define('CATALOGUE_BEST_SERIALIZED_STRING', 'Sèries ben valorades');
		define('CATALOGUE_BEST_SINGLE_STRING', 'Els millors films');
		define('CATALOGUE_FEATURED_SINGLE_STRING', 'Contingut destacat');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'temporades');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Capítols normals');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'Capítol únic');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Temporada');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més temporades sense elements disponibles. Prem aquí per a mostrar-les totes.');
		define('CATALOGUE_SEASONAL_SERIES_STRING', 'Contingut de temporada');
		define('CATALOGUE_SEASONAL_SERIES_ICON', 'fa-fire');
		break;
	case ADVENT_URL:
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'Segueix el calendari d’advent dels fansubs en català! Cada dia hi trobaràs una petita sorpresa en forma d’anime o manga editat en català!');
		define('SITE_INTERNAL_NAME', 'advent');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
	case API_URL:
		break;
	case MAIN_URL:
	default:
		if (!defined('SITE_TITLE')) {
			//It is already defined at the main index page
			if (SITE_IS_HENTAI) {
				define('SITE_TITLE', 'Hentai.cat');
			} else {
				define('SITE_TITLE', 'Fansubs.cat');
			}
		}
		if (SITE_IS_HENTAI) {
			define('SITE_DESCRIPTION', 'Hentai.cat és el portal on podràs gaudir del hentai dels fansubs en català en format anime o manga!');
		} else {
			define('SITE_DESCRIPTION', 'Fansubs.cat és el portal on podràs gaudir de l’anime, del manga i de tota la resta de contingut dels fansubs en català!');
		}
		define('SITE_INTERNAL_NAME', 'main');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'main');
		define('SITE_IS_CATALOGUE', FALSE);
		break;
}
?>
