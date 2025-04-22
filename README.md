# Fansubs.cat

[![English version](https://img.shields.io/badge/English%20version%20available%20here-blue.svg)](https://github.com/fansubscat/Fansubs.cat/blob/master/README.en.md)

Lloc web i serveis del servidor de Fansubs.cat, un portal que agrega tot el contingut produït pels fansubs en català.

## Webs i serveis

Aquest projecte inclou el codi font dels següents webs:
- Principal: La pàgina de portada principal.
- Administració: Un tauler d’administració que permet gestionar tot el contingut dinàmic del portal.
- Advent: Un web que mostra calendaris d’advent configurats pels fansubs.
- API: Una API que proveeix dades a serveis tant interns com externs, incloent-hi l’extensió del Tachiyomi/Mihon.
- Catàleg: Un web que mostra contingut d’anime, manga i imatge real generat pels fansubs.
- Comunitat: Un fòrum phpBB incloent-hi extensions per a integrar-lo al web de Fansubs.cat.
- Notícies: Un web que agrega les notícies de diferents URLs en una sola pàgina.
- Estàtic: Estructura de directoris per a l’emmagatzematge de contingut estàtic.
- Usuaris: Un web que permet gestionar el perfil de l’usuari.

I també inclou els següents fragments de codi addicionals:
- Aplicació d’Android: Mostra notícies i rep notificacions push quan hi ha disponible nou contingut. Actualment sense manteniment.
- Serveis: Serveis interns que fan que el web funcioni com cal, normalment mitjançant tasques programades via «cron».

## Per què existeix aquest web?

Inicialment, el web només era un agregador de notícies. Hi havia diversos fansubs en català i vam pensar que estaria bé tenir-los centralitzats en un sol lloc. Amb aquell web, podies donar-hi una ullada ràpida i veure si hi havia notícies en algun dels fansubs. Després d’un temps, vam afegir-hi un lloc on llegir tot el manga publicat pels fansubs en català. I després, un altre lloc on mirar l’anime llançat pels fansubs en català. El codi ha anat evolucionant constantment amb noves funcionalitats.

Arreu del portal s’esmenten els creadors del contingut i s’hi enllaça per tal de no prendre visitants a les pàgines originals dels fansubs. Evidentment, el web no té cap mena d‘ànim de lucre i sempre continuarà sent així.

## Com funciona?

El web s’executa en un servidor simple amb Debian 12 (Bookworm), un servidor web Apache 2.4, PHP 8.2 i MariaDB 10.11.

Com hem comentat abans, hi ha molts serveis i webs inclosos. Descriure’ls tots en detall implicaria força temps, així que si vols saber-ne més, dóna un cop d’ull al codi o crea una incidència perquè et resolguem els dubtes! :)

## Contribucions

Tots les contribucions raonables són benvingudes. Quan enviïs una «pull request», descriu el problema i els canvis que has aplicat de manera clara i mantén l’estil del codi existent.

## Llicència

Aquest projecte està llicenciat amb la [GNU Affero Public License 3.0](https://github.com/fansubscat/Fansubs.cat/blob/master/LICENSE). Bàsicament, això vol dir que pots fer-ne el que vulguis, però si fas servir el codi en un lloc web, **cal** que publiquis el codi modificat.

Hi ha alguns fitxers i imatges al codi font que són propietat dels seus autors originals i no estan subjectes a aquesta llicència. Si vols que els eliminem, contacta amb nosaltres.
