INSERT INTO series
SELECT id_serie+70 AS id,
'' AS slug, 
CONCAT('[LLPNF] ',IFNULL(titol_pagina,IF(copyright=1,titol_altres,titol))) AS name, 
IF(copyright=0,titol_altres,NULL) AS alternate_names,
'' AS type,
NULL AS air_date,
NULL AS author,
NULL AS director,
NULL AS studio,
NULL AS rating,
0 AS episodes,
sinopsi AS synopsis,
NULL AS duration,
'' AS image,
SUBSTRING_INDEX(REPLACE(REPLACE(info_url,'http://myanimelist.net/anime/',''),'https://myanimelist.net/anime/',''),'/',1) AS myanimelist_id,
CURRENT_TIMESTAMP AS created,
'LlPnFImport' AS created_by,
CURRENT_TIMESTAMP AS updated,
'LlPnFImport' AS updated_by
FROM llpnf_series
WHERE id_serie NOT IN (163,164,127, 50,71,53,159,74) AND estat<>3;

INSERT INTO version
SELECT id_serie+100 AS id,
id_serie+70 AS series_id,
IF(estat=0,2,1) AS status, 
IF(resolucio=0,'480p',IF(resolucio=1,'576p',IF(resolucio=2,'720p',IF(resolucio=3,'1080p',NULL)))) AS default_resolution,
CURRENT_TIMESTAMP AS created,
'LlPnFImport' AS created_by,
CURRENT_TIMESTAMP AS updated,
'LlPnFImport' AS updated_by
FROM llpnf_series
WHERE id_serie NOT IN (163,164,127, 50,71,53,159,74) AND estat<>3;

INSERT INTO rel_version_fansub 
SELECT id_serie+100 AS version_id,
2 AS fansub_id
FROM llpnf_series
WHERE id_serie NOT IN (163,164,127, 50,71,53,159,74) AND estat<>3;

-- Manualment:
--  Bleach: The DiamondDust Rebellion -> SnF collab
