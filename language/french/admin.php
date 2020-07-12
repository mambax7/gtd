<?php
//$Id: admin.php,v 1.26 2005/09/15 19:37:24 eric_juden Exp $

//Menu choices
define('_AM_GTD_ADMIN_TITLE', 'Menu Administration %s');
define('_AM_GTD_BLOCK_TEXT', 'Gestion des Blocs');
define('_AM_GTD_MENU_MANAGE_DEPARTMENTS', 'Gestion des Sections');
define('_AM_GTD_MENU_MANAGE_STAFF', 'Gestion des professeurs');
define('_AM_GTD_MENU_MODIFY_EMLTPL', 'Modifier les Templates des Emails');
define('_AM_GTD_MENU_MODIFY_TICKET_FIELDS', 'Modifier les cours');
define('_AM_GTD_MENU_GROUP_PERM', 'Permissions des Groupes');
define('_AM_GTD_MENU_MIMETYPES', 'Gestion des MIME types');
define('_AM_GTD_MENU_PREFERENCES', 'Pr&eacute;f&eacute;rences');
define('_AM_GTD_MENU_ADD_STAFF', 'Ajouter un professeur');
define('_AM_GTD_UPDATE_MODULE', 'Mise &agrave; Jour du Module'); 
define('_AM_GTD_MENU_MANAGE_ROLES', 'Administration des R&ocirc;les');
define('_AM_GTD_TEXT_MANAGE_NOTIFICATIONS', 'Gestion des Notifications');

define('_AM_GTD_SEC_TICKET_ADD', 0);
define('_AM_GTD_SEC_TICKET_EDIT', 1);
define('_AM_GTD_SEC_TICKET_DELETE', 2);
define('_AM_GTD_SEC_TICKET_OWNERSHIP', 3);
define('_AM_GTD_SEC_TICKET_STATUS', 4);
define('_AM_GTD_SEC_TICKET_GENRE', 5);
define('_AM_GTD_SEC_TICKET_LOGUSER', 6);
define('_AM_GTD_SEC_RESPONSE_ADD', 7);
define('_AM_GTD_SEC_RESPONSE_EDIT', 8);
define('_AM_GTD_SEC_TICKET_MERGE', 9);
define('_AM_GTD_SEC_FILE_DELETE', 10);

define('_AM_GTD_SEC_TEXT_TICKET_ADD', 'Ajouter des inscriptions');
define('_AM_GTD_SEC_TEXT_TICKET_EDIT', 'Modifier des inscriptions');
define('_AM_GTD_SEC_TEXT_TICKET_DELETE', 'Supprimer des inscriptions');
define('_AM_GTD_SEC_TEXT_TICKET_OWNERSHIP', 'Changer le professeur');
define('_AM_GTD_SEC_TEXT_TICKET_STATUS', 'Changer le statut de l\'inscription');
define('_AM_GTD_SEC_TEXT_TICKET_GENRE', 'Changer la genre de l\'inscription');
define('_AM_GTD_SEC_TEXT_TICKET_LOGUSER', 'Changer d\'utilisateur');
define('_AM_GTD_SEC_TEXT_RESPONSE_ADD', 'Ajouter une R&eacute;ponse');
define('_AM_GTD_SEC_TEXT_RESPONSE_EDIT', 'Modifier une R&eacute;ponse');
define('_AM_GTD_SEC_TEXT_TICKET_MERGE', 'Fusionner des inscriptions');
define('_AM_GTD_SEC_TEXT_FILE_DELETE', 'Effacer les fichiers attach&eacute;s');

// Admin Menu
define('_AM_GTD_ADMIN_ABOUT', 'A propos');
define('_AM_GTD_ADMIN_GOTOMODULE', 'Aller au Module');

//Permissions
define('_AM_GTD_GROUP_PERM', 'Permissions des Groupes');
define('_AM_GTD_GROUP_PERM_TITLE', 'Modifier les Permissions des Groupes');
define('_AM_GTD_GROUP_PERM_NAME', 'Permissions');
define('_AM_GTD_GROUP_PERM_DESC', 'S&eacute;lectionnez le(s) service(s) que chaque groupe sera autoris&eacute; &agrave; modifier');

