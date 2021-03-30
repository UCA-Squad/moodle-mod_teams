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
 * Settings for teams plugin.
 *
 * @package    mod_teams
 * @copyright  2020 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('mod_teams/client_id',
        'Application (client) ID',
        'Application (client) ID',
        ''
    ));
    $settings->add(new admin_setting_configtext('mod_teams/tenant_id',
        'Directory (tenant) ID',
        'Directory (tenant) ID',
        ''
    ));
    $settings->add(new admin_setting_configtext('mod_teams/client_secret',
        'Application (client) secret',
        'Application (client) secret',
        ''
    ));
    $settings->add(new admin_setting_configtext('mod_teams/team_model',
        'ID Teams modèle',
        'ID Teams modèle',
        ''
    ));
    $settings->add(new admin_setting_configcheckbox('mod_teams/use_prefix',
        get_string('use_prefix', 'mod_teams'),
        get_string('use_prefix_help', 'mod_teams'),
        0
    ));
    $settings->add(new admin_setting_configcheckbox('mod_teams/notif_mail',
        get_string('notif_mail', 'mod_teams'),
        get_string('notif_mail_help', 'mod_teams'),
        0
    ));
    $settings->add(new admin_setting_configselect('mod_teams/meeting_default_duration',
        get_string('meeting_default_duration', 'mod_teams'),
        get_string('meeting_default_duration_help', 'mod_teams') ,
        '+2 hours',
        array(
            '+30 minutes'   =>  get_string('numminutes', '',30),
            '+1 hour'       =>  get_string('numhours', 'moodle', 1),
            '+2 hours'      =>  get_string('numhours', 'moodle', 2),
            '+3 hours'      =>  get_string('numhours', 'moodle', 3),
        )
    ));
}
