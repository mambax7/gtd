<?php
//$Id: main.php,v 1.35 2005/09/01 14:55:24 eric_juden Exp $

define('_GTD_CATEGORY1', 'Assigner un prof&eacute;sseur');
define('_GTD_CATEGORY2', 'Supprimer les r&eacute;ponses');
define('_GTD_CATEGORY3', 'Supprimer les inscriptions');
define('_GTD_CATEGORY4', 'Journalisation des inscriptions des utilisateurs');
define('_GTD_CATEGORY5', 'Modifier les r&eacute;ponses');
define('_GTD_CATEGORY6', 'Modifier les informations de l\'inscription');

define('_GTD_SEC_TICKET_ADD', 0);
define('_GTD_SEC_TICKET_EDIT', 1);
define('_GTD_SEC_TICKET_DELETE', 2);
define('_GTD_SEC_TICKET_OWNERSHIP', 3);
define('_GTD_SEC_TICKET_STATUS', 4);
define('_GTD_SEC_TICKET_GENRE', 5);
define('_GTD_SEC_TICKET_LOGUSER', 6);
define('_GTD_SEC_RESPONSE_ADD', 7);
define('_GTD_SEC_RESPONSE_EDIT', 8);
define('_GTD_SEC_TICKET_MERGE', 9);
define('_GTD_SEC_FILE_DELETE', 10);

define('_GTD_SEC_TEXT_TICKET_ADD', 'Ajouter des inscriptions');
define('_GTD_SEC_TEXT_TICKET_EDIT', 'Modifier des inscriptions');
define('_GTD_SEC_TEXT_TICKET_DELETE', 'Effacer des inscriptions');
define('_GTD_SEC_TEXT_TICKET_OWNERSHIP', 'Changer le prof&eacute;sseur de l\'inscription');
define('_GTD_SEC_TEXT_TICKET_STATUS', 'Changer le Statut de l\'inscription');
define('_GTD_SEC_TEXT_TICKET_GENRE', 'Changer le Genre de l\'inscription');
define('_GTD_SEC_TEXT_TICKET_LOGUSER', 'Enregistrer l\'inscription pour l\'utilisateur');
define('_GTD_SEC_TEXT_RESPONSE_ADD', 'Ajouter une r&eacute;ponse');
define('_GTD_SEC_TEXT_RESPONSE_EDIT', 'Modifier une r&eacute;ponse');
define('_GTD_SEC_TEXT_TICKET_MERGE', 'Fusionner les inscriptions');
define('_GTD_SEC_TEXT_FILE_DELETE', 'Effacer les fichiers attach&eacute;s');

define('_GTD_JSC_TEXT_DELETE', 'Etes vous certain de vouloir supprimer cette inscription ?');

