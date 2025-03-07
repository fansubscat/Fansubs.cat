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

// Board Settings
$lang = array_merge($lang, array(
	'ACP_BOARD_SETTINGS_EXPLAIN'	=> 'Aquí podeu determinar l’operació bàsica del vostre fòrum, donar-li un nom i una descripció adequats i, entre altres configuracions, ajustar els valors per defecte per al fus horari i l’idioma.',
	'BOARD_INDEX_TEXT'				=> 'Text de l’índex del fòrum',
	'BOARD_INDEX_TEXT_EXPLAIN'		=> 'Aquest text es mostra com a l’índex del fòrum a la ruta de navegació del fòrum. Si no l’especifiqueu, el valor per defecte és “Índex del fòrum”.',
	'BOARD_STYLE'					=> 'Estil del fòrum',
	'CUSTOM_DATEFORMAT'				=> 'Personalizat…',
	'DEFAULT_DATE_FORMAT'			=> 'Format de data',
	'DEFAULT_DATE_FORMAT_EXPLAIN'	=> 'La sintaxi utilitza el mateix format que les <a href="https://www.php.net/manual/datetime.format.php">funcions de data</a> del PHP.',
	'DEFAULT_LANGUAGE'				=> 'Idioma per defecte',
	'DEFAULT_STYLE'					=> 'Estil per defecte',
	'DEFAULT_STYLE_EXPLAIN'			=> 'L’estil per defecte per a usuaris nous.',
	'DISABLE_BOARD'					=> 'Inhabilita el fòrum',
	'DISABLE_BOARD_EXPLAIN'			=> 'Això fa que el fòrum no estigui disponible per als usuaris que no siguin moderadors o administradors. També podeu introduir un missatge curt (255 caràcters) per que es mostri.',
	'DISPLAY_LAST_SUBJECT'			=> 'Mostra l’assumpte de la darrera entrada afegida a la llista dels fòrums',
	'DISPLAY_LAST_SUBJECT_EXPLAIN'	=> 'L’assumpte de la darrera entrada afegida es mostrarà a la llista de fòrums amb un enllaç a aquesta entrada. No es mostraran assumptes de fòrums protegits amb contrasenya o fòrums per als quals l’usuari no té permís de lectura.',
	'DISPLAY_UNAPPROVED_POSTS'		=> 'Mostra entrades pendents d’aprovar a l’autor',
	'DISPLAY_UNAPPROVED_POSTS_EXPLAIN'	=> 'L’autor pot veure les seves entrades pendents d’aprovar. No aplica a entrades d’usuaris visitants.',
	'GUEST_STYLE'					=> 'Estil per visitants',
	'GUEST_STYLE_EXPLAIN'			=> 'L’estil del fòrum per als usuaris visitants.',
	'OVERRIDE_STYLE'				=> 'Sobreescriu l’estil dels usuaris',
	'OVERRIDE_STYLE_EXPLAIN'		=> 'Reemplaça l’estil dels usuaris (i dels visitants) amb l’estil definit a "Estil per defecte".',
	'SITE_DESC'						=> 'Descripció del lloc web',
	'SITE_HOME_TEXT'				=> 'Text per la pàgina inicial',
	'SITE_HOME_TEXT_EXPLAIN'		=> 'Aquest text es mostrarà com un enllaç a la pàgina inicial del vostre lloc web a la ruta de navegació del fòrum. Si no l’especifiqueu, el valor per defecte és “Pàgina inicial”.',
	'SITE_HOME_URL'					=> 'URL de la pàgina inicial',
	'SITE_HOME_URL_EXPLAIN'			=> 'Si l’especifiqueu, es prefixarà un enllaç a aquest URL a la ruta de navegació del fòrum i el logo del fòrum enllaçarà a aquest URL en lloc de l’índex del fòrum. És necessari un URL absolut, p.ex. <samp>http://www.phpbb.com</samp>.',
	'SITE_NAME'						=> 'Nom del lloc web',
	'SYSTEM_TIMEZONE'				=> 'Fus horari dels visitants',
	'SYSTEM_TIMEZONE_EXPLAIN'		=> 'Fus horari utilitzat per mostrar hores als usuaris que no han iniciat sessió (visitants, robots). Els usuaris que han iniciat sessió decideixen el seu fus horari durant el procediment de registre i poden canviar-lo al Tauler de control de l’usuari.',
	'WARNINGS_EXPIRE'				=> 'Durada dels advertiments',
	'WARNINGS_EXPIRE_EXPLAIN'		=> 'Nombre de dies que han de passar abans que un advertiment venci automàticament del registre d’un usuari. Utilitzeu el valor 0 per fer que els advertiments siguin permanents.',
));

// Board Features
$lang = array_merge($lang, array(
	'ACP_BOARD_FEATURES_EXPLAIN'	=> 'Aquí podeu habilitar/inhabilitar diverses característiques del fòrum.',

	'ALLOW_ATTACHMENTS'			=> 'Permet els fitxers adjunts',
	'ALLOW_BIRTHDAYS'			=> 'Permet els aniversaris',
	'ALLOW_BIRTHDAYS_EXPLAIN'	=> 'Permet introduir la data de naixement i que es mostri l’edat als perfils. Tingueu en compte que la llista d’aniversaris a l’índex del fòrum la controla una configuració de càrrega diferent.',
	'ALLOW_BOOKMARKS'			=> 'Permet afegir temes a les adreces d’interès',
	'ALLOW_BOOKMARKS_EXPLAIN'	=> 'L’usuari pot emmagatzemar adreces d’interès personals.',
	'ALLOW_BBCODE'				=> 'Permet el BBCode',
	'ALLOW_FORUM_NOTIFY'		=> 'Permet la subscripció a fòrums',
	'ALLOW_NAME_CHANGE'			=> 'Permet el canvi de nom d’usuari',
	'ALLOW_NO_CENSORS'			=> 'Permet la inhabilitació de la censura de paraules',
	'ALLOW_NO_CENSORS_EXPLAIN'	=> 'Els usuaris poden triar si inhabiliten la censura automàtica de paraules a les entrades i els missatges privats.',
	'ALLOW_PM_ATTACHMENTS'		=> 'Permet els fitxers adjunts als missatges privats',
	'ALLOW_PM_REPORT'			=> 'Permet que els usuaris informin dels missatges privats',
	'ALLOW_PM_REPORT_EXPLAIN'	=> 'Si habiliteu aquesta opció, els usuaris podran informar als moderadors del fòrum sobre un missatge privat que hagin rebut o enviat. Llavors, aquests missatges privats seran visibles al Tauler de control del moderador.',
	'ALLOW_QUICK_REPLY'			=> 'Permet la resposta ràpida',
	'ALLOW_QUICK_REPLY_EXPLAIN'	=> 'Aquesta opció permet inhabilitar la resposta ràpida de forma global a tots els fòrums. Si l’habiliteu, s’usarà la configuració específica de cada fòrum individualment per determinar si es mostra o no la resposta ràpida.',
	'ALLOW_QUICK_REPLY_BUTTON'	=> 'Tramet i habilita la resposta ràpida a tots els fòrums',
	'ALLOW_SIG'					=> 'Permet les signatures',
	'ALLOW_SIG_BBCODE'			=> 'Permet el BBCode a les signatures dels usuaris',
	'ALLOW_SIG_FLASH'			=> 'Permet l’ús de l’etiqueta <code>[FLASH]</code> del BBCode a les signatures dels usuaris',
	'ALLOW_SIG_IMG'				=> 'Permet l’ús de l’etiqueta <code>[IMG]</code> del BBCode a les signatures dels usuaris',
	'ALLOW_SIG_LINKS'			=> 'Permet l’ús d’enllaços a les signatures dels usuaris',
	'ALLOW_SIG_LINKS_EXPLAIN'	=> 'Si no està permès, l’etiqueta <code>[URL]</code> del BBCode i els URL automàtics/màgics estan inhabilitats.',
	'ALLOW_SIG_SMILIES'			=> 'Permet l’ús d’emoticones a les signatures dels usuaris',
	'ALLOW_SMILIES'				=> 'Permet les emoticones',
	'ALLOW_TOPIC_NOTIFY'		=> 'Permet la subscripció a temes',
	'BOARD_PM'					=> 'Missatgeria privada',
	'BOARD_PM_EXPLAIN'			=> 'Habilita la missatgeria privada per a tots els usuaris.',
	'ALLOW_BOARD_NOTIFICATIONS' => 'Permet notificacions del fòrum',
));

