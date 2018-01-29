CREATE TABLE `uman_sitio`.`uman_acceso_perfil` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_perfil` INT NOT NULL,
  `id_modulo` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci
COMMENT = 'Asignación de acceso a diferentes módulos de la plataforma';