define('_GTD_MESSAGE_ADD_DEPT', 'Type ajout&eacute; avec succ&egrave;s');
define('_GTD_MESSAGE_ADD_DEPT_ERROR', 'Erreur : Type non ajout&eacute;');
define('_GTD_MESSAGE_UPDATE_DEPT', 'Type mis &agrave; jour');
define('_GTD_MESSAGE_UPDATE_DEPT_ERROR', 'Erreur : Type non mis &agrave; jour');
define('_GTD_MESSAGE_DEPT_DELETE', 'Type supprim&eacute;');
define('_GTD_MESSAGE_DEPT_DELETE_ERROR', 'Erreur : Type non supprim&eacute;');
define('_GTD_MESSAGE_ADDSTAFF_ERROR', 'Erreur : Membre du Type non ajout&eacute;');
define('_GTD_MESSAGE_ADDSTAFF', 'Membre du Type ajout&eacute;');
define('_GTD_MESSAGE_STAFF_DELETE', 'Membre du Type supprim&eacute;');
define('_GTD_MESSAGE_STAFF_DELETE_ERROR', 'Membre du Type non supprim&eacute;');
define('_GTD_MESSAGE_EDITSTAFF', 'Profil du Membre du Type mis &agrave; jour');
define('_GTD_MESSAGE_EDITSTAFF_ERROR', 'Erreur : Membre du Type non mis &agrave; jour');
define('_GTD_MESSAGE_EDITSTAFF_NOCLEAR_ERROR', 'Erreur : ancien Type non supprim&eacute;');
define('_GTD_MESSAGE_DEPT_EXISTS', 'Ce Type existe d&eacute;j&agrave;');
define('_GTD_MESSAGE_ADDTICKET', 'Inscription enregistr&eacute;e');
define('_GTD_MESSAGE_ADDTICKET_ERROR', 'Erreur : Inscription non enregistr&eacute;e');
define('_GTD_MESSAGE_LOGMESSAGE_ERROR', 'Erreur : Action non enregistr&eacute;e dans la base de donn&eacute;es');
define('_GTD_MESSAGE_UPDATE_GENRE', 'Genre de l\'Inscription mis &agrave; jour');
define('_GTD_MESSAGE_UPDATE_GENRE_ERROR', 'Erreur : Genre de l\'Inscription non mise &agrave; jour');
define('_GTD_MESSAGE_UPDATE_STATUS', 'Statut de l\'Inscription mis &agrave; jour');
define('_GTD_MESSAGE_UPDATE_STATUS_ERROR', 'Erreur : Statut de l\'Inscription non mis &agrave; jour');
define('_GTD_MESSAGE_UPDATE_DEPARTMENT', 'Type de l\'Inscription mis &agrave; jour avec succ&egrave;s');
define('_GTD_MESSAGE_UPDATE_DEPARTMENT_ERROR', 'Erreur : Le Type de l\'Inscription n\'a pas &eacute;t&eacute; mis &agrave; jour');
define('_GTD_MESSAGE_CLAIM_OWNER', 'S\'approprier l\'inscription');
define('_GTD_MESSAGE_CLAIM_OWNER_ERROR', 'Erreur : prof&eacute;sseur de l\'inscription non d&eacute;efini');
define('_GTD_MESSAGE_ASSIGN_OWNER', 'Vous avez associ&eacute; avec succ&egrave;s un prof&eacute;sseur &agrave; cette Inscription');
define('_GTD_MESSAGE_ASSIGN_OWNER_ERROR', 'Erreur : prof&eacute;sseur de l\'inscription non d&eacute;fini;');
define('_GTD_MESSAGE_UPDATE_OWNER', 'Mise &agrave; jour du prof&eacute;sseur de l\'inscription r&eacute;ussie.');
define('_GTD_MESSAGE_ADDFILE', 'Fichier envoy&eacute; avec succ&egrave;s');
define('_GTD_MESSAGE_ADDFILE_ERROR', 'Erreur : Fichier non envoy&eacute;');
define('_GTD_MESSAGE_ADDRESPONSE', 'R&eacute;ponse ajout&eacute;e');
define('_GTD_MESSAGE_ADDRESPONSE_ERROR', 'Erreur : R&eacute;ponse non ajout&eacute;e');
define('_GTD_MESSAGE_UPDATE_CALLS_CLOSED_ERROR', 'Erreur : Cloture de l\'inscription non mis &agrave; jour');
define('_GTD_MESSAGE_ALREADY_OWNER', '%s est d&eacute;j&agrave; prof&eacute;sseur de cette Inscription');
define('_GTD_MESSAGE_ALREADY_STATUS', 'L\'nscription est d&eacute;j&agrave; d&eacute;fini avec ce Statut');
define('_GTD_MESSAGE_DELETE_TICKET', 'Inscription supprim&eacute;e avec succ&egrave;s');
define('_GTD_MESSAGE_DELETE_TICKET_ERROR', 'Erreur : Inscription non supprim&eacute;e');
define('_GTD_MESSAGE_ADD_SIGNATURE', 'Signature ajout&eacute;e avec succ&egrave;s');
define('_GTD_MESSAGE_ADD_SIGNATURE_ERROR', 'Erreur : Signature non mise &agrave; jour');
define('_GTD_MESSAGE_RESPONSE_TPL', 'R&eacute;ponse pr&eacute;d&eacute;finie mise &agrave; jour avec succ&egrave;s');
define('_GTD_MESSAGE_RESPONSE_TPL_ERROR', 'Erreur : R&eacute;ponse non mise &agrave; jour');
define('_GTD_MESSAGE_DELETE_RESPONSE_TPL', 'R&eacute;ponse pr&eacute;d&eacute;finie supprim&eacute;e avec succ&egrave;s');
define('_GTD_MESSAGE_DELETE_RESPONSE_TPL_ERROR', 'Erreur : R&eacute;ponse pr&eacute;d&eacute;finie non supprim&eacute;e');
define('_GTD_MESSAGE_ADD_STAFFREVIEW', 'R&eacute;vision ajout&eacute;e avec succ&egrave;s');
define('_GTD_MESSAGE_ADD_STAFFREVIEW_ERROR', 'Erreur : R&eacute;vision non ajout&eacute;e');
define('_GTD_MESSAGE_UPDATE_STAFF_ERROR', 'Erreur : Profil du Membre non mis &agrave; jour');
define('_GTD_MESSAGE_UPDATE_SIG_ERROR', 'Erreur : Signature non mise &agrave; jour');
define('_GTD_MESSAGE_UPDATE_SIG', 'Signature mise &agrave; jour');
define('_GTD_MESSAGE_EDITTICKET', 'Inscription mise &agrave; jour');
define('_GTD_MESSAGE_EDITTICKET_ERROR', 'Erreur : Inscription non mise &agrave; jour');
define('_GTD_MESSAGE_USER_MOREINFO', 'Inscription mise &agrave; jour avec succ&egrave;s.');
define('_GTD_MESSAGE_USER_MOREINFO_ERROR', 'Erreur : Informations non ajout&eacute;es');
define('_GTD_MESSAGE_USER_NO_INFO', 'Erreur: Vous avez soumis aucune nouvelle information');
define('_GTD_MESSAGE_EDITRESPONSE', 'R&eacute;ponse mise &agrave; jour avec succ&egrave;s');
define('_GTD_MESSAGE_EDITRESPONSE_ERROR', 'Erreur: R&eacute;ponse non mise &agrave; jour');
define('_GTD_MESSAGE_NOTIFY_UPDATE', 'Notifications mises &agrave; jour avec succ&egrave;s');
define('_GTD_MESSAGE_NOTIFY_UPDATE_ERROR', 'Notifications non mises &agrave; jour');
define('_GTD_MESSAGE_NO_NOTIFICATIONS', 'L\'utilisateur n\'avait pas de notification');
define('_GTD_MESSAGE_NO_DEPTS', 'Erreur : Pas de Type d&eacute;fini. Contacter l\'Administrateur.');
define('_GTD_MESSAGE_NO_STAFF', 'Erreur : Pas de Membre dans le Type concern&eacute;. Contacter l\'Administrateur.');
define('_GTD_MESSAGE_TICKET_REOPEN', 'Inscription r&eacute;-ouverte.');
define('_GTD_MESSAGE_TICKET_REOPEN_ERROR', 'Erreur : Inscription non r&eacute;ouverte.');
define('_GTD_MESSAGE_TICKET_CLOSE', 'Inscription close.');
define('_GTD_MESSAGE_TICKET_CLOSE_ERROR', 'Erreur: Inscription non close.');
define('_GTD_MESSAGE_NOT_USER', 'Erreur : vous ne pouvez pas r&eacute;ouvrir une Inscription que vous n\'avez pas soumise.');
define('_GTD_MESSAGE_NO_TICKETS', 'Erreur : Pas d\'inscription s&eacute;lectionn&eacute;e.');
define('_GTD_MESSAGE_NOOWNER', 'Pas de prof&eacute;sseur.');
define('_GTD_MESSAGE_UNKNOWN', 'Inconnu');
define('_GTD_MESSAGE_WRONG_MIMETYPE', 'Erreur : Type de Fichier non autoris&eacute;.');
define('_GTD_MESSAGE_NO_UID', 'Erreur : Pas de Num&eacute;ro Utilisateur sp&eacute;cifi&eacute;');
define('_GTD_MESSAGE_NO_GENRE', 'Erreur : Pas de Genre d&eacute;finie');
define('_GTD_MESSAGE_FILE_ERROR', 'Erreur : Impossible de stocker le fichier transmis pour la raison suivante :<br />%s');
define('_GTD_MESSAGE_UPDATE_EMAIL_ERROR', 'Erreur : Email non mis &agrave; jour');
define('_GTD_MESSAGE_TICKET_DELETE_CNFRM', 'Etes vous certain de vouloir effacer ces inscriptions ?');
define('_GTD_MESSAGE_DELETE_TICKETS', 'Inscriptions effec&eacute;es avec succ&egrave;s');
define('_GTD_MESSAGE_DELETE_TICKETS_ERROR', 'Erreur: les Inscriptions n\'ont pas &eacute;t&eacute; effac&eacute;es');
define('_GTD_MESSAGE_VALIDATE_ERROR', 'Votre inscription contient des erreurs, veuillez la corriger et la resoumettre.');
define('_GTD_MESSAGE_UNAME_TAKEN', ' est d&eacute;j&agrave; en cours d\'utilisation.');
define('_GTD_MESSAGE_INVALID', ' est non valide.');
define('_GTD_MESSAGE_REQUIRED', ' est requis');
define('_GTD_MESSAGE_LONG', ' est trop long.');
define('_GTD_MESSAGE_SHORT', ' est trop court.');
define('_GTD_MESSAGE_NOT_ENTERED', ' n\'est pas entr&eacute;.');
define('_GTD_MESSAGE_NOT_NUMERIC', ' n\'est pas num&eacute;rique.');
define('_GTD_MESSAGE_RESERVED', ' est r&eacute;serv&eacute;.');
define('_GTD_MESSAGE_NO_SPACES', ' ne doit pas contenir d\'espace');
define('_GTD_MESSAGE_NOT_SAME', ' n\'est pas le m&ecirc;me.');
define('_GTD_MESSAGE_NOT_SUPPLIED', ' n\'est pas demand&eacute;.');
define('_GTD_MESSAGE_CREATE_USER_ERROR', 'Utilisateur non cr&eacute;&eacute;');
define('_GTD_MESSAGE_NO_REGISTER', 'L\'inscription a &eacute;t&eacute; close. Vous n\'&ecirc;tes pas autoris&eacute; &agrave; suivre une inscription en ce moment.');
define('_GTD_MESSAGE_NEW_USER_ERR', 'Erreur: votre compte utilisateur n\'a pas &eacute;t&eacute; cr&eacute;&eacute;.');
define('_GTD_MESSAGE_EMAIL_USED', 'Erreur: cette adresse email est d&eacute;ja enregistr&eacute;e.');
define('_GTD_MESSAGE_DELETE_FILE_ERR', 'Erreur: le fichier n\'a pas &eacute;t&eacute; effac&eacute;.');
define('_GTD_MESSAGE_DELETE_SEARCH_ERR', 'Erreur: la recherche n\'a pas &eacute;t&eacute; &eacute;ffac&eacute;e.');