// Avatar Settings
$lang = array_merge($lang, array(
	'ACP_AVATAR_SETTINGS_EXPLAIN'	=> 'Els avatars són imatges generalment petites i úniques que un usuari pot associar amb si mateix. Depenent de l’estil se solen mostrar sota el nom de l’usuari en la visualització de temes. Aquí podeu determinar de quina manera els usuaris poden definir els seus avatars. Tingueu en compte que per penjar avatars cal que creeu el directori que definiu a sota i que us assegureu que el servidor web hi pot escriure. També tingueu en compte que els límits per a la mida del fitxer d’avatar només s’imposen als avatars penjats, no s’apliquen a les imatges enllaçades remotament.',

	'ALLOW_AVATARS'					=> 'Habilita els avatars',
	'ALLOW_AVATARS_EXPLAIN'			=> 'Permet l’ús d’avatars en general;<br>Si inhabiliteu els avatars en general o en qualsevol dels diferents modes, els avatars inhabilitats ja no es mostraran als fòrums, però els usuaris encara podran baixar-se els seus propis avatars des del Tauler de control de l’usuari.',
	'ALLOW_GRAVATAR'				=> 'Habilita els avatars gravatar',
	'ALLOW_LOCAL'					=> 'Habilita la galeria d’avatars',
	'ALLOW_REMOTE'					=> 'Habilita els avatars remots',
	'ALLOW_REMOTE_EXPLAIN'			=> 'Avatars enllaçats des d’un altre lloc web.<br><em><strong class="error">Advertència:</strong> Si habiliteu aquesta funció, és possible que permeteu als usuaris verificar l’existència de fitxers i serveis que només són accessibles a la xarxa local.</em>',
	'ALLOW_REMOTE_UPLOAD'			=> 'Habilita la penjada d’avatars remots',
	'ALLOW_REMOTE_UPLOAD_EXPLAIN'	=> 'Permet penjar avatars des d’un altre lloc web.<br><em><strong class="error">Advertència:</strong> Si habiliteu aquesta funció, és possible que permeteu als usuaris verificar l’existència de fitxers i serveis que només són accessibles a la xarxa local.</em>',
	'ALLOW_UPLOAD'					=> 'Habilita la penjada d’avatars',
	'AVATAR_GALLERY_PATH'			=> 'Camí a la galeria d’avatars',
	'AVATAR_GALLERY_PATH_EXPLAIN'	=> 'Camí a partir del directori arrel del phpBB per a imatges predefinides, p.ex. <samp>images/avatars/gallery</samp>.<br>Els punts dobles com ara <samp>../</samp> s’eliminaran del camí per raons de seguretat.',
	'AVATAR_STORAGE_PATH'			=> 'Camí a l’emmagatzemament d’avatars',
	'AVATAR_STORAGE_PATH_EXPLAIN'	=> 'Camí a partir del directori arrel del phpBB, p.ex. <samp>images/avatars/upload</samp>.<br>La penjada d’avatars <strong>no estarà disponible</strong> si no es pot escriure en aquest camí.<br>Els punts dobles com ara <samp>../</samp> s’eliminaran del camí per raons de seguretat.',
	'MAX_AVATAR_SIZE'				=> 'Dimensions màximes dels avatars',
	'MAX_AVATAR_SIZE_EXPLAIN'		=> 'Amplària x Alçària en píxels.',
	'MAX_FILESIZE'					=> 'Mida màxima dels fitxers d’avatar',
	'MAX_FILESIZE_EXPLAIN'			=> 'Per a fitxers d’avatar penjats. Si el valor és 0, la mida del fitxer a penjar només està limitada per la configuració del PHP.',
	'MIN_AVATAR_SIZE'				=> 'Dimensions mínimes dels avatars',
	'MIN_AVATAR_SIZE_EXPLAIN'		=> 'Amplària x Alçària en píxels.',
));

// Message Settings
$lang = array_merge($lang, array(
	'ACP_MESSAGE_SETTINGS_EXPLAIN'		=> 'Aquí podeu definir la configuració per defecte per a la missatgeria privada.',

	'ALLOW_BBCODE_PM'			=> 'Permet el BBCode als missatges privats',
	'ALLOW_FLASH_PM'			=> 'Permet l’ús de l’etiqueta <code>[FLASH]</code> del BBCode',
	'ALLOW_FLASH_PM_EXPLAIN'	=> 'Tingueu en compte que la possibilitat d’usar flash als missatges privats, si està habilitada aquí, també depèn dels permisos.',
	'ALLOW_FORWARD_PM'			=> 'Permet reenviar missatges privats',
	'ALLOW_IMG_PM'				=> 'Permet l’ús de l’etiqueta <code>[IMG]</code> del BBCode',
	'ALLOW_MASS_PM'				=> 'Permet enviar missatges privats a usuaris múltiples i grups',
	'ALLOW_MASS_PM_EXPLAIN'		=> 'L’enviament a grups es pot ajustar per cada grup a la pàgina de configuració del grup.',
	'ALLOW_PRINT_PM'			=> 'Permet la vista d’impressió a la missatgeria privada',
	'ALLOW_QUOTE_PM'			=> 'Permet citar text als missatges privats',
	'ALLOW_SIG_PM'				=> 'Permet les signatures als missatges privats',
	'ALLOW_SMILIES_PM'			=> 'Permet les emoticones als missatges privats',
	'BOXES_LIMIT'				=> 'Nombre màxim de missatges privats per carpeta',
	'BOXES_LIMIT_EXPLAIN'		=> 'Els usuaris no poden rebre més d’aquest nombre de missatges en cadascuna de les seves carpetes de missatges privats. Introduïu un 0 per permetre un nombre il·limitat de missatges.',
	'BOXES_MAX'					=> 'Nombre màxim de carpetes de missatges privats',
	'BOXES_MAX_EXPLAIN'			=> 'Per defecte els usuaris poden crear aquest nombre de carpetes per a missatges privats.',
	'ENABLE_PM_ICONS'			=> 'Permet l’us d’icones de tema als missatges privats',
	'FULL_FOLDER_ACTION'		=> 'Acció per defecte per a les carpetes plenes',
	'FULL_FOLDER_ACTION_EXPLAIN'=> 'Acció per defecte que es duu a terme quan una carpeta d’un usuari està plena assumint que l’acció de l’usuari per a la carpeta, estigui o no definida, no és aplicable. L’única excepció és la carpeta “Missatges enviats” on l’acció per defecte és sempre eliminar els misatges més antics.',
	'HOLD_NEW_MESSAGES'			=> 'Retenir els missatges nous',
	'PM_EDIT_TIME'				=> 'Limita el temps d’edició',
	'PM_EDIT_TIME_EXPLAIN'		=> 'Limita el temps disponible per editar un missatge privat que encara bo s’hagi entregat. Introduir un 0 inhabilita aquesta característica.',
	'PM_MAX_RECIPIENTS'			=> 'Nombre màxim de destinataris permès',
	'PM_MAX_RECIPIENTS_EXPLAIN'	=> 'El nombre màxim de detinataris permesos en un missatge privat. Si introduïu un 0, es permet un nombre il·limitat. Podeu configurar aquest valor per cada grup a la pàgina de configuració del grup.',
));

// Post Settings
$lang = array_merge($lang, array(
	'ACP_POST_SETTINGS_EXPLAIN'			=> 'Aquí podeu definir totes les configuracions per defecte per a la publicació d’entrades.',
	'ALLOW_POST_LINKS'					=> 'Permet els enllaços a les entrades/missatges privats',
	'ALLOW_POST_LINKS_EXPLAIN'			=> 'Si no està permès, l’etiqueta <code>[URL]</code> del BBCode i els URL automàtics/màgics estan inhabilitats.',
	'ALLOWED_SCHEMES_LINKS'				=> 'Esqumes permeses als enllaços',
	'ALLOWED_SCHEMES_LINKS_EXPLAIN'		=> 'Els usuaris només poden publicar URL sense esquema o un dels esquemes permesos de la llista separada per comes.',
	'ALLOW_POST_FLASH'					=> 'Permet l’ús de l’etiqueta <code>[FLASH]</code> del BBCode a les entrades',
	'ALLOW_POST_FLASH_EXPLAIN'			=> 'Si no està permès, l’etiqueta <code>[FLASH]</code> del BBCode està inhabilitada a les entrades. Altrament, el sistema de permisos controla quins usuaris poden usar l’etiqueta <code>[FLASH]</code> del BBCode.',

	'BUMP_INTERVAL'					=> 'Interval de reactivació',
	'BUMP_INTERVAL_EXPLAIN'			=> 'Nombre de minuts, hores o dies que han de passar des de la darrera entrada en un tema per poder reactivar-lo. Introduir un 0 inhabilita la reactivació.',
	'CHAR_LIMIT'					=> 'Nombre màxim de caràcters per entrada/missatge',
	'CHAR_LIMIT_EXPLAIN'			=> 'El nombre màxim de caràcters permesos en una entrada. Introduïu un 0 per a un nombre de caràcters il·limitat.',
	'DELETE_TIME'					=> 'Limita el temps per eliminar entrades',
	'DELETE_TIME_EXPLAIN'			=> 'Limita el temps disponible per eliminar una entrada nova. Introduïu un 0 per inhabilitar aquest comportament.',
	'DISPLAY_LAST_EDITED'			=> 'Mostra la informació de l’hora de la darrera edició',
	'DISPLAY_LAST_EDITED_EXPLAIN'	=> 'Tria si la informació sobre la darrera edició es mostra a les entrades.',
	'EDIT_TIME'						=> 'Limita el temps d’edició',
	'EDIT_TIME_EXPLAIN'				=> 'Limita el temps disponible per editar una entrada nova. Introduir un 0 inhabilita aquesta característica.',
	'FLOOD_INTERVAL'				=> 'Interval d’inundació',
	'FLOOD_INTERVAL_EXPLAIN'		=> 'Nombre de segons que ha d’esperar un usuari entre la publicació de dues entrades. Per permetre que els usuaris ho ignorin, modifiqueu els seus permisos.',
	'HOT_THRESHOLD'					=> 'Llindar de tema popular',
	'HOT_THRESHOLD_EXPLAIN'			=> 'Llindar d’entrades per tema necessàries per que un tema es consideri popular. Utilitzeu un 0 per inhabilitar els temes populars.',
	'MAX_POLL_OPTIONS'				=> 'Nombre màxim d’opcions en una enquesta',
	'MAX_POST_FONT_SIZE'			=> 'Mida màxima de la lletra a les entrades',
	'MAX_POST_FONT_SIZE_EXPLAIN'	=> 'Mida màxima de la lletra permesa en una entrada. Introduïu un 0 per a una mida il·limitada.',
	'MAX_POST_IMG_HEIGHT'			=> 'Alçària màxima de flash a les entrades',
	'MAX_POST_IMG_HEIGHT_EXPLAIN'	=> 'Alçària màxima d’un fitxer flash en una entrada. Introduïu un 0 per a una alçària il·limitada.',
	'MAX_POST_IMG_WIDTH'			=> 'Amplària màxima de flash a les entrades',
	'MAX_POST_IMG_WIDTH_EXPLAIN'	=> 'Amplària màxima d’un fitxer flash en una entrada. Introduïu un 0 per a una amplària il·limitada.',
	'MAX_POST_URLS'					=> 'Nombre màxim d’enllaços per entrada',
	'MAX_POST_URLS_EXPLAIN'			=> 'Nombre màxim d’URL en una entrada. Introduïu un 0 per a enllaços il·limitats.',
	'MIN_CHAR_LIMIT'				=> 'Nombre mínim de caràcters per entrada/missatge',
	'MIN_CHAR_LIMIT_EXPLAIN'		=> 'El nombre mínim de caràcters que cal que un usuari introdueixi al text d’una entrada o missatge privat. El valor mínim per aquesta configuració es 1.',
	'POSTING'						=> 'Publicació',
	'POSTS_PER_PAGE'				=> 'Entrades per pàgina',
	'QUOTE_DEPTH_LIMIT'				=> 'Profunditat màxima d’incrustació per citacions',
	'QUOTE_DEPTH_LIMIT_EXPLAIN'		=> 'Profunditat màxima de citacions incrustades en una entrada. Introduïu un 0 per a una profunditat il·limitada.',
	'SMILIES_LIMIT'					=> 'Nombre màxim d’emoticones per entrada',
	'SMILIES_LIMIT_EXPLAIN'			=> 'Nombre màxim d’emoticones en una entrada. Introduïu un 0 per a emoticones il·limitades.',
	'SMILIES_PER_PAGE'				=> 'Emoticones per pàgina',
	'TOPICS_PER_PAGE'				=> 'Temes per pàgina',
));

