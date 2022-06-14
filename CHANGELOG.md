# Registre de canvis

## 2022-06-14 - Versió 4.0.4
- **General:**
	- S'afegeix aquest registre de canvis.
- **Administració:**
	- Solució a l'error que impedia afegir capítols a una fitxa.

## 2022-06-13 - Versió 4.0.3
- **Catàleg:**
	- Es mostra entre parèntesis l'autor de la versió en el cas dels fansubs independents (a la previsualització de xarxes socials i a les fitxes). A la resta de llocs continua sent «Fansub independent» a seques.
- **Administració:**
	- S'admeten identificadors no numèrics en el cas d'acció real (MyDramaList requereix un «slug»).
	- S'afegeix un camp nou «Autor de la versió» vàlid només per a fansubs independents.
	- Es canvia el tractament de diversos camps perquè no sigui possible introduir-hi dades si no es compleixen certes condicions.
	- S'inhabilita la pujada de portades per a temporades que no siguin de manga.
- **Serveis:**
	- S'actualitzen les puntuacions de continguts d'acció real mitjançant web scraping a MyDramaList (no hi ha API pública).

## 2022-06-12 - Versió 4.0.2
- **Catàleg:**
	- Afegida una redirecció de les URLs de les portades anteriors a les noves (problemes de la memòria cau del Tachiyomi).
- **API:**
	- S'admeten identificadors majors de 10000 (nous) i menors o iguals (antics). Soluciona problemes amb el Tachiyomi.
	- Es retorna error si l'identificador no és numèric (abans es mostrava una resposta buida).
- **Administració:**
	- Solució a l'error que impedia actualitzar versions als administradors de fansubs.

## 2022-06-11 - Versió 4.0.1
- **Catàleg:**
	- Solució a l'error que feia que en manga no es poguessin visitar les pestanyes de One-shots i Serialitzats.

## 2022-06-10 - Versió 4.0.0
- **General:**
	- S'ha unificat el codi dels portals d'anime, manga i (en el futur) acció real en un de sol, anomenat catàleg.
	- S'elimina Google Analytics.
- **Catàleg:**
	- Es canvien alguns textos per a fer-los més genèrics i que serveixin alhora per a anime, manga i acció real.
	- L'enllaç principal de la capçalera enllaça a www.fansubs.cat i no al portal actual.
	- La previsualització per a xarxes socials mostra el tipus de còmic.
- **Advent:**
	- Nou codi que llegeix la configuració dels calendaris de la base de dades.
- **Administració:**
	- Es pot especificar el tipus de còmic a la fitxa dels mangues: manga, manhwa, manhua o altres.
	- Es poden posar opcionalment portades a les temporades d'anime (de la mateixa manera que ja es podia als volums de manga).
	- S'han afegit avisos quan es modifica algun camp important per al sistema (identificador de sèrie, processament dels vídeos, carpeta d'emmagatzematge).
	- Els calendaris d'advent ara són editables al tauler d'administració.
	- S'ha mogut el camp de durada dels capítols de la fitxa de l'anime a la de la versió. S'ha d'especificar en HH:MM:SS. Als enllaços automàtics es calcula automàticament en importar-los.
	- S'ha unificat «animes relacionats» i «mangues relacionats» en una sola llista de «contingut relacionat». Al web públic es continuen separant.
	- Canviat "usuari" per "administrador", per a no confondre'ls amb usuaris reals.
	- S'ha eliminat Google Drive com a compte d'autoimportació (no es feia servir i el codi estava desfasat).
- **Serveis:**
	- Als tweets i missatges es mostra el tipus de còmic.

## 2022-06-06 - Versió 3.10.9
- **Eines:**
	- Correcció d'un error a l'script de conversió per a Windows.

## 2022-06-03 - Versió 3.10.8
- **Serveis:**
	- No es publica contingut explícit al Discord.

## 2022-06-02 - Versió 3.10.7
- **Anime:**
	- Afegit l'enllaç "Comenta-ho a HitotsumeCAT" amb la URL d'invitació al Discord.
	- Corregit que no es mostressin els controls d'iPhone en passar a pantalla completa.
	- Actualitzat FontAwesome.
- **Manga:**
	- Afegit l'enllaç "Comenta-ho a HitotsumeCAT" amb la URL d'invitació al Discord.
	- Actualitzat FontAwesome.
- **Serveis:**
	- Possibilitat de notificar via webhooks de Discord de les novetats d'anime/manga.

## 2022-05-12 - Versió 3.10.6
- **API:**
	- Nou endpoint intern per a canviar la miniatura d'un enllaç.
- **Administració:**
	- El camp «hidden» es defineix automàticament depenent de si la versió té enllaços o no.
	- S'elimina l'opció «Amaga aquesta versió mentre estigui buida» a les fitxes.
- **Eines:**
	- Nou script per a regenerar totes les miniatures de capítols.
	- Els scripts que processen fitxers informen automàticament de la durada i la miniatura en processar-los.

## 2022-05-04 - Versió 3.10.5
- **Administració:**
	- Es canvia el valor per defecte del processament de vídeo a «Recomprimeix el vídeo i l'àudio».
	- Ja no es mostra el nombre de resultats a les pàgines de cerques (era una consulta massa pesada). Es pot forçar passant el paràmetre «show\_results».

## 2022-05-03 - Versió 3.10.4
- **Administració:**
	- Ara la pàgina de manteniment també mostra animes/mangues sense cap gènere.
