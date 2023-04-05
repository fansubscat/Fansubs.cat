<?php
//This file sets config depending on the hostname used to display the site
//This allows customization but keeping the same codebase
switch (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'wwwv2.fansubs.cat') {
	case 'wwwv2.fansubs.cat':
		define('SITE_BASE_URL', 'https://wwwv2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'main');
		define('SITE_INTERNAL_TYPE', 'main');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case 'noticiesv2.fansubs.cat':
		define('SITE_BASE_URL', 'https://noticiesv2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Notícies dels fansubs en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'news');
		define('SITE_INTERNAL_TYPE', 'news');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case 'usuarisv2.fansubs.cat':
		define('SITE_BASE_URL', 'https://usuarisv2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'users');
		define('SITE_INTERNAL_TYPE', 'users');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case 'mangav2.fansubs.cat':
		define('SITE_BASE_URL', 'https://mangav2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Manga en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs llegir en línia tot el manga editat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'manga');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'manga');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'manga');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet llegir en línia més de %d mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.');
/*		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME_SINGULAR', 'One-shot');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_TADAIMA_ID', 9);
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME_SINGULAR', 'Serialitzat');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_TADAIMA_ID', 9);
		define('CATALOGUE_DIVISION_NAME', 'Volum');
		define('CATALOGUE_DIVISION_NAME', 9);
			'division_name' => "Volum",
			'division_name_lc' => "volum",
			'preview_prefix' => "Manga",
			//Sections
			'section_continue_watching' => "<i class=\"fa fa-fw fa-eye\"></i> Continua llegint",
			'section_last_updated' => "<i class=\"fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"fa fa-fw fa-dice\"></i> A l’atzar",
			'section_popular' => "<i class=\"fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_anime' => "<i class=\"fa fa-fw fa-display\"></i> Resultats d’anime",
			'section_search_manga' => "<i class=\"fa fa-fw fa-book-open\"></i> Resultats de manga",
			'section_search_liveaction' => "<i class=\"fa fa-fw fa-clapperboard\"></i> Resultats d’imatge real",
			'section_related' => "<i class=\"fa fa-fw fa-book-open\"></i> Mangues recomanats",
			'option_show_cancelled' => "Mostra els mangues cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els mangues amb algun capítol sense enllaç vàlid",
		);*/
		break;
	case 'imatgerealv2.fansubs.cat':
		define('SITE_BASE_URL', 'https://imatgerealv2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Imatge real en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot el contingut d’imatge real subtitulat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'liveaction');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'liveaction');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-video');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-display');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'contingut d’imatge real');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d continguts d’imatge real subtitulats en català. Ara pots gaudir de tot el contingut d’imatge real de tots els fansubs en català en un únic lloc.');
/*		$cat_config = array(
			'site_robot_message' => "Fansubs.cat et permet veure en streaming més de %d continguts d’imatge real subtitulats en català. Ara pots gaudir de tot el contingut d’imatge real de tots els fansubs en català en un únic lloc.",
			'site_robot_message_hentai' => "Fansubs.cat et permet veure en streaming més de %d continguts d’imatge real subtitulats en català. Ara pots gaudir de tot el contingut d’imatge real de tots els fansubs en català en un únic lloc.",
			'items_type' => "liveaction",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-display",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "16",
			'items_string_s' => "contingut d’imatge real",
			'items_string_p' => "continguts d’imatge real",
			'items_string_del' => "del contingut d’imatge real",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Imatge real",
			//Sections
			'section_continue_watching' => "<i class=\"fa fa-fw fa-eye\"></i> Continua mirant",
			'section_last_updated' => "<i class=\"fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"fa fa-fw fa-dice\"></i> A l’atzar",
			'section_popular' => "<i class=\"fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_anime' => "<i class=\"fa fa-fw fa-display\"></i> Resultats d’anime",
			'section_search_manga' => "<i class=\"fa fa-fw fa-book-open\"></i> Resultats de manga",
			'section_search_liveaction' => "<i class=\"fa fa-fw fa-clapperboard\"></i> Resultats d’imatge real",
			'section_related' => "<i class=\"fa fa-fw fa-tv\"></i> Continguts d’imatge real recomanats",
			'section_related_other' => "<i class=\"fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
			'option_show_cancelled' => "Mostra els continguts d’imatge real cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els continguts d’imatge real amb algun capítol sense enllaç vàlid",
		);*/
		break;
	case 'animev2.fansubs.cat':
	default:
		define('SITE_BASE_URL', 'https://animev2.fansubs.cat');
		define('SITE_PATH', '');
		define('SITE_TITLE', 'Anime en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot l’anime subtitulat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'anime');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'anime');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-video');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-display');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'anime');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d animes subtitulats en català. Ara pots gaudir de tot l’anime de tots els fansubs en català en un únic lloc.');
		/*$cat_config = array(
			'items_type' => "anime",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-display",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "10",
			'items_string_s' => "anime",
			'items_string_p' => "animes",
			'items_string_del' => "de l’anime",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Anime",
			//Sections
			'section_continue_watching' => "<i class=\"fa fa-fw fa-eye\"></i> Continua mirant",
			'section_last_updated' => "<i class=\"fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"fa fa-fw fa-dice\"></i> A l’atzar",
			'section_popular' => "<i class=\"fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_anime' => "<i class=\"fa fa-fw fa-display\"></i> Resultats d’anime",
			'section_search_manga' => "<i class=\"fa fa-fw fa-book-open\"></i> Resultats de manga",
			'section_search_liveaction' => "<i class=\"fa fa-fw fa-clapperboard\"></i> Resultats d’imatge real",
			'section_related' => "<i class=\"fa fa-fw fa-tv\"></i> Animes recomanats",
			'section_related_other' => "<i class=\"fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
			'option_show_cancelled' => "Mostra els animes cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els animes amb algun capítol sense enllaç vàlid",
		);*/
		break;
	case 'hentaiv2.fansubs.cat':
		$path = explode('/', $_SERVER['REQUEST_URI']);
		$path = count($path)>1 ? $path[1] : '';
		if ($path=='anime') {
			define('SITE_BASE_URL', 'https://hentaiv2.fansubs.cat/anime');
			define('SITE_PATH', '/anime');
			define('SITE_TITLE', 'Anime hentai en català | Fansubs.cat');
			define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot l’anime hentai subtitulat pels fansubs en català!');
			define('SITE_INTERNAL_NAME', 'hentai');
			define('SITE_INTERNAL_TYPE', 'catalogue');
			define('SITE_IS_CATALOGUE', TRUE);
			define('SITE_IS_HENTAI', TRUE);
			define('CATALOGUE_ITEM_TYPE', 'anime');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-video');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-display');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
			define('CATALOGUE_ITEM_STRING_SINGULAR', 'anime');
			define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming anime hentai subtitulat en català. Ara pots gaudir de tot l’anime hentai de tots els fansubs en català en un únic lloc.');
			/*$cat_config = array(
				'items_type' => "anime",
				'filmsoneshots' => "Films",
				'filmsoneshots_icon' => "fa-video",
				'filmsoneshots_s' => "Film",
				'serialized' => "Sèries",
				'serialized_icon' => "fa-display",
				'filmsoneshots_db' => "movie",
				'serialized_db' => "series",
				'filmsoneshots_tadaima_forum_id' => "14",
				'serialized_tadaima_forum_id' => "10",
				'items_string_s' => "anime",
				'items_string_p' => "animes",
				'items_string_del' => "de l’anime",
				'being_published' => "en emissió",
				'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
				'division_name' => "Temporada",
				'division_name_lc' => "temporada",
				'preview_prefix' => "Anime",
				//Sections
				'section_continue_watching' => "<i class=\"fa fa-fw fa-eye\"></i> Continua mirant",
				'section_last_updated' => "<i class=\"fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
				'section_last_completed' => "<i class=\"fa fa-fw fa-check\"></i> Finalitzats recentment",
				'section_random' => "<i class=\"fa fa-fw fa-dice\"></i> A l’atzar",
				'section_popular' => "<i class=\"fa fa-fw fa-fire\"></i> Més populars",
				'section_more_recent' => "<i class=\"fa fa-fw fa-stopwatch\"></i> Més actuals",
				'section_best_rated' => "<i class=\"fa fa-fw fa-heart\"></i> Més ben valorats",
				'section_search_anime' => "<i class=\"fa fa-fw fa-display\"></i> Resultats d’anime",
				'section_search_manga' => "<i class=\"fa fa-fw fa-book-open\"></i> Resultats de manga",
				'section_search_liveaction' => "<i class=\"fa fa-fw fa-clapperboard\"></i> Resultats d’imatge real",
				'section_related' => "<i class=\"fa fa-fw fa-tv\"></i> Animes recomanats",
				'section_related_other' => "<i class=\"fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
				'option_show_cancelled' => "Mostra els animes cancel·lats o abandonats pels fansubs",
				'option_show_missing' => "Mostra els animes amb algun capítol sense enllaç vàlid",
			);*/
		} else if ($path=='manga') {
			define('SITE_BASE_URL', 'https://hentaiv2.fansubs.cat/manga');
			define('SITE_PATH', '/manga');
			define('SITE_TITLE', 'Manga hentai en català | Fansubs.cat');
			define('SITE_DESCRIPTION', 'Aquí podràs llegir en línia tot el manga hentai editat pels fansubs en català!');
			define('SITE_INTERNAL_NAME', 'hentai');
			define('SITE_INTERNAL_TYPE', 'catalogue');
			define('SITE_IS_CATALOGUE', TRUE);
			define('SITE_IS_HENTAI', TRUE);
			define('CATALOGUE_ITEM_TYPE', 'manga');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
			define('CATALOGUE_ITEM_STRING_SINGULAR', 'manga');
			define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet llegir en línia manga hentai en català. Ara pots gaudir de tot el manga hentai de tots els fansubs en català en un únic lloc.');
	/*		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME_SINGULAR', 'One-shot');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
			define('CATALOGUE_ITEM_SUBTYPE_SINGLE_TADAIMA_ID', 9);
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME_SINGULAR', 'Serialitzat');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
			define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_TADAIMA_ID', 9);
			define('CATALOGUE_DIVISION_NAME', 'Volum');
			define('CATALOGUE_DIVISION_NAME', 9);
				'division_name' => "Volum",
				'division_name_lc' => "volum",
				'preview_prefix' => "Manga",
				//Sections
				'section_continue_watching' => "<i class=\"fa fa-fw fa-eye\"></i> Continua llegint",
				'section_last_updated' => "<i class=\"fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
				'section_last_completed' => "<i class=\"fa fa-fw fa-check\"></i> Finalitzats recentment",
				'section_random' => "<i class=\"fa fa-fw fa-dice\"></i> A l’atzar",
				'section_popular' => "<i class=\"fa fa-fw fa-fire\"></i> Més populars",
				'section_more_recent' => "<i class=\"fa fa-fw fa-stopwatch\"></i> Més actuals",
				'section_best_rated' => "<i class=\"fa fa-fw fa-heart\"></i> Més ben valorats",
				'section_search_anime' => "<i class=\"fa fa-fw fa-display\"></i> Resultats d’anime",
				'section_search_manga' => "<i class=\"fa fa-fw fa-book-open\"></i> Resultats de manga",
				'section_search_liveaction' => "<i class=\"fa fa-fw fa-clapperboard\"></i> Resultats d’imatge real",
				'section_related' => "<i class=\"fa fa-fw fa-book-open\"></i> Mangues recomanats",
				'option_show_cancelled' => "Mostra els mangues cancel·lats o abandonats pels fansubs",
				'option_show_missing' => "Mostra els mangues amb algun capítol sense enllaç vàlid",
			);*/
		} else { //Used for the error page when not using a path
			define('SITE_BASE_URL', 'https://wwwv2.fansubs.cat');
			define('SITE_PATH', '');
			define('SITE_TITLE', 'Fansubs.cat');
			define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
			define('SITE_INTERNAL_NAME', 'main');
			define('SITE_INTERNAL_TYPE', 'main');
			define('SITE_IS_CATALOGUE', FALSE);
			define('SITE_IS_HENTAI', FALSE);
		}
		break;
}
?>
