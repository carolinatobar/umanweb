CREATE TABLE `uman_sitio`.`uman_perfil_usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_perfil` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci
COMMENT = 'Asignación de perfiles a usuarios';
