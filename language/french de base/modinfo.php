<?php
//$Id: modinfo.php,v 1.65 2005/09/15 19:51:18 eric_juden Exp $
define('_MI_GTD_NAME', 'gtd');
define('_MI_GTD_DESC', "Utilisé pour stocker les demandes d'assistance des utilisateurs");

//Template variables
define('_MI_GTD_TEMP_ADDTICKET', 'Modéle pour addInscription.php');
define('_MI_GTD_TEMP_SEARCH', 'Modéle pour search.php');
define('_MI_GTD_TEMP_STAFF_INDEX', 'Modéle Equipe pour index.php');
define('_MI_GTD_TEMP_STAFF_PROFILE', 'Modéle pour profile.php');
define('_MI_GTD_TEMP_STAFF_TICKETDETAILS', 'Modéle Equipe pour ticket.php');
define('_MI_GTD_TEMP_USER_INDEX', 'Modéle Utilisateur pour index.php');
define('_MI_GTD_TEMP_USER_TICKETDETAILS', 'Modéle Utilisateur pour ticket.php');
define('_MI_GTD_TEMP_STAFF_RESPONSE', 'Modéle pour response.php');
define('_MI_GTD_TEMP_LOOKUP', 'Modéle pour lookup.php');
define("_MI_GTD_TEMP_STAFFREVIEW", "Modéle pour gérer un membre de l'équipe (staffReview.php)");
define("_MI_GTD_TEMP_EDITTICKET", "Modéle pour éditer un Inscription (editInscription.php)");
define('_MI_GTD_TEMP_EDITRESPONSE', 'Modéle pour éditer une réponse (editResponse.php)');
define('_MI_GTD_TEMP_ANNOUNCEMENT', 'Modéle pour les annonces');
define('_MI_GTD_TEMP_STAFF_HEADER', 'Modéle pour les options du menu Equipe');
define('_MI_GTD_TEMP_USER_HEADER', 'Modéle pour les options du menu Utilisateur');
define('_MI_GTD_TEMP_PRINT', 'Modéle pour la page impression agréable des tickets');
define("_MI_GTD_TEMP_STAFF_ALL", "Modéle pour la page Equipe 'Voir toutes les pages' ");
define('_MI_GTD_TEMP_STAFF_TICKET_TABLE', 'Modéle afficher les tickets de l\'équipe');
define('_MI_GTD_TEMP_SETDEPT', 'Modéle pour le paramétrage de la Page Département');
define('_MI_GTD_TEMP_SETGENRE', 'Modéle pour le paramétrage de la page Priorités');
define('_MI_GTD_TEMP_SETOWNER', 'Modéle pour le paramétrage de la Page Propriétaire');
define('_MI_GTD_TEMP_SETSTATUS', 'Modéle pour la Page de Paramétrage Statuts');
define('_MI_GTD_TEMP_DELETE', 'Modéle pour la Page du Batch d\'effacement de Inscription');
define('_MI_GTD_TEMP_BATCHRESPONSE', 'Modéle pour la Page du Batch d\'Ajout de Réponse');
define('_MI_GTD_TEMP_ANON_ADDTICKET', 'Modéle pour la page d\'ajout de ticket des anonymes');
define('_MI_GTD_TEMP_ERROR', 'Modéle pour la page d\'erreur');
define('_MI_GTD_TEMP_EDITSEARCH', 'Modéle pour éditer une recherche enregistrée.');
define('_MI_GTD_TEMP_USER_ALL', 'Modéle pour la page Utilisateur Voir Tout');


