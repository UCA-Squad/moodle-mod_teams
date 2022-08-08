<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'teams', language 'fr'
 *
 * @package   mod_teams
 * @copyright 2020 Université Clermont Auvergne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Teams';
$string['modulename_help'] = 'Module permettant de créer une ressource Teams pour votre cours et d\'afficher le lien vers celle-ci. Les ressources actuellement disponible à la création sont les équipes et les réunions (ou classes virtuelles) Teams.<br/> Attention les utilisateurs et notamment les étudiants devront avoir créé ou activer leur compte Office 365 pour pouvoir y accéder.';
$string['modulenameplural'] = 'Teams ';
$string['pluginname'] = 'Teams';
$string['pluginadministration'] = 'Ressource Teams';
$string['teams:addinstance'] = 'Ajouter une ressource Teams';
$string['teams:addmeeting'] = 'Créer/modifier une réunion Teams';
$string['teams:addteam'] = 'Créer/modifier une équipe Teams';
$string['teams:view'] = 'Visualiser une ressource Teams';
$string['resourceerror'] = 'Créer/modifier une réunion Teams ou créer/modifier une équipe Teams';
$string['privacy:metadata'] = 'Le module Ressource Teams ne stocke aucune donnée personnelle.';
$string['use_prefix'] = 'Utiliser un préfixe dans le nom de la ressource';
$string['use_prefix_help'] = 'Si cette case est cochée un préfixe du type "[TYPE]" sera ajouté au nom de la ressource pour identifier le type de la ressource (équipe, réunion...).';
$string['notunique'] = 'L\'adresse email n\'est pas unique dans l\'Active Directory Azure.';
$string['notfound'] = 'L\'adresse email n\'a pas pu être reliée à un utilisateur dans l\'Active Directory Azure.';

$string['name'] = 'Nom de la ressource Teams';
$string['name_help'] = 'Nom de la ressource Teams qui sera affiché sur la page de votre cours. Un préfixe pourra être ajouté avant celui-ci pour identifier le type de ressource.';
$string['desc'] = '<div class="alert alert-info">La création d\'une équipe Teams depuis la plateforme Moodle est une action qui peut prendre quelques secondes.<br/>
                            Il est également possible que vous n\'ayez pas accès de suite à la team que vous avez créé et que cette mise à jour d\'accès prenne quelques minutes. Merci de votre compréhension.</div>';
$string['noaccount'] = '<p>Il semblerait que vous n\'ayez pas encore de compte microsoft enregistré, vous ne pouvez donc pour l\'instant pas créer de team.</p>
                                <p>Une fois votre compte créé vous pourrez ensuite revenir pour créer votre team depuis moodle.</p>';
$string['teamserror'] = '<p>Il semblerait qu\'une erreur se soit produite lors de votre tentative de création de la team: "{$a}".</p>
                                <p>Retentez l\'opération, si le souci persiste, contactez le support.</p>';
$string['back'] = 'Revenir au cours';
$string['type'] = 'Type de ressource Teams à créer';
$string['type_help'] = 'Type de ressource Teams à créer: 
                                    <ul><li>Equipe Teams: vous permet de créer une équipe dans Teams que vous pourrez gérer à votre guise (ajouts de canaux...) et avec les membres du cours que vous aurez sélectionné.</li>
                                    <li>Réunion Teams (Classe virtuelle): vous permet de créer directement une réunion Teams (ou classe virtuelle) que pourront rejoindre les utilisateurs cliquant sur le lien de l\'activité moodle.</li></ul><br/>
                                    Seules les ressources pour lesquelles vous avez la capacité de création seront listées ici.';
$string['team'] = 'Equipe Teams<br/>';
$string['meeting'] = 'Réunion Teams<br/> Classe virtuelle';
$string['team_prefix'] = '[EQUIPE] ';
$string['meeting_prefix'] = '[REUNION] ';
$string['population'] = 'Population ciblée';
$string['population_help'] = '<p>Choisissez la population que vous voulez inscrire à votre team:
                                        <ul><li>tous les inscrits au cours (étudiants + enseignants): tous les utilisateurs inscrits à ce cours seront ajoutés comme membres de la team, vous et les autres gestionnaires du cours seront inscrit(e)s comme propriétaire de la team </li>
                                        <li>tous les étudiants inscrits au cours : seulement les étudiants inscrits à ce cours seront ajoutés comme membre de l\'équipe. Par défaut vous serez inscrite(e) comme propriétaire de cette équipe et les autres gestionnaires/enseignants ne seront pas ajoutés comme membres de l\'équipe</li>
                                        <li>un ou plusieurs groupes: seuls les membres de ce(s) groupe(s) seront ajoutés comme membres  de la team et vous serez inscrit(e) comme propriétaire de cette team </li>
                                        <li>des utilisateurs précis: seuls les utilisateurs que vous aurez sélectionnés seront ajoutés comme membres de la team et vous serez inscrit(e) comme propriétaire de cette team</li></ul></p>';
