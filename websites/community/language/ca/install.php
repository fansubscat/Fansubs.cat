<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// Common installer pages
$lang = array_merge($lang, array(
	'INSTALL_PANEL'				=> 'Tauler d’instal·lació',
	'SELECT_LANG'				=> 'Seleccioneu un idioma',
	
	'STAGE_INSTALL'	=> 'S’està instal·lant el phpBB',

	// Introduction page
	'INTRODUCTION_TITLE'	=> 'Introducció',
	'INTRODUCTION_BODY'		=> 'Benvingut al phpBB3!<br /><br />El phpBB® és el programari lliure de fòrums més utilitzat del món. EL phpBB3 és la darrera versió d’un producte que va començar l’any 2000. Com els seus predecessors, el phpBB3 inclou moltes funcions, és amigable i disposa d’asistencia completa per part de l’equip del phppBB. El phpBB3 millora ampliament allò que va fer el phpBB2 popular i afegeix funcions sol·licitades freqüentment que no eren presents en versions anteriors. Esperem que sobrepassi les vostres espectatives.<br /><br />El sistema d’instal·lació us guiarà pel procés d’instal·lar el phpBB3, actualitzar des de versions anteriors a la versió més recent del phpBB3, o convertir al phpBB3 des de sistemes de fòrums diferents (el phpBB2 inclòs). Per obtenir més informació, us recomanem que llegiu <a href="%1$s">la guia d’instal·lació</a>.<br /><br />Per llegir la llicència del phpBB3 o esbrinar com obtenir asistència i la nostra posició al respecte, seleccioneu les opcions adients del menú lateral. Per continuar, seleccioneu la pestanya adequada a sobre.',

	// Support page
	'SUPPORT_TITLE'		=> 'Assistència',
	'SUPPORT_BODY'		=> 'Es proporciona assistencia completa per a la verió estable actual del phpBB3, de forma gratuïta. Això inclou:</p><ul><li>instal·lació</li><li>configuració</li><li>preguntes tècniques</li><li>problemes relacionats amb possibles errades del programari</li><li>actualitzacions des de les versions candidates (RC) a la versió estable més recent</li><li>conversió des del phpBB 2.0.x al phpBB3</li><li>conversió des d’altres programaris de fòrums al phpBB3 (veieu el <a href="https://www.phpbb.com/community/viewforum.php?f=486">Fòrum de Conversors</a>)</li></ul><p>Recomanem als usuaris que encara estiguin utilitzant versions beta del phpBB3 que substitueixin la seva instal·lació amb una còpia neta de la versió més recent.</p><h2>Extensions / Estils</h2><p>Per questions relacionades amb les Extensions, dirigiu-vos al <a href="https://www.phpbb.com/community/viewforum.php?f=451">Fòrum d’Extensions</a>.<br />Per questions relacionades amb els estils, plantilles i temes gràfics, dirigiu-vos al <a href="https://www.phpbb.com/community/viewforum.php?f=471">Fòrum d’estils</a>.<br /><br />Si la vostra pregunta està relacionada amb un paquet determinat, feu-la directament al tema dedicat al paquet.</p><h2>Obtenció d’assistència</h2><p><a href="https://www.phpbb.com/support/">Secció d’assistència</a><br /><a href="https://www.phpbb.com/support/docs/en/3.3/ug/quickstart/">Guia d’inici ràpid</a><br /><br />Per assegurar-vos que us manteniu al dia amb les darreres notícies i versions, seguiu-nos a <a href="https://www.twitter.com/phpbb/">Twitter</a> i <a href="https://www.facebook.com/phpbb/">Facebook</a><br /><br />',

	// License
	'LICENSE_TITLE'		=> 'Llicència pública general',

	// Install page
	'INSTALL_INTRO'				=> 'Benvinguts a la instal·lació',
	'INSTALL_INTRO_BODY'		=> 'Amb aquesta opció podeu instal·lar el phpBB3 en el vostre servidor.</p><p>Per continura necessitareu els paràmetres de configuració de la vostra base de dades. Si no els sabeu, contacteu amb el vostre proveïdor i demaneu-los-hi. Sense ells no podreu continuar. Necessiteu:</p>

	<ul>
		<li>El tipus de base de dades que usareu.</li>
		<li>El nom (adreça) del servidor de la base de dades o DSN.</li>
		<li>El port del servidor de la base de dades (en la majoria de casos no us farà falta).</li>
		<li>El nom de la base de dades al servidor.</li>
		<li>L’usuari de la base de dades i la contrasenya utilitzats per iniciar-hi la sessió i accedir a les dades.</li>
	</ul>

	<p><strong>Nota:</strong> si utilitzeu SQLite per la instal·lació, cal que introduïu el camí complet fins al fitxer de la base de dades al camp DSN i deixeu els camps d’usuari i contrasenya en blanc. Per raons de seguretat, assegureu-vos que el fitxer de la base de dades no sigui en una ubicació accessible des d’Internet.</p>

	<p>phpBB3 és compatible amb les següents bases de dades:</p>
	<ul>
		<li>MySQL 4.1.3 o superior (MySQLi obligatori)</li>
		<li>PostgreSQL 8.3+</li>
		<li>SQLite 3.6.15+</li>
		<li>MS SQL Server 2000 o superior (directament o via ODBC)</li>
		<li>MS SQL Server 2005 o superior (natiu)</li>
		<li>Oracle</li>
	</ul>

	<p>Només es mostraran les bases de dades disponibles al vostre servidor.',

	'ACP_LINK'	=> 'Ves al <a href="%1$s">TCA</a>',

	'INSTALL_PHPBB_INSTALLED'		=> 'El phpBB ja està instal·lat.',
	'INSTALL_PHPBB_NOT_INSTALLED'	=> 'El phpBB encara no està instal·lat.',
));

