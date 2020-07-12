<?php
//$Id: modinfo.php,v 1.65 2005/09/15 19:51:18 eric_juden Exp $
define('_MI_GTD_NAME', 'Dance Inscription');
define('_MI_GTD_DESC', "Utilise pour stocker les demandes d'assistance des utilisateurs");

//Template variables
define('_MI_GTD_TEMP_ADDTICKET', 'Modele pour addInscription.php');
define('_MI_GTD_TEMP_SEARCH', 'Modele pour search.php');
define('_MI_GTD_TEMP_STAFF_INDEX', 'Modele Equipe pour index.php');
define('_MI_GTD_TEMP_STAFF_PROFILE', 'Modele pour profile.php');
define('_MI_GTD_TEMP_STAFF_TICKETDETAILS', 'Modele Equipe pour ticket.php');
define('_MI_GTD_TEMP_USER_INDEX', 'Modele Utilisateur pour index.php');
define('_MI_GTD_TEMP_USER_TICKETDETAILS', 'Modele Utilisateur pour ticket.php');
define('_MI_GTD_TEMP_STAFF_RESPONSE', 'Modele pour response.php');
define('_MI_GTD_TEMP_LOOKUP', 'Modele pour lookup.php');
define("_MI_GTD_TEMP_STAFFREVIEW", "Modele pour gerer un membre de l\'equipe (staffReview.php)");
define("_MI_GTD_TEMP_EDITTICKET", "Modele pour editer une Inscription (editInscription.php)");
define('_MI_GTD_TEMP_EDITRESPONSE', 'Modele pour editer une reponse (editResponse.php)');
define('_MI_GTD_TEMP_ANNOUNCEMENT', 'Modele pour les annonces');
define('_MI_GTD_TEMP_STAFF_HEADER', 'Modele pour les options du menu Equipe');
define('_MI_GTD_TEMP_USER_HEADER', 'Modele pour les options du menu Utilisateur');
define('_MI_GTD_TEMP_PRINT', 'Modele pour la page impression agreable des inscriptions');
define("_MI_GTD_TEMP_STAFF_ALL", "Modele pour la page Equipe 'Voir toutes les pages' ");
define('_MI_GTD_TEMP_STAFF_TICKET_TABLE', 'Modele afficher les inscriptions de l\'equipe');
define('_MI_GTD_TEMP_SETDEPT', 'Modele pour le parametrage de la Page Departement');
define('_MI_GTD_TEMP_SETGENRE', 'Modele pour le parametrage de la page Priorites');
define('_MI_GTD_TEMP_SETOWNER', 'Modele pour le parametrage de la Page Proprietaire');
define('_MI_GTD_TEMP_SETSTATUS', 'Modele pour la Page de Parametrage Statuts');
define('_MI_GTD_TEMP_DELETE', 'Modele pour la Page du Batch d\'effacement d\'Inscription');
define('_MI_GTD_TEMP_BATCHRESPONSE', 'Modele pour la Page du Batch d\'Ajout de Reponse');
define('_MI_GTD_TEMP_ANON_ADDTICKET', 'Modele pour la page d\'ajout d\'inscriptions des anonymes');
define('_MI_GTD_TEMP_ERROR', 'Modele pour la page d\'erreur');
define('_MI_GTD_TEMP_EDITSEARCH', 'Modele pour editer une recherche enregistree.');
define('_MI_GTD_TEMP_USER_ALL', 'Modele pour la page Utilisateur Voir Tout');


// Block variables
define('_MI_GTD_BNAME1', 'Mes Inscriptions en cours');
define('_MI_GTD_BNAME1_DESC', 'Affiche la liste des inscriptions ouverts pour l\'utilisateur');
define('_MI_GTD_BNAME2', 'Inscriptions par Departement');
define('_MI_GTD_BNAME2_DESC', 'Affiche le nombre d\'inscriptions ouverts pour chaque departement.');
define('_MI_GTD_BNAME3', 'Dernieres Inscriptions Vus');
define('_MI_GTD_BNAME3_DESC', 'Affiche les inscriptions qu\'un membre de l\'equipe vient de visualiser recemment.');
define('_MI_GTD_BNAME4', 'Actions');
define('_MI_GTD_BNAME4_DESC', 'Montrer toutes les action que le membre l\'equipe peut effectuer sur l\'inscriptions');
define('_MI_GTD_BNAME5', 'Actions Principales Gestion d\'Inscription');
define('_MI_GTD_BNAME5_DESC', 'Affiche les principales actions du systeme de gestion d\'inscriptions');