// Messages
define('_AM_GTD_MESSAGE_STAFF_UPDATE_ERROR', 'Erreur : Professeur non mise &agrave; jour');
define('_AM_GTD_MESSAGE_FILE_READONLY', 'Ce fichier est en lecture seule. Merci de rendre le r&eacute;pertoire modules/gtd/language/french/mail_templates ouvert en &eacute;criture');
define('_AM_GTD_MESSAGE_FILE_UPDATED', 'Fichier mis &agrave; jour avec succ&egrave;s');
define('_AM_GTD_MESSAGE_FILE_UPDATED_ERROR', 'Erreur : fichier non mis &agrave; jour');
define('_AM_GTD_MESSAGE_ROLE_INSERT', 'R&ocirc;le ajout&eacute; avec succ&egrave;s.');
define('_AM_GTD_MESSAGE_ROLE_INSERT_ERROR', 'Erreur: le r&ocirc;le n\'a pas &eacute;t&eacute; cr&eacute;&eacute;.');
define('_AM_GTD_MESSAGE_ROLE_DELETE', 'R&ocirc;le supprim&eacute; avec succ&egrave;s.');
define('_AM_GTD_MESSAGE_ROLE_DELETE_ERROR', 'Erreur: le R&ocirc;le n\'a pas &eacute;t&eacute; effac&eacute;.');
define('_AM_GTD_MESSAGE_ROLE_UPDATE', 'R&ocirc;le mis &agrave; jour avec succ&egrave;s.');
define('_AM_GTD_MESSAGE_ROLE_UPDATE_ERROR', 'Erreur: le r&ocirc;le n\'a pas &eacute;t&eacute; mis &agrave; jour.');
define('_AM_GTD_MESSAGE_DEPT_STORE', 'Permissions de la section enregistr&eacute;s avec succ&egrave;s.');
define('_AM_GTD_MESSAGE_DEPT_STORE_ERROR', 'Erreur: les permissions de la section n\'ont pas &eacute;t&eacute; enregistr&eacute;es.');
define('_AM_GTD_MESSAGE_DEF_ROLES', 'Les permission par d&eacute;faut des r&ocirc;les ont &eacute;t&eacute; ajout&eacute;es avec succ&egrave;s.');
define('_AM_GTD_MESSAGE_DEF_ROLES_ERROR', 'Les permission par d&eacute;faut des r&ocirc;les n\'ont pas &eacute;t&eacute; ajout&eacute;es.');
define('_AM_GTD_MESSAGE_NO_DEPT', 'Erreur: pas de section sp&eacute;cifi&eacute;');
define('_AM_GTD_MESSAGE_NO_DESC', 'Erreur: Vous n\'avez pas sp&eacute;cifi&eacute; de description.');
define('_AM_MESSAGE_ADD_STATUS_ERR', 'Erreur: le statut n\'a pas &eacute;t&eacute; ajout&eacute;.');
define('_AM_MESSAGE_EDIT_STATUS_ERR', 'Erreur: le statut n\'a pas &eacute;t&eacute; mis &agrave; jour.');
define('_AM_GTD_DEL_STATUS_ERR', 'Erreur: le statut n\'a pas &eacute;t&eacute; effac&eacute;.');
define('_AM_GTD_STATUS_HASTICKETS_ERR', 'Erreur: merci de mettre &agrave; jour les tickets qui utilisent ce Statut.');
define('_AM_GTD_MESSAGE_NO_ID', 'Erreur: le num&eacute;ro id n\'a pas &eacute;t&eacute; sp&eacute;cifi&eacute;.');
define('_AM_GTD_MESSAGE_NO_VALUE', 'Erreur: la valeur du mime type n\'a pas &eacute;t&eacute; sp&eacute;cifi&eacute;e.');
define('_AM_GTD_MESSAGE_EDIT_MIME_ERROR', 'Erreur: le mime type n\'a pas &eacute;t&eacute; mis &agrave; jour.');
define('_AM_GTD_MESSAGE_DELETE_MIME_ERROR', 'Erreur: le mime type n\'a pas &eacute;t&eacute; supprim&eacute;.');
define('_AM_GTD_MESSAGE_ADD_MIME_ERROR', 'Erreur: le mime type n\'a pas &eacute;t&eacute; ajout&eacute;.');

// Buttons
define('_AM_GTD_BUTTON_DELETE', 'Supprimer');
define('_AM_GTD_BUTTON_EDIT', 'Editer');
define('_AM_GTD_BUTTON_SUBMIT', 'Valider');
define('_AM_GTD_BUTTON_RESET', 'RAZ');
define('_AM_GTD_BUTTON_ADDSTAFF', 'Ajouter membre');
define('_AM_GTD_BUTTON_UPDATESTAFF', 'Mise &agrave; jour du professeur');
define('_AM_GTD_BUTTON_CANCEL', 'Annuler');
define('_AM_GTD_BUTTON_UPDATE', 'Mise &agrave; jour');
define('_AM_GTD_BUTTON_CREATE_ROLE', 'Cr&eacute;er un nouveau r&ocirc;le');
define('_AM_GTD_BUTTON_CLEAR_PERMS', 'Supprimer les Permissions');
//define('_AM_GTD_BUTTON_ADD_DEPT', 'Ajouter un D&eacute;partement');

define('_AM_GTD_EDIT_DEPARTMENT', 'Editer la section');
define('_AM_GTD_EXISTING_DEPARTMENTS', 'Sections existantes :');
define('_AM_GTD_MANAGE_DEPARTMENTS', 'Gestion des sections');
define('_AM_GTD_MANAGE_STAFF', 'Gestion des professeurs');
define('_AM_GTD_EXISTING_STAFF', 'Liste des professeurs :');
define('_AM_GTD_ADD_STAFF', 'Ajouter un professeur');
define('_AM_GTD_EDIT_STAFF', 'Editer les professeurs');
define('_AM_GTD_INDEX', 'Index');
define('_AM_GTD_DEPARTMENT_SERVERS', 'Boite Mail de section');
define('_AM_GTD_DEPARTMENT_SERVERS_EMAIL', 'Adresse Email');
define('_AM_GTD_DEPARTMENT_SERVERS_TYPE', 'Type de Boite Mail');
define('_AM_GTD_DEPARTMENT_SERVERS_GENRE', 'Genre par d&eacute;faut du Ticket');
define('_AM_GTD_DEPARTMENT_SERVERS_SERVERNAME', 'Serveur');
define('_AM_GTD_DEPARTMENT_SERVERS_PORT', 'Port');
define('_AM_GTD_DEPARTMENT_SERVERS_ACTION', 'Actions');
define('_AM_GTD_DEPARTMENT_ADD_SERVER', 'Ajouter la Boite Mail &agrave; surveiller');
define('_AM_GTD_DEPARTMENT_SERVER_USERNAME', 'Nom d\'Utilisateur');
define('_AM_GTD_DEPARTMENT_SERVER_PASSWORD', 'Mot de Passe');
define('_AM_GTD_DEPARTMENT_SERVER_EMAILADDRESS', 'Adresse du champs Reply-To');
define('_AM_GTD_DEPARTMENT_NO_ID', 'Ne peut pas trouver le  Num&eacute;ro du D&eacute;partement. Abandon.');
define('_AM_GTD_DEPARTMENT_SERVER_SAVED', 'Ajout d\'une Boite aux Lettres au D&eacute;partement.');
define('_AM_GTD_DEPARTMENT_SERVER_ERROR', 'Erreur lors de la Sauvegarde de la Boite Mail du D&eacute;partement.');
define('_AM_GTD_DEPARTMENT_SERVER_NO_ID', 'La Boite Mail du D&eacute;partement n\'a pas &eacte;t&eacute; d&eacute;finie');
define('_AM_GTD_DEPARTMENT_SERVER_DELETED', 'Boite Mail du D&eacute;partement Effac&eacute;e.');
define('_AM_GTD_DEPARTMENT_SERVER_DELETE_ERROR', 'Erreur lors de la suppression de la Boite Mail du D&eacute;partement.');
define('_AM_GTD_STAFF_ERROR_DEPTARTMENTS', 'Vous devez associer un utilisateur &agrave; 1 ou plusieurs d&eacute;partements avant de sauvegarder');
define('_AM_GTD_STAFF_ERROR_ROLES', 'Vous devez associer un utilisateur &agrave; 1 ou plusieurs r&ocirc;les avant de sauvegarder');
define('_AM_GTD_STAFF_ERROR_USERS', 'Vous devez associer un utilisateur &agrave; devenir membre de l\'&eacute;quipe.');
define('_AM_GTD_STAFF_EXISTS', 'Erreur : cet utilisateur est d&eacute;j&agrave; membre de l\'&eacute;quipe.');