// Requirements translation
$lang = array_merge($lang, array(
	// Filesystem requirements
	'FILE_NOT_EXISTS'						=> 'El fitxer no existeix',
	'FILE_NOT_EXISTS_EXPLAIN'				=> 'Per poder instal·lar el phpBB, el fitxer %1$s ha d’existir.',
	'FILE_NOT_EXISTS_EXPLAIN_OPTIONAL'		=> 'Es recomana que el fitxer %1$s existeixi per tenir una millor experiència d’usuari al fòrum.',
	'FILE_NOT_WRITABLE'						=> 'No es pot escriure al fitxer',
	'FILE_NOT_WRITABLE_EXPLAIN'				=> 'Per poder instal·lar el phpBB, s’ha de poder esciure sobre el fitxer %1$s.',
	'FILE_NOT_WRITABLE_EXPLAIN_OPTIONAL'	=> 'Es recomana que es pugui escriure sobre el fitxer %1$s per tenir una millor experiència d’usuari al fòrum.',

	'DIRECTORY_NOT_EXISTS'						=> 'El directori no existeix',
	'DIRECTORY_NOT_EXISTS_EXPLAIN'				=> 'Per poder instal·lar el phpBB, el directori %1$s ha d’existir.',
	'DIRECTORY_NOT_EXISTS_EXPLAIN_OPTIONAL'		=> 'Es recomana que el directori %1$s existeixi per tenir una millor experiència d’usuari al fòrum.',
	'DIRECTORY_NOT_WRITABLE'					=> 'No es pot escriure al directori',
	'DIRECTORY_NOT_WRITABLE_EXPLAIN'			=> 'Per poder instal·lar el phpBB, s’ha de poder esciure sobre el directori %1$s.',
	'DIRECTORY_NOT_WRITABLE_EXPLAIN_OPTIONAL'	=> 'Es recomana que es pugui escriure sobre el directori %1$s per tenir una millor experiència d’usuari al fòrum.',

	// Server requirements
	'PHP_VERSION_REQD'					=> 'Versió del PHP',
	'PHP_VERSION_REQD_EXPLAIN'			=> 'El phpBB requereix la versió 7.2.0 o superior del PHP.',
	'PHP_GETIMAGESIZE_SUPPORT'			=> 'Es requereix la funció getimagesize() del PHP',
	'PHP_GETIMAGESIZE_SUPPORT_EXPLAIN'	=> 'Per que el phpBB funcioni correctament, la funció getimagesize ha d’estar disponible.',
	'PCRE_UTF_SUPPORT'					=> 'Compatibilitat amb PCRE UTF-8',
	'PCRE_UTF_SUPPORT_EXPLAIN'			=> 'El phpBB no s’executarà si la vostra instal·lació del PHP no ha estat compilada amb compatibilitat per UTF-8 a l’extensió PCRE.',
	'PHP_JSON_SUPPORT'					=> 'Compatibilitat PHP per JSON',
	'PHP_JSON_SUPPORT_EXPLAIN'			=> 'Per que el phpBB funcioni correctament, l’extensió de PHP per JSON ha d’estar disponible.',
	'PHP_MBSTRING_SUPPORT'				=> 'Compatibilitat PHP per mbstring',
	'PHP_MBSTRING_SUPPORT_EXPLAIN'		=> 'Per que el phpBB funcioni correctament, l’extensió de PHP per mbstring ha d’estar disponible.',
	'PHP_XML_SUPPORT'					=> 'Compatibilitat PHP per XML/DOM',
	'PHP_XML_SUPPORT_EXPLAIN'			=> 'Per que el phpBB funcioni correctament, l’extensió de PHP per XML/DOM ha d’estar disponible.',
	'PHP_SUPPORTED_DB'					=> 'Bases de dades compatibles',
	'PHP_SUPPORTED_DB_EXPLAIN'			=> 'El PHP ha de funcionar amb almenys una de les bases de dades compatibles. Si no es mostra cap mòdul de base de dades cal que contacteu amb el vostre proveïdor d’hostatge o revisar la documentació d’instal·lació relevant del PHP.',

	'RETEST_REQUIREMENTS'	=> 'Torna a comprovar els requeriments',

	'STAGE_REQUIREMENTS'	=> 'Comprovació de requeriments',
));

// General error messages
$lang = array_merge($lang, array(
	'INST_ERR_MISSING_DATA'		=> 'Heu d’omplir tots els camps d’aquest bloc.',

	'TIMEOUT_DETECTED_TITLE'	=> 'L’instal·lador ha excedit el temps d’espera',
	'TIMEOUT_DETECTED_MESSAGE'	=> 'L’instal·lador ha excedit el temps d’espera, podeu provar a refrescar la pàgina, però pot provocar que es corrompin les dades. És recomanable que incrementeu la configuració de temps màxim d’espera o proveu d’usar la línia d’ordres (CLI).',
));