// Block variables
define('_MI_GTD_BNAME1', 'Mes Inscriptions Ouverts');
define('_MI_GTD_BNAME1_DESC', 'Affiche la liste des tickets ouverts pour l\'utilisateur');
define('_MI_GTD_BNAME2', 'Inscriptions par Département');
define('_MI_GTD_BNAME2_DESC', 'Affiche le nombre de tickets ouverts pour chaque département.');
define('_MI_GTD_BNAME3', 'Derniers Inscriptions Vus');
define('_MI_GTD_BNAME3_DESC', 'Affiche les tickets qu\'un membre de l\'équipe vient de visualiser récemment.');
define('_MI_GTD_BNAME4', 'Actions de Inscription');
define('_MI_GTD_BNAME4_DESC', 'Montrer toutes les action que le membre l\'équipe peut effectuer sur le ticket');
define('_MI_GTD_BNAME5', 'Actions Principales Gestion de Inscription');
define('_MI_GTD_BNAME5_DESC', 'Affiche les principales actions du systéme de gestion de ticket');

// Config variables
define('_MI_GTD_TITLE', 'Titre du HelpDesk');
define('_MI_GTD_TITLE_DSC', 'Donnez un nom au HelpDesk :');
define('_MI_GTD_UPLOAD', 'Répertoire de stockage des fichiers');
define('_MI_GTD_UPLOAD_DSC', 'Chemin o&ugrave; seront stockés les fichiers attachés &agrave; un ticket');
define('_MI_GTD_ALLOW_UPLOAD', 'Autoriser l\'envoi de fichiers');
define('_MI_GTD_ALLOW_UPLOAD_DSC', 'Autoriser les utilisateurs &agrave; ajouter un fichier &agrave; leur demande ?');
define('_MI_GTD_UPLOAD_SIZE', 'Taille des fichiers envoyés');
define('_MI_GTD_UPLOAD_SIZE_DSC', 'Taille Maxi des fichiers envoyés (en octets)');
define('_MI_GTD_UPLOAD_WIDTH', 'Largeur Maxi');
define('_MI_GTD_UPLOAD_WIDTH_DSC', 'Largeur Maxi des fichiers envoyés (en pixels)');
define('_MI_GTD_UPLOAD_HEIGHT', 'Hauteur Maxi');
define('_MI_GTD_UPLOAD_HEIGHT_DSC', 'Hauteur Maxi des fichiers envoyés (en pixels)');
define('_MI_GTD_NUM_TICKET_UPLOADS', 'Nombre maximum de fichiers uploadables');
define('_MI_GTD_NUM_TICKET_UPLOADS_DSC', 'Ceci est le nombre maximum  de fichiers qui peuvent &ecirc;tre joints &agrave; un ticket lors de la soummission d\'un ticket (ceci n\inclu par les fichiers des champs personnalisés).');
define('_MI_GTD_ANNOUNCEMENTS', 'Sujet des annonces');
//define('_MI_GTD_ANNOUNCEMENTS_DSC', 'C\'est le sujet des annonces pour gtd. Mettez &agrave; jour le module gtd pour voir les nouvelles catégories');
define('_MI_GTD_ANNOUNCEMENTS_DSC', "Ceci est le sujet des actualités qui poussera les annonces pour gtd. <a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/gtd/install.php?op=updateTopics\", \"xoops_module_install_gtd\",400, 300);'>Cliquez ici</a> pour mettre &agrave; jour les nouvelles catégories.");
define('_MI_GTD_ANNOUNCEMENTS_NONE', '***Désactiver les annonces***');
define('_MI_GTD_ALLOW_REOPEN', 'Autoriser la réouverture d\'un Inscription');
define('_MI_GTD_ALLOW_REOPEN_DSC', 'Autorise les utilisateurs &agrave; réouvrir un Inscription soldé ?');
define('_MI_GTD_STAFF_TC', 'Nombre de tickets affichés pour l\'équipe');
define('_MI_GTD_STAFF_TC_DSC', 'Combien de tickets doivent &ecirc;tre affichés pour chaque département ?');
define('_MI_GTD_STAFF_ACTIONS', 'Style des Actions de l\'Equipe');
define('_MI_GTD_STAFF_ACTIONS_DSC', 'Quel style désirez vous appliquer aux actions de l\'Equipe ? Inligne-Style est le style par défaut, Block-Style requiert que vous activiez le bloc des Actions l\'Equipe.');
define('_MI_GTD_ACTION1', 'Style en ligne');
define('_MI_GTD_ACTION2', 'Style en Bloc');
define('_MI_GTD_DEFAULT_DEPT', 'Département par défaut');
define('_MI_GTD_DEFAULT_DEPT_DSC', "Ceci est le département sélectionné par défaut dans la liste &agrave; l'ajout de ticket. <a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/gtd/install.php?op=updateDepts\", \"xoops_module_install_gtd\",400, 300);'>Cliquez ici</a> pour mettre &agrave; jour les départements.");
define('_MI_GTD_OVERDUE_TIME', 'Limite d\'éxécution en temps allouée au ticket');
define('_MI_GTD_OVERDUE_TIME_DSC', 'Ceci détermine le temps dont dispose l\'intervenant afin de cl&ocirc;turer le ticket avant qu\'il ne soit trop tard (en heures).');
define('_MI_GTD_ALLOW_ANON', 'Autoriser les utilisateurs anonymes &agrave; soumettre des tickets');
define('_MI_GTD_ALLOW_ANON_DSC', 'Ceci alloue la création de ticket sur votre site &agrave; tout le monde. Lorsque les utilisateurs anonymes soumettent un ticket, ils sont aussitot conviés &agrave; créer un compte .');
define('_MI_GTD_APPLY_VISIBILITY', 'Appliquer la visibilité du département aux membres de l\'équipe ?');
define('_MI_GTD_APPLY_VISIBILITY_DSC', 'Ceci détermine si l\'équipe est limité &agrave; quelques département lors de la soumission de tickets. si "oui" est sélectionné, les membres de l\'équipe seront limités dans leurs soumissions de tickets aux départements qui leurs sont attribués de part les permissions allouées &agrave; leur groupe.');
define('_MI_GTD_DISPLAY_NAME', 'Montrer le nom d\'utilisateur ou le nom réel ?');
define('_MI_GTD_DISPLAY_NAME_DSC', 'Ceci autorise l\'affichage des noms réels en lieu et place des pseudos comme cela l\'est généralement (Le pseudo sera montré s\'il n\'existe pas de nom réel).');
define('_MI_GTD_USERNAME', 'Pseudo');
define('_MI_GTD_REALNAME', 'Nom réel');

