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

$lang = array_merge($lang, array(
	'ADD_ATTACHMENT'			=> 'Penja un fitxer adjunt',
	'ADD_ATTACHMENT_EXPLAIN'	=> 'Si vols adjuntar un o més fitxers, introdueix-ne els detalls a continuació.',
	'ADD_FILE'					=> 'Afegeix el fitxer',
	'ADD_POLL'					=> 'Crea una enquesta',
	'ADD_POLL_EXPLAIN'			=> 'Si no vols afegir cap enquesta al tema, deixa aquests camps en blanc.',
	'ALREADY_DELETED'			=> 'Aquest missatge ja ha estat eliminat.',
	'ATTACH_COMMENT_NO_EMOJIS'	=> 'El comentari del fitxer adjunt conté caràcters no admesos (emoji).',
	'ATTACH_DISK_FULL'			=> 'No hi ha prou espai lliure al disc per a adjuntar aquest fitxer.',
	'ATTACH_QUOTA_REACHED'		=> 'S’ha arribat al límit de fitxers adjunts del fòrum.',
	'ATTACH_SIG'				=> 'Inclou-hi la signatura (recorda que la pots canviar al tauler de control)',

	'BBCODE_A_HELP'				=> 'Fitxer adjunt en línia: [attachment=]nomfitxer.ext[/attachment]',
	'BBCODE_B_HELP'				=> 'Text en negreta: [b]text[/b]',
	'BBCODE_C_HELP'				=> 'Visualització de codi: [code]codi[/code]',
	'BBCODE_D_HELP'				=> 'Flash: [flash=amplada,alçada]http://url[/flash]',
	'BBCODE_F_HELP'				=> 'Mida de la lletra: [size=85]text petit[/size]',
	'BBCODE_IS_OFF'				=> 'El %sBBCode%s està <em>INACTIU</em>',
	'BBCODE_IS_ON'				=> 'El %sBBCode%s està <em>ACTIU</em>',
	'BBCODE_I_HELP'				=> 'Text en cursiva: [i]text[/i]',
	'BBCODE_L_HELP'				=> 'Llista: [list][*]text[/list]',
	'BBCODE_LISTITEM_HELP'		=> 'Element de la llista: [*]text',
	'BBCODE_O_HELP'				=> 'Llista ordenada: p.ex. [list=1][*]Primer punt[/list] o [list=a][*]Punt A[/list]',
	'BBCODE_P_HELP'				=> 'Insereix una imatge: [img]http://url_imatge[/img]',
	'BBCODE_Q_HELP'				=> 'Cita un text: [quote]text[/quote]',
	'BBCODE_S_HELP'				=> 'Color de la lletra: [color=red]text[/color] o [color=#FF0000]text[/color]',
	'BBCODE_U_HELP'				=> 'Text subratllat: [u]text[/u]',
	'BBCODE_W_HELP'				=> 'Insereix un URL: [url]http://url[/url] o [url=http://url]text de l’URL[/url]',
	'BBCODE_Y_HELP'				=> 'Llista: Afegeix un element a la llista',
	'BUMP_ERROR'				=> 'No pots reactivar aquest tema tan aviat després del darrer missatge.',

	'CANNOT_DELETE_REPLIED'		=> 'Només pots eliminar missatges als quals encara no s’ha respost.',
	'CANNOT_EDIT_POST_LOCKED'	=> 'Aquest missatge està blocat. Ja no pots editar-lo.',
	'CANNOT_EDIT_TIME'			=> 'Ja no pots editar ni eliminar aquest missatge.',
	'CANNOT_POST_ANNOUNCE'		=> 'No pots publicar avisos.',
	'CANNOT_POST_STICKY'		=> 'No pots publicar temes recurrents.',
	'CHANGE_TOPIC_TO'			=> 'Canvia el tipus del tema a',
	'CHARS_POST_CONTAINS'		=> array(
		1	=> 'El missatge conté %1$d caràcter.',
		2	=> 'El missatge conté %1$d caràcters.',
	),
	'CHARS_SIG_CONTAINS'		=> array(
		1	=> 'La signatura conté %1$d caràcter.',
		2	=> 'La signatura conté %1$d caràcters.',
	),
	'CLOSE_TAGS'				=> 'Tanca les etiquetes',
	'CURRENT_TOPIC'				=> 'Tema actual',

	'DELETE_FILE'				=> 'Elimina el fitxer',
	'DELETE_MESSAGE'			=> 'Elimina el missatge',
	'DELETE_MESSAGE_CONFIRM'	=> 'Segur que vols eliminar aquest missatge?',
	'DELETE_OWN_POSTS'			=> 'Només pots eliminar els teus missatges.',
	'DELETE_PERMANENTLY'		=> 'Elimina’l permanentment',
	'DELETE_POST_CONFIRM'		=> 'Segur que vols eliminar aquest missatge?',
	'DELETE_POST_PERMANENTLY_CONFIRM'	=> 'Segur que vols eliminar aquest missatge <strong>permanentment</strong>?',
	'DELETE_POST_PERMANENTLY'	=> array(
		1	=> 'Elimina aquest missatge de forma permanent sense que es pugui recuperar',
		2	=> 'Elimina %1$d missatges de forma permanent sense que es puguin recuperar',
	),
	'DELETE_POSTS_CONFIRM'		=> 'Segur que vols eliminar aquests missatges?',
	'DELETE_POSTS_PERMANENTLY_CONFIRM'	=> 'Segur que vols eliminar aquests missatges <strong>permanentment</strong>?',
	'DELETE_REASON'				=> 'Motiu de l’eliminació',
	'DELETE_REASON_EXPLAIN'		=> 'El motiu que especifiquis per a l’eliminació serà visible per als moderadors.',
	'DELETE_POST_WARN'			=> 'Elimina aquest missatge',
	'DELETE_TOPIC_CONFIRM'		=> 'Segur que vols eliminar aquest tema?',
	'DELETE_TOPIC_PERMANENTLY'	=> array(
		1	=> 'Elimina aquest tema de forma permanent sense que es pugui recuperar',
		2	=> 'Elimina %1$d temes de forma permanent sense que es pugui recuperar',
	),
	'DELETE_TOPIC_PERMANENTLY_CONFIRM'	=> 'Segur que vols eliminar aquest tema <strong>permanentment</strong>?',
	'DELETE_TOPICS_CONFIRM'		=> 'Segur que vols eliminar aquests temes?',
	'DELETE_TOPICS_PERMANENTLY_CONFIRM'	=> 'Segur que vols eliminar aquests temes <strong>permanentment</strong>?',
	'DISABLE_BBCODE'			=> 'Desactiva el BBCode',
	'DISABLE_MAGIC_URL'			=> 'No transformis automàticament els enllaços',
	'DISABLE_SMILIES'			=> 'Desactiva les emoticones',
	'DISALLOWED_CONTENT'		=> 'El fitxer a penjar ha estat rebutjat perquè s’ha identificat com a possible vector d’atac.',
	'DISALLOWED_EXTENSION'		=> 'L’extensió %s no està permesa.',
	'DRAFT_LOADED'				=> 'L’esborrany s’ha carregat a l’àrea d’entrades. Si ho desitgeu, ara podeu acabar l’entrada.<br />L’esborrany s’eliminarà després d’enviar l’entrada.',
	'DRAFT_LOADED_PM'			=> 'L’esborrany s’ha carregat a l’àrea de missatges privats. Si ho desitgeu, ara podeu acabar el missatge privat.<br />L’esborrany s’eliminarà després d’enviar el missatge privat.',
	'DRAFT_SAVED'				=> 'L’esborrany s’ha desat correctament.',
	'DRAFT_TITLE'				=> 'Títol de l’esborrany',

	'EDIT_REASON'				=> 'Motiu pel qual edites el missatge',
	'EMPTY_FILEUPLOAD'			=> 'El fitxer penjat és buit.',
	'EMPTY_MESSAGE'				=> 'Quan envies un missatge, cal que hi introdueixis un text.',
	'EMPTY_REMOTE_DATA'			=> 'No s’ha pogut penjar el fitxer, prova de penjar-lo manualment.',

	'FLASH_IS_OFF'				=> '[flash] està <em>INACTIU</em>',
	'FLASH_IS_ON'				=> '[flash] està <em>ACTIU</em>',
	'FLOOD_ERROR'				=> 'No pots publicar un altre missatge tan poc temps després de l’anterior.',
	'FONT_COLOR'				=> 'Color de la lletra',
	'FONT_COLOR_HIDE'			=> 'Amaga el color de la lletra',
	'FONT_HUGE'					=> 'Enorme',
	'FONT_LARGE'				=> 'Gran',
	'FONT_NORMAL'				=> 'Normal',
	'FONT_SIZE'					=> 'Mida de la lletra',
	'FONT_SMALL'				=> 'Petita',
	'FONT_TINY'					=> 'Minúscula',

	'GENERAL_UPLOAD_ERROR'		=> 'No s’ha pogut penjar el fitxer adjunt a  %s.',

	'IMAGES_ARE_OFF'			=> '[img] està <em>INACTIU</em>',
	'IMAGES_ARE_ON'				=> '[img] està <em>ACTIU</em>',
	'INVALID_FILENAME'			=> '%s és un nom de fitxer no vàlid.',

	'LOAD'						=> 'Carrega',
	'LOAD_DRAFT'				=> 'Carrega l’esborrany',
	'LOAD_DRAFT_EXPLAIN'		=> 'Aquí podeu seleccionar un esborrany que volgueu continuar escrivint. L’entrada actual es cancel·larà, el contingut de l’entrada actual s’eliminarà. Podeu visualitzar, editar i eliminar esborranys al vostre Tauler de control de l’usuari.',
	'LOGIN_EXPLAIN_BUMP'		=> 'Cal que iniciïs la sessió per a reactivar temes en aquest fòrum.',
	'LOGIN_EXPLAIN_DELETE'		=> 'Cal que iniciïs la sessió per a eliminar missatges en aquest fòrum.',
	'LOGIN_EXPLAIN_SOFT_DELETE'	=> 'Cal que iniciïs la sessió per a marcar missatges com a eliminats en aquest fòrum.',
	'LOGIN_EXPLAIN_POST'		=> 'Cal que iniciïs la sessió per a publicar missatges en aquest fòrum.',
	'LOGIN_EXPLAIN_QUOTE'		=> 'Cal que iniciïs la sessió per a citar missatges en aquest fòrum.',
	'LOGIN_EXPLAIN_REPLY'		=> 'Cal que iniciïs la sessió per a publicar respostes en aquest fòrum.',

	'MAX_ATTACHMENT_FILESIZE'	=> 'Mida màxima de fitxer per penjada: %s.',
	'MAX_FONT_SIZE_EXCEEDED'	=> 'Pots utilitzar lletres de mida %d com a màxim.',
	'MAX_FLASH_HEIGHT_EXCEEDED'	=> array(
		1	=> 'Els fitxers flash poden tenir un màxim d’%d píxel d’alçària.',
		2	=> 'Els fitxers flash poden tenir un màxim de %d píxels d’alçària.',
	),
	'MAX_FLASH_WIDTH_EXCEEDED'	=> array(
		1	=> 'Els fitxers flash poden tenir un màxim d’%d píxel d’amplària.',
		2	=> 'Els fitxers flash poden tenir un màxim de %d píxels d’amplària.',
	),
	'MAX_IMG_HEIGHT_EXCEEDED'	=> array(
		1	=> 'Les imatges tenen un màxim d’%d píxel d’alçada.',
		2	=> 'Les imatges tenen un màxim de %d píxels d’alçada.',
	),
	'MAX_IMG_WIDTH_EXCEEDED'	=> array(
		1	=> 'Les imatges tenen un màxim d’%d píxel d’amplada.',
		2	=> 'Les imatges tenen un màxim de %d píxels d’amplada.',
	),

	'MESSAGE_BODY_EXPLAIN'		=> array(
		0	=> '', // zero means no limit, so we don't view a message here.
		1	=> 'Introdueix el missatge aquí. No pot contenir més d’<strong>%d</strong> caràcter.',
		2	=> 'Introduelx el missatge aquí. No pot contenir més de <strong>%d</strong> caràcters.',
	),
	'MESSAGE_DELETED'			=> 'El missatge s’ha eliminat correctament.',
	'MORE_SMILIES'				=> 'Mostra més emoticones',

	'NOTIFY_REPLY'				=> 'Avisa’m quan hi hagi una resposta',
	'NOT_UPLOADED'				=> 'No s’ha pogut penjar el fitxer.',
	'NO_DELETE_POLL_OPTIONS'	=> 'No pots eliminar les opcions de l’enquesta si ja existeixen.',
	'NO_PM_ICON'				=> 'Sense icona MP',
	'NO_POLL_TITLE'				=> 'Cal que introdueixis un títol per a l’enquesta.',
	'NO_POST'					=> 'El missatge sol·licitat no existeix.',
	'NO_POST_MODE'				=> 'No s’ha especificat mode de publicació.',
	'NO_TEMP_DIR'				=> 'No s’ha trobat la carpeta temporal o no s’hi pot escriure.',

	'PHP_UPLOAD_STOPPED'		=> 'Una extensió del PHP ha aturat la penjada del fitxer.',
	'PARTIAL_UPLOAD'			=> 'El fitxer penjat només s’ha transmès parcialment.',
	'PHP_SIZE_NA'				=> 'La mida del fitxer adjunt és massa gran.<br />No s’ha pogut determinar la mida màxima definida pel PHP a php.ini.',
	'PHP_SIZE_OVERRUN'			=> 'La mida del fitxer adjunt és massa gran, la mida màxima de les penjades és %1$d %2$s.<br />Si us plau, tingueu en compte que això es defineix al fitxer php.ini i no es pot sobreescriure.',
	'PLACE_INLINE'				=> 'Situa’l en línia',
	'POLL_DELETE'				=> 'Elimina l’enquesta',
	'POLL_FOR'					=> 'Durada de l’enquesta',
	'POLL_FOR_EXPLAIN'			=> 'Introdueix-hi un 0 perquè no s’acabi mai.',
	'POLL_MAX_OPTIONS'			=> 'Opcions per usuari',
	'POLL_MAX_OPTIONS_EXPLAIN'	=> 'Nombre d’opcions que cada usuari pot seleccionar quan vota.',
	'POLL_OPTIONS'				=> 'Opcions de l’enquesta',
	'POLL_OPTIONS_EXPLAIN'		=> array(
		1	=> 'Situa cada opció en una línia nova. Pots introduir-hi <strong>%d</strong> opció.',
		2	=> 'Situa cada opció en una línia nova. Pots introduir-hi fins a <strong>%d</strong> opcions.',
	),
	'POLL_OPTIONS_EDIT_EXPLAIN'		=> array(
		1	=> 'Situa cada opció en una línia nova. Pots introduir-hi <strong>%d</strong> opció. Si hi afegeixes o elimines opcions, els vots existents es reinicialitzaran.',
		2	=> 'Situa cada opció en una línia nova. Pots introduir-hi fins a <strong>%d</strong> opcions. Si hi afegeixes o elimines opcions, els vots existents es reinicialitzaran.',
	),
	'POLL_QUESTION'				=> 'Títol de l’enquesta',
	'POLL_TITLE_TOO_LONG'		=> 'El títol de l’enquesta ha de tenir menys de 100 caràcters.',
	'POLL_TITLE_COMP_TOO_LONG'	=> 'El títol de l’enquesta és massa gran, prova d’eliminar-ne BBCodes o emoticones.',
	'POLL_VOTE_CHANGE'			=> 'Permet el canvi de vot',
	'POLL_VOTE_CHANGE_EXPLAIN'	=> 'Si ho actives, els usuaris podran canviar el vot després d’haver votat.',
	'POSTED_ATTACHMENTS'		=> 'Fitxers adjunts enviats',
	'POST_APPROVAL_NOTIFY'		=> 'Rebràs un avís quan el missatge hagi estat aprovat.',
	'POST_CONFIRMATION'			=> 'Confirmació del missatge',
	'POST_CONFIRM_EXPLAIN'		=> 'Per tal de prevenir entrades automàtiques, cal que introdueixis un codi de confirmació. El codi es mostra a la imatge que veus a sota. Si tens problemes de visió o per alguna raó no pots llegir aquest codi, posa’t en contacte amb l’%sadministrador del fòrum%s.',
	'POST_DELETED'				=> 'S’ha eliminat el missatge correctament.',
	'POST_EDITED'				=> 'S’ha editat el missatge correctament.',
	'POST_EDITED_MOD'			=> 'S’ha editat el missatge correctament, però cal que l’aprovi un moderador abans que sigui visible públicament.',
	'POST_GLOBAL'				=> 'Global',
	'POST_ICON'					=> 'Icona del missatge',
	'POST_NORMAL'				=> 'Normal',
	'POST_REVIEW'				=> 'Revisió del missatge',
	'POST_REVIEW_EDIT'			=> 'Revisió del missatge',
	'POST_REVIEW_EDIT_EXPLAIN'	=> 'Aquest missatge ha estat modificat per un altre usuari mentre l’editaves. És possible que vulguis revisar la versió actual del missatge per a ajustar la teva edició.',
	'POST_REVIEW_EXPLAIN'		=> 'S’ha publicat com a mínim un missatge nou en aquest tema. És possible que vulguis revisar el teu missatge.',
	'POST_STORED'				=> 'S’ha publicat el missatge correctament.',
	'POST_STORED_MOD'			=> 'S’ha publicat el missatge correctament, però cal que l’aprovi un moderador abans que sigui visible públicament.',
	'POST_TOPIC_AS'				=> 'Publica el tema com a',
	'PROGRESS_BAR'				=> 'Barra de progrés',

	'QUOTE_DEPTH_EXCEEDED'		=> array(
		1	=> 'Només pots incrustar %d nivell de citacions.',
		2	=> 'Només pots incrustar %d nivells de citacions.',
	),
	'QUOTE_NO_NESTING'			=> 'No pots incrustar cap citació dintre d’una citació.',

	'REMOTE_UPLOAD_TIMEOUT'		=> 'No s’ha pogut penjar el fitxer especificat perquè s’ha excedit el temps d’espera de la sol·licitud.',
	'SAVE'						=> 'Desa',
	'SAVE_DATE'					=> 'Desa a',
	'SAVE_DRAFT'				=> 'Desa un esborrany',
	'SAVE_DRAFT_CONFIRM'		=> 'Tingueu en compte que els esborranys només inclouen l’assumpte i el missatge, qualsevol altre element serà eliminat. Voleu desar l’esborrany ara?',
	'SMILIES'					=> 'Emoticones',
	'SMILIES_ARE_OFF'			=> 'Les emoticones estan <em>INACTIVES</em>',
	'SMILIES_ARE_ON'			=> 'Les emoticones estan <em>ACTIVES</em>',
	'STICKY_ANNOUNCE_TIME_LIMIT'=> 'Límit de temps del Tema recurrent/Avís/Global',
	'STICK_TOPIC_FOR'			=> 'Mostra’l com a Tema recurrent durant',
	'STICK_TOPIC_FOR_EXPLAIN'	=> 'Introdueix-hi un 0 perquè sigui un Tema recurrent/Avís/Global per sempre. Tingues en compte que aquest número és relatiu a la data del missatge.',
	'STYLES_TIP'				=> 'Consell: Pots aplicar estils ràpidament al text seleccionat.',

	'TOO_FEW_CHARS'				=> 'El missatge té massa pocs caràcters.',
	'TOO_FEW_CHARS_LIMIT'		=> array(
		1	=> 'Cal que introdueixis, com a mínim, %1$d caràcter.',
		2	=> 'Cal que introdueixis, com a mínim, %1$d caràcters.',
	),
	'TOO_FEW_POLL_OPTIONS'		=> 'Com a mínim has d’introduir dues opcions a l’enquesta.',
	'TOO_MANY_ATTACHMENTS'		=> 'No pots afegir un altre fitxer adjunt, el màxim és %d.',
	'TOO_MANY_CHARS'			=> 'El missatge té massa caràcters.',
	'TOO_MANY_CHARS_LIMIT'		=> array(
		2	=> 'El nombre màxim de caràcters permesos és %2$d.',
	),
	'TOO_MANY_POLL_OPTIONS'		=> 'Has introduït massa opcions a l’enquesta.',
	'TOO_MANY_SMILIES'			=> 'El missatge té massa emoticones. El nombre màxim d’emoticones permeses és %d.',
	'TOO_MANY_URLS'				=> 'El missatge té massa URLs. El nombre màxim d’URLs permesos és %d.',
	'TOO_MANY_USER_OPTIONS'		=> 'No pots especificar més opcions per usuari que les que té l’enquesta.',
	'TOPIC_BUMPED'				=> 'S’ha reactivat el tema correctament.',

	'UNAUTHORISED_BBCODE'		=> 'No pots utilitzar determinats BBCodes: %s.',
	'UNSUPPORTED_CHARACTERS_MESSAGE'	=> 'El missatge conté els següents caràcters no permesos:<br />%s',
	'UNSUPPORTED_CHARACTERS_SUBJECT'	=> 'El títol conté els següents caràcters no permesos:<br />%s',
	'UPDATE_COMMENT'			=> 'Actualitza el comentari',
	'URL_INVALID'				=> 'L’URL que has especificat no és vàlid.',
	'URL_NOT_FOUND'				=> 'El fitxer que has especificat no és vàlid.',
	'URL_IS_OFF'				=> '[url] està <em>INACTIU</em>',
	'URL_IS_ON'					=> '[url] està <em>ACTIU</em>',
	'USER_CANNOT_BUMP'			=> 'No pots reactivar temes en aquest fòrum.',
	'USER_CANNOT_DELETE'		=> 'No pots eliminar missatges en aquest fòrum.',
	'USER_CANNOT_EDIT'			=> 'No pots editar missatges en aquest fòrum.',
	'USER_CANNOT_REPLY'			=> 'No pots repondre en aquest fòrum.',
	'USER_CANNOT_FORUM_POST'	=> 'No pots fer operacions d’enviament en aquest fòrum perquè el tipus de fòrum no ho permet.',

	'VIEW_MESSAGE'				=> '%sMostra el missatge enviat%s',
	'VIEW_PRIVATE_MESSAGE'		=> '%sMostra el missatge privat enviat%s',

	'WRONG_FILESIZE'			=> 'El fitxer és massa gran, la mida màxima permesa és %1$d %2$s.',
	'WRONG_SIZE'				=> 'La imatge ha de tenir com a mínim %1$s d’amplada, %2$s d’alçada i com a màxim %3$s d’amplada i %4$s d’alçada. La imatge que has enviat té %5$s d’amplada i %6$s d’alçada.',
));