- **Eines:**
	- Nou script per a comprovar la taxa de bits màxima dels fitxers de vídeo.
	- Penjats els scripts de conversió de fitxers al format del portal, tant per a Linux com per a Windows. Inclouen una limitació a la taxa de bits.
	- Canviat l'script que comprova i migra els fitxers nous a la darrera versió.

## 2022-04-27 - Versió 3.10.3
- **Anime:**
	- Imatge de xarxes socials generada automàticament amb les dades de les versions.
- **Manga:**
	- Imatge de xarxes socials generada automàticament amb les dades de les versions.

## 2022-04-16 - Versió 3.10.2
- **Anime:**
	- Actualitzada la consulta de més populars.
	- Actualitzats els gèneres per a les recomanacions de Sant Jordi perquè encaixin amb els darrers de MyAnimeList.
- **Manga:**
	- Actualitzada la consulta de més populars.
	- Actualitzats els gèneres per a les recomanacions de Sant Jordi perquè encaixin amb els darrers de MyAnimeList.
- **Administració:**
	- Es pot veure el total de tota la història del portal a «Els més populars».
	- Corregit el càlcul de visualitzacions (només mostraven la versió més alta; ara mostren la suma de versions).

## 2022-04-13 - Versió 3.10.1
- **Anime:**
	- Nova llista d'animes: «Recentment completats».
- **Manga:**
	- Nova llista de mangues: «Recentment completats».
- **Administració:**
	- En editar mangues i animes, es desa la data de compleció perquè les webs puguin fer-la servir.

## 2022-04-10 - Versió 3.10.0
- **Administració:**
	- Nova pàgina de manteniment per a verificar animes i mangues no enllaçats a MyAnimeList, i per a comprovar les diferències de gèneres informats.
	- El funcionament dels gèneres a les fitxes internes s'ha adaptat al nou format de MyAnimeList, agrupant-los per tipus. S'han sincronitzat els diferents gèneres.
	- S'ha canviat que els enllaços d'edició/estadístiques/supressió/etc no saltin de línia (problema causat per mangues/animes amb noms molt llargs).
	- S'elimina l'opció de buidar les visualitzacions a la pàgina de manteniment.
	- S'elimina l'opció de buidar l'historial de cerques a la pàgina de manteniment.
	- S'elimina l'opció de buidar el registre d'accions a la pàgina de manteniment.
- **Serveis:**
	- S'ha migrat a l'API v4 de Jikan (abans fèiem servir la v3).
	- Es tenen en compte les temporades/volums a banda de l'anime/manga per a fer la mitjana de puntuacions.

## 2022-03-16 - Versió 3.9.26
- **Administració:**
	- No s'actualitza la data d'actualització d'un manga si no es penja cap capítol.

## 2022-01-07 - Versió 3.9.25
- **Administració:**
	- Nou avís en esborrar un capítol, indicant que se'n perdran les estadístiques.

## 2022-01-06 - Versió 3.9.24
- **Administració:**
	- Es valida que hi hagi resolució informada en afegir un enllaç a un capítol.

## 2022-01-03 - Versió 3.9.23
- **Administració:**
	- Nova pàgina per a veure l'estat dels servidors d'emmagatzematge.

## 2021-12-28 - Versió 3.9.22
- **Anime:**
	- Si no hi ha cap servidor d'emmagatzematge, no es mostren els enllaços d'emmagatzematge.
	- Correcció de l'error que feia que les icones del reproductor de vídeo no es veiessin als Sants Innocents.

## 2021-12-04 - Versió 3.9.21
- **Advent:**
	- Es permeten enllaços al protocol "javascript:" per a indicar que una casella encara no està llesta.

## 2021-12-01 - Versió 3.9.20
- **Anime:**
	- Correcció del disseny de les imatges que enllacen al calendari d'advent.
- **Manga:**
	- Correcció del disseny de les imatges que enllacen al calendari d'advent.
- **Advent:**
	- Correcció d'un error que feia que no s'obrissin les caselles.

## 2021-11-29 - Versió 3.9.19
- **Advent:**
	- Canvis al disseny per al 2021.

## 2021-11-21 - Versió 3.9.18
- **Anime:**
	- Es mostra l'enllaç al calendari d'advent automàticament quan toca.
	- Les cerques mostraven animes ocults; s'ha solucionat.
- **Manga:**
	- Es mostra l'enllaç al calendari d'advent automàticament quan toca.
	- Les cerques mostraven mangues ocults; s'ha solucionat.
- **Advent:**
	- Nova imatge de fons.
- **Administració:**
	- Estadístiques anuals a «Els més populars».
	- L'usuari «Administrador» pot ometre la recreació d'enllaços d'anime amb una casella de selecció.
- **Serveis:**
	- Corregida consulta que feia petar la generació de recomanacions.
	- Corregides les comes en alguns tweets.

## 2021-11-03 - Versió 3.9.17
- **Advent:**
	- Nou calendari d'advent del 2021.
- **Administració:**
	- Nova pàgina «Els més populars».
- **Serveis:**
	- Es recomanen animes i mangues encara que no tinguin puntuació, i si es força la recomanació, també si estan per sota del llindar.

## 2021-09-24 - Versió 3.9.16
- **Anime:**
	- Nou paràmetre «show\_hidden» per a mostrar animes ocults.
- **Manga:**
	- Nou paràmetre «show\_hidden» per a mostrar mangues ocults.
