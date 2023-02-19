<?php
//This file sets config depending on the hostname used to display the catalogue
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
		);
		break;
	case 'noticies.fansubs.cat':
	case 'noticiesv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://noticiesv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "news.js",
			'own_css' => "news.css",
			'site_title' => "Fansubs.cat",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
		);
		break;
	case 'usuaris.fansubs.cat':
	case 'usuarisv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://usuarisv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "users.js",
			'own_css' => "users.css",
			'site_title' => "Fansubs.cat - Perfil d’usuari",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
		);
		break;
	case 'equips.fansubs.cat':
	case 'equipsv2.fansubs.cat':
		$site_config = array(
			'base_url' => "https://equipsv2.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/common/images/social.jpg",
			'own_js' => "teams.js",
			'own_css' => "teams.css",
			'site_title' => "Fansubs.cat",
			'site_description' => "A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.",
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
			'site_description' => "Aquí podràs veure en línia tot el manga editat pels fansubs en català!",
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
		);
		break;
}
?>
