<?php
//This file sets config depending on the hostname used to display the site
//This allows customization but keeping the same codebase
switch (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'www.fansubs.cat') {
	case 'www.fansubs.cat':
	case 'wwwv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://wwwv2.fansubs.cat",
			'own_js' => "main.js",
			'own_css' => "main.css",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'site_title' => "Fansubs.cat",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
			'is_catalogue' => FALSE,
		);
		break;
	case 'noticies.fansubs.cat':
	case 'noticiesv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://noticiesv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "news.js",
			'own_css' => "news.css",
			'site_title' => "Fansubs.cat - Les notícies dels fansubs en català",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
			'is_catalogue' => FALSE,
		);
		break;
	case 'usuaris.fansubs.cat':
	case 'usuarisv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://usuarisv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "users.js",
			'own_css' => "users.css",
			'site_title' => "Fansubs.cat",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
			'is_catalogue' => FALSE,
		);
		break;
	case 'equips.fansubs.cat':
	case 'equipsv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://equipsv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "teams.js",
			'own_css' => "teams.css",
			'site_title' => "Equips | Fansubs.cat",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
			'is_catalogue' => FALSE,
		);
		break;
	case 'manga.fansubs.cat':
	case 'mangav2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://mangav2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/manga.jpg",
			'own_js' => "catalogue.js",
			'own_css' => "catalogue.css",
			'site_title' => "Fansubs.cat - Manga en català",
			'site_description' => "Aquí podràs llegir en línia tot el manga editat pels fansubs en català!",
			'is_catalogue' => TRUE,
		);
		$cat_config = array(
			'site_robot_message' => "Fansubs.cat et permet llegir en línia més de %d mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.",
			'items_type' => "manga",
			'filmsoneshots' => "One-shots",
			'filmsoneshots_icon' => "fa-book-open",
			'filmsoneshots_s' => "One-shot",
			'serialized' => "Serialitzats",
			'serialized_icon' => "fa-book",
			'filmsoneshots_slug' => "one-shots",
			'serialized_slug' => "serialitzats",
			'filmsoneshots_slug_internal' => "oneshots",
			'serialized_slug_internal' => "serialized",
			'filmsoneshots_db' => "oneshot",
			'serialized_db' => "serialized",
			'filmsoneshots_tadaima_forum_id' => "9",
			'serialized_tadaima_forum_id' => "9",
			'items_string_s' => "manga",
			'items_string_p' => "mangues",
			'items_string_del' => "del manga",
			'being_published' => "en publicació",
			'more_divisions_available' => "Hi ha més volums sense contingut disponible. Prem per a mostrar-los tots.",
			'division_name' => "Volum",
			'division_name_lc' => "volum",
			'preview_prefix' => "Manga",
			//Sections
			'section_last_updated' => "<i class=\"iconsm fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"iconsm fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"iconsm fa fa-fw fa-dice\"></i> A l'atzar",
			'section_popular' => "<i class=\"iconsm fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"iconsm fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"iconsm fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_results' => "<i class=\"iconsm fa fa-fw fa-book-open\"></i> Mangues",
			'section_search_other_results' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts",
			'section_related' => "<i class=\"iconsm fa fa-fw fa-book-open\"></i> Mangues recomanats",
			'section_related_other' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
			'view_now' => "Llegeix-lo ara",
			'option_show_cancelled' => "Mostra els mangues cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els mangues amb algun capítol sense enllaç vàlid",
		);
		break;
	case 'accioreal.fansubs.cat':
	case 'acciorealv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://acciorealv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/liveaction.jpg",
			'own_js' => "catalogue.js",
			'own_css' => "catalogue.css",
			'site_title' => "Fansubs.cat - Acció real en català",
			'site_description' => "Aquí podràs veure en línia tot el contingut d'acció real subtitulat pels fansubs en català!",
			'is_catalogue' => TRUE,
		);
		$cat_config = array(
			'site_robot_message' => "Fansubs.cat et permet veure en streaming més de %d continguts d'acció real subtitulats en català. Ara pots gaudir de tot el contingut d'acció real de tots els fansubs en català en un únic lloc.",
			'items_type' => "liveaction",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-tv",
			'filmsoneshots_slug' => "films",
			'serialized_slug' => "series",
			'filmsoneshots_slug_internal' => "movies",
			'serialized_slug_internal' => "series",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "16",
			'items_string_s' => "contingut",
			'items_string_p' => "continguts",
			'items_string_del' => "del contingut",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Acció real",
			//Sections
			'section_last_updated' => "<i class=\"iconsm fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"iconsm fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"iconsm fa fa-fw fa-dice\"></i> A l'atzar",
			'section_popular' => "<i class=\"iconsm fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"iconsm fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"iconsm fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_results' => "<i class=\"iconsm fa fa-fw fa-tv\"></i> Continguts d’acció real",
			'section_search_other_results' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts",
			'section_related' => "<i class=\"iconsm fa fa-fw fa-tv\"></i> Continguts d’acció real recomanats",
			'section_related_other' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
			'view_now' => "Mira'l ara",
			'option_show_cancelled' => "Mostra els continguts d'acció real cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els continguts d'acció real amb algun capítol sense enllaç vàlid",
		);
		break;
	case 'anime.fansubs.cat':
	case 'animev2.fansubs.cat':
	default:
		$site_config = array(
			'base_url' => "https://animev2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/anime.jpg",
			'own_js' => "catalogue.js",
			'own_css' => "catalogue.css",
			'site_title' => "Fansubs.cat - Anime en català",
			'site_description' => "Aquí podràs veure en línia tot l'anime subtitulat pels fansubs en català!",
			'is_catalogue' => TRUE,
		);
		$cat_config = array(
			'site_robot_message' => "Fansubs.cat et permet veure en streaming més de %d animes subtitulats en català. Ara pots gaudir de tot l'anime de tots els fansubs en català en un únic lloc.",
			'items_type' => "anime",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-tv",
			'filmsoneshots_slug' => "films",
			'serialized_slug' => "series",
			'filmsoneshots_slug_internal' => "movies",
			'serialized_slug_internal' => "series",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "10",
			'items_string_s' => "anime",
			'items_string_p' => "animes",
			'items_string_del' => "de l'anime",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Anime",
			//Sections
			'section_last_updated' => "<i class=\"iconsm fa fa-fw fa-clock-rotate-left\"></i> Darreres actualitzacions",
			'section_last_completed' => "<i class=\"iconsm fa fa-fw fa-check\"></i> Finalitzats recentment",
			'section_random' => "<i class=\"iconsm fa fa-fw fa-dice\"></i> A l'atzar",
			'section_popular' => "<i class=\"iconsm fa fa-fw fa-fire\"></i> Més populars",
			'section_more_recent' => "<i class=\"iconsm fa fa-fw fa-stopwatch\"></i> Més actuals",
			'section_best_rated' => "<i class=\"iconsm fa fa-fw fa-heart\"></i> Més ben valorats",
			'section_search_results' => "<i class=\"iconsm fa fa-fw fa-tv\"></i> Animes",
			'section_search_other_results' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts",
			'section_related' => "<i class=\"iconsm fa fa-fw fa-tv\"></i> Animes recomanats",
			'section_related_other' => "<i class=\"iconsm fa fa-fw fa-square-plus\"></i> Altres continguts recomanats",
			'view_now' => "Mira'l ara",
			'option_show_cancelled' => "Mostra els animes cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els animes amb algun capítol sense enllaç vàlid",
		);
		break;
}
?>