- **Serveis:**
	- Un Tortosí Otaku canvia de nom a Kukafera no Fansub (i en canvia l'obtenció).

## 2021-08-24 - Versió 3.9.15
- **API:**
	- Si un enllaç s'ha actualitzat mentre es convertia, es descarta.
- **Eines:**
	- Nou script per a detectar fitxers que són al servidor però no estan enllaçats a la web.
- **Serveis:**
	- S'augmenta l'interval entre crides a l'API de Jikan de 4 a 10 segons (actualització de puntuacions).

## 2021-08-10 - Versió 3.9.14
- **Anime:**
	- Es corregeix «web» a «webs» si són diversos fansubs.
- **Manga:**
	- Es corregeix «web» a «webs» si són diversos fansubs.

## 2021-08-08 - Versió 3.9.13
- **Notícies:**
	- S'incorporen notícies d'Un Tortosí Otaku.
- **Serveis:**
	- S'incorpora l'obtenció de notícies d'Un Tortosí Otaku.

## 2021-08-01 - Versió 3.9.12
- **Anime:**
	- El «tooltip» d'opcions no es tancava, solucionat.
- **Serveis:**
	- Se soluciona l'obtenció de data de les notícies de Projecte Nou Món.

## 2021-07-19 - Versió 3.9.11
- **Anime:**
	- Canvis als textos d'avís a les fitxes: diferencien fansubs i fandubs, entre altres.
- **Manga:**
	- Canvis als textos d'avís a les fitxes.

## 2021-07-13 - Versió 3.9.10
- **Administració:**
	- Se soluciona que el camp «perdut» es perdi en inserir un nou anime.

## 2021-07-11 - Versió 3.9.9
- **API:**
	- Solució per a alguns capítols de manga que no mostraven el títol.
- **Serveis:**
	- S'obtenen només les notícies de Projecte Nou Món que contenen l'etiqueta «Fansub».

## 2021-07-01 - Versió 3.9.8
- **Manga:**
	- S'afegeix redirecció per a un volum antic de «La Tomo és una noia!» al Piwigo.
- **Anime:**
	- Millores a la implementació de Google Cast.

## 2021-06-25 - Versió 3.9.7
- **Notícies:**
	- S'incorporen notícies de Projecte Nou Món.
- **Administració:**
	- Correcció d'error en desar les relacions anime-manga.
- **Serveis:**
	- S'incorpora l'obtenció de notícies de Projecte Nou Món.

## 2021-06-21 - Versió 3.9.6
- **Notícies:**
	- S'incorporen notícies de YacchySubs.
	- Es corregeix «L'enhorabona» per «Enhorabona».
- **Serveis:**
	- S'incorpora l'obtenció de notícies de YacchySubs.

## 2021-06-02 - Versió 3.9.5
- **Anime:**
	- Si es produeix un error, se surt de la pantalla completa.
	- Millores a la implementació de Google Cast.
- **Administració:**
	- Les gràfiques de manga comencen a comptar el 01/2021 i no el 06/2020.

## 2021-05-16 - Versió 3.9.4
- **Anime:**
	- El total d'animes (arrodonit) ja no és un text; es calcula en base a la base de dades.
- **Manga:**
	- El total de mangues (arrodonit) ja no és un text; es calcula en base a la base de dades.

## 2021-05-06 - Versió 3.9.3
- **API:**
	- Ja no es compten per duplicat les lectures al Tachiyomi (error causat per un canvi a la implementació del Tachiyomi).

## 2021-05-05 - Versió 3.9.2
- **Anime:**
	- Es desa l'origen de les visualitzacions igual que al manga.
- **Administració:**
	- Es mostra l'origen de les visualitzacions igual que al manga.

## 2021-05-04 - Versió 3.9.1
- **Anime:**
	- Millores i correccions a la implementació per a incrustació.
- **Manga:**
	- Millores i correccions a la implementació per a incrustació.
- **Administració:**
	- Millores als registres generats en editar elements.

## 2021-05-03 - Versió 3.9.0
- **Anime:**
	- Suport per a Google Cast!
	- Canviat el reproductor de vídeo de Plyr a VideoJS.
- **Administració:**
	- Solució d'error quan les visualitzacions tenen zero bytes en total.

## 2021-05-01 - Versió 3.8.2
- **General:**
	- Nou sistema de seguiment de visualitzacions: per mida o temps.
- **Anime:**
	- S'afegeix el fansub i la portada als enllaços d'anime (per a una futura implementació de Google Cast).
	- Gestió automàtica dels Sants Innocents i de Sant Jordi.
	- Actualitzat FontAwesome.
	- Corregida la cerca: no tenia en compte els espais en el cas dels gèneres.
- **Manga:**
	- Gestió automàtica dels Sants Innocents i de Sant Jordi.
	- Actualitzat FontAwesome.
	- Corregida la cerca: no tenia en compte els espais en el cas dels gèneres.
- **API:**
	- Se soluciona l'error pel qual no desava res al registre.
- **Administració:**
	- Es mostra el navegador de les visualitzacions.
	- Per defecte, es filtren els esdeveniments més habituals del registre del sistema.
	- Se soluciona l'error pel qual es defineixen igual tots els enllaços de baixada dels diferents fansubs d'una versió.
	- Se soluciona que el camp «perdut» es perdi entre actualitzacions.
	- Ja no es mostren el temps mitjà de visualització ni lectura als gràfics d'anime i manga.

## 2021-04-03 - Versió 3.8.1
- **Anime:**
	- S'envia l'identificador de reproducció a l'emmagatzematge.
- **API:**
	- S'afegeixen algunes crides al registre.
- **Administració:**
	- Gràfics diaris a les pàgines d'estadístiques de versions.

## 2021-04-01 - Versió 3.8.0
- **Anime:**
	- Millores de notificació d'errors de reproducció.
	- Canvis en la visualització de les resolucions.
	- Actualitzades les dependències.
- **Manga:**
	- Actualitzades les dependències.
- **API:**
	- Nou endpoint «change\_link\_episode\_duration», per a corregir errors de durada remotament.
- **Recursos:**
	- Versió inicial. Nou portal amb informació per a fansubejar (wiki de BookStack).
- **Administració:**
	- Nou tipus de processament de fitxers en importar-los a l'emmagatzematge.
	- Corregits els noms dels capítols al verificador d'enllaços.
- **Eines:**
	- Versió inicial. Scripts d'ús intern per a la gestió i comprovació de fitxers.

## 2021-03-15 - Versió 3.7.10
- **Anime:**
	- En acabar un vídeo, es mostra un botó per a repetir el vídeo actual o saltar al següent.
	- Es desa IP i agent d'usuari de les visualitzacions per a associar-les a un usuari.
	- Millores de notificació d'errors de reproducció.
- **Manga:**
	- Es desa IP i agent d'usuari de les visualitzacions per a associar-les a un usuari.
- **Administració:**
	- Es mostra una icona identificativa al costat dels enllaços.
	- Es mostren usuaris a les visualitzacions (anonimitzats).
	- Corregit un error que feia que es veiessin errors duplicats.

## 2021-03-15 - Versió 3.7.9
- **Administració:**
	- Nou tipus de processament de fitxers en importar-los a l'emmagatzematge.
- **Anime:**
	- Correcció de codi als URLs d'incrustació.
- **Manga:**
	- Correcció de codi als URLs d'incrustació.

## 2021-03-11 - Versió 3.7.8
- **Administració:**
	- Els enllaços d'emmagatzematge s'esborren en canviar l'enllaç de MEGA.

## 2021-03-10 - Versió 3.7.7
- **Serveis:**
	- Els tweets fan servir la mateixa lògica que la pàgina per als noms de capítols.

## 2021-03-08 - Versió 3.7.6
- **Anime:**
	- S'amaga el cursor del ratolí en reproduir el vídeo.
	- Actualització del reproductor de vídeo.
	- Millores de notificació d'errors de reproducció.
	- S'afegeix un mètode per a configurar la URL de l'emmagatzematge.
- **Administració:**
	- Canvis en la gestió d'enllaços d'emmagatzematge.

## 2021-03-03 - Versió 3.7.5
- **Anime:**
	- Millores de notificació d'errors de reproducció.
- **Administració:**
	- Es pot especificar el tipus d'importació de fitxers de MEGA a l'emmagatzematge.

## 2021-03-03 - Versió 3.7.4
- **Anime:**
	- Ara s'envia correctament el referrer als servidors d'emmagatzematge.
	- Millores de notificació d'errors de reproducció.
- **Administració:**
	- Correcció d'errors al verificador d'enllaços.
- **Serveis:**
	- Els fansubs s'ordenen per ordre alfabètic als tweets.

## 2021-03-02 - Versió 3.7.3
- **Administració:**
	- Quan es canvien enllaços de MEGA, s'esborra la versió de l'emmagatzematge.

## 2021-03-01 - Versió 3.7.2
- **Notícies:**
	- S'incorporen notícies d'Ou ferrat.
- **Administració:**
	- L'obtenció d'enllaços amb ampersands ara funciona correctament.
- **Serveis:**
	- S'incorpora l'obtenció de notícies d'Ou ferrat.

## 2021-02-28 - Versió 3.7.1
- **Anime:**
	- S'utilitzen enllaços directes a servidors d'emmagatzematge si existeixen.
- **Administració:**
	- Es poden filtrar els registres per a ocultar-ne els missatges més comuns.
	- Solució d'errors a la pàgina de verificació d'enllaços.
	- L'obtenció d'enllaços amb espais ara funciona correctament.

## 2021-02-26 - Versió 3.7.0
- **API:**
	- Nous mètodes interns: get\_unconverted\_links i insert\_converted\_link.
- **Administració:**
	- Es poden configurar carpetes d'emmagatzematge.
	- Solució d'errors a la pàgina d'enllaços de la versió de manga.

## 2021-02-25 - Versió 3.6.7
- **Administració:**
	- Es poden importar alhora enllaços directes i de MEGA.
	- Noves pàgines d'enllaços de versions d'anime i de manga.
- **Anime:**
	- Solució d'errors a la pàgina d'incrustació.
- **Manga:**
	- Solució d'errors a la pàgina d'incrustació.

## 2021-02-23 - Versió 3.6.6
- **General:**
	- Diverses petites correccions i millores de codi.
- **Anime:**
	- Millores als informes d'errors de reproducció.
- **Administració:**
	- Es poden importar enllaços directes.

## 2021-02-20 - Versió 3.6.5
- **General:**
	- Diverses petites correccions i millores de codi.
- **Anime:**
	- Millores als informes d'errors de reproducció.
- **Administració:**
	- Nous gràfics amb els orígens de les visualitzacions i lectures, i nombre d'enllaços per fansub.

## 2021-02-19 - Versió 3.6.4
- **Anime:**
	- La cerca es fa sobre anime i després sobre manga.
	- No es mostren les versions ocultes.
- **Manga:**
	- La cerca es fa sobre manga i després sobre anime.
	- No es mostren les versions ocultes.
- **Administració:**
	- Es permet ocultar versions d'anime i manga.
	- Les relacions de manga-manga, anime-anime i manga-anime són ara bidireccionals, no cal donar-les d'alta als dos costats.

## 2021-02-18 - Versió 3.6.3
- **Anime:**
	- Es despleguen o no els extres d'una versió segons la configuració.
- **Manga:**
	- Es despleguen o no els extres d'una versió segons la configuració.
- **Administració:**
	- Es permet especificar si els extres d'una versió han d'estar desplegats o no per defecte.
	- S'augmenta el límit intern de variables màximes enviades en un formulari (soluciona l'error amb «One Piece»).