// Signature Settings
$lang = array_merge($lang, array(
	'ACP_SIGNATURE_SETTINGS_EXPLAIN'	=> 'Aquí podeu definir totes les configuracions per defecte per a les signatures.',

	'MAX_SIG_FONT_SIZE'				=> 'Mida màxima de la lletra a les signatures',
	'MAX_SIG_FONT_SIZE_EXPLAIN'		=> 'Mida màxima de la lletra permesa en les signatures dels usuaris. Introduïu un 0 per a una mida il·limitada.',
	'MAX_SIG_IMG_HEIGHT'			=> 'Alçària màxima de les imatges a les signatures',
	'MAX_SIG_IMG_HEIGHT_EXPLAIN'	=> 'Alçària màxima d’una imatge/fitxer flash en les signatures dels usuaris. Introduïu un 0 per a una alçària il·limitada.',
	'MAX_SIG_IMG_WIDTH'				=> 'Amplària màxima de les imatges a les signatures',
	'MAX_SIG_IMG_WIDTH_EXPLAIN'		=> 'Amplària màxima d’una imatge/fitxer flash en les signatures dels usuaris. Introduïu un 0 per a una amplària il·limitada.',
	'MAX_SIG_LENGTH'				=> 'Longitud màxima de les signatures',
	'MAX_SIG_LENGTH_EXPLAIN'		=> 'Nombre màxim de caràcters en les signatures dels usuaris.',
	'MAX_SIG_SMILIES'				=> 'Nombre màxim d’emoticones per signatura',
	'MAX_SIG_SMILIES_EXPLAIN'		=> 'Nombre màxim d’emoticones en les signatures dels usuaris. Introduïu un 0 per a emoticones il·limitades.',
	'MAX_SIG_URLS'					=> 'Nombre màxim d’enllaços a les signatures',
	'MAX_SIG_URLS_EXPLAIN'			=> 'Nombre màxim d’enllaços en les signatures dels usuaris. Introduïu un 0 per a enllaços il·limitats.',
));

// Registration Settings
$lang = array_merge($lang, array(
	'ACP_REGISTER_SETTINGS_EXPLAIN'		=> 'Aquí podeu definir la configuració relacionada amb el procediment de registre i el perfil.',

	'ACC_ACTIVATION'				=> 'Activació de comptes',
	'ACC_ACTIVATION_EXPLAIN'		=> 'Això determina si els usuaris tenen accés immediat al fòrum o si cal una confirmació. També podeu inhabilitar completament els nous registres. <em>Cal que la característica d’enviament de correus electrònics del fòrum estigui habilitada per tal d’usar l’activació per usuaris o administradors.</em>',
	'ACC_ACTIVATION_WARNING'		=> 'Tingueu en compte que el mètode d’activació seleccionat actualment requereix que l’enviament de correus electrònics estigui habilitat; si no és així, el registre d’usuaris estarà inhabilitat. És recomanable que seleccioneu un altre mètode d’activació o torneu a activar l’enviament de correus electrònics.',
	'NEW_MEMBER_POST_LIMIT'			=> 'Límit d’entrades per usuaris nous',
	'NEW_MEMBER_POST_LIMIT_EXPLAIN'	=> 'Els usuaris nous són al grup <em>Nous usuaris registrats</em> fins que arriben a aquest nombre d’entrades. Podeu usar aquest grup per evitar que utilitzin el sistema de missatgeria privada o per revisar les seves entrades. <strong>Introduïu un 0 per inhabilitar aquesta funció.</strong>',
	'NEW_MEMBER_GROUP_DEFAULT'		=> 'Assigna el grup Nous usuaris registrats com a grup per defecte',
	'NEW_MEMBER_GROUP_DEFAULT_EXPLAIN'	=> 'Si habiliteu aquesta opció i especifiqueu el límit d’entrades per usuaris nous, no només s’assignara els nous usuaris al grup <em>Nous ususaris registrats</em> sinó que aquest grup també serà el seu grup per defecte. Això pot ser útil si voleu assignar el rang o l’avatar del grup per defecte que l’usuari heretarà.',

	'ACC_ADMIN'					=> 'Per l’administrador',
	'ACC_DISABLE'				=> 'Inhabilita el registre d’usuaris',
	'ACC_NONE'					=> 'Sense activació (accés immediat)',
	'ACC_USER'					=> 'Per l’usuari (verificació per correu electrònic)',
//	'ACC_USER_ADMIN'			=> 'User + Admin',
	'ALLOW_EMAIL_REUSE'			=> 'Permet la reutilització d’adreces electròniques',
	'ALLOW_EMAIL_REUSE_EXPLAIN'	=> 'Usuaris diferents poden registrar-se amb la mateixa adreça electrònica.',
	'COPPA'						=> 'COPPA',
	'COPPA_FAX'					=> 'Número de fax COPPA',
	'COPPA_MAIL'				=> 'Adreça de correu COPPA',
	'COPPA_MAIL_EXPLAIN'		=> 'Aquesta és l’adreça de correu on els pares poden enviar els formularis de registre COPPA.',
	'ENABLE_COPPA'				=> 'Habilita COPPA',
	'ENABLE_COPPA_EXPLAIN'		=> 'Això requereix als usuaris que declarin si tenen 13 anys o més per complir amb la normativa COPPA dels EUA. Si ho inhabiliteu, els grups específics COPPA ja no es mostraran.',
	'MAX_CHARS'					=> 'Màxim',
	'MIN_CHARS'					=> 'Mínim',
	'NO_AUTH_PLUGIN'			=> 'No s’ha trobat un connector auth adequat.',
	'PASSWORD_LENGTH'			=> 'Longitud de la contrasenya',
	'PASSWORD_LENGTH_EXPLAIN'	=> 'Nombre mínim de caràcters per a les contrasenyes. Tingueu en compte que el nombre màxim de caràcters no està limitat.',
	'REG_LIMIT'					=> 'Intents de registre',
	'REG_LIMIT_EXPLAIN'			=> 'Nombre d’intents que pot fer un usuari per resoldre la tasca contra robots de brossa abans que se li bloquegi la sessió.',
	'USERNAME_ALPHA_ONLY'		=> 'Només alfanumèrics',
	'USERNAME_ALPHA_SPACERS'	=> 'Alfanumèrics i d’espaiat',
	'USERNAME_ASCII'			=> 'ASCII (sense unicode internacional)',
	'USERNAME_LETTER_NUM'		=> 'Qualsevol lletra o número',
	'USERNAME_LETTER_NUM_SPACERS'	=> 'Qualsevol lletra, número o caràcter d’espaiat',
	'USERNAME_CHARS'			=> 'Limita els caràcters als noms d’usuari',
	'USERNAME_CHARS_ANY'		=> 'Qualsevol caràcter',
	'USERNAME_CHARS_EXPLAIN'	=> 'Restringeix el tipus de caràcters que poden ser utilitzats als noms d’usuari, els caràcters d’espaiat són: l’espai, -, +, _, [ i ].',
	'USERNAME_LENGTH'			=> 'Longitud del nom d’usuari',
	'USERNAME_LENGTH_EXPLAIN'	=> 'Nombre mínim i màxim de caràcters per als noms d’usuari.',
));

// Feeds
$lang = array_merge($lang, array(
	'ACP_FEED_MANAGEMENT'				=> 'Configuració general dels canals d’informació',
	'ACP_FEED_MANAGEMENT_EXPLAIN'		=> 'Aquest mòdul fa disponibles diversos canals ATOM i analitza el BBCode de les entrades per fer que siguin llegibles a canals externs.',

	'ACP_FEED_GENERAL'					=> 'Configuració general de canals',
	'ACP_FEED_POST_BASED'				=> 'Configuració de canals per entrades',
	'ACP_FEED_TOPIC_BASED'				=> 'Configuració de canals per temes',
	'ACP_FEED_SETTINGS_OTHER'			=> 'Altres canals i configuracions',

	'ACP_FEED_ENABLE'					=> 'Habilita els canals d’informació',
	'ACP_FEED_ENABLE_EXPLAIN'			=> 'Habilita o inhabilita els canals d’informació ATOM a tots els fòrums.<br>Inhabilitar aquesta opció desactiva tots els canals independentment dels valors que hi hagi a les opcions a sota.',
	'ACP_FEED_LIMIT'					=> 'Nombre d’elements',
	'ACP_FEED_LIMIT_EXPLAIN'			=> 'El nombre màxim de canals que es mostren.',

	'ACP_FEED_OVERALL'					=> 'Habilita el canal de tot el fòrum',
	'ACP_FEED_OVERALL_EXPLAIN'			=> 'Entrades noves de tot el fòrum.',
	'ACP_FEED_FORUM'					=> 'Habilita els canals per a fòrums individuals',
	'ACP_FEED_FORUM_EXPLAIN'			=> 'Entrades noves d’un sol fòrum o subfòrum.',
	'ACP_FEED_TOPIC'					=> 'Habilita els canals per a temes individuals',
	'ACP_FEED_TOPIC_EXPLAIN'			=> 'Entrades noves d’un sol tema.',

	'ACP_FEED_TOPICS_NEW'				=> 'Habilita el canal de temes nous',
	'ACP_FEED_TOPICS_NEW_EXPLAIN'		=> 'Habilita el canal “Temes nous” que mostra els darrers temes creats i n’inclou la primera entrada.',
	'ACP_FEED_TOPICS_ACTIVE'			=> 'Habilita el canal de temes actius',
	'ACP_FEED_TOPICS_ACTIVE_EXPLAIN'	=> 'Habilita el canal “Temes actius” que mostra els darrers temes actius i n’inclou la darrera entrada.',
	'ACP_FEED_NEWS'						=> 'Canal de notícies',
	'ACP_FEED_NEWS_EXPLAIN'				=> 'Mostra la primera entrada dels fòrums que seleccioneu. Podeu inhabilitar aquest canal no seleccionant cap fòrum.<br>Podeu seleccionar diversos fòrums si manteniu premut <samp>CTRL</samp> mentre feu clic.',

	'ACP_FEED_OVERALL_FORUMS'			=> 'Habilita el canal dels fòrums',
	'ACP_FEED_OVERALL_FORUMS_EXPLAIN'	=> 'Habilita el canal “Tots els fòrums” que mostra una llista de fòrums.',

	'ACP_FEED_HTTP_AUTH'				=> 'Permet l’autenticació HTTP',
	'ACP_FEED_HTTP_AUTH_EXPLAIN'		=> 'Habilita l’autenticació HTTP que permet als usuaris rebre continguts que estan ocults per als usuaris visitants afegint el paràmetre <samp>auth=http</samp> a l’URL del canal. Tingueu en compte que agunes instal·lacions del PHP requereixen canvis addicionals al fitxer .htaccess en el qual podreu trobar més instruccions.',
	'ACP_FEED_ITEM_STATISTICS'			=> 'Estadístiques dels elements',
	'ACP_FEED_ITEM_STATISTICS_EXPLAIN'	=> 'Mostra estadístiques individuals a sota dels elements del canal<br>(p.ex. autor, data i hora, respostes, visualitzacions)',
	'ACP_FEED_EXCLUDE_ID'				=> 'Exclou aquests fòrums',
	'ACP_FEED_EXCLUDE_ID_EXPLAIN'		=> 'El contingut d’aquests fòrums <strong>no s’inclourà als canals d’informació</strong>. Si no seleccioneu cap fòrum, s’obtindran dades de tots els fòrums.<br>Podeu seleccionar o desseleccionar diversos fòrums si manteniu premut <samp>CTRL</samp> mentre feu clic.',
));

