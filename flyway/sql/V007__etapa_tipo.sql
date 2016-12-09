ALTER TABLE etapa DROP enviar_email_administrador, DROP enviar_email_banca, DROP enviar_email_orientador;

ALTER TABLE `sistema_tcc`.`etapa_tipo`
COLLATE = utf8_general_ci ,
CHANGE COLUMN `nome` `nome` VARCHAR(50) NOT NULL ,
ADD COLUMN `avaliado_banca` TINYINT(1) NOT NULL AFTER `nome`,
ADD COLUMN `avaliado_coordenador` TINYINT(1) NOT NULL AFTER `avaliado_banca`,
ADD COLUMN `avaliado_orientador` TINYINT(1) NOT NULL AFTER `avaliado_coordenador`,
ADD COLUMN `entrega_arquivo` TINYINT(1) NOT NULL AFTER `avaliado_orientador`,
COMMENT = 'Tipos de etapas.';

INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Proposta',1,0,0,1);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Seminário de Andamento',0,1,0,0);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Etapa',0,0,1,1);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Relatório de Orientação',0,1,0,1);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Entrega Final',0,1,0,1);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Mostra Científica',0,1,0,0);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Artigo',1,0,0,1);
INSERT INTO `etapa_tipo` (`id`,`nome`,`avaliado_banca`,`avaliado_coordenador`,`avaliado_orientador`,`entrega_arquivo`) VALUES (NULL,'Monografia',1,0,0,1);