// Admin Menu variables
define('_MI_GTD_MENU_BLOCKS', 'Gestion des Blocs');
define('_MI_GTD_MENU_MANAGE_DEPARTMENTS', 'Gestion des Départements');
define('_MI_GTD_MENU_MANAGE_STAFF', 'Gestion des Equipes');
define('_MI_GTD_MENU_MODIFY_EMLTPL', 'Modifier le modéle des Emails');
define('_MI_GTD_MENU_MODIFY_TICKET_FIELDS', 'Modifier les champs du Inscription');
define('_MI_GTD_MENU_GROUP_PERM', 'Permissions des groupes');
define('_MI_GTD_MENU_ADD_STAFF', 'Ajouter une équipe');
define('_MI_GTD_MENU_MIMETYPES', 'Gestion des Mimes Types');
define('_MI_GTD_MENU_CHECK_TABLES', 'Contr&ocirc;le des Tables');
define('_MI_GTD_MENU_MANAGE_ROLES', 'Gestion des R&ocirc;les');
define('_MI_GTD_MENU_MAIL_EVENTS', 'Evénements d\'email');
define('_MI_GTD_MENU_CHECK_EMAIL', 'Contr&ocirc;ler les Emails');
define('_MI_GTD_MENU_MANAGE_FILES', 'Gestion de fichiers');
define('_MI_GTD_ADMIN_ABOUT', 'A propos');
define('_MI_GTD_TEXT_MANAGE_STATUSES', 'Gestion des états');
define('_MI_GTD_TEXT_MANAGE_FIELDS', 'Gestion des champs personnalisés');
define('_MI_GTD_TEXT_NOTIFICATIONS', 'Gestion de Notifications');

