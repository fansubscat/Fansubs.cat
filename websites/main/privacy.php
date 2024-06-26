<?php
define('PAGE_TITLE', 'Política de privadesa');
define('PAGE_PATH', '/politica-de-privadesa');
define('PAGE_STYLE_TYPE', 'text');
if (str_ends_with($_SERVER['HTTP_HOST'], 'hentai.cat')) {
	define('PAGE_DESCRIPTION', 'Aquesta política de privadesa defineix la manera en què Fansubs.cat («nosaltres») recopila, emmagatzema, utilitza, manté i comparteix la informació («informació» o «dades») recopilada dels usuaris (cadascun, un «usuari», o «tu») del lloc web de Fansubs.cat («lloc web»). Aquesta política de privadesa s’aplica a tots els productes i serveis oferts per Fansubs.cat en relació amb el lloc web («serveis»). Si tens qualsevol dubte relatiu a aquesta política de privadesa o a l’ús que fem de les teves dades, te’l podem resoldre si ens escrius fent servir l’enllaç de contacte de la part inferior de la pàgina.');
} else {
	define('PAGE_DESCRIPTION', 'Aquesta política de privadesa defineix la manera en què Hentai.cat («nosaltres») recopila, emmagatzema, utilitza, manté i comparteix la informació («informació» o «dades») recopilada dels usuaris (cadascun, un «usuari», o «tu») del lloc web de Hentai.cat («lloc web»). Aquesta política de privadesa s’aplica a tots els productes i serveis oferts per Hentai.cat en relació amb el lloc web («serveis»). Si tens qualsevol dubte relatiu a aquesta política de privadesa o a l’ús que fem de les teves dades, te’l podem resoldre si ens escrius fent servir l’enllaç de contacte de la part inferior de la pàgina.');
}

require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

validate_hentai();

require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-user-lock"></i> Política de privadesa</h2>
						<div class="section-content">Aquesta política de privadesa defineix la manera en què <?php echo CURRENT_SITE_NAME; ?> («nosaltres») recopila, emmagatzema, utilitza, manté i comparteix la informació («informació» o «dades») recopilada dels usuaris (cadascun, un «usuari», o «tu») del lloc web de <?php echo CURRENT_SITE_NAME; ?> («lloc web»). Aquesta política de privadesa s’aplica a tots els productes i serveis oferts per <?php echo CURRENT_SITE_NAME; ?> en relació amb el lloc web («serveis»). Si tens qualsevol dubte relatiu a aquesta política de privadesa o a l’ús que fem de les teves dades, te’l podem resoldre si ens escrius fent servir l’enllaç de contacte de la part inferior de la pàgina.</div>
						<h3 class="section-title">Informació que recopilem</h3>
						<h4 class="section-title">Informació personal que ens proporciones</h4>
						<div class="section-content">Recopilem adreces electròniques, dates de naixement, noms d’usuari i una versió xifrada de les contrasenyes, amb l’objectiu de proporcionar-te els serveis del lloc web adaptats a l’usuari. Pots elegir no crear un compte d’usuari al lloc web i fer-lo servir anònimament. No recopilem noms, cognoms, adreces físiques ni cap altra informació personal de l’usuari.</div>
						<h4 class="section-title">Informació recopilada automàticament</h4>
						<div class="section-content">Recopilem certa informació dels usuaris quan interactuen amb el nostre lloc web. La informació pot incloure la versió del sistema operatiu, el model i la marca del dispositiu, l’adreça IP, galetes amb l’identificador de sessió i informació tècnica sobre l’ús que els usuaris fan del nostre lloc web, com ara el flux d’esdeveniments al lloc web, la configuració del lloc web definida per l’usuari, els proveïdors de servei utilitzats i altra informació no identificativa. <strong>No compartim informació en cap cas amb tercers.</strong></div>
						<h3 class="section-title">Com fem servir la informació</h3>
						<div class="section-content">Recopilem i utilitzem la informació dels usuaris per als següents objectius: per a proporcionar-te els serveis; per a millorar el nostre lloc web; per a respondre a les teves consultes o demanar-te més informació en cas que contactis amb nosaltres; per a oferir estadístiques anonimitzades del nostre lloc web, fins i tot públicament; per a protegir els nostres serveis d’atacs o intrusions; per a respondre a peticions de les autoritats o necessitats legals.</div>
						<h3 class="section-title">On, com i quant de temps s’emmagatzema la informació</h3>
						<div class="section-content">Els nostres servidors estan ubicats als Països Baixos. No obstant això, és possible que transferim i/o emmagatzemem dades a proveïdors o servidors ubicats en altres països, fins i tot fora de l’Espai Econòmic Europeu. Si resideixes a l’Espai Econòmic Europeu, tingues en compte que aquests altres països poden no tenir lleis de protecció de dades tan restrictives. Tot i això, ens comprometem a establir les mesures necessàries d’acord amb aquesta política de privadesa i la llei aplicable.</div>
						<div class="section-content new-paragraph">Adoptem les pràctiques adequades de recopilació, emmagatzematge i processament de dades, així com mesures de seguretat per a protegir-nos contra l’accés, l’alteració, la publicació o la destrucció de les dades i informació personal. No obstant això, tingues en compte que no podem garantir al 100% la seguretat a Internet, i que la transmissió de les teves dades als nostres serveis es fa sota el teu propi risc.</div>
						<div class="section-content new-paragraph">Només retindrem la teva informació mentre sigui necessària per a les finalitats detallades en aquesta política de privadesa. En cap cas el període d’emmagatzematge serà superior a 2 anys. Si ja no necessitem les dades, les suprimirem o les anonimitzarem i, si no és possible (per exemple, perquè s’han emmagatzemat en còpies de seguretat), les arxivarem de manera segura i les aïllarem de qualsevol altre processament fins que en sigui possible la supressió.</div>
						<h3 class="section-title">Canvis a aquesta política de privadesa</h3>
						<div class="section-content">Aquest document s’ha actualitzat per darrera vegada el <b>22 de juny del 2023</b>. Ens reservem la possibilitat d’actualitzar aquesta política de privadesa en qualsevol moment. Quan ho fem, modificarem aquesta data de darrera actualització. Recomanem als usuaris que comprovin freqüentment els canvis d’aquesta pàgina per a mantenir-se informats de com ajudem a protegir la informació personal que recopilem. Acceptes que és la teva responsabilitat revisar aquesta política de privadesa periòdicament. L’ús del lloc web o dels serveis després de la modificació d’aquesta política implica l’acceptació de la nova política.</div>
						<h3 class="section-title">Drets i informació de contacte</h3>
						<div class="section-content">Si resideixes a l’Espai Econòmic Europeu, tens certs drets que pots exercir en qualsevol moment. Tant si vols exercir els teus drets com si tens qüestions sobre aquesta política de privadesa, les nostres pràctiques o les interaccions amb el lloc web, pots contactar amb el delegat de protecció de dades fent servir l’enllaç de contacte de la part inferior de la pàgina.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