// Visual Confirmation Settings
$lang = array_merge($lang, array(
	'ACP_VC_SETTINGS_EXPLAIN'				=> 'Aquí podeu seleccionar i configurar connectors que estan dissenyats per bloquejar la tramesa automàtica de formularis per part dels robots de brossa (spambots en anglès). Aquests connectors típicament funcionen plantejant a l’usuari un <em>CAPTCHA</em>, una prova que està dissenyada per que sigui dificil que un ordinador la resolgui.',
	'ACP_VC_EXT_GET_MORE'					=> 'Per trobar connectors contra brossa addicionals millors visiteu la <a href="https://www.phpbb.com/go/anti-spam-ext"><strong>base de dades d’extensions de phpBB.com</strong></a>. Per obtenir més informació sobre com prevenir les entrades brossa al vostre fòrum visiteu la <a href="https://www.phpbb.com/go/anti-spam"><strong>base de coneixements de phpBB.com</strong></a>.',
	'AVAILABLE_CAPTCHAS'					=> 'Connectors disponibles',
	'CAPTCHA_UNAVAILABLE'					=> 'No podeu seleccionar aquest connector ja que no es compleixen els seus requeriments.',
	'CAPTCHA_GD'							=> 'Imatge GD',
	'CAPTCHA_GD_3D'							=> 'Imatge GD 3D',
	'CAPTCHA_GD_FOREGROUND_NOISE'			=> 'Soroll en primer pla',
	'CAPTCHA_GD_EXPLAIN'					=> 'Utilitza la llibreria gràfica GD per fer una imatge més avançada contra els robots de brossa.',
	'CAPTCHA_GD_FOREGROUND_NOISE_EXPLAIN'	=> 'Utilitza soroll en primer pla per fer que la imatge sigui més difícil de llegir.',
	'CAPTCHA_GD_X_GRID'						=> 'Soroll de fons segons l’eix x',
	'CAPTCHA_GD_X_GRID_EXPLAIN'				=> 'Utilitzeu valors baixos per fer que la imatge sigui més difícil de llegir. Un 0 inhabilita el soroll de fons segons l’eix x.',
	'CAPTCHA_GD_Y_GRID'						=> 'Soroll de fons segons l’eix y',
	'CAPTCHA_GD_Y_GRID_EXPLAIN'				=> 'Utilitzeu valors baixos per fer que la imatge sigui més difícil de llegir. Un 0 inhabilita el soroll de fons segons l’eix y.',
	'CAPTCHA_GD_WAVE'						=> 'Distorsió d’oneig',
	'CAPTCHA_GD_WAVE_EXPLAIN'				=> 'Aplica una distorsió de tipus oneig a la imatge.',
	'CAPTCHA_GD_3D_NOISE'					=> 'Afegeix objectes amb soroll 3D',
	'CAPTCHA_GD_3D_NOISE_EXPLAIN'			=> 'Afegeix objectes addicionals a la imatge, per sobre de les lletres.',
	'CAPTCHA_GD_FONTS'						=> 'Utilitza fonts diferents',
	'CAPTCHA_GD_FONTS_EXPLAIN'				=> 'Aquesta configuració controla quantes formes diferents de lletres s’utilitzen. Podeu usar les formes per defecte o introduir lletres modificades. També és possible afegir lletres minúscules.',
	'CAPTCHA_FONT_DEFAULT'					=> 'Per defecte',
	'CAPTCHA_FONT_NEW'						=> 'Formes noves',
	'CAPTCHA_FONT_LOWER'					=> 'Utilitza també les minúscules',
	'CAPTCHA_NO_GD'							=> 'Imatge simple',
	'CAPTCHA_PREVIEW_MSG'					=> 'Els canvis que heu fet no s’han desat, això és només una previsualització.',
	'CAPTCHA_PREVIEW_EXPLAIN'				=> 'Aquest és l’aspecte que tindrà el connector si utilitzeu la configuració actual.',

	'CAPTCHA_SELECT'						=> 'Connectors instal·lats',
	'CAPTCHA_SELECT_EXPLAIN'				=> 'El menú desplegable mostra els connectors reconeguts pel fòrum. Les entrades en gris no estan disponibles ara mateix i és possible que s’hagin de configurar abans de poder usar-les.',
	'CAPTCHA_CONFIGURE'						=> 'Configura els connectors',
	'CAPTCHA_CONFIGURE_EXPLAIN'				=> 'Canvia la configuració del connector seleccionat.',
	'CONFIGURE'								=> 'Configura',
	'CAPTCHA_NO_OPTIONS'					=> 'Aquest connector no té opcions de configuració.',

	'VISUAL_CONFIRM_POST'					=> 'Habilita les mesures contra robots de brossa per a les entrades d’usuaris visitants',
	'VISUAL_CONFIRM_POST_EXPLAIN'			=> 'Requereix als usuaris visitants que resolguin una tasca contra els robots de brossa per tal de prevenir la publicació automatitzada d’entrades.',
	'VISUAL_CONFIRM_REG'					=> 'Habilita les mesures contra robots de brossa durant el registre',
	'VISUAL_CONFIRM_REG_EXPLAIN'			=> 'Requereix als usuaris nous que resolguin una tasca contra els robots de brossa per tal de prevenir el registre automatitzat d’usuaris.',
	'VISUAL_CONFIRM_REFRESH'				=> 'Permet als usuaris refrescar la tasca contra robots de brossa',
	'VISUAL_CONFIRM_REFRESH_EXPLAIN'		=> 'Permet als usuaris demanar una nova tasca contra robots de brossa si no poden resoldre la tasca actual durant el registre. És possible que alguns connectors no permetin aquesta opció.',
));

// Cookie Settings
$lang = array_merge($lang, array(
	'ACP_COOKIE_SETTINGS_EXPLAIN'		=> 'Aquests detalls defineixen les dades que s’utilitzen per enviar galetes als navegadors dels usuaris. En la majoria de casos, els valors per defecte seran suficients. Si cal que en canvieu algun, feu-ho amb compte; una configuració incorrecta pot evitar que els usuaris iniciïn la sessió. Si els usuaris tenen problemes per mantenir la sessió oberta al vostre fòrum, visiteu la <strong><a href="https://www.phpbb.com/support/go/cookie-settings">Base de Coneixements de phpBB.com - Solució de configuracions incorrectes de galetes</a></strong> (en anglès).',

	'COOKIE_DOMAIN'				=> 'Domini de la galeta',
	'COOKIE_DOMAIN_EXPLAIN'		=> 'En la majoria de casos el domini de la galeta és opcional. Deixeu-lo en blanc si no n’esteu segurs.<br><br> En el cas que tingueu el fòrum integrat amb altres aplicacions o tingueu múltiples dominis, per determinar el domini de la galeta heu de fer el següent: Si teniu una situació com <i>exemple.cat</i> i <i>forums.exemple.cat</i>, o potser <i>forums.exemple.cat</i> i <i>bloc.exemple.cat</i>, treieu els subdominis fins que tingueu el domini comú, <i>exemple.com</i> i afegiu-hi un punt al davant. En aquest cas caldria introduir .exemple.cat (fixeu-vos amb el punt al principi).',
	'COOKIE_NAME'				=> 'Nom de la galeta',
	'COOKIE_NAME_EXPLAIN'		=> 'Podeu posar-hi el que vulgueu. Sempre que canvieu la configuració de la galeta n’haurieu de canviar el nom.',
	'COOKIE_NOTICE'				=> 'Avís de galetes',
	'COOKIE_NOTICE_EXPLAIN'		=> 'Si l’habiliteu, es mostrarà un avís de galetes als usuaris quan visitin els fòrums. És possible que sigui un requeriment legal depenent del contingut del fòrum o les extensions que tingueu habilitades.',
	'COOKIE_PATH'				=> 'Camí de la galeta',
	'COOKIE_PATH_EXPLAIN'		=> 'Normalment serà el mateix que el camí de l’script o simplement una barra inclinada per fer que la galeta sigui accessible a tot el domini del lloc web.',
	'COOKIE_SECURE'				=> 'Galeta segura',
	'COOKIE_SECURE_EXPLAIN'		=> 'Si el vostre servidor s’executa sobre SSL habiliteu aquesta opció, en qualsevol altre cas deixeu-la inhabilitada. Si l’habiliteu i el servidor no s’executa sobre SSL es produiran errors del servidor durant les redireccions.',
	'ONLINE_LENGTH'				=> 'Interval de temps per a Qui està connectat',
	'ONLINE_LENGTH_EXPLAIN'		=> 'Nombre de minuts després dels quals els usuaris inactius no apareixeran a la llista “Qui està connectat”. Com més alt és aquest valor, més gran és el processament necessari per generar la llista.',
	'SESSION_LENGTH'			=> 'Durada de la sessió',
	'SESSION_LENGTH_EXPLAIN'	=> 'Les sessións venceran després d’aquest temps en segons.',
));