//NOTIFICATION vars
define('_MI_GTD_DEPT_NOTIFY','Département');
define('_MI_GTD_DEPT_NOTIFYDSC', 'Options de Notification s\'appliquant &agrave; un département');

define('_MI_GTD_TICKET_NOTIFY','Inscription');
define('_MI_GTD_TICKET_NOTIFYDSC','Option de Notification applicable pour le ticket actuel');

define('_MI_GTD_DEPT_NEWTICKET_NOTIFY', 'Sect : Nouveau Inscription');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYCAP', 'Me prévenir lors de la création d\'un nouveau ticket');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYDSC', 'Recevoir une notification quand un nouveau ticket est créé');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription créée - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWTICKET_NOTIFYTPL', 'dept_newticket_notify.tpl');

define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFY', 'Sect : Suppression Inscription');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYCAP', 'Me prévenir lors de la suppression d\'un ticket');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYDSC', 'Recevoir une notification quand un ticket est supprimé');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription supprimée - id {TICKET_ID}');
define('_MI_GTD_DEPT_REMOVEDTICKET_NOTIFYTPL', 'dept_removedticket_notify.tpl');

define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFY', 'Sect : Modification Inscription');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYCAP', 'Me prévenir lors de la modification d\'un ticket');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYDSC', 'Recevoir une notification quand un ticket est modifié');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription modifiée - id {TICKET_ID}');
define('_MI_GTD_DEPT_MODIFIEDTICKET_NOTIFYTPL', 'dept_modifiedticket_notify.tpl');

define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFY', 'Sect : Nouvelle réponse');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYCAP', 'Me prévenir lorsqu\'une réponse est apportée');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYDSC', 'Recevoir une notification quand une réponse est apportée');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Réponse apportée au Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWRESPONSE_NOTIFYTPL', 'dept_newresponse_notify.tpl');

define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFY', 'Sect : Réponse modifiée');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYCAP', 'Me prévenir lorsqu\'une réponse est modifiée');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYDSC', 'Recevoir une notification quand une réponse est modifiée');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Inscription Réponse modifiée - id {TICKET_ID}');
define('_MI_GTD_DEPT_MODIFIEDRESPONSE_NOTIFYTPL', 'dept_modifiedresponse_notify.tpl');

define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFY', 'Sect : Changement d\'Etat d\'un Inscription');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYCAP', 'Me prévenir lorsque L\'Etat du ticket est modifié');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYDSC', 'Recevoir une notification lorsque L\'Etat du ticket est modifié');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYSBJ', 'Gotodance.fr :  Changement d\'Etat d\'un Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_CHANGEDSTATUS_NOTIFYTPL', 'dept_changedstatus_notify.tpl');

define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFY', 'Sect : Changement de Priorité d\'un Inscription');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYCAP', 'Me prévenir lorsque la priorité d\'un ticket est modifiée');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYDSC', 'Recevoir une notification lorsque la priorité d\'un ticket est modifiée');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYSBJ', 'Gotodance.fr :  Changement de priorité d\'un Inscription - id {TICKET_ID}');
define('_MI_GTD_DEPT_CHANGEDGENRE_NOTIFYTPL', 'dept_changedgenre_notify.tpl');

define('_MI_GTD_DEPT_NEWOWNER_NOTIFY', 'Sect : Nouveau responsable de Inscription');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYCAP', 'Me prévenir lorsque le responsable d\'un ticket est modifié');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYDSC', 'Recevoir une notification lorsque le responsable dun ticket est modifié');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYSBJ', 'Gotodance.fr :  Responsable de Inscription modifiée - id {TICKET_ID}');
define('_MI_GTD_DEPT_NEWOWNER_NOTIFYTPL', 'dept_newowner_notify.tpl');