## 2021-02-16 - Versió 3.6.2
- **General:**
	- Canvis a la base de dades per a permetre múltiples enllaços per fitxer.

## 2021-02-16 - Versió 3.6.1
- **General:**
	- Diverses petites correccions i millores de codi.
- **Manga:**
	- S'informa dels errors de migració d'URLs del Piwigo a les noves.

## 2021-02-13 - Versió 3.6.0
- **General:**
	- Diverses petites correccions i millores de codi.
- **Anime:**
	- Si es produeixen errors de reproducció, s'informen.
	- S'ha canviat el reproductor de l'iframe de MEGA al reproductor Plyr.
	- S'ha adaptat el codi de càrrega de fitxers de MEGA al reproductor Plyr.
	- Ja no es mostrarà mai publicitat (provenia del reproductor de MEGA).
- **Manga:**
	- Petites correccions d'errors.
- **Administració:**
	- Nova pàgina de visualització d'errors de reproducció.
	- Verificació d'enllaços directes.
	- L'informe de cerques ara té en compte les cerques per gènere.

## 2021-02-07 - Versió 3.5.2
- **Anime:**
	- Si un anime no tenia valoració per edats, no es mostrava; s'ha corregit.
- **Serveis:**
	- Els tweets s'escurcen automàticament si són massa llargs.
	- Els tweets diferencien entre fansubs i fandubs.