// Contact Settings
$lang = array_merge($lang, array(
	'ACP_CONTACT_SETTINGS_EXPLAIN'		=> 'Aquí podeu habilitar i inhabilitar la pàgina de contacte i també podeu afegir el text que es mostra a la pàgina.',

	'CONTACT_US_ENABLE'				=> 'Habilita la pàgina de contacte',
	'CONTACT_US_ENABLE_EXPLAIN'		=> 'Aquesta pàgina permet als usuaris enviar correus electrònics als admimnistradors del fòrum. Tingueu en compte que cal que l’opció d’enviament de correu electònics del fòrum també estigui habilitada. Podeu trobar aquesta opció a General &gt; Comunicació amb clients &gt; Configuració del correu electrònic.',

	'CONTACT_US_INFO'				=> 'Informació de contacte',
	'CONTACT_US_INFO_EXPLAIN'		=> 'Aquest missatge es mostra a la pàgina de contacte',
	'CONTACT_US_INFO_PREVIEW'		=> 'Pàgina d’informació de contacte - Previsualització',
	'CONTACT_US_INFO_UPDATED'		=> 'S’ha actualitzat la pàgina d’informació de contacte.',
));

// Load Settings
$lang = array_merge($lang, array(
	'ACP_LOAD_SETTINGS_EXPLAIN'	=> 'Aquí podeu habilitar i inhabilitar determinades funcions del fòrum per reduir la quantitat necessària de processament. En la majoria de servidors no cal inhabilitar cap funció. Això no obstant, en determinats sistemes o en entorns d’allotjament compartit pot ser beneficiós inhabilitar capacitats que no necessiteu realment. També podeu especificar límits per a la càrrega del sistema i sessions actives més enllà de les quals el fòrum es quedarà fora de línia.',

	'ALLOW_CDN'						=> 'Permet la utilització de xarxes de distribució de continguts externes',
	'ALLOW_CDN_EXPLAIN'				=> 'Si habiliteu aquesta configuració, alguns fitxers se serviran des de servidors externs en lloc del vostre servidor. Això redueix l’ample de banda utilitzat pel vostre servidor, però pot ser un problema de privacitat en alguns països. En una instal·lació per defecte del phpBB això inclou carregar “jQuery” i la font “Open Sans” des de la xarxa de distribució de continguts de Google. Això també aplica a la font “Font Awesome”, que el phpBB i algunes extensions utilitzen per dibuixar icones.',
	'ALLOW_LIVE_SEARCHES'			=> 'Permet les cerques actives',
	'ALLOW_LIVE_SEARCHES_EXPLAIN'	=> 'Si habiliteu aquesta configuració, es suggereix als usuaris paraules clau mentre escriuen en determinats camps de text del fòrum.',
	'CUSTOM_PROFILE_FIELDS'			=> 'Camps personalitzats del perfil',
	'LIMIT_LOAD'					=> 'Limita la càrrega del sistema',
	'LIMIT_LOAD_EXPLAIN'			=> 'Si la càrrega del sistema per a 1 minut sobrepassa aquest valor, el fòrum quedarà automàticament fora de línia. El valor 1.0 equival a una utilització aproximada del 100% d’un processador. Això només funciona en servidors basats en UNIX i on aquesta informació estigui accessible. Aquest valor es reinicialitza automàticament a 0 si el phpBB no es capaç d’obtenir el límit de càrrega.',
	'LIMIT_SESSIONS'				=> 'Limita les sessions',
	'LIMIT_SESSIONS_EXPLAIN'		=> 'Si el nombre de sessions sobrepassa aquest valor dintre d’un periode d’un minut, el fòrum quedarà fora de línia. Introduïu un 0 per a sessions il·limitades.',
	'LOAD_CPF_MEMBERLIST'			=> 'Permet que els estils mostrin camps personalitzats del perfil a la llista de membres',
	'LOAD_CPF_PM'					=> 'Mostra els camps personalitzats del perfil als missatges privats',
	'LOAD_CPF_VIEWPROFILE'			=> 'Mostra els camps personalitzats del perfil als perfils d’usuari',
	'LOAD_CPF_VIEWTOPIC'			=> 'Mostra els camps personalitzats del perfil a les pàgines dels temes',
	'LOAD_USER_ACTIVITY'			=> 'Mostra l’activitat de l’usuari',
	'LOAD_USER_ACTIVITY_EXPLAIN'	=> 'Mostra els temes/fòrums actius als perfils de l’usuari i al tauler de control de l’usuari. És recomanable inhabilitar aquesta opció en fòrums amb més d’un milió d’entrades.',
	'LOAD_USER_ACTIVITY_LIMIT'		=> 'Límit d’entrades per a l’activitat de l’usuari',
	'LOAD_USER_ACTIVITY_LIMIT_EXPLAIN'	=> 'El tema/fòrum actiu no es mostrarà per usuaris que tinguin més entrades que aquest número. Introduïu un 0 per inhabilitar el límit.',
	'READ_NOTIFICATION_EXPIRE_DAYS'	=> 'Caducitat de les notificacions de lectura',
	'READ_NOTIFICATION_EXPIRE_DAYS_EXPLAIN' => 'Nombre de dies que han de passar abans que una notificació de lectura s’elimini automàticament. Introduïu un 0 per fer que les notificacions siguin permanents.',
	'RECOMPILE_STYLES'				=> 'Recompila els elements dels estils desactualitzats',
	'RECOMPILE_STYLES_EXPLAIN'		=> 'Comprova si hi ha elements dels estils actualitzats al sistema de fitxers i els recompila.',
	'YES_ACCURATE_PM_BUTTON'			=> 'Habilita a les pàgines de temes el botó de MP en funció dels permisos específics',
	'YES_ACCURATE_PM_BUTTON_EXPLAIN'	=> 'Si l’habiliteu, a les entrades, només els perfils dels usuaris que tenen permís per llegir missatges privats tindran un botó de misatge privat.',
	'YES_ANON_READ_MARKING'			=> 'Habilita el marcat de temes per als usuaris visitants',
	'YES_ANON_READ_MARKING_EXPLAIN'	=> 'Emmagatzema informació de l’estat llegit/no llegit per als usuaris visitants. Si l’inhabiliteu, les entrades sempre es mostren llegides als usuaris visitants.',
	'YES_BIRTHDAYS'					=> 'Habilita la llista d’aniversaris',
	'YES_BIRTHDAYS_EXPLAIN'			=> 'Si l’inhabiliteu, la llista d’aniversaris ja no es mostra. Per que aquesta configuració tingui efecte, la característica d’aniversaris també ha d’estar habilitada.',
	'YES_JUMPBOX'					=> 'Habilita el formulari “Salta a”',
	'YES_MODERATORS'				=> 'Mostra els moderadors',
	'YES_ONLINE'					=> 'Habilita la llista de qui està connectat',
	'YES_ONLINE_EXPLAIN'			=> 'Mostra informació de quins usuaris estan connectats a les pagines d’índex, fòrum i tema.',
	'YES_ONLINE_GUESTS'				=> 'Habilita el llistat d’usuaris visitants a “Qui està connectat”',
	'YES_ONLINE_GUESTS_EXPLAIN'		=> 'Permet que es mostri informació de l’usuari visitant a “Qui està connectat”.',
	'YES_ONLINE_TRACK'				=> 'Mostra si l’usuari està connectat o desconnectat',
	'YES_ONLINE_TRACK_EXPLAIN'		=> 'Mostra informació sobre l’estat de connexió per als usuaris a les pàgines del perfil i de tema.',
	'YES_POST_MARKING'				=> 'Habilita el marcat de temes',
	'YES_POST_MARKING_EXPLAIN'		=> 'Indica si un usuari ha publicat una entrada al tema.',
	'YES_READ_MARKING'				=> 'Habilita la gestió al servidor del marcat de temes',
	'YES_READ_MARKING_EXPLAIN'		=> 'Emmagatzema la informació de l’estat llegit/no llegit a la base de dades en lloc d’usar una galeta.',
	'YES_UNREAD_SEARCH'				=> 'Habilita la cerca d’entrades no llegides',
));