define('_GTD_MESSAGE_UPLOAD_ALLOWED_ERR', 'Erreur: Le t&eacute;l&eacute;chargement de fichier n\'est pas activ&eacute; pour le module.');
define('_GTD_MESSAGE_UPLOAD_ERR', 'le fichier %s de %s n\'a pas &eacute;t&eacute; enregist&eacute; parce que %s.');

define('_GTD_MESSAGE_NO_ADD_TICKET', 'Vous ne disposez pas des permissions pour enregistrer des inscriptions.');
define('_GTD_MESSAGE_NO_DELETE_TICKET', 'Vous ne disposez pas des permissions pour effacer des inscriptions.');
define('_GTD_MESSAGE_NO_EDIT_TICKET', 'Vous ne disposez pas des permissions pour &eacute;diter des inscriptions.');
define('_GTD_MESSAGE_NO_CHANGE_OWNER', 'Vous ne disposez pas des permissions pour changer de prof&eacute;sseur.');
define('_GTD_MESSAGE_NO_CHANGE_GENRE', 'Vous ne disposez pas des permissions pour changer de Genre.');
define('_GTD_MESSAGE_NO_CHANGE_STATUS', 'Vous ne disposez pas des permissions pour changer d\'&eacute;tat.');
define('_GTD_MESSAGE_NO_ADD_RESPONSE', 'Vous ne disposez pas des permissions pour ajouter une r&eacute;ponse.');
define('_GTD_MESSAGE_NO_EDIT_RESPONSE', 'Vous ne disposez pas des permissions pour &eacute;diter les r&eacute,ponses.');
define('_GTD_MESSAGE_NO_MERGE', 'Vous ne disposez pas des permissions pour fusionner les inscriptions.');
define('_GTD_MESSAGE_NO_TICKET2', 'Erreur: vous n\'avez pas sp&eacute;cifi&eacute; le inscription &agrave; fusionner avec.');
define('_GTD_MESSAGE_ADDED_EMAIL', 'Email ajout&eacute; avec succ&egrave;s.');
define('_GTD_MESSAGE_ADDED_EMAIL_ERROR', 'Erreur: l\'email n\'a pas &eacute;t&eacute; ajout&eacute;.');
define('_GTD_MESSAGE_NO_EMAIL', 'Erreur: vous n\'avez pas sp&eacute;cifi&eacute; l\'email &agrave; ajouter.');
define('_GTD_MESSAGE_ADD_EMAIL', 'Notifications d\'Email mises &agrave; jour.');
define('_GTD_MESSAGE_ADD_EMAIL_ERROR', 'Erreur : Notifications d\'Email non mises &agrave; jour.');
define('_GTD_MESSAGE_NO_MERGE_TICKET', 'Vous ne disposez pas des permissions pour supprimer un email');
define('_GTD_MESSAGE_NO_FILE_DELETE', 'Vous ne disposez pas des permissions pour effacer des fichiers.');
define('_GTD_MESSAGE_NO_CUSTFLD_ADDED', 'Erreur : La valeur du champs customis&eacute; n\'a pas &eacute;t&eacute; sauvegard&eacute;e.');

