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

// Common
$lang = array_merge($lang, array(
	'ACP_ADMINISTRATORS'		=> 'Administradors',
	'ACP_ADMIN_LOGS'			=> 'Registre dels administradors',
	'ACP_ADMIN_ROLES'			=> 'Rols dels administradors',
	'ACP_ATTACHMENTS'			=> 'Fitxers adjunts',
	'ACP_ATTACHMENT_SETTINGS'	=> 'Configuració dels fitxers adjunts',
	'ACP_AUTH_SETTINGS'			=> 'Autenticació',
	'ACP_AUTOMATION'			=> 'Automatització',
	'ACP_AVATAR_SETTINGS'		=> 'Configuració dels avatars',

	'ACP_BACKUP'				=> 'Còpia de seguretat',
	'ACP_BAN'					=> 'Bandeig',
	'ACP_BAN_EMAILS'			=> 'Bandeig d’adreces electròniques',
	'ACP_BAN_IPS'				=> 'Bandeig d’adreces IP',
	'ACP_BAN_USERNAMES'			=> 'Bandeig d’usuaris',
	'ACP_BBCODES'				=> 'BBCodes',
	'ACP_BOARD_CONFIGURATION'	=> 'Configuració del fòrum',
	'ACP_BOARD_FEATURES'		=> 'Característiques del fòrum',
	'ACP_BOARD_MANAGEMENT'		=> 'Gestió del fòrum',
	'ACP_BOARD_SETTINGS'		=> 'Configuració del fòrum',
	'ACP_BOTS'					=> 'Aranyes/Robots',

	'ACP_CAPTCHA'				=> 'CAPTCHA',

	'ACP_CAT_CUSTOMISE'			=> 'Personalització',
	'ACP_CAT_DATABASE'			=> 'Base de dades',
	'ACP_CAT_DOT_MODS'			=> 'Extensions',
	'ACP_CAT_FORUMS'			=> 'Fòrums',
	'ACP_CAT_GENERAL'			=> 'General',
	'ACP_CAT_MAINTENANCE'		=> 'Manteniment',
	'ACP_CAT_PERMISSIONS'		=> 'Permisos',
	'ACP_CAT_POSTING'			=> 'Enviament d’entrades',
	'ACP_CAT_STYLES'			=> 'Estils',
	'ACP_CAT_SYSTEM'			=> 'Sistema',
	'ACP_CAT_USERGROUP'			=> 'Usuaris i grups',
	'ACP_CAT_USERS'				=> 'Usuaris',
	'ACP_CLIENT_COMMUNICATION'	=> 'Comunicació amb clients',
	'ACP_COOKIE_SETTINGS'		=> 'Configuració de les galetes',
	'ACP_CONTACT'				=> 'Pàgina de contacte',
	'ACP_CONTACT_SETTINGS'		=> 'Configuració de la pàgina de contacte',
	'ACP_CRITICAL_LOGS'			=> 'Registre d’errors',
	'ACP_CUSTOM_PROFILE_FIELDS'	=> 'Camps del perfil personalitzats',

	'ACP_DATABASE'				=> 'Gestió de la base de dades',
	'ACP_DISALLOW'				=> 'Prohibició',
	'ACP_DISALLOW_USERNAMES'	=> 'Prohibició de noms d’usuari',

	'ACP_EMAIL_SETTINGS'		=> 'Configuració del correu electrònic',
	'ACP_EXTENSION_GROUPS'		=> 'Gestió de grups d’extensions de fitxers adjunts',
	'ACP_EXTENSION_MANAGEMENT'	=> 'Gestió d’extensions',
	'ACP_EXTENSIONS'			=> 'Gestiona les extensions',

	'ACP_FORUM_BASED_PERMISSIONS'	=> 'Permisos a nivell de fòrum',
	'ACP_FORUM_LOGS'				=> 'Registres de fòrums',
	'ACP_FORUM_MANAGEMENT'			=> 'Gestió de fòrums',
	'ACP_FORUM_MODERATORS'			=> 'Moderadors de fòrums',
	'ACP_FORUM_PERMISSIONS'			=> 'Permisos de fòrums',
	'ACP_FORUM_PERMISSIONS_COPY'	=> 'Copia els permisos del fòrum',
	'ACP_FORUM_ROLES'				=> 'Rols de fòrums',

	'ACP_GENERAL_CONFIGURATION'		=> 'Configuració general',
	'ACP_GENERAL_TASKS'				=> 'Tasques generals',
	'ACP_GLOBAL_MODERATORS'			=> 'Moderadors globals',
	'ACP_GLOBAL_PERMISSIONS'		=> 'Permisos globals',
	'ACP_GROUPS'					=> 'Grups',
	'ACP_GROUPS_FORUM_PERMISSIONS'	=> 'Permisos grups de de fòrums',
	'ACP_GROUPS_MANAGE'				=> 'Gestió dels grups',
	'ACP_GROUPS_MANAGEMENT'			=> 'Gestió de grups',
	'ACP_GROUPS_PERMISSIONS'		=> 'Permisos de grups',
	'ACP_GROUPS_POSITION'			=> 'Gestiona posicions de grups',

	'ACP_HELP_PHPBB'			=> 'Ajuda al suport del phpBB',

	'ACP_ICONS'					=> 'Icones de tema',
	'ACP_ICONS_SMILIES'			=> 'Icones de tema/emoticones',
	'ACP_INACTIVE_USERS'		=> 'Usuaris inactius',
	'ACP_INDEX'					=> 'Índex del TCA',

	'ACP_JABBER_SETTINGS'		=> 'Configuració del Jabber',

	'ACP_LANGUAGE'				=> 'Gestió d’idiomes',
	'ACP_LANGUAGE_PACKS'		=> 'Paquets d’idioma',
	'ACP_LOAD_SETTINGS'			=> 'Configuració de càrrega',
	'ACP_LOGGING'				=> 'Enregistrament',

	'ACP_MAIN'					=> 'Índex del TCA',

	'ACP_MANAGE_ATTACHMENTS'			=> 'Gestió de fitxers adjunts',
	'ACP_MANAGE_ATTACHMENTS_EXPLAIN'	=> 'Aquí podeu llistar i eliminar fitxers adjunts a entrades i missatges privats.',

	'ACP_MANAGE_EXTENSIONS'		=> 'Gestió de les extensions de fitxers adjunts',
	'ACP_MANAGE_FORUMS'			=> 'Gestió dels fòrums',
	'ACP_MANAGE_RANKS'			=> 'Gestió dels rangs',
	'ACP_MANAGE_REASONS'		=> 'Gestió de les raons d’informe/rebuig',
	'ACP_MANAGE_USERS'			=> 'Gestió dels usuaris',
	'ACP_MASS_EMAIL'			=> 'Correu electrònic massiu',
	'ACP_MESSAGES'				=> 'Missatges',
	'ACP_MESSAGE_SETTINGS'		=> 'Configuració dels missatges privats',
	'ACP_MODULE_MANAGEMENT'		=> 'Configuració de mòduls',
	'ACP_MOD_LOGS'				=> 'Registre dels moderadors',
	'ACP_MOD_ROLES'				=> 'Rols dels moderadors',

	'ACP_NO_ITEMS'				=> 'Encara no hi ha cap element.',

	'ACP_ORPHAN_ATTACHMENTS'	=> 'Fitxers adjunts orfes',

	'ACP_PERMISSIONS'			=> 'Permisos',
	'ACP_PERMISSION_MASKS'		=> 'Màscares de permisos',
	'ACP_PERMISSION_ROLES'		=> 'Permisos dels rols',
	'ACP_PERMISSION_TRACE'		=> 'Traça de permisos',
	'ACP_PHP_INFO'				=> 'Informació del PHP',
	'ACP_POST_SETTINGS'			=> 'Configuració de les entrades',
	'ACP_PRUNE_FORUMS'			=> 'Poda de fòrums',
	'ACP_PRUNE_USERS'			=> 'Poda d’usuaris',
	'ACP_PRUNING'				=> 'Poda',

	'ACP_QUICK_ACCESS'			=> 'Accés ràpid',

	'ACP_RANKS'					=> 'Rangs',
	'ACP_REASONS'				=> 'Raons d’informe/rebuig',
	'ACP_REGISTER_SETTINGS'		=> 'Configuració de registre d’usuaris',

	'ACP_RESTORE'				=> 'Restaura',

	'ACP_FEED'					=> 'Gestió de canals d’informació',
	'ACP_FEED_SETTINGS'			=> 'Configuració de canals d’informació',

	'ACP_SEARCH'				=> 'Configuració de cerca',
	'ACP_SEARCH_INDEX'			=> 'Índex de cerca',
	'ACP_SEARCH_SETTINGS'		=> 'Configuració de cerca',

	'ACP_SECURITY_SETTINGS'		=> 'Configuració de seguretat',
	'ACP_SERVER_CONFIGURATION'	=> 'Configuració del servidor',
	'ACP_SERVER_SETTINGS'		=> 'Configuració del servidor',
	'ACP_SIGNATURE_SETTINGS'	=> 'Configuració de les signatures',
	'ACP_SMILIES'				=> 'Emoticones',
	'ACP_STYLE_MANAGEMENT'		=> 'Gestió dels estils',
	'ACP_STYLES'				=> 'Estils',
	'ACP_STYLES_CACHE'			=> 'Buida la memoria cau',
	'ACP_STYLES_INSTALL'		=> 'Instal·la estils',

	'ACP_SUBMIT_CHANGES'		=> 'Tramet els canvis',

	'ACP_TEMPLATES'				=> 'Plantilles',
	'ACP_THEMES'				=> 'Temes',

	'ACP_UPDATE'					=> 'Actualització',
	'ACP_USERS_FORUM_PERMISSIONS'	=> 'Permisos de fòrums per usuaris',
	'ACP_USERS_LOGS'				=> 'Registre d’usuaris',
	'ACP_USERS_PERMISSIONS'			=> 'Permisos d’usuaris',
	'ACP_USER_ATTACH'				=> 'Fitxers adjunts',
	'ACP_USER_AVATAR'				=> 'Avatar',
	'ACP_USER_FEEDBACK'				=> 'Comentaris',
	'ACP_USER_GROUPS'				=> 'Grups',
	'ACP_USER_MANAGEMENT'			=> 'Gestió d’usuaris',
	'ACP_USER_OVERVIEW'				=> 'Resum',
	'ACP_USER_PERM'					=> 'Permisos',
	'ACP_USER_PREFS'				=> 'Preferències',
	'ACP_USER_PROFILE'				=> 'Perfil',
	'ACP_USER_RANK'					=> 'Rang',
	'ACP_USER_ROLES'				=> 'Rols d’usuari',
	'ACP_USER_SECURITY'				=> 'Seguretat dels usuaris',
	'ACP_USER_SIG'					=> 'Signatura',
	'ACP_USER_WARNINGS'				=> 'Advertiments',

	'ACP_VC_SETTINGS'					=> 'Mesures contra els robots de brossa',
	'ACP_VC_CAPTCHA_DISPLAY'			=> 'Previsualització d’imatges CAPTCHA',
	'ACP_VERSION_CHECK'					=> 'Comprova si hi ha actualitzacions',
	'ACP_VIEW_ADMIN_PERMISSIONS'		=> 'Visualització dels permisos d’administració',
	'ACP_VIEW_FORUM_MOD_PERMISSIONS'	=> 'Visualització dels permisos de moderació',
	'ACP_VIEW_FORUM_PERMISSIONS'		=> 'Visualització dels permisos per fòrum',
	'ACP_VIEW_GLOBAL_MOD_PERMISSIONS'	=> 'Visualització dels permisos de moderació global',
	'ACP_VIEW_USER_PERMISSIONS'			=> 'Visualització dels permisos per usuari',

	'ACP_WORDS'					=> 'Censura de paraules',

	'ACTION'				=> 'Acció',
	'ACTIONS'				=> 'Accions',
	'ACTIVATE'				=> 'Activa',
	'ADD'					=> 'Afegeix',
	'ADMIN'					=> 'Administració',
	'ADMIN_INDEX'			=> 'Índex de l’administrador',
	'ADMIN_PANEL'			=> 'Tauler de control de l’administrador',

	'ADM_LOGOUT'			=> 'Surt&nbsp;del&nbsp;TCA',
	'ADM_LOGGED_OUT'		=> 'Heu finalitzat la sessió del Tauler de control de l’administrador correctament',

	'BACK'					=> 'Enrere',

	'CONTAINER_EXCEPTION'	=> 'El phpBB ha generat un error en construir el contenidor a causa d’una de les extensions instal·lades. Per aquesta raó s’han inhabilitat temporalment totes les extensions. Proveu de netejar la memòria cau del fòrum. Totes les extensions s’habilitaran automàticament quan es resolgui l’error del contenidor. Si l’error continua, visiteu <a href="https://www.phpbb.com/support">phpBB.com</a> per rebre assistència.',
	'EXCEPTION' 			=> 'Excepció',

	'COLOUR_SWATCH'			=> 'Mostra de colors Web-safe',
	'CONFIG_UPDATED'		=> 'La configuració s’ha actualitzat correctament.',
	'CRON_LOCK_ERROR'		=> 'No s’ha pogut obtenir el bloqueig del cron.',
	'CRON_NO_SUCH_TASK'		=> 'No s’ha trobat la tasca de cron “%s”.',
	'CRON_NO_TASK'			=> 'Ara mateix no cal executar cap tasca cron.',
	'CRON_NO_TASKS'			=> 'No s’ha trobat cap tasca cron.',
	'CSV_INVALID'			=> 'La configuració separada per comes proporcionada “%1$s” no és vàlida. Els valors han d’estar delimitats només per comes, la configuració no ha de contenir delimitadors ni al principi ni al final.',
	'CURRENT_VERSION'		=> 'Versió actual',

	'DEACTIVATE'				=> 'Desactiva',
	'DIRECTORY_DOES_NOT_EXIST'	=> 'El camí introduït “%s” no existeix.',
	'DIRECTORY_NOT_DIR'			=> 'El camí introduït “%s” no es un directori.',
	'DIRECTORY_NOT_WRITABLE'	=> 'El camí introduït “%s” no és escrivible.',
	'DISABLE'					=> 'Inhabilita',
	'DOWNLOAD'					=> 'Baixa',
	'DOWNLOAD_AS'				=> 'Anomena i baixa',
	'DOWNLOAD_STORE'			=> 'Baixa o emmagatzema un fitxer',
	'DOWNLOAD_STORE_EXPLAIN'	=> 'Podeu baixar directament el fitxer o desar-lo al directori <samp>store/</samp>.',
	'DOWNLOADS'					=> 'Baixades',

	'EDIT'					=> 'Edita',
	'ENABLE'				=> 'Habilita',
	'EXPORT_DOWNLOAD'		=> 'Baixa',
	'EXPORT_STORE'			=> 'Emmagatzema',

	'GENERAL_OPTIONS'		=> 'Opcions generals',
	'GENERAL_SETTINGS'		=> 'Configuració general',
	'GLOBAL_MASK'			=> 'Màscara de permisos global',

	'INSTALL'				=> 'Instal·la',
	'IP'					=> 'IP de l’usuari',
	'IP_HOSTNAME'			=> 'Adreces IP o noms d’amfitrió',

	'LATEST_VERSION'		=> 'Versió més recent',
	'LOAD_NOTIFICATIONS'			=> 'Mostra les notificacions',
	'LOAD_NOTIFICATIONS_EXPLAIN'	=> 'Mostra la llista de notificacions a totes les pàgines (habitualment a a capçalera).',
	'LOGGED_IN_AS'			=> 'Heu iniciat la sessió com:',
	'LOGIN_ADMIN'			=> 'Per administrar el fòrum, heu de ser un usuari autenticat.',
	'LOGIN_ADMIN_CONFIRM'	=> 'Per administrar el fòrum us heu de reautenticar.',
	'LOGIN_ADMIN_SUCCESS'	=> 'Us heu autenticat correctament i sereu redirigit al Tauler de control de l’administrador.',
	'LOOK_UP_FORUM'			=> 'Seleccioneu un fòrum',
	'LOOK_UP_FORUMS_EXPLAIN'=> 'Podeu seleccionar més d’un fòrum.',

	'MANAGE'				=> 'Gestiona',
	'MENU_TOGGLE'			=> 'Oculta o mostra el menú lateral',
	'MORE'					=> 'Més',			// Not used at the moment
	'MORE_INFORMATION'		=> 'Més informació »',
	'MOVE_DOWN'				=> 'Baixa',
	'MOVE_UP'				=> 'Puja',

	'NOTIFY'				=> 'Notificació',
	'NO_ADMIN'				=> 'No esteu autoritzat a administrar aquest fòrum.',
	'NO_EMAILS_DEFINED'		=> 'No s’ha trobat cap adreça electrònica vàlida.',
	'NO_FILES_TO_DELETE'	=> 'Els fitxers adjunts que heu seleccionat per eliminar no existeixen.',
	'NO_PASSWORD_SUPPLIED'	=> 'Cal que introduïu la vostra contrasenya per accedir al Tauler de control de l’administrador.',

	'OFF'					=> 'Inactiu',
	'ON'					=> 'Actiu',

	'PARSE_BBCODE'						=> 'Transforma el BBCode',
	'PARSE_SMILIES'						=> 'Transforma les emoticones',
	'PARSE_URLS'						=> 'Transforma els enllaços',
	'PERMISSIONS_TRANSFERRED'			=> 'S’han transferit els permisos',
	'PERMISSIONS_TRANSFERRED_EXPLAIN'	=> 'Actualment teniu els permisos de %1$s. Podeu navegar pel fòrum amb els permisos d’aquest usuari, però no podeu accedir al Tauler de control de l’administrador ja que no s’han transferit els permisos d’administració. Podeu <a href="%2$s"><strong>tornar al vostre grup de permisos</strong></a> en qualsevol moment.',
	'PROCEED_TO_ACP'					=> '%sContinua cap al TCA%s',

	'RELEASE_ANNOUNCEMENT'		=> 'Avís',
	'REMIND'							=> 'Recordatori',
	'REPARSE_LOCK_ERROR'				=> 'Ja hi ha un reanàlisi en curs per un altre procés.',
	'RESYNC'							=> 'Resincronitza',

	'RUNNING_TASK'			=> 'Executant la tasca: %s.',
	'SELECT_ANONYMOUS'		=> 'Selecciona l’usuari anònim',
	'SELECT_OPTION'			=> 'Seleccioneu una opció',

	'SETTING_TOO_LOW'		=> 'El valor proporcionat per a la configuració “%1$s” és massa baix. El valor mínim acceptat és %2$d.',
	'SETTING_TOO_BIG'		=> 'El valor proporcionat per a la configuració “%1$s” és massa gran. El valor màxim acceptat és %2$d.',
	'SETTING_TOO_LONG'		=> 'El valor proporcionat per a la configuració “%1$s” és massa llarg. La llargada màxima acceptada és %2$d.',
	'SETTING_TOO_SHORT'		=> 'El valor proporcionat per a la configuració “%1$s” no es prou llarg. La llargada mínima acceptada és %2$d.',

	'SHOW_ALL_OPERATIONS'	=> 'Mostra totes les operacions',

	'TASKS_NOT_READY'			=> 'Tasques no preparades:',
	'TASKS_READY'			=> 'Tasques preparades:',
	'TOTAL_SIZE'			=> 'Mida total',

	'UCP'					=> 'Tauler de control de l’usuari',
	'URL_INVALID'			=> 'L’URL proporcionat per la configuració “%1$s” no és vàlid.',
	'URL_SCHEME_INVALID'	=> 'La combinació proporcionada “%2$s” a la configuració separada per comes “%1$s” no és vàlida. La combinació ha de començar amb un caràcter de l’alfabet llatí seguit de caràcters alfanumèrics, guions o punts.',
	'USERNAMES_EXPLAIN'		=> 'Introduïu cada nom d’usuari en una línia nova.',
	'USER_CONTROL_PANEL'	=> 'Tauler de control de l’usuari',

	'UPDATE_NEEDED'			=> 'El fòrum no està actualitzat.',
	'UPDATE_NOT_NEEDED'		=> 'El fòrum està actualitzat.',
	'UPDATES_AVAILABLE'		=> 'Actualitzacions disponibles:',

	'WARNING'				=> 'Advertiment',
));