// Auth settings
$lang = array_merge($lang, array(
	'ACP_AUTH_SETTINGS_EXPLAIN'	=> 'El phpBB permet l’ús de connectors o mòduls d’autenticació. Aquests us permeten determinar com s’autentiquen els usuaris quan inicien la sessió al fòrum. Per defecte es proporcionen quatre connectors: base de dades, LDAP, Apache i OAuth. No tots els mètodes necessiten informació addicional, només introduïu els camps que siguin rellevants per al mètode seleccionat.',

	'AUTH_METHOD'				=> 'Seleccioneu un mètode d’autenticació',

	'AUTH_PROVIDER_OAUTH_ERROR_ELEMENT_MISSING'	=> 'Heu de proporcionar tant la clau com el secret de cada proveïdor de servei OAuth habilitat. En algún proveïdor de servei OAuth només se n’ha proporcionat un dels dos.',
	'AUTH_PROVIDER_OAUTH_EXPLAIN'				=> 'Cada proveïdor OAuth requereix una combinació única de secret i clau per tal d’autenticar-se amb el servidor extern. Us els ha de proporcionar el servei Oauth quan registreu amb ells el vostre lloc web i els heu d’entrar exactament tal i com us els han comunicat.<br>Qualsevol servei per al qual no es proporcioni aquí tant la clau com el secret no estarà disponible per que l’utilitzin els usuaris del fòrum. Tingueu en compte que els usuaris encara poden registranr-se i iniciar sessió amb el connector d’autenticació per base de dades.',
	'AUTH_PROVIDER_OAUTH_KEY'					=> 'Clau',
	'AUTH_PROVIDER_OAUTH_TITLE'					=> 'OAuth',
	'AUTH_PROVIDER_OAUTH_SECRET'				=> 'Secret',

	'APACHE_SETUP_BEFORE_USE'	=> 'Cal que configureu l’autenticació de l’Apache abans de canviar el phpBB a aquest mètode d’autenticació. Recordeu que el nom d’usuari que utilitzeu per a l’autenticació amb Apache ha de ser el mateix que el nom d’usuari al phpBB. L’autenticació amb Apache només es pot usar amb mod_php (no amb una versió CGI).',

	'LDAP'							=> 'LDAP',
	'LDAP_DN'						=> '<var>dn</var> de la base LDAP',
	'LDAP_DN_EXPLAIN'				=> 'Això es el “Distinguished Name”, que localitza la informació d’usuari, p.ex. <samp>o=La meva companyia,c=ES</samp>.',
	'LDAP_EMAIL'					=> 'Atribut de correu electrònic de LDAP',
	'LDAP_EMAIL_EXPLAIN'			=> 'Indiqueu el nom de la vostra entrada d’atribut de correu electrònic (si n’existeix una) per obtenir l’adreça electrònica dels usuaris nous automàticament. Si ho deixeu buit, les adreces electròniques dels usuaris que iniciïn la sessió per primera vegada estaran buides.',
	'LDAP_INCORRECT_USER_PASSWORD'	=> 'La vinculació amb el servidor LDAP amb el nom d’usuari/contrasenya especificats ha fallat.',
	'LDAP_NO_EMAIL'					=> 'L’atribut de correu electrònic especificat no existeix.',
	'LDAP_NO_IDENTITY'				=> 'No s’ha trobat una identitat d’inici de sessió per a  %s.',
	'LDAP_PASSWORD'					=> 'Contrasenya LDAP',
	'LDAP_PASSWORD_EXPLAIN'			=> 'Deixeu-la buida per usar la vinculació anònima. Altrament, introduïu la contrasenya de l’usuari que hi ha a sobre. És necessària per a servidors Active Directory.<br><em><strong>Advertiment:</strong> Aquesta contrasenya s’emmagatzemarà a la base de dades com a text net i serà visible per a qualsevol que pugui accedir a la vostra base de dades o que pugui veure aquesta pàgina de configuració.</em>',
	'LDAP_PORT'						=> 'Port del servidor LDAP',
	'LDAP_PORT_EXPLAIN'				=> 'Opcionalment, podeu especificar el port que s’ha d’usar per connectar-se amb el servidor LDAP en lloc del port per defecte 389.',
	'LDAP_SERVER'					=> 'Nom del servidor LDAP',
	'LDAP_SERVER_EXPLAIN'			=> 'Si utilitzeu LDAP aquest es el nom de l’amfitrió o l’adreça IP del servidor LDAP. Alternativament, podeu especificar un URL de l’estil ldap://hostname:port/',
	'LDAP_UID'						=> '<var>uid</var> LDAP',
	'LDAP_UID_EXPLAIN'				=> 'Aquesta és la clau sota la qual es cerca una identitat d’inici de sessió determinada, p.ex. <var>uid</var>, <var>sn</var>, etc.',
	'LDAP_USER'						=> '<var>dn</var> d’usuari LDAP',
	'LDAP_USER_EXPLAIN'				=> 'Deixeu-lo buit per usar la vinculació anònima. Si l’introduïu, el phpBB utilitza el distinguished name en els intents d’inici de sessió per trobar l’usuari correcte, p.ex. <samp>uid=Usuari,ou=Unitat,o=Companyia,c=ES</samp>. És necessari per a servidors Active Directory.',
	'LDAP_USER_FILTER'				=> 'Filtre d’usuaris LDAP',
	'LDAP_USER_FILTER_EXPLAIN'		=> 'Opcionalment, podeu limitar encara més els objectes cercats amb filtres addicionals. Per exemple <samp>objectClass=posixGroup</samp> tindria com a resultat l’ús de <samp>(&amp;(uid=$usuari)(objectClass=posixGroup))</samp>',
));

// Server Settings
$lang = array_merge($lang, array(
	'ACP_SERVER_SETTINGS_EXPLAIN'	=> 'Aquí podeu definir configuracions que depenen del servidor i del domini. Assegureu-vos que les dades que introduïu són correctes, si hi ha errors els correus electrònics que s’enviïn des del fòrum contindran informació incorrecta. Quan introduïu el nom de domini recordeu-vos d’incloure http:// o el protocol adequat. Modifiqueu el número de port només si sabeu que el servidor utilitza un valor diferent, el port 80 és correcte en la majoria de casos.',

	'ENABLE_GZIP'				=> 'Habilita la compressió gzip',
	'ENABLE_GZIP_EXPLAIN'		=> 'Es comprimirà el contingut generat abans d’enviar-lo a l’usuari. Això pot reduir el trànsit de la xarxa, però augmentarà l’ús de la CPU tant en el servidor com en el client. Requereix que l’extensió zlib del PHP estigui carregada.',
	'FORCE_SERVER_VARS'			=> 'Força la configuració de l’URL del servidor',
	'FORCE_SERVER_VARS_EXPLAIN'	=> 'Si l’habiliteu, s’usarà la configuració del servidor definida aquí en lloc dels valors determinats automàticament.',
	'ICONS_PATH'				=> 'Camí d’emmagatzemament de les icones per a les entrades',
	'ICONS_PATH_EXPLAIN'		=> 'Camí a partir del directori arrel del phpBB, p.ex. <samp>images/icons</samp>.',
	'MOD_REWRITE_ENABLE'		=> 'Permet la reescriptura d’URL',
	'MOD_REWRITE_ENABLE_EXPLAIN' => 'Si l’habiliteu, els URL que continguin ’app.php’ es reescriuran per treure el nom del fitxer (és a dir app.php/foo es convertirà en /foo). <strong>És necessari el mòdul mod_rewrite del servidor Apache per que això funcioni; si habiliteu aquesta opció sense tenir activat mod_rewrite, és possible que els URL del vostre fòrum deixin de funcionar.</strong>',
	'MOD_REWRITE_DISABLED'		=> 'El mòdul <strong>mod_rewrite</strong> del vostre servidor Apache està inhabilitat. Habiliteu el módul o poseu-vos en contacte amb el vostre proveïdor d’allotjament web si voleu habilitar aquesta funció.',
	'MOD_REWRITE_INFORMATION_UNAVAILABLE' => 'No ha estat possible determinar si aquest servidor permet l’ús de reescriptura d’URL. Podeu habilitar aquesta configuració, però si la reescriptura d’URL no està disponible, és possible que els camins generats per aquest fòrum (com ara els que s’utilitzen als enllaços) deixin de funcionar. Poseu-vos en contacte amb el vostre proveïdor d’allotjament web si no esteu segurs que aquesta funció es pugui activar de forma segura.',
	'PATH_SETTINGS'				=> 'Configuració dels camins',
	'RANKS_PATH'				=> 'Camí d’emmagatzemament de les imatges de rang',
	'RANKS_PATH_EXPLAIN'		=> 'Camí a partir del directori arrel del phpBB, p.ex. <samp>images/ranks</samp>.',
	'SCRIPT_PATH'				=> 'Camí de l’script',
	'SCRIPT_PATH_EXPLAIN'		=> 'Camí on es troba el phpBB respecte al nom de domini, p.ex. <samp>/phpBB3</samp>.',
	'SERVER_NAME'				=> 'Nom de domini',
	'SERVER_NAME_EXPLAIN'		=> 'El nom de domini en el qual s’executa aquest servidor (per exemple: <samp>exemple.cat</samp>).',
	'SERVER_PORT'				=> 'Port del servidor',
	'SERVER_PORT_EXPLAIN'		=> 'El port en què s’executa el servidor, normalment el 80, canvieu-lo només si és diferent.',
	'SERVER_PROTOCOL'			=> 'Protocol del servidor',
	'SERVER_PROTOCOL_EXPLAIN'	=> 'S’utilitza com a protocol del servidor si es força aquesta configuració. Si és buit o no es força la configuració, el protocol es determina per la configuració de galeta segura (<samp>http://</samp> o <samp>https://</samp>).',
	'SERVER_URL_SETTINGS'		=> 'Configuració de l’URL del servidor',
	'SMILIES_PATH'				=> 'Camí d’emmagatzemament de les emoticones',
	'SMILIES_PATH_EXPLAIN'		=> 'Camí a partir del directori arrel del phpBB, p.ex. <samp>images/smilies</samp>.',
	'UPLOAD_ICONS_PATH'			=> 'Camí d’emmagatzemament de les icones dels grups d’extensions',
	'UPLOAD_ICONS_PATH_EXPLAIN'	=> 'Camí a partir del directori arrel del phpBB, p.ex. <samp>images/upload_icons</samp>.',
	'USE_SYSTEM_CRON'		=> 'Executa les tasques periòdiques amb el cron del sistema',
	'USE_SYSTEM_CRON_EXPLAIN'		=> 'Si ho inhabiliteu, el phpBB organitzarà l’execució automàtica de tasques periòdiques. Si ho habiliteu, el phpBB no planificarà cap tasca periòdica per sí mateix; caldrà que un administrador del sistema organitzi l’execució de <code>bin/phpbbcli.php cron:run</code> amb la utilitat cron del sistema a intervals regulars (p.ex. cada 5 minuts).',
));

