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
 * List upgrade changes of the plugin.
 *
 * @package    mod_teams
 * @copyright  2021 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_teams_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2020052600) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('teams');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'enrol_managers');
        }

        $field = new xmldb_field('enrol_managers', XMLDB_TYPE_INTEGER, 1, null, false, null, 0);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }

        $field = new xmldb_field('creator_id', XMLDB_TYPE_INTEGER, 10, null, false, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $index = new xmldb_index('creator_id', XMLDB_INDEX_NOTUNIQUE, array('creator_id'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }

    if ($oldversion < 2020091200) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('teams');

        $opendate = new xmldb_field('opendate', XMLDB_TYPE_INTEGER, 10, null, false, null, 0);
        if (!$dbman->field_exists($table, $opendate)) {
            $dbman->add_field($table, $opendate);
        }

        $closedate = new xmldb_field('closedate', XMLDB_TYPE_INTEGER, 10, null, false, null, 0);
        if (!$dbman->field_exists($table, $closedate)) {
            $dbman->add_field($table, $closedate);
        }
    }

    if ($oldversion < 2020101301) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('teams');

        $type = new xmldb_field('type', XMLDB_TYPE_CHAR, 30, null, false, null, 'team');
        if (!$dbman->field_exists($table, $type)) {
            $dbman->add_field($table, $type);
        }

        $team_id = new xmldb_field('team_id', XMLDB_TYPE_TEXT, null, null, false, null, null);
        if ($dbman->field_exists($table, $team_id)) {
            $dbman->rename_field($table, $team_id, 'resource_teams_id');
        }
    }

    if ($oldversion < 2021011205) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('teams');

        $reuse = new xmldb_field('reuse_meeting', XMLDB_TYPE_INTEGER, 1, null, false, null, 1);
        if (!$dbman->field_exists($table, $reuse)) {
            $dbman->add_field($table, $reuse);
        }
    }

    if ($oldversion < 2021020800) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('teams');

        $owners = new xmldb_field('other_owners', XMLDB_TYPE_TEXT, null, null, false, null, null);
        if (!$dbman->field_exists($table, $owners)) {
            $dbman->add_field($table, $owners);
        }
    }

    return true;
}