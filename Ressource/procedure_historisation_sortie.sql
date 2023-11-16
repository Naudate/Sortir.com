#drop procedure historisation_sortie;

DELIMITER //

create procedure historisation_sortie()
BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE siteSortie_id int;
    DECLARE siteSortie_nom VARCHAR(255);
    DECLARE participants JSON;

    DECLARE organisateurId int;
    DECLARE organisateur_nom VARCHAR(255);
    DECLARE organisateur_prenom VARCHAR(255);
    DECLARE organisateur_pseudo VARCHAR(255);
    DECLARE organisateur_email VARCHAR(255);
    DECLARE organisateur_telephone VARCHAR(10);
    DECLARE organisateur_site_id int;
    DECLARE organisateur_site_nom VARCHAR(255);
    DECLARE organisateur_photo VARCHAR(255);
    DECLARE organisateur_roles json;
    DECLARE organisateur_is_actif tinyint(1);
    DECLARE organisateur_first_connection tinyint(1);
    DECLARE organisateur_is_change_password tinyint(1);


    DECLARE updatedById int;
    DECLARE updatedBy_nom VARCHAR(255);
    DECLARE updatedBy_prenom VARCHAR(255);
    DECLARE updatedBy_pseudo VARCHAR(255);
    DECLARE updatedBy_email VARCHAR(255);
    DECLARE updatedBy_telephone VARCHAR(10);
    DECLARE updatedBy_site_id int;
    DECLARE updatedBy_site_nom VARCHAR(255);
    DECLARE updatedBy_photo VARCHAR(255);
    DECLARE updatedBy_roles json;
    DECLARE updatedBy_is_actif tinyint(1);
    DECLARE updatedBy_first_connection tinyint(1);
    DECLARE updatedBy_is_change_password tinyint(1);



    DECLARE sortie_id int;
    declare sortie_nom varchar(255);
    declare sortie_lieu_id int;
    declare sortie_organisateur_id int;
    declare sortie_site_id int;
    declare sortie_date_heure_debut datetime;
    declare sortie_date_heure_fin datetime;
    declare sortie_date_limite_inscription datetime;
    declare sortie_nombre_max_participant int;
    declare sortie_description varchar(255);
    declare sortie_is_publish TINYINT(1);
    declare sortie_motif varchar(255);
    declare sortie_etat varchar(100);
    declare sortie_updated_by_id int ;
    declare sortie_date_update datetime;



    DECLARE lieuSortie_nom VARCHAR(255);
    DECLARE lieuSortie_rue VARCHAR(255);
    DECLARE lieuSortie_ville_nom VARCHAR(255);
    DECLARE lieuSortie_ville_code_postal VARCHAR(5);
    DECLARE lieuSortie_longitude varchar(255);
    DECLARE lieuSortie_latitude varchar(255);

    DECLARE cursor_sorties CURSOR FOR
        SELECT * FROM sortie
        WHERE date_heure_fin + INTERVAL 1 MONTH <= CURRENT_DATE AND etat = 'Terminée' OR
                        date_update + INTERVAL 1 MONTH <= CURRENT_DATE AND etat = 'Annulée';

    -- Déclaration d'un handler pour le curseur
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Ouvrir le curseur
    OPEN cursor_sorties;

    -- Initialiser la variable pour détecter la fin du curseur
    SET done = FALSE;

    -- Boucle pour traiter chaque sortie
    read_loop: LOOP
        -- Lire les informations de la sortie
        FETCH cursor_sorties INTO sortie_id, sortie_lieu_id, sortie_organisateur_id, sortie_site_id, sortie_nom
            , sortie_date_heure_debut, sortie_date_heure_fin, sortie_date_limite_inscription,
            sortie_nombre_max_participant
            , sortie_description, sortie_is_publish, sortie_motif, sortie_etat
            , sortie_updated_by_id, sortie_date_update;

        -- Vérifier si le curseur a atteint la fin
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Récupérer le nom du site
        SELECT id, nom INTO siteSortie_id, siteSortie_nom FROM site s WHERE s.id = sortie_site_id;

        -- Récupérer les informations sur le lieu
        SELECT l.nom, l.rue, v.nom AS ville_nom, v.code_postal, l.longitude, l.latitude INTO
            lieuSortie_nom, lieuSortie_rue, lieuSortie_ville_nom, lieuSortie_ville_code_postal, lieuSortie_longitude, lieuSortie_latitude
        FROM lieu l
                 JOIN ville v ON l.ville_id = v.id
        WHERE l.id = sortie_lieu_id;

        -- Récupérer les informations de l'organisateur
        SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.telephone, s.id, s.nom, u.photo, u.roles, u.is_actif, u.first_connection, u.is_change_password
        INTO organisateurId, organisateur_nom, organisateur_prenom, organisateur_pseudo, organisateur_email
            , organisateur_telephone, organisateur_site_id, organisateur_site_nom, organisateur_photo, organisateur_roles,  organisateur_is_actif, organisateur_first_connection,
            organisateur_is_change_password
        FROM User u
                 JOIN site s ON u.site_id = s.id
        WHERE u.id = sortie_organisateur_id;

        IF sortie_updated_by_id IS not NULL THEN
            -- Récupérer les informations de l'utilisateur qui a mis à jour la sortie
            SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.telephone, s.id, s.nom, u.photo, u.roles, u.is_actif, u.first_connection, u.is_change_password
            INTO updatedById, updatedBy_nom, updatedBy_prenom, updatedBy_pseudo, updatedBy_email
                , updatedBy_telephone, updatedBy_site_id, updatedBy_site_nom, updatedBy_photo, updatedBy_roles,  updatedBy_is_actif, updatedBy_first_connection,
                updatedBy_is_change_password
            FROM User u
                     JOIN site s ON u.site_id = s.id
            WHERE u.id = sortie_updated_by_id;
        END IF;




        SELECT JSON_ARRAYAGG(JSON_OBJECT('id', u.id, 'nom', u.nom, 'prenom', u.prenom, 'pseudo', u.pseudo, 'email', u.email,
                                         'telephone', u.telephone, 'site',JSON_OBJECT('id', s.id, 'nom', s.nom), 'photo',u.photo,
                                         'roles',u.roles, 'is_actif', u.is_actif, 'first_connection', u.first_connection,
                                         'is_change_password', u.is_change_password, 'last_reset_password', u.last_reset_password))
        INTO participants
        FROM sortie_user su
                 inner JOIN User u ON su.user_id = u.id
                 inner JOIN site s ON u.site_id = s.id
        WHERE su.sortie_id = sortie_id;

        IF NOT EXISTS (SELECT 1 FROM history_sortie WHERE sortie_id = sortie_id) THEN
            -- Insérer dans la table histsortie avec les informations récupérées
            INSERT INTO history_sortie (sortie_id, nom, date_heure_debut, date_heure_fin, date_limite_inscription
                                       , nombre_max_participant, description, is_publish, motif, etat
                                       , lieu_nom, lieu_rue, lieu_ville_nom, lieu_ville_code_postal, lieu_longitude, lieu_latitude
                                       , action, site_id, site_nom, organisateur_id, organisateur_nom, organisateur_prenom,
                organisateur_pseudo, organisateur_mail, organisateur_roles, organisateur_telephone
                                       , organisateur_photo, organisateur_is_actif, organisateur_first_connection, organisateur_is_change_password
                                       , organisateur_site_id, organisateur_site_nom, updated_by_id, updated_by_nom, updated_by_prenom
                                       , updated_by_pseudo, updated_by_email, updated_by_photo, updated_by_telephone, updated_by_roles
                                       , updated_by_first_connection, updated_by_is_change_password, updated_by_is_actif, updated_by_site_id
                                       , updated_by_site_nom, date_modification, participants)
            VALUES (
                       sortie_id,
                       sortie_nom,
                       sortie_date_heure_debut,
                       sortie_date_heure_fin,
                       sortie_date_limite_inscription,
                       sortie_nombre_max_participant,
                       sortie_description,
                       sortie_is_publish,
                       sortie_motif,
                       sortie_etat,
                       lieuSortie_nom,
                       lieuSortie_rue,
                       lieuSortie_ville_nom,
                       lieuSortie_ville_code_postal,
                       lieuSortie_longitude,
                       lieuSortie_latitude,
                       'HISTORISATION',
                       siteSortie_id,
                       siteSortie_nom,
                       organisateurId,
                       organisateur_nom,
                       organisateur_prenom,
                       organisateur_pseudo,
                       organisateur_email,
                       organisateur_roles,
                       organisateur_telephone,
                       organisateur_photo,
                       organisateur_is_actif,
                       organisateur_first_connection,
                       organisateur_is_change_password,
                       organisateur_site_id,
                       organisateur_site_nom,
                       updatedById,
                       updatedBy_nom,
                       updatedBy_prenom,
                       updatedBy_pseudo,
                       updatedBy_email,
                       updatedBy_photo,
                       updatedBy_telephone,
                       updatedBy_roles,
                       updatedBy_first_connection,
                       updatedBy_is_change_password,
                       updatedBy_is_actif,
                       updatedBy_site_id,
                       updatedBy_site_nom,
                       sortie_date_update,
                       participants
                   );
        END IF;
    END LOOP;

    -- Fermer le curseur
    CLOSE cursor_sorties;
END;


//

DELIMITER ;


CREATE EVENT IF NOT EXISTS schedule_historisation_sorties
    ON SCHEDULE EVERY 1 MINUTE
    DO
    CALL historisation_sortie()