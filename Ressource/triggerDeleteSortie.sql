DELIMITER //
CREATE  TRIGGER after_delete_sortie
    AFTER delete
    ON sortie FOR EACH ROW
BEGIN
    DECLARE organisateurSortie VARCHAR(255);
	DECLARE updateBy VARCHAR(255);

    DECLARE siteSortie VARCHAR(255);
     DECLARE lieuSortie_nom VARCHAR(255);
    DECLARE lieuSortie_rue VARCHAR(255);
    DECLARE lieuSortie_ville_nom VARCHAR(255);
	DECLARE lieuSortie_ville_id int;
    DECLARE lieuSortie_ville_code_postal VARCHAR(10);
    DECLARE lieuSortie_longitude VARCHAR(255);
    DECLARE lieuSortie_latitude VARCHAR(255);
  #  declare lieuSortie lieu;
  #declare villeSortie ville;


    select nom into siteSortie from site s  where s.id = old.site_id;

    select l.nom, l.rue, l.latitude, l.longitude, l.ville_id into
        lieuSortie_nom, lieuSortie_rue, lieuSortie_latitude, lieuSortie_longitude, lieuSortie_ville_id from lieu l
    where l.id = old.lieu_id;

    select v.nom, v.code_postal into
        lieuSortie_ville_nom, lieuSortie_ville_code_postal from ville v
    where v.id = lieuSortie_ville_id;

    -- Récupérer le nom et le prénom de l'organisateur
    SELECT CONCAT(u.nom, ' ', u.prenom)
    INTO organisateurSortie
    FROM User u
    WHERE u.id = old.organisateur_id;

    -- Récupérer le nom et le prénom de l'organisateur
    SELECT CONCAT(u.nom, ' ', u.prenom)
    INTO updateBy
    FROM User u
    WHERE u.id = old.updated_by_id;

    -- Insérer dans la table histsortie avec le nom et prénom de l'organisateur
    INSERT INTO history_sortie (nom, date_heure_debut, date_heure_fin, date_limite_inscription
                               , nombre_max_participant, description, is_publish, motif
                               , lieu_nom, lieu_rue,lieu_ville_nom, lieu_ville_code_postal, lieu_longitude, lieu_latitude
                               , organisateur, etat, site, action, update_by, sortie_id)
    VALUES (
               old.nom,
               old.date_heure_debut,
               old.date_heure_fin,
               old.date_limite_inscription,
               old.nombre_max_participant,
               old.description,
               old.is_publish,
               old.motif,
               lieuSortie_nom,
               lieuSortie_rue,
               lieuSortie_ville_nom,
               lieuSortie_ville_code_postal,
               lieuSortie_longitude,
               lieuSortie_latitude,
               organisateurSortie,
               old.etat,
               siteSortie,
               'DELETE',
               updateBy,
               old.id
           );
END;
//

DELIMITER ;