// PHP info
$lang = array_merge($lang, array(
	'ACP_PHP_INFO_EXPLAIN'	=> 'Aquesta pàgina llista la informació de la versió del PHP instal·lada en aquest servidor. Inclou la informació dels mòduls carregats, variables disponibles i configuracions per defecte. Aquesta informació pot ser útil per diagnosticar problemes. Tingueu en compte que algunes companyies d’allotjament web limiten quina informació es mostra per questions de seguretat. És recomanable que no proporcioneu la informació d’aquesta pàgina excepte quan us la demani <a href="https://www.phpbb.com/about/team/">els membres de l’equip oficial</a> dels fòrums d’assistència.',

	'NO_PHPINFO_AVAILABLE'	=> 'No s’ha pogut obtenir informació de la vostra configuració del PHP. La funció phpinfo() està inhabilitada per raons de seguretat.',
));

// Logs
$lang = array_merge($lang, array(
	'ACP_ADMIN_LOGS_EXPLAIN'	=> 'Aquí es llisten totes les accions que els administradors han dut a terme. Podeu ordenar per nom d’usuari, data, IP o acció. Si teniu els permisos adequats, també podeu esborrar operacions individuals o el registre complet.',
	'ACP_CRITICAL_LOGS_EXPLAIN'	=> 'Aquí es llisten totes les accions dutes a terme pel propi fòrum. Aquest registre us proporciona informació que podeu usar per resoldre problemes determinats, per exemple fallades d’enviament de correu electrònic. Podeu ordenar per nom d’usuari, data, IP o acció. Si teniu els permisos adequats, també podeu esborrar operacions individuals o el registre complet.',
	'ACP_MOD_LOGS_EXPLAIN'		=> 'Aquí es llisten totes les accions que els moderadors han dut a terme a fòrums, temes i entrades així com accions sobre usuaris, incloent-hi bandejos. Podeu ordenar per nom d’usuari, data, IP o acció. Si teniu els permisos adequats, també podeu esborrar operacions individuals o el registre complet.',
	'ACP_USERS_LOGS_EXPLAIN'	=> 'Aquí es llisten totes les accions que els usuaris han dut a terme sobre altres usuaris (informes, advertiments i notes de l’usuari).',
	'ALL_ENTRIES'				=> 'Totes les entrades',

	'DISPLAY_LOG'	=> 'Mostra els registres dels darrers',

	'NO_ENTRIES'	=> 'No hi ha registres per aquest període.',

	'SORT_IP'		=> 'Adreça IP',
	'SORT_DATE'		=> 'Data',
	'SORT_ACTION'	=> 'Acció del registre',
));

