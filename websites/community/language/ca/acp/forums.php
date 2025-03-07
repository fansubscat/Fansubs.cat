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

// Forum Admin
$lang = array_merge($lang, array(
	'AUTO_PRUNE_DAYS'			=> 'Poda automàtica per temps de publicació',
	'AUTO_PRUNE_DAYS_EXPLAIN'	=> 'Nombre de dies des de la darrera entrada després dels quals s’elimina el tema.',
	'AUTO_PRUNE_FREQ'			=> 'Freqüència de la poda automàtica',
	'AUTO_PRUNE_FREQ_EXPLAIN'	=> 'Temps en dies entre execucions automàtiques de poda.',
	'AUTO_PRUNE_VIEWED'			=> 'Poda automàtica per temps de visualització',
	'AUTO_PRUNE_VIEWED_EXPLAIN'	=> 'Nombre de dies des de la darrera visualització després dels quals s’elimina el tema.',
	'AUTO_PRUNE_SHADOW_FREQ'	=> 'Freqüència de poda automàtica de temes ombra',
	'AUTO_PRUNE_SHADOW_DAYS'	=> 'Interval  poda automàtica de temes ombra',
	'AUTO_PRUNE_SHADOW_DAYS_EXPLAIN'	=> 'Nombre de dies desprès dels quals s’elimina un tema ombra.',
	'AUTO_PRUNE_SHADOW_FREQ_EXPLAIN'	=> 'Temps en dies entre esdeveniments de poda.',

	'CONTINUE'						=> 'Continua',
	'COPY_PERMISSIONS'				=> 'Copia els permisos des de',
	'COPY_PERMISSIONS_EXPLAIN'		=> 'Per facilitar l’assignació de permisos al nou fòrum podeu copiar el permisos d’un altre fòrum que ja existeixi.',
	'COPY_PERMISSIONS_ADD_EXPLAIN'	=> 'Un cop creat, el fòrum tindrà els mateixos permisos que el que seleccioneu aquí. Si no en seleccioneu cap, el fòrum nou no serà visible fins que en definiu els permisos.',
	'COPY_PERMISSIONS_EDIT_EXPLAIN'	=> 'Si trieu copiar els permisos, el fòrum tindrà els mateixos permisos que el que seleccioneu aquí. Això sobreescriurà tots els permisos que hagueu definit prèviament per aquest fòrum amb els permisos del fòrum que seleccioneu aquí. Si no en seleccioneu cap, es mantindran els permisos actuals.',
	'COPY_TO_ACL'					=> 'Alternativament, també podeu %sassignar permisos nous%s a aquest fòrum.',
	'CREATE_FORUM'					=> 'Crea un fòrum nou',

	'DECIDE_MOVE_DELETE_CONTENT'		=> 'Elimina el contingut o desplaça’l al fòrum',
	'DECIDE_MOVE_DELETE_SUBFORUMS'		=> 'Elimina els subfòrums o desplaça’ls al fòrum',
	'DEFAULT_STYLE'						=> 'Estil per defecte',
	'DELETE_ALL_POSTS'					=> 'Elimina les entrades',
	'DELETE_SUBFORUMS'					=> 'Elimina els subfòrums i les entrades',
	'DISPLAY_ACTIVE_TOPICS'				=> 'Habilita els temes actius',
	'DISPLAY_ACTIVE_TOPICS_EXPLAIN'		=> 'Si habiliteu aquesta opció, els temes actius dels fòrums seleccionats es mostraran en aquesta categoria.',

	'EDIT_FORUM'					=> 'Edita el fòrum',
	'ENABLE_INDEXING'				=> 'Habilita la indexació per a cerques',
	'ENABLE_INDEXING_EXPLAIN'		=> 'Si habiliteu aquesta opció, les entrades que es facin en aquest fòrum s’indexaran per poder cercar-les.',
	'ENABLE_POST_REVIEW'			=> 'Habilita la revisió d’entrades',
	'ENABLE_POST_REVIEW_EXPLAIN'	=> 'Si habiliteu aquesta opció, els usuaris poden revisar la seva entrada si s’han publicat entrades noves al tema mentre l’usuari escrivia la seva. És recomanable que ho inhabiliteu en els fòrums de xat.',
	'ENABLE_QUICK_REPLY'			=> 'Habilita la resposta ràpida',
	'ENABLE_QUICK_REPLY_EXPLAIN'	=> 'Habilita la resposta ràpida en aquest fòrum. Aquesta configuració no es té en compte si la resposta ràpida està inhabilitada de forma global per tots els fòrums. La resposta ràpida només es mostrarà als usuaris que tenen permisos per publicar entrades en aquest fòrum.',
	'ENABLE_RECENT'					=> 'Mostra els temes actius',
	'ENABLE_RECENT_EXPLAIN'			=> 'Si habiliteu aquesta opció, els temes publicats en aquest fòrum es mostraran a la llista de temes actius.',
	'ENABLE_TOPIC_ICONS'			=> 'Habilita les icones de tema',

	'FORUM_ADMIN'						=> 'Administració dels fòrums',
	'FORUM_ADMIN_EXPLAIN'				=> 'Al phpBB3 tot està basat en fòrums. Una categoria es simplement un tipus especial de fòrum. Cada fòrum pot tenir un nombre il·limitat de subfòrums i podeu determinar en quins s’hi poden publicar entrades (p.ex. si actua com una categoria antiga). Aquí podeu afegir, editar, eliminar, bloquejar i desbloquejar fòrums individualment així com definir controls addicionals. Si les entrades i els temes no estan sincronitzats, podeu resincronitzar un fòrum. <strong>Cal que copieu o definiu els permisos adients per als fòrums que creeu per tal que es mostrin.</strong>',
	'FORUM_AUTO_PRUNE'					=> 'Habilita la poda automàtica',
	'FORUM_AUTO_PRUNE_EXPLAIN'			=> 'Poda temes del fòrum, seleccioneu els paràmetres de freqüència/temps a sota.',
	'FORUM_CREATED'						=> 'S’ha creat el fòrum correctament.',
	'FORUM_DATA_NEGATIVE'				=> 'Els paràmetres de poda no poden ser negatius.',
	'FORUM_DESC_TOO_LONG'				=> 'La descripció del fòrum és massa llarga, ha de tenir menys de 4000 caràcters.',
	'FORUM_DELETE'						=> 'Elimina el fòrum',
	'FORUM_DELETE_EXPLAIN'				=> 'El formulari a continuació us permet eliminar un fòrum. Si es poden publicar entrades al fòrum, podeu decidir on voleu posar tots els temes (o fòrums) que conté.',
	'FORUM_DELETED'						=> 'S’ha eliminat el fòrum correctament.',
	'FORUM_DESC'						=> 'Descripció',
	'FORUM_DESC_EXPLAIN'				=> 'Totes les etiquetes HTML que introduïu aquí es mostraran tal qual. Si el tipus de fòrum seleccionat és una categoria, la descripció no s’utilitza.',
	'FORUM_EDIT_EXPLAIN'				=> 'El formulari a continuació us permet personalitzar aquest fòrum. Tingueu en compte que els controls de moderació i compte d’entrades es defineixen a través dels permisos del fòrum per cada usuari o grups d’usuaris.',
	'FORUM_IMAGE'						=> 'Imatge del fòrum',
	'FORUM_IMAGE_EXPLAIN'				=> 'Ubicació, relativa al directori arrel del phpBB, d’una imatge addicional per associar-la amb aquest fòrum.',
	'FORUM_IMAGE_NO_EXIST'				=> 'La imatge del fòrum especificada no existeix',
	'FORUM_LINK_EXPLAIN'				=> 'URL complet (incloent-hi el protocol, p.ex.: <samp>http://</samp>) a la ubicació destí a la que es durà a l’usuari en fer clic al fòrum, p.ex.: <samp>http://www.phpbb.com/</samp>.',
	'FORUM_LINK_TRACK'					=> 'Fes un seguiment de les redireccions de l’enllaç',
	'FORUM_LINK_TRACK_EXPLAIN'			=> 'Registra el nombre de vegades que s’ha fet clic a l’enllaç del fòrum.',
	'FORUM_NAME'						=> 'Nom del fòrum',
	'FORUM_NAME_EMPTY'					=> 'Heu d’introduïr un nom per al fòrum.',
	'FORUM_NAME_EMOJI'					=> 'El nom del fòrum que heu introduït no és vàlid.<br>Conté els següents caràcters no admesos:<br>%s',
	'FORUM_PARENT'						=> 'Fòrum pare',
	'FORUM_PASSWORD'					=> 'Contrasenya del fòrum',
	'FORUM_PASSWORD_CONFIRM'			=> 'Confirmació de la contrasenya del fòrum',
	'FORUM_PASSWORD_CONFIRM_EXPLAIN'	=> 'Nomes cal si heu introduït una contrasenya per al fòrum.',
	'FORUM_PASSWORD_EXPLAIN'			=> 'Defineix una contrasenya per al fòrum, és preferible que utilitzeu el sistema de permisos.',
	'FORUM_PASSWORD_UNSET'				=> 'Elimina la contrasenya del fòrum',
	'FORUM_PASSWORD_UNSET_EXPLAIN'		=> 'Seleccioneu aquesta opció si voleu eliminar la contrasenya del fòrum.',
	'FORUM_PASSWORD_OLD'				=> 'La contrasenya del fòrum utilitza una funció de resum antiga i és recomanable que la canvieu.',
	'FORUM_PASSWORD_MISMATCH'			=> 'Les contrasenyes que heu introduït no coincideixen.',
	'FORUM_PRUNE_SETTINGS'				=> 'Configuració de poda del fòrum',
	'FORUM_PRUNE_SHADOW'				=> 'Habilita la poda automàtica de temes ombra',
	'FORUM_PRUNE_SHADOW_EXPLAIN'			=> 'Poda els temes ombra del fòrum, indiqueu la freqüencia/interval a sota.',
	'FORUM_RESYNCED'					=> 'El fòrum “%s” s’ha resincronitzat correctament',
	'FORUM_RULES_EXPLAIN'				=> 'Les regles del fòrum es mostren a qualsevol pagina del propi fòrum.',
	'FORUM_RULES_LINK'					=> 'Enllaç a les regles del fòrum',
	'FORUM_RULES_LINK_EXPLAIN'			=> 'Aquí podeu introduïr l’URL d’una pàgina/entrada que contingui les regles del fòrum. Aquesta configuració sobreescriurà el text de regles del fòrum que hagueu especificat.',
	'FORUM_RULES_PREVIEW'				=> 'Previsualització de les regles del fòrum',
	'FORUM_RULES_TOO_LONG'				=> 'Les regles del fòrum han de tenir menys de 4000 caràcters.',
	'FORUM_SETTINGS'					=> 'Configuració del fòrum',
	'FORUM_STATUS'						=> 'Estat del fòrum',
	'FORUM_STYLE'						=> 'Estil del fòrum',
	'FORUM_TOPICS_PAGE'					=> 'Temes per pàgina',
	'FORUM_TOPICS_PAGE_EXPLAIN'			=> 'Si no és zero, aquest valor sobreescriurà la configuració per defecte de temes per pàgina.',
	'FORUM_TYPE'						=> 'Tipus de fòrum',
	'FORUM_UPDATED'						=> 'S’ha actualitzat la informació del fòrum correctament.',

	'FORUM_WITH_SUBFORUMS_NOT_TO_LINK'		=> 'Esteu intentant canviar un fòrum on es poden publicar entrades que té subfòrums per convertir-lo en un enllaç. Desplaceu tots els subfòrums a fora d’aquest fòrum abans de continuar ja que després de canviar-lo al tipus enllaç ja no podreu veure els subfòrums que actualment estan connectats a aquest fòrum.',

	'GENERAL_FORUM_SETTINGS'	=> 'Configuració general del fòrum',

	'LINK'					    => 'Enllaç',
	'LIMIT_SUBFORUMS'			=> 'Limita la llegenda als sub-fòrums que són fills directes',
	'LIMIT_SUBFORUMS_EXPLAIN'	=> 'Limita els subfòrums que es mostren als que són descendents directes (fills) del fòrum actual. Si ho inhabiliteu, es mostraran tots els subfòrums que tinguin l’opció “Llista els subfòrums a la llegenda” habilitada, independentment de la seva profunditat.',
	'LIST_INDEX'			    => 'Llista el subfòrum a la llegenda del fòrum pare',
	'LIST_INDEX_EXPLAIN'	    => 'Mostra aquest fòrum a l’índex i en altres pàgines com un enllaç a la llegenda del seu fòrum pare.',
	'LIST_SUBFORUMS'			=> 'Llista els subfòrums a la llegenda',
	'LIST_SUBFORUMS_EXPLAIN'	=> 'Mostra els subfòrums d’aquest fòrum a l’índex i en altres pàgines com un enllaç a la llegenda.',
	'LOCKED'				    => 'Bloquejat',

	'MOVE_POSTS_NO_POSTABLE_FORUM'	=> 'En el fòrum que heu seleccionat per desplaçar-hi les entrades no es poden publicar entrades. Seleccioneu un fòrum on es puguin publicar entrades.',
	'MOVE_POSTS_TO'					=> 'Desplaça les entrades a',
	'MOVE_SUBFORUMS_TO'				=> 'Desplaça els subfòrums a',

	'NO_DESTINATION_FORUM'			=> 'No heu especificat un fòrum per desplaçar-hi el contingut.',
	'NO_FORUM_ACTION'				=> 'No s’ha definit l’acció a fer amb el contingut del fòrum.',
	'NO_PARENT'						=> 'Sense pare',
	'NO_PERMISSIONS'				=> 'No copiïs cap permís',
	'NO_PERMISSION_FORUM_ADD'		=> 'No teniu els permisos necessaris per afegir fòrums.',
	'NO_PERMISSION_FORUM_DELETE'	=> 'No teniu els permisos necessaris per eliminar fòrums.',

	'PARENT_IS_LINK_FORUM'		=> 'El pare que heu especificat és un fòrum de tipus enllaç. Els fòrums de tipus enllaç no poden contenir altres fòrums, especifiqueu una categoria o fòrum com a fòrum pare.',
	'PARENT_NOT_EXIST'			=> 'El pare no existeix.',
	'PRUNE_ANNOUNCEMENTS'		=> 'Poda els anuncis',
	'PRUNE_STICKY'				=> 'Poda els temes recurrents',
	'PRUNE_OLD_POLLS'			=> 'Poda les enquestes antigues',
	'PRUNE_OLD_POLLS_EXPLAIN'	=> 'Elimina temes amb enquestes en les que no s’hagi emès cap vot en el nombre de dies especificat per a la poda per temps de publicació.',

	'REDIRECT_ACL'	=> 'Ara podeu %sassignar permisos%s a aquest fòrum.',

	'SYNC_IN_PROGRESS'			=> 'S’està sincronitzant el fòrum',
	'SYNC_IN_PROGRESS_EXPLAIN'	=> 'S’està resincronitzant el rang de temes %1$d/%2$d.',

	'TYPE_CAT'			=> 'Categoria',
	'TYPE_FORUM'		=> 'Fòrum',
	'TYPE_LINK'			=> 'Enllaç',

	'UNLOCKED'			=> 'Desbloquejat',
));