// Data obtaining translations
$lang = array_merge($lang, array(
	'STAGE_OBTAIN_DATA'	=> 'Introducció de les dades d’instal·lació',

	//
	// Admin data
	//
	'STAGE_ADMINISTRATOR'	=> 'Dades de l’administrator',

	// Form labels
	'ADMIN_CONFIG'				=> 'Configuració de l’administrador',
	'ADMIN_PASSWORD'			=> 'Contrasenya de l’administrador',
	'ADMIN_PASSWORD_CONFIRM'	=> 'Confirmeu la contrasenya de l’administrador',
	'ADMIN_PASSWORD_EXPLAIN'	=> 'Introduïu una contrasenya d’entre 6 i 30 caràcters de longitud.',
	'ADMIN_USERNAME'			=> 'Nom d’usuari de l’administrador',
	'ADMIN_USERNAME_EXPLAIN'	=> 'Introduïu un nom d’usuari d’entre 3 i 20 caràcters de longitud.',

	// Errors
	'INST_ERR_EMAIL_INVALID'		=> 'L’adreça electrònica que heu introduït no és vàlida.',
	'INST_ERR_PASSWORD_MISMATCH'	=> 'Les contrasenyes que heu introduït no coincideixen.',
	'INST_ERR_PASSWORD_TOO_LONG'	=> 'La contrasenya que heu introduït és massa llarga. La longitud màxima és de 30 caràcters.',
	'INST_ERR_PASSWORD_TOO_SHORT'	=> 'La contrasenya que heu introduït és massa curta. La longitud mínima és de 6 caràcters.',
	'INST_ERR_USER_TOO_LONG'		=> 'El nom d’usuari que heu introduït és massa llarg. La longitud màxima és de 20 caràcters.',
	'INST_ERR_USER_TOO_SHORT'		=> 'El nom d’usuari que heu introduït és massa curt. La longitud mínima és de 3 caràcters.',

	//
	// Board data
	//
	// Form labels
	'BOARD_CONFIG'		=> 'Configuració del fòrum',
	'DEFAULT_LANGUAGE'	=> 'Idioma per defecte del fòrum',
	'BOARD_NAME'		=> 'Títol del fòrum',
	'BOARD_DESCRIPTION'	=> 'Descripció curta del fòrum',

	//
	// Database data
	//
	'STAGE_DATABASE'	=> 'Configuració de la base de dades',

	// Form labels
	'DB_CONFIG'				=> 'Configuració de la base de dades',
	'DBMS'					=> 'Tipus de base de dades',
	'DB_HOST'				=> 'Nom del servidor de la base de dades o DSN',
	'DB_HOST_EXPLAIN'		=> 'DSN significa nom d’origen de dades (Data Source Name) i només es rellevant per a instal·lacions que utilitzen ODBC. Amb PostgreSQL, utilitzeu localhost per connectar amb el servidor local via UNIX domain socket i 127.0.0.1 per connectar via TCP. Si utilitzeu SQLite, introduïu el camí complet al fitxer de la base de dades.',
	'DB_PORT'				=> 'Port del servidor de la base de dades',
	'DB_PORT_EXPLAIN'		=> 'Deixeu-lo en blanc a no ser que el servidor utilitzi un port no estàndard.',
	'DB_PASSWORD'			=> 'Contrasenya de la base de dades',
	'DB_NAME'				=> 'Nom de la base de dades',
	'DB_USERNAME'			=> 'Nom d’usuari de la base de dades',
	'DATABASE_VERSION'		=> 'Versió de la base de dades',
	'TABLE_PREFIX'			=> 'Prefix de les taules a la base de dades',
	'TABLE_PREFIX_EXPLAIN'	=> 'El prefix ha de començar amb una lletra i només pot contenir lletres, números o el caràcter de subratllat.',

	// Database options
	'DB_OPTION_MSSQL_ODBC'	=> 'MSSQL Server 2000+ via ODBC',
	'DB_OPTION_MSSQLNATIVE'	=> 'MSSQL Server 2005+ [ Natiu ]',
	'DB_OPTION_MYSQLI'		=> 'MySQL amb extensió MySQLi',
	'DB_OPTION_ORACLE'		=> 'Oracle',
	'DB_OPTION_POSTGRES'	=> 'PostgreSQL',
	'DB_OPTION_SQLITE3'		=> 'SQLite 3',

	// Errors
	'INST_ERR_DB'					=> 'Error d’instal·lació de la base de dades',
	'INST_ERR_NO_DB'				=> 'No s’ha pogut carregar el mòdul del PHP per al tipus de base de dades seleccionat.',
	'INST_ERR_DB_INVALID_PREFIX'	=> 'El prefix que heu introduït no es vàlid. Ha de començar amb una lletra i només pot contenir lletres, números o el caràcter de subratllat.',
	'INST_ERR_PREFIX_TOO_LONG'		=> 'El prefix de taula que heu especificat és massa llarg. La longitud màxima és de %d caràcters.',
	'INST_ERR_DB_NO_NAME'			=> 'No heu especificat el nom de la base de dades.',
	'INST_ERR_DB_FORUM_PATH'		=> 'El fitxer de la base de dades que heu especificat es troba dintre de l’arbre de carpetes del fòrum. És molt recomanable que poseu aquest fitxer en una ubicació que no sigui accessible des d’Internet.',
	'INST_ERR_DB_CONNECT'			=> 'No s’ha pogut connectar amb la base de dades, l’error es mostra a continuació.',
	'INST_ERR_DB_NO_WRITABLE'		=> 'S’ha de poder escriure tant a la base de dades com al directori que la conté.',
	'INST_ERR_DB_NO_ERROR'			=> 'No s’ha rebut cap missatge d’error.',
	'INST_ERR_PREFIX'				=> 'Ja existeixen taules amb el prefix especificat, trieu-ne un altre.',
	'INST_ERR_DB_NO_MYSQLI'			=> 'La versió de MySQL instal·lada en aquesta màquina no és compatible amb l’opció “MySQL amb extensió MySQLi” que heu seleccionat. Proveu-ho amb l’opció “MySQL”.',
	'INST_ERR_DB_NO_SQLITE3'		=> 'La versió de l’extensió SQLite que teniu instal·lada és massa antiga, cal que l’actualitzeu, com a mínim, a la versió 3.6.15.',
	'INST_ERR_DB_NO_ORACLE'			=> 'La versió d’Oracle instal·lada en aquesta màquina requereix que fixeu el paràmetre <var>NLS_CHARACTERSET</var> a <var>UTF8</var>. Podeu actualitzar la base de dades a la versió 9.2+ o bé canviar el paràmetre.',
	'INST_ERR_DB_NO_POSTGRES'		=> 'La base de dades que heu seleccionat no s’ha creat amb codificació <var>UNICODE</var> o <var>UTF8</var>. Feu la instal·lació sobre una base de dades amb codificació <var>UNICODE</var> o <var>UTF8</var>.',
	'INST_SCHEMA_FILE_NOT_WRITABLE'	=> 'No es pot escriure al fitxer d’esquema',

	//
	// Email data
	//
	'EMAIL_CONFIG'	=> 'Configuració de correu electrònic',

	// Package info
	'PACKAGE_VERSION'					=> 'Versió instal·lada del paquet',
	'UPDATE_INCOMPLETE'				=> 'La instal·lació del phpBB no s’ha actualitzat correctament.',
	'UPDATE_INCOMPLETE_MORE'		=> 'Llegiu la informació a continuació per corregir aquest error.',
	'UPDATE_INCOMPLETE_EXPLAIN'		=> '<h1>Actualització incompleta</h1>

		<p>S’ha detectat que la darrera actualització de la vostra instal·lació del phpBB no s’ha completat. Aneu a <a href="%1$s" title="%1$s">l’actualitzador de la base de dades</a>, assegureu-vos que està seleccionada l’opció <em>Actualitza només la base de dades</em> i feu clic al botó <strong>Tramet</strong>. No oblideu eliminar la carpeta "install" després d’actualitzar la base de dades correctament.</p>',

	//
	// Server data
	//
	// Form labels
	'UPGRADE_INSTRUCTIONS'			=> 'Hi ha disponible la nova versió <strong>%1$s</strong>. Llegiu <a href="%2$s" title="%2$s"><strong>l’avís de llançament</strong></a> per assabentar-vos de què ofereix i com fer l’actualització.',
	'SERVER_CONFIG'				=> 'Configuració del servidor',
	'SCRIPT_PATH'				=> 'Camí de l’script',
	'SCRIPT_PATH_EXPLAIN'		=> 'El camí on està ubicat el phpBB relatiu al nom de domini, p.ex. <samp>/phpBB3</samp>.',
));