// Config variables
define('_MI_GTD_TITLE', 'Titre du HelpDesk');
define('_MI_GTD_TITLE_DSC', 'Donnez un nom au HelpDesk :');
define('_MI_GTD_UPLOAD', 'Repertoire de stockage des fichiers');
define('_MI_GTD_UPLOAD_DSC', 'Chemin o&ugrave; seront stockes les fichiers attaches &agrave; une inscription');
define('_MI_GTD_ALLOW_UPLOAD', 'Autoriser l\'envoi de fichiers');
define('_MI_GTD_ALLOW_UPLOAD_DSC', 'Autoriser les utilisateurs &agrave; ajouter un fichier &agrave; leur demande ?');
define('_MI_GTD_UPLOAD_SIZE', 'Taille des fichiers envoyes');
define('_MI_GTD_UPLOAD_SIZE_DSC', 'Taille Maxi des fichiers envoyes (en octets)');
define('_MI_GTD_UPLOAD_WIDTH', 'Largeur Maxi');
define('_MI_GTD_UPLOAD_WIDTH_DSC', 'Largeur Maxi des fichiers envoyes (en pixels)');
define('_MI_GTD_UPLOAD_HEIGHT', 'Hauteur Maxi');
define('_MI_GTD_UPLOAD_HEIGHT_DSC', 'Hauteur Maxi des fichiers envoyes (en pixels)');
define('_MI_GTD_NUM_TICKET_UPLOADS', 'Nombre maximum de fichiers uploadables');
define('_MI_GTD_NUM_TICKET_UPLOADS_DSC', 'Ceci est le nombre maximum  de fichiers qui peuvent &ecirc;tre joints &agrave; une inscription lors de la soummission d\'une inscription (ceci n\inclu par les fichiers des champs personnalises).');
define('_MI_GTD_ANNOUNCEMENTS', 'Sujet des annonces');
//define('_MI_GTD_ANNOUNCEMENTS_DSC', 'C\'est le sujet des annonces pour gtd. Mettez &agrave; jour le module gtd pour voir les nouvelles categories');
define('_MI_GTD_ANNOUNCEMENTS_DSC', "Ceci est le sujet des actualites qui poussera les annonces pour gtd. <a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/gtd/install.php?op=updateTopics\", \"xoops_module_install_gtd\",400, 300);'>Cliquez ici</a> pour mettre &agrave; jour les nouvelles categories.");
define('_MI_GTD_ANNOUNCEMENTS_NONE', '***Desactiver les annonces***');
define('_MI_GTD_ALLOW_REOPEN', 'Autoriser la reouverture d\'une Inscription');
define('_MI_GTD_ALLOW_REOPEN_DSC', 'Autorise les utilisateurs &agrave; reouvrir une Inscription solde ?');
define('_MI_GTD_STAFF_TC', 'Nombre d\'inscriptionss affiches pour l\'equipe');
define('_MI_GTD_STAFF_TC_DSC', 'Combien d\'inscriptionss doivent &ecirc;tre affiches pour chaque departement ?');
define('_MI_GTD_STAFF_ACTIONS', 'Style des Actions de l\'Equipe');
define('_MI_GTD_STAFF_ACTIONS_DSC', 'Quel style desirez vous appliquer aux actions de l\'Equipe ? Inligne-Style est le style par defaut, Block-Style requiert que vous activiez le bloc des Actions l\'Equipe.');
define('_MI_GTD_ACTION1', 'Style en ligne');
define('_MI_GTD_ACTION2', 'Style en Bloc');
define('_MI_GTD_DEFAULT_DEPT', 'Departement par defaut');
define('_MI_GTD_DEFAULT_DEPT_DSC', "Ceci est le departement selectionne par defaut dans la liste &agrave; l'ajout d\'inscriptions. <a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/gtd/install.php?op=updateDepts\", \"xoops_module_install_gtd\",400, 300);'>Cliquez ici</a> pour mettre &agrave; jour les departements.");
define('_MI_GTD_OVERDUE_TIME', 'Limite d\'execution en temps allouee &agrave; l\'inscription');
define('_MI_GTD_OVERDUE_TIME_DSC', 'Ceci determine le temps dont dispose l\'intervenant afin de cl&ocirc;turer l\'inscriptions avant qu\'il ne soit trop tard (en heures).');
define('_MI_GTD_ALLOW_ANON', 'Autoriser les utilisateurs anonymes &agrave; soumettre des inscriptions');
define('_MI_GTD_ALLOW_ANON_DSC', 'Ceci alloue la creation d\'inscriptions sur votre site &agrave; tout le monde. Lorsque les utilisateurs anonymes soumettent une inscription, ils sont aussitot convies &agrave; creer un compte .');
define('_MI_GTD_APPLY_VISIBILITY', 'Appliquer la visibilite du departement aux membres de l\'equipe ?');
define('_MI_GTD_APPLY_VISIBILITY_DSC', 'Ceci determine si l\'equipe est limite &agrave; quelques departement lors de la soumission d\'inscriptionss. si "oui" est selectionne, les membres de l\'equipe seront limites dans leurs soumissions d\'inscriptionss aux departements qui leurs sont attribues de part les permissions allouees &agrave; leur groupe.');
define('_MI_GTD_DISPLAY_NAME', 'Montrer le nom d\'utilisateur ou le nom reel ?');
define('_MI_GTD_DISPLAY_NAME_DSC', 'Ceci autorise l\'affichage des noms reels en lieu et place des pseudos comme cela l\'est generalement (Le pseudo sera montre s\'il n\'existe pas de nom reel).');
define('_MI_GTD_USERNAME', 'Pseudo');
define('_MI_GTD_REALNAME', 'Nom reel');