define('_GTD_ERROR_INV_TICKET', 'Erreur : Inscription invalide !');
define('_GTD_ERROR_INV_RESPONSE', 'Erreur : R&eacute;ponse invalide !');
define('_GTD_ERROR_NODEPTPERM', 'Vous ne pouvez pas soumettre une r&eacute;ponse &agrave; ce inscription. Raison : Vous n\'&ecirc;tes pas membre de ce Type.');
define('_GTD_ERROR_INV_STAFF', 'Erreur : l\'utilsateur n\'est pas un membre du Type.');
define('_GTD_ERROR_INV_TEMPLATE', 'Erreur : Mod&egrave;le invalide');
define('_GTD_ERROR_INV_USER', 'Erreur : Vous ne disposez pas des permissions pour visualiser cette inscription.');

define('_GTD_TITLE_ADDTICKET', 'Je m\'inscris');
define('_GTD_TITLE_ADDRESPONSE', 'Ajouter une R&eacute;ponse');
define('_GTD_TITLE_EDITTICKET', 'Editer les Info de l\'inscription');
define('_GTD_TITLE_EDITRESPONSE', 'Editer la R&eacute;ponse');
define('_GTD_TITLE_SEARCH', 'Rechercher');



define('_GTD_TEXT_NOM', 'Nom');
define('_GTD_TEXT_PRENOM', 'Pr&eacute;nom');
define('_GTD_TEXT_MODE_PAIEMENT', 'Je regle ma cotisation par :  ');
define('_GTD_MODE_PAIEMENT1', 'Cheque');
define('_GTD_MODE_PAIEMENT2', 'Especes');
define('_GTD_TEXT_ECHEANCE_PAIEMENT', 'Nous vous proposons de payer un plusieurs fois, choisissez votre mode de paiement : ');
define('_GTD_VISA', 'Paiement en 1 fois');
define('_GTD_MASTER_CARD', 'Paiement en 3 fois');
define('_GTD_TEXT_NOM_ET_PRENOM', 'Nom & Pr&eacute;nom');
define('_AM_GTD_TEXT_TARIF_EDIT_1', 'Tarif 1 Heure');
define('_AM_GTD_TEXT_TARIF_EDIT_2', 'Tarif 2 Heures');
define('_AM_GTD_TEXT_TARIF_EDIT_3', 'Tarif 3 Heures');