// Default database schema entries...
$lang = array_merge($lang, array(
	'CONFIG_BOARD_EMAIL_SIG'		=> 'Gràcies, els responsables del fòrum',
	'CONFIG_SITE_DESC'				=> 'Un text curt per descriure el fòrum',
	'CONFIG_SITENAME'				=> 'elvostredomini.cat',

	'DEFAULT_INSTALL_POST'			=> '<t>Aquesta és una entrada d’exemple per a la instal·lació del phpBB3. Sembla que tot funciona. Si voleu, podeu eliminar aquesta entrada i continuar amb la configuració del fòrum. Durant el procès d’instal·lació, s’ha assignat un grup adient de permisos a la primera categoria i el primer fòrum per als administradors de grups d’usuaris predefinits, robots, moderadors globals, visitants, usuaris registrats i usuaris COPPA registrats. Si també decidiu eliminar la primera categoria i el primer fòrum, no oblideu assignar permisos per tots els grups d’usuaris a totes les categories i fòrums nous que creeu. És aconsellable que canvieu el nom de la primera categoria i el primer fòrum i copieu els permisos des d’aquests mentre creeu noves categories i fòrums.</t>',

	'FORUMS_FIRST_CATEGORY'			=> 'Primera categoria',
	'FORUMS_TEST_FORUM_DESC'		=> 'Descripció del primer fòrum.',
	'FORUMS_TEST_FORUM_TITLE'		=> 'El primer fòrum',

	'RANKS_SITE_ADMIN_TITLE'		=> 'Administrador',
	'REPORT_WAREZ'					=> 'L’entrada conté enllaços a programari il·legal o piratejat.',
	'REPORT_SPAM'					=> 'L’entrada té com a únic fi anunciar un lloc web o algun producte.',
	'REPORT_OFF_TOPIC'				=> 'L’entrada és fora de tema.',
	'REPORT_OTHER'					=> 'L’entrada no encaixa en cap de les altres categories, si us plau utilitzeu el camp d’informació addicional.',

	'SMILIES_ARROW'					=> 'Fletxa',
	'SMILIES_CONFUSED'				=> 'Confós',
	'SMILIES_COOL'					=> 'Guai',
	'SMILIES_CRYING'				=> 'Plora o molt trist',
	'SMILIES_EMARRASSED'			=> 'Avergonyit',
	'SMILIES_EVIL'					=> 'Malvat o molt enfadat',
	'SMILIES_EXCLAMATION'			=> 'Exclamació',
	'SMILIES_GEEK'					=> 'Friqui',
	'SMILIES_IDEA'					=> 'Idea',
	'SMILIES_LAUGHING'				=> 'Riu',
	'SMILIES_MAD'					=> 'Enfadat',
	'SMILIES_MR_GREEN'				=> 'Sr. Verd',
	'SMILIES_NEUTRAL'				=> 'Neutral',
	'SMILIES_QUESTION'				=> 'Pregunta',
	'SMILIES_RAZZ'					=> 'Treu la llengua',
	'SMILIES_ROLLING_EYES'			=> 'Gira els ulls',
	'SMILIES_SAD'					=> 'Trist',
	'SMILIES_SHOCKED'				=> 'Impressionat',
	'SMILIES_SMILE'					=> 'Somriu',
	'SMILIES_SURPRISED'				=> 'Sorprés',
	'SMILIES_TWISTED_EVIL'			=> 'Recargolat',
	'SMILIES_UBER_GEEK'				=> 'Super friqui',
	'SMILIES_VERY_HAPPY'			=> 'Molt content',
	'SMILIES_WINK'					=> 'Fa l’ullet',

	'TOPICS_TOPIC_TITLE'			=> 'Benvingut al phpBB3',
));

// Common navigation items' translation
$lang = array_merge($lang, array(
	'MENU_OVERVIEW'		=> 'Resum',
	'MENU_INTRO'		=> 'Introducció',
	'MENU_LICENSE'		=> 'Llicència',
	'MENU_SUPPORT'		=> 'Assistència',
));

// Task names
$lang = array_merge($lang, array(
	// Install filesystem
	'TASK_CREATE_CONFIG_FILE'	=> 'S’està creant el fitxer de configuració',

	// Install database
	'TASK_ADD_CONFIG_SETTINGS'			=> 'S’estan afegint les opcions de configuració',
	'TASK_ADD_DEFAULT_DATA'				=> 'S’estan afegint les configuracions per defecte a la base de dades',
	'TASK_CREATE_DATABASE_SCHEMA_FILE'	=> 'S’està creant el fitxer d’esquema de la base de dades',
	'TASK_SETUP_DATABASE'				=> 'S’està configurant la base de dades',
	'TASK_CREATE_TABLES'				=> 'S’estan creant les taules',

	// Install data
	'TASK_ADD_BOTS'				=> 'S’estan registrant els robots',
	'TASK_ADD_LANGUAGES'		=> 'S’estan instal·lant els idiomes disponibles',
	'TASK_ADD_MODULES'			=> 'S’estan instal·lant els mòduls',
	'TASK_CREATE_SEARCH_INDEX'	=> 'S’està creant l’índex de cerca',

	// Install finish tasks
	'TASK_INSTALL_EXTENSIONS'	=> 'S’estan instal·lant els paquets d’extensions',
	'TASK_NOTIFY_USER'			=> 'S’estan enviant els correus de notificació',
	'TASK_POPULATE_MIGRATIONS'	=> 'S’estan omplint les migracions',

	// Installer general progress messages
	'INSTALLER_FINISHED'	=> 'L’instal·lador ha finalitzat correctament',
));

// Installer's general messages
$lang = array_merge($lang, array(
	'MODULE_NOT_FOUND'				=> 'No s’ha trobat el mòdul',
	'MODULE_NOT_FOUND_DESCRIPTION'	=> 'No s’ha trobat el mòdul perquè el servei “%s” no està definit.',

	'TASK_NOT_FOUND'				=> 'No s’ha trobat la tasca',
	'TASK_NOT_FOUND_DESCRIPTION'	=> 'No s’ha trobat la tasca perquè el servei “%s” no està definit.',

	'SKIP_MODULE'	=> 'Omet el mòdul “%s”',
	'SKIP_TASK'		=> 'Omet la tasca “%s”',

	'TASK_SERVICE_INSTALLER_MISSING'	=> 'Tots els serveis de tasques d’instal·lació harien de començar per “installer”',
	'TASK_CLASS_NOT_FOUND'				=> 'La definició del servei de tasques d’instal·lació no és vàlida. El nom del servei proporcionat és “%1$s”, l’espai de noms de classe esperat és “%2$s”. Per més informació llegiu la documentació de task_interface.',

	'INSTALLER_CONFIG_NOT_WRITABLE'	=> 'No es pot escriure al fitxer de configuració d’instal·lació.',
));

// CLI messages
$lang = array_merge($lang, array(
	'CLI_INSTALL_BOARD'				=> 'Instal·la el phpBB',
	'CLI_UPDATE_BOARD'				=> 'Actualitza el phpBB',
	'CLI_INSTALL_SHOW_CONFIG'		=> 'Mostra la configuració que s’usarà',
	'CLI_INSTALL_VALIDATE_CONFIG'	=> 'Valida un fitxer de configuració',
	'CLI_CONFIG_FILE'				=> 'Fitxer de configuració a usar',
	'MISSING_FILE'					=> 'No s’ha pogut accedir al fitxer %1$s',
	'MISSING_DATA'					=> 'Al fitxer de configuració li falten dades o potser conté configuracions no vàlides.',
	'INVALID_YAML_FILE'				=> 'No s’ha pogut analitzar el fitxer YAML “%1$s”',
	'CONFIGURATION_VALID'			=> 'El fitxer de configuració és vàlid',
));