// Admin Menu variables
define('_MI_GTD_MENU_BLOCKS', 'Gestion des Blocs');
define('_MI_GTD_MENU_MANAGE_DEPARTMENTS', 'Gestion des Departements');
define('_MI_GTD_MENU_MANAGE_STAFF', 'Gestion des Equipes');
define('_MI_GTD_MENU_MODIFY_EMLTPL', 'Modifier le modele des Emails');
define('_MI_GTD_MENU_MODIFY_TICKET_FIELDS', 'Modifier les champs de l\'Inscription');
define('_MI_GTD_MENU_GROUP_PERM', 'Permissions des groupes');
define('_MI_GTD_MENU_ADD_STAFF', 'Ajouter une equipe');
define('_MI_GTD_MENU_MIMETYPES', 'Gestion des Mimes Types');
define('_MI_GTD_MENU_CHECK_TABLES', 'Contr&ocirc;le des Tables');
define('_MI_GTD_MENU_MANAGE_ROLES', 'Gestion des R&ocirc;les');
define('_MI_GTD_MENU_MAIL_EVENTS', 'Evenements d\'email');
define('_MI_GTD_MENU_CHECK_EMAIL', 'Contr&ocirc;ler les Emails');
define('_MI_GTD_MENU_MANAGE_FILES', 'Gestion de fichiers');
define('_MI_GTD_ADMIN_ABOUT', 'A propos');
define('_MI_GTD_TEXT_MANAGE_STATUSES', 'Gestion des etats');
define('_MI_GTD_TEXT_MANAGE_FIELDS', 'Gestion des champs personnalises');
define('_MI_GTD_TEXT_NOTIFICATIONS', 'Gestion de Notifications');

//NOTIFICATION vars
define('_MI_GTD_DEPT_NOTIFY','Departement');
define('_MI_GTD_DEPT_NOTIFYDSC', 'Options de Notification s\'appliquant &agrave; un departement');

define('_MI_GTD_TICKET_NOTIFY','Inscription');
define('_MI_GTD_TICKET_NOTIFYDSC','Option de Notification applicable pour l\'inscriptions actuel');

define('_MI_GTD_DEPT_NEWTICKET_NOTIFY', 'Sect : Nouvelle Inscription');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYCAP', 'Me prevenir lors de la creation d\'un nouve&agrave; l\'inscription');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYDSC', 'Recevoir une notification quand un nouve&agrave; l\'inscription est cree');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription creee - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYTPL', 'dept_newticket_notify.tpl');

define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFY', 'Sect : Suppression Inscription');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYCAP', 'Me prevenir lors de la suppression d\'une inscription');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYDSC', 'Recevoir une notification quand une inscription est supprime');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription supprimee - id {TICKET_ID}');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYTPL', 'dept_removedticket_notify.tpl');

