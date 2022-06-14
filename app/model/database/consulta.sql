/* Consulta com varias tabelas */

SELECT nome_usuario, nome_ciclo, caminho_imagens /* Campos Selecionados */
FROM crech964_site.usuarios /* tabela principal */
INNER JOIN crech964_site.ciclo ON id_ciclo = ciclo_usuario /* tabela relacionada */
INNER JOIN crech964_site.imagens ON imagem_usuario = referencia_imagens; /* tabela relacionada */
WHERE id_usuario = 1; /* selecionando por id */



/*  */
SELECT projeto.name_project, patrocinador.name_sponsor
FROM crech964_sia.project_has_sponsor AS patrocinio
INNER JOIN crech964_sia.project AS projeto ON projeto.id_project = patrocinio.cod_project
INNER JOIN crech964_sia.sponsor AS patrocinador ON patrocinador.id_sponsor = patrocinio.cod_sponsor

/* RELACIONAMENTO N:N COM VARIAS TABELAS */
SELECT projeto.name_project AS nome_projeto, educador.name_workshop AS nome_educador, patrocinador.name_sponsor AS nome_patrocinador
FROM crech964_sia.project AS projeto

INNER JOIN crech964_sia.project_has_workshop AS workshop ON projeto.id_project = workshop.cod_project
INNER JOIN crech964_sia.workshop AS educador ON educador.id_workshop = workshop.cod_workshop

INNER JOIN crech964_sia.project_has_sponsor AS patrocinio ON projeto.id_project = patrocinio.cod_project
INNER JOIN crech964_sia.sponsor AS patrocinador ON patrocinador.id_sponsor = patrocinio.cod_sponsor;

/* RELACIONAMENTO N:N PRESTADOR TEM OCUPAÇÃO */
SELECT prestador.name_providers AS Nome, ocupacao.name_occupation AS Ocupação
FROM crech964_sia.providers AS prestador
INNER JOIN crech964_sia.providers_has_occupation AS phc ON prestador.id_providers = phc.cod_providers
INNER JOIN crech964_sia.occupation AS ocupacao ON ocupacao.id_occupation = phc.cod_occupation


news.id_news, news.title_news, image.id_images, image.reference_images, image.path_images, image.author_images