// Security Settings
$lang = array_merge($lang, array(
	'ACP_SECURITY_SETTINGS_EXPLAIN'		=> 'Aquí podeu definir les configuracions relacionades amb les session i l’inici de sessions.',

	'ALL'							=> 'Tota',
	'ALLOW_AUTOLOGIN'				=> 'Permet l’inici de sessió automàtic',
	'ALLOW_AUTOLOGIN_EXPLAIN'		=> 'Determina si es mostra als usuaris l’opció “Recorda’m” quan visiten el fòrum.',
	'ALLOW_PASSWORD_RESET'			=> 'Permet la reinicialització de la contrasenya (“He oblidat la meva contrasenya”)',
	'ALLOW_PASSWORD_RESET_EXPLAIN'	=> 'Determina si els usuaris poden usar l’enllaç “He oblidat la meva contrasenya” de la pàgina d’inici de sessió per recuperar el seu compte. Si utilitzeu un sistema d’autenticació extern, és possible que vulgueu inhabilitar aquesta funció.',
	'AUTOLOGIN_LENGTH'				=> 'Durada (en dies) de la clau d’inici de sessió tipus “Recorda’m”',
	'AUTOLOGIN_LENGTH_EXPLAIN'		=> 'Nombre de dies després dels quals s’eliminen les claus d’inici de sessió tipus “Recorda’m”. Introduïu un zero per inhabilitar-ho.',
	'BROWSER_VALID'					=> 'Validació del navegador',
	'BROWSER_VALID_EXPLAIN'			=> 'Habilita la validació del navegador per cada sessió per millorar la seguretat.',
	'CHECK_DNSBL'					=> 'Comprova l’adreça IP contra la llista DNS Blackhole List',
	'CHECK_DNSBL_EXPLAIN'			=> 'Si l’habiliteu, l’adreça IP de l’usuari es comprova contra els serveis DNSBL següents per registrar-se i publicar entrades: <a href="http://spamcop.net">spamcop.net</a> i <a href="http://www.spamhaus.org">www.spamhaus.org</a>. Aquesta cerca pot trigar una estona, depenent de la configuració del servidor. Si experimenteu lentitud en l’operació o massa falsos positius, us recomanem que inhabiliteu aquesta opció.',
	'CLASS_B'						=> 'A.B',
	'CLASS_C'						=> 'A.B.C',
	'EMAIL_CHECK_MX'				=> 'Comprova si el domini de l’adreça electrònica té un registre MX vàlid',
	'EMAIL_CHECK_MX_EXPLAIN'		=> 'Si l’habiliteu, el domini de l’adreça electrònica proporcionada durant el registre i els canvis en el perfil es comprova per veure si té un registre MX vàlid.',
	'FORCE_PASS_CHANGE'				=> 'Força el canvi de contrasenya',
	'FORCE_PASS_CHANGE_EXPLAIN'		=> 'Obliga els usuaris a canviar la seva contrasenya després d’un nombre determinat de dies. Si introduïu un 0, s’inhabilita aquest comportament.',
	'FORM_TIME_MAX'					=> 'Temps màxim per trametre formularis',
	'FORM_TIME_MAX_EXPLAIN'			=> 'El temps que te un usuari per trametre un formulari. Utilitzeu un -1 per inhabilitar-ho. Tingueu en compte que un formulari pot esdevenir no vàlid si venç la sessió, sense tenir en compte aquesta configuració.',
	'FORM_SID_GUESTS'				=> 'Vincula els formularis a les sessions dels usuaris visitants',
	'FORM_SID_GUESTS_EXPLAIN'		=> 'Si s’habilita, el testimoni proporcionat als usuaris visitants serà exclusiu per a cada sessió. Això pot causar problemes amb alguns proveïdors d’Internet.',
	'FORWARDED_FOR_VALID'			=> 'Valida la capçalera <var>X_FORWARDED_FOR</var>',
	'FORWARDED_FOR_VALID_EXPLAIN'	=> 'Es continuarà la sessió si la capçalera <var>X_FORWARDED_FOR</var> enviada és igual a la capçalera enviada a la sol·licitud anterior. També es comprovaran els bandejos contra direccions IP a <var>X_FORWARDED_FOR</var>.',
	'IP_VALID'						=> 'Validació de sessió per adreça IP',
	'IP_VALID_EXPLAIN'				=> 'Determina quina part de l’adreça IP de l’usuari s’utilitza per validar una sessió; <samp>Tota</samp> compara l’adreça completa, <samp>A.B.C</samp> els primers x.x.x, <samp>A.B</samp> els primers x.x, <samp>Cap</samp> inhabilita la comparació. En adreces IPv6, <samp>A.B.C</samp> compara els 4 primers blocs i <samp>A.B</samp> els 3 primers blocs.',
	'IP_LOGIN_LIMIT_MAX'			=> 'Nombre màxim d’intents d’inici de sessió per adreça IP',
	'IP_LOGIN_LIMIT_MAX_EXPLAIN'	=> 'El llindar d’intents d’inici de sessió permesos des d’una mateixa adreça IP abans que s’activi la tasca contra robots de brossa. Si introduïu un 0, no es tindran en compte les adreces IP per activar la tasca contra robots de brossa.',
	'IP_LOGIN_LIMIT_TIME'			=> 'Temps de caducitat dels intents d’inici de sessió per adreça IP',
	'IP_LOGIN_LIMIT_TIME_EXPLAIN'	=> 'Els intents d’inici de sesió no es tenen en compte quan ha passat aquest interval.',
	'IP_LOGIN_LIMIT_USE_FORWARDED'	=> 'Limita els intents d’inici de sesió mitjançant la capçalera <var>X_FORWARDED_FOR</var>',
	'IP_LOGIN_LIMIT_USE_FORWARDED_EXPLAIN'	=> 'En lloc de limitar els intents d’inici de sesió per adreça IP, es limiten pels valors de <var>X_FORWARDED_FOR</var>. <br><em><strong>Advertiment:</strong> No habiliteu aquesta característica a no ser que gestioneu un servidor intermediari que assigni valors fiables a <var>X_FORWARDED_FOR</var>.</em>',
	'MAX_LOGIN_ATTEMPTS'			=> 'Nombre màxim d’intents d’inici de sessió per nom d’usuari',
	'MAX_LOGIN_ATTEMPTS_EXPLAIN'	=> 'El nombre d’intents d’inici de sessió permesos per un mateix nom d’usuari abans que s’activi la tasca contra robots de brossa. Si introduïu un 0, no es tindran en compte els noms d’usuari per activar la tasca contra robots de brossa.',
	'NO_IP_VALIDATION'				=> 'Cap',
	'NO_REF_VALIDATION'				=> 'Cap',
	'PASSWORD_TYPE'					=> 'Complexitat de la contrasenya',
	'PASSWORD_TYPE_EXPLAIN'			=> 'Determina com de complexa ha de ser una contrasenya en proporcionar-la o modificar-la, les opcións subsegüents inclouen les anteriors.',
	'PASS_TYPE_ALPHA'				=> 'Ha de contenir lletres i números',
	'PASS_TYPE_ANY'					=> 'Sense requisits',
	'PASS_TYPE_CASE'				=> 'Ha de contenir majúscules i minúscules',
	'PASS_TYPE_SYMBOL'				=> 'Ha de contenir símbols',
	'REF_HOST'						=> 'Valida només l’amfitrió',
	'REF_PATH'						=> 'Valida també el camí',
	'REFERRER_VALID'				=> 'Valida la pàgina d’origen',
	'REFERRER_VALID_EXPLAIN'		=> 'Si l’habiliteu, es comprovarà la pàgina des de la qual es fan les sol·licituds POST segons la configuració d’amfitrió/camí. Això pot donar problemes amb fòrums que utilitzin diversos dominis i/o un inici de sessió extern.',
	'TPL_ALLOW_PHP'					=> 'Permet el php a les plantilles',
	'TPL_ALLOW_PHP_EXPLAIN'			=> 'Si s’habilita aquesta opció, les sentències <code>PHP</code> i <code>INCLUDEPHP</code> a les plantilles es reconeixeran i s’analitzaran.',
	'UPLOAD_CERT_VALID'				=> 'Valida certificats de penjada',
	'UPLOAD_CERT_VALID_EXPLAIN'		=> 'Si l’habiliteu, es validaran els certificats de les penjades remotes. Això requereix que definiu el feix CA (CA bundle) a la cofiguració <samp>openssl.cafile</samp> o <samp>curl.cainfo</samp> del fitxer php.ini',
));

