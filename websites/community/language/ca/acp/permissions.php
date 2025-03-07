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
	'ACP_PERMISSIONS_EXPLAIN'	=> '
		<p>Els permisos són altament granulars i estan agrupats en quatre seccions principals que són:</p>

		<h2>Permisos globals</h2>
		<p>S’utilitzen per controlar l’accés a nivell global i apliquen a tot el fòrum. Es divideixen posteriorment en permisos d’usuaris, permisos de grups, d’administradors i de moderadors globals.</p>

		<h2>Permisos per fòrums</h2>
		<p>S’utilitzen per controlar l’accés a fòrums concrets. Es divideixen en permisos de fòrums, moderadors de fòrums, permisos d’usuari per fòrums i permisos de grups per fòrums.</p>

		<h2>Rols de permisos</h2>
		<p>S’utilitzen per crear grups diferents de permisos per poder assignar els diferents tipus de permisos segons els rols. Els rols per defecte probablement cobreixen l’administració de fòrums grans i petits, però dintre de cadascuna de les quatre divisions podeu afegir, editar i eliminar rols com més us convingui.</p>

		<h2>Màscares de permisos</h2>
		<p>S’utilitzen per veure els permisos efectius assignats als usuaris, moderadors (locals i globals), administradors i fòrums.</p>

		<br />

		<p>Per obtenir més informació sobre com definir i gestionar els permisos del fòrum phpBB3, vegeu la secció de <a href="https://www.phpbb.com/support/docs/en/3.3/ug/quickstart/permissions/">Configuració de permisos de la Guia d’inici ràpid</a>.</p>
	',

	'ACL_NEVER'				=> 'Mai',
	'ACL_SET'				=> 'Definició de permisos',
	'ACL_SET_EXPLAIN'		=> 'Els permisos es basen en un sistema simple de tipus <strong>Sí</strong>/<strong>No</strong>. Si trieu l’opció <strong>Mai</strong> per a un usuari o grup d’usuaris, sobreescriu qualsevol altre valor que se li assigni. Si no voleu assignar un valor per una opció per aquest usuari o grup d’usuaris, seleccioneu <strong>No</strong>. Si s’assignen valors per aquesta opció en un altre lloc, s’usaran preferentment, si no s’assumeix el valor <strong>Mai</strong>. Tots els objectes marcats (amb la casella de selecció del davant) copiaran el grup de permisos que heu definit. Tingueu en compte que donar permisos administratius a comptes de fundadors no té cap efecte ja que aquests sempre tenen la configuració de permisos administratius a <strong>Sí</strong>.',
	'ACL_SETTING'			=> 'Configuració',

	'ACL_TYPE_A_'			=> 'Permisos administratius',
	'ACL_TYPE_F_'			=> 'Permisos del fòrum',
	'ACL_TYPE_M_'			=> 'Permisos de moderació',
	'ACL_TYPE_U_'			=> 'Permisos d’usuari',

	'ACL_TYPE_GLOBAL_A_'	=> 'Permisos administratius',
	'ACL_TYPE_GLOBAL_U_'	=> 'Permisos d’usuari',
	'ACL_TYPE_GLOBAL_M_'	=> 'Permisos de moderador global',
	'ACL_TYPE_LOCAL_M_'		=> 'Permisos de moderador del fòrum',
	'ACL_TYPE_LOCAL_F_'		=> 'Permisos del fòrum',

	'ACL_NO'				=> 'No',
	'ACL_VIEW'				=> 'Viewing permissions',
	'ACL_VIEW_EXPLAIN'		=> 'Aquí podeu veure els permisos efectius que té l’usuari/grup. Un quadrat roig indica que l’usuari/grup no té el permís, un quadrat verd indica l’usuari/grup sí que té el permís.',
	'ACL_YES'				=> 'Sí',

	'ACP_ADMINISTRATORS_EXPLAIN'				=> 'Aquí podeu assignar permisos d’administrador a usuaris o grups. Tots els usuaris amb permisos d’administrador poden veure el Tauler de control de l’administrador.',
	'ACP_FORUM_MODERATORS_EXPLAIN'				=> 'Aquí podeu assignar permisos de moderador de fòrum a usuaris o grups. Per definir l’accés als fòrums, per definir moderadors globals o administradors utilitzeu la pàgina apropiada.',
	'ACP_FORUM_PERMISSIONS_EXPLAIN'				=> 'Aquí podeu modificar quins usuaris i grups poden accedir a quins fòrums. Per assignar moderadors o definir administradors utilitzeu la pàgina apropiada.',
	'ACP_FORUM_PERMISSIONS_COPY_EXPLAIN'		=> 'Aquí podeu copiar els permisos d’un fòrum a un altre o altres.',
	'ACP_GLOBAL_MODERATORS_EXPLAIN'				=> 'Aquí podeu assignar permisos de moderador global a usuaris o grups. Aquests moderadors són com els moderadors normals amb l’excepcció que tenen accés a tots els fòrums.',
	'ACP_GROUPS_FORUM_PERMISSIONS_EXPLAIN'		=> 'Aquí podeu assignar permisos de fòrums a grups.',
	'ACP_GROUPS_PERMISSIONS_EXPLAIN'			=> 'Aquí podeu assignar permisos globals a grups - permisos d’usuari, permisos de moderador global i permisos d’administrador. Els permisos d’usuari inclouen capacitats com ara l’ús d’avatars, l’enviament de missatges privats, etc. Els permisos de moderador global inclouen l’aprovació d’entrades, la gestió de temes, la gestió de bandejos, etc. Finalment, els permisos d’administrador inclouen la modificació de permisos, la definició de BBCodes personalitzats, la gestió de fòrums, etc. És recomanable que els permisos d’usuaris individuals només es canviïn en casos excepcionals. El mètode preferit és posar els usuaris en grups i assignar-los els permisos del grup.',
	'ACP_ADMIN_ROLES_EXPLAIN'					=> 'Aquí podeu gestionar els rols per als permisos administratius. Els rols són permisos efectius. Si canvieu un rol, també canviaran els permisos dels elements que tinguin aquest rol assignat.',
	'ACP_FORUM_ROLES_EXPLAIN'					=> 'Aquí podeu gestionar els rols per als permisos de fòrums. Els rols són permisos efectius. Si canvieu un rol, també canviaran els permisos dels elements que tinguin aquest rol assignat.',
	'ACP_MOD_ROLES_EXPLAIN'						=> 'Aquí podeu gestionar els rols per als permisos de moderació. Els rols són permisos efectius. Si canvieu un rol, també canviaran els permisos dels elements que tinguin aquest rol assignat.',
	'ACP_USER_ROLES_EXPLAIN'					=> 'Aquí podeu gestionar els rols per als permisos d’usuari. Els rols són permisos efectius. Si canvieu un rol, també canviaran els permisos dels elements que tinguin aquest rol assignat.',
	'ACP_USERS_FORUM_PERMISSIONS_EXPLAIN'		=> 'Aquí podeu assignar permisos de fòrums a usuaris.',
	'ACP_USERS_PERMISSIONS_EXPLAIN'				=> 'Aquí podeu assignar permisos globals a usuaris - permisos d’usuari, permisos de moderador global i permisos d’administrador. Els permisos d’usuari inclouen capacitats com ara l’ús d’avatars, l’enviament de missatges privats, etc. Els permisos de moderador global inclouen l’aprovació d’entrades, la gestió de temes, la gestió de bandejos, etc. Finalment, els permisos d’administrador inclouen la modificació de permisos, la definició de BBCodes personalitzats, la gestió de fòrums, etc. Per modificar aquestes configuracions per a nombres grans d’usuaris, és preferible usar el sistema de permisos de grup. És recomanable que els permisos dels usuaris només es canviïn en casos excepcionals. El mètode preferit és posar els usuaris en grups i assignar-los els permisos del grup.',
	'ACP_VIEW_ADMIN_PERMISSIONS_EXPLAIN'		=> 'Aquí podeu visualitzar els permisos administratius efectius assignats als usuaris/grups seleccionats.',
	'ACP_VIEW_GLOBAL_MOD_PERMISSIONS_EXPLAIN'	=> 'Aquí podeu visualitzar els permisos de moderació global assignats als usuaris/grups seleccionats.',
	'ACP_VIEW_FORUM_PERMISSIONS_EXPLAIN'		=> 'Aquí podeu visualitzar els permisos de fòrums assignats als usuaris/grups i fòrums seleccionats.',
	'ACP_VIEW_FORUM_MOD_PERMISSIONS_EXPLAIN'	=> 'Aquí podeu visualitzar els permisos de moderador de fòrum assignats als usuaris/grups i fòrums seleccionats.',
	'ACP_VIEW_USER_PERMISSIONS_EXPLAIN'			=> 'Aquí podeu visualitzar els permisos d’usuari efectius assignats als usuaris/grups seleccionats.',

	'ADD_GROUPS'				=> 'Afegeix grups',
	'ADD_PERMISSIONS'			=> 'Afegeix permisos',
	'ADD_USERS'					=> 'Afegeix usuaris',
	'ADVANCED_PERMISSIONS'		=> 'Permisos avançats',
	'ALL_GROUPS'				=> 'Selecciona tots els grups',
	'ALL_NEVER'					=> 'Tots <strong>Mai</strong>',
	'ALL_NO'					=> 'Tots <strong>No</strong>',
	'ALL_USERS'					=> 'Selecciona tots els usuaris',
	'ALL_YES'					=> 'Tots <strong>Sí</strong>',
	'APPLY_ALL_PERMISSIONS'		=> 'Aplica tots els permisos',
	'APPLY_PERMISSIONS'			=> 'Aplica els permisos',
	'APPLY_PERMISSIONS_EXPLAIN'	=> 'Els permisos i rol definits per aquest element només s’aplicaran a aquest element i tots els elements marcats.',
	'AUTH_UPDATED'				=> 'S’han actualitzat els permisos.',

	'COPY_PERMISSIONS_CONFIRM'				=> 'Esteu segur que voleu dur a terme aquesta operació? Tingueu en compte que se sobreecriuran els permisos existents dels destins seleccionats.',
	'COPY_PERMISSIONS_FORUM_FROM_EXPLAIN'	=> 'El fòrum origen des d’on voleu copiar els permisos.',
	'COPY_PERMISSIONS_FORUM_TO_EXPLAIN'		=> 'Els fòrums destí on voleu aplicar els permisos copiats.',
	'COPY_PERMISSIONS_FROM'					=> 'Copia els permisos des de',
	'COPY_PERMISSIONS_TO'					=> 'Aplica el permisos a',

	'CREATE_ROLE'				=> 'Crea un rol',
	'CREATE_ROLE_FROM'			=> 'Utilitza la configuració de…',
	'CUSTOM'					=> 'Personalitzat…',

	'DEFAULT'					=> 'Per defecte',
	'DELETE_ROLE'				=> 'Elimina el rol',
	'DELETE_ROLE_CONFIRM'		=> 'Esteu segur que voleu eliminar aquest rol? Els elements que tinguin aquest rol assignat <strong>no</strong> perdran la seva configuració de permisos.',
	'DISPLAY_ROLE_ITEMS'		=> 'Mostra els elements que utilitzen aquest rol',

	'EDIT_PERMISSIONS'			=> 'Edita els permisos',
	'EDIT_ROLE'					=> 'Edita el rol',

	'GROUPS_NOT_ASSIGNED'		=> 'No hi ha cap grup assignat a aquest rol',

	'LOOK_UP_GROUP'				=> 'Cerca un grup d’usuaris',
	'LOOK_UP_USER'				=> 'Cerca un usuari',

	'MANAGE_GROUPS'		=> 'Gestió dels grups',
	'MANAGE_USERS'		=> 'Gestió dels usuaris',

	'NO_AUTH_SETTING_FOUND'		=> 'No hi ha definida una configuració de permisos.',
	'NO_ROLE_ASSIGNED'			=> 'No hi ha cap rol assignat',
	'NO_ROLE_ASSIGNED_EXPLAIN'	=> 'Assignar aquest rol no canvia els permisos de la dreta. Si voleu treure tots els permisos heu d’usar l’enllaç “Tots <strong>No</strong>”.',
	'NO_ROLE_AVAILABLE'			=> 'No hi ha rols disponibles',
	'NO_ROLE_NAME_SPECIFIED'	=> 'Indiqueu un nom per al rol.',
	'NO_ROLE_SELECTED'			=> 'No s’ha pogut trobar el rol.',
	'NO_USER_GROUP_SELECTED'	=> 'No heu seleccionat cap usuari o grup.',

	'ONLY_FORUM_DEFINED'	=> 'Només heu seleccionat fòrums. Seleccioneu com a mínim un usuari o un grup.',

	'PERMISSION_APPLIED_TO_ALL'		=> 'Els permisos i el rol també s’aplicaran a tots el objectes marcats',
	'PLUS_SUBFORUMS'				=> '+Subfòrums',

	'REMOVE_PERMISSIONS'			=> 'Treu els permisos',
	'REMOVE_ROLE'					=> 'Elimina el rol',
	'RESULTING_PERMISSION'			=> 'Permís resultant',
	'ROLE'							=> 'Rol',
	'ROLE_ADD_SUCCESS'				=> 'S’ha afegit el rol correctament.',
	'ROLE_ASSIGNED_TO'				=> 'Usuaris/Grups assignats a %s',
	'ROLE_DELETED'					=> 'S’ha eliminat el rol correctament.',
	'ROLE_DESCRIPTION'				=> 'Descripció del rol',

	'ROLE_ADMIN_FORUM'			=> 'Administrador del fòrum',
	'ROLE_ADMIN_FULL'			=> 'Administrador complet',
	'ROLE_ADMIN_STANDARD'		=> 'Administrador estàndard',
	'ROLE_ADMIN_USERGROUP'		=> 'Administrador d’usuaris i grups',
	'ROLE_FORUM_BOT'			=> 'Accés de robot',
	'ROLE_FORUM_FULL'			=> 'Accés complet',
	'ROLE_FORUM_LIMITED'		=> 'Accés limitat',
	'ROLE_FORUM_LIMITED_POLLS'	=> 'Accés limitat i enquestes',
	'ROLE_FORUM_NOACCESS'		=> 'Sense accés',
	'ROLE_FORUM_ONQUEUE'		=> 'Amb cua de moderació',
	'ROLE_FORUM_POLLS'			=> 'Accés estàndard i enquestes',
	'ROLE_FORUM_READONLY'		=> 'Accés només de lectura',
	'ROLE_FORUM_STANDARD'		=> 'Accés estàndard',
	'ROLE_FORUM_NEW_MEMBER'		=> 'Accés d’usuari acabat de registrar',
	'ROLE_MOD_FULL'				=> 'Moderador complet',
	'ROLE_MOD_QUEUE'			=> 'Moderador de cua',
	'ROLE_MOD_SIMPLE'			=> 'Moderador simple',
	'ROLE_MOD_STANDARD'			=> 'Moderador estàndard',
	'ROLE_USER_FULL'			=> 'Totes les característiques',
	'ROLE_USER_LIMITED'			=> 'Característiques limitades',
	'ROLE_USER_NOAVATAR'		=> 'Sense avatar',
	'ROLE_USER_NOPM'			=> 'Sense missatges privats',
	'ROLE_USER_STANDARD'		=> 'Característiques estàndard',
	'ROLE_USER_NEW_MEMBER'		=> 'Característiques d’usuari acabat de registrar',

	'ROLE_DESCRIPTION_ADMIN_FORUM'			=> 'Pot accedir a la gestió de fòrums i a la configuració de permisos del fòrum.',
	'ROLE_DESCRIPTION_ADMIN_FULL'			=> 'Té accés a totes les funcions administratives d’aquest fòrum.<br />No és recomanable.',
	'ROLE_DESCRIPTION_ADMIN_STANDARD'		=> 'Té accés a la majoria de les funcions administratives, però no pot usar les eines relacionades amb el servidor o el sistema.',
	'ROLE_DESCRIPTION_ADMIN_USERGROUP'		=> 'Pot gestionar grups i usuaris: Pot canviar permisos, configuracions, gestionar bandejos i rangs.',
	'ROLE_DESCRIPTION_FORUM_BOT'			=> 'Aquest rol és el recomanat per a robots i les aranyes de cerca.',
	'ROLE_DESCRIPTION_FORUM_FULL'			=> 'Pot usar totes les característiques del fòrum, incloent-hi la publicació d’avisos i temes recurrents. També pot ignorar el límit d’inundació.<br />No és recomanable per a usuaris normals.',
	'ROLE_DESCRIPTION_FORUM_LIMITED'		=> 'Pot usar algunes característiques del fòrum, però no pot adjuntar fitxers ni usar les icones d’entrada.',
	'ROLE_DESCRIPTION_FORUM_LIMITED_POLLS'	=> 'Com accés limitat, però també pot crear enquestes.',
	'ROLE_DESCRIPTION_FORUM_NOACCESS'		=> 'No pot ni veure ni accedir al fòrum.',
	'ROLE_DESCRIPTION_FORUM_ONQUEUE'		=> 'Pot usar la majoria de característiques del fòrum incloent-hi els fitxers adjunts, però un moderador ha d’aprovar les seves entrades i temes.',
	'ROLE_DESCRIPTION_FORUM_POLLS'			=> 'Com Accés estàndard, però també pot crear enquestes.',
	'ROLE_DESCRIPTION_FORUM_READONLY'		=> 'Pot llegir el fòrum, però no pot crear temes nous ni respondre entrades.',
	'ROLE_DESCRIPTION_FORUM_STANDARD'		=> 'Pot usar la majoria de característiques del fòrum incloent-hi els fitxers adjunts i eliminar els temes propis, però no pot bloquejar-los ni crear enquestes.',
	'ROLE_DESCRIPTION_FORUM_NEW_MEMBER'		=> 'Un rol per als membres del grup especial “nous usuaris registrats”; conté permisos de tipus <strong>Mai</strong> per bloquejar característiques per als nous usuaris.',
	'ROLE_DESCRIPTION_MOD_FULL'				=> 'Pot usar totes les característiques de moderació, incloent-hi el bandeig.',
	'ROLE_DESCRIPTION_MOD_QUEUE'			=> 'Pot usar la cua de moderació per validar i editar entrades, però res mes.',
	'ROLE_DESCRIPTION_MOD_SIMPLE'			=> 'Només pot usar accions de tema bàsiques. No pot enviar advertiments ni usar la cua de moderació.',
	'ROLE_DESCRIPTION_MOD_STANDARD'			=> 'Pot usar la majoria de les eines de moderació, però no pot bandejar usuaris ni canviar l’autor d’una entrada.',
	'ROLE_DESCRIPTION_USER_FULL'			=> 'Pot usar totes les característiques del fòrum disponibles als usuaris, incloent-hi canviar el seu nom d’usuari o ignorar el límit d’inundació.<br />No és recomanable.',
	'ROLE_DESCRIPTION_USER_LIMITED'			=> 'Té accés a algunes característiques d’usuari. No es permeten fitxers adjunts, correus electrònics o missatges instantanis.',
	'ROLE_DESCRIPTION_USER_NOAVATAR'		=> 'Te un grup de característiques limitat i no pot usar un avatar.',
	'ROLE_DESCRIPTION_USER_NOPM'			=> 'Te un grup de característiques limitat i no pot enviar missatges privats.',
	'ROLE_DESCRIPTION_USER_STANDARD'		=> 'Pot accedir a la majoria de les característiques d’usuari, però no a totes. Per exemple, no pot canviar el seu nom d’usuari ni ignorar el límit d’inundació.',
	'ROLE_DESCRIPTION_USER_NEW_MEMBER'		=> 'Un rol per als membres del grup especial “nous usuaris registrats”; conté permisos de tipus <strong>Mai</strong> per bloquejar característiques per als nous usuaris.',

	'ROLE_DESCRIPTION_EXPLAIN'		=> 'Podeu introduir una explicació curta de què fa el rol o quin és el seu objectiu. El text que introduïu aquí també es mostrarà a les pantalles de permisos.',
	'ROLE_DESCRIPTION_LONG'			=> 'La descripció del rol és massa llarga, limiteu-la a 4000 caràcters.',
	'ROLE_DETAILS'					=> 'Detalls del rol',
	'ROLE_EDIT_SUCCESS'				=> 'S’ha editat el rol correctament.',
	'ROLE_NAME'						=> 'Nom del rol',
	'ROLE_NAME_ALREADY_EXIST'		=> 'Ja existeix un rol anomenat <strong>%s</strong> per al tipus de permís especificat.',
	'ROLE_NOT_ASSIGNED'				=> 'Encara no s’ha assignat aquest rol.',

	'SELECTED_FORUM_NOT_EXIST'		=> 'Els fòrums seleccionats no existeixen.',
	'SELECTED_GROUP_NOT_EXIST'		=> 'Els grups seleccionats no existeixen.',
	'SELECTED_USER_NOT_EXIST'		=> 'Els usuaris seleccionats no existeixen.',
	'SELECT_FORUM_SUBFORUM_EXPLAIN'	=> 'El fòrum que seleccioneu aquí inclourà tots els subfòrums a la selecció.',
	'SELECT_ROLE'					=> 'Seleccioneu un rol…',
	'SELECT_TYPE'					=> 'Seleccioneu un tipus',
	'SET_PERMISSIONS'				=> 'Defineix els permisos',
	'SET_ROLE_PERMISSIONS'			=> 'Defineix els permisos del rol',
	'SET_USERS_PERMISSIONS'			=> 'Defineix els permisos dels usuaris',
	'SET_USERS_FORUM_PERMISSIONS'	=> 'Defineix els permisos de fòrum dels usuaris',

	'TRACE_DEFAULT'					=> 'Per defecte tots els permisos són <strong>No</strong> (no definit). D’aquesta manera els permisos poden ser sobreescrits per altres configuracions.',
	'TRACE_FOR'						=> 'Traça per a',
	'TRACE_GLOBAL_SETTING'			=> '%s (global)',
	'TRACE_GROUP_NEVER_TOTAL_NEVER'	=> 'El permís d’aquest grup té el valor <strong>Mai</strong> com el resultat total i, per tant, es manté el resultat antic.',
	'TRACE_GROUP_NEVER_TOTAL_NEVER_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor<strong>Mai</strong> com el resultat total i, per tant, es manté el resultat antic.',
	'TRACE_GROUP_NEVER_TOTAL_NO'	=> 'El permís d’aquest grup té el valor <strong>Mai</strong> que esdevé el nou valor total perquè encara no estava definit (tenia el valor <strong>No</strong>).',
	'TRACE_GROUP_NEVER_TOTAL_NO_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor<strong>Mai</strong> que esdevé el nou valor total perquè encara no estava definit (tenia el valor <strong>No</strong>).',
	'TRACE_GROUP_NEVER_TOTAL_YES'	=> 'El permís d’aquest grup té el valor <strong>Mai</strong> que sobreescriu el valor total <strong>Sí</strong> amb un <strong>Mai</strong> per aquest usuari.',
	'TRACE_GROUP_NEVER_TOTAL_YES_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor<strong>Mai</strong> que sobreescriu el valor total <strong>Sí</strong> amb un <strong>Mai</strong> per aquest usuari.',
	'TRACE_GROUP_NO'				=> 'El permís val <strong>No</strong> per a aquest grup i, per tant, es manté el valor total antic.',
	'TRACE_GROUP_NO_LOCAL'			=> 'El permís val <strong>No</strong> per a aquest grup en aquest fòrum i, per tant, es manté el valor total antic.',
	'TRACE_GROUP_YES_TOTAL_NEVER'	=> 'El permís d’aquest grup té el valor <strong>Sí</strong>, però el total <strong>Mai</strong> no es pot sobreescriure.',
	'TRACE_GROUP_YES_TOTAL_NEVER_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor <strong>Sí</strong>, però el total <strong>Mai</strong> no es pot sobreescriure.',
	'TRACE_GROUP_YES_TOTAL_NO'		=> 'El permís d’aquest grup té el valor <strong>Sí</strong> que esdevé el nou valor total perquè encara no estava definit (tenia el valor <strong>No</strong>).',
	'TRACE_GROUP_YES_TOTAL_NO_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor <strong>Sí</strong> que esdevé el nou valor total perquè encara no estava definit (tenia el valor <strong>No</strong>).',
	'TRACE_GROUP_YES_TOTAL_YES'		=> 'El permís d’aquest grup té el valor <strong>Sí</strong> i el permís total ja té el valor <strong>Sí</strong> i, per tant, es manté el resultat total.',
	'TRACE_GROUP_YES_TOTAL_YES_LOCAL'	=> 'El permís d’aquest grup per aquest fòrum té el valor <strong>Sí</strong> i el permís total ja té el valor <strong>Sí</strong> i, per tant, es manté el resultat total.',
	'TRACE_PERMISSION'				=> 'Traça el permís - %s',
	'TRACE_RESULT'					=> 'Resultat de la traça',
	'TRACE_SETTING'					=> 'Traça la configuració',

	'TRACE_USER_GLOBAL_YES_TOTAL_YES'		=> 'El permís d’usuari, independent del fòrum, s’avalua com un <strong>Sí</strong>, però el permís total ja té el valor <strong>Sí</strong> i, per tant, es manté el resultat total. %sTraça els permisos globals%s',
	'TRACE_USER_GLOBAL_YES_TOTAL_NEVER'		=> 'El permís d’usuari, independent del fòrum, s’avalua com un <strong>Sí</strong> que sobreescriu el resultat local actual <strong>Mai</strong>. %sTraça els permisos globals%s',
	'TRACE_USER_GLOBAL_NEVER_TOTAL_KEPT'	=> 'El permís d’usuari, independent del fòrum, s’avalua com un <strong>Mai</strong> que no influeix el permís local. %sTraça els permisos globals%s',

	'TRACE_USER_FOUNDER'					=> 'L’usuari és un fundador, per tant, els permisos d’administrador sempre tenen el valor <strong>Sí</strong>.',
	'TRACE_USER_KEPT'						=> 'El permís de l’usuari val <strong>No</strong> i, per tant, es manté el valor total antic.',
	'TRACE_USER_KEPT_LOCAL'					=> 'El permís de l’usuari per aquest fòrum val <strong>No</strong> i, per tant, es manté el valor total antic.',
	'TRACE_USER_NEVER_TOTAL_NEVER'			=> 'El permís de l’usuari té el valor <strong>Mai</strong> i el valor total val <strong>Mai</strong> i, per tant, no canvia res.',
	'TRACE_USER_NEVER_TOTAL_NEVER_LOCAL'	=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Mai</strong> i el valor total val <strong>Mai</strong> i, per tant, no canvia res.',
	'TRACE_USER_NEVER_TOTAL_NO'				=> 'El permís de l’usuari té el valor <strong>Mai</strong> que esdevé el valor total perquè valia <strong>No</strong>.',
	'TRACE_USER_NEVER_TOTAL_NO_LOCAL'		=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Mai</strong> que esdevé el valor total perquè valia <strong>No</strong>.',
	'TRACE_USER_NEVER_TOTAL_YES'			=> 'El permís de l’usuari té el valor <strong>Mai</strong> i sobreescriu el <strong>Sí</strong> anterior.',
	'TRACE_USER_NEVER_TOTAL_YES_LOCAL'		=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Mai</strong> i sobreescriu el <strong>Sí</strong> anterior.',
	'TRACE_USER_NO_TOTAL_NO'				=> 'El permís de l’usuari val <strong>No</strong> i el valor total valia No, per tant, per defecte queda <strong>Mai</strong>.',
	'TRACE_USER_NO_TOTAL_NO_LOCAL'			=> 'El permís de l’usuari per aquest fòrum val <strong>No</strong> i el valor total valia No, per tant, per defecte queda <strong>Mai</strong>.',
	'TRACE_USER_YES_TOTAL_NEVER'			=> 'El permís de l’usuari té el valor <strong>Sí</strong>, però el total <strong>Mai</strong> no es pot sobreescriure.',
	'TRACE_USER_YES_TOTAL_NEVER_LOCAL'		=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Sí</strong>, però el total <strong>Mai</strong> no es pot sobreescriure.',
	'TRACE_USER_YES_TOTAL_NO'				=> 'El permís de l’usuari té el valor <strong>Sí</strong> que esdevé el valor total perquè tenia el valor <strong>No</strong>.',
	'TRACE_USER_YES_TOTAL_NO_LOCAL'			=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Sí</strong> que esdevé el valor total perquè tenia el valor <strong>No</strong>.',
	'TRACE_USER_YES_TOTAL_YES'				=> 'El permís de l’usuari té el valor <strong>Sí</strong> i el valor total val <strong>Sí</strong> i, per tant, no canvia res.',
	'TRACE_USER_YES_TOTAL_YES_LOCAL'		=> 'El permís de l’usuari per aquest fòrum té el valor <strong>Sí</strong> i el valor total val <strong>Sí</strong> i, per tant, no canvia res.',
	'TRACE_WHO'								=> 'Qui',
	'TRACE_TOTAL'							=> 'Total',

	'USERS_NOT_ASSIGNED'			=> 'No hi ha cap usuari assignat a aquest rol',
	'USER_IS_MEMBER_OF_DEFAULT'		=> 'és membre dels següents grups predefinits',
	'USER_IS_MEMBER_OF_CUSTOM'		=> 'és membre dels següents grups definits per l’usuari',

	'VIEW_ASSIGNED_ITEMS'	=> 'Mostra els elements assignats',
	'VIEW_LOCAL_PERMS'		=> 'Permisos locals',
	'VIEW_GLOBAL_PERMS'		=> 'Permisos globals',
	'VIEW_PERMISSIONS'		=> 'Mostra els permisos',

	'WRONG_PERMISSION_TYPE'				=> 'Heu seleccionat un tipus de permís erroni.',
	'WRONG_PERMISSION_SETTING_FORMAT'	=> 'Les configuracions de permisos tenen un format erroni, el phpBB no pot processar-les correctament.',
));
