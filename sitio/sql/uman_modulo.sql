CREATE TABLE `uman_sitio`.`uman_modulo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_menu` INT NOT NULL DEFAULT 1,
  `etiqueta` VARCHAR(45) NOT NULL COMMENT 'Nombre del módulo tal y como debe aparecer en el menú de opciones',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre normalizado usado para control de acceso, se genera deacuerdo a la etiqueta',
  `orden` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci
COMMENT = 'Listado de módulos que requieren restricción de acceso según perfil de usuario';

INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Monitoreo de Equipos', 'MONITOREO_DE_EQUIPOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Monitoreo GPS 2D', 'MONITOREO_GPS_2D');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Monitoreo GPS 3D', 'MONITOREO_GPS_3D');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Monitoreo Alarmas', 'MONITOREO_ALARMAS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Gráfico de Presión y Temperatura', 'GRAFICO_DE_PRESION_Y_TEMPERATURA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Gráfico de Velocidad y Recorrido', 'GRAFICO_DE_VELOCIDAD_Y_RECORRIDO');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Gráfico de Alarmas y Recorrido', 'GRAFICO_DE_ALARMAS_Y_RECORRIDO');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Gráfico de Cobertura', 'GRAFICO_DE_COBERTURA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Histograma', 'HISTOGRAMA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Reporte de Alarmas por Equipo', 'REPORTE_DE_ALARMAS_POR_EQUIPO');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Reporte de Fallas', 'REPORTE_DE_FALLAS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Reporte de Emisiones Diarias', 'REPORTE_DE_EMISIONES_DIARIAS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Reporte de Eventos Mensuales', 'REPORTE_DE_EVENTOS_MENSUALES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Reporte de Datos UMANWeb', 'REPORTE_DE_DATOS_UMANWEB');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tabla de Cobertura', 'TABLA_DE_COBERTURA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tabla de Sensores', 'TABLA_DE_SENSORES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tabla de Neumáticos', 'TABLA_DE_NEUMATICOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tabla de Umbrales', 'TABLA_DE_UMBRALES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tabla de Emisión UMAN Blue', 'TABLA_DE_EMISION_UMAN_BLUE');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Detalle de Equipos', 'DETALLE_DE_EQUIPOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Calculadora', 'CALCULADORA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Tarjeta de Vida', 'TARJETA_DE_VIDA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Ingreso de Flotas', 'INGRESO_DE_FLOTAS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Ingreso de Equipos', 'INGRESO_DE_EQUIPOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Ingreso de SIM', 'INGRESO_DE_SIM');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Ingreso de Neumáticos', 'INGRESO_DE_NEUMATICOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Ingreso de Sensores', 'INGRESO_DE_SENSORES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Asignar Neumáticos', 'ASIGNAR_NEUMATICOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Asignar Sensores', 'ASIGNAR_SENSORES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Configurar Posiciones', 'CONFIGURAR_POSICIONES');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Configurar Parámetros', 'CONFIGURAR_PARAMETROS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Configurar Plantillas', 'CONFIGURAR_PLANTILLAS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Configurar Caja UMAN Blue', 'CONFIGURAR_CAJA_UMAN_BLUE');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Actualizar Firmware UMAN Blue', 'ACTUALIZAR_FIRMWARE_UMAN_BLUE');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Administración de Usuarios', 'ADMINISTRACION_DE_USUARIOS');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Librería', 'LIBRERIA');
INSERT INTO `uman_sitio`.`uman_modulo` (`etiqueta`, `nombre`) VALUES ('Mi Cuenta', 'MI_CUENTA');