// Email Settings
$lang = array_merge($lang, array(
	'ACP_EMAIL_SETTINGS_EXPLAIN'	=> 'Aquesta informació s’utilitza quan el fòrum envia correus electrònics als usuaris. Assegureu-vos que l’adreça electrònica que especifiqueu és vàlida, els missatges retornats o impossibles de lliurar s’enviaran probablement a aquesta adreça. Si el vostre amfitrió no us proporciona un servei de correu electrònic nadiu (basat en PHP), podeu enviar missatges directament utilitzant SMTP. Per això us cal l’adreça d’un servidor adequat (si cal, demaneu-la al vostre proveïdor). Si el servidor requereix autenticació (i només si la requereix) introduïu el nom d’usuari necessari, la contrasenya i el mètode d’autenticació.',

	'ADMIN_EMAIL'					=> 'Adreça electrònica d’enviament',
	'ADMIN_EMAIL_EXPLAIN'			=> 'S’usarà com a adreça electrònica d’enviament en tots els correus electrònics, l’adreça electrònica de contacte tècnic. S’usarà sempre com a adreça al camp <samp>Sender</samp> dels correus electrònics.',
	'BOARD_EMAIL_FORM'				=> 'Els usuaris envien correus electrònics a través del fòrum',
	'BOARD_EMAIL_FORM_EXPLAIN'		=> 'En lloc de mostrar les adreces electròniques dels usuaris, els usuaris poden enviar-se correus electrònics a través del fòrum.',
	'BOARD_HIDE_EMAILS'				=> 'Oculta les adreces electròniques',
	'BOARD_HIDE_EMAILS_EXPLAIN'		=> 'Aquesta funció manté les adreces electròniques completament privades.',
	'CONTACT_EMAIL'					=> 'Adreça electrònica de contacte',
	'CONTACT_EMAIL_EXPLAIN'			=> 'S’usarà aquesta adreça quan es necessiti un punt de contacte específic, p.ex. correu brossa, errors, etc. S’usarà sempre com a adreça en els camps <samp>From</samp> i <samp>Reply-To</samp> dels correus electrònics.',
	'CONTACT_EMAIL_NAME'			=> 'Nom de contacte',
	'CONTACT_EMAIL_NAME_EXPLAIN'	=> 'Aquest es el nom de contacte que veuran els destinataris dels correus electrònics. Si no voleu tenir un nom de contacte, deixeu aquest camp en blanc.',
	'EMAIL_FORCE_SENDER'			=> 'Força l’adreça electrònica d’enviament',
	'EMAIL_FORCE_SENDER_EXPLAIN'	=> 'Fa que al camp <samp>Return-Path</samp> s’hi posi l’adreça electr+onica d’enviament en lloc d’usar l’usuari local i el nom de màquina del servidor. Aquesta configuració no s’aplica si utilitzeu SMTP.<br><em><strong>Adevrtiment:</strong> Requereix que afegiu l’usuari amb el que s’executa el servidor web com usuari de confiança a la configuració de sendmail.</em>',
	'EMAIL_PACKAGE_SIZE'			=> 'Mida dels paquets de correu electrònic',
	'EMAIL_PACKAGE_SIZE_EXPLAIN'	=> 'És el nombre màxim de correus electrònics que s’envien en un paquet. Aquesta configuració s’aplica a la cua interna de missatges; introduïu un 0 si teniu problemes amb correus de notificació que no s’han pogut lliurar.',
	'EMAIL_MAX_CHUNK_SIZE'			=> 'Nombre màxim de destinataris permesos',
	'EMAIL_MAX_CHUNK_SIZE_EXPLAIN'	=> 'Si és necessari, configureu-ho per no excedir el nombre màxim de destinataris que el vostre servidor de correu electrònic permet per un sol missatge.',
	'EMAIL_SIG'						=> 'Signatura per als correus electrònics',
	'EMAIL_SIG_EXPLAIN'				=> 'Aquest text s’adjuntarà a tots els correus electrònics que enviï el fòrum.',
	'ENABLE_EMAIL'					=> 'Habilita els correus electrònics del fòrum',
	'ENABLE_EMAIL_EXPLAIN'			=> 'Si s’inhabilita, el fòrum no enviarà cap correu electrònic. <em>Tingueu en compte que cal que aquesta opció estigui habilitada per a la configuració d’activació de comptes d’usuaris i administradors. Si actualment esteu utilizant l’opció d’activació per “usuari” o “administrador”, inhabilitar l’enviament de correus inhabilitarà el registre de comptes nous.</em>',
	'SEND_TEST_EMAIL'				=> 'Envia un correu electrònic de prova',
	'SEND_TEST_EMAIL_EXPLAIN'		=> 'Això enviarà un correu electrònic de prova a l’adreça electrònica definida al vostre compte.',
	'SMTP_ALLOW_SELF_SIGNED'		=> 'Permet certificats SSL autosignats',
	'SMTP_ALLOW_SELF_SIGNED_EXPLAIN'=> 'Permet connexions a un servidor SMTP amb un certificat SSL autosignat.<em><strong>Advertiment:</strong> Permetre certificats SSL autosignats pot tenir implicacions de seguretat.</em>',
	'SMTP_AUTH_METHOD'				=> 'Mètode d’autenticació SMTP',
	'SMTP_AUTH_METHOD_EXPLAIN'		=> 'Només s’utilitza si s’ha introduït un nom d’usuari i contrasenya, pregunteu al vostre proveïdor si no esteu segur de quin mètode usar.',
	'SMTP_CRAM_MD5'					=> 'CRAM-MD5',
	'SMTP_DIGEST_MD5'				=> 'DIGEST-MD5',
	'SMTP_LOGIN'					=> 'LOGIN',
	'SMTP_PASSWORD'					=> 'Contrasenya SMTP',
	'SMTP_PASSWORD_EXPLAIN'			=> 'Introduïu la contrasenya només si el servidor la requereix.<br><em><strong>Advertiment:</strong> Aquesta contrasenya s’emmagatzemarà com a text net a la base de dades i serà visible per qualsevol persona que tingui accés directe a la vostra base de dades o pugui veure aquesta pàgina de configuració.</em>',
	'SMTP_PLAIN'					=> 'PLAIN',
	'SMTP_POP_BEFORE_SMTP'			=> 'POP-BEFORE-SMTP',
	'SMTP_PORT'						=> 'Port del servidor SMTP',
	'SMTP_PORT_EXPLAIN'				=> 'Canvieu-lo només si sabeu que el servidor SMTP utilitza un port diferent.',
	'SMTP_SERVER'					=> 'Adreça del servidor SMTP',
	'SMTP_SERVER_EXPLAIN'			=> 'No hi poseu un protocol (<samp>ssl://</samp> o <samp>tls://</samp>) si no és que el vostre proveïdor de correu us ho indica.',
	'SMTP_SETTINGS'					=> 'Configuració SMTP',
	'SMTP_USERNAME'					=> 'Nom d’usuari SMTP',
	'SMTP_USERNAME_EXPLAIN'			=> 'Introduïu el nom d’usuari només si el servidor el requereix.',
	'SMTP_VERIFY_PEER'				=> 'Verifica el certificat SSL',
	'SMTP_VERIFY_PEER_EXPLAIN'		=> 'Requereix la verificació del certificat SSL utilitzat pel servidor SMTP.<em><strong>Advertiment:</strong> Connectar-se a servidors amb un certificat SSL no verificat pot tenir implicacions de seguretat.</em>',
	'SMTP_VERIFY_PEER_NAME'			=> 'Verifica el nom del servidor SMTP',
	'SMTP_VERIFY_PEER_NAME_EXPLAIN'	=> 'Requereix la verificació del nom dels servidors SMTP que utilitzen connexions SSL / TLS.<em><strong>Advertiment:</strong> Connectar-se a servidors amb nom no verificat pot tenir implicacions de seguretat.</em>',
	'TEST_EMAIL_SENT'				=> 'S’ha enviat el correu electrònic de prova.<br>Si no l’heu rebut, comproveu la configuració d’enviament de correus electrònics.<br><br>Si necessiteu ajuda, visiteu els <a href="https://www.phpbb.com/community/">fòrums d’assistència del phpBB</a> (en anglès).',

	'USE_SMTP'						=> 'Utilitza el servidor SMTP per al correu electrònic',
	'USE_SMTP_EXPLAIN'				=> 'Seleccioneu “Sí” si voleu o heu d’enviar els correus electrònics a través d’un servidor en lloc de la funcio de correu local.',
));

// Jabber settings
$lang = array_merge($lang, array(
	'ACP_JABBER_SETTINGS_EXPLAIN'	=> 'Aquí podeu habilitar i controlar l’ús del Jabber per a missatgeria instantània i notificacions del fòrum. El Jabber és un protocol de codi font obert i, per tant, disponible per ser utilitzat per qualsevol. Alguns servidors de Jabber inclouen pasarel·les o transports que us permeten contactar amb usuaris d’altres xarxes. No tots els servidors ofereixen tots els transports i canvis en els protocols poden causar que el transport no funcioni. Assegureu-vos d’introduir detalls que corresponguin a un compte que ja s’hagi registrat - el phpBB usarà els detalls que introduïu tal com estan aquí.',

	'JAB_ALLOW_SELF_SIGNED'			=> 'Permet certificats SSL autosignats',
	'JAB_ALLOW_SELF_SIGNED_EXPLAIN'	=> 'Permet connexions a un servidor Jabber amb un certificat SSL autosignat.<em><strong>Advertiment:</strong> Permetre certificats SSL autosignats pot tenir implicacions de seguretat.</em>',
	'JAB_ENABLE'					=> 'Habilita el Jabber',
	'JAB_ENABLE_EXPLAIN'			=> 'Habilita l’ús de la missatgeria i les notificacions Jabber.',
	'JAB_GTALK_NOTE'				=> 'Tingueu en compte que el GTalk no funcionarà perquè no s’ha trobat la funció <samp>dns_get_record</samp>. Aquesta funció no està disponible al PHP4, i no està implementada a les plataformes Windows. Actualment no funciona en els sistemes basats en BSD, Mac OS inclòs.',
	'JAB_PACKAGE_SIZE'				=> 'Mida dels paquets Jabber',
	'JAB_PACKAGE_SIZE_EXPLAIN'		=> 'És el nombre de missatges enviats en un paquet. Si introduïu un 0 el missatge s’envia immediatament i no es ficarà en una cua per enviar-lo més tard.',
	'JAB_PASSWORD'					=> 'Contrasenya Jabber',
	'JAB_PASSWORD_EXPLAIN'			=> '<em><strong>Advertiment:</strong> Aquesta contrasenya s’emmagatzemarà com a text net a la base de dades i serà visible per qualsevol persona que tingui accés directe a la vostra base de dades o pugui veure aquesta pàgina de configuració.</em>',
	'JAB_PORT'						=> 'Port Jabber',
	'JAB_PORT_EXPLAIN'				=> 'Deixeu-lo buit a no ser que sabeu que no és el port 5222.',
	'JAB_SERVER'					=> 'Servidor Jabber',
	'JAB_SERVER_EXPLAIN'			=> 'Vegeu %sjabber.org%s per obtenir una llista de servidors.',
	'JAB_SETTINGS_CHANGED'			=> 'S’ha canviat la configuració del Jabber correctament.',
	'JAB_USE_SSL'					=> 'Utilitza SSL per connectar-te',
	'JAB_USE_SSL_EXPLAIN'			=> 'Si l’habiliteu, s’intentarà establir una connexió segura. El port Jabber es modificarà a 5223 si s’especifica el port 5222.',
	'JAB_USERNAME'					=> 'Nom d’usuari Jabber o JID',
	'JAB_USERNAME_EXPLAIN'			=> 'Especifiqueu un nom d’usuari registrat o un JID vàlid. No es comprovarà que el nom d’usuari sigui vàlid. Si només eswpecifiqueu un nom d’usuari, el vostre JID serà el nom d’usuari i el servidor el que s’ha especificat a sobre. Altrament, especifiqueu un JID vàlid, per exemple usuari@jabber.org.',
	'JAB_VERIFY_PEER'				=> 'Verifica el certificat SSL',
	'JAB_VERIFY_PEER_EXPLAIN'		=> 'Requereix la verificació del certificat SSL utilitzat pel servidor Jabber.<em><strong>Advertiment:</strong> Connectar-se a servidors amb un certificat SSL no verificat pot tenir implicacions de seguretat.</em>',
	'JAB_VERIFY_PEER_NAME'			=> 'Verifica el nom del servidor Jabber',
	'JAB_VERIFY_PEER_NAME_EXPLAIN'	=> 'Requereix la verificació del nom dels servidors Jabber que utilitzen connexions SSL / TLS.<em><strong>Advertiment:</strong> Connectar-se a servidors amb nom no verificat pot tenir implicacions de seguretat.</em>',
));
