
CREATE TABLE `uman_tipo_reconocimiento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Bajar velocidad');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Cambiar circuito de trabajo');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Bajar carga de equipo 10% del nominal');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Evaluar temperatura con UMAN');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Evaluación próximos 30 minutos');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Aislar equipo sin cortar chapa y evaluar cada 2 horas');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Validar alarma de presión');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Evaluar condición próximos 5 minutos');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Evaluar condición próximos 30 minutos');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Detener equipo y regularizar presión');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Realizar chequeo de neumático y llanta en terreno');
INSERT INTO `uman_tipo_reconocimiento` (`descripcion`) VALUES ('Reemplazar sensor');