// Index page
$lang = array_merge($lang, array(
	'ADMIN_INTRO'				=> 'Gràcies per triar el phpBB per al vostre fòrum. Aquesta pantalla us mostrarà un resum ràpid de totes les estadístiques del fòrum. Els enllaços de la part esquerra de la pantalla us permeten controlar tots els aspectes del fòrum. Cada pàgina conté instruccions sobre com usar les eines.',
	'ADMIN_LOG'					=> 'Accions de l’administrador registrades',
	'ADMIN_LOG_INDEX_EXPLAIN'	=> 'Aquest és un resum de les cinc darreres accions dutes a terme pels administradors del fòrum. Podeu visualitzar una còpia sencera del registre des del menú correponent o fent clic a l’enllaç a sota.',
	'AVATAR_DIR_SIZE'			=> 'Mida del directori d’avatars',

	'BOARD_STARTED'		=> 'Data d’inici del fòrum',
	'BOARD_VERSION'		=> 'Versió del fòrum',

	'DATABASE_SERVER_INFO'	=> 'Servidor de la base de dades',
	'DATABASE_SIZE'			=> 'Mida de la base de dades',

	// Environment configuration checks, mbstring related
	'ERROR_MBSTRING_FUNC_OVERLOAD'					=> 'La sobrecàrrega de funcions no està configurada correctament',
	'ERROR_MBSTRING_FUNC_OVERLOAD_EXPLAIN'			=> '<var>mbstring.func_overload</var> s’ha d’establir a 0 o bé a 4. Podeu comprovar el valor actual a la pàgina d’<samp>informació del PHP</samp>.',
	'ERROR_MBSTRING_ENCODING_TRANSLATION'			=> 'La codificació transparent de caràcters no està configurada correctament',
	'ERROR_MBSTRING_ENCODING_TRANSLATION_EXPLAIN'	=> '<var>mbstring.encoding_translation</var> s’ha d’establir a 0. Podeu comprovar el valor actual a la pàgina d’<samp>informació del PHP</samp>.',
	'ERROR_MBSTRING_HTTP_INPUT'						=> 'La conversió de caràcters d’entrada HTTP no està configurada correctament',
	'ERROR_MBSTRING_HTTP_INPUT_EXPLAIN'				=> '<var>mbstring.http_input</var> s’ha de deixar en blanc. Podeu comprovar el valor actual a la pàgina d’<samp>informació del PHP</samp>.',
	'ERROR_MBSTRING_HTTP_OUTPUT'					=> 'La conversió de caràcters de sortida HTTP no està configurada correctament',
	'ERROR_MBSTRING_HTTP_OUTPUT_EXPLAIN'			=> '<var>mbstring.http_output</var> s’ha de deixar en blanc. Podeu comprovar el valor actual a la pàgina d’<samp>informació del PHP</samp>.',
	'ERROR_DEFAULT_CHARSET'							=> 'El joc de caràcters per defecte no està configurat correctament',
	'ERROR_DEFAULT_CHARSET_EXPLAIN'					=> '<var>default_charset</var> s’ha d’establir a <samp>UTF-8</samp>. Podeu comprovar el valor actual a la pàgina d’<samp>informació del PHP</samp>.',

	'FILES_PER_DAY'		=> 'Fitxers adjunts per dia',
	'FORUM_STATS'		=> 'Estadístiques del fòrum',

	'GZIP_COMPRESSION'	=> 'Compressió gzip',

	'NO_SEARCH_INDEX'	=> 'El sistema de cerques seleccionat no té cap índex de cerca.<br />Creeu l’índex per “%1$s” a la secció %2$síndex de cerca%3$s.',
	'NOT_AVAILABLE'		=> 'No disponible',
	'NUMBER_FILES'		=> 'Nombre de fitxers adjunts',
	'NUMBER_POSTS'		=> 'Nombre d’entrades',
	'NUMBER_TOPICS'		=> 'Nombre de temes',
	'NUMBER_USERS'		=> 'Nombre d’usuaris',
	'NUMBER_ORPHAN'		=> 'Fitxers adjunts orfes',

	'PHP_VERSION'		=> 'Versió del PHP',
	'PHP_VERSION_OLD'	=> 'No podreu usar futures versions del phpBB amb la versió del PHP instal·lada en aquest servidor (%1$s). La versió mínima necessària serà el PHP %2$s. %3$sDetalls%4$s',

	'POSTS_PER_DAY'		=> 'Entrades per dia',

	'PURGE_CACHE'			=> 'Buida la memòria cau',
	'PURGE_CACHE_CONFIRM'	=> 'Esteu segur que voleu buidar la memòria cau?',
	'PURGE_CACHE_EXPLAIN'	=> 'Elimina tots els elements relacionats amb la memòria cau, això inclu fitxers de plantilla i consultes.',
	'PURGE_CACHE_SUCCESS'	=> 'S’ha buidat la memoria cau correctament.',

	'PURGE_SESSIONS'			=> 'Elimina totes les sessions',
	'PURGE_SESSIONS_CONFIRM'	=> 'Esteu segur que voleu eliminar totes les sessions? Això finalitzarà la sessió de tots els usuaris.',
	'PURGE_SESSIONS_EXPLAIN'	=> 'Elimina totes les sessions. Això finalitzarà la sessió de tots els usuaris truncant la taula de sessions.',
	'PURGE_SESSIONS_SUCCESS'	=> 'S’han eliminat les sessions sorrectament.',

	'RESET_DATE'					=> 'Reinicialitza la data d’inici del fòrum',
	'RESET_DATE_CONFIRM'			=> 'Esteu segur que voleu reinicialitzar la data d’inici del fòrum?',
	'RESET_DATE_SUCCESS'				=> 'Reinicialitza la data d’inici del fòrum',
	'RESET_ONLINE'					=> 'Reinicialitza el nombre màxim d’usuaris connectats',
	'RESET_ONLINE_CONFIRM'			=> 'Esteu segur que voleu reinicialitzar el nombre màxim d’usuaris connectats?',
	'RESET_ONLINE_SUCCESS'				=> 'Reinicialitza el nombre màxim d’usuaris connectats',
	'RESYNC_POSTCOUNTS'				=> 'Resincronitza els comptadors d’entrades',
	'RESYNC_POSTCOUNTS_EXPLAIN'		=> 'Només es consideraran les entrades existents. Les entrades podades no es comptaran.',
	'RESYNC_POSTCOUNTS_CONFIRM'		=> 'Esteu segur que voleu resincronitzar els comptadors d’entrades?',
	'RESYNC_POSTCOUNTS_SUCCESS'			=> 'S’han resincronitzat els comptadors d’entrades',
	'RESYNC_POST_MARKING'			=> 'Resincronitza els temes marcats',
	'RESYNC_POST_MARKING_CONFIRM'	=> 'Esteu segur que voleu resincronitzar els temes marcats?',
	'RESYNC_POST_MARKING_EXPLAIN'	=> 'Primer desmarca tots els temes i llavors marca correctament els temes que han tingut alguna activitat durant els darrers 6 mesos.',
	'RESYNC_POST_MARKING_SUCCESS'	=> 'S’han resincronitzat els temes marcats',
	'RESYNC_STATS'					=> 'Resincronitza les estadístiques',
	'RESYNC_STATS_CONFIRM'			=> 'Esteu segur que voleu resincronitzar les estadístiques?',
	'RESYNC_STATS_EXPLAIN'			=> 'Recalcula el nombre total d’entrades, temes, usuaris i fitxers.',
	'RESYNC_STATS_SUCCESS'			=> 'S’han resincronitzat les estadístiques',
	'RUN'							=> 'Executa',

	'STATISTIC'					=> 'Estadística',
	'STATISTIC_RESYNC_OPTIONS'	=> 'Resincronitza o reinicialitza les estadístiques',

	'TIMEZONE_INVALID'	=> 'El fus horari que heu seleccionat no es vàlid.',
	'TIMEZONE_SELECTED'	=> '(seleccionat actualment)',
	'TOPICS_PER_DAY'	=> 'Temes per dia',

	'UPLOAD_DIR_SIZE'	=> 'Mida dels fitxers adjunts publicats',
	'USERS_PER_DAY'		=> 'Usuaris per dia',

	'VALUE'							=> 'Valor',
	'VERSIONCHECK_FAIL'				=> 'No s’ha pogut obtenir la informació de la versió més recent.',
	'VERSIONCHECK_FORCE_UPDATE'		=> 'Torna a comprovar la versió',
	'VERSION_CHECK'					=> 'Comprovació de versió',
	'VERSION_CHECK_EXPLAIN'			=> 'Comprova si la instal·lació del phpBB està actualitzada.',
	'VERSIONCHECK_INVALID_ENTRY'	=> 'La informació de la versió més recent conté una entrada no admesa.',
	'VERSIONCHECK_INVALID_URL'		=> 'La informació de la versió més recent conté un URL no vàlid.',
	'VERSIONCHECK_INVALID_VERSION'	=> 'La informació de la versió més recent conté una versió no vàlida.',
	'VERSION_NOT_UP_TO_DATE_ACP'	=> 'La instal·lació del phpBB no està actualitzada.<br />A continuació hi ha un enllaç a l’avís de llançament que conté més informació així com instruccions sobre el procés d’actualització.',
	'VERSION_NOT_UP_TO_DATE_TITLE'	=> 'La instal·lació del phpBB no està actualitzada.',
	'VERSION_UP_TO_DATE_ACP'		=> 'La vostra instal·lació del phpBB està actualitzada. No hi ha cap actualització disponible actualment.',
	'VIEW_ADMIN_LOG'				=> 'Mostra el registre de l’administrador',
	'VIEW_INACTIVE_USERS'			=> 'Mostra els usuaris inactius',

	'WELCOME_PHPBB'			=> 'Benvingut al phpBB',
	'WRITABLE_CONFIG'		=> 'El vostre fitxer de configuració (config.php) actualment pot ser modificat per qualsevol usuari. És molt recomanable que canvieu els permisos a 640 o, com a mínim, a 644 (per exemple: <a href="http://ca.wikipedia.org/wiki/Chmod" rel="external">chmod</a> 640 config.php).',
));