## 2021-02-04 - Versió 3.5.1
- **Anime:**
	- S'agrupen les temporades buides en una única línia. En prémer-la, es mostren totes (com al manga).
	- Si els capítols estan perduts, també s'agrupen com a buits.
	- S'han agrupat films separats en fitxes juntes, i s'han afegit redireccions de les URLs velles a les noves.
- **Manga:**
	- Si els capítols estan perduts, també s'agrupen com a buits.
- **Administració:**
	- Si l'anime/manga està obert/en publicació, mostra «Obert» i no «-1» al nombre de capítols.

## 2021-02-03 - Versió 3.5.0
- **Manga:**
	- Es llança l'extensió del Tachiyomi.
	- Nou enllaç a la part superior si s'hi accedeix amb Android indicant que hi ha l'extensió del Tachiyomi.
- **API:**
	- Els capítols mostren el nom del volum al títol.

## 2021-02-02 - Versió 3.4.8
- **API:**
	- Correcció d'errors.
- **Administració:**
	- Les visualitzacions via API es compten per separat.

## 2021-02-01 - Versió 3.4.7
- **Administració:**
	- Nou avís en afegir notícies de manera manual.
	- No es permet suprimir capítols quan això podria causar problemes d'integritat.

## 2021-01-30 - Versió 3.4.6
- **Manga:**
	- Els tweets fan servir «llegir» en lloc de «mirar».

## 2021-01-29 - Versió 3.4.5
- **Anime:**
	- La cerca admet el nom d'un gènere.
- **Manga:**
	- La cerca admet el nom d'un gènere.
- **API:**
	- Nous endpoints per a l'obtenció de manga recent, manga popular, cerca de manga, detalls de manga, capítols de manga i pàgines de manga (per a la futura extensió del Tachiyomi).

## 2021-01-25 - Versió 3.4.4
- **Manga:**
	- Es canvia el text «100 mangues» per «150 mangues».

## 2021-01-24 - Versió 3.4.3
- **Anime:**
	- Es canvia el text «Novetat» per una icona.
	- Es mostren els fandubs indicant-ho amb un avís i una icona.
- **Manga:**
	- Es canvia el text «Novetat» per una icona.
- **Administració:**
	- Es pot especificar si un fansub és fansub o fandub.

## 2021-01-21 - Versió 3.4.2
- **Administració:**
	- En carregar manga, s'ignoren els fitxers «__MACOSX».

## 2021-01-20 - Versió 3.4.1
- **Manga:**
	- S'agrupen els volums buits en una única línia. En prémer-la, es mostren tots.
	- Opció per a triar llegir el manga en sentit oriental o occidental.
	- Petites correccions d'errors.
- **Administració:**
	- Petites correccions d'errors.

## 2021-01-18 - Versió 3.4.0
- **Anime:**
	- Nova llista amb mangues relacionats.
- **Manga:**
	- Migració a un portal nou de manga, partint d'una còpia del d'anime.
	- Afegit un seguit de redireccions per a convertir els enllaços antics del Piwigo a enllaços nous.
- **Notícies:**
	- Barra lateral de menú en mòbils.
- **Administració:**
	- Gestió de manga i versions de manga, estadístiques de manga, etc.

## 2021-01-16 - Versió 3.3.2
- **Anime:**
	- Nova icona de «reprodueix» en passar per sobre de les portades.

## 2021-01-15 - Versió 3.3.1
- **General:**
	- Canvis interns de cara a l'estrena del nou portal de manga.
	- Diverses petites correccions i millores de codi.

## 2021-01-09 - Versió 3.3.0
- **General:**
	- S'apunta a la nova base de dades unificada.
	- Diverses petites correccions i millores de codi.
- **Notícies:**
	- Qüestió de seguretat per a evitar missatges brossa en els formularis de contacte.
	- S'elimina de la versió pública del web la pàgina d'estat dels recollidors.
- **Advent:**
	- Es mou el contingut a nadal.fansubs.cat i es fa configurable amb un fitxer.
