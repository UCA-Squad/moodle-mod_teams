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
 * Strings for component 'teams', language 'en'
 *
 * @package   mod_teams
 * @copyright 2020 Université Clermont Auvergne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Teams';
$string['modulename_help'] = 'Mod which permits to create a Teams channel and to display a link to it';
$string['modulenameplural'] = 'Teams';
$string['pluginname'] = 'Teams';
$string['pluginadministration'] = 'Teams Resource';
$string['teams:addinstance'] = 'Add a Teams Resource';
$string['teams:view'] = 'View a Teams Resource';
$string['privacy:metadata'] = 'Team Resource plugin does not store or transmit any personal data.';
$string['use_prefix'] = 'Use prefix in the resource name';
$string['use_prefix_help'] = 'If checked a prefix like "[TYPE]" will be use in the resource name to identify its type (team, online meeting...).';
$string['notunique'] = 'The email is not unique in Azure Active Directory.';
$string['notfound'] = 'Email not found in Azure Active Directory.';

$string['name'] = 'Resource name';
$string['name_help'] = 'Resource name which will be displayed on your course page. A prefix will be added to identify the type of the ressource.';
$string['desc'] = '<div class="alert alert-info">Team creation is an action which can take few seconds.<br/>
                            It is also possible you do not have a direct access to this team, the access update can take few minutes. Thanks for your understanding.</div>';
$string['noaccount'] = '<p>It seems you do not have any microsoft account, so you cannot create a Team for now.</p>
                               <p>With your account created come back here to create your team from moodle.</p>';
$string['teamserror'] = '<p>It seems an error occured during your team creation: "{$a}".</p>
                                <p>Please retry this operation and contact the support if this problem persists.</p>';

$string['back'] = 'Return to course';
$string['type'] = 'Teams resource type to create';
$string['type_help'] = 'Teams resource type to create: 
                                    <ul><li>Team: create a team you will manage (add channels...) with members you selected in the course. </li>
                                    <li>Online meeting (virtual classroom): create immediatly a meeting (or virtual classroom) users will be able to join by clicking on the resource link.</li></ul>';
$string['team'] = 'Team<br/>';
$string['meeting'] = 'Online meeting<br/>Virtual classroom';
$string['team_prefix'] = '[TEAM] ';
$string['meeting_prefix'] = '[ONLINE MEETING] ';
$string['population'] = 'Population ';
$string['population_help'] = '<p>Select the populaton you want to enrol to the team:
                                        <ul><li>enrolled users: all enrolled users will be added as team members and all course managers will be added as this team owner</li>
                                        <li>one group or more: only this(these) group(s) members will be added as team members and you will be added as this team owner</li>
                                        <li>chosen users: only users you selected will be added as team members and you will be added as this team owner</li></ul></p>';
$string['population_all'] = 'Enrolled users (students + teachers)';
$string['population_groups'] = 'One group or more';
$string['population_users'] = 'Chosen users';
$string['enrol_managers'] = 'Enrol all course managers as team owners';
$string['enrol_managers_help'] = 'If enable all users with the "manager" role will be add to the team as owner and will have administration rights on it.<br/>
                                           If don\'t, you, as creator of the team, will be the only owner of this team.';
$string['opendate'] = 'Start date of the meeting';
$string['opendate_help'] = 'Start date of the meeting. If this option is not selected the meeting will be avalaible since its creation.';
$string['opendate_session'] = ' (Teams session start)';
$string['closedate'] = 'Closing date of the meeting';
$string['closedate_help'] = 'Closing date of the resource. If this option is not selected the meeting will be avalaible until another action from one of the organizers.';
$string['closedate_session'] = ' (Teams session end)';
$string['dates_help'] = '<div class="alert alert-info"><strong>Be careful, the meeting created on this page will not be displayed on the Teams calendar. You can only see it on the Moodle calendar. Students will not receive email notifications for this meeting.</strong>';
$string['dates_between'] = 'between %s and %s';
$string['dates_from'] = 'from %s';
$string['dates_until'] = 'until %s';
$string['error_groups'] = 'Please select at least one group or change the population field value.';
$string['error_users'] = 'Please select at least one enrolled user or change the population field value.';
$string['error_dates'] = 'Closing date must be later than start date.';
$string['no_owner'] = '<p>You are not a owner of this team so that\'s why you cannot edit its properties.</p>
                                <p>Please contact a team owner to give you these administation rights.</p>';
$string['teamnotfound'] = 'Access to this team is not possible. A problem may be running on Microsoft Server, in this cas retry to connect later. It is also possible an owner delete this team.';
$string['meetingnotfound'] = 'Access to this team seems possible. A problem may be running on Microsoft Server, in this cas retry to connect later. It is also possible an organizer delete this meeting.';
$string['meetingnotavailable'] = 'Access to this meeting (virtual classroom) is not available.%s In case of difficulties please contact your course manager(s).';
$string['meetingavailable'] = 'Teams online meeting is available %s.';
$string['description'] = 'Team created for the course "%s".';
$string['copy_link'] = 'Copy the meeting link to the keyboard';
$string['create_mail_content'] = 'Hello,\nYou have just created the Teams online meeting "%s" on your Moodle course "%s".\nYou can find this meeting by clicking on this link : ';
$string['create_mail_title'] = 'New Teams online meeting created';
$string['messageprovider:meetingconfirm'] = 'Confirmation of the Teams online meeting creation';
$string['notif_mail'] = 'Online meeting creation notification';
$string['notif_mail_help'] = 'Send a notification after the creation of an online meeting with the link to it.';