define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFY', 'Sect : Modification Inscription');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYCAP', 'Me prevenir lors de la modification d\'une inscription');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYDSC', 'Recevoir une notification quand une inscription est modifie');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription modifiee - id {TICKET_ID}');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYTPL', 'dept_modifiedticket_notify.tpl');

define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFY', 'Sect : Nouvelle reponse');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYCAP', 'Me prevenir lorsqu\'une reponse est apportee');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYDSC', 'Recevoir une notification quand une reponse est apportee');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Reponse apportee a l Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYTPL', 'dept_newresponse_notify.tpl');

define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFY', 'Sect : Reponse modifiee');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYCAP', 'Me prevenir lorsqu\'une reponse est modifiee');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYDSC', 'Recevoir une notification quand une reponse est modifiee');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Inscription Reponse modifiee - id {TICKET_ID}');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYTPL', 'dept_modifiedresponse_notify.tpl');

define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFY', 'Sect : Changement d\'Etat d\'une Inscription');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYCAP', 'Me prevenir lorsque L\'Etat de l\'inscription est modifie');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYDSC', 'Recevoir une notification lorsque L\'Etat de l\'inscription est modifie');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYSBJ', 'Gotodance.fr :  Changement d\'Etat d\'une Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYTPL', 'dept_changedstatus_notify.tpl');

define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFY', 'Sect : Changement de Priorite d\'une Inscription');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYCAP', 'Me prevenir lorsque la priorite d\'une inscription est modifiee');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYDSC', 'Recevoir une notification lorsque la priorite d\'une inscription est modifiee');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYSBJ', 'Gotodance.fr :  Changement de priorite d\'une Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYTPL', 'dept_changedgenre_notify.tpl');

define('_MI_GTD_DEPT_NEWOWNER_NOTIFY', 'Sect : Nouveau responsable de l\'Inscription');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYCAP', 'Me prevenir lorsque le responsable d\'une inscription est modifie');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYDSC', 'Recevoir une notification lorsque le responsable dune inscription est modifie');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYSBJ', 'Gotodance.fr :  Responsable de l\'Inscription modifiee - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYTPL', 'dept_newowner_notify.tpl');

define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFY', 'Inscription : Supprimee');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYCAP', 'Me prevenir lorsque cette inscription est supprime');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYDSC', 'Recevoir une notification lorsque cette inscription est supprime');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription Supprimee - id {TICKET_ID}');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYTPL', 'ticket_removedticket_notify.tpl');

define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFY', 'Inscription : Modifiee');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYCAP', 'Me prevenir lorsque cette inscription est modifie');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYDSC', 'Recevoir une notification lorsque cette inscription est modifie');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription modifiee - id {TICKET_ID}');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYTPL', 'ticket_modifiedticket_notify.tpl');

define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFY', 'Inscription : Nouvelle Reponse');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYCAP', 'Me prevenir lorsqu\'une reponse est creee pour cette inscription');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYDSC', 'Recevoir une notification lorsqu\'une reponse est creee pour cette inscription');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Reponse creee pour cette Inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYTPL', 'ticket_newresponse_notify.tpl');

define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFY', 'Inscription : Reponse Modifiee');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYCAP', 'Me prevenir lorsqu\'une reponse est modifiee pour cette inscription');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYDSC', 'Recevoir une notification lorsqu\'une reponse est modifiee pour cette inscription');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Reponse &agrave; ce Inscription modifiee - id {TICKET_ID}');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYTPL', 'ticket_modifiedresponse_notify.tpl');

define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFY', 'Inscription : Changement d\'Etat');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYCAP', 'Me prevenir lorsque l\'Etat de cette inscription est modifie');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYDSC', 'Recevoir une notification lorsque l\'Etat de cette inscription est modifie');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYSBJ', 'Gotodance.fr : Mise a jour de votre inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYTPL', 'ticket_changedstatus_notify.tpl');

define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFY', 'Inscription : Changement de Professeur');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYCAP', 'Me prevenir lorsque le professeur de cette inscription est modifiee');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYDSC', 'Recevoir une notification lorsque la priorite de cette inscription est modifiee');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYSBJ', 'Gotodance.fr :  Professur de l\'Inscription modifie - id {TICKET_ID}');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYTPL', 'ticket_changedgenre_notify.tpl');

