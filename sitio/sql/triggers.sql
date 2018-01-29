/* Cuando se inserta o actualiza la tabla ultimos_eventos se actualiza la tabla alarmas*/

CREATE TRIGGER autoRecon_UE_INSERT BEFORE INSERT ON uman_ultimoevento
	FOR EACH ROW
		BEGIN
			UPDATE uman_alarmas ua set ua.ALARMAESTADO = 2, ua.ALARMAFECHARECONOCEUMANWEB = NOW(), ua.COMENTARIOS = 'Reconocimiento automático'  
        	WHERE ua.ALARMANUMCAMION = NEW.numequipo AND ua.ALARMAPOSICION = NEW.posicion AND ua.ALARMAESTADO = 0 AND NEW.eventotemperatura<ua.ALARMAVALOR AND ua.ALARMATIPO=32;

			UPDATE uman_alarmas ua set ua.ALARMAESTADO = 2, ua.ALARMAFECHARECONOCEUMANWEB = NOW(), ua.COMENTARIOS = 'Reconocimiento automático'  
        	WHERE ua.ALARMANUMCAMION = NEW.numequipo AND ua.ALARMAPOSICION = NEW.posicion AND ua.ALARMAESTADO = 0 AND NEW.eventopresion>NEW.resmin AND NEW.eventopresion<NEW.presmax AND ua.ALARMATIPO=64 OR ua.ALARMATIPO=128;
        END
        
CREATE TRIGGER autoRecon_UE_UPDATE BEFORE UPDATE ON uman_ultimoevento
	FOR EACH ROW
		BEGIN
			UPDATE uman_alarmas ua set ua.ALARMAESTADO = 2, ua.ALARMAFECHARECONOCEUMANWEB = NOW(), ua.COMENTARIOS = 'Reconocimiento automático'  
        	WHERE ua.ALARMANUMCAMION = NEW.numequipo AND ua.ALARMAPOSICION = NEW.posicion AND ua.ALARMAESTADO = 0 AND NEW.eventotemperatura<ua.ALARMAVALOR AND ua.ALARMATIPO=32;

			UPDATE uman_alarmas ua set ua.ALARMAESTADO = 2, ua.ALARMAFECHARECONOCEUMANWEB = NOW(), ua.COMENTARIOS = 'Reconocimiento automático'  
        	WHERE ua.ALARMANUMCAMION = NEW.numequipo AND ua.ALARMAPOSICION = NEW.posicion AND ua.ALARMAESTADO = 0 AND NEW.eventopresion>NEW.resmin AND NEW.eventopresion<NEW.presmax AND ua.ALARMATIPO=64 OR ua.ALARMATIPO=128;
		END 