// Common updater messages
$lang = array_merge($lang, array(
	'UPDATE_INSTALLATION'			=> 'Actualitza la instal·lació del phpBB',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Amb aquesta opció és possible actualitzar la vostra instal·lació del phpBB a la versió més recent.<br />Durant el procés es comprovaran tots els vostres fitxers per verificar-ne la integritat. Podeu revisar totes les diferències i fitxers abans de l’actualització.<br /><br />L’actualització de fitxers en sí es pot fer de dues maneres diferents.</p><h2>Actualització manual</h2><p>Amb aquesta actualització només baixeu el vostre conjunt personal de fitxers modificats per assegurar-vos que no perdeu les modificacions que hagueu fet als fitxers. Després de baixar aquest paquet informàtic cal que pengeu manualment els fitxers a la seva ubicació correcta dintre del directori arrel del phpBB. Quan hagueu acabat, podeu tornar a fer la comprovació de fitxers per veure si heu mogut els fitxers a la seva ubicació correcta.</p><h2>Actualització avançada amb FTP</h2><p>Aquest mètode és similar al primer, però no cal baixar els fitxers modificats i penjar-los manualment ja que es farà automàticament. Per usar aquest mètode heu de saber la informació necessària per iniciar una sessió FTP ja que se us demanarà. Quan hagueu acabat se us redirigirà a la comprovació de fitxers una altra vegada per assegurar-vos que s’ha actualitzat tot correctament.<br /><br />',
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>Avís de llançament</h1>

		<p>Llegiu l’avís de llançament de la versió més recent abans de continuar amb el procés d’actualització, és possible que contingui informació útil. També conté enllaços a les baixades completes així com el registre de canvis.</p>

		<br />

		<h1>Com actualitzar la vostra instal·lació amb el Paquet complet</h1>

		<p>La manera recomanada per actualitzar la vostra instal·lació és usar el paquet complet. Si heu modificat fitxers propis del phpBB al vostre servidor, us pot convenir usar el paquet d’actualització avançada per tal de no perdre els canvis. També podeu actualitzar la vostra instal·lació utilitzant els mètodes indicats al document INSTALL.html. Els passos per actualitzar el phpBB3 utilitzant el paquet complet són:</p>

		<ol style="margin-left: 20px; font-size: 1.1em;">
			<li><strong class="error">Feu una còpia de seguretat de tots els fitxers del fòrum i de la base de dades.</strong></li>
			<li>Aneu a la <a href="https://www.phpbb.com/downloads/" title="https://www.phpbb.com/downloads/">pàgina de baixades de phpBB.com</a> i baixeu-vos el fitxer més recent del "Paquet Complet" ("Full Package" en anglès).</li>
			<li>Descomprimiu el fitxer.</li>
			<li>Elimineu el fitxer <code class="inline">config.php</code>, i les carpetes <code class="inline">/images</code>, <code class="inline">/store</code> i <code class="inline">/files</code> <em>del paquet</em> (no del vostre lloc web).</li>
			<li>Aneu al TCA, Configuració del fòrum, i assegureu-vos que prosilver és l’estil per defecte. Si no ho és, configureu prosilver com a estil per defecte.</li>
			<li>Elimineu les carpetes <code class="inline">/vendor</code> i <code class="inline">/cache</code> de la carpeta arrel del fòrum al servidor.</li>
			<li>Pengeu via FTP o SSH els fitxers i les carpetes restants (és a dir, el CONTINGUT que queda a la carpeta phpBB3) a la carpeta arrel de la vostra instal·lació del fòrum al servidor sobreescrivint els fitxers existents. (Nota: tingueu cura de no eliminar cap extensió de la vostra carpeta <code class="inline">/ext</code> quan pugeu el nou contingut de phpBB3.)</li>
			<li><strong><a href="%1$s" title="%1$s">Comenceu el procés d’actualització anant amb el vostre navegador al directory d’instal·lació</a>.</strong></li>
			<li>Seguiu els passos per actualitzar la base de dades i deixeu que s’executi fins que acabi.</li>
			<li>Elimineu via FTP o SSH la carpeta <code class="inline">/install</code> de la carpeta arrel del fòrum al servidor.<br><br></li>
		</ol>

		<p>Ja tindreu el fòrum actualitzat amb tots els seus usuaris i entrades. Tasques de seguiment:</p>
		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>Actualitzeu el vostres paquet d’idioma</li>
			<li>Actualitzeu el vostres estils<br><br></li>
		</ul>

		<h1>Com actualitzar la vostra instal·lació amb el Paquet d’actualització avançada</h1>

		<p>Només es recomana usar el paquet d’instal·lació avançada als usuaris experts que hàgiu modificat fitxers propis del phpBB al vostre servidor. També podeu actualitzar la vostra instal·lació utilitzant els mètodes indicats al document INSTALL.html. Els passos per actualitzar el phpBB3 utilitzant el paquet d’instal·lació automàtica són:</p>

		<ol style="margin-left: 20px; font-size: 1.1em;">
			<li>Aneu a la <a href="https://www.phpbb.com/downloads/" title="https://www.phpbb.com/downloads/">pàgina de baixades de phpBB.com</a> i baixeu l’arxiu "Advanced Update Package".</li>
			<li>Descomprimiu l’arxiu.</li>
			<li>Pengeu els directoris “install” i"vendor" complets i descomprimits al directori arrel del phpBB (on es troba el fitxer config.php).</li>
		</ol>

		<p>Un cop penjat el fòrum els usuaris normals no podran accedir al fòrum degut a la presència del directori “install” que heu penjat.<br /><br />
		<strong><a href="%1$s" title="%1$s">Inicieu ara el procés d’actualització dirigint el vostre navegador al directori “install”</a>.</strong><br />
		<br />
		Llavors se us guiarà pel procés d’actualització. Rebreu un avís quan l’actualització hagi acabat.
		</p>
	',
));

// Updater forms
$lang = array_merge($lang, array(
	// Updater types
	'UPDATE_TYPE'			=> 'Tipus d’actualització a executar',

	'UPDATE_TYPE_ALL'		=> 'Actualitza els fitxers i la base de dades',
	'UPDATE_TYPE_DB_ONLY'	=> 'Actualitza només la base de dades',

	// File updater methods
	'UPDATE_FILE_METHOD_TITLE'		=> 'Mètodes d’actualització de fitxers',

	'UPDATE_FILE_METHOD'			=> 'Mètode d’actualització de fitxers',
	'UPDATE_FILE_METHOD_DOWNLOAD'	=> 'Baixa els fitxers modificats en un arxiu',
	'UPDATE_FILE_METHOD_FTP'		=> 'Actualitza els fitxers via FTP (automàtic)',
	'UPDATE_FILE_METHOD_FILESYSTEM'	=> 'Actualitza els fitxers via accés directe de fitxers (automàtic)',

	// File updater archives
	'SELECT_DOWNLOAD_FORMAT'	=> 'Seleccioneu el format de l’arxiu de baixada',

	// FTP settings
	'FTP_SETTINGS'			=> 'Configuració de l’FTP',
));