- **Administració:**
	- Es permet l'edició de notícies, obtenidors i fansubs.
	- S'afegeix la pàgina d'estat de recollidors.
	- Es canvia el portal a admin.fansubs.cat, l'objectiu és que es gestioni tot el contingut de manera centralitzada aquí i no a cada subdomini.
	- S'han canviat tots els textos de «sèrie» a «anime».
- **Serveis:**
	- Correcció de l'obtenció de notícies d'AniMugen Fansub.
	- Correcció de l'obtenció d'imatges de notícies de Blogspot.

## 2020-12-27 - Versió 3.2.4
- **Anime:**
	- Les recomanacions mostren els animes pitjor valorats el dia dels Sant Innocents, i Comic Sans.

## 2020-12-26 - Versió 3.2.3
- **Anime:**
	- S'elimina l'enllaç al calendari d'advent.
- **Notícies:**
	- S'elimina l'enllaç al calendari d'advent.
- **Administració:**
	- Millorada la visualització dels noms dels capítols a les visualitzacions.

## 2020-12-07 - Versió 3.2.2
- **Advent:**
	- Correcció d'estils.

## 2020-11-28 - Versió 3.2.1
- **Advent:**
	- Correcció d'estils.

## 2020-11-25 - Versió 3.2.0
- **Anime:**
	- Enllaç al calendari d'advent a la part superior de la pàgina principal.
- **Notícies:**
	- Enllaç al calendari d'advent al lateral.
- **Advent:**
	- Versió inicial del calendari d'advent 2020.

## 2020-11-16 - Versió 3.1.12
- **Anime:**
	- A darreres actualitzacions, s'enllaça a la darrera versió actualitzada, si n'hi ha més d'una.
- **Serveis:**
	- Millor control dels errors en publicar tweets.

## 2020-10-28 - Versió 3.1.11
- **Anime:**
	- Canvi d'un text i petites millores de disseny.

## 2020-10-24 - Versió 3.1.10
- **Administració:**
	- Millores en la visualització de les darreres visualitzacions.

## 2020-10-23 - Versió 3.1.9
- **Administració:**
	- S'han mogut les darreres visualitzacions a una nova pàgina independent.

## 2020-10-22 - Versió 3.1.8
- **Serveis:**
	- Nou script per a importar les visualitzacions perdudes.

## 2020-10-21 - Versió 3.1.7
- **Anime:**
	- Millora del sistema de seguiment de visualitzacions.
	- Canvis de textos i del disseny per a mòbils.

## 2020-10-20 - Versió 3.1.6
- **Anime:**
	- Petits canvis de disseny.
- **Serveis:**
	- Corregit error al generador de recomanacions.

## 2020-10-19 - Versió 3.1.5
- **Anime:**
	- Canvis de disseny, textos i estructura del codi.
- **Administració:**
	- Canvis de codi en la pujada de fitxers.

## 2020-10-18 - Versió 3.1.4
- **Anime:**
	- Càrrega de portades d'anime, desvinculant-nos de MyAnimeList.
	- Més seguiment analític.
	- Si no hi ha imatge pujada, no es mostra el text d'error.
	- Corregit l'estil dels textos de temporades i novetat.
	- S'elimina l'opció del navegador de baixar els vídeos.
- **Administració:**
	- Es mostren les 10 darreres visualitzacions.

## 2020-10-17 - Versió 3.1.3
- **Anime:**
	- Icones a davant de cada secció.
	- Es fa un seguiment dels clics a les llistes.
	- Avís quan es consulta una fitxa amb versions amagades i nou paràmetre «f» per a mostrar-les totes.
	- Les recomanacions es mostren ara amb una imatge grossa.
- **Administració:**
	- Nous camps: «imatge destacada» i «mostra sempre com a recomanat».
- **Serveis:**
	- S'apliquen les recomanacions que tinguin marcat «mostra sempre com a recomanat».

## 2020-10-10 - Versió 3.1.2
- **Anime:**
	- S'especifica que el nombre de recomanacions són 12.
- **Serveis:**
	- Es modifica l'obtenció de notícies d'AniMugen Fansub (nou disseny).

## 2020-10-06 - Versió 3.1.1
- **Anime:**
	- Es mostren les darreres actualitzacions per damunt de les recomanacions.
	- Es canvien les descripcions de les llistes.

## 2020-10-04 - Versió 3.1.0
- **Anime:**
	- Nova llista amb recomanacions de sèries i films.
	- S'afegeixen icones a cada pestanya (Destacats, Films, Sèries).
- **Administració:**
	- Nou camp «recomanable» a les fitxes de versions.
- **Serveis:**
	- Nou script de generació de recomanacions setmanals.

## 2020-09-25 - Versió 3.0.9
- **Anime:**
	- Si hi ha més d'una versió d'una sèrie, es passa l'identificador per paràmetre.

## 2020-09-23 - Versió 3.0.8
- **Anime:**
	- Es clarifica al text de «Qui som?» que algun material és de baixa qualitat.
	- Per defecte, s'amaguen les sèries amb enllaços perduts.
- **Administració:**
	- S'afegeixen estadístiques diàries a banda de les mensuals.
- **Serveis:**
	- Es modifica l'obtenció de notícies de CatSub (nou disseny).
	- Es corregeixen els tweets (informaven malament del nombre de capítols).

## 2020-08-23 - Versió 3.0.7
- **Anime:**
	- Nova llista de mangues relacionats a la fitxa de cada anime.
- **Administració:**
	- Es poden afegir mangues relacionats a la fitxa de l'anime.
	- Es permet verificar enllaços només d'una versió.
