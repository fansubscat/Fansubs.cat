# Instal·lació i configuració del codi de Fansubs.cat

[![English version](https://img.shields.io/badge/English%20version%20available%20here-blue.svg)](https://github.com/fansubscat/Fansubs.cat/blob/master/INSTALLING.en.md)

## Introducció

En aquest document es detalla com instal·lar el codi del portal web de Fansubs.cat i els serveis de rerefons relacionats. Com que aquest document està destinat a persones que vulguin instal·lar-lo en altres dominis i en llengües diferents, mirarem de detallar les coses que estiguin fetes expressament per a la versió en català, com canviar-ne la llengua i com modificar-ne el codi més específic.

El codi de Fansubs.cat es va començar a escriure el 2015, per la qual cosa hi ha seccions de codi poc clares i la més antiga, l’apartat de notícies, està fortament enllaçada amb el format dels blogs dels fansubs en català. Per aquest motiu, recomanem desactivar-la si es pretén fer una instal·lació independent de Fansubs.cat. En aquest cas, també recomanem desactivar l’apartat «Enllaços», dissenyat per a enllaçar a la comunitat en català, i modificar l’apartat «Qui som?» i la política de privadesa. Trobareu com fer-ho més endavant.

## Maquinari necessari

El servidor web de Fansubs.cat és un VPS simple d’arquitectura amd64 amb 1 GB de RAM i 25 GB de disc dur. El codi està pensat per a funcionar amb un o diversos servidors d’emmagatzematge i streaming, que hauran de tenir la capacitat adequada per a emmagatzemar el contingut i fer-lo visible mitjançant HTTP. Els portals d’anime i imatge real poden funcionar directament fent streaming de MEGA, però el portal de manga necessita explícitament el servidor d’emmagatzematge.

## Programari necessari

Per a instal·lar i fer funcionar el codi de Fansubs.cat, us caldrà:
- Debian Linux 12
- Apache 2.4
- MariaDB 10.11
- PHP 8.2
- MegaCMD 1.6

## Instal·lació del servidor

Instal·leu Debian Linux 12 («bookworm») amb normalitat.

Instal·leu els paquets necessaris:

	apt install apache2 php mariadb-server php-mysql php-curl php-dom php-gd php-mbstring php-zip imagemagick
	
Instal·leu MegaCMD seguint les instruccions de https://mega.io/cmd#download.

Creeu el directori `/srv/fansubscat/` on anirà el codi de Fansubs.cat.

Copieu els directoris `common`, `database`, `services`, `temporary` i `websites` del codi a `/srv/fansubscat`.

Creeu una base de dades amb el seu corresponent usuari i contrasenya executant `mariadb -u root` i escrivint-hi:

	CREATE DATABASE fansubscat;
	GRANT ALL ON fansubscat.* TO 'usuari'@'localhost' IDENTIFIED BY 'contrasenya';
	FLUSH PRIVILEGES;

A continuació, canvieu a la vostra base de dades i executeu la importació inicial:

	USE fansubscat;
	\. /srv/fansubscat/database/database_structure.sql
	\. /srv/fansubscat/database/initial_values.sql

Ja podeu sortir del MariaDB.

Creeu un host de l’Apache per a cada subdomini i redirigiu-lo als següents directoris. Es pot fer servir qualsevol nom de domini i canviar els noms dels subdominis:

* `www.dominiprincipal.xyz` **i** `dominiprincipal.xyz` -> `/srv/fansubscat/websites/main/`
* `admin.dominiprincipal.xyz` -> `/srv/fansubscat/websites/admin/`
* `anime.dominiprincipal.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `imatgereal.dominiprincipal.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `manga.dominiprincipal.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `noticies.dominiprincipal.xyz` -> `/srv/fansubscat/websites/news/`
* `static.dominiprincipal.xyz` -> `/srv/fansubscat/websites/static/`
* `usuaris.dominiprincipal.xyz` -> `/srv/fansubscat/websites/users/`
* `advent.dominiprincipal.xyz` -> `/srv/fansubscat/websites/advent/`
* `api.dominiprincipal.xyz` -> `/srv/fansubscat/websites/api/`
* `comunitat.dominiprincipal.xyz` -> `/srv/fansubscat/websites/community/`

També caldrà crear els següents hosts del domini del hentai:

* `www.dominihentai.xyz` **i** `dominihentai.xyz` -> `/srv/fansubscat/websites/main/`
* `anime.dominihentai.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `manga.dominihentai.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `noticies.dominihentai.xyz` -> `/srv/fansubscat/websites/news/`
* `static.dominihentai.xyz` -> `/srv/fansubscat/websites/static/`
* `usuaris.dominihentai.xyz` -> `/srv/fansubscat/websites/users/`
* `api.dominihentai.xyz` -> `/srv/fansubscat/websites/api/`

Caldrà activar els mòduls `proxy` i `headers` de l’Apache. Podeu fer-ho executant:

	a2enmod rewrite proxy headers
	
El web requereix l’ús d’SSL. Recomanem fer servir Certbot. La instal·lació del certificat i la configuració de cada host de l’Apache és fora de l’abast d’aquest document: feu-ho com cregueu més convenient.

Tots els fitxers emmagatzemats a `/srv/fansubscat` han de pertànyer a l’usuari `www-data` o es produiran errors en desar-hi contingut generat. Podeu canviar-los tots executant:

	chmod -R www-data:www.data /srv/fansubscat
	
Tots els fitxers .sh del directori `services` han de tenir la marca d’executables. Podeu definir-la executant:

	chmod a+x /srv/fansubscat/services/*.sh
	
Configureu les tasques programades que executen els serveis executant `crontab -e -u www-data` i copiant-hi els continguts del fitxer `cron_jobs/crontab.txt` del codi.

Si voleu permetre pujar arxius RAR, caldrà que instal·leu l’extensió php-rar del PECL.

## Configuració del web

Al directori `common/config` trobareu un fitxer `config.example.inc.php`. Cal que en canvieu el nom a `config.inc.php` i el configureu com calgui. Hi haureu d’introduir les dades d’accés a la base de dades, els dominis i subdominis, el nom dels webs, usuaris i claus d’API de les xarxes socials (Bluesky, Discord, Mastodon, Telegram i X), un servidor SMTP per a l’enviament de correus, etc. Trobareu una explicació dels diferents camps al mateix fitxer.

Canvieu el fitxer `/etc/php/8.2/apache2/php.ini` definint-hi `session.cookie_lifetime` a `0`. Si heu instal·lat l’extensió php-rar, activeu-la fent servir `extension=rar.so`.

Al fitxer `websites/users/.htaccess`, canvieu l’expressió regular de la línia que comença per `SetEnvIf Origin` perquè encaixi amb els vostres dominis.

Per a un funcionament òptim, caldrà que canvieu tots els fitxers `.xml` i `.webmanifest` dels subdirectoris de `websites/static/favicons` perquè reflecteixin els noms de domini i subdominis desitjats, juntament amb els títols dels webs.

Una vegada fet això, el web ja serà accessible. Caldrà accedir al web d’administració i iniciar-hi la sessió amb l’usuari i contrasenya desitjats per a l’usuari administrador, que es crearà en aquell moment.

Ara el web ja funcionarà, però mostrarà l’aspecte de Fansubs.cat. Al següent apartat explicarem com podeu modificar-lo.

Aquesta guia no inclou la instal·lació i configuració del fòrum de la comunitat: aquest punt resta fora de l’àmbit d’aquest document i caldrà que el feu sota la vostra responsabilitat.

## Canvi de llengua i personalització

El codi actual està pensat per a Fansubs.cat, que funciona en català. Si voleu que el sistema funcioni en una altra llengua, caldrà que modifiqueu diversos elements:

1) Copieu el fitxer `common/languages/lang_ca.json` i traduïu-lo a la vostra llengua. Si trobeu cadenes que fan referència al català, canvieu-les per la referència a la vostra llengua o modifiqueu-les al gust. Segurament també voldreu canviar la política de privadesa i l’explicació de l’apartat «Qui som?», fortament vinculada al web en català.

2) Genereu el fitxer de llengua per al codi en JavaScript executant l’eina `rebuild_javascript_strings.php` del directori `services`.

3) Copieu el fitxer `websites/static/js/videostream-lang_ca.js` i traduïu-lo a la vostra llengua (o obteniu-lo del web de VideoJS si ja existeix).

4) Modifiqueu el fitxer `common/config/config.inc.php` i canvieu-ne els paràmetres `SITE_LANGUAGE` pel codi ISO de la llengua (que ha de coincidir amb el nom dels fitxers que heu editat als passos 1, 2 i 3) i `SITE_LOCALE` al «locale» desitjat (cal que estigui instal·lat al sistema).

5) Editeu tots els fitxers `.htaccess` dels subdirectoris de `websites` perquè els URLs curts que s’hi especifiquen encaixin amb els del vostre fitxer de llengua (són les cadenes que comencen per `url.`).

6) Editeu tots els fitxers `.webmanifest` dels subdirectoris de `websites/static/favicons` perquè els títols dels webs estiguin en la llengua corresponent, i canvieu-ne les icones si ho creieu convenient.

7) Canvieu els logos i imatges existents a `websites/static/images/site` pels vostres, respectant-ne les mides originals.

8) Podeu desactivar els següents funcionaments o parts del portal al fitxer `common/config/config.inc.php`:
	* `DISABLE_NEWS`: Desactiva la funcionalitat relacionada amb les notícies (caldrà que feu que el subdomini sigui inaccessible o que no el creeu directament).
	* `DISABLE_LINKS`: Desactiva la funcionalitat relacionada amb l’apartat d‘enllaços del web principal (caldrà que feu que sigui inaccessible editant-ne la referència al fitxer `.htaccess`).
	* `DISABLE_LIVE_ACTION`: Desactiva la funcionalitat relacionada amb el contingut d’imatge real (caldrà que feu que el subdomini sigui inaccessible o que no el creeu directament).
	* `DISABLE_ADVENT`: Desactiva la funcionalitat relacionada amb els calendaris d’advent (caldrà que feu que el subdomini sigui inaccessible o que no el creeu directament).
	* `DISABLE_RESOURCES`: Amaga l’enllaç al portal de recursos del tauler d’administració.
	* `DISABLE_COMMUNITY`: Amaga l’enllaç i la sincronització amb el fòrum de la comunitat.
	* `DISABLE_FOOLS_DAY`: Desactiva el funcionament especial per al dia 28 de desembre.
	* `DISABLE_SANT_JORDI_DAY`: Desactiva el funcionament especial per al dia 23 d’abril.
	* `DISABLE_HALLOWEEN_DAYS`: Desactiva el funcionament especial per als dies 31 d’octubre i 1r de novembre.
	* `DISABLE_CHRISTMAS_DAYS`: Desactiva el funcionament especial per als dies del 5 de desembre al 6 de gener.
	* `DISABLE_STATUS`: Desactiva l’enllaç a la pàgina d’estat al peu de la pàgina.
	* `DISABLE_REMOTE_STORAGE_FOR_STREAMING`: Desactiva el servidor d’emmagatzematge extern per a vídeo en streaming (es tirarà directament de MEGA).
	* `DISABLE_REMOTE_STORAGE_FOR_MANGA`: Desactiva el servidor d’emmagatzematge extern per a manga (es tirarà directament del directori `storage` local).

9) Quan ja tingueu contingut al vostre web, canvieu les previsualitzacions per a xarxes socials existents a `websites/static/social` per les vostres.

Si heu seguit tots aquests passos, el vostre web ja hauria de tenir un aspecte propi i estar en la vostra llengua. Ara només falta que l’ompliu de contingut!