define('_AM_GTD_MBOX_POP3', 'POP3');
define('_AM_GTD_MBOX_IMAP', 'IMAP');

define('_AM_GTD_TEXT_ADD_DEPT', 'Ajouter un D&eacute;partement :');
define('_AM_GTD_TEXT_EDIT_DEPT', 'Editer le Nom du D&eacute;partement :');
define('_AM_GTD_TEXT_EDIT', 'Editer');
define('_AM_GTD_TEXT_DELETE', 'Supprimer');
define('_AM_GTD_TEXT_SELECTUSER', 'S&eacute;lectionner le pseudo :');
define('_AM_GTD_TEXT_DEPARTMENTS', 'D&eacute;partements :');
define('_AM_GTD_TEXT_USER', 'Pseudo :');
define('_AM_GTD_TEXT_ALL_DEPTS', 'Tous');
define('_AM_GTD_TEXT_NO_DEPTS', 'Aucun');
define('_AM_GTD_TEXT_MAKE_DEPTS', 'Vous devez ajouter un D&eacute;partement avant de cr&eacute;er une &eacute;quipe !');
define('_AM_GTD_LINK_ADD_DEPT', 'Ajouter des D&eacute;partements');
define('_AM_GTD_TEXT_TOP_CLOSERS', 'Top Cl&ocirc;tureurs');
define('_AM_GTD_TEXT_WORST_CLOSERS', 'Pires Cl&ocirc;tureurs');
define('_AM_GTD_TEXT_HIGH_GENRE', 'Les derni&egrave;res inscriptions');
define('_AM_GTD_TEXT_NO_OWNER', 'Pas de professeur');
define('_AM_GTD_TEXT_NO_DEPT', 'Pas de Section');
define('_AM_GTD_TEXT_RESPONSE_TIME', 'Temps de r&eacute;ponse le plus rapide');
define('_AM_GTD_TEXT_RESPONSE_TIME_SLOW', 'Temps de r&eacute;ponse le plus long');
define('_AM_GTD_TEXT_GENRE', 'Genre :');
define('_AM_GTD_TEXT_ELAPSED', 'Ecoul&eacute; :');
define('_AM_GTD_TEXT_STATUS', 'Statut :');
define('_AM_GTD_TEXT_SUBJECT', 'Sujet :');
define('_AM_GTD_TEXT_DEPARTMENT', 'Section :');
define('_AM_GTD_TEXT_OWNER', 'Professeur :');
define('_AM_GTD_TEXT_LAST_UPDATED', 'Derni&egrave;re MAJ :');
define('_AM_GTD_TEXT_LOGGED_BY', 'Enregist&eacute; Par :');
define('_AM_GTD_TEXT_EXISTING_ROLES', 'R&ocirc;les existants');
define('_AM_GTD_TEXT_NO_ROLES', 'Pas de r&ocirc;les trouv&eacute;s');
define('_AM_GTD_TEXT_ROLES', 'R&ocirc;les:');
define('_AM_GTD_TEXT_CREATE_ROLE', 'Cr&eacute;er un nouveau R&ocirc;le');
define('_AM_GTD_TEXT_EDIT_ROLE', 'Editer un R&ocirc;le');
define('_AM_GTD_TEXT_NAME', 'Nom:');
define('_AM_GTD_TEXT_PERMISSIONS', 'Permissions:');
define('_AM_GTD_TEXT_SELECT_ALL', 'S&eacute;lectionner Tout');
define('_AM_GTD_TEXT_DEPT_PERMS', 'Personnaliser les Permissions de la Sectiont');
define('_AM_GTD_TEXT_CUSTOMIZE', 'Personnaliser');
define('_AM_GTD_TEXT_ACTIONS', 'Actions:');
define('_AM_GTD_TEXT_ID', 'N°:');
define('_AM_GTD_TEXT_LOOKUP_USER', 'Consulter l\'Utilisateur');
define('_AM_GTD_TEXT_BY', 'De');
define('_AM_GTD_TEXT_ASCENDING', 'Croissant');
define('_AM_GTD_TEXT_DESCENDING', 'D&eacute;croissant');
define('_AM_GTD_TEXT_SORT_BY', 'Trier par :');
define('_AM_GTD_TEXT_ORDER_BY', 'Ordre :');
define('_AM_GTD_TEXT_NUMBER_PER_PAGE', 'Nombre par Page:');
define('_AM_GTD_TEXT_SEARCH_MIME', 'Recherche de Mime type');
define('_AM_GTD_TEXT_SEARCH_BY', 'Rechercher par :');
define('_AM_GTD_TEXT_SEARCH_TEXT', 'Texte de la Recherche:');
define('_AM_GTD_TEXT_GO_BACK_SEARCH', 'Retour &agrave; la recherche');
define('_AM_GTD_TEXT_FIND_USERS', 'Trouver des utilisateurs');

define('_AM_GTD_SEARCH_BEGINEGINDATE', 'Date de d&eacute;but :');  
define('_AM_GTD_SEARCH_ENDDATE', 'Date de cloture :');

define('_AM_GTD_TEXT_ADD_STATUS', 'Ajouter un Statut');
define('_AM_GTD_TEXT_STATE', 'Statut :');
define('_AM_GTD_TEXT_MANAGE_STATUSES', 'Gestion des Statuts');
define('_AM_GTD_TEXT_EDIT_STATUS', 'Editer les Statuts');