define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFY', 'Inscription : Supprimée');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYCAP', 'Me prévenir lorsque ce ticket est supprimé');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYDSC', 'Recevoir une notification lorsque ce ticket est supprimé');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription Supprimée - id {TICKET_ID}');
define('_MI_GTD_TICKET_REMOVEDTICKET_NOTIFYTPL', 'ticket_removedticket_notify.tpl');

define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFY', 'Inscription : Modifié');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYCAP', 'Me prévenir lorsque ce ticket est modifié');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYDSC', 'Recevoir une notification lorsque ce ticket est modifié');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription modifiée - id {TICKET_ID}');
define('_MI_GTD_TICKET_MODIFIEDTICKET_NOTIFYTPL', 'ticket_modifiedticket_notify.tpl');

define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFY', 'Inscription : Nouvelle Réponse');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYCAP', 'Me prévenir lorsqu\'une réponse est créée pour ce ticket');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYDSC', 'Recevoir une notification lorsqu\'une réponse est créée pour ce ticket');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Réponse créée pour ce Inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWRESPONSE_NOTIFYTPL', 'ticket_newresponse_notify.tpl');

define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFY', 'Inscription : Réponse Modifiée');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYCAP', 'Me prévenir lorsqu\'une réponse est modifiée pour ce ticket');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYDSC', 'Recevoir une notification lorsqu\'une réponse est modifiée pour ce ticket');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYSBJ', 'Gotodance.fr :  Réponse &agrave; ce Inscription modifiée - id {TICKET_ID}');
define('_MI_GTD_TICKET_MODIFIEDRESPONSE_NOTIFYTPL', 'ticket_modifiedresponse_notify.tpl');

define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFY', 'Inscription : Changement d\'Etat');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYCAP', 'Me prévenir lorsque l\'Etat de ce ticket est modifié');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYDSC', 'Recevoir une notification lorsque l\'Etat de ce ticket est modifié');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYSBJ', 'Gotodance.fr : Mise à jour de votre inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_CHANGEDSTATUS_NOTIFYTPL', 'ticket_changedstatus_notify.tpl');

define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFY', 'Inscription : Changement de Priorité');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYCAP', 'Me prévenir lorsque la priorité de ce ticket est modifiée');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYDSC', 'Recevoir une notification lorsque la priorité de ce ticket est modifiée');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYSBJ', 'Gotodance.fr :  Priorité du Inscription modifié - id {TICKET_ID}');
define('_MI_GTD_TICKET_CHANGEDGENRE_NOTIFYTPL', 'ticket_changedgenre_notify.tpl');

define('_MI_GTD_TICKET_NEWOWNER_NOTIFY', 'Inscription : Nouveau Responsable');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYCAP', 'Me prevenir lorsque le responsable change pour ce ticket');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYDSC', 'Recevoir une notification lorsque le reponsable de ce ticket est changé');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYSBJ', 'Gotodance.fr :  Changement de proprétaire du Inscription - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWOWNER_NOTIFYTPL', 'ticket_newowner_notify.tpl');

define('_MI_GTD_TICKET_NEWTICKET_NOTIFY', 'Inscription: Nouveau Inscription');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYCAP', 'Confirmer quand un nouveau ticket est créé');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYDSC', 'Recevoir une notification quand un nouveau ticket est créé');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription créé - id {TICKET_ID}');
define('_MI_GTD_TICKET_NEWTICKET_NOTIFYTPL', 'ticket_newticket_notify.tpl');

define('_MI_GTD_DEPT_CLOSETICKET_NOTIFY', 'Sect : Fermeture de Inscription');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYCAP', 'Me prévenir quand un ticket est clos');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYDSC', 'Recevoir une notification quand un ticket est clos');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription mise à jour - id {TICKET_ID}');
define('_MI_GTD_DEPT_CLOSETICKET_NOTIFYTPL', 'dept_closeticket_notify.tpl');