define('_GTD_TEXT_SIZE', 'Taille :');
define('_GTD_TEXT_REALNAME', 'Nom r&eacute;el');
define('_GTD_TEXT_ID', 'N°');
define('_GTD_TEXT_NAME', 'PSEUDO');
define('_GTD_TEXT_USER', 'Utilisateur:');
define('_GTD_TEXT_USERID', 'N° d\'Utilisateur :');
define('_GTD_TEXT_LOOKUP', 'Recherche');
define('_GTD_TEXT_LOOKUP_USER', 'Recherche Utilisateur');
define('_GTD_TEXT_EMAIL', 'Email :');
define('_GTD_TEXT_ASSIGNTO', 'Type');
define('_GTD_TEXT_GENRE', 'Genre');
define('_GTD_TEXT_STATUS', 'Statut');
define('_GTD_TEXT_SUBJECT', 'NOM Pr&eacute;nom');
define('_GTD_TEXT_DEPARTMENT', 'Type');
define('_GTD_TEXT_OWNER', 'prof&eacute;sseur');
define('_GTD_TEXT_CLOSEDBY', 'Clos par');
define('_GTD_TEXT_NOTAPPLY', 'N/A');
define('_GTD_TEXT_TIMESPENT', 'Temps Pass&eacute;');
define('_GTD_TEXT_DESCRIPTION', 'Message');
define('_GTD_TEXT_ADDFILE', 'Ajouter un fichier :');
define('_GTD_TEXT_FILE', 'Fichier :');
define('_GTD_TEXT_RESPONSE', 'R&eacute;ponse');
define('_GTD_TEXT_RESPONSES', 'R&eacute;ponses');
define('_GTD_TEXT_CLAIMOWNER', 'prof&eacute;sseur de la r&eacute;clamation :');
define('_GTD_TEXT_CLAIM_OWNER', 'S\'approprier la r&eacute;clamation');
define('_GTD_TEXT_TICKETDETAILS', 'D&eacute;tails de l\'inscription');
define('_GTD_TEXT_MINUTES', 'minutes');
define('_GTD_TEXT_SEARCH', 'Recherche :');
define('_GTD_TEXT_SEARCHBY', 'Par :');
define('_GTD_SEARCH_DESC', 'Message');
define('_GTD_SEARCH_SUBJECT', 'NOM Pr&eacute;nom');
define('_GTD_TEXT_NUMRESULTS', 'Nombre de r&eacute;sultats par page:');
define('_GTD_TEXT_RESULT1', '5');
define('_GTD_TEXT_RESULT2', '10');
define('_GTD_TEXT_RESULT3', '25');
define('_GTD_TEXT_RESULT4', '50');
define('_GTD_TEXT_SEARCH_RESULTS', 'R&eacute;sultats de la recherche');
define('_GTD_TEXT_PREDEFINED_RESPONSES', 'R&eacute;ponses Pr&eacute;-Definies :');
define('_GTD_TEXT_PREDEFINED0', '-- Cr&eacute;er une R&eacute;ponse --');
define('_GTD_TEXT_NO_USERS', 'Pas d\'utilisateurs trouv&eacute;');
define('_GTD_TEXT_SEARCH_AGAIN', 'Chercher de nouveau');
define('_GTD_TEXT_LOGGED_BY', 'Suivi par');
define('_GTD_TEXT_LOG_TIME', 'Faite le ');
define('_GTD_TEXT_OWNERSHIP_DETAILS', 'D&eacute;tails sur le prof&eacute;sseur');
define('_GTD_TEXT_ACTIVITY_LOG', 'Suivi d\'activit&eacute;');
define('_GTD_TEXT_HELPDESK_TICKET', 'Inscription de Support:');
define('_GTD_TEXT_YES', 'Oui');
define('_GTD_TEXT_NO', 'Non');
define('_GTD_TEXT_ALL_TICKETS', 'Tous les Inscriptions');
define('_GTD_TEXT_HIGH_GENRE', 'Inscriptions non-assign&eacute;s de Hautes Genre ');
define('_GTD_TEXT_NEW_TICKETS', 'Nouveaux Inscriptions');
define('_GTD_TEXT_MY_TICKETS', 'Inscriptions qui me sont assign&eacute;s');
define('_GTD_TEXT_SUBMITTED_TICKETS', 'Inscriptions que j\'ai soumis');
define('_GTD_TEXT_ANNOUNCEMENTS', 'Annonces');
define('_GTD_TEXT_MY_PERFORMANCE', 'Mes Performances');
define('_GTD_TEXT_RESPONSE_TIME', 'Temps moyen de r&eacute;ponse :');
define('_GTD_TEXT_RATING', 'Ratio :');
define('_GTD_TEXT_NUMREVIEWS', 'Nombre de r&eacute;visions :');
define('_GTD_TEXT_NUM_TICKETS_CLOSED', 'Nombres de d\'inscriptions closes :');
define('_GTD_TEXT_TEMPLATE_NAME', 'Nom du mod&egrave;le :');
define('_GTD_TEXT_MESSAGE', 'Message :');
define('_GTD_TEXT_ACTIONS', 'Actions :');
define('_GTD_TEXT_ACTIONS2', 'Actions');
define('_GTD_TEXT_MY_NOTIFICATIONS', 'Mes notifications');
define('_GTD_TEXT_SELECT_ALL', 'Tous');
define('_GTD_TEXT_USER_IP', 'IP utilisateur :');
define('_GTD_TEXT_OWNERSHIP', 'prof&eacute;sseur');
define('_GTD_TEXT_ASSIGN_OWNER', 'Assigner un prof&eacute;sseur');
define('_GTD_TEXT_TICKET', 'Inscription');
define('_GTD_TEXT_USER_RATING', 'Notation utilisateur :');
define('_GTD_TEXT_EDIT_RESPONSE', 'Editer R&eacute;ponse');
define('_GTD_TEXT_FILE_ADDED', 'Fichier Ajout&eacute; :');
define('_GTD_TEXT_ACTION', 'Action :');
define('_GTD_TEXT_LAST_TICKETS', 'Dernier Inscription soumis par :');
define('_GTD_TEXT_RATE_STAFF', 'Noter la r&eacute;ponse du Type');
define('_GTD_TEXT_COMMENTS', 'Commentaires :');
define('_GTD_TEXT_MY_OPEN_TICKETS', 'Mes Inscriptions en cours');
define('_GTD_TEXT_RATE_RESPONSE', 'Noter la R&eacute;ponse ?');
define('_GTD_TEXT_RESPONSE_RATING', 'Note de R&eacute;ponse :');
define('_GTD_TEXT_REOPEN_TICKET', 'Re-ouvrir une Inscription ?');
define('_GTD_TEXT_MORE_INFO', 'Un compl&eacute;ment d\'information est requis ?');
define('_GTD_TEXT_REOPEN_REASON', 'Raison de la r&eacute;-ouverture (optionnel)');
define('_GTD_TEXT_MORE_INFO2', 'Utilisez ce formulaire afin d\'ajouter un compl&eacute;ment d\'information &agrave; votre inscription.');
define('_GTD_TEXT_NO_DEPT', 'Pas de Type');
define('_GTD_TEXT_NOT_EMAIL', 'Adresse Email :');
define('_GTD_TEXT_LAST_REVIEWS', 'Derni&egrave;res r&eacute;visions du Type');
define('_GTD_TEXT_SORT_TICKETS', 'Trier les Inscriptions par cette colonne');
define('_GTD_TEXT_ELAPSED', 'Ecoul&eacute; :');
define('_GTD_TEXT_FILTERTICKETS', 'Filtrer les Inscriptions :');
define('_GTD_TEXT_LIMIT', 'Enregistrements par page');
define('_GTD_TEXT_SUBMITTEDBY', 'Soumis par :');
define('_GTD_TEXT_NO_INCLUDE', 'Aucun');
define('_GTD_TEXT_PRIVATE_RESPONSE', 'R&eacute;ponse priv&eacute;e :');
define('_GTD_TEXT_PRIVATE', 'Priv&eacute;');
define('_GTD_TEXT_CLOSE_TICKET', 'Fermer l\'Inscription ?');
define('_GTD_TEXT_ADD_SIGNATURE', 'Ajouter une signature aux r&eacute;ponses?');
define('_GTD_TEXT_LASTUPDATE', 'Derni&egrave;re mise &agrave; jour:');
define('_GTD_TEXT_BATCH_ACTIONS', 'Traitement par lot :');
define('_GTD_TEXT_BATCH_DEPARTMENT', 'Changer de Type');
define('_GTD_TEXT_BATCH_GENRE', 'Changer de Genre');
define('_GTD_TEXT_BATCH_STATUS', 'Changer le Statut');
define('_GTD_TEXT_BATCH_DELETE', 'Effacer les Inscriptions');
define('_GTD_TEXT_BATCH_RESPONSE', 'R&eacute;pondre');
define('_GTD_TEXT_BATCH_OWNERSHIP', 'Prendre/Assigner la Propri&eacute;t&eacute;');
define('_GTD_TEXT_UPDATE_COMP', 'Mise &agrave; jour r&eacute;ussie!');
define('_GTD_TEXT_TOPICS_ADDED', 'Sujets ajout&eacute;s');
define('_GTD_TEXT_DEPTS_ADDED', 'Types ajout&eacute;s');
define('_GTD_TEXT_CLOSE_WINDOW', 'Fermer la Fen&ecirc;tre');
define('_GTD_TEXT_USER_LOOKUP', 'R&eacute;solution d\'Utilisateur');
define('_GTD_TEXT_EVENT', 'Ev&eacute;nements');
define('_GTD_TEXT_AVAIL_FILETYPES', 'Types de fichiers valides');
define('_GTD_USER_REGISTER', 'Enregistrement d\'Utilisateur');

