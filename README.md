Mod Teams
==================================
This moodle mod creates and displays a Teams resource (team or online meeting/virtual classroom) from a Moodle course.

Goals
------------
Goals of this plugin were to create a Teams resource from a Moodle course, to access to it and to enrol course members as this teams resource members.

Requirements
------------
- Moodle 3.7 or later.<br/>
-> Tests on Moodle 3.7 to 3.10.1 (tests on older moodle versions not made yet).<br/>
- Composer on your computer/server
- Have an Azure Active Directory web application registred (or rights to create one).

Create Azure Active Directory web application
------------
- Tutorial: <a href="https://docs.microsoft.com/en-us/azure/active-directory/reports-monitoring/howto-configure-prerequisites-for-reporting-api" target="_blank">https://docs.microsoft.com/en-us/azure/active-directory/reports-monitoring/howto-configure-prerequisites-for-reporting-api</a> <br/>
Application (client) ID, Directory (tenant) ID and Object ID will be needed in the moodle plugin configuration.

Installation
------------
1. Local plugin installation

- With git:
> git clone https://github.com/UCA-Squad/moodle-mod_teams.git mod/teams

- Download way:
> Download the zip from https://github.com/UCA-Squad/moodle-mod_teams/archive/main.zip, unzip it in mod/ folder and rename it "teams" if necessary or install it from the "Install plugin" page if you have the right permissions.
 
2. Get Microsoft Graph libs (https://packagist.org/packages/microsoft/microsoft-graph) used in the plugin. Go to the new teams/ folder and use the command ```composer install```.<br/>
You can also get the latest libs versions by using ```composer update```. 
  
3. Then visit your Admin Notifications page to complete the installation.

4. Once installed, you should see new administration options:

> Site administration -> Plugins -> Activity modules -> Teams -> client_id<br/>
> Site administration -> Plugins -> Activity modules -> Teams -> tenant_id<br/>
> Site administration -> Plugins -> Activity modules -> Teams -> client_secret

Parameters from the Azure Active Directory web application created previously and use to communicate with Teams.

> Site administration -> Plugins -> Activity modules -> Teams -> team_model

Id of the team which will be used as a model to create teams from Moodle. If you don't want to use model you can let this field empty and Moodle will create a team with the default format.<br/>
Be careful, it is necessary to always keep this model team and with at least one owner. If you don't the team creation by copy can fail. 

> Site administration -> Plugins -> Activity modules -> Teams -> use_prefix 

If checked a prefix will be used in the resource name. For example for a team: "[TEAM] Name of your team". This prefix will only be used on moodle to identify the Teams resource type and will be visible on Teams.

> Site administration -> Plugins -> Activity modules -> Teams -> notif_mail

If checked a notification will be send to the user after an online meeting creation with a direct link to this meeting.

> Site administration -> Plugins -> Activity modules -> Teams -> meeting_default_duration

Parameter to choose in the given list the default meeting duration. This value will be used if the closedate of the meeting is empty in the form. This closedate will be deducted from the startdate and this selected duration.


Présentation / Features
------------
<p>Team creation:</p>

- 4 population choices for the team:  
  - enrolled users: all course enrolled users (with any role) will be added as team members. By default, course managers will be team owners.
  - enrolled student(s): only student (user with no management roles) will be added as team members. By default, only the activity creator will be team owner.
  - group(s): only users of this(these) selected group(s) will be added as team members. By default, only the activity creator will be team owner (except if you choose the option to add all course managers as team owners).
  - selected user(s): only selected user(s) will be added as team members. By default, only the activity creator will be team owner.
- 3 choices to define the team owners:
  - team creator only: only the team creator (current user) will be added as team owner.
  - team creator + other users: the team creator and manually chosen users will be added as team owners.
  - this cours managers: all the course managers will be added as team owners (feature by default on the previous version of the plugin).
- Displays link to the new team on the Moodle course page.
- Team members synchronization in function of the selected population.<br/>
This synchronization will be made with a powershell script. This script will use a json generated on your moodle (URL: https:mymoodle.com/mod/teams/get_infos.php) and will list all teams created or all expected members for a specific team.
```json
Json examples:

//Listing teams - mymoodle.com/mod/teams/get_infos.php
{
  "teams": [
    "2ecc85b7-60b1-47e3-ae4d-adbdbrec4577",
    "7546aecf-704b-4544-96c9-1234567abdece"
  ]
}

//Listing members of a team - mymoodle.com/mod/teams/get_infos.php?team_id=2ecc85b7-60b1-47e3-ae4d-adbdbrec4577
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

<p>Online meeting (or virtual classroom):</p>

- Create a "permanent" or a "one shot" online meeting:
  - a permanent meeting does not need any informations about dates and will be accessible since its creation.
  - a one shot meeting is defined on a specific time slot. It will be accessible since its creation with direct url or in Teams but tests on dates will be made on Moodle to redirect or not to the meeting.
- Fix start date and end date for a meeting. These dates will be visible on the Moodle calendar, the "Upcoming events" block and on your Teams calendar.
- Possible editing of the dates for a one shot meeting.
- Possible sending of a notification after the meeting creation with the direct link to this meeting.

<p>Note: it won't be possible to restore a Teams activity. If this has been deleted it won't be in the course recycle bin.</p>

[TEAM] Members synchronization
-----
For this members synchonization we made the choice to use a powershell script to do it. Using API here does not seemed relevant or efficient enough with the potential volume of datas to be processed.<br/>
This script will use the given json file by moodle which lists all expected members for each team.<br/>
This powershell script is currently being redesigned in order to be shared later.

Possible improvements
-----
- Add directly members to the team. Avoid a first script execution to have a "full" team. 
- Add more options (if possible with the API). Ex: Waiting lobby, Who can present...
- Add admin setting to select resource types it will be possible to add with the plugin. 
- Use the prefix when we edit inline the resource name. 
<p>Feel free to propose some improvements and/or developments/pull requests to improve this plugin.</p>  

About us
------
<a href="https://www.uca.fr">Université Clermont Auvergne</a> - 2021.<br/>