// Inactive Users
$lang = array_merge($lang, array(
	'INACTIVE_DATE'					=> 'Data de desactivació',
	'INACTIVE_REASON'				=> 'Raó',
	'INACTIVE_REASON_MANUAL'		=> 'Compte desactivat per un administrador',
	'INACTIVE_REASON_PROFILE'		=> 'S’han canviat dades del perfil',
	'INACTIVE_REASON_REGISTER'		=> 'Compte nou',
	'INACTIVE_REASON_REMIND'		=> 'Reactivació forçada per l’usuari',
	'INACTIVE_REASON_UNKNOWN'		=> 'Desconeguda',
	'INACTIVE_USERS'				=> 'Usuaris inactius',
	'INACTIVE_USERS_EXPLAIN'		=> 'Aquí es llisten els usuaris que s’han registrat, però que tenen el compte inactiu. Podeu activar-los, eliminar-los o enviar un recordatori (per correu electrònic).',
	'INACTIVE_USERS_EXPLAIN_INDEX'	=> 'Aquesta llista conté els 10 darrers usuaris registrats amb comptes inactius. Els comptes són inactius o bé perquè s’ha habilitat l’activació de comptes a la configuració de registre d’usuaris i els comptes d’aquests usuaris encara no s’han activat o bé perquè aquest comptes han estat desactivats. Disposeu d’una llista completa al menú corresponent o fent clic a l’enllaç següent des d’on podeu activar, eliminar o enviar un recordatori (per correu electrònic) a aquests usuaris.',

	'NO_INACTIVE_USERS'	=> 'No hi ha usuaris inactius',

	'SORT_INACTIVE'		=> 'Data de desactivació',
	'SORT_LAST_VISIT'	=> 'Darrera visita',
	'SORT_REASON'		=> 'Raó',
	'SORT_REG_DATE'		=> 'Data de registre',
	'SORT_LAST_REMINDER'=> 'Darrer recordatori',
	'SORT_REMINDER'		=> 'Recordatori enviat',

	'USER_IS_INACTIVE'		=> 'L’usuari és inactiu',
));

// Help support phpBB page
$lang = array_merge($lang, array(
	'EXPLAIN_SEND_STATISTICS'	=> 'Si us plau envieu informació sobre el vostre servidor i configuracions del fòrum al web del phpBB per fer-ne una anàlisi estadística. S’ha suprimit tota la informació que pot identificar-vos o identificar el vostre lloc web - les dades són completament <strong>anònimes</strong>. Basem les decisions sobre futures versions del phpBB en aquesta informació. Les estadístiques es fan públiques. També compartim questes dades amb el projecte PHP, el llenguatge de programació en què està escrit el phpBB.',
	'EXPLAIN_SHOW_STATISTICS'	=> 'Utilitzant el botó a sota podeu previsualitzar totes les variables que es transmetran.',
	'DONT_SEND_STATISTICS'		=> 'Torneu al TCA si no voleu enviar informació estadística al web del phpBB.',
	'GO_ACP_MAIN'				=> 'Ves a la pàgina d’inici del TCA',
	'HIDE_STATISTICS'			=> 'Oculta els detalls',
	'SEND_STATISTICS'			=> 'Envia estadístiques',
	'SEND_STATISTICS_LONG'		=> 'Envia informació estadística',
	'SHOW_STATISTICS'			=> 'Mostra els detalls',
	'THANKS_SEND_STATISTICS'	=> 'Gràcies per trametre la vostra informació.',
	'FAIL_SEND_STATISTICS'		=> 'No s’ha pogut enviar les estadístiques',
));