define('_MI_GTD_TICKET_NEWOWNER_NOTIFY', 'Inscription : Nouveau Responsable');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYCAP', 'Me prevenir lorsque le responsable change pour cette inscription');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYDSC', 'Recevoir une notification lorsque le reponsable de cette inscription est change');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYSBJ', 'Gotodance.fr :  Changement de propretaire de l\'Inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYTPL', 'ticket_newowner_notify.tpl');

define('_MI_GTD_TICKET_NEWTICKET_NOTIFY', 'Inscription: Nouvelle Inscription');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYCAP', 'Confirmer quand un nouve&agrave; l\'inscription est cree');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYDSC', 'Recevoir une notification quand un nouve&agrave; l\'inscription est cree');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription cree - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYTPL', 'ticket_newticket_notify.tpl');

define('_MI_GTD_DEPT_CLOSETICKET_NOTIFY', 'Sect : Fermeture Inscription');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYCAP', 'Me prevenir quand une inscription est clos');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYDSC', 'Recevoir une notification quand une inscription est clos');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription mise a jour - id {TICKET_ID}');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYTPL', 'dept_closeticket_notify.tpl');

define('_MI_GTD_TICKET_CLOSETICKET_NOTIFY', 'Inscription: Fermeture Inscription');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYCAP', 'Confirmer quand un Inscription est close');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYDSC', 'Recevoir une notification quand une Inscription est close');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription mise a jour - id {TICKET_ID}');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYTPL', 'ticket_closeticket_notify.tpl');

define('_MI_GTD_TICKET_NEWUSER_NOTIFY', 'Inscription: Nouvel Utilisateur cree &agrave; partir d\'une soumission d\'Email');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYCAP', 'Notifie l\'Utilisateur qu\'un nouveau compte a &eacte;te cr&eacte;e');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est cree &agrave; partir d\'une soumission d\'Email');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYSBJ', 'Gotodance.fr :  Nouvel Utilisateur cree');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYTPL', 'ticket_new_user_byemail.tpl');

define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFY', 'Inscription: Nouveau Utiliteur cree');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYCAP', 'Notifie l\'Utilisateur lorsqu\'un nouveau compte vient d\'&ecirc;tre cree');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est cree par un email de sousmission (Auto Activation)');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYSBJ', 'Gotodance.fr :  Nouveau Utilisateur cree');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYTPL', 'ticket_new_user_activation1.tpl');

define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFY', 'Inscription: Nouveau Utilisateur cree');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYCAP', 'Notifie l\'Utilisateur lorsqu\'un nouveau compte vient d\'&ecirc;tre cree');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est cree par un email de sousmission (Requiert une Activation d\'Admin)');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYSBJ', 'Gotodance.fr :  Nouvel Utilisateur cree');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYTPL', 'ticket_new_user_activation2.tpl');

define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFY', 'Inscription: Erreur d\'Email');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYCAP', 'Notifie l\'Utilisateur lorsque son email n\'est pas enregistre');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYDSC', 'Recevoir une notification quand l\'email de soumission n\'est pas enregistree');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYSBJ', 'RE: {TICKET_SUBJECT}');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYTPL', 'ticket_email_error.tpl');

define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFY', 'Sect : Fusion des Inscriptions');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYCAP', 'Notifier moi lorsque des inscriptions sont fusionnes');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYDSC', 'recevoir une notification lorsque les inscriptions sont fusionnes');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscriptions fusionnees');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYTPL', 'dept_mergeticket_notify.tpl');

define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFY', 'Inscription: Fusion d\'Inscriptions');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYCAP', 'Notifier moi lorsque des inscriptions sont fusionnes');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYDSC', 'Revevoir une notification lorsque des inscriptions sont fusionnes');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscriptions fusionnes');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYTPL', 'ticket_mergeticket_notify.tpl');

define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFY', 'Utilisateur : Nouve&agrave; l\'inscription par Email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYCAP', 'Confirmation de la creation d\'un nouve&agrave; l\'inscription par email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYDSC', 'Recevoir une notification lorsqu\'un nouve&agrave; l\'inscription est cree par email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYSBJ', 'RE: {TICKET_SUBJECT} {TICKET_SUPPORT_KEY}');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYTPL', 'ticket_newticket_byemail_notify.tpl');

// Be sure to add new mail_templates to array in admin/index.php - modifyEmlTpl()
?>