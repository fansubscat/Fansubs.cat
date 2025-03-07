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
	'ACP_SEARCH_INDEX_EXPLAIN'				=> 'Aquí podeu gestionar els índexs dels motor de cerques. Com que normalment només utilitzeu un motor, és recomanable que elimineu tots els índexs que no utilitzeu. Després de modificar alguna de les configuracions de les cerques (p.ex. el nombre mínim/màxim de caràcters) val la pena que recreeu l’índex per que reflecteixi aquests canvis.',
	'ACP_SEARCH_SETTINGS_EXPLAIN'			=> 'Aquí podeu definir quin motor s’usarà per indexar les entrades i realitzar les cerques. Podeu definir diverses opcions que poden influir en la quantitat de processament que requereixen aquestes accions. Algunes d’aquestes opcions són les mateixes per tots els motors de cerca.',

	'COMMON_WORD_THRESHOLD'					=> 'Llindar per a paraules comuns',
	'COMMON_WORD_THRESHOLD_EXPLAIN'			=> 'Les paraules que apareguin en un percentatge més gran d’entre totes les entrades es consideraran comuns. Les paraules comuns s’ignoren en les consultes de cerca. Utilitzeu el zero per inhabilitar-ho. Només té efecte si hi ha més de 100 entrades. Si voleu que es reconsiderin paraules actualment considerades comuns, cal que torneu a crear l’índex.',
	'CONFIRM_SEARCH_BACKEND'				=> 'Esteu segur que voleu canviar a un motor de cerca diferent? Després de canviar el motor de cerca, caldrà que creeu un índex per al nou motor. Si no teniu pensat tornar al motor de cerca antic, també podeu eliminar l’índex del motor antic per alliberar recursos del sistema.',
	'CONTINUE_DELETING_INDEX'				=> 'Continua amb el procediment d’eliminació de l’índex anterior',
	'CONTINUE_DELETING_INDEX_EXPLAIN'		=> 'S’ha iniciat un procediment d’eliminació d’índex. Per accedir a la pàgina de l’índex de cerques cal que el completeu o el cancel·leu.',
	'CONTINUE_INDEXING'						=> 'Continua el procediment d’indexació anterior',
	'CONTINUE_INDEXING_EXPLAIN'				=> 'S’ha iniciat un procediment d’indexació. Per accedir a la pàgina de l’índex de cerques cal que el completeu o el cancel·leu.',
	'CREATE_INDEX'							=> 'Crea l’índex',

	'DEFAULT_SEARCH_RETURN_CHARS'			=> 'Nombre de caràcters que es retornen per defecte',
	'DEFAULT_SEARCH_RETURN_CHARS_EXPLAIN'	=> 'El nombre de caràcters que es retornen per defecte quan es fa una cerca. El valor 0 fa que es retorni l’entrada sencera.',
	'DELETE_INDEX'							=> 'Elimina l’índex',
	'DELETING_INDEX_IN_PROGRESS'			=> 'Eliminació de l’index en curs',
	'DELETING_INDEX_IN_PROGRESS_EXPLAIN'	=> 'El motor de cerca està netejant el seu índex. Això pot trigar uns quants minuts.',

	'FULLTEXT_MYSQL_INCOMPATIBLE_DATABASE'	=> 'Només podeu usar el motor MySQL fulltext amb MySQL4 o superior.',
	'FULLTEXT_MYSQL_NOT_SUPPORTED'			=> 'Els índexs MySQL fulltext només es poden usar amb taules MyISAM o taules InnoDB. Per usar índexs de text complet amb InnoDB és necessària la versió 5.6.8 o superior del MySQL.',
	'FULLTEXT_MYSQL_TOTAL_POSTS'			=> 'Nombre total d’entrades indexades',
	'FULLTEXT_MYSQL_MIN_SEARCH_CHARS_EXPLAIN'	=> 'Només s’indexaran per a les cerques les paraules que tinguin com a mínim aquest nombre de caràcters. Només podeu canviar aquest paràmetre modificant la configuració del MySQL.',
	'FULLTEXT_MYSQL_MAX_SEARCH_CHARS_EXPLAIN'	=> 'Només s’indexaran per a les cerques les paraules que tinguin com a màxim aquest nombre de caràcters. Només podeu canviar aquest paràmetre modificant la configuració del MySQL.',

	'FULLTEXT_POSTGRES_INCOMPATIBLE_DATABASE'	=> 'Només podeu usar el motor PostgreSQL fulltext amb PostgreSQL.',
	'FULLTEXT_POSTGRES_TOTAL_POSTS'			=> 'Nombre total d’entrades indexades',
	'FULLTEXT_POSTGRES_VERSION_CHECK'		=> 'Versió del PostgreSQL',
	'FULLTEXT_POSTGRES_TS_NAME'				=> 'Perfil de configuració de cerca de text:',
	'FULLTEXT_POSTGRES_MIN_WORD_LEN'			=> 'Longitud mínima de les paraules clau',
	'FULLTEXT_POSTGRES_MAX_WORD_LEN'			=> 'Longitud màxima de les paraules clau',
	'FULLTEXT_POSTGRES_VERSION_CHECK_EXPLAIN'	=> 'Aquest motor de cerca necessita la versió 8.3 o superior del PostgreSQL.',
	'FULLTEXT_POSTGRES_TS_NAME_EXPLAIN'			=> 'El perfil de configuració de cerca de text que s’utilitza per determinar l’analitzador i el diccionari.',
	'FULLTEXT_POSTGRES_MIN_WORD_LEN_EXPLAIN'	=> 'Les paraules que tinguin com a mínim aquest quantitat de caràcters s’inclouran a la consulta a la base de dades.',
	'FULLTEXT_POSTGRES_MAX_WORD_LEN_EXPLAIN'	=> 'Les paraules que tinguin com a màxim aquest quantitat de caràcters s’inclouran a la consulta a la base de dades.',

	'FULLTEXT_SPHINX_CONFIGURE'				=> 'Proporcioneu els paràmetres següents per generar un fitxer de configuració sphinx',
	'FULLTEXT_SPHINX_DATA_PATH'				=> 'Camí al directori de dades',
	'FULLTEXT_SPHINX_DATA_PATH_EXPLAIN'		=> 'S’usarà per emmagatzemar els índexos i els fitxers de registre. És aconsellable crear el directori fora dels directaris accessibles des del web y ha d’acabar amb el caràcter de barra inclinada.',
	'FULLTEXT_SPHINX_DELTA_POSTS'			=> 'Nombre d’entrades a l’índex delta actualitzat freqüentment',
	'FULLTEXT_SPHINX_HOST'					=> 'Amfitrió del dimoni de cerca sphinx',
	'FULLTEXT_SPHINX_HOST_EXPLAIN'			=> 'Amfitrió en el qual escolta el dimoni de cerca sphinx (searchd). Deixeu-lo buit per usar el valor per defecte localhost',
	'FULLTEXT_SPHINX_INDEXER_MEM_LIMIT'		=> 'Límit de memòria de l’indexador',
	'FULLTEXT_SPHINX_INDEXER_MEM_LIMIT_EXPLAIN'	=> 'En tot moment aquest número ha de ser inferior a la RAM disponible en la vostra màquina. Si detecteu problemes periòdics de rendiment és possible que la causa sigui que l’indexador està consumint massa recursos. Per evitar-ho reduïu la quantitat de memòria disponible per a l’indexador.',
	'FULLTEXT_SPHINX_MAIN_POSTS'			=> 'Nombre d’entrades a l’índex principal',
	'FULLTEXT_SPHINX_PORT'					=> 'Port del dimoni de cerca sphinx',
	'FULLTEXT_SPHINX_PORT_EXPLAIN'			=> 'Port al qual escolta el dimoni de cerca sphinx (searchd). Deixeu-lo buit per usar el valor per defecte 9312',
	'FULLTEXT_SPHINX_WRONG_DATABASE'		=> 'La cerca sphinx del phpBB només es compatible amb MySQL i PostgreSQL.',
	'FULLTEXT_SPHINX_CONFIG_FILE'			=> 'Fitxer de configuració de sphinx',
	'FULLTEXT_SPHINX_CONFIG_FILE_EXPLAIN'	=> 'El contingut generat del fitxer de configuració de sphinx. Aquestes dades s’han d’enganxar al fitxer sphinx.conf que utilitza el dimoni de cerca sphinx. Substituïu el text [dbuser] i [dbpassword] amb les vostres credencials d’accés a la base de dades.',
	'FULLTEXT_SPHINX_NO_CONFIG_DATA'		=> 'El camí al directori de dades no està definit. Definiu el camí i trameteu-lo per generar el fitxer de configuració.',

	'GENERAL_SEARCH_SETTINGS'				=> 'Configuracions generals de les cerques',
	'GO_TO_SEARCH_INDEX'					=> 'Ves a la pàgina d’índexs de cerca',

	'INDEX_STATS'							=> 'Estadístiques de l’índex',
	'INDEXING_IN_PROGRESS'					=> 'Indexació en curs',
	'INDEXING_IN_PROGRESS_EXPLAIN'			=> 'El motor de cerca està indexant totes les entrades del fòrum. Això pot trigar uns quants minuts o diverses hores depenent de la mida del fòrum.',

	'LIMIT_SEARCH_LOAD'						=> 'Límit de càrrega del sistema per a la pàgina de cerca',
	'LIMIT_SEARCH_LOAD_EXPLAIN'				=> 'Si la càrrega del sistema per a 1 minut sobrepassa aquest valor, la pàgina de cerca es quedarà fora de línia; 1.0 equival a una utilització aproximada del 100% d’un processador. Això només funciona en servidors basats en UNIX.',

	'MAX_SEARCH_CHARS'						=> 'Nombre màxim de caràcters indexats per la cerca',
	'MAX_SEARCH_CHARS_EXPLAIN'				=> 'Només s’indexaran per ser cercades les paraules que tinguin com a màxim aquest nombre de caràcters.',
	'MAX_NUM_SEARCH_KEYWORDS'				=> 'Nombre màxim de paraules permeses',
	'MAX_NUM_SEARCH_KEYWORDS_EXPLAIN'		=> 'Nombre màxim de paraules que es permet usar als usuaris en una cerca. El valor 0 permet un nombre il·limitat de paraules.',
	'MIN_SEARCH_CHARS'						=> 'Nombre mínim de caràcters indexats per la cerca',
	'MIN_SEARCH_CHARS_EXPLAIN'				=> 'Només s’indexaran per ser cercades les paraules que tinguin com a mínim aquest nombre de caràcters.',
	'MIN_SEARCH_AUTHOR_CHARS'				=> 'Nombre mínim de caràcters per al nom de l’autor',
	'MIN_SEARCH_AUTHOR_CHARS_EXPLAIN'		=> 'Els usuaris han d’introduir com a mínim aquesta quantitat de caràcters del nom quan realitzen una cerca per autor amb comodins. Si el nom d’usuari de l’autor és més curt que aquest número, encara podeu cercar les entrades d’aquest autor introduïnt el seu nom d’usuari complet.',

	'PROGRESS_BAR'							=> 'Barra de progrés',

	'SEARCH_GUEST_INTERVAL'					=> 'Interval d’inundació de cerques per a visitants',
	'SEARCH_GUEST_INTERVAL_EXPLAIN'			=> 'Nombre de segons que han d’esperar els visitants entre dues cerques consecutives. Si un dels visitants fa una cerca, tots els altres s’han d’esperar fins que l’interval de temps hagi passat.',
	'SEARCH_INDEX_CREATE_REDIRECT'			=> array(
		1	=> 'Totes les entrades fins l’entrada amb l’id %2$d s’han indexat, de les quals %1$d entrada estava dintre d’aquest pas.',
		2	=> 'Totes les entrades fins l’entrada amb l’id %2$d s’han indexat, de les quals %1$d entrades estaven dintre d’aquest pas.',
	),
	'SEARCH_INDEX_CREATE_REDIRECT_RATE'		=> array(
		1	=> 'La velocitat d’indexació actual és d’aproximadament %1$.1f entrada per segon.',
		2	=> 'La velocitat d’indexació actual és d’aproximadament %1$.1f entrades per segon.',
	),
	'SEARCH_INDEX_DELETE_REDIRECT'			=> array(
		1	=> 'Totes les entrades fins l’entrada amb l’id %2$d s’han tret de l’índex de cerca, de les quals %1$d entrada era en aquest pas.',
		2	=> 'Totes les entrades fins l’entrada amb l’id %2$d s’han tret de l’índex de cerca, de les quals %1$d entrades eren en aquest pas.',
	),
	'SEARCH_INDEX_DELETE_REDIRECT_RATE'		=> array(
		1	=> 'La velocitat d’eliminació actual és d’aproximadament %1$.1f entrada per segon.',
		2	=> 'La velocitat d’eliminació actual és d’aproximadament %1$.1f entrades per segon.',
	),
	'SEARCH_INDEX_CREATED'					=> 'S’han indexat totes les entrades de la base de dades del fòrum correctament.',
	'SEARCH_INDEX_PROGRESS'					=> 'Fets: %1$d | Pendents: %2$d | Total: %3$d',
	'SEARCH_INDEX_REMOVED'					=> 'S’ha eliminat l’índex de cerca d’aquest motor correctament.',
	'SEARCH_INTERVAL'						=> 'Interval d’inundació de cerques per a usuaris',
	'SEARCH_INTERVAL_EXPLAIN'				=> 'Nombre de segons que han d’esperar els usuaris entre dues cerques consecutives. Aquest interval es comprova independentment per cada usuari.',
	'SEARCH_STORE_RESULTS'					=> 'Durada de la mèmoria cau de resultats de la cerca',
	'SEARCH_STORE_RESULTS_EXPLAIN'			=> 'Els resultats emmagatzemats a la memòria cau de la cerca venceran després d’aquest temps, mesurat en segons. Introduïu un 0 si voleu inhabilitar la memòria cau de la cerca.',
	'SEARCH_TYPE'							=> 'Motor de cerca',
	'SEARCH_TYPE_EXPLAIN'					=> 'El phpBB us permet triar el motor que s’utilitza per cercar text al contingut de les entrades. Per defecte, s’utilitza la cerca fulltext pròpia del phpBB.',
	'SWITCHED_SEARCH_BACKEND'				=> 'Heu canviat el motor de cerca. Per usar el nou motor de cerca assegureu-vos que hi hagi un índex per al motor que heu triat.',

	'TOTAL_WORDS'							=> 'Nombre total de paraules indexades',
	'TOTAL_MATCHES'							=> 'Nombre total de relacions indexades entre paraules i entrades',

	'YES_SEARCH'							=> 'Habilita les utilitats de cerca',
	'YES_SEARCH_EXPLAIN'					=> 'Habilita la funcionalitat de cerca per als usuaris, incloent-hi la cerca de membres.',
	'YES_SEARCH_UPDATE'						=> 'Habilita l’actualització fulltext',
	'YES_SEARCH_UPDATE_EXPLAIN'				=> 'Actualització d’índexs fulltext quan es trameten entrades, s’ignora si la cerca està inhabilitada.',
));