define('_GTD_TEXT_SETDEPT', 'Choisir un Type :');
define('_GTD_TEXT_SETGENRE', 'Param&egrave;trage de le Genre de l\'inscription :');
define('_GTD_TEXT_SETOWNER', 'Choisir un prof&eacute;sseur :');
define('_GTD_TEXT_SETSTATUS', 'Param&egrave;trage du Statut de l\'inscription:');
define('_GTD_TEXT_MERGE_TICKET', 'Fusionner les Inscriptions');
define('_GTD_TEXT_MERGE_TITLE', 'Entrer le n° de l\'inscription avec lequel vous voulez fusionner.');
define('_GTD_TEXT_EMAIL_NOTIFICATION', 'Notification d\'email:');
define('_GTD_TEXT_EMAIL_NOTIFICATION_TITLE', 'Ajouter une adresse email afin d\'&ecirc;tre notifi&eacute; des mises &agrave; jour de l\'inscriptions.');
define('_GTD_TEXT_RECEIVE_NOTIFICATIONS', 'Recevoir les Notifications:');
define('_GTD_TEXT_EMAIL_SUPPRESS', 'les Emails sont supprim&eacute;s. Cliquez pour envoyer les notifications d\'Email.');
define('_GTD_TEXT_EMAIL_NOT_SUPPRESS', 'les Emails ont &eacute;t&eacute; envoy&eacute;s. Cliquez pour les supprimer.');
define('_GTD_TEXT_TICKET_NOTIFICATIONS', 'Notifications des Inscriptions');
define('_GTD_TEXT_STATE', 'Etat:');
define('_GTD_TEXT_BY_STATUS', 'Par Statut:');
define('_GTD_TEXT_BY_STATE', 'Par Etat :');
define('_GTD_TEXT_SEARCH_OR', '-- OU --');
define('_GTD_TEXT_VIEW1', 'Vue classique');
define('_GTD_TEXT_VIEW2', 'Vue avanc&eacute;e');
define('_GTD_TEXT_SAVE_SEARCH', 'Sauvegarder la recherche ?');
define('_GTD_TEXT_SEARCH_NAME', 'Nom de recherche:');
define('_GTD_TEXT_SAVED_SEARCHES', 'Recherches sauvegard&eacute;es pr&eacute;vues');
define('_GTD_TEXT_SWITCH_TO', 'Permuter vers ');
define('_GTD_TEXT_ADDITIONAL_INFO', 'Information additionnelle');

define('_GTD_ROLE_NAME1', 'Gestionnaire des Inscriptions');
define('_GTD_ROLE_NAME2', 'Intervenant de Support');
define('_GTD_ROLE_NAME3', 'Simple Consultant');
define('_GTD_ROLE_DSC1', 'Peuvent faire tout et n\'importe quoi');
define('_GTD_ROLE_DSC2', 'Enregistre des inscriptions et des r&eacute;ponses, change le Statut ou le Genre, et enregistre des inscriptions pour les utilisateurs');
define('_GTD_ROLE_DSC3', 'ne peut faire aucun changement');
define('_GTD_ROLE_VAL1', '511');
define('_GTD_ROLE_VAL2', '241');
define('_GTD_ROLE_VAL3', '0');



// Inscription.php - Actions
define('_GTD_TEXT_SELECTED', 'Avec les inscriptions s&eacute;lectionn&eacute;s :');
define('_GTD_TEXT_ADD_RESPONSE', 'Ajouter une R&eacute;ponse');
define('_GTD_TEXT_EDIT_TICKET', 'Editer le Inscription');
define('_GTD_TEXT_DELETE_TICKET', 'Supprimer le Inscription');
define('_GTD_TEXT_PRINT_TICKET', 'Imprimer le Inscription');
define('_GTD_TEXT_UPDATE_GENRE', 'Mettre &agrave; jour le Genre');
define('_GTD_TEXT_UPDATE_STATUS', 'Mettre &agrave; jour l\'&eacute;tat');

define('_GTD_PIC_ALT_USER_AVATAR', 'Avatar utilisateur');

// Index.php - Auto Refresh Page vars
define('_GTD_TEXT_AUTO_REFRESH0', 'Pas d\'auto rafra&icirc;chissement');
define('_GTD_TEXT_AUTO_REFRESH1', 'Toutes les minutes');
define('_GTD_TEXT_AUTO_REFRESH2', 'Toutes les 5 minutes');
define('_GTD_TEXT_AUTO_REFRESH3', 'Toutes les 10 minutes');
define('_GTD_TEXT_AUTO_REFRESH4', 'Toutes les 30 minutes');
define('_GTD_AUTO_REFRESH0', 0);          // Change these to
define('_GTD_AUTO_REFRESH1', 60);         // adjust the values 
define('_GTD_AUTO_REFRESH2', 300);        // in the select box
define('_GTD_AUTO_REFRESH3', 600);
define('_GTD_AUTO_REFRESH4', 1800);