define('_MI_GTD_TICKET_CLOSETICKET_NOTIFY', 'Inscription: Fermeture de Inscription');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYCAP', 'Confirmer quand un Inscription est clos');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYDSC', 'Recevoir une notification quand un Inscription est clos');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscription mise à jour - id {TICKET_ID}');
define('_MI_GTD_TICKET_CLOSETICKET_NOTIFYTPL', 'ticket_closeticket_notify.tpl');

define('_MI_GTD_TICKET_NEWUSER_NOTIFY', 'Inscription: Nouvel Utilisateur créé &agrave; partir d\'une soumission d\'Email');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYCAP', 'Notifie l\'Utilisateur qu\'un nouveau compte a &eacte;té cr&eacte;é');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est créé &agrave; partir d\'une soumission d\'Email');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYSBJ', 'Gotodance.fr :  Nouvel Utilisateur créé');
define('_MI_GTD_TICKET_NEWUSER_NOTIFYTPL', 'ticket_new_user_byemail.tpl');

define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFY', 'Inscription: Nouveau Utiliteur créé');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYCAP', 'Notifie l\'Utilisateur lorsqu\'un nouveau compte vient d\'&ecirc;tre créé');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est créé par un email de sousmission (Auto Activation)');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYSBJ', 'Gotodance.fr :  Nouveau Utilisateur créé');
define('_MI_GTD_TICKET_NEWUSER_ACT1_NOTIFYTPL', 'ticket_new_user_activation1.tpl');

define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFY', 'Inscription: Nouveau Utilisateur créé');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYCAP', 'Notifie l\'Utilisateur lorsqu\'un nouveau compte vient d\'&ecirc;tre créé');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYDSC', 'Recevoir une notification quand un nouveau utilisateur est créé par un email de sousmission (Requiert une Activation d\'Admin)');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYSBJ', 'Gotodance.fr :  Nouvel Utilisateur créé');
define('_MI_GTD_TICKET_NEWUSER_ACT2_NOTIFYTPL', 'ticket_new_user_activation2.tpl');

define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFY', 'Inscription: Erreur d\'Email');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYCAP', 'Notifie l\'Utilisateur lorsque son email n\'est pas enregistré');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYDSC', 'Recevoir une notification quand l\'email de soumission n\'est pas enregistrée');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYSBJ', 'RE: {TICKET_SUBJECT}');
define('_MI_GTD_TICKET_EMAIL_ERROR_NOTIFYTPL', 'ticket_email_error.tpl');

define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFY', 'Sect : Fusion de Inscriptions');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYCAP', 'Notifier moi lorsque des tickets sont fusionnés');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYDSC', 'recevoir une notification lorsque les tickets sont fusionnés');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscriptions fusionnés');
define('_MI_GTD_DEPT_MERGE_TICKET_NOTIFYTPL', 'dept_mergeticket_notify.tpl');

define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFY', 'Inscription: Fusion de Inscriptions');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYCAP', 'Notifier moi lorsque des tickets sont fusionnés');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYDSC', 'Revevoir une notification lorsque des tickets sont fusionnés');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYSBJ', 'Gotodance.fr :  Inscriptions fusionnés');
define('_MI_GTD_TICKET_MERGE_TICKET_NOTIFYTPL', 'ticket_mergeticket_notify.tpl');

define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFY', 'Utilisateur : Nouveau ticket par Email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYCAP', 'Confirmation de la création d\'un nouveau ticket par email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYDSC', 'Recevoir une notification lorsqu\'un nouveau ticket est créé par email');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYSBJ', 'RE: {TICKET_SUBJECT} {TICKET_SUPPORT_KEY}');
define('_MI_GTD_TICKET_NEWTICKET_EMAIL_NOTIFYTPL', 'ticket_newticket_byemail_notify.tpl');

// Be sure to add new mail_templates to array in admin/index.php - modifyEmlTpl()
?>