define('_AM_GTD_TEXT_NO_RECORDS', 'Aucun enregistrement trouv&eacute;');
define('_AM_GTD_TEXT_MAIL_EVENTS', 'Ev&eacute;nements d\'emails');
define('_AM_GTD_TEXT_MAILBOX', 'Boite Mail :');
define('_AM_GTD_TEXT_EVENT_CLASS', 'Classe d\'&eacute;v&eacute;nement :');
define('_AM_GTD_TEXT_TIME', 'Heure :');
define('_AM_GTD_NO_EVENTS', 'aucun &eacute;v&eacute;nement trouv&eacute;');
define('_AM_GTD_SEARCH_EVENTS', 'Recherche d\'&eacute;v&egrave;nements Email');
define('_AM_GTD_BUTTON_SEARCH', 'Recherche');
define('_AM_GTD_BUTTON_TEST', 'Test');
define('_AM_GTD_POSITION', 'Position');
define('_AM_GTD_TEXT_MANAGE_FILES', 'Gestion des fichiers');
define('_AM_GTD_TEXT_TICKETID', 'N° de Ticket:');
define('_AM_GTD_TEXT_FILENAME', 'Nom de fichier :');
define('_AM_GTD_TEXT_MIMETYPE', 'Type Mime :');
define('_AM_GTD_TEXT_TOTAL_USED_SPACE', 'Total de l\'Espace utilis&eacute;');
define('_AM_GTD_TEXT_SIZE', 'Taille :');
define('_AM_GTD_TEXT_DELETE_RESOLVED', 'Supprimer les pi&egrave;ces jointes des tickets r&eacute;solus ?');
define('_AM_GTD_TEXT_NO_FILES', 'Aucun fichier trouv&eacute;');
define('_AM_GTD_TEXT_RESOLVED_ATTACH', 'Espace utilis&eacute; par les Pi&egrave;ces jointes des Tickets R&eacute;solus :');
define('_AM_GTD_TEXT_ALL_ATTACH', 'Toutes les pi&egrave;ces jointes :');
define('_AM_GTD_TEXT_MAINTENANCE', 'Taches de Maintenance');
define('_AM_GTD_TEXT_ORPHANED', 'Supprimer les enregistrements non associ&eacute; &agrave; des &eacute;quipes dans la table gtd_staff ?');
define('_AM_GTD_TEXT_DELETE_STAFF_DEPT', 'Supprimer le membre de l\&eacute;quipe du d&eacute;partement');
define('_AM_GTD_MSG_NO_DEPTID', 'Erreur: aucun d&eacute;partement sp&eacute;cifi&eacute;.');
define('_AM_GTD_MSG_NO_UID', 'Erreur: aucun utilisateur sp&eacute;cifi&eacute;.');
define('_AM_GTD_MSG_REMOVE_STAFF_DEPT_ERR', 'Erreur: le membre de l\'&eacte;quipe n\'a pas &eacute;t&eacute; supprim&eacute; du d&eacute;partement.');
define('_AM_GTD_TEXT_DEFAULT', 'D&eacute;faut');
define('_AM_GTD_TEXT_MAKE_DEFAULT_DEPT', 'D&eacute;finir comme d&eacute;partement par d&eacute;faut');
define('_AM_GTD_TEXT_DEFAULT_DEPT', 'D&eacute;partement par D&eacute;faut');
define('_AM_GTD_MSG_CHANGED_DEFAULT_DEPT', 'Mettre &agrave; jour d&eacute;partement par d&eacute;faut.');

