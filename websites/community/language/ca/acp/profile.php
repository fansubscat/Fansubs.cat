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

// Custom profile fields
$lang = array_merge($lang, array(
	'ADDED_PROFILE_FIELD'	=> 'S’ha afegit el camp personalitzat del perfil correctament.',
	'ALPHA_DOTS'			=> 'Només alfanumèrics i punts',
	'ALPHA_ONLY'			=> 'Només alfanumèrics',
	'ALPHA_SPACERS'			=> 'Alfanumèrics i caràcters d’espaiat',
	'ALPHA_UNDERSCORE'		=> 'Alfanumèrics i caràcters de subratllat',
	'ALPHA_PUNCTUATION'		=> 'Alfanumèrics amb comes, punts, caràcters de subratllat i guions que comencin amb una lletra',
	'ALWAYS_TODAY'			=> 'Sempre la data actual',

	'BOOL_ENTRIES_EXPLAIN'	=> 'Introduïu les opcions ara',
	'BOOL_TYPE_EXPLAIN'		=> 'Definiu el tipus, o bé una casella de selecció o bé botons d’opció. Amb la casella de selecció, només es mostrarà per a un usuari si està activada. En aquest cas,  s’usarà la <strong>segona</strong> opció d’idioma. Els botons d’opció es mostraran sense importar el seu valor.',

	'CHANGED_PROFILE_FIELD'		=> 'S’ha canviat el camp del perfil correctament.',
	'CHARS_ANY'					=> 'Qualsevol caràcter',
	'CHECKBOX'					=> 'Casella de selecció',
	'COLUMNS'					=> 'Columnes',
	'CP_LANG_DEFAULT_VALUE'		=> 'Valor per defecte',
	'CP_LANG_EXPLAIN'			=> 'Descripció del camp',
	'CP_LANG_EXPLAIN_EXPLAIN'	=> 'L’explicació presentada a l’usuari per aquest camp.',
	'CP_LANG_NAME'				=> 'Nom/títol del camp presentat a l’usuari',
	'CP_LANG_OPTIONS'			=> 'Opcions',
	'CREATE_NEW_FIELD'			=> 'Crea un camp nou',
	'CUSTOM_FIELDS_NOT_TRANSLATED'	=> 'Com a mínim un camp personalitzat del perfil encara no està traduït. Introduïu la informació necessària fent clic a l’enllaç “Tradueix”.',

	'DEFAULT_ISO_LANGUAGE'			=> 'Idioma per defecte [%s]',
	'DEFAULT_LANGUAGE_NOT_FILLED'	=> 'Les entrades d’idioma d’aquest camp del perfil no existeixen a l’idioma per defecte.',
	'DEFAULT_VALUE'					=> 'Valor per defecte',
	'DELETE_PROFILE_FIELD'			=> 'Elimina el camp del perfil',
	'DELETE_PROFILE_FIELD_CONFIRM'	=> 'Esteu segur que voleu eliminar aquest camp del perfil?',
	'DISPLAY_AT_PROFILE'			=> 'Mostra’l al Tauler de control de l’usuari',
	'DISPLAY_AT_PROFILE_EXPLAIN'	=> 'Els usuaris poden canviar aquest camp del perfil des del Tauler de control de l’usuari.',
	'DISPLAY_AT_REGISTER'			=> 'Mostra’l a la pantalla de registre',
	'DISPLAY_AT_REGISTER_EXPLAIN'	=> 'Si aquesta opció està habilitada, el camp es mostrarà durant el registre d’usuaris.',
	'DISPLAY_ON_MEMBERLIST'			=> 'Mostra’l a la pantalla de la llista de membres',
	'DISPLAY_ON_MEMBERLIST_EXPLAIN'	=> 'Si aquesta opció està habilitada, el camp es mostrarà a les files d’usuaris a la pantalla de la llista de membres',
	'DISPLAY_ON_PM'					=> 'Mostra’l a la pantalla de visualització de missatges privats',
	'DISPLAY_ON_PM_EXPLAIN'			=> 'Si aquesta opció està habilitada, el camp es mostrarà al mini-perfil a la pantalla de visualització de missatges privats',
	'DISPLAY_ON_VT'					=> 'Mostra’l a la pantalla d’entrades',
	'DISPLAY_ON_VT_EXPLAIN'			=> 'Si aquesta opció està habilitada, el camp es mostrarà al miniperfil a la pantalla d’entrades.',
	'DISPLAY_PROFILE_FIELD'			=> 'Mostra el camp del perfil publicament',
	'DISPLAY_PROFILE_FIELD_EXPLAIN'	=> 'El camp del perfil es mostrarà en totes les ubicacions permeses a la configuració de càrrega. Si trieu “No”, el camp s’ocultarà a les pàgines de tema, als perfils i a la llista de membres.',
	'DROPDOWN_ENTRIES_EXPLAIN'		=> 'Introduïu les opcions ara, cadascuna en una línia.',

	'EDIT_DROPDOWN_LANG_EXPLAIN'	=> 'Tingueu en compte que podeu canviar el text de les opcions i també afegir opcions noves al final. No és recomanable afegir opcions noves entre opcions existents - això podria tenir com a resultat que s’assignessin opcions incorrectes als usuaris. Això també pot passar si elimineu opcions del mig. Si elimineu opcions del final, farà que als usuaris que les hagin seleccionat els quedi assignada l’opció per defecte.',
	'EMPTY_FIELD_IDENT'				=> 'Identificador del camp buit',
	'EMPTY_USER_FIELD_NAME'			=> 'Introduïu un nom/títol per al camp',
	'ENTRIES'						=> 'Entrades',
	'EVERYTHING_OK'					=> 'Tot correcte',

	'FIELD_BOOL'				=> 'Booleà (Sí/No)',
	'FIELD_CONTACT_DESC'		=> 'Descripció de contacte',
	'FIELD_CONTACT_URL'			=> 'Enllaç de contacte',
	'FIELD_DATE'				=> 'Data',
	'FIELD_DESCRIPTION'			=> 'Descripció del camp',
	'FIELD_DESCRIPTION_EXPLAIN'	=> 'L’explicació presentada a l’usuari per aquest camp.',
	'FIELD_DROPDOWN'			=> 'Desplegable',
	'FIELD_IDENT'				=> 'Identificador del camp',
	'FIELD_IDENT_ALREADY_EXIST'	=> 'L’identificador que heu escollit ja existeix. Trieu un altre nom.',
	'FIELD_IDENT_EXPLAIN'		=> 'L’identificador del camp és un nom que identifica el camp del perfil a la base de dades i les plantilles.',
	'FIELD_INT'					=> 'Números',
	'FIELD_IS_CONTACT'			=> 'Mostra el camp com un camp de contacte',
	'FIELD_IS_CONTACT_EXPLAIN'	=> 'Els camps de contacte es mostren a la secció de contacte del perfil de l’usuari i es mostren de forma diferent al mini-perfil al costat de les entrades i els missatges privats. Podeu usar <samp>%s</samp> com a text variable que se substituirà per un valor proporcionat per l’usuari.',
	'FIELD_LENGTH'				=> 'Longitud de la casella de text',
	'FIELD_NOT_FOUND'			=> 'No s’ha trobat el camp del perfil.',
	'FIELD_STRING'				=> 'Camp de text simple',
	'FIELD_TEXT'				=> 'Àrea de text',
	'FIELD_TYPE'				=> 'Tipus de camp',
	'FIELD_TYPE_EXPLAIN'		=> 'No es pot canviar el tipus de camp més endavant.',
	'FIELD_URL'					=> 'URL (enllaç)',
	'FIELD_VALIDATION'			=> 'Validació del camp',
	'FIRST_OPTION'				=> 'Primera opció',

	'HIDE_PROFILE_FIELD'			=> 'Oculta el camp del perfil',
	'HIDE_PROFILE_FIELD_EXPLAIN'	=> 'Amaga el camp del perfil per a tots els usuaris exceptuant els administradors i els moderadors, que també el poden veure. Si l’opció Mostra’l al Tauler de control de l’usuari està inhabilitada, l’usuari no podrà veure o canviar el camp i aquest només el podran canviar els administradors.',

	'INVALID_CHARS_FIELD_IDENT'	=> 'L’identificador del camp només pot contenir lletres en minúscula a-z i _',
	'INVALID_FIELD_IDENT_LEN'	=> 'L’identificador del camp només pot tenir 17 caràcters',
	'ISO_LANGUAGE'				=> 'Idioma [%s]',

	'LANG_SPECIFIC_OPTIONS'		=> 'Opcions específiques de l’idioma [<strong>%s</strong>]',

	'LETTER_NUM_DOTS'			=> 'Qualsevol lletra, números i punts',
	'LETTER_NUM_ONLY'			=> 'Qualsevol lletra i números',
	'LETTER_NUM_PUNCTUATION'	=> 'Qualsevol lletra, números, comes, punts, caràcters de subrratllat i guions que comencin amb una lletra',
	'LETTER_NUM_SPACERS'		=> 'Qualsevol lletra, números i caràcters d’espaiat',
	'LETTER_NUM_UNDERSCORE'		=> 'Qualsevol lletra, números i caràcters de subrratllat',

	'MAX_FIELD_CHARS'		=> 'Nombre màxim de caràcters',
	'MAX_FIELD_NUMBER'		=> 'Número màxim permès',
	'MIN_FIELD_CHARS'		=> 'Nombre mínim de caràcters',
	'MIN_FIELD_NUMBER'		=> 'Número mínim permès',

	'NO_FIELD_ENTRIES'			=> 'No s’ha definit cap entrada',
	'NO_FIELD_ID'				=> 'No s’ha definit l’id del camp.',
	'NO_FIELD_TYPE'				=> 'No s’ha definit el tipus del camp.',
	'NO_VALUE_OPTION'			=> 'Opció equivalent a valor no introduït',
	'NO_VALUE_OPTION_EXPLAIN'	=> 'Valor per a una entrada nul·la. Si aquest camp és necessari, l’usuri rebrà un error si tria l’opció seleccionada aquí.',
	'NUMBERS_ONLY'				=> 'Només números (0-9)',

	'PROFILE_BASIC_OPTIONS'		=> 'Opcions bàsiques',
	'PROFILE_FIELD_ACTIVATED'	=> 'S’ha activat el camp del perfil correctament.',
	'PROFILE_FIELD_DEACTIVATED'	=> 'S’ha desactivat el camp del perfil correctament.',
	'PROFILE_LANG_OPTIONS'		=> 'Opcions específiques d’idioma',
	'PROFILE_TYPE_OPTIONS'		=> 'Opcions específiques del tipus de camp del perfil',

	'RADIO_BUTTONS'				=> 'Botons d’opció',
	'REMOVED_PROFILE_FIELD'		=> 'S’ha eliminat el camp del perfil correctament.',
	'REQUIRED_FIELD'			=> 'Camp necessari',
	'REQUIRED_FIELD_EXPLAIN'	=> 'Obliga l’usuari o administrador a omplir o especificar el camp del perfil. Si l’opció Mostra’l a la pantalla de registre està inhabilitada, el camp només es requerirà quan l’usuari editi el seu perfil.',
	'ROWS'						=> 'Files',

	'SAVE'							=> 'Desa',
	'SECOND_OPTION'					=> 'Segona opció',
	'SHOW_NOVALUE_FIELD'			=> 'Mostra el camp si no s’ha seleccionat cap valor',
	'SHOW_NOVALUE_FIELD_EXPLAIN'	=> 'Determina si cal mostrar el camp del perfil quan no s’ha seleccionat cap valor per camps opcional o quan encara no s’ha selccionat cap valor per camps obligatoris.',
	'STEP_1_EXPLAIN_CREATE'			=> 'Aquí podeu introduir els primers paràmetres bàsics del nou camp del perfil. Aquesta informació és necessària per al segon pas on podreu definir la resta d’opcions i ajustar el perfil amb més detall.',
	'STEP_1_EXPLAIN_EDIT'			=> 'Aquí podeu canviar els paràmetres bàsics del camp del perfil. Les opcions rellevants es recalculen al segon pas.',
	'STEP_1_TITLE_CREATE'			=> 'Afegeix un camp del perfil',
	'STEP_1_TITLE_EDIT'				=> 'Edita el camp del perfil',
	'STEP_2_EXPLAIN_CREATE'			=> 'Aquí podeu definir algunes opcions comuns que podeu modificar.',
	'STEP_2_EXPLAIN_EDIT'			=> 'Aquí podeu canviar algunes opcions comuns.<br /><strong>Tingueu en compte que els canvis als camps del perfil no afectaran els camps del perfil existents introduïts pels usuaris.</strong>',
	'STEP_2_TITLE_CREATE'			=> 'Opcions específiques del tipus de camp del perfil',
	'STEP_2_TITLE_EDIT'				=> 'Opcions específiques del tipus de camp del perfil',
	'STEP_3_EXPLAIN_CREATE'			=> 'Com que teniu més d’un dioma instal·lat al fòrum, també heu d’omplir els elements d’idioma restants. Si no ho feu, s’usarà l’idioma per defecte per aquest camp presonalitzat del prefil, també podeu omplir els elements d’idioma restants més tard.',
	'STEP_3_EXPLAIN_EDIT'			=> 'Com que teniu més d’un dioma instal·lat al fòrum, podeu canviar o afegir els elements d’idioma restants. Si no ho feu, s’usarà l’idioma per defecte per aquest camp presonalitzat del prefil.',
	'STEP_3_TITLE_CREATE'			=> 'Definicions d’idioma restants',
	'STEP_3_TITLE_EDIT'				=> 'Definicions d’idioma',
	'STRING_DEFAULT_VALUE_EXPLAIN'	=> 'Introduïu la frase que es mostrarà per defecte. Deixeu-la buida si voleu que no es mostri res al principi.',

	'TEXT_DEFAULT_VALUE_EXPLAIN'	=> 'Introduïu el text que es mostrarà per defecte. Deixeu-lo buit si voleu que no es mostri res al principi.',
	'TRANSLATE'						=> 'Tradueix',

	'USER_FIELD_NAME'	=> 'Nom/títol del camp presentat a l’usuari',

	'VISIBILITY_OPTION'				=> 'Opcions de visibilitat',
));
