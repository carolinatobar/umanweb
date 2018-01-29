CREATE TABLE `uman_sitio`.`uman_perfil` (
  `id` INT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `tiempo_sesion` INT NULL DEFAULT 3600 COMMENT 'Tiempo de expirado de sesión en segundos. Por defecto 3600 segundos.',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci
COMMENT = 'Perfiles';

INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Administrador');
INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Consultor Cliente');
INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Consultor Operación');
INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Operador');
INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Técnico');
INSERT INTO `uman_sitio`.`uman_perfil` (`nombre`) VALUES ('Soporte SIM');