// Mimetypes
define("_AM_GTD_MIME_ID", "N°");
define("_AM_GTD_MIME_EXT", "EXTENSION");
define("_AM_GTD_MIME_NAME", "Type d'Application");
define("_AM_GTD_MIME_ADMIN", "Membre Equipe");
define("_AM_GTD_MIME_USER", "Utilisateur");
// Mimetype Form
define("_AM_GTD_MIME_CREATEF", "Cr&eacute;er un Mime Type ");
define("_AM_GTD_MIME_MODIFYF", "Modifier Mime Type");
define("_AM_GTD_MIME_EXTF", "Extension fichier :");
define("_AM_GTD_MIME_NAMEF", "Type d'application :<div style='padding-top: 8px;'><span style='font-weight: normal;'>Entrer l'application associ&eacute;e &agrave; cette extension.</span></div>");
define("_AM_GTD_MIME_TYPEF", "Types Mime :<div style='padding-top: 8px;'><span style='font-weight: normal;'>Entrer chaque mime type associ&eacute; avec l'extension. Chaque mime type doit &ecirc;tre s&eacute;par&eacute; avec un espace.</span></div>");
define("_AM_GTD_MIME_ADMINF", "Mime Types autoris&eacute;s pour l'Admin");
define("_AM_GTD_MIME_ADMINFINFO", "<b>Mime Types autoris&eacute;s pour l'envoi de fichier par l'admin :</b>");
define("_AM_GTD_MIME_USERF", "Mime Types autoris&eacute;s pour les utilisateurs");
define("_AM_GTD_MIME_USERFINFO", "<b>Mime Types autoris&eacute;s pour l'envoi de fichier par les utilisateurs :</b>");
define("_AM_GTD_MIME_NOMIMEINFO", "Pas de Mime Type s&eacute;lectionn&eacute;.");
define("_AM_GTD_MIME_FINDMIMETYPE", "Trouver un nouveau Mime Type");
define("_AM_GTD_MIME_EXTFIND", "Recherche Extension de fichier :<div style='padding-top: 8px;'><span style='font-weight: normal;'>Entrer l'extension de fichier recherch&eacute;e.</span></div>");
define("_AM_GTD_MIME_INFOTEXT", "<ul><li>Un nouveau Mime Types peut &ecirc;tre cr&eacute;e, &eacute;dit&eacute; ou supprim&eacute; via ce formulaire.</li> 
	<li>Rechercher de nouveaux Mime Types via un site web externe.</li> 
	<li>Voir les Mime Types affich&eacute;s lors de l'envoi de fichier par les admins et les utilisateurs.</li> 
	<li>Changer le statut d'envoi d'un Mime Type.</li></ul> 
	");

// Mimetype Buttons
define("_AM_GTD_MIME_CREATE", "Cr&eacute;er");
define("_AM_GTD_MIME_CLEAR", "RAZ");
define("_AM_GTD_MIME_CANCEL", "Annuler");
define("_AM_GTD_MIME_MODIFY", "Modifier");
define("_AM_GTD_MIME_DELETE", "Supprimer");
define("_AM_GTD_MIME_FINDIT", "R&eacute;cuperer l'Extension !");
// Mimetype Database
define("_AM_GTD_MIME_DELETETHIS", "Supprimer le Mime Type s&eacute;lectionn&eacute; ?");
define("_AM_GTD_MIME_MIMEDELETED", "Le Mime Type %s a &eacute;t&eacute; supprim&eacute;");
define("_AM_GTD_MIME_CREATED", "Information du Mime Type Cr&eacute;&eacute;e");
define("_AM_GTD_MIME_MODIFIED", "Information du Mime Type Modifi&eacute;e");

define("_AM_GTD_MINDEX_ACTION", "Action");
define("_AM_GTD_MINDEX_PAGE", "<b>Page :<b> ");

//image admin icon 
define("_AM_GTD_ICO_EDIT","Editer cet &eacute;l&eacute;ment");
define("_AM_GTD_ICO_DELETE","Supprimer cet &eacute;l&eacute;ment");
define("_AM_GTD_ICO_ONLINE","En ligne");
define("_AM_GTD_ICO_OFFLINE","Hors ligne");
define("_AM_GTD_ICO_APPROVED","Approuv&eacute;");
define("_AM_GTD_ICO_NOTAPPROVED","Non Approuv&eacute;");

define("_AM_GTD_ICO_LINK","Lien relatif");
define("_AM_GTD_ICO_URL","Ajouter l'URL associ&eacute;e");
define("_AM_GTD_ICO_ADD","Ajouter");
define("_AM_GTD_ICO_APPROVE","Approuver");
define("_AM_GTD_ICO_STATS","Stats");

define("_AM_GTD_ICO_IGNORE","Ignorer");
define("_AM_GTD_ICO_ACK","Rapport de lien bris&eacute; Accept&eacute;");
define("_AM_GTD_ICO_REPORT","Accepter le rapport de lien bris&eacute;?");
define("_AM_GTD_ICO_CONFIRM","Rapport de lien bris&eacute; confirm&eacute;");
define("_AM_GTD_ICO_CONBROKEN","Confirmer le rapport de lien bris&eacute;?");

define("_AM_GTD_UPLOADFILE", "Fichier transmis correctement");
define('_AM_GTD_TEXT_TICKET_INFO', 'Informations sur les Inscriptions');
define('_AM_GTD_TEXT_OPEN_TICKETS', 'Tickets ouverts');
define('_AM_GTD_TEXT_HOLD_TICKETS', 'Tickets en cours');
define('_AM_GTD_TEXT_CLOSED_TICKETS', 'Tickets ferm&eacute;s');
define('_AM_GTD_TEXT_TOTAL_TICKETS', 'Total Tickets');

define('_AM_GTD_TEXT_TEMPLATE_NAME', 'Nom du mod&egrave;le :');
define('_AM_GTD_TEXT_DESCRIPTION', 'Description :');
define('_AM_GTD_TEXT_PATH', 'Chemin :');
define('_AM_GTD_TEXT_GENERAL_TAGS', 'Tags Usuels');
define('_AM_GTD_TEXT_GENERAL_TAGS1', 'X_SITEURL - URL du site');
define('_AM_GTD_TEXT_GENERAL_TAGS2', 'X_SITENAME - nom du site');
define('_AM_GTD_TEXT_GENERAL_TAGS3', 'X_ADMINMAIL - adresse mail de l\'administrateur');
define('_AM_GTD_TEXT_GENERAL_TAGS4', 'X_MODULE - nom du module');
define('_AM_GTD_TEXT_GENERAL_TAGS5', 'X_MODULE_URL - lien vers la page index du module');
define('_AM_GTD_TEXT_TAGS_NO_MODIFY', 'Ne pas modifier les autres tags que ceux list&eacute;s !');

define('_AM_GTD_CURRENTVER', 'Version Courante: <span class="currentVer">%s</span>');
define('_AM_GTD_DBVER', 'Version de la Base de Donn&eacute;es : <span class="dbVer">%s</span>');
define('_AM_GTD_DB_NOUPDATE', 'Votre Base de Donn&eacute;es est &agrave; jour. Aucune mise &agrave; jour n\'est n&eacute;c&eacute;ssaire.');
define('_AM_GTD_DB_NEEDUPDATE', 'Votre Base de Donn&eacute;es est p&eacute;rim&eacute;e. Veuillez mettre &agrave; jour les tables de votre Base de Donn&eacute;es !');
define('_AM_GTD_UPDATE_NOW', 'Mettre &agrave; jour maintenant !');
define('_AM_GTD_DB_NEEDINSTALL', 'Votre Base de Donn&eacute;es n\'est pas synchronis&eacute;e avec la version install&eacute;e. Veuillez installer la m&ecirc;me version que celle de la Base de Donn&eacute;es');
define('_AM_GTD_VERSION_ERR', 'Impossible de d&eacute;terminer la version pr&eacute;vue.');
define('_AM_GTD_MSG_MODIFYTABLE', 'Table %s modifi&eacute;e');
define('_AM_GTD_MSG_MODIFYTABLE_ERR', 'Erreur lors de la modification de la table %s');
define('_AM_GTD_MSG_ADDTABLE', 'Table %s ajout&eacute;e');
define('_AM_GTD_MSG_ADDTABLE_ERR', 'Erreur lors de l\'ajout de la table %s');
define('_AM_GTD_MSG_UPDATESTAFF', 'Equipe #%s mise &agrave; jour');
define('_AM_GTD_MSG_UPDATESTAFF_ERR', 'Erreur lors de la mise &agrave; jour de l\'&eacute;quipe #%s');
define('_AM_GTD_UPDATE_DB', 'Mise &agrave; jour de la Base de Donn&eacute;es :');
define('_AM_GTD_UPDATE_TO', 'Mise &agrave; jour vers la version %s:');
define('_AM_GTD_UPDATE_OK', 'Mise &agrave; jour vers la version %s r&eacute;ussie');
define('_AM_GTD_UPDATE_ERR', 'Erreurs lors de la mise &agrave; jour vers la version %s');
define('_AM_GTD_MSG_UPD_PERMS', 'Permissions de l\'Equipe #%s ajout&eacute;s pour le d&eacute;partement #%s.');
define('_AM_GTD_MSG_REMOVE_TABLE', 'La Table %s a &eacute;t&eacute; effac&eacute;e de votre base de donn&eacute;es.');
define('_AM_GTD_MSG_GLOBAL_PERMS', 'L\'&eacute;quipe #%s dispose des permissions globales.');
define('_AM_GTD_MSG_NOT_REMOVE_TABLE', 'Erreur: la table %s n\'a pas &eacute;t&eacute; effac&eacute;e de votre base de donn&eacute;es.');
define('_AM_GTD_MSG_RENAME_TABLE', 'la Table %s a &eacte;t&eacute; renomm&eacute;e en %s.');
define('_AM_GTD_MSG_RENAME_TABLE_ERR', 'Erreur: la table %s n\'a pas &eacute;t&eacute; renomm&eacute;e.');
define('_AM_GTD_MSG_UPDATE_ROLE', 'Les permissions du r&ocirc;le %s ont &eacute;t&eacute; mises &agrave; jour.');
define('_AM_GTD_MSG_UPDATE_ROLE_ERR', 'Erreur: les permissions du r&ocirc;le %s n\'ont pas &eacte;t&eacute; mises &agrave; jour.');
define('_AM_GTD_MSG_DEPT_DEL_CFRM', 'Etes-vous certain de vouloir effacer le D&eacute;partement #%u?');
define('_AM_GTD_MSG_STAFF_DEL_CFRM', 'Etes-vous certain de vouloir effacer l\'Equipe #%u?');
define('_AM_GTD_MSG_DEPT_MBOX_DEL_CFRM', 'Etes vous certain de vouloir effacer la boite mail %s?');
define('_AM_GTD_MSG_ADD_STATUS_ERR', 'Erreur: Le Statut \'%s\' n\'a pas &eacute;t&eacute; ajout&eacute;.');
define('_AM_GTD_MSG_ADD_STATUS', 'Le Statut \'%s\' a &eacute;t&eacute; ajout&eacute;.');
define('_AM_GTD_MSG_CHANGED_STATUS', 'Tickets mis &agrave; jour avec le nouveau Statut.');
define('_AM_GTD_MSG_CHANGED_STATUS_ERR', 'Erreur: Statut du ticket non mis &agrave; jour.');
define('_AM_GTD_MSG_DELETE_RESOLVED', 'Etes-vous certain de vouloir supprimer les pi&egrave;ces jointes des tickets r&eacute;solus ?');
define('_AM_GTD_MSG_DELETE_FILE', 'Etes-vous certain de vouloir effacer cette pi&egrave;ce jointe ?');
define('_AM_GTD_MSG_ADD_CONFIG_ERR', 'Erreur: le nouveau param&egrave;trage pour le d&eacute;partement n\'a pa &eacute;t&eacute; sauvegard&eacute;');
define('_AM_GTD_MSG_UPDATE_CONFIG_ERR', 'Erreur: le nouveau param&egrave;trage pour le d&eacute;partement n\'a pas &eacute;t&eacute; mis &agrave; jour');
define('_AM_GTD_MSG_CLEAR_ORPHANED_ERR', 'Vos enregistrement Equipe ont &eacute;t&eacute; mis &agrave; jour.');
define('_AM_GTD_MSG_UPDATE_SEARCH', 'La Recherche Sauvegard&eacute;e #%u a &eacute;t&eacute; mise &agrave; jour.');
define('_AM_GTD_MSG_UPDATE_SEARCH_ERR', 'Erreur: La Recherche Sauvegard&eacute;e #%u n\'a pas &eacute;t&eacute; mise &agrave; jour.');

define('_AM_GTD_TEXT_CONTRIB_INFO', 'Informations sur les Contributeurs');
define('_AM_GTD_TEXT_DEVELOPERS', 'D&eacute;velopeurs:');
define('_AM_GTD_TEXT_TRANSLATORS', 'Traducteurs:');
define('_AM_GTD_TEXT_TESTERS', 'Testeurs:');
define('_AM_GTD_TEXT_DOCUMENTER', 'Documentation:');
define('_AM_GTD_TEXT_CODE', 'Patchs:');
define('_AM_GTD_TEXT_MODULE_DEVELOPMENT', 'Informations de D&eacute;velopment du Module');
define('_AM_GTD_TEXT_DEMO_SITE', 'Site de d&eacute;mo :');
define('_AM_GTD_DEMO_SITE', 'Site de d&eacute;mo');
define('_AM_GTD_TEXT_OFFICIAL_SITE', 'Site de support officiel :');
define('_AM_GTD_OFFICIAL_SITE', 'Site officiel');
define('_AM_GTD_TEXT_REPORT_BUG', 'Vous avez d&eacute;couvert, un bug?');
define('_AM_GTD_REPORT_BUG', 'Rapporter un Bug');
define('_AM_GTD_TEXT_NEW_FEATURE', 'Vous d&eacute;sirez une nouvelle fonctionnalit&eacute; ?');
define('_AM_GTD_NEW_FEATURE', 'Proposer une nouvelle fonctionnalit&eacute;');
define('_AM_GTD_TEXT_QUESTIONS', 'Questions ?');
define('_AM_GTD_QUESTIONS', 'Poser une question aux d&eacute;veloppeurs du module');
define('_AM_GTD_TEXT_RELEASE_DATE', 'Date de mise &agrave; disposition :');
define('_AM_GTD_TEXT_DISCLAIMER', 'Avertissement');
define('_AM_GTD_DISCLAIMER', 'Attention : Il ne doit pas &ecirc;tre utilis&eacute; sur un site de production. Les d&eacute;veloppeurs ne peuvent &ecirc;tre tenus responsables en aucune sorte des troubles pouvant &ecirc;tre occasionn&eacute;s par l\'utilisation de ce module.');
define('_AM_GTD_TEXT_CHANGELOG', 'Journal de modifications');
define('_AM_GTD_TEXT_EDIT_DEPT_PERMS', 'Visibilit&eacute; du d&eacute;partement :');

define('_AM_GTD_PATH_CONFIG', "Configuration des R&eacute;pertoire du module");
define('_AM_GTD_PATH_TICKETATTACH', 'Pi&egrave;ces jointes des Inscriptions');
define('_AM_GTD_PATH_EMAILTPL', 'Mod&egrave;le-Template d\'Email');
define('_AM_GTD_TEXT_CREATETHEDIR', 'Cr&eacute;ation du dossier');
define('_AM_GTD_TEXT_SETPERM', 'D&eacute;finir les Permissions');

define('_AM_GTD_PATH_AVAILABLE', "<span style='font-weight: bold; color: green;'>Valide</span>");
define('_AM_GTD_PATH_NOTAVAILABLE', "<span style='font-weight: bold; color: red;'>Non valide</span>");
define('_AM_GTD_PATH_NOTWRITABLE', "<span style='font-weight: bold; color: red;'>Non ouvert en &eacute;criture</span>");
define('_AM_GTD_PATH_CREATED', "Dossier cr&eacute;&eacute; avec succ&egrave;s");
define('_AM_GTD_PATH_NOTCREATED', "Le dossier n\'a pas &eacute;t&eacute; cr&eacute;&eacute;");
define('_AM_GTD_PATH_PERMSET', 'Les Permissions du r&eacute;pertoire ont &eacute;t&eacute;es d&eacute;finies avec succ&egrave;s.');
define("_AM_GTD_PATH_NOTPERMSET", "Les permissions du r&eacute;pertoire n'ont pas pu &ecirc;tre d&eacute;finies.");
define('_AM_GTD_MESSAGE_ACTIVATE', 'Rendre Actif');
define('_AM_GTD_MESSAGE_DEACTIVATE', 'Rendre Inactif');
define('_AM_GTD_TEXT_ACTIVE', 'Actif');
define('_AM_GTD_TEXT_INACTIVE', 'Inactif');
define('_AM_GTD_TEXT_ACTIVITY', 'Activit&eacute;');
define('_AM_GTD_DEPARTMENT_EDIT_SERVER', 'Mise &agrave; jour de la boite mail du d&eacute;partement');

define('_AM_GTD_TEXT_MANAGE_FIELDS', 'Gestion des Cours et R&eacute;ducs.');
define('_AM_GTD_ADD_FIELD', 'Ajouter un champs');
define('_AM_GTD_EDIT_FIELD', 'Modification du champ');
define('_AM_GTD_TEXT_NAME_DESC', 'Titre du champs affich&eacute;');
define('_AM_GTD_TEXT_FIELDNAME', 'Nom du champs :');
define('_AM_GTD_TEXT_FIELDNAME_DESC', 'Champs de la base de donn&eacute;e <br> Rock_<i>(incr&eacute;mentation d\'un num&eacute;ro)</i> <br> Salsa_<i>(incr&eacute;mentation d\'un num&eacute;ro)</i> <br> Stage_<i>(incr&eacute;mentation d\'un num&eacute;ro)</i> <br> Reduc_<i>(incr&eacute;mentation d\'un num&eacute;ro)</i> ');
define('_AM_GTD_TEXT_DESCRIPTION_DESC', 'Informations compl&eacute;mentaires du champs.');
define('_AM_GTD_TEXT_CONTROLTYPE', 'Type de contr&ocirc;le :');
define('_AM_GTD_TEXT_CONTROLTYPE_DESC', 'Rock_ => <i>Bo&icirc;te de Selection</i> <br> Salsa_ => <i>Bo&icirc;te de Selection</i> <br> Stage_ => <i>Bo&icirc;te de Selection</i> <br> Reduc_ => <i>Bo&icirc;te radio</i>');
define('_AM_GTD_TEXT_DEPT_DESC', 'Pour quel quel d&eacute;partement d&eacute;sirez vous montrer ce champ ?');
define('_AM_GTD_TEXT_REQUIRED', 'Requis :');
define('_AM_GTD_TEXT_REQUIRED_DESC', 'Ce champs devra-t-il &ecirc;tre requis lors de l\'inscription ?');
define('_AM_GTD_TEXT_DATATYPE', 'Type de donn&eacute;es :');
define('_AM_GTD_TEXT_DATATYPE_DESC', 'De quel type de donn&eacute;es est ce champ ?');
define('_AM_GTD_TEXT_VALIDATION', 'Validation:');
define('_AM_GTD_TEXT_VALIDATION_DESC', 'Utiliser une expression r&eacute;guliaire afin de valider les donn&eacute;es entr&eacute;es par les utilisateurs.');
define('_AM_GTD_TEXT_WEIGHT', 'Poids :');
define('_AM_GTD_TEXT_WEIGHT_DESC', 'Utilis&eacute; pour classer les champs personnalis&eacute;s.');
define('_AM_GTD_TEXT_FIELDVALUES', 'Liste des valeurs de champs :');
define('_AM_GTD_TEXT_FIELDVALUES_DESC', '

      <TABLE BORDER width=10px>
          <CAPTION>Les diff&eacute;rentes m&eacute;thodes de calculs</CAPTION>
          <TR>
             <TH></TH>  <TH>Rock & Salsa</TH>  <TH>Stage</TH> <TH>Reduc</TH>
          </TR>
		<TR> <TD>Par Tranche</TD>  <TD>1=1 Heure</TD>  <TD>X</TD> <TD>X</TD></TR>
		<TR> <TD>Par Montant</TD> <TD>X</TD>  <TD>200=Stage Lady Salsa</TD> <TD>50=Offre de bienvenue</TD></TR>
		<TR> <TD>Par %</TD>  <TD>X</TD>  <TD>X</TD> <TD>10%=J\'ai - de 25 ans</TD>
		</TR>
        </TABLE><br> 

L\'information avant le "=" est la valeur et apr&eacute;s le texte explicatif.');
define('_AM_GTD_TEXT_DEFAULTVALUE', 'Valeur par d&eacute;faut :');
define('_AM_GTD_TEXT_DEFAULTVALUE_DESC', 'Valeur par d&eacute;faut propos&eacute;e dans le champs personnalis&eacute;.<br />Pour un champ personnalis&eacute; qui aurait plus d\'une valeur, utilisez la cl&eacute; de l\'&eacute;l&eacute;ment.');
define('_AM_GTD_TEXT_LENGTH', 'Longueur :');
define('_AM_GTD_TEXT_LENGTH_DESC', 'Longueur du champ personnalis&eacute;.');



define('_AM_GTD_TEXT_REGEX_CUSTOM', 'Personnalisation');
define('_AM_GTD_TEXT_REGEX_USPHONE', 'Num&eacute;ro de t&eacute;l&eacute;phone');
define('_AM_GTD_TEXT_REGEX_USZIP', 'Code Postal');
define('_AM_GTD_TEXT_REGEX_EMAIL', 'Adresse Email');

define('_GTD_CONTROL_DESC_TXTBOX', 'Bo&icirc;te texte');
define('_GTD_CONTROL_DESC_TXTAREA', 'Bo&icirc;te texte multi-lignes');
define('_GTD_CONTROL_DESC_SELECT', 'Bo&icirc;te de s&eacute;lection');
define('_GTD_CONTROL_DESC_MULTISELECT', 'Bo&icirc;te de multi s&eacute;lection');
define('_GTD_CONTROL_DESC_YESNO', 'Oui / Non');
define('_GTD_CONTROL_DESC_CHECKBOX', 'Bo&icirc;te &agrave; cocher');
define('_GTD_CONTROL_DESC_RADIOBOX', 'Bo&icirc;te radio');
define('_GTD_CONTROL_DESC_DATETIME', 'Date+Heure');
define('_GTD_CONTROL_DESC_FILE', 'Fichier');

define('_GTD_DATATYPE_TEXT', 'Texte');
define('_GTD_DATATYPE_NUMBER_INT', 'Nombre (Entier)');
define('_GTD_DATATYPE_NUMBER_DEC', 'Nombre (D&eacute;cimal)');

define('_AM_GTD_MSG_FIELD_DEL_CFRM', 'Etes-vous certain de vouloir effacer ce champs #%u?');
define('_AM_GTD_VALID_ERR_CONTROLTYPE', 'Type de contr&ocirc;le s&eacute;lectionn&eacute; invalide.');
define('_AM_GTD_TEXT_SESSION_RESET', 'Nettoyer les erreurs');
define('_AM_GTD_VALID_ERR_NAME', 'Nom non param&eacute;tr&eacute;');
define('_AM_GTD_VALID_ERR_FIELDNAME', 'Nom de champ non param&eacute;tr&eacute;');
define('_AM_GTD_VALID_ERR_FIELDNAME_UNIQUE', 'le nom de champ doit &ecirc;tre unique');
define('_AM_GTD_VALID_ERR_LENGTH', 'La longueur doit &ecirc;tre un nombre dont la valeur est comprise entre %u et %u');
define('_AM_GTD_VALID_ERR_DEFAULTVALUE', 'La valeur par d&eacute;faut doit &ecicrc;tre dans la liste d\'options');
define('_AM_GTD_VALID_ERR_VALUE_LENGTH', 'La valeur "%s" est plus grande que la valeur du champ, %u caract&egrave;res');
define('_AM_GTD_VALID_ERR_VALUE', 'Vous devez fournir un param&egrage;trage de valeur pour ce champ');
define('_AM_GTD_MSG_FIELD_ADD_OK', 'Champ ajout&eacute; avec succ&egrave;s');
define('_AM_GTD_MSG_FIELD_ADD_ERR', 'Des erreurs sont apparues lors de l\'ajout du champ');
define('_AM_GTD_MSG_FIELD_UPD_OK', 'Champs mis &agrave; jour avec succ&egrave;s');
define('_AM_GTD_MSG_FIELD_UPD_ERR', 'Des erreurs sont apparues &agrave; la mise &agrave; jour du champ');
define('_AM_GTD_MSG_SUBMISSION_ERR', 'Votre soumission contient des erreurs.  Veuillez la corriger et soumettre de nouveau');
define('_AM_GTD_MSG_NEED_UID', 'Erreur: vous devez s&eacute;lectionner un utilisateur en premier.');

define('_AM_GTD_TEXT_DEFAULT_STATUS', 'Statut par d&eacute;faut');

define('_AM_GTD_VALID_ERR_MIME_EXT', 'Extension de fichier non param&egrave;tr&eacute;e');
define('_AM_GTD_VALID_ERR_MIME_NAME', 'Type/Nom de l\'Application  non param&egrave;tr&eacute;s');
define('_AM_GTD_VALID_ERR_MIME_TYPES', 'Mime Types non param&egrave;tr&eacute;');

define('_AM_GTD_TEXT_NOTIF_NAME', 'Nom de Notification');
define('_AM_GTD_TEXT_SUBSCRIBED_MEMBERS', 'Membres inscrits');

define('_AM_GTD_NOTIF_NEW_TICKET', 'Cr&eacute;ation un ticket');
define('_AM_GTD_NOTIF_DEL_TICKET', 'Suppression d\'un ticket');
define('_AM_GTD_NOTIF_MOD_TICKET', 'Modification de ticket');
define('_AM_GTD_NOTIF_NEW_RESPONSE', 'Ajout de r&eacute;ponse');
define('_AM_GTD_NOTIF_MOD_RESPONSE', 'Modification de r&eacute;ponse');
define('_AM_GTD_NOTIF_MOD_STATUS', 'Modification d\'&eacute;tat');
define('_AM_GTD_NOTIF_MOD_GENRE', 'Modification de genre');
define('_AM_GTD_NOTIF_MOD_OWNER', 'Modification de propri&eacute;taire');
define('_AM_GTD_NOTIF_CLOSE_TICKET', 'Cl&ocirc;ture du ticket');
define('_AM_GTD_NOTIF_MERGE_TICKET', 'Fusion de tickets');

//Used for Manage Notifications page
define('_AM_GTD_STAFF_SETTING1', 'Toutes les &eacute;quipes');
define('_AM_GTD_STAFF_SETTING2', 'Equipe du d&eacute;partement');
define('_AM_GTD_STAFF_SETTING3', 'Propri&eacute;taire du ticket');
define('_AM_GTD_STAFF_SETTING4', 'Notification d&eacute;sactiv&eacute;e');
define('_AM_GTD_USER_SETTING1', 'Notification activ&eacute;');
define('_AM_GTD_USER_SETTING2', 'Notification d&eacute;sactiv&eacute;e');
define('_AM_GTD_TEXT_SUBMITTER', 'D&eacute;posant');
define('_AM_GTD_TEXT_NOTIF_STAFF', 'Notification de l\'&eacute;quipe');
define('_AM_GTD_TEXT_NOTIF_USER', 'Notification de l\'utilisateur');
define('_AM_GTD_TEXT_ASSOC_TPL', 'Mod&egrave;les associ&eacute;s');
define('_AM_GTD_TEXT_AND', 'et');
?>