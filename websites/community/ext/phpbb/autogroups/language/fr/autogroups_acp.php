<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* French translation by Galixte (http://www.galixte.com)
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
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
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_AUTOGROUPS_MANAGE'			=> 'Groupes automatiques',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'En utilisant ce formulaire vous pouvez ajouter, modifier, afficher et supprimer la configuration de l’extension Auto Groups.',
	'ACP_AUTOGROUPS_ADD'			=> 'Ajouter des groupes automatiques',
	'ACP_AUTOGROUPS_EDIT'			=> 'Modifier des groupes automatiques',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Groupe',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Choisir un groupe pour y ajouter / supprimer automatiquement des utilisateurs.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Type de condition',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Choisir le type condition selon laquelle les utilisateurs seront ajoutés ou supprimés de ce groupe.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Valeur minimum',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Les utilisateurs seront ajoutés à ce groupe si ils dépassent la valeur minimale.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Valeur maximum',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Les utilisateurs seront retirés de ce groupe si ils dépassent la valeur maximale. Laissez ce champ vide si vous ne voulez pas que les utilisateurs soient retirés.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Groupe par défaut',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Faire de ce nouveau groupe, le groupe par défaut de l’utilisateur.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Cela n’affectera pas les membres ayant l’un des groupes par défaut suivants : %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Prévenir les utilisateurs',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Envoyer une notification aux utilisateurs après avoir été automatiquement ajouté ou retiré de ce groupe.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Groupes exclus',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclure les membres de ces groupes',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Les membres appartenant à <em>n‘importe quel groupe</em> sélectionné dans cette liste seront ignorés. Laissez ce champ vide si vous souhaitez que ce groupe automatique soit appliqué à <em>tous les membres</em> de votre forum. Sélectionnez plusieurs groupes en maintenant <samp>CTRL</samp> (ou <samp>⌘CMD</samp> sur Mac) et en sélectionnant les groupes.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'Une erreur s‘est produite. Le groupe pour cette condition ne peut pas également être sélectionné dans le champ Groupes exclus.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Définir les groupes exemptés par défaut',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto groups ne modifiera pas le groupe par défaut d’un utilisateur si il est sélectionné dans cette liste. Appuyer sur la touche <samp>CTRL</samp> (ou <samp>&#8984;CMD</samp> sur Mac) tout en cliquant pour sélectionner / désélectionner plus d’un groupe.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Créer un nouveau groupe automatique',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Le groupe automatique a été configuré avec succès.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Êtes-vous sûr de vouloir supprimer la configuration de ce groupe automatique ?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Le groupe automatique a été supprimé avec succès.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Il n’y a aucun groupe automatique.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Aucun groupe disponible',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Une erreur est survenue. Aucun groupe d’utilisateur valide n’a été sélectionné.<br />L’extension Auto Groups fonctionne uniquement avec des groupes d’utilisateurs définis, pouvant être créés depuis la page « Gérer les groupes ».',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Une erreur est survenue. La valeur minimale et la valeur maximale ne peuvent être identiques.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'L’âge de l’utilisateur',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Jours depuis la dernière visite',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Jours d’adhésion',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Messages',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Avertissements',
));