// Requirements messages
$lang = array_merge($lang, array(
	'UPDATE_FILES_NOT_FOUND'	=> 'No s’ha trobat cap directori d’actualització vàlid, assegureu-vos que heu pujat els fitxers rellevants.',

	'NO_UPDATE_FILES_UP_TO_DATE'	=> 'La vostra versió està actualitzada. No cal que executeu l’eina d’actualització. Si desitgeu fer una comprovació d’integritat dels vostres fitxers, assegureu-vos que heu penjat els fitxers actualitzats correctes.',
	'OLD_UPDATE_FILES'				=> 'Els fitxers d’actualització no estan actualitzats. Els fitxers d’actualització que s’han trobat són per actualitzar del phpBB %1$s al phpBB %2$s, però la versió més recent del phpBB és la %3$s.',
	'INCOMPATIBLE_UPDATE_FILES'		=> 'Els fitxers d’actualització trobats no són compatibles amb la versió que teniu instal·lada. La vostra versió és la %1$s i els fitxers d’actualització són per actualitzar del phpBB %2$s al %3$s.',
));

// Update files
$lang = array_merge($lang, array(
	'STAGE_UPDATE_FILES'		=> 'Actualització de fitxers',

	// Check files
	'UPDATE_CHECK_FILES'	=> 'Comprova el fitxers a actualitzar',

	// Update file differ
	'FILE_DIFFER_ERROR_FILE_CANNOT_BE_READ'	=> 'El generador de diferències de fitxers no ha pogut obrir  %s.',

	'UPDATE_FILE_DIFF'		=> 'S’estan generant les diferències dels fitxers modificats',
	'ALL_FILES_DIFFED'		=> 'S’han generat les diferències de tots els fitxers modificats.',

	// File status
	'UPDATE_CONTINUE_FILE_UPDATE'	=> 'Actualitza els fitxers',

	'DOWNLOAD'							=> 'Baixa',
	'DOWNLOAD_CONFLICTS'				=> 'Baixa l’arxiu de conflictes de fusió',
	'DOWNLOAD_CONFLICTS_EXPLAIN'		=> 'Cerqueu la cadena &lt;&lt;&lt; per localitzar els conflictes',
	'DOWNLOAD_UPDATE_METHOD'			=> 'Baixa l’arxiu de fitxers modificats',
	'DOWNLOAD_UPDATE_METHOD_EXPLAIN'	=> 'Quan l’hagueu baixat, heu de descomprimir l’arxiu. Hi trobareu els fitxers modificats que heu de penjar al directori arrel del phpBB. Pengeu els fitxers a les seves ubicacions respectives. Un cop hagueu penjat tots els fitxers podeu continuar amb el procés d’actualització.',

	'FILE_ALREADY_UP_TO_DATE'		=> 'El fitxer ja està actualitzat.',
	'FILE_DIFF_NOT_ALLOWED'			=> 'No es poden buscar les diferències del fitxer.',
	'FILE_USED'						=> 'Informació usada de',			// Single file
	'FILES_CONFLICT'				=> 'Fitxers amb conflictes',
	'FILES_CONFLICT_EXPLAIN'		=> 'Els fitxers següents estan modificats i no representen els fitxers originals de la versió antiga. El phpBB ha determinat que aquests fitxers provocaran conflictes si s’intenta fusionar-los. Investigueu els conflictes i proveu de resoldre’ls manualment o continueu l’actualització triant el mètode de fusió que preferiu. Si resoleu els conflictes manualment, torneu a comprovar els fitxers després de modificar-los. Tambés és possible triar el mètode de fusió preferit per cada fitxer. El primer mètode tindrà com a resultat un fitxer en què les línies amb conflictes del vostre fitxer antic s’han perdut, l’altre tindrà com a resultat un fitxer on s’han perdut els canvis del fitxer nou.',
	'FILES_DELETED'					=> 'Fitxers eliminats',
	'FILES_DELETED_EXPLAIN'			=> 'Els fitxers següents no existeixen a la versió nova. Heu d’eliminar aquests fitxers.',
	'FILES_MODIFIED'				=> 'Fitxers modificats',
	'FILES_MODIFIED_EXPLAIN'		=> 'Els fitxers següents estan modificats i no representen els fitxers originals de la versió antiga. El fitxer actualitzat serà una fusió del les vostres modificacions i el fitxer nou.',
	'FILES_NEW'						=> 'Fitxers nous',
	'FILES_NEW_EXPLAIN'				=> 'Els fitxers següents no existeixen a la vostra instal·lació. Aquests fitxers s’afegiran a la vostra instal·lació.',
	'FILES_NEW_CONFLICT'			=> 'Fitxers nous amb conflictes',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> 'Els fitxers següents són nous per a la versió més recent, però s’ha determinat que ja hi ha un fitxer amb el mateix nom a la mateixa ubicació. Es sobreescriurà aquest fitxer amb la versió nova.',
	'FILES_NOT_MODIFIED'			=> 'Fitxers no modificats',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> 'Els fitxers següents no s’han modificat i representen els fitxers originals de la versió del phpBB que esteu actualitzant.',
	'FILES_UP_TO_DATE'				=> 'Fitxers ja actualitzats',
	'FILES_UP_TO_DATE_EXPLAIN'		=> 'Els fitxers següents ja estan al dia i no cal actualitzar-los.',
	'FILES_VERSION'					=> 'Versió dels fitxers',
	'TOGGLE_DISPLAY'				=> 'Mostra/Oculta la llista de fitxers',

	// File updater
	'UPDATE_UPDATING_FILES'	=> 'S’estan actualitzant els fitxers',

	'UPDATE_FILE_UPDATER_HAS_FAILED'	=> 'L’actualitzador de fitxers “%1$s“ ha fallat. L’instal·lador intentarà usar el sistema alternatiu “%2$s“.',
	'UPDATE_FILE_UPDATERS_HAVE_FAILED'	=> 'L’actualitzador de fitxers fallat. No queda cap més sistema alternatiu.',

	'UPDATE_CONTINUE_UPDATE_PROCESS'	=> 'Continua el procés d’actualització',
	'UPDATE_RECHECK_UPDATE_FILES'		=> 'Torna a comprovar els fitxers',
));

// Update database
$lang = array_merge($lang, array(
	'STAGE_UPDATE_DATABASE'		=> 'Actualització de la base de dades',

	'INLINE_UPDATE_SUCCESSFUL'		=> 'La base de dades s’ha actualitzat correctament.',

	'TASK_UPDATE_EXTENSIONS'	=> 'S’estan actualitzant les extensions',
));

