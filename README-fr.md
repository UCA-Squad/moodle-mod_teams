Module d'activité Teams
==================================
Module permettant de créer une ressource Teams (équipe ou réunion/classe virtuelle) depuis un cours moodle.

Objectifs
------------
Les objectifs de ce module étaient de pouvoir créer une resource Teams à partir d'un cours moodle, d'accéder à celle-ci depuis le cours et d'y retrouver facilement les inscrits au cours. 

Pré-requis
------------
- Moodle en version 3.7 ou plus récente.<br/>
-> Tests effectués sur des versions 3.7 à 3.11.0 (des tests sur des versions antérieures n'ont pas encore été effectués).<br/>
- Composer installé sur votre machine/serveur.
- Avoir créé une application sur l'Active Directory Azure (ou avoir les droits nécessaires pour en créer une).

Création application Active Directory Azure
------------
- Tutorial: <a href="https://docs.microsoft.com/en-us/azure/active-directory/reports-monitoring/howto-configure-prerequisites-for-reporting-api" target="_blank">https://docs.microsoft.com/en-us/azure/active-directory/reports-monitoring/howto-configure-prerequisites-for-reporting-api</a> <br/>
Les informations Application (client) ID, Directory (tenant) ID et Object ID seront à utiliser dans la configuration du plugin moodle. 


Installation
------------
1. Installation du plugin

- Avec git:
> git clone https://github.com/UCA-Squad/moodle-mod_teams.git mod/teams

- Téléchargement:
> Télécharger le zip depuis <a href="https://github.com/UCA-Squad/moodle-mod_teams/archive/refs/heads/main.zip" target="_blank">https://github.com/UCA-Squad/moodle-mod_teams/archive/refs/heads/main.zip </a>, dézipper l'archive dans le dossier mod/ et renommer le si besoin en "teams" ou installez-le depuis la page d'installation des plugins si vous possédez les droits suffisants.

2. Récupérer les librairies Microsoft Graph (https://packagist.org/packages/microsoft/microsoft-graph) utilisées dans le plugin. Pour cela placez-vous dans le dossier teams/ nouvellement créé et lancez la commande ```composer install```.<br/>
Vous pouvez également récupérer les versions les plus récentes de ces librairies en utilisant ```composer update```.
  
3. Aller sur la page de notifications pour finaliser l'installation du plugin.

4. Une fois l'installation terminée, plusieurs options d'administration seront à renseigner:

> Administration du site -> Plugins -> Modules d'activités -> Teams -> client_id<br/>
> Administration du site -> Plugins -> Modules d'activités -> Teams -> tenant_id<br/>
> Administration du site -> Plugins -> Modules d'activités -> Teams -> client_secret

Paramètres liés à l'application créée précédemment dans l'Active Directory Azure pour communiquer avec Teams.

> Administration du site -> Plugins -> Modules d'activités -> Teams -> team_model

Identifiant de l'équipe Teams qui va servir de modèle pour les autres équipes créees depuis moodle. Si vous ne souhaitez pas qu'un modèle soit utilisé, vous pouvez laisser ce paramètre vide, Moodle crééra alors une équipe avec le format par défaut.<br/>
Attention, il est important que cette équipe modèle soit toujours présente et avec au moins un propriétaire dans Teams, la création d'équipes par copie pourrait échouer sinon.

> Administration du site -> Plugins -> Modules d'activités -> Teams -> use_prefix 

Checkbox permettant d'indiquer si un préfixe doit être utilisé dans le nom de la resource. Par exemple pour une équipe: "[EQUIPE] Nom de votre équipe". Ce préfixe ne sera utilisé que sur moodle pour identifier le type de la ressource et ne sera pas visible dans Teams.

> Administration du site -> Plugins -> Modules d'activités -> Teams -> notif_mail

Checkbox permettant d'indiquer si l'on souhaite qu'une notification soit envoyée à l'utilisateur après la création d'une réunion avec le lien direct vers celle-ci.

> Administration du site -> Plugins -> Modules d'activités -> Teams -> meeting_default_duration
 
Permet de choisir dans la liste pré-remplie la durée par défaut d'une réunion. Cette valeur est notamment utilisée lorsqu'une date de fin n'est pas renseignée via le formulaire. La date de fin sera alors calculée en ajoutant cette durée à la date de début de la réunion.


Présentation / Fonctionnalités
------------
<p>Création une équipe Teams:</p>

- Quatre choix de population pour une équipe:  
  - tous les inscrits au cours: tous les utilisateurs inscrits au cours (peu importe leur rôle) seront ajoutés comme membres de l'équipe. Par défaut, les gestionnaires du cours seront paramétrés comme propriétaires de l'équipe.
  - tous les étudiants inscrits au cours: seuls les étudiants inscrits au cours (utilisateurs n'ayant pas de droits de gestion) seront ajoutés comme membres de l'équipe. Par défaut, seul le créateur de l'activité sera inscrit comme propriétaire de l'équipe.
  - un ou plusieurs groupe(s): seuls les utilisateurs du(des) groupe(s) sélectionné(s) seront ajoutés à l'équipe. Par défaut, seul le créateur de l'activité sera ajouté comme propriétaire de l'équipe.
  - utilisateur(s) sélectionné(s): seuls les utilisateurs sélectionné(s) seront ajoutés à l'équipe. Par défaut, seul le créateur de l'activité sera ajouté comme propriétaire de l'équipe.
- Trois choix possibles pour définir les propriétaires de l'équipe:
  - Le créateur de l'équipe uniquement: seul le créateur (utilisateur connecté) sera inscrit comme propriétaire.
  - Le créateur + d'autres utilisateurs: le créateur ainsi que d'autres utilisateurs sélectionnés manuellement seront inscrits comme propriétaires de l'équipe.
  - Tous les gestionnaires du cours: les gestionnaires du cours seront inscrits comme propriétaires de l'équipe (fonctionnement par défaut sur la version précédente du plugin).
- Affichage sur la page de cours du lien vers l'équipe nouvellement créée.
- Synchronisation des membres de l'équipe en fonction du type de population choisie.<br/>
Cette synchronisation se fera par l'intermédiaire d'un script powershell. Ce script utilisera un json mis à disposition sur votre plateforme moodle (adresse: https:mymoodle.com/mod/teams/get_infos.php) qui listera, en fonction des paramètres, soit les équipes créées depuis votre plateforme soit les membres attendus pour une équipe en particulier.
```json
Exemples json:

//Listant les équipes - mymoodle.com/mod/teams/get_infos.php
{
  "teams": [
    "2ecc85b7-60b1-47e3-ae4d-adbdbrec4577",
    "7546aecf-704b-4544-96c9-1234567abdece"
  ]
}

//Listant les membres d'une équipe - mymoodle.com/mod/teams/get_infos.php?team_id=2ecc85b7-60b1-47e3-ae4d-adbdbrec4577
{
  "members": [
    "member1@mymoodle.com",
    "member2@mymoodle.com"
  ],
  "owners": [
    "owner@mymoodle.com"
  ]
}
```
<p>Création d'une réunion (ou classe virtuelle):</p>

- Création d'une réunion soit "permanente" soit "ponctuelle":
  - une réunion permanente ne requiert aucune information de date et sera accessible dès la création de celle-ci.
  - une réunion ponctuelle est définie sur un créneau. Elle reste accessible directement dès sa création via le lien direct ou via Teams mais un test effectué par rapport au créneau choisi si l'accès se fait via Moodle.
- Possibilité de fixer des dates de début et de fin pour la réunion qui remonteront au niveau du calendrier Moodle et du bloc "Evénements à venir" ainsi que dans le calendrier Teams.
- Possibilité de modifier les dates d'une réunion ponctuelle.
- Envoi possible d'une notification à la création de la réunion avec le lien direct vers cette réunion.

<p>Note: il ne sera pas possible de restaurer une activité Teams. Si celle-ci est supprimée elle ne se retrouvera pas dans la corbeille du cours.<br/>
Dans le cadre d'une édition et dans un souci , il ne sera également pas possible de changer le type de ressource: de modifier une équipe en réunion et inversement.</p>

[EQUIPE] Synchronisation des membres
-----

Pour la synchronisation des membres des équipes le choix a été fait de le faire via un script powershell. L'utilisation de l'API à ce niveau ne paraissant pas pertinente et assez efficace compte tenu du potentiel volume de données à traiter.<br/>
Le script s'appuiera sur le json fourni sur moodle (script get_infos.php) qui listera les membres attendus pour chaque équipe.<br/>
Ce script powershell est en cours de refonte pour être partagé par la suite.

Pistes d'améliorations
-----
- Permettre l'ajout en direct des utilisateurs dans l'équipe. Eviter ainsi d'attendre une première synchronisation via le script powershell pour avoir une équipe "remplie".
- Ajout d'options supplémentaires (si possible via l'API). Ex: Salle d'attente, Qui peut présenter...
- Ajouter un réglage dans l'administration pour sélectionner les différents types de ressources qu'il sera possible d'ajouter via le module.
- Prise en compte du préfixe dans l'édition inline du nom de l'activité.
<p>N'hésitez pas à nous proposer des améliorations et/ou des développements/pull requests pour enrichir le plugin.</p>  

A propos
------
<a href="https://www.uca.fr">Université Clermont Auvergne</a> - 2021.<br/>
