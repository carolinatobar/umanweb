CREATE TABLE `uman_sitio`.`uman_menu` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `etiqueta` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `uman_sitio`.`uman_menu` (`etiqueta`) VALUES ('ROOT');
INSERT INTO `uman_sitio`.`uman_menu` (`etiqueta`) VALUES ('Monitoreo');
INSERT INTO `uman_sitio`.`uman_menu` (`etiqueta`) VALUES ('Consultas');
INSERT INTO `uman_sitio`.`uman_menu` (`etiqueta`) VALUES ('Configuraciones');