// Log Entries
$lang = array_merge($lang, array(
	'LOG_ACL_ADD_USER_GLOBAL_U_'		=> '<strong>Ha afegit o editat els permisos d’usuari dels usuaris</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_U_'		=> '<strong>Ha afegit o editat els permisos d’usuari dels grups</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_M_'		=> '<strong>Ha afegit o editat els permisos de moderador global dels usuaris</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_M_'		=> '<strong>Ha afegit o editat els permisos de moderador global dels grups</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_A_'		=> '<strong>Ha afegit o editat els permisos d’administrador dels usuaris</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_A_'		=> '<strong>Ha afegit o editat els permisos d’administrador dels grups</strong><br />» %s',

	'LOG_ACL_ADD_ADMIN_GLOBAL_A_'		=> '<strong>Ha afegit o editat els administradors</strong><br />» %s',
	'LOG_ACL_ADD_MOD_GLOBAL_M_'			=> '<strong>Ha afegit o editat els moderadors globals</strong><br />» %s',

	'LOG_ACL_ADD_USER_LOCAL_F_'			=> '<strong>Ha afegit o editat l’accés al fòrum</strong> %1$s per als usuaris<br />» %2$s',
	'LOG_ACL_ADD_USER_LOCAL_M_'			=> '<strong>Ha afegit o editat l’accés de moderador al fòrum</strong> %1$s per als usuaris<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_F_'		=> '<strong>Ha afegit o editat l’accés al fòrum</strong> %1$s per als grups<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_M_'		=> '<strong>Ha afegit o editat l’accés de moderador al fòrum</strong> %1$s per als grups <br />» %2$s',

	'LOG_ACL_ADD_MOD_LOCAL_M_'			=> '<strong>Ha afegit o editat moderadors</strong> a %1$s<br />» %2$s',
	'LOG_ACL_ADD_FORUM_LOCAL_F_'		=> '<strong>Ha afegit o editat els permisos del fòrum</strong> a %1$s<br />» %2$s',

	'LOG_ACL_DEL_ADMIN_GLOBAL_A_'		=> '<strong>Ha eliminat els administradors</strong><br />» %s',
	'LOG_ACL_DEL_MOD_GLOBAL_M_'			=> '<strong>Ha eliminat els moderadors globals</strong><br />» %s',
	'LOG_ACL_DEL_MOD_LOCAL_M_'			=> '<strong>Ha eliminat els moderadors</strong> a %1$s<br />» %2$s',
	'LOG_ACL_DEL_FORUM_LOCAL_F_'		=> '<strong>Ha eliminat els permisos d’usuari/grup del fòrum</strong> %1$s per als usuaris<br />» %2$s',

	'LOG_ACL_TRANSFER_PERMISSIONS'		=> '<strong>S’ha transferit els permisos de</strong><br />» %s',
	'LOG_ACL_RESTORE_PERMISSIONS'		=> '<strong>S’ha restaurat els permisos propis després d’usar els permisos de</strong><br />» %s',

	'LOG_ADMIN_AUTH_FAIL'		=> '<strong>No ha aconseguit iniciar la sessió d’administració correctament</strong>',
	'LOG_ADMIN_AUTH_SUCCESS'	=> '<strong>Ha iniciat la sessió d’administració correctament</strong>',

	'LOG_ATTACHMENTS_DELETED'	=> '<strong>Ha eliminat fitxers adjunts de l’usuari</strong><br />» %s',

	'LOG_ATTACH_EXT_ADD'		=> '<strong>Ha afegit o editat l’extensió del fitxer adjunt</strong><br />» %s',
	'LOG_ATTACH_EXT_DEL'		=> '<strong>Ha eliminat l’extensió del fitxer adjunt</strong><br />» %s',
	'LOG_ATTACH_EXT_UPDATE'		=> '<strong>Ha actualitzat l’extensió del fitxer adjunt</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_ADD'	=> '<strong>Ha afegit el grup d’extensions</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_EDIT'	=> '<strong>Ha editat el grup d’extensions</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_DEL'	=> '<strong>Ha eliminat el grup d’extensions</strong><br />» %s',
	'LOG_ATTACH_FILEUPLOAD'		=> '<strong>Ha penjat els fitxers adjunts orfes a l’entrada</strong><br />» ID %1$d - %2$s',
	'LOG_ATTACH_ORPHAN_DEL'		=> '<strong>Ha eliminat els fitxers adjunts orfes</strong><br />» %s',

	'LOG_BAN_EXCLUDE_USER'	=> '<strong>Ha exclòs l’usuari del bandeig</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_BAN_EXCLUDE_IP'	=> '<strong>Ha exclòs l’adreça IP del bandeig</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_BAN_EXCLUDE_EMAIL' => '<strong>Ha exclòs l’adreça electrònica del bandeig</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_BAN_USER'			=> '<strong>Ha bandejat l’usuari</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_BAN_IP'			=> '<strong>Ha bandejat l’adreça IP</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_BAN_EMAIL'			=> '<strong>Ha bandejat l’adreça electrònica</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_UNBAN_USER'		=> '<strong>Ha desbandejat l’usuari</strong><br />» %s',
	'LOG_UNBAN_IP'			=> '<strong>Ha desbandejat l’adreça IP</strong><br />» %s',
	'LOG_UNBAN_EMAIL'		=> '<strong>Ha desbandejat l’adreça electrònica</strong><br />» %s',

	'LOG_BBCODE_ADD'		=> '<strong>Ha afegit un BBCode nou</strong><br />» %s',
	'LOG_BBCODE_EDIT'		=> '<strong>Ha editat el BBCode</strong><br />» %s',
	'LOG_BBCODE_DELETE'		=> '<strong>Ha eliminat el BBCode</strong><br />» %s',
	'LOG_BBCODE_CONFIGURATION_ERROR'	=> '<strong>S’ha produït un error en configurar el BBCode</strong>: %1$s<br />» %2$s',

	'LOG_BOT_ADDED'		=> '<strong>Ha afegit el robot nou</strong><br />» %s',
	'LOG_BOT_DELETE'	=> '<strong>Ha eliminat el robot</strong><br />» %s',
	'LOG_BOT_UPDATED'	=> '<strong>Ha editat el robot</strong><br />» %s',

	'LOG_CLEAR_ADMIN'		=> '<strong>Ha esborrat el registre d’administradors</strong>',
	'LOG_CLEAR_CRITICAL'	=> '<strong>Ha esborrat el registre d’errors</strong>',
	'LOG_CLEAR_MOD'			=> '<strong>Ha esborrat el registre de moderadors</strong>',
	'LOG_CLEAR_USER'		=> '<strong>Ha esborrat el registre de l’usuari</strong><br />» %s',
	'LOG_CLEAR_USERS'		=> '<strong>Ha esborrat el registre d’usuaris</strong>',

	'LOG_CONFIG_ATTACH'			=> '<strong>Ha modificat la configuració de fitxers adjunts</strong>',
	'LOG_CONFIG_AUTH'			=> '<strong>Ha modificat la configuració d’autenticació</strong>',
	'LOG_CONFIG_AVATAR'			=> '<strong>Ha modificat la configuració dels avatars</strong>',
	'LOG_CONFIG_COOKIE'			=> '<strong>Ha modificat la configuració de les galetes</strong>',
	'LOG_CONFIG_EMAIL'			=> '<strong>Ha modificat la configuració del correu electrònic</strong>',
	'LOG_CONFIG_FEATURES'		=> '<strong>Ha modificat les característiques del fòrum</strong>',
	'LOG_CONFIG_LOAD'			=> '<strong>Ha modificat la configuració de càrrega</strong>',
	'LOG_CONFIG_MESSAGE'		=> '<strong>Ha modificat la configuració dels missatges privats</strong>',
	'LOG_CONFIG_POST'			=> '<strong>Ha modificat la configuració de les entrades</strong>',
	'LOG_CONFIG_REGISTRATION'	=> '<strong>Ha modificat la configuració de registre d’usuaris</strong>',
	'LOG_CONFIG_FEED'			=> '<strong>Ha modificat la configuració dels canals d’informació</strong>',
	'LOG_CONFIG_SEARCH'			=> '<strong>Ha modificat la configuració de cerca</strong>',
	'LOG_CONFIG_SECURITY'		=> '<strong>Ha modificat la configuració de seguretat</strong>',
	'LOG_CONFIG_SERVER'			=> '<strong>Ha modificat la configuració del servidor</strong>',
	'LOG_CONFIG_SETTINGS'		=> '<strong>Ha modificat la configuració del fòrum</strong>',
	'LOG_CONFIG_SIGNATURE'		=> '<strong>Ha modificat la configuració de les signatures</strong>',
	'LOG_CONFIG_VISUAL'			=> '<strong>Ha modificat la configuració contra robots de brossa</strong>',

	'LOG_APPROVE_TOPIC'			=> '<strong>Ha aprovat el tema</strong><br />» %s',
	'LOG_BUMP_TOPIC'			=> '<strong>L’usuari ha reactivat el tema</strong><br />» %s',
	'LOG_DELETE_POST'			=> '<strong>Ha eliminat l’entrada “%1$s” escrita per “%2$s” per la raó següent</strong><br />» %3$s',
	'LOG_DELETE_SHADOW_TOPIC'	=> '<strong>Ha eliminat el tema ombra</strong><br />» %s',
	'LOG_DELETE_TOPIC'			=> '<strong>Ha eliminat el tema “%1$s” escrit per “%2$s” per la raó següent</strong><br />» %3$s',
	'LOG_FORK'					=> '<strong>Ha copiat el tema</strong><br />» de %s',
	'LOG_LOCK'					=> '<strong>Ha bloquejat el tema</strong><br />» %s',
	'LOG_LOCK_POST'				=> '<strong>Ha bloquejat l’entrada</strong><br />» %s',
	'LOG_MERGE'					=> '<strong>Ha combinat entrades</strong> al tema<br />» %s',
	'LOG_MOVE'					=> '<strong>Ha desplaçat el tema</strong><br />» de %1$s a %2$s',
	'LOG_MOVED_TOPIC'			=> '<strong>Ha desplaçat el tema</strong><br />» %s',
	'LOG_PM_REPORT_CLOSED'		=> '<strong>Ha tancat l’informe de missatge privat</strong><br />» %s',
	'LOG_PM_REPORT_DELETED'		=> '<strong>Ha eliminat l’informe de missatge privat</strong><br />» %s',
	'LOG_POST_APPROVED'			=> '<strong>Ha aprovat l’entrada</strong><br />» %s',
	'LOG_POST_DISAPPROVED'		=> '<strong>Ha rebutjat l’entrada “%1$s” escrita per “%3$s” per la raó següent</strong><br />» %2$s',
	'LOG_POST_EDITED'			=> '<strong>Ha editat l’entrada “%1$s” escrita per “%2$s” per la raó següent</strong><br />» %3$s',
	'LOG_POST_RESTORED'			=> '<strong>Ha restaurat l’entrada</strong><br />» %s',
	'LOG_REPORT_CLOSED'			=> '<strong>Ha tancat l’informe</strong><br />» %s',
	'LOG_REPORT_DELETED'		=> '<strong>Ha eliminat l’informe</strong><br />» %s',
	'LOG_RESTORE_TOPIC'			=> '<strong>Ha restaurat el tema “%1$s” escrit per</strong><br />» %2$s',
	'LOG_SOFTDELETE_POST'		=> '<strong>Ha eliminat temporalment l’entrada “%1$s” escrita per “%2$s” per la raó següent</strong><br />» %3$s',
	'LOG_SOFTDELETE_TOPIC'		=> '<strong>Ha eliminat temporalment el tema “%1$s” escrit per “%2$s” per la raó següent</strong><br />» %3$s',
	'LOG_SPLIT_DESTINATION'		=> '<strong>Ha desplaçat les entrades dividides</strong><br />» a %s',
	'LOG_SPLIT_SOURCE'			=> '<strong>Ha dividit les entrades</strong><br />» de %s',

	'LOG_TOPIC_APPROVED'		=> '<strong>Ha aprovat el tema</strong><br />» %s',
	'LOG_TOPIC_RESTORED'		=> '<strong>Ha restaurat el tema</strong><br />» %s',
	'LOG_TOPIC_DISAPPROVED'		=> '<strong>Ha rebutjat el tema “%1$s” escrit per “%3$s” per la raó següent</strong><br />» %2$s',
	'LOG_TOPIC_RESYNC'			=> '<strong>Ha resincronitzat els comptadors de temes</strong><br />» %s',
	'LOG_TOPIC_TYPE_CHANGED'	=> '<strong>Ha canviat el tipus de tema</strong><br />» %s',
	'LOG_UNLOCK'				=> '<strong>Ha desbloquejat el tema</strong><br />» %s',
	'LOG_UNLOCK_POST'			=> '<strong>Ha desbloquejat l’entrada</strong><br />» %s',

	'LOG_DISALLOW_ADD'		=> '<strong>Ha prohibit el nom d’usuari</strong><br />» %s',
	'LOG_DISALLOW_DELETE'	=> '<strong>Ha eliminat la prohibició del nom d’usuari</strong>',

	'LOG_DB_BACKUP'			=> '<strong>Ha fet una còpia de seguretat de la base de dades</strong>',
	'LOG_DB_DELETE'			=> '<strong>Ha eliminat la còpia de seguretat de la base de dades</strong>',
	'LOG_DB_RESTORE'		=> '<strong>Ha restaurat la còpia de seguretat de la base de dades</strong>',

	'LOG_DOWNLOAD_EXCLUDE_IP'	=> '<strong>Ha exclòs l’adreça IP/nom d’amfitrió de la llista de baixada</strong><br />» %s',
	'LOG_DOWNLOAD_IP'			=> '<strong>Ha afegit l’adreça IP/nom d’amfitrió a la llista de baixada</strong><br />» %s',
	'LOG_DOWNLOAD_REMOVE_IP'	=> '<strong>Ha eliminat l’adreça IP/nom d’amfitrió de la llista de baixada</strong><br />» %s',

	'LOG_ERROR_JABBER'		=> '<strong>Error del Jabber</strong><br />» %s',
	'LOG_ERROR_EMAIL'		=> '<strong>Error del correu electrònic</strong><br />» %s',
	'LOG_ERROR_CAPTCHA'		=> '<strong>Error del CAPTCHA</strong><br />» %s',

	'LOG_FORUM_ADD'							=> '<strong>Ha creat un fòrum nou</strong><br />» %s',
	'LOG_FORUM_COPIED_PERMISSIONS'			=> '<strong>Ha copiat els permisos del fòrum</strong> de %1$s<br />» %2$s',
	'LOG_FORUM_DEL_FORUM'					=> '<strong>Ha eliminat un fòrum</strong><br />» %s',
	'LOG_FORUM_DEL_FORUMS'					=> '<strong>Ha eliminat un fòrum i els seus subfòrums</strong><br />» %s',
	'LOG_FORUM_DEL_MOVE_FORUMS'				=> '<strong>Ha eliminat un fòrum i ha mogut els subfòrums</strong> a %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS'				=> '<strong>Ha eliminat un fòrum i ha mogut les entrades </strong> a %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_FORUMS'		=> '<strong>Ha eliminat un fòrum i els subfòrums i ha mogut les entrades</strong> a %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_MOVE_FORUMS'	=> '<strong>Ha eliminat un fòrum i ha mogut les entrades </strong> a %1$s <strong>i els subfòrums</strong> a %2$s<br />» %3$s',
	'LOG_FORUM_DEL_POSTS'					=> '<strong>Ha eliminat un fòrum i les seves entrades</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_FORUMS'			=> '<strong>Ha eliminat un fòrum, les seves entrades i els seus subfòrums</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_MOVE_FORUMS'		=> '<strong>Ha eliminat un fòrum i les seves entrades, ha mogut els subfòrums</strong> a %1$s<br />» %2$s',
	'LOG_FORUM_EDIT'						=> '<strong>Ha editat els detalls del fòrum</strong><br />» %s',
	'LOG_FORUM_MOVE_DOWN'					=> '<strong>Ha mogut el fòrum </strong> %1$s <strong>a sota de</strong> %2$s',
	'LOG_FORUM_MOVE_UP'						=> '<strong>Ha mogut el fòrum </strong> %1$s <strong>a sobre de</strong> %2$s',
	'LOG_FORUM_SYNC'						=> '<strong>Ha resincronitzat el fòrum</strong><br />» %s',

	'LOG_GENERAL_ERROR'	=> '<strong>S’ha produït un error general</strong>: %1$s <br />» %2$s',

	'LOG_GROUP_CREATED'		=> '<strong>Ha creat un nou grup d’usuaris</strong><br />» %s',
	'LOG_GROUP_DEFAULTS'	=> '<strong>Ha fet de “%1$s” el grup per defecte dels membres</strong><br />» %2$s',
	'LOG_GROUP_DELETE'		=> '<strong>Ha eliminat el grup d’usuaris</strong><br />» %s',
	'LOG_GROUP_DEMOTED'		=> '<strong>Ha fet que deixin de ser líders del grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_GROUP_PROMOTED'	=> '<strong>Ha ascendit aquests membres a líder del grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_GROUP_REMOVE'		=> '<strong>Ha tret aquests membres del grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_GROUP_UPDATED'		=> '<strong>Ha actualitzat els detalls del grup d’usuaris</strong><br />» %s',
	'LOG_MODS_ADDED'		=> '<strong>Ha afegit líders nous al grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_USERS_ADDED'		=> '<strong>Ha afegit membres nous al grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_USERS_APPROVED'	=> '<strong>Ha aprovat l’entrada d’aquests usuaris al grup d’usuaris</strong> %1$s<br />» %2$s',
	'LOG_USERS_PENDING'		=> '<strong>Aquests usuaris han demanat afiliar-se al grup “%1$s” i cal aprovar-ne l’entrada</strong><br />» %2$s',

	'LOG_IMAGE_GENERATION_ERROR'	=> '<strong>S’ha produït un error mentre es creava la imatge</strong><br />» Error a %1$s a la línia %2$s: %3$s',

	'LOG_INACTIVE_ACTIVATE'	=> '<strong>Ha activat els usuaris inactius</strong><br />» %s',
	'LOG_INACTIVE_DELETE'	=> '<strong>Ha eliminat els usuaris inactius</strong><br />» %s',
	'LOG_INACTIVE_REMIND'	=> '<strong>Ha enviat un correu electrònic de recordatori als usuaris inactius</strong><br />» %s',
	'LOG_INSTALL_CONVERTED'	=> '<strong>Ha fet la conversió de %1$s al phpBB %2$s</strong>',
	'LOG_INSTALL_INSTALLED'	=> '<strong>Ha instal·lat el phpBB %s</strong>',

	'LOG_IP_BROWSER_FORWARDED_CHECK'	=> '<strong>Ha fallat la comprovació de sessió IP/navegador/X_FORWARDED_FOR</strong><br />»IP de l’usuari “<em>%1$s</em>” comprovada contra la IP de la sessió “<em>%2$s</em>”, cadena del navegador de l’usuari “<em>%3$s</em>” comprovada contra la cadena del navegador de la sessió “<em>%4$s</em>” i cadena X_FORWARDED_FOR de l’usuari “<em>%5$s</em>” comprovada contra la cadena X_FORWARDED_FOR de la sessió “<em>%6$s</em>”.',

	'LOG_JAB_CHANGED'			=> '<strong>Ha canviat el compte Jabber</strong>',
	'LOG_JAB_PASSCHG'			=> '<strong>Ha canviat la contrasenya Jabber</strong>',
	'LOG_JAB_REGISTER'			=> '<strong>Ha registrat un compte Jabber</strong>',
	'LOG_JAB_SETTINGS_CHANGED'	=> '<strong>Ha canviat la configuració del Jabber</strong>',

	'LOG_LANGUAGE_PACK_DELETED'		=> '<strong>Ha eliminat el paquet d’idioma</strong><br />» %s',
	'LOG_LANGUAGE_PACK_INSTALLED'	=> '<strong>Ha instal·lat el paquet d’idioma</strong><br />» %s',
	'LOG_LANGUAGE_PACK_UPDATED'		=> '<strong>Ha actualitzat els detalls del paquet d’idioma</strong><br />» %s',
	'LOG_LANGUAGE_FILE_REPLACED'	=> '<strong>Ha reemplaçat el fitxer d’idioma</strong><br />» %s',
	'LOG_LANGUAGE_FILE_SUBMITTED'	=> '<strong>Ha tramès el fitxer d’idioma i l’ha situat al directori “store”</strong><br />» %s',

	'LOG_MASS_EMAIL'		=> '<strong>Ha enviat un correu electrònic massiu a</strong><br />» %s',

	'LOG_MCP_CHANGE_POSTER'	=> '<strong>Ha canviat l’autor al tema “%1$s”</strong><br />» de %2$s a %3$s',

	'LOG_MODULE_DISABLE'	=> '<strong>Ha inhabilitat el mòdul</strong><br />» %s',
	'LOG_MODULE_ENABLE'		=> '<strong>Ha habilitat el módul</strong><br />» %s',
	'LOG_MODULE_MOVE_DOWN'	=> '<strong>Ha baixat el mòdul</strong><br />» %1$s sota %2$s',
	'LOG_MODULE_MOVE_UP'	=> '<strong>Ha pujat el mòdul</strong><br />» %1$s sobre %2$s',
	'LOG_MODULE_REMOVED'	=> '<strong>Ha eliminat el mòdul</strong><br />» %s',
	'LOG_MODULE_ADD'		=> '<strong>Ha afegit el mòdul</strong><br />» %s',
	'LOG_MODULE_EDIT'		=> '<strong>Ha editat el mòdul</strong><br />» %s',

	'LOG_A_ROLE_ADD'		=> '<strong>Ha afegit el rol d’administrador</strong><br />» %s',
	'LOG_A_ROLE_EDIT'		=> '<strong>Ha editat el rol d’administrador</strong><br />» %s',
	'LOG_A_ROLE_REMOVED'	=> '<strong>Ha eliminat el rol d’administrador</strong><br />» %s',
	'LOG_F_ROLE_ADD'		=> '<strong>Ha afegit el rol de fòrum</strong><br />» %s',
	'LOG_F_ROLE_EDIT'		=> '<strong>Ha editat el rol de fòrum</strong><br />» %s',
	'LOG_F_ROLE_REMOVED'	=> '<strong>Ha eliminat el rol de fòrum</strong><br />» %s',
	'LOG_M_ROLE_ADD'		=> '<strong>Ha afegit el rol de moderador</strong><br />» %s',
	'LOG_M_ROLE_EDIT'		=> '<strong>Ha editat el rol de moderador</strong><br />» %s',
	'LOG_M_ROLE_REMOVED'	=> '<strong>Ha eliminat el rol de moderador</strong><br />» %s',
	'LOG_U_ROLE_ADD'		=> '<strong>Ha afegit el rol d’usuari</strong><br />» %s',
	'LOG_U_ROLE_EDIT'		=> '<strong>Ha editat el rol d’usuari</strong><br />» %s',
	'LOG_U_ROLE_REMOVED'	=> '<strong>Ha eliminat el rol d’usuari</strong><br />» %s',

	'LOG_PLUPLOAD_TIDY_FAILED'		=> '<strong>No s’ha pogut obrir %1$s per netejar-lo, comproveu-ne els permisos.</strong><br />Excepció: %2$s<br />Traça: %3$s',

	'LOG_PROFILE_FIELD_ACTIVATE'	=> '<strong>Ha activat el camp del perfil</strong><br />» %s',
	'LOG_PROFILE_FIELD_CREATE'		=> '<strong>Ha afegit el camp del perfil</strong><br />» %s',
	'LOG_PROFILE_FIELD_DEACTIVATE'	=> '<strong>Ha desactivat el camp del perfil</strong><br />» %s',
	'LOG_PROFILE_FIELD_EDIT'		=> '<strong>Ha canviat el camp del perfil</strong><br />» %s',
	'LOG_PROFILE_FIELD_REMOVED'		=> '<strong>Ha eliminat el camp del perfil</strong><br />» %s',

	'LOG_PRUNE'					=> '<strong>Ha podat els fòrums</strong><br />» %s',
	'LOG_AUTO_PRUNE'			=> '<strong>S’han podat automàticament els fòrums</strong><br />» %s',
	'LOG_PRUNE_SHADOW'			=> '<strong>S’han podat automàticament els temes ombra</strong><br />» %s',
	'LOG_PRUNE_USER_DEAC'		=> '<strong>Ha desactivat els usuaris</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_DEL'	=> '<strong>Ha podat els usuaris eliminant-ne les entrades</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_ANON'	=> '<strong>Ha podat els usuaris mantenint-ne les entrades</strong><br />» %s',

	'LOG_PURGE_CACHE'			=> '<strong>Ha buidat la memòria cau</strong>',
	'LOG_PURGE_SESSIONS'		=> '<strong>Ha eliminat totes les sessions</strong>',

	'LOG_RANK_ADDED'		=> '<strong>Ha afegit el nou rang</strong><br />» %s',
	'LOG_RANK_REMOVED'		=> '<strong>Ha eliminat el rang</strong><br />» %s',
	'LOG_RANK_UPDATED'		=> '<strong>Ha actualitzat el rang</strong><br />» %s',

	'LOG_REASON_ADDED'		=> '<strong>Ha afegit la raó d’informe/rebuig</strong><br />» %s',
	'LOG_REASON_REMOVED'	=> '<strong>Ha eliminat la raó d’informe/rebuig</strong><br />» %s',
	'LOG_REASON_UPDATED'	=> '<strong>Ha actualitzat la raó d’informe/rebuig</strong><br />» %s',

	'LOG_REFERER_INVALID'		=> '<strong>Li ha fallat la validació de la pàgina d’origen</strong><br />»La pàgina d’origen era “<em>%1$s</em>”. S’ha rebutjat la sol·licitud i s’ha matat la sessió.',
	'LOG_RESET_DATE'			=> '<strong>Ha reinicialitzat la data d’inici del fòrum</strong>',
	'LOG_RESET_ONLINE'			=> '<strong>Ha reinicialitzat el nombre màxim d’usuaris connectats</strong>',
	'LOG_RESYNC_FILES_STATS'	=> '<strong>Ha resincronitzat les estadístiques de fitxers</strong>',
	'LOG_RESYNC_POSTCOUNTS'		=> '<strong>Ha resincronitzat els comptadors d’entrades</strong>',
	'LOG_RESYNC_POST_MARKING'	=> '<strong>Ha resincronitzat els temes marcats</strong>',
	'LOG_RESYNC_STATS'			=> '<strong>Ha resincronitzat les estadístiques d’entrades, temes i usuaris</strong>',

	'LOG_SEARCH_INDEX_CREATED'	=> '<strong>Ha creat un índex de cerca per</strong><br />» %s',
	'LOG_SEARCH_INDEX_REMOVED'	=> '<strong>Ha eliminat l’índex de cerca per</strong><br />» %s',
	'LOG_SPHINX_ERROR'			=> '<strong>Error de Sphinx</strong><br />» %s',

	'LOG_SPAMHAUS_OPEN_RESOLVER'		=> 'Spamhaus no permet peticions que utilitzin un sistema de resolució obert. La comprovació de llista negra s’ha inahiblitat. Disposeu de més informació a https://www.spamhaus.com/product/help-for-spamhaus-public-mirror-users/.',
	'LOG_SPAMHAUS_VOLUME_LIMIT'			=> 'Heu excedit el límit de volum de peticions de Spamhaus. La comprovació de llista negra s’ha inahiblitat. Disposeu de més informació a https://www.spamhaus.com/product/help-for-spamhaus-public-mirror-users/.',	

	'LOG_STYLE_ADD'				=> '<strong>Ha afegit el nou estil</strong><br />» %s',
	'LOG_STYLE_DELETE'			=> '<strong>Ha eliminat l’estil</strong><br />» %s',
	'LOG_STYLE_EDIT_DETAILS'	=> '<strong>Ha editat l’estil</strong><br />» %s',
	'LOG_STYLE_EXPORT'			=> '<strong>Ha exportat l’estil</strong><br />» %s',

	// @deprecated 3.1
	'LOG_TEMPLATE_ADD_DB'			=> '<strong>Ha afegit a la base de dades el grup de plantilles</strong><br />» %s',
	// @deprecated 3.1
	'LOG_TEMPLATE_ADD_FS'			=> '<strong>Ha afegit al sistema de fitxers el grup de plantilles</strong><br />» %s',
	'LOG_TEMPLATE_CACHE_CLEARED'	=> '<strong>Ha eliminat les versions emmagatzemades a la memòria cau dels fitxers de plantilla del grup de plantilles <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_DELETE'			=> '<strong>Ha eliminat el grup de plantilles</strong><br />» %s',
	'LOG_TEMPLATE_EDIT'				=> '<strong>Ha editat el grup de plantilles <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_EDIT_DETAILS'		=> '<strong>Ha editat els detalls del grup de plantilles</strong><br />» %s',
	'LOG_TEMPLATE_EXPORT'			=> '<strong>Ha exportat el grup de plantilles</strong><br />» %s',
	// @deprecated 3.1
	'LOG_TEMPLATE_REFRESHED'		=> '<strong>Ha refrescat el grup de plantilles</strong><br />» %s',

	// @deprecated 3.1
	'LOG_THEME_ADD_DB'			=> '<strong>Ha afegit a la base de dades el tema gràfic nou</strong><br />» %s',
	// @deprecated 3.1
	'LOG_THEME_ADD_FS'			=> '<strong>Ha afegit al sistema de fitxers el tema gràfic nou</strong><br />» %s',
	'LOG_THEME_DELETE'			=> '<strong>Ha eliminat el tema gràfic</strong><br />» %s',
	'LOG_THEME_EDIT_DETAILS'	=> '<strong>Ha editat els detalls del tema gràfic</strong><br />» %s',
	'LOG_THEME_EDIT'			=> '<strong>Ha editat el tema gràfic <em>%1$s</em></strong>',
	'LOG_THEME_EDIT_FILE'		=> '<strong>Ha editat el tema gràfic <em>%1$s</em></strong><br />» Ha modificat el fitxer <em>%2$s</em>',
	'LOG_THEME_EXPORT'			=> '<strong>Ha exportat el tema gràfic</strong><br />» %s',
	// @deprecated 3.1
	'LOG_THEME_REFRESHED'		=> '<strong>Ha refrescat el tema gràfic</strong><br />» %s',

	'LOG_UPDATE_DATABASE'	=> '<strong>Ha actualitzat la base de dades de la versió %1$s a la versió %2$s</strong>',
	'LOG_UPDATE_PHPBB'		=> '<strong>Ha actualitzat el phpBB de la versió %1$s a la versió %2$s</strong>',

	'LOG_USER_ACTIVE'		=> '<strong>Ha activat l’usuari</strong><br />» %s',
	'LOG_USER_BAN_USER'		=> '<strong>Ha bandejat l’usuari a través de la gestió d’usuaris</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_USER_BAN_IP'		=> '<strong>Ha bandejat l’adreça IP a través de la gestió d’usuaris</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_USER_BAN_EMAIL'	=> '<strong>Ha bandejat la direcció electrònica a través de la gestió d’usuaris</strong> per la raó “<em>%1$s</em>”<br />» %2$s',
	'LOG_USER_DELETED'		=> '<strong>Ha eliminat l’usuari</strong><br />» %s',
	'LOG_USER_DEL_ATTACH'	=> '<strong>Ha eliminat tots els fitxers adjunts penjats per l’usuari</strong><br />» %s',
	'LOG_USER_DEL_AVATAR'	=> '<strong>Ha eliminat l’avatar de l’usuari</strong><br />» %s',
	'LOG_USER_DEL_OUTBOX'	=> '<strong>Ha buidat la safata de sortida de l’usuari</strong><br />» %s',
	'LOG_USER_DEL_POSTS'	=> '<strong>Ha eliminat totes les entrades fetes per l’usuari</strong><br />» %s',
	'LOG_USER_DEL_SIG'		=> '<strong>Ha eliminat la signatura de l’usuari</strong><br />» %s',
	'LOG_USER_INACTIVE'		=> '<strong>Ha desactivat l’usuari</strong><br />» %s',
	'LOG_USER_MOVE_POSTS'	=> '<strong>Ha desplaçat les entrades de l’usuari</strong><br />» entrades de “%1$s” al fòrum “%2$s”',
	'LOG_USER_NEW_PASSWORD'	=> '<strong>Ha canviat la contrasenya de l’usuari</strong><br />» %s',
	'LOG_USER_REACTIVATE'	=> '<strong>Ha forçat la reactivació del compte de l’usuari</strong><br />» %s',
	'LOG_USER_REMOVED_NR'	=> '<strong>Ha eliminat l’indicador de “nou usuari registrat” de l’usuari</strong><br />» %s',

	'LOG_USER_UPDATE_EMAIL'	=> '<strong>L’usuari “%1$s” ha canviat la seva direcció electrònica</strong><br />» de “%2$s” a “%3$s”',
	'LOG_USER_UPDATE_NAME'	=> '<strong>Ha canviat el nom d’usuari</strong><br />» de “%1$s” a “%2$s”',
	'LOG_USER_USER_UPDATE'	=> '<strong>Ha actualitzat els detalls de l’usuari</strong><br />» %s',

	'LOG_USER_ACTIVE_USER'		=> '<strong>Ha activat el compte d’usuari</strong>',
	'LOG_USER_DEL_AVATAR_USER'	=> '<strong>Ha eliminat l’avatar de l’usuari</strong>',
	'LOG_USER_DEL_SIG_USER'		=> '<strong>Ha eliminat la signatura de l’usuari</strong>',
	'LOG_USER_FEEDBACK'			=> '<strong>Ha afegit un comentari de l’usuari</strong><br />» %s',
	'LOG_USER_GENERAL'			=> '<strong>Ha afegit una entrada:</strong><br />» %s',
	'LOG_USER_INACTIVE_USER'	=> '<strong>Ha desactivat el compte d’usuari</strong>',
	'LOG_USER_LOCK'				=> '<strong>Ha bloquejat un tema propi</strong><br />» %s',
	'LOG_USER_MOVE_POSTS_USER'	=> '<strong>Ha desplaçat totes les entrades al fòrum</strong>» %s',
	'LOG_USER_REACTIVATE_USER'	=> '<strong>Ha forçat la reactivació del compte d’usuari</strong>',
	'LOG_USER_UNLOCK'			=> '<strong>Ha desbloquejat un tema propi</strong><br />» %s',
	'LOG_USER_WARNING'			=> '<strong>Ha afegit un advertiment a l’usuari</strong><br />» %s',
	'LOG_USER_WARNING_BODY'		=> '<strong>Se li ha fet l’advertiment següent a aquest usuari</strong><br />» %s',

	'LOG_USER_GROUP_CHANGE'			=> '<strong>L’usuari ha canviat el grup per defecte</strong><br />» %s',
	'LOG_USER_GROUP_DEMOTE'			=> '<strong>L’usuari ha deixat de ser líder del grup d’usuaris</strong><br />» %s',
	'LOG_USER_GROUP_JOIN'			=> '<strong>L’usuari s’ha afiliat al grup</strong><br />» %s',
	'LOG_USER_GROUP_JOIN_PENDING'	=> '<strong>L’usuari s’ha afiliat al grup i necessita aprovació</strong><br />» %s',
	'LOG_USER_GROUP_RESIGN'			=> '<strong>L’usuari ha abandonat el grup</strong><br />» %s',

	'LOG_WARNING_DELETED'		=> '<strong>Ha eliminat un advertiment de l’usuari</strong><br />» %s',
	'LOG_WARNINGS_DELETED'		=> array(
		1 => '<strong>Ha eliminat un advertiment de l’usuari</strong><br />» %1$s',
		2 => '<strong>Ha eliminat %2$d advertiments de l’usuari</strong><br />» %1$s', // Example: '<strong>Deleted 2 user warnings</strong><br />» username'
	),
	'LOG_WARNINGS_DELETED_ALL'	=> '<strong>Ha eliminat tots els advertiments de l’usuari</strong><br />» %s',

	'LOG_WORD_ADD'			=> '<strong>Ha afegit la paraula censurada</strong><br />» %s',
	'LOG_WORD_DELETE'		=> '<strong>Ha eliminat la paraula censurada</strong><br />» %s',
	'LOG_WORD_EDIT'			=> '<strong>Ha editat la paraula censurada</strong><br />» %s',

	'LOG_EXT_ENABLE'	=> '<strong>Ha habilitat l’extensió</strong><br />» %s',
	'LOG_EXT_DISABLE'	=> '<strong>Ha deshabilitat l’extensió</strong><br />» %s',
	'LOG_EXT_PURGE'		=> '<strong>Ha eliminat les dades de l’extensió</strong><br />» %s',
	'LOG_EXT_UPDATE'	=> '<strong>Extensió actualitzada</strong><br />» %s',
));