define('_GTD_MENU_MAIN', 'Sommaire');
define('_GTD_MENU_LOG_TICKET', 'S\'Inscrire');
define('_GTD_MENU_MY_PROFILE', 'Mon Profil');
define('_GTD_MENU_ALL_TICKETS', 'Voir Toutes mes Inscriptions');
define('_GTD_MENU_SEARCH', 'Rechercher');

define('_GTD_SEARCH_EMAIL', 'Email');
define('_GTD_SEARCH_USERNAME', 'Nom d\'Utilisateur');
define('_GTD_SEARCH_UID', 'N° d\'Utilisateur');

define('_GTD_BUTTON_ADDRESPONSE', 'Ajouter une R&eacute;ponse');
define('_GTD_BUTTON_ADDTICKET', 'S\'Inscrire');
define('_GTD_BUTTON_EDITTICKET', 'Editer une Inscription');
define('_GTD_BUTTON_RESET', 'Nettoyer');
define('_GTD_BUTTON_EDITRESPONSE', 'Mettre &agrave; jour la R&eacute;ponse');
define('_GTD_BUTTON_SEARCH', 'Recherche');
define('_GTD_BUTTON_LOG_USER', 'Enregistrer pour l\'utilisateur');
define('_GTD_BUTTON_FIND_USER', 'Rechercher un utilisateur');
define('_GTD_BUTTON_SUBMIT', 'Envoyer');
define('_GTD_BUTTON_DELETE', 'Supprimer');
define('_GTD_BUTTON_UPDATE', 'Mise &agrave; Jour');
define('_GTD_BUTTON_UPDATE_GENRE', 'Mettre &agrave; jour le Genre');
define('_GTD_BUTTON_UPDATE_STATUS', 'Mettre &agrave; jour l\'&eacute;tat');
define('_GTD_BUTTON_ADD_INFO', 'Ajouter des Info');
define('_GTD_BUTTON_SET', 'D&eacute;finir');
define('_GTD_BUTTON_ADD_EMAIL', 'Ajouter une adresse Email');
define('_GTD_BUTTON_RUN', 'Go');

define('_GTD_GENRE1', 1);
define('_GTD_GENRE2', 2);
define('_GTD_GENRE3', 3);
define('_GTD_GENRE4', 4);
define('_GTD_GENRE5', 5);

define('_GTD_TEXT_GENRE1', '2 Danseuses');
define('_GTD_TEXT_GENRE2', '2 Danseurs');
define('_GTD_TEXT_GENRE3', '1 Couple');
define('_GTD_TEXT_GENRE4', '1 Danseur');
define('_GTD_TEXT_GENRE5', '1 Danseuse');

define('_GTD_STATUS0', 'OUVERT');
define('_GTD_STATUS1', 'EN ATTENTE');
define('_GTD_STATUS2', 'CLOS');

define('_GTD_STATE1', 'Non R&eacute;solu');
define('_GTD_STATE2', 'R&eacute;solu');
define('_GTD_NUM_STATE1', 1);
define('_GTD_NUM_STATE2', 2);

define('_GTD_RATING0', 'aucune note');
define('_GTD_RATING1', 'mauvais');
define('_GTD_RATING2', 'en dessous de la moyenne');
define('_GTD_RATING3', 'moyen');
define('_GTD_RATING4', 'au dessus de la moyenne');
define('_GTD_RATING5', 'Excellent');

// Log Messages
define('_GTD_LOG_ADDTICKET', 'Inscription cr&eacute;&eacute;');
define('_GTD_LOG_ADDTICKET_FORUSER', 'Inscription cr&eacute;&eacute; pour %s par %s');
define('_GTD_LOG_EDITTICKET', 'Edition de l\'Inscription');
define('_GTD_LOG_UPDATE_GENRE', 'Genre mis &agrave; jour de :%u &agrave; :%u');
define('_GTD_LOG_UPDATE_STATUS', 'Statut mis &agrave; jour de  %s &agrave; %s');
define('_GTD_LOG_CLAIM_OWNERSHIP', 'Propri&eacute;t&eacute; r&eacute;clam&eacute;e');
define('_GTD_LOG_ASSIGN_OWNERSHIP', 'prof&eacute;sseur assign&eacute;e &agrave; %s');
define('_GTD_LOG_ADDRESPONSE', 'R&eacute;ponse ajout&eacute;e');
define('_GTD_LOG_USER_MOREINFO', 'Ajout d\'informations compl&eacute;mentaires');
define('_GTD_LOG_EDIT_RESPONSE', 'R&eacute;ponse # %s &eacute;dit&eacute;e');
define('_GTD_LOG_REOPEN_TICKET', 'Inscription re-ouvert');
define('_GTD_LOG_CLOSE_TICKET', 'Inscription clos');
define('_GTD_LOG_ADDRATING', 'Notation de R&eacute;ponse %u');
define('_GTD_LOG_SETDEPT', 'Assign&eacute; au Type %s');
define('_GTD_LOG_MERGETICKETS', 'Fusionner le inscription %s avec %s');
define('_GTD_LOG_DELETEFILE', 'Fichier %s effac&eacute;');

// Error checking for no records in DB
define('_GTD_NO_TICKETS_ERROR', 'Pas d\'Inscription trouv&eacute;');
define('_GTD_NO_RESPONSES_ERROR', 'Pas de r&eacute;ponse trouv&eacute;e');
define('_GTD_NO_MAILBOX_ERROR', 'Bo&icirc;te aux lettres invalide');
define('_GTD_NO_FILES_ERROR', 'Pas de fichier trouv&eacute;');

define('_GTD_SIG_SPACER', '<br /><br />-------------------------------<br />');
define('_GTD_COMMMENTS', 'Commentaires ?');
define("_GTD_ANNOUNCE_READMORE","Lire la suite...");
define("_GTD_ANNOUNCE_ONECOMMENT","1 commentaire");
define("_GTD_ANNOUNCE_NUMCOMMENTS","%s commentaires");
define("_GTD_TICKET_MD5SIGNATURE", "Cl&eacute; de Support :");


