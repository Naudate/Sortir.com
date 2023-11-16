

DELIMITER //
CREATE  TRIGGER before_delete_user
    before delete
    ON user FOR EACH ROW
BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE siteSortie_nom VARCHAR(255);
    DECLARE participants JSON;

    DECLARE organisateur json;



    DECLARE sortie json;


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

    -- récupération de l'utilsateur
    declare utilisateur json;


-- récuperation des sortie auquel l'utilisateur est concerné
    DECLARE cursor_sorties CURSOR FOR
        SELECT s.* FROM sortie s
                            left join sortie_user su on s.id = su.sortie_id
        WHERE organisateur_id = old.id OR
                su.user_id = old.id;

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
        SELECT  nom INTO siteSortie_nom FROM site s WHERE s.id = sortie_site_id;

        -- Récupérer les informations sur le lieu
        SELECT l.nom, l.rue, v.nom AS ville_nom, v.code_postal INTO
            lieuSortie_nom, lieuSortie_rue, lieuSortie_ville_nom, lieuSortie_ville_code_postal
        FROM lieu l
                 JOIN ville v ON l.ville_id = v.id
        WHERE l.id = sortie_lieu_id;

        SELECT JSON_ARRAYAGG(JSON_OBJECT('nom', u.nom, 'prenom', u.prenom, 'email', u.email,
                                         'telephone', u.telephone, 'site',JSON_OBJECT('nom', s.nom)))
        INTO utilisateur
        FROM User u
                 JOIN site s ON u.site_id = s.id
        WHERE u.id = old.id;

        -- si l'utilisateur supprimé etait l'organisateur de la sortie
        IF sortie_organisateur_id = old.id and sortie_etat = 'En cours' or  sortie_etat = 'Terminée' or sortie_etat = 'Annulée' or  sortie_etat = 'Clôturée'  then
            -- changement etat de la sorie en annulé
            update sortie set etat = 'Annulée', motif = 'organisateur supprimé (historisé)' where id = sortie_id;

            -- historisation de la sortie
            -- appeler une procedure avec id de la sortie en parametre
            call historisation_sortie_by_id(sortie_id);

            select JSON_ARRAYAGG(JSON_OBJECT('nom', sortie_nom, 'date_heure_debut', sortie_date_heure_debut,
                                             'date_heure_fin', sortie_date_heure_fin, 'nombre_max_participant' , sortie_nombre_max_participant ,
                                             'description', sortie_description, 'motif', 'organisateur supprimé (historisé)','site',JSON_OBJECT('nom', siteSortie_nom),
                                             'lieu',JSON_OBJECT('nom', lieuSortie_nom, 'rue', lieuSortie_rue,
                                                                'ville',JSON_OBJECT('nom', lieuSortie_ville_nom,
                                                                                    'code_postal', lieuSortie_ville_code_postal ))))
            INTO sortie;

            SELECT JSON_ARRAYAGG(JSON_OBJECT('nom',u.nom, 'prenom', u.prenom, 'email', u.email,
                                             'telephone', u.telephone, 'site',JSON_OBJECT('nom', s.nom)))
            INTO participants
            FROM sortie_user su
                     inner JOIN User u ON su.user_id = u.id
                     inner JOIN site s ON u.site_id = s.id
            WHERE su.sortie_id = sortie_id and su.user_id = old.id;

            -- historisation de l'utilisateur

            insert into history_user (user, sortie, participants) values (utilisateur, sortie ,participants);

        elseif sortie_organisateur_id != old.id and sortie_etat = 'En cours' or sortie_etat = 'Ouvert' then
            -- suppresssion de l'utilisateur dans la liste des participants
            delete from sortie_user where sortie_id = sortie_id and user_id = old.id;


            select JSON_ARRAYAGG(JSON_OBJECT('nom', sortie_nom, 'date_heure_debut', sortie_date_heure_debut,
                                             'date_heure_fin', sortie_date_heure_fin, 'nombre_max_participant' , sortie_nombre_max_participant ,
                                             'description', sortie_description, 'motif', sortie_motif,'site',JSON_OBJECT('nom', siteSortie_nom),
                                             'lieu',JSON_OBJECT('nom', lieuSortie_nom, 'rue', lieuSortie_rue,
                                                                'ville',JSON_OBJECT('nom', lieuSortie_ville_nom,
                                                                                    'code_postal', lieuSortie_ville_code_postal ))))
            INTO sortie;

            SELECT JSON_ARRAYAGG(JSON_OBJECT('nom',u.nom, 'prenom', u.prenom, 'email', u.email,
                                             'telephone', u.telephone, 'site',JSON_OBJECT('nom', s.nom)))
            INTO participants
            FROM sortie_user su
                     inner JOIN User u ON su.user_id = u.id
                     inner JOIN site s ON u.site_id = s.id
            WHERE su.sortie_id = sortie_id and su.user_id = old.id;

            -- historisation de l'utilisateur

            insert into history_user (user, sortie, participants) values (utilisateur, sortie ,participants);


        elseif sortie_organisateur_id = old.id and sortie_etat = 'Ouvert' or sortie_etat = 'En création' then

            -- suppression de le sortie
            delete from sortie_user where sortie_id = sortie_id;

            delete from sortie where id = sortie_id;


        END IF;

    END LOOP;

    -- Fermer le curseur
    CLOSE cursor_sorties;
END;
//

DELIMITER ;
