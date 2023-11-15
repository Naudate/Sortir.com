DELIMITER //
CREATE PROCEDURE update_sorties_ouvert_to_cloture()
BEGIN
  -- Mettre à jour les sorties dont la dateDébut est inférieure à la date d'aujourd'hui et l'état est 'Ouvert'
UPDATE sortie
SET etat = 'Clôturé'
WHERE date_heure_debut <= NOW() AND etat = 'Ouvert';
END //
DELIMITER ;

-- Créer l'événement planifié qui appelle la procédure toutes les 5 minutes
CREATE EVENT IF NOT EXISTS schedule_verifier_sorties
ON SCHEDULE EVERY 1 MINUTE
DO
  CALL update_sorties_ouvert_to_cloture();