$string['population_all'] = 'Tous les inscrits au cours (étudiants + enseignants)';
$string['population_students'] = 'Tous les étudiants inscrits au cours';
$string['population_groups'] = 'Un ou plusieurs groupes';
$string['population_users'] = 'Des utilisateurs précis';
$string['enrol_managers'] = 'Inscrire les autres gestionnaires du cours comme propriétaire';
$string['enrol_managers_help'] = 'Si ce réglage est activé tous les utilisateurs possédant le rôle de "gestionnaire" sur le cours seront également ajoutés comme propriétaire de la team et posséderont des droits d\'administration sur celle-ci.<br/>
                                            S\'il n\'est pas activé, vous, en tant que créateur de cette team, serez le seul utilisteur avec le rôle de propriétaire de la team.';
$string['opendate'] = 'Début de la réunion';
$string['opendate_help'] = 'Date à partir de laquelle la ressource sera disponible. Si cette option n\'est pas activée la réunion sera disponible dès sa création.';
$string['opendate_session'] = ' (Début réunion Teams)';
$string['closedate'] = 'Fin de la réunion';
$string['closedate_help'] = 'Date à partir de laquelle la ressource ne plus sera disponible. Si cette option n\'est pas activée la réunion restera disponible sans autre action d\'un des organisateurs.';
$string['closedate_session'] = ' (Fin réunion Teams)';
$string['dates_help'] = '<div class="alert alert-info"><strong>Attention, la réunion créée depuis cette interface ne sera pas accessible depuis le Calendrier Teams. Vous ne pourrez y accéder que depuis Moodle. Les étudiants ne recevront pas de notification de cette réunion sur leur messagerie.</strong>';
$string['dates_help'] = '<div class="alert alert-info"><strong>Attention, les étudiants et autres utilisateurs ne recevront pas de notification mail pour la participation à cette réunion.</strong>
                            <ul><li>Réunion ponctuelle: <ul><li>Elle est visible uniquement dans le calendrier Teams de son créateur. Les étudiants voient la réunion ponctuelle depuis la section de cours où elle a été créée, depuis le bloc "Evénements à venir" et le bloc Calendrier de Moodle (pensez à rajouter ces blocs dans le cours, si besoin)</li>
                            <li>Si vous n’activez pas les dates de début ou fin de réunion, un créneau sera attribué par défaut (à partir de l’heure de création de la réunion à laquelle on ajoutera la durée de réunion par défaut configurée dans l\'administration moodle). Des tests seront effectués au moment de l\'accès à l\'activité depuis le cours pour vérifier la disponibilité de celle-ci par rapport à cette période et si vous devez donc être redirigés ou non vers la réunion.</li></ul></li>
                            <li>Réunion permanente: Elle est visible et disponible uniquement depuis la section de cours où elle est ajoutée et utilisable dès sa création.</li></ul>
                            <p>Important: Tous les changements concernant les équipes et les réunions classe virtuelle réalisés directement depuis Teams (changement du nom, des dates de la réunion...) ne se répercuteront pas dans Moodle.</p>';
$string['dates_between'] = 'entre le %s et le %s';
$string['dates_from'] = 'à partir du %s';
$string['dates_until'] = 'jusqu\'au %s';
$string['error_groups'] = 'Veuillez choisir au moins un groupe ou bien changez la valeur du type de population.';
$string['error_users'] = 'Veuillez choisir au moins un utilisateur ou bien changez la valeur du type de population.';
$string['error_dates'] = 'La date de fin doit être postérieure que la date d\'ouverture.';
$string['teams:error_dates_past'] = 'La période définie ne pas correspondre à une période déjà passée.';
$string['no_owner'] = '<p>Vous n\'ếtes pas propriétaire de cette team, vous ne pouvez donc pas éditer les propriétés de celle-ci.</p>
                                <p>Veuillez contater le(s) propriétaire(s) de la team pour qu\'il vous octroie ces droits d\'administration.</p>';
$string['teamnotfound'] = 'L\'accès à cette équipe est impossible. Un problème est peut-être survenu sur le serveur Microsoft, auquel cas retentez de vous connecter un peu plus tard. Il est également possible que cette équipe ait été supprimée par un des propriétaires.';
$string['meetingnotfound'] = 'L\'accès à cette réunion semble impossible. Un problème est peut-être survenu sur le serveur Microsoft, auquel cas retentez de vous connecter un peu plus tard. Il est également possible que cette réunion ait été supprimée par un des organisateurs.';
$string['meetingnotavailable'] = 'L\'accès à cette réunion (classe virtuelle) n\'est actuellement pas disponible.%s En cas de difficultés, contactez directement votre enseignant(e).';
$string['description'] = 'Equipe créée dans le cadre du cours "%s".';
$string['meetingavailable'] = ' La réunion Teams est disponible %s.';
$string['copy_link'] = 'Copier le lien dans le presse-papier';
$string['create_mail_content'] = 'Bonjour,\nVous venez de créer la réunion Teams "%s" depuis votre cours moodle "%s".\nRetrouvez celle-ci en cliquant sur le lien ci-après : ';
$string['create_mail_title'] = 'Création de votre réunion Teams';
$string['messageprovider:meetingconfirm'] = 'Confirmation de la création de réunion Teams';
$string['notif_mail'] = 'Notification de création de réunion';
$string['notif_mail_help'] = 'Envoyer une notification suite à la création d\'une réunion avec le lien vers celle-ci.';

$string['owners'] = 'Définir les propriétaires de l\'équipe';
$string['owners_help'] = '<p>Choisissez les utilisateurs qui seront propriétaires de l\'équipe :
                                        <ul><li>le créateur de l\'équipe uniquement : seul vous ou l\'utilisateur sélectionnée dans le cas de la création pour autrui sera ajouté comme propriétaire de l\'équipe. </li>
                                        <li>le créateur + d\'autres utilisateurs : seul le créateur (vous ou la personne pour qui vous allez créer l\'équipe) ainsi que les utilisateurs choisis parmi la liste des inscrits au cours seront ajoutés comme propriétaires de l\'équipe.</li>
                                        <li>tous les gestionnaires du cours : tous les gestionnaires du cours seront ajoutés comme propriétaires de l\'équipe.</li></ul></p>';
$string['owners_creator'] = 'Le créateur de l\'équipe uniquement';
$string['owners_others'] = 'Le créateur + d\'autres utilisateurs';
$string['owners_managers'] = 'Tous les gestionnaires du cours';
$string['other_owners'] = 'Nommer d\'autres utilisateurs comme propriétaires de l\'équipe';
$string['other_owners_help'] = 'Renseigner ici les autres utilisateurs auxquels vous voudriez donner des droits de propriétaires sur cette équipe.';
$string['reuse_meeting'] = 'Utilisation ?';
$string['reuse_meeting_help'] = 'Type d\'utilisation de la réunion:
                                <ul><li>Permanente: le lien de la réunion généré sera accessible pour les inscrits à votre cours dès sa création (hors restriction d\'accès moodle).</li>
                                <li>Ponctuelle: la réunion est directement accessible à son créateur. Pour les autres utilisateurs moodle fera un contrôle par rapport à la période d\'ouverture définie avant de faire la redirection vers la réunion.</li></ul>';
$string['reuse_meeting_no'] = 'Ponctuelle';
$string['reuse_meeting_yes'] = 'Permanente';
$string['meeting_default_duration'] = 'Durée par défaut de la réunion si une date de fin n\'est pas renseignée';
$string['meeting_default_duration_help'] = 'Permet de renseigner une durée par défaut pour les réunions crées via le module si une date de fin n\'est pas renseignée. La date de fin par défaut sera calculée par rapport à la date de début rénseignée et en ajoutant cette durée.';

$string['gotoresource'] = 'Accéder à la ressource Teams';
$string['title_courseurl'] = 'Revenir au cours';