- **Serveis:**
	- Si hi ha temporades definides, es fa servir el seu nom per als tweets.

## 2020-08-14 - Versió 3.0.6
- **Manga:**
	- Es modifica el tema del Piwigo perquè admeti imatges de més de 1200px d'amplada.
- **Administració:**
	- Les estadístiques comencen al juny de 2020.

## 2020-08-13 - Versió 3.0.5
- **Anime:**
	- S'afegeix un botó per a anar al Twitter dels fansubs.
	- Es permet especificar una versió concreta als enllaços a sèries (paràmetre «v»).
	- Millores al seguiment de Google Analytics.
- **Administració:**
	- Les estadístiques només mostren la darrera setmana.
	- Solució dels errors a les estadístiques de la versió del fansub.
- **Serveis:**
	- Es fan tweets automàtics amb nous capítols d'anime/manga afegits.
	- Ja no es fan tweets automàtics de les noves notícies.

## 2020-07-01 - Versió 3.0.4
- **Anime:**
	- La cerca també cerca a les paraules clau.
- **Administració:**
	- S'afegeixen paraules clau a la fitxa de l'anime (per a la cerca).
- **Serveis:**
	- Es tanca la sessió de MEGA si l'obtenció de MEGA falla.

## 2020-06-28 - Versió 3.0.3
- **Anime:**
	- Correcció de la lògica de «novetat», es mostrava quan no tocava.
- **Administració:**
	- Nombre de resultats de cada cerca.

## 2020-06-24 - Versió 3.0.2
- **Anime:**
	- Indicador de «novetat».

## 2020-06-23 - Versió 3.0.1
- **Anime:**
	- S'informa de les cerques a la base de dades.
- **Administració:**
	- Consulta de cerques al portal d'anime.

## 2020-06-22 - Versió 3.0.0
- **Anime:**
	- Versió inicial. Nou portal d'anime.
- **Administració:**
	- Versió inicial. Nou portal d'administració.

## 2020-06-21 - Versió 2.4.6
- **General:**
	- Enllaç a les capçaleres al portal d'anime.

## 2020-06-16 - Versió 2.4.5
- **Serveis:**
	- Nou script per a enviar un tweet de manera manual.

## 2020-06-14 - Versió 2.4.4
- **General:**
	- Codi de seguiment de Google Analytics.

## 2020-06-11 - Versió 2.4.3
- **Notícies:**
	- Enllaç al Twitter al peu de la pàgina.
- **Serveis:**
	- Correcció del text dels tweets.

## 2020-06-10 - Versió 2.4.2
- **Serveis:**
	- S'envien tweets de cada notícia nova.

## 2020-05-03 - Versió 2.4.1
- **Manga:**
	- Versió inicial. Sols s'inclouen els temes visuals del Piwigo.
- **Notícies:**
	- Afegit selector de webs: Notícies, Manga i (en el futur) Anime.
	- Correccions de textos.

## 2020-01-11 - Versió 2.4.0
- **General:**
	- Codi actualitzat a PHP 7.3.
	- S'utilitza «utf8mb4» a les dades de les taules (permet emojis i altres caràcters especials).

## 2019-04-07 - Versió 2.3.9
- **Notícies:**
	- Apareix correctament «Versió històrica a Archive.org» als enllaços de fansubs històrics.
- **Serveis:**
	- Corregida l'obtenció de notícies d'AniMugen Fansub.
	- Corregida l'obtenció de notícies d'El Detectiu Conan en català (canviant Facebook per Mangadex).

## 2019-03-26 - Versió 2.3.8
- **Notícies:**
	- Els fansubs amb més de 2 anys d'inactivitat passen a «històrics» encara que el web continuï funcionant.

## 2018-12-16 - Versió 2.3.7
- **Serveis:**
	- Corregida l'obtenció de notícies d'AniMugen Fansub (nou disseny).

## 2018-07-09 - Versió 2.3.6
- **Notícies:**
	- Correcció per a l'estil de mida de text de les notícies de Lluna Plena.

## 2018-06-27 - Versió 2.3.5
- **Aplicació:**
	- Correcció per als enllaços que no eren clicables.

## 2018-06-26 - Versió 2.3.4
- **General:**
	- S'incorporen notícies d'AniMugen Fansub (només les que són en català).
- **Aplicació:**
	- Enllaç al portal de manga al menú principal.

## 2018-06-25 - Versió 2.3.3
- **Aplicació:**
	- Lògica d'avís i/o obligació d'actualització de l'aplicació.
	- Implementació parcial d'un lector de manga (desactivat).
	- Correcció de la URL de compartició.
	- Actualitzades les dependències.
- **Serveis:**
	- Nou script de detecció d'imatges no utilitzades.
	- Per raons tècniques, es limita la llargada dels fitxers d'imatge a 140 caràcters.

## 2018-02-25 - Versió 2.3.2
- **Notícies:**
	- Afegides les quatre barres a la icona de la pàgina.
- **Aplicació:**
	- Actualitzades les dependències.

## 2018-02-20 - Versió 2.3.1
- **Aplicació:**
	- La transició de notícia a imatge funciona correctament.
	- Neteja i millora de codi en general.

## 2018-02-16 - Versió 2.3.0
- **Notícies:**
	- Enllaços a l'aplicació i noves capçaleres per a l'aplicació.
- **Aplicació:**
	- Versió inicial. Permet veure les notícies i rebre notificacions push.
- **API:**
	- L'endpoint de notícies també permet fer cerques.