// Converter
$lang = array_merge($lang, array(
	// Common converter messages
	'CONVERT_NOT_EXIST'			=> 'El convertidor especificat no existeix.',
	'DEV_NO_TEST_FILE'			=> 'No s’ha especificat cap valor per a la variable test_file al convertidor. Si sou un usuari del convertidor, no hauríeu de veure aquest error, si us plau comuniqueu aquest error a l’autor del convertidor. Si sou l’autor del convertidor, heu d’especificar el nom d’un fitxer que existeixi a la taula origen per que es pugui verificar-ne el camí.',
	'COULD_NOT_FIND_PATH'		=> 'No s’ha pogut trobar el camí de l’antic fòrum. Si us plau, comproveu les configuracions i torneu a provar-ho.<br />» El camí d’origen que heu especificat és %s.',
	'CONFIG_PHPBB_EMPTY'		=> 'La variable de configuració del phpBB3 “%s” és buida.',

	'MAKE_FOLDER_WRITABLE'		=> 'Asegureu-vos que el directori existeix i que el servidor web hi pot escriure abans de tornar-ho a provar:<br />»<strong>%s</strong>.',
	'MAKE_FOLDERS_WRITABLE'		=> 'Asegureu-vos que els directoris existeixen i que el servidor web hi pot escriure abans de tornar-ho a provar:<br />»<strong>%s</strong>.',

	'INSTALL_TEST'				=> 'Torna-ho a verificar',

	'NO_TABLES_FOUND'			=> 'No s’ha trobat cap taula.',
	'TABLES_MISSING'			=> 'No s’han pogut trobar les taules següents<br />» <strong>%s</strong>.',
	'CHECK_TABLE_PREFIX'		=> 'Comproveu el prefix de la taula i torneu a provar-ho.',

	// Conversion in progress
	'CATEGORY'					=> 'Categoria',
	'CONTINUE_CONVERT'			=> 'Continua la conversió',
	'CONTINUE_CONVERT_BODY'		=> 'S’ha trobat un intent de conversió anterior. Podeu triar entre començar una conversió nova o continuar la conversió existent.',
	'CONVERT_NEW_CONVERSION'	=> 'Conversió nova',
	'CONTINUE_OLD_CONVERSION'	=> 'Continua la conversió iniciada anteriorment',
	'POST_ID'					=> 'ID de l’entrada',

	// Start conversion
	'SUB_INTRO'					=> 'Introducció',
	'CONVERT_INTRO'				=> 'Benvingut a l’Entorn unificat de conversió del phpBB',
	'CONVERT_INTRO_BODY'		=> 'Aquí podeu importar dades d’altres sistemes de fòrums que tingueu instal·lats. La llista següent mostra tots els mòduls de conversió disponibles actualment. Si a la llista no es mostra el convertidor per al tipus de fòrum que voleu convertir, comproveu si al nostre lloc web hi ha mòduls de conversió nous disponibles per baixar.',
	'AVAILABLE_CONVERTORS'		=> 'Convertidors disponibles',
	'NO_CONVERTORS'				=> 'No hi ha cap convertidor disponible per usar.',
	'CONVERT_OPTIONS'			=> 'Opcions',
	'SOFTWARE'					=> 'Programari del fòrum',
	'VERSION'					=> 'Versió',
	'CONVERT'					=> 'Converteix',

	// Settings
	'STAGE_SETTINGS'			=> 'Configuracions',
	'TABLE_PREFIX_SAME'			=> 'El prefix de les taules ha de ser el que utilitza el fòrum quu esteu convertint.<br />» El prefix de taules que heu especificat és %s.',
	'DEFAULT_PREFIX_IS'			=> 'El convertidor no ha pogut trobar taules amb el prefix especificat. Assegureu-vos que heu introduït les dades correctes del fòrum que voleu convertir. El prefix per defecte per a les taules de %1$s és <strong>%2$s</strong>.',
	'SPECIFY_OPTIONS'			=> 'Especifiqueu les opcións de conversió',
	'FORUM_PATH'				=> 'Camí del fòrum',
	'FORUM_PATH_EXPLAIN'		=> 'És el camí <strong>relatiu</strong> al disc del <strong>directori arrel d’aquesta instal·lació del phpBB3</strong> al vostre antic fòrum.',
	'REFRESH_PAGE'				=> 'Refresca aquesta pàgina per continuar amb la conversio',
	'REFRESH_PAGE_EXPLAIN'		=> 'Si trieu Sí, el convertidor refrescarà la pàgina per continuar amb la conversió després d’acabar un pas. Si aquesta és la vostra primera conversió per fer proves i determinar qualsevol error per endavant us recomanem que trieu No.',

	// Conversion
	'STAGE_IN_PROGRESS'			=> 'Conversió en curs',

	'AUTHOR_NOTES'				=> 'Notes de l’autor<br />» %s',
	'STARTING_CONVERT'			=> 'S’està iniciant el procés de conversió',
	'CONFIG_CONVERT'			=> 'S’està convertint la configuració',
	'DONE'						=> 'Fet',
	'PREPROCESS_STEP'			=> 'S’estan executant les funcions/consultes de preprocessament',
	'FILLING_TABLE'				=> 'S’està omplint la taula <strong>%s</strong>',
	'FILLING_TABLES'			=> 'S’estan omplint les taules',
	'DB_ERR_INSERT'				=> 'S’ha produït un error en processar una consulta de tipus <code>INSERT</code>.',
	'DB_ERR_LAST'				=> 'S’ha produït un error en processar <var>query_last</var>.',
	'DB_ERR_QUERY_FIRST'		=> 'S’ha produït un error en executar <var>query_first</var>.',
	'DB_ERR_QUERY_FIRST_TABLE'	=> 'S’ha produït un error en executar <var>query_first</var>, %s (“%s”).',
	'DB_ERR_SELECT'				=> 'S’ha produït un error en executar una consulta de tipus <code>SELECT</code>.',
	'STEP_PERCENT_COMPLETED'	=> 'Pas <strong>%d</strong> de <strong>%d</strong>',
	'FINAL_STEP'				=> 'Processa el pas final',
	'SYNC_FORUMS'				=> 'S’està iniciant la sincronització dels fòrums',
	'SYNC_POST_COUNT'			=> 'S’està sincronitzant post_counts',
	'SYNC_POST_COUNT_ID'		=> 'S’està sincronitzant post_counts des de <var>entry</var> %1$s a %2$s.',
	'SYNC_TOPICS'				=> 'S’està iniciant la sincronització dels temes',
	'SYNC_TOPIC_ID'				=> 'S’estan sincronitzant els temes del <var>topic_id</var> %1$s al %2$s.',
	'PROCESS_LAST'				=> 'S’estan processant les darreres ordres',
	'UPDATE_TOPICS_POSTED'		=> 'S’està generant la informació dels temes publicats',
	'UPDATE_TOPICS_POSTED_ERR'	=> 'S’ha produït un error mentre es generava la informació dels temes publicats. Podeu reintentar aquest pas al TCA quan acabi el procés de conversió.',
	'CONTINUE_LAST'				=> 'Continua les darreres declaracions',
	'CLEAN_VERIFY'				=> 'S’està netejant i verificant l’estructura final',
	'NOT_UNDERSTAND'			=> 'No s’ha comprès %s #%d, taula %s (“%s”)',
	'NAMING_CONFLICT'			=> 'Conflicte de noms: tant %s com %s són àlies<br /><br />%s',

	// Finish conversion
	'CONVERT_COMPLETE'			=> 'S’ha acabat la conversió',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'S’ha convertit el vostre fòrum a phpBB 3.3 correctament. Ja podeu iniciar la sessió i <a href="../">accedir al vostre fòrum</a>. Assegureu-vos que les configuracions s’han transferit correctament abans d’habilitar el fòrum eliminant el directori “install”. Recordeu que disposeu d’ajuda en línia sobre l’ús del phpBB a la <a href="https://www.phpbb.com/support/docs/en/3.3/ug/">documentació</a> (en anglès) i als <a href="https://www.phpbb.com/community/viewforum.php?f=661">fòrums d’assistència</a> (en anglès).',

	'COLLIDING_CLEAN_USERNAME'			=> '<strong>%s</strong> és el nom d’usuari netejat per:',
	'COLLIDING_USER'					=> '» id d’usuari: <strong>%d</strong> nom d’usuari: <strong>%s</strong> (%d entrades)',
	'COLLIDING_USERNAMES_FOUND'			=> 'S’han trobat noms d’usuari coincidents al fòrum antic. Per tal de completar la conversió heu de canviar el nom d’aquests usuaris o eliminar-los de manera que només hi hagi un sol usuari al fòrum antic per cada nom d’usuari netetejat.',
	'CONV_ERR_FATAL'					=> 'Error fatal de conversió',
	'CONV_ERROR_ATTACH_FTP_DIR'			=> 'Les càrregues FTP per a fitxers adjunts estan habilitades al fòrum antic. Inhabiliteu l’opció de càrregues FTP i assegureu-vos que hi ha un directori de càrregues vàlid especificat, llavors copieu tots els fitxers adjunts a aquest directori nou accessible des d’Internet. Un cop ho hagueu fet, reinicieu el convertidor.',
	'CONV_ERROR_CONFIG_EMPTY'			=> 'No hi ha informació de configuració disponible per a la conversió.',
	'CONV_ERROR_FORUM_ACCESS'			=> 'No s’ha pogut obtenir informació d’accés al fòrum.',
	'CONV_ERROR_GET_CATEGORIES'			=> 'No s’ha pogut obtenir les categories.',
	'CONV_ERROR_GET_CONFIG'				=> 'No s’ha pogut obtenir la configuració del vostre fòrum.',
	'CONV_ERROR_COULD_NOT_READ'			=> 'No s’ha pogut accedir/llegir “%s”.',
	'CONV_ERROR_GROUP_ACCESS'			=> 'No s’ha pogut obtenir informació d’autenticació de grups.',
	'CONV_ERROR_INCONSISTENT_GROUPS'	=> 'S’ha detectat una inconsistència en la taula de grups a add_bots() - heu d’afegir tots els grups especials si ho feu manualment.',
	'CONV_ERROR_INSERT_BOT'				=> 'No s’ha pogut insertar el robot a la taula users.',
	'CONV_ERROR_INSERT_BOTGROUP'		=> 'No s’ha pogut insertar el robot a la taula bots.',
	'CONV_ERROR_INSERT_USER_GROUP'		=> 'No s’ha pogut insertar l’usuari a la taula user_group.',
	'CONV_ERROR_MESSAGE_PARSER'			=> 'Error de l’analitzador de missatges',
	'CONV_ERROR_NO_AVATAR_PATH'			=> 'Nota per al desenvolupador: heu d’especificar $convertor[\'avatar_path\'] per usar %s.',
	'CONV_ERROR_NO_FORUM_PATH'			=> 'No s’ha especificat el camí relatiu al fòrum origen.',
	'CONV_ERROR_NO_GALLERY_PATH'		=> 'Nota per al desenvolupador: heu d’especificar $convertor[\'avatar_gallery_path\'] per usar %s.',
	'CONV_ERROR_NO_GROUP'				=> 'No s’ha pogut trobar el grup “%1$s” a %2$s.',
	'CONV_ERROR_NO_RANKS_PATH'			=> 'Nota per al desenvolupador: heu d’especificar $convertor[\'ranks_path\'] per usar %s.',
	'CONV_ERROR_NO_SMILIES_PATH'		=> 'Nota per al desenvolupador: heu d’especificar $convertor[\'smilies_path\'] per usar %s.',
	'CONV_ERROR_NO_UPLOAD_DIR'			=> 'Nota per al desenvolupador: heu d’especificar $convertor[\'upload_path\'] per usar %s.',
	'CONV_ERROR_PERM_SETTING'			=> 'No s’ha pogut inserir/actualitzar la configuració de permisos.',
	'CONV_ERROR_PM_COUNT'				=> 'No s’ha pogut seleccionar el compte de MP de la carpeta.',
	'CONV_ERROR_REPLACE_CATEGORY'		=> 'No s’ha pogut inserir un fòrum nou per reemplaçar la categoria antiga.',
	'CONV_ERROR_REPLACE_FORUM'			=> 'No s’ha pogut inserir un fòrum nou per reemplaçar el fòrum antic.',
	'CONV_ERROR_USER_ACCESS'			=> 'No s’ha pogut obtenir informació d’autenticació d’usuaris.',
	'CONV_ERROR_WRONG_GROUP'			=> 'Grup erroni “%1$s” definit a %2$s.',
	'CONV_OPTIONS_BODY'					=> 'Aquesta pàgina recull les dades necessàries per accedir al fòrum d’origen. Introduïu la informació de la base de dades del vostre fòrum antic; el convertidor no canviarà res de la base de dades proporcionada a continuació. És recomanable que el fòrum d’origen estigui inhabilitat per permetre una conversió consistent.',
	'CONV_SAVED_MESSAGES'				=> 'Missatges desats',

	'PRE_CONVERT_COMPLETE'			=> 'S’han completat correctament tots els passos previs a la conversió. Ja podeu començar el procés de conversió en sí. Tingueu en compte que és possible que hagueu fer diverses tasques i ajustaments de forma manual. Després de la conversió, comproveu especialment els permisos assignats, reconstruïu l’índex de cerca si fos necessari i assegureu-vos que els fitxers com ara els avatars i les emoticones s’han copiat correctament.',
));