define('_GTD_NO_OWNER', 'Pas de prof&eacute;sseur');
define('_GTD_RESPONSE_EDIT', 'R&eacute;ponse modifi&eacute;e par %s le %s');

define('_GTD_TIME_SECS', 'secondes');
define('_GTD_TIME_MINS', 'minutes');
define('_GTD_TIME_HOURS', 'heures');
define('_GTD_TIME_DAYS', 'jours');
define('_GTD_TIME_WEEKS', 'semaines');
define('_GTD_TIME_YEARS', 'ann&eacute;es');

define('_GTD_TIME_SEC', 'seconde');
define('_GTD_TIME_MIN', 'minute');
define('_GTD_TIME_HOUR', 'heure');
define('_GTD_TIME_DAY', 'jour');
define('_GTD_TIME_WEEK', 'semaine');
define('_GTD_TIME_YEAR', 'ann&eacute;e');

define('_GTD_MAILEVENT_CLASS0', '0');     // Connection message
define('_GTD_MAILEVENT_CLASS1', '1');     // Parse message
define('_GTD_MAILEVENT_CLASS2', '2');     // Storage message
define('_GTD_MAILEVENT_CLASS3', '3');     // General message

define('_GTD_MAILEVENT_DESC0', 'Ne peut se connecter au serveur.');
define('_GTD_MAILEVENT_DESC1', 'Ne peut parser le message.');
define('_GTD_MAILEVENT_DESC2', 'Ne peut enregistrer le message.');
define('_GTD_MAILEVENT_DESC3', '');
define('_GTD_MBOX_ERR_LOGIN', 'Echec de connexion au serveur de messagerie : identifiant/mot de passe invalides');
define('_GTD_MBOX_INV_BOXTYPE', 'le type de bo&icirc;te mails sp&eacute;cifi&eacute; n\'est pas support&eacute;');

define('_GTD_MAIL_CLASS0', 'Connection');
define('_GTD_MAIL_CLASS1', 'Parsing');
define('_GTD_MAIL_CLASS2', 'Enregistrement');
define('_GTD_MAIL_CLASS3', 'G&eacute;n&eacute;ral');

define('_GTD_GROUP_PERM_DEPT', 'gtd_dept');
define('_GTD_MISMATCH_EMAIL', '%s a &eactue;t&eacute; notifi&eacute; que son message n\'a pas &eacte;t&eacute; sauvegard&eacute;. La cl&eacute; de support concorde, mais le message aurait du &ecicr;tre envoy&eacute; de %s pour le fait.');
define('_GTD_MESSAGE_MERGE', 'Fusion r&eacute;alis&eacute;e avec succ&egrave;s.');
define('_GTD_MESSAGE_MERGE_ERROR', 'Erreur: la fusion n\'a pas &eacute;t&eacute; r&eacute;alis&eacute;e.');
define('_GTD_RESPONSE_NO_TICKET', 'Aucun inscription trouv&eacute; pour la r&eacute;ponse de l\'inscription');
define('_GTD_MESSAGE_NO_ANON', 'Le Message de %s a &eacute;t&eacute; bloqu&eacute;, la soumission a inscription par les anonymes est d&eacute;sactiv&eacute;e');
define('_GTD_MESSAGE_EMAIL_DEPT_MBOX', 'Le Message de %s a &eacute;t&eacute; bloqu&eacute;, l\'exp&eacute;diteur est une bo&icirc;te email de Type');

define('_GTD_SIZE_BYTES', 'Bytes');
define('_GTD_SIZE_KB', 'KB');
define('_GTD_SIZE_MB', 'MB');
define('_GTD_SIZE_GB', 'GB');
define('_GTD_SIZE_TB', 'TB');

define('_GTD_TEXT_USER_NOT_ACTIVATED', 'L\'utilisateur n\'a pas termin&eacute; le processus d\'activation.');

define('_GTD_TEXT_ADMIN_DISABLED', '<em>[D&eacute;sactiv&eacute; par l\'Administrateur]</em>');

define('_GTD_TEXT_CURRENT_NOTIFICATION', 'M&eacute;thode de notification courante');
define('_GTD_NOTIFY_METHOD1', 'Message Priv&eacute;');
define('_GTD_NOTIFY_METHOD2', 'Email');

define('_GTD_TEXT_TICKET_LISTS', 'Liste des Inscriptions');
define('_GTD_TEXT_LIST_NAME', 'Nom de la Liste');
define('_GTD_TEXT_CREATE_NEW_LIST', 'Cr&eacute;er une nouvelle Liste');
define('_GTD_TEXT_NO_RECORDS', 'Aucun enregistement trouv&eacute;');
define('_GTD_TEXT_EDIT', 'Editer');
define('_GTD_TEXT_DELETE', 'Supprimer');
define('_GTD_TEXT_CREATE_SAVED_SEARCH', 'Cr&eacute;er une Recherche Sauvegard&eacute;e');
define('_GTD_MSG_ADD_TICKETLIST_ERR', 'Erreur: la liste des inscriptions n\' pas &eacute;t&eacute; cr&eacute;ee.');
define('_GTD_MSG_DEL_TICKETLIST_ERR', 'Erreur: la liste des tichets n\' pas &eacute;t&eacute; supprim&eacute;e.');
define('_GTD_MSG_NO_ID', 'Erreur: vous n\'avez pas  d&eacute;fini de num&eacute;ro.');
define('_GTD_TEXT_VIEW_MORE_TICKETS', 'Voir plus d\'Inscriptions');
define('_GTD_MSG_NO_EDIT_SEARCH', 'Erreur: vous n\'&ecirc;tes pas autoris&eacute;s &agrave; modifier cette recherche.');
define('_GTD_MSG_NO_DEL_SEARCH', 'Erreur: vous n\'&ecirc;tes pas autoris&eacute;s &agrave; supprimer cette recherche.');
define('_GTD_TEXT_ADD_STAFF', 'Ajouter un Membre');
?>