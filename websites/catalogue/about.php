<?php
require_once("db.inc.php");

$header_tab="about";

$header_page_title="Qui som?";

$header_social = array(
	'title' => 'Qui som? | '.$config['site_title'],
	'url' => $config['base_url'].'/qui-som',
	'description' => "Aquest portal és un projecte sorgit dels esforços conjunts dels principals fansubs en català i s'allotja a Fansubs.cat. El nostre únic objectiu és potenciar el consum d'anime i manga en català, permetent-ne la visualització en línia, i creiem que aquest web és un dels camins per a arribar-hi.",
	'image' => $config['preview_image']
);

require_once('header.inc.php');
?>
				<div class="section">
					<h2 class="section-title"><span class="iconsm fa fa-fw fa-users"></span> Qui som?</h2>
					<div class="section-content">Aquest portal és un projecte sorgit dels esforços conjunts dels principals fansubs en català i s'allotja a Fansubs.cat. El nostre únic objectiu és potenciar el consum d'anime i manga en català, permetent-ne la visualització en línia, i creiem que aquest web és un dels camins per a arribar-hi.</div>
					<div class="section-content new-paragraph">El web és un recull de tot el contingut subtitulat o editat pels fansubs en català. No en valorem la qualitat ni hi fem cap correcció, per tant, sigues conscient que algunes obres poden tenir una qualitat inferior a l'esperada. Si no t'agrada algun fansub en concret, el pots desactivar a les opcions per a amagar-ne el contingut.</div>
					<h2 class="section-title new-paragraph"><span class="iconsm fa fa-fw fa-file-alt"></span> Declaració de principis</h2>
					<div class="section-content">En aquest portal sols trobaràs anime i manga que no ha estat llicenciat ni editat oficialment en català. Quan sabem que una obra es llicencia per a editar-se en català, es retira immediatament del web amb l'objectiu d'ajudar-ne la comercialització.</div>
					<div class="section-content new-paragraph">Considerem que fem una tasca que, en un país normal, no hauríem de fer. L’objectiu original d’un fansub és acostar al públic material poc rendible comercialment. Malauradament, si parlem de manga i anime en català, les obres susceptibles d'entrar en aquesta categoria són totes, i és que actualment, aquest sector està en una situació crítica: no hi aposten ni les editorials, ni la televisió pública, ni les plataformes de contingut a la carta.</div>
					<div class="section-content new-paragraph">Hem explorat vies per a aconseguir editar el material de manera oficial, però no és viable sense una inversió forta pel sector per part dels ens públics, cosa que de moment no es produeix. Per aquest motiu, hem decidit fer aquesta feina com bonament podem: durant el nostre temps lliure i de manera completament altruista. El material que trobaràs recollit en aquest portal n'és el resultat.</div>
					<div class="section-content new-paragraph">Com que és una tasca altruista, aquest web no té, ni tindrà mai, publicitat de cap mena, i no en rebem cap rendiment econòmic..</div>
					<div class="section-content new-paragraph">Desitgem de tot cor que gaudeixis del nostre contingut, i ens alegrarà molt que ens en facis arribar comentaris.</div>
					<h2 class="section-title new-paragraph"><span class="iconsm fa fa-fw fa-envelope-open-text"></span> Contacte</h2>
					<div class="section-content">Pots contactar amb nosaltres fent servir <a class="contact-link">aquest formulari</a>. Si vols contactar amb un fansub en concret, pots fer-ho mitjançant el seu lloc web o el seu Twitter.</div>
				</div>
<?php
require_once('footer.inc.php');
?>