## 2017-09-18 - Versió 2.2.4
- **Notícies:**
	- Cerca de notícies.
	- Arreglada una de les imatges de capçalera.
	- Eliminat color de fons de la pàgina (abans era una imatge).

## 2017-09-14 - Versió 2.2.3
- **Serveis:**
	- Corregida l'obtenció d'imatges de CatSub.

## 2017-09-13 - Versió 2.2.2
- **General:**f
	- Canvi de directoris per al nou servidor.

## 2017-08-24 - Versió 2.2.1
- **Serveis:**
	- S'incorporen notícies del Facebook d'El Detectiu Conan en català.

## 2017-07-21 - Versió 2.2.0
- **General:**
	- Codi actualitzat a PHP 7.0 i MariaDB 10.1.
- **Notícies:**
	- Correccions de textos.
	- S'utilitzen URLs HTTPS a les dades de xarxes socials.

## 2017-05-22 - Versió 2.1.5
- **General:**
	- S'incorporen notícies de Shinsengumi no Fansub.
- **Notícies:**
	- Actualitzat el logo de CatSub.
- **API:**
	- Millorades les respostes d'error i corregida alguna consulta.

## 2017-01-07 - Versió 2.1.4
- **General:**
	- Es defineix correctament la codificació UTF-8 a la connexió a la base de dades.

## 2016-12-29 - Versió 2.1.3
- **Notícies:**
	- Corregida la URL de la imatge de previsualització.

## 2016-12-28 - Versió 2.1.2
- **Notícies:**
	- El 28 de desembre, Comic Sans.

## 2016-12-27 - Versió 2.1.1
- **Serveis:**
	- Corregida l'obtenció de Lluna Plena no Fansub a causa d'un error en el codi d'una notícia.

## 2016-10-03 - Versió 2.1.0
- **API:**
	- Nous mètodes per a obtenir fansubs i notícies.
- **Serveis:**
	- Corregida l'obtenció de Ronin Fansub (ara també té hora).

## 2016-09-10 - Versió 2.0.5
- **General:**
	- S'incorporen notícies d'AnliumSubs, Els millors animes i Ronin Fansub.
- **Notícies:**
	- S'escurça el text de «Torna a la pàgina principal» perquè no es talli.
	- Ja no es mostra el mètode d'obtenció a la pàgina d'estat.
- **Serveis:**
	- Es corregeix l'ús de majúscules als mesos en blogs de WordPress.

## 2016-07-22 - Versió 2.0.4
- **General:**
	- S'incorporen notícies de Bleach - Sub Català i Ebi no Fansub.
	- Es detecta i informa del nombre de notícies recollides a cada obtenció.

## 2016-07-21 - Versió 2.0.3
- **General:**
	- S'incorporen notícies de Barretina de Palla no Fansub.
- **Notícies:**
	- Pàgina d'estadístiques.
	- Els noms dels fansubs a la barra lateral es limiten a una línia.

## 2016-07-09 - Versió 2.0.2
- **Serveis:**
	- S'incorporen notícies del fòrum de Lluna Plena no Fansub.

## 2016-07-06 - Versió 2.0.1
- **General:**
	- S'incorporen notícies de RuffyNatsu no Fansub.
- **Notícies:**
	- Logo i icona de Minina no Fansub.
	- Corregit el CSS erroni de la selecció dels filtres.
- **Serveis:**
	- L'obtenció del tipus «un sol cop» es fa correctament a la primera execució.

## 2016-07-05 - Versió 2.0.0
- **Notícies:**
	- Nou sistema d'obtenció i visualització completament independent dels RSS i de MoonMoon.
	- Pàgines refetes de zero.
	- Filtre de notícies.
	- Pàgina d'estat.
	- Pàgina de contacte.
- **API:**
	- Versió inicial. Permet forçar una actualització de les notícies a petició.
- **Serveis:**
	- Versió inicial. Se separa part web i part d'obtenció de notícies.

## 2016-01-10 - Versió 1.0.6
- **Notícies:**
	- S'incorporen notícies de Dengeki Daisy Cat i Yoshiwara no Fansub.
	- Si una notícia no té títol o enllaç, no s'importa.
	- Corregida la lògica d'obtenció del blog de Lluna Plena no Fansub.
	- Es canvia de pàgina correctament al blog de XOP Fansub.

## 2015-09-12 - Versió 1.0.5
- **Notícies:**
	- No s'importen les fitxes de Lluna Plena (notícies sense l'etiqueta «Notícies»).
	- L'interval d'actualització passa de 900 a 300 segons.

## 2015-08-21 - Versió 1.0.4
- **Notícies:**
	- Si falla l'obtenció de notícies d'algun dels blogs de Blogger, es reintenta 3 vegades deixant cada cop més espai entre peticions.
	- Si s'obtenen zero notícies, es tracta com a error.
	- Es passa l'HTML de les notícies per Tidy per a assegurar que no hi ha HTML mal format.

## 2015-08-17 - Versió 1.0.3
- **Notícies:**
	- Bàner d'afiliat perquè webs externs ens enllacin.
	- L'interval d'actualització dels blogs de Blogger passa d'1 a 2 segons.

## 2015-08-16 - Versió 1.0.2
- **Notícies:**
	- Ara es pot enllaçar a les notícies d'un fansub en concret.

## 2015-08-15 - Versió 1.0.1
- **Notícies:**
	- L'interval d'actualització dels blogs de Blogger passa de 0,75 a 1 segon.

## 2015-08-14 - Versió 1.0.0
- **Notícies:**
	- Versió inicial. Sistema basat en MoonMoon, un agregador d'RSS.

